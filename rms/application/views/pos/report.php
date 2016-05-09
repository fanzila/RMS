<div data-role="page" data-theme="a">
	<div data-role="header">
		<? if(!$keylogin) { ?><a href="/admin/" data-role="button" data-inline="true" data-ajax="false" data-icon="home">Home</a><? } ?>
		<a href="/pos/" data-ajax="false" data-icon="home">Back</a>
		<h1><?=$title?></h1>
	</div>

	<div data-role="content" data-theme="a">
		<h4>Current Cashpad cash: <?=$pos_cash?>€ | Safe cash: <?=$safe_cash?>€ |  Safe TR num: <?=$safe_tr?></h4>
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
						<p>Cashpad cash: <?=$m['mov']['pos_cash_amount']?>€ | Safe cash: <?=$m['mov']['safe_cash_amount']?>€ | Safe TR num: <?=$m['mov']['safe_tr_num']?></p>
			
						<table style="border: 1px solid #dedcd7; margin-top:10px" cellpadding="5" width="70%">
							<tr style="background-color: #fbf19e;">
								<td>Payment type</td>
								<td>Montant user</td>
								<?if($mov != 'safe') { ?><td>Montant Cashpad</td><? } ?>
								<?if($mov != 'safe') { ?><td>Solde</td><? } ?>
							</tr>
							<?php foreach ($m['pay'] as $m2): ?>
								<? if($m2['id'] == 1) $cash_amount = $m2['amount_user']; ?>
								<tr>
									<td><?=$m2['name']?></td>
									<td><? if($m2['id'] != 12 AND $m2['id'] != 11) { echo $m2['amount_user']; if($m2['id'] == 3) { echo " TR"; } else { echo "€"; }  } else { echo "-"; } ?></td>
									<?if($mov != 'safe') { ?><td><? if($m2['id'] != 9) { echo $m2['amount_pos']."€"; } else { echo "-"; } ?></td><? } ?>
									<?if($mov != 'safe') { ?><td><? if($m2['id'] != 3 AND $m2['id'] != 1) { echo $m2['amount_pos']-$m2['amount_user']."€"; } else echo "-"; ?></td><? } ?>
								</tr>						
							<?php endforeach; ?>
						</table>
<? if($mov =='close') { ?>
	<? if($mov != 'safe') { $check_amount = $cash_amount-$m['mov']['pos_cash_amount']; ?> 
		<? if($check_amount < 0 ) { ?><p style="color : red; font: bold 16px Arial, Verdana, sans-serif;">ALERT! <?=$check_amount?>€ cash missing!</p>
		<? } } ?>
		
		<table style="border: 1px solid #dedcd7; margin-top:10px" cellpadding="5" width="70%">
			<tr style="background-color: #fbf19e;"><td colspan="6">POS Movements</td></tr>
			<tr style="background-color: #fbf19e;">
				<td>Date</td>
				<td>User</td>
				<td>Amount</td>
				<td>Payment type</td>
				<td>Description</td>
				<td>Customer</td>
			</tr>
		<?php foreach ($m['cashmovements'] as $mov): ?> 
			<tr>
				<td><?=$mov['date']?></td>
				<td><? if(empty($mov['username'])) { echo $mov['user']; } echo $mov['username']; ?></td>
				<td><?=$mov['amount']/1000?>€</td>
				<td><?=$mov['method_name']?></td>
				<td><?=$mov['description']?></td>
				<td><? if($mov['customer_first_name']) { echo $mov['customer_first_name'].".".$mov['customer_last_name']; } ?></td>
			</tr>
		<?php endforeach; ?>
	</table>
	<table style="border: 1px solid #dedcd7; margin-top:10px" cellpadding="5" width="70%">
		<tr style="background-color: #fbf19e;">
			<td>Users</td>
		</tr>
	<?php foreach ($m['close_users'] as $cusers): ?> 
		<tr><td><?=$cusers?></td></tr>
		<?php endforeach; ?>
	</table>
	<? } ?>
				</li>
			</ul>
		</div> <!-- end collapsible -->
	<?php endforeach; ?>
</div> <!-- end content -->
</div> <!-- end page -->