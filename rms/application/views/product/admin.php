		<? if($command != 'create') { ?>
			<a href="/product_admin/index/create" class="ui-btn ui-btn-right" rel="external" data-ajax="false" data-icon="plus"><i class="zmdi zmdi-plus zmd-2x"></i></a>
		<?}?>
		</div>
		<div data-role="content" data-theme="a">
			<? if($command != 'create') { ?>
				<fieldset class="ui-grid-a">
					<form id="filter" name="filter" method="post" data-ajax="false" action="/product_admin/index/filter">
						<div class="ui-block-a">
							<?$test=0;?>
							<select name="supplier_id"  data-mini="true" onchange="this.form.submit()">
								<option value="">SELECT</option>
								<? foreach ($suppliers as $sup) { ?>
									<option value="<?=$sup['id']?>" <? if($sup['id'] == $supplier_id) { echo "selected";}?> ><?=$sup['name']?></option>
								<? } ?>
							</select>
						</div>
						<?if($test==0){?>
							<div class="ui-block-b"> 
								<input type="submit" name="submit" value="filter" data-mini="true" data-ajax="false" data-clear-btn="true" /> 
							</div>
						<?}?>
					</form>
				</fieldset>
				<ul data-role="listview" data-inset="true">
				<? 
				if(!empty($products)) {
				foreach ($products as $line) { 
					?>
					<li data-role="list-divider"><?=stripslashes($line['name'])?> <small>- <?=$line['id']?></small></li>
					<? 
					$bkgcolor = '';
					if($line['active'] == 0 ) $bkgcolor = '#e5e5e5'; 
					?>			
					<li style="background-color: <?=$bkgcolor?>">
					<form id="pdt<?=$line['id']?>" class="pdt<?=$line['id']?>" name="pdt<?=$line['id']?>" method="post" action="/product_admin/save">
						<table width="100%">
							<div class="row">
								<div class="col-md">
									<div class="box">
										Name
										<input type="text" name="name" id="name-<?=$line['id']?>" value="<?=stripslashes($line['name'])?>"  data-mini="true" data-clear-btn="true" />
									</div>
								</div>
								<div class="col-md">
									<div class="box">
										<small>Supplier</small>
										<select id="id_supplier<?=$line['id']?>" name="id_supplier"  data-mini="true">
											<? foreach ($suppliers as $sup) { ?>
												<option value="<?=$sup['id']?>" <? if($sup['id'] == $line['supplier_id']) { echo "selected"; } ?> ><?=$sup['name']?></option>
											<? } ?>
										</select>
									</div>
								</div>
								<div class="col-md">
									<div class="box">
										<small>Prix H.T. /unité €</small>
										<input type="text" name="price" id="price-<?=$line['id']?>" value="<?=$line['price']/1000?>"  data-mini="true" data-clear-btn="true" />
									</div>
								</div>
								<div class="col-md">
									<div class="box">
										<small>Colisage</small>
										<input type="text" name="packaging" value="<?=$line['packaging']?>"  id="packaging-<?=$line['id']?>" data-mini="true" data-clear-btn="true" />
									</div>
								</div>
								<div class="col-md">
									<div class="box">
										<small>Unité de vente</small>
										<select id="id_unit<?=$line['id']?>" name="id_unit"  data-mini="true">
											<? foreach ($products_unit as $pack_unit) { ?>
												<option value="<?=$pack_unit['id']?>" <? if($pack_unit['id'] == $line['id_unit']) { echo "selected"; } ?> ><?=$pack_unit['name']?></option>
											<? } ?>
										</select>
									</div>
								</div>
								<div class="col-md">
									<div class="box">
										<small>Category</small>
										<select id="id_category<?=$line['id']?>" name="id_category"  data-mini="true">
											<? foreach ($products_category as $pdt_cat) { ?>
												<option value="<?=$pdt_cat['id']?>" <? if($pdt_cat['id'] == $line['id_category']) { echo "selected"; } ?> ><?=$pdt_cat['name']?></option>
											<? } ?>
										</select>
									</div>
								</div>
								<div class="col-md">
									<div class="box">
										<small>Active</small>
											<select id="active<?=$line['id']?>" name="active"  data-mini="true">
												<option value="0" <? if($line['active'] == 0 ) { echo "selected"; } ?>>no</option>
												<option value="1" <? if($line['active'] == 1 ) { echo "selected"; } ?>>yes</option>
											</select>
									</div>
								</div>
								<div class="col-md">
									<div class="box">
										<small>Freq. inventaire</small>
										<select id="freq_inventory<?=$line['id']?>" name="freq_inventory" data-mini="true">
											<option value="high" <? if($line['freq_inventory'] =='high') { echo "selected"; } ?>>high</option>
											<option value="medium" <? if($line['freq_inventory'] =='medium') { echo "selected"; } ?>>medium</option>
											<option value="low" <? if($line['freq_inventory'] =='low') { echo "selected"; } ?>>low</option>
										</select>

									</div>
								</div>
								<div class="col-md">
									<div class="box">
										<small>Ref. supplier</small>
										<input type="text" name="supplier_reference" value="<?=$line['supplier_reference']?>"  data-mini="true" data-clear-btn="true" />
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-md">
									<div class="box">
										<small>Stock qtty</small><br/>
										<input type="text" id="stock_qtty-<?=$line['id']?>" name="stock_qtty" value="<?=round($line['stock_qtty'],2)?>"  data-mini="true" data-clear-btn="true" />
									</div>
								</div>
								<div class="col-md">
									<div class="box">
										<small>Stock warning</small><br/>
										<input type="text" id="stock_warning-<?=$line['id']?>" name="stock_warning" value="<?=$line['stock_warning']?>"  data-mini="true" data-clear-btn="true" />
									</div>
								</div>
								<div class="col-md">
									<div class="box">
										<small>Stock mini</small><br/>
										<input type="text" id="stock_mini-<?=$line['id']?>" name="stock_mini" value="<?=$line['stock_mini']?>"  data-mini="true" data-clear-btn="true" />
									</div>
								</div>
								<div class="col-md">
									<div class="box">
										<small>Stock max</small><br/>
										<input type="text" id="stock_max-<?=$line['id']?>" name="stock_max" value="<?=$line['stock_max']?>"  data-mini="true" data-clear-btn="true" />
									</div>
								</div>
								<div class="col-md">
									<div class="box">
										<small>Stock last up user</small><br/>
										<small><?=$line['last_update_user']?> <br /><?=$line['last_update_user_name']?></small>
									</div>
								</div>
								<div class="col-md">
									<div class="box">
										<small>Stock last up pos</small><br/>
										<small><?=$line['last_update_pos']?></small>
									</div>
								</div>
								<div class="col-md">
									<div class="box">
										<small>Comment</small><br/>
										<input type="text" name="comment" value="<?=$line['comment']?>"  data-mini="true" data-clear-btn="true" />
									</div>
								</div>
								<div class="col-md">
									<div class="box">
										<br/>
										<input type="submit" id="sub<?=$line['id']?>" onclick="validate(<?=$line['id']?>)" name="submit" value="Save" data-mini="true" data-clear-btn="true" />
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-md">
									<div class="box">
										<br/>
									</div>
								</div>
							</div>
							<input type="hidden" name="id" value="<?=$line['id']?>">
						</table>
					</form>
					</li>
				<? 	} 
				  } ?>
				</ul>  
				<?} 
				?>
				
							<? if($command == 'create') { ?>
								<?if($msg) { ?>
									<div style="background-color: #d6f0d6;" class="ui-body ui-body-a">	
										<?=$msg?> Thanks! Have A Nice Karma!"
									</div>
								<?}?>
								<ul data-role="listview" data-inset="true" data-split-theme="a" data-divider-theme="a">
								<li>
									<form id="pdt1" class="pdt1" name="pdt1" method="post" action="/product_admin/save">
									<table width="100%">
										<tr>
											<td><small>Name</small></td>
											<td><small>Supplier</small></td>
											<td><small>Prix H.T. /unité</small></td>
											<td><small>Colisage</small></td>
											<td><small>Unité de vente</small></td>
											<td><small>Category</small></td>
											<td><small>Active</small></td>
											<td><small>Freq. inventaire</small></td>
											<td><small>Ref. supplier</small></td>
										</tr>
											<tr>
												<td><input type="text" name="name" id="name-1" data-mini="true" data-clear-btn="true" /></td>
												<td>
													<select id="id_supplier" name="id_supplier"  data-mini="true">
														<option value="0"></option>
														<? foreach ($suppliers as $sup) { ?>
															<option value="<?=$sup['id']?>"><?=$sup['name']?></option>
															<? } ?>
														</select>
													</td>
													<td><input type="text" name="price" id="price-1" value="0"  data-mini="true" data-clear-btn="true" /></td>
													<td><input type="text" name="packaging" id="packaging-1" data-mini="true" data-clear-btn="true" /></td>
													<td>
														<select id="id_unit" name="id_unit"  data-mini="true">
															<? foreach ($products_unit as $id_unit) { ?>
																<option value="<?=$id_unit['id']?>"><?=$id_unit['name']?></option>
																<? } ?>
															</select>
														</td>
														<td>
															<select id="id_category" name="id_category"  data-mini="true">
																<? foreach ($products_category as $pdt_cat) { ?>
																	<option value="<?=$pdt_cat['id']?>"><?=$pdt_cat['name']?></option>
																	<? } ?>
																</select>
															</td>
																	<td>
																		<select id="active" name="active"  data-mini="true">
																			<option value="1" selected>yes</option>
																			<option value="0">no</option>

																		</select>
																	</td>
																	<td>
																		<select id="freq_inventory" name="freq_inventory" data-mini="true">
																			<option value="high">high</option>
																			<option value="medium">medium</option>
																			<option value="low">low</option>
																		</select>
																	</td>
																	<td><input type="text" name="supplier_reference" value=""  data-mini="true" data-clear-btn="true" /></td>
																</tr>
																<tr>
																	
																	<td><small>Stock qtty</small></td>
																	<td><small>Stock warning</small></td>
																	<td><small>Stock mini</small></td>
																	<td><small>Stock max</small></td>
																	<td colspan="4"><small>Comment</small></td>
																	<td></td>	
																</tr>
																<tr>
																	<td><input type="text" id="stock_qtty-1" name="stock_qtty" value=""  data-mini="true" data-clear-btn="true" /></td>
																	<td><input type="text" id="stock_warning-1" name="stock_warning" value=""  data-mini="true" data-clear-btn="true" /></td>
																	<td><input type="text" id="stock_mini-1" name="stock_mini" value=""  data-mini="true" data-clear-btn="true" /></td>
																	<td><input type="text" id="stock_max-1" name="stock_max" value=""  data-mini="true" data-clear-btn="true" /></td>
																	<td colspan="4"><input type="text" name="comment" data-mini="true" data-clear-btn="true" /></td>
																	<td><input type="submit" id="sub1" onclick="validate(1)" name="submit" value="Save" data-mini="true" data-clear-btn="true" /></td>		
																</tr>
																<input type="hidden" name="id" value="create">
															</table>
													</form>
					</li>
				</ul>
			<? } ?>
		</div><!-- /content -->
	</div><!-- /page -->
											<script src="/public/jqv/dist/jquery.validate.min.js" type="text/javascript"></script>
											<script src="/public/rmd.js" type="text/javascript"></script>

											<script>
											
											function isNumeric(n) {
												return !isNaN(parseFloat(n)) && isFinite(n);
											}
											
											function validate(idl) {
												var $form = $('#pdt' + idl);
												var done = 0;
												$form.on('submit', function() {
																										
													var name = $('#name-' + idl).val();
													var price = $('#price-' + idl).val();
													var stock_qtty = $('#stock_qtty-' + idl).val();				
													var stock_warning = $('#stock_warning-' + idl).val();	
													var stock_mini = $('#stock_mini-' + idl).val();	
													var stock_max = $('#stock_max-' + idl).val();	
													var packaging = $('#packaging-' + idl).val();	

													
													if(name == '' || price == '') {
														alert('Please fill mandatory fields.');
														return false;
													}
													
													
													var price_test 			= isNumeric(price);
													var stock_qtty_test 	= isNumeric(stock_qtty);
													var stock_warning_test 	= isNumeric(stock_warning);
													var stock_mini_test 	= isNumeric(stock_mini);
													var stock_max_test	 	= isNumeric(stock_max);
													var packaging_test	 	= isNumeric(packaging);
													
													
													if((!price_test && price) || (!stock_qtty_test && stock_qtty) || (!stock_warning_test && stock_warning) || (!stock_mini_test && stock_mini) || (!stock_max_test && stock_max) || (!packaging_test && packaging)) {
														alert('IMPORTANT! For decimal enter a  dot (.) and NOT a comma (,) and please enter numeric value for numeric fields.');
														return false;
													}
													
													$.ajax({
														url: $(this).attr('action'),
														type: $(this).attr('method'),
														data: $(this).serialize(),
														dataType: 'json',
														success: function(json) {
															if(json.reponse == 'okcreate' && done == 0) {
																done = done + 1;
																if(done == 1) {
																	window.location = "/product_admin/index/create1";
																	return false; 
																}
																
																return false;
															} else if(json.reponse == 'ok' && done == 0) {
																done = done + 1;
																if(done == 1) { 
																	alert('Saved!');
																	//location.reload(true);
																	return false; 
																}
																return false;
															} else if(done == 0){
																alert('WARNING! ERROR at saving : '+ json.reponse);
																//window.location = "/product_admin/index/create";
																return false;
															}
														}
													}).done(function(data) {
														return false;
													}).fail(function(data) {
														done = done + 1;
														if(done <= 1) { 
															alert('WARNING! ERROR at saving!');
															return false; 
														}
													});
													return false;
												});
											}
											</script>