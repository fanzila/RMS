<?php
class Posmessage extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->library('ion_auth');
		$this->load->library("hmw");
	}

	public function index()
	{
		$this->hmw->changeBu();// GENERIC changement de Bu
		$id_bu =  $this->session->all_userdata()['bu_id'];
		$data = array();
		
		if ($this->input->post('msg'))
		{
			$this->load->library('mmail');
			$data['msgsent'] = $this->input->post('msg');
			$this->hmw->sendNotif($this->input->post('msg'), $id_bu);	
		}
		
		$this->load->helper('form');
		
		$data['bu_name'] =  $this->session->all_userdata()['bu_name'];
		$data['username'] = $this->session->all_userdata()['identity'];
		
		
	 	$headers = $this->hmw->headerVars(1, "/posmessage/", "Message caisse");
		$this->load->view('jq_header_pre', $headers['header_pre']);
		$this->load->view('jq_header_post', $headers['header_post']);
		$this->load->view('posmessage',$data);
		$this->load->view('jq_footer');
	}
}
?>
