<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Payment_ipn extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->database();
		$this->load->library('hmw');

	}

	private function alertemail($msg) {		
		$CI =& get_instance();

		$CI->load->library('email');
		$emailto = $this->hmw->getParam('notification_email');

		$CI->email->from('hmw@hankrestaurant.com', 'HMW');
		$CI->email->to($emailto); 
		$CI->email->subject('ALERT IPN');
		$CI->email->message($msg);	
		$CI->email->send();

		error_log('IPN SAYS: '.$msg);

	}

	private function CallNumber() {
		// Get the PHP helper library from twilio.com/docs/php/install
		require_once('/usr/share/php/Services/Twilio.php'); // Loads the library
		
		$sid = $this->hmw->getParam('twilio_sid');
		$token = $this->hmw->getParam('twilio_token');
		$twilio_num_from = $this->hmw->getParam('twilio_num_from');
		$twilio_num_to = $this->hmw->getParam('twilio_num_to');
		$twilio_app_sid = $this->hmw->getParam('twilio_app_sid');
		
		$client = new Services_Twilio($sid, $token);

		$call = $client->account->calls->create($twilio_num_from, $twilio_num_to, $twilio_app_sid, array(
		    'Record' => 'false'
		    ));
		error_log('twilio: '.$call->sid);
		
		//todo handle trillo fallback & callback URLs
		
	}
	
	private function sendSmsOctopush() {

		$this->load->library('sms');
				
		$login = $this->hmw->getParam('sms_api_account');
		$key = $this->hmw->getParam('sms_api_key');
		$smsnum = $this->hmw->getParam('sms_api_num');

		$user_login = $login;
		$api_key = $key; 

		$sms_recipients = array($smsnum);
		$sms_text = 'HANK ORDER: ' . date('Y-m-d H:i:s') . ' '; //{ch1} {ch2}

		$sms_type = SMS_PREMIUM; // ou encore SMS_STANDARD,SMS_PREMIUM
		$sms_mode = INSTANTANE; // ou encore DIFFERE
		$sms_sender = 'HANK-HMW';

		$sms = new SMS();

		$sms->set_user_login($user_login);
		$sms->set_api_key($api_key);
		$sms->set_sms_mode($sms_mode);
		$sms->set_sms_text($sms_text);
		$sms->set_sms_recipients($sms_recipients);
		$sms->set_sms_type($sms_type);
		$sms->set_sms_sender($sms_sender);
		$sms->set_sms_request_id(uniqid());
		$sms->set_option_with_replies(0);
		//$sms->set_sms_fields_1(array(''));
		//$sms->set_sms_fields_2(array('a'));
		$sms->set_option_transactional(1);
		$sms->set_sender_is_msisdn(0);
		//$sms->set_date(2016, 4, 17, 10, 19); // En cas d'envoi différé.
		$sms->set_request_keys('TRS');
		$xml = $sms->send();

		echo $xml;
		echo '<br />';
		echo '<textarea style="width:600px;height:600px;">' . $xml . '</textarea>';
	}

	private function sendOrderEmail($info) {
		$CI =& get_instance();

		$CI->load->library('email');
		$emailto = $this->hmw->getParam('notification_email');
		$CI->email->from('hmw@hankrestaurant.com', 'HMW');
		$CI->email->to('mail@hankrestaurant.com');
		$CI->email->cc('pierre@hankrestaurant.com'); 
		$CI->email->subject('TAKE AWAY ORDER');
		$CI->email->message($info);	
		$CI->email->send();
		error_log('NEW CMD: '.$info);
	}

	private function getProductName($id) {

		$this->load->database();
		$req = "SELECT name FROM shop_products WHERE id = $id LIMIT 1";
		$res = $this->db->query($req);
		$r = $res->result_object();
		$str = $r[0]->name;
		$str = substr( $str, ( $pos = strpos( $str, ']' ) ) === false ? 0 : $pos + 1 );
		return $str;
	}

	private function getOrderInfo($info) {

		$cart = "";

		$cart_enc = $info[0]->cart;
		$jcart = json_decode($cart_enc);

		foreach( $jcart as $key => $var ) {
			$cart	.= "\r\n";
			$cart	.= "NAME: $var->name\r\n";
			if(isset($var->burger)) $cart	.= "BURGER: ".$this->getProductName($var->burger)."\r\n";
			if(isset($var->extracheese)) { $xc = 'NO'; if($var->extracheese > 0) $xc ='YES'; $cart	.= "EXTRA CHEESE: $xc \r\n"; }
			if(isset($var->extragf)) { $xg = 'NO'; if($var->extragf > 0) $xg ='YES'; $cart	.= "SANS GLUTEN: $xg \r\n"; }
			if(isset($var->side)) $cart	.= "SIDE: ".$this->getProductName($var->side)."\r\n";
			if(isset($var->friessauce)) $cart	.= "SAUCE: ".$this->getProductName($var->friessauce)."\r\n";
			if(isset($var->drink)) $cart	.= "DRINK: ".$this->getProductName($var->drink)."\r\n";
			$cart	.= "PRICE: $var->price \r\n";
			$cart	.= "-------------------- \r\n\r\n";
		}

		$text 	= "\r\n";
		$text 	.= "PICKUP DATE: ". $info[0]->pickup_date."\r\n";
		$text 	.= "ORDER ID: ". $info[0]->id."\r\n";
		$text 	.= "COMMENT: ". $info[0]->comment."\r\n\r\n";
		$text 	.= "--------------------\r\n ";	
		$text 	.= $cart."\r\n";
		$text 	.= "TOTAL AMOUNT: ". $info[0]->amount."\r\n";
		$text 	.= "____________________\r\n";
		$text 	.= "NAME: ". $info[0]->name."   \r\n";
		$text 	.= "PHONE: ". $info[0]->phone."\r\n";
		$text 	.= "EMAIL: ". $info[0]->email."\r\n";
		$text 	.= "TRANSACTION_ID = ".$info[0]->transaction_id."\r\n";

		return $text;
	}


	private function paypal()
	{		

		$CI =& get_instance();

		$debug = false;
		$data = print_r($_POST, true);
		error_log('data: '.$data);

		if(empty($_POST)) {
			echo "ERROR 1";
			$this->alertemail('Receive a Paypal IPN with no post data! data: '.$data);
			return false;
		}

		$CI->load->library('ipn');
		$CI->ipn->verify();
		$ipnVariables = $CI->ipn->getData(TRUE);

		if($ipnVariables['receiver_email'] =! 'facilitator@test67.com') {
			echo "ERROR 2";
			$this->alertemail('Receive a Paypal IPN with a different receiver_email. data: '.$data);
			return false;
		}	

		if($ipnVariables['txn_type'] =! 'cart') {
			echo "ERROR 3";
			$this->alertemail('Receive a Paypal IPN with txn_type =! cart. data: '.$data);
			return false;
		}

		if($ipnVariables['test_ipn'] == 1 AND $debug == false) {
			echo "ERROR 5";
			$this->alertemail('Receive a Paypal IPN in test mode. data: '.$data);
			return false;
		}

		$txnid = substr($ipnVariables['custom'],4); 
		$query = $this->db->query("SELECT * FROM `shop_payments` WHERE id = $txnid LIMIT 1");
		$row_cnt = $query->num_rows;

		if ($row_cnt != 1) {
			echo "ERROR 6";
			$this->alertemail('Cannot find TXN id in payment db. data: '.$data);
			return false;
		}

		$row = $query->result_array();

		if ($row[0]['amount'] != trim($ipnVariables['mc_gross']))  {
			echo "ERROR 7";
			$this->alertemail('Amount is different from init to payment. data: '.$data);
			return false;
		}

		$payment_status = 'VERIFIED';
		if ($ipnVariables['payment_status'] =! 'Completed')  {
			$this->alertemail('Incorrect payment status. data: '.$data);
			$payment_status = 'FAIL'; 
		}

		//record payment
		$up = "UPDATE `shop_payments` SET 
			transaction_id		= '$ipnVariables[txn_id]',
			payment_provider	= 'paypal',
			payment_status		= '$payment_status',
			payment_type		= '$ipnVariables[payment_type]',
			residence_country	= '$ipnVariables[residence_country]',
			currency 			= '$ipnVariables[mc_currency]',
		ts_payment 			= NOW()
		WHERE id = $txnid";
	if(!$this->db->query($up)) 
	{
		echo 'ERROR 8';
		$this->alertemail('SQL insert into payment fail: '.$this->db->_error_message().'. data: '.$data);

		return false;

	} else {
		$q = "SELECT * FROM shop_payments WHERE id=$txnid AND payment_status = 'VERIFIED' LIMIT 1"; 
		$r = $this->db->query($q) or die('ERROR 9 '.$this->db->_error_message());
		$info = $r->result_object();

		$this->sendOrderEmail($this->getOrderInfo($info));
		echo "OK-$txnid";
		//$this->sendSms();
		return true;
	}
}

private function payplug()
{		

	$debug = true;

	$CI =& get_instance();
	$CI->load->library('mmail');
	
	require_once("../../payplug_php/lib/Payplug.php");
	Payplug::setConfigFromFile("../../payplug_php/parameters.json");

	try {
		$ipn = new IPN();

		$message = "IPN received for ".$ipn->firstName." ".$ipn->lastName
			. " for an amount of ".(($ipn->amount)/100)." EUR isTest: ".$ipn->isTest." TXNID: ".$ipn->customData." ID: ".$ipn->idTransaction;
		if($debug) $this->alertemail($message);
	} catch (InvalidSignatureException $e) {
		$this->alertemail('IPN failed, The signature was invalid');
		echo "ERROR 1";
		return false;
	}

	if($ipn->isTest AND $debug == false) {
		echo "ERROR 5";
		$this->alertemail('Receive an IPN in test mode');
		return false;
	}

	$txnid = trim($ipn->customData); 

	if($debug) error_log("txnid: $txnid");
	$query = $this->db->query("SELECT * FROM `shop_payments` WHERE id = $txnid LIMIT 1");
	$row_cnt = $query->num_rows;
	if($debug) error_log("row_cnt: $row_cnt");

	if ($row_cnt != 1) {
		echo "ERROR 6";
		$this->alertemail('Cannot find TXN id in payment db');
		return false;
	}

	$row = $query->result_array();

	$ipn_amount = trim($ipn->amount)/100;

	if ($row[0]['amount'] != $ipn_amount)  {
		echo "ERROR 7";
		if($debug) error_log('Amount is different from init to payment: '.$row[0]['amount'].' '.$ipn_amount);
		$this->alertemail('Amount is different from init to payment: '.$row[0]['amount'].' '.$ipn_amount);
		return false;
	}

	$payment_status = 'VERIFIED';
	if ($ipn->state =! 'paid')  {
		$this->alertemail('Incorrect payment status');
		$payment_status = 'FAIL';
		echo "ERROR 10";
		if($debug) error_log("IPN ERROR : payment status : $payment_status");
		return false; 
	}

	//record payment
	$up = "UPDATE `shop_payments` SET 
		transaction_id		= '$ipn->idTransaction',
		payment_provider	= 'payplug',
		payment_status		= '$payment_status',
		payment_type		= '',
		residence_country	= '',
		currency 			= 'EUR',
	ts_payment 			= NOW()
	WHERE id = $txnid";
if(!$this->db->query($up)) 
{
	echo 'ERROR 8';
	if($debug) error_log("IPN error 8");
	$this->alertemail('IPN SQL insert into payment fail: '.$this->db->_error_message());
	return false;

} else {
	$q = "SELECT * FROM shop_payments WHERE id=$txnid AND payment_status = 'VERIFIED' LIMIT 1"; 
	$r = $this->db->query($q) or die('ERROR 9 '.$this->db->_error_message().error_log('ERROR 9 '.$this->db->_error_message()));
	$info = $r->result_object();

	$order_info = $this->getOrderInfo($info);
	
	$this->hmw->sendNotif($order_info);
	
	$sms = array();
	$sms['to']			= "email2sms@ovh.net";
	$sms['subject'] 	= "sms-dp131762-1:hanksms:pass:HNKMOORD:+33647384930:::1";
	$sms['msg'] 		= $order_info;
	$CI->mmail->sendEmail($sms);

	$this->CallNumber();

	echo "OK-$txnid";

	if($debug) $this->alertemail('IPN OK');
		
	return true;
}
}

public function index()
{
	$this->payplug();
}

}
?>