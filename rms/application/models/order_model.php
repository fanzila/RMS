<?php
class Order_model extends CI_Model {

	public function __construct()
	{
		$this->load->database();
	}

	public function get_list($limit, $start, $keylogin=null)
	{
		
		$bu_id =  $this->session->all_userdata()['bu_id'];
		
		$this->db->select('r.user, u.username, ur.username as username_reception, u.first_name as first_name, u.last_name as last_name, r.id as lid, r.idorder, r.id, r.date,  r.supplier_id, r.status, r.user_reception, r.date_reception, r.data_reception, r.status_reception, c.status as confirm, s.name as supplier_name');
		$this->db->from('orders as r');
		$this->db->join('users as u', 'r.user = u.id');
		$this->db->join('users as ur', 'r.user_reception = ur.id', 'left');
		$this->db->join('suppliers as s','s.id = r.supplier_id','left');
		$this->db->join('orders_confirm as c','r.idorder = c.idorder','left');
		$this->db->where('r.id_bu', $bu_id);
		$status = array('sent', 'received');
		if($keylogin) $this->db->where_in('r.status', $status);
		$this->db->limit($limit, $start);
		$this->db->order_by('r.date desc');
		$query = $this->db->get() or die($this->mysqli->error);
		$rec = $query->result_array();
		if ($query->num_rows() > 0) {
			return $rec;
		}
		return false;
	}

	public function record_count() {
		$bu_id =  $this->session->all_userdata()['bu_id'];
		$query = $this->db->where('id_bu', $bu_id)->get('orders');
		return $query->num_rows();

	//	return $this->db->count_all('orders');
	}

}
?>