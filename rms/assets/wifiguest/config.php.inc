<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');

require_once ('params.php');

//Establish connection the MySQL server using credentials as above.  If connection cannot be made, the process will fail and produce a troubleshooting error
$mysql = mysql_connect($mysql_host,$mysql_user,$mysql_pass);
	if(!$mysql) {
		die("Database connection failed: " . mysql_error());
	}

//Select the appropriate database for credential storage
$db_select = mysql_select_db($mysql_db,$mysql);
	if(!$db_select) {
		die("Database selection failed: " . mysql_error());
	}

//The function to authenticate a user using Username, Password, and Email as authentication paramaters then return a true boolena to the auth.php page
function auth_user($username,$password,$email,$table) {
	$query = "SELECT * FROM `$table` WHERE `username` = \"$username\"";
	$execute = mysql_query($query);
	while($row = mysql_fetch_assoc($execute)) {
		if(($row['password'] == $password) && ($row['email'] == $email)) {
			echo "true";
			return true;
		} else {
			echo "false";
			echo "Your username, password, or email was not found.<br />You will be redirected to the login page momentarily";
			sleep(5);
			header("Location: auth.php");
		}
	}
//If not requiring email as an authentication parameter, comment out the above while loop and uncomment the following while loop
/*
	while($row = mysql_fetch_assoc($execute)) {
		if($row['password'] == $password) {
			echo "true";
			return true;
		} else {
			echo "false";
			echo "Your username and/or password was not found.<br />You will be redirected to the login page momentarily";
			sleep(5);
			header("Location: auth.php");
		}
	}
*/
}



function sendAuthorization($id, $ap, $minutes, $url) {
	global $unifiServer;
	global $unifiUser;
	global $unifiPass;

	// Start Curl for login
	$ch = curl_init();
	// We are posting data
	curl_setopt($ch, CURLOPT_POST, TRUE);
	// Set up cookies
	$cookie_file = "/tmp/unifi_cookie";
	curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_file);
	curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_file);
	// Allow Self Signed Certs
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
	// Force SSL3 only
	curl_setopt($ch, CURLOPT_SSLVERSION, 3);
	// Login to the UniFi controller
	curl_setopt($ch, CURLOPT_URL, "$unifiServer/login");
	curl_setopt($ch, CURLOPT_POSTFIELDS,"login=login&username=$unifiUser&password=$unifiPass");
	curl_exec ($ch);
	curl_close ($ch);

	// Send user to authorize and the time allowed
	$data = json_encode(array(
		'cmd'=>'authorize-guest',
        'mac'=>$id,
		'ap'=>$ap,
        'minutes'=>$minutes));
	$ch = curl_init();
	// We are posting data
	curl_setopt($ch, CURLOPT_POST, TRUE);
	// Set up cookies
	$cookie_file = "/tmp/unifi_cookie";
	curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_file);
	curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_file);
	// Allow Self Signed Certs
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
	// Force SSL3 only
	curl_setopt($ch, CURLOPT_SSLVERSION, 3);
	// Make the API Call
	curl_setopt($ch, CURLOPT_URL, $unifiServer.'/api/cmd/stamgr');
	curl_setopt($ch, CURLOPT_POSTFIELDS, 'json='.$data);
	curl_exec ($ch);
	curl_close ($ch);
  
	// Logout of the connection
	$ch = curl_init();
	// We are posting data
	curl_setopt($ch, CURLOPT_POST, TRUE);
	// Set up cookies
	$cookie_file = "/tmp/unifi_cookie";
	curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_file);
	curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_file);
	// Allow Self Signed Certs
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
	// Force SSL3 only
	curl_setopt($ch, CURLOPT_SSLVERSION, 3);
	// Make the API Call
	curl_setopt($ch, CURLOPT_URL, $unifiServer.'/logout');
	curl_exec ($ch);
	curl_close ($ch);
	echo "Login successful, I should redirect to: " . $url; //$_SESSION['url'];
	sleep(8); // Small sleep to allow controller time to authorize
	header('Location: ' . $url);//$_SESSION['url']);
}
?>