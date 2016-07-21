		</div>
		<div data-role="content">
			<div data-theme="a" data-form="ui-body-a" class="ui-body ui-body-a ui-corner-all">
			<? if(empty($tasks)) { ?>
			<br />Nothing done so far...<br />
			<? } ?>			
				<ul>
				<?
					foreach ($tasks as $line) {	
				?>
					<li>
						<label> <?=$line->date?> - <?=$line->username?> - <?=$line->task?></label>
					</li>
					<? } ?>
				</ul>
			</div><!-- /theme -->
		</div><!-- /content -->
	</div><!-- /page -->