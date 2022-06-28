<?php
	session_start();
	require_once('../function/server_fns.php');
	require_once('../function/html_page_fns.php');
	if(!session_check(['username'],'password_recovery')){
		require_once('illegal_access.php');
		exit;
	}
	
	title('password recovery','Password Recovery');
	password_recovery_form_p2();
 ?>
 
<script src="js/password_recovery_p2.js"></script>
<?php footer() ?>