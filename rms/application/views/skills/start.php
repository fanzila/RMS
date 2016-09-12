		</div>
		<div data-role="content" data-theme="a">
				<ul data-role="listview" data-inset="true">
					<li data-role="list-divider">Infos pratiques</li>
					<li><? echo nl2br($bu_infos); ?></li>
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
			<ul data-role="listview" data-inset="true">
				<li>
					<iframe width='100%' height='700px' frameborder='0.1' src="<?=$bu_link?>"></iframe>
				</li>
			</ul>
		</div><!-- /content -->
	</div><!-- /page -->	