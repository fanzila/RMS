</div>
<div data-role="content" data-theme="a">
  <h1>Manage Groups</h1>
  
  <table cellpadding="10px">
      <thead>
      <tr>
          <th>Name</th>
          <th>&nbsp;</th>
      </tr>
      </thead>
      <tbody>
      <?php foreach($groups as $group) : ?>
          <tr>
              <td><?php echo $group->name; ?></td>
              <td>
                  <a href="/acl_admin/group_permissions/<?php echo $group->id; ?>" data-ajax="false" data-role="button">Manage Permissions</a>
              </td>
          </tr>
      <?php endforeach; ?>
      </tbody>
  </table>
</div>
</div>
<style>
  table {
    border: 1px solid black;
    border-collapse: collapse;
  }
  td {
    border-top: 1px solid black;
    border-bottom: 1px solid black;
  }
</style>