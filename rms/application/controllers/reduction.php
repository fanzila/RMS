<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Reduction extends CI_Controller {

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
		$this->load->library('reduc');
		
	}

	public function index($task_id = null, $view = null)
	{
		$this->hmw->keyLogin();
		$id_bu =  $this->session->all_userdata()['bu_id'];

		$msg = null;
		

		$this->db->select('users.username, users.last_name, users.first_name, users.email, users.id');
		$this->db->distinct('users.username');
		$this->db->join('users_bus', 'users.id = users_bus.user_id', 'left');
		$this->db->where('users.active', 1);
		$this->db->where('users_bus.bu_id', $id_bu);
		$this->db->order_by('users.username', 'asc');
		$query = $this->db->get("users");
		$users = $query->result();

		$reduc = $this->reduc->getPromo($task_id, $view, $id_bu);

		$data = array(
			'reduc'		=> $reduc,
			'create'	=> 0,
			'users'		=> $users,
			'msg'		=> $msg,
			'keylogin'	=> $this->session->userdata('keylogin'),
			'view'		=> $view
			);

		$data['bu_name'] =  $this->session->all_userdata()['bu_name'];
		$data['username'] = $this->session->all_userdata()['identity'];
		
		$this->load->view('reduction/index',$data);
	}
	
	public function log()
	{
		$this->hmw->keyLogin();
		$id_bu =  $this->session->all_userdata()['bu_id'];
		
		$req 	= "SELECT l.`date`,l.`nature`,u.`username`, l.`id_reduc`, l.`event_type`  FROM reduc_log l JOIN `users` u ON u.id = l.`id_user` WHERE l.id_bu = $id_bu ORDER BY l.`date` DESC LIMIT 100";
		
		$res 	= $this->db->query($req) or die($this->mysqli->error);
		$reducs 	= $res->result();
		$data = array(
			'reducs'	=> $reducs
			);
			
		$data['bu_name'] =  $this->session->all_userdata()['bu_name'];
		$data['username'] = $this->session->all_userdata()['identity'];
			
		$this->load->view('reduction/logs',$data);
	}

	public function save()
	{
		$id_bu =  $this->session->all_userdata()['bu_id'];
		
		$data = $this->input->post();
		$sqlt = "UPDATE ";
		$sqle = " WHERE id = $data[id]";
		$sqln = " WHERE id_reduc = $data[id]";
		$event= 1;
		$reponse = 'ok';
						
		if($data['id'] == 'create') {
			$sqlt = "INSERT INTO ";
			$sqle = "";
		}

		$sql_tasks = "$sqlt reduc_tasks SET `nature` = '".addslashes($data['nature'])."', id_user = $data[user], `date` = NOW(), id_bu = $id_bu $sqle ";
		$this->db->trans_start();
			if (!$this->db->query($sql_tasks)) {
				$response = "Can't place the insert sql request, error message: ".$this->db->_error_message();
			}			
			if($data['id'] == 'create') { 	
				$data['id'] = $this->db->insert_id();
				$sqln = " , id_reduc = $data[id]";
				$event = 0;
			}
			$sql_log = "INSERT INTO reduc_log SET `id_reduc` = '".$data['id']."', `id_user` = '".$data['user']."', `nature` = '".$data['nature']."', event_type = $event, id_bu = $id_bu, `date` = NOW()";
			if(!$this->db->query($sql_log)) {
				$response = "Can't place the insert sql request, error message: ".$this->db->_error_message();
			}
		$this->db->trans_complete();

		echo json_encode(['reponse' => $reponse]);
	}
	
	public function creation($create = null)
	{		
		$id_bu =  $this->session->all_userdata()['bu_id'];
		$this->load->library('reduc');

		$this->db->select('users.username, users.last_name, users.first_name, users.email, users.id');
		$this->db->distinct('users.username');
		$this->db->join('users_bus', 'users.id = users_bus.user_id', 'left');
		$this->db->where('users.active', 1);
		$this->db->where('users_bus.bu_id', $id_bu);
		$this->db->order_by('users.username', 'asc');
		$query = $this->db->get("users");
		$users = $query->result();

		$reduc = $this->reduc->getAllPromo($id_bu);
		$data = array(
			'create'	=> $create,
			'users'		=> $users,
			'reduc'		=> $reduc
			);
		
		$data['bu_name'] =  $this->session->all_userdata()['bu_name'];
		$data['username'] = $this->session->all_userdata()['identity'];
		
		$this->load->view('reduction/reduction_creation',$data);
	}
}
