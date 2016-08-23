		</div>
		<div data-role="content" data-theme="a">
			<h2>Info Bu spé</h2>
				<ul data-role="listview" data-inset="true">
					<li data-role="list-divider"><? echo $bu_name;?></li>
					<li><? echo $bu_infos; ?></li>
				</ul>

			<h2>Skills</h2>
			<ul data-role="listview" data-inset="true" style="background-color : #f5f5f5">
				<li><a rel="external" data-ajax="false" href="/skills/"><i class="zmdi zmdi-star-half zmd-fw"></i> My Skills</a></li>
				<?foreach ($skills_records as $skills_record) {?>
					<?if($skills_record->id_sponsor==$current_user){?>
						<li><a data-ajax="false" href="/skills/index/<?=$skills_record->id_user?>">My trainee : <?=$skills_record->first_name?> <?=$skills_record->last_name?></a></li>
					<?}?>
				<?}?>					
			</ul><br/>
			<h2>Hank's play book</h2>
			<p>Sur chrome uniquement. Si on est connecté à son compte avec un droit de regard.</p>
			<iframe width='100%' height='700px' frameborder='0' scrolling="no" src="https://docs.google.com/document/d/1xkGHiWm0ekGp0yQdLAbrGWr9UwRSJYcfJQ6E3gnoMHc/"></iframe>

		</div><!-- /content -->
	</div><!-- /page -->	