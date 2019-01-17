<?php

class Myfox {

	public function main($levelrequest = null)
	{		
		$debug = true;
		// Cyril E      http://www.ituilerie.com
		// Pour changer l'etat de l'alarme   levelrequest= armed  partial   ou   disarmed

		$CI = & get_instance(); 
		$CI->load->database();
		$CI->load->library('hmw');
		
		$password= $CI->hmw->getParam('myfox_user');    // Mot de passe du compte
		$username= $CI->hmw->getParam('myfox_pass');       // Non d'utilisateur (mail)
		$client_id = $CI->hmw->getParam('myfox_client_id'); // Client ID, s'incrire a l'API
		$client_secret = $CI->hmw->getParam('myfox_client_secret');  // Client secret

		$siteid = 0 ;  // Site ID si vous l'avez deja, sinon laisser 0

		// Authentification
		$curl = curl_init( 'https://api.myfox.me/oauth2/token' );
		curl_setopt( $curl, CURLOPT_POST, true );
		curl_setopt( $curl, CURLOPT_POSTFIELDS, array(
		    	'grant_type' => 'password',
		        'client_id' => $client_id,
		        'client_secret' => $client_secret,
		        'username' => $username,
		    	'password' => $password
		) );
		
		if($debug) { 
			echo "
        		'client_id' => $client_id,
        		'client_secret' => $client_secret,
        		'username' => $username,
    			'password' => $password
				";
		}

		curl_setopt( $curl, CURLOPT_RETURNTRANSFER, 1);
		$auth = curl_exec( $curl );
		if($debug) var_dump($auth);
		$secret = json_decode($auth);
		$token = $secret->access_token;
		if($debug) echo $token;

		// Obtention du site ID
		if ($siteid==0){
		$api_url = "https://api.myfox.me:443/v2/client/site/items?access_token=" . $token;
		$requete = @file_get_contents($api_url);
		$json_result = json_decode($requete,true);
		$siteid = $json_result["payload"]["items"][0]["siteId"];    // On prend en compte le premier site de la liste, si plusieur Sites remplacer la valeur 0 par 1 2 3.....(non testé), le site ID peut etre trouvé dans votre compte, sa valeur ne change pas
		}


		// Obtenir Etat de l'alarme
		$api_url2 = "https://api.myfox.me:443/v2/site/" .$siteid. "/security?access_token=" . $token;
		$requete2 = @file_get_contents($api_url2);
		$json_result2 = json_decode($requete2,true);
		$statusvalue = $json_result2["payload"]["status"];
		$statuslabel = $json_result2["payload"]["statusLabel"];

		echo '<?xml version="1.0" encoding="utf8" ?>';
		echo "<myfox>";
		echo "<statusvalue>" . $statusvalue  . "</statusvalue>";
		echo "<statuslabel>" . $statuslabel  . "</statuslabel>";
		echo "</myfox>";


		// Changer l'etat de l'alarme
		if (!empty($levelrequest))
		{
		$api_url3 = "https://api.myfox.me:443/v2/site/" .$siteid. "/security/set/".$levelrequest."?access_token=" . $token;
		$curl2 = curl_init( $api_url3 );
		curl_setopt( $curl2, CURLOPT_POST, true );
		curl_setopt( $curl2, CURLOPT_RETURNTRANSFER, 1);
		$return = curl_exec( $curl2 );
		}

	}

}
?>