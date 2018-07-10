<?php
$task_id = property_exists($task, 'id') ? $task->id : 'create';
?>

<div class="row">
  <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
    <div class="box">
      <label for="task-name-<?= $task_id ?>">Name:</label>
      <input id="task-name-<?= $task_id ?>" name="task-name-<?= $task_id ?>" type="text" value="<?= stripslashes($task->name) ?>" data-clear-btn="true" <?= $task_readonly ?>/>
    </div>
  </div>
  <div class="col-xs-12 col-sm-6 col-md-4 col-lg-4">
    <div class="box">
      <label for="task-priority-<?= $task_id ?>">Priority:</label>
      <select id="task-priority-<?= $task_id ?>" name="task-priority-<?= $task_id ?>" <?= $task_readonly ?>>
        <?php foreach ($priorities as $index => $priority) { ?>
          <option value="<?= $index ?>"<?php if ($index === $task->priority) echo 'selected'; ?>>
            <?= $priority ?>
          </option>
        <?php } ?>
      </select>
    </div>
  </div>
  <div class="col-xs-12 col-sm-6 col-md-4 col-lg-4">
    <div class="box">
      <label for="task-active-<?= $task_id ?>">Active: (on or off)</label>
      <select id="task-active-<?= $task_id ?>" name="task-active-<?= $task_id ?>" <?= $task_readonly ?>>
        <option value="1" <? if($task->active == 1) echo "selected"; ?>>Yes</option>
        <option value="0" <? if($task->active == 0) echo "selected"; ?>>No</option>
      </select>
    </div>
  </div>
</div>

<div class="row">
  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
    <div class="box">
      <label for="task-comment-<?= $task_id ?>">Comment:</label>
      <textarea id="task-comment-<?= $task_id ?>" rows="8"
        name="task-comment-<?= $task_id ?>" <?= $task_readonly ?>
        ><?= stripslashes($task->comment) ?></textarea>
    </div>
  </div>
</div>

<div class="row">
  <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
    <div class="box">
      <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
          <strong>Week days:</strong>
        </div>
        <?php foreach ($day_week_num as $value => $display) { ?>
          <div class="col-xs-12 col-sm-6 col-md-4 col-lg-3">
            <input type="checkbox" value="<?= $value ?>"
              name="task-day_week_num[]-<?= $task_id ?>"
              id="task-day-week-num[]-<?= $task_id ?>-<?= $value ?>"
              <?php if (in_array($value, $task->day_week_num)) echo 'checked'; ?>>
            <label for="task-day-week-num[]-<?= $task_id ?>-<?= $value ?>"><?= $display ?></label>
          </div>
        <?php } ?>
      </div>
    </div>
  </div>
  <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
    <div class="box">
      <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
          <strong>Month days:</strong>
        </div>
        <?php for ($i = 1 ; $i <= 28 ; $i++) { ?>
          <div class="col-xs-4 col-sm-3 col-md-2 col-lg-2">
            <input type="checkbox" value="<?= $i ?>"
              name="task-day_month_num[]-<?= $task_id ?>"
              id="task-day-month-num[]-<?= $task_id ?>-<?= $i ?>"
              <?php if (in_array('' . $i, $task->day_month_num)) echo 'checked'; ?>>
            <label for="task-day-month-num[]-<?= $task_id ?>-<?= $i ?>"><?= $i ?></label>
          </div>
        <?php } ?>
      </div>
    </div>
  </div>
</div>

<?php if ($task_id !== 'create') { ?>
  <input type="hidden" name="task-order-<?= $task_id ?>" value="<?= $task->order ?>" class="task-order">
  <input type="hidden" name="task-id-<?= $task_id ?>" value="<?= $task_id ?>">
<?php } else { ?>
  <div class="row">
    <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
      <div class="box">
      <select name="task-id_checklist-<?= $checklist_id ?>" required>
          <option value="">Checklist</option>
          <?php foreach ($checklists as $checklist) { ?>
            <option value="<?= $checklist->id ?>"><?= $checklist->id ?>: <?= $checklist->name ?></option>
          <?php } ?>
        </select>
      </div>
    </div>
    <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
      <div class="box">
        <input type="submit" value="Save"/>
      </div>
    </div>
  </div>
  <input type="hidden" name="task-order-<?= $task_id ?>" value="-1">
<?php } ?>
