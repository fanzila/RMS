<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Product_admin extends CI_Controller {

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

		$this->load->library('email');
		$this->load->library('hmw');
		$this->hmw->keyLogin();
		$this->load->database();
	}

	public function index($command = null)
	{		
		$this->load->library('product');
		$product_id = null;
		$msg = null;
		if($command=="create1") {
			$msg = "RECORDED ON: ".date('Y-m-d H:i:s');
			$command = "create";
		}
		if(isset($_GET['id_product'])) {
			$product_id = $_GET['id_product']; 
			$command = 'filter';
		}
		$id_bu			=  $this->session->all_userdata()['bu_id'];

		
		$supplier_id = '';
		$products 	 = '';
		$postid = $this->input->post('supplier_id');
		if($command == null && isset($postid)) $supplier_id = 1;
		if($command == 'filter' && isset($postid)) $supplier_id = $this->input->post('supplier_id');
		if(!empty($supplier_id) OR !empty($product_id) ) $products = $this->product->getProducts($product_id, $supplier_id, null, null, $id_bu);
		
		$suppliers 			= $this->product->getSuppliers(null, null, $id_bu);
		$products_unit 		= $this->product->getProductUnit();
		$products_category 	= $this->product->getProductCategory();


		$data = array(
			'msg'				=> $msg,
			'command'			=> $command,
			'products'			=> $products,
			'suppliers'			=> $suppliers,
			'supplier_id'		=> $supplier_id,
			'products_unit' 	=> $products_unit,
			'products_category' => $products_category
			);
		$data['bu_name'] =  $this->session->all_userdata()['bu_name'];
		$data['username'] = $this->session->all_userdata()['identity'];
		
		$headers = $this->hmw->headerVars(0, "/order/", "Product Admin");
		$this->load->view('jq_header_pre', $headers['header_pre']);
		$this->load->view('jq_header_post', $headers['header_post']);
		$this->load->view('product/admin',$data);
		$this->load->view('jq_footer');
	}

//Old save function (in case there are undetected proble in the new one)	
/*	public function savebkp()
	{

		$this->load->library('ion_auth');
		$id_bu =  $this->session->all_userdata()['bu_id'];
		
		$data = $this->input->post();
		$sqlt = "UPDATE ";
		$sqle = " WHERE `id` = $data[id]";
		$reponse = 'ok';

		if($data['id'] == 'create') {
			$sqlt = "INSERT INTO ";
			$sqle = "";
		}

		if(empty($data['id'])) exit();

		$price = $data['price']*1000;

		$sql = "$sqlt products SET
			name = '".addslashes($data['name'])."',
			id_supplier = '".$data['id_supplier']."',
			price = '".$price."',
			id_unit = '".$data['id_unit']."',
			packaging = '".$data['packaging']."',
			id_category = '".$data['id_category']."',
			active = '".$data['active']."',
			freq_inventory = '".$data['freq_inventory']."',
			supplier_reference = '".$data['supplier_reference']."',
			comment = '".addslashes($data['comment'])."'
			$sqle";

		$req = $this->db->query($sql) or die($this->mysqli->error);
		if($data['id'] == 'create') $new_id = $this->db->insert_id();
		
			$user = $this->ion_auth->user()->row();
			
			if($data['id'] == 'create') $id_product = $new_id;
			if($data['id'] != 'create') $id_product = $data['id'];
			
			$sqlins = "INSERT INTO products_stock SET id_product = $id_product, warning = '$data[stock_warning]', mini = '$data[stock_mini]', max = '$data[stock_max]', qtty = '$data[stock_qtty]', last_update_id_user = $user->id, last_update_user = NOW(), id_bu = $id_bu";

			if($data['id'] == 'create') {
				$this->db->query($sqlins) or die($this->mysqli->error);		
			} else {
				$reqs = $this->db->query("SELECT * FROM products_stock WHERE `id_product` = $data[id]") or die($this->mysqli->error);
				$rets = $reqs->result_array();
				if(empty($rets)) {
					$this->db->query($sqlins) or die($this->mysqli->error);
				} else {
					$this->db->query("UPDATE products_stock SET warning = '$data[stock_warning]', mini = '$data[stock_mini]', max = '$data[stock_max]', qtty = '$data[stock_qtty]', last_update_id_user = $user->id, last_update_user = NOW() WHERE id_product = $id_product") or die($this->mysqli->error);
				}
		}
	

		echo json_encode(['reponse' => $reponse]);
		exit();
	}*/

	public function save()
	{
		$this->load->library('ion_auth');
		$id_bu =  $this->session->all_userdata()['bu_id'];
		$reponse = 'ok';
		$data = $this->input->post();
		if(empty($data['id'])) exit();
		$price = $data['price']*1000;
		$user = $this->ion_auth->user()->row();
		date_default_timezone_set('Europe/Paris');
		$date = date('Y-m-d H:i:s');

		$this->db->set('name', addslashes($data['name']));
		$this->db->set('id_supplier', $data['id_supplier']);
		$this->db->set('price', $price);
		$this->db->set('id_unit', $data['id_unit']);
		$this->db->set('packaging', $data['packaging']);
		$this->db->set('id_category', $data['id_category']);
		$this->db->set('active', $data['active']);
		$this->db->set('freq_inventory', $data['freq_inventory']);
		$this->db->set('supplier_reference', $data['supplier_reference']);
		$this->db->set('comment', addslashes($data['comment']));

		$this->db->trans_start();
		if($data['id'] == 'create') {
			$reponse = 'okcreate';
			$this->db->select('name, id_supplier');
			$this->db->from('products');
			$this->db->where('name', addslashes($data['name']));
			$this->db->where('id_supplier', $data['id_supplier']);
			$res = $this->db->get() or die($this->mysqli->error);
			$test = $res->result();
			if(isset($test[0])){
				echo json_encode(['reponse' => 'The product already is in the database']);
				exit();
			}
			if(1){
				if(!$this->db->insert('products')) {
					$response = "Can't place the insert sql request, error message: ".$this->db->_error_message();
				}
				$new_id = $this->db->insert_id();
				$id_product = $new_id;

				$this->db->set('id_product', $id_product);
				$this->db->set('warning', $data['stock_warning']);
				$this->db->set('mini', $data['stock_mini']);
				$this->db->set('max', $data['stock_max']);
				$this->db->set('qtty', $data['stock_qtty']);
				$this->db->set('last_update_id_user', $user->id);
				$this->db->set('last_update_user', $date);
				$this->db->set('id_bu', $id_bu);
				if(!$this->db->insert('products_stock')) {
					$response = "Can't place the insert sql request, error message: ".$this->db->_error_message();
				}
			}else{
				$reponse = 'The product already is in the database';
			}
		}else{
			$this->db->where('id', $data['id']);
			if(!$this->db->update('products')) {
				$response = "Can't place the insert sql request, error message: ".$this->db->_error_message();
			}
			$id_product = $data['id'];

			$this->db->where('id_product', $data['id']);
			$reqs = $this->db->get('products_stock') or die($this->mysqli->error);
			$rets = $reqs->result_array();
				if(empty($rets)) {
					$this->db->set('id_product', $id_product);
					$this->db->set('warning', $data['stock_warning']);
					$this->db->set('mini', $data['stock_mini']);
					$this->db->set('max', $data['stock_max']);
					$this->db->set('qtty', $data['stock_qtty']);
					$this->db->set('last_update_id_user', $user->id);
					$this->db->set('last_update_user', $date);
					$this->db->set('id_bu', $id_bu);
					if(!$this->db->insert('products_stock')) {
						$response = "Can't place the insert sql request, error message: ".$this->db->_error_message();
					}
				} else {
					$this->db->set('warning', $data['stock_warning']);
					$this->db->set('mini', $data['stock_mini']);
					$this->db->set('max', $data['stock_max']);
					$this->db->set('qtty', $data['stock_qtty']);
					$this->db->set('last_update_id_user', $user->id);
					$this->db->set('last_update_user', $date);					
					$this->db->where('id_product', $id_product);
					if(!$this->db->update("products_stock")) {
						$response = "Can't place the insert sql request, error message: ".$this->db->_error_message();
					}
				}
		}
		$this->db->trans_complete();
		echo json_encode(['reponse' => $reponse]);
		exit();
	}


	public function save_mapping()
	{		
		$this->load->library('product');
		$post 			= $this->input->post();
		$tab 			= array();
		$reponse 		= 'ok';
		$id_bu			=  $this->session->all_userdata()['bu_id'];
		
		foreach ($post as $key => $var) {
			$ex 		= explode('_',$key);
			$x 			= $ex[2];
			$id_pos		 = $ex[1];
			$type 		= $ex[0];
			if($type == 'product') $tab[$x]['id_pos'] = $id_pos;
			if($type == 'product') $tab[$x]['id_product'] = $var;
			if($type == 'coef') $tab[$x]['coef'] = $var;	
		}

		$this->db->query("BEGIN") or die($this->mysqli->error);
		foreach ($tab as $key) {
			$this->db->query("DELETE FROM products_mapping WHERE id_pos = '$key[id_pos]'") or die($this->mysqli->error);
		}

		foreach ($tab as $key) {
			if(!empty($key['coef']) AND !empty($key['id_product'])) {	
				$q = "INSERT INTO products_mapping SET id_pos = '$key[id_pos]', id_product='$key[id_product]', coef='$key[coef]', id_bu=$id_bu";	
				$this->db->query($q) or die($this->mysqli->error);
			}
		}
		$this->db->query("COMMIT") or die($this->mysqli->error);
		
		echo json_encode(['reponse' => $reponse]);
		exit();
	}
	
	public function mapping()
	{		
		$this->load->library('product');
		$id_bu			=  $this->session->all_userdata()['bu_id'];
		$products_pos	= $this->product->getPosProducts();
		$products 		= $this->product->getProducts(null, null, 'p.name', null, $id_bu);
		$mapping		= $this->product->getMapping($id_bu);
		$data = array(
			'products_pos'		=> $products_pos,
			'products'			=> $products,
			'mapping'			=> $mapping
			);
		$data['bu_name'] =  $this->session->all_userdata()['bu_name'];
		$data['username'] = $this->session->all_userdata()['identity'];

	 	$headers = $this->hmw->headerVars(0, "/order/", "Product Mapping");
		$this->load->view('jq_header_pre', $headers['header_pre']);
		$this->load->view('jq_header_post', $headers['header_post']);
		$this->load->view('product/mapping',$data);
		$this->load->view('jq_footer');
	}

}
