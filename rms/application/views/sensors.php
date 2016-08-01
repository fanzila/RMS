
	<!-- <link class="include" rel="stylesheet" type="text/css" href="/public/jqplot/jquery.jqplot.css" /> 
	<link rel="stylesheet" type="text/css" href="/public/jqplot/examples.css" /> -->
	<!-- 	
	<link type="text/css" rel="stylesheet" href="/public/jqplot/syntaxhighlighter/styles/shCoreDefault.min.css" />
    <link type="text/css" rel="stylesheet" href="/public/jqplot/syntaxhighlighter/styles/shThemejqPlot.min.css" />
	-->
	 <!--[if lt IE 9]><script language="javascript" type="text/javascript" src="/public/jqplot/excanvas.js"></script><![endif]-->

	</div>
	<div data-role="content" data-theme="a">
<h3>Current temperature</h3>

<table data-role="table" id="table-custom-2" data-mode="reflow" class="ui-body-d ui-shadow table-stripe ui-responsive" data-column-popup-theme="a">
		<thead>
			<tr class="ui-bar-d">
				<th>Device</th>
				<th>Temp</th>
				<th data-priority="1">Last check</th>
				<th data-priority="3">Last alarm</th>
			</tr>
		</thead>
		<tbody>
			<?foreach ($current as $key => $val) { ?>
				<tr>
					<td><?=$val['name']?></td>
					<td><?=$val['temp']+$val['correction']?></td>
					<td><?=$val['date']?></td>
					<td><?=$val['lastalarm']?></td>
				</tr>
			<? } ?>
		</tbody>
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
