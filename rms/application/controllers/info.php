<?php
class Info extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->database();
		$this->load->model('news_model');
		$this->load->helper('url_helper');
		$this->load->library("pagination");
		$this->load->helper("url");
		$this->load->library('ion_auth');
		$this->load->library("hmw");
	}

	public function index($login=null)
	{
		if (!$this->ion_auth->logged_in())
		{
			redirect('auth/login');
		}
		$this->hmw->changeBu();// GENERIC changement de Bu
		
		$this->hmw->keyLogin();

		$headers = $this->hmw->headerVars(1, "/info/", "Credits & License");
		$this->load->view('jq_header_pre', $headers['header_pre']);
		$this->load->view('jq_header_post', $headers['header_post']);
		$this->load->view('info');
		$this->load->view('jq_footer');
	}
}
?>
