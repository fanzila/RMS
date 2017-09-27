<?php 

require_once('guests/s/default/params.php');

try {
  $dbh = new PDO('mysql:dbname=hotspot;host=localhost', $mysql_user, $mysql_pass);
} catch (PDOException $e){
  echo "Erreur !: " . $e->getMessage() . "<br/>";
  die();
}

$sql = "SELECT value FROM params WHERE name = 'RMSlastID' LIMIT 1";
$query = $dbh->prepare($sql);
$query->execute();
$res = $query->fetch(PDO::FETCH_ASSOC);

if (isset($res['value'])) {
  $currLastID = $rev['value'];
  $sql = "SELECT * FROM creds WHERE ID > " . $currLastID;
  $query = $dbh->prepare($sql);
  $query->execute();
  $res = $query->fetchAll(PDO::FETCH_ASSOC);
} else {
  $sql = "SELECT * FROM creds";
  $query = $dbh->prepare($sql);
  $query->execute();
  $res = $query->fetchAll(PDO::FETCH_ASSOC);
}
  var_dump($res);
  die();

?>