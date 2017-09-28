<?php 
class customers extends CI_Controller {
  
  public function __construct() 
  {
    parent::__construct();
    $this->load->library('ion_auth');
    $this->load->library('hmw');
    $this->load->library('mmail');
    $this->load->database();
  }
  
  public function index() {
  
  }
  
  
  public function record()
  {
    if(!isset($_POST['data'])) exit('No POST data provided');
    if(empty($_POST['data'])) exit('No data provided in POST');
    
    $data = json_decode($_POST['data'], true);
    foreach ($data as $key => $val) {
      if (!$this->db->insert('customers', $val)) {
        error_log("Can't place the insert sql request, error message: ".$this->db->_error_message());
        return ($val['id']);
      }
    }
    $lastID = $this->getLastId();
    $ret = json_encode(array('lastID' => $lastID));
    echo ($ret);
  }
  
  public function getLastId($output = false) {
    $this->db->select_max('id');
    $res = $this->db->get('customers')->row_array();
    $lastID = (isset($res['id']) ? $res['id'] : 0);
    if ($output == true) {
      echo $lastID;
    }
    return ($lastID);
  }
  
}