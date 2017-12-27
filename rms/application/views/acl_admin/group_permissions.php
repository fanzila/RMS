</div>
<div data-role="content" data-theme="a">
  <h1>Manage Group Permissions (Name: <?=$group_name?>)</h1>

  <?php echo form_open('', 'data-ajax="false"'); ?>
      <?php if($permissions) : ?>
          <?php foreach($permissions as $k => $category) : ?>
            <div class="category" data-role="collapsible">
              <h2><?=$category['name']?></h2>
              <div class="container">
                <div class="row">
                  <div class="col-md-5">
                    <span>Name</span>
                  </div>
                  <div class="col-md-3">
                  </div>
                  <div class="col-sm-1">
                    <span>Allow</span>
                  </div>
                  <div class="col-sm-1">
                    <span>Deny</span>
                  </div>
                  <div class="col-sm-1">
                    <span>Ignore</span>
                  </div>
                </div>
                <div class="row">
                  &nbsp;
                </div>
              <?php foreach($category['permissions'] as $v): ?>
                <div class="row">
                  <div class="col-md-5">
                    <?php echo $v['name']; ?>
                  </div>
                  <div class="col-md-3">
                  </div>
                  <div class="col-sm-1">
                    <?php echo form_radio("perm_{$v['id']}", '1', set_radio("perm_{$v['id']}", '1', ( array_key_exists($v['key'], $group_permissions) && $group_permissions[$v['key']]['value'] === TRUE ) ? TRUE : FALSE)); ?>
                  </div>
                  <div class="col-sm-1">
                    <?php echo form_radio("perm_{$v['id']}", '0', set_radio("perm_{$v['id']}", '0', ( array_key_exists($v['key'], $group_permissions) && $group_permissions[$v['key']]['value'] != TRUE ) ? TRUE : FALSE)); ?>
                  </div>
                  <div class="col-sm-1">
                    <?php echo form_radio("perm_{$v['id']}", 'X', set_radio("perm_{$v['id']}", 'X', ( ! array_key_exists($v['key'], $group_permissions) ) ? TRUE : FALSE)); ?>
                  </div>
                </div>
              <?endforeach; ?>
            </div>
          </div>
          <?php endforeach; ?>
      <?php else: ?>
              There are currently no permissions to manage, please add some permissions
      <?php endif; ?>
  <p>
      <?php echo form_submit('save', 'Save'); ?>
      <?php echo form_submit('cancel', 'Cancel');?>
  </p>

  <?php echo form_close(); ?>
</div>
</div>