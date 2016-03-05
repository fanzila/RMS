<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8" />
	<title>Hank Take Away Order</title> 
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="msapplication-tap-highlight" content="no" />
	<link rel="stylesheet" href="../../public/order/order.css" />	
</head>
<body>
<center>
<? if($result) { ?>
	
	<div style="background-color: #fff; margin: 20px; padding:20px; border-radius: 10px;">
		<p><h3>Merci pour votre commande, vous n'avez plus qu'a venir la chercher!</h3>
			Une question? <br /> Contactez-nous : <a href="mailto:contact@hankburger.com">contact@hankburger.com</a>  <br />ou par téléphone : <a href="tel:+33972440399">+33 9 72 44 03 99</a></p>
	</div>
	
	<hr />
	
	<div style="background-color: #fff; margin: 20px; padding:20px; border-radius: 10px;">
		<p><h3>Thank you! You just have to pickup your order!</h3>
		Any question? <br />Reach us by email: <a href="mailto:contact@hankburger.com">contact@hankburger.com</a>  <br />or by phone: <a href="tel:+33972440399">+33 9 72 44 03 99</a></p>
	</div>
<? } else { ?>
	<div style="background-color: #fff; margin: 20px; padding:20px; border-radius: 10px;">
		<p><h3>Votre commande n'a pas abouti, quelque chose n'a pas fonctionné...</h3>
		Essayer de recommencer ou/et contactez-nous : <a href="mailto:contact@hankburger.com">contact@hankburger.com</a>  <br />ou par téléphone: <a href="tel:+33972440399">+33 9 72 44 03 99</a></p>
	</div>
	
	<hr />
	
	<div style="background-color: #fff; margin: 20px; padding:20px; border-radius: 10px;">
		<p><h3>Something went wrong with you order...</h3>
		Please try again and/or reach us by email: <a href="mailto:contact@hankburger.com">contact@hankburger.com</a>  <br />or by phone: <a href="tel:+33972440399">+33 9 72 44 03 99</a></p>
	</div>
<? } ?>
</center>
</body>
</html>