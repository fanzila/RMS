<?php

class Rmd extends CI_Controller  {

	public function __construct()
	{
		parent::__construct();
		$CI =& get_instance();
		$CI->load->database();
		$CI->load->library('ion_auth');
		$CI->load->library('hmw');
		
	}
	
	public function getAllTasks($id_bu)
	{
		$CI =& get_instance();
		
		$CI->db->select('T.id as tid, T.task as ttask, T.comment as tcomment, T.active as tactive, T.priority as tpriority, N.start as nstart, N.end as nend, N.interval as ninterval, N.last as nlast, M.start as mstart, repeat_interval, repeat_year, repeat_month, repeat_day, repeat_week, repeat_weekday');
		$CI->db->from('rmd_tasks as T');
		$CI->db->join('rmd_notif as N', 'N.id_task = T.id');
		$CI->db->join('rmd_meta as M', 'M.id_task = T.id');
		$CI->db->where('T.id_bu', $id_bu);
		$CI->db->order_by('T.id asc');
		$query	= $CI->db->get();
		return $query->result();
	}
			
	public function getTasks($task_id = null, $view = null, $id_bu)
	{

		$CI =& get_instance();
		
		date_default_timezone_set('Europe/Paris');

		$now = strtotime("+0 day");
		$year = date("Y", $now);
		$month = date("m", $now);
		$day = date("d", $now);
		$nowtime = date('Y-m-d H:i:s'); //$year . "-" . $month . "-" . $day;
		$nowdate = date('Y-m-d'); //$year . "-" . $month . "-" . $day;
		$week = (int) ((date('d', $now) - 1) / 7) + 1; //num week of this month
		$weekday = date("N", $now); //day num in week (0 = sunday ...) 

		$CI->db->select('T.task as task, T.comment as comment, T.id as id, M.start as start, T.priority as priority, M.repeat_interval as `interval`, M.repeat_year, M.repeat_month, M.repeat_day, M.repeat_week, M.repeat_weekday, ROUND((UNIX_TIMESTAMP() - (UNIX_TIMESTAMP(start)+repeat_interval))/86400) as overdue')->from('rmd_tasks as T')->join('rmd_meta as M','M.id_task = T.id','right')->where('T.active', 1)->where('T.id_bu', $id_bu);
		if($view != 'all') $CI->db->where_not_in('T.id', "(SELECT id_task FROM rmd_log as L WHERE DATE(L.`date`), DATE(NOW()))->group_by('L.id_task'))");
		if($task_id > 0) $CI->db->where('id_task', $task_id);
		
		if($view != 'all') $CI->db->where("( DATE_ADD(TIMESTAMP(M.`start`), INTERVAL M.`repeat_interval` SECOND) <= TIMESTAMP('$nowtime') )
		OR ( (`repeat_year` = $year OR `repeat_year` = '*' ) AND (`repeat_month` = $month OR `repeat_month` = '*' ) AND (`repeat_day` = $day OR `repeat_day` = '*' ) AND (`repeat_week` = $week OR `repeat_week` = '*' ) AND (`repeat_weekday` = $weekday OR `repeat_weekday` = '*') AND (`start` >= DATE('$nowtime')) )");
	
		$CI->db->order_by('overdue', 'asc');
		$query	= $CI->db->get();
		
		return $query->result();
	}
	
}
?>
