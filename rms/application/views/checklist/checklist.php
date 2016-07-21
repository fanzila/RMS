		</div>
		<div data-role="content">
			<?if($msg) { ?>
				<div style="background-color: #d6f0d6; height:60px;" class="ui-body ui-body-a">	
					<br /><?=$msg?> Thanks! Have A Nice Karma!"
				</div>			
			<? } ?>
			<ul data-role="listview" data-inset="true">
						<?
					foreach ($checklists as $var) {
						?>
						<li><a data-ajax="false" href="/checklist/viewckltasks/<?=$var['id']?>"><?=$var['name']?></a></li>
						<? } ?>
					</ul>
				<a href="/checklist/viewcklprevioustasks" rel="external" data-ajax="false" data-role="button" data-inline="true" data-icon="search" data-mini="true" data-theme="a">Log</a> <? if($keylogin) { ?><a href="#" rel="external" data-ajax="false" data-role="button" data-inline="true" data-icon="power" data-mini="true" data-theme="a" name="buttonClick" onclick="refreshPage()">Refresh</a><? } ?>
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