<div data-role="page" data-theme="a">
	<div data-role="header">
		<a href="/admin/" data-ajax="false" data-icon="home">Home</a>
			<h1>Create news from: <?=$from?></h1>
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
		$attributes = array('name' => 'news', 'id' => 'news');
		echo form_open('news/create', $attributes); ?>	

		<label for="title">Title</label>
		<input data-theme="a" class="input" data-form="ui-body-a" type="text" id="title" name="title" /><br />

		<label for="text">Text</label>
		<textarea id="text" name="text"></textarea><br />
			
		<input data-ajax="false"  data-theme="a" type="submit" onclick="return validateForm()" name="submit" value="Create & send" />

	</form>
</div>
</div>
