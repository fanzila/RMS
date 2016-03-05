<? $title = "Order"; include('jq_header.php'); 
$today = getdate();
?>
<body>
	<div data-role="page">
		<div data-role="header">
			<a href="/order/" data-ajax="false" data-icon="home">Back</a>
			<h1>Order <? if($load <= 0) { ?> <?=strtoupper($order_name)?> <? } ?></h1>
		</div>
		<div data-role="content">
			<div data-theme="a" data-form="ui-body-a" class="ui-body ui-body-a ui-corner-all">				
				<form id="order" name="order" class="order" method="post" action="/order/prepareOrder/" onsubmit="return validateForm()" data-ajax="false">
					<ul data-filter="true" data-role="listview" data-inset="true" data-split-theme="a" data-divider-theme="a">
						<?
					foreach ($products as $line) {
						if($line['freq_inventory'] == $order_name OR ($order_name == 'previous' AND $order_prev[0]['supid'] == $line['supplier_id']) OR $freq_id == 1000) {
							?>
							<li>
								<label for="pdt-<?=$line['id']?>"> <?=strtoupper($line['name'])?> - <span style="font-size:smaller"> <?=$line['supplier_name']?> - <?=$line['id']?>
									<? if(!empty($line['stock_management'])) { ?><br /><b>Current stock: <? if(isset($stock[$line['id']]['qtty'])) { echo $stock[$line['id']]['qtty']; } else { echo "0"; } ?></b>| stock mini: <?=$line['stock_mini']?> | stock max: <?=$line['stock_max']?> | stock warning: <?=$line['stock_warning']?> <? } ?> 

									<br />Colisage: <?=$line['packaging']?> <?=$line['unit_name']?> | Unit price: <?=$line['price']/1000?>€ | Ref. supplier: <?=$line['supplier_reference']?> | Comment: <?=$line['comment']?>
								</span>
							</label>
							<input type="hidden" name="price-<?=$line['id']?>" id="price-<?=$line['id']?>" value="<?=$line['price']?>">
							<input type="hidden" name="packaging-<?=$line['id']?>" id="packaging-<?=$line['id']?>" value="<?=$line['packaging']?>">
							<input type="hidden" name="unitname-<?=$line['id']?>" id="unitname-<?=$line['id']?>" value="<?=$line['unit_name']?>"> 
							<table>
								<tr>
									<tbody>
										<td>
											<input type="text" name="<?=$line['id']?>" id="pdt-<?=$line['id']?>" class="custom" data-mini="true" 
											<? 
											if($load > 0) {
												$qtty = 0;
												foreach ($order_prev as $key => $var) {
													if($var['qtty'] > 0 AND strtoupper($var['id']) == strtoupper($line['id'])) {
														echo "value='".$var['qtty']."'";
														$qtty = $var['qtty'];
													}
												}
											}
											?>
											/>
										</td>
										<? if($line['stock_management'] == 1) { ?>
											<td><small>Stock adjust: </small></td>
										<td>
											<input type="text" name="stock-<?=$line['id']?>" id="stock-<?=$line['id']?>" class="custom" value="0">
										</td>
										<? if($load > 0 && $qtty > 0) { ?>
											<td><label for="add<?=$line['id']?>" data-mini="true">Add to stock</label><input type="checkbox" id="add<?=$line['id']?>" name="add<?=$line['id']?>" class="add" data-mini="true" onclick="disableStock(<?=$line['id']?>);" />
											</td>
											<? } ?> 
											<? } $qtty = 0; ?>
										</tr>
									</tbody>
								</table>
								</li>
								<? } } ?>
							</ul>	
							<ul data-role="listview" data-inset="true" data-split-theme="a" data-divider-theme="a">
								<li data-role="list-divider" style="list-style-type: none;">
								TOTAL ORDER : <span id="total">0</span>€
							</li> 
						</ul>
						<ul data-role="listview" data-inset="true" data-split-theme="a" data-divider-theme="a">
							<li><input type="submit" name="save" value="SAVE"></li>
						</ul>							
						<input type="hidden" name="action" value="save_order">
					</form>
				</div><!-- /theme -->
			</div><!-- /content -->
		</div><!-- /page -->
		<script>

		function disableStock(idl) {
			pdt = $('#pdt-' + idl).val();
			//if(typeof stock !== 'undefined') { stock = 0; } 
			checked = $('#add' + idl).is(':checked');
			sum = 0;
			if(checked) { sum = parseInt(pdt); }

			$('#stock-' + idl).val(sum);
			//$('#stock-' + idl).prop('disabled', ! $('#stock-' + idl).prop('disabled') );
		}

		$("form :input").change(function() {
			var total = 0;
			$(".order input[type='text']").each(function () {

				if($(this).val() === '') {
					// empty
				} else {
					//get name/id of filled elements
					var elid = $(this).attr("name");
					//get price
					var price = $("#price" + '-' + elid).val();
					//get value 
					var nb = $(this).val();
					//get sub total
					var sub = nb*price;
					total = total + sub;
				}
				$( "#total" ).text( (Math.round(total*100)/100)/1000 );
			});



		});
		</script>
		<? include('jq_footer.php'); ?>