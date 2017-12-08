<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Crud extends CI_Controller {
	
	public function __construct()
	{
		parent::__construct();
	
		$this->load->database();
		$this->load->helper('url');

		$this->load->library('grocery_CRUD');
		$this->load->library('hmw');
		$this->load->library('ion_auth');

		$this->hmw->isLoggedIn();

		$group_info = $this->ion_auth_model->get_users_groups()->result();
		if ($group_info[0]->level < 1)
		{
			$this->session->set_flashdata('message', 'You must be a gangsta to view this page');
			redirect('/news/');
		}
		
		$id_bu = $this->session->userdata('bu_id');		
	
	}

    public function cklChecklistTasks()
    {
	
		$crud = new grocery_CRUD();
		$crud->set_theme('bootstrap');
		
		
		$crud->set_table('checklist_tasks');
		$crud->fields('id_checklist','name','comment','priority','active','order','day_week_num','day_month_num');
        $crud->required_fields('id_checklist','name','priority','active','order');
		$crud->display_as('priority','Priority<br />(1=normal, 2=medium, 3=high)')->display_as('day_week_num','Number of the day-week<br />(0=Sunday, 1=Monday...)')->display_as('day_month_num','Number of the day-month <br />(1,2,3...28)')->display_as('id_checklist','Checklist');
		$crud->set_relation('id_checklist','checklists','name');

        $output = $crud->render();

		$this->_example_output($output); 
    }

    public function cklChecklists()
    {
	
		$crud = new grocery_CRUD();
		$crud->set_theme('bootstrap');
		
		$crud->fields('id','name','active','order','id_bu','type');
        $crud->set_table('checklists');
        $output = $crud->render();
 
		$this->_example_output($output); 
    }

    public function Sensors()
    {
		$crud = new grocery_CRUD();
		$crud->set_theme('bootstrap');
		
		$crud->columns('id','name','reference');
		$crud->required_fields('name','reference');
        $crud->set_table('sensors');
        $output = $crud->render();
 
		$this->_example_output($output); 
    }

    public function SensorsAlarm()
    {
		$crud = new grocery_CRUD();
		$crud->set_theme('bootstrap');
		
		$crud->columns('id_sensor','max', 'min');
		$crud->set_relation('id_sensor','sensors','name');
		$crud->required_fields('id_sensor','max', 'min');
        $crud->set_table('sensors_alarm');
        $output = $crud->render();
 
		$this->_example_output($output); 
    }

    public function StockLog()
    {
		$crud = new grocery_CRUD();
		$crud->set_theme('bootstrap');
		
		
		$crud->unset_edit();
		$crud->unset_delete();
		$crud->unset_add();
		$crud->unset_operations();
		
		$crud->columns('type','date', 'keylogin', 'user_id','val1','val2','val3','val4','id_bu' );
		$crud->set_relation('user_id','users','username');
		$crud->set_relation('val1','products','name');
		$crud->set_relation('id_bu','bus','name');
		$crud->display_as('user_id','Username');
		$crud->display_as('val1','Product');
		$crud->display_as('id_bu','BU');
		$crud->display_as('val2','Qtty set');
		$crud->display_as('val3','Order num (if set)');
		$crud->display_as('val4','Previous stock (if set)');
        $crud->set_table('log');
        $output = $crud->render();
 
		$this->_example_output($output); 
    }

    public function rmdTasks()
    {
		$crud = new grocery_CRUD();
		$crud->set_theme('bootstrap');
		
		$crud->columns('id','task', 'comment', 'active', 'priority', 'id_bu', 'type');
		$crud->required_fields('task', 'prority', 'active', 'type');
        $crud->set_table('rmd_tasks');
        $output = $crud->render();
 
		$this->_example_output($output); 
    }

    public function rmdMeta()
    {
		$crud = new grocery_CRUD();
		$crud->set_theme('bootstrap');
		
		$crud->columns('id_task', 'start','repeat_interval','repeat_year','repeat_month','repeat_day','repeat_week','repeat_weekday');
		$crud->set_relation('id_task','rmd_tasks','task');
		$crud->required_fields('id_task','start');
        $crud->set_table('rmd_meta');
        $output = $crud->render();
 
		$this->_example_output($output); 
    }

    public function rmdNotif()
    {
		$crud = new grocery_CRUD();
		$crud->set_theme('bootstrap');
		
		$crud->columns('id_task', 'start','end','interval');
		$crud->set_relation('id_task','rmd_tasks','task');
		$crud->required_fields('id_task','start','end','interval');
        $crud->set_table('rmd_notif');
        $output = $crud->render();
 
		$this->_example_output($output); 
    }

    public function productsUnit()
    {
		$crud = new grocery_CRUD();
		$crud->set_theme('bootstrap');
		
        $crud->set_table('products_unit');
        $output = $crud->render();
 
		$this->_example_output($output); 
    }

    public function productsStock()
    {
		$crud = new grocery_CRUD();
		$crud->set_theme('bootstrap');
		
        $crud->set_table('products_stock');
        $output = $crud->render();
 
		$this->_example_output($output); 
    }

    public function productsCategory()
    {
		$crud = new grocery_CRUD();
		$crud->set_theme('bootstrap');
		
        $crud->set_table('products_category');
        $output = $crud->render();
 
		$this->_example_output($output); 
    }

    public function products()
    {
		$crud = new grocery_CRUD();
		$crud->set_theme('bootstrap');
		
		$crud->set_relation('id_supplier','suppliers','name');
		$crud->set_relation('id_unit','products_unit','name');
		$crud->set_relation('id_category','products_category','name');
        $crud->set_table('products');
        $output = $crud->render();
 
		$this->_example_output($output); 
    }

    public function productsAttribut()
    {
		$crud = new grocery_CRUD();
		$crud->set_theme('bootstrap');
		
		$crud->set_relation('id_product','products','name');
        $crud->set_table('products_attribut');
        $output = $crud->render();
 
		$this->_example_output($output); 
    }

    public function suppliersCategory()
    {	
		$crud = new grocery_CRUD();
		$crud->set_theme('bootstrap');
		
        $crud->set_table('suppliers_category');
        $output = $crud->render();
 
		$this->_example_output($output); 
    }

    public function suppliers()
    {
		$crud = new grocery_CRUD();
		$crud->set_theme('bootstrap');
		
		$crud->set_relation('id_category','suppliers_category','name');
		$crud->set_relation('id_bu','bus','name');
        $crud->set_table('suppliers');
        $output = $crud->render();
 
		$this->_example_output($output); 
    }
    
    public function discount()
    {
		$crud = new grocery_CRUD();
		$crud->set_theme('bootstrap');
		
        $crud->columns('id', 'client', 'nature', 'reason', 'date','id_user', 'id_bu');
        $crud->required_fields('id', 'client', 'nature', 'reason', 'date', 'id_user');
        $crud->set_table('discount');
        $output = $crud->render();
 
        $this->_example_output($output); 
    }

    public function report()
    {
		$crud = new grocery_CRUD();
		$crud->set_theme('bootstrap');
		
        $crud->columns('id', 'name', 'text');
        $crud->required_fields('id', 'name', 'text', 'bu_id');
        $crud->set_table('report_subjects');
        $output = $crud->render();
 
        $this->_example_output($output); 
    }

    public function skills()
    {
		$id_bu = $this->session->userdata('bu_id');
		
		$crud = new grocery_CRUD();
		$crud->set_theme('bootstrap');
		
        $crud->columns('id', 'name', 'order', 'deleted', 'id_bu');
        $crud->set_relation('id_bu', 'bus', 'name');
        $crud->required_fields('id', 'name', 'id_bu');
		$crud->where('id_bu',$id_bu);
        $crud->set_table('skills');
        $output = $crud->render();
 
        $this->_example_output($output); 
    }

    public function skills_item()
    {
		$id_bu = $this->session->userdata('bu_id');
	
		$crud = new grocery_CRUD();
		$crud->set_theme('bootstrap');
		
        $crud->columns('id', 'id_skills', 'name', 'id_cat', 'id_sub_cat', 'order', 'deleted');
        $crud->required_fields('id', 'id_skills', 'name', 'id_cat', 'id_sub_cat');

    	$crud->set_relation('id_skills', 'skills', 'name',array('id_bu' => $id_bu));
	    $crud->set_relation('id_cat', 'skills_category', 'name',array('id_bu' => $id_bu));
        $crud->set_relation('id_sub_cat', 'skills_sub_category', 'name',array('id_bu' => $id_bu));

		$crud->display_as('id_skills', 'Skills');
		$crud->display_as('id_cat', 'Category');
		$crud->display_as('id_sub_cat', 'Sub-category');
		$crud->where('jb56cddaf.id_bu',$id_bu);
		$crud->set_table('skills_item');
        $output = $crud->render();

        $this->_example_output($output); 
    }

    public function skills_record()
    {
		$id_bu = $this->session->userdata('bu_id');
		
		$crud = new grocery_CRUD();
		$crud->set_theme('bootstrap');
		
        $crud->columns('id', 'id_sponsor', 'id_user', 'id_bu');
        $crud->set_relation('id_sponsor', 'users', 'username');
        $crud->set_relation('id_bu', 'bus', 'name');
		$crud->set_relation('id_user', 'users', 'username');
		$crud->display_as('id_sponsor', 'Sponsor');
		$crud->display_as('id_user', 'Users');
		$crud->required_fields('id', 'sponsor', 'id_user');
		$crud->where('id_bu',$id_bu);
        $crud->set_table('skills_record');
        $output = $crud->render();
 
        $this->_example_output($output); 
    }

    public function skills_log()
    {
		$crud = new grocery_CRUD();
		$crud->set_theme('bootstrap');
		
        //$crud->columns('id', 'id_skills_record', 'date');
        $crud->required_fields('id', 'id_skills_record', 'date');
        $crud->set_table('skills_log');
		$crud->set_relation('id_user', 'users', 'username');
		$crud->set_relation('bu_id', 'bus', 'name');
		$crud->display_as('id_user', 'username');
		$crud->unset_add();
  		$crud->unset_edit();
  		$crud->unset_delete();
        $output = $crud->render();
 
        $this->_example_output($output); 
    }

    public function skills_category()
    {
		$id_bu = $this->session->userdata('bu_id');
	
		$crud = new grocery_CRUD();
		$crud->set_theme('bootstrap');
		
        $crud->columns('id', 'name', 'deleted', 'order', 'id_bu');
        $crud->required_fields('id', 'name', 'order', 'id_bu');
		$crud->set_relation('id_bu', 'bus', 'name',array('id' => $id_bu));
		$crud->where('id_bu',$id_bu);
        $crud->set_table('skills_category');
        $output = $crud->render();
 
        $this->_example_output($output); 
    }

    public function skills_sub_category()
    {
		$id_bu = $this->session->userdata('bu_id');
		
		$crud = new grocery_CRUD();
		$crud->set_theme('bootstrap');
        $crud->columns('id', 'name', 'order', 'deleted', 'id_bu');
        $crud->required_fields('id', 'name', 'id_bu');
		$crud->set_relation('id_bu', 'bus', 'name',array('id' => $id_bu));
		$crud->where('id_bu',$id_bu);
        $crud->set_table('skills_sub_category');
        $output = $crud->render();
 
        $this->_example_output($output); 
    }

    public function skills_record_item()
    {
		$crud = new grocery_CRUD();
		$crud->set_theme('bootstrap');
		
        $crud->columns('id', 'id_skills_record', 'id_skills_item', 'checked', 'comment');
        $crud->required_fields('id', 'id_skills_record', 'id_skills_item', 'checked');
		$crud->set_relation('id_skills_item', 'skills_item', 'name');
		$crud->display_as('id_skills_item', 'Skills item');
        $crud->set_table('skills_record_item');
        $output = $crud->render();
 
        $this->_example_output($output); 
    }
		
		public function wpRmsGroups()
		{
			$crud = new grocery_CRUD();
			$crud->set_theme('bootstrap');
			
				$crud->columns('wp_role', 'id_group_rms');
				$crud->required_fields('wp_role', 'id_group_rms');
				$crud->set_relation('id_group_rms', 'groups', 'name');
				$crud->display_as('id_group_rms', 'RMS Group');
				$crud->set_table('wp_roles');
				$output = $crud->render();
				$this->_example_output($output);
		}

		public function customers_api_keys($id_bu)
		{
			$crud = new grocery_CRUD();
			$crud->set_theme('bootstrap');
			
				$crud->columns('id', 'name', 'api_key');
				if (!empty($id_bu)) $crud->where('id', $id_bu);
				$crud->edit_fields('api_key');
				$crud->display_as('name', 'BU Name');
				$crud->display_as('api_key', 'API Key');
				$crud->add_action('Delete Key', '', '/customers/deleteApiKey');
				$crud->unset_delete();
				$crud->unset_add();
				$crud->unset_read();
				$crud->set_table('bus');
				$output = $crud->render();
				$this->_example_output($output);
		}
		
		public function permissions()
		{
			$crud = new grocery_CRUD();
			$crud->set_theme('bootstrap');
			
			$crud->columns('id', 'perm_key', 'perm_name');
			$crud->set_table('permissions');
			$output = $crud->render();
			$this->_example_output($output);
		}
		
		public function group_permissions()
		{
			$crud = new grocery_CRUD();
			$crud->set_theme('bootstrap');
			
			$crud->columns('id', 'group_id', 'perm_id', 'value', 'created_at', 'updated_at');
			$crud->display_as('group_id', 'group');
			$crud->display_as('perm_id', 'permission');
			$crud->set_relation('group_id', 'groups', 'name');
			$crud->set_relation('perm_id', 'permissions', 'perm_name');
			$crud->set_table('groups_permissions');
			$output = $crud->render();
			$this->_example_output($output);
		}

	public function _example_output($output = null)
	{
		$this->load->view('crud.php',$output);
	}



}