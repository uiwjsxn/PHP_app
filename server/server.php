<?php
//只提供一些不需要session认证的基本查询，可以公开的信息查询供jQuery使用，不提供如用户名密码匹配等需要session安全认证的的操作
	header('Content-Type: application/json');
	session_start();
	ob_start();
	require('../function/server_fns.php');
	if(!post_check(['type','data'])){
		echo json_encode(['success'=>false,'error'=>'illegal access']);
		exit;
	}
	
	$pass = true;
	$type = $_POST['type'];
	$data = $_POST['data']; //格式：在jQuery文件中POST data 和 type 至server.php
	try{
		switch($type){
			case 'username_query': //不存在返回true
				$pass = (check_user_existence($data) ? true : false);
				break;
			case 'email_query': //不存在返回true
				$pass = (check_email_existence($data) ? true : false);
				break;
			default:
				throw new Exception('unknown error');
		}
	}catch(Exception $e){
		ob_clean();
		echo json_encode(['success'=>false,'error'=>$e->getMessage()]);
		exit;
	}
	
	ob_clean();
	echo ($pass ? json_encode(['success'=>true,'pass'=>true]) : json_encode(['success'=>true,'pass'=>false]) );
?>