<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Crud extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
	
		$this->load->database();
		$this->load->helper('url');

		$this->load->library('grocery_CRUD');
		
		$this->load->library('ion_auth');

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
		
		$crud->fields('id','name','active','order','id_bu');
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
		
		$crud->columns('id','task', 'comment', 'active', 'priority', 'id_bu');
		$crud->required_fields('task', 'prority', 'active');
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
		$crud = new grocery_CRUD();
		$crud->set_theme('bootstrap');
		
        $crud->columns('id', 'name', 'deleted');
        $crud->required_fields('id', 'name');
        $crud->set_table('skills');
        $output = $crud->render();
 
        $this->_example_output($output); 
    }

    public function skills_item()
    {
		$crud = new grocery_CRUD();
		$crud->set_theme('bootstrap');
		
        $crud->columns('id', 'id_skills', 'name', 'id_cat', 'id_sub_cat', 'deleted');
        $crud->required_fields('id', 'id_skills', 'name', 'id_cat', 'id_sub_cat');
        $crud->set_table('skills_item');
        $output = $crud->render();
 
        $this->_example_output($output); 
    }

    public function skills_record()
    {
		$crud = new grocery_CRUD();
		$crud->set_theme('bootstrap');
		
        $crud->columns('id', 'id_sponsor', 'id_user');
        $crud->required_fields('id', 'id_sponsor', 'id_user');
        $crud->set_table('skills_record');
        $output = $crud->render();
 
        $this->_example_output($output); 
    }

    public function skills_log()
    {
		$crud = new grocery_CRUD();
		$crud->set_theme('bootstrap');
		
        $crud->columns('id', 'id_skills_record', 'date');
        $crud->required_fields('id', 'id_skills_record', 'date');
        $crud->set_table('skills_log');
        $output = $crud->render();
 
        $this->_example_output($output); 
    }

    public function skills_category()
    {
		$crud = new grocery_CRUD();
		$crud->set_theme('bootstrap');
		
        $crud->columns('id', 'name', 'deleted');
        $crud->required_fields('id', 'name');
        $crud->set_table('skills_category');
        $output = $crud->render();
 
        $this->_example_output($output); 
    }

    public function skills_sub_category()
    {
		$crud = new grocery_CRUD();
		$crud->set_theme('bootstrap');
		
        $crud->columns('id', 'name', 'deleted');
        $crud->required_fields('id', 'name');
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
        $crud->set_table('skills_record_item');
        $output = $crud->render();
 
        $this->_example_output($output); 
    }

	public function _example_output($output = null)
	{
		$this->load->view('crud.php',$output);
	}



}