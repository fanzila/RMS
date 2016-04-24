<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once('application/libraries/REST_Controller.php');

class Rest extends REST_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->database();

	}
			
	function shopping_get()
	{

		$data = array();
		
		//get params
		$req_params = "SELECT `key`,`val` FROM params";
		$res_params = $this->db->query($req_params);
		$params = array();
		$ts = time();
		foreach($res_params->result() as $row) {
			$params[$row->key] = [$row->val];
		}
		
		//get products from db
		$req_pdt = "SELECT id,att,type,name,detail,price,image FROM shop_products WHERE status = 1";
		$res_pdt = $this->db->query($req_pdt);
		$products = $res_pdt->result();
	
		$img_path = $params['img_path'][0];		
		$data = array('params' => $params, 'ts' => $ts, 'products' => $products, 'img_path' => $img_path);
		$this->response($data, 200);

	}

	function payment_post()
	{

		if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
			$ip = $_SERVER['HTTP_CLIENT_IP'];
			} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
				$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
			} else {
				$ip = $_SERVER['REMOTE_ADDR'];
			}
			$content = file_get_contents("php://input");
			$postdata = json_decode($content);
			
			$scart = json_encode($postdata->cart);

			if(empty($postdata->info->name) OR empty($postdata->info->phone) OR empty($postdata->info->email) OR empty($scart) OR empty($postdata->info->amount) OR empty($postdata->info->date)) {
				$data = array('tnxid' => "Error, missing data");
				
			} else {

				$data = array(
					'payment_status' => 'INIT' ,
					'name' => $postdata->info->name ,
					'email' => $postdata->info->email,
					'comment' => $postdata->info->comment,
					'IP' => $ip,
					'phone' => $postdata->info->phone,
					'pickup_date' => $postdata->info->date.' '.$postdata->info->time,
					'cart' => $scart,
					'amount' => $postdata->info->amount 
					);

				if (!$this->db->insert('shop_payments', $data)) {
					print("Can't place the insert sql request, error message: ".$this->db->_error_message());
					exit();
				}

				$tnx_id = $this->db->insert_id();
				if ($tnx_id < 0) {
					echo "No transaction id, can't continue.";
					exit();
				}

				$ts = time();
				$data = array('tnxid' => "TNX-".$tnx_id, 'ts' => $ts);
			}

			$this->response($data, 200); // 200 being the HTTP response code
		}
}
?>