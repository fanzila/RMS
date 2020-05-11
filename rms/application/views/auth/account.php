		</div>
		<div data-role="content">

<h1>Change my infos</h1>
<p>Please change what need to be changed.</p>

<div id="infoMessage"><h3><font color="#4a7b50"><?php echo $message;?></font></h3></div>

<?php
$attributes = array('rel' => 'external', 'data-ajax' => 'false');
echo form_open(uri_string(), $attributes);
?>
<div class="row">

  <div class="col-xs-12 col-sm-6 col-md-5">
    <div class="box">
      <p>
            <?php echo lang('edit_user_phone_label', 'phone');?>
            <?php echo form_input($phone);?>
      </p>

      <p>
            <?php echo lang('edit_user_email_label', 'email');?>
            <?php echo form_input($email);?>
      </p>

      <p>
            <?php echo lang('edit_user_password_label', 'password');?>
            <?php echo form_input($password);?>
      </p>

      <p>
            <?php echo lang('edit_user_password_confirm_label', 'password_confirm');?>
            <?php echo form_input($password_confirm);?>
      </p>
          <?php echo form_hidden('id', $user->id);?>
          <?php echo form_hidden($csrf); ?>
      <p><?php echo form_submit('submit', lang('edit_user_submit_btn'));?></p>
    </div>
  </div>
</div>
  <?php echo form_close();?>