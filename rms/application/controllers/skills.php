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
		if(!$this->ion_auth->logged_in()){
			redirect('news', 'refresh');
		}
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
				$link = "/skills/start";
			}
			$headers = $this->hmw->headerVars(0, $link, "Skills of ".$user[0]->first_name." ".$user[0]->last_name);
			$this->load->view('jq_header_pre', $headers['header_pre']);
			$this->load->view('skills/jq_header_spe');
			$this->load->view('jq_header_post', $headers['header_post']);
			$this->load->view('skills/index',$data);
			$this->load->view('jq_footer');
		}else{
			$data['userlevel'] = 0;
			$headers = $this->hmw->headerVars(0, "/skills/start/", "My Skills");
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


		$this->db->select('sl.id as id, sl.type, u.username as username, sli.checked as checked, sli.comment as comment, sl.date as date')
			->from('skills_log as sl')
			->join('skills_log_item as sli', 'sli.id_log = sl.id', 'left')
			->join('users as u', 'u.id = id_user', 'left')
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

	public function start()
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

		$headers = $this->hmw->headerVars(1, "/skills/start/", "Skills");
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
		$this->db->select('sr.id_user, us.username as sponsor_name')
			->from('skills_record as sr')
			->join('users as us', 'us.id = id_sponsor', 'left');
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

				$this->db->set('id_user', $current_user);
				$this->db->set('date', $date);
				$this->db->set('id_skills_record', $data['id']);
				if(!$this->db->insert('skills_log')) {
					$response = "Can't place the insert sql request in log, error message: ".$this->db->_error_message();
				}
				$this->db->insert_id();

				foreach ($skills_items as $skills_item) {
					$this->db->set('id_skills_record', $data['id']);
					$this->db->set('id_skills_item', $skills_item->id);
					$this->db->set('date', $date);
					$this->db->set('checked', false);//set all skills at false by default
					$this->db->set('comment', "creation");//set with the comment 'creation' to avoid incomprehension
					if(!$this->db->insert('skills_record_item')){
						$response = "Can't place the insert sql request, error message: ".$this->db->_error_message();
					}
					$this->db->insert_id();
				}
			$this->db->trans_complete();
		}		

		echo json_encode(['reponse' => $reponse]);
	}

	public function saveSkills()//FIXME!!!!
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
}
