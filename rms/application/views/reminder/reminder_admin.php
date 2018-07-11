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

												<tr><td style="background-color: #fbf19e;">Repeating</td></tr>
												<tr>
													<td>
                            <label for="repeat_interval">Repeat interval (in seconds):</label><br/>
                            <select required name="repeat_interval">
                              <option value=""></option>
                              <option value="86400">1 day</option>
                              <option value="172800">2 days</option>
                              <option value="259200">3 days</option>
                              <option value="345600">4 days</option>
                              <option value="432000">5 days</option>
                              <option value="518400">6 days</option>
                              <option value="604800">1 week</option>
                              <option value="1209600">2 weeks</option>
                              <option value="2592000">30 days</option>
                              <option value="3888000">45 days</option>
                              <option value="7776000">90 days</option>
                              <option value="31536000">1 year</option>
                            </select>
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
                      <tr><td style="background-color: #fbf19e;">Repeating</td></tr>
                      <tr>
                        <td>
                          <label for="repeat_interval-<?=$line->tid?>" id="label-<?=$line->tid?>">Repeat interval (in seconds):</label><br/>
                          <select required name="repeat_interval">
                            <option value=""></option>
                            <option value="86400" <?php
                              if ($line->repeat_interval == '86400') echo 'selected';
                              ?>>1 day</option>
                            <option value="172800" <?php
                              if ($line->repeat_interval == '172800') echo 'selected';
                              ?>>2 days</option>
                            <option value="259200" <?php
                              if ($line->repeat_interval == '259200') echo 'selected';
                              ?>>3 days</option>
                            <option value="345600" <?php
                              if ($line->repeat_interval == '345600') echo 'selected';
                              ?>>4 days</option>
                            <option value="432000" <?php
                              if ($line->repeat_interval == '432000') echo 'selected';
                              ?>>5 days</option>
                            <option value="518400" <?php
                              if ($line->repeat_interval == '518400') echo 'selected';
                              ?>>6 days</option>
                            <option value="604800" <?php
                              if ($line->repeat_interval == '604800') echo 'selected';
                              ?>>1 week</option>
                            <option value="1209600" <?php
                              if ($line->repeat_interval == '1209600') echo 'selected';
                              ?>>2 weeks</option>
                            <option value="2592000" <?php
                              if ($line->repeat_interval == '2592000') echo 'selected';
                              ?>>30 days</option>
                            <option value="3888000" <?php
                              if ($line->repeat_interval == '3888000') echo 'selected';
                              ?>>45 days</option>
                            <option value="7776000" <?php
                              if ($line->repeat_interval == '7776000') echo 'selected';
                              ?>>90 days</option>
                            <option value="31536000" <?php
                              if ($line->repeat_interval == '31536000') echo 'selected';
                              ?>>1 year</option>
                          </select>
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
