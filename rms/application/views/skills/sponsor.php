		</div>
		<div data-role="content">
			<h2>My trainee(s)</h2>
			<?$i=0;?>
			<ul data-role="listview" data-inset="true" style="background-color : #f5f5f5">
				<?foreach ($skills_records as $skills_record) {?>
					<?if($skills_record->id_sponsor==$current_user){?>
						<li><a data-ajax="false" href="/skills/index/<?=$skills_record->id_user?>"><?=$skills_record->first_name?> <?=$skills_record->last_name?></a></li>
						<?$i++;?>
					<?}?>
				<?}?>
			</ul>

			<?if($i==0){?>
				<h3>You have no trainee (yet).</h3>
			<?}?>
		</div><!-- /content -->
	</div><!-- /page -->