</div>
<div data-role="content" data-theme="a">
  <h1>Edit category</h1>

  <div id="infoMessage"><p style="color: red;"><?php echo $message;?></p></div>

  <?php echo form_open('', 'data-ajax="false"');?>

  <p>
      <?php echo form_label('Key:', 'cat_key');?> <br />
      <?php echo form_input('cat_key', set_value('cat_key', $category['key'])); ?> <br />
      <?php echo form_error('cat_key'); ?>
  </p>

  <p>
      <?php echo form_label('Name:', 'cat_name');?> <br />
      <?php echo form_input('cat_name', set_value('cat_name', $category['name'])); ?> <br />
      <?php echo form_error('cat_name'); ?>
  </p>

  <p>
      <?php echo form_submit('submit', 'Save');?>
      <?php echo form_submit('cancel', 'Cancel');?>
  </p>

  <?php echo form_close();?>
</div>
</div>