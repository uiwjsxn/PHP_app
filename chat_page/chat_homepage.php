<?php
	require_once('function/chat_fns.php');
	require_once('function/chat_page_fns.php');
	session_start();
	if(!session_check(['username'],'valid_user')){
		require_once('illegal_access.php');
		exit;
	}
	
	$username = $_SESSION['username'];
	$newContacts = null;
	$db = connect_database();
	try{
		if(empty($_SESSION['contacts']) || $_SESSION['contacts'] = 'none'){
			$_SESSION['contacts'] = null;
			query_relation_send($db,$_SESSION['contacts'],$username);
			query_relation_receive($db,$_SESSION['contacts'],$username);
		}
		query_request_contact($db,$username,$newContacts);
	}catch(Exception $e){
		require_once('../function/html_page_fns.php');
		title('error','Error');
		echo nl2br($e->getMessage());
		footer();
		exit;
	}
	$db->close();
	if(empty($_SESSION['contacts'])) $_SESSION['contacts'] = 'none';
	title_valid_user_chat('chat homepage',"Chat Homepage");
?>
		<hr/>
		<script src="chat_homepage.js"></script>
		<p><strong>Contact List</strong></p>
		<table>
		<tr>
			<th>Username</th> <th>Alias</th> <th>New Message</th> <th>Button</th>
		</tr>
			<?php
				if($_SESSION['contacts'] != 'none'){
					$contacts = &$_SESSION['contacts'];
					foreach($contacts as $key=>$value){
						echo"<tr><td>$key</td> 
						<td>".$value['alias']."</td> 
						<td>".(is_new_message($value['last_query'],$value['last_send_']) ? 'yes' : 'no')."</td>
						<td><button id='$key' class='chat' type='button'>Chat</button></td></tr>"; //当按钮按下时，通过id='$key'可判断是哪个按钮被按下
					}
				}
			?>
		</table>
		<br>
		<hr>
		<p><strong>New Contact</strong></p>
		<table>
			<?php
				if(empty($newContacts)){
					echo "<tr><td>No new contact request</td></tr> ";
				}else{
					echo '<tr>
							<th>Username</th> <th>Alias Setting</th> <th>Accept</th> <th>Decline</th>
						</tr>';
					foreach($newContacts as $value){ //同一个人可能发送多个请求
						echo"<tr class='$value'><td>$value</td> 
						<td><input type='text' class='new_contact' placeholder='enter alias if you accept' /></td>
						<td><button class='accept' type='button'>Accept</button></td> 
						<td><button class='decline' type='button'>Decline</button></td></tr>"; //当按钮按下时，通过id='$key'可判断是哪个按钮被按下
					}
				}
			?>
		</table>
		<br>
		<hr>
<?php footer_chat(); ?>