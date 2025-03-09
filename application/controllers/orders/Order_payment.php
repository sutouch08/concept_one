<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Order_payment extends PS_Controller
{
  public $menu_code = 'ACPMCF';
	public $menu_group_code = 'AC';
  public $menu_sub_group_code = '';
	public $title = 'ตรวจสอบยอดชำระเงิน';
  public $filter;
	public $wms;
	public $error;

  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'orders/order_payment';
    $this->load->model('orders/order_payment_model');
    $this->load->model('masters/bank_model');
    $this->load->helper('bank');
    $this->load->helper('order');
    $this->load->helper('channels');
  }



  public function index()
  {
    $filter = array(
      'code'  => get_filter('code', 'op_code', ''),
      'dp_code' => get_filter('dp_code', 'op_dp_code', ''),
      'customer' => get_filter('customer', 'op_customer', ''),
      'account' => get_filter('account', 'op_account', ''),
      'user'  => get_filter('user', 'op_user', 'all'),
      'channels' => get_filter('channels', 'op_channels', 'all'),
      'is_export' => get_filter('is_export', 'op_is_export', 'all'),
      'from_date' => get_filter('from_date', 'op_from_date', ''),
      'to_date'  => get_filter('to_date', 'op_to_date', ''),
      'valid' => get_filter('valid', 'op_valid', '0')
    );

		//--- แสดงผลกี่รายการต่อหน้า
		$perpage = get_rows();


		$segment  = 4; //-- url segment
		$rows     = $this->order_payment_model->count_rows($filter);
		//--- ส่งตัวแปรเข้าไป 4 ตัว base_url ,  total_row , perpage = 20, segment = 3
		$init	    = pagination_config($this->home.'/index/', $rows, $perpage, $segment);
		$orders   = $this->order_payment_model->get_list($filter, $perpage, $this->uri->segment($segment));

    $filter['orders'] = $orders;

		$this->pagination->initialize($init);
    $this->load->view('orders/payment/order_payment_list', $filter);
  }




  public function get_payment_detail()
  {
    $sc = TRUE;

    $id = $this->input->post('id');

    $detail = $this->order_payment_model->get_detail($id);

    if( ! empty($detail))
    {
      $img = payment_image_url($detail->order_code);

      $bank   = $this->bank_model->get_account_detail($detail->id_account);

      $ds  = array(
        'id' => $detail->id,
        'order_code' => $detail->order_code,
        'orderAmount' => number($detail->order_amount,2),
        'payAmount' => number($detail->pay_amount,2),
        'payDate' => thai_date($detail->pay_date, TRUE, '/'),
        'bankName' => $bank->bank_name,
        'branch' => $bank->branch,
        'accNo' => $bank->acc_no,
        'accName' => $bank->acc_name,
        'date_add' => thai_date($detail->date_upd, TRUE, '/'),
        'imageUrl' => $img,
        'valid' => $detail->valid
      );
    }
    else
    {
      $sc = FALSE;
      $this->error = "Payment not found !";
    }

    $arr = array(
      'status' => $sc === TRUE ? 'success' : 'failed',
      'message' => $sc === TRUE ? 'success' : $this->error,
      'data' => $sc === TRUE ? $ds : NULL
    );

    echo json_encode($arr);
  }


  public function confirm_payment()
  {
    $sc = TRUE;

    if($this->input->post('id'))
    {
      $this->load->model('orders/orders_model');
      $this->load->model('orders/order_state_model');
      $id = $this->input->post('id');

      $detail = $this->order_payment_model->get_detail($id);
			$order = $this->orders_model->get($detail->order_code);

      $arr = array(
        'order_code' => $detail->order_code,
        'state' => 3,
        'update_user' => $this->_user->uname
      );

      //--- start transection
      $this->db->trans_begin();

      //--- mark payment as paid
      $this->order_payment_model->valid_payment($id);

      //--- mark order as paid
      $this->orders_model->paid($detail->order_code, TRUE);

			if($order->state < 3)
			{
				//--- change state to waiting for prepare
	      $this->orders_model->change_state($detail->order_code, 3);

	      //--- add state event
	      $this->order_state_model->add_state($arr);
			}

      //--- complete transecrtion with commit or rollback if any error
			if($sc === TRUE)
			{
				$this->db->trans_commit();
			}
			else
			{
				$this->db->trans_rollback();
			}

      if($sc === TRUE && is_true(getConfig('AUTO_CREATE_DOWN_PAYMENT')))
      {
        $this->add_down_payment($id);
      }
    }
    else
    {
      $sc = FALSE;
      $this->error = 'ไม่พบรายการชำระเงิน';
    }

    echo $sc === TRUE ? 'success' : $this->error;
  }


  public function create_down_payments()
  {
    $sc = TRUE;
    $this->error = "";
    $list = json_decode($this->input->post('payments'));

    if( ! empty($list))
    {
      foreach($list as $id)
      {
        $pm = $this->order_payment_model->get_by_id($id);

        if( ! empty($pm))
        {
          if($pm->valid == 1)
          {
            if(empty($pm->dp_code))
            {
              if(! $this->add_down_payment($id))
              {
                $sc = FALSE;
                $this->error .= "{$pm->order_code} : สร้างใบรับเงินมัดจำไม่สำเร็จ<br/>";
              }
            }
          }
        }
      }
    }
    else
    {
      $sc = FALSE;
      set_error('required');
    }

    $this->_response($sc);
  }


  public function un_confirm_payment()
  {
    $sc = TRUE;

    if($this->input->post('id'))
    {
      $this->load->model('orders/orders_model');
      $this->load->model('orders/order_state_model');
      $id = $this->input->post('id');
      $pm = $this->order_payment_model->get_detail($id);

      if( ! empty($pm))
      {
        if( ! empty($pm->dp_code))
        {
          $sc = FALSE;
          $this->error = "สลิปใบนี้ถูกดึงไปสร้างเอกสารรับเงินมัดจำเลขที่ {$pm->dp_code} แล้ว<br/>หากต้องการแก้ไขกรุณายกเลิกเอกสารรับเงินมัดจำก่อน";
        }

        if($sc === TRUE)
        {
          $order = $this->orders_model->get($pm->order_code);

          if(empty($order))
          {
            $sc = FALSE;
            $this->error = "ไม่พบออเดอร์";
          }

          if($sc === TRUE)
          {
            $this->db->trans_begin();

            //--- mark payment as unpaid
            if( ! $this->order_payment_model->un_valid_payment($id))
            {
              $sc = FALSE;
              $this->error = "Failed to rollback payment status";
            }

            //--- mark order as unpaid
            if($sc === TRUE)
            {
              if( ! $this->orders_model->update($pm->order_code, array('is_paid' => 0)))
              {
                $sc = FALSE;
                $this->error = "Failed to rollback paid status";
              }
            }

            if($sc === TRUE && $order->state != 8 && $order->state != 9)
            {
              //--- change state to waiting for payment
              if( ! $this->orders_model->change_state($pm->order_code, 2))
              {
                $sc = FALSE;
                $this->error = "Failed to change order state";
              }

              //--- add state event
              if($sc === TRUE)
              {
                $arr = array(
                  'order_code' => $pm->order_code,
                  'state' => 2,
                  'update_user' => $this->_user->uname
                );

                if( ! $this->order_state_model->add_state($arr))
                {
                  $sc = FALSE;
                  $this->error = "Failed to add order state transection";
                }
              } //-- $sc === TRUE
            } //--- $sc === TRUE

            if($sc === TRUE)
            {
              $this->db->trans_commit();
            }
            else
            {
              $this->db->trans_rollback();
            }
          } //-- $sc === TRUE
        } //--- $sc === TRUE
      }
      else
      {
        $sc = FALSE;
        $this->error = "Payment not found!";
      }
    }
    else
    {
      $sc = FALSE;
      set_error('required');
    }

    $this->_response($sc);
  }


  public function remove_payment()
  {
    $sc = TRUE;

    $id = $this->input->post('id');

    if( ! empty($id))
    {
      $this->load->model('orders/orders_model');
      $this->load->model('orders/order_state_model');

      $pm = $this->order_payment_model->get_detail($id);

      if( ! empty($pm))
      {
        if( ! empty($pm->dp_code))
        {
          $sc = FALSE;
          $this->error = "สลิปใบนี้ถูกดึงไปสร้างเอกสารรับเงินมัดจำเลขที่ {$pm->dp_code} แล้ว<br/>หากต้องการแก้ไขกรุณายกเลิกเอกสารรับเงินมัดจำก่อน";
        }

        if($sc === TRUE)
        {
          $order = $this->orders_model->get($pm->order_code);

          if(empty($order))
          {
            $sc = FALSE;
            $this->error = "Order not found!";
          }

          if($sc === TRUE)
          {
            //--- start transection
            $this->db->trans_begin();

            //--- mark order as unpaid
            if( ! $this->orders_model->update($pm->order_code, array('is_paid' => 0)))
            {
              $sc = FALSE;
              $this->error = "Failed to rollback paid status";
            }

    				if($sc === TRUE && $order->state != 8 && $order->state != 9)
    				{
    	        //--- change state to pending
    	        if( ! $this->orders_model->change_state($pm->order_code, 1))
              {
                $sc = FALSE;
                $this->error = "Failed to change order state";
              }
              else
              {
                //--- add state event
                $arr = array(
                  'order_code' => $pm->order_code,
                  'state' => 1,
                  'update_user' => get_cookie('uname')
                );

                $this->order_state_model->add_state($arr);
              }
    				}

            if($sc === TRUE)
            {
              //--- now remove payment row
              if( ! $this->order_payment_model->delete($id))
              {
                $sc = FALSE;
                $this->error = "Failed to delete payment";
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
        }
      }
      else
      {
        $sc = FALSE;
        $this->error = "Payment not found!";
      }
    }
    else
    {
      $sc = FALSE;
      set_error('required');
    }

    $this->_response($sc);
  }


  public function add_down_payment($id)
  {
    $this->load->model('orders/orders_model');
    $this->load->model('orders/order_down_payment_model');
    $this->load->model('orders/order_pos_payment_model');
    $this->load->model('masters/payment_methods_model');
    $this->load->helper('image');

    $sc = TRUE;

    $ds = $this->order_payment_model->get_by_id($id);

    if( ! empty($ds))
    {
      $so = $this->orders_model->get($ds->order_code);

      if( ! empty($so))
      {
        $code = $this->get_new_down_payment_code();

        if( ! empty($code))
        {
          $this->db->trans_begin();

          $vat_rate = empty($so->vat_rate) ? getConfig('SALE_VAT_RATE') : $so->vat_rate;
          $payment_code = empty($so->payment_code) ? getConfig('DEFAULT_TRANSFER_PAYMENT') : $so->payment_code;
          $row = array(
            'code' => $code,
            'customer_code' => $so->customer_code,
            'customer_name' => $so->customer_name,
            'customer_ref' => $so->customer_ref,
            'customer_phone' => $so->phone,
            'sale_id' => $so->sale_code,
            'reference' => $so->code,
            'ref_type' => 'WO',
            'ref_code' => NULL,
            'amount' => $ds->pay_amount,
            'used' => 0.00,
            'available' => $ds->pay_amount,
            'status' => 'O',
            'TaxStatus' => 'N',
            'vat_rate' => $vat_rate,
            'VatSum' => get_vat_amount($ds->pay_amount, $vat_rate),
            'isCompany' => $so->isCompany,
            'tax_id' => $so->tax_id,
            'branch_code' => $so->branch_code,
            'branch_name' => $so->branch_name,
            'address' => $so->address,
            'sub_district' => $so->sub_district,
            'district' => $so->district,
            'province' => $so->province,
            'postcode' => $so->postcode,
            'user' => $ds->user,
            'payment_code' => $payment_code,
            'payment_role' => 2,
            'receiveAmount' => $ds->pay_amount,
            'changeAmount' => 0.00,
            'acc_id' => $ds->id_account,
            'image_path' => get_image_url($ds->order_code)
          );

          if( ! $this->order_down_payment_model->add($row))
          {
            $sc = FALSE;
            $this->error = "Failed to create document";
          }

          //---- insert order_pos_payment
          if($sc === TRUE)
          {
            $payments = array(
              "code" => $code,
              "payment_code" => $payment_code,
              "payment_role" => 2,
              "role_name" => "เงินโอน",
              "amount" => $ds->pay_amount,
              "acc_id" => $ds->id_account,
              "uname" => $this->_user->uname,
              "payment_date" => $ds->pay_date,
              "ref_type" => 'DP' //--- DP = down payment, SP = sales payment
            );

            if( ! $this->order_pos_payment_model->add($payments))
            {
              $sc = FALSE;
              $this->error = "Failed to insert order payments details";
            }
          }

          //-- update sales order deposit and balance
          if($sc === TRUE)
          {
            $paidAmount = $so->paidAmount + $ds->pay_amount;
            $balance = $so->doc_total - $paidAmount;
            $arr = array(
              'paidAmount' => $paidAmount,
              'TotalBalance' => $balance < 0 ? 0 : $balance
            );

            if( ! $this->orders_model->update($so->code, $arr))
            {
              $sc = FALSE;
              $this->error = "Failed to update Order outstanding balance";
            }
          }

          //--- update dpCode in order payment
          if($sc === TRUE)
          {
            $arr = array(
              'dp_code' => $code
            );

            if( ! $this->order_payment_model->update($id, $arr))
            {
              $sc = FALSE;
              $this->error = "Failed to updtae Down payment code";
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

          if($sc === TRUE && is_true(getConfig('AUTO_EXPORT_INCOMMING')))
          {
            $this->load->library('export');
            $this->export->export_incomming($code);
          }
        }
        else
        {
          $sc = FALSE;
          $this->error = "Failed to generate document number";
        }
      }
      else
      {
        $sc = FALSE;
        $this->error = "ไม่พบเอกสารใบสั่งขาย";
      }
    }
    else
    {
      $sc = FALSE;
      set_error('required');
    }

    return $sc;
  }


  public function get_new_down_payment_code($date = NULL)
  {
    $prefix = getConfig('PREFIX_DOWN_PAYMENT');
    $run_digit = getConfig('RUN_DIGIT_DOWN_PAYMENT');

    $date = empty($date) ? date('Y-m-d') : ($date < '2020-01-01' ? date('Y-m-d') : $date);
    $Y = date('y', strtotime($date));
    $M = date('m', strtotime($date));
    $pre = $prefix .'-'.$Y.$M;
    $code = $this->order_down_payment_model->get_max_code($pre);

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


  public function clear_filter()
  {
    $filter = array(
      'op_code',
      'op_account',
      'op_user',
      'op_channels',
      'op_from_date',
      'op_to_date',
      'op_customer',
      'op_valid',
      'op_dp_code',
      'op_is_export'
    );

    clear_filter($filter);
  }
} //--- end class

?>
