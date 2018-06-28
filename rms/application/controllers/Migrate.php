<?php

class Migrate extends CI_Controller
{
  public function index()
  {
    $this->load->library('migration');

    if (!$this->migration->current())
      echo 'Error: ' . $this->migration->error_string() . PHP_EOL;
    else
      echo 'Migration(s) ran successfully';
  }
}
