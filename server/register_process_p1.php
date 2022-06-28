<?php
	header('Content-Type: application/json'); //发送json数据一定不能漏了header
	session_start();
	ob_start();
	require_once('../function/server_fns.php');
	if(!post_check(['username','passwd','passwd2'])){
		echo json_encode(['success'=>false,'error'=>'illegal access']);
		exit;
	}
	
	$pass = true;
	$username = $_POST['username'];
	$passwd = $_POST['passwd'];
	$passwd2 = $_POST['passwd2'];
	try{
		if(strcmp($passwd,$passwd2) != 0 || !check_password($passwd)){
			$pass = false;
		}
		if($pass && !check_user_existence($username)){ 
			$pass = false;
		}
	}catch(Exception $e){
		ob_clean();
		echo json_encode(['success'=>false,'error'=>$e->getMessage()]);
		exit;
	}
	ob_clean();
	if($pass){
		echo json_encode(['success'=>true,'pass'=>true]);
		state_change('register');  //state_change会执行session_unset 故因在新的session值加入前先执行
		$_SESSION['username'] = $username;
		$_SESSION['passwd'] = $passwd;
	}else{
		echo json_encode(['success'=>true,'pass'=>false]);
	}
?>