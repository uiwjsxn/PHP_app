<?php
	session_start();
	require_once('../function/server_fns.php');
	require_once('../function/html_page_fns.php');
	if(!session_check(['username'],'valid_user')){
		require_once('illegal_access.php');
		exit;
	}
	
	title_valid_user('homepage','Homepage');
?>
<hr>

<h3>this is your homepage</h3>
<p><a href='../chat_page/chat_homepage.php'>Chat</a></p>
<?php	footer(); ?>