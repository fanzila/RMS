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

  public function toEmail($email)
  {
    if (is_array($email))
      $this->to = array_merge($this->to, $email);
    else
      array_push($this->to, $email);

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
      $CI->db->where('b.bu_id', $id_bu);
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
    $CI->db->join('users_mails_lists AS lu', 'u.id = lu.user_id');
    $CI->db->join('mails_lists AS l', 'l.id = lu.mail_list_id');

    if (is_array($list_name))
      $CI->db->where_in('l.name', $list_name);
    else
      $CI->db->where('l.name', $list_name);

    if (!empty($id_bu))
    {
      $CI->db->join('users_bus AS b', 'u.id = b.user_id', 'left');
      $CI->db->where('b.bu_id', $id_bu);
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

    if (!$this->type === 'html')
      $body = nl2br($body);

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
