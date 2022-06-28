<?php
	require_once('function/chat_fns.php');
	require_once('function/chat_page_fns.php');
	session_start();
	if(!session_check(['username','contacts'],'valid_user')){
		require_once('illegal_access.php');
		exit;
	}
	title_valid_user_chat('contact setting',"Contact Setting");
?>
		<hr/>
		<script src="chat_setting.js"></script>
		<p><strong>Contact Management</strong></p>
		<table>
			<?php
				if($_SESSION['contacts'] == 'none'){
					echo "<tr><td>No contact</td></tr> ";
				}else{
					$contacts = &$_SESSION['contacts'];
					echo '<tr>
							<th>Username</th> <th>Alias</th> <th>Modify Alias</th> <th>Button</th>
						</tr>';
					foreach($contacts as $key=>$value){ 
						echo "<tr id='$key'><td>$key</td> 
						<td>".$value['alias']."</td> 
						<td><button class='alias' type='button' >Modify Alias</button></td> 
						<td><button class='delete' type='button'>Delete</button></td></tr>"; //当按钮按下时，通过id='$key'可判断是哪个按钮被按下
					}
				}
			?>
		</table>
		<br/>
		<hr>
		<p><strong>Contact Adding</strong></p>
		<table>
			<tr>
				<th>Username</th> <th>Alias</th>  <th>Button</th>
			</tr>
			<tr>
				<td><input type="text" class="info1" placeholder="username" maxlength="48" required /></td> 
				<td><input type="text" class="info2" placeholder="alias if needed" maxlength="48" /></td> 
				<td><button type='button' class='add_button'>Send</button></td>
			</tr>
		</table>
<?php footer_chat(); ?>