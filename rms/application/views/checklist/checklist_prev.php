		</div>
		<div data-role="content">
			<ul data-role="listview">
				<? foreach ($checklists_rec as $rec) { ?>
					<li><a rel="external" data-ajax="false" href="/checklist/viewckltasks/0/<?=$rec['lid']?>"><?=$rec['name']?> | <?=$rec['first_name']?> <?=$rec['last_name']?> | <?=$rec['date']?></a></li>
					<? } ?>
				</ul>

			</div>
		</div>