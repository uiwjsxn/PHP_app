<?php
	session_start();
	require_once('../function/server_fns.php');
	if(!session_check(['username'],'valid_user')){
		require_once('illegal_access.php');
		exit;
	}
	
	require_once('../function/html_page_fns.php');
	title('reset password','Reset Password');
	password_reset_form();
?>
<hr>
<strong><a href='homepage.php'>back</a></strong>
<script src="js/password_reset.js"></script>
<?php	footer(); ?>