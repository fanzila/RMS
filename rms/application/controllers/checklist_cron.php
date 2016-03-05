<?php 
class Checklist_cron extends CI_Controller {

	//cd /var/www/hank/HMW/hmw && php index.php checklist_cron index

	public function __construct()
	{
		parent::__construct();
		$this->load->database();
		$this->load->library('mmail');
		
	}

	public function index($id)
	{

		if($this->input->is_cli_request()) {

			if($id == 3) $name ="opening";
			if($id == 4) $name ="closing";
			
			$msg = "WARINING! No $name checklist have been created!";
			
			$sql 	= "SELECT DATE(`date`) FROM ckl_checklist_records WHERE DATE(`date`) = DATE(NOW()) AND id_checklist = $id";
			$query	= $this->db->query($sql);
			$res 	= $query->result();

			if(empty($res)) {	
								
				$email['to']		= 'checklist@hankrestaurant.com';
				$email['subject'] 	= $msg;
				$email['msg'] 		= $msg;
				$this->mmail->sendEmail($email);
				
				$sms = array();
				$sms['to']			= "email2sms@ovh.net";
				$sms['subject'] 	= "sms-dp131762-1:hanksms:gxistf23:HANK:+33647384930,+33650925448:::1";
				$sms['msg'] 		= $email['subject'];
				#$this->mmail->sendEmail($sms);
				
			}
			return;
		} else { 
			echo "Access refused.";
			return; 
		}


	}	
}
?>