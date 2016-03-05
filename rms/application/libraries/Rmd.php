<?php

class Rmd extends CI_Controller  {

	public function __construct()
	{
		parent::__construct();
		$CI =& get_instance();
		$CI->load->database();
		
	}
	
	public function getAllTasks()
	{
		$CI =& get_instance();
		
		$sql = "
		SELECT T.`id` AS `tid`, T.`task` AS ttask, T.`comment` AS tcomment, T.`active` AS tactive, T.`priority` AS tpriority, 
		N.`start` AS nstart, N.`end` AS nend, N.`interval` AS ninterval, N.`last` AS nlast, 
		M.`start` AS mstart, repeat_interval, repeat_year, repeat_month, repeat_day, repeat_week, repeat_weekday
		FROM rmd_tasks AS T
		JOIN rmd_notif AS N ON N.`id_task` = T.`id`  
		JOIN rmd_meta AS M ON M.`id_task` = T.`id` 
		ORDER BY T.`id` ASC";
		
		$query	= $CI->db->query($sql);
		return $query->result();
	}
			
	public function getTasks($task_id = null, $view = null)
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

		$sql = "SELECT T.`task` AS task, T.`comment` AS comment, T.`id` AS `id`, M.`start` AS start, T.`priority` AS `priority`, M.`repeat_interval` AS `interval`,
		M.`repeat_year`, M.`repeat_month`, M.`repeat_day`, M.`repeat_week`, M.`repeat_weekday`, 
		ROUND((UNIX_TIMESTAMP() - (UNIX_TIMESTAMP(`start`)+`repeat_interval`))/86400) AS overdue
		FROM rmd_tasks AS T 
		RIGHT JOIN `rmd_meta` AS M ON M.`id_task` = T.`id`
		WHERE T.`active` = 1 ";
		if($view != 'all') $sql .= "AND T.`id` NOT IN (SELECT id_task FROM rmd_log AS L WHERE DATE(L.`date`) = DATE(NOW()) GROUP BY L.`id_task`) "; 
		if($task_id > 0) $sql .= "AND id_task = $task_id ";
		
		if($view != 'all') $sql .="AND ( ( DATE_ADD(TIMESTAMP(M.`start`), INTERVAL M.`repeat_interval` SECOND) <= TIMESTAMP('$nowtime') )
		OR ( (repeat_year = $year OR repeat_year = '*' ) 
			AND (repeat_month = $month OR repeat_month = '*' ) 
			AND (repeat_day = $day OR repeat_day = '*' ) 
			AND (repeat_week = $week OR repeat_week = '*' ) 
			AND (repeat_weekday = $weekday OR repeat_weekday = '*' ) 
			AND start >= DATE('$nowtime') 
		) )";

		$query	= $CI->db->query($sql);

		return $query->result();
	}
	
	public function getParam($param) 
	{
		$CI =& get_instance();
		$req_params = "SELECT `val` FROM params WHERE `key` = '" . $param . "' LIMIT 1 ";
		$res_params = $CI->db->query($req_params);
		$r = $res_params->result();	
		return $r[0]->val;
	}
}
?>
