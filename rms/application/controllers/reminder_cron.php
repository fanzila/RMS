<?php 
class Reminder_cron extends CI_Controller {

	//cd /var/www/hank/rms/rms && php index.php reminder_cron index 1

	public function __construct()
	{
		parent::__construct();		
	}

	public function index($id_bu)
	{

		if($this->input->is_cli_request()) {

			$CI = & get_instance(); 
			$CI->load->database();
			$CI->load->library('hmw');
			$CI->load->library('rmd');

			$tasks = $CI->rmd->getTasks(null, null, $id_bu);
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
					$req_up 	= "UPDATE rmd_notif SET `last` = NOW() WHERE id_task = $row->id";

					if(!$this->db->query($req_up)) {
						echo $this->db->error;
						return false;
					}
					
				$this->hmw->sendNotif("Reminder: "$row->task, $id_bu);	
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
?>