		</div>
		<div data-role="content">

<h1><?php echo lang('forgot_password_heading');?></h1>

<div id="infoMessage"><font color="#228b22"><?php echo $message;?></font></div>

Please enter your username (firstname.lastname) or your email so we can send you an email to reset your password.

<?php $attributes = array('rel' => 'external', 'data-ajax' => 'false');
echo form_open("auth/forgot_password", $attributes);?>

      <p>
      	<label for="seek"><?php echo sprintf(lang('forgot_password_email_label'), 'Username or email');?></label> <br />
      	<?php echo form_input($seek);?>
      </p>

      <p><?php echo form_submit('submit', lang('forgot_password_submit_btn'));?></p>

<?php echo form_close();?>
	</div><!-- /content -->
	<br /><br />
	<div id="view"></div>
</div><!-- /page -->