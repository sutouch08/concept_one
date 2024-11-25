<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Wms_temp_receive extends PS_Controller
{
	public $menu_code = 'WMSTRE';
	public $menu_group_code = 'WMS';
  public $menu_sub_group_code = 'WMS_RECEIVE';
	public $title = 'WMS Receive Temp';
  public $filter;

  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'rest/V1/wms_temp_receive';
		$this->wms = $this->load->database('wms', TRUE); //--- Temp database
  	$this->load->model('rest/V1/wms_error_logs_model');
		$this->load->model('rest/V1/wms_temp_receive_model');
  }


  public function index()
  {
    $filter = array(
      'code' => get_filter('code', 'receive_code', ''),
      'status' => get_filter('status', 'receive_status', 'all'),
			'type' => get_filter('type', 'receive_type', 'all'),
			'reference' => get_filter('reference', 'receive_reference', ''),
			'from_date' => get_filter('from_date', 'receive_from_date', ''),
			'to_date' => get_filter('to_date', 'receive_to_date', ''),
			'received_from_date' => get_filter('received_from_date', 'received_from_date', ''),
			'received_to_date' => get_filter('received_to_date', 'received_to_date', '')
    );

		//--- แสดงผลกี่รายการต่อหน้า
		$perpage = get_rows();
		//--- หาก user กำหนดการแสดงผลมามากเกินไป จำกัดไว้แค่ 300
		if($perpage > 300)
		{
			$perpage = 20;
		}

		$segment  = 5; //-- url segment
		$rows     = $this->wms_temp_receive_model->count_rows($filter);
		//--- ส่งตัวแปรเข้าไป 4 ตัว base_url ,  total_row , perpage = 20, segment = 3
		$init	    = pagination_config($this->home.'/index/', $rows, $perpage, $segment);
		$orders   = $this->wms_temp_receive_model->get_list($filter, $perpage, $this->uri->segment($segment));

    $filter['orders'] = $orders;

		$this->pagination->initialize($init);

    $this->load->view('rest/V1/temp_receive/temp_receive_list', $filter);
  }



	  public function get_detail($id)
	  {
			$order = $this->wms_temp_receive_model->get($id);

	    $ds['details'] = $this->wms_temp_receive_model->get_details($id);
			$ds['code'] = !empty($order) ? $order->code : NULL;
	    $this->load->view('rest/V1/temp_receive/temp_receive_detail', $ds);
	  }



	public function clear_filter()
	{
		$filter = array(
			'receive_code',
			'receive_status',
			'receive_type',
			'receive_reference',
			'receive_from_date',
			'receive_to_date',
			'received_from_date',
			'received_to_date'
		);

		clear_filter($filter);
		echo "done";
	}


	public function delete($id)
	{
		$sc = TRUE;
		$rs = $this->wms_temp_receive_model->delete($id);
		if(! $rs)
		{
			$sc = FALSE;
			$this->error = "Delete failed";
		}

		echo $sc === TRUE ? 'success' : $this->error;
	}


	public function close_temp($id)
	{
		$sc = TRUE;
		$arr = array(
			'status' => 2,
			'closed_by' => $this->_user->name
		);

		if(! $this->wms_temp_receive_model->update($id, $arr))
		{
			$sc = FALSE;
			$this->error = "Closed failed";
		}

		echo $sc === TRUE ? json_encode($arr) : $this->error;
	}

} //--- end classs
?>
