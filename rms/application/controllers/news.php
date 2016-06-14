<?php
class News extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model('news_model');
		$this->load->helper('url_helper');
		$this->load->library("pagination");
		$this->load->helper("url");
		$this->load->library('ion_auth');
		$this->load->library("hmw");
	}

	public function view($slug = NULL)
	{
		$data['news_item'] = $this->news_model->get_news($slug);

		if (empty($data['news_item']))
		{
			show_404();
		}

		$data['title'] = $data['news_item']['title'];

		$this->load->view('jq_header', $data);
		$this->load->view('news/view', $data);
		$this->load->view('jq_footer');
	}

	public function index()
	{

		$data = array();
		
		$this->hmw->keyLogin();
		
		$user					= $this->ion_auth->user()->row();
		$user_groups 			= $this->ion_auth->get_users_groups()->result();
		$data['username']		= $user->username;
		$data['user_groups']	= $user_groups[0];

		$data['title'] 			= 'News';
		$config = array();
		$config["base_url"] = base_url() . "news/index";
		$config["total_rows"] = $this->news_model->record_count();
		$config["per_page"] = 10;
		$config["uri_segment"] = 3;
		$choice = $config["total_rows"] / $config["per_page"];
		$config["num_links"] = round($choice);

		$this->pagination->initialize($config);

		$page = ($this->uri->segment(3)) ? $this->uri->segment(3) : 0;

		$data["keylogin"] = $this->session->userdata('keylogin');

		$data["results"] = $this->news_model->get_list($config["per_page"], $page);
		$data["links"] = $this->pagination->create_links();

		$data['bu_name'] =  $this->session->all_userdata()['bu_name'];
		
		$this->load->view('jq_header', $data);
		$this->load->view('news/index', $data);
		$this->load->view('jq_footer');
	}

	public function create()
	{


		if (!$this->ion_auth->logged_in())
		{
			redirect('auth/login');
		}

		$group_info = $this->ion_auth_model->get_users_groups()->result();
		if ($group_info[0]->level < 1)
		{
			$this->session->set_flashdata('message', 'You must be a gangsta to view this page');
			redirect('/admin/');
		}

		$user = $this->ion_auth->user()->row();

		$bus_list = $this->hmw->getBus(null, $user->id);
		
		$this->load->helper('form');

		$data['bu_name'] =  $this->session->all_userdata()['bu_name'];

		$data['title']	= 'Create a news item';
		$data['from']	= $user->username;
		$data['bus_list']	= $bus_list;
		$data['bu_id']	= $this->session->all_userdata()['bu_id'];
		

		if (!$this->input->post('title'))
		{
			$this->load->view('jq_header', $data);
			$this->load->view('news/create');
			$this->load->view('jq_footer');
		}
		else
		{

			$news_id = $this->news_model->set_news($user->id);
			$server_name = $this->hmw->getParam('server_name'); 
			
			$this->load->library('mmail');
			$bus = $this->input->post('bus');
			
			$this->db->select('users.username, users.email, users.id');
			$this->db->distinct('users.username');
			$this->db->join('users_bus', 'users.id = users_bus.user_id', 'left');
			$this->db->where('users.active', 1);
			$this->db->where_in('users_bus.bu_id', $bus);
			
			$query = $this->db->get("users");
			
			foreach ($query->result() as $row) {
				$key 	= md5(microtime().rand());
				$link 	= 'http://'.$server_name.'/news/confirm/'.$key;
				
				$confi = array(
					'key' => $key,
					'id_user' => $row->id,
					'id_news' => $news_id,
					'status' => 'sent'
					);
			
				$this->db->insert('news_confirm', $confi);

				$email['from']		= 'news@hankrestaurant.com';
				$email['from_name']	= 'HANK NEWS';
				$email['to']		= $row->email;
				$email['replyto'] 	= "news@hankrestaurant.com";
				$email['subject']	= 'Hank News! '.$this->input->post('title');
				$email['mailtype']	= 'html';

				$msg = $this->input->post('text');
				$msg .= "\r\n\r\n->>>>Merci de confirmer la lecture de ce message en cliquant ici : $link";
				$msg .= "\r\n-- \r\n$user->username";

				$email['msg'] = $msg;
				
				$this->mmail->sendEmail($email);
			}

			$data['bu_name'] =  $this->session->all_userdata()['bu_name'];
			$data['username'] = $this->session->all_userdata()['identity'];
			
			$data['title'] = 'News';
			$this->load->view('jq_header', $data);
			$this->load->view('news/success');
			$this->load->view('jq_footer');
		}
	}

	public function confirm($key = null) {

		$this->load->library('mmail');
		$this->db->get_where('news_confirm', array('key' => $key))->join('users', 'news_confirm.id_user = users.id')->limit(1);
		$res = $this->db->get() or die($this->mysqli->error);
		$ret = $res->result_array();
		$ip  = $_SERVER['REMOTE_ADDR'];
		
		if(isset($ret[0]['id'])) {
			$this->db->get_where('news', array('id' => $ret[0]['id_news']))->limit(1);
			$res_sup = $this->db->get() or die($this->mysqli->error);
			$ret_sup = $res_sup->result_array();

			$this->db->update('news_confirm')->set('date_confirmed', NOW())->set('status', 'confirmed')->set('IP', $ip);
			$this->db->get() or die($this->mysqli->error);
			$data = array('status' => 'OK');

			if($ret[0]['status'] != 'confirmed') {
				$email['from']		= 'checklist@hankrestaurant.com';
				$email['from_name']	= 'HANK';
				$email['to']		= 'checklist@hankrestaurant.com';
				$email['subject'] 	= "Confirmation de lecture de ".$ret[0]['username'].", News: ".$ret_sup[0]['slug'];
				$email['replyto'] 	= "checklist@hankrestaurant.com";
				$email['msg'] 		= "Bonjour, la news : ".$ret_sup[0]['slug']." à été lu par ".$ret[0]['username'].".";
				$email['msg'] 		.= "\n\nHave A Nice Karma,\n-- \nHANK\n";

				//$this->mmail->sendEmail($email);
			}
		} else {
			$data = array('status' => 'NOK');	
		}

		$this->load->view('news/confirm',$data);
	}

}
?>