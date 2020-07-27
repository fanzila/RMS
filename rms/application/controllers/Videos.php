<?php
class Videos extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->database();
		$this->load->library("tools");
		$this->load->library('ion_auth');
		$this->load->library('ion_auth_acl');
	}

	public function index($login=null)
	{
		$this->tools->keyLogin();
		$this->tools->changeBu();// GENERIC changement de Bu
		
		$id_bu =  $this->session->userdata('id_bu');
		$bu_name	= $this->session->userdata('bu_name');
				
		$user					= $this->ion_auth->user()->row();
		$user_groups 			= $this->ion_auth->get_users_groups()->result();
		$bus_list = $this->tools->getBus(null, $user->id);
				
		$this->db->select('bu_info, training_link')->from('bus')->where('id', $id_bu);
		$res = $this->db->get() or die($this->mysqli->error);
		$bu_infos = $res->result();
		
		$data = array(
			'username'	=> $user->username,
			'user_groups'	=> $user_groups[0],
			'title'		=> 'Book',
			'keylogin'	=> $this->session->userdata('keylogin'),
			'bu_infos'		=> $bu_infos[0]->bu_info,
			'bu_link'		=> $bu_infos[0]->training_link,
			'bu_name'	=> $this->session->userdata('bu_name')
			);

		$headers = $this->tools->headerVars(1, "/news/index/", "Videos");
		$this->load->view('jq_header_pre', $headers['header_pre']);
		$this->load->view('jq_header_post', $headers['header_post']);
		$this->load->view('videos',$data);
		$this->load->view('jq_footer');
	}
}
?>
