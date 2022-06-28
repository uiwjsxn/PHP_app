<?php
	session_start();
	require_once('../function/server_fns.php');
	require_once('../function/html_page_fns.php');
	if(!session_check(['username','verified','matched'],'password_recovery') || !post_check(['new_passwd2','new_passwd'])){
		require_once('../front_page/illegal_access.php');
		exit;
	}
	
	$new_passwd = $_POST['new_passwd'];
	$new_passwd2 = $_POST['new_passwd2'];
	$username = $_SESSION['username'];
	try{
		if(strcmp($new_passwd,$new_passwd2) != 0 || !check_password($new_passwd)){
			throw new Exception("wrong password setting");
		}
		if(!update_password($username,$new_passwd)){
			throw new Exception("Can not reset password now, try again later");
		}
	}catch(Exception $e){
		echo nl2br($e->getMessage());
		footer();
		session_unset();
		session_destroy();
		exit;
	}
	title('password recovered','Password Recovered');
	echo '<p>You have successfully recovered your password, you can log in now.</p>';
	echo "<p><a href='../front_page/login.php'>Log In</a></p>";
	footer();
	session_unset();
	session_destroy();
?>
	