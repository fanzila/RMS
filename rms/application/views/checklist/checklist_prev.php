<body>
	<div data-role="page">

		<div data-role="header">
			<a href="/checklist/" data-ajax="false" data-icon="home">Home</a>
			<h1>Checklist View Records</h1>
		</div>

		<div data-role="content">
			<ul data-role="listview">
				<? foreach ($checklists_rec as $rec) { ?>
					<li><a rel="external" data-ajax="false" href="/checklist/viewckltasks/0/<?=$rec['lid']?>"><?=$rec['name']?> | <?=$rec['first_name']?> <?=$rec['last_name']?> | <?=$rec['date']?></a></li>
					<? } ?>
				</ul>

			</div>
		</div>
	<script src="/public/jqm/jquery.mobile-1.4.5.min.js"></script>
