<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Auth extends CI_Controller {

	function __construct()
	{
		parent::__construct();
		$this->load->library('ion_auth');
		$this->load->library('ion_auth_acl');
		$this->load->helper('security');
		$this->load->library('form_validation');
		$this->load->helper('url');
		$this->load->library('hmw');
		$this->load->library('wp_rms');
		$this->load->library('mmail');

		$this->load->database();

		$this->form_validation->set_error_delimiters($this->config->item('error_start_delimiter', 'ion_auth'), $this->config->item('error_end_delimiter', 'ion_auth'));

		$this->lang->load('auth');
		$this->load->helper('language');
	}

	//redirect if needed, otherwise display the user list
	function extra()
	{
		$this->hmw->changeBu();// GENERIC changement de Bu

		$this->load->library("hmw");
		$this->load->library('mmail');

		if ($this->hmw->isLoggedIn() == true)
		{
			
			if (!$this->ion_auth_acl->has_permission('extras')) {
				die ('You are not allowed to view this page.');
			}
			
			$txtmessage = $this->input->post('txtmessage');
			$this->data['message']  = '';
			$sento = '';

			if(!empty($txtmessage)) {
				foreach ($this->input->post() as $key => $var) {
					$line = explode('-', $key);
					if($line[0] == 'sms') {
						$userinfo = $this->hmw->getUser($line[1]);
						$sento .= $userinfo->username." by sms at ".$userinfo->phone ."<br/>";
						$this->hmw->sendSms($userinfo->phone, $txtmessage);
					}
					if($line[0] == 'email') {
						$userinfo = $this->hmw->getUser($line[1]);							
						$email['to']	= $userinfo->email;
						$email['subject'] = 'Open shift @Hank!';
						$email['msg'] = $txtmessage;
						$sento .= $userinfo->username." by email at ".$userinfo->email." <br/>";
						$this->mmail->sendEmail($email);
					}
				}
				$this->data['message']  = '<b>Message sent to:</b> <br />'.$sento;
			}

			//list the users
			$this->data['users'] = $this->ion_auth->users()->result();
			foreach ($this->data['users'] as $k => $user)
			{
				$this->data['users'][$k]->bus = $this->ion_auth->get_users_bus($user->id)->result();
				$this->data['users'][$k]->groups = $this->ion_auth->get_users_groups($user->id)->result();
			}

			$this->data['username'] = $this->session->userdata('identity');
			$this->data['bu_name'] =  $this->session->userdata('bu_name');

			$this->data['current_user'] = $this->ion_auth->user()->row();

			$headers = $this->hmw->headerVars(1, "/auth/extra/", "Extra finder");
			$this->load->view('jq_header_pre', $headers['header_pre']);
			$this->load->view('jq_header_post', $headers['header_post']);
			$this->_render_page('auth/extra', $this->data);
			$this->load->view('jq_footer');
		}
	}

	//redirect if needed, otherwise display the user list
	function index()
	{
		$this->hmw->changeBu();// GENERIC changement de Bu

		$group_info		= $this->ion_auth_model->get_users_groups()->result();
		$user_groups	= $this->ion_auth->get_users_groups()->result();

		if ($this->hmw->isLoggedIn() == true) {
			
			if (!$this->ion_auth_acl->has_permission('view_staff')) {
				die ('You are not allowed to view this page.');
			}
			//set the flash data error message if there is one
			$this->data['message'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('message');

			//list the users
			$this->data['users'] = $this->ion_auth->users()->result();
			foreach ($this->data['users'] as $k => $user)
			{
				$this->data['users'][$k]->groups	= $this->ion_auth->get_users_groups($user->id)->result();
				$this->data['users'][$k]->bus 		= $this->ion_auth->get_users_bus($user->id)->result();
			}

			$this->data['username']		= $this->session->userdata('identity');
			$this->data['bu_name']		= $this->session->userdata('bu_name');
			$this->data['current_user'] = $this->ion_auth->user()->row();
			$this->data['user_groups']	= $user_groups[0];

			$headers = $this->hmw->headerVars(1, "/auth/", "Users");
			$this->load->view('jq_header_pre', $headers['header_pre']);
			$this->load->view('jq_header_post', $headers['header_post']);
			$this->_render_page('auth/index', $this->data);
			$this->load->view('jq_footer');
		}
	}

	//log the user in
	function login()
	{
		$this->data['title'] = "Login";

		//validate form input
		$this->form_validation->set_rules('identity', 'Identity', 'required');
		$this->form_validation->set_rules('password', 'Password', 'required');

		if ($this->form_validation->run() == true)
		{
			//check to see if the user is logging in
			//check for "remember me"
			$remember = (bool) $this->input->post('remember');

			if ($this->ion_auth->login($this->input->post('identity'), $this->input->post('password'), $remember))
			{
				//if the login is successful
				//redirect them back to the home page
				$this->session->set_flashdata('message', $this->ion_auth->messages());
				//redirect('/', 'refresh');
				//set BU
				if ($this->session->userdata('pageBeforeLogin') !== NULL) {
					redirect($this->session->userdata('pageBeforeLogin'));
				} else {
					redirect('/news/index/welcome/');
				}
			}
			else
			{
				//if the login was un-successful
				//redirect them back to the login page
				$this->session->set_flashdata('message', $this->ion_auth->errors());
				redirect('auth/login', 'refresh'); //use redirects instead of loading views for compatibility with MY_Controller libraries
			}
		}
		else
		{
			//the user is not logging in so display the login page
			//set the flash data error message if there is one
			$this->data['message'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('message');

			$this->data['identity'] = array('name' => 'identity',
				'id' => 'identity',
				'type' => 'text',
				'data-clear-btn' => "true",
				'value' => $this->form_validation->set_value('identity'),
				);
			$this->data['password'] = array('name' => 'password',
				'id' => 'password',
				'data-clear-btn' => "true",
				'type' => 'password',
				);

			$this->_render_page('auth/login', $this->data);
		}
	}

	//log the user out
	function logout()
	{
		$this->data['title'] = "Logout";

		//log the user out
		$logout = $this->ion_auth->logout();

		//redirect them to the login page
		$this->session->set_flashdata('message', $this->ion_auth->messages());
		redirect('/news/');
	}

	//change password
	function change_password()
	{
		$this->form_validation->set_rules('old', $this->lang->line('change_password_validation_old_password_label'), 'required');
		$this->form_validation->set_rules('new', $this->lang->line('change_password_validation_new_password_label'), 'required|min_length[' . $this->config->item('min_password_length', 'ion_auth') . ']|max_length[' . $this->config->item('max_password_length', 'ion_auth') . ']|matches[new_confirm]');
		$this->form_validation->set_rules('new_confirm', $this->lang->line('change_password_validation_new_password_confirm_label'), 'required');

		$this->hmw->isLoggedIn();

		$user = $this->ion_auth->user()->row();

		if ($this->form_validation->run() == false)
		{
			//display the form
			//set the flash data error message if there is one
			$this->data['message'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('message');

			$this->data['min_password_length'] = $this->config->item('min_password_length', 'ion_auth');
			$this->data['old_password'] = array(
				'name' => 'old',
				'id'   => 'old',
				'type' => 'password',
				'data-clear-btn' => "true",
				);
			$this->data['new_password'] = array(
				'name' => 'new',
				'id'   => 'new',
				'type' => 'password',
				'data-clear-btn' => "true",
				'pattern' => '^.{'.$this->data['min_password_length'].'}.*$',
				);
			$this->data['new_password_confirm'] = array(
				'name' => 'new_confirm',
				'id'   => 'new_confirm',
				'type' => 'password',
				'data-clear-btn' => "true",
				'pattern' => '^.{'.$this->data['min_password_length'].'}.*$',
				);
			$this->data['user_id'] = array(
				'name'  => 'user_id',
				'id'    => 'user_id',
				'type'  => 'hidden',
				'data-clear-btn' => "true",
				'value' => $user->id,
				);

			$this->data['username'] = $this->session->userdata('identity');
			$this->data['bu_name'] =  $this->session->userdata('bu_name');
			//render
			$this->_render_page('auth/change_password', $this->data);
		}
		else
		{
			$identity = $this->session->userdata('identity');

			$change = $this->ion_auth->change_password($identity, $this->input->post('old'), $this->input->post('new'));

			if ($change)
			{
				//if the password was successfully changed
				$this->session->set_flashdata('message', $this->ion_auth->messages());
				$this->logout();
			}
			else
			{
				$this->session->set_flashdata('message', $this->ion_auth->errors());
				redirect('auth/change_password', 'refresh');
			}
		}
	}


	function forgot_password()
	{
		$this->form_validation->set_rules('username', 'Username', 'required');
		if ($this->form_validation->run() == false) {
			//setup the input
			$this->data['username'] = array('name'    => 'username',
				'id'      => 'username',
				);
			//set any errors and display the form
			$this->data['message'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('message');

			$headers = $this->hmw->headerVars(-1, "/", "Forgot your password?");
			$this->load->view('jq_header_pre', $headers['header_pre']);
			$this->load->view('jq_header_post', $headers['header_post']);
			$this->_render_page('auth/forgot_password', $this->data);
			$this->load->view('jq_footer');
		}
		else {
			//run the forgotten password method to email an activation code to the user
			$forgotten = $this->ion_auth->forgotten_password($this->input->post('username'));

			if ($forgotten) { //if there were no errors
				$this->session->set_flashdata('message', $this->ion_auth->messages());
				redirect("auth/login", 'refresh'); //we should display a confirmation page here instead of the login page
			}
			else {
				$this->session->set_flashdata('message', $this->ion_auth->errors());
				redirect("auth/forgot_password", 'refresh');
			}
		}
	}

	//reset password - final step for forgotten password
	public function reset_password($code = NULL)
	{
		if (!$code)
		{
			show_404();
		}

		$user = $this->ion_auth->forgotten_password_check($code);

		if ($user)
		{
			//if the code is valid then display the password reset form

			$this->form_validation->set_rules('new', $this->lang->line('reset_password_validation_new_password_label'), 'required|min_length[' . $this->config->item('min_password_length', 'ion_auth') . ']|max_length[' . $this->config->item('max_password_length', 'ion_auth') . ']|matches[new_confirm]');
			$this->form_validation->set_rules('new_confirm', $this->lang->line('reset_password_validation_new_password_confirm_label'), 'required');

			if ($this->form_validation->run() == false)
			{
				//display the form

				//set the flash data error message if there is one
				$this->data['message'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('message');

				$this->data['min_password_length'] = $this->config->item('min_password_length', 'ion_auth');
				$this->data['new_password'] = array(
					'name' => 'new',
					'id'   => 'new',
					'type' => 'password',
					'pattern' => '^.{'.$this->data['min_password_length'].'}.*$',
					);
				$this->data['new_password_confirm'] = array(
					'name' => 'new_confirm',
					'id'   => 'new_confirm',
					'type' => 'password',
					'pattern' => '^.{'.$this->data['min_password_length'].'}.*$',
					);
				$this->data['user_id'] = array(
					'name'  => 'user_id',
					'id'    => 'user_id',
					'type'  => 'hidden',
					'value' => $user->id,
					);
				$this->data['csrf'] = $this->_get_csrf_nonce();
				$this->data['code'] = $code;

				//render
				$this->_render_page('auth/reset_password', $this->data);
			}
			else
			{
				// do we have a valid request?
				if ($this->_valid_csrf_nonce() === FALSE || $user->id != $this->input->post('user_id'))
				{

					//something fishy might be up
					$this->ion_auth->clear_forgotten_password_code($code);

					show_error($this->lang->line('error_csrf'));

				}
				else
				{
					// finally change the password
					$identity = $user->{$this->config->item('identity', 'ion_auth')};

					$change = $this->ion_auth->reset_password($identity, $this->input->post('new'));

					if ($change)
					{
						//if the password was successfully changed
						$this->session->set_flashdata('message', $this->ion_auth->messages());
						$this->logout();
					}
					else
					{
						$this->session->set_flashdata('message', $this->ion_auth->errors());
						redirect('auth/reset_password/' . $code, 'refresh');
					}
				}
			}
		}
		else
		{
			//if the code is invalid then send them back to the forgot password page
			$this->session->set_flashdata('message', $this->ion_auth->errors());
			redirect("auth/forgot_password", 'refresh');
		}
	}


	//activate the user
	function activate($id, $code=false)
	{
		if ($code !== false)
		{
			$activation = $this->ion_auth->activate($id, $code);
		}
		else if ($this->ion_auth_acl->has_permission('activate_user'))
		{
			$activation = $this->ion_auth->activate($id);
		}

		if ($activation)
		{
			//redirect them to the auth page
			$this->session->set_flashdata('message', $this->ion_auth->messages());
			redirect("auth", 'refresh');
		}
		else
		{
			//redirect them to the forgot password page
			$this->session->set_flashdata('message', $this->ion_auth->errors());
			redirect("auth/forgot_password", 'refresh');
		}
	}

	//deactivate the user
	function deactivate($id = NULL)
	{
		$id = (int) $id;

		$this->load->library('form_validation');
		$this->form_validation->set_rules('confirm', $this->lang->line('deactivate_validation_confirm_label'), 'required');
		$this->form_validation->set_rules('id', $this->lang->line('deactivate_validation_user_id_label'), 'required|alpha_numeric');

		if ($this->form_validation->run() == FALSE)
		{
			// insert csrf check
			$this->data['csrf'] = $this->_get_csrf_nonce();
			$this->data['user'] = $this->ion_auth->user($id)->row();

			$this->data['username'] = $this->session->userdata('identity');
			$this->data['bu_name'] =  $this->session->userdata('bu_name');

			$headers = $this->hmw->headerVars(0, "/auth/", "Users");
			$this->load->view('jq_header_pre', $headers['header_pre']);
			$this->load->view('jq_header_post', $headers['header_post']);
			$this->_render_page('auth/deactivate_user', $this->data);
			$this->load->view('jq_footer');

		}
		else
		{
			// do we really want to deactivate?
			if ($this->input->post('confirm') == 'yes')
			{
				// do we have a valid request?
				if ($this->_valid_csrf_nonce() === FALSE || $id != $this->input->post('id'))
				{
					show_error($this->lang->line('error_csrf'));
				}

				// do we have the right userlevel?
				if ($this->ion_auth_acl->has_permission('deactivate_user'))
				{
					$this->ion_auth->deactivate($id);
				}
			}

			//redirect them back to the auth page
			redirect('auth', 'refresh');
		}
	}

	//delete the user
	function delete($id = NULL)
	{
		$id = (int) $id;

		$this->load->library('form_validation');
		$this->form_validation->set_rules('confirm', $this->lang->line('deactivate_validation_confirm_label'), 'required');
		$this->form_validation->set_rules('id', $this->lang->line('deactivate_validation_user_id_label'), 'required|alpha_numeric');

		if ($this->form_validation->run() == FALSE)
		{
			// insert csrf check
			$this->data['csrf'] = $this->_get_csrf_nonce();
			$this->data['user'] = $this->ion_auth->user($id)->row();

			$this->data['username'] = $this->session->userdata('identity');
			$this->data['bu_name'] =  $this->session->userdata('bu_name');

			$headers = $this->hmw->headerVars(0, "/auth/", "Users");
			$this->load->view('jq_header_pre', $headers['header_pre']);
			$this->load->view('jq_header_post', $headers['header_post']);
			$this->_render_page('auth/delete_user', $this->data);
			$this->load->view('jq_footer');
		}
		else
		{
			// do we really want to ?
			if ($this->input->post('confirm') == 'yes')
			{
				// do we have a valid request?
				if ($this->_valid_csrf_nonce() === FALSE || $id != $this->input->post('id'))
				{
					show_error($this->lang->line('error_csrf'));
				}

				// do we have the right userlevel?
				if ($this->ion_auth_acl->has_permission('delete_user'))
				{
					$user = $this->ion_auth->user($id)->row_array();
					if (isset($user['WordPress_UID'])) {
						$wpUID = $user['WordPress_UID'];
		        if ($this->wp_rms->deleteWPUser($wpUID, 0) === true) {
		          $WpUID = array('WordPress_UID' => NULL);
		          $this->db->where('id', $id);
		          $this->db->update('users', $WpUID);
		        }
		      }
					$this->ion_auth->delete_user($id);
				}
			}

			//redirect them back to the auth page
			redirect('auth', 'refresh');
		}
	}

	//create a new user
	function create_user()
	{
		$this->load->library("hmw");
		$this->load->library('mmail');

		$this->data['title'] = "Create User";
		
		$this->hmw->isLoggedIn();
		
		if (!$this->ion_auth_acl->has_permission('create_user'))
		{
			die('You are not allowed to do this.');
		}

		$tables = $this->config->item('tables','ion_auth');

		//validate form input
		$this->form_validation->set_rules('first_name', $this->lang->line('create_user_validation_fname_label'), 'required|xss_clean');
		$this->form_validation->set_rules('last_name', $this->lang->line('create_user_validation_lname_label'), 'required|xss_clean');
		$this->form_validation->set_rules('email', $this->lang->line('create_user_validation_email_label'), 'valid_email|is_unique['.$tables['users'].'.email]');
		$this->form_validation->set_rules('phone', $this->lang->line('create_user_validation_phone_label'), 'xss_clean|min_length[12]|max_length[12]');
		$this->form_validation->set_rules('comment', 'Enter a valid comment.', 'xss_clean');
		//$this->form_validation->set_rules('password', $this->lang->line('create_user_validation_password_label'), 'required|min_length[' . $this->config->item('min_password_length', 'ion_auth') . ']|max_length[' . $this->config->item('max_password_length', 'ion_auth') . ']|matches[password_confirm]');
		//$this->form_validation->set_rules('password_confirm', $this->lang->line('create_user_validation_password_confirm_label'), 'required');

		if ($this->form_validation->run() == true)
		{
			$username = trim(strtolower($this->input->post('first_name'))) . '.' . strtolower($this->input->post('last_name'));
			$email    = trim(strtolower($this->input->post('email')));
			$password = $this->hmw->getParam('default_password');

			$additional_data = array(
				'first_name' => trim($this->input->post('first_name')),
				'last_name'  => trim($this->input->post('last_name')),
				'comment'  	 => trim($this->input->post('comment')),
				'phone'      => trim($this->input->post('phone'))
				);
		}
		$first_shift = $this->input->post('sdate');
		if ($this->form_validation->run() == true && $this->ion_auth->register($username, $password, $email, $additional_data, $this->input->post('groups'), $this->input->post('bus'), $first_shift))
		{
			$welcome_email = $this->input->post('welcome_email');

			if(!empty($welcome_email)) {						
				$email 				= array();
				$email['to']		= strtolower($this->input->post('email'));
				$email['subject']	= 'Welcome from Hank!';
				$email['msg'] 		= $this->input->post('txtmessage');
				$this->mmail->sendEmail($email);
			}

			//check to see if we are creating the user
			//redirect them back to the news page
			$this->session->set_flashdata('message', $this->ion_auth->messages());
			redirect("auth", 'refresh');
		}
		else
		{
			//display the create user form
			//set the flash data error message if there is one
			$this->data['message'] = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));

			$this->data['first_name'] = array(
				'name'  => 'first_name',
				'id'    => 'first_name',
				'type'  => 'text',
				'data-clear-btn' => "true",
				'value' => $this->form_validation->set_value('first_name'),
				);
			$this->data['last_name'] = array(
				'name'  => 'last_name',
				'id'    => 'last_name',
				'type'  => 'text',
				'data-clear-btn' => "true",
				'value' => $this->form_validation->set_value('last_name'),
				);
			$this->data['email'] = array(
				'name'  => 'email',
				'id'    => 'email',
				'type'  => 'text',
				'data-clear-btn' => "true",
				'value' => $this->form_validation->set_value('email'),
				);
			$this->data['phone'] = array(
				'name'  => 'phone',
				'id'    => 'phone',
				'type'  => 'text',
				'data-clear-btn' => "true",
				'value' => $this->form_validation->set_value('phone'),
				);
			$this->data['comment'] = array(
				'name'  => 'comment',
				'id'    => 'comment',
				'type'  => 'text',
				'data-clear-btn' => "true",
				'value' => $this->form_validation->set_value('comment'),
				);
			/**
			$this->data['password'] = array(
			'name'  => 'password',
			'id'    => 'password',
			'type'  => 'password',
			'data-clear-btn' => "true",
			'value' => $this->form_validation->set_value('password'),
			);
			$this->data['password_confirm'] = array(
			'name'  => 'password_confirm',
			'id'    => 'password_confirm',
			'type'  => 'password',
			'data-clear-btn' => "true",
			'value' => $this->form_validation->set_value('password_confirm'),
			);
			**/
			$this->data['groups']	= $groups=$this->ion_auth->groups()->result_array();
			$this->data['bus']		= $bus=$this->ion_auth->bus()->result_array();

			$userinfo = $this->ion_auth->user()->row();
			$groupinfo = $this->ion_auth_model->get_users_groups()->result();

			$this->data['current_user'] = $userinfo;
			$this->data['groupinfo'] = $groupinfo;

			$id_bu =  $this->session->userdata('bu_id');
			$buinfo = $this->hmw->getBuInfo($id_bu);
			$this->data['welcome_email'] = $buinfo->welcome_email;

			$this->data['username'] = $this->session->userdata('identity');
			$this->data['bu_name'] =  $this->session->userdata('bu_name');

			$headers = $this->hmw->headerVars(0, "/auth/", "Users");
			$this->load->view('jq_header_pre', $headers['header_pre']);
			$this->load->view('auth/jq_header_spe');
			$this->load->view('jq_header_post', $headers['header_post']);
			$this->_render_page('auth/create_user', $this->data);
			$this->load->view('jq_footer');
		}
	}

	//edit a user
	function edit_user($id)
	{
		$this->data['title'] = "Edit User";
		$this->load->library('hmw');
		$id_bu =  $this->session->userdata('bu_id');
		
		$this->hmw->isLoggedIn();
		
		if (!$this->ion_auth_acl->has_permission('edit_user') && !$this->ion_auth->user()->row()->id == $id)
		{
			redirect('auth', 'refresh');
		}

		$user = $this->ion_auth->user($id)->row();
		$groups=$this->ion_auth->groups()->result_array();
		$bus=$this->ion_auth->bus()->result_array();
		$currentGroups = $this->ion_auth->get_users_groups($id)->result();
		$currentBus = $this->ion_auth->get_users_bus($id)->result();

		//validate form input
		$this->form_validation->set_rules('first_name', $this->lang->line('edit_user_validation_fname_label'), 'required|xss_clean');
		$this->form_validation->set_rules('last_name', $this->lang->line('edit_user_validation_lname_label'), 'required|xss_clean');
		$this->form_validation->set_rules('username', $this->lang->line('edit_user_validation_lname_label'), 'required|xss_clean');
		$this->form_validation->set_rules('email', $this->lang->line('edit_user_validation_email_label'), 'required|valid_email|xss_clean');
		$this->form_validation->set_rules('phone', $this->lang->line('edit_user_validation_phone_label'), 'exact_length[12]|numeric|xss_clean');
		$this->form_validation->set_rules('comment', 'Enter a valid comment', 'xss_clean');
		$this->form_validation->set_rules('iban', 'Enter a valid IBAN', 'xss_clean');
		$this->form_validation->set_rules('groups', $this->lang->line('edit_user_validation_groups_label'), 'xss_clean');
		$this->form_validation->set_rules('bus', $this->lang->line('edit_user_validation_bus_label'), 'xss_clean');

		if (isset($_POST) && !empty($_POST))
		{
			// do we have a valid request?
			if ($this->_valid_csrf_nonce() === FALSE || $id != $this->input->post('id'))
			{
				show_error($this->lang->line('error_csrf'));
			}

			//update the password if it was posted
			if ($this->input->post('password'))
			{
				$this->form_validation->set_rules('password', $this->lang->line('edit_user_validation_password_label'), 'required|min_length[' . $this->config->item('min_password_length', 'ion_auth') . ']|max_length[' . $this->config->item('max_password_length', 'ion_auth') . ']|matches[password_confirm]');
				$this->form_validation->set_rules('password_confirm', $this->lang->line('edit_user_validation_password_confirm_label'), 'required');
			}

			if ($this->form_validation->run() === TRUE)
			{
				$data = array(
					'first_name' => $this->input->post('first_name'),
					'last_name'  => $this->input->post('last_name'),
					'username'   => $this->input->post('username'),
					'email'		 => $this->input->post('email'),
					'phone'      => $this->input->post('phone'),
					'comment'      => $this->input->post('comment'),
					'iban'      => $this->input->post('iban'),
					'door_open'      => $this->input->post('door_open')
					);
					
				$data_WP = array(
					'first_name' => $this->input->post('first_name'),
					'last_name'  => $this->input->post('last_name'),
					'username'   => $this->input->post('username'),
					'email'		 => $this->input->post('email')
				);

				//update the password if it was posted
				if ($this->input->post('password'))
				{
					$data['password'] = $this->input->post('password');
					if (!empty($data['password'])) {
						$data_WP['password'] = $this->input->post('password');
					}
				}
				

				$this->ion_auth->update($user->id, $data);
				if (isset($user->WordPress_UID)) {
					if (!$this->wp_rms->editWPUser($user->WordPress_UID, $data_WP)) {
						error_log("Unable to edit WP data for user " . $user->username);
					}
				}

				// Only allow updating groups if user is admin
				if ($this->ion_auth_acl->has_permission('edit_user_group'))
				{
					//Update the groups user belongs to
					$groupData = $this->input->post('groups');
					$buData    = $this->input->post('bus');
					$first_shift = $this->input->post('sdate');
					if (isset($user->WordPress_UID)) {
						$user_WP_role = $this->wp_rms->userWPRole($id);
						if (isset($user_WP_role['wp_role']) && !empty($user_WP_role['wp_role'])) {
							$WPGroupData = array('roles' => $user_WP_role['wp_role']);
						} else {
							die ('No WordPress user level set for the highest group chosen');
						}
					}
					if (isset($groupData) && !empty($groupData)) {

						$this->ion_auth->remove_from_group('', $id);

						foreach ($groupData as $grp) {
							$this->ion_auth->add_to_group($grp, $id);
						}
						
						if (isset($user->WordPress_UID)) {
							$this->wp_rms->editWPUser($user->WordPress_UID, $WPGroupData);
						}
					}

					if (isset($buData) && !empty($buData)) {

						$this->ion_auth->remove_from_bu('', $id);

						foreach ($buData as $bu) {
							$this->ion_auth->add_to_bu($bu, $id);
						}

					}
					if (isset($first_shift) && !empty($first_shift)) {
						$this->db->where('id', $id);
						$this->db->update('users', array('first_shift' => $first_shift));
					} else {
						$this->db->where('id', $id);
						$this->db->update('users', array('first_shift' => NULL));
					}
				}

				//check to see if we are creating the user
				//redirect them back to the admin page
				$this->session->set_flashdata('message', "User Saved");
				if ($this->ion_auth_acl->has_permission('edit_user'))
				{
					redirect('auth', 'refresh');
				}
				else
				{
					redirect('/', 'refresh');
				}
			}
		}

		//display the edit user form
		$this->data['csrf'] = $this->_get_csrf_nonce();

		//set the flash data error message if there is one
		$this->data['message'] = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));

		//pass the user to the view
		$this->data['user'] = $user;
		$this->data['groups'] = $groups;
		$this->data['bus'] = $bus;
		$this->data['currentGroups'] = $currentGroups;
		$this->data['currentBus'] = $currentBus;

		$this->data['username2'] = $this->session->userdata('identity');
		$this->data['bu_name'] =  $this->session->userdata('bu_name');

		$this->data['first_name'] = array(
			'name'  => 'first_name',
			'id'    => 'first_name',
			'type'  => 'text',
			'data-clear-btn' => "true",
			'value' => $this->form_validation->set_value('first_name', $user->first_name),
			);
		$this->data['last_name'] = array(
			'name'  => 'last_name',
			'id'    => 'last_name',
			'type'  => 'text',
			'data-clear-btn' => "true",
			'value' => $this->form_validation->set_value('last_name', $user->last_name),
			);
		$this->data['username'] = array(
			'name'  => 'username',
			'id'    => 'username',
			'type'  => 'text',
			'data-clear-btn' => "true",
			'value' => $this->form_validation->set_value('username', $user->username),
			);
		$this->data['email'] = array(
			'name'  => 'email',
			'id'    => 'email',
			'type'  => 'text',
			'data-clear-btn' => "true",
			'value' => $this->form_validation->set_value('email', $user->email),
			);
		$this->data['phone'] = array(
			'name'  => 'phone',
			'id'    => 'phone',
			'type'  => 'text',
			'data-clear-btn' => "true",
			'value' => $this->form_validation->set_value('phone', $user->phone),
			);
		$this->data['iban'] = array(
			'name'  => 'iban',
			'id'    => 'iban',
			'type'  => 'text',
			'data-clear-btn' => "true",
			'value' => $this->form_validation->set_value('iban', $user->iban),
			);
		$this->data['comment'] = array(
			'name'  => 'comment',
			'id'    => 'comment',
			'type'  => 'text',
			'data-clear-btn' => "true",
			'value' => $this->form_validation->set_value('comment', $user->comment),
			);
		$this->data['password'] = array(
			'name' => 'password',
			'id'   => 'password',
			'data-clear-btn' => "true",
			'type' => 'password'
			);
		$this->data['password_confirm'] = array(
			'name' => 'password_confirm',
			'id'   => 'password_confirm',
			'data-clear-btn' => "true",
			'type' => 'password'
			);
			if (isset($user->WordPress_UID) && $this->ion_auth_acl->has_permission('edit_WP_user')) {
				$this->data['WpUID'] = $user->WordPress_UID;
			}
			if (isset($user->first_shift) && $this->ion_auth_acl->has_permission('edit_first_shift_user')) {
				$this->data['first_shift'] = $user->first_shift;
			}
		$this->data['current_user_groups'] = $user_groups = $this->ion_auth->get_users_groups()->result();
		
		$buinfo = $this->hmw->getBuInfo($id_bu);
		
		$headers = $this->hmw->headerVars(0, "/auth/", "Users");
		$this->load->view('jq_header_pre', $headers['header_pre']);
		$this->load->view('auth/jq_header_spe');
		$this->load->view('jq_header_post', $headers['header_post']);
		$this->_render_page('auth/edit_user', $this->data);
		$this->load->view('jq_footer');
	}

	// create a new group
	function create_group()
	{
		$this->data['title'] = $this->lang->line('create_group_title');
		
		$this->hmw->isLoggedIn();
		
		if (!$this->ion_auth_acl->has_permission('create_group'))
		{
			redirect('auth', 'refresh');
		}

		//validate form input
		$this->form_validation->set_rules('group_name', $this->lang->line('create_group_validation_name_label'), 'required|alpha_dash|xss_clean');
		$this->form_validation->set_rules('description', $this->lang->line('create_group_validation_desc_label'), 'xss_clean');

		if ($this->form_validation->run() == TRUE)
		{
			$new_group_id = $this->ion_auth->create_group($this->input->post('group_name'), $this->input->post('description'));
			if($new_group_id)
			{
				// check to see if we are creating the group
				// redirect them back to the admin page
				$this->session->set_flashdata('message', $this->ion_auth->messages());
				redirect("auth", 'refresh');
			}
		}
		else
		{
			//display the create group form
			//set the flash data error message if there is one
			$this->data['message'] = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));

			$this->data['group_name'] = array(
				'name'  => 'group_name',
				'id'    => 'group_name',
				'type'  => 'text',
				'data-clear-btn' => "true",
				'value' => $this->form_validation->set_value('group_name')
				);
			$this->data['description'] = array(
				'name'  => 'description',
				'id'    => 'description',
				'type'  => 'text',
				'data-clear-btn' => "true",
				'value' => $this->form_validation->set_value('description')
				);

			$this->data['username'] = $this->session->userdata('identity');
			$this->data['bu_name'] =  $this->session->userdata('bu_name');

			$headers = $this->hmw->headerVars(0, "/auth/", "Users");
			$this->load->view('jq_header_pre', $headers['header_pre']);
			$this->load->view('jq_header_post', $headers['header_post']);
			$this->_render_page('auth/create_group', $this->data);
			$this->load->view('jq_footer');
		}
	}

	//edit a group
	function edit_group($id)
	{
		// bail if no group id given
		if(!$id || empty($id))
		{
			redirect('auth', 'refresh');
		}

		$this->data['title'] = $this->lang->line('edit_group_title');

		$this->hmw->isLoggedIn();
		
		if (!$this->ion_auth_acl->has_permission('edit_group'))
		{
			redirect('auth', 'refresh');
		}

		$group = $this->ion_auth->group($id)->row();

		//validate form input
		$this->form_validation->set_rules('group_name', $this->lang->line('edit_group_validation_name_label'), 'required|alpha_dash|xss_clean');
		$this->form_validation->set_rules('group_description', $this->lang->line('edit_group_validation_desc_label'), 'xss_clean');

		if (isset($_POST) && !empty($_POST))
		{
			if ($this->form_validation->run() === TRUE)
			{
				$group_update = $this->ion_auth->update_group($id, $_POST['group_name'], $_POST['group_description']);

				if($group_update)
				{
					$this->session->set_flashdata('message', $this->lang->line('edit_group_saved'));
				}
				else
				{
					$this->session->set_flashdata('message', $this->ion_auth->errors());
				}
				redirect("auth", 'refresh');
			}
		}

		//set the flash data error message if there is one
		$this->data['message'] = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));

		//pass the user to the view
		$this->data['group'] = $group;

		$this->data['group_name'] = array(
			'name'  => 'group_name',
			'id'    => 'group_name',
			'type'  => 'text',
			'data-clear-btn' => "true",

			'value' => $this->form_validation->set_value('group_name', $group->name),
			);
		$this->data['group_description'] = array(
			'name'  => 'group_description',
			'id'    => 'group_description',
			'type'  => 'text',
			'data-clear-btn' => "true",
			'value' => $this->form_validation->set_value('group_description', $group->description),
			);

		$this->data['username'] = $this->session->userdata('identity');
		$this->data['bu_name'] =  $this->session->userdata('bu_name');

		$headers = $this->hmw->headerVars(0, "/auth/", "Users");
		$this->load->view('jq_header_pre', $headers['header_pre']);
		$this->load->view('jq_header_post', $headers['header_post']);
		$this->_render_page('auth/edit_group', $this->data);
		$this->load->view('jq_footer');
	}
	
	
	// cd /var/www/hank/rms/rms && php index.php auth cliRmdShift [id_bu]
	public function cliRmdShift($id_bu = null) {
		
		if(is_cli()) {
			if ($id_bu == null) {
				die('pass a bu id in parameters');
			}
			$bu_info = $this->hmw->getBuInfo($id_bu);
			$this->db->select('users.id, users.username, users.first_shift, users.last_shift_rmd, bus.name');
			$this->db->join('users_bus', 'users.id = users_bus.user_id');
			$this->db->join('bus', 'users_bus.bu_id = bus.id');
			$this->db->where('users.active', 1);
			$this->db->where('bus.id', $id_bu);
			$res = $this->db->get('users')->result();
			$current_date = new DateTime("now");
			$current_date_string = $current_date->format('Y-m-d');
			$employees_first_rmd = array();
			$employees_second_rmd = array();
			$employees_rmd = array();
			foreach ($res as $key => $val) {
				$user_groups = $this->ion_auth->get_users_groups($val->id)->result_array();
				$username = $val->username;
	      $higher_level['level'] = -1;
	      foreach ($user_groups as $key => $value) {
	        if ($value['level'] > $higher_level['level']) {
	          $higher_level = $value;
	        }
	      }
				if (isset($higher_level['level']) && $higher_level['level'] == 0) {
					if (isset($val->last_shift_rmd)) {
						$last_rmd = new DateTime($val->last_shift_rmd);
						if (isset($val->first_shift)) {
							$inter_last_curr = $current_date->diff($last_rmd);
							$sum = (($inter_last_curr->format('%y') * 365) + ($inter_last_curr->format('%m') * 30) + $inter_last_curr->format('%d'));
							if ($sum == 21) {
								$employees_second_rmd[] = $username;
							} else if ($sum >= 90) {
									$employees_rmd[] = $username;
							}
						} else {
							echo "User: " . $username . " has no first shift\n";
						}
					} else {
							if (isset($val->first_shift)) {
								$first_shift = new DateTime($val->first_shift);
								$inter_first_curr = $current_date->diff($first_shift);
								$sum = (($inter_first_curr->format('%m') * 30) + $inter_first_curr->format('%d'));
								if ($sum >= 21) {
									$employees_first_rmd[] = $username;
								}
							} else {
								echo "User: " . $username . " has no first shift\n";
							}
					}
				}
			}
			if (empty($employees_rmd) && empty($employees_first_rmd) && empty($employees_second_rmd)) {
				// echo "No user need skills validation\n"; 						//uncomment to debug
				return (false);
			}
			$this->db->select('users.email');
			$this->db->join('users_groups', 'users.id = users_groups.user_id');
			$this->db->join('users_bus', 'users.id = users_bus.user_id');
			$this->db->where_in('users_groups.group_id', array(3,4));
			$this->db->where('users.active', 1);
			$this->db->where('users_bus.bu_id', $id_bu);
			$managers = $this->db->get('users')->result_array();
			$managers_email = array();
			foreach ($managers as $manager) {
				$managers_email[] = $manager['email'];
			}
			if (empty($managers_email)) {
				die ('Could not find any manager for this bu');
			}
			$email['to'] = $managers_email;
			$email['subject'] = '[' . $bu_info->name . '] Users that need skills reporting !';
			$email['mailtype'] = 'html';
			$msg = '<p>Hello Managers, here are the users that need skill review as of now :</p><p>* First Report : <br />';
			foreach($employees_first_rmd as $employee) {
				$msg .= '- '.$employee . '<br />';
			}
			$msg .= '</p><hr><p>* Second Report : <br />';
			foreach($employees_second_rmd as $employee) {
				$msg .= '- '.$employee . '<br />';
			}
			$msg .= '</p><hr><p>* Quarterly Report : <br />';
			foreach($employees_rmd as $employee) {
				$msg .= '- '.$employee . '<br />';
			}
			$msg .= '</p><br /><b>Please make a report for each one of them. Thank you !</b>';
			$email['msg'] = $msg;

			$this->mmail->sendEmail($email);
			$employees_to_update = array_merge($employees_first_rmd, $employees_second_rmd, $employees_rmd);
			$this->db->where_in('username', $employees_to_update);
			$this->db->update('users', array('last_shift_rmd' => $current_date_string));
		} else {
			return (false);
		}
	}

	public function edit_oneself($id=null)
	{
		
		if (!$this->ion_auth_acl->has_permission('edit_self')) {
			die('You are not allowed to view this.');
		}
		$this->data['title'] = "Edit User";

		$this->hmw->isLoggedIn();
		
		if (!$this->ion_auth_acl->has_permission('edit_user') && !($this->ion_auth->user()->row()->id == $id))
		{
			redirect('auth', 'refresh');
		}

		$user = $this->ion_auth->user($id)->row();
		$groups=$this->ion_auth->groups()->result_array();
		$bus=$this->ion_auth->bus()->result_array();
		$currentGroups = $this->ion_auth->get_users_groups($id)->result();
		$currentBus = $this->ion_auth->get_users_bus($id)->result();

		//validate form input
		$this->form_validation->set_rules('email', $this->lang->line('edit_user_validation_email_label'), 'required|valid_email|xss_clean');
		$this->form_validation->set_rules('phone', $this->lang->line('edit_user_validation_phone_label'), 'exact_length[12]|numeric|xss_clean');
		$this->form_validation->set_rules('groups', $this->lang->line('edit_user_validation_groups_label'), 'xss_clean');
		$this->form_validation->set_rules('bus', $this->lang->line('edit_user_validation_bus_label'), 'xss_clean');

		if (isset($_POST) && !empty($_POST))
		{
			// do we have a valid request?
			if ($this->_valid_csrf_nonce() === FALSE || $id != $this->input->post('id'))
			{
				show_error($this->lang->line('error_csrf'));
			}

			//update the password if it was posted
			if ($this->input->post('password'))
			{
				$this->form_validation->set_rules('password', $this->lang->line('edit_user_validation_password_label'), 'required|min_length[' . $this->config->item('min_password_length', 'ion_auth') . ']|max_length[' . $this->config->item('max_password_length', 'ion_auth') . ']|matches[password_confirm]');
				$this->form_validation->set_rules('password_confirm', $this->lang->line('edit_user_validation_password_confirm_label'), 'required');
			}

			if ($this->form_validation->run() === TRUE)
			{
				$data = array(
					'email'		 => $this->input->post('email'),
					'phone'      => $this->input->post('phone')
				);

				
				$data_WP = array(
					'email'		 => $this->input->post('email')
				);
				

				//update the password if it was posted
				if ($this->input->post('password'))
				{
					$data['password'] = $this->input->post('password');
					$data_WP['password'] = $this->input->post('password');
				}

				$this->ion_auth->update($user->id, $data);
				if ($wpid = $this->wp_rms->hasWpAccount($user->id)) {
					if (!$this->wp_rms->editWPUser($wpid, $data_WP)) {
						error_log("Unable to edit WP data for user " . $user->username);
					}
				}

				// Only allow updating groups if user is admin
				if ($this->ion_auth_acl->has_permission('edit_self_group'))
				{
					//Update the groups user belongs to
					$groupData = $this->input->post('groups');
					$buData    = $this->input->post('bus');
					if (isset($user->WordPress_UID)) {
						$user_WP_role = $this->wp_rms->userWPRole($id);
						if (isset($user_WP_role['wp_role']) && !empty($user_WP_role['wp_role'])) {
							$WPGroupData = array('roles' => $user_WP_role['wp_role']);
						} else {
							die ('No WordPress user level set for the highest group chosen');
						}
					}
					if (isset($groupData) && !empty($groupData)) {

						$this->ion_auth->remove_from_group('', $id);

						foreach ($groupData as $grp) {
							$this->ion_auth->add_to_group($grp, $id);
						}
						if ($this->wp_rms->hasWpAccount($user->id)) {
							$this->wp_rms->editWPUser($user->WordPress_UID, $WPGroupData);
						}
					}

					if (isset($buData) && !empty($buData)) {

						$this->ion_auth->remove_from_bu('', $id);

						foreach ($buData as $bu) {
							$this->ion_auth->add_to_bu($bu, $id);
						}

					}
				}

				//check to see if we are creating the user
				//redirect them back to the admin page
				$this->session->set_flashdata('message', "Your modifications are recorded");
				redirect('/auth/edit_oneself/'.$id, 'refresh');
			}
		}

		//display the edit user form
		$this->data['csrf'] = $this->_get_csrf_nonce();

		//set the flash data error message if there is one
		$this->data['message'] = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));

		//pass the user to the view
		$this->data['user'] = $user;

		$this->data['email'] = array(
			'name'  => 'email',
			'id'    => 'email',
			'type'  => 'text',
			'data-clear-btn' => "true",
			'value' => $this->form_validation->set_value('email', $user->email),
			);
		$this->data['phone'] = array(
			'name'  => 'phone',
			'id'    => 'phone',
			'type'  => 'text',
			'data-clear-btn' => "true",
			'value' => $this->form_validation->set_value('phone', $user->phone),
			);
		$this->data['password'] = array(
			'name' => 'password',
			'id'   => 'password',
			'data-clear-btn' => "true",
			'type' => 'password'
			);
		$this->data['password_confirm'] = array(
			'name' => 'password_confirm',
			'id'   => 'password_confirm',
			'data-clear-btn' => "true",
			'type' => 'password'
			);
			if (isset($user->WordPress_UID) && $this->ion_auth_acl->has_permission('edit_WP_self')) {
				$this->data['WpUID'] = $user->WordPress_UID;
			}
		$headers = $this->hmw->headerVars(1, "/auth/", "My account");
		$this->load->view('jq_header_pre', $headers['header_pre']);
		$this->load->view('jq_header_post', $headers['header_post']);
		$this->_render_page('auth/account', $this->data);
		$this->load->view('jq_footer');
	}

	function _get_csrf_nonce()
	{
		$this->load->helper('string');
		$key   = random_string('alnum', 8);
		$value = random_string('alnum', 20);
		$this->session->set_flashdata('csrfkey', $key);
		$this->session->set_flashdata('csrfvalue', $value);

		return array($key => $value);
	}

	function _valid_csrf_nonce()
	{
		if ($this->input->post($this->session->flashdata('csrfkey')) !== FALSE &&
			$this->input->post($this->session->flashdata('csrfkey')) == $this->session->flashdata('csrfvalue'))
		{
			return TRUE;
		}
		else
		{
			return FALSE;
		}
	}

	function _render_page($view, $data=null, $render=false)
	{

		$this->viewdata = (empty($data)) ? $this->data: $data;

		$view_html = $this->load->view($view, $this->viewdata, $render);

		if (!$render) return $view_html;
	}

}
