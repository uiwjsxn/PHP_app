<?php
	header('Content-Type: application/json');
	session_start();
	ob_start();
	require_once('../function/chat_fns.php');
	if(!session_check(['username','contacts'],'valid_user') || !post_check(['type_homepage','contact'])){
		echo json_encode(['success'=>false,'error'=>'illegal access']);
		exit;
	}
	
	$type = $_POST['type_homepage'];
	$contact = $_POST['contact'];
	$username = $_SESSION['username'];
	try{
		switch($type){
			case 'chat':
				$_SESSION['contact'] = $contact;
				ob_clean();
				echo json_encode(['success'=>true]);
				exit;
			case 'accept':
				$alias = (empty($_POST['contact']) ? $username : $_POST['contact']);
				accept_contact($username,$contact,$alias);
				if($_SESSION['contacts'] == 'none'){
					$_SESSION['contacts'] = null;
				}
				$_SESSION['contacts'][$contact] = ['alias'=>$alias,'last_query'=>1,'last_send'=>0,'last_query_'=>1,'last_send_'=>0]; //确保当前is_new_message为false
				echo json_encode(['success'=>true]);
				exit;
			case 'decline':
				decline_contact($username,$contact);
				echo json_encode(['success'=>true]);
				exit;
		}
	}catch(Exception $e){
		ob_clean();
		echo json_encode(['success'=>false,'error'=>$e->getMessage()]);
		exit;
	}
?>