<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Payment_process extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->database();
		$this->load->library('hmw');
	}

	public function setup()
	{
		//exit(); //activate just for setup, run once when switching env
		require_once("../../payplug_php/lib/Payplug.php");
		
		$login = $this->hmw->getParam('payplug_login');
		$pass = $this->hmw->getParam('payplug_pass');
		
		$isTest = true;
		$parameters = Payplug::loadParameters($login, $pass, $isTest);
		$parameters->saveInFile("../../payplug_php/parameters.json");
		echo 'OK';
	}
	
	public function index()
	{	

		if(!isset($_POST['custom'])) exit();
		
		require_once("../../payplug_php/lib/Payplug.php");
		Payplug::setConfigFromFile("../../payplug_php/parameters.json");
		
		$txn_id = substr($_POST['custom'],4);
		$req 	= $this->db->query("SELECT amount FROM shop_payments WHERE id = $txn_id LIMIT 1");
		$res	= $req->result_object();
		$amount = $res[0]->amount*100;
		if($amount < 1) echo "Error, please try again";
		$paymentUrl = PaymentUrl::generateUrl(array(
			'amount' => $amount,
			'customData' => $txn_id,
			'currency' => 'EUR',
			'ipnUrl' => 'http://hmw.hankrestaurant.com/payment_ipn/',
		'email' => $_POST['email'],
			'firstName' => $_POST['first_name'],
			'lastName' => '   '
			));
		header("Location: $paymentUrl");
		exit();
	}
}
?>