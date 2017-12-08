</div>
<div data-role="content" data-theme="a">
  <h1>Manage</h1>

      <?php echo anchor('/acl_admin/permissions', "Manage Permissions", "data-ajax='false' data-role='button'"); ?>
      <?php echo anchor('/acl_admin/groups', "Manage Groups Permissions", "data-ajax='false' data-role='button'"); ?>
      <?php if (isset($user_manage)) echo anchor('/acl_admin/users', "Manage Users", "data-ajax='false' data-role='button'"); ?>
</div>
</div>