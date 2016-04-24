<?php

class Gajax  {

	/*
	Gère le script Javascript nécessaire à l'utilisation d'ajax
	Il suffit de mettre PrintJavascript(); entre les balises <head> et </head>
	*/
	public function printJavascript() {
		$return = "";

		$return .= "<script type=\"text/javascript\">
		function request(url,cadre) {
			var XHR = null;

			if(window.XMLHttpRequest) // Firefox
				XHR = new XMLHttpRequest();
			else if(window.ActiveXObject) // Internet Explorer
				XHR = new ActiveXObject(\"Microsoft.XMLHTTP\");
			else { // XMLHttpRequest non supporté par le navigateur
				alert(\"Votre navigateur ne supporte pas les objets XMLHTTPRequest. Ajax ne eut donc pas être exécuté.\");
				return;
			}
			// envoie de la requête, methode GET et de l'url
			XHR.open(\"GET\",url, true);

			// on guette les changements d'état de l'objet
			XHR.onreadystatechange = function attente() {

				// l'état est à 4, requête reçu !
				if(XHR.readyState == 4)     {

					// ecriture de la réponse
					document.getElementById(cadre).innerHTML = XHR.responseText;
				}
			}
			XHR.send(null);		// le travail est terminé
			return;
		}
		</script>";

		return $return;
	}

/*
Cette fonction permet d'interroger Gajax.

Il faut lui transférer trois paramètres :
- $page_a_charger qui contient la page à appeler et qui doit être appelée lors du clic sur le lien
- $intitule_lien est tout simplement l'intitulé du lien
- $nom_module est le nom qui fera le lien avec ReponseGajax();

L'usage de la fonction InterrogeGajax(); est facultatif, il est également possible d'utiliser un lien du type :
<a href="" onclick="request('lien','nom_du_module');return(false)">Intitulé</a>
*/
public function interrogeGajax($page_a_charger, $intitule_lien, $nom_module) {
	$return = "<a href=\"\" onclick=\"request('".$page_a_charger."','".$nom_module."');return(false)\">".$intitule_lien."</a>";

	return $return;
}

/*
Cette fonction gère la réponse de Gajax à l'appel de InterrogeGajax();
Elle peut également être utilisée sans InterrogeGajax(); en utilisant le lien de remplacement expliqué avec cette dernière.

Il faut lui transférer un seul paramètre :
- $nom_module est le nom qui fait le lien avec InterrogeGajax();

Il suffit de placer cette fonction à l'endroit ou l'on souhaite voir affiché le résultat.
*/
public function reponseGajax($nom_module) {
	$return = "<div id=\"".$nom_module."\"></div>";

	return $return;
}

/*
Paramètre $message : Message à afficher

Résultat : Affiche un message dans une boite comme les titres avec un lien de fermeture Gajax
Format du résultat : boite + titre
*/
public function boiteMessageGajax($titre) {
	return "<div id=\"mailbox_message_info\">
		<div class=\"boite_message\">
		<div class=\"options\"><a href=\"\" onclick=\"request('blank.php','mailbox_message_info');return(false)\">[Fermer]</a></div>
	".$titre."
		</div>
	</div>";
}

}
?>