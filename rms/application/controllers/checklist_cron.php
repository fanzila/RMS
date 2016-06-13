<?php 
class Checklist_cron extends CI_Controller {

	//cd /var/www/hank/rms/rms && php index.php checklist_cron index 3 1

	public function __construct()
	{
		parent::__construct();
		$this->load->database();
		$this->load->library('mmail');
		$this->load->library('hmw');
		
	}

	public function index($id, $id_bu)
	{

		if($this->input->is_cli_request()) {

			$this->db->select('checklists.name AS cname, bus.name AS bname');
			$this->db->join('bus', 'checklists.id_bu = bus.id', 'left');
			$this->db->where('checklists.id', $id);
			$this->db->where('checklists.id_bu', $id_bu);
			$query = $this->db->get("checklists");
			$info = $query->result();
			
			$msg = "WARINING! ".$info[0]->bname." No ".$info[0]->cname." checklist have been created!";

			$sql 	= "SELECT DATE(`date`) FROM checklist_records AS cr 
			LEFT JOIN checklists AS c ON c.id = cr.id_checklist 
			WHERE DATE(`date`) = DATE(NOW()) 
			AND cr.id_checklist = $id 
			AND c.id_bu = $id_bu";
			$query	= $this->db->query($sql);
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
?>