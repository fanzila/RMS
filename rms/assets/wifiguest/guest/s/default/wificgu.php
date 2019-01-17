<!DOCTYPE html>
<?php
require_once('params.php');
if(!isset($_GET['id'])) exit("Not allowed");
//Get MAC Address and assign it to _SESSION variable to be available throughout the auth process
if($_GET['id']) {
$id = $_GET['id'];
} else {
exit("Not allowed");
}
//Get original target URL for redirect after auth
if ($_GET['url']) {
$url = $_GET['url'];
} else {
//If original URL not specified, default to ubnt.com
$url = $param_url;
}
?>
<html>
  <head>
    <meta charset="utf-8">
    <title>Conditions Générales d'utilisation - Hank Wifi</title>
    <meta charset="utf-8">
  	<meta http-equiv="X-UA-Compatible" content="IE=edge">
  	<meta name="viewport" content="width=device-width, initial-scale=1">
  	<!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
  	<!-- Bootstrap -->
  	<link href="/guest/s/default/css/bootstrap.min.css" type="text/css" rel="stylesheet">
  	<link href="/guest/s/default/css/wifiguest.css" type="text/css" rel="stylesheet">
  </head>
  <body>
    <div class="content">
      <div class="back-btn">
        <a class='btn btn-primary btn-light btn-success' href='/guest/s/default/index.php?id=<?=$id?>&url=<?=$url?>'>Back to the login page</a>
      </div>
      <div class="cgu-content">
        <h4>ARTICLE 1. ACCÈS AU RÉSEAU WIFI PUBLIC D'HANK</h4><br /><br />

        <p>Dans le cadre de son Offre, le Client pourra accéder, sans frais supplémentaire, au Réseau wifi public de HANK, en utilisant n’importe quel appareil compatible Wi-Fi (ordinateur, tablette, téléphone mobile …),sous réserve
        de respecter les conditions d’accès et d’utilisation énoncées ci-dessous.</p><br />

        <h5>1.1 Conditions d’accès et d’utilisation</h5>

        <p>Afin d’accéder au Réseau wifi public HANK en mode nomadisme, le Client devra : <br />
        - activer le Wi-Fi de son appareil ;<br />
        - sélectionner le nom du réseau Wi-Fi « HANK » parmi la liste des réseaux Wi-Fi à portée. Cette liste est visible dans les paramètres de connexion Wi-Fi de l’appareil utilisé ;<br />
        - lancer le navigateur Internet, la page d’accueil wifi de HANK s’affichera automatiquement ;<br />
        - saisir impérativement son adresse de messagerie dans la zone de connexion au réseau wifi de HANK.<br />
        Le Client ou tout utilisateur du Réseau wifi public de HANK reconnaît que l’utilisation des logiciels Peer-to-Peer et les
        protocoles de téléphonie sur IP ne sont pas autorisés sur le Réseau wifi public de HANK, et s’engage plus généralement à
        respecter l’ensemble des dispositions énoncées aux présentes concernant l’usage de l’internet.<br />
        En cas de non respect des présentes, HANK pourra suspendre tout ou partie de l’Offre dans les conditions énoncées aux articles Suspension / Résiliation.<br />
        HANK, SAS au capital de 1000 euros<br />
        18, rue des Gravilliers 75003 Paris</p><br />

        <h5>1.2 Qualité de service et sécurité et confidentialité des données</h5>

        <p>HANK n’est pas en mesure de garantir le respect d’une quelconque qualité de service lorsque le Client se
        connecte à Internet via le Réseau wifi public de HANK.
        Le Client ou l’utilisateur du Réseau wifi public de HANK reconnaît être informé que le niveau de protection des données
        transmises par voie radio est variable en fonction de son profil de configuration (équipement utilisé, logiciels de sécurité
        installés, …) et que ce niveau est susceptible de varier en fonction des paliers de fonctionnalités introduits par HANK.
        Les communications effectuées via le Réseau wifi public de HANK présentent en principe le même niveau de sécurité que
        les communications Internet standard.<br />
        HANK ne répond pas du fonctionnement de logiciels tiers de sécurité, que le Client ou l’utilisateur pourrait
        installer de lui-même pour augmenter son niveau de sécurité sur ses équipements.<br />
        Une protection absolue contre les intrusions ou les écoutes passives sur Internet ne peut être garantie (en raison de l’état
        de l’art). HANK décline toute responsabilité concernant de tels événements.<br />
        Le Client ou l’utilisateur reconnaît être informé que l'intégrité, l'authentification et la confidentialité des informations, fichiers
        et données de toute nature (code de carte de crédit, etc.) qu'il souhaite échanger sur le réseau Internet doivent faire l’objet
        d’une vigilance particulière.<br />
        Le Client ou l’utilisateur ne doit donc pas transmettre via le réseau Internet des messages dont il souhaiterait voir la
        confidentialité garantie de manière infaillible.</p><br />

        <h5>1.3 Cookies</h5>

        <p>Il peut arriver que certains fichiers, appelés "cookies", soient enregistrés sur l'ordinateur du Client lorsque ce dernier utilise
        le Réseau wifi public de HANK. Ces fichiers facilitent la navigation pour le Client et permettent à HANK
        d'offrir une meilleure qualité de service. En effet, les cookies mémorisent les données du Client pour que ce dernier n'ait pas
        à les saisir à nouveau lors de ses visites ultérieures. Le Client a la faculté de les neutraliser ou de les supprimer de son
        disque dur. L'attention du Client est attirée toutefois sur le fait que certains services proposés à travers la
        fonctionnalité ne seront pas accessibles ou ne le seront que partiellement s'il refuse les cookies. Si le Client souhaite être
        systématiquement informé de l'installation d'un cookie (via message d'avertissement) ou empêcher l'enregistrement d'un
        tel fichier, il lui suffit de configurer son navigateur Internet en conséquence.</p>
      </div>
      <div class="back-btn back-btn-bottom">
        <a class='btn btn-primary btn-light btn-success' href='/guest/s/default/index.php?id=<?=$id?>&url=<?=$url?>'>Back to the login page</a>
      </div>
    </div>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
  	<!-- Include all compiled plugins (below), or include individual files as needed -->
  	<script src="/guest/s/default/js/bootstrap.min.js"></script>
  </body>
</html>