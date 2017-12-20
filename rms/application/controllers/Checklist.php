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
		$this->load->library('ion_auth');
		$this->load->library('ion_auth_acl');
		$this->load->library('hmw');
		$this->load->library('mmail');
		$this->load->database();
	}

	public function index()
	{
		$this->hmw->changeBu();// GENERIC changement de Bu

		$this->hmw->keyLogin();
		$id_bu = $this->session->userdata('bu_id');
		$type = $this->session->userdata('type');
		
		$msg = null;
		$form = $this->input->post();
		if(isset($form)) {
			if ($this->input->post('action') == 'save_tasks') {
				if($this->saveTasks()) {
					$msg = "RECORDED ON: ".date('Y-m-d H:i');
				} else {
					$msg = "WARNING NO RECORD";
				}
			}
		}
		$this->db->select('name, id, type')->from('checklists')->where('active',1)->where('id_bu', $id_bu);
		if ($type != false) $this->db->where('type', $type);
		$this->db->order_by('order asc');
		$checklist_res =  $this->db->get();
		$checklists = $checklist_res->result_array();

		$data = array(
			'msg'			=> $msg,
			'keylogin'		=> $this->session->userdata('keylogin'),	
			'checklists'	=> $checklists);

		$data['bu_name'] 	=  $this->session->userdata('bu_name');
		$data['username'] 	= $this->session->userdata('identity');
		$data['type'] 		= $type;
		$headers 			= $this->hmw->headerVars(1, "/checklist/", "Checklist");
		
		$this->load->view('jq_header_pre', $headers['header_pre']);
		$this->load->view('checklist/jq_header_spe');
		$this->load->view('jq_header_post', $headers['header_post']);
		$this->load->view('checklist/checklist',$data);
		$this->load->view('jq_footer');

	}

	public function viewCklPreviousTasks()
	{		

		$this->hmw->keyLogin();
		$id_bu =  $this->session->userdata('bu_id');
		
		if (isset($type)) {
			$this->db->select('r.user, u.first_name as first_name, u.last_name as last_name, r.id as lid, r.id_checklist, r.date, c.name')->from('checklist_records as r')->join('checklists as c', 'c.id = r.id_checklist')->join('users as u', 'r.user = u.id')->where(array('c.id_bu' => $id_bu, 'c.type' => $type))->order_by('r.date desc')->limit(50);
		} else {
			$this->db->select('r.user, u.first_name as first_name, u.last_name as last_name, r.id as lid, r.id_checklist, r.date, c.name, c.type')->from('checklist_records as r')->join('checklists as c', 'c.id = r.id_checklist')->join('users as u', 'r.user = u.id')->where('c.id_bu', $id_bu)->order_by('r.date desc')->limit(50);
		}
		$checklist_rec_res = $this->db->get() or die($this->mysqli->error);
		$checklist_rec = $checklist_rec_res->result_array();

		$data = array(
			'checklists_rec'	=> $checklist_rec,
			);

		$data['bu_name'] =  $this->session->userdata('bu_name');
		$data['username'] = $this->session->userdata('identity');

		$headers = $this->hmw->headerVars(0, "/checklist/", "Checklist View Record");
		$this->load->view('jq_header_pre', $headers['header_pre']);
		$this->load->view('checklist/jq_header_spe');
		$this->load->view('jq_header_post', $headers['header_post']);
		$this->load->view('checklist/checklist_prev',$data);
		$this->load->view('jq_footer');

	}

	public function viewCklTasks($id_ckl, $load = null)
	{		
		$this->hmw->keyLogin();
		$form = null;
		$checklist_rec_id = null;
		$id_bu =  $this->session->userdata('bu_id');
		$type = $this->session->userdata('type');

		if($load > 0) {
			$this->db->select('r.user, r.id as rec_id, r.data, r.id_checklist, r.date, c.name, c.type')->from('checklist_records as r')->join('checklists as c', 'c.id = r.id_checklist')->where('r.id', $load);
			$checklist_rec_res	= $this->db->get() or die($this->mysqli->error);
			$checklist_rec		= $checklist_rec_res->row();
			$form 				= unserialize($checklist_rec->data);
			$id_ckl				= $form['id_checklist'];
			$checklist_rec_id	= $checklist_rec->rec_id;
		}

		$this->db->select('name, id, type')->from('checklists')->where('id', $id_ckl);
		if ($type != false) $this->db->where('type', $type);
		$this->db->order_by('order asc');
		$checklist_res = $this->db->get();
		$checklists = $checklist_res->row();

		$this->db->select('name, id, comment, priority, day_month_num, day_week_num')->from('checklist_tasks')->where('active', 1)->where('id_checklist', $id_ckl)->order_by('order asc');
		$checklist_task_res = $this->db->get();
		$checklist_tasks = $checklist_task_res->result_array();

		$this->db->select('id, first_name, last_name')->from('users')->where('active', 1)->order_by('first_name asc');
		$users_res = $this->db->get() or die($this->mysqli->error);
		$users = $users_res->result_array();

		$this->db->select('users.username, users.last_name, users.first_name, users.email, users.id');
		$this->db->distinct('users.username');
		$this->db->join('users_bus', 'users.id = users_bus.user_id', 'left');
		$this->db->where('users.active', 1);
		$this->db->where('users_bus.bu_id', $id_bu);
		$this->db->order_by('users.username', 'asc');
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

		$data['bu_name'] =  $this->session->userdata('bu_name');
		$data['username'] = $this->session->userdata('identity');

		$headers = $this->hmw->headerVars(0, "/checklist/", "Checklist Record");
		$this->load->view('jq_header_pre', $headers['header_pre']);
		$this->load->view('checklist/jq_header_spe');
		$this->load->view('jq_header_post', $headers['header_post']);
		$this->load->view('checklist/checklist_tasks',$data);
		$this->load->view('jq_footer');
	}

	private function saveTasks() {

		$srl = serialize($this->input->post());
		$checklist_rec_id = $this->input->post('checklist_rec_id');
		$checklist_name = $this->input->post('checklist_name');
		$bu_name =  $this->session->userdata('bu_name');
		
		$date = date('Y-m-d H:i:s'); 
		
		//get checklist BU, then manager2 + admin email of this BU
		$id_bu =  $this->session->userdata('bu_id');
		$this->db->select('users.username, users.email, users.id');
		$this->db->distinct('users.username');
		$this->db->join('users_bus', 'users.id = users_bus.user_id', 'left');
		$this->db->join('users_groups', 'users.id = users_groups.user_id');
		$this->db->where('users.active', 1);
		$this->db->where_in('users_groups.group_id', array(1,4));
		$this->db->where('users_bus.bu_id', $id_bu);
		$this->db->order_by('users.username', 'asc');
		$query = $this->db->get("users");

		if($checklist_rec_id > 0) {
			$this->db->set('user', $this->input->post('user'));
			$this->db->set('data', $srl);
			$this->db->set('date', 'NOW()', FALSE);
			$req = $this->db->update('checklist_records')->where('id', $checklist_rec_id);
			$email['subject'] = 'Checklist '.$checklist_name.' '. $bu_name .' UPDATED';

		} else {
			$this->db->set('user', $this->input->post('user'));
			$this->db->set('id_checklist', $this->input->post('id_checklist'));
			$this->db->set('data', $srl);
			$this->db->set('date', 'NOW()', FALSE);
			$req = $this->db->insert('checklist_records');	
			$email['subject'] = 'Checklist '.$checklist_name.' '. $bu_name .' CREATED';
		}	

		$comment = false;
		$msg = '';

		$this->db->select('id, first_name, last_name')->from('users')->where('id', $this->input->post('user'));
		$users_res = $this->db->get() or die($this->mysqli->error);
		$user = $users_res->result();

		foreach ($this->input->post() as $key => $var) {

			$line = explode('-', $key);

			if($line[0] == 'comment' AND !empty(trim($var))) {

				$this->db->select('name')->from('checklist_tasks')->where('id', $line[1]);
				$checklist_task_res = $this->db->get() or die($this->mysqli->error);
				$checklist_tasks = $checklist_task_res->result();

				$comment = true;
				$msg .= "ALERTE! User ". $user[0]->first_name." ".$user[0]->last_name." says: \n".$var." \non task : ".$checklist_tasks[0]->name."\n";
				$email['subject'] .= " - ALERT COMMENT!";
			}					
		}

		$msg .= "All good!\nUser: ". $user[0]->first_name." ".$user[0]->last_name;
		$email['msg'] = $msg;

		foreach ($query->result() as $row) {
			$email['to']	= $row->email;	
			$this->mmail->sendEmail($email);
		}

		if(!$req) {
			echo $this->db->error;
			return false;
		}
		return true;
	}

	//20 12 * * * cd /var/www/hank/rms/rms && php index.php checklist clicCheck 3 1 > /dev/null 2>&1
	//30 23 * * * cd /var/www/hank/rms/rms && php index.php checklist clicCheck 4 1 > /dev/null 2>&1
	public function clicCheck($id, $id_bu)
	{

		if(is_cli()) {

			$this->db->select('checklists.name AS cname, bus.name AS bname');
			$this->db->join('bus', 'checklists.id_bu = bus.id', 'left');
			$this->db->where('checklists.id', $id);
			$this->db->where('checklists.id_bu', $id_bu);
			$query = $this->db->get("checklists");
			$info = $query->result();

			$msg = "WARNING! ".$info[0]->bname." No ".$info[0]->cname." checklist have been created!";

			$this->db->select("DATE(date)")->from('checklist_records as cr')
				->join('checklists as c','c.id = cr.id_checklist','left')
				->where("DATE(`date`) = DATE(NOW())")
				->where('cr.id_checklist', $id)
				->where('c.id_bu', $id_bu);
			$query	= $this->db->get();
			$res 	= $query->result();

			if(empty($res)) {	

				//get checklist BU, then manager2 + admin email of this BU
				$this->db->select('users.username, users.email, users.id');
				$this->db->distinct('users.username');
				$this->db->join('users_bus', 'users.id = users_bus.user_id', 'left');
				$this->db->join('users_groups', 'users.id = users_groups.user_id');
				$this->db->where('users.active', 1);
				$this->db->where_in('users_groups.group_id', array(1,4));
				$this->db->where('users_bus.bu_id', $id_bu);
				$query = $this->db->get("users");

				$email['subject'] 	= $msg;
				$email['msg'] 		= $msg;

				foreach ($query->result() as $row) {
					$email['to']	= $row->email;
					$this->mmail->sendEmail($email);
				}
				$this->hmw->sendNotif($msg, $id_bu);
			}
			return;
		} else { 
			echo "Access refused.";
			return; 
		}


	}

}
