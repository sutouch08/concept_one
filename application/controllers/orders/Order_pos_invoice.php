<?php
class Order_pos_invoice extends PS_Controller
{
  public $menu_code = 'SOPOSIV';
  public $menu_group_code = 'POS';
  public $menu_sub_group_code = '';
  public $title = 'ใบกำกับภาษี POS';
  public $segment = 4;

  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'orders/order_pos_invoice';
    $this->load->model('orders/order_pos_model');
    $this->load->model('orders/order_pos_invoice_model');
    $this->load->model('masters/shop_model');
    $this->load->model('masters/pos_model');
    $this->load->model('masters/invoice_customer_model');
    $this->load->helper('shop');
    $this->load->helper('order_pos');
    $this->load->helper('warehouse');
  }

  public function index()
  {
    $filter = array(
      'code' => get_filter('code', 'invoice_code', ''),
      'reference' => get_filter('reference', 'invoice_reference', ''),
      'customer' => get_filter('customer', 'invoice_customer', ''),
      'tax_id' => get_filter('tax_id', 'invoice_tax_id', ''),
      'inv_code' => get_filter('inv_code', 'inv_code', ''),
      'shop_id' => get_filter('shop_id', 'invoice_shop_id', 'all'),
      'pos_id' => get_filter('pos_id', 'invoice_pos_id', 'all'),
      'warehouse' => get_filter('warehouse', 'invoice_warehouse', 'all'),
      'status' => get_filter('status', 'invoice_sap_status', 'all'),
      'from_date' => get_filter('fromDate', 'invoice_from_date', ''),
      'to_date' => get_filter('toDate', 'invoice_to_date', '')
    );

    if($this->input->post('search'))
    {
      redirect($this->home);
    }
    else
    {
      $perpage = get_rows();
      $rows = $this->order_pos_invoice_model->count_rows($filter);
      $filter['orders'] = $this->order_pos_invoice_model->get_list($filter, $perpage, $this->uri->segment($this->segment));
      $init = pagination_config($this->home.'/index/', $rows, $perpage, $this->segment);
      $this->pagination->initialize($init);
      $this->load->view('order_pos_invoice/invoice_list', $filter);
    }
  }


  public function add_new()
  {
    $this->title = "ออกใบกำกับจาก บิลขาย POS";
    $this->load->view('order_pos_invoice/invoice_add');
  }


  public function get_invoice_customer_by_tax()
  {
    $sc = TRUE;
    $ds = array();

    $tax_id = trim($this->input->get('tax_id'));

    if( ! empty($tax_id))
    {
      $ds = $this->invoice_customer_model->get_by_tax_id($tax_id);

      if(empty($ds))
      {
        $sc = FALSE;
        $this->error = "ไม่พบข้อมูล";
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
      'data' => $sc === TRUE ? $ds : NULL
    );

    echo json_encode($arr);
  }


  public function get_bill_code()
  {
    $code = $_REQUEST['term'];

    $ds = [];

    $this->db->select('code');

    if($code != '*')
    {
      $this->db->like('code', $code);
    }

    $rs = $this->db
    ->order_by('date_add', 'DESC')
    ->order_by('code', 'DESC')
    ->limit(20)
    ->get('order_pos');

    if($rs->num_rows() > 0)
    {
      foreach($rs->result() as $ro)
      {
        $ds[] = $ro->code;
      }
    }

    echo json_encode($ds);
  }


  public function add_invoice()
  {
    $this->load->model('inventory/delivery_order_model');
    $this->load->model('inventory/movement_model');

    $sc = TRUE;

    $ds = json_decode($this->input->post('data'));

    if( ! empty($ds))
    {
      $bill = $this->order_pos_model->get_by_id($ds->bill_id);

      if( ! empty($bill))
      {
        if( $bill->status == 'O' && empty($bill->ref_code) && empty($bill->invoice_code))
        {
          if( empty($ds->customer_id) )
          {
            $rs = $this->invoice_customer_model->get_by_tax_id($ds->tax_id);

            if(empty($rs))
            {
              $arr = array(
                'name' => $ds->name,
                'tax_id' => $ds->tax_id,
                'branch_code' => $ds->branch_code,
                'branch_name' => $ds->branch_name,
                'address' => $ds->address,
                'phone' => $ds->phone,
                'is_company' => $ds->is_company == 1 ? 1 : 0
              );

              $customer_id = $this->invoice_customer_model->add($arr);

              if($customer_id)
              {
                $ds->customer_id = $customer_id;
              }
              else
              {
                $sc = FALSE;
                $this->error = "เพิ่มข้อมูลลูกค้าไม่สำเร็จ";
              }
            }
          }


          if($sc === TRUE && ! empty($ds->customer_id))
          {
            $date_add = getConfig('ORDER_SOLD_DATE') == 'D' ? $bill->date_add : now();

            $cust = $this->invoice_customer_model->get_by_id($ds->customer_id);

            if( ! empty($cust))
            {
              $shop = $this->shop_model->get_by_id($bill->shop_id);

              if( ! empty($shop))
              {
                $this->db->trans_begin();

                $code = $this->get_new_code($shop->prefix, $shop->running);

                $arr = array(
                  'doc_date' => date('Y-m-d'),
                  'code' => $code,
                  'bookcode' => getConfig('BOOK_CODE_POS_INVOICE'),
                  'customer_code' => $bill->customer_code,
                  'tax_id' => $cust->tax_id,
                  'branch_code' => $cust->branch_code,
                  'branch_name' => $cust->branch_name,
                  'customer_name' => $cust->name,
                  'customer_address' => $cust->address,
                  'phone' => $cust->phone,
                  'is_company' => $cust->is_company,
                  'channels_code' => $bill->channels_code,
                  'payment_code' => $bill->payment_code,
                  'reference' => $bill->code,
                  'amount_bf_disc' => $bill->amount_bf_disc,
                  'disc_amount' => $bill->disc_amount,
                  'amount' => $bill->amount,
                  'vat_type' => $bill->vat_type,
                  'vat_rate' => $bill->vat_rate,
                  'vat_amount' => $bill->vat_amount,
                  'shop_id' => $bill->shop_id,
                  'pos_id' => $bill->pos_id,
                  'warehouse_code' => $bill->warehouse_code,
                  'zone_code' => $bill->zone_code
                );

                $id = $this->order_pos_invoice_model->add($arr);

                if($id)
                {
                  $details = $this->order_pos_model->get_details_by_id($bill->id);

                  if( ! empty($details))
                  {
                    foreach($details as $rs)
                    {
                      if($sc === FALSE)
                      {
                        break;
                      }

                      $arr = array(
                        'invoice_id' => $id,
                        'invoice_code' => $code,
                        'order_code' => $rs->order_code,
                        'product_code' => $rs->product_code,
                        'product_name' => $rs->product_name,
                        'unit_code' => $rs->unit_code,
                        'qty' => $rs->qty,
                        'price' => $rs->price,
                        'final_price' => $rs->final_price,
                        'discount_label' => $rs->discount_label,
                        'discount_amount' => $rs->discount_amount,
                        'amount' => $rs->total_amount,
                        'vat_code' => $rs->vat_code,
                        'vat_type' => $rs->vat_type,
                        'vat_rate' => $rs->vat_rate,
                        'vat_amount' => $rs->vat_amount,
                        'id_rule' => $rs->id_rule,
                        'id_policy' => $rs->id_policy
                      );

                      if( ! $this->order_pos_invoice_model->add_detail($arr))
                      {
                        $sc = FALSE;
                        $this->error = "เพิ่มรายการไม่สำเร็จ";
                      }
                      else
                      {
                        //--- add to order_sold
                        //--- ข้อมูลสำหรับบันทึกยอดขาย
                        $arr = array(
                          'reference' => $code,
                          'role' => 'H', //--- POS
                          'payment_code'  => $bill->payment_code,
                          'channels_code' => $bill->channels_code,
                          'product_code' => $rs->product_code,
                          'product_name' => $rs->product_name,
                          'product_style' => $rs->style_code,
                          'cost' => $rs->cost,
                          'price' => $rs->price,
                          'sell' => $rs->final_price,
                          'qty' => $rs->qty,
                          'discount_label' => $rs->discount_label,
                          'discount_amount' => $rs->discount_amount,
                          'total_amount' => $rs->total_amount,
                          'total_cost' => $rs->cost * $rs->qty,
                          'margin' =>  $rs->total_amount - ($rs->cost * $rs->qty),
                          'id_rule' => $rs->id_rule,
                          'id_policy' => $rs->id_policy,
                          'customer_code' => $bill->customer_code,
                          'sale_code'   => $bill->sale_id,
                          'user' => $bill->uname,
                          'date_add'  => $date_add, //---- เปลี่ยนไปตาม config ORDER_SOLD_DATE
                          'zone_code' => $bill->zone_code,
                          'warehouse_code'  => $bill->warehouse_code,
                          'update_user' => $this->_user->uname,
                          'order_detail_id' => $rs->id
                        );

                        if( ! $this->delivery_order_model->sold($arr))
                        {
                          $sc = FALSE;
                          $this->error = "บันทึกขายไม่สำเร็จ : Failed to insert order_sold";
                        }
                        else
                        {
                          //--- 2. update movement
                          if($rs->is_count)
                          {
                            $arr = array(
                              'reference' => $code,
                              'warehouse_code' => $bill->warehouse_code,
                              'zone_code' => $bill->zone_code,
                              'product_code' => $rs->product_code,
                              'move_in' => 0,
                              'move_out' => $rs->qty,
                              'date_add' => $date_add
                            );

                            if( ! $this->movement_model->add($arr))
                            {
                              $sc = FALSE;
                              $this->error = 'บันทึก movement ขาออกไม่สำเร็จ';
                            }
                          } //-- is_count
                        }
                      }
                    }

                    if($sc === TRUE)
                    {
                      if( ! $this->order_pos_model->update($bill->code, array('invoice_code' => $code, 'status' => 'C')))
                      {
                        $sc = FALSE;
                        $this->error = "Failed to update bill status";
                      }

                      if($sc === TRUE)
                      {
                        if( ! $this->order_pos_model->update_details($bill->code, array('status' => 'C')))
                        {
                          $sc = FALSE;
                          $this->error = "Failed to update items status";
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

                    if($sc === TRUE)
                    {
                      //--- export to SAP
                      if( ! $this->do_export($code))
                      {
                        $sc = FALSE;
                        $this->error = "บันทึกเอกสารสำเร็จ แต่ส่งข้อมูลเข้า SAP Temp ไม่สำเร็จ กรุณากดส่งใหม่อีกครั้ง";
                      }
                    }
                  }
                  else
                  {
                    $sc = FALSE;
                    $this->error = "ไม่พบรายการสินค้า";
                  }
                }
                else
                {
                  $sc = FALSE;
                  $this->error = "สร้างใบกำกับภาษีไม่สำเร็จ";
                }
              }
              else
              {
                $sc = FALSE;
                $this->error = "ไม่พบข้อมูลจุดขาย";
              }
            }
            else
            {
              $sc = FALSE;
              $this->error = "ไม่พบข้อมูลลูกค้า";
            }
          }
          else
          {
            $sc = FALSE;
            $this->error = "เพิ่มข้อมูลลูกค้าไม่สำเร็จ";
          }
        }
        else
        {
          $sc = FALSE;
          $this->error = "ไม่สามารถเปิดใบกำกับภาษีได้ เนื่องจาก บิลนี้ถูกดึงไปตัดเปิดใบกำกับภาษี หรือ ถูกดึงไปตัดยอดขายแล้ว";
        }
      }
      else
      {
        $sc = FALSE;
        $this->error = "ไม่พบเลขที่บิล";
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
      'invoice_code' => $sc === TRUE ? $code : NULL,
      'invoice_id' => $sc === TRUE ? $id : NULL
    );

    echo json_encode($arr);
  }


  public function view_detail($id)
  {
    $order = $this->order_pos_invoice_model->get_by_id($id);

    if( ! empty($order))
    {
      $details = $this->order_pos_invoice_model->get_details_by_id($id);
      $pos = $this->pos_model->get_pos($order->pos_id);
      $ds = array(
        'order' => $order,
        'details' => $details,
        'pos' => $pos
      );

      $this->load->view('order_pos_invoice/invoice_detail', $ds);
    }
    else
    {
      $this->page_error();
    }
  }


  public function cancel_invoice()
  {
    $sc = TRUE;
    $id = $this->input->post('id');
    $reason = trim($this->input->post('reason'));

    if($this->pm->can_delete)
    {
      $order = $this->order_pos_invoice_model->get_by_id($id);

      if( ! empty($order))
      {
        if($order->status != 'D')
        {
          $sap = $this->order_pos_invoice_model->get_sap_doc_num($order->code);

          if( ! empty($sap))
          {
            $sc = FALSE;
            $this->error = "กรุณายกเลิก DO : {$sap} ใน SAP ก่อนทำการยกเลิก";
          }

          if($sc === TRUE)
          {
            $this->load->model('inventory/invoice_model');
            $this->load->model('inventory/movement_model');

            $this->db->trans_begin();

            /* 1. cancel invoice
               2. cancel invoice rows
               3. remove order_sold
               4. remove movement
               5. roll back bill status
            */
            $arr = array(
              'status' => 'D',
              'cancel_reason' => $reason,
              'cancel_date' => now(),
              'cancel_user' => $this->_user->uname
            );

            //--1. cancel invoice
            if( ! $this->order_pos_invoice_model->update_by_id($order->id, $arr))
            {
              $sc = FALSE;
              $this->error = "ยกเลิกเอกสารไม่สำเร็จ : Failed to update invoice status";
            }

            //--2. cancel invoice rows
            if($sc === TRUE)
            {
              if( ! $this->order_pos_invoice_model->update_details_by_id($order->id, array('status' => 'D')))
              {
                $sc = FALSE;
                $this->error = 'ยกเลิกรายการไม่สำเร็จ : Failed to update invoice rows status';
              }
            }

            //---3. remove order_sold
            if($sc === TRUE)
            {
              if( ! $this->invoice_model->drop_all_sold($order->code))
              {
                $sc = FALSE;
                $this->error = "ลบรายการบันทึกขายไม่สำเร็จ : Failed to delete order_sold";
              }
            }

            //---4. remove movement
            if($sc === TRUE)
            {
              if( ! $this->movement_model->drop_movement($order->code))
              {
                $sc = FALSE;
                $this->error = "ลบรายการเคลื่อนไหวสินค้าไม่สำเร็จ : Failed to delete stock movement";
              }
            }

            //---5. roll back bill status
            if($sc === TRUE)
            {
              $arr = array(
                'invoice_code' => NULL,
                'status' => 'O'
              );

              if( ! $this->order_pos_model->update($order->reference, $arr))
              {
                $sc = FALSE;
                $this->error = "ย้อนสถานะบิลขายไม่สำเร็จ : {$order->reference} : Failed to rollback bill status {$order->reference}";
              }

              if($sc === TRUE)
              {
                if( ! $this->order_pos_model->update_details($order->reference, array('status' => 'O')))
                {
                  $sc = FALSE;
                  $this->error = "ย้อนสถานะรายการบิลขายไม่สำเร็จ : {$order->reference} : Failed to rollback bill item status {$order->reference}";
                }
              }
            }

            if( $sc === TRUE)
            {
              $this->db->trans_commit();

              $this->load->model('inventory/delivery_order_model');

              //---- ถ้าออเดอร์ยังไม่ถูกเอาเข้า SAP ลบออกจากถังกลางด้วย
              $middle = $this->delivery_order_model->get_middle_delivery_order($order->code);

              if( ! empty($middle))
              {
                foreach($middle as $rows)
                {
                  $this->delivery_order_model->drop_middle_exits_data($rows->DocEntry);
                }
              }
            }
            else
            {
              $this->db->trans_rollback();
            }
          }
        }
        else
        {
          $sc = FALSE;
          $this->error = "เอกสารถูกยกเลิกแล้วโดย {$order->cancel_user} เมื่อวันที่ ".thai_date($order->cancel_date, TRUE);
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
      set_error('permission');
    }

    $arr = array(
      'status' => $sc === TRUE ? 'success' : 'failed',
      'message' => $sc === TRUE ? 'success' : $this->error
    );

    echo json_encode($arr);
  }


  public function manual_export($code)
  {
    echo $this->do_export($code) ? 'success' : $this->error;
  }

  private function do_export($code)
  {
    $sc = TRUE;

    $this->load->library('export');

    if(! $this->export->export_invoice($code))
    {
      $sc = FALSE;
      $this->error = trim($this->export->error);
    }

    return $sc;
  }


  public function print_invoice($invoice_code)
  {
    $this->load->library('xprinter');
    $this->load->helper('print');

    $order = $this->order_pos_invoice_model->get_by_code($invoice_code);

    if( ! empty($order))
    {
      $details = $this->order_pos_invoice_model->get_details_by_code($invoice_code);

      $arr = array(
        'order' => $order,
        'details' => $details,
        'title' => 'ใบเสร็จรับเงิน/ใบกำกับภาษี'
      );

      $this->load->view('print/print_pos_invoice', $arr);
    }
    else
    {
      $this->page_error();
    }
  }

  public function get_new_code($prefix, $run_digit = 4, $date = NULL)
  {
    $date = empty($date) ? date('Y-m-d') : ($date < '2020-01-01' ? date('Y-m-d') : $date);
		$Y = date('y', strtotime($date));
    $M = date('m', strtotime($date));
    $pre = $prefix .'-'.$Y.$M;
    $code = $this->order_pos_invoice_model->get_max_code($pre);

    if(! empty($code))
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

  function clear_filter()
  {
    $filter = array(
      'invoice_code',
      'invoice_reference',
      'invoice_customer',
      'invoice_tax_id',
      'inv_code',
      'invoice_shop_id',
      'invoice_pos_id',
      'invoice_warehouse',
      'invoice_status',
      'invoice_from_date',
      'invoice_to_date'
    );

    return clear_filter($filter);
  }
}
 ?>
