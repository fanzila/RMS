<?php 

require_once('guest/s/default/params.php');

try {
  $dbh = new PDO('mysql:dbname=hotspot;host=localhost', $mysql_user, $mysql_pass);
} catch (PDOException $e){
  echo "Erreur !: " . $e->getMessage() . "<br/>";
  die();
}

$sql = "SELECT value FROM params WHERE name = 'RMS_last_id' LIMIT 1";
$query = $dbh->prepare($sql);
$query->execute();
$res = $query->fetch(PDO::FETCH_ASSOC);

if (isset($res['value'])) {
  $currLastID = $rev['value'];
  $sql = "SELECT * FROM creds WHERE id > " . $currLastID;
  $query = $dbh->prepare($sql);
  $query->execute();
  $res = $query->fetchAll(PDO::FETCH_ASSOC);
} else {
  $sql = "SELECT * FROM creds";
  $query = $dbh->prepare($sql);
  $query->execute();
  $res = $query->fetchAll(PDO::FETCH_ASSOC);
}
$jdata = array();
if (isset($res) && !empty($res)) {
  $jdata = json_encode($res);
}

$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, "http://rms.hankrestaurant.com/customers/record");
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, array('data' => $jdata));
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
#curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
#curl_setopt($ch, CURLOPT_PORT,  443);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$server_output = curl_exec($ch);
$ret = json_decode($server_output, true);
curl_close($ch);
if (isset($ret['lastID']) && is_numeric($ret['lastID'])) {
  $lastID = $ret['lastID'];
} else {
  $sql = "SELECT MAX(id) FROM CREDS";
  $query = $dbh->prepare($sql);
  $query->execute();
  $res = $query->fetch(PDO::FETCH_ASSOC);
  if (isset($res['id'])) {
    $lastID = $res['id'];
  } else {
    $lastID = 0;
  }
}
$sql = "SELECT value FROM params WHERE name = 'RMS_last_id' LIMIT 1";
$query->dbh->prepare($sql);
$query->execute();
$res = $query->fetch(PDO::FETCH_ASSOC);
if (isset($res['value'])) {
  $sql = "UPDATE params SET value = $lastID WHERE name = 'RMS_last_id'";
} else {
  $sql = "INSERT INTO params VALUES (NULL, 'RMS_last_id', $lastID)";
}
$query->dbh->prepare($sql);
$query->execute();

?>