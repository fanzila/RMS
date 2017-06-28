		<link rel="stylesheet" href="/public/reminder.css" />
		</div>
		<div data-role="content">
			<div data-theme="a" data-form="ui-body-a" class="ui-body ui-body-a ui-corner-all">
				<?if($msg) { ?>
					<ul data-role="listview" data-inset="true" data-split-theme="a" data-divider-theme="a">
						<li style="background-color: #e8ffb9;">Thanks! Votre action a bien été enregistrée. [<?=$msg?>]</li>
					</ul>
					<? } ?>
					<form id="tasks" name="tasks" method="post" action="/reminder/">
						<ul id="ReminderUL">
							<li style="list-style-type: none;">
								<? if($view != 'all') { ?><input type="button" rel="external" data-inline="true" data-theme="a" data-ajax="false" name="view" onClick="javascript:location.href='/reminder/index/view/all'" value="All tasks"><? } ?>
								<? if($view == 'all') { ?><input type="button" rel="external" data-ajax="false" data-inline="true" data-theme="a" name="view" onClick="javascript:location.href='/reminder/'" value="Overdue tasks"><? } ?><input type="button" rel="external" data-ajax="false" data-inline="true" data-theme="a" name="view" onClick="javascript:location.href='/reminder/log'" value="Log"></li>	
								<?
								if(empty($tasks)) { ?>
									<br />Great! Nothing is to be done!<br /><br /> 	
									<?
							}
							$type = $this->session->userdata('type');
							foreach ($tasks as $line) {
								$bkg_color	= '';
								$font_color = ''; 
								if($line->priority == 3) $bkg_color = "#ffcabf";
								if($line->priority == 2) $bkg_color = "#ffdfa6";
								if($line->priority == 1) $bkg_color = "#e0e0e0";

								$overdue = null;
								if($line->overdue > 0) {
									$overdue = "overdue: $line->overdue day(s)";
									$font_color = "#ff5b50";
								}
								
								if($line->overdue < 0) {
									$overdue = "due in: ".abs($line->overdue)." day(s)";
									$font_color = "#4a7b50";
								}
								
								?>
									<?if ($type == false) { ?>
											<input type="checkbox" name="task_<?=$line->id?>" id="task-<?=$line->id?>" class="custom <?= $line->type ?>" />
											<label class="<?= $line->type ?>" style="background-color: <?=$bkg_color?>" for="task-<?=$line->id?>" id="label-<?=$line->id?>"> <?=$line->task?>  &nbsp;&nbsp;&nbsp;&nbsp;<font size="2" color="<?=$font_color?>"><i><?=$overdue?></i></font><? if(!empty($line->comment)) { echo "<font style='font-size:smaller'><i><br />".nl2br($line->comment)."</i></font>"; } ?> <?if ($line->interval > 0) { ?><font style='font-size:smaller'>| every <?=$line->interval/3600/24?> day(s)</font><? } ?></label>
									<?} else {?>
									<input type="checkbox" name="task_<?=$line->id?>" id="task-<?=$line->id?>" class="custom" />
									<label style="background-color: <?=$bkg_color?>" for="task-<?=$line->id?>" id="label-<?=$line->id?>"> <?=$line->task?>  &nbsp;&nbsp;&nbsp;&nbsp;<font size="2" color="<?=$font_color?>"><i><?=$overdue?></i></font><? if(!empty($line->comment)) { echo "<font style='font-size:smaller'><i><br />".nl2br($line->comment)."</i></font>"; } ?> <?if ($line->interval > 0) { ?><font style='font-size:smaller'>| every <?=$line->interval/3600/24?> day(s)</font><? } ?></label>
								<? 	}
									} ?>
									<select style="background-color:#a1ff7c" name="user" id="user" data-inline="true" data-theme="a" required>
										<option value="0">User</option>
										<?
									foreach ($users as $user) {
										?>
										<option value="<?=$user->id?>" <? if(isset($form['user']) AND $form['user']==$user->id) { ?> selected <? } ?>><?=$user->first_name?> <?=$user->last_name?></option>
										<? 
									}
									?>
									?>
								</select>
							<input type="button" rel="external" data-ajax="false" name="save" onClick="validator();" value="SAVE">
						</ul>
						<input type="hidden" name="action" value="save_tasks">
					</form>

				</div><!-- /theme -->
			</div><!-- /content -->
		</div><!-- /page -->
		<?if ($type == false) { ?>
		<script>
			var service = document.getElementsByClassName('service');
			var kitchen = document.getElementsByClassName('kitchen');
			var ul = document.getElementById('ReminderUL');
			var h3Service = document.createElement('h3');
			var h3Kitchen = document.createElement('h3');
			h3Service.innerHTML = 'Service Reminders :';
			h3Kitchen.innerHTML = 'Kitchen Reminders :';
			var serviceDiv = document.createElement('div');
			var kitchenDiv = document.createElement('div');
			var user = document.getElementById('user');
			
			
			serviceDiv.setAttribute('id', 'service-div');
			kitchenDiv.setAttribute('id', 'kitchen-div');
			ul.insertBefore(kitchenDiv, user);
			ul.insertBefore(serviceDiv, kitchenDiv);
			ul.insertBefore(h3Kitchen, kitchenDiv);
			ul.insertBefore(h3Service, serviceDiv);
			if (service.length > 0) {
				for (i = 0; i < service.length; i += 1) {
					serviceDiv.appendChild(service[i]);
				}
			} else {
				serviceDiv.innerHTML = 'No reminders';
			}
			if (kitchen.length > 0) {
				for (i = 0; i < kitchen.length; i += 1) {
					kitchenDiv.appendChild(kitchen[i]);
				}
			} else {
				kitchenDiv.innerHTML = 'No reminders';
			}
		</script>
		<? } ?>