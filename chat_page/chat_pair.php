<?php
	//每一次刷新此页面都要加载所有聊天历史记录，如果有客户端app 可以在客户端缓存这些历史记录，并由客户端渲染出这些记录
	session_start();
	require_once('function/chat_fns.php');
	if(!session_check(['username','contacts','contact'],'valid_user' || $_SESSION['contacts'] == 'none')){
		require_once('illegal_access.php');
		exit;
	}

	$username = $_SESSION['username'];
	$contact = $_SESSION['contact'];
	$message_sent = null;
	$message_receive = null;
	try{
		$db = connect_database();
		query_record_all($db,$username,$contact,$message_sent);
		//原子操作
		if($db->query('start transaction') === false){
			throw new Exception('failed to start transaction');
		}
		$time = time();
		set_query_time($db,$username,$contact,$time);
		$_SESSION['contacts'][$contact]['last_query'] = $time; //在修改数据库set_query_time的同时，不要忘了SESSION中的缓存
		query_record_all($db,$contact,$username,$message_receive);
		$db->query('commit');
	}catch(Exception $e){
		$db->query('rollback');
		require_once('../function/html_page_fns.php');
		title('error','Error');
		//echo nl2br($e->getMessage()); //用于测试
		echo nl2br('something went wrong.');
		footer();
		exit;
	}
	$db->close();
?>
<!DOCTYPE html>
<html>
    <head>
        <title>chatting</title>
        <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
        <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap-theme.min.css">
        <style>
            .bubble-recv
            {
              position: relative;
              width: 330px;
              height: 75px;
              padding: 10px;
              background: #AEE5FF;
              -webkit-border-radius: 10px;
              -moz-border-radius: 10px;
              border-radius: 10px;
              border: #000000 solid 1px;
              margin-bottom: 10px;
            }
            
            .bubble-recv:after 
            {
              content: '';
              position: absolute;
              border-style: solid;
              border-width: 15px 15px 15px 0;
              border-color: transparent #AEE5FF;
              display: block;
              width: 0;
              z-index: 1;
              left: -15px;
              top: 12px;
            }
            
            .bubble-recv:before 
            {
              content: '';
              position: absolute;
              border-style: solid;
              border-width: 15px 15px 15px 0;
              border-color: transparent #000000;
              display: block;
              width: 0;
              z-index: 0;
              left: -16px;
              top: 12px;
            }
                        
            .bubble-sent
            {
              position: relative;
              width: 330px;
              height: 75px;
              padding: 10px;
              background: #00E500;
              -webkit-border-radius: 10px;
              -moz-border-radius: 10px;
              border-radius: 10px;
              border: #000000 solid 1px;
              margin-bottom: 10px;
            }
            
            .bubble-sent:after 
            {
              content: '';
              position: absolute;
              border-style: solid;
              border-width: 15px 0 15px 15px;
              border-color: transparent #00E500;
              display: block;
              width: 0;
              z-index: 1;
              right: -15px;
              top: 12px;
            }
            
            .bubble-sent:before 
            {
              content: '';
              position: absolute;
              border-style: solid;
              border-width: 15px 0 15px 15px;
              border-color: transparent #000000;
              display: block;
              width: 0;
              z-index: 0;
              right: -16px;
              top: 12px;
            }
            
            .spinner {
              display: inline-block;
              opacity: 0;
              width: 0;
            
              -webkit-transition: opacity 0.25s, width 0.25s;
              -moz-transition: opacity 0.25s, width 0.25s;
              -o-transition: opacity 0.25s, width 0.25s;
              transition: opacity 0.25s, width 0.25s;
            }
            
            .has-spinner.active {
              cursor:progress;
            }
            
            .has-spinner.active .spinner {
              opacity: 1;
              width: auto; 
            }
            
            .has-spinner.btn-mini.active .spinner {
              width: 10px;
            }
            
            .has-spinner.btn-small.active .spinner {
              width: 13px;
            }
            
            .has-spinner.btn.active .spinner {
              width: 16px;
            }
            
            .has-spinner.btn-large.active .spinner {
              width: 19px;
            }
            
            .panel-body {
              padding-right: 35px;
              padding-left: 35px;
            }
            
			.center{
				text-align:center;
			}
			
			.right{
				text-align:right;
			}
        </style>
    </head>
    <body>
    <h1 style="text-align:center">Chatting</h1>
    <div class="container">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h2 class="panel-title"><?php echo "Chatting with <strong>".$contact.'</strong>' ?></h2>
            </div>
            <div class="panel-body" id="chatPanel">
			<?php
				$last_query_ = $_SESSION['contacts'][$contact]['last_query_'];
				function zero($num){
					if($num < 10) return '0'.$num;
					return $num;
				}
				$i_rec = 0;
				$i_send = 0;
				$n_rec = (empty($message_receive) ? 0 : count($message_receive));
				$n_send = (empty($message_sent) ? 0 : count($message_sent));
				$send = true; // true : send	false : receive
				while($i_rec != $n_rec || $i_send != $n_send){
					if($i_rec == $n_rec){
						$send = true;
					}else if($i_send == $n_send){
						$send = false;
					}else{
						if($message_receive[$i_rec]['date_created'] < $message_sent[$i_send]['date_created']){
							$send = false;
						}else{
							$send = true;
						}
					}
					if($send){
						$time = getdate($message_sent[$i_send]['date_created']);
						//echo $time[0].' and '.$last_query_.'</br>';
						$timestr = $time['year'].'-'.$time['mon'].'-'.$time['mday'].'	'.zero($time['hours']).':'.zero($time['minutes']).':'.zero($time['seconds']);
						$readstr = ($time[0] > $last_query_ ? '<strong class="notRead"> (Not Read) </strong>' : '<strong> (Read) </strong>');
						echo '<div class="right">'.$timestr.$readstr.'</div>
						<div class="row bubble-sent pull-right">'.$message_sent[$i_send++]['message'].'</div>
						<div class="clearfix"></div>';
					}else{
						$time = getdate($message_receive[$i_rec]['date_created']);
						$timestr = $time['year'].'-'.$time['mon'].'-'.$time['mday'].'	'.zero($time['hours']).':'.zero($time['minutes']).':'.zero($time['seconds']);
						echo '<div>'.$timestr.'</div>
						<div class="row bubble-recv">'.$message_receive[$i_rec++]['message'].'</div>
						<div class="clearfix"></div>';
					}
				}
			?>
			<div class="center">----------------------------------History Messages----------------------------------</div>
            </div>
            <div class="panel-footer">
                <div class="input-group">
                    <input rows="3" type="text" class="form-control" id="chatMessage" placeholder="Send a message here..." />
                    <span class="input-group-btn">
                        <button id="sendMessageBtn" class="btn btn-primary has-spinner" type="button">
                            <span class="spinner"><i class="icon-spin icon-refresh"></i></span>
                            Send
                        </button>
                    </span>
                </div>
            </div>
        </div>
    </div>
	<hr/>
	<p><strong><a href='chat_homepage.php'>Quit Chatting</a></strong></p>
    <script src="//code.jquery.com/jquery-2.2.4.min.js"></script>
    <script src="chat_pair.js"></script>
    </body>
</html>