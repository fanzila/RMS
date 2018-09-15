</div>
		<div data-role="content"><?
			if($create) {
			?>
				<?$attributes = array('id' => "discount", 'name' => "discount");
				echo form_open("discount/save", $attributes);?>
					<table width="100%" style="border: 1px solid #dedcd7; margin-top:10px" cellpadding="8">
						<tr>
							<td colspan="2" style="background-color: #fbf19e;">Discount information :
							</td>
						</tr>
						<tr>
							<td>
								<label for="client" id="label">Client:</label>
								<input id="client" type="text" name="client" value="" data-clear-btn="true" />
								<label for="reason" id="label">Reason:</label>
								<input id="reason" type="text" name="reason" value="" data-clear-btn="true" />
								<label for="nature" id="label">Nature:</label>
								<input id="nature" type="text" name="nature" value="" data-clear-btn="true" />
							</td>
						</tr>
						<tr>
							<td>
								<label for="user" id="label">User saving the discount:</label>
								<select style="background-color:#a1ff7c" name="user" id="user" data-inline="true" data-theme="a" required>
									<option value="0">User</option>
									<?foreach ($users as $user) {?>
										<option value="<?=$user->id?>" <? if(isset($form['user']) AND $form['user']==$user->id) { ?> selected <? } ?>><?=$user->first_name?> <?=$user->last_name?>
										</option>
									<? } ?>
								</select>
								
								<label for="persistent" id="label">Persistent:</label>
								<select style="background-color:#a1ff7c" name="persistent" id="persistent" data-inline="true" data-theme="a" required>
									<option value="0">No</option>
									<option value="1">Yes</option>
									</select>	
									<label for="allrest" id="label">Valid in all restaurants:</label>
									<select style="background-color:#a1ff7c" name="allrest" id="allrest" data-inline="true" data-theme="a" required>
										<option value="0">No</option>
										<option value="999999">Yes</option>
										</select>	
							</td>
						</tr>
					</table>
						<input type="hidden" name="id" value="create">
						<?$attributes = array('id' => "sub", 'name' => "submit");
						echo form_submit($attributes, 'Save');?>
				</form>

						<script>
						$(document).ready(function() {

							var $form = $('#discount');

							$('#sub').on('click', function() {
								$form.trigger('submit');
								return false;
							});

							$form.on('submit', function() {

								var nature = $('#nature').val();
								var user = $('#user').val();
								var client = $('#client').val();
								var reason = $('#reason').val();

								if(nature == '') {
									alert('Please fill discount nature.');
								} else if(user == 0){
									alert('Please indicate who you are.');
								}else {
									$.ajax({
										url: $(this).attr('action'),
										type: $(this).attr('method'),
										data: $(this).serialize(),
										dataType: 'json',
										success: function(json) {
											if(json.reponse == 'ok') {
												//alert('Saved!');
											} else {
												alert('WARNING! ERROR at saving : '+ json.reponse);
											}
										}
									}).done(function(data) {
											window.location = "/discount/index/create";
									    }).fail(function(data) {
									    	alert('WARNING! ERROR at saving!');
									    });
								}
								return false;
							});
						});

						</script>
			<? } ?>
				</div><!-- /content -->
			</div><!-- /page -->
