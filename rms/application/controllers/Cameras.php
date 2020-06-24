<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Cameras extends CI_Controller {

	/**
	* Index Page for this controller.
	*
	* Maps to the following URL
	* 		http://example.com/index.php/welcome
	*	- or -  
	* 		http://example.com/index.php/welcome/index
	*	- or -
	* Since this controller is set as the default controller in 
	* config/routes.php, it's displayed at http://example.com/
	*
	* So any other public methods not prefixed with an underscore will
	* map to /index.php/welcome/<method_name>
	* @see http://codeigniter.com/user_guide/general/urls.html
	*/

	public function index($onebu = false)
	{
		$this->load->library('ion_auth');
		$this->load->library('ion_auth_acl');
		$this->load->library('hmw');
		$this->load->library('session');
		$this->load->library('cashier');

		$this->hmw->changeBu();// GENERIC changement de Bu

		header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
		header("Cache-Control: post-check=0, pre-check=0", false);
		header("Pragma: no-cache");
		header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");

		$this->hmw->isLoggedIn();

		if (!$this->ion_auth_acl->has_permission('cameras')) {
			die('You are not allowed to view this page');
		}

		$url = array();

		$local			= false;
		$ip 			= $this->input->ip_address();
		$ca 			= array();
		$data			= array();
		$bu_postion_id	= array();
		$buname 		= array();
		$camera			= array();
		$id_bu 			= $this->session->userdata('bu_id');
		$buinfo 		= $this->hmw->getBuInfo($id_bu);
		$date 			= new DateTime("now");
		$curr_date 		= $date->format('Y-m-d ');
		 
		$bal_ca = $this->db->get('turnover');
		$ca = $bal_ca->result_array();

		$this->db->select('date, bu_id, to, id');
		$this->db->where('DATE(date)',$curr_date);
		$query = $this->db->get('infos_close');
		$data['infos_close'] = $query->result_array();

		$cameras = $this->getCamerasNamesFromDb($id_bu);

		$p = array(
			'type'		=>  'user_access_cam',
			'val1'		=> $_SERVER['HTTP_USER_AGENT'],
			'val2' 		=> $_SERVER['REMOTE_ADDR']
			);
			$this->hmw->LogRecord($p, $this->session->userdata('bu_id'));

			$user					= $this->ion_auth->user()->row();
			$data['bus_list']		= $this->hmw->getBus(null, $user->id);
			$data['all_bus']		= $this->hmw->getBus(null, null);
			$data['bu_id']			= $id_bu;

			$planning 				= $this->snapplanning();
			$data['bu_postion_id']	= explode (',',$buinfo->humanity_positions);
			$data['info_bu'] 		= $buinfo;
			$data['ca']				= $ca;
			$data['planning'] 		= $planning;
			$data['cameras'] 		= $cameras;
			$session_data['cam'] 	= $url;

			$data['cash_fund'] = $this->cashier->getCashFund($id_bu);

			$this->session->set_userdata($session_data);
			$this->load->view('camera/cameras', $data);
		}

		private function getCamerasNamesFromDb($id_bu = null)
		{
			$this->load->library('hmw');

			if (!$this->hmw->isLoggedIn())
				die();
			$this->db->select('name, id_bu');
			if (!empty($id_bu)) $this->db->where('id_bu', $id_bu);
			$this->db->where('active', true);
			$query = $this->db->get('cameras');
			if ($query->result_array() != NULL)
				$cameras = $query->result_array();
			else
				$cameras = false;
			return ($cameras);
		}

		function getStream($camera_name) 
		{

			$this->load->database();
			$this->load->library('ion_auth');
			$this->load->library('ion_auth_acl');
			$camera_proxy = $this->load->library('camera_proxy');
			$this->load->library('hmw');

			if (!$this->hmw->isLoggedIn())
				die();

			session_write_close();

			$this->db->where('id_bu', $this->session->userdata('bu_id'));
			$this->db->where('name', $camera_name);
			$query = $this->db->get('cameras');
			$camera = $query->row_array();

			if (empty($camera))
				die('Camera not found in DB');

			if($camera['type'] == 'image') {

				$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, $camera['address']);
				curl_setopt($ch, CURLOPT_HEADER, 0);
				curl_setopt($ch, CURLOPT_USERPWD, "$camera[login]:$camera[password]");
				//curl_setopt($ch, CURLOPT_HTTPAUTH);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
				curl_setopt($ch, CURLOPT_BINARYTRANSFER,1);
				$picture = curl_exec($ch);
				curl_close($ch);

				//Display the image in the browser
				header('Content-type: image/jpeg');
				echo $picture;
				exit;

			} else { 

				// Register the wrapper
				stream_wrapper_register("stream", "camera_proxy")
					or die("Failed to register protocol");

				// Open the "file"
				$fp = fopen("stream://CameraCGIStreamContent", "r+")
				or die;
				# On envoie les memes headers que la Camera Axis
					header('Content-Type: multipart/x-mixed-replace; boundary=myboundary');

				$ch = curl_init($camera['address']);
				curl_setopt($ch, CURLOPT_USERPWD, "$camera[login]:$camera[password]");
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
				curl_setopt($ch, CURLOPT_FILE, $fp);
				curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_DIGEST);
				curl_exec($ch);

				if (curl_error($ch))
				{
					echo curl_error($ch);
					exit;
				}

				curl_close($ch);
				fclose($fp);
			}
		}

		private function Snapplanning() 
		{

			$this->load->library('hmw');
			$snapshift_token	= $this->hmw->getParam('snapshift_token'); 
			$snapshift_url 		= $this->hmw->getParam('snapshift_url'); 

			$snapshift_path		= "/api/v1/plannings?start_date=".date('Y-m-d');
			$header 			= ["Authorization: Bearer $snapshift_token"];

			$this->db->select('data, ts');
			$query = $this->db->get('snapshift');
			$snap = $query->row_array();

			$snap_ts	= new DateTime($snap['ts']);
			$db_ts		= $snap_ts->getTimestamp();
			$out_db		= $snap['data'];
			$cache 		= $db_ts+(60*10);
			$curl_error = 0;
						
			if($cache < time()) {

				$ch = curl_init($snapshift_url.$snapshift_path);
				curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
				curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 16); 
				curl_setopt($ch, CURLOPT_TIMEOUT, 16); //timeout in seconds
				$out = curl_exec($ch);

				if (curl_error($ch))
				{
					$errno = curl_errno($ch);
					$errtx = curl_error($ch);
					$curl_error = "CURL error : $errno -  $errtx";
					curl_close($ch);
				} else { 
					curl_close($ch);
					$this->db->set('data', $out);
					if (!$this->db->update('snapshift')) {
						$reponse = "Can't place the insert sql request, error message: ".$this->db->_error_message();
					}
				}	
			}

			$this->db->select('data, ts');
			$query = $this->db->get('snapshift');
			$snap = $query->row_array();
			$out = $snap['data'];
			
			$planning = json_decode($out, true);
			$sorted1 = array();

			foreach ($planning as $key) {
				if( $key['date'] == date('Y-m-d')) { 
					$sorted1[] = $key;
				}
			}

			$sorted2 = array();
			$sorted2['FR75ARCH']	= array();
			$sorted2['FR75GRAV']	= array();
			$sorted2['FR75ROCH']	= array();
			$sorted2['FR69OPERA']	= array();

			foreach ($sorted1 as $key) {
				if($key['location_id'] == '3017bcc5-d766-488f-b466-bbb56bf5d0e1') $sorted2['FR75ARCH'][]	= $key;
				if($key['location_id'] == 'de196513-8283-4ee7-9b2c-880d909d7a44') $sorted2['FR75GRAV'][]	= $key;
				if($key['location_id'] == 'ac99b712-e2fc-4d3d-b12a-ca9e015767de') $sorted2['FR75ROCH'][]	= $key;
				if($key['location_id'] == '9dd29ab8-4ccd-48e0-9170-7e5580442a1c') $sorted2['FR69OPERA'][]	= $key;
			}

			
			return $sorted2;
		}

			public function frame($num)
			{		
				header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
				header("Cache-Control: post-check=0, pre-check=0", false);
				header("Pragma: no-cache");
				header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");

				$this->load->library('ion_auth');
				$this->load->library('ion_auth_acl');
				$this->load->library('hmw');
				$this->load->library('session');

				if (!$this->ion_auth->logged_in())
				{
					exit;
				}
				$data['cams'] = $this->session->userdata('cam');
				$data['num']  = $num;
				$this->load->view('camera/frame', $data);
			}
		}

		Class Camera_proxy {

			function stream_open($path, $mode, $options, &$opened_path)
			{
				// Has to be declared, it seems...
				return true;
			}

			public function stream_write($data)
			{
				echo $data;

				return strlen($data);
			}

		}
