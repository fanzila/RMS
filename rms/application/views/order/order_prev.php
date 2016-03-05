<? $title = "Order"; include('jq_header.php'); ?>
<body>
	<div data-role="page">
		<div data-role="header">
			<a href="/order/" data-ajax="false" data-icon="home">Back</a>
			<h1>Orders</h1>
		</div>
		<div data-role="content">
			<ul data-role="listview" data-inset="true" data-split-theme="a" data-divider-theme="a">
				<? foreach ($order as $rec) { ?>
					<li data-role="list-divider"><?=$rec['date']?> | <?=$rec['supplier_name']?> |  <?=$rec['first_name']?> <?=$rec['last_name']?> | ID: <?=$rec['idorder']?> | Status commande: <?=$rec['status']?> <?if($rec['status'] == 'sent') { ?> | Status confirmation : <?=$rec['confirm']?> <? } ?>

							<li data-inset="true" data-split-theme="a"> <a rel="external" data-ajax="false" href="/order/downloadOrder/<?=$rec['idorder']?>_<?=$rec['supplier_name']?>">View BDC</a></li>
							<li data-inset="true" data-split-theme="a"> <a rel="external" data-ajax="false" href="/order/viewProducts/0/<?=$rec['idorder']?>">Open order</a></li>
					<? } ?>
				</ul>
			</div>
		</div>
<? include('jq_footer.php'); ?>