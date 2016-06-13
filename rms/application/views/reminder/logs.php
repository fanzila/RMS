<!DOCTYPE html>
<html lang="en">
<head>
	<title>HANK - Reminder</title>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="msapplication-tap-highlight" content="no" />
	<link rel="stylesheet" href="/public/jqm/jquery.mobile-1.4.5.min.css" />
	<link rel="stylesheet" href="/public/jqm/themes/hmw.min.css" />
	<link rel="stylesheet" href="/public/jqm/themes/jquery.mobile.icons.min.css" />
	<link rel="stylesheet" href="/public/jqm/jquery.mobile.structure-1.4.5.min.css" />
</head>
<body>
	<div data-role="page">
		<div data-role="header">
			<a href="/reminder/" data-ajax="false" data-icon="home">Back</a>
			<h1>Reminder logs | <?=$bu_name?> | <?=$username?></h1>
		</div>
		<div data-role="content">
			<div data-theme="a" data-form="ui-body-a" class="ui-body ui-body-a ui-corner-all">
			<? if(empty($tasks)) { ?>
			<br />Nothing done so far...<br />
			<? } ?>			
				<ul>
				<?
					foreach ($tasks as $line) {	
				?>
					<li>
						<label> <?=$line->date?> - <?=$line->username?> - <?=$line->task?></label>
					</li>
					<? } ?>
				</ul>
			</div><!-- /theme -->
		</div><!-- /content -->
	</div><!-- /page -->
	<script src="/public/jquery-1.11.3.min.js" type="text/javascript"></script>
	<script src="/public/jqm/jquery.mobile-1.4.5.min.js" type="text/javascript"></script>
	<script src="/public/jqv/dist/jquery.validate.min.js" type="text/javascript"></script>
	<script src="/public/rmd.js" type="text/javascript"></script>
</body>
</html>