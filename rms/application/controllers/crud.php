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
	
	
		$this->grocery_crud->set_table('checklist_tasks');
		$this->grocery_crud->fields('id_checklist','name','comment','priority','active','order','day_week_num','day_month_num');
        $this->grocery_crud->required_fields('id_checklist','name','priority','active','order');
		$this->grocery_crud->display_as('priority','Priority<br />(1=normal, 2=medium, 3=high)')->display_as('day_week_num','Number of the day-week<br />(0=Sunday, 1=Monday...)')->display_as('day_month_num','Number of the day-month <br />(1,2,3...28)')->display_as('id_checklist','Checklist');
		$this->grocery_crud->set_relation('id_checklist','checklists','name');

        $output = $this->grocery_crud->render();

		$this->_example_output($output); 
    }

    public function cklChecklists()
    {
		$this->grocery_crud->fields('id','name','active','order','id_bu');
        $this->grocery_crud->set_table('checklists');
        $output = $this->grocery_crud->render();
 
		$this->_example_output($output); 
    }

    public function Sensors()
    {
		$this->grocery_crud->columns('id','name','reference');
		$this->grocery_crud->required_fields('name','reference');
        $this->grocery_crud->set_table('sensors');
        $output = $this->grocery_crud->render();
 
		$this->_example_output($output); 
    }

    public function SensorsAlarm()
    {
		$this->grocery_crud->columns('id_sensor','max', 'min');
		$this->grocery_crud->set_relation('id_sensor','sensors','name');
		$this->grocery_crud->required_fields('id_sensor','max', 'min');
        $this->grocery_crud->set_table('sensors_alarm');
        $output = $this->grocery_crud->render();
 
		$this->_example_output($output); 
    }

    public function rmdTasks()
    {
		$this->grocery_crud->columns('id','task', 'comment', 'active', 'priority', 'id_bu');
		$this->grocery_crud->required_fields('task', 'prority', 'active');
        $this->grocery_crud->set_table('rmd_tasks');
        $output = $this->grocery_crud->render();
 
		$this->_example_output($output); 
    }

    public function rmdMeta()
    {
		$this->grocery_crud->columns('id_task', 'start','repeat_interval','repeat_year','repeat_month','repeat_day','repeat_week','repeat_weekday');
		$this->grocery_crud->set_relation('id_task','rmd_tasks','task');
		$this->grocery_crud->required_fields('id_task','start');
        $this->grocery_crud->set_table('rmd_meta');
        $output = $this->grocery_crud->render();
 
		$this->_example_output($output); 
    }

    public function rmdNotif()
    {
		$this->grocery_crud->columns('id_task', 'start','end','interval');
		$this->grocery_crud->set_relation('id_task','rmd_tasks','task');
		$this->grocery_crud->required_fields('id_task','start','end','interval');
        $this->grocery_crud->set_table('rmd_notif');
        $output = $this->grocery_crud->render();
 
		$this->_example_output($output); 
    }

    public function productsUnit()
    {
        $this->grocery_crud->set_table('products_unit');
        $output = $this->grocery_crud->render();
 
		$this->_example_output($output); 
    }

    public function productsStock()
    {
        $this->grocery_crud->set_table('products_stock');
        $output = $this->grocery_crud->render();
 
		$this->_example_output($output); 
    }

    public function productsCategory()
    {
        $this->grocery_crud->set_table('products_category');
        $output = $this->grocery_crud->render();
 
		$this->_example_output($output); 
    }

    public function products()
    {
		$this->grocery_crud->set_relation('id_supplier','suppliers','name');
		$this->grocery_crud->set_relation('id_unit','products_unit','name');
		$this->grocery_crud->set_relation('id_category','products_category','name');
        $this->grocery_crud->set_table('products');
        $output = $this->grocery_crud->render();
 
		$this->_example_output($output); 
    }

    public function productsAttribut()
    {
		$this->grocery_crud->set_relation('id_product','products','name');
        $this->grocery_crud->set_table('products_attribut');
        $output = $this->grocery_crud->render();
 
		$this->_example_output($output); 
    }

    public function suppliersCategory()
    {
        $this->grocery_crud->set_table('suppliers_category');
        $output = $this->grocery_crud->render();
 
		$this->_example_output($output); 
    }

    public function suppliers()
    {
		$this->grocery_crud->set_relation('id_category','suppliers_category','name');
		$this->grocery_crud->set_relation('id_bu','bus','name');
        $this->grocery_crud->set_table('suppliers');
        $output = $this->grocery_crud->render();
 
		$this->_example_output($output); 
    }
    
    public function discount()
    {
        $this->grocery_crud->columns('id', 'client', 'nature', 'reason', 'date','id_user', 'id_bu');
        $this->grocery_crud->required_fields('id', 'client', 'nature', 'reason', 'date', 'id_user');
        $this->grocery_crud->set_table('discount');
        $output = $this->grocery_crud->render();
 
        $this->_example_output($output); 
    }

    public function report()
    {
        $this->grocery_crud->columns('id', 'name', 'text');
        $this->grocery_crud->required_fields('id', 'name', 'text', 'bu_id');
        $this->grocery_crud->set_table('report_subjects');
        $output = $this->grocery_crud->render();
 
        $this->_example_output($output); 
    }

    public function skills()
    {
        $this->grocery_crud->columns('id', 'name', 'deleted');
        $this->grocery_crud->required_fields('id', 'name');
        $this->grocery_crud->set_table('skills');
        $output = $this->grocery_crud->render();
 
        $this->_example_output($output); 
    }

    public function skills_item()
    {
        $this->grocery_crud->columns('id', 'id_skills', 'name', 'id_cat', 'id_sub_cat', 'deleted');
        $this->grocery_crud->required_fields('id', 'id_skills', 'name', 'id_cat', 'id_sub_cat');
        $this->grocery_crud->set_table('skills_item');
        $output = $this->grocery_crud->render();
 
        $this->_example_output($output); 
    }

    public function skills_record()
    {
        $this->grocery_crud->columns('id', 'id_sponsor', 'id_user');
        $this->grocery_crud->required_fields('id', 'id_sponsor', 'id_user');
        $this->grocery_crud->set_table('skills_record');
        $output = $this->grocery_crud->render();
 
        $this->_example_output($output); 
    }

    public function skills_log()
    {
        $this->grocery_crud->columns('id', 'id_skills_record', 'date');
        $this->grocery_crud->required_fields('id', 'id_skills_record', 'date');
        $this->grocery_crud->set_table('skills_log');
        $output = $this->grocery_crud->render();
 
        $this->_example_output($output); 
    }

    public function skills_category()
    {
        $this->grocery_crud->columns('id', 'name', 'deleted');
        $this->grocery_crud->required_fields('id', 'name');
        $this->grocery_crud->set_table('skills_category');
        $output = $this->grocery_crud->render();
 
        $this->_example_output($output); 
    }

    public function skills_sub_category()
    {
        $this->grocery_crud->columns('id', 'name', 'deleted');
        $this->grocery_crud->required_fields('id', 'name');
        $this->grocery_crud->set_table('skills_sub_category');
        $output = $this->grocery_crud->render();
 
        $this->_example_output($output); 
    }

    public function skills_record_item()
    {
        $this->grocery_crud->columns('id', 'id_skills_record', 'id_skills_item', 'checked', 'comment');
        $this->grocery_crud->required_fields('id', 'id_skills_record', 'id_skills_item', 'checked');
        $this->grocery_crud->set_table('skills_record_item');
        $output = $this->grocery_crud->render();
 
        $this->_example_output($output); 
    }

    function _example_output($output = null)
 
    {
        $this->load->view('crud.php',$output);    
    }


}
