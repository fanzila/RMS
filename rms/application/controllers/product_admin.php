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


		$this->load->library('ion_auth');
		$this->load->library('email');
		$this->load->library('hmw');
		$this->hmw->keyLogin();
	}

	public function index($command = null, $page = 1)
	{		
		$id_bu	=  $this->session->all_userdata()['bu_id'];
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
		$cat_id = '';
		$supplier_id = '';
		$products 	 = '';
		$postid = $this->input->post('supplier_id');
		$postfreq = $this->input->post('freq');
		$postcat_id = $this->input->post('pdt_cat_id');
		$postinput_ref = $this->input->post('supplier_reference');
		$postpdt_name = $this->input->post('pdt_name');
		$postpdt_active = $this->input->post('pdt_active');
		$poststock_manage = $this->input->post('stock_manage');
		$postpdt_unit = $this->input->post('pdt_unit');
		$postmanage_only = $this->input->post('managed_only');
		
		$filters = array();
		if($command == null && isset($postid)) $supplier_id = 1;
		if($command == 'filter' && isset($postid)) $filters['p.id_supplier'] = $this->input->post('supplier_id');
		if ($command == 'filter' && isset($postfreq)) $filters['p.freq_inventory'] = $this->input->post('freq');
		if ($command == 'filter' && isset($postcat_id)) $filters['p.id_category'] = $this->input->post('pdt_cat_id');
		if ($command == 'filter' && isset($postinput_ref)) $filters['p.supplier_reference'] = $this->input->post('supplier_reference');
		if ($command == 'filter' && isset($postpdt_name)) $filters['p.name'] = $this->input->post('pdt_name');
		if ($command == 'filter' && isset($poststock_manage)) $filters['p.manage_stock'] = $this->input->post('stock_manage');
		if ($command == 'filter' && isset($postpdt_active)) $filters['p.active'] = $this->input->post('pdt_active');
		if ($command == 'filter' && isset($postpdt_unit)) $filters['p.id_unit'] = $this->input->post('pdt_unit');
		if ($postmanage_only == 'on') {
			$filters['manage_stock'] = '1';
		}
		if (!empty($filters)) {
			$products = $this->product->getProductsWithFilters($product_id, null, $id_bu, $filters, $page);
		} else if (!empty($product_id)) {
			$products = $this->product->getProducts($product_id, $supplier_id, null, null, $id_bu);
		}
		
		$total_products = $this->product->countProductsWithFilters($product_id, null, $id_bu, $filters);
		$suppliers 			= $this->product->getSuppliers(null, null, $id_bu);
		$products_unit 		= $this->product->getProductUnit();
		$products_category 	= $this->product->getProductCategory();

		$data = array(
			'msg'				=> $msg,
			'command'			=> $command,
			'products'			=> $products,
			'suppliers'			=> $suppliers,
			'supplier_id'		=> $postid,
			'products_unit' 	=> $products_unit,
			'products_category' => $products_category,
			'freq' => $postfreq,
			'cat_id' => $postcat_id,
			'input_ref' => $postinput_ref,
			'pdt_name' => $postpdt_name,
			'pdt_active' => $postpdt_active,
			'stock_manage' => $poststock_manage,
			'pdt_unit' => $postpdt_unit,
			'managed_only' => $postmanage_only,
			'current_page' => $page,
			'total_products' => $total_products
			);
		$data['bu_name'] =  $this->session->all_userdata()['bu_name'];
		$data['username'] = $this->session->all_userdata()['identity'];

		if($command!='create'){
			$headers = $this->hmw->headerVars(0, "/order/", "Product Admin");
			$this->load->view('jq_header_pre', $headers['header_pre']);
			$this->load->view('jq_header_post', $headers['header_post']);
			$this->load->view('product/admin',$data);
			$this->load->view('jq_footer');
		}else{
			$headers = $this->hmw->headerVars(0, "/product_admin/", "Product Admin");
			$this->load->view('jq_header_pre', $headers['header_pre']);
			$this->load->view('jq_header_post', $headers['header_post']);
			$this->load->view('product/admin',$data);
			$this->load->view('jq_footer');
		}

	}

	public function save()
	{
		$this->load->library('product');
		$id_bu =  $this->session->all_userdata()['bu_id'];
		$reponse = 'ok';
		$data = $this->input->post();
		if(empty($data['id'])) exit();
		$price = $data['price']*1000;
		$user = $this->ion_auth->user()->row();
		date_default_timezone_set('Europe/Paris');
		$date = date('Y-m-d H:i:s');

		$this->db->select('name, id_supplier');
		$this->db->from('products');
		$this->db->where('name', addslashes($data['name']));
		$this->db->where('id_supplier', $data['id_supplier']);
		$res = $this->db->get() or die($this->mysqli->error);
		$test = $res->result();

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
		$this->db->set('manage_stock', $data['manage_stock']);

		$this->db->trans_start();
		if($data['id'] == 'create') {

			$reponse = 'okcreate';

			if(isset($test[0])){
				$test=1;
			}
			if($test != 1){
				if(!$this->db->insert('products')) {
					$response = "Can't place the insert sql request, error message: ".$this->db->_error_message();
				}
				$new_id = $this->db->insert_id();
				$id_product = $new_id;
				$previous_stock = 0;
				$this->db->set('id_product', $id_product);
				$this->db->set('warning', $data['stock_warning']);
				$this->db->set('mini', $data['stock_mini']);
				$this->db->set('max', $data['stock_max']);
				$this->db->set('qtty', $data['stock_qtty']);
				$this->db->set('last_update_id_user', $user->id);
				$this->db->set('last_update_user', $date);
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
			$pdt_info = $this->product->getProducts($id_product, null, null, null, $id_bu);
			$previous_stock = $pdt_info[$id_product]['stock_qtty'];
			
			$this->db->set('warning', $data['stock_warning']);
			$this->db->set('mini', $data['stock_mini']);
			$this->db->set('max', $data['stock_max']);
			$this->db->set('qtty', $data['stock_qtty']);
			$this->db->set('last_update_id_user', $user->id);
			$this->db->set('last_update_user', $date);					
			$this->db->where('id_product', $id_product);
			if(!$this->db->update("products_stock")) {
				$response = "Can't place the update sql request, error message: ".$this->db->_error_message();
			}
		}
		
		$this->db->trans_complete();
		
		$p = array(
			'type' => 'stock_product_admin', 
			'val1' => "$id_product",
			'val2' => "$data[stock_qtty]",
			'val4' => "$previous_stock"
			);
		$this->hmw->LogRecord($p);
		
		if ($this->product->isManaged($id_product)) {
			
			$historyEntry = array(
				'id_user' => $user->id,
				'id_product' => $id_product,
				'date_inv' => $date,
				'stock_theorical' => $previous_stock,
				'stock_real' => $data['stock_qtty']
			);
			if (!$this->db->insert('stock_history', $historyEntry)) {
				$response = "Can't place the insert sql request, error message: ".$this->db->_error_message();
			}
			$sql = "DELETE FROM stock_history WHERE id_product = $id_product AND
							id NOT IN
							(SELECT * FROM
							(SELECT id FROM stock_history order by id desc limit 100)
 							as temp)";
			$this->db->query($sql);
		}
		
		echo json_encode(['reponse' => $reponse]);
		exit();
	}


	public function tableProductHistory($pdt_id)
	{
		$this->load->library('product');
		$hist = $this->product->getProductHistory($pdt_id);
		foreach ($hist as $historyline) { 
				$delta = $historyline['stock_real'] - $historyline['stock_theorical']; 
		 echo "<tr>
		 <td>" . $historyline['id'] . "</td>
		 <td>" . $historyline['username'] . "</td>
		 <td>" . $historyline['name'] . "</td>
		 <td>" . $historyline['date_inv'] . "</td>
		 <td>" . $historyline['stock_theorical'] . "</td>
		 <td>" . $historyline['stock_real'] . "</td>
		 <td>" . $delta . "</td>
		 </tr>";
		 }
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
		$products_pos	= $this->product->getPosProducts($id_bu);
		$products 		= $this->product->getManagedProducts(null, null, 'p.name', null, $id_bu, 1);
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
