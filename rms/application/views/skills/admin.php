		</div>
		<div data-role="content">
			<?php $id_bu =  $this->session->userdata('id_bu'); ?>
			<div data-role="collapsible-set" data-inset="true">
			<style type="text/css">	
				td { 
					text-align: left;
					vertical-align: middle;
					}
					
				tr:hover {background-color: #f5f5f5;}
				tr:nth-child(even) {background-color: #f2f2f2;}
				
				th {
				  background-color: #4CAF50;
				  color: white;
				}
				</style>
				<table border="0" style="background-color: #ffffff; border: 2px solid #dedcd7; margin-top:10px" cellpadding="2" cellspacing="1">
					
					<tr>
						<td><b>Skills</b></td>
						<?
						foreach($userswithsponsor as $user){ 
							$sponsor_fill = false; ?>
							<td style="text-align: center;"><a data-ajax="false" href="/skills/index/<?=$user->id?>/1"><?=$user->username?></a>
								<br />
								<small>
							<?foreach ($skills_records as $skills_record) {
								if($user->id == $skills_record->id_user && $skills_record->id_bu == $id_bu) {
									$sponsor_fill = true;
									?>
									Sponsor : <?=$skills_record->sponsorname?>
									<?
								}
							} 
							if($sponsor_fill == false) echo "No sponsor"; ?></small>
						</td>
						<? } ?>
					</tr>
					

						<?foreach($skills_items_map as $skills_item) { ?>
					<tr>
						<td>
								<?=$skills_item->s_name?> ->
								<?=$skills_item->c_name?> -> 
								<?=$skills_item->sub_name?>
								
	
						</td>
					
						<?foreach($userswithsponsor as $user){ ?>
							<td style="text-align: center;">
								<?
								$checked = false;
								if(isset($checked_subcat_byuser[$user->id])) {
									if($checked_subcat_byuser[$user->id][$skills_item->ssc_id] == true) {
									$checked = true;
										echo "✅";
									}
								}
								if(!$checked) echo "❌";
								?>		
							</td>
						<? } ?>
							
					</tr>	
					<? } ?>
				</table>
				<br />
				<div data-role="collapsible" style="background-color : #f8f8f9">
					<h1>Create a sponsoring link</h1>
					<?
					$attributes = array('id' => "sponsorship", 'name' => "sponsorship");
					echo form_open("skills/save", $attributes);?>
						<table width="100%" style="background-color: #ffffff; border: 1px solid #dedcd7; margin-top:10px" cellpadding="8">
							<tr>
								<td colspan="2" style="background-color: #fbf19e;">Sponsorship to create :
								</td>
							</tr>
							<tr>
								<td width="6%"><label for="sponsor" id="label">Sponsor :</label></td>
								<td width="94%">
									<select style="background-color:#a1ff7c" name="sponsor" id="sponsor" data-inline="true" data-theme="a" required>
										<option value="">Sponsor</option>
										<?foreach ($users as $user) {?>
											<option value="<?=$user->id?>" <? if(isset($form['user']) AND $form['user']==$user->id) { ?> selected <? } ?>><?=$user->first_name?> <?=$user->last_name?>
											</option>
										<? } ?>
									?></select>
								</td>
							</tr>
							<tr>
								<td><label for="user" id="label">Trainee:</label></td>
								<td>
									<select style="background-color:#a1ff7c" name="user" id="user" data-inline="true" data-theme="a" required>
										<?$ok=0;?>
										<option value="">Trainee</option>
										<?foreach ($users as $user) {?>
											<?$ok=0;?>
											<?if($user->id!=$current_user){?>
												<?foreach ($skills_records as $skills_record) {?>
													<?if($user->username == $skills_record->username){
														$ok=1;
														break;
													}?>
												<?}?>
												<?if($ok==0){?>
													<option value="<?=$user->id?>" <? if(isset($form['user']) AND $form['user']==$user->id) { ?> selected <? } ?>><?=$user->first_name?> <?=$user->last_name?>
													</option>
												<?}?>
											<?}?>
										<?}?>
									?></select>
								</td>
							</tr>
							<tr>
								<td colspan="2">
									<?$attributes = array('id' => "sub", 'name' => "submit");
									echo form_submit($attributes, 'Save');?>
								</td>
							</tr>
						</table>
					</form>
				</div><!--/collapsible-->
					
				<div data-role="collapsible" style="background-color : #f8f8f9">
					<h1>Log</h1>
					<table style="background-color : #f0f0f0" data-role="table" id="table-custom-2" data-mode="reflow" data-filter="true" class="ui-body-d ui-shadow table-stripe ui-responsive" data-column-popup-theme="a" data-filter-placeholder="Filter logs">
						<thead>
							<tr>
								<th>ID</th>
								<th>User</th>
								<th>Action</th>
								<th>User touched</th>
								<th>Item touched</th>
								<th>Validated</th>
								<th>Comment</th>
								<th>Date</th>
							</tr>
						</thead>
						<tbody>
							<?foreach ($skills_logs as $skills_log) {?>

								<tr>
									<td><?=$skills_log->id?></td>
									<td><?=$skills_log->username?></td>
									<td><?=$skills_log->type?></td>
									<td><?=$skills_log->toucheduser?></td>
									<td><?=$skills_log->name?></td>
									<td><?=$skills_log->checked?></td>
									<td><?=$skills_log->comment?></td>
									<td><?=$skills_log->date?></td>
								</tr>
						<?}?>
						</tbody>
					</table>
				</div>

			<?if($this->ion_auth_acl->has_permission('skills_admin')){?>
				<?php if($level > 1) { ?>
					<p><a href="/skills/general/" rel="external" data-ajax="false" class="ui-btn ui-btn-raised">Manage Skills</a></p>
				<?php } ?>
			<?php } ?>
			</div><!--/collapsible set-->
		</div><!-- /content -->
	</div><!-- /page -->
	<script>
		$(document).ready(function() {

			var $form = $('#sponsorship');
			var $valid;

			$('#sub').on('click', function() {
				$form.trigger('submit');
				return false;
			});

			$form.on('submit', function() {

				var sponsor = $('#sponsor').val();
				var user = $('#user').val();

				if(sponsor == '') {
					alert('Please fill sponsor.');
				} else if(user == 0){
					alert('Please indicate who has to learn.');
				} else if(user == sponsor){
					alert('You have to choose two different people.');
				}else {
					$.ajax({
						url: $(this).attr('action'),
						type: $(this).attr('method'),
						data: $(this).serialize(),
						dataType: 'json',
						success: function(json) {
							if(json.reponse == 'ok') {
								//alert('Saved!');
								$valid=1;
							} else {
								alert('WARNING! ERROR at saving : '+ json.reponse);
								$valid = 0;
							}
						}
					}).done(function(data) {
							if($valid == 1)
								window.location = "/skills/index/"+user+"/1";
					    }).fail(function(data) {
					    	alert('WARNING! ERROR at saving!');
					    });
				}
				return false;
			});
		});
	</script>
	<script>
		$(document).ready(function() {
			var $form = $('#skill');
			$('#sub-skill').on('click', function() {
				$form.trigger('submit');
				return false;
			});

			$form.on('submit', function() {
				var name = document.getElementById("name[1]").value;
				if(name == '') {
					alert('There\'s just one thing to fill, seriously...');
				} else {
					$.ajax({
						url: $(this).attr('action'),
						type: $(this).attr('method'),
						data: $(this).serialize(),
						dataType: 'json',
						success: function(json) {
							if(json.reponse == 'ok') {
								alert('Saved!');
							} else {
								alert('WARNING! ERROR at saving : '+ json.reponse);
							}
						}
					}).done(function(data) {
							location.reload(true);
					    }).fail(function(data) {
					    	alert('WARNING! ERROR at saving!');
					    });
				}
				return false;
			});
		});
	</script>
	<script>
		$(document).ready(function() {
			var $form = $('#cat');
			$('#sub-cat').on('click', function() {
				$form.trigger('submit');
				return false;
			});

			$form.on('submit', function() {
				var name = document.getElementById("name[2]").value;
				if(name == '') {
					alert('There\'s just one thing to fill, seriously...');
				} else {
					$.ajax({
						url: $(this).attr('action'),
						type: $(this).attr('method'),
						data: $(this).serialize(),
						dataType: 'json',
						success: function(json) {
							if(json.reponse == 'ok') {
								alert('Saved!');
							} else {
								alert('WARNING! ERROR at saving : '+ json.reponse);
							}
						}
					}).done(function(data) {
							location.reload(true);
					    }).fail(function(data) {
					    	alert('WARNING! ERROR at saving!');
					    });
				}
				return false;
			});
		});
	</script>
	<script>
		$(document).ready(function() {
			var $form = $('#subcat');
			$('#sub-subcat').on('click', function() {
				$form.trigger('submit');
				return false;
			});

			$form.on('submit', function() {
				var name = document.getElementById("name[3]").value;
				if(name == '') {
					alert('There\'s just one thing to fill, seriously...');
				} else if(name == 'NONE' || name == 'None' || name == 'none') {
					alert('This name is reserved.');
				} else {
					$.ajax({
						url: $(this).attr('action'),
						type: $(this).attr('method'),
						data: $(this).serialize(),
						dataType: 'json',
						success: function(json) {
							if(json.reponse == 'ok') {
								alert('Saved!');
							} else {
								alert('WARNING! ERROR at saving : '+ json.reponse);
							}
						}
					}).done(function(data) {
							location.reload(true);
					    }).fail(function(data) {
					    	alert('WARNING! ERROR at saving!');
					    });
				}
				return false;
			});
		});
	</script>
	<script>
		$(document).ready(function() {

			var $form = $('#skills_item');

			$('#s_sub').on('click', function() {
				$form.trigger('submit');
				return false;
			});

			$form.on('submit', function() {
				var s_skill	= $('#s_skill').val();
				var s_cat	= $('#s_cat').val();
				var s_subcat= $('#s_subcat').val();
				var s_item	= $('#s_item').val();

				if(s_skill == '') {
					alert('Please select the skill.');
				}else if(s_cat == ''){
					alert('Please select the category.');
				}else if(s_subcat == ''){
					alert('Please select the sub-category.');
				}else if(s_item == '' || s_item == 'Name of the item'){
					alert('Please fill the name of the new item.');
				}else {
					$.ajax({
						url: $(this).attr('action'),
						type: $(this).attr('method'),
						data: $(this).serialize(),
						dataType: 'json',
						success: function(json) {
							if(json.reponse == 'ok') {
								alert('Saved!');
							} else {
								alert('WARNING! ERROR at saving : '+ json.reponse);
							}
						}
					}).done(function(data) {
							location.reload(true);
					    }).fail(function(data) {
					    	alert('WARNING! ERROR at saving!');
					    });
				}
				return false;
			});
		});
	</script>