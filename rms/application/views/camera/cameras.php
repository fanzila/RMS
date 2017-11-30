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
	$bgcolor = '#f7bf35';
	if($info_bu->id == 2) $bgcolor = '#e15849'; 
	?>
	<div style="width:99%; background-color: <?=$bgcolor?>; padding:6px; margin: 0 auto 5px; font: 17px 'Lucida Grande', Lucida, Verdana, sans-serif; font-weight: bold;"><a href="/"><?=$info_bu->name?></a>] | ARCH: <?=number_format($ca[1]['amount']/1000, 0, ',', ' ')?>€  <small><?=$ca[1]['last']?></small> | GRAV: <?=number_format($ca[2]['amount']/1000, 0, ',', ' ')?>€ <small><?=$ca[2]['last']?></small> - <small><a href="/cameras/index/onebu/1">Current BU view only</a></small></div>
	<!-- <div class="res-choose">
		<div>
			<label for="compression-range">Compression</label>
			<input type="range" min="10" max="100" step="5" list="tickmarks" name="compression-range" id="compression-range">
			
		</div>
		<div class="">
			<input
		</div>
	</div> -->
	<?
	if ($this->ion_auth->in_group('Admin')) {?>
		<div class="chooseBu" data-role="collapsible">
			<h3>Choose Bus</h3>
			<?foreach ($all_bus as $bu) { ?>
					<div class="bu">
						<input class="checkBu" type="checkbox" value="<?=$bu['id']?>" name="bu_<?=$bu['id']?>" id="bu_<?=$bu['id']?>" onchange="chooseCamBu()" <?if ($info_bu->id === $bu['id']) echo "checked"?>>
						<label for='bu_<?=$bu['id']?>'><?=$bu['name']?></label> 
					</div>
			<? } ?>
		</div>
	<? } ?>
	<?foreach ($cameras as $camera) { ?>
		<img class="camera" src="/cameras/getStream/<?=$camera['name']?>" alt="<?=$camera['name']?>" <?if ($this->ion_auth->in_group('Admin')) echo "data-id-bu=" . $camera['id_bu']; ?> <?if ($camera['id_bu'] != $info_bu->id) echo "hidden='true'";?> />
	<? } ?>
<?
$iname = 1;
foreach($bu_postion_id AS $bupid) {

	$avatars_url = 'https://s3.amazonaws.com/uf.shiftplanning.com/';
	$p = $planning['data'];
	$i=false;
	if($p) {
		?>
		<div class="row">
			<div class="col-md" style="margin: 3px;">
				<div class="box">
					<b>Who's on now @<?=$buname[$iname]?>?</b>
				</div>
			</div>
		</div>		
		<?			
	foreach($p AS $r) {
		if(!empty($r['employee_avatar_url'])) {
			$av_json_decode = json_decode($r['employee_avatar_url']);
			$avatar = $av_json_decode->small;
		}

		$pos = in_array ($r['schedule_id'], $bupid);	
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
	$iname++;
}
?>
<?if ($this->ion_auth->in_group('Admin')) {?>
	<script>
		
		function chooseCamBu()
		{
			var ids_bu = new Array();
			$('.checkBu').each(function(index) {
				if ($(this).is(':checked')) {
					ids_bu.push($(this).attr('value'));
				}
			});
			$('.camera').each(function(index){
				if ($.inArray($(this).attr('data-id-bu'), ids_bu) != -1) {
					$(this).removeAttr("hidden");
				} else {
					$(this).attr("hidden", "true");
				}
			});
		}
	</script>
<? } ?>
</body>
</html>