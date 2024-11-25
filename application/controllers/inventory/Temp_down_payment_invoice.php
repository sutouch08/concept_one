<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class temp_down_payment_invoice extends PS_Controller
{
  public $menu_code = 'TEDPIV';
	public $menu_group_code = 'TE';
  public $menu_sub_group_code = 'TEDPM';
	public $title = 'ใบกำกับภาษีเงินมัดจำ - ถังกลาง';
  public $filter;
  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'inventory/Temp_down_payment_invoice';
    $this->load->model('inventory/temp_down_payment_invoice_model');
  }


  public function index()
  {
    $filter = array(
      'code' => get_filter('code', 'temp_code', ''),
      'customer' => get_filter('customer', 'temp_customer', ''),
      'from_date' => get_filter('from_date', 'temp_from_date', ''),
      'to_date' => get_filter('to_date', 'temp_to_date', ''),
      'status' => get_filter('status', 'temp_status', 'all')
    );

		//--- แสดงผลกี่รายการต่อหน้า
		$perpage = get_rows();

		$segment  = 4; //-- url segment
		$rows     = $this->temp_down_payment_invoice_model->count_rows($filter);
		//--- ส่งตัวแปรเข้าไป 4 ตัว base_url ,  total_row , perpage = 20, segment = 3
		$init	    = pagination_config($this->home.'/index/', $rows, $perpage, $segment);
		$orders   = $this->temp_down_payment_invoice_model->get_list($filter, $perpage, $this->uri->segment($segment));

    $filter['orders'] = $orders;

		$this->pagination->initialize($init);
    $this->load->view('inventory/temp_down_payment_invoice/temp_list', $filter);
  }


  public function get_detail($id)
  {
    $ds['doc'] = $this->temp_down_payment_invoice_model->get($id);
    $ds['details'] = $this->temp_down_payment_invoice_model->get_detail($id);
    $this->load->view('inventory/temp_down_payment_invoice/temp_detail', $ds);
  }


	function delete_temp($id)
	{
		$sc = TRUE;
		$doc = $this->temp_down_payment_invoice_model->get($id);

		if(!empty($doc))
		{
			if($doc->F_Sap === 'N' OR $doc->F_Sap == NULL)
			{
				$this->mc->trans_begin();
				$ds = $this->temp_down_payment_invoice_model->delete_temp_details($id);
				$rs = $this->temp_down_payment_invoice_model->delete_temp($id);

				if($ds && $rs)
				{
					$this->mc->trans_commit();
				}
				else
				{
					$this->mc->trans_rollback();
					$sc = FALSE;
					$this->error = "Delete Temp failed";
				}
			}
		}
		else
		{
			$sc = FALSE;
			$this->error = "ยกเลิกไม่สำเร็จ เอกสารถูกนำเข้า SAP แล้ว";
		}

		echo $sc === TRUE ? 'success' : $this->error;
	}

  public function clear_filter()
  {
    $filter = array(
      'temp_code',
      'temp_customer',
      'temp_from_date',
      'temp_to_date',
      'temp_status'
    );

    return clear_filter($filter);    
  }

}//--- end class
?>
