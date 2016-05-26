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
		
		$q = "SELECT * FROM 
		( SELECT st.id AS stid, st.id_sensor AS idsensor, st.date, st.temp, s.name, s.correction, sa.lastalarm  
			FROM sensors_temp AS st 
			LEFT JOIN sensors AS s ON st.id_sensor = s.id  
			LEFT JOIN sensors_alarm AS sa ON sa.id_sensor = s.id 
			ORDER BY st.id DESC ) AS tmp_table GROUP BY idsensor";
	
		$r = $this->db->query($q) or die('ERROR '.$this->db->_error_message().error_log('ERROR '.$this->db->_error_message()));
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
			
			$q = "DELETE FROM sensors_temp WHERE date < DATE_ADD(NOW(), INTERVAL -10 DAY)";
			$r = $this->db->query($q) or die('ERROR '.$this->db->_error_message().error_log('ERROR '.$this->db->_error_message()));
			
			//error_log($val);	
		}
		
	}

}
?>