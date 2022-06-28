<?php
	header('Content-Type: application/json');
	session_start();
	ob_start();
	require_once('../function/chat_fns.php');
	if(!session_check(['username','contacts'],'valid_user') || !post_check(['type'])){
		echo json_encode(['success'=>false,'error'=>'illegal access']);
		exit;
	}
	$type = $_POST['type'];
	$username = $_SESSION['username'];
	$contact = $_POST['contact'];
	try{
		switch($type){
			case 'alias':
				if(!post_check('newAlias'))	throw new Exception('illegal access');
				modify_alias($username,$contact,$_POST['newAlias']);
				$_SESSION['contacts'][$contact]['alias'] = $_POST['newAlias'];
				ob_clean();
				echo json_encode(['success'=>true]);
				exit;
			case 'add':
				if($username == $contact){
					throw new Exception('Can not add yourself as a contact');
				}
				$alias = (empty($_POST['alias']) ? $contact : $_POST['alias']);
				add_contact_request($username,$contact,$alias);
				ob_clean();
				echo json_encode(['success'=>true]);
				exit;
			case 'delete':
				delete_contact($username,$contact);
				unset($_SESSION['contacts'][$contact]);
				if(empty($_SESSION['contacts'])) $_SESSION['contacts'] = 'none';
				ob_clean();
				echo json_encode(['success'=>true]);
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