<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Order_pos extends PS_Controller
{
  public $menu_code = 'SOOPOS';
	public $menu_group_code = 'POS';
  public $menu_sub_group_code = '';
	public $title = 'ขายหน้าร้าน';
  public $segment = 5;

  public function __construct()
  {
    parent::__construct();
		$this->home = base_url().'orders/order_pos';
		$this->load->model('masters/shop_model');
		$this->load->model('masters/pos_model');
    $this->load->model('masters/bank_model');
		$this->load->model('orders/order_pos_model');
    $this->load->model('orders/pos_sales_movement_model');
    $this->load->model('orders/order_down_payment_model');
    $this->load->model('orders/order_pos_payment_model');
		$this->load->model('orders/discount_model');
    $this->load->helper('bank');
		$this->load->helper('discount');
    $this->load->helper('order_pos');
  }


  public function index()
  {
    $this->load->view('order_pos/pos_list');
  }


  public function set_pc()
  {
    $this->load->model('masters/shop_pc_model');

    $sc = TRUE;

    $temp_id = $this->input->post('temp_id');
    $code = trim($this->input->post('pc_code'));

    $pc = $this->shop_pc_model->get_by_code($code);

    if( ! empty($pc))
    {
      if( ! $this->order_pos_model->update_temp($temp_id, array('pc_id' => $pc->id)))
      {
        $sc = FALSE;
        $this->error = "Failed to update Order PC ID";
      }
    }
    else
    {
      $sc = FALSE;
      $this->error = "ไม่พบรหัสพนักงาน";
    }

    $arr = array(
      'status' => $sc === TRUE ? 'success' : 'failed',
      'message' => $sc === TRUE ? 'success' : $this->error,
      'pc' => $sc === TRUE ? $pc : NULL
    );

    echo json_encode($arr);
  }


  public function down_payment_list($shop_id, $device_id)
  {
    $home = $this->home.'/down_payment_list/'.$shop_id.'/'.$device_id;
    $this->load->helper('shop');
    $this->title = "รับเงินมัดจำ";

    $filter = array(
      'shop_id' => $shop_id,
      'code' => get_filter('code', 'down_code', ''),
      'reference' => get_filter('reference', 'down_reference', ''),
      'ref_code' => get_filter('ref_code', 'down_ref_code', ''),
      'status' => get_filter('status', 'down_status', 'all'),
      'from_date' => get_filter('from_date', 'down_from_date', ''),
      'to_date' => get_filter('to_date', 'down_to_date', '')
    );

    if($this->input->post('search'))
    {
      redirect($this->home.'/down_payment_list/'.$shop_id.'/'.$device_id);
    }
    else
    {
      $this->segment = 6;
      $perpage = get_rows();
      $rows = $this->order_down_payment_model->count_rows($filter);
      $filter['orders'] = $this->order_down_payment_model->get_list($filter, $perpage, $this->uri->segment($this->segment));
      $filter['pos'] = $this->pos_model->get_pos_by_device_id($device_id);
      $init = pagination_config($home, $rows, $perpage, $this->segment);
      $this->pagination->initialize($init);
      $this->load->view('order_pos/down_payment_list', $filter);
    }
  }


  public function add_down_payment()
  {
    $this->load->model('orders/sales_order_model');
    $this->load->model('orders/orders_model');

    $sc = TRUE;
    $ds = json_decode($this->input->post('data'));

    if( ! empty($ds))
    {
      $paymentDate = db_date($ds->paymentDate, FALSE);

      $pos = $this->pos_model->get_pos($ds->pos_id);

      if($ds->doc_type == 'WO')
      {
        $so = $this->orders_model->get($ds->so_code);
      }
      else
      {
        $so = $this->sales_order_model->get($ds->so_code);
      }


      if( ! empty($so))
      {
        $code = $this->get_new_down_payment_code();

        if(! empty($code))
        {
          $this->db->trans_begin();

          $vat_rate = empty($so->vat_rate) ? getConfig('SALE_VAT_RATE') : $so->vat_rate;

          $row = array(
            'code' => $code,
            'customer_code' => $so->customer_code,
            'customer_name' => $so->customer_name,
            'customer_ref' => $so->customer_ref,
            'customer_phone' => $so->phone,
            'sale_id' => $ds->doc_type == 'WO' ? $so->sale_code : $so->sale_id,
            'reference' => $so->code,
            'ref_type' => $ds->doc_type,
            'ref_code' => NULL,
            'amount' => $ds->amount,
            'used' => 0.00,
            'available' => $ds->amount,
            'pos_id' => $ds->pos_id,
            'shop_id' => $ds->shop_id,
            'status' => 'O',
            'TaxStatus' => 'N',
            'vat_rate' => $vat_rate,
            'VatSum' => get_vat_amount($ds->amount, $vat_rate),
            'isCompany' => $so->isCompany,
            'tax_id' => $so->tax_id,
            'branch_code' => $so->branch_code,
            'branch_name' => $so->branch_name,
            'address' => $so->address,
            'sub_district' => $so->sub_district,
            'district' => $so->district,
            'province' => $so->province,
            'postcode' => $so->postcode,
            'user' => $this->_user->uname,
            'payment_code' => $ds->payment_code,
            'payment_role' => $ds->payment_role,
            'receiveAmount' => $ds->receive,
            'changeAmount' => $ds->change,
            'acc_id' => empty($ds->acc_id) ? $pos->account_id : $ds->acc_id,
            'round_id' => $pos->round_id
          );

          if( ! $this->order_down_payment_model->add($row))
          {
            $sc = FALSE;
            $this->error = "Failed to create document";
          }

          //-- if paymet by cash , update cash amount in cash drawer
          if($sc === TRUE && ($ds->payment_role == 1 OR $ds->payment_role == 6) && $ds->cashAmount > 0)
          {
            $cash_amount = $pos->cash_amount + $ds->cashAmount;

            $arr = array('cash_amount' => $cash_amount);

            if( ! $this->pos_model->update_by_id($ds->pos_id, $arr))
            {
              $sc = FALSE;
              $this->error = "บันทึกยอดเงินเข้าลิ้นชักไม่สำเร็จ";
            }
          }

          //--- add sales movement
          if($sc === TRUE)
          {
            if($ds->cashAmount > 0)
            {
              $arr = array(
                'code' => $code,
                'type' => 'DP',     //--- S = Sales, C = Cancel, R = Return, CR = Cancel return, CI = Cash In, CO = Cash out, DP = Down Payment
                'shop_id' => $ds->shop_id,
                'pos_id' => $ds->pos_id,
                'amount' => $ds->cashAmount,
                'payment_role' => 1,
                'acc_id' => NULL,
                'user' => $this->_user->uname,
                'round_id' => $pos->round_id
              );

              if( ! $this->pos_sales_movement_model->add($arr))
              {
                $sc = FALSE;
                $this->error = "Insert sales movement failed";
              }
            }

            if($ds->transferAmount > 0)
            {
              $arr = array(
                'code' => $code,
                'type' => 'DP',     //--- S = Sales, C = Cancel, R = Return, CR = Cancel return, CI = Cash In, CO = Cash out, DP = Down Payment
                'shop_id' => $ds->shop_id,
                'pos_id' => $ds->pos_id,
                'amount' => $ds->transferAmount,
                'payment_role' => 2,
                'acc_id' => empty($ds->acc_id) ? $pos->account_id : $ds->acc_id,
                'user' => $this->_user->uname,
                'round_id' => $pos->round_id
              );

              if( ! $this->pos_sales_movement_model->add($arr))
              {
                $sc = FALSE;
                $this->error = "Insert sales movement failed";
              }
            }

            if($ds->cardAmount > 0)
            {
              $arr = array(
                'code' => $code,
                'type' => 'DP',     //--- S = Sales, C = Cancel, R = Return, CR = Cancel return, CI = Cash In, CO = Cash out, DP = Down Payment
                'shop_id' => $ds->shop_id,
                'pos_id' => $ds->pos_id,
                'amount' => $ds->cardAmount,
                'payment_role' => 3,
                'acc_id' => NULL,
                'user' => $this->_user->uname,
                'round_id' => $pos->round_id
              );

              if( ! $this->pos_sales_movement_model->add($arr))
              {
                $sc = FALSE;
                $this->error = "Insert sales movement failed";
              }
            }

            if($ds->chequeAmount > 0)
            {
              $arr = array(
                'code' => $code,
                'type' => 'DP',     //--- S = Sales, C = Cancel, R = Return, CR = Cancel return, CI = Cash In, CO = Cash out, DP = Down Payment
                'shop_id' => $ds->shop_id,
                'pos_id' => $ds->pos_id,
                'amount' => $ds->chequeAmount,
                'payment_role' => 7,
                'acc_id' => NULL,
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

          //---- insert order_pos_payment
          if($sc === TRUE)
          {
            $payments = [];

            if($ds->cashAmount > 0)
            {
              $payments[] = [
                "code" => $code,
                "payment_code" => $pos->cash_payment,
                "payment_role" => 1,
                "role_name" => "เงินสด",
                "amount" => $ds->cashAmount,
                "uname" => $this->_user->uname,
                "payment_date" => $paymentDate,
                "shop_id" => $ds->shop_id,
                "pos_id" => $ds->pos_id,
                "ref_type" => 'DP' //--- DP = down payment, SP = sales payment
              ];
            }

            if($ds->transferAmount > 0)
            {
              $payments[] = [
                "code" => $code,
                "payment_code" => $pos->transfer_payment,
                "payment_role" => 2,
                "role_name" => "เงินโอน",
                "amount" => $ds->transferAmount,
                "acc_id" => empty($ds->acc_id) ? $pos->account_id : $ds->acc_id,
                "uname" => $this->_user->uname,
                "payment_date" => $paymentDate,
                "shop_id" => $ds->shop_id,
                "pos_id" => $ds->pos_id,
                "ref_type" => 'DP' //--- DP = down payment, SP = sales payment
              ];
            }

            if($ds->cardAmount > 0)
            {
              $payments[] = [
                "code" => $code,
                "payment_code" => $pos->card_payment,
                "payment_role" => 3,
                "role_name" => "บัตรเครดิต",
                "amount" => $ds->cardAmount,
                "uname" => $this->_user->uname,
                "payment_date" => $paymentDate,
                "shop_id" => $ds->shop_id,
                "pos_id" => $ds->pos_id,
                "ref_type" => 'DP' //--- DP = down payment, SP = sales payment
              ];
            }

            if($ds->chequeAmount > 0)
            {
              $payments[] = [
                "code" => $code,
                "payment_code" => "CHEQUE",
                "payment_role" => 7,
                "role_name" => "เช็ค",
                "amount" => $ds->chequeAmount,
                "uname" => $this->_user->uname,
                "payment_date" => $paymentDate,
                "shop_id" => $ds->shop_id,
                "pos_id" => $ds->pos_id,
                "ref_type" => 'DP' //--- DP = down payment, SP = sales payment
              ];
            }

            if( ! empty($payments))
            {
              foreach($payments as $arr)
              {
                if( ! $this->order_pos_model->add_order_payment($arr))
                {
                  $sc = FALSE;
                  $this->error = "Failed to insert order payments details";
                }
              }
            }
          }

          //-- update sales order deposit and balance
          if($sc === TRUE)
          {
            if($ds->doc_type == 'WO')
            {
              $paidAmount = $so->paidAmount + $ds->amount;
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
            else
            {
              $paidAmount = $so->paidAmount + $ds->amount;
              $balance = $so->DocTotal - $paidAmount;

              $arr = array(
                'paidAmount' => $paidAmount,
                'TotalBalance' => $balance < 0 ? 0 : $balance
              );

              if( ! $this->sales_order_model->update($so->code, $arr))
              {
                $sc = FALSE;
                $this->error = "Failed to update Sales order outstanding balance";
              }
            }

          }

          if($sc === TRUE)
          {
            $row['date_add'] = date('d-m-Y');
            $row['amount'] = number($row['amount'], 2);
            $row['status_label'] = bill_status_label('O');
            $row['remark'] = $row['customer_ref'] . (empty($row['customer_phone']) ? "" : " : ").$row['customer_phone'];
          }

          if($sc === TRUE)
          {
            $this->db->trans_commit();
          }
          else
          {
            $this->db->trans_rollback();
          }

          if($sc === TRUE && is_true('AUTO_EXPORT_INCOMMING'))
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

    $arr = array(
      'status' => $sc === TRUE ? 'success' : 'failed',
      'message' => $sc === TRUE ? 'success' : $this->error,
      'data' => $sc === TRUE ? (object) $row : NULL
    );

    echo json_encode($arr);
  }


  public function bill_list($shop_id)
  {
    $this->load->helper('shop');
    $this->load->helper('channels');
    $this->load->helper('saleman');
    $shop = $this->shop_model->get($shop_id);
    $this->title = "บิลขาย : ".$shop->code." : ".$shop->name;

    $filter = array(
      'shop_id' => $shop_id,
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
      'sale_id' => get_filter('sale_id', 'bill_sale_id', 'all')
    );

    if($this->input->post('search'))
    {
      redirect($this->home.'/bill_list/'.$shop_id);
    }
    else
    {
      $perpage = get_rows();
      $rows = $this->order_pos_model->count_rows($filter);

      $filter['orders'] = $this->order_pos_model->get_list($filter, $perpage, $this->uri->segment($this->segment));
      $init = pagination_config($this->home.'/bill_list/'.$shop_id.'/', $rows, $perpage, $this->segment);
      $this->pagination->initialize($init);
      $this->load->view('order_pos/bill_list', $filter);
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

      $this->load->view('order_pos/bill_detail', $ds);
    }
    else
    {
      $this->page_eror();
    }
  }


  public function get_active_pos_list()
  {
    $sc = TRUE;
    $posList = $this->pos_model->get_active_pos_list();

    if(empty($posList))
    {
      $sc = FALSE;
    }

    $arr = array(
      'status' => $sc === TRUE ? 'success' : 'failed',
      'message' => $sc === TRUE ? 'success' : 'ไม่พบเครื่อง POS กรุณาตรวจสอบฐานข้อมูล',
      'data' => $posList
    );

    echo json_encode($arr);
  }


	public function main($device_id)
	{
    $pos = $this->pos_model->get_pos_by_device_id($device_id);

    if( ! empty($pos))
    {
      $valid = $this->shop_model->validate_shop_user($pos->shop_id, $this->_user->uname);

      if( ! $valid)
      {
        redirect(base_url().'users/authentication/pos_login');
      }
      else
      {
        $this->load->model('masters/payment_methods_model');
        $this->load->helper('payment_method');
        $pos->account_id = NULL;

        if( ! empty($pos->transfer_payment))
        {
          $transfer_payment = $this->payment_methods_model->get($pos->transfer_payment);

          if( ! empty($transfer_payment))
          {
            $pos->account_id = $transfer_payment->account_id;
          }
        }


        $order = $this->order_pos_model->get_active_temp($pos->id);

        if(empty($order))
        {
          //--- create new order_temp
          $arr = array(
            'shop_id' => $pos->shop_id,
            'pos_id' => $pos->id,
            'payment_code' => $pos->cash_payment,
            'vat_rate' => $pos->use_vat ? getConfig('SALE_VAT_RATE') : 0,
            'uname' => $this->_user->uname
          );

          $id = $this->order_pos_model->add_temp($arr);

          if( ! $id)
          {
            $sc = FALSE;
            $this->error = "สร้างเอกสารไม่สำเร็จ";
          }
          else
          {
            $order = $this->order_pos_model->get_temp($id);
          }
        }

        $details = $this->order_pos_model->get_temp_details($order->id);
        $pos->order_code = $this->get_new_code($pos->prefix, $pos->running);
        $billDiscPrcnt = $order->bill_disc_percent;
        $totalQty = 0;
        $totalBfDisc = 0;
        $billDiscAmount = 0;
        $totalTaxAmount = 0;
        $amountAfterDisc = 0;
        $amountAfterDiscAndTax = 0;
        $docTotal = 0;
        $WhtAmount = 0;
        $vat_type = $order->vat_type;

        if( ! empty($details))
        {
          foreach($details as $rs)
          {
            if($rs->qty > 0 && $rs->price > 0)
        		{
        			$totalBfDisc += $rs->total_amount;
              $billDiscAmount += $rs->total_amount * $rs->avgBillDiscAmount;
              $totalQty += $rs->qty;
        		}
          }

          //--- update bill discount
          $billDiscPercent = $order->bill_disc_percent;
          $billDiscAmount = $billDiscPrcnt > 0 ? round($totalBfDisc * ($billDiscPrcnt * 0.01), 2) : round($billDiscAmount, 2);

        	//---- bill discount amount
        	$amountAfterDisc = $totalBfDisc - $billDiscAmount;  //--- มูลค่าสินค้า หลังหักส่วนลดท้ายบิล
        	// $amountBeforeDiscWithTax = $totalTaxAmount;   //-- มูลค่าสินค้า เฉพาะที่มีภาษี
        	//--- คำนวนภาษี หากมีส่วนลดท้ายบิล
        	//--- เฉลี่ยส่วนลดออกให้ทุกรายการ โดยเอาส่วนลดท้ายบิล(จำนวนเงิน)/มูลค่าสินค้าก่อนส่วนลด
        	//--- ได้มูลค่าส่วนลดท้ายบิลที่เฉลี่ยนแล้ว ต่อ บาท เช่น หารกันมาแล้ว ได้ 0.16 หมายถึงทุกๆ 1 บาท จะลดราคา 0.16 บาท
        	$avgBillDiscAmount = $totalBfDisc > 0 ? round($billDiscAmount/$totalBfDisc, 6) : 0;

          //--- คำนวนภาษี
        	//--- นำผลลัพธ์ข้างบนมาคูณ กับ มูลค่าที่ต้องคิดภาษี (ตัวที่ไม่มีภาษีไม่เอามาคำนวณ)
        	//--- จะได้มูลค่าส่วนลดที่ต้องไปลบออกจากมูลค่าสินค้าที่ต้องคิดภาษ
          foreach($details as $rs)
          {
            if($rs->qty > 0 && $rs->price > 0)
            {
              if($rs->vat_rate > 0)
              {
                $sumBillDiscAmount = round($rs->total_amount * $rs->avgBillDiscAmount, 6);
                $amountAfDisc = $rs->total_amount - $sumBillDiscAmount;
                $totalTaxAmount += $amountAfDisc > 0 ? ($vat_type == 'E' ? ($amountAfDisc * ($rs->vat_rate * 0.01)) : ($amountAfDisc * $rs->vat_rate)/(100 + $rs->vat_rate)) : 0;
              }
            }
          }

          $totalTaxAmount = round($totalTaxAmount, 2);
          $WhtAmount = $order->WhtPrcnt == 0 ? 0 : ($vat_type == 'E' ? $amountAfterDisc * ($order->WhtPrcnt * 0.01) :($amountAfterDisc - $totalTaxAmount) * ($order->WhtPrcnt * 0.01));
          $amountAfterDiscAndTax = $vat_type == 'E' ? round($amountAfterDisc + $totalTaxAmount) : $amountAfterDisc;
          $docTotal = $vat_type == 'E' ? round($amountAfterDisc + $totalTaxAmount) : $amountAfterDisc;
        }

        $dpList = [];
        $dpAvailable = 0;
        $dpUse = 0;

        //--- downpayment
        $dps = empty($order->so_code) ? NULL : $this->order_down_payment_model->get_by_reference($order->so_code);

        if( ! empty($dps))
        {
          $dp_no = 1;

          $dpTotal = $docTotal;

          foreach($dps as $dp)
          {
            $dp->no = $dp_no;
            $dp->amount_label = number($dp->amount, 2);
            $dp->used_amount = $dp->used;
            $dp->used_label = number($dp->used, 2);
            $dp->available_label = number($dp->available, 2);
            $dp->payment_role_name = $dp->payment_role == 1 ? 'เงินสด' : ($dp->payment_role == 2 ? 'เงินโอน' : ($dp->payment_role == 3 ? 'บัตรเครดิต' : 'หลายช่องทาง'));

            $use_amount = $dp->available > 0 ? round($dp->available <= $dpTotal ? $dp->available : $dpTotal, 2) : 0;
            $dp->use_amount = $use_amount;
            $dp->disabled = $dp->available > 0 ? '' : 'disabled';

            array_push($dpList, $dp);
            $dp_no++;
            $dpTotal = $dpTotal - $use_amount;
            $dpUse += $use_amount;
            $dpAvailable += $dp->available;
          }
        }
         //---- end of down payment

        $ds = array(
          'pos' => $pos,
          'order' => $order,
          'details' => $details,
          'holdBillCount' => $this->order_pos_model->count_hold_bills($pos->id),
          'totalQty' => $totalQty,
          'totalBfDisc' => $totalBfDisc,
          'vatSum' => $totalTaxAmount,
          'amountAfterDisc' => $amountAfterDisc,
          'amountAfterDiscAndTax' => $amountAfterDiscAndTax,
          'downPayment' => round($dpUse, 2),
          'downPaymentList' => $dpList,
          'docTotal' => $docTotal - $dpUse - $WhtAmount,
        );

        $this->load->view('order_pos/pos', $ds);
      }
    }
    else
    {
      $this->error_page();
    }
	}

  public function return_list($shop_id, $device_id)
  {
    $this->load->helper('shop');
    $this->load->model('orders/order_pos_return_model');

    $this->title = "รับคืนสินค้า";

    $filter = array(
      'shop_id' => $shop_id,
      'code' => get_filter('code', 'return_code', ''),
      'order_code' => get_filter('order_code', 'return_order_code', ''),
      'ref_code' => get_filter('ref_code', 'return_ref_code', ''),
      'status' => get_filter('status', 'return_status', 'all'),
      'from_date' => get_filter('from_date', 'return_from_date', ''),
      'to_date' => get_filter('to_date', 'return_to_date', '')
    );

    if($this->input->post('search'))
    {
      redirect($this->home.'/return_list/'.$shop_id.'/'.$device_id);
    }
    else
    {
      $this->segment = 6;
      $perpage = get_rows();
      $rows = $this->order_pos_return_model->count_rows($filter);
      $filter['orders'] = $this->order_pos_return_model->get_list($filter, $perpage, $this->uri->segment($this->segment));
      $filter['pos'] = $this->pos_model->get_pos_by_device_id($device_id);
      $init = pagination_config($this->home.'/bill_list/', $rows, $perpage, $this->segment);
      $this->pagination->initialize($init);
      $this->load->view('order_pos/return_list', $filter);
    }
  }


  public function return_bill($id)
  {
    $this->title = "รับคืนสินค้า";
    $this->load->model('orders/order_pos_return_model');

    $order = $this->order_pos_model->get_by_id($id);

    if( ! empty($order))
    {
      $details = $this->order_pos_model->get_details_by_id($id);

      $bcList = array();

      if( ! empty($details))
      {
        foreach($details as $rs)
        {
          if( ! empty($rs->barcode))
          {
            $code = md5($rs->barcode);

            if(empty($bcList[$rs->barcode]))
            {
              $arr = array(
                'code' => $code,
                'barcode' => $rs->barcode,
                'id' => $rs->id,
                'pdCode' => $rs->product_code
              );

              $bcList[$rs->barcode] = (object) $arr;

              $rs->bc_code = $code;
            }
            else
            {
              $rs->bc_code = $code;
            }
          }
        }
      }

      $ds = array(
        'order' => $order,
        'details' => $details,
        'pos' => $this->pos_model->get_pos($order->pos_id),
        'bcList' => $bcList
      );

      $this->load->view('order_pos/return_add', $ds);
    }
    else
    {
      $this->error_page();
    }
  }


  public function add_return()
  {
    $this->load->model('orders/order_pos_return_model');

    $sc = TRUE;

    $data = json_decode($this->input->post('data'));

    if( ! empty($data))
    {
      $order = $this->order_pos_model->get_by_id($data->bill_id);

      if( ! empty($order))
      {
        if($order->status != 'D')
        {
          if( ! empty($data->items))
          {
            //--- create new return code
            $pos = $this->pos_model->get_pos($order->pos_id);

            if( ! empty($pos))
            {
              $code = $this->get_new_return_code($pos->return_prefix, $pos->return_running);

              $arr = array(
                'code' => $code,
                'order_code' => $order->code,
                'customer_code' => $order->customer_code,
                'customer_name' => $order->customer_name,
                'shop_id' => $order->shop_id,
                'pos_id' => $order->pos_id,
                'pos_no' => $pos->pos_no,
                'payment_role' => $data->payment_role,
                'acc_id' => $pos->account_id,
                'uname' => $this->_user->uname,
                'approver' => $data->approver,
                'approve_date' => now(),
                'remark' => get_null(trim($data->remark)),
                'warehouse_code' => $order->warehouse_code,
                'zone_code' => $order->zone_code,
                'round_id' => $pos->round_id
              );

              $total_amount = 0;
              $total_qty = 0;
              $total_vat = 0;

              $this->db->trans_begin();

              $return_id = $this->order_pos_return_model->add($arr);

              if($return_id)
              {
                foreach($data->items as $item)
                {
                  if($sc === FALSE)
                  {
                    break;
                  }

                  $rs = $this->order_pos_model->get_detail($item->id);

                  if( ! empty($rs))
                  {
                    $return_qty = $rs->return_qty + $item->qty;

                    if($return_qty <= $rs->qty)
                    {
                      $amount = $rs->final_price * $item->qty;
                      $vat_amount = $amount * ($rs->vat_rate * 0.01);

                      $arr = array(
                        'return_id' => $return_id,
                        'return_code' => $code,
                        'order_id' => $order->id,
                        'order_code' => $order->code,
                        'order_line_id' => $rs->id,
                        'product_code' => $rs->product_code,
                        'product_name' => $rs->product_name,
                        'style_code' => $rs->style_code,
                        'unit_code' => $rs->unit_code,
                        'order_qty' => $rs->qty,
                        'cost' => $rs->cost,
                        'std_price' => $rs->std_price,
                        'price' => $rs->price,
                        'discount_label' => $rs->discount_label,
                        'discount_amount' => $rs->price - $rs->final_price,
                        'final_price' => $rs->final_price,
                        'return_qty' => $item->qty,
                        'total_amount' => $amount,
                        'vat_code' => $rs->vat_code,
                        'vat_rate' => $rs->vat_rate,
                        'vat_amount' => $vat_amount,
                        'is_count' => $rs->is_count
                      );

                      if( ! $this->order_pos_return_model->add_detail($arr))
                      {
                        $sc = FALSE;
                        $this->error = "เพิ่มรายการรับคืนไม่สำเร็จ";
                      }
                      else
                      {
                        $total_qty += $item->qty;
                        $total_amount += $amount;
                        $total_vat += $vat_amount;

                        //-- update return qty inp bill
                        $arr = array('return_qty' => $return_qty);

                        if( ! $this->order_pos_model->update_detail($rs->id, $arr))
                        {
                          $sc = FALSE;
                          $this->error = "แก้ไขจำนวนคืนแล้วในบิลไม่สำเร็จ";
                        }
                      }
                    }
                    else
                    {
                      $sc = FALSE;
                      $this->error = "รับค้นไม่สำเร็จ เนื่องจาก จำนวนรับคืนรวมเกินจำนวนขาย";
                    }
                  }
                  else
                  {
                    $sc = FALSE;
                    $this->error = "ไม่พบรายการขาย";
                  }
                } //--- end foreach

                if($sc === TRUE)
                {
                  //--- update total amoun , total qty ,total vat in order_pos_return
                  $arr = array(
                    'qty' => $total_qty,
                    'amount' => $total_amount,
                    'vat_amount' => $total_vat
                  );

                  if( ! $this->order_pos_return_model->update($return_id, $arr))
                  {
                    $sc = FALSE;
                    $this->error = "Upate return summary failed";
                  }

                  //--- add sales movement
                  if($sc === TRUE)
                  {
                    $arr = array(
                      'code' => $code,
                      'type' => 'R',     //--- S = sales , C = Cancel, R = Return
                      'shop_id' => $order->shop_id,
                      'pos_id' => $order->pos_id,
                      'amount' => $total_amount * (-1),
                      'payment_role' => $data->payment_role,
                      'acc_id' => $data->payment_role == 2 ? $pos->account_id : NULL,
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
              else
              {
                $sc = FALSE;
                $this->error = "สร้างเอกสารรับคืนไม่สำเร็จ";
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
              $this->error = "ไม่พบเครื่อง POS";
            }
          }
          else
          {
            $sc = FALSE;
            $this->error = "ไม่พบรายการรับคืน";
          }
        }
        else
        {
          $sc = FALSE;
          $this->error = "ไม่สามารถรับคืนได้เนื่องจากบิลขายถูกยกเลิกไปแล้ว";
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
      'return_id' => $sc === TRUE ? $return_id : NULL
    );

    echo json_encode($arr);
  }


  public function return_detail($return_id)
  {
    $this->load->model('orders/order_pos_return_model');

    $doc = $this->order_pos_return_model->get_by_id($return_id);

    if( ! empty($doc))
    {
      $ds = array(
        'doc' => $doc,
        'details' => $this->order_pos_return_model->get_details_by_id($return_id)
      );

      $this->load->view('order_pos/return_detail', $ds);
    }
    else
    {
      $this->error_page();
    }
  }


  public function get_return_view()
  {
    $this->load->model('orders/order_pos_return_model');
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


  public function add_to_temp()
	{
		$sc = TRUE;
    $ds = array();

		$this->load->model('masters/products_model');
		$this->load->model('orders/promotion_model');

    $pos_id = $this->input->post('pos_id');
    $temp_id = $this->input->post('order_temp_id');
    $barcode = trim($this->input->post('barcode'));
    $customer_code = $this->input->post('customer_code');
    $channels_code = $this->input->post('channels_code');
    $payment_code = $this->input->post('payment_code');
    $is_free = $this->input->post('is_free');

    $qty = $this->input->post('qty') <= 0 ? 1 : $this->input->post('qty');

    if( ! empty($temp_id) && ! empty($barcode))
    {
      $pos = $this->pos_model->get_pos($pos_id);

      if( ! empty($pos))
      {
        $item = $this->products_model->get_product_by_barcode($barcode); //--- barcode

        if(empty($item))
        {
          $item = $this->products_model->get($barcode); //-- get_by_code
        }

        if( ! empty($item))
        {
          $detail = $this->order_pos_model->get_temp_detail_by_item_and_price($temp_id, $item->code, $item->price, $is_free);

          if( ! empty($detail) && empty($detail->line_id) && empty($detail->is_edit))
          {
            //---- update detail
  					$Qty = $detail->qty + $qty;

            if( ! $is_free)
            {
              $discount = $this->promotion_model->get_item_discount($item->code, $customer_code, $Qty, $payment_code, $channels_code, date('Y-m-d'), $temp_id);
              $discount_label = discountLabel($discount['discLabel1'], $discount['discLabel2'], $discount['discLabel3']);
              $item_disc_amount = empty($discount['amount']) ? 0 : round($discount['amount'] / $Qty, 2);
              $sell_price = $item->price - $item_disc_amount;
              $total_amount = round($sell_price * $Qty, 2);
            }

            $ds = array(
              'qty' => $Qty,
              'discount_label' => $is_free ? '100%' : $discount_label,
              'discount_amount' => $is_free ? ($item->price * $Qty) : $discount['amount'],
              'final_price' => $is_free ? 0.00 : $sell_price,
              'total_amount' => $is_free ? 0.00 : $total_amount,
              'vat_amount' => $is_free ? 0.00 : get_vat_amount($total_amount, $detail->vat_rate, 'I'),
              'id_rule' => $is_free ? NULL : $discount['id_rule']
            );

            if($this->order_pos_model->update_temp_detail($detail->id, $ds))
  					{
              $ds['id'] = $detail->id;
              $ds['item_type'] = $detail->item_type;
              $ds['product_code'] = $detail->product_code;
              $ds['product_name'] = $detail->product_name;
              $ds['unit_code'] = $detail->unit_code;
              $ds['std_price'] = $detail->std_price;
              $ds['price'] = $detail->price;
              $ds['vat_rate'] = $detail->vat_rate;
              $ds['is_count'] = $detail->is_count;
              $ds['qty_label'] = number($Qty, 2);
              $ds['price_label'] = number($item->price, 2);
              $ds['total_amount_label'] = $is_free ? 0.00 : number($total_amount, 2);
              $ds['is_edit'] = $detail->is_edit;
              $ds['status'] = 'update';
  					}
  					else
  					{
  						$error = $this->db->error();
  						echo "Update item failed : ".$error['message'];
  					}
          }
          else
          {
            if( ! $is_free)
            {
              $discount = $this->promotion_model->get_item_discount($item->code, $customer_code, $qty, $payment_code, $channels_code, date('Y-m-d'), $temp_id);
              $discount_label = discountLabel($discount['discLabel1'], $discount['discLabel2'], $discount['discLabel3']);
              $item_disc_amount = empty($discount['amount']) ? 0 : round($discount['amount'] / $qty, 2);
              $sell_price = $item->price - $item_disc_amount;
              $total_amount = round($sell_price * $qty, 2);
            }

            $ds = array(
              'order_temp_id' => $temp_id,
              'item_type' => $item->count_stock ? 'I' : 'S',
              'product_code' => $item->code,
              'product_name' => $item->name,
              'style_code' => $item->style_code,
              'unit_code' => $item->unit_code,
              'qty' => $qty,
              'cost' => $item->cost,
              'std_price' => $item->price,
              'price' => $item->price,
              'discount_label' => $is_free ? '100%' : $discount_label,
  						'discount_amount' => $is_free ? ($item->price * $qty) : $discount['amount'],
  						'final_price' => $is_free ? 0.00 : $sell_price,
  						'total_amount' => $is_free ? 0.00 : $total_amount,
              'vat_code' => $item->sale_vat_code,
  						'vat_rate' => $item->sale_vat_rate,
  						'vat_amount' => $is_free ? 0.00 : get_vat_amount($total_amount, $item->sale_vat_rate, 'I'), //-- vat type 'I' => include, E = exclude
  						'is_count' => $item->count_stock,
              'is_free' => $is_free,
              'id_rule' => $is_free ? NULL : $discount['id_rule'],
              'id_policy' => $is_free ? NULL : $discount['id_policy'],
              'uid' => uniqid(rand())
            );

            $id = $this->order_pos_model->add_temp_detail($ds);

            if( ! $id)
            {
              $sc = FALSE;
              $this->error = "เพิ่มรายการไม่สำเร็จ";
            }
            else
            {
              $ds['id'] = $id;
              $ds['qty_label'] = number($qty, 2);
              $ds['price_label'] = number($item->price, 2);
              $ds['total_amount_label'] = $is_free ? 0.00 : number($total_amount, 2);
              $ds['is_edit'] = 0;
              $ds['status'] = 'add';
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
        $this->error = "Invalid POS Manchine";
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


  public function add_so_to_temp()
  {
    $sc = TRUE;
    $ds = array();
    $rowCount = 0;

    $this->load->model('masters/products_model');
    $this->load->model('orders/sales_order_model');

    $pos_id = $this->input->post('pos_id');
    $temp_id = $this->input->post('order_temp_id');
    $customer_code = $this->input->post('customer_code');
    $channels_code = $this->input->post('channels_code');
    $payment_code = $this->input->post('payment_code');
    $so_code = $this->input->post('so_code');

    $so = $this->sales_order_model->get($so_code);

    if( ! empty($so))
    {
      if($so->status == 'O')
      {
        if($so->is_term == 0)
        {
          $rows  = $this->sales_order_model->get_details($so_code);

          if( ! empty($rows))
          {
            $this->db->trans_begin();
            //--- drop current rows
            if( ! $this->order_pos_model->drop_temp_details($temp_id))
            {
              $sc = FALSE;
              $this->error = "Failed to delete previous items";
            }

            $billDiscAmount = 0;

            if($sc === TRUE)
            {
              foreach($rows as $rs)
              {
                if($rs->OpenQty > 0 && $rs->line_status == 'O')
                {
                  $detail = $this->order_pos_model->get_so_temp_row($rs->id);
                  $total_amount = $rs->sell_price * $rs->OpenQty;
                  $sumBillDiscAmount = $rs->avgBillDiscAmount * $total_amount;
                  $amountAfDisc = $total_amount - $sumBillDiscAmount;
                  $vatAmount = get_vat_amount($amountAfDisc, $rs->vat_rate, $rs->vat_type);

                  $ds = array(
                  'order_temp_id' => $temp_id,
                  'item_type' => $rs->is_count ? 'I' : 'S',
                  'product_code' => $rs->product_code,
                  'product_name' => $rs->product_name,
                  'style_code' => $rs->style_code,
                  'unit_code' => $rs->unit_code,
                  'qty' => $rs->OpenQty,
                  'cost' => $rs->cost,
                  'std_price' => $rs->std_price,
                  'price' => $rs->price,
                  'discount_label' => $rs->discount_label,
                  'discount_amount' => $rs->discount_amount * $rs->OpenQty,
                  'final_price' => $rs->sell_price,
                  'total_amount' => $rs->sell_price * $rs->OpenQty,
                  'avgBillDiscAmount' => $rs->avgBillDiscAmount,
                  'vat_type' => $rs->vat_type,
                  'vat_code' => $rs->vat_code,
                  'vat_rate' => $rs->vat_rate,
                  'vat_amount' => $vatAmount,
                  'is_count' => $rs->is_count,
                  'is_free' => 0,
                  'id_rule' => NULL,
                  'id_policy' => NULL,
                  'is_edit' => 1, //--- ระบุว่ามีการแก้ไขส่วนลด จะไม่มีการคำนวนส่วนลดใหม่
                  'uid' => uniqid(rand()),
                  'baseCode' => $so_code,
                  'line_id' => $rs->id
                  );

                  if(empty($detail))
                  {
                    if( ! $this->order_pos_model->add_temp_detail($ds))
                    {
                      $sc = FALSE;
                      $this->error = "เพิ่มรายการไม่สำเร็จ";
                      break;
                    }
                  }
                  else
                  {
                    if( ! $this->order_pos_model->update_temp_detail($detail->id, $ds))
                    {
                      $sc = FALSE;
                      $this->error = "Failed to update item row";
                      break;
                    }
                  }

                  $rowCount++;
                  $billDiscAmount += $sumBillDiscAmount;
                } //--- if openQty

                if($sc === TRUE)
                {
                  $arr = array(
                    'so_code' => $so->code,
                    'vat_type' => $so->vat_type,
                    'vat_rate' => $so->vat_rate,
                    'bill_disc_percent' => $so->DiscPrcnt,
                    'bill_disc_amount' => $billDiscAmount,
                    'WhtPrcnt' => $so->WhtPrcnt
                  );

                  if( ! $this->order_pos_model->update_temp($temp_id, $arr))
                  {
                    $sc = FALSE;
                    $this->error = "Failed to link Sales order code";
                  }
                }
              } //--- end foreach

              if($rowCount == 0)
              {
                $sc = FALSE;
                $this->error = "ไม่พบรายการค้างส่งในใบสั่งขาย";
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
            $this->error = "ไม่พบรารการในใบสั่งงาน";
          }
        }
        else
        {
          $sc = FALSE;
          $this->error = "ใบสั่งขายเงินเชื่อ ไม่สามารถตัดบิลได้";
        }
      }
      else
      {
        $sc = FALSE;
        $this->error = "สถานะใบสั่งงานไม่ถูกต้อง";
      }
    }
    else
    {
      $sc = FALSE;
      $this->error = "ใบสั่งงานไม่ถูกต้อง";
    }

    $arr = array(
    'status' => $sc === TRUE ? 'success' : 'failed',
    'message' => $sc === TRUE ? 'success' : $this->error
    );

    echo json_encode($arr);
  }


  public function remove_so_temp()
  {
    $sc = TRUE;

    $temp_id = $this->input->post('temp_id');
    $so_code = $this->input->post('so_code');

    if( ! empty($temp_id) && ! empty($so_code))
    {
      $tmp = $this->order_pos_model->get_temp($temp_id);

      if( ! empty($tmp))
      {
        $this->db->trans_begin();

        $arr = array(
          'so_code' => NULL,
          'vat_type' => 'I',
          'vat_rate' => getConfig('SALE_VAT_RATE'),
          'bill_disc_percent' => 0.00,
          'bill_disc_amount' => 0.00,
          'WhtPrcnt' => 0.00
        );

        if( ! $this->order_pos_model->update_temp($temp_id, $arr))
        {
          $sc = FALSE;
          $this->error = "Failed to unlink Sales order number";
        }

        if($sc === TRUE)
        {
          if( ! $this->order_pos_model->drop_temp_details($temp_id))
          {
            $sc = FALSE;
            $this->error = "Failed to delete item rows";
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
        $this->error = "Invaid temp_id";
      }
    }
    else
    {
      $sc = FALSE;
      set_error('required');
    }

    $arr = array(
      'status' => $sc === TRUE ? 'success' : 'failed',
      'message' => $sc === TRUE ? 'success' : $this->error
    );

    echo json_encode($arr);
  }



  public function update_temp_payment()
  {
    $sc = TRUE;
    $temp_id = $this->input->post('temp_id');
    $payment_code = $this->input->post('payment_code');
    $payment_role = $this->input->post('payment_role');

    $arr = array(
      'payment_code' => $payment_code,
      'payment_role' => $payment_role
    );

    if( ! $this->order_pos_model->update_temp($temp_id, $arr))
    {
      $sc = FALSE;
      set_error('update');
    }

    $this->_response($sc);
  }


  public function remove_temp()
	{
		$sc = TRUE;
		$id = $this->input->post('id');

		if(! $this->order_pos_model->delete_temp_detail($id))
		{
			$sc = FALSE;
			$this->error = "Delete failed";
		}

		$this->_response($sc);
	}


  public function remove_temp_rows()
  {
    $sc = TRUE;

    $ids = $this->input->post('rows');

    if( ! empty($ids))
    {
      if( ! $this->order_pos_model->delete_temp_details_by_id_list($ids))
      {
        $sc = FALSE;
        $this->error = "Delete failed";
      }
    }
    else
    {
      $sc = FALSE;
      $this->error = "Missing required parameter";
    }

    $this->_response($sc);
  }


  //----- Delete all temp details
  public function clear_temp_details()
  {
    $sc = TRUE;
    $temp_id = $this->input->post('temp_id');

    if( ! $this->order_pos_model->drop_temp_details($temp_id))
    {
      $sc = FALSE;
      $this->error = "Failed to remove items";
    }

    $this->_response($sc);
  }


  function recal_discount()
  {
    $this->load->model('orders/promotion_model');
    $sc = TRUE;
    $temp_id = $this->input->post('temp_id');
    $customer_code = $this->input->post('customer_code');
    $channels_code = $this->input->post('channels_code');
    $payment_code = $this->input->post('payment_code');

    $details = $this->order_pos_model->get_temp_details($temp_id);

    if( ! empty($details))
    {
      foreach($details as $rs)
      {
        if($rs->is_free == 0 && $rs->is_edit == 0)
        {
          $discount = $this->promotion_model->get_item_discount($rs->product_code, $customer_code, $rs->qty, $payment_code, $channels_code, date('Y-m-d'), $temp_id);
          $discount_label = discountLabel($discount['discLabel1'], $discount['discLabel2'], $discount['discLabel3']);
          $item_disc_amount = empty($discount['amount']) ? 0 : round($discount['amount'] / $rs->qty, 2);
          $sell_price = $rs->price - $item_disc_amount;
          $total_amount = round($sell_price * $rs->qty, 2);
          $vat_amount = get_vat_amount($total_amount, $rs->vat_rate, 'I');

          $ds = array(
            'discount_label' => $discount_label,
            'discount_amount' => $discount['amount'],
            'final_price' => $sell_price,
            'total_amount' => $total_amount,
            'vat_amount' => $vat_amount,
            'id_rule' => $discount['id_rule'],
            'id_policy' => $discount['id_policy']
          );

          if($this->order_pos_model->update_temp_detail($rs->id, $ds))
          {
            $rs->discount_label = $discount_label;
            $rs->discount_amount = $discount['amount'];
            $rs->final_price = $sell_price;
            $rs->total_amount = $total_amount;
            $rs->vat_amount = $vat_amount;
            $rs->id_rule = $discount['id_rule'];
            $rs->id_policy = $discount['id_policy'];
            $rs->qty_label = number($rs->qty, 2);
            $rs->price_label = number($rs->price, 2);
            $rs->total_amount_label = number($total_amount, 2);
          }
        }
        else
        {
          $rs->qty_label = number($rs->qty, 2);
          $rs->price_label = number($rs->price, 2);
          $rs->total_amount_label = number($rs->total_amount, 2);
        }
      }
    }
    else
    {
      $sc = FALSE;
      $this->error = "ไม่พบรายการขาย";
    }

    $arr = array(
      'status' => $sc === TRUE ? 'success' : 'failed',
      'message' => $sc === TRUE ? 'success' : $this->error,
      'data' => $sc === TRUE ? $details : NULL
    );

    echo json_encode($arr);
  }


  public function update_temp_bill_disc()
  {
    $sc = TRUE;
    $temp_id = $this->input->post('temp_id');
    $disc_percent = $this->input->post('discount_percent');
    $disc_amount = $this->input->post('discount_amount');

    $arr = array(
      'bill_disc_percent' => $disc_percent,
      'bill_disc_amount' => $disc_amount
    );

    if( ! $this->order_pos_model->update_temp($temp_id, $arr))
    {
      $sc = FALSE;
      $this->error = "Failed to update discount";
    }

    echo $sc === TRUE ? 'success' : $this->error;
  }


  public function save_order()
	{
		$sc = TRUE;

		$this->load->model('masters/products_model');
		$this->load->model('masters/payment_methods_model');
		$this->load->model('masters/channels_model');
		$this->load->model('masters/customers_model');
    $this->load->model('orders/sales_order_model');

    $pos_id = $this->input->post('pos_id');
    $temp_id = $this->input->post('temp_id');
    $acc_id = $this->input->post('acc_id');
    $amountBfDisc = $this->input->post('amountBfDisc');
    $discPrcnt = $this->input->post('discPrcnt');
    $discAmount = $this->input->post('discAmount');
    $amount = $this->input->post('amount'); //--- amount after disc and tax
    $downPaymentAmount = $this->input->post('downPaymentAmount');
    $downPaymentUse = empty($this->input->post('downPaymentUse')) ? NULL : json_decode($this->input->post('downPaymentUse'));
    $payAmount = $this->input->post('payAmount'); //-- amount - down payment
    $vatSum = $this->input->post('vatSum'); //---- vat sum calculate from amount after discount (no down payment)
    $cashReceive = $this->input->post('cashReceive'); //--- เงินสดที่รับมา อาจจะมากกว่าที่ต้องรับ เช่น ของ 900 รับมา 1000 เป็นต้น
    $cashAmount = $this->input->post('cashAmount');
    $transferAmount = $this->input->post('transferAmount');
    $cardAmount = $this->input->post('cardAmount');
    $chequeAmount = $this->input->post('chequeAmount');
    $receive = $this->input->post('receive');
    $change = $this->input->post('change');
    $whtPrcnt = get_zero($this->input->post('whtPrcnt'));
    $whtAmount = get_zero($this->input->post('whtAmount'));
    $paymentDate = db_date($this->input->post('paymentDate'), FALSE);
    $payment_role = $this->input->post('paymentRole');

    $df_sale_id = getConfig('DEFAULT_SALES_ID');

    $pos = $this->pos_model->get_pos($pos_id);

    if( ! empty($pos))
    {
      $temp = $this->order_pos_model->get_temp($temp_id);

      if( ! empty($temp))
      {
        $details = $this->order_pos_model->get_temp_details($temp_id);

        if( ! empty($details))
        {
          //--- create new order
          $code = $this->get_new_code($pos->prefix, $pos->running);

          $this->db->trans_begin();

          $balance = $amount - ($downPaymentAmount + $payAmount + $whtAmount);

          //--- เช็คว่ามาจากหลายช่องทางมั้ย
          $cp = $cashAmount > 0 ? 1 : 0;
          $tp = $transferAmount > 0 ? 1 : 0;
          $ca = $cardAmount > 0 ? 1 : 0;
          $cq = $chequeAmount > 0 ? 1 : 0;
          $multi = ($cp + $ca + $tp + $cq) > 1 ? TRUE : FALSE;

          $payment_code = $multi ? "MULTIPAYMENT" : ($chequeAmount > 0 ? "CHEQUE" : ($cashAmount > 0 ? $pos->cash_payment :($transferAmount > 0 ? $pos->transfer_payment : ($cardAmount > 0 ? $pos->card_payment : $temp->payment_code))));
          // $payment_role = $multi ? 6 : ($chequeAmount > 0 ? 7 : ($cashAmount > 0 ? 1 :($transferAmount > 0 ? 2 : ($cardAmount > 0 ? 3 : $temp->payment_role))));

          $so = empty($temp->so_code) ? NULL : $this->sales_order_model->get($temp->so_code);
          $sale_id =  empty($so) ? (empty($this->_user->sale_id) ? $df_sale_id : $this->_user->sale_id)  : (empty($so->sale_id) ? $df_sale_id : $so->sale_id);
          $customer = empty($so) ? $this->customers_model->get($pos->customer_code) : $this->customers_model->get($so->customer_code);

          $ds = array(
            'code' => $code,
            'customer_code' => empty($so) ? $customer->code : $so->customer_code,
            'customer_name' => empty($so) ? $pos->customer_name : $so->customer_name,
            'branch_code' => empty($so) ? NULL : $so->branch_code,
            'branch_name' => empty($so) ? NULL : $so->branch_name,
            'tax_id' => empty($so) ? $customer->Tax_Id : $so->tax_id,
            'address' => empty($so) ? NULL : $so->address,
            'sub_district' => empty($so) ? NULL : $so->sub_district,
            'district' => empty($so) ? NULL : $so->district,
            'province' => empty($so) ? NULL : $so->province,
            'postcode' => empty($so) ? NULL : $so->postcode,
            'phone' => empty($so) ? NULL : $so->phone,
            'channels_code' => empty($so) ? $pos->channels_code : $so->channels_code,
            'payment_code' => $payment_code,
            'payment_role' => $payment_role,
            'shop_id' => $pos->shop_id,
            'pos_id' => $pos->id,
            'pos_no' => $pos->pos_no,
            'use_vat' => $pos->use_vat,
            'vat_type' => $temp->vat_type,
            'vat_rate' => $temp->vat_rate,
            'amount_bf_disc' => $amountBfDisc,
            'discPrcnt' => $discPrcnt,
            'disc_amount' => $discAmount,
            'vat_amount' => $vatSum,
            'amount' => $amount, //---- amount after disc and tax
            'down_payment_amount' => $downPaymentAmount,
            'payAmount' => $payAmount, //--- amount - down payment
            'balance' => $balance > 0 ? $balance : 0, //-- amount - (pay amount + down payment)
            'received' => $receive,
            'changed' => $change,
            'WhtPrcnt' => $whtPrcnt,
            'WhtAmount' => $whtAmount,
            'acc_id' => $temp->payment_role == 2 ? $acc_id : NULL,
            'warehouse_code' => $pos->warehouse_code,
            'zone_code' => $pos->zone_code,
            'uname' => $this->_user->uname,
            'sale_id' => $sale_id,
            'pc_id' => $temp->pc_id,
            'so_code' => empty($so) ? NULL : $so->code,
            'round_id' => $pos->round_id
          );

          $id = $this->order_pos_model->add($ds);

          if( ! empty($id) )
          {
            $total_vat = 0;

            foreach($details as $rs)
            {
              if($sc === FALSE)
              {
                break;
              }

              //--- ข้อมูลสำหรับบันทึกยอดขาย
              $totalBfDisc = $rs->final_price * $rs->qty; //--- มูลค่าก่อนส่วนลดท้ายบิล (ลดในรายการแล้ว)
              $avgBillDiscAmount = $amountBfDisc > 0 ? $discAmount/$amountBfDisc : 0; //--- ส่วนลดท้ายบิลเฉลี่ย / บาท  เอาส่วนลดท้ายบิล / มูลค่ารวมก่อนส่วนลดท้ายบิล
              $sumBillDiscAmount = $totalBfDisc * $avgBillDiscAmount;
              $total_amount = $totalBfDisc - $sumBillDiscAmount; //--- มูลค่าหลังส่วนลดท้ายบิล
              $vatAmount = get_vat_amount($total_amount, $rs->vat_rate, $temp->vat_type);
              $PriceAfDisc = $rs->final_price - ($avgBillDiscAmount * $rs->final_price); //---- ราคาหลังส่วนลดท้ายบิล
              $totalDiscount = $rs->discount_amount * $rs->qty;

              $arr = array(
                'order_id' => $id,
                'item_type' => $rs->item_type,
                'order_code' => $code,
                'product_code' => $rs->product_code,
                'product_name' => $rs->product_name,
                'style_code' => $rs->style_code,
                'unit_code' => $rs->unit_code,
                'qty' => $rs->qty,
                'cost' => $rs->cost,
                'std_price' => $rs->std_price,
                'price' => $rs->price,
                'discount_label' => $rs->discount_label,
                'discount_amount' => $rs->discount_amount, //--- ส่วนลดรวมก่อนส่วนลดท้ายบิล
                'final_price' => $rs->final_price,
                'total_amount' => $totalBfDisc, //---- มูลค่ารวมก่อนส่วนลดท้ายบิล
                'sumBillDiscAmount' => $sumBillDiscAmount, //--- ส่วนลดท้ายบิลเฉลี่ยของบรรทัดนี้
                'avgBillDiscAmount' => $avgBillDiscAmount, //--- ส่วนลดท้ายบิลเฉลี่ยต่อชิ้นของบรรทัดนี้
                'PriceBfVAT' => remove_vat($PriceAfDisc, $rs->vat_rate), //--- ราคาขายหลังส่วนลดท้ายบิล ไม่รวม VAT
                'TotalBfVAT' => remove_vat($total_amount, $rs->vat_rate), //--- มูลค่าหลังส่วนลดท้ายบิล ไม่รวม VAT
                'use_vat' => $pos->use_vat,
                'vat_type' => $temp->vat_type,
                'vat_code' => $rs->vat_code,
                'vat_rate' => $rs->vat_rate,
                'vat_amount' => $vatAmount, //--- มูลค่า VAT หลังส่วนลดท้ายบิล
                'is_count' => $rs->is_count,
                'id_rule' => $rs->id_rule,
                'id_policy' => $rs->id_policy,
                'uid' => $rs->uid,
                'parent_uid' => $rs->parent_uid,
                'is_free' => $rs->is_free,
                'baseCode' => get_null($rs->baseCode),
                'line_id' => get_null($rs->line_id)
              );

              if( ! $this->order_pos_model->add_detail($arr))
              {
                $sc = FALSE;
                $this->error = "เพิ่มรายการไม่สำเร็จ";
              }

              //--- update so open qty
              if($sc === TRUE && ! empty($rs->line_id))
              {
                $sol = $this->sales_order_model->get_detail($rs->line_id);

                if( ! empty($sol))
                {
                  if($sol->line_status == 'O')
                  {
                    if($sol->OpenQty > 0 && $sol->OpenQty >= $rs->qty)
                    {
                      $OpenQty = $sol->OpenQty - $rs->qty;

                      $arr = ['OpenQty' => $OpenQty];

                      if($OpenQty == 0)
                      {
                        $arr['line_status'] = 'C';
                      }

                      if( ! $this->sales_order_model->update_detail($rs->line_id, $arr))
                      {
                        $sc = FALSE;
                        $this->error = "Failed to update OpenQty On Line Id : {$rs->line_id}";
                      }
                    }
                    else
                    {
                      $sc = FALSE;
                      $this->error = "จำนวนที่ตัดมากกว่าจำนวนคงค้างในใบสั่งขาย {$rs->baseCode} : {$rs->product_code}";
                    }
                  }
                  else
                  {
                    $sc = FALSE;
                    $this->error = "สถานะรายการในใบสั่งขายถูกปิดไปแล้ว {$rs->baseCode} : {$rs->product_code}";
                  }
                }
                else
                {
                  $sc = FALSE;
                  $this->error = "ไม่พบรายการเชื่อมโยงในใบสั่งขาย {$rs->baseCode} : {$rs->product_code}";
                }
              } //--- end if so_line_id
            } //-- end foreach details

            if($sc === TRUE && ! empty($temp->so_code))
            {
              //--- close sales order
              if( ! empty($so))
              {
                //---- close so if all line closed
                $count = $this->sales_order_model->count_open_line($temp->so_code);
                $paidAmount = $so->paidAmount + $payAmount;
                $balance = $so->DocTotal - $paidAmount;

                $arr = [
                  'paidAmount' => $paidAmount,
                  'TotalBalance' => $balance > 0 ? $balance : 0,
                  'bill_code' => $code,
                  'date_upd' => now(),
                  'update_user' => $this->_user->uname
                ];

                if($count == 0)
                {
                  $arr['status'] = 'C';
                }

                if( ! $this->sales_order_model->update($temp->so_code, $arr))
                {
                  $sc = FALSE;
                  $this->error = "Failed to close Sales order";
                }
                else
                {
                  $arr = array(
                    'code' => $so->code,
                    'action' => 'close',
                    'uname' => $this->_user->uname,
                    'name' => $this->_user->name,
                    'date_upd' => now()
                  );

                  $this->sales_order_model->add_logs($arr);
                }
              }
            }

            if($sc === TRUE)
            {
              if( ! empty($downPaymentUse))
              {
                foreach($downPaymentUse as $rs)
                {
                  if($sc === FALSE)
                  {
                    break;
                  }

                  $dp = $this->order_down_payment_model->get_by_id($rs->id);

                  if( ! empty($dp))
                  {
                    if($dp->status == 'O')
                    {
                      $used = $dp->used + $rs->amount;
                      $available = $dp->amount - $used;
                      $status = $available > 0 ? 'O' : 'C';

                      $arr = array(
                        'ref_code' => $code,
                        'used' => $used,
                        'available' => $available > 0 ? $available : 0,
                        'status' => $status
                      );


                      if( ! $this->order_down_payment_model->update($dp->id, $arr))
                      {
                        $sc = FALSE;
                        $this->error = "ตัดยอดเงินมัดจำไม่สำเร็จ";
                      }

                      if($sc === TRUE)
                      {
                        $arr = array(
                          'down_payment_id' => $dp->id,
                          'down_payment_code' => $dp->code,
                          'TargetRef' => $code,
                          'TargetType' => 'POS',
                          'so_code' => $temp->so_code,
                          'order_code' => NULL,
                          'bill_code' => $code,
                          'invoice_code' => NULL,
                          'amount' => $rs->amount,
                          'amountBfUse' => $dp->available,
                          'amountAfUse' => $available > 0 ? $available : 0,
                          'payment_role' => $dp->payment_role,
                          'acc_id' => $dp->acc_id,
                          'user' => $this->_user->uname
                        );

                        if( ! $this->order_down_payment_model->add_detail($arr))
                        {
                          $sc = FALSE;
                          $this->error = "บันทึกรายการตัดยอดเงินมัดจำไม่สำเร็จ";
                        }
                      } //-- if( $sc === TRUE)
                    } //-- if( dp->status == 'O')
                  } //-- if( ! empty($dp))
                } //--- foreach(downPaymentUse)
              } //-- if( ! empty($downPaymentUse))
            } //--- if( $sc === TRUE)

            //-- if paymet by cash , update cash amount in cash drawer
            if($sc === TRUE && $cashAmount > 0)
            {
              $cash_amount = $pos->cash_amount + $cashAmount;
              $arr = array('cash_amount' => $cash_amount);

              if( ! $this->pos_model->update_by_id($pos->id, $arr))
              {
                $sc = FALSE;
                $this->error = "บันทึกยอดเงินเข้าลิ้นชักไม่สำเร็จ";
              }
            }

            //--- remove temp
            if( $sc === TRUE)
            {
              if( ! $this->order_pos_model->drop_temp_details($temp_id))
              {
                $sc = FALSE;
                $this->error = "ลบรายการขายชั่วคราวไม่สำเร็จ";
              }

              if($sc === TRUE && ! $this->order_pos_model->drop_temp($temp_id))
              {
                $sc = FALSE;
                $this->error = "ลบรายการขายชัวคราวไม่สำเร็จ";
              }
            }

            //--- add sales movement
            if($sc === TRUE)
            {
              if($cashAmount == 0 && $transferAmount == 0 && $cardAmount == 0 && $chequeAmount == 0)
              {
                //--- กรณีที่ส่วนลด 100%
                $arr = array(
                  'code' => $code,
                  'type' => 'S',     //--- S = sales , C = Cancel, R = Return
                  'shop_id' => $pos->shop_id,
                  'pos_id' => $pos->id,
                  'amount' => $cashAmount,
                  'payment_role' => 1,
                  'acc_id' => NULL,
                  'user' => $this->_user->uname,
                  'round_id' => $pos->round_id
                );

                if( ! $this->pos_sales_movement_model->add($arr))
                {
                  $sc = FALSE;
                  $this->error = "Insert sale movement failed";
                }
              }
              else
              {
                //--- cash movement
                if($cashAmount > 0)
                {
                  $arr = array(
                    'code' => $code,
                    'type' => 'S',     //--- S = sales , C = Cancel, R = Return
                    'shop_id' => $pos->shop_id,
                    'pos_id' => $pos->id,
                    'amount' => $cashAmount,
                    'payment_role' => 1,
                    'acc_id' => NULL,
                    'user' => $this->_user->uname,
                    'round_id' => $pos->round_id
                  );

                  if( ! $this->pos_sales_movement_model->add($arr))
                  {
                    $sc = FALSE;
                    $this->error = "Insert cash movement failed";
                  }
                }

                if($transferAmount > 0)
                {
                  $arr = array(
                    'code' => $code,
                    'type' => 'S',     //--- S = sales , C = Cancel, R = Return
                    'shop_id' => $pos->shop_id,
                    'pos_id' => $pos->id,
                    'amount' => $transferAmount,
                    'payment_role' => 2,
                    'acc_id' => $acc_id,
                    'user' => $this->_user->uname,
                    'round_id' => $pos->round_id
                  );

                  if( ! $this->pos_sales_movement_model->add($arr))
                  {
                    $sc = FALSE;
                    $this->error = "Insert transfer movement failed";
                  }
                }

                if($cardAmount > 0)
                {
                  $arr = array(
                    'code' => $code,
                    'type' => 'S',     //--- S = sales , C = Cancel, R = Return
                    'shop_id' => $pos->shop_id,
                    'pos_id' => $pos->id,
                    'amount' => $cardAmount,
                    'payment_role' => 3,
                    'acc_id' => NULL,
                    'user' => $this->_user->uname,
                    'round_id' => $pos->round_id
                  );

                  if( ! $this->pos_sales_movement_model->add($arr))
                  {
                    $sc = FALSE;
                    $this->error = "Insert card movement failed";
                  }
                }

                if($chequeAmount > 0)
                {
                  $arr = array(
                    'code' => $code,
                    'type' => 'S',     //--- S = sales , C = Cancel, R = Return
                    'shop_id' => $pos->shop_id,
                    'pos_id' => $pos->id,
                    'amount' => $chequeAmount,
                    'payment_role' => 7,
                    'acc_id' => NULL,
                    'user' => $this->_user->uname,
                    'round_id' => $pos->round_id
                  );

                  if( ! $this->pos_sales_movement_model->add($arr))
                  {
                    $sc = FALSE;
                    $this->error = "Insert card movement failed";
                  }
                }
              }
            }

            //---- insert order_pos_payment
            if($sc === TRUE)
            {
              $payments = [];

              if($cashAmount > 0)
              {
                $payments[] = [
                  "code" => $code,
                  "payment_code" => $pos->cash_payment,
                  "payment_role" => 1,
                  "role_name" => "เงินสด",
                  "amount" => $cashAmount,
                  "uname" => $this->_user->uname,
                  "payment_date" => $paymentDate,
                  "shop_id" => $pos->shop_id,
                  "pos_id" => $pos->id,
                  "ref_type" => 'SP' //-- DP = down payment, SP = sales payment
                ];
              }

              if($transferAmount > 0)
              {
                $payments[] = [
                  "code" => $code,
                  "payment_code" => $pos->transfer_payment,
                  "payment_role" => 2,
                  "role_name" => "เงินโอน",
                  "amount" => $transferAmount,
                  "acc_id" => $pos->account_id,
                  "uname" => $this->_user->uname,
                  "payment_date" => $paymentDate,
                  "shop_id" => $pos->shop_id,
                  "pos_id" => $pos->id,
                  "ref_type" => 'SP' //-- DP = down payment, SP = sales payment
                ];
              }

              if($cardAmount > 0)
              {
                $payments[] = [
                  "code" => $code,
                  "payment_code" => $pos->card_payment,
                  "payment_role" => 3,
                  "role_name" => "บัตรเครดิต",
                  "amount" => $cardAmount,
                  "uname" => $this->_user->uname,
                  "payment_date" => $paymentDate,
                  "shop_id" => $pos->shop_id,
                  "pos_id" => $pos->id,
                  "ref_type" => 'SP' //-- DP = down payment, SP = sales payment
                ];
              }

              if($chequeAmount > 0)
              {
                $payments[] = [
                  "code" => $code,
                  "payment_code" => "CHEQUE",
                  "payment_role" => 7,
                  "role_name" => "เช็ค",
                  "amount" => $chequeAmount,
                  "uname" => $this->_user->uname,
                  "payment_date" => $paymentDate,
                  "shop_id" => $pos->shop_id,
                  "pos_id" => $pos->id,
                  "ref_type" => 'SP' //-- DP = down payment, SP = sales payment
                ];
              }

              if( ! empty($payments))
              {
                foreach($payments as $arr)
                {
                  if( ! $this->order_pos_model->add_order_payment($arr))
                  {
                    $sc = FALSE;
                    $this->error = "Failed to insert order payments details";
                  }
                }
              }
            }
          }
          else
          {
            $sc = FALSE;
            $this->error = "บันทึกเอกสารไม่สำเร็จ";
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
          $this->error = "ไม่พบรายการขาย";
        }
      }
      else
      {
        $sc = FALSE;
        $this->error = "ไม่พบเอกสารขาย";
      }
    }
    else
    {
      $sc = FALSE;
      $this->error = "ไม่พบข้อมูลเครื่อง POS";
    }

    $arr = array(
      'status' => $sc === TRUE ? 'success' : 'failed',
      'message' => $sc === TRUE ? 'success' : $this->error,
      'order_code' => $sc === TRUE ? $code : NULL
    );

    echo json_encode($arr);
	}


  public function save_cash_in()
  {
    $sc = TRUE;
    $pos_id = $this->input->post('pos_id');
    $amount = $this->input->post('amount');

    $pos = $this->pos_model->get_pos($pos_id);

    if( ! empty($pos))
    {
      $cash_amount = $pos->cash_amount + $amount;

      $arr = array(
        'code' => $pos->code,
        'type' => 'CI',     //--- S = sales , C = Cancel, R = Return, CI = Cash In, CO = Cash Out
        'shop_id' => $pos->shop_id,
        'pos_id' => $pos->id,
        'amount' => $amount,
        'payment_role' => 1,
        'acc_id' => NULL,
        'user' => $this->_user->uname,
        'round_id' => $pos->round_id
      );

      $this->db->trans_begin();
      $trans_id = $this->pos_sales_movement_model->add($arr);

      if($trans_id)
      {
        //--- update cash amount
        $arr = array(
          'cash_amount' => $cash_amount
        );

        if( ! $this->pos_model->update_by_id($pos->id, $arr))
        {
          $sc = FALSE;
          $this->error = "บันทึกยอดเงินเข้าลิ้นชักไม่สำเร็จ";
        }
      }
      else
      {
        $sc = FALSE;
        $this->error = "Insert sales movement failed";
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
      $this->error = "ไม่พบข้อมูลเครื่อง POS";
    }

    $arr = array(
      'status' => $sc === TRUE ? 'success' : 'failed',
      'message' => $sc === TRUE ? 'success' : $this->error,
      'movement_id' => $sc === TRUE ? $trans_id : NULL
    );

    echo json_encode($arr);
  }


  public function print_cash_in($movement_id)
  {
    $movement = $this->pos_sales_movement_model->get($movement_id);

    if( ! empty($movement))
    {
      $pos = $this->pos_model->get_pos($movement->pos_id);

      $ds = array(
        'pos' => $pos,
        'movement' => $movement
      );

      $this->load->view('print/print_cash_in', $ds);
    }
    else
    {
      $this->error_page();
    }
  }


  public function save_cash_out()
  {
    $sc = TRUE;
    $pos_id = $this->input->post('pos_id');
    $amount = $this->input->post('amount');

    $pos = $this->pos_model->get_pos($pos_id);

    if( ! empty($pos))
    {
      $cash_amount = $pos->cash_amount - $amount;

      $arr = array(
        'code' => $pos->code,
        'type' => 'CO',     //--- S = sales , C = Cancel, R = Return, CI = Cash In, CO = Cash Out
        'shop_id' => $pos->shop_id,
        'pos_id' => $pos->id,
        'amount' => $amount * -1,
        'payment_role' => 1,
        'acc_id' => NULL,
        'user' => $this->_user->uname,
        'round_id' => $pos->round_id
      );

      $this->db->trans_begin();

      $trans_id = $this->pos_sales_movement_model->add($arr);

      if($trans_id)
      {
        //--- update cash amount
        $arr = array(
          'cash_amount' => $cash_amount
        );

        if( ! $this->pos_model->update_by_id($pos->id, $arr))
        {
          $sc = FALSE;
          $this->error = "บันทึกยอดเงินออกจากลิ้นชักไม่สำเร็จ";
        }
      }
      else
      {
        $sc = FALSE;
        $this->error = "Insert sales movement failed";
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
      $this->error = "ไม่พบข้อมูลเครื่อง POS";
    }

    $arr = array(
      'status' => $sc === TRUE ? 'success' : 'failed',
      'message' => $sc === TRUE ? 'success' : $this->error,
      'movement_id' => $sc === TRUE ? $trans_id : NULL
    );

    echo json_encode($arr);
  }


  public function print_cash_out($movement_id)
  {
    $movement = $this->pos_sales_movement_model->get($movement_id);

    if( ! empty($movement))
    {
      $pos = $this->pos_model->get_pos($movement->pos_id);

      $ds = array(
        'pos' => $pos,
        'movement' => $movement
      );

      $this->load->view('print/print_cash_out', $ds);
    }
    else
    {
      $this->error_page();
    }
  }


  public function open_drawer($pos_id)
  {
    if( ! empty($pos_id))
    {
      $pos = $this->pos_model->get_pos($pos_id);

      $ds = array(
        'pos' => $pos
      );

      $this->load->view('print/print_open_drawer', $ds);
    }
    else
    {
      $this->error_page();
    }
  }

  public function print_slip($code)
  {
    $order = $this->order_pos_model->get($code);

    if( ! empty($order))
    {
      $order->emp_name = $this->user_model->get_name($order->uname);

      $ds = array(
        'order' => $order,
        'details' => $this->order_pos_model->get_details($code),
        'payments' => $this->order_pos_model->get_order_payment_details($code),
        'pos' => $this->pos_model->get_pos($order->pos_id)
      );

      $this->load->library('printer');
      $this->load->helper('print');
      $this->load->view('print/print_pos_bill', $ds);
    }
    else
    {
      $this->page_error();
    }
  }


  public function print_return($code)
  {
    $this->load->model('orders/order_pos_return_model');

    $order = $this->order_pos_return_model->get($code);

    if( ! empty($order))
    {
      $order->emp_name = $this->user_model->get_name($order->uname);

      $ds = array(
        'order' => $order,
        'details' => $this->order_pos_return_model->get_details($code),
        'pos' => $this->pos_model->get_pos($order->pos_id)
      );

      $this->load->library('printer');
      $this->load->helper('print');
      $this->load->view('print/print_pos_return_bill', $ds);
    }
    else
    {
      $this->page_error();
    }
  }


  public function print_down_payment($code)
  {
    $this->load->model('orders/sales_order_model');
    $this->load->model('orders/orders_model');
    $this->load->model('masters/slp_model');

    $order = $this->order_down_payment_model->get($code);

    if( ! empty($order))
    {
      $pos = $this->pos_model->get_pos($order->pos_id);
      $so = $order->ref_type == 'WO' ? $this->orders_model->get($order->reference) : $this->sales_order_model->get($order->reference);
      $payments = $this->order_pos_model->get_order_payment_details($code);

      if( ! empty($so))
      {
        $item = $order->ref_type == 'WO' ? "รับเงินมัดจำจากออเดอร์เลขที่ {$order->reference}" : "รับเงินมัดจำตามใบสั่งขายเลขที่ {$order->reference} ";
        $customer_ref = empty($order->customer_ref) ? NULL : "จาก : ".$order->customer_ref;
        $customer_phone = empty($order->customer_phone) ? NULL : "โทร. ".$order->customer_phone;

        $ds = array(
          'id' => $order->id,
          'pos' => $pos,
          'code' => $order->code,
          'customer_name' => $so->customer_ref,
          'sale_name' => $order->ref_type == 'WO' ? $this->slp_model->get_name($so->sale_code) : $this->slp_model->get_name($so->sale_id),
          'date_add' => thai_date($order->date_add, TRUE),
          'item' => $item,
          'customer_ref' => $customer_ref,
          'customer_phone' => $customer_phone,
          'payment_name' => $order->payment_name,
          'payment_role' => $order->payment_role,
          'payments' => $order->payment_role == 6 ? $payments : NULL,
          'amount' => number($order->amount, 2),
          'received' => number($order->receiveAmount, 2),
          'changed' => number($order->changeAmount, 2),
          'staff' => $this->user_model->get_name($order->user)
        );

        $this->load->library('printer');
        $this->load->helper('print');
        $this->load->view('print/print_down_payment_bill', $ds);
      }
      else
      {
        $this->page_error();
      }
    }
    else
    {
      $this->page_error();
    }
  }


  public function get_bill_view()
  {
    $sc = TRUE;
    $code = $this->input->get('code');
    $order = $this->order_pos_model->get($code);

    if( ! empty($order))
    {
      $total_return = 0;
      $total_qty = 0;
  		$total_price = 0;
  		$total_amount = 0;
  		$total_discount = 0;

      $pos = $this->pos_model->get_pos($order->pos_id);
      $details = $this->order_pos_model->get_details($code);

      $payments = $this->order_pos_model->get_order_payment_details($code);

      foreach($details as $rs)
      {
        $total_qty += $rs->qty;
        $total_return += $rs->return_qty;
        $total_price += ($rs->qty * $rs->price);
        $total_amount += $rs->total_amount;
        $total_discount += $rs->discount_amount;

        $rs->total_amount = number($rs->qty * $rs->price, 2);
        $rs->qty = number($rs->qty);
        $rs->return_qty = number($rs->return_qty);
        $rs->price = number($rs->price, 2);
        $rs->vat_sign = $rs->use_vat ? 'V' : '';

        if($rs->is_free)
        {
          $rs->product_code = 'Free-'.$rs->product_code;
        }
      }


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
        'code' => $order->code,
        'so_code' => $order->so_code,
        'id' => $order->id,
        'date_add' => thai_date($order->date_add, TRUE),
        'details' => $details,
        'total_qty' => number($total_qty),
        'total_return' => number($total_return),
        'total_price' => number($total_price, 2),
        'total_amount' => number($total_amount - $order->disc_amount, 2),
        'total_discount' => number($total_discount + $order->disc_amount, 2),
        'use_vat' => $order->use_vat ? TRUE : ($order->so_code && $order->vat_type != 'N' ? TRUE : FALSE),
        'vatable' => number($order->amount - $order->vat_amount, 2),
        'vat_amount' => number($order->vat_amount, 2),
        'down_payment' => empty($order->down_payment_amount) ? FALSE : number($order->down_payment_amount, 2),
        'amount_to_pay' => number($order->payAmount, 2),
        'payment_name' => $order->payment_name,
        'payment_role' => $order->payment_role,
        'received' => number($order->received, 2),
        'changed' => number($order->changed, 2),
        'invoice_code' => $order->invoice_code,
        'payment_details' => $order->payment_role == 6 ? $payments : NULL,
        'staff' => $this->user_model->get_name($order->uname),
        'allow_print' => TRUE,
        'allow_cancel' => date('Y-m-d') > date('Y-m-d', strtotime($order->date_add)) ? FALSE : ($order->status == 'O' ? TRUE : FALSE),
        'allow_invoice' => $order->status == 'O' ? TRUE : FALSE,
        'status' => $order->status,
        'is_cancel' => $order->status == 'D' ? TRUE : FALSE,
        'cancel_reason' => $order->cancel_reason,
        'cancel_user' => $order->cancel_user,
        'cancel_date' => thai_date($order->cancel_date, TRUE)
      );
    }
    else
    {
      $sc = FALSE;
      $this->error = "Document cannot be found !";
    }

    $arr = array(
      'status' => $sc === TRUE ? 'success' : 'failed',
      'message' => $sc === TRUE ? 'success' : $this->error,
      'data' => $sc === TRUE ? $ds : NULL
    );

    echo json_encode($arr);
  }


  public function get_down_payment_view()
  {
    $this->load->model('orders/sales_order_model');
    $this->load->model('orders/orders_model');

    $sc = TRUE;

    $code = $this->input->get('code');
    $order = $this->order_down_payment_model->get($code);

    if( ! empty($order))
    {
      $pos = $this->pos_model->get_pos($order->pos_id);
      $so = $order->ref_type == 'WO' ? $this->orders_model->get($order->reference) : $this->sales_order_model->get($order->reference);
      $payments = $this->order_pos_model->get_order_payment_details($code);

      if( ! empty($so))
      {
        $item = $order->ref_type == 'WO' ? "รับเงินมัดจำจากออเดอร์เลขที่ {$order->reference}" : "รับเงินมัดจำตามใบสั่งขายเลขที่ {$order->reference} ";
        $customer_ref = empty($order->customer_ref) ? NULL : "จาก : ".$order->customer_ref;
        $customer_phone = empty($order->customer_phone) ? NULL : "โทร. ".$order->customer_phone;

        $ds = array(
          'id' => $order->id,
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
          'code' => $order->code,
          'customer_name' => $so->customer_ref,
          'customer_ref' => $customer_ref,
          'customer_phone' => $customer_phone,
          'date_add' => thai_date($order->date_add, TRUE),
          'item' => $item,
          'payment_name' => $order->payment_name,
          'payment_role' => $order->payment_role,
          'payments' => $order->payment_role == 6 ? $payments : NULL,
          'amount' => number($order->amount, 2),
          'received' => number($order->receiveAmount, 2),
          'changed' => number($order->changeAmount, 2),
          'staff' => $this->user_model->get_name($order->user),
          'allow_print' => $order->status == 'D' ? FALSE : TRUE,
          'allow_cancel' => ($order->status == 'O' ? TRUE : FALSE),
          'status' => $order->status,
          'is_cancel' => $order->status == 'D' ? TRUE : FALSE,
          'cancel_reason' => $order->cancle_reason,
          'cancel_user' => $order->cancle_user,
          'cancel_date' => thai_date($order->cancle_date, TRUE)
        );
      }
      else
      {
        $sc = FALSE;
        $this->error = "ไม่พบใบสั่ง่ขาย";
      }
    }
    else
    {
      $sc = FALSE;
      $this->error = "Document cannot be found !";
    }

    $arr = array(
      'status' => $sc === TRUE ? 'success' : 'failed',
      'message' => $sc === TRUE ? 'success' : $this->error,
      'data' => $sc === TRUE ? $ds : NULL
    );

    echo json_encode($arr);
  }


  public function get_so_view()
  {
    $this->load->model('orders/sales_order_model');
    $sc = TRUE;
    $code = $this->input->get('code');
    $order = $this->sales_order_model->get($code);

    if(empty($order))
    {
      $sc = FALSE;
      $this->error = "Document cannot be found !";
    }

    $arr = array(
      'status' => $sc === TRUE ? 'success' : 'failed',
      'message' => $sc === TRUE ? 'success' : $this->error,
      'data' => $sc === TRUE ? $order : NULL
    );

    echo json_encode($arr);
  }


  public function get_wo_view()
  {
    $this->load->model('orders/orders_model');

    $sc = TRUE;

    $code = $this->input->get('code');

    $order = $this->orders_model->get($code);

    if(empty($order))
    {
      $sc = FALSE;
      $this->error = "Document cannot be found !";
    }

    $arr = array(
      'status' => $sc === TRUE ? 'success' : 'failed',
      'message' => $sc === TRUE ? 'success' : $this->error,
      'data' => $sc === TRUE ? $order : NULL
    );

    echo json_encode($arr);
  }


  public function edit_temp($pos_id, $temp_id)
	{
    $pos = $this->pos_model->get_pos($pos_id);

    if( ! empty($pos))
    {
      $valid = $this->shop_model->validate_shop_user($pos->shop_id, $this->_user->uname);

      if( ! $valid)
      {
        redirect(base_url().'users/authentication/pos_login');
      }
      else
      {
        $this->load->model('masters/payment_methods_model');
        $this->load->helper('payment_method');

        $order = $this->order_pos_model->get_temp($temp_id);

        if( ! empty($order))
        {
          $details = $this->order_pos_model->get_temp_details($order->id);
          $pos->order_code = $this->get_new_code($pos->prefix, $pos->running);
          $totalQty = 0;
          $totalBfDisc = 0;
          $vatSum = 0;
          $downPayment = empty($order->so_code) ? 0.00 : $this->order_down_payment_model->get_sum_amount_by_reference($order->so_code);
          $amountAfterDiscAndTax = 0;
          $amountAfterDisc = 0;
          $docTotal = 0;


          $total = 0;
          $totalTaxAmount = 0;
          $vat_type = $order->vat_type;
          $df_rate = $vat_type == 'N' ? 0 : getConfig('SALE_VAT_RATE');
          $taxRate = $df_rate * 0.01;
          $rounding = 0;

          if( ! empty($details))
          {
            foreach($details as $rs)
            {
              if($rs->qty > 0 && $rs->price > 0)
              {
                $rate = $rs->vat_rate;
                $total += $rs->total_amount;

                if($rate > 0 && $taxRate > 0) {
                  $totalTaxAmount += $rs->total_amount;
                }

                $totalQty += $rs->qty;
              }
            }

            //--- update bill discount
            $billDiscPercent = $order->bill_disc_percent;
            $billDiscAmount = $order->bill_disc_amount;

            //---- bill discount amount
            $amountAfterDisc = $total - $billDiscAmount;  //--- มูลค่าสินค้า หลังหักส่วนลด
            $amountBeforeDiscWithTax = $totalTaxAmount;   //-- มูลค่าสินค้า เฉพาะที่มีภาษี
            //--- คำนวนภาษี หากมีส่วนลดท้ายบิล
            //--- เฉลี่ยส่วนลดออกให้ทุกรายการ โดยเอาส่วนลดท้ายบิล(จำนวนเงิน)/มูลค่าสินค้าก่อนส่วนลด
            //--- ได้มูลค่าส่วนลดท้ายบิลที่เฉลี่ยนแล้ว ต่อ บาท เช่น หารกันมาแล้ว ได้ 0.16 หมายถึงทุกๆ 1 บาท จะลดราคา 0.16 บาท
            $everageBillDisc = $total > 0 ? $billDiscAmount/$total : 0;
            //everageBillDisc = roundNumber(everageBillDisc, 2); //-- ไม่ต้องปัดเศษ

            //--- นำผลลัพธ์ข้างบนมาคูณ กับ มูลค่าที่ต้องคิดภาษี (ตัวที่ไม่มีภาษีไม่เอามาคำนวณ)
            //--- จะได้มูลค่าส่วนลดที่ต้องไปลบออกจากมูลค่าสินค้าที่ต้องคิดภาษี
            $totalDiscTax = round($amountBeforeDiscWithTax * $everageBillDisc, 2);

            $amountToPayTax = round($amountBeforeDiscWithTax - $totalDiscTax, 2);

            $taxAmount = $vat_type == 'I' ? $amountToPayTax - ($amountToPayTax / ($taxRate + 1)) : ($vat_type == 'E' ? $amountToPayTax * $taxRate : 0.00);
            $taxAmount = round($taxAmount, 2);

            $amountAfterDiscAndTax = $vat_type == 'I' ? round($amountAfterDisc + $rounding, 2) : round($amountAfterDisc + $taxAmount + $rounding, 2);

            $totalBfDisc = $total;
            $vatSum = $taxAmount;
            $docTotal = $amountAfterDiscAndTax;
          }

          $ds = array(
          'pos' => $pos,
          'order' => $order,
          'details' => $details,
          'holdBillCount' => $this->order_pos_model->count_hold_bills($pos->id),
          'totalQty' => $totalQty,
          'totalBfDisc' => $totalBfDisc,
          'vatSum' => $vatSum,
          'amountAfterDisc' => $amountAfterDisc,
          'amountAfterDiscAndTax' => $amountAfterDiscAndTax,
          'downPayment' => $downPayment,
          'docTotal' => $docTotal - $downPayment
          );

          $this->load->view('order_pos/pos', $ds);

        }

      }
    }
    else
    {
      $this->error_page();
    }
	}



	public function hold_bill()
	{
		$sc = TRUE;

		$temp_id = $this->input->post('temp_id');
    $pos_id = $this->input->post('pos_id');

		if( ! empty($temp_id) && ! empty($pos_id))
		{
			$arr = array(
        'status' => 1  //--- 0 = active, 1 = hold
      );

      if( ! $this->order_pos_model->update_temp($temp_id, $arr))
      {
        $sc = FALSE;
        $this->error = "พักบิลไม่สำเร็จ";
      }
		}
		else
		{
			$sc = FALSE;
			set_error('required');
		}

		$this->_response($sc);
	}




	public function get_hold_bills($pos_id)
	{
		$bills = $this->order_pos_model->get_hold_bills($pos_id);

		if( ! empty($bills) )
		{
			$ds = array();

			foreach($bills as $rs)
			{
				$arr = array(
					'temp_id' => $rs->id,
					'pos_id' => $rs->pos_id,
					'date_upd' => thai_date($rs->date_upd, TRUE)
				);

				array_push($ds, $arr);
			}
		}

    $arr = array(
      'count' => empty($bills) ? 0 : count($bills),
      'data' => empty($bills) ? NULL : $ds
    );

    echo json_encode($arr);
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


  public function create_delivery()
  {
    $this->load->model('account/consign_order_model');

    $sc = TRUE;

    $data = json_decode($this->input->post('data'));

    if( ! empty($data))
    {
      $shop = $this->shop_model->get_by_id($data->shop_id);

      if( ! empty($shop))
      {
        if( ! empty($data->bills))
        {
          $details = $this->order_pos_model->get_details_in_bills($data->bills);

          if( ! empty($details))
          {
            //--- create WM
            $code = $this->get_new_wm_code();

            $bookcode = getConfig('BOOK_CODE_CONSIGN_SOLD');
            $dd = $details[0];
            $sale_code = count($data->bills) == 1 ? $dd->sale_id : -1;

            $arr = array(
              'code' => $code,
              'bookcode' => $bookcode,
              'customer_code' => $shop->customer_code,
              'customer_name' => $shop->customer_name,
              'zone_code' => $shop->zone_code,
              'zone_name' => $shop->zone_name,
              'warehouse_code' => $shop->warehouse_code,
              'remark' => 'Generate by '.$shop->name,
              'date_add' => now(),
              'shipped_date' => now(),
              'status' => 0,
              'ref_type' => 4,
              'sale_code' => $sale_code,
              'user' => $this->_user->uname
            );

            $this->db->trans_begin();

            if(! $this->consign_order_model->add($arr))
            {
              $sc = FALSE;
              $this->error = "เพิ่มเอกสารไม่สำเร็จ";
            }


            if($sc === TRUE)
            {
              foreach($details as $rs)
              {
                if($sc === FALSE)
                {
                  break;
                }

                $arr = array(
                  'consign_code' => $code,
                  'style_code' => $rs->style_code,
                  'product_code' => $rs->product_code,
                  'product_name' => $rs->product_name,
                  'cost' => $rs->cost,
                  'price' => $rs->price,
                  'qty' => $rs->qty,
                  'discount' => $rs->discount_label,
                  'discount_amount' => $rs->discount_amount,
                  'amount' => $rs->total_amount,
                  'status' => 0,
                  'ref_code' => $rs->order_code,
                  'so_code' => $rs->so_code,
                  'channels_code' => $rs->channels_code,
                  'sale_code' => $rs->sale_id,
                  'input_type' => 4, //---- 1 = key in , 2 = consolidate, 3 = excel, 4 = POS
                  'is_count' => $rs->is_count
                );

                if( ! $this->consign_order_model->add_detail($arr))
                {
                  $sc = FALSE;
                  $this->error = "เพิ่มรายการสินค้าเข้าเอกสารไม่สำเร็จ - {$rs->product_code} : {$rs->order_code}";
                }
              }
            }

            //---- Update bill status
            if($sc === TRUE)
            {
              $arr = array('status' => 'C');

              if( ! $this->order_pos_model->update_bills_details($data->bills, $arr))
              {
                $sc = FALSE;
                $this->error = "Failed to update pos items status";
              }

              if($sc === TRUE)
              {
                $arr = array('status' => 'C', 'ref_code' => $code);

                if( ! $this->order_pos_model->update_bills($data->bills, $arr))
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
          $this->error = "ไม่พบบิลขาย";
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



  public function create_delivery_by_channels_and_sale()
  {
    $this->load->model('account/consign_order_model');
    $this->load->model('masters/channels_model');
    $this->load->model('inventory/delivery_order_model');
    $this->load->model('inventory/movement_model');
    $this->load->library('export');

    $sc = TRUE;

    $data = json_decode($this->input->post('data'));

    if( ! empty($data))
    {
      $shop = $this->shop_model->get_by_id($data->shop_id);

      if( ! empty($shop))
      {
        if( ! empty($data->bills))
        {
          $cs = $this->order_pos_model->group_channels_and_sales_in_bills($data->bills);

          if( ! empty($cs))
          {
            foreach($cs as $ra)
            {
              $details = $this->order_pos_model->get_details_in_bills_group_by_channels_and_sales($data->bills, $ra->channels_code, $ra->sale_id);
              $bills = []; //--- รวม id ที่บิลไว้สำหรับ update status

              if( ! empty($details))
              {
                //--- create WM
                $code = $this->get_new_wm_code();

                $bookcode = getConfig('BOOK_CODE_CONSIGN_SOLD');

                $arr = array(
                  'code' => $code,
                  'bookcode' => $bookcode,
                  'customer_code' => $shop->customer_code,
                  'customer_name' => $shop->customer_name,
                  'channels_code' => $ra->channels_code,
                  'zone_code' => $shop->zone_code,
                  'zone_name' => $shop->zone_name,
                  'warehouse_code' => $shop->warehouse_code,
                  'remark' => 'Generate by '.$shop->name,
                  'date_add' => now(),
                  'shipped_date' => now(),
                  'status' => 1,
                  'ref_type' => 4,
                  'sale_code' => $ra->sale_id,
                  'user' => $this->_user->uname
                );

                $this->db->trans_begin();

                if(! $this->consign_order_model->add($arr))
                {
                  $sc = FALSE;
                  $this->error = "เพิ่มเอกสารไม่สำเร็จ";
                }

                if($sc === TRUE)
                {
                  foreach($details as $rs)
                  {
                    if($sc === FALSE)
                    {
                      break;
                    }

                    $arr = array(
                      'consign_code' => $code,
                      'style_code' => $rs->style_code,
                      'product_code' => $rs->product_code,
                      'product_name' => $rs->product_name,
                      'cost' => $rs->cost,
                      'price' => $rs->price,
                      'qty' => $rs->qty,
                      'discount' => $rs->discount_label,
                      'discount_amount' => $rs->discount_amount,
                      'amount' => $rs->total_amount,
                      'status' => 1,
                      'ref_code' => $rs->order_code,
                      'so_code' => $rs->so_code,
                      'channels_code' => $ra->channels_code,
                      'sale_code' => $ra->sale_id,
                      'input_type' => 4, //---- 1 = key in , 2 = consolidate, 3 = excel, 4 = POS
                      'is_count' => $rs->is_count
                    );

                    if( ! $this->consign_order_model->add_detail($arr))
                    {
                      $sc = FALSE;
                      $this->error = "เพิ่มรายการสินค้าเข้าเอกสารไม่สำเร็จ - {$rs->product_code} : {$rs->order_code}";
                    }
                    else
                    {
                      $bills[$rs->order_id] = $rs->order_id;
                    }

                    //--- บันทึกขาย order_sold
                    if($sc === TRUE)
                    {
                      $final_price = $rs->total_amount/$rs->qty;
                      //--- ข้อมูลสำหรับบันทึกยอดขาย
                      $arr = array(
                        'reference' => $code,
                        'role'   => 'M', ///--- ตัดยอดฝากขาย(shop)
                        'channels_code' => $ra->channels_code,
                        'product_code'  => $rs->product_code,
                        'product_name'  => $rs->product_name,
                        'product_style' => $rs->style_code,
                        'cost'  => $rs->cost,
                        'price'  => $rs->price,
                        'sell'  => $final_price,
                        'qty'   => $rs->qty,
                        'discount_label'  => $rs->discount_label,
                        'discount_amount' => $rs->discount_amount,
                        'total_amount'   => $rs->total_amount,
                        'total_cost'   => $rs->cost * $rs->qty,
                        'margin'  =>  ($final_price * $rs->qty) - ($rs->cost * $rs->qty),
                        'id_policy'   => NULL,
                        'id_rule'     => NULL,
                        'customer_code' => $shop->customer_code,
                        'customer_ref' => NULL,
                        'sale_code'   => $ra->sale_id,
                        'user' => $this->_user->uname,
                        'date_add'  => now(),
                        'zone_code' => $shop->zone_code,
                        'warehouse_code'  => $shop->warehouse_code,
                        'update_user' => $this->_user->uname,
                        'budget_code' => NULL,
                        'is_count' => $rs->is_count,
                        'ref_code' => $rs->order_code,
                        'so_code' => $rs->so_code
                      );

                      //--- 1.บันทึกขาย
                      if(! $this->delivery_order_model->sold($arr))
                      {
                        $sc = FALSE;
                        $this->error = 'บันทึกขายไม่สำเร็จ';
                        break;
                      }

                      //--- 2. update movement
                      $arr = array(
                        'reference' => $code,
                        'warehouse_code' => $shop->warehouse_code,
                        'zone_code' => $shop->zone_code,
                        'product_code' => $rs->product_code,
                        'move_in' => 0,
                        'move_out' => $rs->qty,
                        'date_add' => now()
                      );

                      if(! $this->movement_model->add($arr))
                      {
                        $sc = FALSE;
                        $this->error = 'บันทึก movement ขาออกไม่สำเร็จ';
                        break;
                      }
                    } //--- order_sold
                  } //--- foreach details
                } //--- add details

                //---- Update bill status
                if($sc === TRUE)
                {
                  $arr = array('status' => 'C');

                  if( ! $this->order_pos_model->update_bills_details($bills, $arr))
                  {
                    $sc = FALSE;
                    $this->error = "Failed to update pos items status";
                  }

                  if($sc === TRUE)
                  {
                    $arr = array('status' => 'C', 'ref_code' => $code);

                    if( ! $this->order_pos_model->update_bills($bills, $arr))
                    {
                      $sc = FALSE;
                      $this->error = "Failed to update bills status";
                    }
                  }
                }

                if($sc === TRUE)
                {
                  $this->db->trans_commit();

                  //--- export to SAP TEMP
                  $this->export->export_consign_order($code);
                }
                else
                {
                  $this->db->trans_rollback();
                }

              } //--- if( ! empty($details ))

            } //--- end foreach
          }
        }
        else
        {
          $sc = FALSE;
          $this->error = "ไม่พบบิลขาย";
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
      'message' => $sc === TRUE ? 'success' : $this->error
    );

    echo json_encode($arr);
  }


  public function get_new_wm_code($date = NULL)
	{
    $date = empty($date) ? date('Y-m-d') : $date;
    $Y = date('y', strtotime($date));
    $M = date('m', strtotime($date));
    $prefix = getConfig('PREFIX_CONSIGN_SOLD');
    $run_digit = getConfig('RUN_DIGIT_CONSIGN_SOLD');
    $pre = $prefix .'-'.$Y.$M;
    $code = $this->consign_order_model->get_max_code($pre);

    if(!empty($code))
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


	public function add_to_order()
	{
		$sc = TRUE;

		$this->load->model('masters/products_model');
		$this->load->model('orders/discount_model');

		$order_code = trim($this->input->get('order_code'));
		$product_code = trim($this->input->get('product_code'));
		$customer_code = trim($this->input->get('customer_code'));
		$payment_code = trim($this->input->get('payment_code'));
		$zone_code = trim($this->input->get('zone_code'));
		$channels_code = trim($this->input->get('channels_code'));

		$item = $this->products_model->get_product_by_barcode($product_code); //--- barcode or code

		if(!empty($item))
		{
			$order = $this->order_pos_model->get($order_code);

			if(!empty($order))
			{
				$detail = $this->order_pos_model->get_order_detail_by_product($order_code, $product_code);

				if(empty($detail))
				{
					$qty = 1;
					$discount = $this->discount_model->get_item_discount($item->code, $customer_code, $qty, $payment_code, $channels_code, date('Y-m-d'));
					$vat_rate = $this->products_model->get_vat_rate($item->code);
					$item_disc_amount = empty($discount['amount']) ? 0 : round($discount['amount'] / $qty, 2);
					$sell_price = $item->price - $item_disc_amount;
					$total_amount = round($sell_price * $qty, 2);

					$arr = array(
						'item_type' => $item->count_stock ? 'I' : 'S',
						'order_code' => $order_code,
						'product_code' => $item->code,
						'product_name' => $item->name,
						'unit_code' => $item->unit_code,
						'qty' => $qty,
						'std_price' => $item->price,
						'price' => $item->price,
						'discount_label' => discountLabel($discount['discLabel1'], $discount['discLabel2'], $discount['discLabel3']),
						'discount_amount' => $discount['amount'],
						'final_price' => $sell_price,
						'total_amount' => $total_amount,
						'vat_rate' => $vat_rate,
						'vat_amount' => $total_amount * ($vat_rate * 0.01),
						'is_count' => $item->count_stock,
						'zone_code' => $zone_code,
						'status' => 0
					);

					$id = $this->order_pos_model->add_detail($arr);

					if(!empty($id))
					{
						$arr['id'] = $id;

						echo json_encode($arr);
					}
					else
					{
						$error = $this->db->error();
						echo "Insert Item failed : ".$error['message'];
					}
				}
				else
				{
					//---- update detail
					$qty = $detail->qty + 1;
					$discount = $this->discount_model->get_item_discount($item->code, $customer_code, $qty, $payment_code, $channels_code, date('Y-m-d'));
					$vat_rate = $this->products_model->get_vat_rate($item->code);
					$item_disc_amount = empty($discount['amount']) ? 0 : round($discount['amount'] / $qty, 2);
					$sell_price = $item->price - $item_disc_amount;
					$total_amount = round($sell_price * $qty, 2);

					$arr = array(
						'qty' => $qty,
						'discount_label' => discountLabel($discount['discLabel1'], $discount['discLabel2'], $discount['discLabel3']),
						'discount_amount' => $discount['amount'],
						'final_price' => $sell_price,
						'total_amount' => $total_amount,
						'vat_rate' => $vat_rate,
						'vat_amount' => $total_amount * ($vat_rate * 0.01)
					);

					if($this->order_pos_model->update_detail($detail->id, $arr))
					{
						$arr = array(
							'id' => $detail->id,
							'item_type' => $item->count_stock ? 'I' : 'S',
							'order_code' => $order_code,
							'product_code' => $item->code,
							'product_name' => $item->name,
							'unit_code' => $item->unit_code,
							'qty' => $qty,
							'std_price' => $item->price,
							'price' => $item->price,
							'discount_label' => discountLabel($discount['discLabel1'], $discount['discLabel2'], $discount['discLabel3']),
							'discount_amount' => $discount['amount'],
							'final_price' => $sell_price,
							'total_amount' => $total_amount,
							'vat_rate' => $vat_rate,
							'vat_amount' => $total_amount * ($vat_rate * 0.01),
							'is_count' => $item->count_stock,
							'zone_code' => $zone_code,
							'status' => 0
						);

						echo json_encode($arr);
					}
					else
					{
						$error = $this->db->error();
						echo "Update item failed : ".$error['message'];
					}
				}
			}
			else
			{
				echo "Invalid Order code : {$order_code}";
			}
		}
		else
		{
			echo "No item found";
		}
	}



	public function update_item()
	{
		$sc = TRUE;
		$result = array();

		$this->load->helper('discount');

		$id = $this->input->post('id');
		$qty = $this->input->post('qty');
		$price = $this->input->post('price');
		$discount_label = trim($this->input->post('discount_label'));
    $is_edit = $this->input->post('is_edit');

		if( ! empty($id))
		{
			$detail = $this->order_pos_model->get_temp_detail_by_id($id);

			if(!empty($detail))
			{
				//-- discount_helper
				//-- return discount array per 1 item
				$discount = parse_discount_text($discount_label, $price);
				$sell_price = $price - $discount['discount_amount'];
				$total_amount = $sell_price * $qty;

				$arr = array(
					'qty' => $qty,
					'price' => $price,
					'discount_label' => $discount_label,
					'discount_amount' => $discount['discount_amount'] * $qty,
					'final_price' => $sell_price,
					'total_amount' => $total_amount,
					'vat_amount' => $total_amount * ($detail->vat_rate * 0.01),
          'is_edit' => $is_edit
				);

				if(! $this->order_pos_model->update_temp_detail($id, $arr))
				{
					$sc = FALSE;
					$this->error = "Update failed";
				}
			}
			else
			{
				$sc = FALSE;
				$this->error = "Item Not found";
			}
		}
		else
		{
			$sc = FALSE;
			set_error('required');
		}

		$this->_response($sc);
	}



	public function update_order()
	{
		$sc = TRUE;

		$order_code = trim($this->input->post('order_code'));
		$customer_code = trim($this->input->post('customer_code'));
		$customer_name = trim($this->input->post('customer_name'));
		$recal = $this->input->post('recal_discount');

		$arr = array(
			'customer_code' => $customer_code,
			'customer_name' => $customer_name
		);

		if($this->order_pos_model->update($order_code, $arr))
		{
			if(!empty($recal))
			{
				$this->load->model('masters/products_model');
				$this->load->model('orders/discount_model');
				$this->load->helper('discount');
				$payment_code = $this->input->post('payment_code');
				$channels_code = $this->input->post('channels_code');

				$details = $this->order_pos_model->get_details($order_code);

				if(!empty($details))
				{
					foreach($details as $rs)
					{
						$qty = $rs->qty;
						$discount = $this->discount_model->get_item_discount($rs->product_code, $customer_code, $qty, $payment_code, $channels_code, date('Y-m-d'));
						$vat_rate = $this->products_model->get_vat_rate($rs->product_code);
						$item_disc_amount = empty($discount['amount']) ? 0 : round($discount['amount'] / $qty, 2);
						$sell_price = $rs->price - $item_disc_amount;
						$total_amount = round($sell_price * $qty, 2);

						$arr = array(
							'qty' => $qty,
							'discount_label' => discountLabel($discount['discLabel1'], $discount['discLabel2'], $discount['discLabel3']),
							'discount_amount' => $discount['amount'],
							'final_price' => $sell_price,
							'total_amount' => $total_amount,
							'vat_rate' => $vat_rate,
							'vat_amount' => $total_amount * ($vat_rate * 0.01)
						);
					}
				}
			}
		}
		else
		{
			$sc = FALSE;
			$this->error = "Update Order failed";
		}

		$this->response($sc);
	}


	public function bill($order_code)
	{
		if(!empty($order_code))
		{
			$this->load->model('masters/payment_methods_model');
			$this->title = "Invoice#{$order_code}";
			$order = $this->order_pos_model->get($order_code);
			$details = $this->order_pos_model->get_details($order_code);
			$shop = $this->shop_model->get($order->shop_id);
			$pos = $this->pos_model->get($order->pos_id);
			$payment = $this->payment_methods_model->get_name($order->payment_code);

			$ds = array(
				'shop' => $shop,
				'pos' => $pos,
				'order' => $order,
				'details' => $details,
				'pay_by' => $payment
			);

			$this->load->view('print/print_pos_bill', $ds);
		}
		else
		{
			$this->page_error();
		}

	}



	public function get_product_data()
	{
		$code = trim($this->input->get('product_code'));
		$zone_code = trim($this->input->get('zone_code'));

		if(! is_null($code) && $code != '')
		{
			$this->load->model('masters/products_model');
			$this->load->model('stock/stock_model');
			$this->load->helper('product_images');


			$item = $this->products_model->get_product_by_barcode($code);

      if(empty($item))
      {
        $item = $this->products_model->get($code);
      }

			if(!empty($item))
			{
				$stock = $this->stock_model->get_stock_zone($zone_code, $item->code);
				$image = get_product_image($item->code, 'default');

				$arr = array(
					'item_type' => $item->count_stock ? 'Item' : 'Service',
					'item_code' => $item->code,
					'item_name' => $item->name,
					'price' => number($item->price),
					'vat_rate' => $item->sale_vat_rate,
					'qty' => number($stock),
					'img' => $image
				);

				echo json_encode($arr);
			}
			else
			{
				echo "Product Not Found";
			}
		}
		else
		{
			echo "Missing required parameter : product code";
		}
	}


  public function search_bill_code($shop_id, $pos_id = NULL)
  {
    $ds = array();

    $code = trim($_REQUEST['term']);

    $this->db->select('code')->where('status !=', 'D')->where('shop_id', $shop_id);

    if( ! empty($pos_id))
    {
      $this->db->where('pos_id', $pos_id);
    }

    if($code != '*')
    {
      $this->db->like('code', $code);
    }

    $rs = $this->db->order_by('date_add', 'DESC')->limit(20)->get('order_pos');

    if($rs->num_rows() > 0)
    {
      foreach($rs->result() as $row)
      {
        $ds[] = $row->code;
      }
    }

    echo json_encode($ds);
  }


  public function search_so_code()
  {
    $ds = array();

    $code = trim($_REQUEST['term']);

    $this->db->select('code, customer_ref')->where('status', 'O');

    if($code != '*')
    {
      $this->db->like('code', $code);
    }

    $rs = $this->db->order_by('date_add', 'DESC')->limit(20)->get('sale_order');

    if($rs->num_rows() > 0)
    {
      foreach($rs->result() as $row)
      {
        $ds[] = $row->code .' | '.$row->customer_ref;
      }
    }

    echo json_encode($ds);
  }


  public function search_wo_code()
  {
    $ds = array();

    $code = trim($_REQUEST['term']);

    $this->db
    ->select('code, customer_ref')
    ->where('role', 'S')
    ->where('status', 1)
    ->where('state <=', 8);

    if($code != '*')
    {
      $this->db->like('code', $code);
    }

    $rs = $this->db->order_by('date_add', 'DESC')->limit(20)->get('orders');

    if($rs->num_rows() > 0)
    {
      foreach($rs->result() as $row)
      {
        $ds[] = $row->code .' | '.$row->customer_ref;
      }
    }

    echo json_encode($ds);
  }


  public function open_pos_round()
  {
    $this->load->model('orders/order_pos_round_model');

    $sc = TRUE;
    $pos_id = $this->input->post('pos_id');
    $amount = $this->input->post('amount');

    $pos = $this->pos_model->get_pos($pos_id);

    if( ! empty($pos))
    {
      if(empty($pos->round_id))
      {
        //-- check open round
        $round = $this->order_pos_round_model->get_open_round_by_pos_id($pos_id);

        if(empty($round))
        {
          $code = $this->get_new_round_code($pos->code);

          $arr = array(
            'code' => $code,
            'pos_id' => $pos->id,
            'shop_id' => $pos->shop_id,
            'open_cash' => $amount,
            'open_user' => $this->_user->uname,
            'open_date' => now()
          );

          $this->db->trans_begin();

          $id = $this->order_pos_round_model->add($arr);

          if($id)
          {
            if( ! $this->pos_model->update_by_id($pos->id, array('round_id' => $id, 'cash_amount' => $amount)))
            {
              $sc = FALSE;
              $this->error = "Failed to update sales shift";
            }

            if($sc === TRUE)
            {
              //--- add movement
              $arr = array(
                'code' => $code,
                'type' => 'RO', //--- เปิดการขาย ยอดเงินเข้า
                'shop_id' => $pos->shop_id,
                'pos_id' => $pos->id,
                'amount' => $amount,
                'payment_role' => 1, //--- 1 = เงินสด, 2 = โอน, 3 = บัตรเครดิต
                'user' => $this->_user->uname,
                'round_id' => $id
              );

              if( ! $this->pos_sales_movement_model->add($arr))
              {
                $sc = FALSE;
                $this->error = "Failed to insert sales movement";
              }
            }
          }
          else
          {
            $sc = FALSE;
            $this->error = "เปิดการขายไม่สำเร็จ";
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
      $this->error = "Invalid POS ID";
    }

    echo $sc === TRUE ? 'success' : $this->error;
  }


  public function close_pos_round()
  {
    $this->load->model('orders/order_pos_round_model');
    $this->load->model('orders/order_pos_return_model');

    $sc = TRUE;
    $pos_id = $this->input->post('pos_id');
    $amount = $this->input->post('amount');
    $close_date = now();
    $round_total = 0;

    $pos = $this->pos_model->get_pos($pos_id);

    if( ! empty($pos))
    {
      if( ! empty($pos->round_id))
      {
        //-- check open round
        $round = $this->order_pos_round_model->get($pos->round_id);

        if( ! empty($round) && $round->status == 'O')
        {
          //--- get summary
          $sm = $this->get_round_summary($pos->id, $pos->round_id);
          $round_total = $sm->totalAmount + $sm->totalDownPayment + ($sm->totalReturn * -1); //--- ยอดรวมทั้งรอบ

          $arr = array(
            'status' => 'C',
            'close_cash' => $amount,
            'close_user' => $this->_user->uname,
            'close_date' => $close_date,
            'down_cash' => $sm->totalCashDownPayment,
            'down_transfer' => $sm->totalTransferDownPayment,
            'down_card' => $sm->totalCardDownPayment,
            'cash_in' => $sm->totalCashIn, //--- นำเงินเข้าลิ้นชัก
            'cash_out' => $sm->totalCashOut, //--- นำเงินออกจากลิ้นชัก
            'return_cash' => $sm->totalReturnCash * -1, ///---- คืนเงินสด
            'return_transfer' => $sm->totalReturnTransfer * -1, //--- คืนเงินด้วยการโอน
            'total_cash' => $sm->totalCash, //-- pos total cash without down payment
            'total_transfer' => $sm->totalTransfer, //--- pos total transfer without down payment
            'total_card' => $sm->totalCard, //-- pos total credit card without down payment
            'round_total' => $round_total
          );

          $this->db->trans_begin();

          if($this->order_pos_round_model->update($pos->round_id, $arr))
          {
            //---- clear ยอดใน ลิ้นชักและเอา round id ออก เพื่อให้เปิดใหม่อีกที
            if( ! $this->pos_model->update_by_id($pos->id, array('round_id' => NULL, 'cash_amount' => 0)))
            {
              $sc = FALSE;
              $this->error = "Failed to update sales shift";
            }

            if($sc === TRUE)
            {
              //--- add movement
              $arr = array(
                'code' => $round->code,
                'type' => 'RC', //--- เปิดการขาย ยอดเงินเข้า
                'shop_id' => $pos->shop_id,
                'pos_id' => $pos->id,
                'amount' => $amount * -1,
                'payment_role' => 1, //--- 1 = เงินสด, 2 = โอน, 3 = บัตรเครดิต
                'user' => $this->_user->uname,
                'round_id' => $pos->round_id
              );

              if( ! $this->pos_sales_movement_model->add($arr))
              {
                $sc = FALSE;
                $this->error = "Failed to insert sales movement";
              }
            }
          }
          else
          {
            $sc = FALSE;
            $this->error = "เปิดการขายไม่สำเร็จ";
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
          $this->error = "Sales shift already closed";
        }
      }
      else
      {
        $sc = FALSE;
        $this->error = "Sales shift already closed";
      }
    }
    else
    {
      $sc = FALSE;
      $this->error = "Invalid POS ID";
    }

    $arr = array(
      'status' => $sc === TRUE ? 'success' : 'failed',
      'message' => $sc === TRUE ? 'success' : $this->error,
      'round_id' => $sc === TRUE ? $round->id : NULL,
      'code' => $sc === TRUE ? $round->code : NULL,
      'open_date' => $sc === TRUE ? thai_date($round->open_date, TRUE, '/') : NULL,
      'close_date' => $sc === TRUE ? thai_date($close_date, TRUE, '/') : NULL,
      'total_round' => $sc === TRUE ? $round_total : 0
    );

    echo json_encode($arr);
  }


  public function get_round_summary($pos_id, $round_id)
  {
    //---- total order amount
    $arr = array(
      'totalAmount' => $this->order_pos_model->get_sum_amount_by_round_id($pos_id, $round_id),
      'totalDownPayment' => $this->order_down_payment_model->get_sum_amount_by_round_id($pos_id, $round_id),
      'totalReturn' => $this->order_pos_return_model->get_sum_amount_by_round_id($pos_id, $round_id),
      'totalCashDownPayment' => $this->order_down_payment_model->get_sum_role_amount_by_round_id($pos_id, 1, $round_id),
      'totalTransferDownPayment' => $this->order_down_payment_model->get_sum_role_amount_by_round_id($pos_id, 2, $round_id),
      'totalCardDownPayment' => $this->order_down_payment_model->get_sum_role_amount_by_round_id($pos_id, 3, $round_id),
      'totalCash' => $this->order_pos_model->get_sum_role_amount_by_round_id($pos_id, 1, $round_id),
      'totalTransfer' => $this->order_pos_model->get_sum_role_amount_by_round_id($pos_id, 2, $round_id),
      'totalCard' => $this->order_pos_model->get_sum_role_amount_by_round_id($pos_id, 3, $round_id),
      'totalReturnCash' => $this->order_pos_return_model->get_sum_role_amount_by_round_id($pos_id, 1, $round_id),
      'totalReturnTransfer' => $this->order_pos_return_model->get_sum_role_amount_by_round_id($pos_id, 2, $round_id),
      'totalCashIn' => $this->pos_sales_movement_model->get_sum_cash_in_by_round_id($pos_id, $round_id),
      'totalCashOut' => $this->pos_sales_movement_model->get_sum_cash_out_by_round_id($pos_id, $round_id)
    );

    return (object) $arr;
  }

  public function print_pos_round($pos_id, $round_id)
  {
    $this->load->model('orders/order_pos_round_model');
    $pos = $this->pos_model->get_pos($pos_id);
    $round = $this->order_pos_round_model->get($round_id);

    if( ! empty($pos) && ! empty($round))
    {
      $this->load->library('xprinter');
      $this->load->helper('print');

      $ds = array(
        'pos' => $pos,
        'round' => $round
      );

      $this->load->view('print/print_pos_round', $ds);
    }
    else
    {
      $this->page_error();
    }
  }


  public function get_new_round_code($prefix)
  {
    $D = date('Ymd');
    $run_digit = 2;
    $pre = $prefix.'-'.$D.'-';

    $code = $this->order_pos_round_model->get_max_code($pre);

    if(! empty($code))
    {
      $run_no = mb_substr($code, ($run_digit*-1), NULL, 'UTF-8') + 1;
      $new_code = $pre . sprintf('%0'.$run_digit.'d', $run_no);
    }
    else
    {
      $new_code = $pre . sprintf('%0'.$run_digit.'d', '01');
    }

    return $new_code;
  }


	public function get_new_code($prefix, $run_digit = 4, $date = NULL)
	{
		$date = empty($date) ? date('Y-m-d') : ($date < '2020-01-01' ? date('Y-m-d') : $date);
		$Y = date('y', strtotime($date));
    $M = date('m', strtotime($date));
    $pre = $prefix .'-'.$Y.$M;
    $code = $this->order_pos_model->get_max_code($pre);

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


  public function get_new_return_code($prefix, $run_digit = 4, $date = NULL)
	{
		$date = empty($date) ? date('Y-m-d') : ($date < '2020-01-01' ? date('Y-m-d') : $date);
		$Y = date('y', strtotime($date));
    $M = date('m', strtotime($date));
    $pre = $prefix .'-'.$Y.$M;
    $code = $this->order_pos_return_model->get_max_code($pre);

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
      'bill_code',
      'bill_invoice_code',
      'bill_ref_code',
      'bill_so_code',
      'bill_channels',
      'bill_payment',
      'bill_status',
      'bill_from_date',
      'bill_to_date',
      'bill_pos_id',
      'bill_user',
      'bill_sale_id',
      'return_code',
      'return_order_code',
      'return_ref_code',
      'return_status',
      'return_from_date',
      'return_to_date',
      'down_code',
      'down_reference',
      'down_ref_code',
      'down_status',
      'down_from_date',
      'down_to_date'
    );

    clear_filter($filter);
  }

}

//--- End class
?>
