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

	public function index($task_id = null)
	{
		$this->hmw->changeBu();// GENERIC changement de Bu

		$this->hmw->keyLogin();//->est-ce que cela doit être accessible via keylogin? non tel que conçu je suppose
		$id_bu =  $this->session->all_userdata()['bu_id'];

		/* SPECIFIC Recuperation depuis la base de donnees des informations discounts */
		date_default_timezone_set('Europe/Paris');
		$this->db->select('R.id, R.id_user, RI.checked, RI.comment, I.name as i_name, skills.name as s_name, cat.name as c_name, subcat.name as sub_name')
			->from('skills_record as R')
			->join('skills_record_item as RI', 'R.id = RI.id_skills_record')
			->join('skills_item as I', 'I.id = RI.id_skills_item')
			->join('skills', 'skills.id = I.id_skills')
			->join('skills_category as cat', 'I.id_cat = cat.id')
			->join('skills_sub_category as subcat', 'I.id_sub_cat = subcat.id')
			->where('R.id_user', $this->ion_auth->get_user_id())
			->order_by('I.name desc');
		$res 	= $this->db->get() or die($this->mysqli->error);
		$skills_items = $res->result();
/*foreach ($skills_items as $item) {
	echo $item->id."\n";
	echo $item->id_user."\n";
	echo $item->checked."\n";
	echo $item->comment."\n";
	echo $item->i_name."\n";
	echo $item->s_name."\n";
	echo $item->c_name."\n";
	echo $item->sub_name."\n";
}*/


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

		$headers = $this->hmw->headerVars(1, "/skills/", "My skills");
		$this->load->view('jq_header_pre', $headers['header_pre']);
		$this->load->view('jq_header_post', $headers['header_post']);
		$this->load->view('skills/index',$data);
		$this->load->view('jq_footer');
	}
	
	public function admin)(
	{
		$headers = $this->hmw->headerVars(1, "/skills/admin", "Skills Management");
		$this->load->view('jq_header_pre', $headers['header_pre']);
		$this->load->view('jq_header_post', $headers['header_post']);
		$this->load->view('skills/admin');
		$this->load->view('jq_footer');
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
