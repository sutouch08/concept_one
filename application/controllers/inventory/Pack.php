<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Pack extends PS_Controller
{
  public $menu_code = 'ICCKQC';
	public $menu_group_code = 'IC';
  public $menu_sub_group_code = 'CHECK';
	public $title = 'ตรวจสอบ QC';
  public $filter;
  public $error;
  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'inventory/pack';
    $this->load->model('inventory/pack_model');
  }


  public function index()
  {
    $filter = array(
      'order_code' => get_filter('order_code', 'order_code', ''),
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
		$rows     = $this->pack_model->count_rows($filter);
		//--- ส่งตัวแปรเข้าไป 4 ตัว base_url ,  total_row , perpage = 20, segment = 3
		$init	    = pagination_config($this->home.'/index/', $rows, $perpage, $segment);
		$ds   = $this->pack_model->get_data($filter, $perpage, $this->uri->segment($segment));

    $filter['data'] = $ds;

		$this->pagination->initialize($init);
    $this->load->view('inventory/pack/pack_view', $filter);
  }



	public function delete_row()
	{
    $this->load->model('orders/orders_model');

		$sc = TRUE;
		$id = trim($this->input->post('id'));

		if(!empty($id))
		{
      $row = $this->pack_model->get($id);

      if( ! empty($row))
      {
        if($this->pack_model->delete($id))
        {
          //-- unvalid qc on order details
          $this->orders_model->update_detail($row->order_detail_id, ['valid_qc' => '0']);
        }
        else
        {
          $sc = FALSE;
  				$this->error = "ลบรายการไม่สำเร็จ";
        }
      }
      else
      {
        $sc = FALSE;
        $this->error = "Invalid id";
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
    $filter = array('order_code', 'pd_code', 'from_date', 'to_date');

    clear_filter($filter);
  }


} //--- end class
?>
