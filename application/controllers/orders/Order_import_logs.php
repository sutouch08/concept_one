<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Order_import_logs extends PS_Controller
{
	public $title = 'Order Import Logs';
	public $menu_code = 'SOIMPL';
	public $menu_group_code = 'SO';
  public $menu_sub_group_code = '';
  public $filter;

  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'orders/order_import_logs';
  	$this->load->model('orders/order_import_logs_model');
  }


  public function index()
  {
    $filter = array(
			'reference' => get_filter('reference', 'logs_reference', ''),
      'order_code' => get_filter('order_code', 'logs_order_code', ''),
      'status' => get_filter('status', 'logs_status', 'all'),
			'action' => get_filter('action', 'logs_action', 'all'),
			'from_date' => get_filter('from_date', 'from_date', ''),
			'to_date' => get_filter('to_date', 'to_date', '')
    );

		if($this->input->post('search'))
		{
			redirect($this->home);
		}
		else
		{
			//--- แสดงผลกี่รายการต่อหน้า
			$perpage = get_rows();

			$segment = 4; //-- url segment
			$rows = $this->order_import_logs_model->count_rows($filter);
			//--- ส่งตัวแปรเข้าไป 4 ตัว base_url ,  total_row , perpage = 20, segment = 3
			$init	= pagination_config($this->home.'/index/', $rows, $perpage, $segment);
			$logs = $this->order_import_logs_model->get_list($filter, $perpage, $this->uri->segment($segment));

			$filter['logs'] = $logs;

			$this->pagination->initialize($init);
			$this->load->view('order_import_logs/logs_view', $filter);
		}

  }

	public function clear_filter()
	{
		$filter = array(
			'logs_reference',
			'log_order_code',
			'logs_status',
			'logs_action',
			'from_date',
			'to_date'
		);

		return clear_filter($filter);
	}

} //--- end classs
?>
