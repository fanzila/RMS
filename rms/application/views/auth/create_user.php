<? $title = "Users"; include('jq_header.php'); ?> 
<body>
	<div data-role="page">
		<div data-role="header">
			<a href="/auth/" data-transition="slide" data-icon="home">Back</a>
			<h1>Users</h1>
		</div>
		<div data-role="content">

<h1><?php echo lang('create_user_heading');?></h1>
<p><?php echo lang('create_user_subheading');?></p>

<div id="infoMessage"><?php echo $message;?></div>

<?php 
$attributes = array('rel' => 'external', 'data-ajax' => 'false');
echo form_open("auth/create_user", $attributes);
?>

      <p>
            <?php echo lang('create_user_fname_label', 'first_name');?> <br />
            <?php echo form_input($first_name);?>
      </p>

      <p>
            <?php echo lang('create_user_lname_label', 'last_name');?> <br />
            <?php echo form_input($last_name);?>
      </p>

      <p>
            <?php echo lang('create_user_email_label', 'email');?> <br />
            <?php echo form_input($email);?>
      </p>

      <p>
            <?php echo lang('create_user_phone_label', 'phone');?> ONLY this format: +336XXXXXXXX <br />
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
	
      <h3><?php echo lang('edit_user_groups_heading');?></h3>
      <?php foreach ($groups as $group):?>
		<? if($group['level'] <= 0) { ?>
          <label class="checkbox">
          <input type="checkbox" name="groups[]" value="<?php echo $group['id'];?>" <? if($group['id'] == 2) echo "checked"; ?>>
          <?php echo htmlspecialchars($group['name'],ENT_QUOTES,'UTF-8');?>
          </label>
		<? } ?>
      <?php endforeach?>

		<h3><?php echo lang('edit_user_bus_heading');?></h3>
        <?php foreach ($bus as $bu):?>
            <label class="checkbox">
            <input type="checkbox" name="bus[]" value="<?php echo $bu['id'];?>">
            <?php echo htmlspecialchars($bu['name'],ENT_QUOTES,'UTF-8');?>
            </label>
        <?php endforeach?>

		<h3>Welcome email</h3>
		<label class="checkbox">
			<input type="checkbox" name="welcome_email" value="1" checked>
			Send welcome email message
		</label>
<textarea name="txtmessage"><?=$welcome_email?></textarea>
		
      <p><?php echo form_submit('submit', lang('create_user_submit_btn'));?></p>

<?php echo form_close();?>
	</div><!-- /content -->
	<br /><br />
	<div id="view"></div>
</div><!-- /page -->
<? include('jq_footer.php'); ?>