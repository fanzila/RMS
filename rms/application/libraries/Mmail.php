<?php

class Mmail
{

  public function sendEmail($email, $dest = [], $id_bu = null)
  {

    // CI
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

  public function toEmail($email)
  {
    if (is_array($email))
      $this->email = array_merge($this->email, $email);
    else
      array_push($this->email, $email);

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

    $this->db->select('u.email');
    $this->db->from('users AS u');
    $this->db->distinct('u.email');
    $this->db->join('users_groups AS g', 'u.id = g.user_id');

    if (is_array($group_id))
      $this->db->where_in('g.group_id', $group_id);
    else
      $this->db->where('g.group_id', $group_id);

    if (!empty($id_bu))
    {
      $this->db->join('users_bus AS b', 'u.id = b.user_id', 'left');
      $this->db->where('b.bu_id', $id_bu);
    }

    $this->db->where('u.active', 1);
    $result = $this->db->get()->result();

    foreach ($result as $user)
      array_push($this->to, $user->email);

    return $this;
  }

  public function toList($list_name, $id_bu = null)
  {
    $CI = &get_instance();
    $CI->load->database();

    $this->db->select('u.email');
    $this->db->from('users AS u');
    $this->db->distinct('u.email');
    $this->db->join('users_mails_lists AS lu', 'u.id = lu.user_id');
    $this->db->join('mails_lists AS l', 'l.id = lu.mail_list_id');

    if (is_array($list_name))
      $this->db->where_in('l.name', $list_name);
    else
      $this->db->where('l.name', $list_name);

    if (!empty($id_bu))
    {
      $this->db->join('users_bus AS b', 'u.id = b.user_id', 'left');
      $this->db->where('b.bu_id', $id_bu);
    }

    $this->db->where('u.active', 1);

    $result = $this->db->get()->result();

    foreach ($result as $user)
      array_push($this->to, $user->email);

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

    foreach ($this->to as $to)
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

    $CI->email->initialize($config);
    $CI->email->clear(TRUE);

    $CI->email->from($config['from'], $config['from_name']);
    $CI->email->to($to);
    $CI->email->subject($this->subject);

    $body = $this->type === 'html'
      ? nl2br($this->body)
      : $this->body;
    $CI->email->message($body);

    if (isset($this->cc))
      $CI->email->cc($this->cc);

    if (isset($this->reply_to))
      $CI->email->reply_to($this->reply_to, 'HANK');

    if (isset($this->attach))
      $CI->email->attach($this->attach);

    return $CI->email->send(TRUE);
  }
}
