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
      // please set your WP db password here :
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

    // If id is not passed in parameters, the function will check for a wordpress account in the current user data
    private function hasWpAccount($id = null) 
    {
      if (isset($id) && !empty($id)) {
        $this->db->select('WordPress_UID');
        $this->db->where('id', $id);
        $query = $this->db->get('users');
        $ret = $query->row_array();
        $this->db->select('username');
        $this->db->where('id', $id);
        $username = $this->db->get('users')->row_array();
        if (isset($ret['WordPress_UID']) && !empty($ret['WordPress_UID'])) {
          return ($ret['WordPress_UID']);
        } else {
          $this->wpdb->where('user_login', $username['username']);
          $this->wpdb->get('wp_users');
          $res = $query->row_array();
          if (isset($res['user_login'])) {
            error_log("User " . $username['username'] . " has WP account, but UID is NULL in RMS");
            return ($res['ID']);
          } else {
            return (false);
          }
        }
      } else {
        $user = $this->ion_auth->user()->row();
        $this->db->select('WordPress_UID');
        $this->db->where('id', $user->id);
        $query = $this->db->get('users');
        $ret = $query->row_array();
        $username = $user->username;
        if (isset($ret['WordPress_UID']) && !empty($ret['WordPress_UID'])) {
          return ($ret['WordPress_UID']);
        } else {
          $this->wpdb->where('user_login', $username);
          $res = $this->wpdb->get('wp_users')->row_array();
          if (isset($res['user_login'])) {
            error_log("User $username has WP account, but UID is NULL in RMS");
            return ($res['ID']);
          } else {
            return (false);
          }
        }
      }
    }
    
    
    //To be able to login after using this function, the WP plugin "PHP native password hash" is required : https://fr.wordpress.org/plugins/password-hash/
    private function setWPPass($id = null)
    {
      if (isset($id) && !empty($id)) {
        if ($this->hasWpAccount($id)) {
          
          $this->db->select('password', 'WordPress_UID', 'username');
          $this->db->where('id', $id);
          $uinfo = $this->db->get('users')->row_array();
          
          if (!empty($uinfo)) {
            
            $this->wpdb->where('id', $uinfo['WordPress_UID']);
            $this->wpdb->update('wp_users', array('user_pass', $uinfo['password']));
            
            if ($this->wpdb->affected_rows() > 0) {
              return (true);
            } else {
              error_log('Could not set WP password for user: ' . $uinfo['username']);
            }
          } else {
            die ('Could not fetch password from RMS database');
          }
        } else {
          die ('Error while setting WP password, please retry creating your account, or contact an admin');
        }
      } else {
        
        $user = $this->ion_auth->user()->row();
        
        if (!empty($user)) {
          
          if ($WpUID = $this->hasWpAccount()) {
            
            $this->wpdb->where('id', $WpUID);
            $this->wpdb->update('wp_users', array('user_pass' => $user->password));
            
            if ($this->wpdb->affected_rows() > 0) {
              return (true);
            } else {
              error_log('Could not set WP password for user: ' . $user->username);
            }
          } else {
            die ('Error while setting WP password, please retry creating your account, or contact an admin');
          }
        } else {
          die ('Could not fetch password from current user data');
        }
      }
    }
    
    public function index() 
    {
      if ($this->hasWpAccount()) {
        echo "hasWpAccount";
      } else {
        if ($this->wp_rms->createWPAccount())
        {
          $this->setWPPass();
        }
      }
    }
}