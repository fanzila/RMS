<?php
class Operations extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->database();
		$this->load->library("tools");
		$this->load->library('ion_auth');
		$this->load->library('ion_auth_acl');
	}

	public function index($login=null)
	{
		$this->tools->keyLogin();
		$this->tools->changeBu();// GENERIC changement de Bu
		
		if (!$this->ion_auth_acl->has_permission('access_cruds')) {
			die ('You are not allowed to do this');
		}

		
		//copy reminders
		$fromBu = 1;
		$toBu 	= 5;
		$doIt	= false;
	
		$this->db->from('rmd_tasks')->where('id_bu', $fromBu)->where('active', 1);
		$res = $this->db->get() or die($this->mysqli->error);
		$tasks = $res->result();
		//print_r($tasks);

		foreach ($tasks as $key => $val) { 
			print_r($val->task);

			$this->db->set('task', $val->task);
			$this->db->set('comment', $val->comment);
			$this->db->set('active', 1);
			$this->db->set('priority', $val->priority);
			$this->db->set('id_bu', $toBu);
			$this->db->set('type', $val->type);
			$this->db->set('notify_tablet', $val->notify_tablet);
			//$req = $this->db->insert('rmd_tasks');
			$task_id = $this->db->insert_id();
			

		}

		

		$id_bu =  $this->session->userdata('id_bu');
				
		$user					= $this->ion_auth->user()->row();
		$user_groups 			= $this->ion_auth->get_users_groups()->result();
				
		$this->db->select('bu_info, training_link')->from('bus')->where('id', $id_bu);
		$res = $this->db->get() or die($this->mysqli->error);
		$bu_infos = $res->result();
		
		$data = array(
			'username'	=> $user->username,
			'user_groups'	=> $user_groups[0],
			'title'		=> 'Book',
			'keylogin'	=> $this->session->userdata('keylogin'),
			'bu_infos'		=> $bu_infos[0]->bu_info,
			'bu_link'		=> $bu_infos[0]->training_link,
			'bu_name'	=> $this->session->userdata('bu_name')
			);


	}
}
?>
