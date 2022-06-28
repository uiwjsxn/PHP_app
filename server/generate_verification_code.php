<?php
	//从前端的post获得邮箱地址，向邮箱发送verification code
	header('Content-Type: application/json');
	//所有可能出现的错误信息或反馈信息（如发送邮件时stmp服务器与本地交互的命令行信息）全部输入缓冲区中，在真正要输出json格式数据前，将缓冲区可能存在的错误信息编入json格式中或清空缓存区，
	//之后才输出json格式至缓冲区。当页面结束时，缓存区中的json内容会自动发送到浏览器。
	//这样避免打乱echo json_encode 中的json格式，因此在所有的返回json格式的.php中都应该开启 ob_start()
	ob_start(); 
	session_start();
	require_once('../class/verification_code.php');
	require_once('../function/server_fns.php');
	if(!post_check(['email']) || empty($_SESSION['username'])){ //必须在会话中提交了用户名username和 POST email地址 的用户才能访问本页
		echo json_encode(['success'=>false,'error'=>'illegal access']);
		exit;
	}
	
	$email = $_POST['email'];
	$pass = true;
	try{
		if(!check_email($email)){
			$pass = false;
		}
		if($pass && !empty($_SESSION['email_code'])){
			$email_code = unserialize($_SESSION['email_code']);
			if($email_code->whether_next_code() == false){
				$pass = false;
			}
		}
		if($pass){
			$new_code = new Code($email);
			$_SESSION['email_code'] = serialize($new_code);
			$code = $new_code->get_code();
			if(!generate_verification_email($email,$code)){
				throw new Exception("failed to send email, try again later");
			}
		}
	}catch(Exception $e){
		ob_clean(); //清除缓冲区中可能的反馈信息，以免打乱json格式。也可将先将此取出，清空缓冲区后再保存至json_encode返回给浏览器
		echo json_encode(['success'=>false,'error'=>$e->getMessage()]);
		exit;
	}
	ob_clean();
	//success: 表示程序运行没有出现异常， pass：验证通过
	echo ($pass ? json_encode(['success'=>true,'pass'=>true]) : json_encode(['success'=>true,'pass'=>false]) );
?>