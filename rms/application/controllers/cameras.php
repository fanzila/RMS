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

	public function index($allbu = false)
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

		if($allbu) { 
			$this->db->from('cameras');
		} else {
			$this->db->from('cameras')->where('id_bu', $this->session->userdata('bu_id'));
		}
		$res = $this->db->get();
		$row = $res->result();
		$i = 1;
		
		$local 		= false;
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

		
		if($ip == $buinfo1->net_ip OR $ip == $buinfo2->net_ip) $local = true;

		foreach ($row as $key => $var) {
			$url['cam'.$i] = $var->adress;
			if($local) $url['cam'.$i] = $var->adress_local;
			$i++;	
		}
		
		$data['url'] = $url;
		$data['ca'] = $ca;
		$session_data['cam'] = $url;
		$this->session->set_userdata($session_data);
		
		$this->load->view('camera/cameras', $data);
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