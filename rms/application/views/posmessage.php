<div data-role="page" data-theme="a">
	<div data-role="header">
	<a href="/admin/" data-ajax="false" data-icon="home">Home</a>
		<h1>Message caisse | <?=$bu_name?> | <?=$username?></h1>
	</div>
	<div data-role="content" data-theme="a">

			<script>
			function validateForm() {
				var x = document.forms["form"]["msg"].value;
				if (x == null || x == "") {
					alert("Message must be filled out");
					return false;
				}
			}
			</script>

				<?php echo validation_errors(); ?>

			<?php 
			$attributes = array('name' => 'msg', 'id' => 'form');
			echo form_open('posmessage', $attributes); 
			?>	
			<?if(isset($msgsent)) {?>
			<p style="background-color: #dae3d8; padding: 15px;">Message sent: <?=$msgsent?></p>
			<? } ?>
			<label for="text">Message:</label>
			<textarea id="msg" name="msg"></textarea><br />

			<input data-ajax="false"  data-theme="a" type="submit" onclick="return validateForm()" name="submit" value="Send" />

		</form>

	</div>
</div>