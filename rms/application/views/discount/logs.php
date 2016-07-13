<!DOCTYPE html>
<html lang="en">
<head>
	<title>HANK - Discount</title>
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
			<a href="/discount/" data-ajax="false" data-icon="back">Back</a>
			<h1>Discount logs | <?=$bu_name?> | <?=$username?></h1>
		</div>
		<div data-role="content">
			<div data-theme="a" data-form="ui-body-a" class="ui-body ui-body-a ui-corner-all">
			<? if(empty($discounts)) { ?>
			<br />Nothing done so far...<br />
			<? } ?>	
				<table data-role="table" id="table-custom-2" data-mode="reflow" data-filter="true" class="ui-body-d ui-shadow table-stripe ui-responsive" data-column-popup-theme="a">
					<thead>
						<th></th>
						<th>Event</th>
						<th>date</th>
						<th>User</th>
						<th>Nature</th>
						<th>Used?</th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ($discounts as $line):?>
						<? $bgcolor = ""; if($line->used == "yes") $bgcolor = "#aaaaaa"; ?>
						<tr style="background-color: <?=$bgcolor?>">
							<td>ID <?=$line->id_discount?></td>
							<td><?=$line->event_type?></td>
							<td><?=$line->date?></td>
							<td><?=$line->username?></td>
							<td><?=$line->nature?></td>
							<td><?=$line->used?></td>
						</tr>
					<?php endforeach;?>
					</tbody>
				</table>

				
			</div><!-- /theme -->
		</div><!-- /content -->
	</div><!-- /page -->
	<script src="/public/jquery-1.11.3.min.js" type="text/javascript"></script>
	<script src="/public/jqm/jquery.mobile-1.4.5.min.js" type="text/javascript"></script>
	<script src="/public/jqv/dist/jquery.validate.min.js" type="text/javascript"></script>
	<script src="/public/rmd.js" type="text/javascript"></script>
</body>
</html>
