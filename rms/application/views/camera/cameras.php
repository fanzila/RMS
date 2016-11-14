<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="msapplication-tap-highlight" content="no" />
	<meta http-equiv="cache-control" content="no-cache">
	<meta http-equiv="expires" content="-1">
	<meta http-equiv="pragma" content="no-cache">
	<META http-equiv="refresh" content="60"> 
</head>
<body>
	<div style="width:99%; background-color: #b8cb64; padding:6px; margin: 0 auto 5px; font: 17px 'Lucida Grande', Lucida, Verdana, sans-serif; font-weight: bold;">ARCH: <?=number_format($ca[1]['amount']/1000, 0, ',', ' ')?>€  <small><?=$ca[1]['last']?></small> | GRAV: <?=number_format($ca[2]['amount']/1000, 0, ',', ' ')?>€ <small><?=$ca[2]['last']?></small></div>
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
<p><a href="/cameras/index/onebu/1">View cam for current BU only</a></p>
</body>
</html>