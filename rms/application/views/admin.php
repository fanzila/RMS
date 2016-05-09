<?php $title = "Admin Hank"; include('jq_header.php'); ?> 
<body>
	<div data-role="page" data-theme="a">
		<div data-role="header">
			<h1>RMS Hank | <?=$username?> - <?=$user_groups->name?></h1>
			<a href="/auth/logout" data-transition="slide" class="ui-btn-right" data-icon="power">Logout</a> 
		</div>
		<div data-role="content" data-theme="a">
			
			<?php 
			
			$last = $ca['last'];
			$date = new DateTime($ca['last']);
			$date->add(new DateInterval('PT01H'));
			$l = $user_groups->level;
			
			if($l >= 2) { ?>
			Bank balance: <?=number_format($bank_balance, 2, ',', ' ');?>€
			<? } ?> | CA: <?=number_format($ca['amount']/1000, 0, ',', ' ')?>€ | Last ticket: <?=$date->format('Y-m-d H:i:s')?> | Num: <?=$ca['num']?>
			
			<ul data-role="listview" data-inset="true" data-filter="true">
			<!-- Admin --> 
			<?php if($l >= 2) { ?>
			<li><a href="/video/">Cams</a></li> 
			<li><a href="/video/index/1">Cams local</a></li>
			<?php if($l >= 3) { ?><!--<li><a rel="external" data-ajax="false" href="https://hmw.hankrestaurant.com/">Ajaxterm hank1</a></li> --><? } ?>
			<hr />
			<? } ?>
			<li><a rel="external" data-ajax="false" href="/news">News</a></li> 
			<li><a rel="external" data-ajax="false" href="http://hank.shiftplanning.com/app/">Shiftplanning</a></li>
			<?php if($l >= 2) { ?><li><a rel="external" data-ajax="false" href="/pos/">Cashier</a></li><? } ?>
			<?php if($l >= 2) { ?><li><a rel="external" data-ajax="false" href="/posmessage/">Message caisse</a></li><? } ?>			
			<?php if($l >= 2) { ?><li><a rel="external" data-ajax="false" href="https://hank.recruiterbox.com/app/#candidates/overview">Recruiter Box (RB)</a></li><? } ?>
			<?php if($l >= 2) { ?><li><a rel="external" data-ajax="false" href="https://www.cashpad.net">Reporting Cashpad</a></li><? } ?>
			<?php if($l >= 2) { ?><li><a rel="external" data-ajax="false" href="https://secure.tiime.fr">Tiime (compta)</a></li><? } ?>
			<li><a rel="external" data-ajax="false" href="/checklist/">Checklist</a></li>
			<li><a rel="external" data-ajax="false" href="/reminder/">Reminder</a></li>
			<li><a rel="external" data-ajax="false" href="/sensors/">Sensors</a></li>
			<li><a rel="external" data-ajax="false" href="/order/">Order</a></li>
			<?php if($l >= 1) { ?><li><a rel="external" data-ajax="false" href="/auth/">Staff management</a></li><? } ?>
			<?php if($l >= 2) { ?><li><a rel="external" data-ajax="false" href="/auth/extra">Extra finder</a></li><? } ?>
			<?php if($l >= 1) { ?><li><a rel="external" data-ajax="false" href="/reminder_admin/">Reminder tasks management</a></li><? } ?>
			<hr />
			<?php if($l >= 1) { ?><li><a rel="external" data-ajax="false" href="http://drive.google.com/">Google Drive</a></li><? } ?>
			<?php if($l >= 1) { ?><li><a rel="external" data-ajax="false" href="http://trello.com">Trello</a></li><? } ?>
			<?php if($l >= 1) { ?><li><a rel="external" data-ajax="false" href="http://mail.hankrestaurant.com">Email Hank (mail@hankrestaurant.com)</a></li><? } ?>
			<?php if($l >= 1) { ?><li><a rel="external" data-ajax="false" href="http://dropbox.com/home">Dropbox</a></li><? } ?>
			<hr />
			<?php if($l >= 2) { ?><li><a rel="external" data-ajax="false" href="/crud/cklChecklistTasks/">Checklists tasks management</a></li><? } ?>
			<?php if($l >= 3) { ?><li><a rel="external" data-ajax="false" href="/crud/cklChecklists/">Checklists management</a></li><? } ?>
			<?php if($l >= 3) { ?><li><a rel="external" data-ajax="false" href="/crud/rmdMeta/">Reminder task management</a></li><? } ?>
			<?php if($l >= 3) { ?><li><a rel="external" data-ajax="false" href="/crud/rmdNotif/">Reminder notification management</a></li><? } ?>
			<?php if($l >= 3) { ?><li><a rel="external" data-ajax="false" href="/crud/rmdTasks/">Reminder management</a></li><? } ?>
			<?php if($l >= 3) { ?><li><a rel="external" data-ajax="false" href="/crud/sensors/">Sensors management</a></li><? } ?>
			<?php if($l >= 3) { ?><li><a rel="external" data-ajax="false" href="/crud/sensorsAlarm/">Sensors alarm management</a></li><? } ?>
			<?php if($l >= 3) { ?><li><a rel="external" data-ajax="false" href="/crud/sensors/">Sensors management</a></li><? } ?>
			<?php if($l >= 3) { ?><li><a rel="external" data-ajax="false" href="/crud/productsUnit/">productsUnit</a></li><? } ?>
			<?php if($l >= 3) { ?><li><a rel="external" data-ajax="false" href="/crud/productsStock/">productsStock</a></li><? } ?>
			<?php if($l >= 3) { ?><li><a rel="external" data-ajax="false" href="/crud/productsCategory/">productsCategory</a></li><? } ?>
			<?php if($l >= 3) { ?><li><a rel="external" data-ajax="false" href="/crud/products/">products</a></li><? } ?>
			<?php if($l >= 3) { ?><li><a rel="external" data-ajax="false" href="/crud/suppliersCategory/">suppliersCategory</a></li><? } ?>
			<?php if($l >= 2) { ?><li><a rel="external" data-ajax="false" href="/crud/suppliers/">suppliers</a></li><? } ?>
			<hr />
			<li><a rel="external" data-ajax="false" href="http://intranet.hankrestaurant.com">Intranet (Wiki)</a></li>
			<li><a rel="external" data-ajax="false" href="http://62.210.240.67/hank/public/videos/">Archives videos</a></li>
			<li><a rel="external" data-ajax="false" href="/reporting/">Reporting CA pasteque (old)</a></li>
		</ul>
	</div><!-- /content -->
	<br /><br />
	<div id="view"></div>
</div><!-- /page -->
<?php include('jq_footer.php'); ?>




