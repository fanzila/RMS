		</div>
		<div data-role="content">

<h1><?php echo lang('forgot_password_heading');?></h1>
<p><?php echo sprintf(lang('forgot_password_subheading'), 'Username');?></p>

<div id="infoMessage"><?php echo $message;?></div>

<?php $attributes = array('rel' => 'external', 'data-ajax' => 'false');
echo form_open("auth/forgot_password", $attributes);?>

      <p>
      	<label for="email"><?php echo sprintf(lang('forgot_password_email_label'), 'Username');?></label> <br />
      	<?php echo form_input($username);?>
      </p>

      <p><?php echo form_submit('submit', lang('forgot_password_submit_btn'));?></p>

<?php echo form_close();?>
	</div><!-- /content -->
	<br /><br />
	<div id="view"></div>
</div><!-- /page -->