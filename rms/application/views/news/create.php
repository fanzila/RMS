		<a href="/news/" class="ui-btn ui-btn-left"><i class="zmdi zmdi-arrow-back zmd-fw"></i></a>
	</div>
	<div data-role="content" data-theme="a">
		<script>
		function validateForm() {
			var x = document.forms["news"]["title"].value;
			if (x == null || x == "") {
				alert("Title must be filled out");
				return false;
			}
		}
		</script>

			<?php echo validation_errors(); ?>

		<?php 
		$attributes = array('name' => 'news', 'id' => 'news', 'data-ajax' => 'false');
		echo form_open('news/create', $attributes); ?>	

		<label for="title">Title</label>
		<input data-theme="a" class="input" data-form="ui-body-a" type="text" id="title" name="title" data-clear-btn="true" /><br />

		<label for="text">Text</label>
		<textarea id="text" name="text"></textarea><br />

		<label for="bu">BU(s)</label>
        <?php foreach ($bus_list as $bu):?>
            <label class="checkbox" style="background-color: #ffffff">
            <input data-inline="true" type="checkbox" name="bus[]" value="<?php echo $bu->id;?>" <? if($bu_id == $bu->id) echo "checked"; ?>>
            <?php echo htmlspecialchars($bu->name,ENT_QUOTES,'UTF-8');?>
            </label>
        <?php endforeach?>
			
		<input data-ajax="false"  data-theme="a" type="submit" onclick="return validateForm()" name="submit" value="Create & send" />

	</form>
</div>
</div>
