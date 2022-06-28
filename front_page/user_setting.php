<?php
	session_start();
	require_once('../function/server_fns.php');
	if(!session_check(['username'],'valid_user')){
		require_once('illegal_access.php');
		exit;
	}
	
	require_once('../function/html_page_fns.php');
	title_valid_user('user setting','User Setting');
?>

<p><a href='email_reset_p1.php'>Reset Email</a></p>
<p><a href='password_reset.php'>Reset Password</a></p>
	
<?php	footer(); ?>