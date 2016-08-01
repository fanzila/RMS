<?php
class News extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->database();
		$this->load->model('news_model');
		$this->load->helper('url_helper');
		$this->load->library("pagination");
		$this->load->helper("url");
		$this->load->library('ion_auth');
		$this->load->library("hmw");
		$this->load->helper('html');
	}

	public function view($slug = NULL)
	{
		$data['news_item'] = $this->news_model->get_news($slug);

		if (empty($data['news_item']))
		{
			show_404();
		}

		$header['title'] = $data['news_item']['title'];

		$this->load->view('jq_header_pre', $header);
		$this->load->view('news/jq_header_spe');
		$this->load->view('jq_header_post');
		$this->load->view('news/view', $data);
		$this->load->view('jq_footer');
	}

	public function index($login=null)
	{
		if (!$this->ion_auth->logged_in())
		{
			redirect('auth/login');
		}
		$bu_test = $this->session->all_userdata()['bu_name'];
		$this->hmw->changeBu();// GENERIC changement de Bu
		if($bu_test != $this->session->all_userdata()['bu_name'] && $login!=1){
			redirect('news');
		}
		$this->hmw->keyLogin();
		
		$user					= $this->ion_auth->user()->row();
		$user_groups 			= $this->ion_auth->get_users_groups()->result();
		$bus_list = $this->hmw->getBus(null, $user->id);
				
		$config = array();
		$config["base_url"] = base_url() . "news/index";
		$config["total_rows"] = $this->news_model->record_count();
		$config["per_page"] = 10;
		$config["uri_segment"] = 3;
		$choice = $config["total_rows"] / $config["per_page"];
		$config["num_links"] = round($choice);

		$this->pagination->initialize($config);

		$page = ($this->uri->segment(3)) ? $this->uri->segment(3) : 0;
		
		$data = array(
			'username'	=> $user->username,
			'user_groups'	=> $user_groups[0],
			'title'		=> 'News',
			'keylogin'	=> $this->session->userdata('keylogin'),
			'results'	=> $this->news_model->get_list($config["per_page"], $page),
			'links'		=> $this->pagination->create_links(),
			'login'		=> $login,
			'bu_name'	=> $this->session->all_userdata()['bu_name']
			);

		$headers = $this->hmw->headerVars(1, "/news/index/", "News");
		$this->load->view('jq_header_pre', $headers['header_pre']);
		$this->load->view('jq_header_post', $headers['header_post']);
		$this->load->view('news/index',$data);
		$this->load->view('jq_footer');
	}

	public function create()
	{

		$error=0;
		if (!$this->ion_auth->logged_in())
		{
			redirect('auth/login');
		}

		$group_info = $this->ion_auth_model->get_users_groups()->result();
		if ($group_info[0]->level < 1)
		{
			$this->session->set_flashdata('message', 'You must be a gangsta to view this page');
			redirect('/news/');
		}

		$user = $this->ion_auth->user()->row();

		$bus_list = $this->hmw->getBus(null, $user->id);
		
		$this->load->helper('form');
		
		$headers = $this->hmw->headerVars(0, "/news/index/", "Create News");
		$error = array('error' => "");
		if (!$this->input->post('title'))
		{
			$this->load->view('jq_header_pre', $headers['header_post']);
			$this->load->view('news/jq_header_spe');
			$this->load->view('jq_header_post', $headers['header_post']);
			$this->load->view('news/create', $error);
			$this->load->view('jq_footer');
		}else{
			$config['upload_path'] = './public/pictures';
			$config['allowed_types'] = 'gif|jpg|png';
			$config['max_size']	= '2000';
			$this->load->library('upload', $config);
			$checkUpload = $this->upload->do_upload();
			if ( ! $checkUpload){

				$error = array('error' => $this->upload->display_errors());
				$checkError = array('error' => '<p>You did not select a file to upload.</p>');
				if($error != $checkError){
					//a rendre invisible pour "You did not select a file to upload"
					$this->load->view('jq_header_pre', $headers['header_post']);
					$this->load->view('news/jq_header_spe');
					$this->load->view('jq_header_post', $headers['header_post']);
					$this->load->view('news/create', $error);
					$this->load->view('jq_footer');
				}else{
					$error=0;
				}

			}else{
				$data = $this->upload->data();
				$picName = $data['file_name'];
			}


			$news_id = $this->news_model->set_news($user->id);
			if ($checkUpload){
				$this->db->set('picture', $picName)->where('id', $news_id);
				$this->db->update('news');
			}

			$server_name = $this->hmw->getParam('server_name'); 
			
			$this->load->library('mmail');
			$bus = $this->input->post('bus');
			
			$this->db->select('users.username, users.email, users.id');
			$this->db->distinct('users.username');
			$this->db->join('users_bus', 'users.id = users_bus.user_id', 'left');
			$this->db->join('users_groups', 'users.id = users_groups.user_id', 'left');
			$this->db->join('groups', 'groups.id = users_groups.group_id', 'left');
			$this->db->where('users.active', 1);
			$this->db->where('groups.name !=', 'extra');			
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

			$this->load->view('jq_header_pre', $headers['header_pre']);
			$this->load->view('news/jq_header_spe');
			$this->load->view('jq_header_post', $headers['header_post']);
			$this->load->view('news/success');
			$this->load->view('jq_footer');
		}
	}

	public function confirm($key = null) {

		$this->load->library('mmail');

		$this->db->join('users', 'news_confirm.id_user = users.id');
		$this->db->limit(1) or die($this->mysqli->error);				
		$res = $this->db->get_where('news_confirm', array('key' => $key));
		$ret = $res->result_array();
		$ip  = $_SERVER['REMOTE_ADDR'];
		
		if(isset($ret[0]['id'])) {
			$res_sup = $this->db->get_where('news', array('id' => $ret[0]['id_news'])) or die($this->mysqli->error);
			$ret_sup = $res_sup->result_array();
			
			$this->db->set('date_confirmed', "NOW()")
			->set('status', 'confirmed')
			->set('IP', $ip)
			->where('key', $key);
			$this->db->update('news_confirm') or die($this->mysqli->error);
			
			$data = array('status' => 'OK');
		} else {
			$data = array('status' => 'NOK');	
		}

		$this->load->view('news/confirm',$data);
	}

}
?>
