<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

Class Wp_access extends CI_Controller {
  
    public $db;
    public $wpdb;
    
    function __construct() 
    {
      parent::__construct();
      $this->db = $this->load->database('default', TRUE);
      $wpdb_config['hostname'] = 'localhost';
      $wpdb_config['username'] = 'root';
      $wpdb_config['password'] = '';
      $wpdb_config['database'] = 'wp';
      $wpdb_config['dbdriver'] = 'mysqli';
      $wpdb_config['pconnect'] = TRUE;
      $wpdb_config['cache_on'] = FALSE;
      $wpdb_config['cachedir'] = '';
      $wpdb_config['charset'] = 'utf8';
      $wpdb_config['dbcollat'] = 'utf8_general_ci';
      $this->wpdb = $this->load->database($wpdb_config, TRUE);
      $this->load->library('ion_auth');
      $this->load->library('wp_rms');
      $this->load->helper('url');
    }

    private function hasWpAccount() 
    {
      $user = $this->ion_auth->user()->row();
      $groups = $this->ion_auth->get_users_groups()->result();
      $this->db->select('WordPress_UID');
      $this->db->where('id', $user->id);
      $query = $this->db->get('users');
      $ret = $query->row_array();
      $username = $user->username;
      if (isset($ret['WordPress_UID']) && $ret['WordPress_UID'] != NULL) {
        return (true);
      } else {
        $this->wpdb->where('user_login', $username);
        $this->wpdb->get('wp_users');
        $res = $query->row_array();
        if (isset($ret['user_login'])) {
          error_log("User $username has WP account, but UID is NULL in RMS");
          return (true);
        } else {
          return (false);
        }
      }
    }
    
    public function index() 
    {
      if ($this->hasWpAccount()) {
        echo "hasWpAccount";
      } else {
        $this->wp_rms->createWPAccount();
      }
    }
}