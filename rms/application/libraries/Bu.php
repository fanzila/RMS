<?php

class Bu
{
  private $id_bu;

  public function __construct($params)
  {
    $this->id_bu = $params['id_bu'];
  }

  public function getInfos()
  {
		$CI = & get_instance();
		$CI->load->database();

    $CI->db->select('*');
    return $CI->db->get('bus')->row();
  }

  public function getUsersToEmail($groups = null)
  {
		$CI = &get_instance();
		$CI->load->database();

    $CI->db->select('users.username, users.email, users.id');
    $CI->db->distinct('users.username');
    $CI->db->join('users_bus', 'users.id = users_bus.user_id', 'left');
    $CI->db->join('users_groups', 'users.id = users_groups.user_id');
    $CI->db->where('users_bus.bu_id', $this->id_bu);
    $CI->db->where_in('users.active', 1);

    if (!empty($groups))
    {
      if (is_array($groups))
        $CI->db->where_in('users_groups.group_id', $groups);
      else
        $CI->db->where('users_groups.group_id', $groups);
    }

    return $CI->db->get('users')->result();
  }
}
