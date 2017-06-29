		</div>
		<div data-role="content">
			<div data-role="collapsible-set" data-inset="true">
				<div data-role="collapsible" style="background-color : #f8f8f9">
					<h1>Create a sponsoring link</h1>
					<?$attributes = array('id' => "sponsorship", 'name' => "sponsorship");
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
					<h1>Sponsors map</h1>
					<table style="background-color : #f0f0f0" data-role="table" id="table-custom-2" data-mode="reflow" data-filter="true" class="ui-body-d ui-shadow table-stripe ui-responsive" data-column-popup-theme="a" data-filter-placeholder="Filter...">
						<thead>
							<tr>
								<th>Sponsor</th>
								<th>Trainee</th>
							</tr>
						</thead>
						<?$id_bu =  $this->session->all_userdata()['bu_id'];?>
						<tbody>
							<?foreach ($skills_records as $skills_record) {
									if($skills_record->bu_id == $id_bu){?>
										<tr>
											<td><?=$skills_record->sponsorname?></td>
											<td><?=$skills_record->username?></td>
										</tr>
									<?}?>
								<?//if($skills_record->id_user == $user->bu_id){?>
							<?}?>
						</tbody>
					</table>
				</div>
				<div data-role="collapsible" style="background-color : #f8f8f9">
					<h1>Skills map</h1>
					<table style="background-color : #fff" data-role="table" id="table-custom-2" data-mode="reflow" data-filter="true" class="ui-body-d ui-shadow table-stripe ui-responsive" data-column-popup-theme="a" data-filter-placeholder="Filter...">
						<thead>
							<tr>
								<th>Skill</th>
								<th>Staff</th>
							</tr>
						</thead>
						<tbody>
							<?foreach ($skills as $skill) {?><!--Affichage des skills générales-->
							<?$check=0;foreach($skills_items as $skills_item){
								if($skills_item->s_name == $skill->name){
									$check+=1;
									break;
								}
							}?>
							<?if($check==1){?>
							
										<?foreach ($skills_categories as $category) {?><!--Affichage des Catégories-->
											<?$check=0;foreach($skills_items as $skills_item){
												if($skills_item->c_name == $category->name && $skills_item->s_name == $skill->name){
													$check+=1;
													break;
												}
											}?>
											<?if($check==1){?>
														<?foreach($users as $user){
															$validated=0;
															foreach ($skills_items as $skills_item) {
																if($skills_item->c_name == $category->name && $skills_item->id_user == $user->id){
																	if($skills_item->checked == true){
																		$validated=1;
																	}else{
																		$validated=0;
																		break;
																	}
																}
															}
															if($validated == 1){
																?>
																<tr>
																	<td><?=$skill->name?> - <?=$category->name?></td>
																	<td><?=$user->username?></td>
																</tr>
																<?
															}
														}?>
											<?}?>
										<?}?>
							<?}?>
						<?}?>
						</tbody>
					</table>
				</div>
				<div data-role="collapsible" style="background-color : #f8f8f9">
					<h1>Skills by person</h1>					
					<ul data-role="listview" data-inset="true" data-filter="true" style="background-color : #f8f8f9">
						<?$ok=0;?>
						<?foreach ($users as $user) {?>
							<?$ok=0;?>
							<?if($user->id!=$current_user){?>
								<?foreach ($skills_records as $skills_record) {?>
									<?if($user->username == $skills_record->username){
										$ok=1;
										break;
									}?>
								<?}?>
								<?if($ok==1){?>
									<li><a data-ajax="false" href="/skills/index/<?=$user->id?>/1"><?=$user->first_name?> <?=$user->last_name?></font></a></li>
								<?}?>
							<?}?>
						<?}?>
					</ul>
				</div>
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

				<?php if($level >= 3) { ?>
					<p><a href="/skills/general/" rel="external" data-ajax="false" class="ui-btn ui-btn-raised">Manage Skills</a></p>
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