<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Support extends PS_Controller
{
  public $menu_code = 'ICSUPP';
  public $menu_group_code = 'IC';
  public $menu_sub_group_code = 'REQUEST';
  public $title = 'เบิกอภินันท์';
  public $filter;

  public function __construct()
  {
    parent::__construct();
    $this->home = base_url() . 'inventory/support';
    $this->load->model('orders/orders_model');
    $this->load->model('orders/support_model');
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
    $this->load->helper('discount');
    $this->load->helper('product_images');
    $this->load->helper('warehouse');
  }


  public function index()
  {
    $filter = array(
      'code'      => get_filter('code', 'support_code', ''),
      'customer'  => get_filter('customer', 'support_customer', ''),
      'user_ref'  => get_filter('user_ref', 'support_user_ref', ''),
      'from_date' => get_filter('fromDate', 'support_fromDate', ''),
      'to_date'   => get_filter('toDate', 'support_toDate', ''),
      'isApprove' => get_filter('isApprove', 'support_isApprove', 'all'),
      'warehouse' => get_filter('warehouse', 'support_warehouse', 'all'),
      'notSave' => get_filter('notSave', 'support_notSave', NULL),
      'onlyMe' => get_filter('onlyMe', 'support_onlyMe', NULL),
      'isExpire' => get_filter('isExpire', 'support_isExpire', NULL),
      'sap_status' => get_filter('sap_status', 'support_sap_status', 'all')
    );

    $state = array(
      '1' => get_filter('state_1', 'support_state_1', 'N'),
      '2' => get_filter('state_2', 'support_state_2', 'N'),
      '3' => get_filter('state_3', 'support_state_3', 'N'),
      '4' => get_filter('state_4', 'support_state_4', 'N'),
      '5' => get_filter('state_5', 'support_state_5', 'N'),
      '6' => get_filter('state_6', 'support_state_6', 'N'),
      '7' => get_filter('state_7', 'support_state_7', 'N'),
      '8' => get_filter('state_8', 'support_state_8', 'N'),
      '9' => get_filter('state_9', 'support_state_9', 'N')
    );

    $state_list = array();

    $button = array();

    for ($i = 1; $i <= 9; $i++)
    {
      if ($state[$i] === 'Y')
      {
        $state_list[] = $i;
      }

      $btn = 'state_' . $i;
      $button[$btn] = $state[$i] === 'Y' ? 'btn-info' : '';
    }

    $button['not_save'] = empty($filter['notSave']) ? '' : 'btn-info';
    $button['only_me'] = empty($filter['onlyMe']) ? '' : 'btn-info';
    $button['is_expire'] = empty($filter['isExpire']) ? '' : 'btn-info';


    $filter['state_list'] = empty($state_list) ? NULL : $state_list;

    //--- แสดงผลกี่รายการต่อหน้า
    $perpage = get_rows();
    //--- หาก user กำหนดการแสดงผลมามากเกินไป จำกัดไว้แค่ 300
    if ($perpage > 300)
    {
      $perpage = 20;
    }

    $role     = 'U'; //--- U = เบิกอภินันท์;
    $segment  = 4; //-- url segment
    $rows     = $this->orders_model->count_rows($filter, $role);
    //--- ส่งตัวแปรเข้าไป 4 ตัว base_url ,  total_row , perpage = 20, segment = 3
    $init      = pagination_config($this->home . '/index/', $rows, $perpage, $segment);
    $orders   = $this->orders_model->get_list($filter, $perpage, $this->uri->segment($segment), $role);
    $ds       = array();
    if (!empty($orders))
    {
      foreach ($orders as $rs)
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
    $this->load->view('support/support_list', $filter);
  }


  public function add_new()
  {
    $this->load->view('support/support_add');
  }


  public function add()
  {
    $sc = TRUE;
    $h = json_decode($this->input->post('data'));

    if (! empty($h))
    {
      $this->load->model('inventory/invoice_model');
      $this->load->model('masters/warehouse_model');
      $this->load->model('masters/sender_model');
      $this->load->model('address/address_model');


      $book_code = getConfig('BOOK_CODE_SUPPORT');
      $date_add = db_date($h->date_add, TRUE);

      $code = $this->get_new_code($date_add);

      $customer = $this->customers_model->get($h->customer_code);

      $role = 'U'; //--- U = เบิกอภินันท์;

      $has_term = TRUE;

      if ($sc === TRUE)
      {
        $wh = $this->warehouse_model->get($h->warehouse_code);

        $ship_to = empty($h->customer_ref) ? $this->address_model->get_ship_to_address($customer->code) : $this->address_model->get_shipping_address($h->customer_ref);

        $id_address = empty($ship_to) ? NULL : (count($ship_to) == 1 ? $ship_to[0]->id : NULL);

        $ds = array(
          'date_add' => $date_add,
          'code' => $code,
          'role' => $role,
          'bookcode' => $book_code,
          'TaxStatus' => $h->TaxStatus,
          'vat_type' => $h->vat_type,
          'reference' => NULL,
          'customer_code' => $customer->code,
          'customer_name' => $h->customer_name,
          'customer_ref' => $h->customer_ref,
          'tax_id' => $h->tax_id,
          'isCompany' => $h->isCompany,
          'branch_code' => $h->branch_code,
          'branch_name' => $h->branch_name,
          'address' => $h->address,
          'sub_district' => $h->sub_district,
          'district' => $h->district,
          'province' => $h->province,
          'postcode' => $h->postcode,
          'phone' => $h->phone,
          'channels_code' => NULL,
          'payment_code' => NULL,
          'warehouse_code' => $h->warehouse_code,
          'sale_code' => NULL,
          'is_term' => $h->is_term,
          'user' => $this->_user->uname,
          'remark' => get_null(addslashes($h->remark)),
          'id_address' => $id_address,
          'id_sender' => $this->sender_model->get_main_sender($customer->code)
        );

        if (! $this->orders_model->add($ds))
        {
          $sc = FALSE;
          $this->error = "เพิ่มเอกสารไม่สำเร็จ กรุณาลองใหม่อีกครั้ง";
        }
        else
        {
          $arr = array(
            'order_code' => $code,
            'state' => 1,
            'update_user' => $this->_user->uname
          );

          $this->order_state_model->add_state($arr);
        }
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
      'code' => $sc === TRUE ? $code : NULL
    );

    echo json_encode($arr);
  }



  public function edit_order($code, $approve_view = NULL)
  {
    $this->load->model('approve_logs_model');
    $this->load->model('address/address_model');
    $this->load->helper('sender');

    $ds = array();
    $order = $this->orders_model->get($code);
    if (!empty($order))
    {       
      $order->user = $this->user_model->get_name($order->user);
      $order->state_name = get_state_name($order->state);
      $state = $this->order_state_model->get_order_state($code);
      $ost = array();
      if (!empty($state))
      {
        foreach ($state as $st)
        {
          $ost[] = $st;
        }
      }
      
      $details = $this->orders_model->get_order_details($code);

      $ds['total_qty'] = 0;
      $ds['order_amount'] = 0;
      $ds['total_amount'] = 0;

      if (! empty($details))
      {
        foreach ($details as $ra)
        {
          $ds['total_qty'] += $ra->qty;
          $ds['order_amount'] += $ra->qty * $ra->price;
          $ds['total_amount'] += $ra->total_amount;
        }
      }

      $ship_to = empty($order->customer_ref) ? $this->address_model->get_ship_to_address($order->customer_code) : $this->address_model->get_shipping_address($order->customer_ref);
      
      $ds['netAmount'] = ($ds['total_amount'] - $order->bDiscAmount);
      $ds['state'] = $ost;
      $ds['order'] = $order;
      $ds['details'] = $details;
      $ds['addr']  = $ship_to;            
      $ds['cancle_reason'] = ($order->state == 9 ? $this->orders_model->get_cancle_reason($code) : NULL);
      $ds['allowEditDisc'] = FALSE;
      $ds['allowEditPrice'] = getConfig('ALLOW_EDIT_PRICE') == 1 ? TRUE : FALSE;
      $ds['edit_order'] = TRUE; //--- ใช้เปิดปิดปุ่มแก้ไขราคาสินค้าไม่นับสต็อก
      $ds['approve_view'] = $approve_view;
      $ds['approve_logs'] = $this->approve_logs_model->get($code);
      $this->load->view('support/support_edit', $ds);
    }
    else
    {
      $this->load->view('page_error');
    }
  }



  public function update_order()
  {
    $sc = TRUE;

    if ($this->input->post('order_code'))
    {
      $code = $this->input->post('order_code');
      $order = $this->orders_model->get($code);
      if (!empty($order))
      {
        if ($order->state > 1)
        {
          $ds = array(
            'remark' => $this->input->post('remark')
          );
        }
        else
        {
          $this->load->model('masters/warehouse_model');
          $wh = $this->warehouse_model->get($this->input->post('warehouse'));

          $ds = array(
            'customer_code' => $this->input->post('customer_code'),
            'date_add' => db_date($this->input->post('date_add')),
            'user_ref' => $this->input->post('user_ref'),
            'warehouse_code' => $wh->code,
            'id_address' => NULL,
            'id_sender' => NULL,
            'remark' => $this->input->post('remark'),
            'status' => 0
          );
        }

        $rs = $this->orders_model->update($code, $ds);

        if ($rs === FALSE)
        {
          $sc = FALSE;
          $this->error = 'ปรับปรุงข้อมูลไม่สำเร็จ';
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
    if ($rs->state <= 3)
    {
      $rs->customer_name = $this->customers_model->get_name($rs->customer_code);
      $details = $this->orders_model->get_order_details($code);
      $ds['order'] = $rs;
      $ds['details'] = $details;
      $ds['allowEditDisc'] = FALSE;
      $ds['allowEditPrice'] = getConfig('ALLOW_EDIT_PRICE') == 1 ? TRUE : FALSE;
      $ds['edit_order'] = FALSE; //--- ใช้เปิดปิดปุ่มแก้ไขราคาสินค้าไม่นับสต็อก
      $this->load->view('support/support_edit_detail', $ds);
    }
  }


  public function save($code)
  {
    $sc = TRUE;

    $h = json_decode($this->input->post('data'));


    if (! empty($h))
    {
      $this->db->trans_begin();

      $vat_type = $h->vat_type == 'N' ? 'I' : $h->vat_type;

      $arr = array(
        'is_term' => $h->is_term,
        'vat_type' => $h->vat_type,
        'TaxStatus' => $h->TaxStatus == 'N' ? 'N' : 'Y',
        'id_sender' => $h->id_sender,
        'shipping_code' => $h->tracking,
        'date_add' => db_date($h->date_add, TRUE),
        'customer_code' => $h->customer_code,
        'customer_name' => $h->customer_name,
        'customer_ref' => $h->customer_ref,
        'tax_id' => $h->tax_id,
        'branch_code' => $h->branch_code,
        'branch_name' => $h->branch_name,
        'address' => $h->address,
        'sub_district' => $h->sub_district,
        'district' => $h->district,
        'province' => $h->province,
        'postcode' => $h->postcode,
        'phone' => $h->phone,
        'channels_code' => NULL,
        'reference' => NULL,
        'warehouse_code' => $h->warehouse_code,
        'sale_code' => NULL,
        'remark' => get_null($h->remark),
        'bDiscText' => $h->bDiscText,
        'bDiscAmount' => $h->bDiscAmount,
        'isWht' => $h->WhtPrcnt > 0 ? 1 : 0,
        'WhtPrcnt' => $h->WhtPrcnt,
        'WhtAmount' => $h->WhtAmount,
        'doc_total' => $h->DocTotal,
        'VatSum' => $h->VatSum
      );

      if (! $this->orders_model->update($code, $arr))
      {
        $sc = FALSE;
        $this->error = "Update failed";
      }

      //---- Calculate avgBillDiscAmount vat amount each row
      if ($sc === TRUE)
      {
        $details = $this->orders_model->get_details($code);
        $avgBillDiscAmount = $h->bDiscAmount == 0 ? 0 : ($h->amountBfDisc > 0 ? $h->bDiscAmount / $h->amountBfDisc : 0);

        if (! empty($details))
        {
          foreach ($details as $rs)
          {
            if ($sc === FALSE)
            {
              break;
            }

            $sumBillDiscAmount = $avgBillDiscAmount * $rs->total_amount;
            $totalAfDisc = $rs->total_amount - $sumBillDiscAmount;
            $vatAmount = round(get_vat_amount($totalAfDisc, $rs->vat_rate, $vat_type), 6);

            $arr = array(
              'avgBillDiscAmount' => $avgBillDiscAmount,
              'sumBillDiscAmount' => $sumBillDiscAmount,
              'vat_amount' => $vatAmount
            );

            if (! $this->orders_model->update_detail($rs->id, $arr))
            {
              $sc = FALSE;
              $this->error = "Failed to update bill discount rows";
            }
          } //-- end foreach
        } //-- if ! empty($details)
      } //--- $sc === TRUE


      if ($sc === TRUE)
      {
        $order = $this->orders_model->get($code);
        
        if ($sc === TRUE)
        {
          $totalBalance = $order->doc_total - $order->paidAmount;

          $arr = array(
            'status' => 1,
            'TotalBalance' => $totalBalance < 0 ? 0 : $totalBalance
          );

          if (! $this->orders_model->update($code, $arr))
          {
            $sc = FALSE;
            $this->error = 'บันทึกออเดอร์ไม่สำเร็จ';
          }
        }
      }

      if ($sc === TRUE)
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
      $this->error = "Missing required parameter";
    }

    echo $sc === TRUE ? 'success' : $this->error;
  }  


  public function get_item()
  {
    $sc = TRUE;

    $item_code = $this->input->post('item_code');

    if (! empty($item_code))
    {
      $warehouse_code = get_null($this->input->post('warehouse_code'));

      $item = $this->products_model->get($item_code);

      if (! empty($item))
      {
        $sell_stock = $this->stock_model->get_sell_stock($item->code, $warehouse_code);
        $reserv_stock = $this->orders_model->get_reserv_stock($item->code, $warehouse_code);
        $availableStock = $sell_stock - $reserv_stock;

        $item->stock = $sell_stock <= 0 ? 0 : $sell_stock;
        $item->reserv_stock = $reserv_stock <= 0 ? 0 : $reserv_stock;
        $item->available = $availableStock > 0 ? $availableStock : 0;
      }
      else
      {
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
      'item' => $sc === TRUE ? $item : NULL
    );

    echo json_encode($arr);
  }

  public function get_new_code($date)
  {
    $date = $date == '' ? date('Y-m-d') : $date;
    $Y = date('y', strtotime($date));
    $M = date('m', strtotime($date));
    $prefix = getConfig('PREFIX_SUPPORT');
    $run_digit = getConfig('RUN_DIGIT_SUPPORT');
    $pre = $prefix . '-' . $Y . $M;
    $code = $this->orders_model->get_max_code($pre);
    if (! is_null($code))
    {
      $run_no = mb_substr($code, ($run_digit * -1), NULL, 'UTF-8') + 1;
      $new_code = $prefix . '-' . $Y . $M . sprintf('%0' . $run_digit . 'd', $run_no);
    }
    else
    {
      $new_code = $prefix . '-' . $Y . $M . sprintf('%0' . $run_digit . 'd', '001');
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


  public function get_customer_bill_to_address()
  {
    $sc = TRUE;
    $code = $this->input->get('code');
    $this->load->model('address/customer_address_model');

    if (! empty($code))
    {
      $customer = $this->customers_model->get($code);

      if (! empty($customer))
      {
        $addr = $this->customer_address_model->get_bill_to_address($customer->code);

        if (! empty($addr))
        {
          $no = 1;

          foreach ($addr as $adr)
          {
            $adr->no = $no;
            $adr->name = $customer->name;
            $no++;
          }
        }
        else
        {
          $sc = FALSE;
          $this->error = "No address found";
        }
      }
      else
      {
        $sc = FALSE;
        $this->error = "Invalid customer code";
      }
    }
    else
    {
      $sc = FALSE;
      $this->error = "Missing required parameter";
    }

    $arr = array(
      'status' => $sc === TRUE ? 'success' : 'failed',
      'message' => $sc === TRUE ? 'success' : $this->error,
      'address' => $sc === TRUE ? $addr : NULL
    );

    echo json_encode($arr);
  }


  public function clear_filter()
  {
    $filter = array(
      'support_code',
      'support_customer',
      'support_user',
      'support_user_ref',
      'support_fromDate',
      'support_toDate',
      'support_isApprove',
      'support_warehouse',
      'support_wms_export',
      'support_sap_status',
      'support_notSave',
      'support_onlyMe',
      'support_isExpire',
      'support_state_1',
      'support_state_2',
      'support_state_3',
      'support_state_4',
      'support_state_5',
      'support_state_6',
      'support_state_7',
      'support_state_8',
      'support_state_9'
    );

    clear_filter($filter);
  }
}
