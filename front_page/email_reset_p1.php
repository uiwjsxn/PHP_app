<?php
	session_start();
	require_once('../function/server_fns.php');
	require_once('../function/html_page_fns.php');
	if(!session_check(['username'],'valid_user')){
		require_once('illegal_access.php');
		exit;
	}
	
	title('reset email','Reset Email');
	email_reset_form_p1();
?>
<hr>
<strong><a href='homepage.php'>back</a></strong>
<script src="js/email_reset_p1.js"></script>
<?php	footer(); ?>