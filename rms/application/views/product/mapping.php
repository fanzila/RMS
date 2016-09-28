
		</div>
		<div data-role="content">
			<div data-role="collapsible-set" data-inset="false" data-filter="true" data-filter-placeholder="Filter discounts">
			<? $i = 0; foreach ($products_pos as $line) { ?>
				<div data-role="collapsible">
					<h2><?=$line['name']?></h2>
					<form id="fo<?=$line['id']?>" class="fo<?=$line['id']?>" name="fo<?=$line['id']?>">
						<ul data-role="listview" data-theme="d" data-divider-theme="d">
							<li>
								<table border="0">
									<tr>
										<td colspan="4"><a href="#" data-role="button" data-icon="plus" id="btn_<?=$line['id']?>" data-mini="true" class="add_field_button_<?=$line['id']?>" onclick="apbtn(<?=$line['id']?>);">Add</a></td>
									</tr>
									<? foreach ($mapping as $keymap) { 
										if($keymap['id_pos'] == $line['id']) { 
											?>
											<tr>
												<td>
													<select id="product_<?=$line['id']?>_<?=$i?>" name="product_<?=$line['id']?>_<?=$i?>" data-mini="true"><? foreach ($products as $pdt) { ?><option value="<?=$pdt['id']?>" 
													<? if($pdt['id'] == $keymap['id_product']) echo "selected";?>><?=stripslashes($pdt['name'])?></option><? } ?></select></td><td><small>Coef:</small></td><td><input id="coef_<?=$line['id']?>_<?=$i?>" type="text" name="coef_<?=$line['id']?>_<?=$i?>" value="<?=$keymap['coef']?>" data-mini="true" data-clear-btn="true" />
												</td>
												<td>
													
												</td>
											</tr>
												<? } $i++; ?>
												<script> var x = <?=$i?>; </script>
												<? } ?>
										<tr>
											<td colspan="4">
												<div class="input_fields_wrap_<?=$line['id']?>"></div>		
											</td>					
										<tr>
											<td colspan="4">
												<input type="submit" id="submit_<?=$line['id']?>" name="submit_<?=$line['id']?>" value="Save" data-mini="true" onclick="validate(<?=$line['id']?>)" data-clear-btn="true" />
											</td>
										</tr>
								</table>
							</li>
						</ul>
					</form>
				</div>
			<? } ?>

					</div><!-- /content -->
				</div><!-- /page -->
				<script src="/public/jqv/dist/jquery.validate.min.js" type="text/javascript"></script>
				<script>
				var x = 0;
				function validate(idl) {
					var $form = $('#fo' + idl);
					var done = 0;
					$form.on('submit', function() {

						$.ajax({
							url: '/product_admin/save_mapping',
							type: 'post',
							data: $(this).serialize(),
							dataType: 'json',
							success: function(json) {
								
								if(json.reponse == 'ok' && done == 0) {
									done = done + 1;
									if(done == 1) { 
										alert('Saved!'); 
										return false; 
									}
									return false;
								} else if(done == 0){
									alert('WARNING! ERROR at saving : '+ json.reponse);
									return false;
								}
							}
						}).done(function(data) {
							//location.reload(true);
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

				function apbtn(idl) {
					var max_fields      = 100000; //maximum input boxes allowed
					var wrapper         = $(".input_fields_wrap_" + idl); //Fields wrapper

					if(! x){
					    x = 0;
					};

					if(x < max_fields){ //max input box allowed

						var text = '<div><tr><td><select id="product_' + idl + '_' + x + '" name="product_' + idl + '_' + x + '" data-mini="true"><option value="0"></option><? foreach ($products as $pdt) { ?><option value="<?=$pdt['id']?>"><?=addslashes($pdt['name'])?></option><? } ?></select></td><td> </td><td colspan="2"><input id="coef_' + idl + '_' + x + '" type="text" name="coef_' + idl + '_' + x + '" value="1" data-mini="true"> <a href="#" class="remove_field">X</a></td><td></td></tr></div>'
						$(wrapper).append(text); //add input box
					
						x++; //text box increment

					}

					$(wrapper).on("click",".remove_field", function(e){ //user click on remove text
						e.preventDefault(); $(this).parent('div').remove(); x--;
					})
				}

				</script>