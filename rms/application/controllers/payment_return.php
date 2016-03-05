<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Payment_return extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->database();
	}

	public function index()
	{		
		$data = array('result' => false);
		$this->load->view('payment_return', $data);
	}

	public function success()
	{		
		$data = array('result' => true);
		$this->load->view('payment_return', $data);
	}

	public function cancel()
	{		
		$data = array('result' => false);
		$this->load->view('payment_return', $data);

	}
}
?>