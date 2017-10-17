<?php 

Class Customers_lib {
  
  public function getApiKeys() {
    $CI = & get_instance();
    
    $CI->load->database();
    
    $query = $CI->db->get('customers_api_keys');
    $ret = $query->result_array();
    
    return ($ret); 
  }
  
  
  public function checkApiKey($appName, $apiKey) {
    if (!isset($appName) || !isset($apiKey) || empty($appName) || empty($apiKey)) {
      return (false);
    }
    $CI = & get_instance();
    
    $CI->load->database();
    $CI->db->select('key')->where('app_name', $appName);
    $query = $CI->db->get('customers_api_keys');
    $res = $query->row_array();
    if (isset($res['key'])) {
      $pass_verify = password_verify($apiKey, $res['key']);
      if ($pass_verify === true) {
        return (true);
      } else {
        return (false);
      }
    } else {
      return (false);
    }
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