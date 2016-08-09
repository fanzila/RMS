<?//		<a href="/skills/log/" class="ui-btn ui-btn-right" rel="external" data-ajax="false" data-icon="plus"><i class="zmdi zmdi-more-vert"></i></a>?>
		</div>

		<div role="main" class="ui-content wow fadeIn" data-inset="false">

			<div data-role="content">
				<div class="ui-body ui-corner-all">
					<br/>
					<? if($skills_items!=null){ ?>
						<div data-inset="false" data-filter="true" data-filter-placeholder="Filter skills">
							<?foreach ($skills as $skill) {?><!--Affichage des skills générales-->
								<?$check=0;foreach($skills_items as $skills_item){
									if($skills_item->s_name == $skill->name) $check+=1;
									if($check == 1) break;
								}?>
								<?if($check==1){?>
									<div data-role="collapsible" data-collapsed="false">
										<h4><?=$skill->name?></h4>
										<div data-inset="true">
											<?foreach ($skills_categories as $category) {?><!--Affichage des Catégories-->
												<?$check=0;foreach($skills_items as $skills_item){
													if($skills_item->c_name == $category->name) $check+=1;
													if($check == 1) break;
												}?>
												<?if($check==1){?>
													<div data-role="collapsible" data-collapsed="false">
														<h4><?=$category->name?></h4>
														<div data-inset="true">
															<?foreach ($skills_sub_categories as $sub_category) {?><!--Affichage des sub-catégories-->
																<?$check=0;foreach($skills_items as $skills_item){
																	if($skills_item->sub_name == $sub_category->name) $check+=1;
																	if($check == 1) break;
																}?>
																<?if($check==1){?>
																	<div data-role="collapsible" data-collapsed="false">
																		<h4><?=$sub_category->name?></h4>
																		<div data-inset="true">
																			<?foreach($skills_items as $skills_item){?>
																				<?if($skills_item->sub_name == $sub_category->name){?>
																					<p><?if($skills_item->checked == true){?><i class="zmdi zmdi-check"></i><?}?> <?=$skills_item->i_name?></p>
																				<?}?>
																			<?}?>
																		</div><!-- /inset for items -->
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
					<?}?>
				</div><!-- /theme -->
			</div><!-- /content -->
		</div>
	</div><!-- /page -->