<? $title = "Users"; include('jq_header.php'); ?> 
<body>
	<div data-role="page">
		<div data-role="header">
			<a href="/auth/" data-transition="slide" data-icon="home">Back</a>
			<h1>Users | <?=$bu_name?> | <?=$username?></h1>
		</div>
		<div data-role="content">

<h1>Delete</h1>
<p>Delete: <?php echo $user->username;?></p>


<?php 
$attributes = array('rel' => 'external', 'data-ajax' => 'false');
echo form_open("auth/delete/".$user->id, $attributes);?>

  <p>
  	<?php echo lang('deactivate_confirm_y_label', 'confirm');?>
    <input type="radio" name="confirm" value="yes" checked="checked" />
    <?php echo lang('deactivate_confirm_n_label', 'confirm');?>
    <input type="radio" name="confirm" value="no" />
  </p>

  <?php echo form_hidden($csrf); ?>
  <?php echo form_hidden(array('id'=>$user->id)); ?>

  <p><?php echo form_submit('submit', lang('deactivate_submit_btn'));?></p>

<?php echo form_close();?>	
</div><!-- /content -->
	<br /><br />
	<div id="view"></div>
</div><!-- /page -->
<? include('jq_footer.php'); ?>