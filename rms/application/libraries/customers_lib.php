<?php 

Class Customers_lib {
  
  public function getApiKeys() {
    $CI = & get_instance();
    
    $CI->load->database();
    
    $query = $CI->db->get('customers_api_keys');
    $ret = $query->result_array();
    
    return ($ret); 
  }
  
  
  public function checkApiKey($apiKey) {
    $CI = & get_instance();
    
    $CI->load->database();
  }
  
  public function addApiKey($appName, $apiKey) {
    $CI = & get_instance();
    
    $CI->load->database();
    
    $array = array('app_name' => $appName, 'key' => $apiKey);
    
    if ($CI->db->insert('customers_api_keys', $array)) {
      return (true);
    } else {
      return (false);
    }
  }
  
  
  public function getCustomers() {
    $CI = & get_instance();
    
    $CI->load->database();
    
    $query = $CI->db->get('customers');
    $ret = $query->result_array();
    
    return ($ret);
  }
  
} 
?>