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
    
    $data = json_decode($_POST['data']);
    foreach ($data as $key => $val) {
      $ex 	= explode("|",$val);
      $id = $ex[0];
      $email = $ex[1];
      $clientIP = $ex[2];
      $clientUserAgent = $ex[3];
      $clientMac = $ex[4];
      $optout = $ex[5];
      $date = $ex[6];
      
      $req = array (
        'id' => $id,
        'email' => $email,
        'clientIP' => $clientIP,
        'clientUserAgent' => $clientUserAgent,
        'clientMac' => $clientMac,
        'optout' => $optout,
        'date' => $date
        );

      if (!$this->db->insert('customers', $req)) {
        error_log("Can't place the insert sql request, error message: ".$this->db->_error_message());
        exit();
      }
    }
    $lastID = $this->getLastId($data);
    return ($lastID);
  }
  
  private function getLastId($res) {
    $lastID = 1;
    foreach ($res as $key => $value) {
      if (isset($value['id'])) {
        if ($value['id'] > $lastID) {
          $lastID = $value['id'];
        }  
      } else {
        return (false);
      }
    }
    return ($lastID);
  }
  
}