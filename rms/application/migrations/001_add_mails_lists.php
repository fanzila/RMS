<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_mails_lists extends CI_Migration
{
  public function up()
  {
    // create mails_lists table
    $this->dbforge->add_field('id');
    $this->dbforge->add_field('name VARCHAR(255) NOT NULL');
    $this->dbforge->create_table('mails_lists');

    // create users_mails_lists table which links users to mails lists
    $users_mails_lists_schema = [
      'mail_list_id' => [
        'type' => 'INT',
        'constraint' => 9
      ],
      'user_id' => [
        'type' => 'INT',
        'constraint' => 9
      ]
    ];
    $this->dbforge->add_field($users_mails_lists_schema);
    $this->dbforge->create_table('users_mails_lists');
    $this->db->query('ALTER TABLE `users_mails_lists` ADD PRIMARY KEY (mail_list_id, user_id)');
  }

  public function down()
  {
    $this->dbforge->drop_table('users_mails_lists');
    $this->dbforge->drop_table('mails_lists');
  }
}
