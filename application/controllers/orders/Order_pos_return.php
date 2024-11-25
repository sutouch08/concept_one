<?php
class Order_pos_return extends PS_Controller
{
  public $menu_code = 'SOPOSRT';
  public $menu_group_code = 'POS';
  public $menu_sub_group_code = '';
  public $title = 'ใบลดหนี้เครื่อง POS';
  public $segment = 5;

  public function __construct()
  {
    parent::__construct();

    $this->home = base_url().'orders/order_pos_return';
    $this->load->model('orders/order_pos_return_model');
    $this->load->model('orders/order_pos_model');
    $this->load->model('orders/pos_sales_movement_model');
    $this->load->model('masters/shop_model');
    $this->load->model('masters/pos_model');
    $this->load->model('inventory/return_order_model');
    $this->load->helper('discount');
    $this->load->helper('shop');
    $this->load->helper('payment_method');
    $this->load->helper('warehouse');
    $this->load->helper('order_pos');
  }

  public function index()
  {
    $filter = array(
      'shop_id' => get_filter('shop_id', 'return_shop_id', 'all'),
      'pos_id' => get_filter('pos_id', 'return_pos_id', 'all'),
      'code' => get_filter('code', 'return_code', ''),
      'order_code' => get_filter('order_code', 'return_order_code', ''),
      'ref_code' => get_filter('ref_code', 'return_ref_code', ''),
      'status' => get_filter('status', 'return_status', 'all'),
      'from_date' => get_filter('from_date', 'return_from_date', ''),
      'to_date' => get_filter('to_date', 'return_to_date', '')
    );

    if($this->input->post('search'))
    {
      redirect($this->home);
    }
    else
    {
      $this->segment = 4;
      $perpage = get_rows();
      $rows = $this->order_pos_return_model->count_rows($filter);
      $filter['orders'] = $this->order_pos_return_model->get_list($filter, $perpage, $this->uri->segment($this->segment));
      $init = pagination_config($this->home.'/index/', $rows, $perpage, $this->segment);
      $this->pagination->initialize($init);
      $this->load->view('order_pos_return/order_pos_return_list', $filter);
    }
  }


  public function get_return_view()
  {    
    $sc = TRUE;
    $code = $this->input->get('code');
    $doc = $this->order_pos_return_model->get($code);

    if( ! empty($doc))
    {
      $total_return = 0;
      $total_qty = 0;
  		$total_price = 0;
  		$total_amount = 0;
  		$total_discount = 0;

      $pos = $this->pos_model->get_pos($doc->pos_id);
      $details = $this->order_pos_return_model->get_details_by_id($doc->id);

      $ds = array(
        'bill_header_1' => $pos->bill_header_1,
        'header_size_1' => $pos->header_size_1,
        'bill_header_2' => $pos->bill_header_2,
        'header_size_2' => $pos->header_size_2,
        'bill_header_3' => $pos->bill_header_3,
        'header_size_3' => $pos->header_size_3,
        'header_align_1' => $pos->header_align_1,
        'header_align_2' => $pos->header_align_2,
        'header_align_3' => $pos->header_align_3,
        'tax_id' => $pos->tax_id,
        'pos_no' => $pos->pos_no,
        'shop_code' => $pos->shop_code,
        'shop_name' => $pos->shop_name,
        'warehouse_code' => $doc->warehouse_code,
        'zone_code' => $doc->zone_code,
        'zone_name' => $doc->zone_name,
        'code' => $doc->code,
        'id' => $doc->id,
        'date_add' => thai_date($doc->date_add, TRUE),
        'details' => $details,
        'total_qty' => number($doc->qty, 2),
        'total_amount' => number($doc->amount, 2),
        'total_vat' => number($doc->vat_amount, 2),
        'order_code' => $doc->order_code,
        'ref_code' => $doc->ref_code,
        'staff' => $this->user_model->get_name($doc->uname),
        'allow_cancel' => $doc->status == 'O' ? TRUE : FALSE,
        'status' => $doc->status,
        'status_label' => bill_status_label($doc->status),
        'is_cancel' => $doc->status == 'D' ? TRUE : FALSE,
        'cancel_reason' => $doc->cancel_reason,
        'cancel_user' => $doc->cancel_user,
        'cancel_date' => thai_date($doc->cancel_date, TRUE),
        'remark' => $doc->remark,
        'approver' => $doc->approver,
        'approve_date' => thai_date($doc->approve_date, TRUE)
      );
    }
    else
    {
      $sc = FALSE;
      $this->error = "ไม่พบเลขเอกสาร";
    }

    $arr = array(
      'status' => $sc === TRUE ? 'success' : 'failed',
      'message' => $sc === TRUE ? 'success' : $this->error,
      'data' => $sc === TRUE ? $ds : NULL
    );

    echo json_encode($arr);
  }

  public function cancel_bill()
  {
    $sc = TRUE;
    $code = $this->input->post('code');
    $reason = $this->input->post('reason');

    if($this->pm->can_delete)
    {
      if( ! empty($code))
      {
        $doc = $this->order_pos_return_model->get($code);

        if( ! empty($doc))
        {
          $pos = $this->pos_model->get_pos($doc->pos_id);

          if($doc->status == 'O')
          {
            $this->db->trans_begin();

            //--- 'O' = Open, 'C' = Closed, 'D' = Cancelled
            //--- set line status to 'D'
            if( ! $this->order_pos_return_model->update_details($doc->id, array('status' => 'D')))
            {
              $sc = FALSE;
              $this->error = "Failed to update line status";
            }

            //--- set document status to 'D'
            if($sc === TRUE)
            {
              $arr = array(
                'status' => 'D',
                'cancel_reason' => trim($reason),
                'cancel_user' => $this->_user->uname,
                'cancel_date' => now()
              );

              if( ! $this->order_pos_return_model->update($doc->id, $arr))
              {
                $sc = FALSE;
                $this->error = "Failed to update document status";
              }

              //--- update return_qty in bill item
              if($sc === TRUE)
              {
                $details = $this->order_pos_return_model->get_details($code);

                if( ! empty($details))
                {
                  foreach($details as $rs)
                  {
                    if($sc === FALSE)
                    {
                      break;
                    }

                    $qty = $rs->return_qty * (-1); //--- ทำจำนวนให้ติดลบ เพื่อให้ยอดลดลงเมื่อบวกกลับเข้าไปใน field order_pos_detail.return_qty

                    if( ! $this->order_pos_model->update_return_qty($rs->order_line_id, $qty))
                    {
                      $sc = FALSE;
                      $this->error = "Failed to rollback bill return qty";
                    }
                  }
                }

                //--- add sales movement
                if($sc === TRUE)
                {
                  $arr = array(
                    'code' => $doc->code,
                    'type' => 'CR',     //--- S = sales , C = Cancel, R = Return
                    'shop_id' => $doc->shop_id,
                    'pos_id' => $doc->pos_id,
                    'amount' => $doc->amount,
                    'payment_role' => $doc->payment_role,
                    'acc_id' => $doc->acc_id,
                    'user' => $this->_user->uname,
                    'round_id' => $pos->round_id
                  );

                  if( ! $this->pos_sales_movement_model->add($arr))
                  {
                    $sc = FALSE;
                    $this->error = "Insert sales movement failed";
                  }
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
            set_error('status');
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


  public function create_cn()
  {
    $sc = TRUE;

    $data = json_decode($this->input->post('data'));

    if( ! empty($data))
    {
      $shop = $this->shop_model->get_by_id($data->shop_id);

      if( ! empty($shop))
      {
        if( ! empty($data->bills))
        {
          $details = $this->order_pos_return_model->get_details_in_bills($data->bills);

          if( ! empty($details))
          {
            //--- create WM
            $code = $this->get_new_sm_code();

            $bookcode = getConfig('BOOK_CODE_RETURN_ORDER');

            $arr = array(
              'code' => $code,
              'bookcode' => $bookcode,
              'customer_code' => $shop->customer_code,
              'warehouse_code' => $shop->warehouse_code,
              'zone_code' => $shop->zone_code,
              'remark' => 'Generate by '.$shop->name,
              'date_add' => now(),
              'shipped_date' => now(),
              'status' => 0,
              'ref_type' => 4,
              'user' => $this->_user->uname
            );

            $this->db->trans_begin();

            if(! $this->return_order_model->add($arr))
            {
              $sc = FALSE;
              $this->error = "เพิ่มเอกสารไม่สำเร็จ";
            }


            if($sc === TRUE)
            {
              foreach($details as $rs)
              {
                if($rs->return_qty > 0)
                {
                  $arr = array(
                    'return_code' => $code,
                    'order_code' => $rs->order_code,
                    'product_code' => $rs->product_code,
                    'product_name' => $rs->product_name,
                    'sold_qty' => $rs->order_qty,
                    'qty' => $rs->return_qty,
                    'receive_qty' => $rs->return_qty,
                    'price' => $rs->price,
                    'discount_percent' => round(discountAmountToPercent($rs->discount_amount, 1, $rs->price), 2),
                    'amount' => $rs->total_amount,
                    'vat_amount' => $rs->vat_amount,
                    'valid' => 1,
                    'ref_code' => $rs->return_code,
                    'input_type' => 4 //---- 1 = key in , 2 = consolidate, 3 = excel, 4 = POS
                  );

                  if( ! $this->return_order_model->add_detail($arr))
                  {
                    $sc = FALSE;
                    $this->error = "เพิ่มรายการสินค้าเข้าเอกสารไม่สำเร็จ - {$rs->product_code} : {$rs->order_code}";
                    break;
                  }
                } //--- end if qty
              }
            }

            //---- Update bill status
            if($sc === TRUE)
            {
              $arr = array('status' => 'C');

              if( ! $this->order_pos_return_model->update_bills_details($data->bills, $arr))
              {
                $sc = FALSE;
                $this->error = "Failed to update pos items status";
              }

              if($sc === TRUE)
              {
                $arr = array('status' => 'C', 'ref_code' => $code);

                if( ! $this->order_pos_return_model->update_bills($data->bills, $arr))
                {
                  $sc = FALSE;
                  $this->error = "Failed to update bills status";
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
            $this->error = "ไม่พบรายการสินค้า";
          }
        }
        else
        {
          $sc = FALSE;
          $this->error = "ไม่พบเอกสารรับคืน";
        }
      }
      else
      {
        $sc = FALSE;
        $this->error = "ไม่พบจุดขาย";
      }
    }

    $arr = array(
      'status' => $sc === TRUE ? 'success' : 'failed',
      'message' => $sc === TRUE ? 'success' : $this->error,
      'code' => $sc === TRUE ? $code : NULL
    );

    echo json_encode($arr);
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


  public function get_new_sm_code($date = NULL)
  {
    $date = empty($date) ? date('Y-m-d') : $date;
    $Y = date('y', strtotime($date));
    $M = date('m', strtotime($date));
    $prefix = getConfig('PREFIX_RETURN_ORDER');
    $run_digit = getConfig('RUN_DIGIT_RETURN_ORDER');
    $pre = $prefix .'-'.$Y.$M;
    $code = $this->return_order_model->get_max_code($pre);

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


  public function clear_filter()
  {
    $filter = array(
      'return_shop_id',
      'return_pos_id',
      'return_code',
      'return_order_code',
      'return_ref_code',
      'return_status',
      'return_from_date',
      'return_to_date'
    );

    return clear_filter($filter);
  }

} //--- end class
 ?>
