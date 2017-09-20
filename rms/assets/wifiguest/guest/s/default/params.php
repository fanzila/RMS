<?php
  $mysql_db = "hotspot";
  $mysql_host = "localhost"; //If the MySQL server is running on the same server as the web auth page
  $mysql_user = "root"; //Change to the appropriate user that has access to DB/table
  $mysql_pass = "incorrect"; //Change to the appropriate password for the user above
  $table = "creds";  //Change to the appropriate table name

  //UniFi server credentials
  $unifiServer = "https://10.9.5.72:8443"; //Change to the IP/FQDN of your UniFi Server
  //It's important to note that if this server is offsite, you need to have port 8443 forwarded through to it
  $unifiUser = "admin"; //Change to your UniFi Username
  $unifiPass = "gxistfd123"; //Change to your UniFi Password
  $unifiControllerVersion = "5.4.15";
  $unifiSite = "default";
  $duration = 240; //Defines how long a guest stays connected to the wifi, in minutes;
?>