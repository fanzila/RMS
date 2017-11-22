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
    
    //To be able to login after using this function, the WP plugin "PHP native password hash" is required : https://fr.wordpress.org/plugins/password-hash/
    private function setWPPass($id = null)
    {
      if (isset($id) && !empty($id)) {
        if ($this->wp_rms->hasWpAccount($id)) {
          
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
          
          if ($WpUID = $this->wp_rms->hasWpAccount()) {
            
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
        if ($wpUID = $this->wp_rms->hasWpAccount($id)) {
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
      if ($WpUID = $this->wp_rms->hasWpAccount()) {
        $this->wp_rms->loginWPUser($WpUID);
      } else {
        if ($this->wp_rms->createWPAccount())
        {
          if ($this->setWPPass())
          {
            $WpUID = $this->wp_rms->hasWpAccount();
            $this->wp_rms->loginWPUser($WpUID);
          }
        }
      }
    }
}