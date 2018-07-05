<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Create_defaults_mails_lists extends CI_Migration
{
  public function up()
  {
    $groups_list_mapping = [
      'admin' => [
        'checklists_editions',
        'checklists_notifications',
        'shifts_management',
        'sensors_notifications',
        'reports',
        'cashier_alerts'
      ],
      'Staff' => [
        'cashier_alerts'
      ],
      'Assistant_manager' => [
        'checklists_notifications',
        'sensors_notifications',
        'cashier_alerts'
      ],
      'Manager' => [
        'checklists_notifications',
        'shifts_management',
        'sensors_notifications',
        'reports',
        'cashier_alerts'
      ],
      'extra' => [
        'shifts_management'
      ],
      'Director' => [
        'checklists_editions',
        'checklists_notifications',
        'shifts_management',
        'sensors_notifications',
        'reports',
        'cashier_alerts'
      ]
    ];

    $this->db->trans_begin();

    $this->db->insert_batch('mails_lists', [
      [ 'name' => 'checklists_editions' ],
      [ 'name' => 'checklists_notifications' ],
      [ 'name' => 'shifts_management' ],
      [ 'name' => 'sensors_notifications' ],
      [ 'name' => 'reports' ],
      [ 'name' => 'cashier_alerts' ]
    ]);

    $this->db->trans_commit();

    $mails_lists = $this->get_mails_lists();
    $users_groups = $this->get_users_groups();

    foreach ($users_groups as $group => $users)
    {
      $lists = $groups_list_mapping[$group];

      foreach ($users as $user)
      {
        foreach ($lists as $list)
        {
          $sql = $this->db->insert_string('users_mails_lists', [
            'mail_list_id' => $mails_lists[$list],
            'user_id'      => $user
          ]);

          $this->db->query($sql . ' ON DUPLICATE KEY UPDATE user_id = VALUES(user_id)');
        }
      }
    }

    $this->db->trans_complete();
  }

  public function down()
  {
    $this->db->where('1 = 1');
    $this->db->delete('users_mails_lists');
    $this->db->where('1 = 1');
    $this->db->delete('mails_lists');
  }

  private function get_mails_lists()
  {
    $this->db->select('id, name');
    $result = $this->db->get('mails_lists')->result();
    $mails_lists = [];

    foreach ($result as $line)
      $mails_lists[$line->name] = $line->id;

    return $mails_lists;
  }

  private function get_users_groups()
  {
    $this->db->select('g.name, u.id AS user_id');
    $this->db->from('groups AS g');
    $this->db->join('users_groups AS u', 'u.group_id = g.id');

    $result = $this->db->get()->result();
    $users_groups = [];

    foreach ($result as $user_group)
    {
      $name = $user_group->name;

      if (!array_key_exists($name, $users_groups))
        $users_groups[$name] = [];

      array_push($users_groups[$name], $user_group->user_id);
    }

    return $users_groups;
  }
}
