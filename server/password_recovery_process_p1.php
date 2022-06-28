<?php
	header('Content-Type: application/json');
	ob_start();
	session_start();
	require_once('../function/server_fns.php');
	if(!post_check(['username'])){
		echo json_encode(['success'=>false,'error'=>'illegal access']);
		exit;
	}
	
	$username = $_POST['username'];
	$pass = true;
	try{
		if(check_user_existence($username)){ //通过表示用户不存在
			$pass = false;
		}
	}catch(Exception $e){
		ob_clean();
		echo json_encode(['success'=>false,'error'=>$e->getMessage()]);
		exit;
	}
	
	ob_clean();
	if($pass){
		state_change('password_recovery');
		$_SESSION['username'] = $username;
		echo json_encode(['success'=>true,'pass'=>true]);
	}else{
		echo json_encode(['success'=>true,'pass'=>false]);
	}
?>