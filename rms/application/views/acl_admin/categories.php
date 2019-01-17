</div>
<div data-role="content" data-theme="a">
  <h1>Manage Categories</h1>

  <div class="button"> 
    <?php echo anchor('/acl_admin/add_category', 'Add Category', 'data-ajax="false" data-role="button"'); ?>
  </div>
  <br>
  <?php if (!empty($categories)): ?>
    <table cellpadding="10px">
        <thead>
        <tr>
            <th>Name</th>
            <th>&nbsp;</th>
            <th>&nbsp;</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach($categories as $category) : ?>
          <? if ($category['id'] != 0) { ?>
            <tr>
                <td><?php echo $category['name']; ?></td>
                <td>
                    <a href="/acl_admin/update_category/<?php echo $category['id']; ?>" data-ajax="false" data-role="button">Edit</a>
                </td>
                <td>
                  <a href="/acl_admin/delete_category/<?php echo $category['id']; ?>" data-ajax='false' data-role='button'>Delete</a>
                </td>
            </tr>
          <? } ?>
        <?php endforeach; ?>
      <? else: echo "No category to display"; endif; ?>
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
  tr {
    border-right: 1px solid black;
  }
</style>