<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Temp_consignment_goods_receipt extends PS_Controller
{
  public $menu_code = 'TECMGN';
	public $menu_group_code = 'TE';
  public $menu_sub_group_code = 'TECONSIGNMENT';
	public $title = 'ตรวจสอบ Goods Receipt ฝากขายเทียม';
  public $filter;
  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'inventory/temp_consignment_goods_receipt';
    $this->load->model('inventory/temp_consignment_goods_receipt_model');
  }


  public function index()
  {
    $filter = array(
      'code'          => get_filter('code', 'temp_receive_code', ''),
      'from_date'     => get_filter('from_date', 'temp_receive_from_date', ''),
      'to_date'       => get_filter('to_date', 'temp_receive_to_date', ''),
      'status'      => get_filter('status', 'temp_receive_status', 'all')
    );

		//--- แสดงผลกี่รายการต่อหน้า
		$perpage = get_rows();
		//--- หาก user กำหนดการแสดงผลมามากเกินไป จำกัดไว้แค่ 300
		if($perpage > 300)
		{
			$perpage = 20;
		}

		$segment  = 4; //-- url segment
		$rows     = $this->temp_consignment_goods_receipt_model->count_rows($filter, 8);
		//--- ส่งตัวแปรเข้าไป 4 ตัว base_url ,  total_row , perpage = 20, segment = 3
		$init	    = pagination_config($this->home.'/index/', $rows, $perpage, $segment);
		$orders   = $this->temp_consignment_goods_receipt_model->get_list($filter, $perpage, $this->uri->segment($segment), 8);

    $filter['orders'] = $orders;

		$this->pagination->initialize($init);
    $this->load->view('inventory/temp_consignment_goods_receipt/temp_list', $filter);
  }



  public function get_detail($docEntry)
  {
    $this->load->model('stock/stock_model');
    $detail = $this->temp_consignment_goods_receipt_model->get_detail($docEntry);
    $code = "";
    if(!empty($detail))
    {
      foreach($detail as $rs)
      {
        $rs->onhand = $this->stock_model->get_stock_zone($rs->BinCode, $rs->ItemCode);
        $code = $rs->U_ECOMNO;
      }
    }

    $ds['details'] = $detail;
    $ds['code'] = $code;
    $this->load->view('inventory/temp_consignment_goods_receipt/temp_detail', $ds);
  }



  public function clear_filter()
  {
    $filter = array(
      'temp_receive_code',
      'temp_receive_from_date',
      'temp_receive_to_date',
      'temp_receive_status'
    );

    clear_filter($filter);

    echo 'done';
  }

}//--- end class
?>
