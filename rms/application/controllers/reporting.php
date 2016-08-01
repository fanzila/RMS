<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Reporting extends CI_Controller {

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

	public function index()
	{		
		$this->load->library('ion_auth');
		
		if (!$this->ion_auth->logged_in())
		{
			redirect('auth/login');
		}
			
		$group_info = $this->ion_auth_model->get_users_groups()->result();
		if ($group_info[0]->level <= 0)
		{
			$this->session->set_flashdata('message', 'You must be a gangsta to view this page');
			redirect('/news/');
		}
		
		$this->load->view('reporting');
	}
}
