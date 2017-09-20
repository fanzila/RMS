<!DOCTYPE html>
<?php
/**
if(!isset($_GET['id'])) exit("Not allowed");
//Start session to grab MAC Address and have it available throughout the auth process
session_start();
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
$url = 'http://www.ubnt.com';
}

**/
?>
<html>
<head>
	<title>HotSpot Login</title> <!--Put whatever you want displayed as the title of the tab-->
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
	<!-- Bootstrap -->
	<link href="/assets/wifiguest/guest/s/default/css/bootstrap.min.css" type="text/css" rel="stylesheet">
	<link href="/assets/wifiguest/guest/s/default/css/wifiguest.css" type="text/css" rel="stylesheet">
	<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
	<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
	<!--[if lt IE 9]>
	<script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
	<script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
	<![endif]-->
</head>
<style>

</style>
<body>
	<div class="container">
		<br />
		<div class="jumbotron">
				<img src="/assets/wifiguest/guest/s/default/logo.png" class="logo">
			<br /><br />
			<? if (isset($_GET['pass_error'])) :?>
				<p style="color: red;"> Field "password" is incorrect, please retry. </p>
			<?endif;?>
			<form action="auth.php" method="POST">
				<div class="form-group">
					<label for="InputEmail1">Adresse Email / Email Address</label>
					<input type="email" class="form-control" id="InputEmail1" name="InputEmail1" aria-describedby="emailHelp" placeholder="Insérer email / Enter email" required>
					<small id="emailHelp" class="form-text text-muted">Article R.10-13 du CPCE, Décret de 24 mars 2006.</small><br />
					<small id="emailHelp" class="form-text text-muted">Nous ne partagerons jamais votre adresse email. /</small>
					<small id="emailHelp" class="form-text text-muted">We'll never share your email with anyone else.</small>
				</div>
				<div class="form-group">
					<input id="InputOptOut" name="InputOptOut" aria-describedby="InputOptOut" type="checkbox" aria-label="..."> 
					<label id="contactlbl" for="InputOptOut">Je ne veux pas être contacté par Hank / I do not want to be contacted by Hank</label>
				</div>
				<div class="form-group">
					<label for="InputPass">Mot de Passe / Password</label>
					<input type="password" class="form-control" id="InputPass" name="InputPass" aria-describedby="passHelp" placeholder="Enter Password" required>
					<small id="passHelp" class="form-text text-muted">Le mot de passe est écrit sur votre ticket de caisse / The password is written on your recipt</small>
				</div>				
				<div class="row">
			    <div class="col-lg-10">
						<div class="form-group">
							<input id="InputCond" name="InputCond" aria-describedby="InputCond" type="checkbox" aria-label="..." required> 
							<label id="cgu" for="InputCond">J'accepte les <a href="/assets/wifiguest/wificgu.html">Conditions Générales d'utilisation</a> / I agree with the <a href="wificgu.html">Terms Of Use</a></label>
					  </div> 
					</div> 
			  </div> 
			
				<button id="login-btn" name="submitLogIn" type="submit" class="btn btn-lg btn-success">Log In</button>
			</form>

		</div>
	</div>

	<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
	<!-- Include all compiled plugins (below), or include individual files as needed -->
	<script src="/assets/wifiguest/guest/s/default/js/bootstrap.min.js"></script>
</body>
</html>
