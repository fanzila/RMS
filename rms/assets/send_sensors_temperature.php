<?php
$dev = true;
set_time_limit(60);
$command="sudo /usr/bin/digitemp_DS9097U -q -c /etc/digitemp.conf -a -o '%s | %Y-%m-%d %H:%M:%S | %.2C'";
if(!$dev) exec($command, $output, $ret);

if($dev) { 
	$jdata = '["0 | 2016-06-22 22:00:34 | 30","1 | 2016-06-21 22:11:36 | 30","2 | 2016-06-20 22:11:37 | 30","3 | 2016-06-19 22:11:38 | 30","4 | 2016-06-18 22:11:39 | 30","5 | 2016-06-17 22:11:40 | 30","6 | 2016-06-16 22:11:41 | 30","7 | 2016-06-15 22:11:42 | 30","8 | 2016-06-14 22:11:43 | 30","9 | 2016-06-13 22:11:44 | 30","10 | 2016-06-12 22:11:45 | 30"]';
} else { 
	$jdata = json_encode($output);
}
$ch = curl_init();

if($dev) { 
	curl_setopt($ch, CURLOPT_URL,"http://forkrms.dev/sensors/record");
} else {
	curl_setopt($ch, CURLOPT_URL,"http://rms.hankrestaurant.com/sensors/record");
}
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, array('data' => $jdata));
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT ,30);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$server_output = curl_exec($ch);

if($dev){var_dump($server_output);}
curl_close ($ch);

?>
