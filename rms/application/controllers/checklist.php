<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Checklist extends CI_Controller {

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
		$this->load->library('hmw');		
		$this->hmw->keyLogin();
	}

	public function index()
	{
		$this->load->database();
		$id_bu = $this->session->all_userdata()['bu_id'];
		
		$msg = null;
		$form = $this->input->post();
		if(isset($form)) {
			if ($this->input->post('action') == 'save_tasks') {
				if($this->saveTasks()) {
					$msg = "RECORDED ON: ".date('Y-m-d H:m');
				} else {
					$msg = "WARNING NO RECORD";
				}
			}
		}
		$checklist_req = "SELECT `name`, `id` FROM checklists WHERE active=1 AND id_bu = $id_bu ORDER BY `order` ASC";
		$checklist_res = $this->db->query($checklist_req);
		$checklists = $checklist_res->result_array();
		
		$data = array(
			'msg'			=> $msg,
			'keylogin'		=> $this->session->userdata('keylogin'),	
			'checklists'	=> $checklists);

		$data['bu_name'] =  $this->session->all_userdata()['bu_name'];
		$data['username'] = $this->session->all_userdata()['identity'];

		$this->load->view('checklist/header');
		$this->load->view('checklist/checklist',$data);
		$this->load->view('checklist/footer');
		
	}

	public function viewCklPreviousTasks()
	{		

		$id_bu =  $this->session->all_userdata()['bu_id'];
		
		$checklist_rec_req = "SELECT r.user, u.first_name AS first_name, u.last_name AS last_name, r.id AS lid, r.id_checklist, r.date, c.name 
			FROM checklist_records AS r 
			JOIN checklists AS c ON c.id = r.id_checklist 
			JOIN users AS u ON r.user = u.id 
			WHERE c.id_bu = $id_bu
			ORDER BY r.date DESC LIMIT 50";
		$checklist_rec_res = $this->db->query($checklist_rec_req) or die($this->mysqli->error);
		$checklist_rec = $checklist_rec_res->result_array();

		$data = array(
			'checklists_rec'	=> $checklist_rec,
			);

		$data['bu_name'] =  $this->session->all_userdata()['bu_name'];
		$data['username'] = $this->session->all_userdata()['identity'];

		$this->load->view('checklist/header');
		$this->load->view('checklist/checklist_prev',$data);
		$this->load->view('checklist/footer');
		
	}

	public function viewCklTasks($id_ckl, $load = null)
	{		

		$form = null;
		$checklist_rec_id = null;
		$id_bu =  $this->session->all_userdata()['bu_id'];

		if($load > 0) {
			$checklist_rec_req 	= "SELECT r.user, r.id AS rec_id, r.data, r.id_checklist, r.date, c.name FROM checklist_records AS r JOIN checklists AS c ON c.id = r.id_checklist WHERE r.id = $load";
			$checklist_rec_res	= $this->db->query($checklist_rec_req) or die($this->mysqli->error);
			$checklist_rec		= $checklist_rec_res->row();
			$form 				= unserialize($checklist_rec->data);
			$id_ckl				= $form['id_checklist'];
			$checklist_rec_id	= $checklist_rec->rec_id;
		}

		$checklist_req = "SELECT `name`, `id`  FROM checklists WHERE id = $id_ckl ORDER BY `order` ASC";
		$checklist_res = $this->db->query($checklist_req);
		$checklists = $checklist_res->row();

		if($id_ckl == 2 OR $id_ckl == 3 OR $id_ckl == 4) { 	
			$checklist_task_req = "SELECT `name`, `id`, `comment`, `priority`, `day_month_num`, `day_week_num` FROM checklist_tasks WHERE active=1 AND id_checklist = $id_ckl ORDER BY `order` ASC";
			$checklist_task_res = $this->db->query($checklist_task_req);
			$checklist_tasks = $checklist_task_res->result_array();
		}

		$users_req = "SELECT `id`, `first_name`, `last_name`  FROM users WHERE active=1 ORDER BY `first_name` ASC";
		$users_res = $this->db->query($users_req) or die($this->mysqli->error);
		$users = $users_res->result_array();
		
		$this->db->select('users.username, users.last_name, users.first_name, users.email, users.id');
		$this->db->distinct('users.username');
		$this->db->join('users_bus', 'users.id = users_bus.user_id', 'left');
		$this->db->where('users.active', 1);
		$this->db->where('users_bus.bu_id', $id_bu);
		$query = $this->db->get("users");
		$users = $query->result();
		
		$data = array(
			'checklists'			=> $checklist_tasks,
			'checklists_name'		=> $checklists->name,
			'checklists_id'			=> $id_ckl,
			'checklist_rec_id'		=> $checklist_rec_id,
			'form'					=> $form,
			'load' 					=> $load,
			'users'					=> $users
			);

		$data['bu_name'] =  $this->session->all_userdata()['bu_name'];
		$data['username'] = $this->session->all_userdata()['identity'];

		$this->load->view('checklist/header');
		$this->load->view('checklist/checklist_tasks',$data);
		$this->load->view('checklist/footer');
	}

	private function saveTasks() {

		$this->load->library('mmail');
		$srl = serialize($this->input->post());
		$checklist_rec_id = $this->input->post('checklist_rec_id');
		$checklist_name = $this->input->post('checklist_name');
		$bu_name =  $this->session->all_userdata()['bu_name'];
		
		$date = date('Y-m-d H:i:s'); 

		//get checklist BU, then manager2 + admin email of this BU
		$id_bu =  $this->session->all_userdata()['bu_id'];
		$this->db->select('users.username, users.email, users.id');
		$this->db->distinct('users.username');
		$this->db->join('users_bus', 'users.id = users_bus.user_id', 'left');
		$this->db->join('users_groups', 'users.id = users_groups.user_id');
		$this->db->where('users.active', 1);
		$this->db->where_in('users_groups.group_id', array(1,4));
		$this->db->where('users_bus.bu_id', $id_bu);
		
		$query = $this->db->get("users");

		if($checklist_rec_id > 0) {
			$req = "UPDATE checklist_records SET `user` = '".$this->input->post('user')."', `date` = NOW(), `data` = '".$srl."' WHERE id = $checklist_rec_id";
			$email['subject'] = 'Checklist '.$checklist_name.' '. $bu_name .' UPDATED';

		} else {
			$req = "INSERT INTO checklist_records SET `user` = '".$this->input->post('user')."', `date` = NOW(), `id_checklist` = ".$this->input->post('id_checklist').", `data` = '".$srl."'";	
			$email['subject'] = 'Checklist '.$checklist_name.' '. $bu_name .' CREATED';
		}	

		$comment = false;
		$msg = '';

		$users_req = "SELECT `id`, `first_name`, `last_name`  FROM users WHERE id = ".$this->input->post('user');
		$users_res = $this->db->query($users_req) or die($this->mysqli->error);
		$user = $users_res->result();

		foreach ($this->input->post() as $key => $var) {

			$line = explode('-', $key);

			if($line[0] == 'comment' AND !empty($var)) {

				$checklist_task_req = "SELECT `name` FROM checklist_tasks WHERE id= ".$line[1];
				$checklist_task_res = $this->db->query($checklist_task_req) or die($this->mysqli->error);
				$checklist_tasks = $checklist_task_res->result();

				$comment = true;
				$msg .= "ALERTE! User ". $user[0]->first_name." ".$user[0]->last_name." says: ".$var." on task : ".$checklist_tasks[0]->name."\n";
				$email['subject'] .= " - ALERT COMMENT!";
			}					
		}

		if($comment) {
			$msg .= "\nRaw data: \n $srl";
		} else {
			$msg .= "All good!\nUser: ". $user[0]->first_name." ".$user[0]->last_name;
		}


		$email['msg'] = $msg;
		
		foreach ($query->result() as $row) {
			$email['to']	= $row->email;	
			$this->mmail->sendEmail($email);
		}
		
		if(!$this->db->query($req)) {
			echo $this->db->error;
			return false;
		}
		return true;
	}

}
