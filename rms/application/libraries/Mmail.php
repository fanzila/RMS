<?php

class Mmail
{
	public function sendEmail($email, $dest = [], $id_bu = null)
	{

		$CI = &get_instance();

		$CI->load->library('email');

		$config = array();
		$config['charset'] = 'utf-8';
		$config['mailtype'] = 'html';
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
		$CI->email->message(nl2br($email['msg']));
		$CI->email->send();
	}

	public function prepare($subject, $body)
	{
		return new RMS_Email($subject, $body);
	}
	
	public function templateEmail($inc)
	{
		$tpl = "<!doctype html>
			<html>
		<head>
		<meta name='viewport' content='width=device-width'>
		<meta http-equiv='Content-Type' content='text/html; charset=UTF-8'>
		<title>RMS</title>
		<style>

		@media only screen and (max-width: 720px) {
			table[class=body] h1 {
				font-size: 28px !important;
				margin-bottom: 10px !important;
			}
			table[class=body] p,
			table[class=body] ul,
			table[class=body] ol,
			table[class=body] td,
			table[class=body] span,
			table[class=body] a {
				font-size: 16px !important;
			}
			table[class=body] .wrapper,
			table[class=body] .article {
				padding: 10px !important;
			}
			table[class=body] .content {
				padding: 0 !important;
			}
			table[class=body] .container {
				padding: 0 !important;
				width: 100% !important;
			}
			table[class=body] .main {
				border-left-width: 0 !important;
				border-radius: 0 !important;
				border-right-width: 0 !important;
			}
			table[class=body] .btn table {
				width: 100% !important;
			}
			table[class=body] .btn a {
				width: 100% !important;
			}
			table[class=body] .img-responsive {
				height: auto !important;
				max-width: 100% !important;
				width: auto !important;
			}
		}

		@media all {
			.ExternalClass {
				width: 100%;
			}
			.ExternalClass,
			.ExternalClass p,
			.ExternalClass span,
			.ExternalClass font,
			.ExternalClass td,
			.ExternalClass div {
				line-height: 100%;
			}
			.apple-link a {
				color: inherit !important;
				font-family: inherit !important;
				font-size: inherit !important;
				font-weight: inherit !important;
				line-height: inherit !important;
				text-decoration: none !important;
			}
			#MessageViewBody a {
				color: inherit;
				text-decoration: none;
				font-size: inherit;
				font-family: inherit;
				font-weight: inherit;
				line-height: inherit;
			}
			.btn-primary table td:hover {
				background-color: #34495e !important;
			}
			.btn-primary a:hover {
				background-color: #34495e !important;
				border-color: #34495e !important;
			}
		}
		hr {
			border-style: solid;
			border-width: 1px;
			border-color: silver;
			width:90%;
		}
		</style>
		</head>
		<body class='' style='background-color: #f6f6f6; font-family: sans-serif; -webkit-font-smoothing: antialiased; font-size: 14px; line-height: 1.4; margin: 0; padding: 0; -ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%;'>
		<table border='0' cellpadding='0' cellspacing='0' class='body' style='border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; width: 100%; background-color: #f6f6f6;'>
		<tr>
		<td style='font-family: sans-serif; font-size: 14px; vertical-align: top;'>&nbsp;</td>
		<td class='container' style='font-family: sans-serif; font-size: 14px; vertical-align: top; display: block; Margin: 0 auto; max-width: 720px; padding: 10px; width: 720px;'>
		<div class='content' style='box-sizing: border-box; display: block; Margin: 0 auto; max-width: 720px; padding: 10px;'>

		<!-- START CENTERED WHITE CONTAINER -->
		<span class='preheader' style='color: transparent; display: none; height: 0; max-height: 0; max-width: 0; opacity: 0; overflow: hidden; mso-hide: all; visibility: hidden; width: 0;'>RMS CLOSE REPORT</span>
		<table class='main' style='border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; width: 100%; background: #ffffff; border-radius: 3px;'>

		<!-- START MAIN CONTENT AREA -->
		<tr>
		<td class='wrapper' style='font-family: sans-serif; font-size: 14px; vertical-align: top; box-sizing: border-box; padding: 20px;'>
		<table border='0' cellpadding='0' cellspacing='0' style='border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; width: 100%;'>
		<tr>
		<td style='font-family: sans-serif; font-size: 14px; vertical-align: top;'>
				  		
		$inc

		</td>
		</tr>
		</table>
		</td>
		</tr>

		<!-- END MAIN CONTENT AREA -->
		</table>

		<!-- START FOOTER -->
		<div class='footer' style='clear: both; Margin-top: 10px; text-align: center; width: 100%;'>
		<table border='0' cellpadding='0' cellspacing='0' style='border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; width: 100%;'>
		<tr>
		<td class='content-block' style='font-family: sans-serif; vertical-align: top; padding-bottom: 10px; padding-top: 10px; font-size: 12px; color: #999999; text-align: center;'>
		<span class='apple-link' style='color: #999999; font-size: 12px; text-align: center;'>© HANK Restaurants<br />Please contact administrator if you do not wish to receive this email.</span>
		</td>
		</tr>

		</table>
		</div>
		<!-- END FOOTER -->

		<!-- END CENTERED WHITE CONTAINER -->
		</div>
		</td>
		<td style='font-family: sans-serif; font-size: 14px; vertical-align: top;'>&nbsp;</td>
		</tr>
		</table>
		</body>
		</html>";  
		return $tpl;
	}
}

class RMS_Email
{
	private $subject;
	private $body;
	private $from;
	private $to;
	private $cc;
	private $type;
	private $reply_to;
	private $attach;
	private $hooks;

	public function __construct($subject, $body)
	{
		$this->subject = $subject;
		$this->body = $body;

		$this->type = 'html';
		$this->from = [
			'email' => 'noreply@hankrestaurant.com',
			'name'  => 'RMS'
		];
		$this->to = [];
		$this->cc = null;
		$this->reply_to = null;
		$this->attach = null;
		$this->hooks = [];
	}

	public function type($type)
	{
		$this->type = $type;
		return $this;
	}

	public function replyTo($reply_to)
	{
		$this->reply_to = $reply_to;
		return $this;
	}

	public function from($email, $name = 'RMS')
	{
		$this->from['email'] = $email;
		$this->from['name']  = $name;

		return $this;
	}

	public function cc($cc)
	{
		$this->cc = $cc;

		return $this;
	}

	public function attach($attach)
	{
		$this->attach = $attach;

		return $this;
	}

	public function toEmail($email)
	{
    
		if (strpos($email, ',')) {
			$list = explode(',', $email);
			$this->to = array_merge($this->to, $list);
			return $this;
		}
	
		if (is_array($email)) {
			$this->to = array_merge($this->to, $email);
		} else {
			array_push($this->to, $email);
		}
	
		return $this;
	}

	public function toUser($id_user)
	{
		$CI = &get_instance();
		$CI->load->database();

		$CI->db->select('email');
		$CI->db->from('users');

		if (is_array($id_user))
			$CI->db->where_in('id', $id_user);
		else
			$CI->db->where('id', $id_user);

		$user = $CI->get->row();

		array_push($this->to, $user->email);

		return $this;
	}

	public function toGroup($group_id, $id_bu = null)
	{
		$CI = &get_instance();
		$CI->load->database();

		$CI->db->select('u.email');
		$CI->db->from('users AS u');
		$CI->db->distinct('u.email');
		$CI->db->join('users_groups AS g', 'u.id = g.user_id');

		if (is_array($group_id))
			$CI->db->where_in('g.group_id', $group_id);
		else
			$CI->db->where('g.group_id', $group_id);

		if (!empty($id_bu))
		{
			$CI->db->join('users_bus AS b', 'u.id = b.user_id', 'left');

			if (is_array($id_bu))
				$CI->db->where_in('b.id_bu', $id_bu);
			else
				$CI->db->where('b.id_bu', $id_bu);
		}

		$CI->db->where('u.active', 1);
		$result = $CI->db->get()->result();

		foreach ($result as $user)
			array_push($this->to, $user->email);

		return $this;
	}

	public function toList($list_name, $id_bu = null)
	{
		$CI = &get_instance();
		$CI->load->database();

		$CI->db->select('u.email');
		$CI->db->from('users AS u');
		$CI->db->distinct('u.email');
		$CI->db->join('users_notifications AS lu', 'u.id = lu.user_id');
		$CI->db->join('notifications AS l', 'l.id = lu.notification_id');

		if (is_array($list_name))
			$CI->db->where_in('l.name', $list_name);
		else
			$CI->db->where('l.name', $list_name);

		if (!empty($id_bu))
		{
			$CI->db->join('users_bus AS b', 'u.id = b.user_id', 'left');

			if (is_array($id_bu))
				$CI->db->where_in('b.id_bu', $id_bu);
			else
				$CI->db->where('b.id_bu', $id_bu);
		}

		$CI->db->where('u.active', 1);

		$result = $CI->db->get()->result();

		foreach ($result as $user)
			array_push($this->to, $user->email);

		return $this;
	}

	public function before($cb)
	{
		$this->hooks['before'] = $cb;
		return $this;
	}

	public function after($cb)
	{
		$this->hooks['after'] = $cb;
		return $this;
	}

	public function send()
	{
		$config = [
			'charset'   => 'utf-8',
			'mailtype'  => 'html',
			'crlf'      => "\n",
			'newline'   => "\n",
			'from'      => $this->from['email'],
			'from_name' => $this->from['name'],
			'mailtype'  => $this->type
		];

		$success = [];
		$fail = [];

		$emails = array_unique($this->to);

		foreach ($emails as $to)
		{
			if ($this->sendOne($config, $to))
				array_push($success, $to);
			else
				array_push($fail, $to);
		}

		return [
			'success' => !count($fail),
			'sent'    => $success,
			'unsent'  => $fail
		];
	}

	private function sendOne($config, $to)
	{
		$CI = &get_instance();
		$CI->load->library('email');

		$subject   = $this->subject;
		$body      = $this->body;
		$from      = $config['from'];
		$from_name = $config['from_name'];
		$cc        = isset($this->cc) && !empty($this->cc)
			? $this->cc
				: NULL;
		$reply_to  = isset($this->reply_to) && !empty($this->reply_to)
			? $this->reply_to
				: NULL;

		if (array_key_exists('before', $this->hooks) && is_callable($this->hooks['before']))
		{
			$before_args = [
				'subject'   => $subject,
				'body'      => $body,
				'from'      => $from,
				'from_name' => $from,
				'cc'        => $cc,
				'reply_to'  => $from
			];

			$before_args_read = [
				'email' => $to,
				'type' => $this->type
			];

			// use array_merge to make a copy of the first array
			$result = $this->hooks['before'](array_merge($before_args_read, $before_args));

			if (!empty($result))
			{
				foreach ($before_args as $key => $original)
				{
					if (array_key_exists($key, $result) && !empty($result[$key]))
						$$key = $result[$key];
				}
			}
		}

		$CI->email->initialize($config);
		$CI->email->clear(TRUE);

		$CI->email->from($from, $from_name);
		$CI->email->to($to);
		$CI->email->subject($subject);

		//if ($this->type === 'html')
		//	$body = nl2br($body);

		$CI->email->message($body);

		if (!empty($cc))
			$CI->email->cc($cc);

		if (!empty($reply_to))
			$CI->email->reply_to($reply_to, 'HANK');

		if (isset($this->attach))
			$CI->email->attach($this->attach);

		$result = $CI->email->send(TRUE);

		if (array_key_exists('after', $this->hooks) && is_callable($this->hooks['after']))
		{
			$this->hooks['after']([
				'email'   => $to,
				'success' => $result
					]);
			}

			return $result;
		}
  
	}
