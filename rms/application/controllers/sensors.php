<?php
class Sensors extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->library('ion_auth');
		$this->load->library("hmw");
		$this->load->library('mmail');

	}

	public function index()
	{
		$this->hmw->changeBu();// GENERIC changement de Bu
		$this->hmw->keyLogin();

		$id_bu =  $this->session->all_userdata()['bu_id'];

		$this->db->select('s.id AS sid, st.id AS stid, st.id_sensor AS idsensor, MAX(st.date) as date, st.temp, s.name, s.correction, sa.lastalarm, MAX(sap.date_fin) as date_fin');
			$this->db->from('sensors_temp as st')
				->join('sensors as s', 'st.id_sensor = s.id  ','left')
				->join('sensors_alarm as sa', 'sa.id_sensor = s.id','left')
				->join('sensors_alarm_pause as sap', 'sap.id_sensor = s.id', 'left')
				->where('s.id_bu', $id_bu)
				->order_by('st.id DESC')
				->group_by('idsensor');

		$r = $this->db->get() or die('ERROR '.$this->db->_error_message().error_log('ERROR '.$this->db->_error_message()));
		$info = $r->result_array();

		if ($this->input->post('submit_pause') !== false AND $this->input->post('delayVal') !== false
				AND $this->input->post('s_id') !== false) {
			$delay = $this->input->post('delayVal');
			$s_id = $this->input->post('s_id');
			$this->setDelay($s_id, $delay);
		}

		$data['bu_name'] =  $this->session->all_userdata()['bu_name'];
		$data['username'] = $this->session->all_userdata()['identity'];

		$data['current'] 	= $info;
		foreach ($data['current'] as $key => &$val) {
			if ($this->checkForOngoingDelay($val['sid']) === true) {
				$val['ongoingDelay'] = $this->getOngoingDelay($val['sid']);
			}
			else {
				$val['ongoingDelay'] = NULL;
				$val['date_fin'] = NULL;
			}
		}
		$data['title'] 		= 'Sensors';
		$data['keylogin']	= $this->session->userdata('keylogin');

		$headers = $this->hmw->headerVars(1, "/", "Sensors");
		$this->load->view('jq_header_pre', $headers['header_pre']);
		$this->load->view('jq_header_post', $headers['header_post']);
		$this->load->view('sensors', $data);
		$this->load->view('jq_footer');
	}

	public function record()
	{
		$data = json_decode($_POST['data']);
		foreach ($data as $key => $val) {
			$ex 	= explode("|",$val);
			$date	= $ex[1];
			$sensor	= $ex[0];
			$temp 	= $ex[2];

			$req = array (
				'date'		=> $date,
				'temp'		=> $temp,
				'id_sensor'	=> $sensor
				);

			if (!$this->db->insert('sensors_temp', $req)) {
				error_log("Can't place the insert sql request, error message: ".$this->db->_error_message());
				exit();
			}

			$this->db->where("date < DATE_ADD(NOW(), INTERVAL -10 DAY)");
			$r = $this->db->delete('sensors_temp') or die('ERROR '.$this->db->_error_message().error_log('ERROR '.$this->db->_error_message()));
		}
	}

	public function getOngoingDelay($id_sensor)
	{
		$currentDate = new DateTime('now');
		$this->db->select_max('date_fin');
		$this->db->from('sensors_alarm_pause');
		$whereArray = array ('id_sensor' => $id_sensor, 'date_fin >' => $currentDate->format('Y-m-d H:i:s'));
		$this->db->where($whereArray);
		$query = $this->db->get() or die('ERROR ' .$this->db->error_message().error_log('ERROR '.$this->db->_error_message()));
		$res = $query->result_array()[0]['date_fin'];
		if (isset($res)) {
			$endDate = DateTime::CreateFromFormat('Y-m-d H:i:s', $res);
			$delay = abs($endDate->getTimestamp()-$currentDate->getTimestamp());
			return ($delay);
		} else {
			return 0;
		}
	}

	public function checkForOngoingDelay($id_sensor)
	{
		$currentDate = date('Y-m-d H:i:s');
		$this->db->select_max('date_fin');
		$this->db->from('sensors_alarm_pause');
		$whereArray = array ('id_sensor' => $id_sensor, 'date_fin >' => $currentDate);
		$this->db->where($whereArray);
		$query = $this->db->get() or die('ERROR ' .$this->db->error_message().error_log('ERROR '.$this->db->_error_message()));
		$res = $query->result_array()[0];
		if (isset($res) AND isset($res['date_fin'])) {
			return true;
		} else {
			return false;
		}
	}

	public function setDelay($id_sensor, $delay)
	{
		$currentDate = new DateTime('now');
		$dateToSet = new DateTime('now');
		$cfd = $this->checkForOngoingDelay($id_sensor);
		if ($delay != 0) {
			$dateToSet->add(new DateInterval('PT' . $delay . 'S'));
			if ($cfd === true) {
				$this->data['message'] = 'Pause déjà active, veuillez la réinitialiser';
			} else {
					$dataToInsert = array (
						'id_sensor' => $id_sensor,
						'id_user_pause' => $this->ion_auth->user()->row()->id,
						'date_fin' => $dateToSet->format('Y-m-d H:i:s'),
						'date_last_action' => $currentDate->format('Y-m-d H:i:s')
					);
					if (!$this->db->insert('sensors_alarm_pause', $dataToInsert)) {
						error_log("Can't place the insert sql request, error message: ".$this->db->_error_message());
						exit();
					}
			}
		} else {
				$this->db->select('id');
				$this->db->from('sensors_alarm_pause');
				$whereCond = "id_sensor = " . $id_sensor . " AND date_fin > NOW()";
				$this->db->where($whereCond);
				$this->db->limit(1);
				$query = $this->db->get() or die('ERROR '.$this->db->error_message().error_log('ERROR '.$this->db->_error_message()));
				$id = $query->row()->id;
				$dataToInsert = array (
						'id_user_pause' => $this->ion_auth->user()->row()->id,
						'date_fin' => $currentDate->format('Y-m-d H:i:s'),
						'date_last_action' => $currentDate->format('Y-m-d H:i:s')
					);
					$this->db->where('id', $id);
					if (!$this->db->update('sensors_alarm_pause', $dataToInsert)) {
						error_log("Can't place the insert sql request, error message: ".$this->db->_error_message());
						exit();
					}
		}
	}

	public function cliCheckLast($id_bu)
	{

		if($this->input->is_cli_request()) {
			$this->db->from('sensors_temp as st')
				->join('sensors as s', 's.id = st.id_sensor')
				->where('s.id_bu', $id_bu);
			$r = $this->db->get() or die('ERROR '.$this->db->_error_message().error_log('ERROR '.$this->db->_error_message()));
			$info = $r->result_array();

			foreach ($info as $key => $val) {

				$email	= array();
				$msg	= '';
				$date	= $val['date'];

				$this->db->select('st.date, s.name')
					->from('sensors_temp as st')
					->join('sensors as s', 'st.id_sensor = s.id')
					->where('st.id_sensor', $val['id_sensor'])
					->where('s.id_bu', $id_bu)
					->where("CAST(st.`date` AS DATE) < CAST(DATE_SUB(CAST(NOW() AS DATE),INTERVAL 55 MINUTE) AS DATE)")
					->where("CAST(NOW() AS TIME) BETWEEN CAST('09:00:00' AS TIME) AND CAST('22:00:00' AS TIME)")
					->order_by('date desc')->limit(1);
				$rs = $this->db->get() or die('ERROR '.$this->db->_error_message().error_log('ERROR '.$this->db->_error_message()));

				$is = $rs->result();

				if(!empty($is)) {
					$buinfo = $this->hmw->getBuInfo($id_bu);
					$msg = "$buinfo->name ERROR sensor ".$is[0]->name.": Last check at ".$is[0]->date."\n ";

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
					$ru = $this->db->update('sensors_alarm') or die('ERROR '.$this->db->_error_message().error_log('ERROR '.$this->db->_error_message()));
				}
			}
			return;

		} else {
			echo "Access refused.";
			return;
		}
	}

	//cd /var/www/hank/rms/rms && php index.php sensors cliCheck 1 #every 5 mn
	//cd /var/www/hank/rms/assets/1wire.sh #every 5 mn
	public function cliCheck($id_bu)
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
				$s_id   = $val['id_sensor'];

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
				$rs = $this->db->get() or die('ERROR '.$this->db->_error_message().error_log('ERROR '.$this->db->_error_message()));

				$is = $rs->result();

				if(!empty($is)) {

					$temp		= $is[0]->temp; // + $correction;
					$correction	= $is[0]->correction;

					// "AND $temp != 85" is a cludge for wrong data collecting by 1-wire which report sometimes, for unknown reason, 85 instead of minus something...
					if(($temp >= $max OR $temp <= $min) AND ($temp != 85 AND $temp > -100 AND $temp < 100) AND ($this->checkForOngoingDelay($s_id) == false)) {
						$buinfo = $this->hmw->getBuInfo($id_bu);
						$msg = "$buinfo->name ERROR sensor ".$is[0]->name.": ".$temp."° at ".$is[0]->date."\n
							The temperature should be max: ".$max."° and min: ".$min."°";
						echo "notif sent";

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
					} else if ($this->checkForOngoingDelay($s_id) == true) {
						echo "alarm delayed";
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
