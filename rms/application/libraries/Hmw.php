<?php

class Hmw {

	public function getParam($param) 
	{
		$CI = & get_instance(); 
		$CI->load->database();
		$CI->db->select('val')->from('params')->where('key')->limit(1);
        $res_params = $CI->db->get();
		$r = $res_params->result();	
		return $r[0]->val;
	}

	public function callBox($data) 
	{
	
		/** DEMO
		$_GET = array(
		'key'	=> $key,
		'order' => 'chacun', 
		'module' => 'switch', // or 'dimmer'
		'value' => 'on', // or '1' to '255'
		'id' => '2');

		$_GET = array(
		'key'	=> $key,
		'order' => 'sound', 
		'jingle' => '/home/pos/sncf2.mp3', 
		'type' => 'audio', //or 'text'
		'message' => '/home/pos/evening.mp3'); // or text : 'Good morning planet.'
		**/
		
		
		$CI = & get_instance(); 
		$CI->load->database();
		
		$jdata = json_encode($data);
		
		$ch = curl_init();
		$api_box_url = $this->getParam('api_box_url');
		$key = $this->getParam('keylogin');
		if($_GET['key'] != $key) exit('error B');

		curl_setopt($ch, CURLOPT_URL, $api_box_url);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, array('data' => $jdata));
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT ,30);
		curl_setopt($ch, CURLOPT_TIMEOUT, 30);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$server_output = curl_exec($ch);
		var_dump($server_output);
		curl_close ($ch);
	}

	public function getUser($id) 
	{
		$CI = & get_instance(); 
		$CI->load->database();
		$CI->db->select('*')->from('users')->where('id', $id)->limit(1);
        $res_params = $CI->db->get();
		$r = $res_params->result();	
		return $r[0];
	}

	public function getBus($id = null, $iduser = null) 
	{
		$CI = & get_instance(); 
		$CI->load->database();
		$CI->db->select('bus.id, bus.name, bus.country, bus.zip, bus.description')->from('bus as bus');
		if($iduser) $CI->db->join('users_bus as ub', 'ub.bu_id = bus.id')->where('ub.user_id', $iduser);
		if($id) $CI->db->where('id', $id);
		$res = $CI->db->get();
		return $res->result();
	}

	public function getBuInfo($id_bu) {
		$CI = & get_instance(); 
		//$CI->db->select('bus.id_pos_cash_method');
		$CI->db->where('bus.id', $id_bu);
		$query = $CI->db->get("bus");
		$res = $query->result();
		return $res[0];
	}
		
	public function updateUserBu($id_bu, $id_user) 
	{
		$CI = & get_instance(); 
		$CI->load->database();
		if($id_user) $req = "UPDATE users SET current_bu_id = $id_bu WHERE id = $id_user";
		$res = $CI->db->query($req);
	}
	
	public function getUsers() 
	{
		$CI = & get_instance(); 
		$CI->load->database();
		$res_params = $CI->db->get_where('users', array('id' => $id));
		return $res_params->result();	
	}

	public function getEmail($type, $id_bu)
	{
		$CI = & get_instance(); 
		$CI->load->database();
		
		if($type == 'order') {
			$req_params = "SELECT email_order FROM bus WHERE id = $id_bu";
			$res_params = $CI->db->query($req_params);
			//Actuellement invérifiable :
			//$CI->db->select('email_order');
			//$res_params = $CI->db->get_where('bus', array('id', $id_bu));
			$res = $res_params->result();
			return $res[0]->email_order;
		}
		
	}
	
	public function sendSms($num, $msg) {
		
		$CI = & get_instance(); 
		$CI->load->library('mmail');
		
		$sms_user	= $this->getParam('ovh_sms_user'); 
		$sms_pass	= $this->getParam('ovh_sms_pass'); 
		$sms_nic	= $this->getParam('ovh_sms_nic'); 
		
		$sms = array();
		$sms['to']			= "email2sms@ovh.net";
		$sms['subject'] 	= $sms_nic.":".$sms_user.":".$sms_pass.":HANK:".$num.":::1";
		$sms['msg'] 		= $msg;
		$CI->mmail->sendEmail($sms);
	}
	
	public function sendNotif($msg, $id_bu) {

		$address	= $this->getParam('pushover_address');
		$token		= $this->getParam('pushover_token');
		$user		= $this->getParam('pushover_user');
		$buinfo 	= $this->getBuInfo($id_bu);
		$device		= $buinfo->pushover_device;
		
		curl_setopt_array(
			$ch = curl_init(), array(
				CURLOPT_URL => $address,
				CURLOPT_POSTFIELDS => array(
					"token" => $token,
					"user" => $user,
					"device" => $device,
					"message" => $msg
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