<?php
//error_reporting(E_ALL);
//ini_set('display_errors', '1');

require_once('Unifi-API-client/src/Client.php');
require_once('params.php');

$post = $_POST;
$date = date('Y-m-d H:i:s');
$debug = false;
$clientMac = '';

if (isset($_POST['id'])) {
  $clientMac = trim($_POST['id']);
}

try {
  $dbh = new PDO('mysql:host='. $mysql_host . ';dbname=' . $mysql_db, $mysql_user, $mysql_pass);
} catch (PDOException $e){
    echo "Erreur !: " . $e->getMessage() . "<br/>";
    die();
}

if (isset($post['submitLogIn'])) {
  if (isset($post['InputCond'])) {
    if (isset($post['InputEmail1'])) {
      $userEmail = trim($post['InputEmail1']);
      if (isset($post['InputOptOut'])) {
        $sql = "INSERT INTO " . $table . " VALUES (NULL, '" . $userEmail . "', '" . $_SERVER['REMOTE_ADDR'] . "', '" . $_SERVER['HTTP_USER_AGENT'] . "', '" . $clientMac . "', true, '" . $date . "')";
        $query = $dbh->prepare($sql);
        if ($query->execute() === true) {
          $sql = "DELETE FROM " . $table . " WHERE date < DATE_SUB(NOW(), INTERVAL 2 YEAR)";
          $query = $dbh->prepare($sql);
          $query->execute();
        }
      } else {
        $sql = "INSERT INTO " . $table . " VALUES (NULL, '" . $userEmail . "', '" . $_SERVER['REMOTE_ADDR'] . "', '" . $_SERVER['HTTP_USER_AGENT'] . "', '" . $clientMac . "', false, '" . $date . "')";
        $query = $dbh->prepare($sql);
        $query->execute();
        if ($query->execute() === true) {
          $sql = "DELETE FROM " . $table . " WHERE date < DATE_SUB(NOW(), INTERVAL 2 YEAR)";
          $query = $dbh->prepare($sql);
          $query->execute();
        }
      }
      if (isset($post['InputPass'])) {
        $userPass = trim($post['InputPass']);
        $sql = "SELECT wifi_pass FROM params LIMIT 1";
        $query = $dbh->prepare($sql);
        $query->execute();
        $result = $query->fetch(PDO::FETCH_ASSOC);
        $wifi_pass = $result['wifi_pass'];
        if (password_verify($userPass, $wifi_pass) === true) {
          $unifi_connection = new UniFi_API\Client($unifiUser, $unifiPass, $unifiServer, $unifiSite, $unifiControllerVersion);
          $set_debug_mode   = $unifi_connection->set_debug($debug);
          $login = $unifi_connection->login();
          $auth_result = $unifi_connection->authorize_guest($clientMac, $duration);
          header('Location: ' . $post['url']);
        } else {
          header('Location: index.php/?pass_error=true');
        }
      } else {
        die ('password required');
      }
    } else {
      die("Please input an email");
    }
  } else {
    die("Unable to connect you: you haven't accepted our terms of use. <br/> Redirecting you to the login page.");
  }
} else {
  die("Forbidden");
}
?>