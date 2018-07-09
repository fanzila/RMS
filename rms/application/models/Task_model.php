<?php

class Task_model extends CI_Model
{
  public $id_checklist;
  public $name;
  public $comment;
  public $priority;
  public $active;
  public $order;
  public $day_week_num;
  public $day_month_num;

  const INSERTABlE_FIELDS = [
    'id_checklist',
    'name',
    'comment',
    'priority',
    'active',
    'order',
    'day_week_num',
    'day_month_num'
  ];

  public function insert_entry($data)
  {
    foreach (self::INSERTABlE_FIELDS as $field)
      $this->$field = $data[$field];

    $this->db->insert('checklist_tasks', $this);
    return $this->db->affected_rows() === 1;
  }
}
