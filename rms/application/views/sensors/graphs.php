  <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.0/Chart.js"></script>
  <link rel="stylesheet" href="/public/css/sensors.css" />
</div>
<div data-role="content" data-theme="a">
  <h3>Sensors Graphs</h3>
  <?if (empty($sensors)) {?> 
    <p> No sensors graphs to view.
  <? } ?>
  <?foreach($sensors as $sensor) { ?>
      <div id="graph-table">
        <div id="sensor-stat" style="max-width: 800px; max-height: 400px; min-width: 400px; min-height: 200px; margin-bottom: 100px;">
          <h4><?=$sensor['name']?></h4>
          <canvas id="s_<?=$sensor['id']?>-canvas" width=800 height=400></canvas>
        </div>
    		<script type="text/javascript">
    		  var ctx = $('#s_<?=$sensor['id']?>-canvas');
    		  var myChart = new Chart(ctx, {
    		    type: 'line',
    		    data: {
    					labels: [<?=$sensor['lastMonthTemp']['dateList']?>],
    					datasets: [{
    						label: 'Sensor temps average for the month (<?=$sensor['name']?>)',
    						data: [<?=$sensor['lastMonthTemp']['tempList']?>],
    						lineTension: 0,
    						borderColor: "rgb(255, 0, 0)",
    						fill: false
    					}]
    				},
    		    options: {
    		      maintainAspectRatio: false,
    		      responsive: true
    		    }
    		  });
    		</script>
      </div>
<? } ?>
</div>
</div>