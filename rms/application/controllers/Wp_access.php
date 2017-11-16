<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

Class Wp_access extends CI_Controller {
  
    public $db;
    public $wpdb;
    
    function __construct() 
    {
      parent::__construct();
      
      $this->load->library('hmw');
      $this->hmw->isLoggedIn();
      
      $this->db = $this->load->database('default', TRUE);
      $this->wpdb = $this->load->database('wpdb', TRUE);
      $this->load->library('ion_auth');
      $this->load->library('wp_rms');
      $this->load->helper('url');
    }

    // If id is not passed in parameters, the function will check for a wordpress account in the current user data
    private function hasWpAccount($id = null) 
    {
      if (isset($id) && !empty($id)) {
        $this->db->select('WordPress_UID', 'username');
        $this->db->where('id', $id);
        $query = $this->db->get('users');
        $username = $query->row_array();
        if (isset($username['WordPress_UID']) && !empty($username['WordPress_UID'])) {
          return ($username['WordPress_UID']);
        } else {
          if (isset($username['username'])) {
            $this->wpdb->where('user_login', $username['username']);
            $this->wpdb->get('wp_users');
            $res = $query->row_array();
            if (isset($res['user_login'])) {
              error_log("User " . $username['username'] . " has WP account, but UID is NULL in RMS");
              return ($res['ID']);  
            } else {
              return (false);
            }
          } else {
            echo ('No user account corresponding');
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
              return (false);
            }
          } else {
            die ('Error while setting WP password, please retry creating your account, or contact an admin');
          }
        } else {
          die ('Could not fetch password from current user data');
        }
      }
    }
    
    public function delete($id) {
      $user = $this->ion_auth->user()->row();
      if ($this->ion_auth->is_admin()) {
        if ($wpUID = $this->hasWpAccount($id)) {
          if ($this->wp_rms->deleteWPUser($wpUID, 0) === true) {
            $WpUID = array('WordPress_UID' => NULL);
            $this->db->where('id', $id);
            $this->db->update('users', $WpUID);
            
            if ($this->db->affected_rows() > 0) {
        			$response_array['status'] = 'success';
        		} else {
        			$response_array['status'] = 'fail';
        		}
        		header('Content-type: application/json');
        		echo json_encode($response_array);
          } else {
            $response_array['status'] = 'fail';
        		header('Content-type: application/json');
        		echo json_encode($response_array);
          }
        }
      } else {
        $response_array['status'] = 'forbidden';
    		header('Content-type: application/json');
    		echo json_encode($response_array);
      }
    }
    
    public function index() 
    {
      if ($WpUID = $this->hasWpAccount()) {
        $this->wp_rms->loginWPUser($WpUID);
      } else {
        if ($this->wp_rms->createWPAccount())
        {
          if ($this->setWPPass())
          {
            $WpUID = $this->hasWpAccount();
            $this->wp_rms->loginWPUser($WpUID);
          }
        }
      }
    }
}