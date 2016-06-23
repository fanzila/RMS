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
		$id_bu			=  $this->session->all_userdata()['bu_id'];
		
		$supplier_id = '';
		$postid = $this->input->post('supplier_id');
		if($command == null && isset($postid)) $supplier_id = 1;
		if($command == 'filter' && isset($postid)) $supplier_id = $this->input->post('supplier_id');
		$products 			= $this->product->getProducts($product_id, $supplier_id, null, null, $id_bu);
		$suppliers 			= $this->product->getSuppliers(null, null, $id_bu);
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
		$data['bu_name'] =  $this->session->all_userdata()['bu_name'];
		$data['username'] = $this->session->all_userdata()['identity'];
		
		$this->load->view('product/admin',$data);
	}
	
	public function save()
	{

		$this->load->library('ion_auth');
		$data = $this->input->post();
		$reponse = 'ok';

		if(empty($data['id'])) exit();

		$price = $data['price']*1000;
		$this->db->set('name', addslashes($data['name']))->set('id_supplier', $data['id_supplier'])->set('price', $price)->set('id_unit', $data['id_unit'])->set('packaging', $data['packaging'])->set('id_category', $data['id_category'])->set('active', $data['active'])->set('freq_inventory', $data['freq_inventory'])->set('supplier_reference', $data['supplier_reference'])->set('comment', addslashes($data['comment']));
		
		if($data['id'] == 'create') {
			$this->db->insert('products') or die($this->mysqli->error);
			$sqle = "";
		}else{
			$this->db->where('id', $data['id']);
			$this->db->update('products') or die($this->mysqli->error);
		}
		if($data['id'] == 'create') $new_id = $this->db->insert_id();
		
			$user = $this->ion_auth->user()->row();
			
			if($data['id'] == 'create') $id_product = $new_id;
			if($data['id'] != 'create') $id_product = $data['id'];
			if($data['id'] == 'create') {
				$this->db->set('id_product', $id_product)->set('warning', $data['stock_warning'])->set('mini', $data['stock_mini'])->set('max', $data['stock_max'])->set('qtty', $data['stock_qtty'])->set('last_update_id_user', $user->id)->set('last_update_user', "NOW()", FALSE);
				$this->db->insert('products_stock') or die($this->mysqli->error);		
			} else {
				$this->db->from('products_stock')->where('id_product', $data['id']);
				$reqs = $this->db->get() or die($this->mysqli->error);
				$rets = $reqs->result_array();
				if(empty($rets)) {
					$this->db->set('id_product', $id_product)->set('warning', $data['stock_warning'])->set('mini', $data['stock_mini'])->set('max', $data['stock_max'])->set('qtty', $data['stock_qtty'])->set('last_update_id_user', $user->id)->set('last_update_user', "NOW()", FALSE);
					$this->db->insert('products_stock') or die($this->mysqli->error);
				} else {
					$this->db->set('warning', $data['stock_warning'])->set('mini', $data['stock_mini'])->set('max', $data['stock_max'])->set('qtty', $data['stock_qtty'])->set('last_update_id_user', $user->id)->set('last_update_user', "NOW()", FALSE)->where('id_product', $id_product);
					$this->db->update('products_stock') or die($this->mysqli->error);
				}
		}

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

		$this->db->trans_begin() or die($this->mysqli->error);
		foreach ($tab as $key) {
			$this->db->where('id_pos', $key['id_pos']);
			$this->db->delete('products_mapping') or die($this->mysqli->error);
		}

		foreach ($tab as $key) {
			if(!empty($key['coef']) AND !empty($key['id_product'])) {
				$this->db->set('id_pos', $key['id_pos'])
					->set('id_product', $key['id_product'])
					->set('coef',$key['coef'])
					->set('id_bu', $id_bu);	
				$this->db->insert('products_mapping') or die($this->mysqli->error);
			}
		}
		$this->db->trans_commit() or die($this->mysqli->error);
		
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
			
		$this->load->view('product/mapping',$data);
	}

}
