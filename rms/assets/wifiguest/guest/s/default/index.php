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
	<title>HotSpot Login</title> <!--Put whatever you want displayed as the title of the tab-->
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
	<!-- Bootstrap -->
	<link href="/guest/s/default/css/bootstrap.min.css" type="text/css" rel="stylesheet">
	<link href="/guest/s/default/css/wifiguest.css" type="text/css" rel="stylesheet">
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
				<img src="/guest/s/default/logo.png" class="logo">
			<br /><br />
			<?php if (isset($_GET['pass_error'])) :?>	
				<p style="color: red;"> Field "password" is incorrect, please retry. </p>
			<?php endif;?>
			<form action="/guest/s/default/auth.php" method="POST">
				<div class="form-group">
					<label for="InputEmail1">Adresse Email / Email Address <br />Nous ne partagerons ABSOLUMENT JAMAIS votre adresse email avec PERSONNE.<br />We'll NEVER EVER share your email with ANYONE else.
					</label>
					<input type="email" class="form-control" id="InputEmail1" name="InputEmail1" aria-describedby="emailHelp" placeholder="Insérer email / Enter email" required>
					<small id="emailHelp" class="form-text text-muted">Article R.10-13 du CPCE, Décret de 24 mars 2006.</small>
				</div>
				<div class="form-group">
					<input id="InputOptOut" name="InputOptOut" aria-describedby="InputOptOut" type="checkbox" aria-label="..."> 
					<label id="contactlbl" for="InputOptOut">Je veux recevoir des réductions ou de belles informations de la part de Hank (environ 5 emails par an!) <br /> I Want to be contacted by Hank about discounts and great news (around 5 emails per year!).</label>
				</div>
				<div class="form-group">
					<label for="InputPass">Mot de Passe / Password</label>
					<input type="text" class="form-control" id="InputPass" name="InputPass" aria-describedby="passHelp" placeholder="Enter Password" required>
					<small id="passHelp" class="form-text text-muted">Le mot de passe est écrit sur votre ticket de caisse. <br /> The password is written on your receipt.</small>
				</div>				
				<div class="row">
			    <div class="col-lg-10">
						<div class="form-group">
							<input id="InputCond" name="InputCond" aria-describedby="InputCond" type="checkbox" aria-label="..." required> 
							<label id="cgu" for="InputCond">J'accepte les <a href="/guest/s/default/wificgu.php?id=<?=$id?>&url=<?=$url?>">Conditions Générales d'utilisation</a> <br /> I agree with the <a href="/guest/s/default/wificgu.php?id=<?=$id?>&url=<?=$url?>">Terms Of Use</a></label>
					  </div> 
					</div> 
			  </div> 
				<input type="hidden" id="mac" name="id" value="<?=$id?>">
				<input type="hidden" id="url" name="url" value="<?=$url?>">
				<button id="login-btn" name="submitLogIn" type="submit" class="btn btn-lg btn-success">Log In</button>
			</form>

		</div>
	</div>

	<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
	<!-- Include all compiled plugins (below), or include individual files as needed -->
	<script src="/guest/s/default/js/bootstrap.min.js"></script>
</body>
</html>
