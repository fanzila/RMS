<?php
class Posmessage extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->library('ion_auth');
		$this->load->library('ion_auth_acl');
		$this->load->library("tools");
	}

	public function index()
	{
		$this->tools->changeBu();// GENERIC changement de Bu
		$id_bu =  $this->session->userdata('id_bu');
		$data = array();
		
		if ($this->input->post('msg'))
		{
			$this->load->library('mmail');
			$data['msgsent'] = $this->input->post('msg');
			if ($this->input->post('service')) {
				$this->tools->sendNotif($this->input->post('msg'), $id_bu);
			}
			if ($this->input->post('kitchen')) {
				$this->tools->sendNotif($this->input->post('msg'), $id_bu, 'kitchen');
			}
		}
		
		$this->load->helper('form');
		
		$data['bu_name'] =  $this->session->userdata('bu_name');
		$data['username'] = $this->session->userdata('identity');
		
		
	 	$headers = $this->tools->headerVars(1, "/posmessage/", "Message caisse");
		$this->load->view('jq_header_pre', $headers['header_pre']);
		$this->load->view('jq_header_post', $headers['header_post']);
		$this->load->view('posmessage',$data);
		$this->load->view('jq_footer');
	}
}
?>
