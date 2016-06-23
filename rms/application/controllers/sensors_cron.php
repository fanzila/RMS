<?php

class Sensors_cron extends CI_Controller {
	//cd /var/www/hank/rms/rms && php index.php sensors_cron index 1 #every 5 mn
	//cd /var/www/hank/rms/assets/1wire.sh #every 5 mn
	public function __construct()
	{
		parent::__construct();
		$this->load->database();
		$this->load->library('hmw');
		$this->load->library('mmail');
	}
	public function index($id_bu)
	{
		if($this->input->is_cli_request()) {
			$this->db->from('sensors_alarm as sa')
				->join('sensors as s', 's.id = sa.id_sensor')
				->where('s.id_bu', $id_bu);
			$r = $this->db->get() or die('ERROR '.$this->db->_error_message().error_log('ERROR '.$this->db->_error_message()));
			$info = $r->result_array();
			foreach ($info as $key => $val) {
				$email	= array();
				$msg	= '';
				$max    = $val['max'];
				$min    = $val['min'];

				$this->db->select('st.temp, st.date, s.name, s.correction as correction')
					->from('sensors_temp as st')
					->join('sensors as s', 'st.id_sensor = s.id')
					->join('sensors_alarm as sa', 'sa.id_sensor = s.id')
					->where('st.id_sensor', $val['id_sensor'])
					->where('s.id_bu', $id_bu)
					->where("CAST(st.`date` AS DATE) = CAST(NOW() AS DATE)")
					->where("sa.lastalarm <= DATE_ADD(NOW(), INTERVAL -600 SECOND)")
					->where("CAST(NOW() AS TIME) BETWEEN CAST('08:00:00' AS TIME) AND CAST('23:30:00' AS TIME)")
					->order_by('date desc')->limit(1);
				$rs = $this->db->get() or die('ERROR '.$this->db->_error_message().error_log('ERROR '.$this->db->_error_message()));//*/

				/*$qs = "SELECT st.`temp`, st.`date`, s.`name`, s.`correction` AS correction FROM sensors_temp AS st
					JOIN sensors AS s ON st.id_sensor = s.id
					JOIN sensors_alarm AS sa ON sa.id_sensor = s.id
					WHERE st.id_sensor = $val[id_sensor]
					AND s.id_bu = $id_bu
					AND CAST(st.`date` AS DATE) = CAST(NOW() AS DATE)
					AND sa.lastalarm <= DATE_ADD(NOW(), INTERVAL -600 SECOND)
					AND CAST(NOW() AS TIME) BETWEEN CAST('08:00:00' AS TIME) AND CAST('23:30:00' AS TIME)
					ORDER BY `date` DESC LIMIT 1";
				$rs = $this->db->query($qs) or die('ERROR '.$this->db->_error_message().error_log('ERROR '.$this->db->_error_message()));//*/
				$is = $rs->result();
				if(!empty($is)) {
					
					$temp		= $is[0]->temp; // + $correction;
					$correction	= $is[0]->correction;				
					// "AND $temp != 85" is a cludge for wrong data collecting by 1-wire which report sometimes, for unknown reason, 85 instead of minus something...
					if(($temp >= $max OR $temp <= $min) AND $temp != 85) {
						
						$buinfo = $this->hmw->getBuInfo($id_bu);
						$msg = "$buinfo->name ERROR sensor ".$is[0]->name.": ".$temp."° at ".$is[0]->date."\n 
The temperature should be max: ".$max."° and min: ".$min."°";
						$this->hmw->sendNotif($msg, $id_bu);
						//get checklist BU, then manager2 + admin email of this BU
						$this->db->select('users.username, users.email, users.id');
						$this->db->distinct('users.username');
						$this->db->join('users_bus', 'users.id = users_bus.user_id', 'left');
						$this->db->join('users_groups', 'users.id = users_groups.user_id');
						$this->db->where('users.active', 1);
						$this->db->where_in('users_groups.group_id', array(1,4));
						$this->db->where('users_bus.bu_id', $id_bu);
						$query = $this->db->get("users");
						
						$email['subject'] 	= $buinfo->name." Sensor '".$is[0]->name."' error!";
						$email['msg'] 		= $msg;
						foreach ($query->result() as $row) {
							$email['to']	= $row->email;	
							$this->mmail->sendEmail($email);
						}
						$this->db->set('lastalarm', "NOW()", FALSE)->where('id_sensor', $val['id_sensor']);
						$ru = $this->db->update('sensors_alarm') or die('ERROR '.$this->db->_error_message().error_log('ERROR '.$this->db->_error_message()));
						//$qu = "UPDATE sensors_alarm SET lastalarm = NOW() WHERE id_sensor = ".$val['id_sensor'];
						//$ru = $this->db->query($qu) or die('ERROR '.$this->db->_error_message().error_log('ERROR '.$this->db->_error_message()));
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















/*class Sensors_cron extends CI_Controller {

	//cd /var/www/hank/rms/rms && php index.php sensors_cron index 1 #every 5 mn
	//cd /var/www/hank/rms/assets/1wire.sh #every 5 mn

	public function __construct()
	{
		parent::__construct();
		$this->load->database();
		$this->load->library('hmw');
		$this->load->library('mmail');
	}

	public function index($id_bu)
	{

		if($this->input->is_cli_request()) {

			$this->db->from('sensors_alarm as sa')
			->join('sensors as s', 's.id = sa.id_sensor')
			->where('s.id_bu', $id_bu);
			$r = $this->db->get() or die('ERROR '.$this->db->_error_message().error_log('ERROR '.$this->db->_error_message()));
			$info = $r->result_array();

			foreach ($info as $key => $val) {

				$email	= array();
				$msg	= '';
				$max    = $val['max'];
				$min    = $val['min'];

				$this->db->select('st.temp, st.date, s.name, s.correction as correction')->from('sensors_temp as st')
				->join('sensors as s', 'st.id_sensor = s.id')
				->join('sensors_alarm as sa', 'sa.id_sensor = s.id')
				->where('st.id_sensor', $val['id_sensor'])
				->where('s.id_bu', $id_bu)
				->where("CAST(st.`date` AS DATE) = CAST(NOW() AS DATE)")
				->where("sa.lastalarm <= DATE_ADD(NOW(), INTERVAL -600 SECOND)")
				->where("CAST(NOW() AS TIME) BETWEEN CAST('08:00:00' AS TIME) AND CAST('23:30:00' AS TIME)")
				->order_by('date desc')->limit(1);
				$rs = $this->db->get() or die('ERROR '.$this->db->_error_message().error_log('ERROR '.$this->db->_error_message()));
				$is = $rs->result();

				if(!empty($is)) {
					
					$temp		= $is[0]->temp; // + $correction;
					$correction	= $is[0]->correction;				

					// "AND $temp != 85" is a cludge for wrong data collecting by 1-wire which report sometimes, for unknown reason, 85 instead of minus something...
					if(($temp >= $max OR $temp <= $min) AND $temp != 85) {
						
						$buinfo = $this->hmw->getBuInfo($id_bu);
						$msg = "$buinfo->name ERROR sensor ".$is[0]->name.": ".$temp."° at ".$is[0]->date."\n 
The temperature should be max: ".$max."° and min: ".$min."°";

						$this->hmw->sendNotif($msg, $id_bu);

						//get checklist BU, then manager2 + admin email of this BU
						$this->db->select('users.username, users.email, users.id');
						$this->db->distinct('users.username');
						$this->db->join('users_bus', 'users.id = users_bus.user_id', 'left');
						$this->db->join('users_groups', 'users.id = users_groups.user_id');
						$this->db->where('users.active', 1);
						$this->db->where_in('users_groups.group_id', array(1,4));
						$this->db->where('users_bus.bu_id', $id_bu);
						$query = $this->db->get("users");
						
						$email['subject'] 	= $buinfo->name." Sensor '".$is[0]->name."' error!";
						$email['msg'] 		= $msg;

						foreach ($query->result() as $row) {
							$email['to']	= $row->email;	
							$this->mmail->sendEmail($email);
						}
						$this->db->set('lastalarm', "NOW()", FALSE)->where('id_sensor', $val['id_sensor']);
						$ru = $this->db->update('sensors_alarm') or die('ERROR '.$this->db->_error_message().error_log('ERROR '.$this->db->_error_message()));
						
					}
				}
			}
			return;

		} else {
			echo "Access refused.";
			return;
		}
	}
}*/
?>
