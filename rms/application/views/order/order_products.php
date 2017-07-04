<?
$today = getdate();
?>
</div>
<script>var addtostock = [];</script>
<div data-role="content">
	<div data-theme="a" data-form="ui-body-a" class="ui-body ui-body-a ui-corner-all">	
		<? if($load > 0 AND $type == 'reception') { ?><h3>Order: <?=$load?></h3><? } ?>			
		<form onload="updateTotal()" id="order" name="order" class="order" method="post" action="/order/detailOrder/"  data-ajax="false" <? if($keylogin) { ?>onsubmit="return validateForm()"<? } ?>>
			<ul data-filter="true" data-role="listview" data-inset="true" data-split-theme="a" data-divider-theme="a">
				<?
			$hide = 0;
			foreach ($products as $line) {
				if($load) {
					if(empty($line['qtty']) AND (!isset($line['stock']) OR $line['stock'] == 0) AND $hide == 0) {
						$hide = 1;
						?>
						<li><input type='button' id='hideshow' value='View other products'></li>
						<?
				}
			}
			?>
			<div id='hide-<?=$line['id']?>' class="hide-<?=$hide?>">
				<li>
					<label for="pdt-<?=$line['id']?>"> <span style="font-size:large"> <?=strtoupper($line['name'])?></span> - <?=$line['supplier_name']?> - <?=$line['id']?>
						<br />Colisage: <?=$line['packaging']?>  | Unité de vente : <?=$line['unit_name']?> | Prix H.T. /unité : <?=$line['price']/1000?>€ <br />
						Current stock: <? if(isset($stock[$line['id']]['qtty'])) { echo round($stock[$line['id']]['qtty'], 2); } else { echo "0"; } ?> | stock mini: <?=$line['stock_mini']?> | stock max: <?=$line['stock_max']?> | stock warning: <?=$line['stock_warning']?> <br /> Ref. supplier: <?=$line['supplier_reference']?> | Comment: <?=$line['comment']?>
					</label>
			
					<table cellpadding="4">
						<tr>
									<td>
										<? 
										$qtty = null;
										if($load > 0) {
											$qtty = 0;
											if(isset($line['qtty'])) {
												if($line['qtty'] > 0) {
													$qtty = $line['qtty'];
												}
											}
										}
										if($type != 'reception' AND $type != 'viewreception') { ?>
											
										<input type="text" style="width:120px" name="qtty[<?=$line['id']?>]" id="qtty-<?=$line['id']?>" class="custom" data-mini="true" value="<?=$qtty?>" data-clear-btn="true" />
										<? } else { ?><input type="hidden" name="qtty[<?=$line['id']?>]" id="qtty-<?=$line['id']?>" value="<?=$qtty?>" />
										<small>Order qtty:</small> <?=$qtty?><small> => </small><? } ?>
									</td>

									<? if($load > 0 && $type == 'viewreception' && (isset($line['stock']) OR (isset($line['qtty']) && $line['qtty'] > 0 ))) { 
										(!isset($line['stock'])) ? $line['stock']=0 : $line['stock'];

										$added_stock = 0;
										if(isset($line['stock'])) {
												$added_stock = $line['stock'];
												if($qtty == $added_stock) {
													$icon = 'zmdi-badge-check';
													$fontcolor = 'green';
												} else {
													$icon = 'zmdi-alert-triangle';
													$fontcolor = 'red';
												}

										}											
										?>
										<td><span class="zmdi <?=$icon?>"></span><font color="<?=$fontcolor?>"> Received qtty:</font></td>
										<td><font color="<?=$fontcolor?>"><?=$added_stock?></font></td>
										<td><span>&nbsp;&nbsp;&nbsp;</span></td>
										<td>
											<small>Modify reception: </small>
											<input type="text" style="width:120px" name="editQtty[<?=$line['id']?>]" id="editQtty-<?=$line['id']?>" class="custom" data-mini="true" data-clear-btn="true">
										</td>
										<td><span>&nbsp;&nbsp;&nbsp;</span></td>
										<td>
											<small>Comment: </small><small style="color: red;"> <? if (isset($unsrl_order['pdt'][$line['id']]['comment'])) echo $unsrl_order['pdt'][$line['id']]['comment'];?></small>
										</td>
										<? } ?> 
										<? if($load > 0 && $type == 'reception') { ?>
											<td><small>Received: </small></td>
											<td>
												<input type="text" name="stock[<?=$line['id']?>]" id="stock-<?=$line['id']?>" class="custom" value="0" style="width:120px" data-clear-btn="true" />
											</td>
											<? if($load > 0 && $qtty > 0 && $type == 'reception') { ?>
												<td><input type="button" id="add<?=$line['id']?>" name="add[<?=$line['id']?>]" class="add" value="OK" data-mini="true" onclick="AddStock(<?=$line['id']?>);" />
												</td>
												<td>
													<small>Comment product:</small>
													<input type="text" id="comment-<?=$line['id']?>" name="comment[<?=$line['id']?>]" class="custom" data-mini="true" data-clear-btn="true" style="width:500px"/>
												</td>
												<script>
												addtostock[<?=$line['id']?>] = <?=$qtty?>;
												</script>
												<? } ?> 
												<? } ?>
											</tr>
										</table>
	
									</li>
									
									<? if($load > 0 && $type == 'reception') { ?>
										<!-- QTTY for reception check status -->
										<input type="hidden" id="qtty_check[<?=$line['id']?>]" name="qtty_check[<?=$line['id']?>]" value="<?=$qtty?>">
									<? } ?>
									
									<!-- START hidden price: 1 for php, 1 for js -->
									<input type="hidden" id="price-<?=$line['id']?>" name="price-<?=$line['id']?>" value="<?=$line['price']?>">
									<input type="hidden" id="price[<?=$line['id']?>]" name="price[<?=$line['id']?>]" value="<?=$line['price']?>">
									<!-- END hidden price -->
									<input type="hidden" name="pdt" value="<?=$line['id']?>">
									<input type="hidden" name="pdt_name[<?=$line['id']?>]" value="<?=strtoupper($line['name'])?>">
								</div>
								<? $qtty = 0; $added_stock = 0; } ?>
							</ul>
							
							<? if($type == 'reception') { ?>
							<ul data-role="listview" data-inset="true" data-split-theme="a" data-divider-theme="a"><li><input style="background-color: #49b049;" type="button" id="checkall" name="checkall" value="[CHECK ALL]" onclick="AddStockAll();"></li></ul>
							<? } ?>
							
							<? if($type == 'reception') { ?>
								<p><small>Comment on order:</small> <?=$comment_order?></p>
									
								<label for="comment_reception" data-mini="true" style="background-color:#ffffff">Comments for reception (Only to notify or if problem)</label>
								<input type="text" name="comment_reception" id="comment_reception" class="custom"  data-clear-btn="true" />
								<input type="hidden" name="idorder" value="<?=$load?>">
								<? } ?>
								<? if($type == 'viewreception') { ?>
									<p><small>Comment on order:</small> <?=$comment_order?></p>
									<p><small>Comment on reception:</small> <?=$comment_recept?></p> 
								<? } ?>	
								<ul data-role="listview" data-inset="true" data-split-theme="a" data-divider-theme="a">
									<? if($type == 'reception' AND $keylogin) { ?>
										<li>													
											<select style="background-color:#a1ff7c" name="user" id="user" data-inline="true" data-theme="a" required>
												<option value="">User</option>
												<? foreach ($users as $user) { ?>
													<option value="<?=$user->id?>"><?=$user->first_name?> <?=$user->last_name?></option>
												<? } ?>
										</select>
										<li>
											<input type="submit" name="save" value="SAVE">
										</li>
									<? } ?>
									
									<? if(!$keylogin && $type != 'viewreception') { ?>
										<li><input type="submit" name="save" value="SAVE"></li>
									<? } else if (!$keylogin && $type == 'viewreception') { ?>
										<input type="hidden" name="editReception" value="true">
										<input type="hidden" name="srl_order_post" value='<?=serialize($unsrl_order)?>'>
										<input type="hidden" name="id_order" value='<?=$load?>'>
										<input type="hidden" name="current_url" value=<?=current_url()?>>
										<li><input type="submit" name="save" value="SAVE"></li>
									<? } ?>
									</ul>

									<ul data-role="listview" data-inset="true" data-split-theme="a" data-divider-theme="a">
										<li data-role="list-divider" style="list-style-type: none;">
											<? if($type != 'reception' AND $type != 'viewreception') { ?>TOTAL ORDER: <span id="total">0</span>€ H.T.<hr /><? } ?>
											Carriage paid: <?=$supinfo['carriage_paid']?> | Delivery days: <?=$supinfo['delivery_days']?> | Location: <?=$supinfo['location']?> <br />
											Internal comments: <?=$supinfo['comment_internal']?> <br />
											Order comment: <?=$supinfo['comment_order']?> <br /> 
											Delivery comment: <?=$supinfo['comment_delivery']?> <br /> 
											Delivery schedule: <?=$supinfo['comment_delivery_info']?> <br />
											Order method: <?=$supinfo['order_method']?> | Payment type: <?=$supinfo['payment_type']?> | Payment delay: <?=$supinfo['payment_delay']?> <br />
											Contact sale: <?=$supinfo['contact_sale_name']?> | <?=$supinfo['contact_sale_tel']?> |  <?=$supinfo['contact_sale_email']?><br />
											Contact order: <?=$supinfo['contact_order_name']?> | <?=$supinfo['contact_order_tel']?> |  <?=$supinfo['contact_order_email']?>
										</li>
									</ul>				
									<input type="hidden" name="supplier" value="<?=$supinfo['id']?>">
									<input type="hidden" name="action" value="save_order">
									<input type="hidden" name="type" value="<?=$type?>">
								</form>
								<? if (!$keylogin && $type == 'viewreception') { ?>
									<form id="order" name="order" class="order" method="post" action="/order/cancelReception/"  data-ajax="false" onsubmit="return confirm('Do you really want to cancel the reception of this order ?')">
										<input type="hidden" name="cancelReception" value="true">
										<input type="hidden" name="srl_order_post" value='<?=serialize($unsrl_order)?>'>
										<input type="hidden" name="id_order" value='<?=$load?>'>
										<input type="hidden" name="current_url" value=<?=current_url()?>>
										<input style="background-color: #e33030;" type="submit" name="save" value="CANCEL RECEPTION">
									</form>
								<? } ?>
							</div><!-- /theme -->
						</div><!-- /content -->
					</div><!-- /page -->
					<script>
					
					jQuery(document).ready(function(){
						jQuery('.hide-1').toggle('hide');
						jQuery('#hideshow').on('click', function(event) {        
							jQuery('.hide-1').toggle('show');
						});
					});

					function AddStockAll() {
						jQuery(document).ready(function(){
							len = addtostock.length;
							for (i = 0; i < len; i++) {
								if(addtostock[i]) {
									qtty = addtostock[i];
									sum = parseInt(qtty);
									$('#stock-' + i).val(sum);
								}
							}
							return false; 
						});
					}
					
					function AddStock(idl) {
						qtty = $('#qtty-' + idl).val();
						sum = parseInt(qtty);
						$('#stock-' + idl).val(sum);
					}

					function updateTotal() {
						var total = 0;
						$(".order input[type='text']").each(function () {
							if($(this).val() === '') {
								// empty
							} else {
								var elid	= $(this).attr("name");
								var elid1	= elid.split("[");
								var elid2	= elid1[1].split("]");
								var idpdt	= elid2[0];
								var item	= elid1[0];
								var price	= $('#price' + '-' + idpdt).val();
								var qtty	= $('#qtty' + '-' + idpdt).val();

								if(item == 'qtty' && qtty > 0) { 						
									//get sub total
									if(price) var sub = qtty*price;
									if(price) total = total + sub;
								}
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

					function validateForm() {
					    var x = document.forms["order"]["user"].value;
					    if (x == "") {
					        alert("User must be filled out");
					        return false;
					    }
					}

					</script>