<? $title = "Admin"; include('jq_header.php'); ?> 
<body>
	<div data-role="page">
		<div data-role="header">
			
			<h1>Admin</h1>
		</div>
		<div data-role="content">

<h1><?php echo lang('login_heading');?></h1>
<p><?php echo lang('login_subheading');?></p>

<div id="infoMessage"><?php echo $message;?></div>

<form action="/auth/login" method="post" accept-charset="utf-8" rel="external" data-ajax="false">

  <p>
    <?php echo lang('login_identity_label', 'identity');?>
    <?php echo form_input($identity);?>
  </p>

  <p>
    <?php echo lang('login_password_label', 'password');?>
    <?php echo form_input($password);?>
  </p>

  <p>
    <?php echo lang('login_remember_label', 'remember');?>
    <?php echo form_checkbox('remember', '1', TRUE, 'id="remember"');?>
  </p>


  <p><?php echo form_submit('submit', lang('login_submit_btn'));?></p>

<?php echo form_close();?>

<a href="/auth/forgot_password" data-role="button" data-inline="true"><?php echo lang('login_forgot_password');?></a>

	</div><!-- /content -->
	<br /><br />
	<div id="view"></div>
</div><!-- /page -->
<? include('jq_footer.php'); ?>