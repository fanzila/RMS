<?php

class Hmw {

	public function getParam($param) 
	{
		$CI = & get_instance(); 
		$CI->load->database();
		$req_params = "SELECT `val` FROM params WHERE `key` = '" . $param . "' LIMIT 1 ";
		$res_params = $CI->db->query($req_params);
		$r = $res_params->result();	
		return $r[0]->val;
	}

	public function getUser($id) 
	{
		$CI = & get_instance(); 
		$CI->load->database();
		$req_params = "SELECT * FROM users WHERE `id` = " . $id . " LIMIT 1 ";
		$res_params = $CI->db->query($req_params);
		$r = $res_params->result();	
		return $r[0];
	}

	public function getUsers() 
	{
		$CI = & get_instance(); 
		$CI->load->database();
		$req_params = "SELECT * FROM users WHERE active =1";
		$res_params = $CI->db->query($req_params);
		return $res_params->result();	
	}
		
	public function sendEmail($param) {

		require_once __DIR__.'/../libraries/mandrill/Mandrill.php'; //Not required with Composer
		$mandrill = new Mandrill('OZe7-oEYxORoEOWEUHzM2g');
	}


	public function sendNotif($msg) {

		$address	= $this->getParam('pushover_address');
		$token		= $this->getParam('pushover_token');
		$user		= $this->getParam('pushover_user');

		curl_setopt_array(
			$ch = curl_init(), array(
				CURLOPT_URL => $address,
				CURLOPT_POSTFIELDS => array(
					"token" => $token,
					"user" => $user,
					"message" => $msg,
					)
				)
			);
		curl_exec($ch);
		curl_close($ch);
	}
	
	public function keyLogin() {

		$CI = & get_instance(); 
		$CI->load->library('ion_auth');
		$CI->load->library('hmw');
		$CI->load->library('session');	

		$getkey = $CI->input->get('keylogin');

		if(!empty($getkey)) {
			$keyl = $this->getParam('keylogin');
			if($getkey == $keyl) {
				
				$user = $this->getParam('keylogin_user');
				$pass = $this->getParam('keylogin_pass');
				$newdata = array('keylogin'  => TRUE);
				$CI->session->set_userdata($newdata);
				
				$CI->ion_auth->login($user, $pass, true);
			}

		} else {

			if (!$CI->ion_auth->logged_in())
			{
				redirect('auth/login');
			}
		}
	}

}
?>