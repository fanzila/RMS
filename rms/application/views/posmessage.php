	</div>
	<div data-role="content">

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
			Message:
			<textarea id="msg" name="msg"></textarea><br />
				<label for="type">TYPE</label>
				<label class="checkbox">
				<input data-inline="true" type="checkbox" name="service" id="service" checked style="background-color: #ffffff">
				Service
				</label>
				<label class="checkbox">
					<input data-inline="true" type="checkbox" name="kitchen" id="kitchen" style="background-color: #ffffff">
					Kitchen
				</label>
			<input data-ajax="false"  data-theme="a" type="submit" onclick="return validateForm()" name="submit" value="Send" />

		</form>

	</div>
</div>