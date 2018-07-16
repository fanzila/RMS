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
		$this->load->library('ion_auth_acl');
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

		$this->hmw->keyLogin();
		
		$bu_test = $this->session->userdata('bu_name');
		$this->hmw->changeBu();// GENERIC changement de Bu
		if($bu_test != $this->session->userdata('bu_name') && $login!='welcome'){
			redirect('news');
		}
		
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
			'bu_name'	=> $this->session->userdata('bu_name')
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
		$this->hmw->isLoggedIn();

		if (!$this->ion_auth_acl->has_permission('create_news')) {
			redirect('/news/');
		}

		$user = $this->ion_auth->user()->row();

		$bus_list = $this->hmw->getBus(null, $user->id);
		
		$this->load->helper('form');
		
		$headers = $this->hmw->headerVars(0, "/news/index/", "Create News");
		$error = array('error' => "");
		if (!$this->input->post('title'))
		{
			$data['user'] = $user->username;
			$data['error'] = $error['error'];
			$this->load->view('jq_header_pre', $headers['header_post']);
			$this->load->view('news/jq_header_spe');
			$this->load->view('jq_header_post', $headers['header_post']);
			$this->load->view('news/create', $data);
			$this->load->view('jq_footer');
		}else{
			$text = $this->input->post('text');
			if (!isset($text) || empty($text)) {
				redirect('/news/create');
			}
			$config['upload_path'] = './public/pictures';
			$config['allowed_types'] = 'gif|jpg|png';
			$config['max_size']	= '2000';
			$this->load->library('upload', $config);
			$checkUpload = $this->upload->do_upload();
			$error = 0;
			if ( ! $checkUpload){

				$error = array('error' => $this->upload->display_errors());
				$checkError = array('error' => '<p>You did not select a file to upload.</p>');
				if($error != $checkError){
					$this->load->view('jq_header_pre', $headers['header_pre']);
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

			if($error==0){
				$news_id = $this->news_model->set_news($user->id);
			}
			if ($checkUpload){
				$this->db->set('picture', $picName)->where('id', $news_id);
				$this->db->update('news');
			}

			if($error==0){
				$server_name = $this->hmw->getParam('server_name');

				$this->load->library('mmail');
				$bus = $this->input->post('bus');
        $bus = array_map(function($bu) {
          return intval($bu);
        }, $bus);

        $subject = 'Hank news! ' . $this->input->post('title');

        $msg = '';

        if ($checkUpload)
          $msg = '<img src="http://' . $server_name . '/public/pictures/' . $picName
            . '" class="img-responsive" style="max-height: 300px; max-width: 300px;" alt="" /><br/><br/>';

        $msg .= $this->input->post('text');

        $this->mmail->prepare($subject, $msg)
          ->from('news@hankrestaurant.com', 'HANK NEWS')
          ->replyTo('news@hankrestaurant.com')
          ->toList('news', $bus)
          ->before(function($config) use ($server_name, $news_id) {
            $this->db->select('id, username');
            $this->db->where('email', $config['email']);
            $result = $this->db->get('users')->result();

            if (empty($result))
              return;

            $user = $result[0];

            $key  = md5(microtime().rand());
            $link = 'http://' . $server_name . '/news/confirm/' . $key;
            $confirm = [
              'key'     => $key,
              'id_user' => $user->id,
              'id_news' => $news_id,
              'status'  => 'sent'
            ];

            $this->db->insert('news_confirm', $confirm);

            $config['body'] .= "\r\n\r\n->>>>Merci de confirmer la lecture de ce message en cliquant ici :";

              if ($config['type'] === 'html')
                $config['body'] .= '<a href="' . $link . '">' . $link
                  . "</a>\r\n-- \r\n" . $user->username;
              else
                $config['body'] .= $link . "\r\n-- \r\n" . $user->username;

            return $config;
          })
          ->send();

				$this->load->view('jq_header_pre', $headers['header_pre']);
				$this->load->view('news/jq_header_spe');
				$this->load->view('jq_header_post', $headers['header_post']);
				$this->load->view('news/success');
				$this->load->view('jq_footer');
			}
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
			
			$this->db->set('date_confirmed', 'NOW()', FALSE)
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
