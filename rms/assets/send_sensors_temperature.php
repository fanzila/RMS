<?
$dev = false;
set_time_limit(60);
$command="sudo /usr/bin/digitemp_DS9097U -q -c /etc/digitemp.conf -a -o '%s | %Y-%m-%d %H:%M:%S | %.2C'";
if(!$dev) exec($command, $output, $ret);

if($dev) { 
	$jdata = '["0 | 2015-10-27 22:11:34 | 5.69","1 | 2015-10-27 22:11:36 | 14.12","2 | 2015-10-27 22:11:37 | 20.44","3 | 2015-10-27 22:11:38 | 18.12","4 | 2015-10-27 22:11:39 | 7.69","5 | 2015-10-27 22:11:40 | 25.00","6 | 2015-10-27 22:11:41 | -9.00","7 | 2015-10-27 22:11:42 | -2.56","8 | 2015-10-27 22:11:43 | -13.56","9 | 2015-10-27 22:11:44 | -0.56","10 | 2015-10-27 22:11:45 | 24.25"]';
} else { 
	$jdata = json_encode($output);
}
$ch = curl_init();

if($dev) { 
	curl_setopt($ch, CURLOPT_URL,"http://rms.dev/sensors/record");
} else {
	curl_setopt($ch, CURLOPT_URL,"http://rms.hankrestaurant.com/sensors/record");
}
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, array('data' => $jdata));
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT ,30);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$server_output = curl_exec($ch);

if($dev) var_dump($server_output);
curl_close ($ch);

?>