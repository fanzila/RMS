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
					foreach ($checklists as $var) {
						if ($type == false) {
						?>
						<li class="<?=$var['type']?>"><a data-ajax="false" href="/checklist/viewckltasks/<?=$var['id']?>"><?=$var['name']?></a></li>
						<? } else { ?>
						<li><a data-ajax="false" href="/checklist/viewckltasks/<?=$var['id']?>"><?=$var['name']?></a></li>
					<?		} 
						} ?>
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
	<? }
		if ($type == false) { ?>
			<script>
				var service = document.getElementsByClassName('service');
				var kitchen = document.getElementsByClassName('kitchen');
				var ul = document.getElementById('ChecklistsUL');
				var h3Service = document.createElement('h3');
				var h3Kitchen = document.createElement('h3');
				h3Service.innerHTML = 'Service Checklists :';
				h3Kitchen.innerHTML = 'Kitchen Checklists :';
				var serviceDiv = document.createElement('div');
				var kitchenDiv = document.createElement('div');
				console.log(service);
				
				serviceDiv.setAttribute('id', 'service-div');
				kitchenDiv.setAttribute('id', 'kitchen-div');
				ul.appendChild(serviceDiv);
				ul.appendChild(kitchenDiv);
				ul.insertBefore(h3Kitchen, kitchenDiv);
				ul.insertBefore(h3Service, serviceDiv);
				if (service.length > 0) {
					for (i = 0; i < service.length; i += 1) {
						serviceDiv.appendChild(service[i]);
					}
				} else {
					serviceDiv.innerHTML = 'No reminders';
				}
				if (kitchen.length > 0) {
					for (i = 0; i < kitchen.length; i += 1) {
						kitchenDiv.appendChild(kitchen[i]);
					}
				} else {
					kitchenDiv.innerHTML = 'No reminders';
				}
			</script>
<?
		}
	 ?>
	