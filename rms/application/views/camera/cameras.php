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
	<!--Doit pouvoir être modulé selon la Bu-->
	<link rel="stylesheet" href="/public/droid2/css/nativedroid2.css" />
	<!--A remplacer par une bu en local-->
	<link rel="stylesheet" href="/public/fontAwesome/4.6.3/css/font-awesome.min.css" />
	<script type="text/javascript" src="/public/jquery-1.11.3.min.js"></script>
</head>
<body>
	<div style="width:99%; background-color: #b8cb64; padding:6px; margin: 0 auto 5px; font: 17px 'Lucida Grande', Lucida, Verdana, sans-serif; font-weight: bold;">ARCH: <?=number_format($ca[1]['amount']/1000, 0, ',', ' ')?>€  <small><?=$ca[1]['last']?></small> | GRAV: <?=number_format($ca[2]['amount']/1000, 0, ',', ' ')?>€ <small><?=$ca[2]['last']?></small> - <small><a href="/cameras/index/onebu/1">Current BU view only</a></small></div>
<?if(isset($url['cam1'])) { ?>
	<iframe width="640" marginheight="0" marginwidth="0" height="360" scrolling="no" frameborder="0" src="/cameras/frame/1"></iframe>
<? } ?>
<?if(isset($url['cam2'])) { ?>
	<iframe width="640" marginheight="0" marginwidth="0" height="360" scrolling="no" frameborder="0" src="/cameras/frame/2"></iframe>	
<? } ?>
<?if(isset($url['cam3'])) { ?>
	<iframe width="640" marginheight="0" marginwidth="0" height="360" scrolling="no" frameborder="0" src="/cameras/frame/3"></iframe>		
<? } ?>
<?if(isset($url['cam4'])) { ?>
	<iframe width="640" marginheight="0" marginwidth="0" height="360" scrolling="no" frameborder="0" src="/cameras/frame/4"></iframe>			
<? } ?>
<?if(isset($url['cam5'])) { ?>
	<iframe width="640" marginheight="0" marginwidth="0" height="360" scrolling="no" frameborder="0" src="/cameras/frame/5"></iframe>			
<? } ?>
<?if(isset($url['cam6'])) { ?>
	<iframe width="640" marginheight="0" marginwidth="0" height="360" scrolling="no" frameborder="0" src="/cameras/frame/6"></iframe>			
<? } ?>
<?if(isset($url['cam7'])) { ?>
	<iframe width="640" marginheight="0" marginwidth="0" height="360" scrolling="no" frameborder="0" src="/cameras/frame/7"></iframe>			
<? } ?>
<?if(isset($url['cam8'])) { ?>
	<iframe width="640" marginheight="0" marginwidth="0" height="360" scrolling="no" frameborder="0" src="/cameras/frame/8"></iframe>			
<? } ?>

<div class="row">
	<div class="col-md" style="margin: 3px;">
		<div class="box">
			<b>Who's on?</b>
		</div>
	</div>
</div>		
<?			
$avatars_url = 'https://s3.amazonaws.com/uf.shiftplanning.com/';
$p = $planning['data'];

foreach($p AS $r) {
	$av_json_decode = json_decode($r['employee_avatar_url']);
	$avatar = $av_json_decode->small;
	?>

	<div class="row" style="background-color: #FFF; border: 1px solid silver; margin: 5px;">	

		<div class="col-md" style="margin: 3px;" style="background-color: #FFF; border: 1px solid silver; margin: 5px;">
			<div class="box">
				<img src="<?=$avatars_url?><?=$avatar?>"> <?=$r['employee_name'];?> <small>(<?=$r['schedule_name'];?>)</small>
			</div>
		</div>

		<div class="col-md" style="margin: 3px;" style="background-color: #FFF; border: 1px solid silver; margin: 5px;">
			<div class="box">
				<?=$r['shift_start']['time'];?> - <?=$r['shift_end']['time'];?>
			</div>
		</div>
	</div>
	<?
}
?>
</body>
</html>