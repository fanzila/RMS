<div data-role="page" data-theme="a">
	<div data-role="header">
		<? if(!$keylogin) { ?><a href="/admin/" data-role="button" data-inline="true" data-ajax="false" data-icon="home">Home</a><? } ?>
		<a href="/pos/" data-ajax="false" data-icon="home">Back</a>
		<h1><?=$title?></h1>
	</div>
	<div data-role="content" data-theme="a">

		<form id="pos" name="pos" method="post" action="/pos/save">

			<div data-role="fieldcontain">
				Mouvement: 
				<fieldset data-role="controlgroup" data-type="horizontal">
					<input type="radio" name="mov" id="radio-choice-a" value="safe_in" />
					<label for="radio-choice-a">IN (rentrer dans le coffre)</label>
					<input type="radio" name="mov" id="radio-choice-b" value="safe_out" />
					<label for="radio-choice-b">OUT (sortir du coffre)</label>
				</fieldset>
			</div>

			<table border="0" cellpadding="5" width="100%">
				<tr style="background-color: #fdfff9;">
					<td align="center">Montant Cash</td>
					<td align="center">Nombre TR</td>
				</tr>
				<tr style="background-color: #fdfff9;">
					<td><input maxlength="20" type="text" name="man_1" id="man_1" value="0.00" /></td>
					<td><input maxlength="20" type="text" name="man_3" id="man_3" value="0" /></td>
				</tr>
			</table>
			
			Comments: <input type="text" name="comment" id="comment"  />

			<select style="background-color:#a1ff7c" name="user" id="user" data-inline="true" data-theme="a" required>
				<option value="0">User</option>
				<?
			foreach ($users as $user) {
				?>
				<option value="<?=$user['id']?>" <? if(isset($form['user']) AND $form['user']==$user['id']) { ?> selected <? } ?>><?=$user['first_name']?> <?=$user['last_name']?></option>
				<? 
			}
			?>
		</select>
		<input maxlength="20" type="hidden" name="action" value="safe" />
		<input type="button" name="save" onClick="validator();" value="SAVE">
	</form>
</div>
</div>