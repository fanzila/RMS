<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Reminder extends CI_Controller {

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
		$this->load->library('hmw');
		$this->hmw->keyLogin();
	}

	public function index($task_id = null, $view = null)
	{		
		$this->load->library('rmd');

		$id_bu =  $this->session->all_userdata()['bu_id'];

		$msg = null;
		$form = $this->input->post();

		if(!empty($form)) {

			foreach ($form as $key => $val) {	

				$ex = explode('_', $key);
				if($ex[0] == 'task') {
					$this->db->set('start', "NOW()", FALSE)->where('id_task', $ex[1])->where('repeat_year', '')->where('repeat_month', '')->where('repeat_day', '')->where('repeat_week', '')->where('repeat_weekday', '');
					$this->db->update('rmd_meta');
					$req_up = $this->db->get('rmd_meta');
					echo $req_up;

					if(!$req_up) {
						echo $this->db->error;
						return false;
					}

					$this->db->set('id_user', $form['user'])->set('date', "NOW()", FALSE)->set('id_task', $ex[1]);
					$req_up = $this->db->insert('rmd_log');
					if(!$req_up) {
						echo $this->db->error;
						return false;
					}

					$msg = "RECORDED ON: ".date('Y-m-d H:m');
				}
			}

		}

		$this->db->select('users.username, users.last_name, users.first_name, users.email, users.id');
		$this->db->distinct('users.username');
		$this->db->join('users_bus', 'users.id = users_bus.user_id', 'left');
		$this->db->where('users.active', 1);
		$this->db->where('users_bus.bu_id', $id_bu);
		$query = $this->db->get("users");
		$users = $query->result();

		$rmd = $this->rmd->getTasks($task_id, $view, $id_bu);

		$data = array(
			'tasks'		=> $rmd,
			'users'		=> $users,
			'msg'		=> $msg,
			'keylogin'	=> $this->session->userdata('keylogin'),
			'view'		=> $view
			);

		$data['bu_name'] =  $this->session->all_userdata()['bu_name'];
		$data['username'] = $this->session->all_userdata()['identity'];
		
		$this->load->view('reminder/index',$data);
	}
	
	public function log()
	{
		
		$id_bu =  $this->session->all_userdata()['bu_id'];
		
		$this->db->select('l.date, t.task, u.username')->from('rmd_log as l')->join('users as u', 'u.id = l.id_user')->join('rmd_tasks as t', 't.id = l.id_task')->where('t.id_bu', $id_bu)->order_by('l.date desc')->limit(100);
		$res 	= $this->db->get() or die($this->mysqli->error);
		$tasks 	= $res->result();
		$data = array(
			'tasks'		=> $tasks
			);
			
		$data['bu_name'] =  $this->session->all_userdata()['bu_name'];
		$data['username'] = $this->session->all_userdata()['identity'];
			
		$this->load->view('reminder/logs',$data);
	}
}
