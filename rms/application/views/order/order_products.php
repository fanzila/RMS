<?
$today = getdate();
?>
		</div>
		<div data-role="content">
			<div data-theme="a" data-form="ui-body-a" class="ui-body ui-body-a ui-corner-all">				
				<form onload="updateTotal()" id="order" name="order" class="order" method="post" action="/order/prepareOrder/" onsubmit="return validateForm()" data-ajax="false">
					<ul data-filter="true" data-role="listview" data-inset="true" data-split-theme="a" data-divider-theme="a">
						<?
					foreach ($products as $line) {
						if($line['freq_inventory'] == $order_name OR ($order_name == 'previous' AND $order_prev[0]['supid'] == $line['supplier_id']) OR $freq_id == 1000) {
							?>
							<li>
								<label for="pdt-<?=$line['id']?>"> <span style="font-size:large"> <?=strtoupper($line['name'])?></span> - <?=$line['supplier_name']?> - <?=$line['id']?>
									<br /><b>Current stock: <? if(isset($stock[$line['id']]['qtty'])) { echo round($stock[$line['id']]['qtty'], 2); } else { echo "0"; } ?></b> | stock mini: <?=$line['stock_mini']?> | stock max: <?=$line['stock_max']?> | stock warning: <?=$line['stock_warning']?>
									<br />Colisage: <?=$line['packaging']?>  <?=$line['unit_name']?> | Unité facturation: <?=$line['price']/1000?>€ | Ref. supplier: <?=$line['supplier_reference']?> | Comment: <?=$line['comment']?>
								
							</label>
							<input type="hidden" name="price-<?=$line['id']?>" id="price-<?=$line['id']?>" value="<?=$line['price']?>">
							<input type="hidden" name="packaging-<?=$line['id']?>" id="packaging-<?=$line['id']?>" value="<?=$line['packaging']?>">
							<input type="hidden" name="unitname-<?=$line['id']?>" id="unitname-<?=$line['id']?>" value="<?=$line['unit_name']?>"> 
							<table>
								<tr>
									<? $atexist = false; foreach ($attributs as $seek) { if($seek['id_product'] == $line['id']) $atexist = true; } ?>
									<? if($atexist) { ?>
										<td>
											<select id="attribut-<?=$line['id']?>" name="attribut-<?=$line['id']?>"  data-mini="true">
												<option value="0"></option>
												<? foreach ($attributs as $att) { 
													if($att['id_product'] == $line['id']) { 
													?>
													<option value="<?=$att['id']?>" 
														<? 
														if($load > 0) { 
															foreach ($order_prev as $key => $var) {
																if($var['attribut'] > 0 AND $var['attribut'] == $att['id']) {
																	echo "selected";
																}
															}
														}
														?>
														><?=$att['name']?></option>
													<? } } ?>
												</select>
											</td>
											<? } else { ?>
												<td><input type="hidden" name="attribut-<?=$line['id']?>" id="attribut-<?=$line['id']?>" value="0"></td> 
											<? } ?>
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
												data-clear-btn="true" />
											</td>
												<td><small>Stock adjust: </small></td>
												<td>
													<input type="text" name="stock-<?=$line['id']?>" id="stock-<?=$line['id']?>" class="custom" value="0" data-clear-btn="true" />
												</td>
												<? if($load > 0 && $qtty > 0) { ?>
													<td><label for="add<?=$line['id']?>" data-mini="true" style="background-color:#ffffff">Add to stock</label><input type="checkbox" id="add<?=$line['id']?>" name="add<?=$line['id']?>" class="add" data-mini="true" onclick="disableStock(<?=$line['id']?>);" />
													</td>
													<? } ?> 
													<? } $qtty = 0; ?>
												</tr>
										</table>
									</li>
									<? } ?>
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
					checked = $('#add' + idl).is(':checked');
					sum = 0;
					if(checked) { sum = parseInt(pdt); }
					$('#stock-' + idl).val(sum);
				}
				
				function updateTotal() {
					var total = 0;
					$(".order input[type='text']").each(function () {

						if($(this).val() === '') {
							// empty
						} else {
							//get name/id of filled elements
							var elid = $(this).attr("name");							
							//get price
							var price = $("#price" + '-' + elid).val();
							//get qtty
							var nb = $("#pdt" + '-' + elid).val();
							//get sub total
							if(price) var sub = nb*price;
							if(price) total = total + sub;
						}
						$( "#total" ).text( (Math.round(total*100)/100)/1000 );
					});
				}
				
				$("form :input").change(function() {
					updateTotal();
				});
				
				$( document ).ready(function() {
					updateTotal();
				});
				
				</script>