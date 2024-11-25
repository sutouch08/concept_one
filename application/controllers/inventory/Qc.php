<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Qc extends PS_Controller
{
  public $menu_code = 'ICODQC';
	public $menu_group_code = 'IC';
  public $menu_sub_group_code = 'PICKPACK';
	public $title = 'ตรวจสินค้า';
  public $filter;
  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'inventory/qc';
    $this->load->model('inventory/qc_model');
    $this->load->model('orders/orders_model');
    $this->load->model('orders/order_state_model');
  }

  public function close_order()
  {
    $sc = TRUE;
    $code = $this->input->post('order_code');
    $state = $this->orders_model->get_state($code);
    if($state == 6)
    {
      $arr = array(
        'order_code' => $code,
        'state' => 7,
        'update_user' => get_cookie('uname')
      );

      if($this->orders_model->change_state($code, 7))
      {
        $this->order_state_model->add_state($arr);
      }
    }
    else
    {
      $sc = FALSE;
      $message = 'ไม่สามารถปิดออเดอร์ได้ เนื่องจากสถานะออเดอร์ได้ถูกเปลี่ยนไปแล้ว';
    }

    echo $sc === TRUE ? 'success' : $message;
  }



  public function save_qc()
  {
    $sc = TRUE;

    $data = json_decode($this->input->post('data'));

    if( ! empty($data))
    {
      if( ! empty($data->order_code))
      {
        if( ! empty($data->rows))
        {
          $this->load->model('inventory/buffer_model');

          $this->db->trans_begin();

          foreach($data->rows as $row)
          {
            $qty = $row->qty;

            $details = $this->orders_model->get_unvalid_qc_detail($data->order_code, $row->product_code);

            if( ! empty($details))
            {
              foreach($details as $detail)
              {
                if($qty > 0)
                {
                  $bufferQty = $this->buffer_model->get_sum_buffer_product($detail->order_code, $detail->product_code, $detail->id); //--- 5
                  $qcQty = $this->qc_model->get_sum_qty($detail->order_code, $detail->product_code, $detail->id); //-- 2
                  $checkQty = $bufferQty - $qcQty;
                  $Qty = $qty >= $checkQty ? $checkQty : $qty; //$detail->qty ? $detail->qty : $qty; //-- 3

                  //--- ยอดที่จะบันทึกตรวจต้องรวมกันแล้วไม่เกินยอดที่จัดและต้องไม่เกินยอดสั่ง
                  $updateQty = $qcQty + $Qty; //--- 2 + 3

                  if($updateQty > $bufferQty)
                  {
                    $sc = FALSE;
                    $this->error = $detail->product_code.' ยอดตรวจเกินยอดจัดหรือยอดสั่ง';
                  }

                  //--- update ยอดตรวจ
                  if( ! $this->qc_model->update_checked($data->order_code, $detail->product_code, $data->id_box, $Qty, $detail->id))
                  {
                    $sc = FALSE;
                    $this->error = $detail->product_code.' บันทึกยอดตรวจไม่สำเร็จ';
                  }

                  $qty = $qty - $Qty;

                  if($detail->qty == $updateQty)
                  {
                    $this->orders_model->valid_qc($detail->id);
                  }
                }
              }
            }
            else
            {
              $sc = FALSE;
              $this->error = "Order item {$row->product_code} not exists";
            }
          } //--- end foreach

          if($sc === TRUE)
          {
            $this->qc_model->drop_zero_qc($data->order_code);
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
          $this->error = "No item found";
        }
      }
      else
      {
        $sc = FALSE;
        $this->eror = "Order number not found";
      }
    }
    else
    {
      $sc = FALSE;
      $this->error = "Missing required parameter";
    }

    echo $sc == TRUE ? 'success' : $this->error;
  }

  public function index()
  {
    $this->load->helper('channels');
    $filter = array(
      'code'          => get_filter('code', 'ic_code', ''),
      'reference' => get_filter('reference', 'ic_reference', ''),
      'customer'      => get_filter('customer', 'ic_customer', ''),
      'user'          => get_filter('user', 'ic_user', ''),
      'channels'      => get_filter('channels', 'ic_channels', ''),
      'from_date'     => get_filter('from_date', 'ic_from_date', ''),
      'to_date'       => get_filter('to_date', 'ic_to_date', ''),
      'sort_by'       => get_filter('sort_by', 'ic_sort_by', ''),
      'order_by'      => get_filter('order_by', 'ic_order_by', '')
    );
    //--- แสดงผลกี่รายการต่อหน้า
		$perpage = get_rows();
		//--- หาก user กำหนดการแสดงผลมามากเกินไป จำกัดไว้แค่ 300
		if($perpage > 300)
		{
			$perpage = 20;
		}

    $state = 5; //---- รอตรวจ
		$segment  = 4; //-- url segment
		$rows     = $this->qc_model->count_rows($filter, $state);
		//--- ส่งตัวแปรเข้าไป 4 ตัว base_url ,  total_row , perpage = 20, segment = 3
		$init	    = pagination_config($this->home.'/index/', $rows, $perpage, $segment);
    $offset   = $rows < $this->uri->segment($segment) ? NULL : $this->uri->segment($segment);
		$orders   = $this->qc_model->get_data($filter, $state, $perpage, $offset);
    $filter['orders'] = $orders;
    $this->pagination->initialize($init);
    $this->load->view('inventory/qc/qc_list', $filter);
  }



  public function view_process()
  {
    $this->load->helper('channels');
    $filter = array(
      'code'          => get_filter('code', 'ic_code', ''),
      'reference' => get_filter('reference', 'ic_reference', ''),
      'customer'      => get_filter('customer', 'ic_customer', ''),
      'user'          => get_filter('user', 'ic_user', ''),
      'channels'      => get_filter('channels', 'ic_channels', ''),
      'from_date'     => get_filter('from_date', 'ic_from_date', ''),
      'to_date'       => get_filter('to_date', 'ic_to_date', ''),
      'sort_by'       => get_filter('sort_by', 'ic_sort_by', ''),
      'order_by'      => get_filter('order_by', 'ic_order_by', '')
    );
    //--- แสดงผลกี่รายการต่อหน้า
		$perpage = get_rows();
		//--- หาก user กำหนดการแสดงผลมามากเกินไป จำกัดไว้แค่ 300
		if($perpage > 300)
		{
			$perpage = 20;
		}
    $state = 6; //---- รอตรวจ
		$segment  = 5; //-- url segment
		$rows     = $this->qc_model->count_rows($filter, $state);
		//--- ส่งตัวแปรเข้าไป 4 ตัว base_url ,  total_row , perpage = 20, segment = 3
		$init	    = pagination_config($this->home.'/view_process/index/', $rows, $perpage, $segment);
    $offset   = $rows < $this->uri->segment($segment) ? NULL : $this->uri->segment($segment);
		$orders   = $this->qc_model->get_data($filter, $state, $perpage, $offset);
    $filter['orders'] = $orders;
    $this->pagination->initialize($init);
    $this->load->view('inventory/qc/qc_view_process_list', $filter);
  }




  public function process($code)
  {
    $this->load->model('masters/customers_model');
    $this->load->model('masters/channels_model');
    $this->load->model('inventory/buffer_model');
    $state = $this->orders_model->get_state($code);

    if($state == 5)
    {
      $rs = $this->orders_model->change_state($code, 6);
      if($rs)
      {
        $arr = array(
          'order_code' => $code,
          'state' => 6,
          'update_user' => get_cookie('uname')
        );
        $this->order_state_model->add_state($arr);
      }
    }

    $order = $this->orders_model->get($code);

    if(!empty($order))
    {
      $order->customer_name = $this->customers_model->get_name($order->customer_code);
      $order->channels_name = $this->channels_model->get_name($order->channels_code);
    }

    $barcode_list = array();

    $uncomplete = $this->qc_model->get_in_complete_list($code);

    if(!empty($uncomplete))
    {
      foreach($uncomplete as $rs)
      {
        $barcode = $this->get_barcode($rs->product_code);
        $rs->barcode = empty($barcode) ? $rs->product_code : $barcode;
        $bc = new stdClass();
        $bc->barcode = md5($rs->barcode);
        $bc->product_code = $rs->product_code;
        $barcode_list[] = $bc;
        $arr = array(
          'order_code' => $code,
          'product_code' => $rs->product_code,
          'is_count' => $rs->is_count
        );

        $rs->from_zone = $this->get_prepared_from_zone($arr);
      }
    }

    $complete = $this->qc_model->get_complete_list($code);

    if(!empty($complete))
    {
      foreach($complete as $rs)
      {
        $barcode = $this->get_barcode($rs->product_code);
        $rs->barcode = empty($barcode) ? $rs->product_code : $barcode;
        $bc = new stdClass();
        $bc->barcode = md5($rs->barcode);
        $bc->product_code = $rs->product_code;
        $barcode_list[] = $bc;

        $arr = array(
          'order_code' => $code,
          'product_code' => $rs->product_code,
          'is_count' => $rs->is_count
        );

        $rs->from_zone = $this->get_prepared_from_zone($arr);
      }
    }

    $ds = array(
      'order' => $order,
      'uncomplete_details' => $uncomplete,
      'complete_details' => $complete,
      'barcode_list' => $barcode_list,
      'box_list' => $this->qc_model->get_box_list($code),
      'qc_qty' => $this->qc_model->total_qc($code),
      'all_qty' => $this->get_sum_qty($code),
      'disActive' => $order->state == 6 ? '' : 'disabled'
    );

    $this->load->view('inventory/qc/qc_process', $ds);
  }


  public function get_barcode($item_code)
  {
    $this->load->model('masters/products_model');
    return $this->products_model->get_barcode($item_code);
  }


  public function get_sum_qty($code)
  {
    $this->load->model('inventory/prepare_model');

    $order_qty = $this->orders_model->get_order_total_qty($code);
  	$prepared = $this->prepare_model->get_total_prepared($code);

  	return $order_qty < $prepared ? $order_qty : $prepared;
  }


  public function get_prepared_from_zone(array $ds = array())
  {
    $label = "ไม่พบข้อมูล";

    if( ! empty($ds))
    {
      if( ! empty($ds['is_count']))
      {
        $buffer = $this->buffer_model->get_prepared_from_zone($ds['order_code'], $ds['product_code']);

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


  public function get_box()
  {
    $code = $this->input->get('order_code');
    $barcode = $this->input->get('barcode');

    $box = $this->qc_model->get_box($code, $barcode);
    if(!empty($box))
    {
      echo $box->id;
    }
    else
    {
      //--- insert new box
      $box_no = $this->qc_model->get_last_box_no($code) + 1;
      $id_box = $this->qc_model->add_new_box($code, $barcode, $box_no);
      echo $id_box === FALSE ? 'เพิมกล่องไม่สำเร็จ' : $id_box;
    }
  }



  public function get_box_list()
  {
    $sc = TRUE;
    $code = $this->input->get('order_code');
    $id = $this->input->get('id_box');
    $box_list = $this->qc_model->get_box_list($code);
    if(!empty($box_list))
    {
      $ds = array();

      foreach($box_list as $box)
      {
        $arr = array(
          'no' => $box->box_no,
          'id_box' => $box->id,
          'qty' => number($box->qty),
          'class' => $box->id == $id ? 'btn-success' : 'btn-default'
        );
        array_push($ds, $arr);
      }
    }
    else
    {
      $sc = FALSE;
    }

    echo $sc === TRUE ? json_encode($ds) : 'no box';

  }



  public function get_checked_table()
  {
    $sc = TRUE;
    $code = $this->input->get('order_code');
    $item_code = $this->input->get('product_code');
    $list = $this->qc_model->get_checked_table($code, $item_code);
    if(!empty($list))
    {
      $ds = array();
      foreach($list as $rs)
      {
        $arr = array(
          'id_qc' => $rs->id,
          'barcode' => $rs->barcode,
          'box_no' => $rs->box_no,
          'qty' => $rs->qty
        );

        array_push($ds, $arr);
      }
    }
    else
    {
      $sc = FALSE;
      $this->error = "ไม่พบรายการตรวจสินค้า";
    }

    echo $sc === TRUE ? json_encode($ds) : $this->error;
  }



  public function remove_check_qty()
  {
    $sc = TRUE;
    $id = $this->input->post('id'); //--- id qc
    $qty = $this->input->post('qty'); //--- remove qty

    $qc = $this->qc_model->get($id);

    if( ! empty($qc))
    {
      if($qty == $qc->qty)
      {
        if(! $this->qc_model->delete_qc($id))
        {
          $sc = FALSE;
          $this->error = "ลบรายการไม่สำเร็จ";
        }
      }
      else
      {
        if(! $this->qc_model->update_qty($id, (-1) * $qty))
        {
          $sc = FALSE;
          $this->error = "ปรับปรุงยอดตรวจนับไม่สำเร็จ";
        }
      }
    }
    else
    {
      $sc = FALSE;
      $this->error = "ไม่พบรายการตรวจนับ";
    }

    echo $sc === TRUE ? 'success' : $this->error;
  }

  public function print_box($code, $box_id)
  {
    $this->load->library('printer');
    $this->load->model('masters/customers_model');

    $order = $this->orders_model->get($code);
    $order->customer_name = $this->customers_model->get_name($order->customer_code);
    $details = $this->qc_model->get_box_details($code, $box_id);
    $box_no = $this->qc_model->get_box_no($box_id);
    $all_box = $this->qc_model->count_box($code);
    $ds = array();
    $ds['order'] = $order;
    $ds['details'] = $details;
    $ds['box_no'] = $box_no;
    $ds['all_box'] = $all_box;

    $this->load->view('inventory/qc/packing_list', $ds);
  }



  public function clear_filter()
  {
    $filter = array('ic_code', 'ic_reference', 'ic_customer', 'ic_user', 'ic_channels', 'ic_from_date', 'ic_to_date', 'ic_sort_by', 'ic_order_by');
    clear_filter($filter);
  }

} //--- end Qc
?>
