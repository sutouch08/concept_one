<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Tongthai_temp_order extends PS_Controller
{
	public $title = 'Tongthai Orders';
	public $menu_code = 'APITTP';
	public $menu_group_code = 'TONGTHAI';
  public $menu_sub_group_code = '';
  public $filter;

  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'rest/V1/tongthai_temp_order';
		$this->tt = $this->load->database('tt', TRUE); //--- Tongthai database
  	$this->load->model('rest/V1/tongthai_order_model');
  }


  public function index()
  {
    $filter = array(
      'reference' => get_filter('reference', 'tt_ref', ''),
      'company' => get_filter('company', 'tt_company', ''),
			'name' => get_filter('name', 'tt_name', ''),
      'phone' => get_filter('phone', 'tt_phone', ''),
			'from_date' => get_filter('from_date', 'tt_from_date', ''),
			'to_date' => get_filter('to_date', 'tt_to_date', '')
    );



		//--- แสดงผลกี่รายการต่อหน้า
		$perpage = get_rows();
		//--- หาก user กำหนดการแสดงผลมามากเกินไป จำกัดไว้แค่ 300
		if($perpage > 300)
		{
			$perpage = 20;
		}

		$segment  = 5; //-- url segment
		$rows     = $this->tongthai_order_model->count_rows($filter);
		//--- ส่งตัวแปรเข้าไป 4 ตัว base_url ,  total_row , perpage = 20, segment = 3
		$init	    = pagination_config($this->home.'/index/', $rows, $perpage, $segment);
		$data   = $this->tongthai_order_model->get_list($filter, $perpage, $this->uri->segment($segment));

    $filter['data'] = $data;

		$this->pagination->initialize($init);
    $this->load->view('rest/V1/tongthai/order_list', $filter);
  }



	public function view_detail($id)
	{
		$order = $this->tongthai_order_model->get($id);
		$details = $this->tongthai_order_model->get_details($id);

		$ds = array(
			'order' => $order,
			'details' => $details
		);

		$this->load->view('rest/V1/tongthai/order_detail', $ds);
	}


	public function get_order_qty($id)
	{
		return $this->tongthai_order_model->get_order_qty($id);
	}


	public function get_order_amount($id)
	{
		return $this->tongthai_order_model->get_order_amount($id);
	}

	public function clear_filter()
	{
		$filter = array(
			'tt_ref',
			'tt_name',
			'tt_phone',
			'tt_company',
			'tt_from_date',
			'tt_to_date'
		);

		clear_filter($filter);
		echo "done";
	}

} //--- end classs
?>
