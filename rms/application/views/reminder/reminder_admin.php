			<a href="/reminder/admin/1/" class="ui-btn ui-btn-right" rel="external" data-ajax="false" data-icon="plus"><i class="zmdi zmdi-plus zmd-2x"></i></a>
		</div>
		<div data-role="content">
			<? 
			if($create) {
			?>
									<form id="task" name="task" method="post" action="/reminder/adminSave">
										<table width="100%" style="border: 1px solid #dedcd7; margin-top:10px" cellpadding="8">
											<tr><td colspan="2" style="background-color: #fbf19e;">Task info</td></tr>
											<tr>
												<td>
													<label for="name" id="label">Name:</label>
													<input id="name" type="text" name="task" value="">
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
											</tr>
										</table>

										<table width="100%" style="border: 1px solid #dedcd7; margin-top:10px" cellpadding="8">
											<tr><td colspan="2" style="background-color: #fbf19e;">Notifications</td></tr>
											<tr>
												<td>
													<label for="nstart" id="label">Start (what time should it start, time format HH:MN:SS):</label>
													<input id="nstart" type="text" name="nstart" value="">
												</td>
												<td>
													<label for="nend" id="label">End (what time should it end, time format HH:MN:SS):</label>
													<input id="nend" type="text" name="nend" value="">
												</td>
											</tr>

											<tr>
												<td>
													<label for="ninterval" id="label">Notification interval (in seconds):</label>
													<input id="ninterval" type="text" name="ninterval" value="">
												</td>
												<td>
													<label for="nlast" id="label">Last notification (datetime format YYYY-MM-DD HH:MN:SS): </label>
													<input id="nlast" type="text" name="nlast" value="">
												</td>
											</tr>
											<table width="100%" style="border: 1px solid #dedcd7; margin-top:10px" cellpadding="8">
												<tr><td colspan="2" style="background-color: #fbf19e;">Repeating</td></tr>
												<tr>
													<td>
														<label for="mstart" id="label">Start (datetime format YYYY-MM-DD HH:MN:SS):</label>
														<input id="mstart" type="text" name="mstart" value="">
													</td>
													<td>
														<label for="repeat_interval" id="label">Repeat interval (in seconds): <br /> 
															86400=1 day, 1296000 = 15 days, 2592000 = 30 days, 3888000 = 45 days</label>
														<input id="repeat_interval" type="text" name="repeat_interval" value="">
													</td>
												</tr>

												<tr>
													<td>
														<label for="repeat_year" id="label">Repeat year (YYYY or * or null):</label>
														<input id="repeat_year" type="text" name="repeat_year" value="">
													</td>
													<td>
														<label for="repeat_month" id="label">Repeat month (MM or * or null):</label>
														<input id="repeat_month" type="text" name="repeat_month" value="">
													</td>
												</tr>

												<tr>
													<td>
														<label for="repeat_day" id="label">Repeat day (DD or * or null):</label>
														<input id="repeat_day" type="text" name="repeat_day" value="">
													</td>
													<td>
														<label for="repeat_week" id="label">Repeat week (WW or * or null):</label>
														<input id="repeat_week" type="text" name="repeat_week" value="">
													</td>
												</tr>

												<tr>
													<td>
														<label for="repeat_weekday" id="label">Repeat weekday (WD or * or null):</label>
														<input id="repeat_weekday" type="text" name="repeat_weekday" value="">
													</td>
													<td>
														About repeat year, month, day, week, weekday : put either a repeat interval and repeat_* = null <br />
														OR no repeat interval and a value on one or more repeat_*. <br />
														Ex: a daily task : repeat interval = 0, repeat_* = *. <br />
														Every 10 days tasks : repeat interval =  864000, repeat_* = null.
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
																//OK
														    }).fail(function(data) {
														    	alert('WARNING! ERROR at saving!');
														    });
													}
													return false;
												});
											});

											</script>
											
				
			<? }
			
			
			if(!$create) { 
			foreach ($tasks as $line) {	?>
				<div data-role="collapsible">
					<h2>ID: <?=$line->tid?> | <?=$line->ttask?></h2>
					<ul data-role="listview" data-theme="d" data-divider-theme="d">
						<li>
							<form id="task<?=$line->tid?>" name="task<?=$line->tid?>" method="post" action="/reminder/adminSave">
								<table width="100%" style="border: 1px solid #dedcd7; margin-top:10px" cellpadding="8">
									<tr><td colspan="2" style="background-color: #fbf19e;">Task info</td></tr>
									<tr>
										<td>
											<label for="name-<?=$line->tid?>" id="label-<?=$line->tid?>">Name:</label>
											<input id="name-<?=$line->tid?>" type="text" name="task" value="<?=stripslashes($line->ttask)?>">
										</td>
										<td>
											<label for="comment-<?=$line->tid?>" id="label-<?=$line->tid?>">Comment:</label>
<textarea id="comment-<?=$line->tid?>" name="comment" rows="5" cols="20">
<?=stripslashes($line->tcomment)?>
</textarea>
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
									</tr>
								</table>

								<table width="100%" style="border: 1px solid #dedcd7; margin-top:10px" cellpadding="8">
									<tr><td colspan="2" style="background-color: #fbf19e;">Notifications</td></tr>
									<tr>
										<td>
											<label for="nstart-<?=$line->tid?>" id="label-<?=$line->tid?>">Start (what time should it start, time format HH:MN:SS):</label>
											<input id="nstart-<?=$line->tid?>" type="text" name="nstart" value="<?=$line->nstart?>">
										</td>
										<td>
											<label for="nend-<?=$line->tid?>" id="label-<?=$line->tid?>">End (what time should it end, time format HH:MN:SS):</label>
											<input id="nend-<?=$line->tid?>" type="text" name="nend" value="<?=$line->nend?>">
										</td>
									</tr>

									<tr>
										<td>
											<label for="ninterval-<?=$line->tid?>" id="label-<?=$line->tid?>">Notification interval (in seconds):</label>
											<input id="ninterval-<?=$line->tid?>" type="text" name="ninterval" value="<?=$line->ninterval?>">
										</td>
										<td>
											<label for="nlast-<?=$line->tid?>" id="label-<?=$line->tid?>">Last notification (datetime format YYYY-MM-DD HH:MN:SS): </label>
											<input id="nlast-<?=$line->tid?>" type="text" name="nlast" value="<?=$line->nlast?>">
										</td>
									</tr>
									<table width="100%" style="border: 1px solid #dedcd7; margin-top:10px" cellpadding="8">
										<tr><td colspan="2" style="background-color: #fbf19e;">Repeating</td></tr>
										<tr>
											<td>
												<label for="mstart-<?=$line->tid?>" id="label-<?=$line->tid?>">Start (datetime format YYYY-MM-DD HH:MN:SS):</label>
												<input id="mstart-<?=$line->tid?>" type="text" name="mstart" value="<?=$line->mstart?>">
											</td>
											<td>
												<label for="repeat_interval-<?=$line->tid?>" id="label-<?=$line->tid?>">Repeat interval (in seconds): <br /> 
													86400=1 day, 1296000 = 15 days, 2592000 = 30 days, 3888000 = 45 days</label>
												<input id="repeat_interval-<?=$line->tid?>" type="text" name="repeat_interval" value="<?=$line->repeat_interval?>">
											</td>
										</tr>

										<tr>
											<td>
												<label for="repeat_year-<?=$line->tid?>" id="label-<?=$line->tid?>">Repeat year (YYYY or * or null):</label>
												<input id="repeat_year-<?=$line->tid?>" type="text" name="repeat_year" value="<?=$line->repeat_year?>">
											</td>
											<td>
												<label for="repeat_month-<?=$line->tid?>" id="label-<?=$line->tid?>">Repeat month (MM or * or null):</label>
												<input id="repeat_month-<?=$line->tid?>" type="text" name="repeat_month" value="<?=$line->repeat_month?>">
											</td>
										</tr>

										<tr>
											<td>
												<label for="repeat_day-<?=$line->tid?>" id="label-<?=$line->tid?>">Repeat day (DD or * or null):</label>
												<input id="repeat_day-<?=$line->tid?>" type="text" name="repeat_day" value="<?=$line->repeat_day?>">
											</td>
											<td>
												<label for="repeat_week-<?=$line->tid?>" id="label-<?=$line->tid?>">Repeat week (WW or * or null):</label>
												<input id="repeat_week-<?=$line->tid?>" type="text" name="repeat_week" value="<?=$line->repeat_week?>">
											</td>
										</tr>

										<tr>
											<td>
												<label for="repeat_weekday-<?=$line->tid?>" id="label-<?=$line->tid?>">Repeat weekday (WD or * or null):</label>
												<input id="repeat_weekday-<?=$line->tid?>" type="text" name="repeat_weekday" value="<?=$line->repeat_weekday?>">
											</td>
											<td>
												About repeat year, month, day, week, weekday : put either a repeat interval and repeat_* = null <br />
												OR no repeat interval and a value on one or more repeat_*. <br />
												Ex: a daily task : repeat interval = 0, repeat_* = *. <br />
												Every 10 days tasks : repeat interval =  864000, repeat_* = null.
											</td>
										</tr>

									</table>
									<input type="hidden" name="id" value="<?=$line->tid?>">
									<input type="submit" id="sub<?=$line->tid?>" name="submit" value="Save">
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
					<? } } ?>
				</div><!-- /content -->
			</div><!-- /page -->