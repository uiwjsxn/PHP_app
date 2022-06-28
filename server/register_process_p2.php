<?php
	session_start();
	require_once('../function/server_fns.php');
	require_once('../function/html_page_fns.php');
	if(!session_check(['verified','email_code','email','username','passwd'],'register')){
		require_once('../front_page/illegal_access.php');
		exit;
	}
	
	$email = $_SESSION['email'];
	$username = $_SESSION['username'];
	$password = $_SESSION['passwd'];
	try{
		if(!check_email_existence($email)){
			throw new Exception("failed to register now");
		}
		if(!insert_user($username,$password,$email)){
			throw new Exception("failed to register now, try again later");
		}
	}catch(Exception $e){
		title('error','Error');
		echo nl2br($e->getMessage());
		footer();
		exit;
	}	
	title("register success","Register Success");
	echo "you have completed the registration, you can log in now";
	echo "<p><a href='../front_page/login.php'>Log In</a></p>";
	footer();
	session_unset();
	session_destroy();
?>