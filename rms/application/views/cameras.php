<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="msapplication-tap-highlight" content="no" />
</head>
<body>

			
	<style type="text/css">
	#img1 {
	    width: auto;
	    height: auto;
		border: 1px solid #b1b1b1;
		float: right;
	}

	#img2 {
	    width: auto;
	    height: auto;
		border: 1px solid #b1b1b1;
		float: left;
	}
	</style>

<?if(isset($cam1)) { ?><div id="img"><img id="img1" src="<?=$cam1?>"></div><? } ?>
<?if(isset($cam2)) { ?><div id="img"><img id="img2" src="<?=$cam2?>"></div><? } ?>
<?if(isset($cam3)) { ?><div id="img"><img id="img1" src="<?=$cam3?>"></div><? } ?>
<?if(isset($cam4)) { ?><div id="img"><img id="img2" src="<?=$cam4?>"></div><? } ?>
</body>
</html>