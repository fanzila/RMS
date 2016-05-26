<? $title = "Users"; include('jq_header.php'); ?> 
<body>
	<div data-role="page">
		<div data-role="header">
			<a href="/admin/" data-transition="slide" data-icon="home">Home</a>
			<h1>Users | <?=$bu_name?> | <?=$username?></h1>
		</div>
		<div data-role="content">

<h1><?php echo lang('reset_password_heading');?></h1>

<div id="infoMessage"><?php echo $message;?></div>

<?php $attributes = array('rel' => 'external', 'data-ajax' => 'false');
echo form_open('auth/reset_password/' . $code, $attributes);?>

	<p>
		<label for="new_password"><?php echo sprintf(lang('reset_password_new_password_label'), $min_password_length);?></label> <br />
		<?php echo form_input($new_password);?>
	</p>

	<p>
		<?php echo lang('reset_password_new_password_confirm_label', 'new_password_confirm');?> <br />
		<?php echo form_input($new_password_confirm);?>
	</p>

	<?php echo form_input($user_id);?>
	<?php echo form_hidden($csrf); ?>

	<p><?php echo form_submit('submit', lang('reset_password_submit_btn'));?></p>

<?php echo form_close();?>
	</div><!-- /content -->
	<br /><br />
	<div id="view"></div>
</div><!-- /page -->
<? include('jq_footer.php'); ?>