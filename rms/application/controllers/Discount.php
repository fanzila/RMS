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
		$this->load->library('ion_auth_acl');
		$this->load->library('hmw');
		$this->load->helper(array('form', 'url'));
		$this->load->library('form_validation');
	}
	
	public function index($task_id = null)
	{
		$this->hmw->changeBu();// GENERIC changement de Bu

		$this->hmw->keyLogin();
		$id_bu =  $this->session->userdata('bu_id');
		$q = $this->input->get('q');

		/* SPECIFIC Creation d'un message si fonction create utilisee */
		$msg = null;
		if($task_id=="create") {
				$msg = "RECORDED ON: ".date('Y-m-d H:i:s');
		}

		/* SPECIFIC Recuperation depuis la base de donnees des informations users */
		$this->db->select('users.username, users.last_name, users.first_name, users.email, users.id');
		$this->db->distinct('users.username');
		$this->db->join('users_bus', 'users.id = users_bus.user_id', 'left');
		$this->db->where('users.active', 1);
		$this->db->where('users_bus.bu_id', $id_bu);
		$this->db->order_by('users.username', 'asc');
		$query = $this->db->get("users");
		$users = $query->result();

		/* SPECIFIC Recuperation depuis la base de donnees des informations discounts */
		date_default_timezone_set('Europe/Paris');
		$this->db->select('T.id as tid, T.nature as tnature, T.client as tclient, T.reason as treason, T.id_user as tuser, T.date as tdate, T.deleted as tdel, T.used as tused, T.allbu as tallbu, T.persistent as tpersistent')->from('discount as T');
			$this->db->where('T.deleted', 0);
			$this->db->where('T.used', 0);
			$this->db->where('(T.id_bu', $id_bu);
			$this->db->or_where("T.allbu = 1)", NULL, FALSE);
			$this->db->order_by('T.date', 'desc');
			$this->db->limit(30);
			if(isset($q)) $this->db->like('T.client', "$q", 'both'); 
			
		if($task_id > 0) $this->db->where('id', $task_id);
		$this->db->order_by('T.date desc');
		$query	= $this->db->get();
		$discount = $query->result();

		$data = array(
			'discount'	=> $discount,
			'create'	=> 0,
			'users'		=> $users,
			'msg'		=> $msg
			);
		$data['bu_name']	= $this->session->userdata('bu_name');
		$data['username']	= $this->session->userdata('identity');
		$data['q']		 	= $q;

		$headers = $this->hmw->headerVars(1, "/discount/", "Discount");
		$this->load->view('jq_header_pre', $headers['header_pre']);
		$this->load->view('jq_header_post', $headers['header_post']);
		$this->load->view('discount/index',$data);
		$this->load->view('jq_footer');
	}
	
	public function log()
	{
		$this->hmw->keyLogin();
		$id_bu =  $this->session->userdata('bu_id');
		$this->db->select('l.date, l.client, l.reason, l.nature, u.username, l.id_discount, l.event_type, l.used')
			->from('discount_log as l')
			->join('users as u', 'u.id = l.id_user')
			->where('l.id_bu', $id_bu)
			->order_by('l.date desc')
			->limit(100);
		$res 	= $this->db->get() or die($this->mysqli->error);
		$discounts 	= $res->result();
		$data = array(
			'discounts'	=> $discounts,
			'bu_name' 	=> $this->session->userdata('bu_name'),
			'username' 	=> $this->session->userdata('identity')
			);

		
	 	$headers = $this->hmw->headerVars(0, "/discount/", "Discount Log");
		$this->load->view('jq_header_pre', $headers['header_pre']);
		$this->load->view('jq_header_post', $headers['header_post']);
		$this->load->view('discount/logs',$data);
		$this->load->view('jq_footer');
	}

	public function save()
	{
		$id_bu			= $this->session->userdata('bu_id');		
		$data 			= $this->input->post();
		$reponse 		= 'ok';
		
		$this->db->set('nature', $data['nature']);
		$this->db->set('client', $data['client']);
		$this->db->set('reason', $data['reason']);
		$this->db->set('persistent', $data['persistent']);
		$this->db->set('allbu', $data['allbu']);
		$this->db->set('id_user', $data['user']);
		$this->db->set('date', date('Y-m-d H:i:s'));
		$this->db->set('id_bu', $id_bu); 
		
		$this->db->trans_start();
			if($data['id'] == 'create') {
				if(!$this->db->insert('discount')) {
					$reponse = "Can't place the insert sql request, error message: ".$this->db->_error_message();
				}
				$data['id'] = $this->db->insert_id();
			} else {
				
				$this->db->set('used', $data['used']);
				if($data['persistent'] == 1 AND $this->ion_auth_acl->has_permission('validate_persistent_discount')) $this->db->set('used', false);
				
				$this->db->where('id', $data['id']);
				if (!$this->db->update('discount')) {
					$reponse = "Can't place the insert sql request, error message: ".$this->db->_error_message();
				}
					$this->db->set('used', $data['used']);
				$this->db->where('id_bu', $id_bu);
				$this->db->where('id_discount', $data['id']);
				if(!$this->db->update('discount_log')) {
					$reponse = "Can't place the insert sql request, error message: ".$this->db->_error_message();
				}
				$this->db->set('event_type', "update");
				$this->db->set('used', $data['used']);
			}

			$this->db->set('id_discount', $data['id']);
			$this->db->set('id_user', $data['user']);
			$this->db->set('nature', $data['nature']);
			$this->db->set('client', $data['client']);
			$this->db->set('reason', $data['reason']);
			$this->db->set('id_bu', $id_bu);
			$this->db->set('date', date('Y-m-d H:i:s'));
			
			if(!$this->db->insert('discount_log')) {
				$reponse = "Can't place the insert sql request, error message: ".$this->db->_error_message();
			}
		$this->db->trans_complete();

		echo json_encode(['reponse' => $reponse]);
	}
	
	public function creation($create = null)
	{		
		$id_bu =  $this->session->userdata('bu_id');

		$this->db->select('users.username, users.last_name, users.first_name, users.email, users.id');
		$this->db->distinct('users.username');
		$this->db->join('users_bus', 'users.id = users_bus.user_id', 'left');
		$this->db->where('users.active', 1);
		$this->db->where('users_bus.bu_id', $id_bu);
		$this->db->order_by('users.username', 'asc');
		$query = $this->db->get("users");
		$users = $query->result();

		$this->db->select('T.id as tid, T.nature as tnature, T.id_user as tuser, T.date as tdate, T.deleted as tdel, T.used as tused, T.persistent as tpersistent, T.allbu as tallbu')
			->from('discount as T')
			->where('T.id_bu', $id_bu)
			->where('T.deleted', 0)
			->order_by('T.date desc');
		$query	= $this->db->get();
		$discount = $query->result();
		
		$data = array(
			'create'	=> $create,
			'users'		=> $users,
			'discount'	=> $discount
			);
		
		$data['bu_name'] =  $this->session->userdata('bu_name');
		$data['username'] = $this->session->userdata('identity');
		
		$headers = $this->hmw->headerVars(0, "/discount/", "Discount create");
		$this->load->view('jq_header_pre', $headers['header_pre']);
		$this->load->view('jq_header_post', $headers['header_post']);
		$this->load->view('discount/discount_creation',$data);
		$this->load->view('jq_footer');
	}
}
