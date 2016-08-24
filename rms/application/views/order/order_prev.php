		</div>
		<div data-role="content" data-theme="a">
			<ul data-role="listview" data-inset="true"  data-filter="true">
				<? foreach ($order as $rec) { ?>
					<li data-role="list-divider"><?=$rec['date']?> | <?=$rec['supplier_name']?> |  <?=$rec['first_name']?> <?=$rec['last_name']?> | ID: <?=$rec['idorder']?> | Status commande: <?=$rec['status']?> <?if($rec['status'] == 'sent') { ?> | Status confirmation : <?=$rec['confirm']?> <? } ?>

							<?if($rec['status'] != 'draft') { ?>
								<li data-inset="true" data-split-theme="a" data-filtertext="<?=$rec['date']?> <?=$rec['supplier_name']?> <?=$rec['first_name']?> <?=$rec['last_name']?> <?=$rec['idorder']?> <?=$rec['status']?>">
									<a rel="external" data-ajax="false" href="/order/downloadOrder/<?=$rec['idorder']?>_<?=$rec['supplier_name']?>">View BDC</a>
								</li>
							<?}?>
							<li data-inset="true" data-split-theme="a" data-filtertext="<?=$rec['date']?> <?=$rec['supplier_name']?>	<?=$rec['first_name']?> <?=$rec['last_name']?> <?=$rec['idorder']?> <?=$rec['status']?>"> <a rel="external" data-ajax="false" href="/order/viewProducts/0/<?=$rec['idorder']?>">
								Open order</a>
							</li>
					<? } ?>
				</ul>
			</div>
		</div>