<?php

class Discounts extends CI_Controller  {

	public function __construct()
	{
		parent::__construct();
		$CI =& get_instance();
		$CI->load->database();
		$CI->load->helper(array('form', 'url'));
		$CI->load->library('form_validation');
		
	}
	
	public function getAllPromo($id_bu)
	{
		$CI =& get_instance();
		
		$CI->db->select('T.id as tid, T.nature as tnature, T.id_user as tuser, T.date as tdate, T.deleted as tdel, T.used as tused')
			->from('discount as T')
			->where('T.id_bu', $id_bu)
			->where('T.deleted', 0)
			->order_by('T.date desc');
		$query	= $CI->db->get();
		return $query->result();
	}
			
	public function getPromo($task_id = null, $view = null, $id_bu)
	{

		$CI =& get_instance();
		
		date_default_timezone_set('Europe/Paris');

		$CI->db->select('T.id as tid, T.nature as tnature, T.id_user as tuser, T.date as tdate, T.deleted as tdel, T.used as tused')
			->from('discount as T')
			->where('T.deleted', 0)
			->where('T.id_bu', $id_bu);
		if($task_id > 0) $CI->db->where('id', $task_id);
		
		if($view != 'all') $CI->db->where("DATE(T.date) = DATE(NOW())");
		

		$CI->db->order_by('T.date desc');
		$query	= $CI->db->get();
		return $query->result();
	}
	
}
?>
