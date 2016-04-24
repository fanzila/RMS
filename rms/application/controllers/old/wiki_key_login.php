<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Wiki_key_login extends CI_Controller {

	public function __construct()
	{

		parent::__construct();

	}

	public function index()
	{

		$this->load->library('hmw');

		$loginkey = "";
		
		$page = $this->input->get('page');
		if(!isset($page)) $page = "start";
		
		$getkey = $this->input->get('keylogin');

		if(!isset($getkey)) {
			echo "invalid credidentials";
			exit();
		}

		if($loginkey != $getkey) {
			echo "invalid credidentials";
			exit();
		}
		
		$wuser = $this->hmw->getParam('keylogin_wiki_user');
		$wpass = $this->hmw->getParam('keylogin_wiki_pass');

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, 'http://vm/hank/intranet/start');
		curl_setopt($ch, CURLOPT_USERAGENT,'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Ubuntu Chromium/32.0.1700.107 Chrome/32.0.1700.107 Safari/537.36');
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_VERBOSE, 1);
		curl_setopt($ch, CURLOPT_HEADER, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, "u=$wuser&p=$wpass&id=start&do=login");
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_COOKIESESSION, true);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_COOKIEJAR, 'cookie-name');  //could be empty, but cause problems on some hosts
		curl_setopt($ch, CURLOPT_COOKIEFILE, '/tmp');  //could be empty, but cause problems on some hosts

		// forward current cookies to curl
		$cookies = array();
		foreach ($_COOKIE as $key => $value)
		{
		    if ($key != 'Array')
		    {
		        $cookies[] = $key . '=' . $value;
		    }
		}
		curl_setopt( $ch, CURLOPT_COOKIE, implode(';', $cookies) );
		session_write_close();
		$response = curl_exec($ch);
		if (curl_error($ch)) {
		    //echo curl_error($ch);
		}
		//curl_close($ch);
		session_start();
		list($header, $body) = explode("\r\n\r\n", $response, 2);
		// extract cookies form curl and forward them to browser
		preg_match_all('/^(Set-Cookie:\s*[^\n]*)$/mi', $header, $cookies);
		foreach($cookies[0] AS $cookie)
		{
		     header($cookie, false);
		}

		//echo $body;

		header("Location: http://vm/hank/intranet/$page"); /* Redirection du navigateur */

	
	}
	
}
