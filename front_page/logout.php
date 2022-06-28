<?php
	session_start();
	require_once('../function/server_fns.php');
	require_once('../function/html_page_fns.php');
	if(!session_check(['username'],'valid_user')){
		require_once('illegal_access.php');
		exit;
	}
	
	title('log out','log out');
	echo '<h3>you have logged out</h3>';
	footer();
	
	session_unset();
	session_destroy();
?>