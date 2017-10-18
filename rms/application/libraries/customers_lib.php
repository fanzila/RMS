<?php 

Class Customers_lib {
  
  public function getApiKeys($id_bu = null) {
    $CI = & get_instance();
    
    $CI->load->database();
    $CI->db->select('id, name, api_key');
    if (!empty($id_bu)) $CI->db->where('id', $id_bu);
    $query = $CI->db->get('bus');
    if (!empty($id_bu)) {
      $ret = $query->row_array();
    } else {
      $ret = $query->result_array();
    }
    return ($ret); 
  }
  
  
  public function checkApiKey($buName, $apiKey) {
    if (empty($buName) || empty($apiKey)) {
      return (false);
    }
    $CI = & get_instance();
    
    $CI->load->database();
    $CI->db->select('api_key')->where('name', $buName);
    $query = $CI->db->get('bus');
    $res = $query->row_array();
    if (isset($res['api_key'])) {
      $pass_verify = password_verify($apiKey, $res['api_key']);
      if ($pass_verify === true) {
        return (true);
      } else {
        return (false);
      }
    } else {
      return (false);
    }
  }
  
  public function addApiKey($id, $apiKey) {
    $CI = & get_instance();
    
    $CI->load->database();
    
    $CI->db->where('id', $id);
    $array = array('api_key' => $apiKey);
    
    if ($CI->db->update('bus', $array)) {
      return (true);
    } else {
      return (false);
    }
  }
  
  public function removeApiKey($id_bu) {
    $CI = & get_instance();
    
    $CI->load->database();
    
    $CI->db->where('id', $id_bu);
    if ($CI->db->update('bus', array('api_key' => ''))) {
      return (true);
    } else {
      return (false);
    }
  }
  
  public function getCustomers() {
    $CI = & get_instance();
    
    $CI->load->database();
    
    $CI->db->order_by('id', 'desc');
    $CI->db->limit('200');
    $query = $CI->db->get('customers');
    $ret = $query->result_array();
    
    return ($ret);
  }
  
  public function countOptOut() {
    $CI = & get_instance();
    
    $CI->load->database();
    $CI->db->distinct();
    $CI->db->select('email');
    $CI->db->where('optout', 1);
    $query = $CI->db->get('customers');
    $res = $query->num_rows();
    return ($res);
  }
  
  public function countOptIn() {
    $CI = & get_instance();
    
    $CI->load->database();
    $CI->db->distinct();
    $CI->db->select('email');
    $CI->db->where('optout', 0);
    $query = $CI->db->get('customers');
    $res = $query->num_rows();
    return ($res);
  }
} 
?>