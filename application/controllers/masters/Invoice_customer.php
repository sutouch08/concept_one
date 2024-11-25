<?php
class Invoice_customer extends PS_Controller
{
  public $menu_code = 'DBCMIV';
  public $menu_group_code = 'DB';
  public $menu_sub_group_code = 'CUSTOMER';
  public $title = 'รายชื่อลูกค้า POS';
  public $segment = 5;

  public function __construct()
  {
    $this->load->model('masters/invoice_customer_model');
  }

  public function index()
  {
    
  }

  public function get_invoice_customer_by_tax()
  {
    $sc = TRUE;
    $ds = array();

    $tax_id = trim($this->input->get('tax_id'));

    if( ! empty($tax_id))
    {
      $ds = $this->invoice_customer_model->get_by_tax_id($tax_id);

      if(empty($ds))
      {
        $sc = FALSE;
        $this->error = "ไม่พบข้อมูล";
      }
    }
    else
    {
      $sc = FALSE;
      set_error('required');
    }

    $arr = array(
      'status' => $sc === TRUE ? 'success' : 'failed',
      'message' => $sc === TRUE ? 'success' : $this->error,
      'data' => $sc === TRUE ? $ds : NULL
    );

    echo json_encode($arr);
  }
}

 ?>
