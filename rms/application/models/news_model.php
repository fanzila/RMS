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

		$this->db->select('news.id, users.username, news.title, news.slug, news.text, news.date, news.id_user');
		$this->db->limit($limit, $start);
		$this->db->order_by("news.id", "desc");
		$this->db->join('users', 'users.id = news.id_user', 'left');
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
		return $this->db->count_all('news');
	}

	public function set_news($id_user)
	{
		$this->load->helper('url');

		$slug = url_title($this->input->post('title'), 'dash', TRUE);

		$data = array(
			'title' => $this->input->post('title'),
			'slug' => $slug,
			'text' => $this->input->post('text'),
			'id_user' => $id_user
			);

		$this->db->insert('news', $data);
		return $this->db->insert_id();
	}


}
?>