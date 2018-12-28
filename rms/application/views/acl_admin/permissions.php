</div>
<div data-role="content" data-theme="a">
  <h1>Manage Permissions</h1>

    <div class="button"> 
      <?php echo anchor('/acl_admin/add_permission', 'Add Permission', 'data-ajax="false" data-role="button"'); ?>
    </div>
    <br>
    <a href="#" data-ajax="false" data-role="button" style="width:200px; margin-top: 50px; margin-bottom: 50px;" onclick="location.reload()">Reload data</a>
    <? foreach ($categories as $category) { ?>
    <div class="category" data-role="collapsible">
      <h3><?=ucfirst($category['name'])?></h3>
      <? if (!empty($category['permissions'])) {?>
        <table cellpadding='10px'>
            <thead>
                <tr>
                    <th>Key</th>
                    <th>Name</th>
                    <th>&nbsp;</th>
                    <th>&nbsp;</th>
                    <th>&nbsp;</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($category['permissions'] as $permission) : ?>
                <tr>
                    <td><?php echo $permission['key']; ?></td>
                    <td><?php echo $permission['name']; ?></td>
                    <td>
                        <a href="/acl_admin/update_permission/<?php echo $permission['id']; ?>" data-ajax='false' data-role='button'>Edit</a>
                    </td>
                    <td>
                      <a href="/acl_admin/delete_permission/<?php echo $permission['id']; ?>" data-ajax='false' data-role='button'>Delete</a>
                    </td>
                    <td>
                      <select id="select_<?=$permission['id']?>" name="category" default="category" onchange="selectCat(<?=$permission['id']?>, '<?=$permission['name']?>')">
                        <?foreach ($categories as $category_select) { ?>
                          <option value="<?=$category_select['id']?>" <?if ($permission['id_category'] == $category_select['id']) echo "selected";?>><?=ucfirst($category_select['name'])?></option>
                        <? } ?>
                      </select>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
      <? } else { echo "No permissions in this category";} ?>
      </div>
    <? } ?>
    <script>
      function selectCat(perm_id, perm_name) {
        var select = $('#select_' + perm_id);
        var id_cat = select.val();
        var url = '/acl_admin/add_permission_to_category/' + perm_id + '/' + id_cat;
        console.log(url);
        
        var get = $.get(url, function(data) {
          alert(data['response']);
        }, 'json');
      }
    </script>
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