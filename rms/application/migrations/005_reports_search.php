<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Reports_search extends CI_Migration
{
  public function up()
  {
    $this->db->query('CREATE FULLTEXT INDEX privmsgs_subject_search ON privmsgs(privmsg_subject)');
  }

  public function down()
  {
    $this->db->query('ALTER TABLE privmsgs DROP INDEX privmsgs_subject_search');
  }
}
