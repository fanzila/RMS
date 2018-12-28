		</div>
		<div data-role="content">

<h1><?php echo lang('create_user_heading');?></h1>
<p><?php echo lang('create_user_subheading');?></p>

<div id="infoMessage"><?php echo $message;?></div>
<h1 style="color:red">RAPPEL: FAIRE LA DPAE!</h1>
<?php 
$attributes = array('rel' => 'external', 'data-ajax' => 'false');
echo form_open("auth/create_user", $attributes);
?>
<div class="row">

  <div class="col-xs-12 col-sm-6 col-md-5">
    <div class="box">
      <p>
            <?php echo lang('create_user_fname_label', 'first_name');?>
            <?php echo form_input($first_name);?>
      </p>
      <p>
            <?php echo lang('create_user_lname_label', 'last_name');?>
            <?php echo form_input($last_name);?>
      </p>
      <p>
            <?php echo lang('create_user_email_label', 'email');?>
            <?php echo form_input($email);?>
      </p>
      <p>
            <?php echo lang('create_user_phone_label', 'phone');?> ONLY this format: +336XXXXXXXX
            <?php echo form_input($phone);?>
      </p>
      <p>
            Comments: <br />
            <?php echo form_input($comment);?>
      </p>

	<? /**
      <p>
            <?php echo lang('create_user_password_label', 'password');?> <br />
            <?php echo form_input($password);?>
      </p>

      <p>
            <?php echo lang('create_user_password_confirm_label', 'password_confirm');?> <br />
            <?php echo form_input($password_confirm);?>
      </p>
	**/ ?>
	<td><label for="sdate" id="label">First Shift Date :</label></td>
	<td><input type="text" data-role="date" id="sdate" name="sdate" data-clear-btn="true" /></td>
    </div>
  </div>
  <div class="col-xs-12 col-sm-6 col-md-7">
    <div class="row">
      <div class="col-xs-6 col-sm-6 col-md-6">
        <div class="box">	
          <h3><?php echo lang('edit_user_groups_heading');?></h3>
          <?php foreach ($groups as $group):?>
    		<? if($group['level'] <= 0) { ?>
              <label class="checkbox">
              <input type="checkbox" name="groups[]" value="<?php echo $group['id'];?>" <? if($group['id'] == 2) echo "checked"; ?>>
              <?php echo htmlspecialchars($group['name'],ENT_QUOTES,'UTF-8');?>
              </label>
    		<? } ?>
          <?php endforeach?>
        </div>
      </div>
      <div class="col-xs-6 col-sm-6 col-md-6">
        <div class="box"> 
    		<h3><?php echo lang('edit_user_bus_heading');?></h3>
            <?php foreach ($bus as $bu):?>
                <label class="checkbox">
                <input type="checkbox" name="bus[]" value="<?php echo $bu['id'];?>">
                <?php echo htmlspecialchars($bu['name'],ENT_QUOTES,'UTF-8');?>
                </label>
            <?php endforeach?>
        </div>
      </div>
      <div class="col-xs-12 col-sm-12 col-md-12">
        <div class="box"> 
    		<h3>Welcome email</h3>
    		<label class="checkbox">
    			<input type="checkbox" name="welcome_email" value="1" checked>
    			Send welcome email message
    		</label>
        </div>
      </div>
    </div>
    <div class="col-xs-12 col-sm-12 col-md-12">
        <div class="box"> 
          <textarea name="txtmessage"><?=$welcome_email?></textarea>
        </div>
    </div>
  </div>
</div>
          <h1 style="color:red">RAPPEL: FAIRE LA DPAE!</h1>
          <p><?php echo form_submit('submit', lang('create_user_submit_btn'));?></p>
<?php echo form_close();?>
	</div><!-- /content -->
	<br /><br />
	<div id="view"></div>
</div><!-- /page -->
<script>
	$(document).ready(function() {
	$("#sdate").datepicker({ dateFormat: 'yy-mm-dd' });
	});
</script>