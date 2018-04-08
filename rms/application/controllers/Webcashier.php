<?php
class webCashier extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		@$this->load->library('ion_auth');
		$this->load->library('ion_auth_acl');
		$this->load->library("hmw");
		$this->load->library("mmail");
		$this->load->library("cashier");
		$this->load->helper(array('form', 'url'));
		$this->load->library('form_validation');
	}

	public function index()
	{		
		$this->hmw->changeBu();// GENERIC changement de Bu

		$data = array();

		$this->hmw->keyLogin();
		$id_bu			 		=  $this->session->userdata('bu_id');

		$user					= $this->ion_auth->user()->row();
		$user_groups 			= $this->ion_auth->get_users_groups()->result();
		$data['username']		= $user->username;
		$data['user_groups']	= $user_groups[0];
		$data["keylogin"] 		= $this->session->userdata('keylogin');
		$data['title'] 			= 'Cashier';
		$data['safe_cash'] 		= $this->cashier->calc('safe_current_cash_amount', $id_bu);
		
		$data['bu_name'] =  $this->session->userdata('bu_name');

		$headers = $this->hmw->headerVars(1, "/webcashier/", "Cashier");
		$this->load->view('jq_header_pre', $headers['header_pre']);
		$this->load->view('jq_header_post', $headers['header_post']);
		$this->load->view('webcashier/index',$data);
		$this->load->view('webcashier/jq_footer_spe');
		$this->load->view('jq_footer');
	}

	public function stats()
	{		

		$this->hmw->changeBu();// GENERIC changement de Bu
		$this->hmw->keyLogin();
		
		$data = array();
		$id_bu			 		= $this->session->userdata('bu_id');
		$user					= $this->ion_auth->user()->row();
		$user_groups 			= $this->ion_auth->get_users_groups()->result();
		$data['username']		= $user->username;
		$data['user_groups']	= $user_groups[0];
		$data["keylogin"] 		= $this->session->userdata('keylogin');
		$data['title'] 			= 'Cashier - Sales stats';
		$data['bu_name'] 		=  $this->session->userdata('bu_name');
		
		$this->db->select('name, id_pos');
		$this->db->where('deleted', 0);
		$this->db->where('id_bu', $id_bu);
		$this->db->order_by('name', 'asc'); 
		$query = $this->db->get("users_pos");
		$data['users'] = $query->result_array();
		
		$pos_burger_category 	= '1A41C9AC-2BDA-421D-A64A-876A82F2A84F';
		$pos_dessert_category 	= '19E864A9-7FE1-4982-989D-F930E2C50091';
		$pos_potatoes 			= '2CC9930D-5A03-4206-841B-512B060EE030';
		$pos_cheese		 		= 'AE147589-5B91-42C1-B168-ACC2FAFE3193';
		
		$stats					= array();
		$post_user 				= $this->input->post('user');
		$post_date 				= $this->input->post('date');
		$rdate 					= "LIKE '".$post_date."%'";

		if(isset($post_date)) {
		if(!empty($post_date)) {

				//select total CA by user
				$q_total = "SELECT ROUND(SUM(amount_total)/1000) AS amount FROM sales_receipt AS sr 
					WHERE date_closed $rdate  
					AND sr.owner = '".$post_user."'
					AND sr.id_bu = $id_bu
					AND sr.canceled = 0";
				$r_total = $this->db->query($q_total) or die('ERROR '.$this->db->_error_message().error_log('ERROR '.$this->db->_error_message()));
				$o_total = $r_total->result_array();
				$stats[$post_user]['total'] = $o_total[0]['amount'];
				$stats[$post_user]['name'] = '';

				//select burger by users
				$q_burger = "
					SELECT ROUND(SUM(sri.quantity)/1000) AS count FROM sales_receiptitem AS sri 
					WHERE sri.product IN (SELECT sp.id_pos FROM sales_product AS sp JOIN sales_productcategory AS spc ON spc.id = sp.category WHERE spc.id = '".$pos_burger_category."')
					AND sri.receipt IN (SELECT id FROM sales_receipt WHERE owner = '".$post_user."' AND canceled = 0 AND id_bu = $id_bu AND date_closed $rdate)
					";

				$r_burger = $this->db->query($q_burger) or die('ERROR '.$this->db->_error_message().error_log('ERROR '.$this->db->_error_message()));
				$o_burger = $r_burger->result_array();
				$stats[$post_user]['burger'] = $o_burger[0]['count'];

				//select dessert by users
				$q_dessert = "SELECT ROUND(SUM(sri.quantity)/1000) AS count FROM sales_receiptitem AS sri 
				WHERE sri.product IN (SELECT sp.id_pos FROM sales_product AS sp JOIN sales_productcategory AS spc ON spc.id = sp.category WHERE spc.id = '".$pos_dessert_category."')
				AND sri.receipt IN (SELECT id FROM sales_receipt WHERE owner = '".$post_user."' AND canceled = 0 AND id_bu = $id_bu AND date_closed $rdate)";
			$r_dessert = $this->db->query($q_dessert) or die('ERROR '.$this->db->_error_message().error_log('ERROR '.$this->db->_error_message()));
			$o_dessert = $r_dessert->result_array();
			$stats[$post_user]['dessert'] = $o_dessert[0]['count'];	
			
			//select potatoes by users
			$q_potatoes = "SELECT ROUND(SUM(sri.quantity)/1000) AS count FROM sales_receiptitem AS sri 
				WHERE sri.product = '".$pos_potatoes."'
				AND sri.receipt IN (SELECT id FROM sales_receipt WHERE owner = '".$post_user."' AND canceled = 0 AND id_bu = $id_bu AND date_closed $rdate)";
			$r_potatoes = $this->db->query($q_potatoes) or die('ERROR '.$this->db->_error_message().error_log('ERROR '.$this->db->_error_message()));
			$o_potatoes = $r_potatoes->result_array();
			$stats[$post_user]['potatoes'] = $o_potatoes[0]['count'];

			//select potatoes cheese by users
			$q_cheese = "SELECT SUM(sria.quantity) AS count 
				FROM sales_receiptitemaddon AS sria 
				JOIN sales_receiptitem AS sri ON sri.id = sria.receiptitem
				JOIN sales_receipt AS sr ON sri.receipt = sr.id 
				WHERE sria.productaddon = '".$pos_cheese."'
				AND sr.owner = '".$post_user."' 
				AND sr.canceled = 0 
				AND sr.id_bu = $id_bu 
				AND sr.date_closed $rdate";
				
			$r_cheese = $this->db->query($q_cheese) or die('ERROR '.$this->db->_error_message().error_log('ERROR '.$this->db->_error_message()));
			$o_cheese = $r_cheese->result_array();
			$stats[$post_user]['cheese'] = $o_cheese[0]['count'];	
			
			$data['form_values']['date'] = $post_date;	
			$data['form_values']['user'] = $post_user;
		} 		

		$data['stats_sorted'] 	= $this->array_sort($stats, 'total', SORT_DESC);
/**
		$data['sum_cheese']		= array_sum(array_column($stats, 'cheese'));
		$data['sum_potatoes']	= array_sum(array_column($stats, 'potatoes'));
		$data['sum_dessert']	= array_sum(array_column($stats, 'dessert'));
		$data['sum_burger']		= array_sum(array_column($stats, 'burger'));
		
**/
		
	}

		$headers = $this->hmw->headerVars(1, "/webcashier/", "Cashier - Sales stats");
		$this->load->view('jq_header_pre', $headers['header_pre']);
		$this->load->view('jq_header_post', $headers['header_post']);
		$this->load->view('webcashier/stats',$data);
		$this->load->view('webcashier/jq_footer_spe');
		$this->load->view('jq_footer');
	}
	
	private function array_sort($array, $on, $order=SORT_ASC)
	{
	    $new_array = array();
	    $sortable_array = array();

	    if (count($array) > 0) {
	        foreach ($array as $k => $v) {
	            if (is_array($v)) {
	                foreach ($v as $k2 => $v2) {
	                    if ($k2 == $on) {
	                        $sortable_array[$k] = $v2;
	                    }
	                }
	            } else {
	                $sortable_array[$k] = $v;
	            }
	        }

	        switch ($order) {
	            case SORT_ASC:
	                asort($sortable_array);
	            break;
	            case SORT_DESC:
	                arsort($sortable_array);
	            break;
	        }

	        foreach ($sortable_array as $k => $v) {
	            $new_array[$k] = $array[$k];
	        }
	    }

	    return $new_array;
	}
	
	public function save_report_comment()
	{
		$id_bu = $this->session->userdata('bu_id');
		$user = $this->session->userdata('username');
		$curr_date = date('Y-m-d H:i:s');
		$reponse = 'ok';
		$data = $this->input->post();
		$updatedb = true;

		$this->db->select('name');
		$this->db->where('id', $id_bu);
		$bu_name = $this->db->get('bus')->row_array()['name'];
		$subject = "CASHIER $bu_name : New comment on report";
		
		$comment_data = array(
			'content' => $data['comment-'.$data['id']],
			'date' => $curr_date,
			'username' => $user,
			'mov_id' => $data['id']
		);
		
		if(!empty($data['comment-'.$data['id']])) {
			if(!$this->db->insert('pos_comment_report', $comment_data)) {
				$reponse = "Can't place the insert sql request, error message: ".$this->db->_error_message();
			}	
		}
		
		if (isset($data['validate-'.$data['id']])) {
			$this->db->set('status', 'validated');
			$subject .= " (Director Validated)";
		} else {
			if ($data['diff-'.$data['id']] != '0') $this->db->set('status', 'error');
		}
		
		if (!empty($data['corrected-'.$data['id']])) {
			$data['corrected-'.$data['id']] = str_replace(',', '.', $data['corrected-'.$data['id']]);
			if(!is_numeric($data['corrected-'.$data['id']])) { 
				$reponse = "corrected DIFF must be a number";
				$updatedb = false;
			} else {
				$this->db->set('corrected', $data['corrected-'.$data['id']]);
			}
		}		
	
		if($updatedb) { 
			$this->db->where('id', $data['id']);
			if (!$this->db->update('pos_movements')) {
				$reponse = "Can't place the insert sql request, error message: ".$this->db->_error_message();
			}
		}
		
		$this->db->select('movement, date');
		$this->db->where('id', $data['id']);
		$mov = $this->db->get('pos_movements')->row_array();
		$this->db->select('users.username, users.email, users.id');
		$this->db->distinct('users.username');
		$this->db->join('users_bus', 'users.id = users_bus.user_id', 'left');
		$this->db->join('users_groups', 'users.id = users_groups.user_id');
		$this->db->where('users.active', 1);
		$this->db->where_in('users_groups.group_id', array(1,4));
		$this->db->where('users_bus.bu_id', $id_bu);
		$query = $this->db->get("users");

		$server_name = $this->hmw->getParam('server_name');
							
		$email['subject'] 	= $subject;
		$email['msg'] 		= 'Comment on report for '.$bu_name.' from '.$user.' on ID: '.$data['id'].' | '.$mov['date'].' ('.$mov['movement'].') : <br />'. $data['comment-'.$data['id']]."<br /><a href='http://".$server_name."/webcashier/report/#".$data['id']."'>http://".$server_name."/webcashier/report/#".$data['id']."</a>";
		
		foreach ($query->result() as $row) {
			$email['to']	= $row->email;	
			$this->mmail->sendEmail($email);
		}
		echo json_encode(['reponse' => $reponse]);
	}
	
	// cd /var/www/hank/rms/rms && php index.php webcashier cliCheckClose 1
	
	public function cliCheckClose($id_bu) 
	{
		$currentDate =  date('Y-m-d');
		
		$this->db->select('date');
		$this->db->from('pos_movements');
		$this->db->where('movement', 'close');
		$this->db->order_by('date', 'DESC');
		$result = $this->db->get();
		$lastCloseDate = $result->row()->date;
		$createDate = new DateTime($lastCloseDate);
		$strip = $createDate->format('Y-m-d');
		if ($strip < $currentDate) {
			$this->db->select('users.username, users.email, users.id');
			$this->db->distinct('users.username');
			$this->db->join('users_bus', 'users.id = users_bus.user_id', 'left');
			$this->db->join('users_groups', 'users.id = users_groups.user_id');
			$this->db->where('users.active', 1);
			$this->db->where_in('users_groups.group_id', array(1,4));
			$this->db->where('users_bus.bu_id', $id_bu);
			$query = $this->db->get("users");
			
			$this->db->select('name');
			$this->db->where('id', $id_bu);
			$bu_name = $this->db->get('bus')->row_array()['name'];
			$email['subject'] 	= 'WARNING '.$bu_name.': No close for this evening';
			$email['msg'] 		= 'Cashier '.$bu_name.' wasn\'t closed this evening';
			foreach ($query->result() as $row) {
				$email['to']	= $row->email;	
				$this->mmail->sendEmail($email);
			}
		}
	}
	
	// cd /var/www/hank/rms/rms && php index.php webcashier cliAlertSafe 1
	
	public function cliAlertSafe($id_bu) {
		$currentAmount = $this->cashier->calc('safe_current_cash_amount', $id_bu);
		
		if ($currentAmount < 1) {
			$this->db->select('users.username, users.email, users.id');
			$this->db->distinct('users.username');
			$this->db->join('users_bus', 'users.id = users_bus.user_id', 'left');
			$this->db->join('users_groups', 'users.id = users_groups.user_id');
			$this->db->join('groups', 'users_groups.group_id = groups.id');
			$this->db->where('users.active', 1);
			$this->db->where('groups.level', 3);
			$this->db->where('users_bus.bu_id', $id_bu);
			$query = $this->db->get("users");
			
			$this->db->select('name');
			$this->db->where('id', $id_bu);
			$bu_name = $this->db->get('bus')->row_array()['name'];
			
			$email['subject'] 	= 'WARNING '.$bu_name.': Safe cash under 1 €';
			$email['msg'] 		= 'Safe '.$bu_name.' cash amount is '.$currentAmount.' €';
			foreach ($query->result() as $row) {
				
				$email['to']	= $row->email;	
				$this->mmail->sendEmail($email);
			}
		} else {
			$this->db->select('cashier_alert_amount_safe');
			$this->db->from('bus');
			$this->db->where('id', $id_bu);
			$cashierAlertAmountSafe = $this->db->get()->row_array()['cashier_alert_amount_safe'];
			
			if ($currentAmount > $cashierAlertAmountSafe) {
				$this->db->select('users.username, users.email, users.id');
				$this->db->distinct('users.username');
				$this->db->join('users_bus', 'users.id = users_bus.user_id', 'left');
				$this->db->join('users_groups', 'users.id = users_groups.user_id');
				$this->db->where('users.active', 1);
				$this->db->where_in('users_groups.group_id', array(3));
				$this->db->where('users_bus.bu_id', $id_bu);
				$query = $this->db->get("users");
				
				$this->db->select('name');
				$this->db->where('id', $id_bu);
				$bu_name = $this->db->get('bus')->row_array()['name'];
				$email['subject'] 	= 'WARNING '.$bu_name.': Safe cash is above '.$cashierAlertAmountSafe.' €';
				$email['msg'] 		= 'Safe '.$bu_name.' cash amount is '.$currentAmount;
				foreach ($query->result() as $row) {
					$email['to']	= $row->email;	
					$this->mmail->sendEmail($email);
				}
			}
		}
	}
	
	public function safe()
	{
		if (!$this->ion_auth_acl->has_permission('view_safe')) {
			redirect('/news/index');
		}

		$data = array();

		$user					= $this->ion_auth->user()->row();
		$user_groups 			= $this->ion_auth->get_users_groups()->result();
		$data['username']		= $user->username;

		$data["keylogin"] = $this->session->userdata('keylogin');
		$data['bu_name'] =  $this->session->userdata('bu_name');

		$data['title']  	= 'Safe';

		$headers = $this->hmw->headerVars(0, "/webcashier/", "Cashier - SAFE");
		$this->load->view('jq_header_pre', $headers['header_pre']);
		$this->load->view('jq_header_post', $headers['header_post']);
		$this->load->view('webcashier/safe',$data);
		$this->load->view('webcashier/jq_footer_spe');
		$this->load->view('jq_footer');
	}

	public function report($page = 1)
	{
		
		$this->load->library('pagination');
		if (!$this->ion_auth_acl->has_permission('view_report')) {
			redirect('/news/index');
		}

		$data = array();
		if ($this->input->post('type')) {	$filters['type'] = $this->input->post('type'); } else {	$filters['type'] = ""; }
		if ($this->input->post('user')) {	$filters['user-id'] = $this->input->post('user');	}	else { $filters['user-id'] = ""; }
		if ($this->input->post('sdate')) { $filters['sdate'] = $this->input->post('sdate'); } else { $filters['sdate'] = ""; }
		if ($this->input->post('edate')) { $filters['edate'] = $this->input->post('edate'); } else { $filters['edate'] = ""; }
		if ($this->input->post('status_ok')) { $filters['status_ok'] = true; } else { $filters['status_ok'] = ""; }
		if ($this->input->post('status_error')) { $filters['status_error'] = true; } else { $filters['status_error'] = ""; }
		if ($this->input->post('status_validated')) { $filters['status_validated'] = true; } else { $filters['status_validated'] = ""; }
		$data['filter'] = $filters;

		$id_bu			 		=  $this->session->userdata('bu_id');
		$param_pos_info 		= array();
		$param_pos_info['id_bu'] = $id_bu;
		
		$this->db->select('users.username, users.last_name, users.first_name, users.email, users.id');
		$this->db->distinct('users.username');
		$this->db->join('users_bus', 'users.id = users_bus.user_id', 'left');
		$this->db->where('users.active', 1);
		$this->db->where('users_bus.bu_id', $id_bu);
		$this->db->order_by('users.username', 'asc'); 
		$query = $this->db->get("users");
		$data['users'] = $query->result_array();

		$user					= $this->ion_auth->user()->row();
		$user_groups 			= $this->ion_auth->get_users_groups()->result();
		$data['username']		= $user->username;
		$data['user_groups']	= $user_groups[0];
		$data['all_user_groups'] = $user_groups;
		$data["keylogin"] 		= $this->session->userdata('keylogin');
		$data['title'] 			= 'Cashier reports';
		$data['safe_cash'] 		= $this->cashier->calc('safe_current_cash_amount', $id_bu);
		$data['safe_tr'] 		= $this->cashier->calc('safe_current_tr_amount', $id_bu);
		$data['monthly_to']		= $this->cashier->calc('current_monthly_turnover', $id_bu);
		$data['pos_cash'] 		= $this->cashier->posInfo('cashfloat', $param_pos_info);		
		$data['live_movements'] = $this->cashier->posInfo('getLiveMovements', $param_pos_info);
		$data['bu_name'] 		=  $this->session->userdata('bu_name');
		$lines					= array();
		
		$config_pages['base_url'] = base_url() . 'webcashier/report/';
		$config_pages['per_page'] = 50;
		$config_pages['use_page_numbers'] = TRUE;
		
		$this->db->select('pm.date, pm.id, u.username, pm.comment, pm.movement, pm.prelevement_amount, pm.pos_cash_amount, pm.safe_cash_amount, pm.safe_tr_amount, pm.closing_file, pm.corrected, pm.comment_report, pm.status, pm.employees_sp')
			->from('pos_movements as pm')
			->join('users as u', 'u.id = pm.id_user', 'left')
			->where('pm.id_bu', $id_bu);
			if (!empty($filters['type'])) $this->db->where('pm.movement', $filters['type']);
			if (!empty($filters['user-id'])) $this->db->where('u.id', $filters['user-id']);
			if (!empty($filters['sdate'])) $this->db->where('pm.date >= ', $filters['sdate']);
			if (!empty($filters['edate'])) $this->db->where('pm.date <= ', $filters['edate']);
			if (!empty($filters['status_ok'])) $status[] = 'ok';
			if (!empty($filters['status_error'])) $status[] = 'error';
			if (!empty($filters['status_validated'])) $status[] = 'validated';
			if (!empty($status)) $this->db->where_in('pm.status', $status);
			$this->db->order_by('pm.id desc');
			$this->db->limit(300);
		$r_pm = $this->db->get() or die('ERROR '.$this->db->_error_message().error_log('ERROR '.$this->db->_error_message()));
		
		$res_pm = $r_pm->result_array();
		$temp_sp = $res_pm;
		foreach ($temp_sp as $key => $line) {
			$res_pm[$key]['employees_sp'] = unserialize($line['employees_sp']);
		}
		$offset = $config_pages['per_page'] * ($page - 1);
		$config_pages['total_rows'] = $r_pm->num_rows();
		$res_pm = array_slice($res_pm, $offset, $config_pages['per_page']);
		$this->pagination->initialize($config_pages);
		
		foreach ($res_pm as $key_pm => $m) {
			$this->db->from('pos_payments as pp')
					->join('pos_payments_type as ppt', 'pp.id_payment = ppt.id')
					->where('id_movement', $m['id'])
					->where('ppt.id_bu', $id_bu)
					->order_by('id_payment asc');
			$r_pp = $this->db->get() or die('ERROR '.$this->db->_error_message().error_log('ERROR '.$this->db->_error_message()));
			$res_pp = $r_pp->result_array();
			$lines[$m['id']]['mov'] = $m;
			$lines[$m['id']]['pay'] = $res_pp;
				
			if($m['movement'] == 'close') {
				if (empty($m['closing_file'])) exit ("No closing file");
				$param = array('closing_file' =>  $m['closing_file']);
				$param['id_bu'] = $id_bu;
				
				//get cashFdcMovements to user archive
				$paramFdc = $param;
				$paramFdc['archive'] = $m['closing_file'];
				
				$lines[$m['id']]['close_users'] 	= $this->cashier->posInfo('getUsers', $param);
				$lines[$m['id']]['cashmovements'] 	= $this->cashier->posInfo('getMovements', $param);

				$archivedCashfloat = $this->cashier->posInfo('getFdcMovements', $paramFdc);
				$param['archivedCashfloat'] = $archivedCashfloat;
				$lines[$m['id']]['cashFdcMovements'] = $this->cashier->posInfo('getCashContainerName', $param);
								
				$lines[$m['id']]['cashDrawerOpened'] = $this->cashier->getArchivedDrawerOpenedEvents($id_bu, $m['closing_file']);
				$lines[$m['id']]['cancelledReceipts'] = $this->cashier->getArchivedCancelledReceipts($id_bu, $m['closing_file']);
				$lines[$m['id']]['userActionStats'] = $this->cashier->userActionStats($id_bu, $m['closing_file']);
				$lines[$m['id']]['total_actions'] = $this->cashier->countAllArchivedReceipts($id_bu, $m['closing_file']);
			}
			
			//get comments for movement
			$this->db->from('pos_comment_report');
			$this->db->where('mov_id', $m['id']);
			$this->db->limit(50);
			$this->db->order_by('id', 'desc');
			$comments_mov = $this->db->get()->result_array();
			$lines[$m['id']]['comments'] = $comments_mov;
		}

		$data['lines'] = $lines;
		$headers = $this->hmw->headerVars(0, "/webcashier/", "Cashier - REPORT");
		$this->load->view('jq_header_pre', $headers['header_pre']);
		$this->load->view('jq_header_post', $headers['header_post']);
		$this->load->view('webcashier/report',$data);
		$this->load->view('webcashier/jq_footer_spe');
		$this->load->view('webcashier/jq_footer_report.php');
		$this->load->view('jq_footer');
	}

	public function movement($mov)
	{

		//'middle','close','safe_in','safe_out','pos_in','pos_out'
		$data = array();
		$this->hmw->keyLogin();

		$user					= $this->ion_auth->user()->row();
		$user_groups 			= $this->ion_auth->get_users_groups()->result();
		$data['username']		= $user->username;
		$data['user_groups']	= $user_groups[0];
		$data['all_user_groups'] = $user_groups;
		$data['mov']			= $mov;
		$data['archive_file'] 	= null;
		$data['bu_name'] 		=  $this->session->userdata('bu_name');
		$data["keylogin"] 		= $this->session->userdata('keylogin');
		$data['title'] 			= "Cashier - ".strtoupper($mov);
		$id_bu			 		=  $this->session->userdata('bu_id');
		$param_pos_info 		= array();

		$this->db->select('*')->from('pos_payments_type')->where('active',1)->where('id_bu', $id_bu);
		$r = $this->db->get() or die('ERROR '.$this->db->_error_message().error_log('ERROR '.$this->db->_error_message()));
		$data['payment'] = $r->result_object();

		$this->db->select('users.username, users.last_name, users.first_name, users.email, users.id');
		$this->db->distinct('users.username');
		$this->db->join('users_bus', 'users.id = users_bus.user_id', 'left');
		$this->db->where('users.active', 1);
		$this->db->where('users_bus.bu_id', $id_bu);
		$this->db->order_by('users.username', 'asc'); 
		$query = $this->db->get("users");
		$data['users'] = $query->result();

		//trying to find POS closing archive
		if($mov == 'close') {
			
			//Get last archive
			$d = $this->cashier->getClosureData(null, null, $id_bu);
			
			//Get date of this archive
			$archive_date_ex = $this->cashier->getPosArchivesDatetime($d['file']);

			//check if the date is yesterday or today
			$archive_date = "$archive_date_ex[Y]-$archive_date_ex[m]-$archive_date_ex[dd]";
			$today_date = @date('Y-m-d');
			$yesterday_date = @date("Y-m-d", time() - 60 * 60 * 24);
			
			if(empty($d['seqid'])) { echo "La derniere cloture semble vide, As tu cloture 2 fois la caisse ?"; exit(); }
			
			//check if this archive has already been used for closing
			$this->db->select('closing_id')->from('pos_movements')->where('movement', 'close')->where('closing_id', $d['seqid'])->where('id_bu', $id_bu);
			$rsid = $this->db->get() or die('ERROR '.$this->db->_error_message().error_log('ERROR '.$this->db->_error_message()));
			$osid = $rsid->result_object();
			$data['force'] = 0;
			
			$param_pos_info['id_bu'] = $id_bu;

			if(($archive_date == $today_date OR $archive_date == $yesterday_date) AND empty($osid)) { 
				$data['closure_data'] = $d;
				$data['archive_file'] = $d['file'];
				$data['archive_date'] = $archive_date;
				$this->cashier->InsertTerminals($id_bu);
				$this->cashier->posInfo('updateUsers', $param_pos_info);
				$this->cashier->posInfo('updateProductCategory', $param_pos_info);
			} else {
				$force = $this->input->get('force');
				if(!empty($force)) { 
					$data['archive_date'] = $archive_date;
					$data['force'] = 1;
					$this->cashier->posInfo('updateUsers', $param_pos_info);
					$this->cashier->InsertTerminals($id_bu);
				} else {
					header("Refresh:20");
					echo "<h2>Impossible de trouver une cloture.<br />
					As tu bien cloture la caisse ? <br />
					Si oui attends quelques minutes, la page de cloture va bientot s'afficher. <br />
					Ou alors, tu as deja entre tes donnees.</h2>
					Derniere cloture faite pour : $archive_date
					<h2><a href='/webcashier/'>Retour</a></h2>
					<p><small><a href='/webcashier/movement/close?force=1'>Voir l'interface</a></small></p>";
					exit();
				}
			}
		}
		$headers = $this->hmw->headerVars(0, "/webcashier/", "Cashier - POS");
		$this->load->view('jq_header_pre', $headers['header_pre']);
		$this->load->view('jq_header_post', $headers['header_post']);
		$this->load->view('webcashier/movement',$data);
		$this->load->view('webcashier/jq_footer_spe');
		$this->load->view('jq_footer');
	}
	
	private function planning() 
	{
	
		$this->load->library('hmw');
		$this->load->library('shiftplanning');
	
		$sp_key		= $this->hmw->getParam('sp_key'); 
		$sp_user	= $this->hmw->getParam('sp_user');
		$sp_pass	= $this->hmw->getParam('sp_pass');
	
		/* set the developer key on class initialization */
		$shiftplanning = new shiftplanning(array('key' => $sp_key));

		$session = $shiftplanning->getSession( );
		if( !$session ) {
			// perform a single API call to authenticate a user
			$response = $shiftplanning->doLogin(
			array('username' => $sp_user, 'password' => $sp_pass));

				if( $response['status']['code'] == 1 )
				{// check to make sure that login was successful
					$session = $shiftplanning->getSession( );	// return the session data after successful login
				} else {
					error_log(" CANT GET SESSION".$response['status']['text'] . "--" . $response['status']['error']);
				}
		}

		if( $session ) {
			$response = $shiftplanning->setRequest(
				array(
					array('module' => 'dashboard.onnow', 'method' => 'GET'),
					array('module' => 'location.locations', 'method' => 'GET')
				)
			);
			//print_r($shiftplanning->getResponse(1));
			$r = $shiftplanning->getResponse(0);
			return $r;		
		}
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
		$data['bu_name'] 		= $this->session->userdata('bu_name');
		$data['mov']			= $this->input->post('mov');
		$userpost 				= $this->input->post('user');
		$id_bu			 		= $this->session->userdata('bu_id');
		$param_pos_info 		= array();
		$param_pos_info['id_bu'] = $id_bu;
		$param_pos_info['archive'] = $this->input->post('archive');
		if($this->input->post('mov') == 'close') $planning = $this->planning();
		
		$employees_sp = array();
		$buinfo = $this->hmw->getBuInfo($id_bu);
		$bu_position_id = explode (',',$buinfo->humanity_positions);
		if (isset($planning['data'])) {
			foreach ($planning['data'] as $line) {
				if (in_array ($line['schedule_id'], $bu_position_id)) 
				array_push($employees_sp, $line['employee_name']);
			}
		}
		if(empty($userpost)) { 
			$userid = $user->id; 
		} else {
			$userid = $this->input->post('user');
		}
		
		$postmov = $this->input->post('mov');
		if(empty($postmov)) exit('Error, empty movement, please try again.');
		
		$this->db->trans_begin();
		$keys = "(movement, id_user, comment, prelevement_amount, safe_cash_amount, safe_tr_amount, id_bu, employees_sp";
		
		
		$values = "('" . $postmov . "', " . $userid . ", '" . addslashes($this->input->post('comment')) . "', '" . $this->input->post('prelevement') . "', '" 
							. $this->cashier->calc('safe_current_cash_amount', $id_bu) . "', '" . $this->cashier->calc('safe_current_tr_num', $id_bu) . "', '"
							. $id_bu . "', '" . serialize($employees_sp) . "'"; 

		$comment_report = $this->input->post('comment_report');
		if(!empty($comment_report)){
			$keys .= ", comment_report";
			$values	.= ", '" . addslashes($this->input->post('comment_report')) . "'";
		} 
		
		if($this->input->post('mov') == 'close') {
			$keys .= ", pos_cash_amount";
			$values .= ", '" . $this->cashier->posInfo('cashfloatArchive', $param_pos_info) . "'";
		}
		$keys .= ")";
		$values .= ")";
		$queryStringPm = "INSERT INTO `pos_movements` " . $keys . "VALUES" . $values . ";";
		$this->db->query($queryStringPm);
		$pmid = $this->db->insert_id();

		$payid = $pmid;
		$pay = array();

		if($this->input->post('mov')) { 
			foreach ($this->input->post() as $key => $val) {	
				$ex = @explode('_',$key);
				if($ex[0] == 'man' OR $ex[0] == 'pos') {
					$pay[$ex[1]][$ex[0]] = $val;
				}
			}
			
			if($this->input->post('mov') != 'safe_in' AND $this->input->post('mov') != 'safe_out') {
				
				$pay[1]['man'] = $this->cashier->clean_number($this->input->post('cash2'));
				$pay[2]['man'] = $this->cashier->clean_number($this->input->post('cbemv'))+$this->cashier->clean_number($this->input->post('cbcless'));
			}

			foreach ($pay as $idp => $val2) {
				if(!isset($val2['man']) OR empty($val2['man']) ) $val2['man'] = 0;
				if(!isset($val2['pos']) OR empty($val2['pos']) ) $val2['pos'] = 0;
				$val2man = $this->cashier->clean_number($val2['man']);
				if($this->input->post('mov') == 'safe_out') $val2man = -1 * abs($val2man);
				$queryStringPp = "INSERT INTO `pos_payments` (id_payment, id_movement, amount_pos, amount_user) VALUES ('" . $idp . "', '" . $pmid . "', '" . $this->cashier->clean_number($val2['pos']) . "', '" . $val2man . "');";  
				$this->db->query($queryStringPp);
			}
		}
		
		$pay_values = $pay;
		foreach ($pay as $key => $value) {
			$this->db->where('active',1)->where('id_bu', $id_bu)->where('id', $key);
			$r = $this->db->get('pos_payments_type') or die('ERROR '.$this->db->_error_message().error_log('ERROR '.$this->db->_error_message()));
			$payment = $r->row_array();
			$pay_values[$key]['id'] = $payment['id'];
			$pay_values[$key]['name'] = $payment['name'];
			if(!isset($value['man']) OR empty($value['man']) ) $pay_values[$key]['man'] = 0;
			if(!isset($value['pos']) OR empty($value['pos']) ) $pay_values[$key]['pos'] = 0;
		}
		
		uasort($pay_values, array("webcashier", "cmp"));
		
		if($this->input->post('mov') == 'close') {
			
			$this->db->select('cashier_alert_amount_close_min,cashier_alert_amount_close_max');
			$this->db->from('bus');
			$this->db->where('id', $id_bu);
			$alert_amount = $this->db->get()->row_array() or die('ERROR: (probably missing value in database) '.$this->db->_error_message.error_log('ERROR '.$this->db->_error_message()));
			
			$cashpad_amount = $this->cashier->posInfo('cashfloatArchive', $param_pos_info);
			$cash_user = floatval($pay_values[1]['man']);
			$cb_balance = $pay_values[2]['man'] - $pay_values[2]['pos'];
		 	$tr_balance = $pay_values[3]['man'] - $pay_values[3]['pos'];
			$chq_balance = $pay_values[4]['man'] - $pay_values[4]['pos'];
			$cc_balance = $pay_values[11]['man'] - $pay_values[11]['pos'];
			$prelevement = floatval($this->input->post('prelevement'));
            
			$diff = $cash_user + $cb_balance + $tr_balance + $chq_balance - $cashpad_amount + $prelevement + $cc_balance;
			$test_diff = false;
            if($diff <= $alert_amount['cashier_alert_amount_close_min']) $test_diff = true;
            if($diff >= $alert_amount['cashier_alert_amount_close_max']) $test_diff = true;
			
            if (($test_diff)) {
				if (!$this->input->post('blc')) {
					$this->db->trans_rollback();
					$form_values = $this->input->post();
					$form_values['cashpad_amount'] = $cashpad_amount;
					$form_values['diff'] = $diff;
					$this->session->set_flashdata('form_values', $form_values);
					$this->session->set_flashdata('pay_values', $pay_values);
					
					redirect('/webcashier/movement/close', 'location');
				} else {
					$this->db->select('users.username, users.email, users.id');
					$this->db->distinct('users.username');
					$this->db->join('users_bus', 'users.id = users_bus.user_id', 'left');
					$this->db->join('users_groups', 'users.id = users_groups.user_id');
					$this->db->where('users.active', 1);
					$this->db->where_in('users_groups.group_id', array(1,4));
					$this->db->where('users_bus.bu_id', $id_bu);
					$query = $this->db->get("users");
						
					$server_name = $this->hmw->getParam('server_name');
					$this->db->select('name');
					$this->db->where('id', $id_bu);
					$bu_name = $this->db->get('bus')->row_array()['name'];
					$operand = $this->addOperand($num);
					$email['subject'] 	= 'RMS CASHIER WARNING '.$bu_name.': Erreur de caisse';
					$email['msg'] 		= "BU: ".$bu_name." | ID: ".$pmid."<br />Difference de $operand" . number_format($diff, 2) ."€ <br /><a href='http://".$server_name."/webcashier/report/#".$pmid."'>http://".$server_name."/webcashier/report/#".$pmid."</a>";
					foreach ($query->result() as $row) {
						$email['to']	= $row->email;	
						$this->mmail->sendEmail($email);
					}
				}
				$this->db->trans_commit();
				$this->db->set('status', 'error');
				$this->db->where('id', $pmid);
				$this->db->update('pos_movements');
			} else {
				$this->db->trans_commit();
			}
			$this->closing($this->input->post('archive'), $pmid);
		} else {
			$this->db->trans_commit();
		}
		
		$cancelledReceipts = $this->cashier->getArchivedCancelledReceipts($id_bu, $this->input->post('archive'));
		if (count($cancelledReceipts) > 1) {
			$this->db->set('status', 'error');
			$this->db->where('id', $pmid);
			$this->db->update('pos_movements');
		}

		$data['idtrans'] = $payid;

		$headers = $this->hmw->headerVars(0, "/webcashier/", "Cashier - POS");
		$this->load->view('jq_header_pre', $headers['header_pre']);
		$this->load->view('jq_header_post', $headers['header_post']);
		$this->load->view('webcashier/save', $data);
		$this->load->view('jq_footer');
	}
	
	private function cmp($a, $b) {
		 $ret = ($a['id'] > $b['id'] ? true : false);
		 return ($ret);
	}

	private function closing($file, $pmid)
	{
		$id_bu =  $this->session->userdata('bu_id');
		
		if(empty($file)) exit('Error: empty archive file');
		if(empty($pmid)) exit('Error: empty movement id');
		 
		//Get archive info
		$d = $this->cashier->getClosureData(null, $file, $id_bu);
		

		//fill pos_payments with closing data, update or create
		foreach ($d['ca'] as $key => $val) {

			$this->db->set('amount_pos', $val['SUM'])->where('id_movement', $pmid)->where('id_payment', $val['IDMETHOD']);
			$this->db->update('pos_payments');
			$af  = $this->db->affected_rows();

			$this->db->select('amount_pos')->where('id_movement', $pmid)->where('amount_pos != 0');
			$pos_payments = $this->db->get('pos_payments');
			
			if(empty($af) && empty($pos_payments)) {
				$this->db->set('amount_pos', $val['SUM'])->set('id_movement', $pmid)->set('id_payment', $val['IDMETHOD']);
				$this->db->insert('pos_payments') or die('ERROR '.$this->db->_error_message().error_log('ERROR '.$this->db->_error_message()));
			}
		}

		$this->db->set('closing_file', $file)->set('closing_id', $d['seqid'])->where('id', $pmid)->where('id_bu', $id_bu);
		$this->db->update('pos_movements') or die('ERROR '.$this->db->_error_message().error_log('ERROR '.$this->db->_error_message()));
	}
	
	private function addOperand($num) {
		if($num > 0) return "+";
		return "";
	}

}
?>