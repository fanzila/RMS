<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Account extends CI_Controller {

	/**
	* Index Page for this controller.
	*
	* Maps to the following URL
	* 		http://example.com/index.php/welcome
	*	- or -  
	* 		http://example.com/index.php/welcome/index
	*	- or -
	* Since this controller is set as the default controller in 
	* config/routes.php, it's displayed at http://example.com/
	*
	* So any other public methods not prefixed with an underscore will
	* map to /index.php/welcome/<method_name>
	* @see http://codeigniter.com/user_guide/general/urls.html
	*/

	public function __construct()
	{

		parent::__construct();

		$this->load->database();
		$this->load->library('ion_auth');
		$this->load->library('hmw');
		$this->load->helper(array('form', 'url'));
		$this->load->library('form_validation');
	}

	public function index()
	{
		$this->hmw->changeBu();// GENERIC changement de Bu

		$this->hmw->keyLogin();
		$id_bu =  $this->session->all_userdata()['bu_id'];


		$data = array(
			'mail'	=> $?,
			'phone'	=> $?,
			'fname'	=> $?,
			'lname'	=> $?
			);
		$data['bu_name'] =  $this->session->all_userdata()['bu_name'];
		$data['username'] = $this->session->all_userdata()['identity'];

		$headers = $this->hmw->headerVars(1, "/account/", "My account");
		$this->load->view('jq_header_pre', $headers['header_pre']);
		$this->load->view('jq_header_post', $headers['header_post']);
		$this->load->view('auth/account',$data);
		$this->load->view('jq_footer');
	}

	public function save()
	{
		$id_bu =  $this->session->all_userdata()['bu_id'];		
		$data = $this->input->post();
		
		$reponse = 'ok';
		$this->db->set('email', $data['email']);
		$this->db->set('phone', $data['phone']);
		//password aussi!
		//$this->db->where('id', /*get user Id*/);

		$this->db->trans_start();
			if(1//le password est bon) {
				if(!$this->db->update('users')) {
					$response = "Can't place the insert sql request, error message: ".$this->db->_error_message();
				}
			} else {
				$response = "The password entered is wrong.";
			}
		$this->db->trans_complete();

		echo json_encode(['reponse' => $response]);
	}
	$this->load->view('jq_footer');
	}
}
