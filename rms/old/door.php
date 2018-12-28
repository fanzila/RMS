<?php
class Door extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->database();
		$this->load->library("hmw");
	}

	public function index($action = null)
	{
		
		$this->hmw->changeBu();// GENERIC changement de Bu
		$this->hmw->keyLogin();
		
		$id_bu 		= $this->session->all_userdata()['bu_id'];
		$bu_name	= $this->session->all_userdata()['bu_name'];
		$msg		= null;	
		$status		= null;
		$buinfo		= $this->hmw->getBuInfo($id_bu);
		$log		= array();
		
		if(isset($buinfo->door_device)) $this->data['door_device'] = $buinfo->door_device;
			
		if(empty($buinfo->door_device)) exit('No device for this BU'); 			
		
		
		if($action) { 
			
			$curlcmd = 'curl -c /tmp/cookies_jar.txt -b /tmp/cookies_jar.txt';
			
			$command_init = $curlcmd. ' \'https://somfy.opendoors.net/index.php?page=account/login&\' \
-H \'Origin: https://somfy.opendoors.net\' \
-H \'Accept-Encoding: gzip, deflate, br\' \
-H \'Accept-Language: en-US,en;q=0.8,fr;q=0.6\' \
-H \'Upgrade-Insecure-Requests: 1\' \
-H \'User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_12_3) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/56.0.2924.87 Safari/537.36\' \
-H \'Content-Type: application/x-www-form-urlencoded\' \
-H \'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8\' \
-H \'Cache-Control: max-age=0\' \
-H \'Referer: https://somfy.opendoors.net/index.php?page=account/login\' \
-H \'Connection: keep-alive\' \
--data "LOGIN='.$buinfo->door_login.'&PASSWORD='.$buinfo->door_pass.'&SIGNIN=" --compressed';

			
			if($action == 1) {

				$log['val1'] = 'open';
				$command_2nd = $curlcmd. ' \'https://somfy.opendoors.net/index.php?page=gateway/store_action&dv='.$buinfo->door_device.'&a=1&json\' \
-H \'Accept-Encoding: gzip, deflate, sdch, br\' \
-H \'Accept-Language: en-US,en;q=0.8,fr;q=0.6\' \
-H \'User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_12_3) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/56.0.2924.87 Safari/537.36\' \
-H \'Accept: application/json, text/javascript, */*; q=0.01\' \
-H \'Referer: https://somfy.opendoors.net/index.php?page=main\' \
-H \'X-Requested-With: XMLHttpRequest\' \
-H \'Connection: keep-alive\' \
--compressed';
								
			}
			
			if($action == 2) {
			
				$log['val1'] = 'close';
				$command_2nd = $curlcmd. ' \'https://somfy.opendoors.net/index.php?page=gateway/store_action&dv='.$buinfo->door_device.'&a=2&json\' \
-H \'Accept-Encoding: gzip, deflate, sdch, br\' \
-H \'Accept-Language: en,fr;q=0.8,en-US;q=0.6\' \
-H \'User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_12_3) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/56.0.2924.87 Safari/537.36\' \
-H \'Accept: application/json, text/javascript, */*; q=0.01\' \
-H \'Referer: https://somfy.opendoors.net/index.php?page=main\' \
-H \'X-Requested-With: XMLHttpRequest\' \
-H \'Connection: keep-alive\' \
--compressed';
			}

			exec($command_init, $output, $ret);
			exec($command_2nd, $output, $status);
			$msg = $ret. ' R: '.$output[0];
			$log['type'] = 'door';
			$this->hmw->LogRecord($log, $id_bu);
				
		}
		
		$data = array(
			'title'		=> 'Door',
			'keylogin'	=> $this->session->userdata('keylogin'),
			'msg' 		=> $msg,
			'status'	=> $status,
			'bu_name'	=> $this->session->all_userdata()['bu_name']
			);
		
		$headers = $this->hmw->headerVars(1, "/news/index/", "Door");
		$this->load->view('jq_header_pre', $headers['header_pre']);
		$this->load->view('jq_header_post', $headers['header_post']);
		$this->load->view('door',$data);
		$this->load->view('jq_footer');
	}	
}
?>
