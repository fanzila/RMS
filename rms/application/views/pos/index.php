<div data-role="page" data-theme="a">
	<? if($keylogin) { ?>	
		<div data-role="header">
	&nbsp;&nbsp;&nbsp;
		</div>
		<? } ?>
		<div data-role="header">
		<? if(!$keylogin) { ?><a href="/admin/" data-role="button" data-inline="true" data-ajax="false" data-icon="home">Home</a><? } ?>
		<h1>Cashier | <?=$bu_name?> | <?=$username?></h1>
	</div>
	<div data-role="content" data-theme="a">
		<ul data-role="listview" data-inset="true">
			<li><a rel="external" data-ajax="false" href="/pos/movement/open">Open</a></li>
			<li><a rel="external" data-ajax="false" href="/pos/movement/close">Close</a></li>
			<?php if($user_groups->level >= 2) { ?>
			<li><a rel="external" data-ajax="false" href="/pos/safe/">Safe</a></li>
			<li><a rel="external" data-ajax="false" href="/pos/report/">Report</a></li>
			<? } ?>
		</ul>
	</div>
</div>