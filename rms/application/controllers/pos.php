<?php
class Pos extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		@$this->load->library('ion_auth');
		$this->load->library("hmw");
		$this->load->library("cashier");
	}

	public function index()
	{		
		
		$data = array();

		$this->hmw->keyLogin();

		$user					= $this->ion_auth->user()->row();
		$user_groups 			= $this->ion_auth->get_users_groups()->result();
		$data['username']		= $user->username;
		$data['user_groups']	= $user_groups[0];
		$data["keylogin"] 		= $this->session->userdata('keylogin');
		$data['title'] 			= 'Pos';
		$data['safe_cash'] 		= $this->cashier->calc('safe_current_cash_amount');

		$this->load->view('pos/header', $data);
		$this->load->view('pos/index', $data);
		$this->load->view('pos/footer');
	}

	public function safe()
	{

		$group_info = $this->ion_auth_model->get_users_groups()->result();
		if ($group_info[0]->level < 2)
		{
			$this->session->set_flashdata('message', 'You must be a gangsta to view this page');
			redirect('/pos/');
		}

		$data = array();

		$user					= $this->ion_auth->user()->row();
		$user_groups 			= $this->ion_auth->get_users_groups()->result();
		$data['username']		= $user->username;
		$data['user_groups']	= $user_groups[0];

		$users_req = "SELECT `id`, `first_name`, `last_name`  FROM users WHERE active=1 ORDER BY `first_name` ASC";
		$users_res = $this->db->query($users_req) or die($this->mysqli->error);
		$data['users'] = $users_res->result_array();

		$data["keylogin"] = $this->session->userdata('keylogin');

		$data['title']  	= 'Safe';

		$this->load->view('pos/header', $data);
		$this->load->view('pos/safe', $data);
		$this->load->view('pos/footer');
	}

	public function report()
	{

		$group_info = $this->ion_auth_model->get_users_groups()->result();
		if ($group_info[0]->level < 2)
		{
			$this->session->set_flashdata('message', 'You must be a gangsta to view this page');
			redirect('/pos/');
		}

		$data = array();

		$user					= $this->ion_auth->user()->row();
		$user_groups 			= $this->ion_auth->get_users_groups()->result();
		$data['username']		= $user->username;
		$data['user_groups']	= $user_groups[0];
		$data["keylogin"] 		= $this->session->userdata('keylogin');
		$data['title'] 			= 'Cashier reports';
		$data['safe_cash'] 		= $this->cashier->calc('safe_current_cash_amount');
		$data['safe_tr'] 		= $this->cashier->calc('safe_current_tr_num');
		$data['pos_cash'] 		= $this->cashier->posInfo('cashfloat');		
		$data['live_movements'] = $this->cashier->posInfo('getLiveMovements');
		$lines					= array();

		$q_pm = "SELECT pm.`date`, pm.id, u.username, pm.comment, pm.movement, pm.pos_cash_amount, pm.safe_cash_amount, pm.safe_tr_num, pm.closing_file 
			FROM pos_movements AS pm
			LEFT JOIN users AS u ON u.id = pm.id_user 
			ORDER BY pm.`id` DESC LIMIT 500";

		$r_pm = $this->db->query($q_pm) or die('ERROR '.$this->db->_error_message().error_log('ERROR '.$this->db->_error_message()));

		foreach ($r_pm->result_array() as $key_pm => $m) {
			$q_pp = "SELECT * FROM pos_payments AS pp 
				JOIN pos_payments_type AS ppt ON pp.id_payment = ppt.id 
				WHERE id_movement = ".$m['id']." 
				ORDER BY  id_payment ASC";
			$r_pp = $this->db->query($q_pp) or die('ERROR '.$this->db->_error_message().error_log('ERROR '.$this->db->_error_message()));
			$res_pp = $r_pp->result_array();
			$lines[$m['id']]['mov'] = $m;
			$lines[$m['id']]['pay'] = $res_pp;
				
			if($m['movement'] == 'close') {
				$param = array('closing_file' =>  $m['closing_file']);
				$lines[$m['id']]['close_users'] 	= $this->cashier->posInfo('getUsers', $param);
				$lines[$m['id']]['cashmovements'] 	= $this->cashier->posInfo('getMovements', $param);
			}
		}

		$data['lines'] = $lines;
		
		$this->load->view('pos/header', $data);
		$this->load->view('pos/report', $data);
		$this->load->view('pos/footer');
	}

	public function movement($mov)
	{

		//'middle','open','close','safe_in','safe_out','pos_in','pos_out'
		$data = array();
		$this->hmw->keyLogin();

		$user					= $this->ion_auth->user()->row();
		$user_groups 			= $this->ion_auth->get_users_groups()->result();
		$data['username']		= $user->username;
		$data['user_groups']	= $user_groups[0];
		$data['mov']			= $mov;
		$data['archive_file'] 	= null;

		$data["keylogin"] = $this->session->userdata('keylogin');

		$data['title'] 			= "Cashier - ".strtoupper($mov);

		$q = "SELECT * FROM pos_payments_type WHERE active = 1";
		$r = $this->db->query($q) or die('ERROR '.$this->db->_error_message().error_log('ERROR '.$this->db->_error_message()));
		$data['payment'] = $r->result_object();

		$users_req = "SELECT `id`, `first_name`, `last_name`  FROM users WHERE active=1 ORDER BY `first_name` ASC";
		$users_res = $this->db->query($users_req) or die($this->mysqli->error);
		$data['users'] = $users_res->result_array();

		//trying to find POS closing archive
		if($mov == 'close') {

			//Get last archive
			$d = $this->cashier->getClosureData();

			//Get date of this archive
			$archive_date_ex = $this->cashier->getPosArchivesDatetime($d['file']);

			//check if the date is yesterday or today
			$archive_date = "$archive_date_ex[Y]-$archive_date_ex[m]-$archive_date_ex[dd]";
			$today_date = @date('Y-m-d');
			$yesterday_date = @date("Y-m-d", time() - 60 * 60 * 24);
			
			if(empty($d['seqid'])) { echo "La derniere cloture semble vide, As tu cloture 2 fois la caisse ?"; exit(); }
			
			//check if this archive has already been used for closing
			$qsid = "SELECT closing_id FROM pos_movements WHERE movement = 'close' AND closing_id = $d[seqid]";
			$rsid = $this->db->query($qsid) or die('ERROR '.$this->db->_error_message().error_log('ERROR '.$this->db->_error_message()));
			$osid = $rsid->result_object();
			$data['force'] = 0;
			
			if(($archive_date == $today_date OR $archive_date == $yesterday_date) AND empty($osid)) { 
				$data['archive_file'] = $d['file'];
				$data['archive_date'] = $archive_date;
				$this->cashier->posInfo('updateUsers');
			} else {
				$force = $this->input->get('force');
				if(!empty($force)) { 
					$data['archive_date'] = $archive_date;
					$data['force'] = 1;
					$this->cashier->posInfo('updateUsers');
				} else {
					header("Refresh:20");
					echo "<h2>Impossible de trouver une cloture.<br />
					As tu bien cloture la caisse ? <br />
					Si oui attends quelques minutes, la page de cloture va bientot s'afficher. <br />
					Ou alors, tu as deja entre tes donnees.</h2>
					Dernière cloture faite le : $archive_date
					<h2><a href='/pos/'>Retour</a></h2>
					<p><small><a href='/pos/movement/close?force=1'>Voir l'interface</a></small></p>";
					exit();
				}
			}
		}

		$this->load->view('pos/header', $data);
		$this->load->view('pos/movement', $data);
		$this->load->view('pos/footer');
	}

	public function save()
	{
	
		$data = array();
		$this->hmw->keyLogin();
		$user					= $this->ion_auth->user()->row();
		$user_groups 			= $this->ion_auth->get_users_groups()->result();
		$data['username']		= $user->username;
		$data['user_groups']	= $user_groups[0];
		$data["keylogin"]		= $this->session->userdata('keylogin');
		$data['title']			= 'Cashier';
		$data['mov']			= $this->input->post('mov');
		$userpost 				= $this->input->post('user');
		
		if(empty($userpost)) { 
			$userid = $user->id; 
		} else {
			$userid = $this->input->post('user');
		}
		
		$qpm = "INSERT INTO pos_movements SET movement = '".$this->input->post('mov')."', id_user = ".$userid.", comment = '".addslashes($this->input->post('comment'))."', pos_cash_amount = ".$this->cashier->posInfo('cashfloat').", safe_cash_amount = ".$this->cashier->calc('safe_current_cash_amount').", safe_tr_num = ".$this->cashier->calc('safe_current_tr_num');
		$rpm = $this->db->query($qpm) or die('ERROR '.$this->db->_error_message().error_log('ERROR '.$this->db->_error_message()));
		$pmid = $this->db->insert_id();

		$payid = date('y-m-d/').$pmid;
		$pay = array();

		if($this->input->post('mov')) { 
			foreach ($this->input->post() as $key => $val) {	
				$ex = @explode('_',$key);
				if($ex[0] == 'man' OR $ex[0] == 'pos') {
					$pay[$ex[1]][$ex[0]] = $val;
				}
			}
			
			if($this->input->post('mov') != 'safe_in' AND $this->input->post('mov') != 'safe_out') {
				$pay[1]['man'] = $this->cashier->clean_number($this->input->post('cash1'))+$this->cashier->clean_number($this->input->post('notes1'));
				$pay[2]['man'] = $this->cashier->clean_number($this->input->post('cbemv'))+$this->cashier->clean_number($this->input->post('cbcless'));
			}

			foreach ($pay as $idp => $val2) {
				if(!isset($val2['man']) OR empty($val2['man']) ) $val2['man'] = 0;
				if(!isset($val2['pos']) OR empty($val2['pos']) ) $val2['pos'] = 0;
				$val2man = $this->cashier->clean_number($val2['man']);
				if($this->input->post('mov') == 'safe_out') $val2man = -1 * abs($val2man);
				$qpp = "INSERT INTO pos_payments SET id_payment = $idp, id_movement = $pmid, amount_pos = ".$this->cashier->clean_number($val2['pos']).", amount_user = ".$val2man; 
				$rpp = $this->db->query($qpp) or die('ERROR '.$this->db->_error_message().error_log('ERROR '.$this->db->_error_message()));
			}
			
		}

		if($this->input->post('mov') == 'close') {
			$this->closing($this->input->post('archive'), $pmid);
		}

		$data['idtrans'] = $payid;

		$this->load->view('pos/header', $data);
		$this->load->view('pos/save', $data);
		$this->load->view('pos/footer');
	}

	private function closing($file, $pmid)
	{
		//Get archive info
		$d = $this->cashier->getClosureData(null, $file);

		//fill pos_payments with closing data, update or create
		foreach ($d['ca'] as $key => $val) {

			$qpm = "UPDATE pos_payments SET amount_pos = $val[SUM] WHERE id_movement = $pmid AND id_payment = $val[IDMETHOD]";
			$rup = $this->db->query($qpm) or die('ERROR '.$this->db->_error_message().error_log('ERROR '.$this->db->_error_message()));
			$af  = $this->db->affected_rows();

			if(empty($af)) {
				$qpmi = "INSERT INTO pos_payments SET amount_pos = $val[SUM], id_movement = $pmid, id_payment = $val[IDMETHOD]";
				$rupi = $this->db->query($qpmi) or die('ERROR '.$this->db->_error_message().error_log('ERROR '.$this->db->_error_message()));
			}	
		}

		$qup = "UPDATE pos_movements SET closing_file = '".$file."', closing_id = $d[seqid] WHERE id = $pmid";
		$rup = $this->db->query($qup) or die('ERROR '.$this->db->_error_message().error_log('ERROR '.$this->db->_error_message()));
	}

}
?>