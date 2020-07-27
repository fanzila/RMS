<?php (defined('BASEPATH')) OR exit('No direct script access allowed');

/**
 * Dashboard.php
 *
 * @package     CI-ACL
 * @author      Steve Goodwin
 * @copyright   2015 Plumps Creative Limited
 */
class Dashboard extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->library('ion_auth');
        $this->load->library('ion_auth_acl');
        $this->load->library('tools');

        if( ! $this->ion_auth->logged_in() )
            redirect('/login');
    }

    public function index()
    {
        $data['users_groups']           =   $this->ion_auth->get_users_groups()->result();
        $data['users_permissions']      =   $this->ion_auth_acl->build_acl();

        $headers = $this->tools->headerVars(1, "/dashboard", "ACL Dashboard");
  			$this->load->view('jq_header_pre', $headers['header_pre']);
  			$this->load->view('jq_header_post', $headers['header_post']);
        $this->load->view('acl_admin/dashboard', $data);
        $this->load->view('jq_footer');
    }

}