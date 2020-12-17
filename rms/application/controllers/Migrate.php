<?php

class Migrate extends CI_Controller
{
/**
  public function index()
  {
    $this->load->library('migration');

    if (!$this->migration->current())
      echo 'Error: ' . $this->migration->error_string() . PHP_EOL;
    else
      echo 'Migration(s) ran successfully' . PHP_EOL;
  }

  
  public function copyRmd($from, $to) {

    $this->db->select('task, comment, priority, type, notify_tablet, notif.start as notif_start, notif.end as notif_end, notif.interval, notif.last, meta.start as meta_start, meta.repeat_interval as meta_repeat, repeat_interval');
    $this->db->where('id_bu', $from);
    $this->db->where('active', 1);
    $this->db->join('rmd_notif as notif', 'notif.id_task = rmd_tasks.id', 'left');
    $this->db->join('rmd_meta as meta', 'meta.id_task = rmd_tasks.id', 'left');
    $query = $this->db->get("rmd_tasks");
    
    foreach ($query->result_array() as $tasks){
      
      $this->db->set('task', $tasks['task']);
      $this->db->set('active', 1);
      $this->db->set('priority', $tasks['priority']);
      $this->db->set('id_bu', $to);
      $this->db->set('type', $tasks['type']);
      $this->db->set('notify_tablet', $tasks['notify_tablet']);
      $this->db->insert('rmd_tasks');
      $inserted = $this->db->insert_id();
      echo $inserted;

      $this->db->set('id_task', $inserted);
      $this->db->set('start', $tasks['notif_start']);
      $this->db->set('end', $tasks['notif_end']);
      $this->db->set('interval', $tasks['interval']);
      $this->db->set('last', $tasks['last']);
      $this->db->insert('rmd_notif');

      $this->db->set('id_task', $inserted);
      $this->db->set('start', $tasks['meta_start']);
      $this->db->set('repeat_interval', $tasks['repeat_interval']);
      $this->db->insert('rmd_meta');

      print_r($tasks);

    }

    echo "The end";

  }

  public function copyProducts($from, $to) {

    $this->db->where('id_supplier', $from);
    $this->db->where('active', 1);
    $this->db->where('deleted', 0);
    $query = $this->db->get("products");
    
    foreach ($query->result_array() as $row){
      
      $this->db->set('name', $row['name']);
      $this->db->set('id_supplier', $to);
      $this->db->set('price', $row['price']);
      $this->db->set('id_unit', $row['id_unit']);
      $this->db->set('packaging', $row['packaging']);
      $this->db->set('active', $row['active']);
      $this->db->set('id_category', $row['id_category']);
      $this->db->set('freq_inventory', $row['freq_inventory']);
      $this->db->set('daily_unit_conso', $row['daily_unit_conso']);
      $this->db->set('supplier_reference', $row['supplier_reference']);
      $this->db->set('comment', $row['comment']);
      $this->db->set('deleted', $row['deleted']);
      $this->db->set('manage_stock', 0);
      $this->db->insert('products');
      $inserted = $this->db->insert_id();
      echo $inserted;

      $this->db->set('id_product', $inserted);
      $this->db->insert('products_stock');

    }

    echo "The end";

  }


}
**/

?>