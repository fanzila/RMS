</div>
<div data-role="content" data-theme="a">
	<div class="row">
		<div class="col-xs">
			<div class="box">
				<a href="/order/viewOrders/" rel="external" data-ajax="false" class="ui-btn ui-btn-raised">ORDERS</a>
			</div>
		</div>
		<div class="col-xs">
			<div class="box">
				<a href="/order/loss/" rel="external" data-ajax="false" class="ui-btn ui-btn-raised">LOSS</a>
			</div>
		</div>
			<?php if($this->ion_auth_acl->has_permission('product_admin')) { ?>
			<div class="col-xs">
				<div class="box">
					<a href="/product_admin/" rel="external" data-ajax="false" class="ui-btn ui-btn-raised">Products admin</a>
				</div>
			</div>
			<div class="col-xs">
				<div class="box">
					<a href="/product_admin/mapping/" rel="external" data-ajax="false" class="ui-btn ui-btn-raised">Products mapping</a>
				</div>
			</div>
				<div class="col-xs">
					<div class="box">
						<a href="/supplier/" rel="external" data-ajax="false" class="ui-btn ui-btn-raised">Suppliers</a>
					</div>
				</div>
				<!--
				<div class="col-xs">
					<div class="box">
						<a href="/crud/productsAttribut/" rel="external" data-ajax="false" class="ui-btn ui-btn-raised" onclick="window.open(this.href);return false;">Products attribut</a>
					</div>
				</div>
				-->
				<? } ?>
			</div>
			<ul data-role="listview" data-inset="true" data-filter="false">
				<li data-role="list-divider">ARTICLES</li>
				<li>	
					<ul id="autocomplete_pdt" data-role="listview" data-inset="true" data-filter="true" data-filter-placeholder="Find a product..." data-filter-theme="d"></ul>
				</li>
			</ul>
			<ul data-role="listview" data-inset="true" data-filter="false">
				<li data-role="list-divider">SUPPLIERS</li>
				<?
			foreach ($suppliers as $varsup) {
				?>
				<li><a data-ajax="false" href="/order/viewProducts/0/<?=$varsup['id']?>"><?=strtoupper($varsup['name'])?> 
					<? if(isset($varsup['last_order'])) { ?><small>  <i>Last order: <?=$varsup['last_order']?> by <?=$varsup['last_order_user']->username?></i></small><? } ?></a></li>
					<? } ?>
			</ul>				
					</div><!-- /content -->
					<div id="view"></div>
				</div><!-- /page -->
				<script>
				$( document ).on( "pageinit", "#pageid", function() {
										
					$( "#autocomplete_pdt" ).on( "listviewbeforefilter", function ( e, data ) {
						var $ul = $( this ),
						$input = $( data.input ),
						value = $input.val(),
						html = "<table width='100%' border='0' cellspacing='0' cellpadding='6'>";
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
									html += "<tr bgcolor=''><td style='border: 1px solid #e8e8e8;'><a data-ajax='false' href='/product_admin/index?id_product=" + res[1] + "'>" + res[0] + " </a></td><td style='border: 1px solid #e8e8e8;'> " + res[2] + " </td><td style='border: 1px solid #e8e8e8;'> QTTY: " + res[3] + " </td><td style='border: 1px solid #e8e8e8;'> Colisage: " + res[6] + " </td><td style='border: 1px solid #e8e8e8;'> " + res[4]/1000 + "€ / " + res[5] + "</td></tr>";
								});
								html += "</table>";
								$ul.html( html );
								$ul.listview( "refresh" );
								$ul.trigger( "updatelayout");
							});
						}
					});
					
					
				});
				</script>