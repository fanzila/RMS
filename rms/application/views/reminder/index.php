<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>HANK - Reminder</title>
	<meta http-equiv="X-UA-Compatible" content="IE=edge" />
	<link rel="stylesheet" href="/public/jqm/themes/hmw.min.css" />
	<link rel="stylesheet" href="/public/jqm/themes/jquery.mobile.icons.min.css" />
	<link rel="stylesheet" href="/public/jqm/jquery.mobile.structure-1.4.5.min.css" />
</head>
<body>
	<div data-role="page">
		<div data-role="header">
			<? if(!$keylogin) { ?><a href="/admin/" data-ajax="false" data-icon="home">Home</a><? } ?>
			<h1>Reminder</h1>
		</div>

		<?if($msg) { ?>
			<div data-role="content">
				<div data-theme="a" data-form="ui-body-a" class="ui-body ui-body-a ui-corner-all">
				<?=$msg?> - Thanks! Have A Nice Karma!"
				<br />
				<input type="button" rel="external" data-ajax="false" data-inline="true" data-theme="a" name="back" onClick="javascript:location.href='/reminder/'" value="Back">
			</div>
		</div>
		<? } ?>
		<? if(empty($msg)) { ?>
		<div data-role="content">
			<div data-theme="a" data-form="ui-body-a" class="ui-body ui-body-a ui-corner-all">					
				<form id="tasks" name="tasks" method="post" action="/reminder/">
					<ul>
						<li style="list-style-type: none;">
							<? if($view != 'all') { ?><input type="button" rel="external" data-inline="true" data-theme="a" data-ajax="false" name="view" onClick="javascript:location.href='/reminder/index/view/all'" value="All tasks"><? } ?>
						<? if($view == 'all') { ?><input type="button" rel="external" data-ajax="false" data-inline="true" data-theme="a" name="view" onClick="javascript:location.href='/reminder/'" value="Overdue tasks"><? } ?><input type="button" rel="external" data-ajax="false" data-inline="true" data-theme="a" name="view" onClick="javascript:location.href='/reminder/log'" value="Log"></li>	
						<?
					if(empty($tasks)) { ?>
					<br />Great! Nothing is to be done!<br /><br /> 	
					<?
					}
					foreach ($tasks as $line) {	
						$bkg_color	= '';
						$font_color = ''; 

						if($line->priority == 3) $bkg_color = "#ffcabf";
						if($line->priority == 2) $bkg_color = "#ffdfa6";
						
						$overdue = null;
						if(($line->overdue > 0) && empty($line->repeat_year) && empty($line->repeat_month) && empty($line->repeat_day) && empty($line->repeat_week) && empty($line->repeat_weekday)) {
							$overdue = "overdue: $line->overdue day(s)";
							$font_color = "#ff5b50";
						}
						
						if(($line->overdue < 0) && empty($line->repeat_year) && empty($line->repeat_month) && empty($line->repeat_day) && empty($line->repeat_week) && empty($line->repeat_weekday)) {
							$overdue = "due in: ".abs($line->overdue)." day(s)";
							$font_color = "#4a7b50";
						}
						
						?>
						<li>
							<input type="checkbox" name="task_<?=$line->id?>" id="task-<?=$line->id?>" class="custom" />
							<label style="background-color: <?=$bkg_color?>" for="task-<?=$line->id?>" id="label-<?=$line->id?>"> <?=$line->task?> &nbsp;&nbsp;&nbsp;&nbsp;<font size="2" color="<?=$font_color?>"><i><?=$overdue?></i></font><? if(!empty($line->comment)) { echo "<font style='font-size:smaller'><i><br />".nl2br($line->comment)."</i></font>"; } ?></label>

						</li>
						<? } ?>
						<li style="list-style-type: none;">
						<select style="background-color:#a1ff7c" name="user" id="user" data-inline="true" data-theme="a" required>
							<option value="0">User</option>
							<?
						foreach ($users as $user) {
							?>
							<option value="<?=$user['id']?>" <? if(isset($form['user']) AND $form['user']==$user['id']) { ?> selected <? } ?>><?=$user['first_name']?> <?=$user['last_name']?></option>
							<? 
						}
						?>
					</select>
				</li>
						<li><input type="button" rel="external" data-ajax="false" name="save" onClick="validator();" value="SAVE"></li>
					</ul>
					<input type="hidden" name="action" value="save_tasks">
				</form>
			
			</div><!-- /theme -->
		</div><!-- /content -->
	<? } ?>
	</div><!-- /page -->
	<script src="/public/jquery-1.11.3.min.js" type="text/javascript"></script>
	<script src="/public/jqm/jquery.mobile-1.4.5.min.js" type="text/javascript"></script>
	<script src="/public/jqv/dist/jquery.validate.min.js" type="text/javascript"></script>
	<script src="/public/rmd.js" type="text/javascript"></script>
</body>
</html>