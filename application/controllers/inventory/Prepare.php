<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Prepare extends PS_Controller
{
  public $menu_code = 'ICODPR';
	public $menu_group_code = 'IC';
  public $menu_sub_group_code = 'PICKPACK';
	public $title = 'จัดสินค้า';
  public $filter;
  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'inventory/prepare';
    $this->load->model('inventory/prepare_model');
    $this->load->model('orders/orders_model');
    $this->load->model('orders/order_state_model');
    $this->load->helper('warehouse');
    $this->load->helper('zone');
  }


  public function index()
  {
    $this->load->helper('channels');
    $this->load->helper('payment_method');

    $filter = array(
      'code' => get_filter('code', 'ic_code', ''),
      'so_code' => get_filter('so_code', 'so_code', ''),
      'customer' => get_filter('customer', 'ic_customer', ''),
      'user' => get_filter('user', 'ic_user', 'all'),
      'channels' => get_filter('channels', 'ic_channels', ''),
      'role' => get_filter('role', 'ic_role', 'all'),
      'from_date' => get_filter('from_date', 'ic_from_date', ''),
      'to_date' => get_filter('to_date', 'ic_to_date', ''),
      'warehouse' => get_filter('warehouse', 'ic_warehouse', 'all')
    );

    //--- แสดงผลกี่รายการต่อหน้า
    $perpage = get_rows();

    $segment  = 4; //-- url segment
    $rows     = $this->prepare_model->count_rows($filter, 3);
    //--- ส่งตัวแปรเข้าไป 4 ตัว base_url ,  total_row , perpage = 20, segment = 3
    $init	    = pagination_config($this->home.'/index/', $rows, $perpage, $segment);
    $orders   = $this->prepare_model->get_data($filter, $perpage, $this->uri->segment($segment), 3);

    if( ! empty($orders))
    {
      foreach($orders as $rs)
      {
        $rs->qty = $this->prepare_model->get_sum_order_qty($rs->code);
      }
    }

    $filter['orders'] = $orders;

    $this->pagination->initialize($init);
    $this->load->view('inventory/prepare/prepare_list', $filter);
  }


  public function view_process()
  {
    $this->load->helper('channels');
    $this->load->helper('payment_method');
    $this->load->helper('warehouse');
    $filter = array(
      'code' => get_filter('code', 'ic_code', ''),
      'so_code' => get_filter('so_code', 'so_code', ''),
      'customer' => get_filter('customer', 'ic_customer', ''),
      'user' => get_filter('user', 'ic_user', ''),
      'channels' => get_filter('channels', 'ic_channels', ''),
      'role' => get_filter('role', 'ic_role', 'all'),
      'from_date' => get_filter('from_date', 'ic_from_date', ''),
      'to_date' => get_filter('to_date', 'ic_to_date', ''),
      'warehouse' => get_filter('warehouse', 'ic_warehouse', 'all')
    );

		//--- แสดงผลกี่รายการต่อหน้า
		$perpage = get_rows();
		//--- หาก user กำหนดการแสดงผลมามากเกินไป จำกัดไว้แค่ 300
		if($perpage > 300)
		{
			$perpage = 20;
		}

		$segment  = 4; //-- url segment
		$rows     = $this->prepare_model->count_rows($filter, 4);
		//--- ส่งตัวแปรเข้าไป 4 ตัว base_url ,  total_row , perpage = 20, segment = 3
		$init	    = pagination_config($this->home.'/view_process/', $rows, $perpage, $segment);
		$orders   = $this->prepare_model->get_data($filter, $perpage, $this->uri->segment($segment), 4);

    if( ! empty($orders))
    {
      foreach($orders as $rs)
      {
        $rs->qty = $this->prepare_model->get_sum_order_qty($rs->code);
      }
    }

    $filter['orders'] = $orders;

		$this->pagination->initialize($init);
    $this->load->view('inventory/prepare/prepare_view_process', $filter);
  }


  public function process($code)
  {
    $this->load->model('masters/customers_model');
    $this->load->model('masters/channels_model');
    $state = $this->orders_model->get_state($code);

    if($state == 3)
    {
      $rs = $this->orders_model->change_state($code, 4);
      if($rs)
      {
        $arr = array(
          'order_code' => $code,
          'state' => 4,
          'update_user' => $this->_user->uname
        );
        $this->order_state_model->add_state($arr);
      }
    }

    $order = $this->orders_model->get($code);
    $order->customer_name = $this->customers_model->get_name($order->customer_code);
    $order->channels_name = $this->channels_model->get_name($order->channels_code);

    $uncomplete = $this->orders_model->get_unvalid_details($code);

    if(!empty($uncomplete))
    {
      foreach($uncomplete as $rs)
      {
        $rs->barcode = $this->get_barcode($rs->product_code);
        $rs->prepared = $this->prepare_model->get_prepared($rs->order_code, $rs->product_code, $rs->id);
        $rs->stock_in_zone = $this->get_stock_in_zone($rs->product_code, get_null($order->warehouse_code));
      }
    }

    $complete = $this->orders_model->get_valid_details($code);

    if( ! empty($complete))
    {
      foreach($complete as $rs)
      {
        $rs->barcode = $this->get_barcode($rs->product_code);
        $rs->prepared = $rs->is_count == 1 ? $this->prepare_model->get_prepared($rs->order_code, $rs->product_code, $rs->id) : $rs->qty;

        $arr = array(
          'order_code' => $rs->order_code,
          'product_code' => $rs->product_code,
          'order_detail_id' => $rs->id,
          'is_count' => $rs->is_count
        );

        $rs->from_zone = $rs->is_count == 1 ? $this->get_prepared_from_zone($arr) : "ไม่นับสต็อก";
      }
    }

    $ds = array(
      'order' => $order,
      'uncomplete_details' => $uncomplete,
      'complete_details' => $complete,
      'default_zone' => $order->warehouse_code.'-SYSTEM-BiN-LOCATION'
    );

    $this->load->view('inventory/prepare/prepare_process', $ds);
  }




  public function do_prepare()
  {
    $sc = TRUE;
    $valid = 0;
    if($this->input->post('order_code'))
    {
      $this->load->model('masters/products_model');

      $order_code = $this->input->post('order_code');
      $zone_code  = $this->input->post('zone_code');
      $barcode    = $this->input->post('barcode');
      $qty        = $this->input->post('qty');

      $state = $this->orders_model->get_state($order_code);
      //--- ตรวจสอบสถานะออเดอร์ 4 == กำลังจัดสินค้า
      if($state == 4)
      {
        $item = $this->products_model->get_product_by_barcode($barcode);

        if(empty($item))
        {
          $item = $this->products_model->get($barcode);
        }

        //--- ตรวจสอบบาร์โค้ดที่ยิงมา
        if(!empty($item))
        {
          if($item->count_stock == 1)
          {
            //---- มีสินค้านี้อยู่ในออเดอร์หรือไม่ ถ้ามี รวมยอดมา อาจมีมาก
            $ds = $this->orders_model->get_unvalid_order_detail($order_code, $item->code);
            //$orderQty = $this->orders_model->get_sum_item_qty($order_code, $item->code);

            if( ! empty($ds))
            {
              //--- ดึงยอดที่จัดแล้ว
              // $prepared = $this->get_prepared($order_code, $item->code);
              $prepared = $this->prepare_model->get_prepared($order_code, $item->code, $ds->id);

              //--- ยอดคงเหลือค้างจัด
              $bQty = $ds->qty - $prepared;

              //---- ตรวจสอบยอดที่ยังไม่ครบว่าจัดเกินหรือเปล่า
              if( $bQty < $qty)
              {
                $sc = FALSE;
                $this->error = "สินค้าเกิน กรุณาคืนสินค้าแล้วจัดสินค้าใหม่อีกครั้ง";
              }
              else
              {
                $stock = $this->get_stock_zone($zone_code, $item->code); //1000;

                if($stock < $qty)
                {
                  $sc = FALSE;
                  $this->error = "สินค้าไม่เพียงพอ กรุณากำหนดจำนวนสินค้าใหม่";
                }
                else
                {
                  $this->db->trans_begin();

                  if( ! $this->prepare_model->update_buffer($order_code, $item->code, $zone_code, $qty, $ds->id))
                  {
                    $sc = FALSE;
                    $this->error = "Failed to update buffer";
                  }

                  if($sc === TRUE)
                  {
                    if( ! $this->prepare_model->update_prepare($order_code, $item->code, $zone_code, $qty, $ds->id))
                    {
                      $sc = FALSE;
                      $this->error = "Failed to update prepare";
                    }
                  }

                  if($sc === TRUE)
                  {
                    $this->db->trans_commit();
                  }
                  else
                  {
                    $this->trans_rollback();
                  }

                  if($sc === TRUE)
                  {
                    $preparedQty = $prepared + $qty;

                    if($preparedQty == $ds->qty)
                    {
                      $this->orders_model->valid_detail($ds->id);
                      $valid = 1;
                    }
                  }
                }
              }

            }
            else
            {
              $sc = FALSE;
              $this->error = 'สินค้าไม่ตรงกับออเดอร์';
            }
          }
          else
          {
            $sc = FALSE;
            $this->error = 'สินค้าไม่นับสต็อก ไม่จำเป็นต้องจัดสินค้านี้';
          }
        }
        else
        {
          $sc = FALSE;
          $this->error = 'บาร์โค้ดไม่ถูกต้อง กรุณาตรวจสอบ';
        }
      }
      else
      {
        $sc = FALSE;
        $this->error = 'สถานะออเดอร์ถูกเปลี่ยน ไม่สามารถจัดสินค้าต่อได้';
      }
    }

    echo $sc === TRUE ? json_encode(array("id" => $ds->id, "qty" => $qty, "valid" => $valid)) : $this->error;
  }



  public function get_barcode($item_code)
  {
    $this->load->model('masters/products_model');
    return $this->products_model->get_barcode($item_code);
  }


  public function get_prepared($order_code, $item_code, $detail_id)
  {
    return $this->prepare_model->get_prepared($order_code, $item_code, $detail_id);
  }


  public function get_order_code()
  {
    $sc = TRUE;
    $code = NULL;
    $reference = trim($this->input->post('reference'));

    if( ! empty($reference))
    {
      $order = $this->orders_model->get($reference);

      if(empty($order))
      {
        $order = $this->orders_model->get_order_by_reference($reference);
      }

      if(empty($order))
      {
        $order = $this->orders_model->get_order_by_tracking($reference);
      }

      if( ! empty($order))
      {
        if( ! $order->state == 3 && $order->state != 4)
        {
          $sc = FALSE;
          set_error('status');
        }
        else
        {
          $code = $order->code;
        }
      }
      else
      {
        $sc = FALSE;
        set_error('notfound');
      }
    }
    else
    {
      $sc = FALSE;
      set_error('required');
    }

    $arr = array(
      'status' => $sc === TRUE ? 'success' : 'failed',
      'message' => $sc === TRUE ? 'success' : $this->error,
      'code' => $code
    );

    echo json_encode($arr);
  }


  public function get_prepared_from_zone(array $ds = array())
  {
    $label = "ไม่พบข้อมูล";

    if( ! empty($ds))
    {
      if( ! empty($ds['is_count']))
      {
        $buffer = $this->prepare_model->get_prepared_from_zone($ds['order_code'], $ds['product_code'], $ds['order_detail_id']);

        if( ! empty($buffer))
        {
          $label = "";

          foreach($buffer as $rs)
          {
            $label .= $rs->name.' : '.number($rs->qty).'<br/>';
          }
        }
        else
        {
          $label = "ไม่พบข้อมูล";
        }
      }
      else
      {
        $label = "ไม่นับสต็อก";
      }
    }

  	return $label;
  }




  public function get_stock_in_zone($item_code, $warehouse = NULL)
  {
    $sc = "ไม่มีสินค้า";
    $this->load->model('stock/stock_model');
    $stock = $this->stock_model->get_stock_in_zone($item_code, $warehouse);
    if(!empty($stock))
    {
      $sc = "";
      foreach($stock as $rs)
      {
        $prepared = $this->prepare_model->get_buffer_zone($item_code, $rs->code);
        $qty = $rs->qty - $prepared;
        if($qty > 0)
        {
          $sc .= $rs->code.' : '.($rs->qty - $prepared).'<br/>';
        }

      }
    }

    return empty($sc) ? 'ไม่พบสินค้า' : $sc;
  }




  //---- สินค้าคงเหลือในโซน ลบด้วย สินค้าที่จัดไปแล้ว
  public function get_stock_zone($zone_code, $item_code)
  {
    $this->load->model('stock/stock_model');
    $this->load->model('masters/warehouse_model');
    $this->load->model('masters/zone_model');

    $zone = $this->zone_model->get($zone_code);
    $wh = $this->warehouse_model->get($zone->warehouse_code);
    $gb_auz = getConfig('ALLOW_UNDER_ZERO');
    $wh_auz = $wh->auz == 1 ? TRUE : FALSE;
    $auz = $gb_auz == 1 ? TRUE : $wh_auz;

    if($auz === TRUE)
    {
      return 1000000;
    }

    //---- สินค้าคงเหลือในโซน
    $stock = $this->stock_model->get_stock_zone($zone_code, $item_code);

    //--- ยอดจัดสินค้าที่จัดออกจากโซนนี้ไปแล้ว แต่ยังไม่ได้ตัด
    $prepared = $this->prepare_model->get_prepared_zone($zone_code, $item_code);


    return $stock - $prepared;

  }


  public function set_zone_label($value)
  {
    $this->input->set_cookie(array('name' => 'showZone', 'value' => $value, 'expire' => 3600 , 'path' => '/'));
  }





  public function finish_prepare()
  {
    $code = $this->input->post('order_code');
    $sc = TRUE;

    $state = $this->orders_model->get_state($code);
    $useQc = getConfig('USE_QC') == 1 ? TRUE : FALSE;

    //---	ถ้าสถานะเป็นกำลังจัด (บางทีอาจมีการเปลี่ยนสถานะตอนเรากำลังจัดสินค้าอยู่)
    if( $state == 4)
    {
      $this->db->trans_begin();

      //--- mark all detail as valid
      if( ! $this->orders_model->valid_all_details($code))
      {
        $sc = FALSE;
        $this->error = "Failed to update valid details";
      }

      if($sc === TRUE)
      {
        $state = $useQc ? 5 : 7;
        //---	เปลียน state ของออเดอร์
        if( ! $this->orders_model->change_state($code, $state))
        {
          $sc = FALSE;
          $this->error = "Failed to change order state";
        }

        if($sc === TRUE)
        {
          $arr = array(
            'order_code' => $code,
            'state' => $state,
            'update_user' => $this->_user->uname
          );

          //--- add state event
          if( ! $this->order_state_model->add_state($arr))
          {
            $sc = FALSE;
            $this->error = "Failed to add state logs";
          }
        }
      }

      if($sc === TRUE)
      {
        $this->db->trans_commit();
      }
      else
      {
        $this->db->trans_rollback();
      }
    }
    else
    {
      $sc = FALSE;
      $this->error = "Invalid order state";
    }

    echo $sc === TRUE ? 'success' : $this->error;
  }


  public function check_state()
  {
    $code = $this->input->get('order_code');
    $rs = $this->orders_model->get_state($code);
    echo $rs;
  }


  public function pull_order_back()
  {
    $code = $this->input->post('order_code');
    $state = $this->orders_model->get_state($code);
    if($state == 4)
    {
      $arr = array(
        'order_code' => $code,
        'state' => 3,
        'update_user' => $this->_user->uname
      );

      $this->orders_model->change_state($code, 3);
      $this->order_state_model->add_state($arr);
    }

    echo 'success';
  }


  function remove_buffer()
  {
    $sc = TRUE;
    $this->load->model('inventory/buffer_model');
    $order_code = $this->input->post('order_code');
    $item_code = $this->input->post('product_code');
    $detail_id = $this->input->post('order_detail_id');

    $this->db->trans_begin();

    if( ! $this->buffer_model->remove_buffer($order_code, $item_code, $detail_id))
    {
      $sc = FALSE;
      $this->error = "Failed to delete buffer";
    }

    if( $sc === TRUE)
    {
      if( ! $this->prepare_model->remove_prepare($order_code, $item_code, $detail_id))
      {
        $sc = FALSE;
        $this->error = "Failed to delete prepare logs";
      }
    }

    if($sc === TRUE)
    {
      if( ! $this->orders_model->unvalid_detail($detail_id) )
      {
        $sc = FALSE;
        $this->error = "Failed to rollback item status (unvalid)";
      }
    }

    if($sc === TRUE)
    {
      $this->db->trans_commit();
    }
    else
    {
      $this->db->trans_rollback();
    }

    echo $sc === TRUE ? 'success' : $this->error;
  }


  public function clear_filter()
  {
    $filter = array(
      'ic_code',
      'ic_customer',
      'ic_user',
      'ic_channels',
      'ic_is_online',
      'ic_role',
      'ic_from_date',
      'ic_to_date',
      'ic_order_by',
      'ic_sort_by',
      'ic_stated',
      'ic_startTime',
      'ic_endTime',
      'ic_item_code',
      'ic_display_name',
      'ic_payment',
      'ic_warehouse',
      'so_code'
    );

    clear_filter($filter);
  }


} //--- end class
?>
