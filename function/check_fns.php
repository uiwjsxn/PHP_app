<?php
	function session_check($sessions,$state){ //输入各种名称 $sessions为数组
		if($state != 'nostate' && (empty($_SESSION['state']) || $_SESSION['state'] != $state)){
			return false;
		}
		foreach($sessions as $item){
			if(empty($_SESSION[$item])){
				return false;
			}
		}
		return true;
	}
	
	function post_check($post){
		foreach($post as $item){
			if(empty($_POST[$item])){
				return false;
			}
			$_POST[$item] = htmlspecialchars($_POST[$item]);
		}
		return true;
	}
	
	function state_change($string){
		session_unset();
		$_SESSION['state'] = $string;
	}
?>