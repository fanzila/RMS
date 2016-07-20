<body>
	<div data-role="page">
		<?php include('adminpanel.html'); ?>

		<div data-role="header" data-position="fixed" class="wow fadeIn">
			<? if(!$keylogin) { ?><a href="#adminpanel" class="ui-btn ui-btn-left wow fadeIn" data-wow-delay='0.8s'><i class="zmdi zmdi-menu"></i></a><?}?>
			<a href="/discount/creation/1/" class="ui-btn ui-btn-right" data-icon="plus"><i class="zmdi zmdi-plus zmd-2x"></i></a>
			<h1 class="wow fadeIn" data-wow-delay='0.4s'><?=$title?> | <?=$bu_name?> | <?=$username?></h1>
		</div>

		<div role="main" class="ui-content wow fadeIn" data-inset="false" data-wow-delay="0.2s">

			<div data-role="content">
				<div data-theme="a" data-form="ui-body-a" class="ui-body ui-body-a ui-corner-all">
					
					<div class="row">
						<a href="/discount/log/" class="ui-btn ui-btn-raised">Log</a>
					</div>
					<br/>
					<?if($msg) { ?>
						<div style="background-color: #d6f0d6;" class="ui-body ui-body-a">	
							<?=$msg?> Thanks! Have A Nice Karma!"
						</div>
					<?}?>
					<? if(empty($discount)) { ?>
						<br />Great! No discount today!<br /><br /> 	
					<? } else { ?>
						<div data-role="collapsible-set" data-inset="false" data-filter="true" data-filter-placeholder="Filter discounts">
							<?foreach ($discount as $line) {
								if($line->tused == false){
									$bkg_color	= '';
									$font_color = "#4a7b50";?>
									<div data-role="collapsible">
										<h4><i class="zmdi zmdi-account"></i> <?=$line->tclient?>   <i class="zmdi zmdi-money-off"></i> <?=$line->tnature?>   <font size="2" color="<?=$font_color?>"><i class="zmdi zmdi-alarm-check"> <?=date($line->tdate);?></i></font></h4>
										<form id="discount<?=$line->tid?>" name="discount<?=$line->tid?>" method="post" action="/discount/save">
											<label for="client-<?=$line->tid?>" id="label">Client:</label>
											<input id="client-<?=$line->tid?>" type="text" name="client" value="<?=stripslashes($line->tclient)?>">
											
											<label for="reason-<?=$line->tid?>" id="label">Reason:</label>
											<input id="reason-<?=$line->tid?>" type="text" name="reason" value="<?=stripslashes($line->treason)?>">

											<label for="nature-<?=$line->tid?>" id="label">Nature:</label>
											<input id="nature-<?=$line->tid?>" type="text" name="nature" value="<?=stripslashes($line->tnature)?>">

											<select style="background-color:#a1ff7c" id="used-<?=$line->tid?>" name="used" data-inline="true" data-theme="a" required>
												<option value="0">Utiliser : NON</option>
												<option value="1" selected>Utiliser : OUI</option>
											</select>

											<select style="background-color:#a1ff7c" name="user" id="user-<?=$line->tid?>" data-inline="true" data-theme="a" required>
												<option value="0">User</option>
												<?foreach ($users as $user) {?>
													<option value="<?=$user->id?>" <? if(isset($form['user']) AND $form['user']==$user->id) { ?> selected <? } ?>><?=$user->first_name?> <?=$user->last_name?>
													</option>
												<?}?>
											</select>

											<input type="hidden" name="id" value="<?=$line->tid?>">
											<?$attributes = array('id' => "sub=".$line->tid, 'name' => "submit");
											echo form_submit($attributes, 'Save');?>
										</form>
										<script>
											$(document).ready(function() {

												var $form = $('#discount<?=$line->tid?>');

												$('#sub<?=$line->tid?>').on('click', function() {
													$form.trigger('submit');
													return false;
												});

												$form.on('submit', function() {

													var nature = $('#nature-<?=$line->tid?>').val();
													var client = $('#client-<?=$line->tid?>').val();
													var user = $('#user-<?=$line->tid?>').val();
													var used = $('#used-<?=$line->tid?>').val();
													var reason = $('#reason-<?=$line->tid?>').val();

													if(nature == '') {
														alert('Please fill discount nature.');
													} else if(client == ''){
														alert('Please fill discount client.');
													} else if(reason == ''){
														alert('Please fill discount reason.');
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

									</div><!-- /collapsible -->
								<?}
							}?>
						</div><!-- /collapsible-set -->
					<?}?>
				</div><!-- /theme -->
			</div><!-- /content -->
		</div>
	</div><!-- /page -->