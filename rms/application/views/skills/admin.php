		</div>
		<div data-role="content">
			<div data-role="collapsible-set" data-inset="true">
				<div data-role="collapsible" style="background-color : #e0e0e0">
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
				<div data-role="collapsible" style="background-color : #ffffff">
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
				<div data-role="collapsible" style="background-color : #e0e0e0">
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
				<div data-role="collapsible" style="background-color : #fff">
					<h1>Skills by person</h1>					
					<ul data-role="listview" data-inset="true" data-filter="true" style="background-color : #e0e0e0">
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
				<div data-role="collapsible" style="background-color : #e0e0e0">
					<h1>Add New Skills</h1>
					<?$attributes = array('id' => "skill", 'name' => "skill");
					echo form_open("skills/create/skill/", $attributes);?>
						<table width="100%" style="background-color: #ffffff; border: 1px solid #dedcd7; margin-top:10px" cellpadding="8">
							<tr>
								<td colspan="3" style="background-color: #fbf19e;">Topic to create :
								</td>
							</tr>
							<tr>
								<td width="8%"><label for="name[1]" id="label">Topic:</label></td>
								<td width="82%"><input type="text" id="name[1]" name="name[1]" value="" data-clear-btn="true" /></td>
								<td width="10%">
									<?$attributes = array('id' => "sub-skill", 'name' => "submit");
									echo form_submit($attributes, 'Save');?>
								</td>
							</tr>
						</table>
					</form>
					<?$attributes = array('id' => "cat", 'name' => "cat");
						echo form_open("skills/create/cat", $attributes);?>
					<table width="100%" style="background-color: #ffffff; border: 1px solid #dedcd7; margin-top:10px" cellpadding="8">
						<tr>
							<td colspan="3" style="background-color: #fbf19e;">Category to create :
							</td>
						</tr>
						<tr>
							<td width="8%"><label for="name[2]" id="label">Category:</label></td>
							<td width="82%"><input type="text" id="name[2]" name="name[2]" value="" data-clear-btn="true" /></td>
							<td width="10%">
								<?$attributes = array('id' => "sub-cat", 'name' => "submit");
								echo form_submit($attributes, 'Save');?>
							</td>
						</tr>
					</table>
					</form>
					<?$attributes = array('id' => "subcat", 'name' => "subcat");
					echo form_open("skills/create/subcat", $attributes);?>
					<table width="100%" style="background-color: #ffffff; border: 1px solid #dedcd7; margin-top:10px" cellpadding="8">
						<tr>
							<td colspan="3" style="background-color: #fbf19e;">Sub-category to create :
							</td>
						</tr>
						<tr>
							<td width="8%"><label for="name[3]" id="label">Sub-Category:</label></td>
							<td width="82%"><input type="text" id="name[3]" name="name[3]" value="" data-clear-btn="true" /></td>
							<td width="10%">
								<?$attributes = array('id' => "sub-subcat", 'name' => "submit");
								echo form_submit($attributes, 'Save');?>
							</td>
						</tr>
					</table>
					</form>

					<?$attributes = array('id' => "skills_item", 'name' => "skills_item");
					echo form_open("skills/createItem", $attributes);?>
						<table width="100%" style="background-color: #ffffff; border: 1px solid #dedcd7; margin-top:10px" cellpadding="8">
							<tr>
								<td colspan="3" style="background-color: #fbf19e;">Skill item to create :
								</td>
							</tr>
							<tr>
								<td>
									<select style="background-color:#a1ff7c" name="s_skill" id="s_skill" data-inline="true" data-theme="a" required>
										<option value="">Select a Topic</option>
										<?foreach ($skills as $skill) {?>
											<option value="<?=$skill->id?>"><?=$skill->name?></option>
										<? } ?>
									</select>
									<select style="background-color:#a1ff7c" name="s_cat" id="s_cat" data-inline="true" data-theme="a" required>
										<option value="">Select a category</option>
										<?foreach ($skills_categories as $skills_category) {?>
											<option value="<?=$skills_category->id?>"><?=$skills_category->name?></option>
										<? } ?>
									</select>
									<select style="background-color:#a1ff7c" name="s_subcat" id="s_subcat" data-inline="true" data-theme="a" required>
										<option value="">Select a sub-category</option>
										<option value="0">NONE</option>
										<?foreach ($skills_sub_categories as $skills_sub_category) {?>
											<option value="<?=$skills_sub_category->id?>"><?=$skills_sub_category->name?></option>
										<? } ?>
									</select>
								</td>
							</tr>
							<tr>
								<td><input type="text" id="s_item" name="s_item" value="Name of the item" onFocus="this.select()" data-clear-btn="true" /></td>
							</tr>
							<tr>
								<td>
									<?$attributes = array('id' => "s_sub", 'name' => "submit");
									echo form_submit($attributes, 'Save');?>
								</td>
							</tr>
						</table>
					</form>					
				</div>
				<div data-role="collapsible" style="background-color : #ffffff">
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