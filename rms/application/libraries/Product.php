<?php

class Product {

	public function getProducts($id = null, $supplier_id = null, $order = null, $term = null, $id_bu) {
		$CI =& get_instance();
		$CI->db->select('p.id, s.id as supplier_id, p.supplier_reference, pc.name as category_name, p.name as name, s.name as supplier_name, p.price, p.active, p.id_category, puprc.name as unit_name, p.id_unit as id_unit, p.packaging as packaging, p.freq_inventory, p.comment, ps.mini as stock_mini, ps.max as stock_max, ps.qtty as stock_qtty, ps.warning as stock_warning, ps.last_update_user as last_update_user, ps.last_update_pos as last_update_pos, u.username as last_update_user_name')
		->from('products as p')
		->join('suppliers as s', 'p.id_supplier = s.id')
		->join('products_unit as puprc', 'p.id_unit = puprc.id')
		->join('products_category as pc', 'p.id_category = pc.id', 'left')
		->join('products_stock as ps', 'p.id= ps.id_product', 'left')
		->join('users as u', 'ps.last_update_id_user = u.id', 'left')
		->where('p.deleted', 0);
		if($id) $CI->db->where('p.id', $id);
		if($supplier_id != null) $CI->db->where('s.id', $supplier_id);
		if($term != null) $CI->db->like('p.name', $term, 'both');
		$CI->db->where('s.id_bu', $id_bu);
		if($order){
			$CI->db->order_by('p.id_supplier asc, ".$order."')->limit(10000);
		}else{
			$CI->db->order_by('p.active desc, p.id_supplier asc')->limit(10000);	
		}
		$req = $CI->db->get() or die($this->mysqli->error);
	$ret = array();
	foreach ($req->result_array() as $key) {
		$ret[$key['id']] = $key;
	}

	return $ret;
}

public function getPosProducts() {
	$CI =& get_instance();
	$CI->db->select('*')->from('sales_product')->where('deleted', 0);
	$req = $CI->db->get() or die($this->mysqli->error);
	$ret = array();
	foreach ($req->result_array() as $key) {
		$ret[$key['id']] = $key;
	}
	return $ret;
}

public function getAttributName($id) {
	$CI =& get_instance();
	$CI->db->select('name')->from('products_attribut')->where('id', $id)->limit(1);
	$req = $CI->db->get() or die($this->mysqli->error);
	$ret = $req->result_array();
	return $ret[0]['name'];
}

public function getAttributs() {
	$CI =& get_instance();
	//bug avec la fonction getProducts
	//$CI->db->select('*')->from('products_attribut');
	//$req = $CI->db->get() or die($this->mysqli->error);
	$req = $CI->db->query("SELECT * FROM products_attribut") or die($this->mysqli->error);
	$ret = array();
	foreach ($req->result_array() as $key) {
		$ret[$key['id']] = $key;
	}
	return $ret;
}

public function getMapping($id_bu) {
	$CI =& get_instance();
	$CI->db->select('*')->from('products_mapping')->where('id_bu', $id_bu);
	$req = $CI->db->get() or die($this->mysqli->error);
	$ret = array();
	foreach ($req->result_array() as $key) {
		$ret[$key['id']] = $key;
	}
	return $ret;
}

public function getStock($id_bu) {
	$CI =& get_instance();
	//bug avec la fonction getProducts
	//$CI->db->select('*')->from('products_stock')->where('id_bu', $id_bu);
	//$req = $CI->db->get() or die($this->mysqli->error);
	$req = $CI->db->query("SELECT * FROM products_stock WHERE id_bu = $id_bu") or die($this->mysqli->error);
	$ret = array();
	foreach ($req->result_array() as $key) {
		$ret[$key['id_product']] = $key;
	}
	return $ret;
}

public function getSuppliers($order = null, $idsup = null, $id_bu = null) {
	$CI =& get_instance();
	$CI->load->library('hmw');
if($order)
{
	$CI->db->select('s.id as id, s.name, s.carriage_paid, s.payment_type, s.payment_delay, s.contact_order_name, s.contact_order_tel, s.contact_order_email, s.delivery_days, s.order_method, s.comment_internal, s.comment_order, s.comment_delivery, s.comment_delivery_info')
		->from('suppliers as s')
		->join('(SELECT date, user, status, supplier_id FROM orders WHERE status = "sent" AND date IS NOT null) as o','o.supplier_id = s.id','left')
		->where('s.active', 1)
		->where('s.deleted', 0)
		->where('s.id_bu', $id_bu)
		->order_by('o.date desc');
}else
{
	$CI->db->select('s.id as id, s.name, s.carriage_paid, s.payment_type, s.payment_delay, s.contact_order_name, s.contact_order_tel, s.contact_order_email, s.delivery_days, s.order_method, s.comment_internal, s.comment_order, s.comment_delivery, s.comment_delivery_info')
		->from('suppliers as s')
		->where('active', 1)
		->where('deleted', 0)
		->where('s.id_bu', $id_bu);

	if(!empty($idsup)) $CI->db->where('s.id', $idsup);
	$CI->db->order_by('s.id asc');
}
	$req = $CI->db->get() or die($this->mysqli->error);
	$ret = array();
	foreach ($req->result_array() as $key) {
		//bug d'affichage avec la "solution" proposée. à résoudre plus tard
		$CI->db->select('date, user')->from('orders')->where('supplier_id', $key["id"])->where('status', 'sent')->where('id_bu', $id_bu)->order_by('date desc')->limit(1);
		$reql = $CI->db->get() or die($this->mysqli->error);
		//$reql = $CI->db->query("SELECT `date`, user FROM orders WHERE supplier_id = $key[id] AND status = 'sent' AND id_bu = $id_bu ORDER BY `date` DESC LIMIT 1") or die($this->mysqli->error);
		$rowl = $reql->result_array();
		$ret[$key['id']] = $key;
		if(isset($rowl[0]['date'])) {
			$now = new DateTime();
			$dateBdd = new DateTime($rowl[0]['date']);
			$interval = $dateBdd->diff($now);
			
			$ret[$key['id']]['last_order'] = $interval->format('%h hour(s) and %i minute(s) ago');
			if($dateBdd->diff($now)->days > 0) $ret[$key['id']]['last_order'] = $interval->format('%d day(s) ago');
			
			$ret[$key['id']]['last_order_user'] = $CI->hmw->getUser($rowl[0]['user']);
		}
	}
	return $ret;
}

public function getProductUnit() {
	$CI =& get_instance();
	$CI->db->select('*')->from('products_unit');
	$req = $CI->db->get() or die($this->mysqli->error);
	$ret = array();
	foreach ($req->result_array() as $key) {
		$ret[$key['id']] = $key;
	}
	return $ret;
}

public function getProductCategory() {
	$CI =& get_instance();
	$CI->db->select('*')->from('products_category')->where('active', 1)->where('deleted', 0)->order_by('id asc');
	$req = $CI->db->get() or die($this->mysqli->error);
	$ret = array();
	foreach ($req->result_array() as $key) {
		$ret[$key['id']] = $key;
	}
	return $ret;
}

public function getSupplierCategory() {
	$CI =& get_instance();
	$CI->db->select('*')->from('suppliers_category')->where('active', 1)->where('deleted', 0)->order_by('id asc');
	$req = $CI->db->get() or die($this->mysqli->error);
	$ret = array();
	foreach ($req->result_array() as $key) {
		$ret[$key['id']] = $key;
	}
	return $ret;
}

}
?>
