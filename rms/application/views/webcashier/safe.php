	</div>
	<div data-role="content" data-theme="a">

		<form id="pos" name="pos" method="post" action="/webcashier/save">

			<div data-role="fieldcontain">
				Movement: 
				<fieldset data-role="controlgroup" data-type="horizontal">
					<input type="radio" name="mov" id="radio-choice-a" value="safe_in" />
					<label for="radio-choice-a">IN (inside safe)</label>
					<input type="radio" name="mov" id="radio-choice-b" value="safe_out" />
					<label for="radio-choice-b">OUT (outside safe)</label>
				</fieldset>
			</div>

			<table border="0" cellpadding="5" width="100%">
				<tr style="background-color: #fdfff9;">
					<td align="center">Cash amount</td>
					<td align="center">TR num</td>
				</tr>
				<tr style="background-color: #fdfff9;">
					<td><input maxlength="20" type="text" name="man_1" id="man_1" value="0.00" data-clear-btn="true" /></td>
					<td><input maxlength="20" type="text" name="man_3" id="man_3" value="0" data-clear-btn="true" /></td>
				</tr>
			</table>
			
			Comments: <input type="text" name="comment_report" id="comment_report" data-clear-btn="true" />
		<input maxlength="20" type="hidden" name="action" value="safe" data-clear-btn="true" />
		<input type="button" name="save" onClick="validator();" value="SAVE">
	</form>
</div>
</div>