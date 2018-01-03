		</div>
<style>
	ul {
  list-style-image: none;
  list-style: none;
  list-style-type: none;
}
</style>
		<div role="main" class="ui-content wow fadeIn" data-inset="false">
			<div data-role="content">
				<div class="ui-body ui-corner-all">
					<?
					$i=0;
					$rouge_petard	= "#ffbfb5";
					$rouge_doux		= "#ffbfb5";
					$R_font_color	= "#ff0000";
					$vert_pomme		= "#77ff77";
					$vert_leger		= "#77ff77";
					$V_font_color	= "#4a7b50";?>
					<form id="skills" name="skills" method="post" action="/skills/saveSkills/">
					<? if($skills_items!=null){ ?>
						<div data-inset="false">
							<?foreach ($skills as $skill) {?><!--Affichage des skills générales-->
								<?$check=0;foreach($skills_items as $skills_item){
									if($skills_item->s_name == $skill->name){
										$check+=1;
										break;
									}
								}?>
								<?if($check==1){?>
									<?$color = $vert_leger;
									$check=0;
									foreach($skills_items as $skills_item){
										foreach ($skills_categories as $category) {
											if($skills_item->c_name == $category->name && $skills_item->s_name == $skill->name){
												foreach ($skills_sub_categories as $sub_category) {
													if($skills_item->sub_name == $sub_category->name && $skills_item->c_name == $category->name){
														if($skills_item->checked == false){
															$check+=1;
															break;
														}
													}
												}
											}
										}
										if($check == 1){
											$color = $rouge_doux;
											$valided = '<i class="zmdi"></i>';
											break;
										}
									}?>
									<div data-role="collapsible" <?if($check==1){?> data-collapsed="false"<?}?>>
										<h4 style="background-color: <?=$color?>"><?=$skill->name?></h4>
										<div data-inset="true">
											<?foreach ($skills_categories as $category) {?><!--Affichage des Catégories-->
												<?$check=0;foreach($skills_items as $skills_item){
													if($skills_item->c_name == $category->name && $skills_item->s_name == $skill->name){
														$check+=1;
														break;
													}
												}?>
												<?if($check==1){?>
													<?$color = $vert_leger;
													$valided = '<i class="zmdi"></i>';
													$check=0;
													foreach($skills_items as $skills_item){
														if($skills_item->c_name == $category->name){
															foreach ($skills_sub_categories as $sub_category) {
																if($skills_item->sub_name == $sub_category->name && $skills_item->c_name == $category->name){
																	if($skills_item->checked == false) $check+=1;
																}
															}
														}
														if($check == 1){
															$color = $rouge_doux;
															$valided = '<i class="zmdi"></i>';
															break;
														}
													}?>
													<div data-role="collapsible" <?if($check==1){?> data-collapsed="false"<?}?>>
														<h4 style="background-color: <?=$color?>"><?=$valided?> <?=$category->name?> <?=$valided?></h4>
														<ul data-role="">
															<?foreach($skills_items as $skills_item){?>
																<?if($skills_item->sub_id == 0 && $skills_item->c_name == $category->name && $skills_item->s_name == $skill->name){?>
																	<?$bkg_color = '';
																	if($skills_item->checked == true) $bkg_color = $vert_pomme;
																	else $bkg_color = $rouge_petard?>
																	<?if($this->ion_auth_acl->has_permission('add_comment_skill')){?>
																							<li>
																								<input type="checkbox" class="custom" name="checked[<?=$i?>]" id="checked[<?=$i?>]" <?if($skills_item->checked == true) { ?>checked<? } ?>>
																								<label style="background-color: <?=$bkg_color?>" for="checked[<?=$i?>]" id="label[<?=$i?>]"> <?=$skills_item->i_name?> <?if($skills_item->date!=null){?><i><span style="font-size:smaller">(recorded the : <?=$skills_item->date?>)<br/></span></i><?}?><?if($skills_item->comment!=null){?>> Comment : <?=$skills_item->comment?><?}?></label> <?if(!empty($skills_item->i_link)) { ?><small><a target="_blank" href="<?=$skills_item->i_link?>">> BOOK</a></small><?}?>
																								
<label style="font-size:smaller" for="comment[<?=$i?>]">> Comment :</label>																								<input data-inline="true" data-theme="a" class="input" data-form="ui-body-a" type="text" id="comment[<?=$i?>]" name="comment[<?=$i?>]" value="<?=$skills_item->comment?>"  data-clear-btn="true" />											</li>
																						<?}else{?>
																							<li>
																								<label id="label[<?=$i?>]"><?if($skills_item->checked == true){?><font size="3" color="<?=$V_font_color?>"><i class="zmdi zmdi-check-circle"></i><?}else{?><font size="4" color="<?=$R_font_color?>"><i class="zmdi zmdi-circle-o"></i><?}?> <?=$skills_item->i_name?> <?if($skills_item->date!=null){?><i><span style="font-size:smaller">(recorded the : <?=$skills_item->date?>)<br/></span></i><?}?><?if($skills_item->comment!=null){?>> Comment : <?=$skills_item->comment?><?}?></font></label> <?if(!empty($skills_item->i_link)) { ?><small><a target="_blank" href="<?=$skills_item->i_link?>">> BOOK</a></small><?}?>
																							</li>
																						<?}?>
																	<input type="hidden" id="id_item[<?=$i?>]" name="id_item[<?=$i?>]" value="<?=$skills_item->i_id?>">
																	<?$i+=1;?>
																<?}?>
															<?}?>
														</ul>

														<div data-inset="true">
															<?foreach ($skills_sub_categories as $sub_category) {?><!--Affichage des sub-catégories-->
																<?$check=0;foreach($skills_items as $skills_item){
																	if($skills_item->sub_name == $sub_category->name && $skills_item->c_name == $category->name && $skills_item->sub_name!= 'NONE') $check+=1;
																	if($check == 1) break;
																}?>
																<?if($check==1){?>
																	<?$color = $vert_leger;
																	$valided = '<i class="zmdi"></i>';
																	$check=0;
																	foreach($skills_items as $skills_item){
																		if($skills_item->sub_name == $sub_category->name && $skills_item->c_name == $category->name){
																			if($skills_item->checked == false) $check+=1;
																		}
																		if($check == 1){
																			$color = $rouge_doux;
																			$valided = '<i class="zmdi"></i>';
																			break;
																		}
																	}?>
																	<div data-role="collapsible" <?if($check==1){?> data-collapsed="true"<?}?>>
																		<h4 style="background-color: <?=$color?>"><?=$valided?> <?=$sub_category->name?></h4>
																		<ul data-role="">
																			<?foreach($skills_items as $skills_item){?>
																				<?if($skills_item->sub_name == $sub_category->name && $skills_item->c_name == $category->name && $skills_item->s_name == $skill->name){?>
																					<?$bkg_color = '';
																					if($skills_item->checked == true) $bkg_color = $vert_pomme;
																					else $bkg_color = $rouge_petard?>
																						<?if($this->ion_auth_acl->has_permission('add_comment_skill')){?>
																							<li>
																								<input type="checkbox" class="custom" name="checked[<?=$i?>]" id="checked[<?=$i?>]" <?if($skills_item->checked == true) { ?>checked<? } ?>>
																								<label style="background-color: <?=$bkg_color?>" for="checked[<?=$i?>]" id="label[<?=$i?>]"> <?=$skills_item->i_name?> <?if($skills_item->date!=null){?><i><span style="font-size:smaller">(recorded the : <?=$skills_item->date?>)<br/></span></i><?}?><?if($skills_item->date!=null){?>> Comment : <?=$skills_item->comment?><?}?></label>

<?if(!empty($skills_item->i_link)) { ?><small><a target="_blank" href="<?=$skills_item->i_link?>">> BOOK</a></small><?}?>																
																								<label style="font-size:smaller" for="comment[<?=$i?>]">> Comment :</label>
																								<input data-inline="true" data-theme="a" class="input" data-form="ui-body-a" type="text" id="comment[<?=$i?>]" name="comment[<?=$i?>]" value="<?=$skills_item->comment?>"  data-clear-btn="true" /> 
																							</li>
																						<?}else{?>
																							<li>
																								<label id="label[<?=$i?>]"><?if($skills_item->checked == true){?><font size="3" color="<?=$V_font_color?>"><i class="zmdi zmdi-check-circle"></i><?}else{?><font size="4" color="<?=$R_font_color?>"><i class="zmdi zmdi-circle-o"></i><?}?> <?=$skills_item->i_name?> <?if($skills_item->date!=null){?><i><span style="font-size:smaller">(recorded the : <?=$skills_item->date?>)<br/></span></i><?}?><?if($skills_item->comment!=null){?>> Comment : <?=$skills_item->comment?><?}?></font></label>
																							</li>
																						<?}?>
																					<input type="hidden" id="id_item[<?=$i?>]" name="id_item[<?=$i?>]" value="<?=$skills_item->i_id?>">
																					<?$i+=1;?>
																				<?}?>
																			<?}?>
																		</ul>
																	</div><!-- /collapsible -->
																<?}?>
															<?}?>
														</div><!-- /insert for sub categories -->
													</div><!-- /collapsible -->
												<?}?>
											<?}?>
										</div><!-- /inset for categories -->
									</div><!-- /collapsible -->
								<?}?>
							<?}?>
						</div><!-- /skills filter -->
						<input type="hidden" id="id_record" name="id_record" value="<?=$skills_item->id?>">
						<input type="hidden" id="i" name="i" value="<?=$i?>">
						<?if($this->ion_auth_acl->has_permission('add_comment_skill')){?><input type="button" id="save" name="save" value="SAVE" style="background-color: #303030" ><?}?>
					<?}else{?>
						<h2>You have no sponsor yet.</h2>
					<?}?>
					</form>

					<script>
						$(document).ready(function() {
							var $form = $('#skills');
							$('#save').on('click', function() {
								$form.trigger('submit');
								return false;
							});

							$form.on('submit', function() {

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
									location.reload(true);
								}).fail(function(data) {
									alert('WARNING! ERROR at saving!');
								});
								return false;
							});
						});
					</script>
				</div><!-- /theme -->
			</div><!-- /content -->
		</div>
	</div><!-- /page -->