	</div>
	<div data-role="content" data-theme="a">
		<?if($mov == 'close') { ?><small>Closing date: <?=$archive_date?></small><? } ?>
		<?$form_values = $this->session->flashdata('form_values'); $pay_values = $this->session->flashdata('pay_values');?>
		<?if ($pay_values) { ?>
			<p><b>Erreur dans le(s) montant(s) indiqué(s).</b></p>
			<p>Consultez les erreurs dans le tableau ci-dessous et corrigé éventuellement les montants que vous avez indiqués.<br />
			<b>Si vos comptages sont justes, cocher la case "Ignorer les erreurs et continuer" et ajouter un commentaire.<br />
			Dans tous les cas, vous devez valider ce formulaire.</b>Votre manager prendra contact avez vous ultérieurement.</p>
				<table style="border: 1px solid #dedcd7; margin-top:10px" cellpadding="5" width="100%">
					<tr style="background-color: #c2ff91; margin-top:10px">
						<td colspan="4">
						<b>Aide à la correction</b>
						</td>
					</tr>
					<tr style="background-color: #fbf19e;">
						<td>Type paiement</td>
						<td>Montant utilisateur</td>
						<td>Montant caisse</td>
						<td>Différence</td>
						
					</tr>

						<? foreach ($pay_values as $key => $value) { ?>
						<tr>
							<td><?=$value['name']?></td>
							<? if ($value['id'] == 9) { ?>
								<td><?=$value['man']?></td>
								<td> - </td>
							<? } else { ?>
							<td <?if (($value['man'] - $value['pos']) != 0) echo "style='color:red;'"?>><? if (isset($value['man']) AND !empty($value['man'])) { echo $value['man']; } else { echo "0"; }?></td>
							
							<? if ($value['id'] == 1) $value['pos'] = $form_values['cashpad_amount']; ?>
	
							<td <?if (($value['man'] - $value['pos']) != 0) echo "style='color:red;'"?>><? if (isset($value['pos']) AND !empty($value['pos'])) { echo $value['pos']; } else { echo "0"; }?></td>
							<? } ?>
							<? if ($value['id'] == 9) { ?>
								<td> - </td>
							<? } else { ?>
								<td><?=$value['man']-$value['pos']?></td>
							<? } ?>
						</tr>
					<? }?>
				</table>
		<? } ?> 
		<br />
		<form id="pos" name="pos" method="post" action="/webcashier/save">
			<table border="0" cellpadding="5" width="100%">
				<tr style="background-color: #dfdfdf;">
					<td>Payment type</td>
					<td>Amount</td>
					<td>Info</td>
				</tr>
				<?php foreach ($payment as $mode): ?>
					<?php 
					$amount_user	= '0.00';
					if($mov == 'open')  $com = $mode->comment_open;
					if($mov == 'close') $com = $mode->comment_close;
					if($mode->report == 'no') continue;
					?>
					<tr style="background-color: #fdfff9;">
						<td width="10%"><b><?=$mode->name?></b></td>
						<td width="40%">
							<? if($mode->id == 1) { ?>
								<table border="0" cellpadding="2" width="100%"><tr>
									<td>Pièces:</td>
									<td><input maxlength="10" type="text" name="cash2" id="basic" data-clear-btn="true" <?if (isset($form_values['cash2'])) echo 'value="'.$form_values['cash2'] .'"';?>/></td>
									</tr>
									<tr>
										<td> </td>
									</tr>
									<tr>
									<td>Billets:</td>
									<td><small>20€</small><input maxlength="10" type="text" name="20Bill" id="basic" data-clear-btn="false" <?if (isset($form_values['20Bill'])) { echo 'value="'.$form_values['20Bill'] .'"'; }?>/></td>
									<td><small>10€</small><input maxlength="10" type="text" name="10Bill" id="basic" data-clear-btn="false" <?if (isset($form_values['10Bill'])) { echo 'value="'.$form_values['10Bill'] .'"'; }?>/></td>
									<td><small>5€</small><input maxlength="10" type="text" name="5Bill" id="basic" data-clear-btn="false" <?if (isset($form_values['5Bill'])) { echo 'value="'.$form_values['5Bill'] .'"'; }?>/></td>
								</tr></table>
							<? } elseif($mode->id == 2) { ?>
									<table border="0" cellpadding="2" width="100%"><tr>
										<td>CB EMV: <input maxlength="10" type="text" name="cbemv" id="basic" data-clear-btn="true" <?if (isset($form_values['cbemv'])) { echo 'value="'.$form_values['cbemv'] .'"'; }?>/></td>
										<td>CB CLESS: <input maxlength="10" type="text" name="cbcless" id="basic" data-clear-btn="true" <?if (isset($form_values['cbcless'])) { echo 'value="'.$form_values['cbcless'] .'"'; }?>/></td>
									</tr></table>
							<? } else { ?>
								<input maxlength="10" type="text" name="man_<?=$mode->id?>" id="basic" data-clear-btn="true" <?if (isset($form_values['man_'.$mode->id])) { echo 'value="'.$form_values['man_'.$mode->id] .'"'; }?>/>
							<? } ?>
								</td>
						<td width="40%"><?=nl2br($com)?></td>
					</tr>
				<?php endforeach; ?>
			</table>
			Comments: <input type="text" name="comment" id="comment" data-clear-btn="true" />
			<select style="background-color:#a1ff7c" name="user" id="user" data-inline="true" data-theme="a" required>
				<option value="0">User</option>
				<?
			foreach ($users as $user) {
				?>
				<option value="<?=$user->id?>" <?if(isset($form_values['user']) && ($form_values['user'] == $user->id)) echo "selected";?>><?=$user->first_name?> <?=$user->last_name?></option>
				<? 
			}	
			?>
		</select>
		<input type="hidden" name="mov" value="<?=$mov?>" />
		<input type="hidden" name="archive" value="<?=$archive_file?>" />
		<?if(empty($force)) { ?>
			
			<? if ($mov == 'close') { ?>
				<? if ($form_values) { ?>
					<label style="background-color: white;" for="blc">Ignorer les erreurs et continuer.</label>
					<input type="checkbox" name="blc" id="blc">
				<? } ?>
				<? foreach ($closure_data['ca'] as $pos) { ?>
						<input type="hidden" name="<?='pos_'.$pos['IDMETHOD']?>" id="<?='pos_'.$pos['IDMETHOD']?>" value="<?=$pos['SUM']?>">
				<? } 
					} ?>
			<input type="button" onClick="validator();" name="save" value="SAVE">			
			<? } ?>
	</form>
</div>
</div>