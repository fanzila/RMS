<?php
//error_reporting(E_ALL);
//ini_set('display_errors', '1');

	require_once 'config.php.inc';

	$user = trim($_POST['username']);
	$password = trim($_POST['password']);
	$email = trim($_POST['email']);
	$mac = trim($_POST['mac']);
	$ap = trim($_POST['ap']);
	$url = trim($_POST['url']);
	
	$auth = auth_user($user,$password,$email,$table);
	echo $url;
	if($auth) {
		//Minutes to authorize, change to suit your needs
		$minutes = 120;
		sendAuthorization($mac,$ap,$minutes,$url);
	}

?>