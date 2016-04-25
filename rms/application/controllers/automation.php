<?php
class Automation extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->library("hmw");
	}

	//enbale, disable, alert
	public function alarm($status)
	{
		$key = $this->hmw->getParam('keylogin');
		if($_GET['key'] != $key) exit('error A');

		if($status == 'alert') {
			//turn OFF alarm
			$cmd = array(
				'key'	=> $key,
				'order' => 'chacun', 
				'module' => 'switch', // or 'dimmer'
				'value' => 'off', // or '1' to '255'
				'id' => '5');
			//$this->hmw->callbox($cmd);
			
			//turn ON lights
			$cmd = array(
				'key'	=> $key,
				'order' => 'chacun', 
				'module' => 'switch', // or 'dimmer'
				'value' => 'on', // or '1' to '255'
				'id' => '4');
			$this->hmw->callbox($cmd);
			
			//turn ON ampli
			$cmd = array(
				'key'	=> $key,
				'order' => 'chacun', 
				'module' => 'switch', // or 'dimmer'
				'value' => 'on', // or '1' to '255'
				'id' => '6');
			$this->hmw->callbox($cmd);
		
			//send sms IFFTT
			$ifttmakerkey = $this->hmw->getParam('ifttmakerkey');
			$url = "https://maker.ifttt.com/trigger/SendSMS/with/key/$ifttmakerkey?value1=alarm_trigered";
			//$this->callUrl($url);
		
			//Active alarm sound
			$url = "https://maker.ifttt.com/trigger/AlarmToCall/with/key/$ifttmakerkey";
			//$this->callUrl($url);
		
			//log event //todo create log table
		
			//send sound message
			$cmd = array(
				'key'	=> $key,
				'order' => 'sound', 
				'jingle' => '', 
				'type' => 'audio', //or 'text'
				'message' => 'alarm.mp3'); // or text : 'Good morning planet.'
			$this->hmw->callbox($cmd);
		}
		
		if($status == 'enable') {
			//IFFTT armed
		}
		
		if($status == 'disable') {
			//IFFTT disarmed
		}
		
	}
	
	private function callUrl($url) {
		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT ,30);
		curl_setopt($ch, CURLOPT_TIMEOUT, 30);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$server_output = curl_exec($ch);
		//var_dump($server_output);
		curl_close ($ch);
	}
}
?>