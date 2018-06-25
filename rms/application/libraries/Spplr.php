<?php

class Spplr
{
  public function __construct()
  {
		$CI = &get_instance();
		$CI->load->database();
    $CI->load->library('ion_auth');
		$CI->load->library('hmw');
  }

  const UPDATABLE_FIELDS = [
    'name',
    'main_product',
    'location',
    'delivery_days',
    'carriage_paid',
    'payment_type',
    'payment_delay',
    'contact_sale_name',
    'contact_sale_tel',
    'contact_sale_email',
    'contact_order_name',
    'contact_order_tel',
    'contact_order_email',
    'order_method',
    'website',
    'comment_internal',
    'comment_order',
    'comment_delivery',
    'comment_delivery_info',
    'simple_order_form',
    'no_chased_email',
    'active'
  ];

  public function getUpdatableFields() {
    return self::UPDATABLE_FIELDS;
  }

  public function getAllSuppliers($id_bu)
  {
    $CI = &get_instance();

    $CI->db->select(implode(', ', [
      's.id',
      's.name',
      's.main_product',
      's.location',
      's.delivery_days',
      's.carriage_paid',
      's.payment_type',
      's.payment_delay',
      's.contact_sale_name',
      's.contact_sale_tel',
      's.contact_sale_email',
      's.contact_order_name',
      's.contact_order_tel',
      's.contact_order_email',
      's.order_method',
      's.website',
      's.comment_internal',
      's.comment_order',
      's.comment_delivery',
      's.comment_delivery_info',
      's.simple_order_form',
      's.no_chased_email',
      's.active',
      'c.name AS category_name',
      'c.active AS category_active'
    ]));
    $CI->db->from('suppliers AS s');
    $CI->db->join('suppliers_category AS c', 's.id_category = c.id AND c.active = 1');
    $CI->db->where('s.id_bu', $id_bu);
    $CI->db->where('s.deleted', 0);
    $CI->db->order_by('s.active DESC, s.name ASC');

    return $CI->db->get()->result();
  }

  public function getAllCategories()
  {
    $CI = &get_instance();

    $CI->db->select('id, name, active');
    $CI->db->from('suppliers_category');
    $CI->db->where('deleted', 0);
    $CI->db->order_by('active DESC, name ASC');

    return $CI->db->get()->result();
  }

  public function save($data, $id = null)
  {
    $CI = &get_instance();

    if (!empty($id))
    {
      $update = $this->convertData($data);

      foreach ($update as $field => $value)
        $CI->db->set($field, $value);

      $CI->db->where('id', $id);
      $success = $CI->db->update('suppliers');
    }
    else
    {
      $success = $CI->db->insert('suppliers', $this->convertData($data));
    }

    return !$success
      ? [ 'success' => false, 'message' => $this->db->_error_message() ]
      : [ 'success' => true ];
  }

  private function convertData($raw)
  {
    $data = [];

    $number_fields = [
      'id_category',
      'carriage_paid',
      'active',
      'deleted',
      'simple_order_form',
      'no_chased_email',
      'id_bu'
    ];

    foreach (self::UPDATABLE_FIELDS as $field)
    {
      if (array_key_exists($field, $raw))
      {
        $value = $raw[$field];

        if (in_array($field, $number_fields))
          $value = intval($value);

        $data[$field] = $value;
      }
    }

    return $data;
  }
}
