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

		if (!$this->ion_auth->logged_in())
		{
			redirect('auth/login');
		}

		$group_info = $this->ion_auth_model->get_users_groups()->result();
		if ($group_info[0]->level < 1)
		{
			$this->session->set_flashdata('message', 'You must be a gangsta to view this page');
			redirect('/news/');
		}

		$url = array();

		if($onebu) { 
			$this->db->from('cameras')->where('id_bu', $this->session->userdata('bu_id'));
		} else {
			$this->db->from('cameras');
		}
		$res = $this->db->get();
		$row = $res->result();
		$i = 1;
		
		$local1		= false;
		$local2		= false;
		$ip 		= $this->input->ip_address();
		$ca 		= array();
		$data		= array();
		//$id_bu 		= $this->session->all_userdata()['bu_id'];
		$buinfo1 	= $this->hmw->getBuInfo(1);
		$buinfo2 	= $this->hmw->getBuInfo(2);
		
		$this->db->from('turnover')->order_by('date desc')->where('id_bu',1)->limit(1);
	 	$bal_ca = $this->db->get();
		$ca[1] = $bal_ca->row_array();

		$this->db->from('turnover')->order_by('date desc')->where('id_bu',2)->limit(1);
	 	$bal_ca = $this->db->get();
		$ca[2] = $bal_ca->row_array();

		if($ip == $buinfo1->net_ip) $local1 = true;
		if($ip == $buinfo2->net_ip) $local2 = true;
		
		foreach ($row as $key => $var) {
			$url['cam'.$i] = $var->adress;
			if($local1 AND $var->id_bu == 1) $url['cam'.$i] = $var->adress_local;
			if($local2 AND $var->id_bu == 2) $url['cam'.$i] = $var->adress_local;
			$i++;	
		}
		
		$planning = $this->planning();
	
		$data['url'] = $url;
		$data['ca'] = $ca;
		$data['planning'] = $planning;
	
		$session_data['cam'] = $url;
		$this->session->set_userdata($session_data);
		
		$this->load->view('camera/cameras', $data);
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
			$response = $shiftplanning->setRequest(array(array('module' => 'dashboard.onnow', 'method' => 'GET')));
			$send_message = $shiftplanning->getResponse(0);	// returns the response/data for the first api call (index=0)
			
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
		$data['cams'] = $this->session->all_userdata()['cam'];
		$data['num']  = $num;
		$this->load->view('camera/frame', $data);
	}
}