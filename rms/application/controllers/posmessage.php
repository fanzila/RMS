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
		
		$data = array();
		
		if ($this->input->post('msg'))
		{
			$this->load->library('mmail');
			$data['msgsent'] = $this->input->post('msg');
			$this->hmw->sendNotif($this->input->post('msg'));	
		}
		
		$this->load->helper('form');
		
		$data['title'] = 'Message caisse';

		$this->load->view('jq_header', $data);
		$this->load->view('posmessage', $data);
		$this->load->view('jq_footer');
	}
}
?>