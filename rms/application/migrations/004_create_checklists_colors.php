
<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Create_checklists_colors extends CI_Migration
{
  public function up()
  {
    $this->dbforge->add_column('checklist_tasks', [
      'color' => [
        'type'       => 'VARCHAR',
        'constraint' => 20,
        'null'       => false,
        'default'    => '#FFFFFF'
      ]
    ]);
  }

  public function down()
  {
    $this->dbforge->drop_column('checklist_tasks', 'color');
  }
}
