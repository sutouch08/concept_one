<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Temp_items extends PS_Controller
{
  public $menu_code = 'TEPDCK';
	public $menu_group_code = 'TE';
  public $menu_sub_group_code = 'TEMASTER';
	public $title = 'Item Masters';
  public $filter;

  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'inventory/temp_items';
    $this->load->model('inventory/temp_items_model');
  }


  public function index()
  {
    $filter = array(
      'code'          => get_filter('code', 'temp_pd_code', ''),
      'from_date'     => get_filter('from_date', 'temp_pd_from_date', ''),
      'to_date'       => get_filter('to_date', 'temp_pd_to_date', ''),
      'status'      => get_filter('status', 'temp_pd_status', 'all')
    );

		//--- แสดงผลกี่รายการต่อหน้า
		$perpage = get_rows();
		//--- หาก user กำหนดการแสดงผลมามากเกินไป จำกัดไว้แค่ 300
		if($perpage > 300)
		{
			$perpage = 20;
		}

		$segment  = 4; //-- url segment
		$rows     = $this->temp_items_model->count_rows($filter, 8);
		//--- ส่งตัวแปรเข้าไป 4 ตัว base_url ,  total_row , perpage = 20, segment = 3
		$init	    = pagination_config($this->home.'/index/', $rows, $perpage, $segment);
		$items   = $this->temp_items_model->get_list($filter, $perpage, $this->uri->segment($segment), 8);

    $filter['items'] = $items;

		$this->pagination->initialize($init);
    $this->load->view('inventory/temp_items/temp_items', $filter);
  }


  public function clear_filter()
  {
    $filter = array(
      'temp_pd_code',
      'temp_pd_from_date',
      'temp_pd_to_date',
      'temp_pd_status'
    );

    clear_filter($filter);

    echo 'done';
  }

}//--- end class
?>
