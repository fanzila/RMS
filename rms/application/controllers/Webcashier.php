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

		//cd ; rsync -a --progress 62.210.214.17:/home/cashbkp/1/cashpad/archives/c3c9e76c-30e1-47dd-8375-4d5f37ba87ff/201805* Dropbox/www_vm/hank/pos/1/cashpad/archives/c3c9e76c-30e1-47dd-8375-4d5f37ba87ff/
		//ssh vm ; sudo -s ; cd /var/www/hank/rms/rms && php index.php order cliUpdateSales 1
		
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
		$data['bu_name'] =  $this->session->userdata('bu_name');

		$pos_burger_category 	= '1A41C9AC-2BDA-421D-A64A-876A82F2A84F';
		$pos_dessert_category 	= '19E864A9-7FE1-4982-989D-F930E2C50091';
		$pos_potatoes 			= '2CC9930D-5A03-4206-841B-512B060EE030';
		$pos_cheese		 		= 'AE147589-5B91-42C1-B168-ACC2FAFE3193';
		
		$stats = array();
		$rdate = ">= DATE(NOW()) - INTERVAL 7 DAY";
		$post_date = $this->input->post('date');
		if(isset($post_date)) {
			$rdate = "LIKE '".$this->input->post('date')."%'";
			$data['form_values']['date'] = $this->input->post('date');
		}
			 
		//select user
		$q_user = "SELECT UPPER(id_pos) as id_pos, name, id_bu FROM users_pos
			WHERE deleted = 0 AND id_bu = $id_bu";
		$r_user = $this->db->query($q_user) or die('ERROR '.$this->db->_error_message().error_log('ERROR '.$this->db->_error_message()));
		$o_user = $r_user->result_object();

		foreach ($o_user as $key => $value) {

			//select total CA by user
			$q_total = "SELECT UPPER(sr.owner) as owner, ROUND(SUM(amount_total)/1000) AS amount FROM sales_receipt AS sr 
				WHERE date_closed $rdate  
			AND sr.owner = '".$value->id_pos."'
			AND sr.id_bu = $id_bu
			AND sr.canceled = 0";
			$r_total = $this->db->query($q_total) or die('ERROR '.$this->db->_error_message().error_log('ERROR '.$this->db->_error_message()));
			$o_total = $r_total->result_array();
			$stats[$value->id_pos]['total'] = $o_total[0]['amount'];
			$stats[$value->id_pos]['name'] = $value->name;

			//select burger by users
			$q_burger = "
				SELECT ROUND(SUM(sri.quantity)/1000) AS count FROM sales_receiptitem AS sri
			WHERE sri.product IN (SELECT sp.id_pos FROM sales_product AS sp JOIN sales_productcategory AS spc ON spc.id = sp.category WHERE spc.id = '".$pos_burger_category."')
			AND sri.receipt IN (SELECT id FROM sales_receipt WHERE owner = '".$value->id_pos."' AND canceled = 0 AND id_bu = $id_bu AND date_closed $rdate)
			";

			$r_burger = $this->db->query($q_burger) or die('ERROR '.$this->db->_error_message().error_log('ERROR '.$this->db->_error_message()));
			$o_burger = $r_burger->result_array();
			$stats[$value->id_pos]['burger'] = $o_burger[0]['count'];

			//select dessert by users
			$q_dessert = "SELECT ROUND(SUM(sri.quantity)/1000) AS count FROM sales_receiptitem AS sri
				WHERE sri.product IN (SELECT sp.id_pos FROM sales_product AS sp JOIN sales_productcategory AS spc ON spc.id = sp.category WHERE spc.id = '".$pos_dessert_category."')
			AND sri.receipt IN (SELECT id FROM sales_receipt WHERE owner = '".$value->id_pos."' AND canceled = 0 AND id_bu = $id_bu AND date_closed $rdate)";
			$r_dessert = $this->db->query($q_dessert) or die('ERROR '.$this->db->_error_message().error_log('ERROR '.$this->db->_error_message()));
			$o_dessert = $r_dessert->result_array();
			$stats[$value->id_pos]['dessert'] = $o_dessert[0]['count'];

			//select potatoes by users
			$q_potatoes = "SELECT ROUND(SUM(sri.quantity)/1000) AS count FROM sales_receiptitem AS sri
				WHERE sri.product = '".$pos_potatoes."'
			AND sri.receipt IN (SELECT id FROM sales_receipt WHERE owner = '".$value->id_pos."' AND canceled = 0 AND id_bu = $id_bu AND date_closed $rdate)";
			$r_potatoes = $this->db->query($q_potatoes) or die('ERROR '.$this->db->_error_message().error_log('ERROR '.$this->db->_error_message()));
			$o_potatoes = $r_potatoes->result_array();
			$stats[$value->id_pos]['potatoes'] = $o_potatoes[0]['count'];

			//select potatoes cheese by users
			$q_cheese = "
				SELECT SUM(sria.quantity) AS count
			FROM sales_receiptitemaddon AS sria
			WHERE sria.productaddon = '".$pos_cheese."'
			AND sria.receiptitem IN (SELECT sri.id FROM sales_receiptitem AS sri
				JOIN sales_receipt AS sr ON sri.receipt = sr.id
				WHERE sr.owner = '".$value->id_pos."'
				AND sr.canceled = 0
				AND sr.id_bu = $id_bu
				AND sr.date_closed $rdate)
				";

				$r_cheese = $this->db->query($q_cheese) or die('ERROR '.$this->db->_error_message().error_log('ERROR '.$this->db->_error_message()));
				$o_cheese = $r_cheese->result_array();
				$stats[$value->id_pos]['cheese'] = $o_cheese[0]['count'];
			}

			$data['stats_sorted'] 	= $this->array_sort($stats, 'total', SORT_DESC);
			$data['sum_cheese']		= array_sum(array_column($stats, 'cheese'));
			$data['sum_potatoes']	= array_sum(array_column($stats, 'potatoes'));
			$data['sum_dessert']	= array_sum(array_column($stats, 'dessert'));
			$data['sum_burger']		= array_sum(array_column($stats, 'burger'));

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
			if(empty($data)) exit('No transmission');
		
			$this->db->select('name');
			$this->db->where('id', $id_bu);
			$bu_name = $this->db->get('bus')->row_array()['name'];
			$subject = "CASHIER $bu_name : New comment on report";
			$tosend = true;
		
			if(empty($data['comment-'.$data['id']])) $data['comment-'.$data['id']] = " "; 
		
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
				$tosend   = false;
			} else {
				$this->db->set('status', 'error');
			}

			if (!empty($data['corrected-'.$data['id']])) {
				$data['corrected-'.$data['id']] = str_replace(',', '.', $data['corrected-'.$data['id']]);
				if(!is_numeric($data['corrected-'.$data['id']])) {
					$reponse = "corrected DIFF must be a number";
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

			$server_name = $this->hmw->getParam('server_name');

			$msg = 'Comment on report for '.$bu_name.' from '.$user.' on ID: '.$data['id'].' | '.$mov['date'].' ('.$mov['movement'].') : <br />'. $data['comment-'.$data['id']]."<br /><a href='http://".$server_name."/webcashier/report/#".$data['id']."'>http://".$server_name."/webcashier/report/#".$data['id']."</a>";

				if($tosend) {
					$this->mmail->prepare($subject, $msg)
						->toList('reports', $id_bu)
							->send();
				}

				echo json_encode(['reponse' => $reponse]);
			}

			// cd /var/www/hank/rms/rms && php index.php webcashier cliCheckClose 1

			public function cliCheckClose($id_bu)
			{

				if(!is_cli()) exit();

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

					$this->db->select('name');
					$this->db->where('id', $id_bu);
					$bu_name = $this->db->get('bus')->row_array()['name'];

					$subject = 'WARNING ' . $bu_name . ': No close for this evening';
					$msg     = 'Cashier ' . $bu_name . ' wasn\'t closed this evening';
					$this->mmail->prepare($subject, $msg)
						->toList('cashier_alerts', $id_bu)
							->send();
				}
			}

			// cd /var/www/hank/rms/rms && php index.php webcashier cliReport
			public function cliReport()
			{

				if(!is_cli()) exit();

				$this->db->select('id, name');
				$query = $this->db->get('bus');
				$total_ca = 0;
				
				$txt = "<p style='font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; Margin-bottom: 15px;'><b>RMS CLOSE REPORT ". date('d/m/Y'). "</b></p>
					<table border='0' cellpadding='3' cellspacing='3' class='btn btn-primary' style='border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; width: 100%; box-sizing: border-box;'>
				<tbody>
				<tr bgcolor='#ffc300'>
				<td align='center' style='font-family: sans-serif; font-size: 12px; vertical-align: top; padding-bottom: 15px;'>
				BU
				</td>
				<td align='center' style='font-family: sans-serif; font-size: 12px; vertical-align: top; padding-bottom: 15px;'>
				Date
				</td>
				<td align='center' style='font-family: sans-serif; font-size: 12px; vertical-align: top; padding-bottom: 15px;'>
				User close
				</td>
				<td align='center' style='font-family: sans-serif; font-size: 12px; vertical-align: top; padding-bottom: 15px;'>
				Status
				</td>
				<td align='center' style='font-family: sans-serif; font-size: 12px; vertical-align: top; padding-bottom: 15px;'>
				TO
				</td>
				<td align='center' style='font-family: sans-serif; font-size: 12px; vertical-align: top; padding-bottom: 15px;'>
				Diff <br />(canceled)<br />Comment close
				</td>
				<td align='center' style='font-family: sans-serif; font-size: 12px; vertical-align: top; padding-bottom: 15px;'>
				 User checklist<br />Date
				</td>
				  
				</tr>";
		
				foreach ($query->result() as $row) {

					//infos_close
					$this->db->where('DATE_FORMAT( infos_close.date,  \'%Y-%m-%d\' ) = CURDATE()');
					$this->db->where('bu_id', $row->id);
					$query_ic = $this->db->get("infos_close");
					$res_ic = $query_ic->result_array();
					
					$bgcolor = '#ECECEC';
					if(!isset($res_ic[0]['status'])) $res_ic[0]['status'] = '';
					if($res_ic[0]['status'] == 'error') $bgcolor = '#ff6400';
					if(isset($res_ic[1]['status'])) if($res_ic[1]['status'] == 'error') $bgcolor = '#ff6400';
					if(isset($res_ic[2]['status'])) if($res_ic[2]['status'] == 'error') $bgcolor = '#ff6400';
					if(isset($res_ic[3]['status'])) if($res_ic[3]['status'] == 'error') $bgcolor = '#ff6400';
					if(isset($res_ic[4]['status'])) if($res_ic[4]['status'] == 'error') $bgcolor = '#ff6400';
					
					$txt .= "<tr bgcolor='#ECECEC'>";
					$txt .= "<td align='left' style='font-family: sans-serif; font-size: 14px; vertical-align: top; padding-bottom: 15px;'>$row->name</td>";
					$txt .= "<td align='left' style='font-family: sans-serif; font-size: 14px; vertical-align: top; padding-bottom: 15px;'>";
					if(!isset($res_ic[0]['date'])) $res_ic[0]['date'] = '';
					$txt .= $res_ic[0]['date'];
					if(isset($res_ic[1]['date'])) $txt .= "<hr />".$res_ic[1]['date'];
					if(isset($res_ic[2]['date'])) $txt .= "<hr />".$res_ic[2]['date'];
					if(isset($res_ic[3]['date'])) $txt .= "<hr />".$res_ic[3]['date'];
					if(isset($res_ic[4]['date'])) $txt .= "<hr />".$res_ic[4]['date'];
					$txt .= " </td>";
					$txt .= "<td align='left' style='font-family: sans-serif; font-size: 14px; vertical-align: top; padding-bottom: 15px;'>";			
					$info_user_cashier = '-';
					if(isset($res_ic[0]['id_user_cashier'])) $info_user_cashier = $this->hmw->getUser($res_ic[0]['id_user_cashier'])->username;
					$info_user_cashier1 = '-';
					if(isset($res_ic[1]['id_user_cashier'])) $info_user_cashier1 = $this->hmw->getUser($res_ic[1]['id_user_cashier'])->username;
					$info_user_cashier2 = '-';
					if(isset($res_ic[2]['id_user_cashier'])) $info_user_cashier2 = $this->hmw->getUser($res_ic[2]['id_user_cashier'])->username;
					$info_user_cashier3 = '-';
					if(isset($res_ic[3]['id_user_cashier'])) $info_user_cashier3 = $this->hmw->getUser($res_ic[3]['id_user_cashier'])->username;
					$info_user_cashier4 = '-';
					if(isset($res_ic[4]['id_user_cashier'])) $info_user_cashier4 = $this->hmw->getUser($res_ic[4]['id_user_cashier'])->username;
					$txt .= $info_user_cashier;
					if(isset($res_ic[1]['id_user_cashier'])) $txt .= "<hr />".$info_user_cashier1;
					if(isset($res_ic[2]['id_user_cashier'])) $txt .= "<hr />".$info_user_cashier2;
					if(isset($res_ic[3]['id_user_cashier'])) $txt .= "<hr />".$info_user_cashier3;
					if(isset($res_ic[4]['id_user_cashier'])) $txt .= "<hr />".$info_user_cashier4;
					$txt .= " </td>";
					$txt .= "<td align='left' style='font-family: sans-serif; font-size: 14px; vertical-align: top; padding-bottom: 15px;' bgcolor='$bgcolor'>";	
					$txt .=	$res_ic[0]['status'];
					if(isset($res_ic[1]['status'])) $txt .= "<hr />".$res_ic[1]['status'];
					if(isset($res_ic[2]['status'])) $txt .= "<hr />".$res_ic[2]['status'];
					if(isset($res_ic[3]['status'])) $txt .= "<hr />".$res_ic[3]['status'];
					if(isset($res_ic[4]['status'])) $txt .= "<hr />".$res_ic[4]['status'];
					$txt .= "</td>
					<td align='left' style='font-family: sans-serif; font-size: 14px; vertical-align: top; padding-bottom: 15px;'>
					";
					if(!isset($res_ic[0]['to'])) $res_ic[0]['to'] = 0;
					$txt .= number_format($res_ic[0]['to'], 2)."€";
					$total_ca += $res_ic[0]['to'];
					if(isset($res_ic[1]['to'])) { $txt .= "<hr />".number_format($res_ic[1]['to'], 2)."€"; $total_ca += $res_ic[1]['to']; }
					if(isset($res_ic[2]['to'])) { $txt .= "<hr />".number_format($res_ic[2]['to'], 2)."€"; $total_ca += $res_ic[2]['to']; }
					if(isset($res_ic[3]['to'])) { $txt .= "<hr />".number_format($res_ic[3]['to'], 2)."€"; $total_ca += $res_ic[3]['to']; }
					if(isset($res_ic[4]['to'])) { $txt .= "<hr />".number_format($res_ic[4]['to'], 2)."€"; $total_ca += $res_ic[4]['to']; }	 
					$txt .= " </td><td align='left' style='font-family: sans-serif; font-size: 14px; vertical-align: top; padding-bottom: 15px;'>
					";
					if(!isset($res_ic[0]['cashier_diff'])) $res_ic[0]['cashier_diff'] = 0;
					if(isset($res_ic[0])) $operand = $this->addOperand($res_ic[0]['cashier_diff']);
					if(isset($res_ic[1])) $operand1 = $this->addOperand($res_ic[1]['cashier_diff']);
					if(isset($res_ic[2])) $operand2 = $this->addOperand($res_ic[2]['cashier_diff']);
					if(isset($res_ic[3])) $operand3 = $this->addOperand($res_ic[3]['cashier_diff']);
					if(isset($res_ic[4])) $operand4 = $this->addOperand($res_ic[4]['cashier_diff']);			
					if(isset($res_ic[0])) $txt .= "$operand". number_format($res_ic[0]['cashier_diff'], 2)."€";
					if(isset($res_ic[1])) $txt .= "<hr />$operand1". number_format($res_ic[1]['cashier_diff'], 2)."€";
					if(isset($res_ic[2])) $txt .= "<hr />$operand2". number_format($res_ic[2]['cashier_diff'], 2)."€";
					if(isset($res_ic[3])) $txt .= "<hr />$operand3". number_format($res_ic[3]['cashier_diff'], 2)."€";
					if(isset($res_ic[4])) $txt .= "<hr />$operand4". number_format($res_ic[4]['cashier_diff'], 2)."€";
					$txt .= "<br />";
					if(!isset($res_ic[0]['cancel_ticket'])) $res_ic[0]['cancel_ticket'] = '';
					if(isset($res_ic[0])) $txt .= "(".$res_ic[0]['cancel_ticket'].")";
					if(isset($res_ic[1])) $txt .= "<hr />(".$res_ic[1]['cancel_ticket'].")";
					if(isset($res_ic[2])) $txt .= "<hr />(".$res_ic[2]['cancel_ticket'].")";
					if(isset($res_ic[3])) $txt .= "<hr />(".$res_ic[3]['cancel_ticket'].")";
					if(isset($res_ic[4])) $txt .= "<hr />(".$res_ic[4]['cancel_ticket'].")";
					$txt .= "<br />";
					if(!isset($res_ic[0]['comment_cashier'])) $res_ic[0]['comment_cashier'] = '';
					if(isset($res_ic[0])) $txt .= $res_ic[0]['comment_cashier'];
					if(isset($res_ic[1])) $txt .= "<hr />".$res_ic[1]['comment_cashier'];
					if(isset($res_ic[2])) $txt .= "<hr />".$res_ic[2]['comment_cashier'];
					if(isset($res_ic[3])) $txt .= "<hr />".$res_ic[3]['comment_cashier'];
					if(isset($res_ic[4])) $txt .= "<hr />".$res_ic[4]['comment_cashier'];
					$txt .= "
					</td>

					<td align='left' style='font-family: sans-serif; font-size: 14px; vertical-align: top; padding-bottom: 15px;'>
					";
					//checklist
					$this->db->where('DATE_FORMAT( checklist_records.date,  \'%Y-%m-%d\' ) = CURDATE()');
					$this->db->where('checklists.id_bu', $row->id);
					$this->db->like('checklists.name', 'CLOSING');
					$this->db->join('checklists', 'checklists.id = checklist_records.id_checklist');
					$query_cl = $this->db->get("checklist_records");
					$res_cl = $query_cl->result_array();
			
					$info_user_cl = '-';
					$info_user_cl1 = '-';
					$info_user_cl2 = '-';
					$info_user_cl3 = '-';
					$info_user_cl4 = '-';
					if(isset($res_cl[0]['user'])) $info_user_cl = $this->hmw->getUser($res_cl[0]['user'])->username;
					if(isset($res_cl[1]['user'])) $info_user_cl1 = $this->hmw->getUser($res_cl[1]['user'])->username;
					if(isset($res_cl[2]['user'])) $info_user_cl2 = $this->hmw->getUser($res_cl[2]['user'])->username;
					if(isset($res_cl[3]['user'])) $info_user_cl3 = $this->hmw->getUser($res_cl[3]['user'])->username;
					if(isset($res_cl[4]['user'])) $info_user_cl4 = $this->hmw->getUser($res_cl[4]['user'])->username;
					if(isset($res_cl[0])) $txt .=  $info_user_cl;
					if(isset($res_cl[1])) $txt .=  "<hr />".$info_user_cl1;
					if(isset($res_cl[2])) $txt .=  "<hr />".$info_user_cl2;
					if(isset($res_cl[3])) $txt .=  "<hr />".$info_user_cl3;
					if(isset($res_cl[4])) $txt .=  "<hr />".$info_user_cl4;
						
					$txt .= "<br />";
					if(isset($res_cl[0])) $txt .=  $res_cl[0]['date'];
					if(isset($res_cl[1])) $txt .=  "<hr>".$res_cl[1]['date'];
					if(isset($res_cl[2])) $txt .=  "<hr>".$res_cl[2]['date'];
					if(isset($res_cl[3])) $txt .=  "<hr>".$res_cl[3]['date'];
					if(isset($res_cl[4])) $txt .=  "<hr>".$res_cl[4]['date'];
					
					$txt .= "
						</td>
					</tr>
					";

				}
				
				$txt .= "
				</tbody>
				</table>
				<small>Total CA = $total_ca €</small>
				";	
				
				$final_txt = $this->mmail->templateEmail($txt);
				//echo $final_txt;
				$this->mmail->prepare('RMS CLOSE REPORT', $final_txt)->toList('close_reports')->send();

			}

			// cd /var/www/hank/rms/rms && php index.php webcashier cliAlertSafe 1

			public function cliAlertSafe($id_bu) {

				if(!is_cli()) exit();

				$currentAmount = $this->cashier->calc('safe_current_cash_amount', $id_bu);

				if ($currentAmount < 1) {
					$this->db->select('name');
					$this->db->where('id', $id_bu);
					$bu_name = $this->db->get('bus')->row_array()['name'];

					$subject = 'WARNING ' . $bu_name . ': Safe cash under 1 €';
					$msg     = 'Safe ' . $bu_name . ' cash amount is ' . $currentAmount . ' €';

					$this->mmail->prepare($subject, $msg)
						->toList('cashier_alerts', $id_bu)
							->send();
				} else {
					$this->db->select('cashier_alert_amount_safe');
					$this->db->from('bus');
					$this->db->where('id', $id_bu);
					$cashierAlertAmountSafe = $this->db->get()->row_array()['cashier_alert_amount_safe'];

					if ($currentAmount > $cashierAlertAmountSafe) {
						$this->db->select('name');
						$this->db->where('id', $id_bu);
						$bu_name = $this->db->get('bus')->row_array()['name'];

						$subject = 'WARNING ' . $bu_name . ': Safe cash is above '
							. $cashierAlertAmountSafe . ' €';
						$msg     = 'Safe ' . $bu_name . ' cash amount is ' . $currentAmount;

						$this->mmail->prepare($subject, $msg)
							->toList('cashier_alerts', $id_bu)
								->send();
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
				$config_pages['per_page'] = 15;
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
					$lastclose				 = $this->cashier->getLastClose($id_bu);
					
					$data['seqid'] 		  	 = $d['seqid'];
					$data['lastCloseId']	 = $lastclose['id'];
					$data['lastCloseDate']	 = $lastclose['date'];
						
					$data['close_waiting'] = false;
					if(($archive_date == $today_date OR $archive_date == $yesterday_date) AND empty($osid)) { 
						$data['closure_data'] = $d;
						$data['archive_file'] = $d['file'];
						$data['archive_date'] = $archive_date;
						$this->cashier->InsertTerminals($id_bu);
						$this->cashier->posInfo('updateUsers', $param_pos_info);
						$this->cashier->posInfo('updateProductCategory', $param_pos_info);
					} else {
						$force = $this->input->get('force');
						$data['archive_date'] = $archive_date;
						if(!empty($force)) { 
							$data['force'] = 1;
							$this->cashier->posInfo('updateUsers', $param_pos_info);
							$this->cashier->InsertTerminals($id_bu);
						} else {
							$data['close_waiting'] = true;						
						}
					}
				}
				
				$headers = $this->hmw->headerVars(0, "/webcashier/", "Cashier - POS");
				if($data['close_waiting']) {
					header("Refresh:7");
					$this->load->view('jq_header_pre', $headers['header_pre']);
					$this->load->view('jq_header_post', $headers['header_post']);
					$this->load->view('webcashier/close_waiting',$data);
					$this->load->view('webcashier/jq_footer_spe');
					$this->load->view('jq_footer');
				} else {
					$this->load->view('jq_header_pre', $headers['header_pre']);
					$this->load->view('jq_header_post', $headers['header_post']);
					$this->load->view('webcashier/movement',$data);
					$this->load->view('webcashier/jq_footer_spe');
					$this->load->view('jq_footer');					
				}
			}

			//to clean db after test, delete the last record from pos_movements 
			public function save()
			{
	
				$data = array();
				$this->hmw->keyLogin();
				$user						= $this->ion_auth->user()->row();
				$user_groups 				= $this->ion_auth->get_users_groups()->result();
				$data['username']			= $user->username;
				$data['user_groups']		= $user_groups[0];
				$data["keylogin"]			= $this->session->userdata('keylogin');
				$data['title']				= 'Cashier';
				$data['bu_name'] 			= $this->session->userdata('bu_name');
				$data['mov']				= $this->input->post('mov');
				$userpost 					= $this->input->post('user');
				$id_bu			 			= $this->session->userdata('bu_id');
				$param_pos_info 			= array();
				$param_pos_info['id_bu'] 	= $id_bu;
				$param_pos_info['archive'] 	= $this->input->post('archive');
		
				if(empty($userpost)) { 
					$userid = $user->id; 
				} else {
					$userid = $this->input->post('user');
				}
		
				$postmov = $this->input->post('mov');
				if(empty($postmov)) exit('Error, empty movement, please try again.');
		
				$this->db->trans_begin();
				$keys = "(movement, id_user, comment, prelevement_amount, safe_cash_amount, safe_tr_amount, id_bu";
		
		
				$values = "('" . $postmov . "', " . $userid . ", '" . addslashes($this->input->post('comment')) . "', '" . $this->input->post('prelevement') . "', '" 
					. $this->cashier->calc('safe_current_cash_amount', $id_bu) . "', '" . $this->cashier->calc('safe_current_tr_num', $id_bu) . "', '"
						. $id_bu . "'"; 

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
				$total_to = 0;
		
				if($this->input->post('mov')) { 
					foreach ($this->input->post() as $key => $val) {	
						$ex = @explode('_',$key);
						if($ex[0] == 'man' OR $ex[0] == 'pos') {
							$pay[$ex[1]][$ex[0]] = $val;
						}
					}
			
					if($this->input->post('mov') != 'safe_in' AND $this->input->post('mov') != 'safe_out') {
				
						$pay[1]['man'] = $this->cashier->clean_number($this->input->post('cash2'));
						$pay[2]['man'] = $this->cashier->clean_number($this->input->post('cb'));
					}

					foreach ($pay as $idp => $val2) {
						if(!isset($val2['man']) OR empty($val2['man']) ) $val2['man'] = 0;
						if(!isset($val2['pos']) OR empty($val2['pos']) ) $val2['pos'] = 0;
						$val2man = $this->cashier->clean_number($val2['man']);
						if($this->input->post('mov') == 'safe_out') $val2man = -1 * abs($val2man);
						$queryStringPp = "INSERT INTO `pos_payments` (id_payment, id_movement, amount_pos, amount_user) VALUES ('" . $idp . "', '" . $pmid . "', '" . $this->cashier->clean_number($val2['pos']) . "', '" . $val2man . "');";  
						$this->db->query($queryStringPp);
						$total_to += $this->cashier->clean_number($val2['pos']); 
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
					$prelevement = floatval($this->input->post('prelevement'));
					@$amount_pos  = $cashpad_amount+$pay_values[2]['pos']+$pay_values[3]['pos']+$pay_values[4]['pos']+$pay_values[5]['pos']+$pay_values[12]['pos']+$pay_values[13]['pos']+$pay_values[14]['pos']+$pay_values[11]['pos'];
					@$amount_user = $pay_values[1]['man']+$pay_values[2]['man']+$pay_values[3]['man']+$pay_values[4]['man']+$pay_values[13]['man']+$pay_values[5]['pos']+$pay_values[12]['pos']+$pay_values[14]['pos']+$pay_values[11]['pos']+$prelevement; 
			
					$diff 				= $amount_user - $amount_pos ;
					$test_diff 			= false;
					$test_diff_control 	= false;
			
					if($diff <= $alert_amount['cashier_alert_amount_close_min']) { $test_diff = true;  $test_diff_control = true; }
					if($diff >= $alert_amount['cashier_alert_amount_close_max']) { $test_diff = false; $test_diff_control = true; }
				
					if (!$this->input->post('blc') AND $test_diff) {
						$this->db->trans_rollback();
						$form_values = $this->input->post();
					
						$form_values['cashpad_amount'] = $cashpad_amount;
						$form_values['diff'] = $diff;
						$this->session->set_flashdata('form_values', $form_values);
						$this->session->set_flashdata('pay_values', $pay_values);

						$varslog = "Closing fail, rollback, redirecting to movement - test_diff = $test_diff - test_diff_control = $test_diff_control - diff = $diff - alert_amount['cashier_alert_amount_close_max'] = $alert_amount[cashier_alert_amount_close_min] - alert_amount['cashier_alert_amount_close_min'] = $alert_amount[cashier_alert_amount_close_min]";
						error_log($varslog);
					
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
						$seterror_status = false; 

						$server_name = $this->hmw->getParam('server_name');
						$this->db->select('name');
						$this->db->where('id', $id_bu);
						$bu_name = $this->db->get('bus')->row_array()['name'];
						$operand = $this->addOperand($diff);

						// send email
						$subject = 'RMS CASHIER WARNING ' . $bu_name . ': Erreur de caisse';
						$link = 'http://' . $server_name . '/webcashier/report/#' . $pmid;
						$msg = 'BU: ' . $bu_name . ' | ID: ' . $pmid . '<br />Difference de '
							. $operand . number_format($diff , 2) . '€ <br /><a href="'
								. $link . '">' . $link . "</a>";

						$this->mmail->prepare($subject, $msg)
							->toList('cachier_alerts', $id_bu)
								->send();
					}

					$this->db->trans_commit();

					if($test_diff_control) {
						$this->db->set('status', 'error');
						$seterror_status = 'DIFF';
						$this->db->where('id', $pmid);
						$this->db->update('pos_movements');
						error_log('test_diff_control : '.$test_diff_control);
					}
				
					$this->closing($this->input->post('archive'), $pmid);
				
					//set error status if cancelled receipts
					$cancel_ticket = false;
					$cancelledReceipts = $this->cashier->getArchivedCancelledReceipts($id_bu, $this->input->post('archive'));
					if (count($cancelledReceipts) > 0) {
						$this->db->set('status', 'error');
						$seterror_status = 'RCPT';
						$this->db->where('id', $pmid);
						$this->db->update('pos_movements');
						$cancel_ticket = count($cancelledReceipts);
						error_log('set status error cancel_ticket : '.$cancel_ticket);	
					}
			
					//insert into infos_close
					$this->db->set('cashier_diff', $diff);
					$this->db->set('bu_id', $id_bu);
					$this->db->set('id_pos_movements', $pmid);
					$this->db->set('comment_cashier', addslashes($this->input->post('comment')));
					$this->db->set('id_user_cashier', $userid);
					$this->db->set('to', $total_to);
					if($seterror_status) {	
						//$this->db->set('status', 'error');
						error_log('set status error : '.$seterror_status);
					}
					if($cancel_ticket)		$this->db->set('cancel_ticket', $cancel_ticket);
					$this->db->insert('infos_close') or die('ERROR '.$this->db->_error_message().error_log('ERROR '.$this->db->_error_message()));
			
		
					$varslog = "Closing processed - test_diff = $test_diff - test_diff_control = $test_diff_control - diff = $diff - alert_amount['cashier_alert_amount_close_max'] = $alert_amount[cashier_alert_amount_close_min] - alert_amount['cashier_alert_amount_close_min'] = $alert_amount[cashier_alert_amount_close_min] - seterror_status = $seterror_status";
					error_log($varslog);				
				
				} else {
					$this->db->trans_commit();
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

			// cd /var/www/hank/rms/rms && php index.php webcashier cliCheckCashFund 1
			public function cliCheckCashFund($id_bu)
			{
				if (!is_cli()) return 1;

				$cashFund = $this->cashier->getCashFund($id_bu);

				if ($cashFund > 0)
					return 0;

				$bu_name = $this->hmw->getBuInfo(intval($id_bu))->name;
				$users   = $this->hmw->getBuUsers($id_bu, [ 1, 3, 4, 6 ]);

				$email = [
					'subject' => 'ALERTE!! ' . $bu_name . ' - pas de fond de caisse!',
					'msg'     => 'ALERTE!! ' . $bu_name . ' - pas de fond de caisse! Insérer le fond de caisse.'
				];

				$this->hmw->sendNotif($email['msg'], $id_bu);

				foreach ($users as $user)
				{
					$email['to'] = $user->email;
					$this->mmail->sendEmail($email);
				}

				//add notif
				$this->hmw->sendNotif($email['msg'], $id_bu);
			}
		}
		?>
