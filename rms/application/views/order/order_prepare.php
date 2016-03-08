<? $title = "Order Prepare"; include('jq_header.php'); ?>
<body>
	<div data-role="page">

		<div data-role="header">
			<a data-ajax="false" href="/order/" data-icon="home">Back</a>
			<h1>Order Prepare</h1>
		</div>

		<div data-role="content">
			<? if($stock_update) { ?><ul data-role="listview" data-inset="true" data-split-theme="a" data-divider-theme="a">
				<li style="background-color: #e8ffb9;">Stock updated</li>
				<? foreach ($maj as $keyst) { if($keyst['stock'] != 0) { ?>
					<li data-inset="true" data-split-theme="a"><?=strtoupper($keyst['name'])?> <b>+ <?=$keyst['stock']?></b></li>
				<? } } ?>
			</ul><a data-ajax="false" href="/order/" data-role="button" data-icon="home">Back</a><? } ?>
			
			<form id="order" name="order" method="post" action="/order/confirmOrder/" data-ajax="false">
				<? $i=0; $totalprice = 0;
				foreach ($order as $supplier_id => $pdt_list) { 			
					?>
					<ul data-role="listview" data-inset="true" data-split-theme="a" data-divider-theme="a">
						<li data-role="list-divider"><?=$suppliers[$supplier_id]['name']?></li>

						<?
						foreach ($pdt_list as $key => $pdt) { 
							if(!empty($pdt['qtty'])) {  
								$idorder = $pdt['idorder'];
								?>
								<li data-inset="true" data-split-theme="a">
									<?=strtoupper($pdt['name'])?> | QTTE: <?=$pdt['qtty']?>  | Prix: <?=$pdt['subtotalprice']/1000?>€ (pour <?=$pdt['qtty']?> <?=$pdt['unitname']?>)</span>
								</li>
								<input type="hidden" name="<? echo $i."_PDT_NAME_".$pdt['id']; ?>" value="<?=strtoupper($pdt['name'])?>">
								<input type="hidden" name="<? echo $i."_PDT_QTTY_".$pdt['id']; ?>" value="<?=$pdt['qtty']?>">
								<input type="hidden" name="<? echo $i."_PDT_UNIT_".$pdt['id']; ?>" value="<?=$pdt['unitname']?>">
								<input type="hidden" name="<? echo $i."_PDT_PACK_".$pdt['id']; ?>" value="<?=$pdt['packaging']?>">
								<input type="hidden" name="<? echo $i."_PDT_CODEF_".$pdt['id']; ?>" value="<?=$pdt['codef']?>">
								<input type="hidden" name="<? echo $i."_PDT_ATTR_".$pdt['id']; ?>" value="<?=$pdt['attribut']?>">
								<input type="hidden" name="<? echo $i."_PDT_PRIC_".$pdt['id']; ?>" value="<?=$pdt['price']?>">
								<? $totalprice += $pdt['subtotalprice']; } ?>				 
								<? } ?>

								<li style="background-color:#f1f0f0;"></li>
								<li>TOTAL PRIX: <?=$totalprice/1000?>€ H.T. | Franco: <?=$suppliers[$supplier_id]['carriage_paid']?>€ H.T. 
									
									
								<?if($totalprice/1000 < $suppliers[$supplier_id]['carriage_paid']) { ?><font color="red"><b>ATTENTION FRANCO INFERIEUR!</b></font><? } ?> | Délais livraison: <?=$suppliers[$supplier_id]['delivery_days']?> | Paiement: <?=$suppliers[$supplier_id]['payment_type']?> <br />Contact : <?=$suppliers[$supplier_id]['contact_order_name']?> | Tel: <?=$suppliers[$supplier_id]['contact_order_tel']?> | Email: <?=$suppliers[$supplier_id]['contact_order_email']?> | Méthode commande: <?=$suppliers[$supplier_id]['order_method']?>
								</li>
								<li>
									<label for="addtext-<?=$supplier_id?>">Comment:</label>
									<textarea name="<?=$i?>_COMT" id="addtext-<?=$supplier_id?>"></textarea>
								</li>
								<li> 
									<label for="addemail-<?=$supplier_id?>">CC email:</label>
									<input type="text" name="<?=$i?>_CCEMAIL" id="addemail-<?=$supplier_id?>" class="custom"/>
								</li>
							</ul>
							<input type="hidden" name="<?=$i?>_SUP"  value="<?=$suppliers[$supplier_id]['name']?>">
							<input type="hidden" name="<?=$i?>_EMAIL"  value="<?=$suppliers[$supplier_id]['contact_order_email']?>">
							<input type="hidden" name="<?=$i?>_DLV_INFO"  value="<?=$suppliers[$supplier_id]['comment_delivery_info']?>">
							<input type="hidden" name="<?=$i?>_DLV_COMT"  value="<?=$suppliers[$supplier_id]['comment_delivery']?>">
							<input type="hidden" name="<?=$i?>_SUPID"  value="<?=$supplier_id?>">
							<input type="hidden" name="<?=$i?>_FRANCO"  value="<?=$suppliers[$supplier_id]['carriage_paid']?>">
							<input type="hidden" name="<?=$i?>_TOTALPRICE"  value="<?=$totalprice?>">
							<input type="hidden" name="<?=$i?>_IDORDER"  value="<?=$idorder?>">
							<? $i++;  $totalprice = 0; } ?>
							<ul data-role="listview" data-inset="true" data-split-theme="a" data-divider-theme="a">
								<li><input type="submit" name="save" onClick="" value="VIEW"></li>
							</ul>
						</form>
					</div>
				</div>
				<? include('jq_footer.php'); ?>