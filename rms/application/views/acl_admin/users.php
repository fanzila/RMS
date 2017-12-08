</div>
<div data-role="content" data-theme="a">
  <h1>Manage Users</h1>
  
  <table cellpadding="10px">
      <thead>
      <tr>
          <th>Username</th>
          <th>&nbsp;</th>
      </tr>
      </thead>
      <tbody>
      <?php foreach($users as $user) : ?>
          <tr>
              <td><?php echo $user->username; ?></td>
              <td>
                  <a href="/acl_admin/manage_user/<?php echo $user->id; ?>" data-ajax="false" data-role="button">Manage User</a>
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