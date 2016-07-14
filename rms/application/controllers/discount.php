<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Discount extends CI_Controller {

	/**
	* Index Page for this controller.
	*
	* Maps to the following URL
	* 		http://example.com/index.php/welcome
	*	- or -  
	* 		http://example.com/index.php/welcome/index
	*	- or -
	* Since this controller is set as the default controller in 
	* config/routes.php, it's displayed at http://example.com/
	*
	* So any other public methods not prefixed with an underscore will
	* map to /index.php/welcome/<method_name>
	* @see http://codeigniter.com/user_guide/general/urls.html
	*/

	public function __construct()
	{

		parent::__construct();

		$this->load->library('email');
		$this->load->database();
		$this->load->library('ion_auth');
		$this->load->library('hmw');
		$this->load->library('discounts');
		
	}

	public function index($task_id = null, $view = null)
	{
		$this->hmw->keyLogin();
		$id_bu =  $this->session->all_userdata()['bu_id'];

		$msg = null;
		if($task_id=="create") {
				$msg = "RECORDED ON: ".date('Y-m-d H:i:s');
		}

		$this->db->select('users.username, users.last_name, users.first_name, users.email, users.id');
		$this->db->distinct('users.username');
		$this->db->join('users_bus', 'users.id = users_bus.user_id', 'left');
		$this->db->where('users.active', 1);
		$this->db->where('users_bus.bu_id', $id_bu);
		$this->db->order_by('users.username', 'asc');
		$query = $this->db->get("users");
		$users = $query->result();

		$discount = $this->discounts->getPromo($task_id, $view, $id_bu);

		$data = array(
			'discount'		=> $discount,
			'create'	=> 0,
			'users'		=> $users,
			'msg'		=> $msg,
			'keylogin'	=> $this->session->userdata('keylogin'),
			'view'		=> $view
			);

		$data['bu_name'] =  $this->session->all_userdata()['bu_name'];
		$data['username'] = $this->session->all_userdata()['identity'];
		
		$this->load->view('discount/index',$data);
	}
	
	public function log()
	{
		$this->hmw->keyLogin();
		$id_bu =  $this->session->all_userdata()['bu_id'];
		
		$req 	= "SELECT l.`date`,l.`nature`,u.`username`, l.`id_discount`, l.`event_type`, l.`used`  FROM discount_log l JOIN `users` u ON u.id = l.`id_user` WHERE l.id_bu = $id_bu ORDER BY l.`date` DESC LIMIT 100";
		
		$res 	= $this->db->query($req) or die($this->mysqli->error);
		$discounts 	= $res->result();
		$data = array(
			'discounts'	=> $discounts
			);
			
		$data['bu_name'] =  $this->session->all_userdata()['bu_name'];
		$data['username'] = $this->session->all_userdata()['identity'];
			
		$this->load->view('discount/logs',$data);
	}

	public function save()
	{
		$id_bu =  $this->session->all_userdata()['bu_id'];
		
		$data = $this->input->post();
		
		$sqln = " WHERE id_discount = $data[id]";
		$reponse = 'ok';
						
		if($data['id'] == 'create') {
			$sqlt = "INSERT INTO ";
			$sqle = "";
		}else{
			$sqlt = "UPDATE ";
			$sqle = ", used = '$data[used]' WHERE id = $data[id]";
		}

		$sql_tasks = "$sqlt discount SET `nature` = '".addslashes($data['nature'])."', id_user = $data[user], `date` = NOW(), id_bu = $id_bu $sqle ";
		$this->db->trans_start();
			if (!$this->db->query($sql_tasks)) {
				$response = "Can't place the insert sql request, error message: ".$this->db->_error_message();
			}
			if($data['id'] == 'create') {	
				$data['id'] = $this->db->insert_id();
				$sqln = " , id_discount = $data[id]";
				$event = "";
			}else{
				$event= ", event_type = 'update', used = '$data[used]'";
				$sql_used = "UPDATE discount_log SET `used` = '$data[used]' WHERE id_bu = $id_bu AND id_discount = '".$data['id']."'";
				if(!$this->db->query($sql_used)) {
					$response = "Can't place the insert sql request, error message: ".$this->db->_error_message();
				}
			}
			$sql_log = "INSERT INTO discount_log SET `id_discount` = '".$data['id']."', `id_user` = '".$data['user']."', `nature` = '".$data['nature']."', id_bu = $id_bu, `date` = NOW() $event";
			if(!$this->db->query($sql_log)) {
				$response = "Can't place the insert sql request, error message: ".$this->db->_error_message();
			}
		$this->db->trans_complete();

		echo json_encode(['reponse' => $reponse]);
	}
	
	public function creation($create = null)
	{		
		$id_bu =  $this->session->all_userdata()['bu_id'];

		$this->db->select('users.username, users.last_name, users.first_name, users.email, users.id');
		$this->db->distinct('users.username');
		$this->db->join('users_bus', 'users.id = users_bus.user_id', 'left');
		$this->db->where('users.active', 1);
		$this->db->where('users_bus.bu_id', $id_bu);
		$this->db->order_by('users.username', 'asc');
		$query = $this->db->get("users");
		$users = $query->result();

		$discount = $this->discounts->getAllPromo($id_bu);
		$data = array(
			'create'	=> $create,
			'users'		=> $users,
			'discount'		=> $discount
			);
		
		$data['bu_name'] =  $this->session->all_userdata()['bu_name'];
		$data['username'] = $this->session->all_userdata()['identity'];
		
		$this->load->view('discount/discount_creation',$data);
	}
}
