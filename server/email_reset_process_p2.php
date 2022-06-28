<?php
	header('Content-Type: application/json');
	session_start();
	ob_start();
	require_once('../function/server_fns.php');
	require_once('../class/verification_code.php');
	if(!session_check(['email_code','username','old_email_verified'],'valid_user') || !post_check(['email','code'])){
		echo json_encode(['success'=>false,'error'=>'illegal access']);
		exit;
	}
	//这里POST直接来自与html <form> <input>的name，不是来自于jQuery
	$post_code = $_POST['code'];
	$email = $_POST['email'];
	$email_code = unserialize($_SESSION['email_code']);
	$username = $_SESSION['username'];
	$pass = true;
	try{
		if(!$email_code->check_code($post_code,$email)){ //每一次check_code都会令Code对象的left_times减1，但这里只影响了$email_code，
		//无法影响到_SESSON['email_code']的对象，故要用新的$email_code对象覆盖掉$_SESSION中的Code对象，保留下修改对下次页面访问生效
			$pass = false;
			$_SESSION['email_code'] = serialize($email_code);
			if(!$email_code->check_valid()){
				session_unset();
				session_destroy();
			}
		}
		if($pass && !change_email($username,$email)){
			throw new Exception('failed to change email now, try again later');
		}
	}catch(Exception $e){ //认证不通过不算异常，此时success为true，表示程序正常运行，pass为false。只有程序故障才属于异常，此时success为false
		ob_clean();
		echo json_encode(['success'=>false,'error'=>$e->getMessage()]);
		exit;
	}
	if($pass){
		echo json_encode(['success'=>true,'pass'=>true]);
		session_unset();
		session_destroy();
	}else{
		echo json_encode(['success'=>true,'pass'=>false]);
	}
?>