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
		$this->load->library('ion_auth_acl');
		$this->load->model('order_model');
		$this->load->library("pagination");
		$this->load->helper(array('form', 'url'));
		$this->load->library('product');
		$this->load->database();

	}

	public function index()
	{
		$this->hmw->changeBu();// GENERIC changement de Bu
		$this->hmw->keyLogin();
		$id_bu			=  $this->session->userdata('bu_id');
		$user_groups	= $this->ion_auth->get_users_groups()->result();
		$suppliers 		= $this->product->getSuppliers(true, null, $id_bu);

		$data = array(
			'keylogin'		=> $this->session->userdata('keylogin'),
			'user_groups'	=> $user_groups[0],
			'suppliers'    	=> $suppliers);

		$data['bu_name']  =  $this->session->userdata('bu_name');
		$data['username'] = $this->session->userdata('identity');
		
		if ($this->session->userdata('filters')) {
			$this->session->unset_userdata('filters');
		}
		
		if ($this->session->userdata('keep_filters')) {
			$this->session->unset_userdata('keep_filters');
		}

		$headers = $this->hmw->headerVars(1, "/order/", "Order");
		$this->load->view('jq_header_pre', $headers['header_pre']);
		$this->load->view('jq_header_post', $headers['header_post']);
		$this->load->view('order/index',$data);
		$this->load->view('jq_footer');
	}

	public function loss() {

		$this->hmw->keyLogin();
		$user = $this->ion_auth->user()->row();

		$id_bu					= $this->session->userdata('bu_id');
		$post					= $this->input->post();

		$this->db->select('users.username, users.last_name, users.first_name, users.email, users.id');
		$this->db->distinct('users.username');
		$this->db->join('users_bus', 'users.id = users_bus.user_id', 'left');
		$this->db->where('users.active', 1);
		$this->db->where('users_bus.bu_id', $id_bu);
		$this->db->order_by('users.username', 'asc');
		$query = $this->db->get("users");
		$users = $query->result();

		$data = array();
		$data['bu_name']	= $this->session->userdata('bu_name');
		$data['username']	= $this->session->userdata('identity');
		$data['keylogin']	= $this->session->userdata('keylogin');
		$data['users']		= $users;

		$headers = $this->hmw->headerVars(0, "/order/", "Loss");
		$this->load->view('jq_header_pre', $headers['header_pre']);
		$this->load->view('jq_header_post', $headers['header_post']);
		$this->load->view('order/loss',$data);
		$this->load->view('jq_footer');
	}

	public function saveLoss()
	{
		$id_bu =  $this->session->userdata('bu_id');
		$reponse = 'ok';
		$data = $this->input->post();
		$user = $this->ion_auth->user()->row();
		$this->load->library('cashier');

		$user_receive = $user->id;
		if(isset($post['user'])) {
			if($post['user']) {
				$user_receive = $post['user'];
			}
		}

		$data['value'] = $this->hmw->cleanNumber($data['value']);
		if(empty($data['value'])) $value = '0';
		if(!empty($data['value']) AND !is_numeric($data['value'])) exit('Stock has to be numeric, invalid: '.$data['value']);

		if($data['type'] == 'ARTICLE') {

			$pdt_info = $this->product->getProducts($data['id'], null, null, null, $id_bu);
			$previous_qtty = $pdt_info[$data['id']]['stock_qtty'];

			$q = "UPDATE products_stock SET qtty=qtty+-$data[value], last_update_id_user=$user_receive, last_update_user=NOW() WHERE id_product = $data[id]";
			$this->db->query($q) or die($this->mysqli->error);

			$p = array(
				'type'	=> 'stock_loss',
				'val1'	=> "$data[id]",
				'val2'	=> "$data[value]",
				'val4'	=> "$previous_qtty"
			);
			$this->hmw->LogRecord($p);

		} elseif($data['type'] == 'PRODUCT') {
			$this->cashier->updateProductStock($data['id'], $data['value'], $id_bu, 'stock_loss');
		}
		echo json_encode(['reponse' => $reponse]);
		exit();
	}

	public function autoCompLoss(){

		$id_bu =  $this->session->userdata('bu_id');

		if (isset($_GET['q'])){
			$q = strtolower($_GET['q']);
			$row_set = array();
			$this->db->select('p.name AS name, p.id AS id, s.name AS sname, ps.qtty AS stock, p.price AS price, p.packaging AS packaging, puprc.name AS unitname')
				->from('products AS p')
				->join('suppliers as s', 'p.id_supplier = s.id', 'right')
				->join('products_unit as puprc', 'p.id_unit = puprc.id')
				->join('products_stock as ps', 'p.id = ps.id_product', 'right')
				->like('p.name', "$q", 'both')
				->where('p.deleted', 0)
				->where('p.active', 1)
				->where('s.deleted', 0)
				->where('s.active', 1)
				->where('s.id_bu', $id_bu)
				->order_by('p.name asc')->limit(100);
			$query = $this->db->get() or die($this->mysqli->error);

			$products_pos	= $this->product->getPosProducts($id_bu, $q);

			if($query->num_rows() > 0) {
				$article = $query->result_array();

				foreach ($article as $row){
					$row_set['a'.$row['id']] = htmlentities(stripslashes($row['name']))."|||".$row['id']."|||".$row['sname']."|||".$row['stock']."|||".$row['price']."|||".$row['unitname']."|||".$row['packaging']."|||ARTICLE";
				}
			}

			foreach ($products_pos as $rowp){
				$row_set['p'.$rowp['id']] = htmlentities(stripslashes($rowp['name']))."|||".$rowp['id']."|||-|||-|||-|||PIECE|||1|||PRODUCT";
			}

			echo $_GET['callback']."(".json_encode($row_set).");";
		}
	}

	public function autoCompProducts(){

		$id_bu =  $this->session->userdata('bu_id');

		if (isset($_GET['q'])){
			$q = strtolower($_GET['q']);
			$row_set = array();
			$this->db->select('p.name AS name, p.id AS id, s.name AS sname, ps.qtty AS stock, p.price AS price, p.packaging AS packaging, puprc.name AS unitname')
				->from('products AS p')
				->join('suppliers as s', 'p.id_supplier = s.id', 'right')
				->join('products_unit as puprc', 'p.id_unit = puprc.id')
				->join('products_stock as ps', 'p.id = ps.id_product', 'right')
				->like('p.name', "$q", 'both')
				->where('p.deleted', 0)
				->where('p.active', 1)
				->where('s.deleted', 0)
				->where('s.active', 1)
				->where('s.id_bu', $id_bu)
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

		if(empty($id_bu)) exit('empty BU ID');

		if(is_cli()) {
			$param = array();
			$param['id_bu'] = $id_bu;
			if(is_cli()) {
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

		if(is_cli()) {

			$param = array();
			$param['id_bu'] = $id_bu;
			if(is_cli()) {
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

					$msg = "WARNING! " . $info->name . " CASHPAD NOT CLOSED!";

          $this->mmail->prepare($msg, $msg)
            ->toList('cashier_alerts', $id_bu)
            ->send();

          $this->hmw->sendNotif($msg, $id_bu);

				}

			} else {
				return false;
			}
		}
	}

	public function viewOrders()
	{
		$this->hmw->keyLogin();
		$this->load->library('session');
		$this->load->library('user_agent');
		
		$id_bu 		= $this->session->userdata('bu_id');
		$keylogin 	= $this->session->userdata('keylogin');
		$this->db->select('users.username, users.last_name, users.first_name, users.email, users.id');
		$this->db->distinct('users.username');
		$this->db->join('users_bus', 'users.id = users_bus.user_id', 'left');
		$this->db->where('active', 1);
		$this->db->where('users_bus.bu_id', $id_bu);
		$this->db->order_by('users.username', 'asc');
		$query					= $this->db->get("users");
		$users					= $query->result();

		$config 				= array();
		$config["base_url"] 	= base_url() . "order/viewOrders";
		$config["total_rows"] 	= $this->order_model->record_count();
		$config["per_page"] 	= 10;
		$config["uri_segment"] 	= 3;
		$config['first_link'] 	= 'First';
		$config['prev_link'] 	= 'Previous';
		$config['next_link'] 	= 'Next';
		$config['last_link'] 	= 'Last';
		$config['num_links'] 	= 4;
		$config['prev_tag_close'] = '&nbsp;&nbsp;';
		$config['next_tag_close'] = '&nbsp;&nbsp;';
		$config['next_tag_open'] = '&nbsp;';
		$config['first_tag_close'] = '&nbsp;&nbsp;';
		$config['last_tag_close'] = '&nbsp;';
		$config['num_tag_close'] = '&nbsp;';
		$config['cur_tag_close'] = '&nbsp;';
		$this->pagination->initialize($config);

		$page = ($this->uri->segment(3)) ? $this->uri->segment(3) : 0;
		$post  = $this->input->get();

		if(!empty($post) && isset($post['search'])) {
			foreach ($post as $key => $val) {
				$filters[$key] = $val;
			}
			$this->session->set_userdata('filters', $filters);
			$results	= $this->searchOrder($post, $id_bu, $keylogin);
			$search		= true;
		} else {
			$results	= $this->order_model->get_list($config["per_page"], $page, $keylogin);
			$search		= false;
		}
		
		$res2 = $results;
		if (!empty($res2)){
			foreach ($res2 as $key => $rec) {
				if (isset($rec['data'])) {
					$usrl = unserialize($rec['data']);
					if (isset($usrl['pricetotal']) && is_numeric($usrl['pricetotal'])) {
						$results[$key]['totalht'] = ($usrl['pricetotal'] / 1000);
					} else {
						$results[$key]['totalht'] = 'Unable to get total for this order';
					}
				}
				$this->db->where('idorder', $rec['idorder']);
				$query = $this->db->get('orders_comments');
				$results[$key]['countComments'] = $query->num_rows();
				$results[$key]['comments'] = $query->result_array(); 
			}
		}
		
		$data = array(
			'suppliers'	=> $this->product->getSuppliers(null, null, $id_bu),
			'users'		=> $users,
			'results'	=> $results,
			'keylogin'	=> $keylogin,
			'search'	=> $search,
			'links'		=> $this->pagination->create_links()
			);
		if (($this->session->userdata('keep_filters') === 'true') || (!empty($post['keep_filters']) && $post['keep_filters'] == 'true'))
		{
			if ($this->session->userdata('filters') !== null) {
				$data['filters'] = $this->session->userdata('filters');
			}
			//$this->session->unset_userdata('keep_filters');
		} else {
			//$this->session->unset_userdata('filters');
		}

		$referrer	= $this->agent->referrer();
		$ref_ex 	= explode('/', $referrer);
		if(!isset($ref_ex['4'])) $ref_ex['4'] = 'NONE';
		
		if($this->session->userdata('keep_filters') == 'true' && ($ref_ex['4'] != 'viewOrders' OR $ref_ex == 'NONE') && $this->session->userdata('reset_filters') != true) {
			if (!empty($data['filters'])) {
				$location = '/order/viewOrders?'.http_build_query($data['filters']);
			} else {
				$location = '/order/viewOrders';
			}
			$this->session->unset_userdata('filters');
			$this->session->unset_userdata('keep_filters');
			$this->session->set_userdata('reset_filters', true);
			header('Location: '.$location);
			exit();
		}
	
		$data['bu_name'] =  $this->session->userdata('bu_name');
		$data['username'] = $this->session->userdata('identity');
				
		$headers = $this->hmw->headerVars(0, "/order/", "Orders");
		$this->load->view('jq_header_pre', $headers['header_pre']);
		$this->load->view('order/jq_header_spe');
		$this->load->view('jq_header_post', $headers['header_post']);
		$this->load->view('order/order_view',$data);
		$this->load->view('jq_footer');
		
		$this->session->unset_userdata('reset_filters');		
	}
	
	private function sortArray($array) {
		$high = array();
		$medium = array();
		$low = array();
		foreach ($array as $value) {
			if ($value['freq_inventory'] == 'high') {
				array_push($high, $value);
			}
			else if ($value['freq_inventory'] == 'medium') {
				array_push($medium, $value);
			}
			else {
				array_push($low, $value);
			}
		}
		$productsFinal = array_merge($high, $medium, $low);
		return ($productsFinal);
	}

	private function sortProductOrder($a, $b) {
		if(isset($b['stock']) && $b['stock'] > 0) {
			$b['qtty'] = $b['stock'];
		}
		return $b['qtty'] - $a['qtty'];
	}

	public function viewProducts($load = null, $supplier_id = null, $type = null)
	{

		$this->hmw->keyLogin();
		$id_bu =  $this->session->userdata('bu_id');

		$order_prev		= null;
		$supinfo		= null;
		$data_reception = null;
		$products		= $this->product->getProducts(null, $supplier_id, null, null, $id_bu, true);
		$stock 			= $this->product->getStock();
		$attributs		= $this->product->getAttributs();
		$comment_order 	= '';
		$comment_recept	= '';

		if($load > 0) {
			$this->db->from('orders as r')->where('r.idorder', $load)->where('id_bu', $id_bu);
			$order_rec_res	= $this->db->get() or die($this->mysqli->error);
			$order_rec		= $order_rec_res->row();
			$comment_order 	= $order_rec->comment;
			$comment_recept	= $order_rec->comment_reception;
			$order_prev		= unserialize($order_rec->data);
			//$supplier_id	= $order_prev['supplier'];

			if($type == 'viewreception') $order_recev = unserialize($order_rec->data_reception);

			foreach ($products as $key => $val) {

				$products[$key]['qtty'] = null;
				if(isset($order_prev['pdt'][$key]['qtty']) && $order_prev['pdt'][$key]['qtty'] > 0) {
					$products[$key]['qtty'] = $order_prev['pdt'][$key]['qtty'];
				}
				//Inject product added at reception
				if($type == 'viewreception' && isset($order_recev['pdt'][$key]['stock'])) {
					$products[$key]['stock'] = $order_recev['pdt'][$key]['stock'];
					if(!isset($products[$key]['qtty']) AND $order_recev['pdt'][$key]['stock'] > 0) $products[$key]['qtty'] = 0;
				}

				//Remove product from viewreception if empty (not used anymore)
				// if($type == 'viewreception' && (empty($products[$key]['stock']) AND empty($products[$key]['qtty']))) {
				// 	unset($products[$key]);
				// }

			}
			uasort($products, array($this, "sortProductOrder"));
			//print_r($products);
		}

		if(isset($supplier_id)) $supinfo = $this->product->getSuppliers(null, $supplier_id, $id_bu);

		$this->db->select('users.username, users.last_name, users.first_name, users.email, users.id');
		$this->db->distinct('users.username');
		$this->db->join('users_bus', 'users.id = users_bus.user_id', 'left');
		$this->db->where('users.active', 1);
		$this->db->where('users_bus.bu_id', $id_bu);
		$this->db->order_by('users.username', 'asc');
		$query = $this->db->get("users");
		$users = $query->result();
		
		if ($type != 'reception' && ($load == null || $load == 0)) $products = $this->sortArray($products);
		
		$data = array(
			'products'			=> $products,
			'stock'				=> $stock,
			'attributs'			=> $attributs,
			'comment_order'		=> $comment_order,
			'comment_recept'	=> $comment_recept,
			'users'				=> $users,
			'supinfo'			=> $supinfo[$supplier_id],
			'load' 				=> $load,
			'type'				=> $type,
			);
		
		if (isset($order_recev)) $data['unsrl_order'] = $order_recev;
		$title 				= "Order ".$supinfo[$supplier_id]['name'];
		$data['bu_name']	= $this->session->userdata('bu_name');
		$data['username']	= $this->session->userdata('identity');
		$data['keylogin']	= $this->session->userdata('keylogin');
		if ($type == 'reception' || $type == 'order' || $type == 'viewreception') {
		$headers = $this->hmw->headerVars(0, "/order/viewOrders/", $title);
		$this->session->set_userdata('keep_filters', 'true');
	} else {
		$headers = $this->hmw->headerVars(0, "/order/", $title);
	}
		$this->load->view('jq_header_pre', $headers['header_pre']);
		$this->load->view('jq_header_post', $headers['header_post']);
		$this->load->view('order/order_products',$data);
		$this->load->view('jq_footer');

	}
	
	public function saveComment($idorder = null) 
	{	
		$this->hmw->isLoggedIn();
		if (empty($idorder)) {
			die("Error: Missing Parameters");
		}
		$post = $this->input->post();
		$date = date('Y-m-d H:i:s');
		$user = $this->ion_auth->user()->row();
		if (empty($post) || empty($post['comment']))
			die("No data received");
		$entry['content'] = $post['comment'];
		$entry['username'] = $user->username;
		$entry['idorder'] = $idorder;
		$entry['date'] = $date;
		$this->session->set_userdata('keep_filters', 'true');
		$this->db->insert('orders_comments', $entry);
		redirect('/order/viewOrders', 'auto');
	}
	
	public function cancelReception() {
		
		$post = $this->input->post();
		if (isset($post['srl_order_post'])) {
			$unsrl_order = unserialize($post['srl_order_post']);
			foreach ($unsrl_order['pdt'] as $key => $val) {
				if (isset($val['stock'])) {
					$received = $val['stock'];
					$this->db->select('qtty');
					$this->db->where('id_product', $key);
					$current_stock = $this->db->get('products_stock')->row_array();
					$new_stock = $current_stock;
					$new_stock['qtty'] = $current_stock['qtty'] - $received;
					$this->db->where('id_product', $key);
					$this->db->update('products_stock', $new_stock);
				}
			}
		}
		$array_cancel = array('data_reception' => null, 'status' => 'sent');
		$this->db->where('idorder', $post['id_order']);
		$this->db->update('orders', $array_cancel);
		redirect('order/viewOrders', 'refresh');
		die();
	}
	
	public function editReception($post) {
		
		$unsrl_order = unserialize($post['srl_order_post']);
		$editQtty = $post['editQtty'];
		foreach ($editQtty as $key => $val) {
			if (!empty($val)) {
				$diff = $unsrl_order['pdt'][$key]['stock'] - $val;
				$this->db->select('qtty');
				$this->db->where('id_product', $key);
				$current_stock = $this->db->get('products_stock')->row_array();
				$new_stock = $current_stock;
				$new_stock['qtty'] = $current_stock['qtty'] - $diff;
				$this->db->where('id_product', $key);
				$this->db->update('products_stock', $new_stock);
				$unsrl_order['pdt'][$key]['stock'] = $val;
			}
		}
		$srl = serialize($unsrl_order);
		$array_order = array('data_reception' => $srl);
		$this->db->where('idorder', $post['id_order']);
		$this->db->update('orders', $array_order);
		redirect($post['current_url'], 'refresh');
	}

	public function detailOrder() {

		$this->hmw->keyLogin();
		$user			= $this->ion_auth->user()->row();
		$stock_update	= false;
		$post			= $this->input->post();
		$id_bu 			= $this->session->userdata('bu_id');
		$update_stock	= array();
		$do_something	= false;

		if(empty($post)) exit('Nothing to process, go back');
		
		if (isset($post['editReception']) && $post['editReception'] == true) {
			$this->editReception($post);
			return;
		}
		foreach ($post as $key => $var) {

			//update stock
			if($key == 'stock') {
				foreach ($var as $id_pdt => $value) {
					$value = $this->hmw->cleanNumber($value);
					if(empty($value)) $value = '0';
					if(!empty($value) AND !is_numeric($value)) exit('Stock has to be numeric, invalid: '.$value);
					$pdt_info = $this->product->getProducts($id_pdt, null, null, null, $id_bu);
					$previous_qtty = $pdt_info[$id_pdt]['stock_qtty'];

					$q = "UPDATE products_stock SET qtty=qtty+$value, last_update_id_user=$user->id, last_update_user=NOW() WHERE id_product = $id_pdt";
					$this->db->query($q) or die($this->mysqli->error);

					$update_stock[$id_pdt]['stock']	= $value;
					$update_stock[$id_pdt]['name']	= $pdt_info[$id_pdt]['name'];

					if(!empty($var) AND $value > 0) {
						$p = array(
							'type' => 'stock_reception',
							'val1' => "$id_pdt",
							'val2' => "$value",
							'val3' => "$post[idorder]",
							'val4' => "$previous_qtty"
						);
						$this->hmw->LogRecord($p);
						$stock_update = true;
						$do_something	= true;
					}
				}
			}

			if($post['type'] != 'reception') {
				//insert order
				$id_pdt = 0;
				$value = 0;
				if($key == 'qtty') {
					$order = array();
					$order['id'] = date('ymd').rand(1000, 9000);
					$order['supplier'] = $post['supplier'];
					$pricetotal = 0;

					foreach ($var as $id_pdt => $value) {
						if($value > 0) {
							$order['pdt'][$id_pdt] = array(
								'qtty' => trim($value),
								'name' => trim($post['pdt_name'][$id_pdt]),
								'price' => trim($post['price'][$id_pdt]),
								'subtotal' => $post['price'][$id_pdt]*$value
								);
						}
						if(!empty($value) AND !is_numeric($value)) exit('Qtty has to be numeric, invalid: '.$value);
						if(!empty($value) AND is_numeric($value)) $do_something	= true;
						if($post['pkg'][$id_pdt] <= 0) exit('Colisage incorrect, doit être supérieur à 0 pour '.$post['pdt_name'][$id_pdt].'.');
						$packaging_check = trim($value/$post['pkg'][$id_pdt]);
						if(is_float($packaging_check)) exit('Colisage incorrect: '.$packaging_check.', entrez un multiple de '.$post['pkg'][$id_pdt].' pour '.$post['pdt_name'][$id_pdt].'.');
						
						$pricetotal += $post['price'][$id_pdt]*$value;
					}
					$order['pricetotal'] = $pricetotal;

					//serialize and insert into db
					$srl = serialize($order);
					$this->db->set('data', $srl);
					$this->db->set('idorder', $order['id']);
					$this->db->set('supplier_id', $post['supplier']);
					$this->db->set('user', $user->id);
					$this->db->set('id_bu', $id_bu);
					$this->db->insert('orders');
				}

				//order is reception
			} else {
				$do_something = true;
				$user_receive = $user->id;
				if(isset($post['user'])) {
					if($post['user']) {
						$user_receive = $post['user'];
					}
				}
				if($key == 'stock') {
					$order_reception = array();
					$status_reception = true;
					foreach ($var as $id_pdt => $value) {
						if($value > 0) {
							$order_reception['pdt'][$id_pdt] = array(
								'qtty' => $value,
								'name' => $post['pdt_name'][$id_pdt],
								'price' => $post['price'][$id_pdt],
								'stock' => $post['stock'][$id_pdt],
								'subtotal' => $post['price'][$id_pdt]*$value
								);
						}
						
						if(isset($post['comment'][$id_pdt])) $order_reception['pdt'][$id_pdt]['comment'] = $post['comment'][$id_pdt];

							if($post['qtty_check'][$id_pdt] != $post['stock'][$id_pdt]) {
								$status_reception = false;
							}
					}


					//serialize and insert into db
					if(isset($post['user'])) $user_receive = $post['user'];
					$srl = serialize($order_reception);
					$this->db->set('data_reception', $srl);
					$this->db->set('comment_reception', $post['comment_reception']);
					$this->db->set('status', 'received');
					$this->db->set('user_reception', $user_receive);
					$this->db->set('date_reception', "NOW()", FALSE);
					$this->db->set('status_reception', $status_reception);
					$this->db->where('idorder', $post['idorder']);
					$this->db->update('orders')  or die($this->mysqli->error);
					$order = array('id' => $post['idorder']);
				}
			}
		}

		$supinfo = $this->product->getSuppliers(null, $post['supplier'], $id_bu);
		$pdtinfo = $this->product->getProducts(null, $post['supplier'], null, null, $id_bu, null);

		$data = array(
			'order'			=> $order,
			'suppliers'		=> $post['supplier'],
			'stock_update'	=> $stock_update,
			'supinfo'		=> $supinfo[$post['supplier']],
			'pdtinfo'		=> $pdtinfo,
			'type'			=> $post['type'],
			'update_stock' 	=> $update_stock);
			
		$this->session->set_userdata('keep_filters', 'true');

		$data['bu_name'] =  $this->session->userdata('bu_name');
		$data['username'] = $this->session->userdata('identity');

		if(!$do_something) exit('Empty form, go back');

		$headers = $this->hmw->headerVars(0, "/order/viewOrders", "Order Detail");
		$this->load->view('jq_header_pre', $headers['header_pre']);
		$this->load->view('jq_header_post', $headers['header_post']);
		$this->load->view('order/order_detail',$data);
		$this->load->view('jq_footer');
	}

	public function confirm($key = null) {

		$this->load->library('mmail');

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
			$this->db->set('comment', $scomment);
			$this->db->set('IP', $ip);
			$this->db->where('key', $key);
			$this->db->update('orders_confirm')  or die($this->mysqli->error);
			$data = array('status' => 'OK', 'key' => $key, 'scomment' => $scomment);

			if($ret[0]['status'] != 'confirmed' OR ($ret[0]['status'] == 'confirmed' AND !empty($scomment) ) ) {

        $subject=  '';
        if(!empty($scomment))
          $subject .= "ALERT COMMENT! ";
        $subject .= 'Confirmation de commande de ' . $ret_sup[0]['name'] . ', order: ' . $ret[0]['idorder'];

        $msg = 'Commande : ' . $ret[0]['idorder'] . ' validée par fournisseur ' . $ret_sup[0]['name'] . '.';

        $this->mmail->prepare($subject, $msg)
          ->from($order_email, 'HANK')
          ->toEmail($order_email)
          ->replyTo($order_email);
			}
		} else {
			$data = array('status' => 'NOK');
		}

		$this->load->view('order/confirm',$data);
	}
	
	public function cancelOrder($load = null, $supplier_id = null, $confirm = false)
	{
		$this->load->library('mmail');
		$this->hmw->isLoggedIn();
		if ($confirm == true) {
			if (empty($load) || empty($supplier_id))
				die("Cannot cancel order: Missing parameters");
			
			$id_bu = $this->session->userdata('bu_id');
			$user  = $this->ion_auth->user()->row();
			$this->db->where('idorder', $load);
			$this->db->where('supplier_id', $supplier_id);
			$query = $this->db->get('orders');
			$order = $query->row_array();
			$order_email 	= $this->hmw->getEmail('order', $id_bu);

			$this->db->where('id', $supplier_id);
			$query = $this->db->get('suppliers');
			$supplier = $query->row_array();
			$supplier_email = $supplier['contact_order_email'];

			if (empty($order))
				die("Cannot cancel order: Cannot find order in database");
			if (empty($supplier_email))
				die("Cannot cancel order: Cannot find supplier email");

				if(!empty($supplier_email)) {
					$cc = $order_email;
					if(!empty($order['ccemail'])) $cc .= ','.$order->ccemail;
				}

      $subject = 'Annulation Commande ' . $load;
      $msg = 'Bonjour ' . $supplier['name']
        . "!\n\nNous souhaitons annuler la commande en PJ.\n\n"
        . "\n\nHave A Nice Karma,\n-- \nHANK - " . $user->username
        . "\nEmail : " . $order_email . "\nTel : $user->phone";

      $this->mmail->prepare($subject, $msg)
        ->from($order_email, 'HANK')
        ->toEmail($supplier_email)
        ->cc($cc)
        ->replyTo($order_email)
        ->attach($order['file']);

			$this->db->set('status', 'canceled');
			$this->db->where('idorder', $load);
			$this->db->update('orders');

			$this->session->set_userdata('keep_filters', 'true');

			redirect('/order/viewOrders/', 'auto');
		} else {
			if (empty($load) || empty($supplier_id))
				die("Cannot cancel order: Missing parameters");

			$this->db->where('idorder', $load);
			$this->db->where('supplier_id', $supplier_id);
			$query = $this->db->get('orders');
			$order = $query->row_array();

			$this->db->where('id', $supplier_id);
			$query = $this->db->get('suppliers');
			$supplier = $query->row_array();

			if (empty($order)) {
				die("Cannot cancel order: Cannot find order in database");
			}

			if (empty($supplier)) {
				die("Cannot cancel order: Cannot find supplier in database");
			}

			$filename = $order['file'];
			$data['order'] = $order;
			$fileencode = str_replace("/", "-", $filename);
			$data['filename']	= urlencode($fileencode);
			$data['supplier'] = $supplier;
			$headers = $this->hmw->headerVars(0, "/order/ViewOrders", "Cancel Order");
			$this->load->view('jq_header_pre', $headers['header_pre']);
			$this->load->view('jq_header_post', $headers['header_post']);
			$this->load->view('order/confirmCancelOrder', $data);
			$this->load->view('jq_footer');
		}
	}

	public function sendOrder() {

		$this->hmw->keyLogin();
		$this->load->helper('download');
		$this->load->library('mmail');
		$this->load->helper('file');

		$id_bu 			= $this->session->userdata('bu_id');
		$user 			= $this->ion_auth->user()->row();
		$post			= $this->input->post();

		$idorder		= $post['idorder'];
		$sup 			= array();
		$disp 			= array();
		$inc 			= 0;
		$order			= $this->getOrder($idorder, $id_bu) or die('Can\'t get order: ' .$idorder. ' for BU '.$id_bu);

		$server_name	= $this->hmw->getParam('server_name');
		$order_email 	= $this->hmw->getEmail('order', $id_bu);
		$getSupInfo		= $this->product->getSuppliers(null, $order->supplier_id, $id_bu);
		$supinfo		= $getSupInfo[$order->supplier_id];

		$email 	= array();
		$cc 	= '';

		$key 	= md5(microtime().rand());
		$link 	= 'http://'.$server_name.'/order/confirm/'.$key;

		if(!empty($supinfo['contact_order_email'])) {
			$cc = $order_email;
			if(!empty($order->ccemail)) $cc .= ','.$order->ccemail;

      $subject = 'Nouvelle commande ' . $idorder;

      $msg     = 'Bonjour ' . $supinfo['name']
        . "!\n\nVoici une nouvelle commande en PJ.\n\n";
      if(!empty($order->comment))
        $msg .= $order->comment."\n\n";
      $msg .= 'Merci de bien vouloir valider la prise en compte de cette commande'
       . ' en cliquant sur ce lien : $link';
      $msg .= "\n\nHave A Nice Karma,\n-- \nHANK - " . $user->username
        . "\nEmail : " . $order_email . "\nTel : " . $user->phone;

      $this->mmail->prepare($subject, $msg)
        ->from($order_email, 'HANK')
        ->toEmail($supinfo['contact_order_email'])
        ->cc($cc)
        ->replyTo($ordeR_email)
        ->attach($order->file)
        ->send();

			if (!empty($supinfo['contact_order_tel']) AND isset($post['SMSSupplier']) AND $post['SMSSupplier'] == "on") {
				$msg = 'Une commande HANK vient d\'être envoyée, merci de bien vouloir consulter vos emails.';
				$this->hmw->sendSms($supinfo['contact_order_tel'], $msg);
			}
			$this->db->set('status', 'sent')->set('date', "NOW()", FALSE);
			$this->db->where('idorder', $idorder)->order_by('date desc')->limit(1);
			$this->db->update('orders');

			$req_conf = "INSERT INTO orders_confirm SET `date_sent` = NOW(), `key` = '$key', `idorder` = ".$idorder.", `status` = 'sent' ON DUPLICATE KEY UPDATE `date_sent` = NOW(), `key` = '$key'";
			$this->db->query($req_conf);
		}

		$data = array('name' => $supinfo['name']);
		$data['bu_name'] =  $this->session->userdata('bu_name');
		$data['username'] = $this->session->userdata('identity');

		$this->session->set_userdata('keep_filters', 'true');

		$headers = $this->hmw->headerVars(0, "/order/ViewOrders", "Order Sent");
		$this->load->view('jq_header_pre', $headers['header_pre']);
		$this->load->view('jq_header_post', $headers['header_post']);
		$this->load->view('order/order_send', $data);
		$this->load->view('jq_footer');

	}

	public function confirmOrder() {

		$this->hmw->keyLogin();
		$user = $this->ion_auth->user()->row();
		$this->load->helper(array('dompdf', 'file'));
		$id_bu					= $this->session->userdata('bu_id');
		$post					= $this->input->post();
		$order					= $this->getOrderData($post['idorder'], $id_bu);
		$getSupInfo				= $this->product->getSuppliers(null, $post['supplier'], $id_bu);

		$html					= '';
		$pdf					= '';

		$info 					= array();
		$info['date']			= date('d/m/Y H:i');
		$info['idorder']		= $post['idorder'];
		$info['user'] 			= $this->hmw->getUser($user->id);
		$info['buinfo']			= $this->hmw->getBuInfo($id_bu);
		$info['supplier']		= $getSupInfo[$post['supplier']];
		$info['pdtinfo']		= $this->product->getProducts(null, $post['supplier'], null, null, $id_bu, null);
		$info['valid_number'] = $this->validateSupplierTel($info['supplier']['contact_order_tel']);

		$info['cc_email'] 		= $post['ccemail'];
		$info['comment'] 		= $post['comment'];


		$date_y = date('Y');
		$date_m	= date('m');

		if (!is_dir('orders/'.$date_y)) {
			mkdir('./orders/' . $date_y, 0777, TRUE);
		}

		if (!is_dir('orders/'.$date_y.'/'.$date_m)) {
			mkdir('./orders/'.$date_y.'/'.$date_m, 0777, TRUE);
		}

		$data = array('info' => $info, 'order' => $order);
		$html = $this->load->view('order/bdc', $data, true);
		//$this->load->view('order/bdc', $data); //to debug uncomment this line, comment 1 line above and all the following

		$pdf = pdf_create($html, '', false);
		$filename = 'orders/'.$date_y.'/'.$date_m.'/'.$info['idorder'].'_'.strtoupper($info['supplier']['name']).'.pdf';

		//update order
		$this->db->set('comment', $post['comment']);
		$this->db->set('ccemail', $post['ccemail']);
		$this->db->set('file', $filename);
		$this->db->where('idorder', $post['idorder']);
		$this->db->where('id_bu', $id_bu);
		$this->db->update('orders')  or die($this->mysqli->error);

		write_file($filename, $pdf);
		$fileencode = str_replace("/", "-", $filename);
		$data['filename']	= urlencode($fileencode);
		$data['bu_name']	= $this->session->userdata('bu_name');
		$data['username']	= $this->session->userdata('identity');

		$this->session->set_userdata('keep_filters', 'true');

		$headers = $this->hmw->headerVars(0, "/order/viewOrders", "Order Confirm");
		$this->load->view('jq_header_pre', $headers['header_pre']);
		$this->load->view('jq_header_post', $headers['header_post']);
		$this->load->view('order/order_confirm', $data);
		$this->load->view('jq_footer');
	}

	public function pdfPreview($file) {
		$filedecode = urldecode($file);
		$filename = str_replace("-", "/", $filedecode);
		$im = new imagick($filename);
		$im->setImageFormat('png');
		if($im->getNumberImages() > 1) {
			$im->previousImage();
			$im->previousImage();
		}
		header('Content-Type: image/png');
		echo $im;
	}

	public function downloadOrder($id) {

		$this->load->helper('download');
		$date_y = '20'.$id[0].$id[1];
		$date_m = $id[2].$id[3];
		$data = file_get_contents('orders/'.$date_y.'/'.$date_m.'/'.$id.'.pdf');
		$name = $id.'.pdf';
		force_download($name, $data);

	}

	private function validateSupplierTel($number)
	{
		$pattern = '/(^\+33\d{9}$)/';
		if (preg_match($pattern, $number) == 1) {
			return (true);
		} else {
			return (false);
		}
	}
	
	private function searchOrder($data, $id_bu, $keylogin=null){
		$ok=0;
		$this->db->select('r.user, u.username, ur.username as username_reception, u.first_name as first_name, u.last_name as last_name, r.id as lid, r.idorder, r.id, r.date, r.data, r.supplier_id, r.status, r.user_reception, r.date_reception, r.data_reception, r.status_reception, c.status as confirm, s.name as supplier_name');
		$this->db->from('orders as r');
		$this->db->join('users as u', 'r.user = u.id');
		$this->db->join('users as ur', 'r.user_reception = ur.id', 'left');
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
			$this->db->like('r.idorder',	$data['idorder']);
		}
		$status = array();
		if (isset($data['sent']) && $data['sent']!=''){
			$ok=1;
			$status[] = 'sent';
		}
		if (isset($data['received']) && $data['received']!=''){
			$ok=1;
			$status[] = 'received';
		}
		if (isset($data['draft']) && $data['draft']!=''){
			$ok=1;
			$status[] = 'draft';
		}
		if (isset($data['canceled']) && $data['canceled']!=''){
			$ok=1;
			$status[] = 'canceled';
		}
		if (isset($status) && !empty($status)) {
			$this->db->where_in('r.status', $status);
		} else {
			$status = 'undefined';
			$this->db->where('r.status', $status);
		}
		if($data['sdate']!=''){
			$ok=1;
			$this->db->where('r.date >=',	$data['sdate']);
		}
		if($data['edate']!=''){
			$ok=1;
			$this->db->where('r.date <=',	$data['edate']);
		}
		if($data['status_reception']!='') {
			$ok=1;
			if ($data['status_reception'] == 'NOK') {
				$status_r = 0;
			} else if ($data['status_reception'] == 'OK') {
				$status_r = 1;
			} else {
				$status_r = '';
			}
			$this->db->where('r.status_reception', $status_r);
		}
		$this->db->order_by('r.date desc')->limit(50);
		$rec_res = $this->db->get() or die($this->mysqli->error);

		return $rec_res->result_array();
	}

	public function clicCheckConfirm($id_bu)
	{
		$this->load->library('mmail');

		if(is_cli()) {

			$date_current	= new DateTime();
			$current_weekday = $date_current->format('D');
			$this->db->select('orders_confirm.idorder, orders_confirm.key, orders_confirm.date_sent, suppliers.name, suppliers.contact_order_email, orders.user, orders.ccemail');

			$this->db->join('orders', 'orders_confirm.idorder = orders.idorder', 'left');
			$this->db->join('suppliers', 'orders.supplier_id = suppliers.id', 'left');
			$this->db->where('orders_confirm.count_confirm <', 2);
			$status = array('sent','chased');
			$this->db->where_in('orders_confirm.status', $status);
			$this->db->where('suppliers.no_chased_email', false);
			$this->db->where("orders.date > DATE_ADD(NOW(), INTERVAL -5 DAY)");
			$this->db->where('orders.id_bu', $id_bu);
			
			$query = $this->db->get("orders_confirm");
			$lines = $query->result();
			foreach ($lines as $line) {

				$date_sent		= new DateTime($line->date_sent);
				$date_sent		->modify('+1 day');
				$ds 			= $date_sent->format('U');
				$dc 			= $date_current->format('U');
				if ($current_weekday != 'Sat' AND $current_weekday != 'Sun') {
					if(!empty($line->contact_order_email) AND $ds <= $dc) {

						$server_name		= $this->hmw->getParam('server_name');
						$order_email		= $this->hmw->getEmail('order', $id_bu);
						$user 				= $this->hmw->getUser($line->user);
						$email 				= array();
						$cc 				= (!empty($line->ccemail)) ? $order_email.','.$line->ccemail : $order_email;
						$link 				= 'http://'.$server_name.'/order/confirm/'.$line->key;
						$date_y 			= '20'.$line->idorder[0].$line->idorder[1];
						$date_m 			= $line->idorder[2].$line->idorder[3];

            $subject = 'Relance de confirmation de commande ' . $line->idorder;

            $msg  = "Bonjour " . $line->name
              . "!\n\nIl y a 1 jour ou plus, nous vous avons envoyé la commande numéro "
              . $line->idorder . " de nouveau en PJ.\n\n"
              . 'Afin de nous assurer de la bonne prise en compte de celle-ci, merci de bien vouloir la valider en cliquant sur ce lien : '
              . $link . "\n\nHave A Nice Karma,\n-- \nHANK - "
              . $user->username . "\nEmail : $order_email \nTel : " . $user->phone;

            $attach = 'orders/' . $date_y . '/' . $date_m . '/' . $line->idorder
              . '_' . $line->name . '.pdf';

            $this->mmail->prepare($subject, $msg)
              ->from($order_email, 'HANK')
              ->toEmail($line->contact_order_email)
              ->cc($cc)
              ->replyTo($order_email)
              ->attach($attach)
              ->send();

						$req_conf = "UPDATE orders_confirm SET `date_sent` = NOW(), status ='chased', count_confirm = count_confirm+1 WHERE `idorder` = $line->idorder";
						$this->db->query($req_conf);
					}
				} else {
						if (!empty($line->contact_order_email) AND $ds <= $dc) {
							$date_to_send = new DateTime('now');
							$mod = "next monday " . $date_sent->format('H:i:s');
							$date_to_send->modify($mod);
							$req_conf = "UPDATE orders_confirm SET `date_sent` = '" . $date_to_send->format('Y-m-d H:i:s') . "' WHERE `idorder` = $line->idorder";
							$this->db->query($req_conf);
						}
				}
			}
		} else {
			echo "Access refused.";
			return;
		}
	}

	private function getOrder($id, $id_bu) {
		$this->db->from('orders')->where('idorder', $id)->where('id_bu', $id_bu);
		$order_rec_res	= $this->db->get() or die($this->mysqli->error);
		return $order_rec_res->row();
	}

	private function getOrderData($id, $id_bu) {
		$this->db->select('r.user, r.id as rec_id, r.data, r.date')->from('orders as r')->where('r.idorder', $id)->where('id_bu', $id_bu);
		$order_rec_res	= $this->db->get() or die($this->mysqli->error);
		$order_rec		= $order_rec_res->row();
		return unserialize($order_rec->data);
	}

}
?>
