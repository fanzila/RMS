<?php
$checklist_id = property_exists($checklist, 'id') ? $checklist->id : 'create';
$can_edit_tasks = $this->ion_auth_acl->has_permission('admin_panel_checklist_task');
$task_readonly = $can_edit_tasks ? '' : 'readonly disabled';
?>

<?php foreach ($checklist->tasks as $task) {
$bgcolor = $task->active ? '#eceeff' : '#bbbdbd';
?>
  <div data-id="<?= $task->id ?>" data-role="collapsible" data-mini="true" style="background-color: <?= $bgcolor ?>" class="checklist-task">
  <h2>
  ID: <?= $task->id ?> | <?= $task->name ?>
  <?php if (property_exists($task, 'priority') && $task->priority !== null) { ?>
  | <small><?= $priorities[$task->priority] ?></small>
  <?php } ?>
  <?php if ($can_edit_tasks) { ?>
  <span class="sort-icon tasks-sort"><?= $task->order ?>&nbsp;<i class="fa fa-sort"></i></span>
  <?php } ?>
  </h2>
  <div data-role="listview" style="background-color: #fff;">
  <?php require('task_form.php'); ?>
</div>
  </div>
<?php } ?>
