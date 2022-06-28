<?php
	session_start();
	require('../function/html_page_fns.php');
	require('../function/server_fns.php');
	if(!session_check(['username','passwd'],'register')){
		require_once('../front_page/illegal_access.php');
		exit;
	}
	
	require_once('../function/html_page_fns.php');
	title('register','Register');
	register_form_p2();
?>

<script src="js/register_p2.js"></script>
<?php footer() ?>
