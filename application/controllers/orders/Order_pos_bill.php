<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Order_pos_bill extends PS_Controller
{
  public $menu_code = 'SOPOSBI';
	public $menu_group_code = 'POS';
  public $menu_sub_group_code = '';
	public $title = 'บิลขายหน้าร้าน';
  public $segment = 4;

  public function __construct()
  {
    parent::__construct();
		$this->home = base_url().'orders/order_pos_bill';
		$this->load->model('masters/shop_model');
		$this->load->model('masters/pos_model');
    $this->load->model('masters/invoice_customer_model');
    $this->load->model('orders/order_pos_model');
		$this->load->model('orders/order_pos_bill_model');
    $this->load->model('orders/pos_sales_movement_model');
    $this->load->model('orders/order_pos_payment_model');
    $this->load->model('orders/order_down_payment_model');
		$this->load->helper('shop');
    $this->load->helper('order_pos');
    $this->load->helper('warehouse');
    $this->load->helper('payment_method');
    $this->load->helper('channels');
    $this->load->helper('saleman');
  }


  public function index()
  {
    $this->load->helper('payment_method');

    $filter = array(
      'shop_id' => get_filter('shop_id', 'bill_shop_id', 'all'),
      'pos_id' => get_filter('pos_id', 'bill_pos_id', 'all'),
      'code' => get_filter('code', 'bill_code', ''),
      'invoice_code' => get_filter('invoice_code', 'bill_invoice_code', ''),
      'ref_code' => get_filter('ref_code', 'bill_ref_code', ''),
      'so_code' => get_filter('so_code', 'bill_so_code', ''),
      'channels' => get_filter('channels', 'bill_channels', 'all'),
      'payment' => get_filter('payment', 'bill_payment', 'all'),
      'status' => get_filter('status', 'bill_status', 'all'),
      'from_date' => get_filter('from_date', 'bill_from_date', ''),
      'to_date' => get_filter('to_date', 'bill_to_date', ''),
      'user' => get_filter('user', 'bill_user', 'all'),
      'sale_id' => get_filter('sale_id', 'bill_sale_id', 'all'),
      'vat_type' => get_filter('vat_type', 'bill_vat_type', 'all')
    );

    if($this->input->post('search'))
    {
      redirect($this->home);
    }
    else
    {
      $perpage = get_rows();
      $rows = $this->order_pos_bill_model->count_rows($filter);
      $filter['orders'] = $this->order_pos_bill_model->get_list($filter, $perpage, $this->uri->segment($this->segment));
      $init = pagination_config($this->home.'/index/', $rows, $perpage, $this->segment);
      $this->pagination->initialize($init);
      $this->load->view('order_pos_bill/bill_list', $filter);
    }
  }


  public function view_bill_detail($id)
  {
    $this->title = "บิลขายหน้าร้าน";
    $this->load->helper('saleman');
    $this->load->helper('payment_method');
    $order = $this->order_pos_model->get_by_id($id);

    if( ! empty($order))
    {
      $ds = array(
        'order' => $order,
        'pos' => $this->pos_model->get_pos($order->pos_id),
        'details' => $this->order_pos_model->get_details_by_id($id),
        'down_payment' => $this->order_down_payment_model->get_details_by_target($order->code),
        'payments' => $this->order_pos_payment_model->get_payments($order->code)
      );

      $this->load->view('order_pos_bill/bill_detail', $ds);
    }
    else
    {
      $this->page_eror();
    }
  }


  public function view_detail($code)
  {
    $this->title = "บิลขายหน้าร้าน";
    $this->load->helper('saleman');
    $this->load->helper('payment_method');
    $order = $this->order_pos_model->get($code);

    if( ! empty($order))
    {
      $ds = array(
        'order' => $order,
        'pos' => $this->pos_model->get_pos($order->pos_id),
        'details' => $this->order_pos_model->get_details($code),
        'down_payment' => $this->order_down_payment_model->get_details_by_target($order->code),
        'payments' => $this->order_pos_payment_model->get_payments($order->code)
      );

      $this->load->view('order_pos_bill/bill_detail', $ds);
    }
    else
    {
      $this->page_eror();
    }
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

  public function add_invoice_customer()
  {
    $sc = TRUE;

    $ds = json_decode($this->input->post('data'));

    if( ! empty($ds))
    {
      $arr = array(
        'name' => $ds->customer_name,
        'tax_id' => $ds->tax_id,
        'branch_code' => get_null($ds->branch_code),
        'branch_name' => get_null($ds->branch_name),
        'address' => get_null($ds->address),
        'sub_district' => get_null($ds->subDistrict),
        'district' => get_null($ds->district),
        'province' => get_null($ds->province),
        'postcode' => get_null($ds->postcode),
        'phone' => $ds->phone,
        'is_company' => $ds->is_company == 1 ? 1 : 0
      );

      if( empty($ds->customer_id) )
      {

        if( ! $this->invoice_customer_model->add($arr))
        {
          $sc = FALSE;
          $this->error = "เพิ่มข้อมูลลูกค้าไม่สำเร็จ";
        }
      }
      else
      {
        if( ! $this->invoice_customer_model->update($ds->customer_id, $arr))
        {
          $sc = FALSE;
          $this->error = "แก้ไขข้อมูลลูกค้าไม่สำเร็จ";
        }
      }
    }
    else
    {
      $sc = FALSE;
      $this->errorr = "Missing required parameter";
    }

    $arr = array(
      'status' => $sc === TRUE ? 'success' : 'failed',
      'message' => $sc === TRUE ? 'success' : $this->error
    );

    echo json_encode($arr);
  }


  public function update()
  {
    $sc = TRUE;
    $ds = json_decode($this->input->post('data'));

    if( ! empty($ds))
    {
      $arr = array(
        'customer_name' => $ds->customer_name,
        'tax_id' => $ds->tax_id,
        'isCompany' => $ds->is_company,
        'branch_code' => $ds->branch_code,
        'branch_name' => $ds->branch_name,
        'Address' => $ds->address,
        'sub_district' => $ds->sub_district,
        'district' => $ds->district,
        'province' => $ds->province,
        'postcode' => $ds->postcode,
        'phone' => $ds->phone
      );

      if( ! $this->order_pos_model->update($ds->bill_code, $arr))
      {
        $sc = FALSE;
        $this->error = "Failed to update billing address";
      }
    }
    else
    {
      $sc = FALSE;
      $this->error = "Missing required parameter";
    }

    echo $sc === TRUE ? 'success' : $this->error;
  }


  public function cancel_bill()
  {
    $this->load->model('orders/sales_order_model');
    $sc = TRUE;
    $code = $this->input->post('code');
    $id = $this->input->post('id');
    $reason = trim($this->input->post('reason'));
    $return_payment_role = $this->input->post('return_payment_role');

    $bill = $this->order_pos_model->get_by_id($id);

    if( ! empty($bill))
    {
      $pos = $this->pos_model->get_pos($bill->pos_id);

      if($bill->status == 'O' OR $bill->status == 'D')
      {
        if($bill->status  == 'O')
        {
          $this->db->trans_begin();

          $ds = array(
            'status' => 'D'
          );

          if( ! $this->order_pos_model->update_details_by_id($bill->id, $ds))
          {
            $sc = FALSE;
            $this->error = "Failed to update items status";
          }

          if($sc === TRUE)
          {
            $ds = array(
              'status' => 'D',
              'cancel_reason' => $reason,
              'cancel_user' => $this->_user->uname,
              'cancel_date' => now(),
              'return_payment_role' => empty($return_payment_role) ? 1 : $return_payment_role
            );

            if( ! $this->order_pos_model->update_by_id($bill->id, $ds))
            {
              $sc = FALSE;
              $this->error = "Failed to update bill status";
            }

            //--- add sales movement
            if($sc === TRUE)
            {
              $arr = array(
                'code' => $bill->code,
                'type' => 'C',     //--- S = sales , C = Cancel, R = Return
                'shop_id' => $bill->shop_id,
                'pos_id' => $bill->pos_id,
                'amount' => $bill->payAmount * (-1),
                'payment_role' => $return_payment_role,
                'acc_id' => $bill->acc_id,
                'user' => $this->_user->uname,
                'round_id' => $pos->round_id
              );

              if( ! $this->pos_sales_movement_model->add($arr))
              {
                $sc = FALSE;
                $this->error = "Insert sales movement failed";
              }
            }

            //--- roll back down payment
            if($sc === TRUE && $bill->down_payment_amount > 0)
            {
              $rows = $this->order_down_payment_model->get_details_by_target($bill->code); //$this->order_down_payment_model->get_by_ref_code($bill->code);

              if( ! empty($rows))
              {
                //--- roll back status down_payment
                foreach($rows as $ro)
                {
                  if($sc === FALSE)
                  {
                    break;
                  }

                  $dp = $this->order_down_payment_model->get_by_id($ro->down_payment_id);

                  if( ! empty($dp))
                  {
                    if($this->order_down_payment_model->update_detail($ro->id, ['is_cancel' => 1]))
                    {
                      $used = $dp->used - $ro->amount;
                      $available = $dp->amount - $used;
                      $arr = array(
                        'used' => $used,
                        'available' => $available,
                        'status' => 'O'
                      );

                      if( ! $this->order_down_payment_model->update($dp->id, $arr))
                      {
                        $sc = FALSE;
                        $this->error = "Cannot update down payment available amount";
                      }
                    }
                    else
                    {
                      $sc = FALSE;
                      $this->error = "Cannot update down payment detail status";
                    }
                  }
                  else
                  {
                    $sc = FALSE;
                    $this->error = "Invalid Down payment document number";
                  }
                } //--- end foreach
              } //-- end if rows
            }

            //--- rollback so
            if($sc === TRUE && ! empty($bill->so_code))
            {
              $so = $this->sales_order_model->get($bill->so_code);

              if( ! empty($so))
              {
                $paidAmount = $so->paidAmount - $bill->payAmount;
                $paidAmount = $paidAmount > 0 ? $paidAmount : 0;
                $totalBalance = $so->DocTotal - $paidAmount;

                $arr = array(
                  'status' => 'O',
                  'paidAmount' => $paidAmount,
                  'TotalBalance' => $totalBalance > 0 ? $totalBalance : 0
                );

                if( ! $this->sales_order_model->update($so->code, $arr))
                {
                  $sc = FALSE;
                  $this->error = "Failed to rollback Sales order status";
                }
              }

              //--- roll back open qty
              if($sc === TRUE)
              {
                $details = $this->order_pos_model->get_details($bill->code);

                if( ! empty($details))
                {
                  foreach($details as $rs)
                  {
                    if( ! empty($rs->line_id))
                    {
                      $sol = $this->sales_order_model->get_detail($rs->line_id);

                      if( ! empty($sol))
                      {
                        $openQty = $rs->qty + $sol->OpenQty;

                        if($openQty <= $sol->qty)
                        {
                          if( ! $this->sales_order_model->update_detail($rs->line_id, ['OpenQty' => $openQty, 'line_status' => 'O']))
                          {
                            $sc = FALSE;
                            $this->error = "Failed to rollback Sales Order OpenQty @line_id : {$rs->line_id}";
                          }
                        }
                        else
                        {
                          $sc = FALSE;
                          $this->error = "Open quantity cannot be greater than sale order quantity @line_id : {$rs->line_id}";
                        }
                      }
                      else
                      {
                        $sc = FALSE;
                        $this->error = "Sales order line not found";
                      } //-- if( ! empty($sol))
                    } // -- if( ! empty($rs->line_id))
                  } //--- foreach details
                } //--- if( ! empty($details))
              } //--- $sc === TRUE
            } // --- if( ! empty($bill->so_code))
          } //-- $sc === TRUE

          if($sc === TRUE)
          {
            $this->db->trans_commit();
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
        $this->error = "Invalid document status";
      }
    }
    else
    {
      $sc = FALSE;
      $this->error = "ไม่พบเลขที่บิล";
    }

    $arr = array(
      'status' => $sc === TRUE ? 'success' : 'failed',
      'message' => $sc === TRUE ? 'success' : $this->error
    );

    echo json_encode($arr);
  }



  public function clear_filter()
  {
    $filter = array(
      'bill_code',
      'bill_ref_code',
      'bill_reference',
      'bill_so_code',
      'bill_shop_id',
      'bill_pos_id',
      'bill_payment',
      'bill_warehouse',
      'bill_status',
      'bill_from_date',
      'bill_to_date',
      'bill_vat_type'
    );

    clear_filter($filter);
  }

}

//--- End class
?>
