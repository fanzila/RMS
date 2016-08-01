	</div>
	<div data-role="content" data-theme="a">
		<?if($mov == 'close') { ?><small>Closing date: <?=$archive_date?></small><? } ?>
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
									<td>Pièces: <input maxlength="10" type="text" name="cash2" id="basic" data-clear-btn="true" /></td>
									<td>Billets: <input maxlength="10" type="text" name="notes1" id="basic" data-clear-btn="true" /></td>
								</tr></table>
							<? } elseif($mode->id == 2) { ?>
									<table border="0" cellpadding="2" width="100%"><tr>
										<td>CB EMV: <input maxlength="10" type="text" name="cbemv" id="basic" data-clear-btn="true" /></td>
										<td>CB CLESS: <input maxlength="10" type="text" name="cbcless" id="basic" data-clear-btn="true" /></td>
									</tr></table>
							<? } else { ?>
								<input maxlength="10" type="text" name="man_<?=$mode->id?>" id="basic" data-clear-btn="true" />
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
				<option value="<?=$user->id?>"><?=$user->first_name?> <?=$user->last_name?></option>
				<? 
			}
			?>
		</select>
		<input type="hidden" name="mov" value="<?=$mov?>" />
		<input type="hidden" name="archive" value="<?=$archive_file?>" />
		<?if(empty($force)) { ?><input type="button" name="save" onClick="validator();" value="SAVE"><? } ?>
	</form>
</div>
</div>