<?php

class Product {

	public function getProducts($id = null, $supplier_id = null, $order = null) {

		$CI =& get_instance();
		$sqladd = '';
		if($id) $sqladd = " AND p.id = $id";
		if($supplier_id != null) $sqladd = " AND s.id = $supplier_id";
		$ordersql = "p.`active` DESC"; 
		if($order) $ordersql = $order; 
		
		$req = $CI->db->query("SELECT 
			p.`id`, 
			s.`id` AS supplier_id, 
			p.supplier_reference, 
			pc.name AS category_name, 
			p.name AS name, 
			s.name AS supplier_name, 
			p.price, 
			p.active,
			p.id_category,
			puprc.name AS unit_name, 
			p.id_unit AS id_unit,
			p.packaging AS packaging,
			p.freq_inventory, 
			p.comment, 
			ps.mini AS stock_mini, 
			ps.max AS stock_max, 
			ps.qtty AS stock_qtty, 
			ps.warning AS stock_warning,
			ps.last_update_user AS last_update_user,
			ps.last_update_pos AS last_update_pos,
			u.username AS last_update_user_name
			FROM products AS p 
			JOIN products_category AS pc on p.`id_category` = pc.`id` 
			JOIN suppliers AS s ON p.id_supplier = s.`id` 
			JOIN products_unit AS puprc ON p.id_unit = puprc.`id` 
			LEFT JOIN products_stock AS ps ON p.`id`= ps.id_product 
			LEFT JOIN users AS u ON ps.last_update_id_user = u.id
			WHERE p.deleted=0 $sqladd 
		ORDER BY $ordersql, p.id_supplier ASC LIMIT 10000") or die($this->mysqli->error);

	$ret = array();
	foreach ($req->result_array() as $key) {
		$ret[$key['id']] = $key;
	}

	return $ret;
}

public function getPosProducts() {
	$CI =& get_instance();
	$req = $CI->db->query("SELECT * FROM sales_product WHERE deleted = 0") or die($this->mysqli->error);
	$ret = array();
	foreach ($req->result_array() as $key) {
		$ret[$key['id']] = $key;
	}
	return $ret;
}

public function getAttributName($id) {
	$CI =& get_instance();
	$req = $CI->db->query("SELECT name FROM products_attribut WHERE id = $id LIMIT 1") or die($this->mysqli->error);
	$ret = $req->result_array();
	return $ret[0]['name'];
}

public function getAttributs() {
	$CI =& get_instance();
	$req = $CI->db->query("SELECT * FROM products_attribut") or die($this->mysqli->error);
	$ret = array();
	foreach ($req->result_array() as $key) {
		$ret[$key['id']] = $key;
	}
	return $ret;
}

public function getMapping() {
	$CI =& get_instance();
	$req = $CI->db->query("SELECT * FROM products_mapping") or die($this->mysqli->error);
	$ret = array();
	foreach ($req->result_array() as $key) {
		$ret[$key['id']] = $key;
	}
	return $ret;
}

public function getStock() {
	$CI =& get_instance();
	$req = $CI->db->query("SELECT * FROM products_stock") or die($this->mysqli->error);
	$ret = array();
	foreach ($req->result_array() as $key) {
		$ret[$key['id_product']] = $key;
	}
	return $ret;
}

public function getSuppliers($order = null) {
	$CI =& get_instance();
	$CI->load->library('hmw');
	
	$q = "SELECT * FROM suppliers WHERE active=1 AND deleted=0 ORDER BY `id` ASC";
	
	if($order) $q = "SELECT s.id, s.name  FROM suppliers AS s LEFT JOIN ( SELECT date, user, status, supplier_id FROM orders WHERE status = 'sent' AND date IS NOT NULL) AS o ON o.supplier_id = s.id WHERE s.active=1 AND s.deleted=0 ORDER BY o.date DESC"; 
	
	$req = $CI->db->query($q) or die($this->mysqli->error);
	$ret = array();
	foreach ($req->result_array() as $key) {
		$reql = $CI->db->query("SELECT `date`, user FROM orders WHERE supplier_id = $key[id] AND status = 'sent' ORDER BY `date` DESC LIMIT 1") or die($this->mysqli->error);
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
	$req = $CI->db->query("SELECT * FROM products_unit") or die($this->mysqli->error);
	$ret = array();
	foreach ($req->result_array() as $key) {
		$ret[$key['id']] = $key;
	}
	return $ret;
}

public function getProductCategory() {
	$CI =& get_instance();
	$req = $CI->db->query("SELECT * FROM products_category WHERE active=1 AND deleted=0 ORDER BY `id` ASC") or die($this->mysqli->error);
	$ret = array();
	foreach ($req->result_array() as $key) {
		$ret[$key['id']] = $key;
	}
	return $ret;
}

public function getSupplierCategory() {
	$CI =& get_instance();
	$req = $CI->db->query("SELECT * FROM suppliers_category WHERE active=1 AND deleted=0 ORDER BY `id` ASC") or die($this->mysqli->error);
	$ret = array();
	foreach ($req->result_array() as $key) {
		$ret[$key['id']] = $key;
	}
	return $ret;
}

}
?>
