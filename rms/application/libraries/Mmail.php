<?php

class Mmail extends CI_Controller  {
		
	public function sendEmail($email) {
	
		// CI 
		$CI =& get_instance();
		
		$CI->load->library('email');
		
		$config = array();
		$config['charset'] = 'utf-8';
		$config['from'] = 'www-data@hank1.isvtec.net';
		$config['from_name'] = 'HMW';
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
			
		/**  
		//************* PEAR MAIL **********
		require_once "Mail.php";

		$from = '<hankhnkmobile@gmail.com>';

		$headers = array(
		    'From' => $from,
		    'To' => $email['to'],
		    'Subject' => $email['subject']
		);

		$smtp = Mail::factory('smtp', array(
		        'host' => 'ssl://smtp.gmail.com',
		        'port' => '465',
		        'auth' => true,
		        'username' => 'hankhnkmobile@gmail.com',
		        'password' => ''
		    ));

		$mail = $smtp->send($email['to'], $headers, $email['msg']);

		if (PEAR::isError($mail)) {
		    echo('<p>' . $mail->getMessage() . '</p>');
		}
		
		/**
		// ***************** WITH MANDRILL MAIL ******************		
		require_once __DIR__.'/../libraries/mandrill/Mandrill.php'; //Not required with Composer
	
		try {
		    $mandrill = new Mandrill('');
		    $message = array(
		        'html' => '<p>Example HTML content</p>',
		        'text' => 'Example text content',
		        'subject' => 'example subject',
		        'from_email' => 'hmw@hankrestaurant.com',
		        'from_name' => 'Example Name',
		        'to' => array(
		            array(
		                'email' => 'pierre@doleans.net',
		                'name' => 'Recipient Name',
		                'type' => 'to'
		            )
		        ),
		        'headers' => array('Reply-To' => 'hmw@hankrestaurant.com'),
		        'important' => false,
		        'track_opens' => null,
		        'track_clicks' => null,
		        'auto_text' => null,
		        'auto_html' => null,
		        'inline_css' => null,
		        'url_strip_qs' => null,
		        'preserve_recipients' => null,
		        'view_content_link' => null,
		        'bcc_address' => null,
		        'tracking_domain' => null,
		        'signing_domain' => null,
		        'return_path_domain' => null,
		        'merge' => true,
		        'merge_language' => 'mailchimp',
		        'global_merge_vars' => array(
		            array(
		                'name' => 'merge1',
		                'content' => 'merge1 content'
		            )
		        ),
		        'merge_vars' => array(
		            array(
		                'rcpt' => 'pierre@doleans.net',
		                'vars' => array(
		                    array(
		                        'name' => 'merge2',
		                        'content' => 'merge2 content'
		                    )
		                )
		            )
		        ),
		        'tags' => array('password-resets'),
		        'subaccount' => 'customer-123',
		        'google_analytics_domains' => array(''),
		        'google_analytics_campaign' => '',
		        'metadata' => array('website' => ''),
		        'recipient_metadata' => array(
		            array(
		                'rcpt' => '',
		                'values' => array('user_id' => 123456)
		            )
		        ),
		        'attachments' => array(
		            array(
		                'type' => 'text/plain',
		                'name' => 'myfile.txt',
		                'content' => 'ZXhhbXBsZSBmaWxl'
		            )
		        ),
		        'images' => array(
		            array(
		                'type' => 'image/png',
		                'name' => 'IMAGECID',
		                'content' => 'ZXhhbXBsZSBmaWxl'
		            )
		        )
		    );
		    $async = false;
		    $ip_pool = 'Main Pool';
		    $send_at = null;
		    $result = $mandrill->messages->send($message, $async, $ip_pool, $send_at);
		    print_r($result);
		    
		} catch(Mandrill_Error $e) {
		    // Mandrill errors are thrown as exceptions
		    echo 'A mandrill error occurred: ' . get_class($e) . ' - ' . $e->getMessage();
		    // A mandrill error occurred: Mandrill_Unknown_Subaccount - No subaccount exists with the id 'customer-123'
		    throw $e;
		}
		END MANDRILL 
		**/

}
?>
