<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Skills extends CI_Controller {

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
		$this->load->database();
		$this->load->library('ion_auth');
		$this->load->library('hmw');
		$this->load->helper(array('form', 'url'));
		$this->load->library('form_validation');
	}

	public function index($id = null)
	{
		$this->hmw->changeBu();// GENERIC changement de Bu
		$current_user = $this->ion_auth->get_user_id();
		if($id!= null && $id!=$current_user){
			$this->db->select('sr.id, us.id as id_sponsor, u.id as id_user')
				->from('skills_record as sr')
				->join('users as us', 'us.id = id_sponsor', 'left')
				->join('users as u', 'u.id = id_user', 'left')
				->where('id_user', $id);
			$res 	= $this->db->get() or die($this->mysqli->error);
			$skills_records = $res->result();
			if($skills_records!=null){
				if($skills_records[0]->id_sponsor==$current_user){
					$bypass_sponsor=1;
				}else{
					$bypass_sponsor=0;
				}
			}else{
				$bypass_sponsor=0;
			}
			if($bypass_sponsor==0 && (!$this->ion_auth->is_admin() && !($this->ion_auth->user()->row()->id == $id))){
				if(!$this->ion_auth->logged_in()){
					redirect('news', 'refresh');
				}
				redirect('skills', 'refresh');
			}else{
				$this->db->select('users.username, users.last_name, users.first_name, users.email, users.id');
				$this->db->distinct('users.username');
				$this->db->join('users_bus', 'users.id = users_bus.user_id', 'left');
				$this->db->where('users.id', $id);
				$this->db->order_by('users.username', 'asc');
				$query = $this->db->get("users");
				$user = $query->result();
			}
		}else{
			$id = $current_user;
		}
		$id_bu =  $this->session->all_userdata()['bu_id'];

		/* SPECIFIC Recuperation depuis la base de donnees des informations discounts */
		date_default_timezone_set('Europe/Paris');
		$this->db->select('R.id, RI.date, R.id_user, RI.checked, RI.comment, I.name as i_name, I.id as i_id, skills.name as s_name, cat.name as c_name, subcat.name as sub_name, skills.id as s_id, cat.id as c_id, subcat.id as sub_id')
			->from('skills_record as R')
			->join('skills_record_item as RI', 'R.id = RI.id_skills_record')
			->join('skills_item as I', 'I.id = RI.id_skills_item')
			->join('skills', 'skills.id = I.id_skills')
			->join('skills_category as cat', 'I.id_cat = cat.id')
			->join('skills_sub_category as subcat', 'I.id_sub_cat = subcat.id')
			->where('R.id_user', $id)
			->order_by('I.name desc');
		$res 	= $this->db->get() or die($this->mysqli->error);
		$skills_items = $res->result();

		$this->db->select('id, name')
			->from('skills')
			->order_by('name desc');
		$res 	= $this->db->get() or die($this->mysqli->error);
		$skills = $res->result();

		$this->db->select('id, name')
			->from('skills_category')
			->order_by('name desc');
		$res 	= $this->db->get() or die($this->mysqli->error);
		$skills_categories = $res->result();

		$this->db->select('id, name')
			->from('skills_sub_category')
			->order_by('name desc');
		$res 	= $this->db->get() or die($this->mysqli->error);
		$skills_sub_categories = $res->result();



		$data = array(
			'skills'	=> $skills,
			'skills_categories'	=> $skills_categories,
			'skills_sub_categories'	=> $skills_sub_categories,
			'skills_items'	=> $skills_items
			);
		$data['bu_name'] =  $this->session->all_userdata()['bu_name'];
		$data['username'] = $this->session->all_userdata()['identity'];

		if($id != $current_user){
			$data['userlevel'] = 1;
			if($bypass_sponsor!=1){
				$link = "/skills/admin";
			}else{
				$link = "/skills/sponsor";
			}
			$headers = $this->hmw->headerVars(0, $link, "Skills of ".$user[0]->first_name." ".$user[0]->last_name);
			$this->load->view('jq_header_pre', $headers['header_pre']);
			$this->load->view('skills/jq_header_spe');
			$this->load->view('jq_header_post', $headers['header_post']);
			$this->load->view('skills/index',$data);
			$this->load->view('jq_footer');
		}else{
			$data['userlevel'] = 0;
			$headers = $this->hmw->headerVars(1, "/skills/", "My Skills");
			$this->load->view('jq_header_pre', $headers['header_pre']);
			$this->load->view('skills/jq_header_spe');
			$this->load->view('jq_header_post', $headers['header_post']);
			$this->load->view('skills/index',$data);
			$this->load->view('jq_footer');
		}
	}
	
	public function admin()
	{
		if (!$this->ion_auth->is_admin()) {
			if(!$this->ion_auth->logged_in()){
				redirect('news', 'refresh');
			}
			redirect('skills', 'refresh');
		}
		$id_bu =  $this->session->all_userdata()['bu_id'];
		/* SPECIFIC Recuperation depuis la base de donnees des informations users */
		$this->db->select('users.username, users.last_name, users.first_name, users.email, users.id');
		$this->db->distinct('users.username');
		$this->db->join('users_bus', 'users.id = users_bus.user_id', 'left');
		$this->db->where('users.active', 1);
		$this->db->where('users_bus.bu_id', $id_bu);
		$this->db->order_by('users.username', 'asc');
		$query = $this->db->get("users");
		$users = $query->result();

/* SPECIFIC used for consulting a staff's qualifications*/
		date_default_timezone_set('Europe/Paris');
		$this->db->select('R.id, R.id_user, RI.checked, RI.comment, I.name as i_name, skills.name as s_name, cat.name as c_name, subcat.name as sub_name')
			->from('skills_record as R')
			->join('skills_record_item as RI', 'R.id = RI.id_skills_record')
			->join('skills_item as I', 'I.id = RI.id_skills_item')
			->join('skills', 'skills.id = I.id_skills')
			->join('skills_category as cat', 'I.id_cat = cat.id')
			->join('skills_sub_category as subcat', 'I.id_sub_cat = subcat.id')
			->order_by('I.name desc');
		$res 	= $this->db->get() or die($this->mysqli->error);
		$skills_staff = $res->result();

		/* SPECIFIC used for creating new sponsorship link */
		date_default_timezone_set('Europe/Paris');
		$this->db->select('R.id, R.id_user, RI.checked, RI.comment, I.name as i_name, skills.name as s_name, cat.name as c_name, subcat.name as sub_name')
			->from('skills_record as R')
			->join('skills_record_item as RI', 'R.id = RI.id_skills_record')
			->join('skills_item as I', 'I.id = RI.id_skills_item')
			->join('skills', 'skills.id = I.id_skills')
			->join('skills_category as cat', 'I.id_cat = cat.id')
			->join('skills_sub_category as subcat', 'I.id_sub_cat = subcat.id')
			->order_by('I.name desc');
		$res 	= $this->db->get() or die($this->mysqli->error);
		$skills_items = $res->result();

		$this->db->select('id, name')
			->from('skills')
			->order_by('name desc');
		$res 	= $this->db->get() or die($this->mysqli->error);
		$skills = $res->result();

		$this->db->select('id, name')
			->from('skills_category')
			->order_by('name desc');
		$res 	= $this->db->get() or die($this->mysqli->error);
		$skills_categories = $res->result();

		$this->db->select('id, name')
			->from('skills_sub_category')
			->order_by('name desc');
		$res 	= $this->db->get() or die($this->mysqli->error);
		$skills_sub_categories = $res->result();

		$this->db->select('sr.id, us.username as sponsorname, u.username')
			->from('skills_record as sr')
			->join('users as us', 'us.id = id_sponsor', 'left')
			->join('users as u', 'u.id = id_user', 'left')
			->order_by('sponsorname asc');
		$res 	= $this->db->get() or die($this->mysqli->error);
		$skills_records = $res->result();


		$data = array(
			'skills'	=> $skills,
			'skills_categories'	=> $skills_categories,
			'skills_sub_categories'	=> $skills_sub_categories,
			'skills_items'	=> $skills_items,
			'skills_staff' => $skills_staff,
			'skills_records' => $skills_records,
			'current_user' => $this->ion_auth->get_user_id()
			);

		$data['users'] = $users;
		$headers = $this->hmw->headerVars(1, "/skills/admin", "Skills Management");
		$this->load->view('jq_header_pre', $headers['header_pre']);
		$this->load->view('skills/jq_header_spe');
		$this->load->view('jq_header_post', $headers['header_post']);
		$this->load->view('skills/admin', $data);
		$this->load->view('jq_footer');
	}

	public function sponsor()
	{
		if (!$this->ion_auth->logged_in()) {
				redirect('news', 'refresh');
		}
		$id_bu =  $this->session->all_userdata()['bu_id'];

		$this->db->select('sr.id, us.id as id_sponsor, u.first_name, u.last_name, u.id as id_user')
			->from('skills_record as sr')
			->join('users as us', 'us.id = id_sponsor', 'left')
			->join('users as u', 'u.id = id_user', 'left');
		$res 	= $this->db->get() or die($this->mysqli->error);
		$skills_records = $res->result();

		$data = array(
			'skills_records' => $skills_records,
			'current_user' => $this->ion_auth->get_user_id()
			);

		$headers = $this->hmw->headerVars(1, "/skills/sponsor", "Skills Sponsoring");
		$this->load->view('jq_header_pre', $headers['header_pre']);
		$this->load->view('skills/jq_header_spe');
		$this->load->view('jq_header_post', $headers['header_post']);
		$this->load->view('skills/sponsor', $data);
		$this->load->view('jq_footer');
	}

	/*TODO : function to add new items to skills.*/
	public function create()
	{/*
		if (!$this->ion_auth->is_admin()) {
			if(!$this->ion_auth->logged_in()){
				redirect('news', 'refresh');
			}
			redirect('skills', 'refresh');
		}
		
	*/}

	public function save()
	{		
		$data = $this->input->post();
		$this->db->select('sr.id_user, us.username as sponsor_name')
			->from('skills_record as sr')
			->join('users as us', 'us.id = id_sponsor', 'left');
		$res 	= $this->db->get() or die($this->mysqli->error);
		$users = $res->result();

		$check=0;
		$reponse = 'ok';
		foreach ($users as $user) {
			if($data['user']==$user->id_user){
				$check+=1;
				$reponse = "This user already has a sponsor (".$user->sponsor_name.")";
				break;
			}
		}
		if($check==0){
			$this->db->select('id')->from('skills_item');
			$res = $this->db->get() or die($this->mysqli->error);
			$skills_items = $res->result();

			$this->db->set('id_sponsor', $data['sponsor']);
			$this->db->set('id_user', $data['user']);
			$date = date('Y-m-d H:i:s');
			$this->db->set('date', $date);

			$this->db->trans_start();
				if(!$this->db->insert('skills_record')) {
					$response = "Can't place the insert sql request, error message: ".$this->db->_error_message();
				}
				$data['id'] = $this->db->insert_id();
				foreach ($skills_items as $skills_item) {
					$this->db->set('id_skills_record', $data['id']);
					$this->db->set('id_skills_item', $skills_item->id);
					$this->db->set('date', $date);
					$this->db->set('checked', false);//set all skills at false by default
					$this->db->set('comment', "creation");//set with the comment 'creation' to avoid incomprehension
					$this->db->insert('skills_record_item');
					$this->db->insert_id();
				}
			$this->db->trans_complete();
		}		

		echo json_encode(['reponse' => $reponse]);
	}

	private function saveSkills() {//FIXME!!!!

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

			if($line[0] == 'comment' AND !empty($var)) {

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

/*	public function log($admin = null)
	{
		$this->hmw->keyLogin();
		$id_bu =  $this->session->all_userdata()['bu_id'];
		//mettre un checkpoint de droit d'accès TODO pour admin
		$this->db->select('l.id, l.id_skills_record, l.date, r.id_sponsor, i.name, ri.checked, ri.comment, u.username')
			->from('skills_log as l')
			->join('skills_record as r', 'r.id = l.id_skills_record', 'left')
			->join('skills_record_item as ri', 'ri.id_skills_record = r.id', 'left')
			->join('skills_item as i', 'i.id = ri.id_skills_item', 'left')
			->join('users as u', 'u.id = r.id_sponsor', 'left')
			->where('r.id_user', $this->ion_auth->get_user_id())
			->order_by('l.date desc')
			->limit(100);
		$res 	= $this->db->get() or die($this->mysqli->error);
		$skills = $res->result();
		$data = array(
			'skills'	=> $skills,
			'bu_name' 	=> $this->session->all_userdata()['bu_name'],
			'username' 	=> $this->session->all_userdata()['identity']
			);

		
	 	$headers = $this->hmw->headerVars(0, "/skills/", "Skills Log");
		$this->load->view('jq_header_pre', $headers['header_pre']);
		$this->load->view('jq_header_post', $headers['header_post']);
		$this->load->view('skills/log',$data);
		$this->load->view('jq_footer');
	}*/
}
