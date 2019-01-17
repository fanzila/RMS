		</div>
		<div data-role="content">

<h1><?php echo lang('create_group_heading');?></h1>
<p><?php echo lang('create_group_subheading');?></p>

<div id="infoMessage"><?php echo $message;?></div>

<?php $attributes = array('rel' => 'external', 'data-ajax' => 'false');
echo form_open("auth/create_group", $attributes);
?>

      <p>
            <?php echo lang('create_group_name_label', 'group_name');?>
            <?php echo form_input($group_name);?>
      </p>

      <p>
            <?php echo lang('create_group_desc_label', 'description');?>
            <?php echo form_input($description);?>
      </p>

      <p><?php echo form_submit('submit', lang('create_group_submit_btn'));?></p>

<?php echo form_close();?>
	</div><!-- /content -->
	<br /><br />
	<div id="view"></div>
</div><!-- /page -->