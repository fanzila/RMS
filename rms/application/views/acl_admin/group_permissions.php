</div>
<div data-role="content" data-theme="a">
  <h1>Manage Group Permissions (Name: <?=$group_name?>)</h1>

  <?php echo form_open('', 'data-ajax="false"'); ?>
      <?php if($permissions) : ?>
          <?php foreach($permissions as $k => $category) : ?>
            <div id="category_<?=$category['id']?>" data-role="collapsible">
              <h2><?=$category['name']?></h2>
              <div class="container">
                <div class="row">
                  <div class="col-md-5" style="display: flex; flex-flow: row wrap; justify-content: center;">
                    <span><b>Name</b></span>
                  </div>
                  <div class="col-md" style="display: flex; flex-flow: row wrap; justify-content: center;">
                  </div>
                  <div class="col-sm-2" style="display: flex; flex-flow: row wrap; justify-content: center;">
                    <span><b>Allow</b></span>
                  </div>
                  <div class="col-sm-2" style="display: flex; flex-flow: row wrap; justify-content: center;">
                    <span><b>Deny</b></span>
                  </div>
                  <div class="col-sm-2" style="display: flex; flex-flow: row wrap; justify-content: center;">
                    <span><b>Ignore</b></span>
                  </div>
                </div>
                <hr></hr>
              <?php foreach($category['permissions'] as $v): ?>
                <div class="row" id="permissions_cat_<?=$category['id']?>">
                  <div class="col-md-5">
                    <?php echo $v['name']; ?>
                  </div>
                  <div class="col-md" style="display: flex; flex-flow: row wrap; justify-content: center;">
                  </div>
                  <div class="col-sm-2" style="display: flex; flex-flow: row wrap; justify-content: center;">
                    <?php echo form_radio("perm_{$v['id']}", '1', set_radio("perm_{$v['id']}", '1', ( array_key_exists($v['key'], $group_permissions) && $group_permissions[$v['key']]['value'] === TRUE ) ? TRUE : FALSE)); ?>
                  </div>
                  <div class="col-sm-2" style="display: flex; flex-flow: row wrap; justify-content: center;">
                    <?php echo form_radio("perm_{$v['id']}", '0', set_radio("perm_{$v['id']}", '0', ( array_key_exists($v['key'], $group_permissions) && $group_permissions[$v['key']]['value'] != TRUE ) ? TRUE : FALSE)); ?>
                  </div>
                  <div class="col-sm-2" style="display: flex; flex-flow: row wrap; justify-content: center;">
                    <?php echo form_radio("perm_{$v['id']}", 'X', set_radio("perm_{$v['id']}", 'X', ( ! array_key_exists($v['key'], $group_permissions) ) ? TRUE : FALSE)); ?>
                  </div>
                </div>
              <?endforeach; ?>
              <div class="row">
                &nbsp;
              </div>
              <div class="row">
                <div class="col-md-5">
                </div>
                <div class="col-md">
                </div>
                <div class="col-sm-2" style="display: flex; flex-flow: row wrap; justify-content: center;">
                  <a data-role="button" href="#" onclick="allow_all(<?=$category['id']?>)" data-mini="true" data-ajax="false">Allow All</a>
                </div>
                <div class="col-sm-2" style="display: flex; flex-flow: row wrap; justify-content: center;">
                  <a data-role="button" href="#" onclick="deny_all(<?=$category['id']?>)" data-mini="true" data-ajax="false">Deny All</a>
                </div>
                <div class="col-sm-2" style="display: flex; flex-flow: row wrap; justify-content: center;">
                  <a data-role="button" href="#" onclick="ignore_all(<?=$category['id']?>)" data-mini="true" data-ajax="false">Ignore All</a>
                </div>
              </div>
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
<script type="text/javascript">

  function allow_all(category_id) {
    var permissions = $('#category_' + category_id);
    var inputs = permissions.find(':radio');
    inputs.each(function(index) {
      if ($(this).prop('value') === "1") {
        $(this).prop('checked', true);
      }
    });
  }
  
  function deny_all(category_id) {
    var permissions = $('#category_' + category_id);
    var inputs = permissions.find(':radio');
    inputs.each(function(index) {
      if ($(this).prop('value') === "0") {
        $(this).prop('checked', true);
      }
    });
  }
  
  function ignore_all(category_id) {
    var permissions = $('#category_' + category_id);
    var inputs = permissions.find(':radio');
    inputs.each(function(index) {
      if ($(this).prop('value') === "X") {
        $(this).prop('checked', true);
      }
    });
  }
</script>