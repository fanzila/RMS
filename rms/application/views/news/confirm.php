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
<p><b>Merci</b> pour ta confirmation!<br>

<? } else { ?>

<h1>Ooooops! </h1>
<p>Trop chelou, la news que tu tentes de confirmer est introuvable :-(</p>
<? } ?>
<p>Have A Nice Karma!</p>
</div>		
</body>
</html>