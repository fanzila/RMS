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
		$this->load->library('ion_auth_acl');
		$this->load->library('hmw');
		$this->load->helper(array('form', 'url'));
		$this->load->library('form_validation');
	}

	public function index($id = null, $back=null)
	{
		$this->hmw->isLoggedIn();
		$id_bu = $this->session->userdata('bu_id');

		//TODO change somewhere here for disabling disabled users in sponsor
		$current_user = $this->ion_auth->get_user_id();
		if($id!= null && $id!=$current_user){
			$this->db->select('sr.id, us.id as id_sponsor, u.id as id_user')
				->from('skills_record as sr')
				->join('users as us', 'us.id = id_sponsor', 'left')
				->join('users as u', 'u.id = id_user', 'left')
				->where('id_user', $id)
				->where('id_bu', $id_bu);
				
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
				$this->hmw->isLoggedIn();
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
		$id_bu =  $this->session->userdata('bu_id');

		date_default_timezone_set('Europe/Paris');
		$this->db->select('R.id, RI.date, R.id_user, RI.checked, RI.comment, I.name as i_name, I.id as i_id, skills.name as s_name, cat.name as c_name, subcat.name as sub_name, skills.id as s_id, cat.id as c_id, I.link as i_link, subcat.id as sub_id')
			->from('skills_record as R')
			->join('skills_record_item as RI', 'R.id = RI.id_skills_record')
			->join('skills_item as I', 'I.id = RI.id_skills_item')
			->join('skills', 'skills.id = I.id_skills')
			->join('skills_category as cat', 'I.id_cat = cat.id')
			->join('skills_sub_category as subcat', 'I.id_sub_cat = subcat.id')
			->where('R.id_user', $id)
			->where('R.id_bu', $id_bu)
			->order_by('skills.order asc, cat.order asc, subcat.order asc, I.order asc');
		$res 	= $this->db->get() or die($this->mysqli->error);
		$skills_items = $res->result();

		$this->db->select('id, name')
			->from('skills')
			->where('id_bu', $id_bu)
			->order_by('order asc');
		$res 	= $this->db->get() or die($this->mysqli->error);
		$skills = $res->result();
		
		$this->db->select('id, name')
			->where('id_bu', $id_bu)
			->from('skills_category')
			->order_by('order asc');
		$res 	= $this->db->get() or die($this->mysqli->error);
		$skills_categories = $res->result();

		$this->db->select('id, name')
			->where('id_bu', $id_bu)
			->from('skills_sub_category')
			->order_by('order asc');
		$res 	= $this->db->get() or die($this->mysqli->error);
		$skills_sub_categories = $res->result();

		$data = array(
			'skills'	=> $skills,
			'skills_categories'	=> $skills_categories,
			'skills_sub_categories'	=> $skills_sub_categories,
			'skills_items'	=> $skills_items
			);
		$data['bu_name'] =  $this->session->userdata('bu_name');
		$data['username'] = $this->session->userdata('identity');
		
		//SELECT u.username, si.name, sri.checked FROM users AS u JOIN skills_record AS sr ON sr.id_user = u.id JOIN skills_record_item AS sri ON sri.id_skills_record = sr.id JOIN skills_item AS si ON sri.id_skills_item = si.id;
		
		if($id != $current_user){
			$data['userlevel'] = 1;
			if($bypass_sponsor!=1 || $back==1){
				$link = "/skills/admin";
			}else{
				$link = "/skills/start";
			}
			$headers = $this->hmw->headerVars(0, $link, "Skills of ".$user[0]->first_name." ".$user[0]->last_name);
			$this->load->view('jq_header_pre', $headers['header_pre']);
			$this->load->view('jq_header_post', $headers['header_post']);
			$this->load->view('skills/index',$data);
			$this->load->view('jq_footer');
		}else{
			$data['userlevel'] = 0;
			$headers = $this->hmw->headerVars(0, "/skills/start/", "My Skills");
			$this->load->view('jq_header_pre', $headers['header_pre']);
			$this->load->view('jq_header_post', $headers['header_post']);
			$this->load->view('skills/index',$data);
			$this->load->view('jq_footer');
		}
	}
	
	public function general()
	{
		if (!$this->ion_auth_acl->has_permission('skills_manage_crud')) {
			$this->hmw->isLoggedIn();
			redirect('skills', 'refresh');
		}
		$this->hmw->changeBu();
		$id_bu = $this->session->userdata('bu_id');
		
		$headers = $this->hmw->headerVars(1, "/skills/general", "Manage Skills");
		$this->load->view('jq_header_pre', $headers['header_pre']);
		$this->load->view('jq_header_post', $headers['header_post']);
		$this->load->view('skills/general');
		$this->load->view('jq_footer');
	}
	
	public function admin()
	{
		if (!$this->ion_auth_acl->has_permission('skills_admin')) {
			$this->hmw->isLoggedIn();
			redirect('skills', 'refresh');
		}
		$this->hmw->changeBu();// GENERIC changement de Bu
		$id_bu =  $this->session->userdata('bu_id');
		$users_group_id = array(1,2,3,4,6);
		/* SPECIFIC Recuperation depuis la base de donnees des informations users */
		$this->db->select('users.username, users.last_name, users.first_name, users.email, users.id, sr.id_sponsor');
		$this->db->distinct('users.username');
		$this->db->join('users_bus', 'users.id = users_bus.user_id', 'left');
		$this->db->join('users_groups', 'users.id = users_groups.user_id', 'left');
		$this->db->join('skills_record AS sr', 'users.id = sr.id_user', 'left');
		$this->db->where('users.active', 1);
		$this->db->where_in('users_groups.group_id', $users_group_id);
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
			->where('R.id_bu', $id_bu)
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
			->where('R.id_bu', $id_bu)
			->order_by('I.name desc');
		$res 	= $this->db->get() or die($this->mysqli->error);
		$skills_items = $res->result();
		
		/** ************************* SKILLS MAP START ************************** **/
	
	
		$this->db->select('DISTINCT(u.id), u.username, u.last_name, u.first_name, sr.id_sponsor');
		$this->db->from('skills_record AS sr');
		$this->db->join('users AS u', 'u.id = sr.id_user', 'left');
		$this->db->join('users_bus', 'u.id = users_bus.user_id', 'left');
		$this->db->join('users_groups', 'u.id = users_groups.user_id', 'left');
		$this->db->where('u.active', 1);
		$this->db->where_in('users_groups.group_id', $users_group_id);
		$this->db->where('users_bus.bu_id', $id_bu);
		$this->db->order_by('u.username', 'asc');
		$query = $this->db->get("users");
		$userswithsponsor = $query->result();
		
		$checked_subcat_byuser = array();
		
		date_default_timezone_set('Europe/Paris');
		$this->db->select('DISTINCT(subcat.id) as ssc_id, cat.name as c_name, subcat.name as sub_name, skills.name as s_name')
			->from('skills_record as R')
			->join('skills_record_item as RI', 'R.id = RI.id_skills_record')
			->join('skills_item as I', 'I.id = RI.id_skills_item')
			->join('skills', 'skills.id = I.id_skills')
			->join('skills_category as cat', 'I.id_cat = cat.id')
			->join('skills_sub_category as subcat', 'I.id_sub_cat = subcat.id')
			->where('skills.id_bu', $id_bu)
			->where('R.id_bu', $id_bu)	
			->group_by('subcat.id')	
			->order_by('skills.order, cat.order, subcat.order, I.order ASC');
		$res 	= $this->db->get() or die($this->mysqli->error);
		$skills_items_map = $res->result();
		
		foreach ($this->hmw->getUsers() as $user) {
			
			$this->db->select('id')
				->from('skills_sub_category');
			$res 	= $this->db->get() or die($this->mysqli->error);
			$subcat = $res->result();
			
			foreach ($subcat as $subcatlist) {
					
				$q = $this->db->select("ssc.id AS ssc_id, sri.checked AS sri_checked")
					->from('skills_record_item AS sri')
					->join('skills_record AS sr','sr.id = sri.id_skills_record')
					->join('skills_item AS si','si.id = sri.id_skills_item')
					->join('skills_sub_category AS ssc','ssc.id=si.id_sub_cat')
					->join('skills AS s','s.id = si.id_skills')
					->where("s.id_bu = $id_bu")
					->where("sr.id_user = $user->id")
					->where("ssc.id = $subcatlist->id")
					->order_by('sri.checked ASC')
					->limit('1');
				$res = $this->db->get() or die($this->mysqli->error);
				$sum = $res->result_array();
				if(isset($sum[0])) {
					$checked_subcat_byuser[$user->id][$sum[0]['ssc_id']] = $sum[0]['sri_checked'];
				}	
			}
		}
						
		/** ************************* SKILLS MAP END ************************** **/
		
		$this->db->select('id, name')
			->from('skills')
			->where('id_bu', $id_bu)
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

		$this->db->select('sr.id, sr.id_user, us.username as sponsorname, u.username, ub.bu_id')
			->from('skills_record as sr')
			->join('users as us', 'us.id = id_sponsor', 'left')
			->join('users as u', 'u.id = id_user', 'left')
			->join('users_bus as ub', 'ub.user_id = u.id', 'left')
			->where('u.active', 1)
			->where('id_bu', $id_bu)
			->order_by('sponsorname asc');
		$res 	= $this->db->get() or die($this->mysqli->error);
		$skills_records = $res->result();


		$this->db->select('sl.id as id, sl.type, uv.username as toucheduser, si.name, u.username as username, sli.checked as checked, sli.comment as comment, sl.date as date')
			->from('skills_log as sl')
			->join('skills_log_item as sli', 'sli.id_log = sl.id', 'left')
			->join('skills_record_item as ri', 'ri.id = sli.id_record_item', 'left')
			->join('skills_item as si', 'si.id = ri.id_skills_item', 'left')
			->join('users as u', 'u.id = id_user', 'left')
			->join('skills_record as sr', 'sr.id = sl.id_skills_record')
			->join('users as uv', 'uv.id = sr.id_user', 'left')
			->where('sl.id_bu', $id_bu)
			->order_by('date desc')
			->limit(100);
		$res 	= $this->db->get() or die($this->mysqli->error);
		$skills_logs = $res->result();


		$data = array(
			'skills'	=> $skills,
			'skills_categories'	=> $skills_categories,
			'skills_sub_categories'	=> $skills_sub_categories,
			'skills_items'	=> $skills_items,
			'skills_staff' => $skills_staff,
			'skills_records' => $skills_records,
			'skills_logs' => $skills_logs,
			'skills_items_map' => $skills_items_map,
			'checked_subcat_byuser' => $checked_subcat_byuser,
			'userswithsponsor' => $userswithsponsor,
			'current_user' => $this->ion_auth->get_user_id()
			);

		$user_groups	= $this->ion_auth->get_users_groups()->result();
		$data['level']	= $user_groups[0]->level;
		$data['users'] = $users;
		$headers = $this->hmw->headerVars(1, "/skills/admin", "Skills Management");
		$this->load->view('jq_header_pre', $headers['header_pre']);
		$this->load->view('jq_header_post', $headers['header_post']);
		$this->load->view('skills/admin', $data);
		$this->load->view('jq_footer');
	}

	public function start()
	{
		$this->hmw->isLoggedIn();
		$this->hmw->changeBu();// GENERIC changement de Bu
		$id_bu =  $this->session->userdata('bu_id');
		$bu_name	= $this->session->userdata('bu_name');

		$this->db->select('sr.id, us.id as id_sponsor, u.first_name, u.last_name, u.id as id_user')
			->from('skills_record as sr')
			->join('users as us', 'us.id = id_sponsor', 'left')
			->join('users as u', 'u.id = id_user', 'left')
			->where('id_bu', $id_bu);
			
		$res 	= $this->db->get() or die($this->mysqli->error);
		$skills_records = $res->result();

		$data = array(
			'skills_records' => $skills_records,
			'id_bu'			=> $id_bu,
			'bu_name'		=> $bu_name,
			'current_user' => $this->ion_auth->get_user_id()
			);

		$headers = $this->hmw->headerVars(1, "/skills/start/", "My space");
		$this->load->view('jq_header_pre', $headers['header_pre']);
		$this->load->view('jq_header_post', $headers['header_post']);
		$this->load->view('skills/start', $data);
		$this->load->view('jq_footer');
	}


	/*function to add new skills, cat and sub-cat.*/
	public function create($id=null)
	{
		$data = $this->input->post();
		$i=-1;
		$exist=false;
		$reponse = 'ok';
		
		$this->db->select('name');
		if($id=='skill'){
			$this->db->from('skills');
			$i=1;
		}else if($id=='cat'){
			$this->db->from('skills_category');
			$i=2;
		}else if($id=='subcat'){
			$this->db->from('skills_sub_category');
			$i=3;
		}
		$res 	= $this->db->get() or die($this->mysqli->error);
		$results = $res->result();

		foreach ($results as $result) {
			if($result->name==$data['name'][$i]){
				$exist=true;
			}
		}

		if($exist==false){
			$this->db->trans_start();
				$this->db->set('name', $data['name'][$i]);
				if($id=='skill'){
					if(!$this->db->insert('skills')) {
						$response = "Can't place the insert sql request, error message: ".$this->db->_error_message();
					}
				}else if($id=='cat'){
					if(!$this->db->insert('skills_category')) {
						$response = "Can't place the insert sql request, error message: ".$this->db->_error_message();
					}
				}else if($id=='subcat'){
					if(!$this->db->insert('skills_sub_category')) {
						$response = "Can't place the insert sql request, error message: ".$this->db->_error_message();
					}
				}
				if(!$this->db->insert_id()) {
					$response = "Can't place the insert sql request, error message: ".$this->db->_error_message();
				}
			$this->db->trans_complete();
		}

		echo json_encode(['reponse' => $reponse]);
	}

		public function createItem()
	{
		$data = $this->input->post();
		$exist=false;
		$reponse = 'ok';
		
		$this->db->select('name, id_skills, id_cat, id_sub_cat');
		$this->db->from('skills_item');
		$res 	= $this->db->get() or die($this->mysqli->error);
		$results = $res->result();

		foreach ($results as $result) {
			if($result->name==$data['s_item'] ){
				if($result->id_skills == $data['s_skill'] || $result->id_cat == $data['s_cat'] || $result->id_sub_cat == $data['s_subcat']){
					$exist=true;
				}
				
			}
		}

		if($exist==false){
			$this->db->trans_start();
				$this->db->set('name',		$data['s_item']);
				$this->db->set('id_skills',	$data['s_skill']);
				$this->db->set('id_cat',	$data['s_cat']);
				$this->db->set('id_sub_cat',$data['s_subcat']);
				
				if(!$this->db->insert('skills_item')) {
					$response = "Can't place the insert sql request, error message: ".$this->db->_error_message();
				}
				if(!$this->db->insert_id()) {
					$response = "Can't place the insert sql request, error message: ".$this->db->_error_message();
				}
			$this->db->trans_complete();
		}else{
			$reponse = 'The item already exist!';
		}

		echo json_encode(['reponse' => $reponse]);
	}

	public function save()//here we create a sponsoring link
	{		
		$data = $this->input->post();
		$id_bu 		= $this->session->userdata('bu_id');
		
		$this->db->select('sr.id_user, us.username as sponsor_name')
			->from('skills_record as sr')
			->join('users as us', 'us.id = id_sponsor', 'left')
			->where('id_bu', $id_bu);
		$res 	= $this->db->get() or die($this->mysqli->error);
		$users = $res->result();

		$check=0;
		$reponse = 'ok';
		$current_user = $this->ion_auth->get_user_id();
		foreach ($users as $user) {
			if($data['user']==$user->id_user){
				$check+=1;
				$reponse = "This user already has a sponsor (".$user->sponsor_name.")";
				break;
			}
		}
		if($check==0){
			$this->db->select('I.id')
				->from('skills_item as I')
				->join('skills', 'skills.id = I.id_skills')
				->join('skills_category as cat', 'I.id_cat = cat.id')
				->join('skills_sub_category as subcat', 'I.id_sub_cat = subcat.id', 'left')
				->where('skills.id_bu', $id_bu)
				->where('skills.deleted', 0)
				->where('I.deleted', 0)
				->where('cat.deleted', 0)
				->where('subcat.deleted', 0)//
			;
			$res = $this->db->get() or die($this->mysqli->error);
			$skills_items = $res->result();

			$this->db->set('id_sponsor', $data['sponsor']);
			$this->db->set('id_user', $data['user']);
			$this->db->set('id_bu', $id_bu);
			$date = date('Y-m-d H:i:s');
			$this->db->set('date', $date);

			$this->db->trans_start();
				if(!$this->db->insert('skills_record')) {
					$response = "Can't place the insert sql request, error message: ".$this->db->_error_message();
				}
				$data['id'] = $this->db->insert_id();

				$this->db->set('id_user', $current_user);
				$this->db->set('date', $date);
				$this->db->set('id_skills_record', $data['id']);
				$this->db->set('id_bu', $this->session->userdata('bu_id'));
				if(!$this->db->insert('skills_log')) {
					$response = "Can't place the insert sql request in log, error message: ".$this->db->_error_message();
				}
				$this->db->insert_id();

				foreach ($skills_items as $skills_item) {
					$this->db->set('id_skills_record', $data['id']);
					$this->db->set('id_skills_item', $skills_item->id);
					$this->db->set('checked', false);//set all skills at false by default
					$this->db->set('comment', "");//set with the comment 'creation' to avoid incomprehension
					if(!$this->db->insert('skills_record_item')){
						$response = "Can't place the insert sql request, error message: ".$this->db->_error_message();
					}
					$this->db->insert_id();
				}
			$this->db->trans_complete();
		}		

		echo json_encode(['reponse' => $reponse]);
	}

	public function saveSkills()
	{
		$reponse = 'ok';
		$i = 0;
		$changed=0;
		$checked=0;
		$comment=0;
		$error=0;
		$id_log=0;
		$test_rep=0;
		$current_user = $this->ion_auth->get_user_id();

		$data = $this->input->post();
		date_default_timezone_set('Europe/Paris');
		$date = date('Y-m-d H:i:s');

		$this->db->trans_start();
			for($i ; $i<$data['i'] ; $i++){
				if($error==0){
					$comment=0;
					$changed=0;
					$checked=0;
					$this->db->select('id, checked, comment')->from('skills_record_item')->where('id_skills_item', $data['id_item'][$i])->where('id_skills_record', $data['id_record']);
					$int = $this->db->get();
					$test = $int->result();
					if(isset($data['checked'][$i])){
						if($test[0]->checked==0){
							$this->db->set('checked', 1);
							$checked=11;
							$changed=1;
						}
					}else{
						if($test[0]->checked==1){
							$this->db->set('checked', 0);
							$checked=10;
							$changed=1;
						}
					}
					if($test[0]->comment != $data['comment'][$i]){
						$this->db->set('comment', $data['comment'][$i]);
						$comment=1;
						$changed=1;
					}
					if($changed==1){
						$this->db->set('date', $date);
						$this->db->from('skills_record_item');
						$this->db->where('id_skills_item', $data['id_item'][$i]);
						$this->db->where('id_skills_record', $data['id_record']);
						if(!$this->db->update('skills_record_item')) {
							$response = "Can't place the insert sql request (line ".$i."), error message: ".$this->db->_error_message();
							$error++;
						}
						if($test_rep==0){
							/*add a line in the main log*/
							$this->db->set('id_user', $current_user);
							$this->db->set('date', $date);
							$this->db->set('type', 'edit');
							$this->db->set('id_skills_record', $data['id_record']);
							$this->db->set('id_bu', $this->session->userdata('bu_id'));
							if(!$this->db->insert('skills_log')) {
								$response = "Can't place the insert sql request in log, error message: ".$this->db->_error_message();
							}
							$id_log = $this->db->insert_id();
						}
						if($error==0){
							/*add a line in the log_item for each modified item*/
							$this->db->set('id_log', $id_log);
							$this->db->set('id_record_item', $test[0]->id);
							if($checked==10)$this->db->set('checked', 'NO');
							if($checked==11)$this->db->set('checked', 'YES');
							if($comment==1)$this->db->set('comment', $data['comment'][$i]);
							$this->db->insert('skills_log_item');
							$this->db->insert_id();
						}
						
						$test_rep++;
					}
				}
			}
			if($error==0 && $test_rep>0){
				/*update the date of the main record*/
				$this->db->set('date', $date);
				$this->db->from('skills_record');
				$this->db->where('id', $data['id_record']);
				if(!$this->db->update('skills_record')) {
					$response = "Can't place the insert sql request (id_record), error message: ".$this->db->_error_message();
				}
			}	
		$this->db->trans_complete();

		if($error>0){

		}else if($test_rep>0){
			$reponse = 'ok';
		}else{
			$reponse = 'Nothing to change!';
		}

		echo  json_encode(['reponse' => $reponse]);
	}

  public function duplicate($id)
  {
		$this->hmw->isLoggedIn();
		$current_user = $this->ion_auth->get_user_id();

    $this->load->model('skill_model');
    $skill = $this->skill_model->getOne($id, true);

    if (!$this->input->post('id_bu'))
      return $this->duplicateForm($skill, $current_user);

    $new_id_bu = $this->input->post('id_bu');

    if ($this->skill_model->alreadyExists($skill->name, $new_id_bu))
      return $this->duplicateForm($skill, $current_user, 'A skill with this name already exists in the required BU');

    if (!$this->skill_model->duplicate($id, $new_id_bu))
      return $this->duplicateForm($skill, $current_user, 'Duplication did not work');

    $this->hmw->changeBu($new_id_bu);
    redirect('/skills/general');
  }

  function duplicateForm($skill, $user, $error = null)
  {
    $bus = $this->hmw->getBus(null, $user);

    $headers = $this->hmw->headerVars(0, '/crud/skills', 'Duplicate skill ' . $skill->name);

    $data = [
      'skill' => $skill,
      'bus'   => $bus,
      'error' => $error
    ];

    $this->load->view('jq_header_pre', $headers['header_pre']);
    $this->load->view('jq_header');
    $this->load->view('jq_header_post', $headers['header_post']);
    $this->load->view('skills/duplicate', $data);
    $this->load->view('jq_footer');
  }
}
