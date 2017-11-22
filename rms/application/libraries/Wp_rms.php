<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

//This library is written by Nael AWAYES to enable RMS to communicate with 
//the Wordpress REST API.
//Some function necessitate to install authentication plugins (Application Passwords, for example) and cannot be used as is.

// define your hashed application password in application/config/config.php at the $config['app_pass'] index.

  class Wp_rms 
  {
    public $db;
    public $wpdb;
    
    function __construct() {
      $CI = & get_instance();
      $this->db = $CI->load->database('default', TRUE);
      $this->wpdb = $CI->load->database('wpdb', TRUE);  
    }
    // $resource is a string for the routes, and should begin with a '/'
    // function inspired by blog.wixiweb.fr/wordpress-api-rest
    // set define $config['WpApiUrl'] with you own in application/config/config.php, you can set $ret_url to true to get the full url for your request, so you only have to set $apiUrl once
    
    public function get($resource = null, $ret_url = false) {
      
      $CI = & get_instance();
      $apiUrl = $CI->config->item('WpApiUrl');
      if (!$ret_url) {  
        $json = file_get_contents($apiUrl.$resource);
        $result = json_decode($json);
        return ($result);
      } else {
        return ($apiUrl.$resource);
      }
    }
    
    //same function as above but API URL is passed to the function through arguments
    public function getfromAPIUrl($apiUrl, $resource) {
      $json = file_get_contents($apiUrl.$resource);
      $result = json_decode($json);
      return ($result);
    }
    
    //function to list all WP users
    // to add arguments, pass them with the args array using the following : $args['your_argument_name'] = argument_value
    // You can find all possible arguments here : https://developer.wordpress.org/rest-api/reference/users/#arguments 
    // NOT FINISHED
    
    
    // public function listWPUsers($args = null) {
    //   $route = '/wp/v2/users/';
    //   if (isset($args) && !empty($args)) {
    //     foreach ($args as $key => $value) {
    //       if ($key == 'roles') {
    //         $route .= "?$key=[";
    //         foreach ($value as $role) {
    //           $route .= "'$value'";
    // 
    //         }
    //       }
    //       $route .= "?$key='$value'";
    //     }
    //   }
    //   return ($list);
    // }
    
    
    
    //function to get a WP user with the rest API, using their Wordpress UID.
    public function getWPUser($uid) {
      $user = get('/wp/v2/users/'.$uid);
      return ($user);
    }
    
    // If id is not passed in parameters, the function will check for a wordpress account in the current user data
    public function hasWpAccount($id = null) 
    {
      $CI = & get_instance();
      if (isset($id) && !empty($id)) {
        $this->db->select('WordPress_UID');
        $this->db->select('username');
        $this->db->where('id', $id);
        $query = $this->db->get('users');
        $username = $query->row_array();
        if (isset($username['WordPress_UID']) && !empty($username['WordPress_UID'])) {
          return ($username['WordPress_UID']);
        } else {
          if (isset($username['username'])) {
            $this->wpdb->where('user_login', $username['username']);
            $query = $this->wpdb->get('wp_users');
            if (isset($res['user_login'])) {
              $this->db->where('id', $id);
              $this->db->set('WordPress_UID', $res['ID']);
              $this->db->update('users');
              error_log("User " . $username['username'] . " has WP account, but UID is NULL in RMS, updating to ID : " . $res['ID']);
              return ($res['ID']);
            } else {
              return (false);
            }
            die();
          } else {
            echo ('No user account corresponding');
          }
        }
      } else {
        $user = $CI->ion_auth->user()->row();
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
            $this->db->where('id', $user->id);
            $this->db->set('WordPress_UID', $res['ID']);
            $this->db->update('users');
            error_log("User " . $username . " has WP account, but UID is NULL in RMS, updating to ID : " . $res['ID']);
            return ($res['ID']);
          } else {
            return (false);
          }
        }
      }
    }
    
    
    //function to get the corresponding WordPress Role according to the user's role in RMS, using his RMS id.
    public function userWPRole($id = null) {
      $CI = & get_instance();
      
      $user_groups = $CI->ion_auth->get_users_groups($id)->result_array();
      $higher_level['level'] = -1;
      foreach ($user_groups as $key => $value) {
        if ($value['level'] > $higher_level['level']) {
          $higher_level = $value;
        }
      }
      $this->db->where('id_group_rms', $higher_level['id']);
      $res = $this->db->get('wp_roles')->row_array();
      return ($res);
    }
    
    
    
    //function to login a user in the wordpress DB
    //NEEDS AUTHENTICATION
    public function loginWPUser($uid)
    {
        
      $CI = & get_instance();
      $appPass = $CI->config->item('app_pass');
      
      $post = array( 'id' => $uid );
      $post = http_build_query($post);
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, $this->get('/wpRMS/v2/getlink', true));
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
      curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
      curl_setopt($ch, CURLOPT_POST, 1);
      $headers = array();
      $headers[] = "Content-Type: application/x-www-form-urlencoded";
      $headers[] = "Authorization: Basic $appPass";
      curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
      $result = curl_exec($ch);
       if (curl_errno($ch)) {
       echo 'Error:' . curl_error($ch);
       }
       curl_close ($ch);
      $response = json_decode($result, true);
      if (filter_var($response, FILTER_VALIDATE_URL)) {
        redirect($response);
      }
    }
  
    //function to create a user in the WordPress DB, using information from RMS database
    //NEEDS AUTHENTICATION
    public function createWPAccount()
    {
      $CI = & get_instance();
      $RMS_user = $CI->ion_auth->user()->row_array();
      $appPass = $CI->config->item('app_pass');
      $user_role = $this->userWPRole();
      if (!isset($user_role) || empty($user_role)) {
        die ('No WordPress user roles have been defined for your RMS user group, please contact an Admin.');
      }
      $post = array(
        'username' => $RMS_user['username'],
        'email'    => $RMS_user['email'],
        'password' => 'ilovehankrestaurant',
        'roles' => $user_role['wp_role'],
        'first_name' => $RMS_user['first_name'],
        'last_name' => $RMS_user['last_name'],
        );
      $post = http_build_query($post);
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, $this->get('/wp/v2/users', true));
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
      curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
      curl_setopt($ch, CURLOPT_POST, 1);
      $headers = array();
      $headers[] = "Content-Type: application/x-www-form-urlencoded";
      $headers[] = "Authorization: Basic $appPass";
      curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
      $result = curl_exec($ch);
       if (curl_errno($ch)) {
       echo 'Error:' . curl_error($ch);
       }
       curl_close ($ch);
      $response = json_decode($result, true);
      if (isset($response['id'])) {
        $this->db->where('id', $RMS_user['id']);
        $this->db->update('users', array('WordPress_UID' => $response['id']));
        return (true);
      } else {
        error_log("Could not add WordPress User ID to RMS db User " . $RMS_user['username']);
        if (isset($response['code']) && isset($response['message'])) {
          echo ($response['code'] . ": " . $response['message']);
          error_log($response['code'] . ": " . $response['message']);
        }
        return (false);
      }
    }
    
    //function to delete WP User by UID, and reassign content by passing WP UID to $reassign (default doesn't reassign content, 
    //unless you have a user with a UID that has 0 for value)
    //NEEDS AUTHENTICATION
    public function deleteWPUser($uid, $reassign = 0)
    {
      $CI = & get_instance();
      $appPass = $CI->config->item('app_pass');
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, $this->get('/wp/v2/users/'.$uid.'?force=true&reassign='.$reassign, true));
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
      curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
      $headers = array();
      $headers[] = "Content-Type: application/x-www-form-urlencoded";
      $headers[] = "Authorization: Basic $appPass";
      curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
      $result = curl_exec($ch);
       if (curl_errno($ch)) {
       echo 'Error:' . curl_error($ch);
       }
       curl_close ($ch);
      $response = json_decode($result, true);
      if (isset($response['deleted']) && $response['deleted'] == 1) {
        return (true);
      } else {
        return (false);
      }
    }
    
    //function to edit user ($values is an array with account parameters to be changed, to see what can be used, refer to the WP REST API documentation)
    //NEEDS AUTHENTICATION
    public function editWPUser($uid, $values = null)
    {
      if (!isset($values) || empty($values)) {
        return (false);
      }
      $CI = & get_instance();
      $appPass = $CI->config->item('app_pass');
      $post = http_build_query($values);
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, $this->get('/wp/v2/users/'.$uid, true));
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
      curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
      curl_setopt($ch, CURLOPT_POST, 1);
      $headers = array();
      $headers[] = "Content-Type: application/x-www-form-urlencoded";
      $headers[] = "Authorization: Basic $appPass";
      curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
      $result = curl_exec($ch);
       if (curl_errno($ch)) {
       echo 'Error:' . curl_error($ch);
       }
       curl_close ($ch);
      $response = json_decode($result, true);
      if (isset($response['id'])) {
        return (true);
      } else {
        if (isset($response['code']) && isset($response['message'])) {
          error_log($response['code'] . ": " . $response['message']);
          die($response['code'] . ": " . $response['message']);
        }
        return (false);
    }
  }
}