<?php

class Mmail {
		
	public function sendEmail($email) {
	
		// CI 
		$CI =& get_instance();
		
		$CI->load->library('email');
		
		$config = array();
		$config['charset'] = 'utf-8';
		$config['mailtype'] = 'text';
		$config['crlf'] = "\n";
		$config['newline'] = "\n";
		
		$config['from'] = 'noreply@hankrestaurant.com';
		$config['from_name'] = 'RMS';
		if(isset($email['from'])) {
		 	$config['from'] = $email['from'];
			$config['from_name'] = $email['from_name'];
		}
		
		if(isset($email['mailtype'])) $config['mailtype'] = $email['mailtype'];
		$CI->email->initialize($config);
		
		$CI->email->clear(TRUE);
		
		$CI->email->from($config['from'], $config['from_name']);
		$CI->email->to($email['to']);
		if(isset($email['cc'])) $CI->email->cc($email['cc']); 
		if(isset($email['replyto'])) $CI->email->reply_to($email['replyto'], 'HANK');
		if(isset($email['attach'])) $CI->email->attach($email['attach']);
		$CI->email->subject($email['subject']);
		$CI->email->message($email['msg']);

		$CI->email->send();
	}
}
?>
