<?php

class Chkl
{
  public function __construct()
  {
    $CI = &get_instance();
    $CI->load->database();
    $CI->load->library('ion_auth');
    $CI->load->library('hmw');
  }

  const UPDATABLE_CHECKLIST_FIELDS = [
    'name',
    'active',
    'order',
    'type'
  ];

  const UPDATABLE_TASK_FIELDS = [
    'name',
    'comment',
    'priority',
    'active',
    'order',
    'day_week_num',
    'day_month_num'
  ];

  public function getUpdatableFields()
  {
    return self::UPDATABLE_CHECKLIST_FIELDS;
  }

  public function getTasksUpdatableFields()
  {
    return self::UPDATABLE_TASK_FIELDS;
  }

  public function getAllChecklists($id_bu, $with_tasks = false)
  {
    $order = 'c.active DESC, c.order ASC, c.name ASC';

    if ($with_tasks)
      $order .= ', t.order ASC';

    $query = $this->prepareGet($with_tasks);
    $query->where('c.id_bu', $id_bu);
    $query->order_by($order);

    $result = $query->get()->result();

    if (!$with_tasks)
      return $result;

    $grouping = [];

    foreach ($result as $line)
    {
      $id = $line->id;

      if (!array_key_exists($id, $grouping))
      {
        $grouping[$id] = (object)[
          'id'     => $line->id,
          'name'   => $line->name,
          'active' => $line->active,
          'order'  => $line->order,
          'type'   => $line->type
        ];

        $grouping[$id]->tasks = [];
      }

      $fields = array_keys((array)$line);
      $task = [];

      foreach ($fields as $field)
      {
        if (strpos($field, 'task_') === 0)
        {
          $count = 1; // only variables can be passed as references
          $task[str_replace('task_', '', $field, $count)] = $line->$field;
        }
      }

      array_push($grouping[$id]->tasks, (object)$task);
    }

    return array_values($grouping); // reset array keys
  }

  public function getOneChecklist($id, $with_tasks = false)
  {
    $query = $this->prepareGet($with_tasks);
    $query->where('c.id', $id);

    if ($with_tasks)
      $query->order_by('t.order ASC');

    $result = $query->get()->result()[0];

    if (!$with_tasks)
      return $result;

    $checklist = (object)[
      'id'     => $result->id,
      'name'   => $result->name,
      'active' => $result->active,
      'order'  => $result->order,
      'type'   => $result->type
    ];

    $checklist->tasks = [];

    foreach ($result as $line)
    {
      $fields = array_keys((array)$line);
      $task = [];

      foreach ($fields as $field)
      {
        if (strpos($field, 'task_') === 0)
        {
          $count = 1; // only variables can be passed as references
          $task[str_replace('task_', '', $field, $count)] = $line->$field;
        }
      }

      array_push($checklist->tasks, (object)$task);
    }

    return $checklist;
  }

  public function save($data, $id_bu, $id = null)
  {
    $CI = &get_instance();

    if (!empty($id))
    {
      $update = $this->convertData($data);

      foreach ($update as $field => $value)
        $CI->db->set($field, $value);

      $CI->db->where('id', $id);
      $success = $CI->db->update('checklists');
    }
    else
    {
      $insert = $this->convertData($data);
      $insert['id_bu'] = $id_bu;
      $success = $CI->db->insert('checklists', $insert);
      $id = $CI->db->insert_id();
    }

    if (!$success)
      return [ 'success' => false, 'message' => $CI->db->_error_message() ];

    $with_tasks = !empty($data->tasks);

    return [
      'success' => true,
      'entity' => $this->getOneChecklist($id, $with_tasks)
    ];
  }

  private function prepareGet($with_tasks = false)
  {
    $CI = &get_instance();
    $db = $CI->db;

    $select = [
      'c.id',
      'c.name',
      'c.active',
      'c.order',
      'c.type'
    ];

    if ($with_tasks)
    {
      $task_fields = array_map(function($field) {
        return 't.' . $field . ' AS task_' . $field;
      }, [
        'id',
        'name',
        'comment',
        'priority',
        'active',
        'order',
        'day_week_num',
        'day_month_num'
      ]);

      $select = array_merge($select, $task_fields);
    }

    $db->select(implode(', ', $select));
    $db->from('checklists AS c');
    $db->order_by('c.active DESC, c.order ASC, c.name ASC');

    if (!$with_tasks)
      return $db;

    $db->join('checklist_tasks AS t', 't.id_checklist = c.id', 'left');

    return $db;
  }

  private function convertData($raw)
  {
    $data = [];

    $number_fields = [
      'active',
      'order',
      'id_bu'
    ];

    foreach (self::UPDATABLE_CHECKLIST_FIELDS as $field)
    {
      if (array_key_exists($field, $raw))
      {
        $value = $raw[$field];

        if (in_array($field, $number_fields))
          $value = intval($value);

        $data[$field] = $value;
      }
    }

    return $data;
  }
}
