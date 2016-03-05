<div data-role="page" data-theme="a">
	<div data-role="header">
		<? if(!$keylogin) { ?><a href="/admin/" data-role="button" data-inline="true" data-ajax="false" data-icon="home">Home</a><? } ?>
		<a href="/pos/" data-ajax="false" data-icon="home">Back</a>
		<h1><?=$title?></h1>
	</div>

	<div data-role="content" data-theme="a">
		<h4>Safe cash balance : <?=$safe_cash?>€ | Pos cash balance : <?=$pos_cash?>€</h4>
		<h2>Movements</h2>

		<?php foreach ($lines as $m): ?>
			<? $mov = '';
			if($m['mov']['movement'] == 'safe_in' OR $m['mov']['movement'] == 'safe_out') $mov = 'safe';
			if($m['mov']['movement'] == 'open') $mov = 'open';
			if($m['mov']['movement'] == 'close') $mov = 'close';
			?>
			<div data-role="collapsible">
				<h2>ID: <? $dateid = new DateTime($m['mov']['date']); echo date_format($dateid, 'ymd'); echo $m['mov']['id'] ?> - <?=strtoupper($m['mov']['movement'])?></h2>

				<ul data-role="listview" data-theme="d" data-divider-theme="d">
					<li>
						<h3>Date: <?=$m['mov']['date']?></h3>
						<h3>User: <?=$m['mov']['username']?> </h3>
						<p>Comments: <?=$m['mov']['comment']?></p>
						<p>Safe cash amount: <?=$m['mov']['safe_cash_amount']?> | POS cash amount: <?=$m['mov']['pos_cash_amount']?></p>
			
						<table style="border: 1px solid #dedcd7; margin-top:10px" cellpadding="8" width="50%">
							<tr style="background-color: #fbf19e;">
								<td>Payment type</td>
								<td>Montant user</td>
								<td>Montant POS</td>
								<td>Solde</td>
							</tr>
							<?php foreach ($m['pay'] as $m2): ?>
								<? if($m2['id'] == 1) $cash_user = $m2['amount_user']; ?>
								<tr>
									<td><?=$m2['name']?></td>
									<td><?=$m2['amount_user']?></td>
									<td><?=$m2['amount_pos']?></td>
									<td><? echo $m2['amount_pos']-$m2['amount_user']; ?></td>
								</tr>						
							<?php endforeach; ?>
						</table>
<? if($mov =='close') { ?>
<p>Cash pos - cash user: = <?=$m['mov']['pos_cash_amount']-$cash_user?></p>
<p>Users:<br /> 
<?php foreach ($m['close_users'] as $cusers): ?> 
	<?=$cusers?> <br />
	<?php endforeach; ?>
	</p> 
<? } ?>
				</li>

			</ul>

		</div> <!-- end collapsible -->

	<?php endforeach; ?>
</div> <!-- end content -->
</div> <!-- end page -->