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
		$this->load->database();

	}

	public function index()
	{

		$this->hmw->keyLogin();		
		$this->load->library('product');
				
		$user_groups	= $this->ion_auth->get_users_groups()->result();
		$freq			= $this->freq();
		$suppliers 		= $this->product->getSuppliers(true);

		$data = array(
			'keylogin'	=> $this->session->userdata('keylogin'),
			'user_groups'	=> $user_groups[0],
			'suppliers'    => $suppliers,
			'freq'			=> $freq);
	
		$this->load->view('order/index',$data);
	}

  	public function autoCompProducts(){
	
    	if (isset($_GET['q'])){
      		$q = strtolower($_GET['q']);

			$row_set = array();
			$query = $this->db->query("SELECT 
				p.name AS name, p.id AS id
				FROM products AS p 
			WHERE p.name LIKE LOWER('%$q%')
				AND p.deleted=0 ORDER BY p.name ASC LIMIT 100") or die($this->mysqli->error);
			if($query->num_rows() > 0){
				foreach ($query->result_array() as $row){
			    	$row_set[] = htmlentities(stripslashes($row['name']))."||".$row['id']; 
				}
		    }
		    echo $_GET['callback']."(".json_encode($row_set).");";	
		}
  	}

	public function cliUpdateSales() {
		if($this->input->is_cli_request()) {
					
			$this->load->library("cashier");
	
			$this->cashier->posInfo('salesUpdate');		
			$this->cashier->updateStock();	
		} else { 
			return false; 
		}
	}

	public function previousOrders()
	{		

		$this->hmw->keyLogin();

		$rec_req = "SELECT r.user, u.first_name AS first_name, u.last_name AS last_name, r.id AS lid, r.idorder, r.id, r.date,  r.supplier_id, r.status, c.status AS confirm, s.name AS supplier_name
			FROM orders AS r 
			JOIN users AS u ON r.user = u.id 
			LEFT JOIN suppliers AS s ON s.id = r.supplier_id
			LEFT JOIN orders_confirm AS c ON r.idorder = c.idorder
			ORDER BY r.date DESC LIMIT 300";
		$rec_res = $this->db->query($rec_req) or die($this->mysqli->error);
		$rec = $rec_res->result_array();

		$data = array(
			'order'	=> $rec,
			);

		$this->load->view('order/order_prev',$data);
	}

	public function viewProducts($id_freq = null, $load = null, $supplier_id = null)
	{		

		$this->hmw->keyLogin();
		$this->load->library('product');

		$order_prev = null;
		$freq = $this->freq();

		if($load > 0) {
			$order_rec_req 	= "SELECT r.user, r.id AS rec_id, r.data, r.date FROM orders AS r WHERE r.idorder = $load";
			$order_rec_res	= $this->db->query($order_rec_req) or die($this->mysqli->error);
			$order_rec		= $order_rec_res->row();
			$order_prev		= unserialize($order_rec->data);
		}

		$users_req = "SELECT `id`, `first_name`, `last_name`  FROM users WHERE active=1 ORDER BY `first_name` ASC";
		$users_res = $this->db->query($users_req) or die($this->mysqli->error);
		$users = $users_res->result_array();

		$products	= $this->product->getProducts(null, $supplier_id);
		$stock 		= $this->product->getStock();
		$attributs	= $this->product->getAttributs();

		$data = array(
			'products'			=> $products,
			'order_name'		=> $freq[$id_freq]['name'],
			'freq_id'			=> $id_freq,
			'order_prev'		=> $order_prev,
			'stock'				=> $stock,
			'attributs'			=> $attributs,
			'load' 				=> $load,
			'users'				=> $users
			);

		$this->load->view('order/order_products',$data);

	}

	public function prepareOrder() {

		$this->hmw->keyLogin();
		$this->load->library('product');
		$this->load->library('ion_auth');
		$user = $this->ion_auth->user()->row();
		$stock_update = false;
		$post = $this->input->post();

		//maj stock
		$maj = array();
		foreach ($post as $key => $var) {
			$ex = @explode('-',$key);
			if($ex[0] == 'stock') { 
				$q = "INSERT INTO products_stock (qtty, id_product, last_update_id_user, last_update_user) VALUES($var, $ex[1], $user->id, NOW()) ON DUPLICATE KEY UPDATE qtty=qtty+$var, last_update_id_user=$user->id, last_update_user=NOW()";
				$this->db->query($q) or die($this->mysqli->error);
				$pdt_info = $this->product->getProducts($ex[1]);
				$maj[$ex[1]]['stock'] = $var;
				$maj[$ex[1]]['name'] = $pdt_info[$ex[1]]['name'];
				if(!empty($var)) $stock_update = true;
			}
		}

		$suppliers = $this->product->getSuppliers();
		$data = array(
			'order' => $this->groupOrder($post), 
			'suppliers' => $suppliers, 
			'stock_update' => $stock_update, 
			'maj' => $maj);
		$this->load->view('order/order_prepare',$data);
	}

	public function confirm($key = null) {

		$this->load->library('mmail');

		$get = "SELECT * FROM orders_confirm WHERE `key` = '".$key."' LIMIT 1";
		$res = $this->db->query($get) or die($this->mysqli->error);
		$ret = $res->result_array();
		$ip  = $_SERVER['REMOTE_ADDR'];

		if(isset($ret[0]['id'])) {		

			$get_sup = "SELECT * FROM orders AS o LEFT JOIN suppliers AS s ON s.`id` = o.`supplier_id` WHERE `idorder` = '".$ret[0]['idorder']."' LIMIT 1";
			$res_sup = $this->db->query($get_sup) or die($this->mysqli->error);
			$ret_sup = $res_sup->result_array();
			$fcomment = $this->input->post('scomment');

			if(!empty($fcomment)) {
				$scomment = $fcomment;
			} else {
				$scomment = stripslashes($ret[0]['comment']);
			}

			$req_conf = "UPDATE orders_confirm SET `date_confirmed` = NOW(), `status` = 'confirmed', `comment`= '".mysql_real_escape_string(addslashes($scomment))."', `IP`= '".$ip."' WHERE `key` = '".$key."'";
			$this->db->query($req_conf)  or die($this->mysqli->error);
			$data = array('status' => 'OK', 'key' => $key, 'scomment' => $scomment);

			if($ret[0]['status'] != 'confirmed' OR ($ret[0]['status'] == 'confirmed' AND !empty($scomment) ) ) {
				$email['from']		= 'commande@hankrestaurant.com';
				$email['from_name']	= 'HANK';
				$email['to']		= 'commande@hankrestaurant.com';
				$email['subject'] 	= '';
				if(!empty($scomment)) { $email['subject'] .= "ALERT COMMENT! "; }
				$email['subject'] 	.= "Confirmation de commande de ".$ret_sup[0]['name'].", order: ".$ret[0]['idorder'];
				$email['replyto'] 	= "commande@hankrestaurant.com";
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

		$post 	= $this->input->post();
		$sup 	= array();
		$disp 	= array(); 
		$inc 	= 0;

		$server_name = $this->hmw->getParam('server_name');

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
				$cc 				= "commande@hankrestaurant.com";
				if(!empty($var['emailcc'])) $cc .= ','.$var['emailcc'];

				$email['from']		= 'commande@hankrestaurant.com';
				$email['from_name']	= 'HANK';
				$email['cc'] 		= $cc;
				$email['to']		= $var['email'];
				$email['subject'] 	= "Nouvelle commande ".$var['id'];
				$email['attach'] 	= 'orders/'.$date_y.'/'.$date_m.'/'.$var['id'].'.pdf';
				$email['replyto'] 	= "commande@hankrestaurant.com";
				$email['msg'] 		= "Bonjour ".$var['name']."!\n\nVoici une nouvelle commande en PJ.\n\n";
				if(!empty($var['comt'])) $email['msg'] .= $var['comt']."\n\n"; 
				$email['msg'] 		.= "Merci de bien vouloir valider la prise en compte de cette commande en cliquant sur ce lien : $link";
				$email['msg'] 		.= "\n\nHave A Nice Karma,\n-- \nHANK - ".$var['user']."\nEmail : commande@hankrestaurant.com \n Tel : $user->phone";

				$this->mmail->sendEmail($email);
				$req = "UPDATE orders SET `status` = 'sent', `date` = NOW() WHERE `idorder` = ".$var['idorder']." ORDER BY `date` DESC LIMIT 1";
				$this->db->query($req);

				$req_conf = "INSERT INTO orders_confirm SET `date_sent` = NOW(), `key` = '$key', `idorder` = ".$var['idorder'].", `status` = 'sent' ON DUPLICATE KEY UPDATE `date_sent` = NOW(), `key` = '$key'";
				$this->db->query($req_conf);

				$disp[] = $var['name'];
			}

		}

		$data = array('disp' => $disp);
		$this->load->view('order/order_sent', $data);

	}

	public function confirmOrder() {

		$this->hmw->keyLogin();
		$this->load->library('ion_auth');
		$user = $this->ion_auth->user()->row();
		$this->load->library('product');

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

		for( $i = 0; $i < $inc; ++$i )
		{
			$data 				= array();
			$html				= '';
			$pdf				= '';
			$info 				= array();
			$idorder 			= $post[$i.'_IDORDER'];
			$id					= $idorder.'_'.$post[$i.'_SUP'];
			$user 				= $this->hmw->getUser($user->id);

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
			//pdf_create($html, 'filename');
			$pdf = pdf_create($html, '', false);
			write_file('orders/'.$date_y.'/'.$date_m.'/'.$id.'.pdf', $pdf);
			$order[] = array('id' => $id, 'idorder' => $idorder, 'comt' => $info['comt'], 'user' => $user->username, 'userid' => $user->id, 'sup_name' => $info['sup_name'], 'sup_id' => $info['sup_id'], 'sup_email' => $info['sup_email'], 'cc_email' => $info['cc_email']);
		}

		$data2 = array('order' => $order);
		$this->load->view('order/order_confirm', $data2);
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


	private function groupOrder($data) 
	{
		$this->load->library('product');
		$products = $this->product->getProducts();
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
			$req = "INSERT INTO orders SET `data` = '".addslashes($srl)."', `idorder` = '".addslashes($idorder)."', supplier_id = '$supplier_id', user='".$user->id."'";
			$this->db->query($req);		
		}
		return $total;
	}

	private function clean_number($num) {
		$t1 = str_replace ( ',' , '.' , $num);
		$t2 = trim($t1);
		//$t3 = preg_replace("/[^0-9,.]/", "", $t2);
		return $t2;
	}
}

/**
public function stock_alert()
{

if($this->input->is_cli_request()) {

if($id == 3) $name ="opening";
if($id == 4) $name ="closing";

$msg = "WARINING! No $name checklist have been created!";

$sql 	= "SELECT DATE(`date`) FROM ckl_checklist_records WHERE DATE(`date`) = DATE(NOW()) AND id_checklist = $id";
$query	= $this->db->query($sql);
$res 	= $query->result();

if(empty($res)) {	

$email['to']		= 'checklist@hankrestaurant.com';
$email['subject'] 	= $msg;
$email['msg'] 		= $msg;
$this->mmail->sendEmail($email);

$sms = array();
$sms['to']			= "email2sms@ovh.net";
$sms['subject'] 	= "sms-dp131762-1:hanksms:gxistf23:HANK:+33647384930,+33650925448:::1";
$sms['msg'] 		= $email['subject'];
#$this->mmail->sendEmail($sms);

}
return;
} else { 
echo "Access refused.";
return; 
}

}
**/