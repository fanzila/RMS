			<a href="/reminder/admin/1/" class="ui-btn ui-btn-right" rel="external" data-ajax="false" data-icon="plus"><i class="zmdi zmdi-plus zmd-2x"></i></a>
		</div>
		<div data-role="content">
			<? 
			if($create) {
			?>
									<form id="task" name="task" method="post" action="/reminder/adminSave">
										<table width="100%" style="border: 1px solid #dedcd7; margin-top:10px" cellpadding="8">
											<tr><td colspan="3" style="background-color: #fbf19e;">Task info</td></tr>
											<tr>
												<td>
													<label for="name" id="label">Name:</label>
													<input id="name" type="text" name="task" value="" data-clear-btn="true" />
												</td>
												<td>
													<label for="comment" id="label">Comment:</label>
<textarea id="comment" name="comment" rows="5" cols="20">
</textarea>
												</td>
											</tr>

											<tr>
												<td>
													<label for="active" id="label">Active: (on or off)</label>
													<select id="active" name="active">
														<option value="1">Yes</option>
														<option value="0">No</option>
													</select>
												</td>
												<td>
													<label for="priority" id="label">Priority:</label>
													<select id="priority" name="priority">
														<option value="2">Normal</option>
														<option value="1">Low</option>
														<option value="3">High</option>
													</select>
												</td>
												<td>
													<label for="type" id="type">Type:</label>
													<select id="type" name="type">
														<option value="service" selected>Service</option>
														<option value="kitchen">Kitchen</option>
													</select>
												</td>
											</tr>
										</table>

										<table width="100%" style="border: 1px solid #dedcd7; margin-top:10px" cellpadding="8">
											<tr><td colspan="2" style="background-color: #fbf19e;">Notifications</td></tr>
											<tr>
												<td>
													<label for="nstart" id="label">Start (what time should it start, time format HH:MN:SS):</label>
													<input id="nstart" type="text" name="nstart" value="" data-clear-btn="true" />
												</td>
												<td>
													<label for="nend" id="label">End (what time should it end, time format HH:MN:SS):</label>
													<input id="nend" type="text" name="nend" value="" data-clear-btn="true" />
												</td>
											</tr>

											<tr>
												<td>
													<label for="ninterval" id="label">Notification interval (in seconds):</label>
													<input id="ninterval" type="text" name="ninterval" value="" data-clear-btn="true" />
												</td>
												<td>
													<label for="nlast" id="label">Last notification (datetime format YYYY-MM-DD HH:MN:SS): </label>
													<input id="nlast" type="text" name="nlast" value="" data-clear-btn="true" />
												</td>
											</tr>
											<table width="100%" style="border: 1px solid #dedcd7; margin-top:10px" cellpadding="8">
												<tr><td colspan="2" style="background-color: #fbf19e;">Repeating</td></tr>
												<tr>
													<td>
														<label for="mstart" id="label">Start: <br /> (datetime format YYYY-MM-DD HH:MN:SS)</label>
														<input id="mstart" type="text" name="mstart" value="" data-clear-btn="true" />
													</td>
													<td>
														<label for="repeat_interval" id="label">Repeat interval (in seconds): <br /> 
															86400=1 day, 1296000 = 15 days, 2592000 = 30 days, 3888000 = 45 days</label>
														<input id="repeat_interval" type="text" name="repeat_interval" value="" data-clear-btn="true" />
													</td>
												</tr>
											</table>
											<input type="hidden" name="id" value="create">
											<input type="submit" id="sub" name="submit" value="Save">
										</form>			

											<script>
											$(document).ready(function() {

												var $form = $('#task');

												$('#sub').on('click', function() {
													$form.trigger('submit');
													return false;
												});

												$form.on('submit', function() {

													var name = $('#name').val();

													if(name == '') {
														alert('Please fill task name.');
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
																window.location = "/reminder/admin/";
														    }).fail(function(data) {
														    	alert('WARNING! ERROR at saving!');
														    });
													}
													return false;
												});
											});

											</script>
											
				
			<? }
			
			
		if(!$create) { ?>
			<div data-role="collapsible-set">
				<? foreach ($tasks as $line) {	
					if($line->ttype == 'service') $bgstyle=" style='background-color: #eceeff;';"; 
					if($line->ttype == 'kitchen') $bgstyle=" style='background-color: #ffe8d1;';"; 
					if($line->tactive == 0) $bgstyle=" style='background-color: #bbbdbd;';"; 
					?>
						<div data-role="collapsible"<?=$bgstyle?>>
							<h2>ID: <?=$line->tid?> | <?=$line->ttask?> | <small><?=$line->ttype?></small></h2>
							<ul data-role="listview" data-theme="d" data-divider-theme="d">
								<li>
									<form id="task<?=$line->tid?>" name="task<?=$line->tid?>" method="post" action="/reminder/adminSave">
										<table width="100%" style="border: 1px solid #dedcd7; margin-top:10px" cellpadding="8">
											<tr><td colspan="3" style="background-color: #fbf19e;">Task info</td></tr>
											<tr>
												<td>
													<label for="name-<?=$line->tid?>" id="label-<?=$line->tid?>">Name:</label>
													<input id="name-<?=$line->tid?>" type="text" name="task" value="<?=stripslashes($line->ttask)?>" data-clear-btn="true" />
												</td>
												<td>
													<label for="comment-<?=$line->tid?>" id="label-<?=$line->tid?>">Comment:</label>
													<textarea id="comment-<?=$line->tid?>" name="comment" rows="5" cols="20"><?=stripslashes($line->tcomment)?></textarea>
												</td>
											</tr>
											<tr>
												<td>
													<label for="active-<?=$line->tid?>" id="label-<?=$line->tid?>">Active: (on or off)</label>
													<select id="active-<?=$line->tid?>" name="active">
														<option value="1" <? if($line->tactive == 1) echo "selected"; ?> >Yes</option>
														<option value="0" <? if($line->tactive == 0) echo "selected"; ?> >No</option>
													</select>
												</td>
												<td>
													<label for="priority-<?=$line->tid?>" id="label-<?=$line->tid?>">Priority:</label>
													<select id="priority-<?=$line->tid?>" name="priority">
														<option value="2" <? if($line->tpriority == 2) echo "selected"; ?> >Normal</option>
														<option value="1" <? if($line->tpriority == 1) echo "selected"; ?>>Low</option>
														<option value="3" <? if($line->tpriority == 3) echo "selected"; ?>>High</option>
													</select>
												</td>
												<td>
													<label for="type-<?=$line->tid?>" id="label-<?=$line->tid?>">Type:</label>
													<select id="type-<?=$line->tid?>" name="type">
														<option value="service" <? if($line->ttype == 'service') echo 'selected'; ?> >Service</option>
														<option value="kitchen" <? if($line->ttype == 'kitchen') echo 'selected'; ?>>Kitchen</option>
													</select>
												</td>
											</tr>
										</table>
										<table width="100%" style="border: 1px solid #dedcd7; margin-top:10px" cellpadding="8">
											<tr><td colspan="2" style="background-color: #fbf19e;">Notifications</td></tr>
											<tr>
												<td>
													<label for="nstart-<?=$line->tid?>" id="label-<?=$line->tid?>">Start (what time should it start, time format HH:MN:SS):</label>
													<input id="nstart-<?=$line->tid?>" type="text" name="nstart" value="<?=$line->nstart?>" data-clear-btn="true" />
												</td>
												<td>
													<label for="nend-<?=$line->tid?>" id="label-<?=$line->tid?>">End (what time should it end, time format HH:MN:SS):</label>
													<input id="nend-<?=$line->tid?>" type="text" name="nend" value="<?=$line->nend?>" data-clear-btn="true" />
												</td>
											</tr>
											<tr>
												<td>
													<label for="ninterval-<?=$line->tid?>" id="label-<?=$line->tid?>">Notification interval (in seconds):</label>
													<input id="ninterval-<?=$line->tid?>" type="text" name="ninterval" value="<?=$line->ninterval?>" data-clear-btn="true" />
												</td>
												<td>
													<label for="nlast-<?=$line->tid?>" id="label-<?=$line->tid?>">Last notification (datetime format YYYY-MM-DD HH:MN:SS): </label>
													<input id="nlast-<?=$line->tid?>" type="text" name="nlast" value="<?=$line->nlast?>" data-clear-btn="true" />
												</td>
											</tr>
											<table width="100%" style="border: 1px solid #dedcd7; margin-top:10px" cellpadding="8">
												<tr><td colspan="2" style="background-color: #fbf19e;">Repeating</td></tr>
												<tr>
													<td>
														<label for="mstart-<?=$line->tid?>" id="label-<?=$line->tid?>">Start: <br /> (datetime format YYYY-MM-DD HH:MN:SS)</label>
														<input id="mstart-<?=$line->tid?>" type="text" name="mstart" value="<?=$line->mstart?>" data-clear-btn="true" />
													</td>
													<td>
														<label for="repeat_interval-<?=$line->tid?>" id="label-<?=$line->tid?>">Repeat interval (in seconds): <br /> 
															86400=1 day, 1296000 = 15 days, 2592000 = 30 days, 3888000 = 45 days</label>
														<input id="repeat_interval-<?=$line->tid?>" type="text" name="repeat_interval" value="<?=$line->repeat_interval?>" data-clear-btn="true" />
													</td>
												</tr>
											</table>
											
											<input type="hidden" name="id" value="<?=$line->tid?>">
											<input type="submit" id="sub<?=$line->tid?>" name="submit" value="Save">
										</table>
									</form>
									<script>
									$(document).ready(function() {

										var $form = $('#task<?=$line->tid?>');

										$('#sub<?=$line->tid?>').on('click', function() {
											$form.trigger('submit');
											return false;
										});

										$form.on('submit', function() {
											
											var name = $('#name-<?=$line->tid?>').val();

											if(name == '') {
												alert('Please fill task name.');
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
								</li>
							</ul>
						</div>
					<? } ?>
				</div>
			<? } ?>
		</div><!-- /content -->
	</div><!-- /page -->