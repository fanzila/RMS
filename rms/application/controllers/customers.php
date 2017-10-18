<?php 
class customers extends CI_Controller {
  
  public function __construct() 
  {
    parent::__construct();
    $this->load->library('ion_auth');
    $this->load->library('hmw');
    $this->load->library('mmail');
    $this->load->library('customers_lib');
    $this->load->library('useragentparser');
    $this->load->helper('form');
    $this->load->database();
  }
  
  
  public function index() 
  {
    $this->hmw->isLoggedIn();
    $this->hmw->changeBu();
    
    $id_bu = $this->session->userdata('bu_id');
    $headers = $this->hmw->headerVars(1, "/customers/", "Customers");
		$this->load->view('jq_header_pre', $headers['header_pre']);
		$this->load->view('jq_header_post', $headers['header_post']);
		$this->load->view('customers/index');
		$this->load->view('jq_footer');
  }
  
  
  public function api() 
  {
    $this->hmw->isLoggedIn();
    $this->hmw->changeBu();
    
    $data = array();
    $id_bu = $this->session->userdata('bu_id');
    $keys = $this->customers_lib->getApiKeys($id_bu);
    
    if (!empty($keys)) {
      $data['keys'] = $keys;
    }
    
    $data['id_bu'] = $id_bu;
    
    $headers = $this->hmw->headerVars(0, "/customers/", "API");
    $this->load->view('jq_header_pre', $headers['header_pre']);
		$this->load->view('jq_header_post', $headers['header_post']);
		$this->load->view('customers/api', $data);
		$this->load->view('jq_footer');
  }
  
  public function createApikey() 
  {
    $this->hmw->isLoggedIn();
    $this->hmw->changeBu();
    
    $post = $this->input->post();
    $id_bu = $this->session->userdata('bu_id');
    $buName = $this->hmw->getBuInfo($id_bu)->name;
    
    if (isset($id_bu)) {
      if (empty($post)) {
        $error = "No data sent, please retry.";
      } else {
        $cstrong = true;
        $apiKey = bin2hex(openssl_random_pseudo_bytes(16, $cstrong));
        $hashedKey = password_hash($apiKey, PASSWORD_DEFAULT);
        if (!$this->customers_lib->addApiKey($id_bu, $hashedKey)) {
          $error = "Could not insert your api key in the database";
          $apiKey = null;
        }
      }  
    } else {
      $error = "Could not recognize current BU.";
    }
    
    if (isset($apiKey)) {
      $data['buName'] = $buName;
      $data['apiKey'] = $apiKey;
    }
    
    if (isset($error)) {
      $data['error'] = $error;
    }
    
    $headers = $this->hmw->headerVars(0, "/customers/api/", "Create API Key");
    $this->load->view('jq_header_pre', $headers['header_pre']);
		$this->load->view('jq_header_post', $headers['header_post']);
    $this->load->view('customers/createApikey', $data);
		$this->load->view('jq_footer');
  }
  
  public function deleteApiKey($id) 
  {
    $this->hmw->isLoggedIn();
    $this->hmw->changeBu();
    
    $id_bu = $this->session->userdata('bu_id');
    
    if (!empty($id)) {
      $delete = $this->customers_lib->removeApiKey($id);
      redirect('/crud/customers_api_keys/' . $id_bu);
      } else {
      die('You must provide the bu ID to remove key from when calling this function.');
    }
  }
  
  public function viewCustomers() 
  {
    $this->hmw->isLoggedIn();
    $this->hmw->changeBu();
    
    $customers = $this->customers_lib->getCustomers();
    
    if (!empty($customers)) {
      $temp = $customers;
      foreach ($temp as $key => $customer) {
        if (isset($customer['clientUserAgent'])) {
          $readableUserAgent = $this->useragentparser->parse_user_agent($customer['clientUserAgent']);
          $customers[$key]['clientUserAgent'] = $readableUserAgent;
        }
      }
      $data['countOptOut'] = $this->customers_lib->countOptOut();
      $data['countOptIn'] = $this->customers_lib->countOptIn();
      $data['customers'] = $customers;
    }
    
    $headers = $this->hmw->headerVars(0, "/customers/", "View Customers");
    $this->load->view('jq_header_pre', $headers['header_pre']);
    $this->load->view('jq_header_post', $headers['header_post']);
    $this->load->view('customers/viewCustomers', $data);
    $this->load->view('jq_footer');
  }
  
  
  public function record()
  {
    if(!isset($_POST['data'])) exit('No POST data provided');
    if(empty($_POST['data'])) exit('No data provided in POST');
    if (isset($_SERVER['HTTP_CUSTAPIKEY']) && isset($_SERVER['HTTP_APPNAME'])) {
      $apiKey = $_SERVER['HTTP_CUSTAPIKEY'];
      $appName = $_SERVER['HTTP_APPNAME'];
      if ($this->customers_lib->checkApiKey($appName, $apiKey)) {
        $data = json_decode($_POST['data'], true);
        foreach ($data as $key => $val) {
          if (!$this->db->insert('customers', $val)) {
            error_log("Can't place the insert sql request, error message: ".$this->db->_error_message());
            $ret = json_encode(array('lastID' => $data[$prev_key]['id']));
            echo ($ret);
            die('Transfer interrupted at id : ' . $val['id']);
          }
          $prev_key = $key;
        }
      } else {
        $ret = json_encode(array('apiError' => 'FORBIDDEN: Wrong API key'));
        echo $ret;
      }
    } else {
      $ret = json_encode(array('apiError' => 'FORBIDDEN: No API key received'));
      echo ($ret);
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
      if (isset($_SERVER['HTTP_CUSTAPIKEY']) && isset($_SERVER['HTTP_APPNAME'])) {
        $apiKey = $_SERVER['HTTP_CUSTAPIKEY'];
        $appName = $_SERVER['HTTP_APPNAME'];
        if ($this->customers_lib->checkApiKey($appName, $apiKey)) {
          echo $lastID;
        } else {
          $ret = json_encode(array('apiError' => 'FORBIDDEN: Wrong API key'));
          echo $ret;
        }
      } else {
        $ret = json_encode(array('apiError' => 'FORBIDDEN: No API key received'));
        echo ($ret);
      }
    }
    return ($lastID);
  }
  
}