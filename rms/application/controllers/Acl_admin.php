<?php (defined('BASEPATH')) OR exit('No direct script access allowed');

//https://code.tutsplus.com/tutorials/a-better-login-system--net-3461


/**
 * Admin.php
 *
 * @package     CI-ACL
 * @author      Steve Goodwin
 * @copyright   2015 Plumps Creative Limited
 */
class Acl_admin extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->library('ion_auth');
        $this->load->library('hmw');
        $this->load->library('form_validation');
        $this->load->library('ion_auth_acl');
        
        if( ! $this->ion_auth->logged_in() )
            redirect('/auth/login');

        if( ! $this->ion_auth_acl->has_permission('access_admin') )
            redirect('/dashboard');
    }

    public function index()
    {
        redirect('/acl_admin/manage');
    }

    public function manage()
    {
        $headers = $this->hmw->headerVars(0, "/dashboard", "ACL Admin");
        $this->load->view('jq_header_pre', $headers['header_pre']);
        $this->load->view('jq_header_post', $headers['header_post']);
        $this->load->view('acl_admin/manage');
        $this->load->view('jq_footer');
    }

    public function permissions()
    {
        $data['permissions']    =   $this->ion_auth_acl->permissions('full');
        $data['categories']     =   $this->ion_auth_acl->permissions_categories();
        
        $headers = $this->hmw->headerVars(0, "/acl_admin/manage", "Manage Permissions");
        $this->load->view('jq_header_pre', $headers['header_pre']);
        $this->load->view('jq_header_post', $headers['header_post']);
        $this->load->view('acl_admin/permissions', $data);
        $this->load->view('jq_footer');
    }
    
    public function permissions_categories()
    {
      $data['permissions']    =   $this->ion_auth_acl->permissions_categories();
      
      $headers = $this->hmw->headerVars(0, "/acl_admin/manage", "Manage Categories");
      $this->load->view('jq_header_pre', $headers['header_pre']);
      $this->load->view('jq_header_post', $headers['header_post']);
      $this->load->view('acl_admin/permissions_categories', $data);
      $this->load->view('jq_footer');
    }

    public function add_permission()
    {
        if( $this->input->post() && $this->input->post('cancel') )
            redirect('/acl_admin/permissions', 'refresh');

        $this->form_validation->set_rules('perm_key', 'key', 'required|trim');
        $this->form_validation->set_rules('perm_name', 'name', 'required|trim');

        $this->form_validation->set_message('required', 'Please enter a %s');

        if( $this->form_validation->run() === FALSE )
        {
            $data['message'] = ($this->ion_auth_acl->errors() ? $this->ion_auth_acl->errors() : $this->session->flashdata('message'));
            
            $headers = $this->hmw->headerVars(0, "/acl_admin/permissions", "Add Permission");
            $this->load->view('jq_header_pre', $headers['header_pre']);
            $this->load->view('jq_header_post', $headers['header_post']);
            $this->load->view('acl_admin/add_permission', $data);
            $this->load->view('jq_footer');
        }
        else
        {
            $new_permission_id = $this->ion_auth_acl->create_permission($this->input->post('perm_key'), $this->input->post('perm_name'));
            if($new_permission_id)
            {
                // check to see if we are creating the permission
                // redirect them back to the admin page
                $this->session->set_flashdata('message', $this->ion_auth->messages());
                redirect("/acl_admin/permissions", 'refresh');
            }
        }
    }

    public function update_permission()
    {
        if( $this->input->post() && $this->input->post('cancel') )
            redirect('acl_admin/permissions', 'refresh');

        $permission_id  =   $this->uri->segment(3);

        if( ! $permission_id )
        {
            $this->session->set_flashdata('message', "No permission ID passed");
            redirect("acl_admin/permissions", 'refresh');
        }

        $permission =   $this->ion_auth_acl->permission($permission_id);

        $this->form_validation->set_rules('perm_key', 'key', 'required|trim');
        $this->form_validation->set_rules('perm_name', 'name', 'required|trim');

        $this->form_validation->set_message('required', 'Please enter a %s');

        if( $this->form_validation->run() === FALSE )
        {
            $data['message']    = ($this->ion_auth_acl->errors() ? $this->ion_auth_acl->errors() : $this->session->flashdata('message'));
            $data['permission'] = $permission;
            $headers = $this->hmw->headerVars(0, "/acl_admin/permissions", "Edit Permission");
            $this->load->view('jq_header_pre', $headers['header_pre']);
            $this->load->view('jq_header_post', $headers['header_post']);
            $this->load->view('acl_admin/edit_permission', $data);
            $this->load->view('jq_footer');
        }
        else
        {
            $additional_data    =   array(
                'perm_name' =>  $this->input->post('perm_name')
            );

            $update_permission = $this->ion_auth_acl->update_permission($permission_id, $this->input->post('perm_key'), $additional_data);
            if($update_permission)
            {
                // check to see if we are creating the permission
                // redirect them back to the admin page
                $this->session->set_flashdata('message', $this->ion_auth->messages());
                redirect("/acl_admin/permissions", 'refresh');
            }
        }
    }

    public function delete_permission()
    {
        if( $this->input->post() && $this->input->post('cancel') )
            redirect('/acl_admin/permissions', 'refresh');

        $permission_id  =   $this->uri->segment(3);

        if( ! $permission_id )
        {
            $this->session->set_flashdata('message', "No permission ID passed");
            redirect("/acl_admin/permissions", 'refresh');
        }

        if( $this->input->post() && $this->input->post('delete') )
        {
            if( $this->ion_auth_acl->remove_permission($permission_id) )
            {
                $this->session->set_flashdata('message', $this->ion_auth->messages());
                redirect("/acl_admin/permissions", 'refresh');
            }
            else
            {
                echo $this->ion_auth_acl->messages();
            }
        }
        else
        {
            $data['message'] = ($this->ion_auth_acl->errors() ? $this->ion_auth_acl->errors() : $this->session->flashdata('message'));
            
            $headers = $this->hmw->headerVars(0, "/acl_admin/permissions", "Delete Permission");
            $this->load->view('jq_header_pre', $headers['header_pre']);
            $this->load->view('jq_header_post', $headers['header_post']);
            $this->load->view('/acl_admin/delete_permission', $data);
            $this->load->view('jq_footer');
        }
    }

    public function groups()
    {
        $data['groups'] = $this->ion_auth->groups()->result();
        
        $headers = $this->hmw->headerVars(0, "/acl_admin/manage", "Manage Groups");
        $this->load->view('jq_header_pre', $headers['header_pre']);
        $this->load->view('jq_header_post', $headers['header_post']);
        $this->load->view('/acl_admin/groups', $data);
        $this->load->view('jq_footer');
    }

    public function group_permissions()
    {
        if( $this->input->post() && $this->input->post('cancel') )
            redirect('/acl_admin/groups', 'refresh');

        $group_id  =   $this->uri->segment(3);

        if( ! $group_id )
        {
            $this->session->set_flashdata('message', "No group ID passed");
            redirect("acl_admin/groups", 'refresh');
        }

        if( $this->input->post() && $this->input->post('save') )
        {
            foreach($this->input->post() as $k => $v)
            {
                if( substr($k, 0, 5) == 'perm_' )
                {
                    $permission_id  =   str_replace("perm_","",$k);

                    if( $v == "X" )
                        $this->ion_auth_acl->remove_permission_from_group($group_id, $permission_id);
                    else
                        $this->ion_auth_acl->add_permission_to_group($group_id, $permission_id, $v);
                }
            }

            redirect('/acl_admin/groups', 'refresh');
        }

        $data['permissions']            =   $this->ion_auth_acl->permissions('full', 'perm_key');
        $data['group_permissions']      =   $this->ion_auth_acl->get_group_permissions($group_id);
          
        $headers = $this->hmw->headerVars(0, "/acl_admin/groups", "Group Permissions");
        $this->load->view('jq_header_pre', $headers['header_pre']);
        $this->load->view('jq_header_post', $headers['header_post']);  
        $this->load->view('/acl_admin/group_permissions', $data);
        $this->load->view('jq_footer');
    }

    public function users()
    {
        $data['users']  =   $this->ion_auth->users()->result();

        $headers = $this->hmw->headerVars(0, "/acl_admin/manage", "ACL Admin");
        $this->load->view('jq_header_pre', $headers['header_pre']);
        $this->load->view('jq_header_post', $headers['header_post']);
        $this->load->view('/acl_admin/users', $data);
        $this->load->view('jq_footer');
    }

    public function manage_user()
    {
        $user_id  =   $this->uri->segment(3);

        if( ! $user_id )
        {
            $this->session->set_flashdata('message', "No user ID passed");
            redirect("acl_admin/users", 'refresh');
        }

        $data['user']               =   $this->ion_auth->user($user_id)->row();
        $data['user_groups']        =   $this->ion_auth->get_users_groups($user_id)->result();
        $data['user_acl']           =   $this->ion_auth_acl->build_acl($user_id);

        $headers = $this->hmw->headerVars(0, "/dashboard", "ACL Admin");
        $this->load->view('jq_header_pre', $headers['header_pre']);
        $this->load->view('jq_header_post', $headers['header_post']);
        $this->load->view('/acl_admin/manage_user', $data);
        $this->load->view('jq_footer');
    }

    public function user_permissions()
    {
        $user_id  =   $this->uri->segment(3);

        if( ! $user_id )
        {
            $this->session->set_flashdata('message', "No user ID passed");
            redirect("acl_admin/users", 'refresh');
        }

        if( $this->input->post() && $this->input->post('cancel') )
            redirect("/acl_admin/manage_user/{$user_id}", 'refresh');


        if( $this->input->post() && $this->input->post('save') )
        {
            foreach($this->input->post() as $k => $v)
            {
                if( substr($k, 0, 5) == 'perm_' )
                {
                    $permission_id  =   str_replace("perm_","",$k);

                    if( $v == "X" )
                        $this->ion_auth_acl->remove_permission_from_user($user_id, $permission_id);
                    else
                        $this->ion_auth_acl->add_permission_to_user($user_id, $permission_id, $v);
                }
            }

            redirect("/acl_admin/manage_user/{$user_id}", 'refresh');
        }

        $user_groups    =   $this->ion_auth_acl->get_user_groups($user_id);

        $data['user_id']                =   $user_id;
        $data['permissions']            =   $this->ion_auth_acl->permissions('full', 'perm_key');
        $data['group_permissions']      =   $this->ion_auth_acl->get_group_permissions($user_groups);
        $data['users_permissions']      =   $this->ion_auth_acl->build_acl($user_id);

        $headers = $this->hmw->headerVars(0, "/dashboard", "ACL Admin");
        $this->load->view('jq_header_pre', $headers['header_pre']);
        $this->load->view('jq_header_post', $headers['header_post']);
        $this->load->view('/acl_admin/user_permissions', $data);
        $this->load->view('jq_footer');
    }

}