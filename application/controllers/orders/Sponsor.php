<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Sponsor extends PS_Controller
{
  public $menu_code = 'SOODSP';
	public $menu_group_code = 'SO';
  public $menu_sub_group_code = 'ORDER';
	public $title = 'สปอนเซอร์';
  public $filter;
  public $error;
	public $isAPI;

  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'orders/sponsor';
    $this->load->model('orders/orders_model');
    $this->load->model('orders/sponsor_model');
    $this->load->model('masters/customers_model');
    $this->load->model('orders/order_state_model');
    $this->load->model('masters/product_tab_model');
    $this->load->model('stock/stock_model');
    $this->load->model('masters/product_style_model');
    $this->load->model('masters/products_model');

    $this->load->helper('order');
    $this->load->helper('customer');
    $this->load->helper('users');
    $this->load->helper('state');
    $this->load->helper('product_images');
    $this->load->helper('warehouse');

		$this->isAPI = is_true(getConfig('WMS_API'));
  }


  public function index()
  {
    $filter = array(
      'code' => get_filter('code', 'sponsor_code', ''),
      'customer' => get_filter('customer', 'sponsor_customer', ''),
      'user' => get_filter('user', 'sponsor_user', ''),
      'zone_code' => get_filter('zone', 'sponsor_zone', ''),
      'from_date' => get_filter('fromDate', 'sponsor_fromDate', ''),
      'to_date' => get_filter('toDate', 'sponsor_toDate', ''),
      'notSave' => get_filter('notSave', 'sponsor_notSave', NULL),
      'onlyMe' => get_filter('onlyMe', 'sponsor_onlyMe', NULL),
      'isExpire' => get_filter('isExpire', 'sponsor_isExpire', NULL),
      'isApprove' => get_filter('isApprove', 'sponsor_isApprove', 'all'),
			'warehouse' => get_filter('warehouse', 'sponsor_warehouse', ''),
      'sap_status' => get_filter('sap_status', 'sponsor_sap_status', 'all'),
			'wms_export' => get_filter('wms_export', 'sponsor_wms_export', 'all')
    );

    $state = array(
      '1' => get_filter('state_1', 'sponsor_state_1', 'N'),
      '2' => get_filter('state_2', 'sponsor_state_2', 'N'),
      '3' => get_filter('state_3', 'sponsor_state_3', 'N'),
      '4' => get_filter('state_4', 'sponsor_state_4', 'N'),
      '5' => get_filter('state_5', 'sponsor_state_5', 'N'),
      '6' => get_filter('state_6', 'sponsor_state_6', 'N'),
      '7' => get_filter('state_7', 'sponsor_state_7', 'N'),
      '8' => get_filter('state_8', 'sponsor_state_8', 'N'),
      '9' => get_filter('state_9', 'sponsor_state_9', 'N')
    );

    $state_list = array();

    $button = array();

    for($i =1; $i <= 9; $i++)
    {
    	if($state[$i] === 'Y')
    	{
    		$state_list[] = $i;
    	}

      $btn = 'state_'.$i;
      $button[$btn] = $state[$i] === 'Y' ? 'btn-info' : '';
    }

    $button['not_save'] = empty($filter['notSave']) ? '' : 'btn-info';
    $button['only_me'] = empty($filter['onlyMe']) ? '' : 'btn-info';
    $button['is_expire'] = empty($filter['isExpire']) ? '' : 'btn-info';


    $filter['state_list'] = empty($state_list) ? NULL : $state_list;

		//--- แสดงผลกี่รายการต่อหน้า
		$perpage = get_rows();
		//--- หาก user กำหนดการแสดงผลมามากเกินไป จำกัดไว้แค่ 300
		if($perpage > 300)
		{
			$perpage = 20;
		}

    $role     = 'P'; //--- P = sponsor;
		$segment  = 4; //-- url segment
		$rows     = $this->orders_model->count_rows($filter, $role);
		//--- ส่งตัวแปรเข้าไป 4 ตัว base_url ,  total_row , perpage = 20, segment = 3
		$init	    = pagination_config($this->home.'/index/', $rows, $perpage, $segment);
		$orders   = $this->orders_model->get_data($filter, $perpage, $this->uri->segment($segment), $role);
    $ds       = array();
    if(!empty($orders))
    {
      foreach($orders as $rs)
      {
        $rs->customer_name = $this->customers_model->get_name($rs->customer_code);
        $rs->total_amount  = $this->orders_model->get_order_total_amount($rs->code);
        $rs->state_name    = get_state_name($rs->state);
        $ds[] = $rs;
      }
    }

    $filter['orders'] = $ds;
    $filter['state'] = $state;
    $filter['btn'] = $button;

		$this->pagination->initialize($init);
    $this->load->view('sponsor/sponsor_list', $filter);
  }


  public function get_sponsor_budget($customer_code)
  {
    echo $this->get_budget($customer_code);
  }


  private function get_budget($customer_code)
  {
    $current = $this->sponsor_model->get_budget($customer_code);
    $used = $this->sponsor_model->get_budget_used($customer_code);

    return ($current - $used);
  }



  public function add_new()
  {
    $this->load->view('sponsor/sponsor_add');
  }



  public function add()
  {
		$this->load->model('masters/warehouse_model');
    if($this->input->post('customerCode'))
    {
      $book_code = getConfig('BOOK_CODE_SPONSOR');
      $date_add = db_date($this->input->post('date'));
      if($this->input->post('code'))
      {
        $code = $this->input->post('code');
      }
      else
      {
        $code = $this->get_new_code($date_add);
      }

      $role = 'P'; //--- P = Sponsor
      $wh = $this->warehouse_model->get($this->input->post('warehouse'));
      $ds = array(
        'date_add' => $date_add,
        'code' => $code,
        'role' => $role,
        'bookcode' => $book_code,
        'customer_code' => $this->input->post('customerCode'),
        'user' => $this->_user->uname,
        'remark' => $this->input->post('remark'),
        'user_ref' => $this->input->post('empName'),
        'warehouse_code' => $wh->code,
				'is_wms' => $wh->is_wms,
				'transformed' => $this->input->post('transformed')
      );

      if($this->orders_model->add($ds) === TRUE)
      {
        $arr = array(
          'order_code' => $code,
          'state' => 1,
          'update_user' => $this->_user->uname
        );

        $this->order_state_model->add_state($arr);

        redirect($this->home.'/edit_detail/'.$code);
      }
      else
      {
        set_error('เพิ่มเอกสารไม่สำเร็จ กรุณาลองใหม่อีกครั้ง');
        redirect($this->home.'/add_new');
      }
    }
    else
    {
      set_error('ไม่พบข้อมูลลูกค้า กรุณาตรวจสอบ');
      redirect($this->home.'/add_new');
    }
  }



  public function edit_order($code, $approve_view = NULL)
  {
    $this->load->model('approve_logs_model');
		$this->load->model('address/address_model');
		$this->load->helper('sender');
    $ds = array();
    $rs = $this->orders_model->get($code);
    if(!empty($rs))
    {
      $rs->customer_name = $this->customers_model->get_name($rs->customer_code);
      $rs->total_amount  = $this->orders_model->get_order_total_amount($rs->code);
      $rs->user          = $this->user_model->get_name($rs->user);
      $rs->state_name    = get_state_name($rs->state);

      $state = $this->order_state_model->get_order_state($code);
      $ost = array();
      if(!empty($state))
      {
        foreach($state as $st)
        {
          $ost[] = $st;
        }
      }

      $details = $this->orders_model->get_order_details($code);
			$ship_to = $this->address_model->get_ship_to_address($rs->customer_code);

      $ds['state'] = $ost;
      $ds['approve_view'] = $approve_view;
      $ds['approve_logs'] = $this->approve_logs_model->get($code);
      $ds['order'] = $rs;
      $ds['details'] = $details;
			$ds['addr']  = $ship_to;
			$ds['cancle_reason'] = ($rs->state == 9 ? $this->orders_model->get_cancle_reason($code) : NULL);
      $ds['allowEditDisc'] = FALSE; //getConfig('ALLOW_EDIT_DISCOUNT') == 1 ? TRUE : FALSE;
      $ds['allowEditPrice'] = getConfig('ALLOW_EDIT_PRICE') == 1 ? TRUE : FALSE;
      $ds['edit_order'] = TRUE; //--- ใช้เปิดปิดปุ่มแก้ไขราคาสินค้าไม่นับสต็อก
      $this->load->view('sponsor/sponsor_edit', $ds);
    }
    else
    {
      $this->load->view('page_error');
    }
  }



  public function update_order()
  {
    $sc = TRUE;

    if($this->input->post('order_code'))
    {
			$this->load->model('masters/warehouse_model');

      $code = $this->input->post('order_code');
      $order = $this->orders_model->get($code);

      if(!empty($order))
      {
        if($order->state > 1)
        {
          $ds = array(
            'remark' => $this->input->post('remark')
          );
        }
        else
        {
					$wh = $this->warehouse_model->get($this->input->post('warehouse'));
          $ds = array(
            'customer_code' => $this->input->post('customer_code'),
            'date_add' => db_date($this->input->post('date_add')),
            'user_ref' => $this->input->post('user_ref'),
            'warehouse_code' => $wh->code,
            'remark' => $this->input->post('remark'),
            'status' => 0,
						'id_address' => NULL,
						'id_sender' => NULL,
						'is_wms' => $wh->is_wms,
						'transformed' => $this->input->post('transformed')
          );
        }

        if(! $this->orders_model->update($code, $ds))
        {
          $sc = FALSE;
          $this->error = "ปรับปรุงเอกสารไม่สำเร็จ";
        }
      }
      else
      {
        $sc = FALSE;
        $this->error = "เลขที่เอกสารไม่ถูกต้อง : {$code}";
      }
    }
    else
    {
      $sc = FALSE;
      $this->error = 'ไม่พบเลขที่เอกสาร';
    }

    echo $sc === TRUE ? 'success' : $this->error;
  }



  public function edit_detail($code)
  {
    $this->load->helper('product_tab');
    $ds = array();
    $rs = $this->orders_model->get($code);
    if($rs->state <= 3)
    {
      $rs->customer_name = $this->customers_model->get_name($rs->customer_code);
      $details = $this->orders_model->get_order_details($code);
      $ds['order'] = $rs;
      $ds['details'] = $details;
      $ds['allowEditDisc'] = FALSE;
      $ds['allowEditPrice'] = getConfig('ALLOW_EDIT_PRICE') == 1 ? TRUE : FALSE;
      $ds['edit_order'] = FALSE; //--- ใช้เปิดปิดปุ่มแก้ไขราคาสินค้าไม่นับสต็อก
      $this->load->view('sponsor/sponsor_edit_detail', $ds);
    }
  }



  public function save($code)
  {
    $sc = TRUE;
    $order = $this->orders_model->get($code);

    //---- check credit balance
    $amount = $this->orders_model->get_order_total_amount($code);
    //--- creadit used
    $credit_used = $this->sponsor_model->get_budget_used($order->customer_code);

    //--- credit balance from sap
    $credit_balance = $this->sponsor_model->get_budget($order->customer_code);

    if($credit_used > $credit_balance)
    {
      $diff = $credit_used - $credit_balance;
      $sc = FALSE;
      $message = 'เครดิตคงเหลือไม่พอ (ขาด : '.number($diff, 2).')';
    }

		if(empty($order->id_address))
		{
			$this->load->model('address/address_model');
			$id_address = NULL;

			if(!empty($order->customer_ref))
			{
				$id_address = $this->address_model->get_shipping_address_id_by_code($order->customer_ref);
			}
			else
			{
				$id_address = $this->address_model->get_default_ship_to_address_id($order->customer_code);
			}

			if(!empty($id_address))
			{
				$arr = array(
					'id_address' => $id_address
				);

				$this->orders_model->update($order->code, $arr);
			}
		}


		if(empty($order->id_sender))
		{
			$this->load->model('masters/sender_model');
			$id_sender = NULL;

			$sender = $this->sender_model->get_customer_sender_list($order->customer_code);

			if(!empty($sender))
			{
				if(!empty($sender->main_sender))
				{
					$id_sender = $sender->main_sender;
				}
			}

			if(!empty($id_sender))
			{
				$arr = array(
					'id_sender' => $id_sender
				);

				$this->orders_model->update($order->code, $arr);
			}
		}

    if($sc === TRUE)
    {
      $rs = $this->orders_model->set_status($code, 1);
      if($rs === FALSE)
      {
        $sc = FALSE;
        $message = 'บันทึกออเดอร์ไม่สำเร็จ';
      }
    }

    echo $sc === TRUE ? 'success' : $message;
  }



  public function get_new_code($date)
  {
    $date = $date == '' ? date('Y-m-d') : $date;
    $Y = date('y', strtotime($date));
    $M = date('m', strtotime($date));
    $prefix = getConfig('PREFIX_SPONSOR');
    $run_digit = getConfig('RUN_DIGIT_SPONSOR');
    $pre = $prefix .'-'.$Y.$M;
    $code = $this->orders_model->get_max_code($pre);
    if(! is_null($code))
    {
      $run_no = mb_substr($code, ($run_digit*-1), NULL, 'UTF-8') + 1;
      $new_code = $prefix . '-' . $Y . $M . sprintf('%0'.$run_digit.'d', $run_no);
    }
    else
    {
      $new_code = $prefix . '-' . $Y . $M . sprintf('%0'.$run_digit.'d', '001');
    }

    return $new_code;
  }



  public function set_never_expire()
  {
    $code = $this->input->post('order_code');
    $option = $this->input->post('option');
    $rs = $this->orders_model->set_never_expire($code, $option);
    echo $rs === TRUE ? 'success' : 'ทำรายการไม่สำเร็จ';
  }


  public function un_expired()
  {
    $code = $this->input->post('order_code');
    $rs = $this->orders_model->un_expired($code);
    echo $rs === TRUE ? 'success' : 'ทำรายการไม่สำเร็จ';
  }

  public function clear_filter()
  {
    $filter = array(
      'sponsor_code',
      'sponsor_customer',
      'sponsor_user',
      'sponsor_zone',
      'sponsor_fromDate',
      'sponsor_toDate',
      'sponsor_isApprove',
			'sponsor_warehouse',
			'sponsor_wms_export',
      'sponsor_sap_status',
      'sponsor_notSave',
      'sponsor_onlyMe',
      'sponsor_isExpire',
      'sponsor_state_1',
      'sponsor_state_2',
      'sponsor_state_3',
      'sponsor_state_4',
      'sponsor_state_5',
      'sponsor_state_6',
      'sponsor_state_7',
      'sponsor_state_8',
      'sponsor_state_9'
    );

    clear_filter($filter);
  }
}
?>
