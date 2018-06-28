<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_mails_lists extends CI_Migration
{
  public function up()
  {
    // create mails_lists table
    $this->dbforge->add_field('id');
    $this->dbforge->add_field('name VARCHAR(255) NOT NULL');
    $this->dbforge->add_key('id');
    $this->dbforge->create_table('mails_lists');

    // create mails_lists_users table which links users to mails lists
    $mails_lists_users_schema = [
      'id_mail_list' => [
        'type' => 'INT',
        'constraint' => 9
      ],
      'id_user' => [
        'type' => 'INT',
        'constraint' => 9
      ]
    ];
    $this->dbforge->add_field($mails_lists_users_schema);
    $this->dbforge->add_key([ 'id_mail_list', 'id_user' ]);
    $this->dbforge->create_table('mails_lists_users');
  }

  public function down()
  {
    $this->dbforge->drop_table('mails_lists_users');
    $this->dbforge->drop_table('mails_lists');
  }
}
