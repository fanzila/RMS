<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="msapplication-tap-highlight" content="no" />
	<meta http-equiv="cache-control" content="no-cache">
	<meta http-equiv="expires" content="-1">
	<meta http-equiv="pragma" content="no-cache">
	<META http-equiv="refresh" content="60"> 

		<link rel="stylesheet" href="/public/jqm/themes/hmw.min.css" />
		<link rel="stylesheet" href="/public/jqm/themes/jquery.mobile.icons.min.css" />
		<link rel="stylesheet" href="/public/jqm/jquery.mobile-1.4.5.min.css" />
		<link rel="stylesheet" href="/public/jqm/jquery.mobile.structure-1.4.5.min.css" />
		<link rel="stylesheet" href="/public/droid2/vendor/wow/animate.css" />
		<link rel="stylesheet" href="/public/droid2/vendor/waves/waves.min.css" />

		<link rel="stylesheet" href="/public/droid2/css/nativedroid2.css" />
		<!--A remplacer par une bu en local-->
		<link rel="stylesheet" href="/public/fontAwesome/4.6.3/css/font-awesome.min.css" />
		<link rel="stylesheet" href="/public/cameras.css" />
		<script type="text/javascript" src="/public/jquery-1.11.3.min.js"></script>
	</head>
	<body>
		<? 
	$bgcolor = '#FFF';
	$ca_amount = "0";
	$ca_last = "-";
	$total_ca = 0;
	$buname = array();
	foreach ($bus_list as $bu) { $buname[$bu->id] = $bu->name; }
	
	foreach ($ca as $caline) {
		if($caline['id_bu'] == $bu_id) {
			$ca_amount = $caline['amount'];
			$ca_last = $caline['last'];
		}
		
		$conca_ca[] = $buname[$caline['id_bu']].": ". number_format($caline['amount']/1000, 0, ',', ' ')."€ <small>- ".$caline['last']."</small><br />";
		$total_ca += $caline['amount'];
	}	
?>
	<div style="width:99%; background-color: <?=$bgcolor?>; padding:6px; margin: 0 auto 5px; font: 17px 'Lucida Grande', Lucida, Verdana, sans-serif; font-weight: bold;">
		<table><tr><td></td><td><form action="#" method="POST">
			<select name="bus" class="ui-btn" onchange="this.form.submit()">
			<? foreach ($bus_list as $bu) { ?>
  				<option value="<?=$bu->id?>" <? if($bu_id == $bu->id) echo "selected"; ?>><?=$bu->name?></option>
			<? } ?>
			</select>
		</form> </td><td> <a href="/">[BACK]</a> | TO: <?=number_format($ca_amount/1000, 0, ',', ' ')?>€ </td><td> | <small>Last ticket: <?=$ca_last?></small></td></tr></table></div>
		
	<? if(empty($cameras)) { ?>
		No cam.
		<? } else { 	
	foreach ($cameras as $camera) { ?>
		<img class="camera" src="/cameras/getStream/<?=$camera['name']?>" alt="<?=$camera['name']?>" />
	<? } } ?>
	
<p><b>TO by BUs</b><br />
<? 
foreach ($conca_ca as $line) { 
	echo $line; 
	}
?> 
<b>Total: <?=number_format($total_ca/1000, 0, ',', ' ')?>€</b></p>
<?
	$avatars_url = 'https://s3.amazonaws.com/uf.shiftplanning.com/';
	$p = $planning['data'];
	$i=false;
	if($p) {
		?>
		<div class="row">
			<div class="col-md" style="margin: 3px;">
				<div class="box">
					<b>Who's on now @<?=$info_bu->name?>?</b>
				</div>
			</div>
		</div>		
		<?			
	foreach($p AS $r) {
		if(!empty($r['employee_avatar_url'])) {
			$av_json_decode = json_decode($r['employee_avatar_url']);
			$avatar = $av_json_decode->small;
		}
		
		$pos = in_array ($r['schedule_id'], $bu_postion_id);	
		if($pos) {
			$i=true;
			?>

			<div class="row" style="background-color: #FFF; border: 1px solid silver; margin: 5px;">	
				<div class="col-md" style="margin: 3px;">
					<div class="box">
						<? if(!empty($r['employee_avatar_url'])) { ?><img src="<?=$avatars_url?><?=$avatar?>"><? } ?> <?=$r['employee_name'];?> <small>(<?=$r['schedule_name'];?>)</small> | <?=$r['shift_start']['time'];?> - <?=$r['shift_end']['time'];?>
					</div>
				</div>
			</div>
			<?
		}
	}
	if(!$i) { 
		?><p>No one.</p><? 
		}
	} 
?>
</body>
</html>