<?php
	function title($title,$header){
		?>
		<!doctype html>
		<html>
		<head>
			<meta charset="utf-8">
			<title><?php echo $title; ?></title>
			<style>
			body { font-family: Arial, Helvetica, sans-serif; font-size: 13px }
			li, td { font-family: Arial, Helvetica, sans-serif; font-size: 13px }
			hr { color: #3333cc;}
			a { color: #000 }
			div.formblock
				{ background: #ccc; width: 300px; padding: 6px; border: 1px solid #000;}
			</style>
		</head>
		<body>
			<script src="//code.jquery.com/jquery-2.2.4.min.js"></script>
			<div>
				<img src="http://127.0.0.1:8887/data/bookmark.gif" alt="Logo" height="55" width="57" style="float: left; padding-right: 6px;" />
				<h1><?php echo $header; ?></h1>
			</div>
		<hr />
		<?php
	}
	
	function title_valid_user($title,$header){
		?>
		<!DOCTYPE html>
		<html>
		<head>
			<title><?php echo $title; ?></title>
			<link href="../data/styles.css" type="text/css" rel="stylesheet">
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
				<a href="homepage.php">
				<img src="../data/s-logo.gif" alt="" height="20" width="20" /> 
				<span class="menutext">Home</span>
				</a>
			</div>

			<div class="menuitem">
				<a href="user_setting.php">
				<img src="../data/s-logo.gif" alt="" height="20" width="20" />  
				<span class="menutext">User Setting</span>
				</a>
			</div>

			<div class="menuitem">
				<a href="logout.php">
				<img src="../data/s-logo.gif" alt="" height="20" width="20" /> 
				<span class="menutext">Log Out</span>
				</a>
			</div>
			
			<div class="menuitem">
				<span class="menutext">User: <?php echo $_SESSION['username']; ?></span>
			</div>
		</nav>

		<!-- page content -->
		<?php
	}
	
	function footer(){
		?>
			<footer>
			</footer>
			</body>
		</html>
		<?php
	}
	
	function login_form(){
		?>
		<p><a href="register_p1.php">Not a member?</a></p>
		<form method="post" action="../server/login_process.php">
			<div class="formblock">
				<h2>Members Log In Here</h2>
			
				<p><label for="username">Username:</label><br/>
				<input type="text" name="username" id="username" disabled='disabled' required /></p>

				<p><label for="passwd">Password:</label><br/>
				<input type="password" name="passwd" id="passwd" disabled='disabled' required /></p>

				<button type="submit" disabled='disabled' >Log In</button>

				<p><a href="password_recovery_p1.php">Forgot your password?</a></p>
			</div>
		</form>
		<?php
	}
	
	function register_form_p1(){
		?>
		<form method="post" action="../server/register_process_p1.php">
			<div class="formblock">
			<h2>Register Now</h2>

			<p id='p1'><label for="username">Preferred Username <br>(max 48 chars):</label><br/>
			<input type="text" name="username" id="username" disabled='disabled'
			size="20" maxlength="48" required /></p>

			<p id='p2'><label for="passwd">Password <br>(between 8 and 50 chars,contain numbers,characters and special chars):</label><br/>
			<input type="password" name="passwd" id="passwd" disabled='disabled'
			size="20" maxlength="50" required /></p>

			<p id='p3'><label for="passwd2">Confirm Password:</label><br/>
			<input type="password" name="passwd2" id="passwd2" disabled='disabled'
			size="20" maxlength="50" required /></p>

			<button type="submit" disabled='disabled'>Next</button>
			</div>
		</form>
		<?php
	}
	
	function register_form_p2(){
		?>
		<form method="post" action="../server/register_process_p2.php">
			<div class="formblock">
			<h2>Register Now</h2>

			<p><label for="email">Email Address:</label><br/>
			<input type="email" name="email" id="email" 
			size="0" maxlength="98" disabled='disabled' required /></p>
			
			<p><label for="verification_code">Verification Code:</label><br/>
			<input type="text" name="verification_code" id="verification_code" 
			size="6" maxlength="6" disabled='disabled' required /><button id="send_button" type="button" disabled='disabled'>Send</button></p>
			
			<button id="submit_button" type="submit" disabled='disabled'>Register</button>
			</div>
		</form>
		<?php
	}
	
	function password_reset_form(){
		?>
		 <form action="../server/password_reset_process.php" method="post">
			<div class="formblock">
				<h2>Change Password</h2>

				<p><label for="old_passwd">Old Password:</label><br/>
				<input type="password" name="old_passwd" id="old_passwd" 
				size="20" maxlength="48" disabled='disabled' required /></p>

				<p id='p2'><label for="passwd2">New Password:</label><br/>
				<input type="password" name="new_passwd" id="new_passwd" 
				size="20" maxlength="48" disabled='disabled' required /></p>

				<p><label for="passwd2">Repeat New Password:</label><br/>
				<input type="password" name="new_passwd2" id="new_passwd2" 
				size="20" maxlength="48" disabled='disabled' required /></p>

				<button type="submit" disabled='disabled'>Change Password</button>
			</div>
		</form>
		<?php
	}
	
	function email_reset_form_p1(){
		?>
		 <form action="../server/email_reset_process_p1.php" method="post">
			<div class="formblock">
				<h2>Change Email</h2>

				<p><label for="old_email">Old Email:</label><br/>
				<input type="text" name="old_email" id="old_email" 
				size="20" maxlength="98" disabled='disabled' required /></p>
				
				<p><label for="verification_code">Verification Code:</label><br/>
				<input type="text" name="verification_code" id="verification_code" 
				size="6" maxlength="6" disabled='disabled' required /><button type="button" id='send_button' disabled='disabled'>Send</button></p>	

				<button type="submit" id='submit_button' disabled='disabled'>Next</button>
			</div>
		</form>
		<?php
	}
	
	function email_reset_form_p2(){
		?>
		 <form action="../server/email_reset_process_p2.php" method="post">
			<div class="formblock">
				<h2>Change Email</h2>

				<p><label for="new_email">New Email:</label><br/>
				<input type="text" name="new_email" id="new_email" 
				size="20" maxlength="98" disabled='disabled' required /></p>
				
				<p><label for="verification_code">Verification Code:</label><br/>
				<input type="text" name="verification_code" id="verification_code" 
				size="6" maxlength="6" disabled='disabled' required /><button type="button" id='send_button' disabled='disabled'>Send</button></p>	

				<button type="submit" id='submit_button' disabled='disabled'>Change Email</button>
			</div>
		</form>
		<?php
	}
	
	function password_recovery_form_p1(){
		?>
		<form action="../server/password_recovery_process_p1.php" method="post">
			<div class="formblock">
				<h2>Recover Password</h2>
				
				<p><label for="username">Username:</label><br/>
				<input type="text" name="username" id="username" 
				size="20" maxlength="48" disabled='disabled' required /></p>

				<button type="submit">Next</button>
			</div>
		</form>
		<?php
	}
	
	function password_recovery_form_p2(){
		?>
		<form action="../server/password_recovery_process_p2.php" method="post">
			<div class="formblock">
				<h2>Recover Password</h2>
				
				<p><label for="email">Email:</label><br/>
				<input type="text" name="email" id="email" 
				size="20" maxlength="98" disabled='disabled' required /></p>
				
				<p><label for="username">Verification Code:</label><br/>
				<input type="text" name="verification_code" id="verification_code" 
				size="6" maxlength="6" disabled='disabled' required /><button type="button" id='send_button' disabled='disabled' >Send</button></p>

				<button type="submit" id='submit_button' disabled='disabled' >submit</button>
			</div>
		</form>
		<?php
	}
	
	function password_recovery_form_p3(){
		?>
		<form action="../server/password_recovery_process_p3.php" method="post">
			<div class="formblock">
				<h2>Recover Password</h2>
				
				<p><label for="passwd2">New Password<br>(between 8 and 50 chars,contain numbers,characters and special chars):</label><br/>
				<input type="password" name="new_passwd" id="new_passwd" 
				size="20" maxlength="48" disabled='disabled' required /></p>

				<p><label for="passwd2">Repeat New Password:</label><br/>
				<input type="password" name="new_passwd2" id="new_passwd2" 
				size="20" maxlength="48" disabled='disabled' required /></p>

				<button type="submit" disabled='disabled' >submit</button>
			</div>
		</form>
		<?php
	}
?>