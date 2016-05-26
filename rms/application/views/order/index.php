<?php $title = "Order"; include('jq_header.php'); ?> 
<body>
	<div id="orderpage" data-role="page">
		<div data-role="header">
			<? if(!$keylogin) { ?><a href="/admin/" data-ajax="false" data-icon="home">Home</a><? } ?>
			<h1>Order | <?=$bu_name?> | <?=$username?></h1>
		</div>
		<div data-role="content">
			<?php if($user_groups->level >= 1) { ?>

				<ul data-role="listview" data-inset="true" data-filter="false">
					<li data-role="list-divider">ORDER FREQUENCY</li>
					<?
				foreach ($freq as $var) {
					if($var['name'] != 'previous') {
						?>
						<li><a data-ajax="false" href="/order/viewProducts/<?=$var['id']?>"><?=strtoupper($var['name'])?></a></li>
						<? }  } ?>
					</ul>
					<? } ?>
					<a href="/order/previousOrders" rel="external" data-ajax="false" data-role="button" data-inline="true" data-icon="search" data-mini="true" data-theme="a">Log</a>
					<?php if($user_groups->level >= 2) { ?>
						<a href="/product_admin/" rel="external" data-ajax="false" data-role="button" data-inline="true" data-icon="edit" data-mini="true" data-theme="a">Products admin</a>
						<a href="/product_admin/mapping" rel="external" data-ajax="false" data-role="button" data-inline="true" data-icon="edit" data-mini="true" data-theme="a">Products mapping</a>
						<a href="/crud/suppliers/" rel="external" data-ajax="false" data-role="button" data-inline="true" data-icon="edit" data-mini="true" data-theme="a">Suplliers</a>
						<a href="/crud/productsAttribut/" rel="external" data-ajax="false" data-role="button" data-inline="true" data-icon="edit" data-mini="true" data-theme="a">Products attribut</a>
							
						<ul id="autocomplete" data-role="listview" data-inset="true" data-filter="true" data-filter-placeholder="Find a product..." data-filter-theme="d"></ul>
						<hr />
						<ul data-role="listview" data-inset="true" data-filter="true">
							<li data-role="list-divider">SUPPLIERS</li>
							<?
						foreach ($suppliers as $varsup) {
								?>
								<li><a data-ajax="false" href="/order/viewProducts/1000/0/<?=$varsup['id']?>"><?=strtoupper($varsup['name'])?> 
								<? if(isset($varsup['last_order'])) { ?><small>  <i>Last order: <?=$varsup['last_order']?> by <?=$varsup['last_order_user']->username?></i></small><? } ?></a></li>
								<? } ?>
							</ul>
					<? } ?>
					<br /><br />						
				</div><!-- /content -->

				<div id="view"></div>
			</div><!-- /page -->
		    <script>
				$( document ).on( "pageinit", "#orderpage", function() {
					$( "#autocomplete" ).on( "listviewbeforefilter", function ( e, data ) {
						var $ul = $( this ),
							$input = $( data.input ),
							value = $input.val(),
							html = "";
						$ul.html( "" );
						if ( value && value.length > 1 ) {
							$ul.html( "<li><div class='ui-loader'><span class='ui-icon ui-icon-loading'></span></div></li>" );
							$ul.listview( "refresh" );
							$.ajax({
								url: "/order/autoCompProducts",
								
								dataType: "jsonp",
								crossDomain: true,
								data: {
									q: $input.val()
								}
							})
							.then( function ( response ) {
								$.each( response, function ( i, val ) {
									var res = val.split("|||");	
									html += "<li><a data-ajax='false' href='/product_admin/index?id_product=" + res[1] + "'><table width='100%' border='0'><tr><td width='40%'>" + res[0] + " </td><td width='40%'> " + res[2] + " </td><td width='10%'> QTTY: " + res[3] + " </td><td width='10%'> Colisage: " + res[6] + " </td><td width='10%'> " + res[4]/1000 + "€ / " + res[5] + "</td></tr></table></a></li>";
								});
								$ul.html( html );
								$ul.listview( "refresh" );
								$ul.trigger( "updatelayout");
							});
						}
					});
				});
		    </script>
			<?php include('jq_footer.php'); ?>