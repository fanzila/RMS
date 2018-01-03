<?//		<a href="/skills/log/" class="ui-btn ui-btn-right" rel="external" data-ajax="false" data-icon="plus"><i class="zmdi zmdi-more-vert"></i></a>?>
		</div>

		<div role="main" class="ui-content wow fadeIn" data-inset="false">

			<div data-role="content">
				<div class="ui-body ui-corner-all">
					<br/>
					<?
					$rouge_petard	= "#ff1919";
					$rouge_doux		= "#ff7777";
					$vert_pomme		= "#19b319";
					$vert_leger		= "#77ff77";?>
					<form id="skills" name="skills" method="post" action="/skills/save/">
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
											$valided = '<i class="zmdi zmdi-star-border zmd-fw"></i>';
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
													$valided = '<i class="zmdi zmdi-star zmd-fw"></i>';
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
															$valided = '<i class="zmdi zmdi-star-border zmd-fw"></i>';
															break;
														}
													}?>
													<div data-role="collapsible" <?if($check==1){?> data-collapsed="false"<?}?>>
														<h4 style="background-color: <?=$color?>"><?=$valided?> <?=$category->name?> <?=$valided?></h4>
														<div data-inset="true">
															<?foreach ($skills_sub_categories as $sub_category) {?><!--Affichage des sub-catégories-->
																<?$check=0;foreach($skills_items as $skills_item){
																	if($skills_item->sub_name == $sub_category->name && $skills_item->c_name == $category->name) $check+=1;
																	if($check == 1) break;
																}?>
																<?if($check==1){?>
																	<?$color = $vert_leger;
																	$valided = '<i class="zmdi zmdi-star zmd-fw"></i>';
																	$check=0;
																	foreach($skills_items as $skills_item){
																		if($skills_item->sub_name == $sub_category->name && $skills_item->c_name == $category->name){
																			if($skills_item->checked == false) $check+=1;
																		}
																		if($check == 1){
																			$color = $rouge_doux;
																			$valided = '<i class="zmdi zmdi-star-border zmd-fw"></i>';
																			break;
																		}
																	}?>
																	<div data-role="collapsible" <?if($check==1){?> data-collapsed="false"<?}?>>
																		<h4 style="background-color: <?=$color?>"><?=$valided?> <?=$sub_category->name?></h4>
																		<ul data-role="">
																			<?foreach($skills_items as $skills_item){?>
																				<?if($skills_item->sub_name == $sub_category->name && $skills_item->c_name == $category->name && $skills_item->s_name == $skill->name){?>
																					<?$bkg_color = '';
																					if($skills_item->checked == true) $bkg_color = $vert_pomme;
																					else $bkg_color = $rouge_petard?>
																					<input type="checkbox" name="<?=$skills_item->i_id?>-<?=$skills_item->c_name?>-<?=$skills_item->sub_name?>" id="task-<?=$skills_item->i_id?>-<?=$skills_item->c_name?>-<?=$skills_item->sub_name?>" class="custom" <?if($skills_item->checked == true) { ?>checked<? } ?> />
																					<label style="background-color: <?=$bkg_color?>" for="task-<?=$skills_item->i_id?>-<?=$skills_item->c_name?>-<?=$skills_item->sub_name?>" id="label-<?=$skills_item->i_name?>"> <?=$skills_item->i_name?> <i><span style="font-size:smaller">(recorded the : <?=$skills_item->date?>)<br/></span></i><?if($skills_item->comment!=null){?>Comments : <?=$skills_item->comment?><?}?></label>
																					<?if($this->ion_auth_acl->has_permission('add_comment_skill')){?>
																						<label style="font-size:smaller" for="comment-<?=$skills_item->i_id?>-<?=$skills_item->c_name?>-<?=$skills_item->sub_name?>">Comment :</label>
																						<input data-inline="true" data-theme="a" class="input" data-form="ui-body-a" type="text" id="comment-<?=$skills_item->i_id?>-<?=$skills_item->c_name?>-<?=$skills_item->sub_name?>" name="comment-<?=$skills_item->i_id?>-<?=$skills_item->c_name?>-<?=$skills_item->sub_name?>" value="<?=$skills_item->comment?>"  data-clear-btn="true" />
																					<?}?>

																				<?/*	<p><?if($skills_item->checked == true){?><font size="4" color="#4a7b50"><i class="zmdi zmdi-check"></i><?}?> <?=$skills_item->i_name?> <?if($skills_item->checked == true){?></font><font size="2" color="#4a7b50">(recorded the : <?=$skills_item->date;?>)<?}?></font></p>*/?>
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
						<?if($this->ion_auth_acl->has_permission('create_skill')){?><input type="button" name="save" onClick="validator();" value="SAVE (pas fonctionnel!)" style="background-color: #303030" ><?}?>
					<?}?>
				</div><!-- /theme -->
			</div><!-- /content -->
		</div>
	</div><!-- /page -->