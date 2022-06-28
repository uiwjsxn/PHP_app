<?php
	header('Content-Type: application/json');
	ob_start();
	session_start();
	require_once('../function/server_fns.php');
	if(!post_check(['username','passwd'])){
		echo json_encode(['success'=>false,'error'=>'illegal access']);
	}
	
	$username = trim($_POST['username']);
	$password = trim($_POST['passwd']);
	$pass = true;
	try{
		$pass = (match_user_password($username,$password) ? true : false);
	}catch(Exception $e){
		ob_clean();
		echo json_encode(['success'=>false,'error'=>$e->getMessage()]);
		exit;
	}
	if($pass){
		state_change('valid_user'); //成功登陆的用户以valid_user区分,进入valid_user state
		$_SESSION['username'] = $username; 
		echo json_encode(['success'=>true,'pass'=>true]);
	}else{
		echo json_encode(['success'=>true,'pass'=>false]);
	}
?>