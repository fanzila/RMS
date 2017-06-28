		<link rel="stylesheet" href="/public/reminder.css" />
		</div>
		<div data-role="content">
			<?if($msg) { ?>
				<div style="background-color: #d6f0d6; height:60px;" class="ui-body ui-body-a">	
					<br /><?=$msg?> Thanks! Have A Nice Karma!"
				</div>			
			<? } ?>
			<ul id="ChecklistsUL" data-role="listview" data-inset="true">
						<?
							if ($type == false) { ?>
								<h3>Service Checklists :</h3>
								<div id="service-div">
									<? 	$servVars = 0;
											foreach ($checklists as $var) {
												if ($var['type'] == 'service') {
											?>
												<li><a class="ui-btn ui-icon-right" style="text-align: left;" data-ajax="false" href="/checklist/viewckltasks/<?=$var['id']?>"><?=$var['name']?></a></li>
								<?		$servVars += 1;
											}
										}
										if ($servVars == 0) { 
											echo "<span>No checklists available</span>";
										} ?>
								</div>
								
								<h3 id="h3Kitchen">Kitchen Checklists :</h3>
								<div id="kitchen-div">
									<? 	$kitchenVars = 0;
											foreach ($checklists as $var) {
												if ($var['type'] == 'kitchen') {
											?>
													<li><a class="ui-btn ui-icon-right" style="text-align: left;" data-ajax="false" href="/checklist/viewckltasks/<?=$var['id']?>"><?=$var['name']?></a></li>
								<?			$kitchenVars += 1;
												}
										}
										if ($kitchenVars == 0) { ?> 
											<script>
												var h3Kitchen = document.getElementById('h3Kitchen');
												var kitchenDiv = document.getElementById('kitchen-div');
												
												h3Kitchen.parentNode.removeChild(h3Kitchen);
												kitchenDiv.parentNode.removeChild(kitchenDiv);
											</script>
									<?
									}
									?>
								</div>
						<?} else { 
									$varCount = 0;
									foreach ($checklists as $var) {
									?>
										<li><a class="ui-btn ui-icon-right" style="text-align: left;" data-ajax="false" href="/checklist/viewckltasks/<?=$var['id']?>"><?=$var['name']?></a></li>
						<?		$varCount += 1;
									}
									if ($varCount == 0) { 
												echo "<span>No checklists available</span>";
											}
							}?>
			</ul>
				<a href="/checklist/viewcklprevioustasks" rel="external" data-ajax="false" data-role="button" data-inline="true" data-icon="search" data-mini="true" data-theme="a">Log</a> 
				<!--
				<? if($keylogin) { ?>
					<a href="#" rel="external" data-ajax="false" data-role="button" data-inline="true" data-icon="power" data-mini="true" data-theme="a" name="buttonClick" onclick="refreshPage()">Refresh</a>
				<? } ?>
				-->
		</div><!-- /content -->

		<br /><br />
		<div id="view"></div>
	</div><!-- /page -->
  <? if($keylogin) { ?>
	<script type="text/javascript" charset="utf-8">
	function refreshPage()
	{
	    jQuery.mobile.changePage(window.location.href, {
	        allowSamePageTransition: true,
	        transition: 'none',
	        reloadPage: true
	    });
	}
      </script>
	<? } ?>
	