	</div>
		<div data-role="content" data-theme="a">
		<?if($valided==1){?>
			<ul data-role="listview" data-inset="true"  data-filter="false">
				<? foreach ($order as $rec) { ?>
					<li data-role="list-divider"><?=$rec['date']?> | <?=$rec['supplier_name']?> |  <?=$rec['first_name']?> <?=$rec['last_name']?> | ID: <?=$rec['idorder']?> | Status commande: <?=$rec['status']?> <?if($rec['status'] == 'sent') { ?> | Status confirmation : <?=$rec['confirm']?> <? } ?>
					<?if($rec['status'] != 'draft') { ?><li data-inset="true" data-split-theme="a"> <a rel="external" data-ajax="false" href="/order/downloadOrder/<?=$rec['idorder']?>_<?=$rec['supplier_name']?>">View BDC</a></li>
					<?}?>
					<li data-inset="true" data-split-theme="a"> <a rel="external" data-ajax="false" href="/order/viewProducts/0/<?=$rec['idorder']?>">Open order</a></li>
				<? } ?>
				</ul>
		<?}else{?>
			<h2>You haven't give parameter to the research!</h2>
		<?}?>
		</div>
	</div>