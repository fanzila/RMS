		</div>
<?
$today = getdate();
$service = "$today[weekday] $today[mday] $today[month] $today[hours]:$today[minutes] - $checklists_name"; 
?>
<style>
	ul {
  list-style-image: none;
  list-style: none;
  list-style-type: none;
}
</style>
		<div data-role="content">
			<div data-theme="a" data-form="ui-body-a" class="ui-body ui-body-a ui-corner-all">			
				<? if($load <= 0) { ?>Service: <?=$service?> <? } ?>
				<form id="tasks" name="tasks" method="post" action="/checklist/">

					<ul data-role="">
							<?
							foreach ($checklists as $line) {
								
								$continue = false;
								
								if(!empty($line['day_week_num']) AND $load == null) {
									$dw = explode(',',$line['day_week_num']);
									if(!in_array(trim($today['wday']), $dw)) {
										//echo 'DAY WEEK: ('.$today['wday'].')  '.$line['day_week_num'];
										$continue = true;
									}
								}
								
								if(!empty($line['day_month_num']) AND $load == null) {
									$dm = explode(',',$line['day_month_num']);
									if(in_array(trim($today['mday']), $dm)) {
										//echo '<br>DAY MONTH: ('.$today['mday'].') '.$line['day_month_num'].'<br><br>';
										$continue = false;
									}
								}
								
								if($continue) continue;
								
								$bkg_color = '';
								$comment = 'comment-'.$line['id'];
									if($load == null OR ($load > 0 || $line['id'] > 0)) {
								if($line['priority'] == 3) $bkg_color = "#ffcabf";
								if($line['priority'] == 2) $bkg_color = "#ffdfa6";
								if($line['priority'] == 1) $bkg_color = "#e0e0e0";
								?>
								
								<li>
									<input type="checkbox" name="<?=$line['id']?>" id="task-<?=$line['id']?>" class="custom" <?if(isset($form[$line['id']])) { ?>checked<? } ?> />
									<label style="background-color: <?=$bkg_color?>" for="task-<?=$line['id']?>" id="label-<?=$line['id']?>"><?=nl2br($line['name'])?> <br /> <i><span style="font-size:smaller"><?=nl2br($line['comment'])?></span></i></label>

									<div data-role="fieldcontain">
										<label style="font-size:smaller" for="comment-<?=$line['id']?>">Comments</label>
										<input data-inline="true" data-theme="a" class="input" data-form="ui-body-a" type="text" id="comment-<?=$line['id']?>"
										name="comment-<?=$line['id']?>" value="<? if(isset($comment) && isset($form['comment-'.$line['id']])) echo nl2br($form['comment-'.$line['id']]); ?>"  data-clear-btn="true" />
									</div>
								</li>
								<? } } ?>
									<select style="background-color:#a1ff7c" name="user" id="user" data-inline="true" data-theme="a" required>
										<option value="0">User</option>
										<?
										foreach ($users as $user) {
											?>
											<option value="<?=$user->id?>" <? if(isset($form['user']) AND $form['user']==$user->id) { ?> selected <? } ?>><?=$user->first_name?> <?=$user->last_name?></option>
											<? 
										}
										?>
									</select><font size="14" color='red'><b><div class="error_cont"></div></b></font>
								<input type="button" name="save" onClick="validator();" value="SAVE">
							</ul>
							<input type="hidden" id="id_checklist" name="id_checklist" value="<?=$checklists_id?>">
							<input type="hidden" name="action" value="save_tasks">
							<input type="hidden" name="checklist_name" value="<?=$checklists_name?>">
							<input type="hidden" name="checklist_rec_id" value="<?=$checklist_rec_id?>">
						</form>
						<a href="#top" class="back-to-top">Back to top</a>
					</div><!-- /theme -->
				</div><!-- /content -->
			</div><!-- /page -->