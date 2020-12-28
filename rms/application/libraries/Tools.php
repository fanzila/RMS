<?php

class Tools
{

	public function LogRecord($p, $id_bu = null)
	{

		$CI = &get_instance();

		if (!isset($id_bu)) $id_bu = $CI->session->userdata('id_bu');
		if ($CI->session->userdata('keylogin') != NULL) $keylogin = $CI->session->userdata('keylogin');

		$user		= $CI->ion_auth->user()->row();

		$CI->db->set('type', $p['type']);
		$CI->db->set('id_bu', $id_bu);

		if (isset($keylogin)) $CI->db->set('keylogin', $keylogin);
		if (isset($user->id)) $CI->db->set('user_id', $user->id);

		if (isset($p['val1'])) $CI->db->set('val1', "$p[val1]");
		if (isset($p['val2'])) $CI->db->set('val2', "$p[val2]");
		if (isset($p['val3'])) $CI->db->set('val3', "$p[val3]");
		if (isset($p['val4'])) $CI->db->set('val4', "$p[val4]");

		$CI->db->insert('log');
	}


	public function cleanNumber($num)
	{
		$t1 = str_replace(',', '.', $num);
		$t2 = trim($t1);
		//$t3 = preg_replace("/[^0-9,.]/", "", $t2);
		return $t2;
	}

	public function getParam($param)
	{
		$CI = &get_instance();
		$CI->load->database();
		$CI->db->select('val')->from('params')->where('key', $param)->limit(1);
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


		$CI = &get_instance();
		$CI->load->database();

		$jdata = json_encode($data);

		$ch = curl_init();
		$api_box_url = $this->getParam('api_box_url');
		$key = $this->getParam('keylogin');
		if ($_GET['key'] != $key) exit('error B');

		curl_setopt($ch, CURLOPT_URL, $api_box_url);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, array('data' => $jdata));
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
		curl_setopt($ch, CURLOPT_TIMEOUT, 30);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$server_output = curl_exec($ch);
		var_dump($server_output);
		curl_close($ch);
	}

	public function getUser($id)
	{
		$CI = &get_instance();
		$CI->load->database();
		$CI->db->select('*')->from('users')->where('id', $id)->limit(1);
		$res_params = $CI->db->get();
		$r = $res_params->result();
		return $r[0];
	}

	public function getBus($id = null, $iduser = null)
	{
		$CI = &get_instance();
		$CI->load->database();
		$CI->db->select('bus.id, bus.name, bus.country, bus.zip, bus.description')->from('bus as bus');

		if ($iduser) $CI->db->join('users_bus as ub', 'ub.id_bu = bus.id')->where('ub.user_id', $iduser);

		if ($id) {
			if (is_array($id))
				$CI->db->where_in('id', $id);
			else
				$CI->db->where('id', $id);
		}

		$res = $CI->db->get();
		return $res->result();
	}

	public function getBuInfo($id_bu)
	{
		$CI = &get_instance();
		$CI->db->where('bus.id', $id_bu);
		$query = $CI->db->get("bus");
		$res = $query->result();
		return $res[0];
	}

	public function updateUserBu($id_bu, $id_user)
	{
		$CI = &get_instance();
		$CI->load->database();
		if ($id_user) {
			$CI->db->set('current_id_bu', $id_bu)->where('id', $id_user);
			$CI->db->update('users');
		}
	}

	public function getUsers()
	{
		$CI = &get_instance();
		$CI->load->database();
		$res_params = $CI->db->get('users');
		return $res_params->result();
	}

	public function getEmail($type, $id_bu)
	{
		$CI = &get_instance();
		$CI->load->database();

		if ($type == 'order') {
			$CI->db->select('email_order')->from('bus')->where('id', $id_bu);
			$res_params = $CI->db->get();
			$res = $res_params->result();
			return $res[0]->email_order;
		}

		if ($type == 'generic') {
			$CI->db->select('email_generic')->from('bus')->where('id', $id_bu);
			$res_params = $CI->db->get();
			$res = $res_params->result();
			return $res[0]->email_generic;
		}
	}

	public function sendSms($num, $msg)
	{

		$CI = &get_instance();
		$CI->load->library('mmail');

		$sms_user	= $this->getParam('ovh_sms_user');
		$sms_pass	= $this->getParam('ovh_sms_pass');
		$sms_nic	= $this->getParam('ovh_sms_nic');

		$subject = $sms_nic . ':' . $sms_user . ':' . $sms_pass . ':HANK:' . $num . ':::1';

		$CI->mmail->prepare($subject, $msg)
			->toEmail('email2sms@ovh.net')
			->send();
	}

	public function sendNotif($msg, $id_bu, $devParam = null)
	{

		$address	= $this->getParam('pushover_address');
		$token		= $this->getParam('pushover_token');
		$user		= $this->getParam('pushover_user');
		$buinfo 	= $this->getBuInfo($id_bu);
		$device 	= $buinfo->pushover_device;

		if ($devParam === 'kitchen') {
			$device = $buinfo->pushover_device_kitchen;
		}

		curl_setopt_array(
			$ch = curl_init(),
			array(
				CURLOPT_URL => $address,
				CURLOPT_POSTFIELDS => array(
					"token" => $token,
					"user" => $user,
					"device" => $device,
					"priority" => 2,
					"retry" => 120,
					"expire" => 3600,
					"message" => $msg
				)
			)
		);
		curl_exec($ch);
		curl_close($ch);
	}

	public function isLoggedIn()
	{
		$CI = &get_instance();
		$CI->load->library('ion_auth');

		if (!$CI->ion_auth->logged_in()) {
			$CI->session->set_userdata('pageBeforeLogin', current_url());
			redirect('auth/login');
		} else {
			return (true);
		}
	}

	public function keyLogin()
	{

		$CI = &get_instance();
		$CI->load->library('ion_auth');
		$CI->load->library('tools');
		$CI->load->library('session');
		$CI->load->library('email');
		$CI->load->library('mmail');

		$getkey	= $CI->input->get('keylogin');
		$id_bu	= $CI->input->get('id_bu');
		$type		= $CI->input->get('type');

		if (!empty($getkey)) {
			$keyl = $this->getParam('keylogin');
			if ($getkey == $keyl) {

				$user = $this->getParam('keylogin_user_' . $id_bu);
				$pass = $this->getParam('keylogin_pass_' . $id_bu);

				if ($type == 'kitchen') {
					$CI->session->set_userdata('type', 'kitchen');
				} else if ($type == 'service' || $type == false) {
					$CI->session->set_userdata('type', 'service');
				}
				//login($user, $pass, remember, keylogin);
				$CI->ion_auth->login($user, $pass, true, true);
			}
		} else {

			if (!$CI->ion_auth->logged_in()) {
				$CI->session->set_userdata('pageBeforeLogin', current_url());
				redirect('auth/login');
			}
		}

		$data['bu_name'] =  $CI->session->userdata('bu_name');
		$data['username'] = $CI->session->userdata('identity');
	}

	public function changeBu($bu = null)
	{
		$CI = &get_instance();

		$change_bu = $bu ? $bu : $CI->input->post('bus');

		if (!empty($change_bu)) {
			$bu_info = $CI->tools->getBus($change_bu);
			$session_data = array('id_bu'  => $change_bu, 'bu_name' => $bu_info[0]->name);
			$CI->tools->updateUserBu($change_bu, $CI->session->userdata('user_id'));
			$CI->session->set_userdata($session_data);
		}
	}

	public function headerVars($index, $indexlocation, $title)
	{
		$CI = &get_instance();

		if ($index != -1) {
			$user			= $CI->ion_auth->user()->row();
			$bus_list		= $CI->tools->getBus(null, $user->id);
			$user_groups	= $CI->ion_auth->get_users_groups()->result();

			$higher_level = new stdClass();
			$higher_level->level = -1;
			foreach ($user_groups as $key => $value) {
				if ($value->level > $higher_level->level) {
					$higher_level = $value;
				}
			}
			$id_bu			= $CI->session->userdata('id_bu');
			$keylogin 		= $CI->session->userdata('keylogin');
			$bu_name		= $CI->session->userdata('bu_name');
			$username		= $CI->session->userdata('identity');
			$buinfo 		= $CI->tools->getBuInfo($id_bu);

			$CI->db->from('turnover')->order_by('date desc')->where('id_bu', $id_bu)->limit(1);
			$bal_ca = $CI->db->get();
			$ca = $bal_ca->row_array();

			$headers = array(
				'header_pre'	=> array(
					'title' => $title,
					'id_bu'	=> $id_bu
				),
				'header_post'	=> array(
					'id_bu'			=> $id_bu,
					'bu_name'		=> $bu_name,
					'bus_list'		=> $bus_list,
					'ca'			=> $ca,
					'index'			=> $index,
					'keylogin'		=> $keylogin,
					'indexlocation'	=> $indexlocation,
					'title'			=> $title,
					'groupname' 	=> $higher_level->name,
					'userlevel' 	=> $higher_level->level,
					'username'		=> $username,
					'user_id'		=> $user->id
				)
			);
		} else {
			$headers = array(
				'header_pre'	=> array(
					'title' => $title,
					'id_bu'	=> null
				),
				'header_post'	=> array(
					'id_bu'			=> null,
					'bu_name'		=> null,
					'bus_list'		=> null,
					'ca'			=> null,
					'index'			=> 0,
					'keylogin'		=> null,
					'indexlocation'	=> $indexlocation,
					'title'			=> $title,
					'groupname' 	=> null,
					'userlevel' 	=> null,
					'username'		=> null
				)
			);
		}
		return $headers;
	}

	public function getBuUsers($id_bu, $groups = null)
	{
		$CI = &get_instance();
		$CI->load->database();

		$CI->db->select('users.username, users.email, users.id');
		$CI->db->distinct('users.username');
		$CI->db->join('users_bus', 'users.id = users_bus.user_id', 'left');
		$CI->db->join('users_groups', 'users.id = users_groups.user_id');
		$CI->db->where('users_bus.id_bu', $id_bu);
		$CI->db->where_in('users.active', 1);

		if (!empty($groups)) {
			if (is_array($groups))
				$CI->db->where_in('users_groups.group_id', $groups);
			else
				$CI->db->where('users_groups.group_id', $groups);
		}

		return $CI->db->get('users')->result();
	}

	public function SnapConnect($command)
	{

		$snapshift_token	= $this->getParam('snapshift_token');
		$snapshift_url 		= $this->getParam('snapshift_url');

		$snapshift_path		= $command;
		$header 			= ["Authorization: Bearer $snapshift_token"];

		$ch = curl_init($snapshift_url . $snapshift_path);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 16);
		curl_setopt($ch, CURLOPT_TIMEOUT, 16); //timeout in seconds
		$out = curl_exec($ch);

		if (curl_error($ch)) {
			$errno = curl_errno($ch);
			$errtx = curl_error($ch);
			curl_close($ch);
			return "CURL error : $errno -  $errtx";
		} else {
			curl_close($ch);
			return $out;
			}
	}
	
	public function SnapPlanning()
	{
		$CI = &get_instance();

		$CI->db->select('data, ts');
		$query = $CI->db->get('snapshift');
		$snap = $query->row_array();

		$snap_ts	= new DateTime($snap['ts']);
		$db_ts		= $snap_ts->getTimestamp();
		$cache 		= $db_ts + (60 * 10);

		if ($cache < time()) {

			$out = $this->SnapConnect("/api/v1/plannings?start_date=" . date('Y-m-d'));
			$CI->db->set('data', $out);
			if (!$CI->db->update('snapshift')) {
				return "Can't place the insert sql request, error message: " . $CI->db->_error_message();
			}
		}

		$CI->db->select('data, ts');
		$query = $CI->db->get('snapshift');
		$snap = $query->row_array();
		$out = $snap['data'];

		$planning = json_decode($out, true);
		$sorted1 = array();

		foreach ($planning as $key) {
			if ($key['date'] == date('Y-m-d')) {
				$sorted1[] = $key;
			}
		}

		$sorted2 = array();
		$sorted2['FR75ARCH']	= array();
		$sorted2['FR75GRAV']	= array();
		$sorted2['FR75ROCH']	= array();
		$sorted2['FR69OPERA']	= array();
		$sorted2['FR750BER']	= array();
		$sorted2['FR59CSE']		= array();

		foreach ($sorted1 as $key) {
			if ($key['location_id'] == '3017bcc5-d766-488f-b466-bbb56bf5d0e1') $sorted2['FR75ARCH'][]	= $key;
			if ($key['location_id'] == 'de196513-8283-4ee7-9b2c-880d909d7a44') $sorted2['FR75GRAV'][]	= $key;
			if ($key['location_id'] == 'ac99b712-e2fc-4d3d-b12a-ca9e015767de') $sorted2['FR75ROCH'][]	= $key;
			if ($key['location_id'] == '9dd29ab8-4ccd-48e0-9170-7e5580442a1c') $sorted2['FR69OPERA'][]	= $key;
			if ($key['location_id'] == '5b4b7a9e-311b-4ccc-a384-23ce8e03b9e6') $sorted2['FR75OBER'][]	= $key;
			if ($key['location_id'] == 'c09f1dc5-c951-43b7-8345-dada0d70a975') $sorted2['FR59CSE'][]	= $key;
		}

		return $sorted2;
	}
}