<?php
	header('Content-Type: application/json');
	session_start();
	ob_start();
	require_once('../function/chat_fns.php');
	if(!session_check(['username','contacts','contact'],'valid_user') || !post_check(['type']) || $_SESSION['contact'] == 'none'){
		echo json_encode(['success'=>false,'error'=>'illegal access']);
		exit;
	}
	
	$type = $_POST['type'];
	$username = $_SESSION['username'];
	$contact = $_SESSION['contact'];
	$last_query = $_SESSION['contacts'][$contact]['last_query'];
	try{
		switch($type){
			case 'send':
				if(!post_check(['message'])){
					throw new Exception('message can not be null');
				}
				send_message($username,$contact,$_POST['message']);
				$_SESSION['contacts'][$contact]['last_send'] = time();
				ob_clean();
				echo json_encode(['success'=>true]);
				exit;
			case 'receive':
				$str_array = null;
				$_SESSION['contacts'][$contact]['last_query'] = time();
				$last_query_ = receive_message($contact,$username,$last_query,$str_array); //返回对方上一次query时间
				$_SESSION['contacts'][$contact]['last_query_'] = $last_query_;
				if(empty($last_query_)){
					throw new Exception('falied to query last_query_');
				}
				ob_clean();
				echo json_encode(['success'=>true,'message'=>$str_array,'last_query_'=>$last_query_]);
				exit;
			default:
				throw new Exception('unknown error');
		}
	}catch(Exception $e){
		ob_clean();
		echo json_encode(['success'=>false,'error'=>$e->getMessage()]);
		exit;
	}
?>