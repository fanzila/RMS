  <a href="#create" class="ui-btn ui-btn-right" rel="external" data-ajax="false" data-icon="plus"><i class="zmdi zmdi-plus zmd-2x"></i></a>
</div>
<script type="text/javascript" src="/public/Sortable.min.js"></script>

<div data-role="content">
  <div id="creation-form" style="display: none;">
    <?php
      $checklist = $empty_checklist;
      require('checklist_form.php');
    ?>
  </div>

  <div id="update-forms">
    <?php foreach ($checklists as $checklist) {
      $bgcolor = $checklist->active ? '#eceeff' : '#bbbdbd';
    ?>
      <div data-id="<?= $checklist->id ?>" data-role="collapsible" style="background-color: <?= $bgcolor ?>" class="draggable">
        <h2>
          ID: <?= $checklist->id ?> | <?= $checklist->name ?> | <small> <?= $checklist->type ?></small>
          <span class="sort-icon checklists-sort"><?= $checklist->order ?>&nbsp;<i class="fa fa-sort"></i></span>
        </h2>
        <ul data-role="listview" data-theme="d" data-divider-theme="d">
          <li>
            <?php require('checklist_form.php'); ?>
          </li>
        </ul>
      </div>
    <?php } ?>
    <script type="text/javascript">
      $(document).ready(function() {
        // prepare creation form toggle
        var create = $('#creation-form');
        var update = $('#update-forms');
        function toggleCreationForm() {
          if (create.is(':visible')) {
            create.hide();
            update.show();
          } else {
            update.hide();
            create.show();
          }
        }
        $('a[href="#create"]').on('click', toggleCreationForm);

        // prepare forms ajax
        function submitForm(evt) {
          evt.preventDefault();
          var elem = $(this);

          var inputName = elem.find('input[name="name"]');
          var name = inputName.val().trim().toUpperCase();

          inputName.val(name);

          var idField = elem.find('input[name="id"]');
          var isUpdate = idField && idField.val();

          var tasks = {};
          var data = elem.serializeArray().reduce(function(data, entry) {
            var name = entry.name, value = entry.value;

            if (name.indexOf('task-') === -1) {
              data[name] = value;
            } else {
              var infos = name.split('-');
              var id = parseInt(infos[infos.length - 1], 10);
              var field = infos.slice(1, -1);

              if (!tasks[id])
                tasks[id] = {};

              tasks[id][field] = value;
            }

            return data;
          }, {
            tasks: []
          });

          for (var i in tasks)
            data.tasks.push(tasks[i]);

          $.ajax({
            url: elem.attr('action'),
            type: elem.attr('method'),
            data: data,
            dataType: 'json'
          }).done(function(data) {
            if (!data.success)
              return alert(data.message || 'An unknown error occured, not saved');

            alert('Data has been saved');

            if (!isUpdate)
              window.location.reload(true);
          });
        }
        $('form.checklist-update').each(function(i, form) {
          form = $(form);

          form.find('input[type="submit"]').on('click', function(evt) {
            evt.preventDefault();
            form.trigger('submit');
          });

          form.on('submit', submitForm);
        });

        // sort checklists
        function saveOrder()
        {
          update.addClass('saving-sort');
          checklistsSort.option('disabled', true);

          var orderedIds = [];

          update.children().each(function(idx, elem) {
            var toUpdate = elem.dataset
              && elem.dataset.id !== undefined
              && elem.dataset.id !== null;

            if (toUpdate) {
              orderedIds.push(elem.dataset.id);

              var orderElem = elem.getElementsByClassName('checklists-sort')[0];
              setTimeout(function() {
                orderElem.innerHTML = idx + '&nbsp;<i class="fa fa-sort"></i>';
              }, 0);
            }
          });

          $.ajax({
            url: '/checklist/order',
            type: 'POST',
            data: { ids: orderedIds },
            dataType: 'json'
          }).done(function(data) {
            checklistsSort.option('disabled', false);
            update.removeClass('saving-sort');

            if (!data.success)
              return alert(data.message || 'An unknown error occured, not saved');
          });
        }
        var checklistsSort = new Sortable(update.get(0), {
          group: 'checklists',
          sort: true,
          handle: '.checklists-sort',
          onUpdate: saveOrder
        });

        // sort tasks
        function updateTasksOrder(parent)
        {
          parent.children().each(function (idx, elem) {
            setTimeout(function() {
              elem.find('.sort-icon').html(idx + '&nbsp;<i class="fa fa-sort"></i>');
              elem.find('.task-order').val(idx);
            }, 0);
          });
        }
        update.find('.tasks-sorting').each(function (idx, tasksSorting) {
          var id = tasksSorting.dataset.id;

          var checklistsSort = new Sortable(tasksSorting, {
            group: 'tasks-' + id,
            sort: true,
            handle: '.tasks-sort',
            onUpdate: function() { updateTasksOrder($(tasksSorting)); }
          });
        });
      });
    </script>
  </div>
</div>
