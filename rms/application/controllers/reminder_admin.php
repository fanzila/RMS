<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Reminder_admin extends CI_Controller {

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

	public function save()
	{
		$id_bu =  $this->session->all_userdata()['bu_id'];
		
		$data = $this->input->post();
		$sqlt = "UPDATE ";
		$sqle = " WHERE `id` = $data[id]";
		$sqln = " WHERE id_task = $data[id]";
		$reponse = 'ok';
		
		if($data['id'] == 'create') {
			$sqlt = "INSERT INTO ";
			$sqle = "";
		}
		
		
		$this->db->trans_start();/*----------------------------------------------*/				
		$this->db->set('task', $data[task])
			->set('comment', $data[comment])
			->set('active', $data[active])
			->set('priority', $data[priority])
			->set('id_bu', $id_bu);
		if($data['id'] == 'create') {
			$sql_tasks = $this->db->insert('rmd_tasks');
		}else{
			$this->db->where('id', $data[id]);
			$sql_tasks = $this->db->update('rmd_tasks');
		}
		if (!$sql_tasks) {
			$response = "Can't place the insert sql request, error message: ".$this->db->_error_message();
		}		

		if($data['id'] == 'create') {
			$data['id'] = $this->db->insert_id();

			$this->db->set('start', $data['nstart'])
				->set('end', $data['nend'])
				->set('interval', $data['ninterval'])
				->set('last', $data['nlast'])
				->set('id_task', $data[id]);
			$sql_notif = $this->db->insert('rmd_notif');

			$this->db->set('start', $data['mstart'])
				->set('repeat_interval', $data['repeat_interval'])
				->set('repeat_year', $data['repeat_year'])
				->set('repeat_month', $data['repeat_month'])
				->set('repeat_day', $data['repeat_day'])
				->set('repeat_week', $data['repeat_week'])
				->set('repeat_weekday', $data['repeat_weekday'])
				->set('id_task', $data[id]);
			$sql_meta = $this->db->insert('rmd_meta');

		}else{
			$this->db->set('start', $data['nstart'])
				->set('end', $data['nend'])
				->set('interval', $data['ninterval'])
				->set('last', $data['nlast'])
				->where('id_task', $data[id]);
			$sql_notif = $this->db->update('rmd_notif');

			$this->db->set('start', $data['mstart'])
				->set('repeat_interval', $data['repeat_interval'])
				->set('repeat_year', $data['repeat_year'])
				->set('repeat_month', $data['repeat_month'])
				->set('repeat_day', $data['repeat_day'])
				->set('repeat_week', $data['repeat_week'])
				->set('repeat_weekday', $data['repeat_weekday'])
				->where('id_task', $data[id]);
			$sql_meta = $this->db->update('rmd_meta');
		}	

		if (!$sql_notif) {
			$response = "Can't place the insert sql request, error message: ".$this->db->_error_message();
		}
		
		if (!$sql_meta) {
			$response = "Can't place the insert sql request, error message: ".$this->db->_error_message();
		}
		$this->db->trans_complete();/*----------------------------------------------*/

		echo json_encode(['reponse' => $reponse]);
	}//*/

	/*public function save()
	{
		$id_bu =  $this->session->all_userdata()['bu_id'];
		
		$data = $this->input->post();
		$sqlt = "UPDATE ";
		$sqle = " WHERE `id` = $data[id]";
		$sqln = " WHERE id_task = $data[id]";
		$reponse = 'ok';
		
						
		if($data['id'] == 'create') {
			$sqlt = "INSERT INTO ";
			$sqle = "";
		}
		
		$sql_tasks = "$sqlt rmd_tasks SET `task` = '".addslashes($data['task'])."', comment = '".addslashes($data['comment'])."', active = $data[active], priority = $data[priority], id_bu = $id_bu $sqle ";
		
		$this->db->trans_start();
		if (!$this->db->query($sql_tasks)) {
			$response = "Can't place the insert sql request, error message: ".$this->db->_error_message();
		}
		
		if($data['id'] == 'create') { 	
			$data['id'] = $this->db->insert_id();
			$sqln = " , id_task = $data[id]";
		}
		 
		$sql_notif = "$sqlt rmd_notif SET `start` = '".$data['nstart']."', `end` = '".$data['nend']."', `interval` = '".$data['ninterval']."', `last` = '".$data['nlast']."' $sqln";
		
		$sql_meta = "$sqlt rmd_meta SET `start` = '".$data['mstart']."', repeat_interval = '".$data['repeat_interval']."', repeat_year = '".$data['repeat_year']."', repeat_month = '".$data['repeat_month']."', repeat_day = '".$data['repeat_day']."', repeat_week = '".$data['repeat_week']."', repeat_weekday = '".$data['repeat_weekday']."' $sqln";		

		if (!$this->db->query($sql_notif)) {
			$response = "Can't place the insert sql request, error message: ".$this->db->_error_message();
		}
		
		if (!$this->db->query($sql_meta)) {
			$response = "Can't place the insert sql request, error message: ".$this->db->_error_message();
		}
		$this->db->trans_complete();
		
		echo json_encode(['reponse' => $reponse]);
	}//*/
	
	public function index($create = null)
	{		
		$id_bu =  $this->session->all_userdata()['bu_id'];
		$this->load->library('rmd');

		$rmd = $this->rmd->getAllTasks($id_bu);
		$data = array(
			'create'	=> $create,
			'tasks'		=> $rmd
			);
		
		$data['bu_name'] =  $this->session->all_userdata()['bu_name'];
		$data['username'] = $this->session->all_userdata()['identity'];
		
		$this->load->view('reminder_admin',$data);
	}

}
