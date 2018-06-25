<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Supplier extends CI_Controller
{

  public function __construct()
  {
    parent::__construct();

    $this->load->database();
    $this->load->library('ion_auth');
    $this->load->library('ion_auth_acl');
    $this->load->library('hmw');
    $this->load->library('spplr');
  }

  public function admin()
  {
    $this->hmw->keyLogin();
    $this->hmw->changeBu();
    $id_bu = $this->session->userdata('bu_id');

    $data = [
      'suppliers'      => $this->spplr->getAllSuppliers($id_bu),
      'empty_supplier' => $this->createEmptySupplier(true),
      'categories'     => $this->spplr->getAllCategories(),
      'payment_types'  => [ 'LCR',   'WIRE', 'CHEQ', 'CARD', 'CASH', 'DEBIT' ],
      'order_methods'  => [ 'email', 'tel',  'fax',  'www' ],
      'bu_name'        => $this->session->userdata('bu_name'),
      'username'       => $this->session->userdata('identity')
    ];

    $headers = $this->hmw->headerVars(0, '/supplier/admin', 'Supplier admin');

    $this->load->view('jq_header_pre', $headers['header_pre']);
    $this->load->view('reminder/jq_header_spe');
    $this->load->view('jq_header_post', $headers['header_post']);
    $this->load->view('supplier/supplier_admin', $data);
    $this->load->view('jq_footer');
  }

  public function save()
  {
		$id_bu = $this->session->userdata('bu_id');

		$data = $this->input->post();

    if (array_key_exists('id', $data) && !empty($data['id']))
      $result = $this->spplr->save($data, $data['id']);
    else
      $result = $this->spplr->save($data);

    if (!$result['success'])
    {
      return print(json_encode([
        'success' => false,
        'message' => $result['message']
      ]));
    }

    return print(json_encode([
      'success' => true
    ]));
  }

  private function createEmptySupplier($withForeign = false) {
    $supplier = new StdClass();
    $updatable = $this->spplr->getUpdatableFields();

    foreach ($updatable as $field)
      $supplier->$field = null;

    if ($withForeign)
    {
      $supplier->category_name = null;
      $supplier->category_active = null;
    }

    $supplier->active = 1;

    return $supplier;
  }
}
