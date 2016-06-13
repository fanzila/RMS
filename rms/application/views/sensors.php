<!DOCTYPE html>
<html lang="en">
<head>
	<title>HANK - <?=$title?></title>	
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="msapplication-tap-highlight" content="no" />
	<!-- <link class="include" rel="stylesheet" type="text/css" href="/public/jqplot/jquery.jqplot.css" /> 
	<link rel="stylesheet" type="text/css" href="/public/jqplot/examples.css" /> -->
	<link rel="stylesheet" href="/public/jqm/jquery.mobile-1.4.5.min.css" />
	<link rel="stylesheet" href="/public/jqm/themes/jquery.mobile.icons.min.css" />
	<link rel="stylesheet" href="/public/jqm/jquery.mobile.structure-1.4.5.min.css" />
	<link rel="stylesheet" href="/public/jqm/themes/hmw.min.css" />
	<!-- 	
	<link type="text/css" rel="stylesheet" href="/public/jqplot/syntaxhighlighter/styles/shCoreDefault.min.css" />
    <link type="text/css" rel="stylesheet" href="/public/jqplot/syntaxhighlighter/styles/shThemejqPlot.min.css" />
	-->
	 <!--[if lt IE 9]><script language="javascript" type="text/javascript" src="/public/jqplot/excanvas.js"></script><![endif]-->
	<script src="/public/jquery-1.11.3.min.js" type="text/javascript"></script>
	<script src="/public/jqm/jquery.mobile-1.4.5.min.js" type="text/javascript"></script>

</head>

<div data-role="page" data-theme="a">
	<div data-role="header">
		<? if(!$keylogin) { ?><a href="/admin/" data-ajax="false" data-icon="home">Home</a><? } ?> 
		<h1>Sensors | <?=$bu_name?> | <?=$username?></h1>
	</div>
	<div data-role="content" data-theme="a">
<h3>Current temperature</h3>
<table style='padding:10px;'>
	<tr><td style='padding:6px; border: 3px solid silver;' width='30%'>Device</td><td style='padding:6px; border: 3px solid silver;' width='10%'>Temp</td><td width='30%' style='padding:6px; border: 3px solid silver;'>Last check</td><td width='30%' style='padding:6px; border: 3px solid silver;'>Last alarm</td></tr>
<? 
foreach ($current as $key => $val) { ?>
	<tr>
		<td style='padding:6px; border: 1px solid silver;'><?=$val['name']?></td>
		<td style='padding:6px; border: 1px solid silver;'><?=$val['temp']+$val['correction']?></td>
		<td style='padding:6px; border: 1px solid silver;'><?=$val['date']?></td>
		<td style='padding:6px; border: 1px solid silver;'><?=$val['lastalarm']?></td>
	</tr>
<? } ?>
</table>
<!--
<hr>
<h3>Last 12h</h3>

<div class="example-plot" id="chart1"></div>
		

<script type="text/javascript" language="javascript">

$(document).ready(function(){
    var line1 = [[1,6.5], [5,9.2], [8,14], [12,19.65]];
    var line2 = [[1,3.5], [2,6.2], [3,7], [4,18]];
 
	$.jqplot('chart1',  [line1,line2],
	{ 

	  axes:{
		yaxis:{min:-30, max:40},
		xaxis:{min:1, max:12, numberTicks:12},
	},
	  series:[{label:'congel1'},{label:'congel2'}],
	  legend: {
	        show: true,
	        location: 'ne',     // compass direction, nw, n, ne, e, se, s, sw, w.
	        xoffset: 12,        // pixel offset of the legend box from the x (or x2) axis.
	        yoffset: 12,        // pixel offset of the legend box from the y (or y2) axis.
	    }
	
	});
	

 
});
</script>
-->
</div>
</div>
<!--
<script class="include" type="text/javascript" src="/public/jqplot/jquery.jqplot.min.js"></script>
<script language="javascript" type="text/javascript" src="/public/jqplot/plugins/jqplot.logAxisRenderer.js"></script>
<script type="text/javascript" src="/public/jqplot/syntaxhighlighter/scripts/shCore.min.js"></script>
<script type="text/javascript" src="/public/jqplot/syntaxhighlighter/scripts/shBrushJScript.min.js"></script>
<script type="text/javascript" src="/public/jqplot/syntaxhighlighter/scripts/shBrushXml.min.js"></script>
-->
</body>
</html>