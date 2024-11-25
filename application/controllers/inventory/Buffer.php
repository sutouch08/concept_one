<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Buffer extends PS_Controller
{
  public $menu_code = 'ICCKBF';
	public $menu_group_code = 'IC';
  public $menu_sub_group_code = 'CHECK';
	public $title = 'ตรวจสอบ BUFFER';
  public $filter;
  public $error;
  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'inventory/buffer';
    $this->load->model('inventory/buffer_model');
    $this->load->model('orders/orders_model');
  }


  public function index()
  {
    $filter = array(
      'order_code' => get_filter('order_code', 'order_code', ''),
      'zone_code' => get_filter('zone_code', 'zone_code', ''),
      'pd_code' => get_filter('pd_code', 'pd_code'),
      'from_date' => get_filter('from_date', 'from_date', ''),
      'to_date' => get_filter('to_date', 'to_date', ''),
      'user' => get_filter('user', 'user', 'all')
    );

		//--- แสดงผลกี่รายการต่อหน้า
		$perpage = get_rows();
		//--- หาก user กำหนดการแสดงผลมามากเกินไป จำกัดไว้แค่ 300
		if($perpage > 300)
		{
			$perpage = 20;
		}

		$segment  = 4; //-- url segment
		$rows     = $this->buffer_model->count_rows($filter);
		//--- ส่งตัวแปรเข้าไป 4 ตัว base_url ,  total_row , perpage = 20, segment = 3
		$init	    = pagination_config($this->home.'/index/', $rows, $perpage, $segment);
		$ds   = $this->buffer_model->get_data($filter, $perpage, $this->uri->segment($segment));

    $filter['data'] = $ds;

		$this->pagination->initialize($init);
    $this->load->view('inventory/buffer/buffer_view', $filter);
  }



	public function delete_buffer()
	{
		$sc = TRUE;

		$id = trim($this->input->post('id'));

		if(!empty($id))
		{
      $bf = $this->buffer_model->get($id);

      if( ! empty($bf))
      {
        if( ! $this->buffer_model->delete($id))
        {
          $sc = FALSE;
  				$this->error = "ลบรายการไม่สำเร็จ";
        }
      }
      else
      {
        $sc = FALSE;
        $this->error = "Invalid buffer id";
      }

      if($sc === TRUE)
      {
        $this->buffer_model->delete_prepared($bf->order_detail_id, $bf->zone_code);
        $this->orders_model->update_detail($bf->order_detail_id, ['valid' => '0']);
      }
		}
		else
		{
			$sc = FALSE;
			$this->error = "Missing required parameter : id";
		}

		echo $sc === TRUE ? 'success' : $this->error;
	}

  function clear_filter(){
    $filter = array('order_code', 'pd_code', 'zone_code', 'from_date', 'to_date', 'user');
    clear_filter($filter);
  }


} //--- end class
?>
