<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Temp_goods_issue extends PS_Controller
{
  public $menu_code = 'TEGICK';
	public $menu_group_code = 'TE';
  public $menu_sub_group_code = 'TEADJUST';
	public $title = 'ตรวจสอบ Goods Issue';
  public $filter;
  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'inventory/temp_goods_issue';
    $this->load->model('inventory/temp_goods_issue_model');
  }


  public function index()
  {
    $filter = array(
      'code'          => get_filter('code', 'temp_issue_code', ''),
      'from_date'     => get_filter('from_date', 'temp_issue_from_date', ''),
      'to_date'       => get_filter('to_date', 'temp_issue_to_date', ''),
      'status'      => get_filter('status', 'temp_issue_status', 'all')
    );

		//--- แสดงผลกี่รายการต่อหน้า
		$perpage = get_rows();
		//--- หาก user กำหนดการแสดงผลมามากเกินไป จำกัดไว้แค่ 300
		if($perpage > 300)
		{
			$perpage = 20;
		}

		$segment  = 4; //-- url segment
		$rows     = $this->temp_goods_issue_model->count_rows($filter, 8);
		//--- ส่งตัวแปรเข้าไป 4 ตัว base_url ,  total_row , perpage = 20, segment = 3
		$init	    = pagination_config($this->home.'/index/', $rows, $perpage, $segment);
		$orders   = $this->temp_goods_issue_model->get_list($filter, $perpage, $this->uri->segment($segment), 8);

    $filter['orders'] = $orders;

		$this->pagination->initialize($init);
    $this->load->view('inventory/temp_goods_issue/temp_list', $filter);
  }


  public function get_detail($docEntry)
  {
    $this->load->model('stock/stock_model');
    $detail = $this->temp_goods_issue_model->get_detail($docEntry);
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
    $this->load->view('inventory/temp_goods_issue/temp_detail', $ds);
  }


  public function remove_temp($docEntry)
	{
		$sc = TRUE;

		if(! $this->temp_goods_issue_model->removeTemp($docEntry))
		{
			$sc = FALSE;
			$this->error = "Delete failed";
		}

		echo $sc === TRUE ? 'success' : $this->error;
	}


  public function set_temp_to_success($docEntry)
  {
    $sc = TRUE;

    if( ! $this->temp_goods_issue_model->setStatus($docEntry, 'Y'))
    {
      $sc = FALSE;
      $this->error = "Failed to change status";
    }

    echo $sc === TRUE ? 'success' : $this->error;
  }


  public function clear_filter()
  {
    $filter = array(
      'temp_issue_code',
      'temp_issue_from_date',
      'temp_issue_to_date',
      'temp_issue_status'
    );

    clear_filter($filter);

    echo 'done';
  }

}//--- end class
?>
