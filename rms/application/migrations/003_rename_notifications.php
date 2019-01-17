<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Rename_notifications extends CI_Migration
{
  public function up()
  {
    // rename mails lists tables
    $this->dbforge->rename_table('mails_lists', 'notifications');
    $this->dbforge->rename_table('users_mails_lists', 'users_notifications');
    $this->dbforge->modify_column('users_notifications', [
      'mail_list_id' => [
        'name'       => 'notification_id',
        'type'       => 'INT',
        'constraint' => 9
      ]
    ]);

    // rename some notifications
    $this->renameNotifications('notifications', [
      'checklists_notifications' => 'checklists',
      'sensors_notifications' => 'sensors'
    ]);
  }

  public function down()
  {
    // rename mails lists tables
    $this->dbforge->rename_table('notifications', 'mails_lists');
    $this->dbforge->rename_table('users_notifications', 'users_mails_lists');
    $this->dbforge->modify_column('users_mails_lists', [
      'notification_id' => [
        'name'       => 'mail_list_id',
        'type'       => 'INT',
        'constraint' => 9
      ]
    ]);

    // rename some notifications
    $this->renameNotifications('mails_lists', [
      'checklists' => 'checklists_notifications',
      'sensors' => 'sensors_notifications'
    ]);
  }

  private function renameNotifications($table, $renames)
  {
    $this->db->trans_begin();

    foreach ($renames as $old_name => $new_name)
    {
      $this->db->where('name', $old_name);
      $this->db->update($table, [ 'name' => $new_name ]);
    }

    $this->db->trans_complete();
    return $this->db->trans_status();
  }
}
