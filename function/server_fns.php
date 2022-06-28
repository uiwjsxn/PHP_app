<?php
	require_once('check_fns.php');
	//这里面的函数绝对不能出现echo语句，因为这些函数可能被用于返回 json输出的 .php文件中，直接出现echo语句会打乱json格式
	function connect_database(){
		require('../data/database_user.php'); //这里不能require_once(), 因为在一个php页面中可能调用好几次数据库连接，
		//而require_once()只是在函数内部执行，引入的只是局部变量，在一个含有数据库连接的函数（如match_user_password）执行完后，
		//require_once()引入的局部变量$host,$user,$password,$database会被释放掉，而下一个函数（如update_password）执行时，
		//将无法require database_user.php文件（require_once()的限制）
		$db = new mysqli($host,$user,$password,$database);
		if(mysqli_connect_errno()){
			throw new Exception("failed to connect the database\n".mysqli_connect_error());
		}
		return $db;
	}
	//database user
	function match_user_password($userName,$password){
		$db = connect_database();
		$query = "select hash from users where username = ?";
		$stmt = $db->prepare($query);
		$stmt->bind_param('s',$userName);
		$stmt->execute();
		$stmt->store_result();
		$stmt->bind_result($hash);
		if($stmt->num_rows == 1){
			if($stmt->fetch() && password_verify($password,$hash)){
				$stmt->free_result();
				$db->close();
				return true;	
			}
		}
		$db->close();
		return false;
	}
	
	function match_user_email($userName,$email){
		$db = connect_database();
		$query = "select email from users where username = ? and email = ?";
		$stmt = $db->prepare($query);
		$stmt->bind_param('ss',$userName,$email);
		$stmt->execute();
		$stmt->store_result();
		if($stmt->num_rows == 1){
			$stmt->free_result();
			$db->close();
			return true;
		}
		$db->close();
		return false;
	}
	
	function check_user_existence($userName){ //不存在返回true
		$db = connect_database();
		$query = "select * from users where username = ?";
		$stmt = $db->prepare($query);
		$stmt->bind_param('s',$userName);
		$stmt->execute();
		$stmt->store_result();
		if($stmt->num_rows == 0){
			$db->close();
			return true;
		}
		$stmt->free_result();
		$db->close();
		return false;
	}
	
	function check_email_existence($email){ //在数据库中email栏应该被index,不存在返回true
		$db = connect_database();
		$query = "select * from users where email = ?";
		$stmt = $db->prepare($query);
		$stmt->bind_param('s',$email);
		$stmt->execute();
		$stmt->store_result();
		if($stmt->num_rows == 0){
			$db->close();
			return true;
		}
		$stmt->free_result();
		$db->close();
		return false;
	}
		
	function insert_user($userName,$password,$emailAddress){
		$db = connect_database();
		$query = "insert into users value(null,?,?,?)";
		$stmt = $db->prepare($query);
		$hash = password_hash($password,PASSWORD_DEFAULT);
		$stmt->bind_param('sss',$userName,$hash,$emailAddress);//故意在字符两边加上 . 防止sql注入,但$hash不可能成为sql语句，故不要加 .
		$stmt->execute();
		if($stmt->affected_rows <= 0){
			$db->close();
			throw new Exception("failed to add a user to the database\n".mysqli_connect_error());
		}
		$db->close();
		return true;
	}
	
	function delete_user($userName){
		$db = connect_database();
		$query = "delete from users where username = ?";
		$stmt = $db->prepare($query);
		$stmt->bind_param('s',$userName);
		$stmt->execute();
		if(!$stmt->affected_rows <= 0){
			$db->close();
			throw new Exception("failed to delete a user from the database\n".mysqli_connect_error());
		}
		$db->close();
		return true;
	}

	function update_password($userName,$password){
		$db = connect_database();
		$query = "update users set hash = ? where username = ?";
		$hash = password_hash($password,PASSWORD_DEFAULT);
		$stmt = $db->prepare($query);
		$stmt->bind_param('ss',$hash,$userName);
		$stmt->execute();
		if($stmt->affected_rows <= 0){
			$db->close();
			throw new Exception("failed to update user password from the database\n".mysqli_connect_error());
		}
		$db->close();
		return true;
	}
	
	function change_email($userName,$emailAddress){
		$db = connect_database();
		$query = "update users set email = ? where username = ?";
		$stmt = $db->prepare($query);
		$stmt->bind_param('ss',$emailAddress,$userName);
		$stmt->execute();
		if($stmt->affected_rows <= 0){
			$db->close();
			throw new Exception("failed to update user email address from the database\n".mysqli_connect_error());
		}
		$db->close();
		return true;
	}
	
	function check_password($password){
		if(strlen($password) > 8 || strlen($password) <= 50){
			if(preg_match('/[a-zA-Z]/',$password) === 1 && preg_match('/[\d]/',$password) === 1 && preg_match('/[\W]|[_]/',$password) === 1){
				return true;
			}
		}
		return false;
	}
	
	function check_email($email){ 
		if(strlen($email) >= 6){
			if(preg_match('%^[\w\-.]+@[a-zA-Z0-9][a-zA-Z0-9\-]*\.[a-zA-Z0-9][a-zA-Z0-9\-.]*$%',$email)){
				//echo 'valid email';
				return true;
			}
		}
		return false;
	}
	
	function generate_verification_email($to,$code){
		require '../PHPMailer/src/Exception.php';
		require '../PHPMailer/src/PHPMailer.php';
		require '../PHPMailer/src/SMTP.php';
		
		$subject = 'Verification Code';
		$message = 'Your verification is: '.$code.' \r\nvalid for 5 minutes.';
		$message_for_html = str_replace('\r\n','<br/>',$message);
		$mail = new PHPMailer\PHPMailer\PHPMailer(true); //true开启debug,PHPMailer\PHPMailer是namespace 见PHPMailer.php文件
		//From https://support.microsoft.com/en-us/office/pop-imap-and-smtp-settings-for-outlook-com-d088b986-291d-42b8-9564-9c414e2aa040
		try {
			//Server settings
			$mail->SMTPDebug = PHPMailer\PHPMailer\SMTP::DEBUG_SERVER;                      // Enable verbose debug output
			$mail->isSMTP();                                            // Send using SMTP
			$mail->Host       = 'smtp-mail.outlook.com';                    // Set the SMTP server to send through
			$mail->SMTPAuth   = true;                                   // Enable SMTP authentication
			$mail->Username   = 'ofasifslfso@outlook.com';                     // SMTP username
			$mail->Password   = 'fj3a$%89yf@=yhf72O';                               // SMTP password
			$mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;         // Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` encouraged or PHPMailer::ENCRYPTION_STARTTLS
			$mail->Port       = 587;                                    // TCP port to connect to, use 465 for `PHPMailer::ENCRYPTION_SMTPS` above
	
			//Recipients
			$mail->setFrom('ofasifslfso@outlook.com', 'verification_code');
			//$mail->addAddress('joe@example.net', 'Joe User');     // Add a recipient
			$mail->addAddress($to);               // Name is optional
			//$mail->addReplyTo('info@example.com', 'Information');
			//$mail->addCC('cc@example.com');
			//$mail->addBCC('bcc@example.com');

			// Attachments
			//$mail->addAttachment('/var/tmp/file.tar.gz');         // Add attachments
			//$mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name

			// Content
			$mail->isHTML(true);                                  // Set email format to HTML
			$mail->Subject = $subject;
			$mail->Body    = $message_for_html;
			$mail->AltBody = $message;                           //'如果邮件客户端不支持HTML则显示此内容

			$mail->send();
			//echo 'Message has been sent';
		} catch (Exception $e) {
			throw $e;
			//echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
			//return false;
		}
		return true;
	}
?>