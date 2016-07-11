<!DOCTYPE html>
<html lang="en">
<head>
	<title>HANK - Reduction</title>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="msapplication-tap-highlight" content="no" />
	<link rel="stylesheet" href="/public/jqm/jquery.mobile-1.4.5.min.css" />
	<link rel="stylesheet" href="/public/jqm/themes/hmw.min.css" />
	<link rel="stylesheet" href="/public/jqm/themes/jquery.mobile.icons.min.css" />
	<link rel="stylesheet" href="/public/jqm/jquery.mobile.structure-1.4.5.min.css" />
	<script src="/public/jquery-1.11.3.min.js" type="text/javascript"></script>
</head>
<body>
	<div data-role="page">
		<div data-role="header">
			<a href="/admin/" data-ajax="false" data-icon="home">Home</a> <a href="/reduction/creation/1" data-ajax="false" data-icon="plus">Create</a>
			<h1>Reduction | <?=$bu_name?> | <?=$username?></h1>
		</div>
			<div data-role="content"><?
			if(!$create)?>
				<div data-theme="a" data-form="ui-body-a" class="ui-body ui-body-a ui-corner-all">
					<ul>
						<li style="list-style-type: none;">
							<? if($view != 'all') { ?><input type="button" rel="external" data-inline="true" data-theme="a" data-ajax="false" name="view" onClick="javascript:location.href='/reduction/index/view/all'" value="All reductions"><? } ?>
							<? if($view == 'all') { ?><input type="button" rel="external" data-ajax="false" data-inline="true" data-theme="a" name="view" onClick="javascript:location.href='/reduction/'" value="Today's reductions"><? } ?><input type="button" rel="external" data-ajax="false" data-inline="true" data-theme="a" name="view" onClick="javascript:location.href='/reduction/log'" value="Log">
						</li>
						<? if(empty($reduc)) { ?>
						<br />Great! No reduction today!<br /><br /> 	
						<? }else{ ?>
							<ul data-role="listview" data-inset="true" data-filter="true">
						<?}
						foreach ($reduc as $line) {
							$bkg_color	= '';
							$font_color = "#4a7b50";	?>
						<div data-role="collapsible">
							<h2><?=$line->tnature?> | <font size="2" color="<?=$font_color?>"><i>last modification : <?=date($line->tdate);?></i></h2>
							<ul data-role="listview" data-theme="d" data-divider-theme="d">
								<li>
									<!--?$attributes = array('id' => "reduc=".$line->tid, 'name' => "reduc=".$line->tid);
									echo form_open("reduction/save", $attributes);?-->
									<form id="reduc<?=$line->tid?>" name="reduc<?=$line->tid?>" method="post" action="/reduction/save">
										<table width="100%" style="border: 1px solid #dedcd7; margin-top:10px" cellpadding="8">
											<tr>
												<td colspan="2" style="background-color: #fbf19e;">
													Reduction Info :
												</td>
											</tr>
											<tr>
												<td>
													<label for="nature-<?=$line->tid?>" id="label-<?=$line->tid?>">Actual Nature : <?=stripslashes($line->tnature)?><br/>New Nature :</label>
													<input id="nature-<?=$line->tid?>" type="text" name="nature" value="">
												</td>
											</tr>
											<tr>
												<td>
												<select style="background-color:#a1ff7c" name="user" id="user-<?=$line->tid?>" data-inline="true" data-theme="a" required>
													<option value="0">User</option>
													<?foreach ($users as $user) {?>
														<option value="<?=$user->id?>" <? if(isset($form['user']) AND $form['user']==$user->id) { ?> selected <? } ?>><?=$user->first_name?> <?=$user->last_name?>
														</option>
													<? } ?>
												?></select>
												</td>
											</tr>
										</table>

										<input type="hidden" name="id" value="<?=$line->tid?>">
										<?$attributes = array('id' => "sub=".$line->tid, 'name' => "submit");
										echo form_submit($attributes, 'Save');?>

									</form>
									<script>
										$(document).ready(function() {

											var $form = $('#reduc<?=$line->tid?>');

											$('#sub<?=$line->tid?>').on('click', function() {
												$form.trigger('submit');
												return false;
											});

											$form.on('submit', function() {
												
												var nature = $('#nature-<?=$line->tid?>').val();
												var user = $('#user-<?=$line->tid?>').val();

												if(nature == '') {
													alert('Please fill reduction nature');
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
															//OK
													    }).fail(function(data) {
													    	alert('WARNING! ERROR at saving!');
													    });
												}
												return false;
											});
										});

									</script>

								</li>
							</ul>
						</div>
						<?} ?>
						</ul>
						</ul>
				</div><!-- /theme -->
		</div><!-- /content -->
	</div><!-- /page -->
	<script src="/public/jqm/jquery.mobile-1.4.5.min.js" type="text/javascript"></script>
	<script src="/public/jqv/dist/jquery.validate.min.js" type="text/javascript"></script>
	<script src="/public/reduc.js" type="text/javascript"></script>
</body>
</html>
