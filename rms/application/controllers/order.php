<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Order extends CI_Controller {

	/**
	* Index Page for this controller.
	*
	* Maps to the following URL
	* 		http://example.com/index.php/welcome
	*	- or -  
	* 		http://example.com/index.php/welcome/index
	*	- or -
	* Since this controller is set as the default controller in 
	* config/routes.php, it's displayed at http://example.com/
	*
	* So any other public methods not prefixed with an underscore will
	* map to /index.php/welcome/<method_name>
	* @see http://codeigniter.com/user_guide/general/urls.html
	*/

	public function __construct()
	{

		parent::__construct();
		$this->load->library('hmw');
		$this->load->library('ion_auth');
		$this->load->model('order_model');
		$this->load->library("pagination");
		$this->load->helper(array('form', 'url'));
		$this->load->database();

	}

	public function index()
	{
		$this->hmw->changeBu();// GENERIC changement de Bu

		$this->hmw->keyLogin();		
		$this->load->library('product');

		$id_bu			=  $this->session->all_userdata()['bu_id'];

		$user_groups	= $this->ion_auth->get_users_groups()->result();
		$freq			= $this->freq();
		$suppliers 		= $this->product->getSuppliers(true, null, $id_bu);

		$data = array(
			'keylogin'		=> $this->session->userdata('keylogin'),
			'user_groups'	=> $user_groups[0],
			'suppliers'    	=> $suppliers,
			'freq'			=> $freq);

		$data['bu_name']  =  $this->session->all_userdata()['bu_name'];
		$data['username'] = $this->session->all_userdata()['identity'];

	 	$headers = $this->hmw->headerVars(1, "/order/", "Order");
		$this->load->view('jq_header_pre', $headers['header_pre']);
		$this->load->view('jq_header_post', $headers['header_post']);
		$this->load->view('order/index',$data);
		$this->load->view('jq_footer');

		
	}

	public function autoCompProducts(){

		$id_bu =  $this->session->all_userdata()['bu_id'];

		if (isset($_GET['q'])){
			$q = strtolower($_GET['q']);
			$row_set = array();
			$this->db->select('p.name AS name, p.id AS id, s.name AS sname, ps.qtty AS stock, p.price AS price, p.packaging AS packaging, puprc.name AS unitname')
			 	->from('products AS p')
				->join('suppliers as s', 'p.id_supplier = s.id')
				->join('products_unit as puprc', 'p.id_unit = puprc.id')
				->join('products_stock as ps', 'p.id = ps.id_product', 'left')
			 	->like('p.name', "$q", 'both')
			 	->where('p.deleted', 0)
			 	->where('p.active', 1)
			 	->where('ps.id_bu', $id_bu)
			 	->order_by('p.name asc')->limit(100);
			 	$query = $this->db->get() or die($this->mysqli->error);
		if($query->num_rows() > 0){
			foreach ($query->result_array() as $row){
				$row_set[] = htmlentities(stripslashes($row['name']))."|||".$row['id']."|||".$row['sname']."|||".$row['stock']."|||".$row['price']."|||".$row['unitname']."|||".$row['packaging']; 
			}
		}
		echo $_GET['callback']."(".json_encode($row_set).");";	
	}
}

//cd /var/www/hank/rms/rms && php index.php order cliUpdateSales 1
public function cliUpdateSales($id_bu) {

	if($this->input->is_cli_request()) {

		$param = array();
		$param['id_bu'] = $id_bu;
		if($this->input->is_cli_request()) {
			$this->load->library("cashier");
			$this->cashier->posInfo('updateTurnover', $param);	
			$this->cashier->posInfo('salesUpdate', $param);	
			$this->cashier->updateStock($id_bu);
			@unlink ( '/tmp/cashlock'.$id_bu );
		} else { 
			return false; 
		}
	}
}

//cd /var/www/hank/rms/rms && php index.php order cliCheckPosClosing 1
public function cliCheckPosClosing($id_bu) {

	if($this->input->is_cli_request()) {

		$param = array();
		$param['id_bu'] = $id_bu;
		if($this->input->is_cli_request()) {
			$this->load->library("cashier");

			$today_day = @date('d');
			$this->db->where('movement', 'close'); 
			$this->db->order_by("id", "desc"); 
			$this->db->limit(1);
			
			$query = $this->db->get('pos_movements') or die($this->mysqli->error);
			$res = $query->result_object();
			
			$timestamp = strtotime($res[0]->date);
			$archive_day = date('d', $timestamp);
			//echo "TODAY $today_day - DB $archive_day";
			
			if($archive_day != $today_day) {
			
				$info = $this->hmw->getBuInfo($id_bu);
				$this->load->library('mmail');
				
				$msg = "WARINING! ".$info->name." CASHPAD NOT CLOSED!";
			
				//get manager2 + admin email of this BU
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
			
		} else { 
			return false; 
		}
	}
}

public function previousOrders()
{
	$this->hmw->keyLogin();
	$id_bu =  $this->session->all_userdata()['bu_id'];

	$config = array();
	$config["base_url"] = base_url() . "order/previousOrders";
	$config["total_rows"] = $this->order_model->record_count();
	$config["per_page"] = 10;
	$config["uri_segment"] = 3;
	$choice = $config["total_rows"] / $config["per_page"];
	$config["num_links"] = round($choice);

	$this->pagination->initialize($config);

	$page = ($this->uri->segment(3)) ? $this->uri->segment(3) : 0;

	$data = array(
		'results'	=> $this->order_model->get_list($config["per_page"], $page),
		'links'		=> $this->pagination->create_links()
		);
	$data['bu_name'] =  $this->session->all_userdata()['bu_name'];
	$data['username'] = $this->session->all_userdata()['identity'];

	$headers = $this->hmw->headerVars(0, "/order/", "Orders");
	$this->load->view('jq_header_pre', $headers['header_pre']);
	$this->load->view('jq_header_post', $headers['header_post']);
	$this->load->view('order/order_prev',$data);
	$this->load->view('jq_footer');
}

public function viewProducts($id_freq = null, $load = null, $supplier_id = null)
{		

	$this->hmw->keyLogin();
	$this->load->library('product');
	$id_bu =  $this->session->all_userdata()['bu_id'];

	$order_prev = null;
	$freq = $this->freq();

	if($load > 0) {
		$this->db->select('r.user, r.id as rec_id, r.data, r.date')->from('orders as r')->where('r.idorder', $load)->where('id_bu', $id_bu);
		$order_rec_res	= $this->db->get() or die($this->mysqli->error);
		$order_rec		= $order_rec_res->row();
		$order_prev		= unserialize($order_rec->data);
	}

	$products	= $this->product->getProducts(null, $supplier_id, null, null, $id_bu, true);
	$stock 		= $this->product->getStock($id_bu);
	$attributs	= $this->product->getAttributs();

	$data = array(
		'products'			=> $products,
		'order_name'		=> $freq[$id_freq]['name'],
		'freq_id'			=> $id_freq,
		'order_prev'		=> $order_prev,
		'stock'				=> $stock,
		'attributs'			=> $attributs,
		'load' 				=> $load
		);
	if($load <= 0) { 
		$title	= "Order ".strtoupper($data['order_name']);
	}else{
		$title = "Order n°".$load;
	}
	$data['bu_name'] =  $this->session->all_userdata()['bu_name'];
	$data['username'] = $this->session->all_userdata()['identity'];
	
	$headers = $this->hmw->headerVars(0, "/order/", $title);
	$this->load->view('jq_header_pre', $headers['header_pre']);
	$this->load->view('jq_header_post', $headers['header_post']);
	$this->load->view('order/order_products',$data);
	$this->load->view('jq_footer');

	//$this->load->view('order/order_products',$data);

}

public function prepareOrder() {

	$this->hmw->keyLogin();
	$this->load->library('product');
	$this->load->library('ion_auth');
	$user = $this->ion_auth->user()->row();
	$stock_update = false;
	$post = $this->input->post();
	$id_bu =  $this->session->all_userdata()['bu_id'];

	//maj stock
	$maj = array();
	foreach ($post as $key => $var) {
		$ex = @explode('-',$key);
		if($ex[0] == 'stock') { 
			$q = "INSERT INTO products_stock (qtty, id_product, last_update_id_user, last_update_user, id_bu) VALUES($var, $ex[1], $user->id, NOW(), $id_bu) ON DUPLICATE KEY UPDATE qtty=qtty+$var, last_update_id_user=$user->id, last_update_user=NOW()";
			$this->db->query($q) or die($this->mysqli->error);
			$pdt_info = $this->product->getProducts($ex[1], null, null, null, $id_bu);
			$maj[$ex[1]]['stock'] = $var;
			$maj[$ex[1]]['name'] = $pdt_info[$ex[1]]['name'];
			if(!empty($var)) $stock_update = true;
		}
	}

	$suppliers = $this->product->getSuppliers(null, null, $id_bu);

	$data = array(
		'order' => $this->groupOrder($post, $id_bu), 
		'suppliers' => $suppliers, 
		'stock_update' => $stock_update, 
		'maj' => $maj);

	$data['bu_name'] =  $this->session->all_userdata()['bu_name'];
	$data['username'] = $this->session->all_userdata()['identity'];

	$headers = $this->hmw->headerVars(0, "/order/", "Order Prepare");
	$this->load->view('jq_header_pre', $headers['header_pre']);
	$this->load->view('jq_header_post', $headers['header_post']);
	$this->load->view('order/order_prepare',$data);
	$this->load->view('jq_footer');
}

public function confirm($key = null) {

	$this->load->library('mmail');
	$this->load->library('hmw');

	$this->db->from('orders_confirm')->where('key', $key)->limit(1);
	$res = $this->db->get() or die($this->mysqli->error);
	$ret = $res->result_array();
	$ip  = $_SERVER['REMOTE_ADDR'];

	if(isset($ret[0]['id'])) {		

		$this->db->from('orders as o')->join('suppliers as s','s.id = o.supplier_id','left')->where('idorder', $ret[0]['idorder'])->limit(1);
		$res_sup = $this->db->get() or die($this->mysqli->error);
		$ret_sup = $res_sup->result_array();

		$fcomment = $this->input->post('scomment');
		$order_email = $this->hmw->getEmail('order', $ret_sup[0]['id_bu']);


		if(!empty($fcomment)) {
			$scomment = $fcomment;
		} else {
			$scomment = stripslashes($ret[0]['comment']);
		}

		$this->db->set('date_confirmed', "NOW()", FALSE);
	 	$this->db->set('status', "confirmed");
	 	$this->db->set('comment', mysql_real_escape_string(addslashes($scomment)));
	 	$this->db->set('IP', $ip);
	 	$this->db->where('key', $key);
	 	$this->db->update('orders_confirm')  or die($this->mysqli->error);
		$data = array('status' => 'OK', 'key' => $key, 'scomment' => $scomment);

		if($ret[0]['status'] != 'confirmed' OR ($ret[0]['status'] == 'confirmed' AND !empty($scomment) ) ) {
			$email['from']		= $order_email;
			$email['from_name']	= 'HANK';
			$email['to']		= $order_email;
			$email['subject'] 	= '';
			if(!empty($scomment)) { $email['subject'] .= "ALERT COMMENT! "; }
			$email['subject'] 	.= "Confirmation de commande de ".$ret_sup[0]['name'].", order: ".$ret[0]['idorder'];
			$email['replyto'] 	= $order_email;
			$email['msg'] 		= "Commande : ".$ret[0]['idorder']." validée par fournisseur ".$ret_sup[0]['name'].".";
			if(!empty($scomment)) { $email['msg'] .= "\n\nCOMMENTAIRE: ".stripslashes($scomment)."\n"; }
			$email['msg'] 		.= "\n\nHave A Nice Karma,\n-- \nHANK\n";
			$this->mmail->sendEmail($email);
		}
	} else {
		$data = array('status' => 'NOK');	
	}

	$this->load->view('order/confirm',$data);
}

public function downloadOrder($id) {

	$this->load->helper('download');
	$date_y = '20'.$id[0].$id[1]; 
	$date_m = $id[2].$id[3];
	$data = file_get_contents('orders/'.$date_y.'/'.$date_m.'/'.$id.'.pdf');
	$name = $id.'.pdf';
	force_download($name, $data);

}

public function sendOrder() {

	$this->hmw->keyLogin();

	$this->load->helper('download');
	$this->load->library('mmail');
	$this->load->helper('file');
	$id_bu =  $this->session->all_userdata()['bu_id'];

	$post 	= $this->input->post();
	$sup 	= array();
	$disp 	= array(); 
	$inc 	= 0;

	$server_name = $this->hmw->getParam('server_name');
	$order_email = $this->hmw->getEmail('order', $id_bu);

	foreach ($post as $key => $var) {
		$ex = @explode('_',$key);
		if(@$ex[1] == 'ID') $sup[$ex[0]]['id'] = $var;
		if(@$ex[1] == 'EMAIL') $sup[$ex[0]]['email'] = $var;
		if(@$ex[1] == 'EMAILCC') $sup[$ex[0]]['emailcc'] = $var;
		if(@$ex[1] == 'IDORDER') $sup[$ex[0]]['idorder'] = $var;
		if(@$ex[1] == 'SUP') $sup[$ex[0]]['name'] = $var;
		if(@$ex[1] == 'SUPID') $sup[$ex[0]]['supid'] = $var;
		if(@$ex[1] == 'USER') $sup[$ex[0]]['user'] = $var;
		if(@$ex[1] == 'USERID') $sup[$ex[0]]['userid'] = $var;
		if(@$ex[1] == 'COMT') $sup[$ex[0]]['comt'] = $var;
		$inc++;
	}

	foreach ($sup as $key => $var) {

		$email 	= array();
		$cc 	= '';
		$date_y = '20'.$var['id'][0].$var['id'][1]; 
		$date_m = $var['id'][2].$var['id'][3];
		$key 	= md5(microtime().rand());
		$link 	= 'http://'.$server_name.'/order/confirm/'.$key;
		$user 	= $this->hmw->getUser($var['userid']);

		if(!empty($var['email'])) {
			$cc 				= $order_email;
			if(!empty($var['emailcc'])) $cc .= ','.$var['emailcc'];

			$email['from']		= $order_email;
			$email['from_name']	= 'HANK';
			$email['cc'] 		= $cc;
			$email['to']		= $var['email'];
			$email['subject'] 	= "Nouvelle commande ".$var['id'];
			$email['attach'] 	= 'orders/'.$date_y.'/'.$date_m.'/'.$var['id'].'.pdf';
			$email['replyto'] 	= $order_email;
			$email['msg'] 		= "Bonjour ".$var['name']."!\n\nVoici une nouvelle commande en PJ.\n\n";
			if(!empty($var['comt'])) $email['msg'] .= $var['comt']."\n\n"; 
			$email['msg'] 		.= "Merci de bien vouloir valider la prise en compte de cette commande en cliquant sur ce lien : $link";
			$email['msg'] 		.= "\n\nHave A Nice Karma,\n-- \nHANK - ".$var['user']."\nEmail : $order_email \n Tel : $user->phone";

			$this->mmail->sendEmail($email);
			$this->db->set('status', 'sent')->set('date', "NOW()", FALSE);
			$this->db->where('idorder', $var['idorder'])->order_by('date desc')->limit(1);
			$this->db->update('orders');

			$req_conf = "INSERT INTO orders_confirm SET `date_sent` = NOW(), `key` = '$key', `idorder` = ".$var['idorder'].", `status` = 'sent' ON DUPLICATE KEY UPDATE `date_sent` = NOW(), `key` = '$key'";
			$this->db->query($req_conf);

			$disp[] = $var['name'];
		}

	}

	$data = array('disp' => $disp);
	$data['bu_name'] =  $this->session->all_userdata()['bu_name'];
	$data['username'] = $this->session->all_userdata()['identity'];
	$headers = $this->hmw->headerVars(0, "/order/", "Order Sent");
	$this->load->view('jq_header_pre', $headers['header_pre']);
	$this->load->view('jq_header_post', $headers['header_post']);
	$this->load->view('order/order_sent', $data);
	$this->load->view('jq_footer');

}

public function confirmOrder() {

	$this->hmw->keyLogin();
	$this->load->library('ion_auth');
	$user = $this->ion_auth->user()->row();
	$this->load->library('product');
	$this->load->library('hmw');
	$id_bu =  $this->session->all_userdata()['bu_id'];

	$post = $this->input->post();
	$sup = array();
	$inc = 0;

	//group suppliers
	foreach ($post as $key => $var) {
		$ex = @explode('_',$key);
		if(@$ex[1] == 'SUP') {
			$sup[] = $var;
			$inc++;
		}

	}

	//group products
	$pdt = array();
	foreach ($sup as $key2 => $var2) {
		foreach ($post as $key3 => $var3) {
			$ex2 = @explode('_',$key3);
			if(@$ex2[0] == $key2)  {
				if(@$ex2[2] == 'NAME') $pdt[$key2][$ex2[3]]['name'] 		= $var3;
				if(@$ex2[2] == 'QTTY') $pdt[$key2][$ex2[3]]['qtty'] 		= $var3;
				if(@$ex2[2] == 'UNIT') $pdt[$key2][$ex2[3]]['unitname']		= $var3;
				if(@$ex2[2] == 'PACK') $pdt[$key2][$ex2[3]]['packaging'] 	= $var3;
				if(@$ex2[2] == 'ATTR') $pdt[$key2][$ex2[3]]['attribut'] 	= $var3;
				if(@$ex2[2] == 'CODEF') $pdt[$key2][$ex2[3]]['codef']		= $var3;
				if(@$ex2[2] == 'PRIC') $pdt[$key2][$ex2[3]]['pric']			= $var3;
				if(@$ex2[2] == 'ATTR') {
					$pdt[$key2][$ex2[3]]['attribut'] 	= $var3;
					if($var3 > 0) $pdt[$key2][$ex2[3]]['attributname'] = $this->product->getAttributName($var3);
				}

			}
		}
	}

	$this->load->helper(array('dompdf', 'file'));
	$order = array();
	$getBuInfo = $this->hmw->getBuInfo($id_bu);

	for( $i = 0; $i < $inc; ++$i )
	{
		$data 				= array();
		$html				= '';
		$pdf				= '';
		$info 				= array();
		$idorder 			= $post[$i.'_IDORDER'];
		$id					= $idorder.'_'.$post[$i.'_SUP'];
		$user 				= $this->hmw->getUser($user->id);
		$supinfo			= $this->product->getSuppliers(null, $post[$i.'_SUPID'], $id_bu);

		$info['date']		= date('d/m/Y H:i');		
		$info['id']			= $id;
		$info['idorder']	= $idorder;
		$info['sup_email'] 	= $post[$i.'_EMAIL'];
		$info['cc_email'] 	= $post[$i.'_CCEMAIL'];
		$info['comt'] 		= $post[$i.'_COMT'];
		$info['sup_name'] 	= $post[$i.'_SUP'];
		$info['sup_id'] 	= $post[$i.'_SUPID'];
		$info['dlv_info'] 	= $post[$i.'_DLV_INFO'];
		$info['dlv_comt'] 	= $post[$i.'_DLV_COMT'];
		$info['franco'] 	= $post[$i.'_FRANCO'];
		$info['totalprice'] = $post[$i.'_TOTALPRICE'];	
		$info['userid'] 	= $user->id;	
		$info['user'] 		= $user->username;
		$info['user_tel'] 	= $user->phone;			
		$info['sup_tel'] 	= $supinfo[$post[$i.'_SUPID']]['contact_order_tel'];

		$info['company_info']	= $getBuInfo->delivery_header;
		$info['delivery_info']	= $getBuInfo->delivery_info;
		$info['email_order']	= $getBuInfo->email_order;

		$date_y = date('Y');
		$date_m	= date('m');

		if (!is_dir('orders/'.$date_y)) {
			mkdir('./orders/' . $date_y, 0777, TRUE);

		}

		if (!is_dir('orders/'.$date_y.'/'.$date_m)) {
			mkdir('./orders/'.$date_y.'/'.$date_m, 0777, TRUE);

		}

		$data = array('info' => $info, 'products' => $pdt[$i]);
		$html = $this->load->view('order/bdc', $data, true);
		$pdf = pdf_create($html, '', false);
		write_file('orders/'.$date_y.'/'.$date_m.'/'.$id.'.pdf', $pdf);
		$order[] = array('id' => $id, 'idorder' => $idorder, 'comt' => $info['comt'], 'user' => $user->username, 'userid' => $user->id, 'sup_name' => $info['sup_name'], 'sup_id' => $info['sup_id'], 'sup_email' => $info['sup_email'], 'cc_email' => $info['cc_email']);
	}

	$data2 = array('order' => $order);

	$data2['bu_name'] =  $this->session->all_userdata()['bu_name'];
	$data2['username'] = $this->session->all_userdata()['identity'];

	$headers = $this->hmw->headerVars(0, "/order/", "Order Confirm");
	$this->load->view('jq_header_pre', $headers['header_pre']);
	$this->load->view('jq_header_post', $headers['header_post']);
	$this->load->view('order/order_confirm', $data2);
	$this->load->view('jq_footer');
}

private function freq()
{
	$freq = array(
		0 => array ( 'name' => 'previous', 'id' => 0),
		1 => array ( 'name' => 'high', 'id' => 1),
		2 => array ( 'name' => 'medium', 'id' => 2),
		3 => array ( 'name' => 'low', 'id' => 3),
		1000 => array ( 'name' => 'all', 'id' => 1000)
		);

	return $freq;
}


private function groupOrder($data, $id_bu) 
{
	$this->load->library('product');
	$products = $this->product->getProducts(null, null, null, null, $id_bu);
	$this->load->library('ion_auth');
	$user = $this->ion_auth->user()->row();
	$ar = array();

	//fill array with suppliers
	foreach ($data as $key => $var) {
		if(is_numeric($key)) {
			$$products[$key]['supplier_name'] = array($key => $var);
			$qtty = $this->clean_number($var);
			if(!empty($var) AND !is_numeric($qtty)) exit('Quantity has to be numeric, invalid: '.$var);

			$complet[] = array(
				'supplier' => $products[$key]['supplier_name'], 
				'supplier_id' => $products[$key]['supplier_id'], 
				'id' => $key, 
				'qtty' => $qtty, 
				'name' => $products[$key]['name'], 
				'unitname' => $products[$key]['unit_name'],
				'packaging' => $products[$key]['packaging'],
				'codef' => $products[$key]['supplier_reference'],
				'price' => $products[$key]['price'],
				'attribut' => $data['attribut-'.$key]
				);				
		}
	}

	//group suppliers 
	$sups = array();
	foreach ($complet as $h) {
		if($h['qtty'] > 0) $sups[] = $h['supplier_id'];
	}
	$uniqueSups = array_unique($sups);

	//group orders by suppliers
	$total = array();	
	foreach ($uniqueSups as $supplier_id) {
		$insert = '';
		$idorder = date('ymd').rand(1000, 9000);
		foreach ($complet as $key2) {
			if($key2['supplier_id'] == $supplier_id) {
				if($key2['qtty'] > 0) {
					$insert = array(
						'id' => $key2['id'], 
						'qtty' => $key2['qtty'], 
						'name' => $key2['name'],
						'packaging' => $key2['packaging'], 
						'unitname' => $key2['unitname'], 
						'codef' => $key2['codef'], 
						'idorder' => $idorder,
						'supplier' => $key2['supplier'],
						'supid' => $key2['supplier_id'],
						'price' => $key2['price'],
						'attribut' => $key2['attribut'],
						'subtotalprice' => $key2['price']*$key2['qtty'] 
						);
					$total[$supplier_id][] = $insert;
				}
			}
		}
		//serialize and insert into db
		$srl = serialize($total[$supplier_id]);
		$this->db->set('data', $srl);
		$this->db->set('idorder', $idorder);
		$this->db->set('supplier_id', $supplier_id);
		$this->db->set('user', $user->id);
		$this->db->set('id_bu', $id_bu);
		$this->db->insert('orders');	
	}
	return $total;
}
	function getprevorder(){
		$id_bu =  $this->session->all_userdata()['bu_id'];		
		$data = $this->input->post();
		$ok=0;
		$this->db->select('r.user, u.first_name as first_name, u.last_name as last_name, r.id as lid, r.idorder, r.id, r.date,  r.supplier_id, r.status, c.status as confirm, s.name as supplier_name');
		$this->db->from('orders as r');
		$this->db->join('users as u', 'r.user = u.id');
		$this->db->join('suppliers as s','s.id = r.supplier_id','left');
		$this->db->join('orders_confirm as c','r.idorder = c.idorder','left');
		$this->db->where('r.id_bu', $id_bu);

		if($data['supplier']!=''){
			$ok=1;
			$this->db->where('s.name',		$data['supplier']);
		}
		if($data['user']!=''){
			$ok=1;
			$this->db->where('u.username',	$data['user']);
		}
		if($data['idorder']!=''){
			$ok=1;
			$this->db->where('r.idorder',	$data['idorder']);
		}
		if($data['status']!=''){
			$ok=1;
			$this->db->where('r.status',	$data['status']);
		}
		if($data['sdate']!=''){
			$ok=1;
			$this->db->where('r.date >=',	$data['sdate']);
		}
		if($data['edate']!=''){
			$ok=1;
			$this->db->where('r.date <=',	$data['edate']);
		}

		$this->db->order_by('r.date desc')->limit(25);
		$rec_res = $this->db->get() or die($this->mysqli->error);
		$rec = $rec_res->result_array();

		$data = array(
			'order'	=>	$rec,
			'bu_name'	=> $this->session->all_userdata()['bu_name'],
			'username'	=> $this->session->all_userdata()['identity'],
			'valided'	=> $ok
			);
		$headers = $this->hmw->headerVars(0, "/order/previousOrders", "Order prev : search results");
		$this->load->view('jq_header_pre', $headers['header_pre']);
		$this->load->view('jq_header_post', $headers['header_post']);
		$this->load->view('order/search_results', $data);
		$this->load->view('jq_footer');

	}

private function clean_number($num) {
	$t1 = str_replace ( ',' , '.' , $num);
	$t2 = trim($t1);
	//$t3 = preg_replace("/[^0-9,.]/", "", $t2);
	return $t2;
}
}
?>