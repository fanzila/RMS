		</div>
		<div data-role="content" data-theme="a">
			<? if($type == 'reception') { ?>
				<h2>Thanks, order <?=$order['id']?> received!</h2>
			<? } ?>
			<? if($stock_update) { ?><ul data-role="listview" data-inset="true" data-split-theme="a" data-divider-theme="a">
				<li style="background-color: #e8ffb9;">Stock updated</li>
				<? foreach ($update_stock as $keyst) { if($keyst['stock'] != 0) { ?>
					<li data-inset="true" data-split-theme="a"><?=strtoupper($keyst['name'])?> <b>+ <?=$keyst['stock']?></b></li>
				<? } } ?>
			</ul><a data-ajax="false" href="/order/" data-role="button" data-icon="home">Back</a><? } ?>
			<? if($type != 'reception') { ?>
			<form id="order" name="order" method="post" action="/order/confirmOrder/" data-ajax="false">
					<ul data-role="listview" data-inset="true">
						<li data-role="list-divider"><?=$supinfo['name']?></li>
						<?
						$totalprice = 0;
						foreach ($order['pdt'] as $key => $value) {  
							if(!empty($value['qtty'])) {  
								$idorder = $order['id'];
								
								?>
								<li data-inset="true" data-split-theme="a">
									<?=strtoupper($value['name'])?> | QTTE: <?=$value['qtty']?>  | Prix: <?=$value['subtotal']/1000?>€ (pour <?=$value['qtty']?> <?=$pdtinfo[$key]['unit_name']?>)</span>
								</li>
								<? $totalprice += $value['subtotal']; } ?>				 
								<? } ?>
								<li style="background-color:#f1f0f0;"></li>
								<li>TOTAL PRIX: <?=$totalprice/1000?>€ H.T. | Franco: <?=$supinfo['carriage_paid']?>€ H.T. 			
								<?if($totalprice/1000 < $supinfo['carriage_paid']) { ?><font color="red"><b>ATTENTION FRANCO INFERIEUR!</b></font><? } ?> | Délais livraison: <?=$supinfo['delivery_days']?> | Paiement: <?=$supinfo['payment_type']?> <br />Contact : <?=$supinfo['contact_order_name']?> | Tel: <?=$supinfo['contact_order_tel']?> | Email: <?=$supinfo['contact_order_email']?> | Méthode commande: <?=$supinfo['order_method']?>
								</li>
								<li>
									<label for="addtext">Comment:</label>
									<textarea name="comment" id="addtext"></textarea>
								</li>
								<li> 
									<label for="addemail">CC email:</label>
									<input type="text" name="ccemail" id="addemail" class="custom"/>
								</li>
							</ul>
							<ul data-role="listview" data-inset="true" data-split-theme="a" data-divider-theme="a">
								<li><input type="submit" name="save" onClick="" value="VIEW"></li>
							</ul>
							<input type="hidden" name="idorder" value="<?=$order['id']?>">
							<input type="hidden" name="supplier" value="<?=$supinfo['id']?>">
						</form>
						<? } ?>
					</div>
				</div>