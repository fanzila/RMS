<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Admin extends CI_Controller {

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
	}

	public function index()
	{		
		
		if (!$this->ion_auth->logged_in())
		{
			redirect('auth/login');
		}
		
		
		$user = $this->ion_auth->user()->row();
		//print_r($user);
		$user_groups = $this->ion_auth->get_users_groups()->result();
		
		$bal_req = "SELECT val FROM bank_balance";
		$bal_res = $this->db->query($bal_req);
		$bal = $bal_res->row_array();
		
		$bal_ca = "SELECT * FROM turnover ORDER BY `date` DESC LIMIT 1";
		$bal_ca = $this->db->query($bal_ca);
		$ca = $bal_ca->row_array();
		
		$data = array(
			'user_groups'		=> $user_groups[0],
			'bank_balance'		=> $bal['val'],
			'ca'				=> $ca,
			'ticket'			=> '',
			'last_ticket'		=> '',
			'username'			=> $user->username
		);
			
		
		$this->load->view('admin', $data);
	}

}
?>