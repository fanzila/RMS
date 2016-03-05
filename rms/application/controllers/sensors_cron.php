<?php
class Sensors_cron extends CI_Controller {

	//cd /var/www/hank/HMW/hmw && php index.php sensors_cron index #every 5 mn
	//cd /var/www/hank/HMW/assets/1wire.sh #every 5 mn

	public function __construct()
	{
		parent::__construct();
		$this->load->database();
		$this->load->library('hmw');
		$this->load->library('mmail');
	}

	public function index()
	{

		if($this->input->is_cli_request()) {
			$q = "SELECT * FROM `sensors_alarm`";
			$r = $this->db->query($q) or die('ERROR '.$this->db->_error_message().error_log('ERROR '.$this->db->_error_message()));
			$info = $r->result_array();

			foreach ($info as $key => $val) {

				$email	= array();
				$msg	= '';
				$max    = $val['max'];
				$min    = $val['min'];

				$qs = "SELECT st.`temp`, st.`date`, s.`name`, s.`correction` AS correction FROM sensors_temp AS st
					JOIN sensors AS s ON st.id_sensor = s.id
					JOIN sensors_alarm AS sa ON sa.id_sensor = s.id
					WHERE st.id_sensor = ".$val['id_sensor']."
					AND CAST(st.`date` AS DATE) = CAST(NOW() AS DATE)
					AND sa.lastalarm <= DATE_ADD(NOW(), INTERVAL -600 SECOND)
					AND CAST(NOW() AS TIME) BETWEEN CAST('08:00:00' AS TIME) AND CAST('23:30:00' AS TIME)
					ORDER BY `date` DESC LIMIT 1";


				$rs = $this->db->query($qs) or die('ERROR '.$this->db->_error_message().error_log('ERROR '.$this->db->_error_message()));
				$is = $rs->result();

				if(!empty($is)) {
					
					$temp		= $is[0]->temp; // + $correction;
					$correction	= $is[0]->correction;				

					// != 85 is a cludge for wrong data collecting by 1-wire which report sometimes, for unknown reason, 85 instead of minus something...
					if(($temp >= $max OR $temp <= $min) AND $temp != 85) {

						$msg = "ERROR sensor ".$is[0]->name.": the temperature should be max: ".$max."° and min: ".$min."° \nbut it's ".$temp."° at ".$is[0]->date."\n";

						$this->hmw->sendNotif($msg);

						$email['to']		= 'checklist@hankrestaurant.com';
						$email['subject'] 	= "Sensor '".$is[0]->name."' error!";
						$email['msg'] 		= $msg;

						$this->mmail->sendEmail($email);
						$qu = "UPDATE sensors_alarm SET lastalarm = NOW() WHERE id_sensor = ".$val['id_sensor'];
						$ru = $this->db->query($qu) or die('ERROR '.$this->db->_error_message().error_log('ERROR '.$this->db->_error_message()));	

					}
				}
			}
			return;

		} else {
			echo "Access refused.";
			return;
		}
	}
}
?>