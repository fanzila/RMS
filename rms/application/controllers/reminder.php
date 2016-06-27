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
		$this->load->database();
		$this->load->library('ion_auth');
		$this->load->library('hmw');
		$this->load->library('rmd');
		
	}

	public function index($task_id = null, $view = null)
	{		
	
		$this->hmw->keyLogin();
		$id_bu =  $this->session->all_userdata()['bu_id'];

		$msg = null;
		$form = $this->input->post();

		if(!empty($form)) {

			foreach ($form as $key => $val) {	

				$ex = explode('_', $key);
				if($ex[0] == 'task') {
					$req_up 	= "UPDATE rmd_meta SET `start` = NOW() WHERE id_task = ". $ex[1]." AND ( repeat_year ='' AND repeat_month  ='' AND repeat_day  ='' AND repeat_week  ='' AND repeat_weekday  ='')";
					echo $req_up;
					$req_ins	= "INSERT INTO rmd_log SET `id_user` = ".$form['user'].", `date` = NOW(), `id_task` = ".$ex[1];

					if(!$this->db->query($req_up)) {
						echo $this->db->error;
						return false;
					}

					if(!$this->db->query($req_ins)) {
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
		$this->db->order_by('users.username', 'asc');
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
		$this->hmw->keyLogin();
		$id_bu =  $this->session->all_userdata()['bu_id'];
		
		$req 	= "SELECT l.`date`,t.`task`,u.`username`  FROM rmd_log l JOIN `users` u ON u.id = l.`id_user` JOIN rmd_tasks t ON t.id = l.`id_task` WHERE t.id_bu = $id_bu ORDER BY l.`date` DESC LIMIT 100";
		
		$res 	= $this->db->query($req) or die($this->mysqli->error);
		$tasks 	= $res->result();
		$data = array(
			'tasks'		=> $tasks
			);
			
		$data['bu_name'] =  $this->session->all_userdata()['bu_name'];
		$data['username'] = $this->session->all_userdata()['identity'];
			
		$this->load->view('reminder/logs',$data);
	}
	
	//cd /var/www/hank/rms/rms && php index.php reminder cliNotify 1
	public function cliNotify($id_bu)
	{
		if($this->input->is_cli_request()) {

			$tasks = $this->rmd->getTasks(null, null, $id_bu);
			foreach ($tasks as $row) {

				$now			= time();
				$notif			= $this->getNotif($row->id);
				$notif_start	= 0;
				$notif_end		= 999999999999;
				$interval		= 0;

				if(isset($notif->id)) {
					
					
					$notif_start	= strtotime(date('Y-m-d '.$notif->start));
					$notif_end		= strtotime(date('Y-m-d '.$notif->end));
					$notif_interval = $notif->interval;
					$notif_last		= strtotime($notif->last);
					$interval 		= $notif_last+$notif_interval;
				}
				
				if($notif_start <= $now && $notif_end > $now && $interval < $now) {
					$this->db->set('last', "NOW()", FALSE)->where('id_task', $row->id);
					if(!$this->db->update('rmd_notif')) {
						echo $this->db->error;
						return false;
					}
				$this->hmw->sendNotif("Reminder: ".$row->task, $id_bu);	
				
				}
			}

			return;
		} else { 
			echo "Access refused.";
			return; 
		}


	}
	private function getNotif($id) {
		$req = "SELECT * FROM rmd_notif WHERE id_task = $id LIMIT 1";
		$res = $this->db->query($req);
		$r = $res->result();
		if(!empty($r[0])) return $r[0];
		return false;
	}
}
