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
		header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
		header("Cache-Control: post-check=0, pre-check=0", false);
		header("Pragma: no-cache");
		header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
		
		$this->load->library('ion_auth');
		$this->load->library('hmw');
		$this->load->library('session');

		$this->hmw->isLoggedIn();
		
		$group_info = $this->ion_auth_model->get_users_groups()->result();
		if ($group_info[0]->level < 1)
		{
			$this->session->set_flashdata('message', 'You must be a gangsta to view this page');
			redirect('/news/');
		}

		$url = array();
				
		$local1			= false;
		$local2			= false;
		$ip 			= $this->input->ip_address();
		$ca 			= array();
		$data			= array();
		$bu_postion_id	= array();
		$buname 		= array();
		$camera			= array();
		$buinfo1 		= $this->hmw->getBuInfo(1);
		$buinfo2 		= $this->hmw->getBuInfo(2);
		
		$this->db->from('turnover')->order_by('date desc')->where('id_bu',1)->limit(1);
	 	$bal_ca = $this->db->get();
		$ca[1] = $bal_ca->row_array();

		$this->db->from('turnover')->order_by('date desc')->where('id_bu',2)->limit(1);
	 	$bal_ca = $this->db->get();
		$ca[2] = $bal_ca->row_array();

		if ($this->ion_auth->in_group('Admin') && !$onebu) {
			
			$this->db->select('id, name');
			$query = $this->db->get('bus');
			$all_bus = $query->result_array();
			
			$cameras = $this->getCamerasNamesFromDb();
		} else {
			$id_bu = $this->session->userdata('bu_id');
			if (!empty($id_bu))
				$cameras = $this->getCamerasNamesFromDb($id_bu);
			else
				die("Can't find bu");
		}

		if (empty($cameras))
			die("Error when getting cameras from db");

		$bu_postion_id[1]		= explode (',',$buinfo1->humanity_positions);
		$bu_postion_id[2]		= explode (',',$buinfo2->humanity_positions);
		$buname[1] 				= $buinfo1->name;
		$buname[2]				= $buinfo2->name;
		
		$info_current_bu 		= $this->hmw->getBuInfo($this->session->userdata('bu_id'));
		$planning 				= $this->planning();
		if (!empty($all_bus)) {
			$data['all_bus'] = $all_bus;
		}
		$data['bu_postion_id'] 	= $bu_postion_id;
		$data['info_bu'] 		= $info_current_bu;
		$data['buname'] 		= $buname;
		$data['ca']				= $ca;
		$data['planning'] 		= $planning;
		$data['cameras'] = $cameras;
		$session_data['cam'] 	= $url;
		
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
		$camera_proxy = $this->load->library('camera_proxy');
		$this->load->library('hmw');
		
		$is_admin = $this->ion_auth->in_group('admin');
		if (!$this->hmw->isLoggedIn())
			die();
			
		session_write_close();
		
		if ($is_admin === false) {
			$this->db->where('id_bu', $this->session->userdata('bu_id'));
		}
		$this->db->where('name', $camera_name);
		$query = $this->db->get('cameras');
		$camera = $query->row_array();
		
		if (empty($camera))
			die('Camera not found in DB');
		
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

		curl_exec($ch);

		if (curl_error($ch))
		{
		  echo curl_error($ch);
		  exit;
		}

		curl_close($ch);
		fclose($fp);
	}
	
	private function planning() 
	{
	
		$this->load->library('hmw');
		$this->load->library('shiftplanning');
	
		$sp_key		= $this->hmw->getParam('sp_key'); 
		$sp_user	= $this->hmw->getParam('sp_user');
		$sp_pass	= $this->hmw->getParam('sp_pass');
	
		/* set the developer key on class initialization */
		$shiftplanning = new shiftplanning(array('key' => $sp_key));

		$session = $shiftplanning->getSession( );
		if( !$session ) {
			// perform a single API call to authenticate a user
			$response = $shiftplanning->doLogin(
			array('username' => $sp_user, 'password' => $sp_pass));

				if( $response['status']['code'] == 1 )
				{// check to make sure that login was successful
					$session = $shiftplanning->getSession( );	// return the session data after successful login
				} else {
					echo " CANT GET SESSION".$response['status']['text'] . "--" . $response['status']['error'];
				}
		}

		if( $session ) {
			$response = $shiftplanning->setRequest(
				array(
					array('module' => 'dashboard.onnow', 'method' => 'GET'),
					array('module' => 'location.locations', 'method' => 'GET')
				)
			);
			//print_r($shiftplanning->getResponse(1));
			$r = $shiftplanning->getResponse(0);
			return $r;		
		}
	}
	
	public function frame($num)
	{		
		header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
		header("Cache-Control: post-check=0, pre-check=0", false);
		header("Pragma: no-cache");
		header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
		
		$this->load->library('ion_auth');
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