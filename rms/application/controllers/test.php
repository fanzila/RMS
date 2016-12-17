<?php
class Test extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->database();
		$this->load->library('ion_auth');
		$this->load->library("hmw");
		$this->load->library("product");
	}

	public function index()
	{
		if (!$this->ion_auth->logged_in())
		{
			redirect('auth/login');
		}

		$active = true; 

		if($active) { 
			echo "<h1>DB TEST</h1>";

			$r_pdt = $this->db->get('products') or die($this->mysqli->error);

			echo "<pre>";
			foreach ($r_pdt->result_array() as $pdt) { 

				//check stock 			
				$qps = "SELECT * FROM products_stock WHERE id_product= $pdt[id]";
				$r_ps = $this->db->query($qps) or die('ERROR '.$this->db->_error_message().error_log('ERROR '.$this->db->_error_message()));
				$res_ps = $r_ps->result_array();
				$stock_num = count($res_ps);

				if(empty($stock_num)) {
echo "
INSERT INTO products_stock SET id_product = $pdt[id];";
				}

				if($stock_num > 1) {
					echo "
						WARNING! STOCK > 1 for $pdt[id]";
				}

			}
			echo "</pre>";
		}

	}
}
?>