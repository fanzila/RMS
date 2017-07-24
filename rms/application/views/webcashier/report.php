	</div>

	<div data-role="content" data-theme="a">
		<h4>Current Cashpad cash: <?=$pos_cash?>€ | Safe cash: <?=number_format($safe_cash,  2, '.', ' ')?>€ |  Safe TR num: <?=$safe_tr?> | Monthly TO: <?=number_format($monthly_to,  2, '.', ' ')?>€</h4>
		<p>Daily Cashpad cash movements</p>
		<ul data-role="listview" data-inset="true">
		<? foreach ($live_movements as $lm):?>
		<li>Date: <?=$lm['DATE']?> | Amount: <? $am = $lm['AMOUNT']/1000; echo $am."€"; ?> | User: <?=$lm['USERNAME']?> |  Description: <?=$lm['DESCRIPTION']?> | Method: <?=$lm['PAYMENTNAME']?> | Customer: <?=$lm['CFIRSTNAME']?> <?=$lm['CLASTNAME']?></li>
		<?php endforeach; ?>
		<? if(empty($live_movements)) { ?>No movement<? } ?>
		</ul>
		<h2>Movements</h2>
		
		<?php foreach ($lines as $m): ?>
			<? $mov = '';
			$cash_amount = 0;
			if($m['mov']['movement'] == 'safe_in' OR $m['mov']['movement'] == 'safe_out') $mov = 'safe';
			if($m['mov']['movement'] == 'open') $mov = 'open';
			if($m['mov']['movement'] == 'close') $mov = 'close';
			?>
			<div data-role="collapsible" style="background-color: <? if ($mov == 'close') { ?> <?if ($m['mov']['status'] == 'ok') {echo "lightgreen";} else if ($m['mov']['status'] == 'error') { echo "rgba(255, 0, 0, 0.6)";} else if ($m['mov']['status'] == 'validated') { echo "rgba(255, 0, 0, 0.4)";}} else { echo "rgb(220, 220, 220)";}?>">
				<h2>ID: <? $dateid = new DateTime($m['mov']['date']); echo date_format($dateid, 'ymd'); echo $m['mov']['id'] ?> - <?=strtoupper($m['mov']['movement'])?></h2>

				<ul data-role="listview" data-theme="d" data-divider-theme="d">
					<li>
						<h3>Date: <?=$m['mov']['date']?></h3>
						<h3>User: <?=$m['mov']['username']?> </h3>
						<p>Comments Cashpad: <?=$m['mov']['comment']?></p>
						<p>Cashpad cash: <?=$m['mov']['pos_cash_amount']?>€ | Safe cash: <?=number_format($m['mov']['safe_cash_amount'],  2, '.', ' ')?>€ | Safe TR num: <?=$m['mov']['safe_tr_num']?></p>
			
						<table style="border: 1px solid #dedcd7; margin-top:10px" cellpadding="5" width="70%">
							<tr style="background-color: #fbf19e;">
								<td>Payment type</td>
								<td>User amount</td>
								<?if($mov != 'safe') { ?><td>Cashpad amount</td><? } ?>
								<?if($mov != 'safe') { ?><td>Balance</td><? } ?>
							</tr>
							<?php $total = 0; $diff = $m['mov']['pos_cash_amount']; foreach ($m['pay'] as $m2): ?>
								<? 
								$total += $m2['amount_pos'];
								if ($m2['id'] == 1) $diff = $diff - $m2['amount_user'];
								if ($m2['id'] == 2 OR $m2['id'] == 3 OR $m2['id'] == 4) $diff = $diff + ($m2['amount_pos']-$m2['amount_user']);
								?>
								<? if($m2['id'] == 1) $cash_amount = $m2['amount_user']; ?>
								<tr>
									<td><?=$m2['name']?></td>
									<td><? if($m2['id'] != 12 AND $m2['id'] != 11 AND $m2['id'] != 5) { echo $m2['amount_user']. "€"; } else { echo "-"; } ?></td>
									<?if($mov != 'safe') { ?><td><? if($m2['id'] != 9) { echo $m2['amount_pos']."€"; } else { echo "-"; } ?></td><? } ?>
									<?if($mov != 'safe') { ?><td><? if($m2['id'] != 1) { echo $m2['amount_pos']-$m2['amount_user']."€"; } else echo "-"; ?></td><? } ?>
								</tr>						
							<?php endforeach; ?>
						</table>
						<? if($mov == 'close') { ?>
							<small>Total Cashpad amount: <?=$total?>€   </small>
							<? if ($diff != 0) { ?>
								<p style="color: red;">Diff: <?=$diff?>€</p>
						<? 	}
							} ?>

	<? if($mov != 'safe') { $check_amount = $cash_amount-$m['mov']['pos_cash_amount']; ?> 
		<? if($check_amount < 0 ) { ?><p style="color : red; font: bold 16px Arial, Verdana, sans-serif;">ALERT! <?=$check_amount?>€ cash missing!</p>
		<? } } ?>
<div style="width:70%">		
		<h3>Movement comments</h3>
		<?
		$id_form = "report".$m['mov']['id'];
		$attributes = array('id' => $id_form, 'name' => $id_form);
		echo form_open("webcashier/save_report_comment", $attributes);?>
			<input maxlength="255" type="text" name="comment-<?=$m['mov']['id']?>" id="comment-<?=$m['mov']['id']?>" data-clear-btn="true" data-inline="true" data-theme="a" value="<?=$m['mov']['comment_report']?>" />
			<? foreach ($all_user_groups as $user_group) {
			 if ($user_group->level >= 3 && $mov == 'close') { ?>
			 <div class="box">
					 <input type="checkbox" name="validate-<?=$m['mov']['id']?>" id="validate-<?=$m['mov']['id']?>" class="custom" <?if ($m['mov']['status'] == 'validated') echo 'checked';?>" />
					 <label style="background-color: white;" for="validate-<?=$m['mov']['id']?>" id="label-<?=$m['mov']['id']?>">Quittance Directeur</label>
			 </div>
			 <? break;
		 			}
		 		} ?>
			<input type="submit" id="sub<?=$m['mov']['id']?>" onclick="validate(<?=$m['mov']['id']?>)" name="submit" value="Save" data-mini="true" data-clear-btn="true" />
			<input type="hidden" name="id" value="<?=$m['mov']['id']?>">
			<input type="hidden" name="diff-<?=$m['mov']['id']?>" id="diff-<?=$m['mov']['id']?>" value="<?=$diff?>">
		</form>
</div>
<? if($mov =='close') { ?>		
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
	<table style="border: 1px solid #dedcd7; margin-top:10px" cellpadding="5" width="70%">
		<tr style="background-color: #fbf19e;"><td colspan="6">Cash Drawer Opened</td></tr>
		<tr style="background-color: #fbf19e;">
			<td>Date</td>
			<td>User</td>
			<td>Terminal</td>
		</tr>
	<?php foreach ($m['cashDrawerOpened'] as $mov): ?> 
		<tr>
			<td><?=$mov['DATE']?></td>
			<td><?= $mov['USER']?></td>
			<td><?=$mov['TERMINAL']?></td>
		</tr>
	<?php endforeach; ?>
</table>
<table style="border: 1px solid #dedcd7; margin-top:10px" cellpadding="5" width="70%">
	<tr style="background-color: #fbf19e;"><td colspan="6">Cancelled Receipts</td></tr>
	<tr style="background-color: #fbf19e;">
		<td>Receipt Closure Date</td>
		<td>User</td>
		<td>Reason</td>
	</tr>
<?php foreach ($m['cancelledReceipts'] as $mov): ?> 
	<tr>
		<td><?=$mov['DATE_CLOSED']?></td>
		<td><?= $mov['OWNER']?></td>
		<td><?=$mov['CANCELLATION_REASON']?></td>
	</tr>
<?php endforeach; ?>
</table>
<table style="border: 1px solid #dedcd7; margin-top:10px" cellpadding="5" width="70%">
	<tr style="background-color: #fbf19e;"><td colspan="6">User Actions : </td></tr>
	<tr>
		<td colspan="6">Total : <?=$m['total_actions']?></td>
	</tr>
	<tr style="background-color: #fbf19e;">
		<td>Receipt Closure Date</td>
		<td>User</td>
		<td>Nb</td>
		<td>Percent</td>
	</tr>
<?php foreach ($m['userActionStats'] as $mov): ?> 
	<tr>
		<td><?=$mov['date_closed']?></td>
		<td><?= $mov['owner']?></td>
		<td><?=$mov['count']?></td>
		<td><?=$mov['percent']?></td>
	</tr>
<?php endforeach; ?>
</table>
	<? } ?>	
				</li>
			</ul>
		</div> <!-- end collapsible -->
	<?php endforeach; ?>
</div> <!-- end content -->
</div> <!-- end page -->

						<script src="/public/jqv/dist/jquery.validate.min.js" type="text/javascript"></script>
						<script>
						
						function isNumeric(n) {
							return !isNaN(parseFloat(n)) && isFinite(n);
						}
						
						function validate(idl) {
							var $form = $('#report' + idl);
							var done = 0;
							
							$form.on('submit', function() {
																					
								var comment = $('#comment-' + idl).val();
							
								if (done >= 1) { return (false); }
								
								$.ajax({
									url: $(this).attr('action'),
									type: $(this).attr('method'),
									data: $(this).serialize(),
									dataType: 'json',
									success: function(json) {
										if(json.reponse == 'okcreate' && done == 0) {
											done = done + 1;
											if(done == 1) {
												//window.location = "/product_admin/index/create1";
												return false; 
											}
											
											return false;
										} else if(json.reponse == 'ok' && done == 0) {
											done = done + 1;
											if(done == 1) { 
												alert('Saved!');
												//location.reload(true);
												return false; 
											}
											return false;
										} else if(done == 0){
											alert('WARNING! ERROR at saving : '+ json.reponse);
											return false;
										}
									}
								}).done(function(data) {
									return false;
								}).fail(function(data) {
									done = done + 1;
									if(done <= 1) { 
										alert('WARNING! ERROR at saving!');
										return false; 
									}
								});
								return false;
							});
						}
						</script>