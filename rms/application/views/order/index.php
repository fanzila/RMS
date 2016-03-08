<?php $title = "Order"; include('jq_header.php'); ?> 
<body>
	<div data-role="page">
		<div data-role="header">
			<? if(!$keylogin) { ?><a href="/admin/" data-ajax="false" data-icon="home">Home</a><? } ?>
			<h1>Order</h1>
		</div>
		<div data-role="content">
			<?php if($user_groups->level >= 1) { ?>

				<ul data-role="listview" data-inset="true" data-filter="true">
					<li data-role="list-divider">FREQUENCE</li>
					<?
				foreach ($freq as $var) {
					if($var['name'] != 'previous') {
						?>
						<li><a data-ajax="false" href="/order/viewProducts/<?=$var['id']?>"><?=strtoupper($var['name'])?></a></li>
						<? }  } ?>
					</ul>
					<? } ?>
					<a href="/order/previousOrders" rel="external" data-ajax="false" data-role="button" data-inline="true" data-icon="search" data-mini="true" data-theme="a">Log</a>
					<?php if($user_groups->level >= 2) { ?>
						<a href="/product_admin/mapping" rel="external" data-ajax="false" data-role="button" data-inline="true" data-icon="edit" data-mini="true" data-theme="a">Products mapping</a>
						<a href="/product_admin/" rel="external" data-ajax="false" data-role="button" data-inline="true" data-icon="edit" data-mini="true" data-theme="a">Products admin</a>
						<a href="/crud/productsAttribut/" rel="external" data-ajax="false" data-role="button" data-inline="true" data-icon="edit" data-mini="true" data-theme="a">Products attribut</a>								
						<hr />
						<ul data-role="listview" data-inset="true" data-filter="true">
							<li data-role="list-divider">SUPPLIERS</li>
							<?
						foreach ($suppliers as $varsup) {
								?>
								<li><a data-ajax="false" href="/order/viewProducts/1000/0/<?=$varsup['id']?>"><?=strtoupper($varsup['name'])?> 
								<? if(isset($varsup['last_order'])) { ?><small>  <i>Last order: <?=$varsup['last_order']?> days ago by <?=$varsup['last_order_user']->username?></i></small><? } ?></a></li>
								<? } ?>
							</ul>
					<? } ?>
					<br /><br />							
				</div><!-- /content -->

				<div id="view"></div>
			</div><!-- /page -->
			<?php include('jq_footer.php'); ?>