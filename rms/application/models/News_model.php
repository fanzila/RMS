<?php
class News_model extends CI_Model {

	public function __construct()
	{
		$this->load->database();
	}

	public function get_news($slug = FALSE)
	{
		if ($slug === FALSE)
		{
			
			$this->db->select('*');
			$this->db->from('news');
			$this->db->join('users', 'users.id = news.id_user');
			$query = $this->db->get();
			
			return $query->result_array();
		}
		
		$this->db->where('news', array('slug' => $slug));

		return $query->row_array();
	}

	public function get_list($limit, $start)
	{
		
		$bu_id =  $this->session->userdata('bu_id');
		
		$this->db->select('news.id as news_id, users.username, news.title, news.slug, news.text, news.picture, news.date, news.id_user, bus.name, bus.id');
		$this->db->where('news_bus.id_bu', $bu_id);
		$this->db->limit($limit, $start);
		$this->db->order_by("news.id", "desc");
		$this->db->join('users', 'users.id = news.id_user', 'left');
		$this->db->join('news_bus', 'news.id = news_bus.id_news');
		$this->db->join('bus', 'news_bus.id_bu = bus.id');
		
		$query = $this->db->get("news");

		if ($query->num_rows() > 0) {
			foreach ($query->result() as $row) {
				$data[] = $row;
			}
			return $data;
		}
		return false;
	}

	public function record_count() {
		$bu_id =  $this->session->userdata('bu_id');
		$query = $this->db->where('id_bu', $bu_id)->get('news_bus');
		return $query->num_rows();

	//	return $this->db->count_all('news');
	}

	public function set_news($id_user)
	{
		$this->load->helper('url');

		$slug = url_title($this->input->post('title'), '-', TRUE);

		$text_modif = str_replace("%", "&#37;", $this->input->post('text'));		
				
		$data = array(
			'title' => $this->input->post('title'),
			'slug' => $slug,
			'text' => $text_modif,
			'id_user' => $id_user
			);
		$this->db->insert('news', $data);
		$last_id = $this->db->insert_id();
		
		$bus = $this->input->post('bus');
		
		foreach ($bus as $row) {
			$data2 = array(
				'id_news' => $last_id, 
				'id_bu' => $row
				);
			$this->db->insert('news_bus', $data2);
		}
	
		return $last_id;
	}


}
?>