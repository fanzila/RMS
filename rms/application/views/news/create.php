		<a href="/news/" class="ui-btn ui-btn-left"><i class="zmdi zmdi-arrow-back zmd-fw"></i></a>
	</div>
	<div data-role="content" data-theme="a">
		<script>
		function validateForm() {
			var title = document.forms["news"]["title"].value;
			var content = document.forms["news"]["text"].value;
			if (title == null || title == "") {
				alert("Title must be filled out");
				return false;
			}
			if (content == null || content == "") {
				alert("Text field must be filled out");
			}
		}
		</script>

			<?php echo validation_errors(); ?>
			<?php echo $error;?>
			

		<?php 
		$attributes = array('name' => 'news', 'id' => 'news', 'data-ajax' => 'false');
		echo form_open_multipart('news/create', $attributes); ?>	

		<label for="title">Title</label>
		<input data-theme="a" class="input" data-form="ui-body-a" type="text" id="title" name="title" data-clear-btn="true" /><br />

		<label for="text">Text</label>
		<textarea id="text" name="text" required></textarea><br />

		<label for="title">Picture (not needed) (Size max : 2 Mo)</label>
		<input id="userfile" type="file" name="userfile" size="20" data-clear-btn="true" accept="image/*" /><br/>

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
<div class="preview" id="news-preview">
	<h3>PREVIEW</h3>
	<ul data-role="listview" data-inset="true">
		<li data-role="list-divider" id="titlebar"><span id="title-prev"></span> - </li>
		<hr></hr>
		<li data-filtertext="">
			<div class="img-content-prev">
				<img id="img-prev" class="img-responsive" style="display: none;" />
			</div>
			<br/>
			<div class="text-content-prev">
			<p id="text-prev"><font color="#000"></font></p>
			</div>
		</li>
	</ul>
</div>
</div>
<script type="text/javascript">

	function dateNowString() {	
		var date = new Date();
		var dateISO = date.toISOString();
		var regExp = new RegExp('\\..*');
		dateISO = dateISO.replace('T', ' ');
		dateISO = dateISO.replace(regExp, '');
		return (dateISO);
	}
	
	var user = <?=json_encode($user);?>;
	
	$('#titlebar').append(dateNowString() + ' | ' + user);
	
	if ($('#title-prev').text() == '') {
		$('#title-prev').text('[title]');
	}
	
	$('#title').keyup(function (){
		var content = $('#title').val();
		var titlePrev = $('#title-prev');
		titlePrev.text(content);
		if ($('#title-prev').text() == '') {
			$('#title-prev').text('[title]');
		}
	});
	
	$('#text').keyup(function (){
		var text = $('#text').val();
		var textPrev = $('#text-prev');
		textPrev.html(text);
	});
	
	$('#userfile').change(function (){
		var reader = new FileReader();
		
		reader.onload = function (e) {
			$('#img-prev').attr('src', e.target.result);
			$('#img-prev').removeAttr('style');
			$('#img-prev').attr('style', 'max-height:300px; max-width: 300px;');
		};
		
		reader.readAsDataURL(this.files[0]);
		
	});
	
	$.valHooks.textarea = {
    get: function(elem) {
        return elem.value.replace(/\r?\n/g, "<br/	>");
    }
};
	
</script>
