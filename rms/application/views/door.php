	</div>
	<div data-role="content" data-theme="a">		
				<? if(!empty($msg)) { 
					$bgcolor = '#ffaeb8'; 
					if($status == 0) $bgcolor = '#e8ffb9'; ?>
					
					<ul data-role="listview" data-inset="true" data-split-theme="a" data-divider-theme="a">
						<li style="background-color: <?=$bgcolor?>;">Action effectuée, vérifiez <b>==> IMPERATIVEMENT <==</b> AVANT DE PARTIR</li> 
						<li style="background-color: <?=$bgcolor?>;"><small>Code de retour : <?=$msg?></small></li>
					</ul>
				<? } ?>
				<div class="row">
				    <div class="col-xs">
				        <div class="box"><a href="/door/index/1/" rel="external" data-ajax="false" class="ui-btn ui-btn-raised">Open</a></div>
				    </div>
				    <div class="col-xs">
				        <div class="box"><a href="/door/index/2/" rel="external" data-ajax="false" class="ui-btn ui-btn-raised">Close</a></div>
				    </div>
				</div>
	</div><!-- /content -->
</div><!-- /page -->