	</div>
	<div style="background-color: #fffde8;" data-role="content" data-theme="a">
		<?if($mov == 'close') { ?><h2>CLOSE</h2><small>Closing date: <?=$archive_date?></small><? } ?>
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
					<tr>
						<td>Prélèvement billets</td>
						<td><?=$form_values['prelevement']?></td>
						<td> - </td>
						<td> - </td>
					</tr>
						<? foreach ($pay_values as $key => $value) { 
							$value['man'] = str_replace(',', '.', $value['man']);
							if ($value['id'] == 1) $value['pos'] = $form_values['cashpad_amount']-$form_values['prelevement']; 
							?>
						<tr>
							<td>
							<? if($value['name'] == 'CB') $value['name'] = 'CB + cartes TR'; ?>	
							<?=$value['name']?>
							</td>
							<? if ($value['id'] == 9) { ?>
								<td><?=number_format($value['man'], 2)?></td>
								<td> - </td>
								
							<? } elseif ($value['id'] == 12) { ?>
								<td> - </td>
								<td><?=number_format($value['pos'], 2)?></td>
								
							<? } else { ?>
							<td <?if (($value['man'] - $value['pos']) != 0) echo "style='color:red;'"?>><? if (isset($value['man']) AND !empty($value['man'])) { echo number_format($value['man'], 2); } else { echo "0"; }?></td>
	
							<td <?if (($value['man'] - $value['pos']) != 0) echo "style='color:red;'"?>><? if (isset($value['pos']) AND !empty($value['pos'])) { echo number_format($value['pos'], 2); } else { echo "0"; }?></td>
							<? } ?>
							<? if ($value['id'] == 9) { ?>
								<td> - </td>
							<? } else { ?>
								<td>
								<?
								$calcdiff = $value['man']-$value['pos'];
								echo number_format($calcdiff, 2) 
								?></td>
							<? } ?>
						</tr>
					<? }?>
					<tr style="background-color: #ff7c76; margin-top:10px">
						<td colspan="4">Ecart = <b><?=number_format($form_values['diff'], 2)?></b> </td>
					</tr>
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
				<tr style="background-color: #fdfff9;">
					<td><b>Prélèvement billets</b></td>
					<td>
				<input type="text" name="prelevement" id="prelevement" data-clear-btn="true" value="<? if(isset($form_values['prelevement'])) echo $form_values['prelevement']; ?>"/>
					</td>
					<td>Indiquer le montant total des billets prélevés du fond de caisse.</td>
				</tr>
				<?php foreach ($payment as $mode): ?>
					<?php 
					$amount_user	= '0.00';
					if($mov == 'close') $com = $mode->comment_close;
					if($mode->report == 'no') continue;
					?>
					<tr style="background-color: #fdfff9;">
						<td width="10%"><b><?=$mode->name?></b></td>
						<td width="40%">
							<? if($mode->id == 1) { ?>
								<input maxlength="10" type="text" name="cash2" id="basic" data-clear-btn="true" <?if (isset($form_values['cash2'])) echo 'value="'.$form_values['cash2'] .'"';?>/>
							<? } elseif($mode->id == 2) { ?>
								<table border="0" cellpadding="2" width="100%"><tr>
								<td>CB EMV: <input maxlength="10" type="text" name="cbemv" id="basic" data-clear-btn="true" <?if (isset($form_values['cbemv'])) { echo 'value="'.$form_values['cbemv'] .'"'; }?>/></td>
								<td>CB CLESS: <input maxlength="10" type="text" name="cbcless" id="basic" data-clear-btn="true" <?if (isset($form_values['cbcless'])) { echo 'value="'.$form_values['cbcless'] .'"'; }?>/></td>
								</tr></table>
							<? } elseif($mode->id == 3) { ?>
								<table border="0" cellpadding="2" width="100%"><tr>
								<td>PAPIERS: <input maxlength="10" type="text" name="man_3" id="basic" data-clear-btn="true" <?if (isset($form_values['man_3'])) { echo 'value="'.$form_values['man_3'] .'"'; }?>/></td>
								<td>CARTES: <input maxlength="10" type="text" name="titre_card" id="basic" data-clear-btn="true" <?if (isset($form_values['titre_card'])) { echo 'value="'.$form_values['titre_card'] .'"'; }?>/></td>
								</tr></table>
							<? } else { ?>
								<input maxlength="10" type="text" name="man_<?=$mode->id?>" id="basic" data-clear-btn="true" <?if (isset($form_values['man_'.$mode->id])) { echo 'value="'.$form_values['man_'.$mode->id] .'"'; }?>/>
							<? } ?>
								</td>
						<td width="40%"><?=nl2br($com)?></td>
					</tr>
				<?php endforeach; ?>
			</table>
			<br /> 
			Comments: <input type="text" name="comment" id="comment" data-clear-btn="true" value="<? if(isset($form_values['comment'])) echo $form_values['comment']; ?>"/>
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
			<input type="button" onClick="validateBoth();" name="save" value="SAVE">			
			<? } ?>
	</form>
</div>
</div>
<script>
function validateError() {
	var comment = document.forms["pos"]["comment"].value;
	var blc = document.forms["pos"]["blc"];
	if (typeof(blc) != 'undefined') {
		if (blc.checked === true) {
			if (comment == null || comment == "") {
				alert('Commentaire obligatoire en cas d\'erreur');
				return (false);
			} else {
				return (true);
			}
		} else {
			return (true);
		}
	} else {
		return (true);
	}
}

function validateBoth() {
	if (validateError() == true) {
		validator();
	} else {
		return (false);
	}
}
</script>