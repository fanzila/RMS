<html>
<head>
<title>Confirmation HANK</title>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="msapplication-tap-highlight" content="no" />
<style>
body {
	background-color: #f0efef;
	font: 20px "Lucida Grande", Lucida, Verdana, sans-serif;
	color: #413f3f;
}
</style>

</head>
<body>
<div style="width:70%;text-align: center;margin:auto;border: 1px groove #4f4e4e;padding: 30px;">	
<? if($status == 'OK') {
	?>
	
<h1>F O R M I D A B L E !</h1>
<p>Merci pour votre confirmation :-) <? if(!empty($scomment)) {?> et votre message! <? } ?></p>

<p><? if(!empty($scomment)) {?>Au besoin, vous pouvez le modifier ici :<? } else { ?>Une précision, une réponse ? <br>Dites nous cela ici : </p><? } ?>
<form action="/order/confirm/<?=$key?>" method="post">
<textarea name="scomment" rows="10" cols="50">
<? if(isset($scomment)) { echo $scomment; } ?>
</textarea><p>
<input type="submit" name="submit" style="padding:15px 15px; 
    background:#ccc; 
    border:0 none;
    cursor:pointer;
    -webkit-border-radius: 5px;
    border-radius: 5px;" value="Envoyer"></p>
<input type="hidden" name="key" value="<?=$key?>">
</form>
	
<? } else { ?>

<h1>Ooooops! </h1>
<p>La commande que vous tentez de confirmer est introuvable :-(</p>
Pouvez-vous prendre contact avec nous ? <br />Nos contacts sont sur le BDC.</p>
<? } ?>
<hr>
<p>HANK<br>Have A Nice Karma!</p>
</div>		
</body>
</html>