<?php
	session_start();
	require_once('../function/server_fns.php');
	if(!session_check(['username'],'valid_user') || !post_check(['old_passwd','new_passwd','new_passwd2'])){
		require_once('../front_page/illegal_access.php');
		exit;
	}
	
	require_once('../function/server_fns.php');
	require_once('../function/html_page_fns.php');
	$username = $_SESSION['username'];
	$old_passwd = $_POST['old_passwd'];
	$new_passwd = $_POST['new_passwd'];
	$new_passwd2 = $_POST['new_passwd2'];
	try{
		if(!match_user_password($username,$old_passwd)){
			throw new Exception('Wrong old password');
		}
		if(strcmp($new_passwd,$new_passwd2) != 0 || !check_password($new_passwd)){
			throw new Exception("Wrong password setting");
		}
		if(!update_password($username,$new_passwd)){
			throw new Exception("Can not reset password now, try again later");
		}
	}catch(Exception $e){
		title('error','Error');
		echo nl2br($e->getMessage());
		footer();
		exit;
	}
	title('password reset success','Password Reset Success');
	echo 'You have successfully reset your password, you can log in now.';
	echo "<p><a href='../front_page/login.php'>Log In</a></p>";
	session_unset();
	session_destroy();
	footer();
?>