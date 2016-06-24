<?php
class Sensors extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->library('ion_auth');
		$this->load->library("hmw");
		
	}

	public function index()
	{
		$this->hmw->keyLogin();
		
		$id_bu =  $this->session->all_userdata()['bu_id'];

		$this->db->select('st.id AS stid, st.id_sensor AS idsensor, st.date, st.temp, s.name, s.correction, sa.lastalarm');
		$this->db->from('sensors_temp as st')
			->join('sensors as s', 'st.id_sensor = s.id  ','left')
			->join('sensors_alarm as sa', 'sa.id_sensor = s.id','left')
			->where('s.id_bu', $id_bu)
			->order_by('st.id DESC')
			->group_by('idsensor');		
		$r = $this->db->get() or die('ERROR '.$this->db->_error_message().error_log('ERROR '.$this->db->_error_message()));
		$info = $r->result_array();
		
		$data['bu_name'] =  $this->session->all_userdata()['bu_name'];
		$data['username'] = $this->session->all_userdata()['identity'];
		
		$data['current'] 	= $info;
		$data['title'] 		= 'Sensors';
		$data['keylogin']	= $this->session->userdata('keylogin');
		$this->load->view('sensors', $data);
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
}
?>