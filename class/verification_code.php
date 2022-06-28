<?php
	class Code{
		private $expire_time;
		private $next_code_time;
		private $left_times;
		private $code;
		private $email;
		public function generate_code(){
			$array = ['a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l','m', 'n', 'o', 'p', 'q', 'r', 's', 
			't', 'u', 'v', 'w', 'x', 'y','z', 'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L','M', 'N', 'O', 
			'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y','Z', '0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];
			shuffle($array);
			$code_array = array_rand($array,6);
			$code = '';
			foreach($code_array as $key){
				$code .= $array[$key];
			}
			return $code;
		}
		public function __construct($post_email){
			$this->code = $this->generate_code();
			$this->expire_time = time()+5*60;//验证码有效期5分钟
			$this->next_code_time = time()+60;//1分钟后才能发送下一个验证码
			$this->left_times = 3;//每个验证码最多允许验证3次，输错2次，即告作废，作废时应清除session
			$this->email = $post_email;
		}
		public function check_code($str,$post_email){
			if($post_email == $this->email && $this->left_times-- > 0 && $str == $this->code && $this->expire_time > time()){ //$this->left_times--放在开头，否则其他条件失败后此语句不会执行
				return true;
			}
			return false;
		}
		public function check_valid(){
			return (($this->left_times > 0 && $this->expire_time > time()) ? true : false);
		}
		public function whether_next_code(){
			return time() > $this->next_code_time;
		}
		public function get_code(){
			return $this->code;
		}
	};
?>