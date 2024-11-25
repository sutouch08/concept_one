<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Prepare_list extends PS_Controller
{
  public $menu_code = 'ICPREP';
	public $menu_group_code = 'IC';
  public $menu_sub_group_code = 'CHECK';
	public $title = 'ตรวจสอบ รายการจัดสินค้า';
  public $filter;
  public $error;
  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'inventory/prepare_list';
    $this->load->model('inventory/prepare_list_model');
    $this->load->model('orders/orders_model');
    $this->load->model('masters/zone_model');
    $this->load->helper('order');
  }


  public function index()
  {
    $filter = array(
      'order_code' => get_filter('order_code', 'order_code', ''),
      'zone_code' => get_filter('zone_code', 'zone_code', ''),
      'pd_code' => get_filter('pd_code', 'pd_code'),
      'from_date' => get_filter('from_date', 'from_date', ''),
      'to_date' => get_filter('to_date', 'to_date', '')
    );

		//--- แสดงผลกี่รายการต่อหน้า
		$perpage = get_rows();
		//--- หาก user กำหนดการแสดงผลมามากเกินไป จำกัดไว้แค่ 300
		if($perpage > 300)
		{
			$perpage = 20;
		}

		$segment  = 4; //-- url segment
		$rows     = $this->prepare_list_model->count_rows($filter);
		//--- ส่งตัวแปรเข้าไป 4 ตัว base_url ,  total_row , perpage = 20, segment = 3
		$init	    = pagination_config($this->home.'/index/', $rows, $perpage, $segment);
		$ds   = $this->prepare_list_model->get_list($filter, $perpage, $this->uri->segment($segment));

    $filter['data'] = $ds;

		$this->pagination->initialize($init);
    $this->load->view('inventory/prepare_list/prepare_list_view', $filter);
  }


  function clear_filter(){
    $filter = array('order_code', 'pd_code', 'zone_code', 'from_date', 'to_date');
    clear_filter($filter);
  }


} //--- end class
?>
