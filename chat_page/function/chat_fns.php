<?php
	require_once('check_fns.php');
	function connect_database(){
		require('../data/database_user.php'); 
		$db = new mysqli($host,$user,$password,$database);
		if(mysqli_connect_errno()){
			throw new Exception("failed to connect the database\n".mysqli_connect_error());
		}
		return $db;
	}
	
	function query_user_exist(&$db,$username){
		$query = "select username from users where username = ?";
		$stmt = $db->prepare($query);
		$stmt->bind_param('s',$username);
		$stmt->execute();
		$stmt->store_result();
		if($stmt->num_rows == 0){
			throw new Exception('no such user');
		}
		$stmt->free_result();
	}
	//下面两个函数username 来自于$_session，不会发生sql注入
	function query_relation_send(&$db,&$contacts,$username){ //$contacts为二维数组[$name:[$alias, $last_query, $last_send, $last_query_, $last_send_], ...]
		$query = "select name2,alias,last_query,last_send from relations where name1 = '$username'";
		$result = $db->query($query);
		if($result === false){ //就算结果为空，$result 对象也不为空，除非查询出现错误
			throw new Exception('something wrong with mysql query');
		}	
		while($res = $result->fetch_assoc()){
			$contacts[$res['name2']] = ['alias'=>$res['alias'],'last_query'=>$res['last_query'],'last_send'=>$res['last_send'],'$last_query_'=>0,'$last_send_'=>0];
		}
		$result->free();
	}
	
	function query_relation_receive(&$db,&$contacts,$username){
		$query = "select name1,last_query,last_send from relations where name2 = '$username'";
		$result = $db->query($query);
		if($result === false){ 
			throw new Exception('something wrong with mysql query,code 1');
		}
		while($res = $result->fetch_assoc()){
			$contacts[$res['name1']]['last_query_'] = $res['last_query'];
			$contacts[$res['name1']]['last_send_'] =  $res['last_send'];
		}
		$result->free();
	}
		
	function query_record_all(&$db,$sent_by,$sent_to,&$string){ //初次加载页面
		$query = "select message,date_created from records
				  where sent_to = ? and sent_by = ? order by date_created"; //升序		  
		$stmt = $db->prepare($query);		  
		$stmt->bind_param('ss',$sent_to,$sent_by);
		$stmt->execute();
		$stmt->store_result();
		$stmt->bind_result($message,$date_created);
		while($stmt->fetch()){
			$string[] = ['date_created'=>$date_created,'message'=>$message];
		}
		$stmt->free_result();
	}
/****************************************************************************************************/		
	function set_query_time(&$db,$name1,$name2){
		$query = "update relations set last_query = ? where name1 = ? and name2 = ?";
		$stmt = $db->prepare($query);
		$time = time();
		$stmt->bind_param('dss',$time,$name1,$name2);
		$stmt->execute();
		if($stmt->affected_rows == -1){
			throw new Exception('failed to set query time');
		}else if($stmt->affected_rows == 0){
			throw new Exception('reload too fast');
		}
	}
	
	function set_send_time(&$db,$name1,$name2){
		$query = "update relations set last_send = ? where name1 = ? and name2 = ?";
		$stmt = $db->prepare($query);
		$time = time();
		$stmt->bind_param('dss',$time,$name1,$name2);
		$stmt->execute();
		if($stmt->affected_rows == -1){
			throw new Exception('failed to set send time');
		}else if($stmt->affected_rows == 0){
			throw new Exception('reload too fast');
		}
	}

	function query_record(&$db,$sent_by,$sent_to,$last_query,&$str_array){ //结果放在string中
		$query = "select message,date_created from records
				  where date_created >= ? and sent_to = ? and sent_by = ? order by date_created";
		$stmt = $db->prepare($query);		  
		$stmt->bind_param('dss',$last_query,$sent_to,$sent_by);
		$stmt->execute();
		$stmt->store_result();
		$stmt->bind_result($message,$date_created);
		while($stmt->fetch()){
			$str_array[] = ['date_created'=>$date_created,'message'=>$message];
		}
		$stmt->free_result();
	}
	
	function add_record(&$db,$sent_by,$sent_to,$message){
		$query = "insert into records value(null,?,?,?,?)";
		$stmt = $db->prepare($query);
		$time = time();
		$stmt->bind_param('ssss',$message,$sent_by,$sent_to,$time);
		$stmt->execute();
		if($stmt->affected_rows != 1){
			throw new Exception('failed to add record');
		}
	}
	
	function send_message($sent_by,$sent_to,$message){
		$db = connect_database();
		try{
			if($db->query('start transaction') === false){
				throw new Exception('failed to start transaction');
			}
			add_record($db,$sent_by,$sent_to,$message);  //这两句顺序不能反
			set_send_time($db,$sent_by,$sent_to);
			$db->query('commit');
		}catch(Exception $e){
			$db->query('rollback');
			$db->close();
			throw new Exception($e->getMessage());
		}
		$db->close();
	}
	
	function query_read_time(&$db,$username,$contact){
		$query = "select last_query from relations where name1 = ? and name2 = ?";
		$stmt = $db->prepare($query);
		$stmt->bind_param('ss',$username,$contact);
		$stmt->execute();
		$stmt->store_result();
		if($stmt->num_rows != 1) throw new Exception('failed to query read time');
		$stmt->bind_result($res);
		$stmt->fetch();
		$stmt->free_result();
		return $res;
	}
	
	function receive_message($contact,$username,$last_query,&$str_array){
		$db = connect_database();
		try{
			if($db->query('start transaction') === false){
				throw new Exception('failed to start transaction');
			}
			$res = query_read_time($db,$contact,$username);
			set_query_time($db,$username,$contact);  //这两句顺序不能反
			query_record($db,$contact,$username,$last_query,$str_array);
			$db->query('commit');
		}catch(Exception $e){
			$db->query('rollback');
			$db->close();
			throw new Exception($e->getMessage());
		}
		if(!empty($res)) return $res;
		$db->close(); 
	}
/****************************************************************************************************/	
	function is_contacts(&$db,$username,$contact){
		$res = false;
		$query = "select id from relations where name1 = ? and name2 = ?";
		$stmt = $db->prepare($query);
		$stmt->bind_param('ss',$username,$contact);
		$stmt->execute();
		$stmt->store_result();
		if($stmt->num_rows == 1){
			$res = true;
		}
		$stmt->free_result();
		return $res;
	}
	
	function is_request(&$db,$username,$contact){
		$res = false;
		$query = "select id from request_contact where sent_by = ? and sent_to = ? and done=false";
		$stmt = $db->prepare($query);
		$stmt->bind_param('ss',$username,$contact);
		$stmt->execute();
		$stmt->store_result();
		if($stmt->num_rows == 1){
			$res = true;
		}
		$stmt->free_result();
		return $res;
	}
	
	function is_new_message($last_query,$last_send_){
		return $last_query <= $last_send_;
	}
	
	
	function modify_alias($username,$contact,$newAlias){
		$db = connect_database();
		$query = "update relations set alias = ? where name1 = ? and name2 = ?";
		$stmt = $db->prepare($query);
		$stmt->bind_param('sss',$newAlias,$username,$contact);
		$stmt->execute();
		if($stmt->affected_rows != 1){
			throw new Exception('failed to set new alias');
		}
		$db->close();
	}
/****************************************************************************************************/	
	function add_contact_request($username,$contact,$alias){
		$db = connect_database();
		if(is_request($db,$username,$contact)){
			$db->close();
			throw new Exception('you have already send the request, waiting for acceptance');
		}
		if(is_contacts($db,$username,$contact)){
			$db->close();
			throw new Exception('you two has already been contacts, no need to request again');
		}
		$query = "insert into request_contact value(null,?,?,?,false)";  //参数数量与数据库表的项数要相同，即使表中已提供了默认值
		$stmt = $db->prepare($query);
		$stmt->bind_param('sss',$username,$contact,$alias);
		$stmt->execute();
		if($stmt->affected_rows != 1){
			throw new Exception('falied to add contact request');
			//调试时可以输出：throw new Exception('falied to add contact request'.$stmt->error);
		}
		$db->close();
	}
	
	function query_request_contact(&$db,$to,&$res){  //$to 来自于username，即$_session，不会发生sql注入
		$query = "select sent_by from request_contact where sent_to='$to' and done=false";
		$result = $db->query($query);
		if($result === false){
			throw new Exception('failed to query request for adding contact');
		}
		while($tmp = $result->fetch_assoc()){
			$res[] = $tmp['sent_by'];
		}
		$result->free();
	}
	
	function finish_contact_request(&$db,$username,$contact){
		$query = "update request_contact set done=true where sent_by=? and sent_to=?";
		$stmt = $db->prepare($query);
		$stmt->bind_param('ss',$username,$contact);
		$stmt->execute();
		if($stmt->affected_rows  == 0){
			throw new Exception('action falied');
		}
		$stmt->bind_param('ss',$contact,$username); //可能双方都互相发送了请求
		$stmt->execute();
	}
	
	function decline_contact($username,$contact){
		$db = connect_database();
		finish_contact_request($db,$contact,$username);
		$db->close();
	}
	//原子操作，使用MySQL事务
	function accept_contact($username,$contact,$alias){ //alias2 set by contact ,alias set by username(发送请求一方）
		$db = connect_database();
		$query = "select alias from request_contact where sent_to = ? and sent_by = ? and done=false"; 
		$stmt = $db->prepare($query);
		$stmt->bind_param('ss',$username,$contact);
		$stmt->execute();
		$stmt->store_result();
		if($stmt->num_rows != 1) throw new Exception('failed to query alias');
		$stmt->bind_result($alias2);
		$stmt->fetch();
		$stmt->free_result();
		
		$query = "insert into relations value(null,?,?,?,1,0)";
		if($db->query("start transaction") === false) throw new Exception('failed to open transaction');
		$stmt = $db->prepare($query);
		$stmt->bind_param('sss',$username,$contact,$alias);
		$stmt->execute();
		if($stmt->affected_rows != 1){
			$db->query("rollback");
			throw new Exception('failed to accept contact');
		}
		$stmt->bind_param('sss',$contact,$username,$alias2);
		$stmt->execute();
		if($stmt->affected_rows != 1){
			$db->query("rollback");
			throw new Exception('failed to accept contact');
		}
		if($db->query('commit') === false) throw new Exception('failed to commit');
		
		finish_contact_request($db,$contact,$username);
		$db->close();
	}
	//原子操作，只要一方提交删除联系人，另一方自动也删除相应联系人，使用MySQL事务
	function delete_contact($username,$contact){
		$db = connect_database();
		$query = "delete from relations where name1 = ? and name2 = ?";
		if($db->query("start transaction") === false){
			throw new Exception('failed to open transaction');
		}
		$stmt = $db->prepare($query);
		$stmt->bind_param('ss',$username,$contact);
		$stmt->execute();
		if($stmt->affected_rows != 1){
			$db->query("rollback");
			throw new Exception('failed to delete contact');
		}
		$stmt->bind_param('ss',$contact,$username);
		$stmt->execute();
		if($stmt->affected_rows != 1){
			$db->query("rollback");
			throw new Exception('failed to delete contact');
		}
		if($db->query('commit') === false){
			throw new Exception('failed to commit');
		}
		$db->close();
	}
?>