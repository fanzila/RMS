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
	}

	public function index($command = null)
	{		
		$this->load->library('product');
		$product_id = null;
		
		if(isset($_GET['id_product'])) {
			$product_id = $_GET['id_product']; 
			$command = 'filter';
		}
		
		$supplier_id = '';
		$postid = $this->input->post('supplier_id');
		if($command == null && isset($postid)) $supplier_id = 1;
		if($command == 'filter' && isset($postid)) $supplier_id = $this->input->post('supplier_id');
		$products 			= $this->product->getProducts($product_id, $supplier_id, null);
		$suppliers 			= $this->product->getSuppliers();
		$products_unit 		= $this->product->getProductUnit();
		$products_category 	= $this->product->getProductCategory();


		$data = array(
			'command'			=> $command,
			'products'			=> $products,
			'suppliers'			=> $suppliers,
			'supplier_id'		=> $supplier_id,
			'products_unit' 	=> $products_unit,
			'products_category' => $products_category
			);

		$this->load->view('product/admin',$data);
	}
	
	public function save()
	{

		$this->load->library('ion_auth');
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
			
			$sqlins = "INSERT INTO products_stock SET id_product = $id_product, warning = '$data[stock_warning]', mini = '$data[stock_mini]', max = '$data[stock_max]', qtty = '$data[stock_qtty]', last_update_id_user = $user->id, last_update_user = NOW()";

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
	}

	public function save_mapping()
	{		
		$this->load->library('product');
		$post = $this->input->post();
		$tab = array();
		$reponse = 'ok';
		
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
				$q = "INSERT INTO products_mapping SET id_pos = '$key[id_pos]', id_product='$key[id_product]', coef='$key[coef]'";	
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

		$products_pos	= $this->product->getPosProducts();
		$products 		= $this->product->getProducts(null, null, 'p.name');
		$mapping		= $this->product->getMapping();
		$data = array(
			'products_pos'			=> $products_pos,
			'products'			=> $products,
			'mapping'			=> $mapping
			);
			
		$this->load->view('product/mapping',$data);
	}

}
