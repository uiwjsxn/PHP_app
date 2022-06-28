<?php 	
	function title_valid_user_chat($title,$header){
		?>
		<!DOCTYPE html>
		<html>
		<head>
			<title><?php echo $title; ?></title>
			<link href="../data/styles.css" type="text/css" rel="stylesheet">
			<style type="text/css">
				table, th, td {
				border-collapse: collapse;
				border: 1px solid black;
				padding: 6px;
				}
	
				th {
					background: #ccccff;      
				}
			</style>
			<script src="//code.jquery.com/jquery-2.2.4.min.js"></script>
		</head>
		<body>

		<!-- page header -->
		<header>    
			<img src="../data/logo.gif" alt="logo" height="70" width="70" /> 
			<h1><?php echo $header; ?></h1>
		</header>

		<!-- menu -->
		<nav>
			<div class="menuitem">
				<a href="../front_page/homepage.php">
				<img src="../data/s-logo.gif" alt="" height="20" width="20" /> 
				<span class="menutext">Home</span>
				</a>
			</div>

			<div class="menuitem">
				<a href="../front_page/user_setting.php">
				<img src="../data/s-logo.gif" alt="" height="20" width="20" />  
				<span class="menutext">User Setting</span>
				</a>
			</div>


			<div class="menuitem">
				<a href="../front_page/logout.php">
				<img src="../data/s-logo.gif" alt="" height="20" width="20" /> 
				<span class="menutext">Log Out</span>
				</a>
			</div>
			
			<div class="menuitem">
				<a href="chat_homepage.php">
				<img src="../data/s-logo.gif" alt="" height="20" width="20" /> 
				<span class="menutext">Chat Homepage</span>
				</a>
			</div>
			
			<div class="menuitem">
				<a href="chat_setting.php">
				<img src="../data/s-logo.gif" alt="" height="20" width="20" /> 
				<span class="menutext">Contact Setting</span>
				</a>
			</div>
			
			<div class="menuitem">
				<span class="menutext">User: <?php echo $_SESSION['username']; ?></span>
			</div>
		</nav>

		<!-- page content -->
		<?php
	}

	function footer_chat(){
		?>
			<footer>
			</footer>
			</body>
		</html>
		<?php
	}