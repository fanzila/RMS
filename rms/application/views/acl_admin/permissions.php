</div>
<div data-role="content" data-theme="a">
  <h1>Manage Permissions</h1>

    <div class="button"> 
      <?php echo anchor('/acl_admin/add_permission', 'Add Permission', 'data-ajax="false" data-role="button"'); ?>
    </div>
    <br>
  <table cellpadding='10px'>
      <thead>
          <tr>
              <th>Key</th>
              <th>Name</th>
          </tr>
      </thead>
      <tbody>
          <?php foreach($permissions as $permission) : ?>
          <tr>
              <td><?php echo $permission['key']; ?></td>
              <td><?php echo $permission['name']; ?></td>
              <td>
                  <a href="/acl_admin/update_permission/<?php echo $permission['id']; ?>" data-ajax='false' data-role='button'>Edit</a>
              </td>
              <td>
                <a href="/acl_admin/delete_permission/<?php echo $permission['id']; ?>" data-ajax='false' data-role='button'>Delete</a>
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