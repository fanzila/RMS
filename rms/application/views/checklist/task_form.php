<?php
$task_id = property_exists($task, 'id') ? $task->id : 'create';
?>

<div class="row">
  <div class="col-xs-6 col-sm-12 col-md-4 col-lg-4">
    <div class="box">
      <label for="task-name-<?= $task_id ?>">Name:</label>
      <input id="task-name-<?= $task_id ?>" name="task-name-<?= $task_id ?>" type="text" value="<?= stripslashes($task->name) ?>" data-clear-btn="true" <?= $task_readonly ?>/>
    </div>
  </div>
  <div class="col-xs-6 col-sm-5 col-md-4 col-lg-4">
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
  <div class="col-xs-1 col-sm-2 col-md-1 col-lg-1">
    <div class="box">
      <label for="task-color-<?= $task_id ?>">Color:</label>
      <input type="hidden" name="task-color-<?= $task_id ?>" class="color-picker" value="<?= $task->color ?>">
    </div>
  </div>
  <div class="col-xs-11 col-sm-5 col-md-3 col-lg-3">
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
<!-- FIX MEE
<div class="row">
  <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
    <div class="box">
      <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
          <strong>Week days:</strong>
        </div>
        <div class="col-xs-12 col-sm-6 col-md-4 col-lg-3">
          <input type="checkbox" value="0"
            name="task-day_week_num[]-<?= $task_id ?>"
            id="task-day-week-num[]-<?= $task_id ?>-0"
            <?php if (in_array('0', $task->day_week_num)) echo 'checked'; ?>>
          <label for="task-day-week-num[]-<?= $task_id ?>-0">Monday</label>
        </div>
        <div class="col-xs-12 col-sm-6 col-md-4 col-lg-3">
          <input type="checkbox" value="1"
            name="task-day_week_num[]-<?= $task_id ?>"
            id="task-day-week-num[]-<?= $task_id ?>-1"
            <?php if (in_array('1', $task->day_week_num)) echo 'checked'; ?>>
          <label for="task-day-week-num[]-<?= $task_id ?>-1">Tuesday</label>
        </div>
        <div class="col-xs-12 col-sm-6 col-md-4 col-lg-3">
          <input type="checkbox" value="2"
            name="task-day_week_num[]-<?= $task_id ?>"
            id="task-day-week-num[]-<?= $task_id ?>-2"
            <?php if (in_array('2', $task->day_week_num)) echo 'checked'; ?>>
          <label for="task-day-week-num[]-<?= $task_id ?>-2">Wednesday</label>
        </div>
        <div class="col-xs-12 col-sm-6 col-md-4 col-lg-3">
          <input type="checkbox" value="3"
            name="task-day_week_num[]-<?= $task_id ?>"
            id="task-day-week-num[]-<?= $task_id ?>-3"
            <?php if (in_array('3', $task->day_week_num)) echo 'checked'; ?>>
          <label for="task-day-week-num[]-<?= $task_id ?>-3">Thursday</label>
        </div>
        <div class="col-xs-12 col-sm-6 col-md-4 col-lg-3">
          <input type="checkbox" value="4"
            name="task-day_week_num[]-<?= $task_id ?>"
            id="task-day-week-num[]-<?= $task_id ?>-4"
            <?php if (in_array('4', $task->day_week_num)) echo 'checked'; ?>>
          <label for="task-day-week-num[]-<?= $task_id ?>-4">Friday</label>
        </div>
        <div class="col-xs-12 col-sm-6 col-md-4 col-lg-3">
          <input type="checkbox" value="5"
            name="task-day_week_num[]-<?= $task_id ?>"
            id="task-day-week-num[]-<?= $task_id ?>-5"
            <?php if (in_array('5', $task->day_week_num)) echo 'checked'; ?>>
          <label for="task-day-week-num[]-<?= $task_id ?>-5">Saturday</label>
        </div>
        <div class="col-xs-12 col-sm-6 col-md-4 col-lg-3">
          <input type="checkbox" value="6"
            name="task-day_week_num[]-<?= $task_id ?>"
            id="task-day-week-num[]-<?= $task_id ?>-6"
            <?php if (in_array('6', $task->day_week_num)) echo 'checked'; ?>>
          <label for="task-day-week-num[]-<?= $task_id ?>-6">Sunday</label>
        </div>
      </div>
    </div>
  </div>

  <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
    <div class="box">
      <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
          <strong>Month days:</strong>
        </div>
        <div class="col-xs-4 col-sm-3 col-md-2 col-lg-2">
          <input type="checkbox" value="1"
            name="task-day_month_num[]-<?= $task_id ?>"
            id="task-day-month-num[]-<?= $task_id ?>-1"
            <?php if (in_array('1', $task->day_month_num)) echo 'checked'; ?>>
          <label for="task-day-month-num[]-<?= $task_id ?>-1">1</label>
        </div>
        <div class="col-xs-4 col-sm-3 col-md-2 col-lg-2">
          <input type="checkbox" value="2"
            name="task-day_month_num[]-<?= $task_id ?>"
            id="task-day-month-num[]-<?= $task_id ?>-2"
            <?php if (in_array('2', $task->day_month_num)) echo 'checked'; ?>>
          <label for="task-day-month-num[]-<?= $task_id ?>-2">2</label>
        </div>
        <div class="col-xs-4 col-sm-3 col-md-2 col-lg-2">
          <input type="checkbox" value="3"
            name="task-day_month_num[]-<?= $task_id ?>"
            id="task-day-month-num[]-<?= $task_id ?>-3"
            <?php if (in_array('3', $task->day_month_num)) echo 'checked'; ?>>
          <label for="task-day-month-num[]-<?= $task_id ?>-3">3</label>
        </div>
        <div class="col-xs-4 col-sm-3 col-md-2 col-lg-2">
          <input type="checkbox" value="4"
            name="task-day_month_num[]-<?= $task_id ?>"
            id="task-day-month-num[]-<?= $task_id ?>-4"
            <?php if (in_array('4', $task->day_month_num)) echo 'checked'; ?>>
          <label for="task-day-month-num[]-<?= $task_id ?>-4">4</label>
        </div>
        <div class="col-xs-4 col-sm-3 col-md-2 col-lg-2">
          <input type="checkbox" value="5"
            name="task-day_month_num[]-<?= $task_id ?>"
            id="task-day-month-num[]-<?= $task_id ?>-5"
            <?php if (in_array('5', $task->day_month_num)) echo 'checked'; ?>>
          <label for="task-day-month-num[]-<?= $task_id ?>-5">5</label>
        </div>
        <div class="col-xs-4 col-sm-3 col-md-2 col-lg-2">
          <input type="checkbox" value="6"
            name="task-day_month_num[]-<?= $task_id ?>"
            id="task-day-month-num[]-<?= $task_id ?>-6"
            <?php if (in_array('6', $task->day_month_num)) echo 'checked'; ?>>
          <label for="task-day-month-num[]-<?= $task_id ?>-6">6</label>
        </div>
        <div class="col-xs-4 col-sm-3 col-md-2 col-lg-2">
          <input type="checkbox" value="7"
            name="task-day_month_num[]-<?= $task_id ?>"
            id="task-day-month-num[]-<?= $task_id ?>-7"
            <?php if (in_array('7', $task->day_month_num)) echo 'checked'; ?>>
          <label for="task-day-month-num[]-<?= $task_id ?>-7">7</label>
        </div>
        <div class="col-xs-4 col-sm-3 col-md-2 col-lg-2">
          <input type="checkbox" value="8"
            name="task-day_month_num[]-<?= $task_id ?>"
            id="task-day-month-num[]-<?= $task_id ?>-8"
            <?php if (in_array('8', $task->day_month_num)) echo 'checked'; ?>>
          <label for="task-day-month-num[]-<?= $task_id ?>-8">8</label>
        </div>
        <div class="col-xs-4 col-sm-3 col-md-2 col-lg-2">
          <input type="checkbox" value="9"
            name="task-day_month_num[]-<?= $task_id ?>"
            id="task-day-month-num[]-<?= $task_id ?>-9"
            <?php if (in_array('9', $task->day_month_num)) echo 'checked'; ?>>
          <label for="task-day-month-num[]-<?= $task_id ?>-9">9</label>
        </div>
        <div class="col-xs-4 col-sm-3 col-md-2 col-lg-2">
          <input type="checkbox" value="10"
            name="task-day_month_num[]-<?= $task_id ?>"
            id="task-day-month-num[]-<?= $task_id ?>-10"
            <?php if (in_array('10', $task->day_month_num)) echo 'checked'; ?>>
          <label for="task-day-month-num[]-<?= $task_id ?>-10">10</label>
        </div>
        <div class="col-xs-4 col-sm-3 col-md-2 col-lg-2">
          <input type="checkbox" value="11"
            name="task-day_month_num[]-<?= $task_id ?>"
            id="task-day-month-num[]-<?= $task_id ?>-11"
            <?php if (in_array('11', $task->day_month_num)) echo 'checked'; ?>>
          <label for="task-day-month-num[]-<?= $task_id ?>-11">11</label>
        </div>
        <div class="col-xs-4 col-sm-3 col-md-2 col-lg-2">
          <input type="checkbox" value="12"
            name="task-day_month_num[]-<?= $task_id ?>"
            id="task-day-month-num[]-<?= $task_id ?>-12"
            <?php if (in_array('12', $task->day_month_num)) echo 'checked'; ?>>
          <label for="task-day-month-num[]-<?= $task_id ?>-12">12</label>
        </div>
        <div class="col-xs-4 col-sm-3 col-md-2 col-lg-2">
          <input type="checkbox" value="13"
            name="task-day_month_num[]-<?= $task_id ?>"
            id="task-day-month-num[]-<?= $task_id ?>-13"
            <?php if (in_array('13', $task->day_month_num)) echo 'checked'; ?>>
          <label for="task-day-month-num[]-<?= $task_id ?>-13">13</label>
        </div>
        <div class="col-xs-4 col-sm-3 col-md-2 col-lg-2">
          <input type="checkbox" value="14"
            name="task-day_month_num[]-<?= $task_id ?>"
            id="task-day-month-num[]-<?= $task_id ?>-14"
            <?php if (in_array('14', $task->day_month_num)) echo 'checked'; ?>>
          <label for="task-day-month-num[]-<?= $task_id ?>-14">14</label>
        </div>
        <div class="col-xs-4 col-sm-3 col-md-2 col-lg-2">
          <input type="checkbox" value="15"
            name="task-day_month_num[]-<?= $task_id ?>"
            id="task-day-month-num[]-<?= $task_id ?>-15"
            <?php if (in_array('15', $task->day_month_num)) echo 'checked'; ?>>
          <label for="task-day-month-num[]-<?= $task_id ?>-15">15</label>
        </div>
        <div class="col-xs-4 col-sm-3 col-md-2 col-lg-2">
          <input type="checkbox" value="16"
            name="task-day_month_num[]-<?= $task_id ?>"
            id="task-day-month-num[]-<?= $task_id ?>-16"
            <?php if (in_array('16', $task->day_month_num)) echo 'checked'; ?>>
          <label for="task-day-month-num[]-<?= $task_id ?>-16">16</label>
        </div>
        <div class="col-xs-4 col-sm-3 col-md-2 col-lg-2">
          <input type="checkbox" value="17"
            name="task-day_month_num[]-<?= $task_id ?>"
            id="task-day-month-num[]-<?= $task_id ?>-17"
            <?php if (in_array('17', $task->day_month_num)) echo 'checked'; ?>>
          <label for="task-day-month-num[]-<?= $task_id ?>-17">17</label>
        </div>
        <div class="col-xs-4 col-sm-3 col-md-2 col-lg-2">
          <input type="checkbox" value="18"
            name="task-day_month_num[]-<?= $task_id ?>"
            id="task-day-month-num[]-<?= $task_id ?>-18"
            <?php if (in_array('18', $task->day_month_num)) echo 'checked'; ?>>
          <label for="task-day-month-num[]-<?= $task_id ?>-18">18</label>
        </div>
        <div class="col-xs-4 col-sm-3 col-md-2 col-lg-2">
          <input type="checkbox" value="19"
            name="task-day_month_num[]-<?= $task_id ?>"
            id="task-day-month-num[]-<?= $task_id ?>-19"
            <?php if (in_array('19', $task->day_month_num)) echo 'checked'; ?>>
          <label for="task-day-month-num[]-<?= $task_id ?>-19">19</label>
        </div>
        <div class="col-xs-4 col-sm-3 col-md-2 col-lg-2">
          <input type="checkbox" value="20"
            name="task-day_month_num[]-<?= $task_id ?>"
            id="task-day-month-num[]-<?= $task_id ?>-20"
            <?php if (in_array('20', $task->day_month_num)) echo 'checked'; ?>>
          <label for="task-day-month-num[]-<?= $task_id ?>-20">20</label>
        </div>
        <div class="col-xs-4 col-sm-3 col-md-2 col-lg-2">
          <input type="checkbox" value="21"
            name="task-day_month_num[]-<?= $task_id ?>"
            id="task-day-month-num[]-<?= $task_id ?>-21"
            <?php if (in_array('21', $task->day_month_num)) echo 'checked'; ?>>
          <label for="task-day-month-num[]-<?= $task_id ?>-21">21</label>
        </div>
        <div class="col-xs-4 col-sm-3 col-md-2 col-lg-2">
          <input type="checkbox" value="22"
            name="task-day_month_num[]-<?= $task_id ?>"
            id="task-day-month-num[]-<?= $task_id ?>-22"
            <?php if (in_array('22', $task->day_month_num)) echo 'checked'; ?>>
          <label for="task-day-month-num[]-<?= $task_id ?>-22">22</label>
        </div>
        <div class="col-xs-4 col-sm-3 col-md-2 col-lg-2">
          <input type="checkbox" value="23"
            name="task-day_month_num[]-<?= $task_id ?>"
            id="task-day-month-num[]-<?= $task_id ?>-23"
            <?php if (in_array('23', $task->day_month_num)) echo 'checked'; ?>>
          <label for="task-day-month-num[]-<?= $task_id ?>-23">23</label>
        </div>
        <div class="col-xs-4 col-sm-3 col-md-2 col-lg-2">
          <input type="checkbox" value="24"
            name="task-day_month_num[]-<?= $task_id ?>"
            id="task-day-month-num[]-<?= $task_id ?>-24"
            <?php if (in_array('24', $task->day_month_num)) echo 'checked'; ?>>
          <label for="task-day-month-num[]-<?= $task_id ?>-24">24</label>
        </div>
        <div class="col-xs-4 col-sm-3 col-md-2 col-lg-2">
          <input type="checkbox" value="25"
            name="task-day_month_num[]-<?= $task_id ?>"
            id="task-day-month-num[]-<?= $task_id ?>-25"
            <?php if (in_array('25', $task->day_month_num)) echo 'checked'; ?>>
          <label for="task-day-month-num[]-<?= $task_id ?>-25">25</label>
        </div>
        <div class="col-xs-4 col-sm-3 col-md-2 col-lg-2">
          <input type="checkbox" value="26"
            name="task-day_month_num[]-<?= $task_id ?>"
            id="task-day-month-num[]-<?= $task_id ?>-26"
            <?php if (in_array('26', $task->day_month_num)) echo 'checked'; ?>>
          <label for="task-day-month-num[]-<?= $task_id ?>-26">26</label>
        </div>
        <div class="col-xs-4 col-sm-3 col-md-2 col-lg-2">
          <input type="checkbox" value="27"
            name="task-day_month_num[]-<?= $task_id ?>"
            id="task-day-month-num[]-<?= $task_id ?>-27"
            <?php if (in_array('27', $task->day_month_num)) echo 'checked'; ?>>
          <label for="task-day-month-num[]-<?= $task_id ?>-27">27</label>
        </div>
        <div class="col-xs-4 col-sm-3 col-md-2 col-lg-2">
          <input type="checkbox" value="28"
            name="task-day_month_num[]-<?= $task_id ?>"
            id="task-day-month-num[]-<?= $task_id ?>-28"
            <?php if (in_array('28', $task->day_month_num)) echo 'checked'; ?>>
          <label for="task-day-month-num[]-<?= $task_id ?>-28">28</label>
        </div>
      </div>
    </div>
  </div>

</div>
-->
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
    <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
      <div class="box">
        <input type="submit" value="Save"/>
      </div>
    </div>
  </div>
  <input type="hidden" name="task-order-<?= $task_id ?>" value="-1">
<?php } ?>
