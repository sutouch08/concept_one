<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Orders extends PS_Controller
{
  public $menu_code = 'SOODSO';
	public $menu_group_code = 'SO';
  public $menu_sub_group_code = 'ORDER';
	public $title = 'ออเดอร์';
  public $filter;
  public $error;
	public $logs; //--- logs database;
  public $sync_chatbot_stock = FALSE;
  public $log_delete = TRUE;

  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'orders/orders';
    $this->load->model('orders/orders_model');
    $this->load->model('masters/channels_model');
    $this->load->model('masters/payment_methods_model');
    $this->load->model('masters/customers_model');
    $this->load->model('orders/order_state_model');
    $this->load->model('masters/product_tab_model');
    $this->load->model('stock/stock_model');
    $this->load->model('masters/product_style_model');
    $this->load->model('masters/products_model');
    $this->load->model('orders/discount_model');

    $this->load->helper('order');
    $this->load->helper('channels');
    $this->load->helper('saleman');
    $this->load->helper('payment_method');
    $this->load->helper('customer');
    $this->load->helper('users');
    $this->load->helper('state');
    $this->load->helper('product_images');
    $this->load->helper('discount');
    $this->load->helper('warehouse');

    $this->filter = getConfig('STOCK_FILTER');
  }


  public function index()
  {
    $filter = array(
      'code' => get_filter('code', 'order_code', ''),
			'so_code' => get_filter('so_code', 'so_code', ''),
      'customer' => get_filter('customer', 'order_customer', ''),
      'user' => get_filter('user', 'order_user', 'all'),
      'sale_code' => get_filter('sale_code', 'order_sale_code', 'all'),
      'reference' => get_filter('reference', 'order_reference', ''),
      'ship_code' => get_filter('shipCode', 'order_shipCode', ''),
      'channels' => get_filter('channels', 'order_channels', 'all'),
      'payment' => get_filter('payment', 'order_payment', 'all'),
      'from_date' => get_filter('fromDate', 'order_fromDate', ''),
      'to_date' => get_filter('toDate', 'order_toDate', ''),
      'warehouse' => get_filter('warehouse', 'order_warehouse', 'all'),
      'notSave' => get_filter('notSave', 'notSave', NULL),
      'onlyMe' => get_filter('onlyMe', 'onlyMe', NULL),
      'isExpire' => get_filter('isExpire', 'isExpire', NULL),
			'invoice_code' => get_filter('invoice_code', 'invoice_code', ''),
			'is_term' => get_filter('is_term', 'is_term', 'all')
    );

    $state = array(
      '1' => get_filter('state_1', 'state_1', 'N'),
      '2' => get_filter('state_2', 'state_2', 'N'),
      '3' => get_filter('state_3', 'state_3', 'N'),
      '4' => get_filter('state_4', 'state_4', 'N'),
      '5' => get_filter('state_5', 'state_5', 'N'),
      '6' => get_filter('state_6', 'state_6', 'N'),
      '7' => get_filter('state_7', 'state_7', 'N'),
      '8' => get_filter('state_8', 'state_8', 'N'),
      '9' => get_filter('state_9', 'state_9', 'N')
    );

    if($this->input->post('search'))
    {
      redirect($this->home);
    }
    else
    {
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

  		$segment  = 4; //-- url segment
  		$rows     = $this->orders_model->count_rows($filter, 'S');
  		//--- ส่งตัวแปรเข้าไป 4 ตัว base_url ,  total_row , perpage = 20, segment = 3
  		$init	    = pagination_config($this->home.'/index/', $rows, $perpage, $segment);
  		$orders   = $this->orders_model->get_list($filter, $perpage, $this->uri->segment($segment), 'S');

      $ds       = array();

      if(!empty($orders))
      {
        foreach($orders as $rs)
        {
          $rs->channels_name = $this->channels_model->get_name($rs->channels_code);
          $rs->payment_name  = $this->payment_methods_model->get_name($rs->payment_code);
          $rs->state_name    = get_state_name($rs->state);
          $rs->sale_name = get_sale_name($rs->sale_code);
        }
      }

      $filter['orders'] = $orders;
      $filter['state'] = $state;
      $filter['btn'] = $button;

  		$this->pagination->initialize($init);
      $this->load->view('orders/orders_list', $filter);
    }
  }



  //---- รายการรออนุมัติ
  public function get_un_approve_list()
  {
    $role = $this->input->get('role');
    $rows = $this->orders_model->count_un_approve_rows($role);
    $limit = empty($this->input->get('limit')) ? 10 : intval($this->input->get('limit'));
    $list = $this->orders_model->get_un_approve_list($role, $limit);


    $result_rows = empty($list) ? 0 :count($list);

    $ds = array();
    if(!empty($list))
    {
      foreach($list as $rs)
      {
        $arr = array(
          'date_add' => thai_date($rs->date_add),
          'code' => $rs->code,
          'customer' => $rs->customer_name,
          'empName' => $rs->empName
        );

        array_push($ds, $arr);
      }
    }

    $data = array(
      'result_rows' => $result_rows,
      'rows' => $rows,
      'data' => $ds
    );

    echo json_encode($data);
  }


  public function add_new()
  {
    $this->load->view('orders/orders_add');
  }


  public function is_exists_order($code, $old_code = NULL)
  {
    $exists = $this->orders_model->is_exists_order($code, $old_code);
    if($exists)
    {
      echo 'เลขที่เอกสารซ้ำ';
    }
    else
    {
      echo 'not_exists';
    }
  }


  public function add()
  {
    $sc = TRUE;
    $h = json_decode($this->input->post('data'));

    if( ! empty($h))
    {
      $this->load->model('inventory/invoice_model');
			$this->load->model('masters/warehouse_model');
			$this->load->model('masters/sender_model');
      $this->load->model('address/address_model');


      $book_code = getConfig('BOOK_CODE_ORDER');
      $date_add = db_date($h->date_add, TRUE);

      $code = $this->get_new_code($date_add);

      $customer = $this->customers_model->get($h->customer_code);

      $role = 'S'; //--- S = ขาย

      $has_term = $h->is_term == 1 ? TRUE : FALSE;
      //--- check over due
      $is_strict = getConfig('STRICT_OVER_DUE') == 1 ? TRUE : FALSE;

      $overDue = $is_strict ? $this->invoice_model->is_over_due($h->customer_code) : FALSE;

      //--- ถ้ามียอดค้างชำระ และ เป็นออเดอร์แบบเครดิต
      //--- ไม่ให้เพิ่มออเดอร์
      if($overDue && $has_term && ! $customer->skip_overdue)
      {
        $sc = FALSE;
        $this->error = 'มียอดค้างชำระเกินกำหนดไม่อนุญาติให้ขาย';
      }

      if($sc === TRUE)
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
          'reference' => get_null($h->reference),
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
          'channels_code' => $h->channels_code,
          'payment_code' => NULL,
          'warehouse_code' => $h->warehouse_code,
          'sale_code' => $h->sale_code,
          'is_term' =>$h->is_term,
          'user' => $this->_user->uname,
          'remark' => get_null(addslashes($h->remark)),
					'id_address' => $id_address,
					'id_sender' => $this->sender_model->get_main_sender($customer->code)
        );

        if( ! $this->orders_model->add($ds))
        {
          $sc = FALSE;
          $this->error = "เพิ่มเอกสารไม่สำเร็จ กรุณาลองใหม่อีกครั้ง";
        }
        else
        {
          $arr = array(
            'order_code' => $code,
            'state' => 1,
            'update_user' => get_cookie('uname')
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


  public function add_detail($order_code)
  {
    $auz = getConfig('ALLOW_UNDER_ZERO');
    $overStock = getConfig('ORDER_OVER_STOCK') == 1 ? TRUE : FALSE;
    $result = TRUE;
    $err = "";
    $err_qty = 0;
    $data = $this->input->post('data');
    $order = $this->orders_model->get($order_code);

    if(!empty($data))
    {
      foreach($data as $rs)
      {
        $code = $rs['code']; //-- รหัสสินค้า
        $qty = $rs['qty'];
        $item = $this->products_model->get($code);

        if( $qty > 0 && ! empty($item))
        {
          $qty = ceil($qty);

          //---- ยอดสินค้าที่่สั่งได้
          $sumStock = $this->get_sell_stock($item->code, $order->warehouse_code);

          //--- ถ้ามีสต็อกมากว่าที่สั่ง หรือ เป็นสินค้าไม่นับสต็อก
          if( $sumStock >= $qty OR $item->count_stock == 0 OR $auz == 1 OR $overStock == TRUE)
          {

            //---- ถ้ายังไม่มีรายการในออเดอร์
            //--- อาจจะได้มากกกว่า 1 บรรทัด แต่จะเอามาแค่บรรทัดเดียว
            $detail = $this->orders_model->get_exists_detail($order_code, $item->code, $item->price);

            if(empty($detail))
            {
              //---- คำนวณ ส่วนลดจากนโยบายส่วนลด
              $discount = array(
                'amount' => 0,
                'id_rule' => NULL,
                'discLabel1' => 0,
                'discLabel2' => 0,
                'discLabel3' => 0
              );

              if($order->role == 'S')
              {
                $discount = $this->discount_model->get_item_discount($item->code, $order->customer_code, $qty, $order->payment_code, $order->channels_code, $order->date_add, $order->code);
              }

              if($order->role == 'C' OR $order->role == 'N')
              {
                $gp = $order->gp;
                //------ คำนวณส่วนลดใหม่
      					$step = explode('+', $gp);
      					$discAmount = 0;
      					$discLabel = array(0, 0, 0);
      					$price = $item->price;
      					$i = 0;
      					foreach($step as $discText)
      					{
      						if($i < 3) //--- limit ไว้แค่ 3 เสต็ป
      						{
      							$disc = explode('%', $discText);
      							$disc[0] = floatval(trim($disc[0])); //--- ตัดช่องว่างออก
      							$amount = count($disc) == 1 ? $disc[0] : $price * ($disc[0] * 0.01); //--- ส่วนลดต่อชิ้น
      							$discLabel[$i] = count($disc) == 1 ? $disc[0] : $disc[0].'%';
      							$discAmount += $amount;
      							$price -= $amount;
      						}

      						$i++;
      					}

                $total_discount = $qty * $discAmount; //---- ส่วนลดรวม
      					//$total_amount = ( $qty * $price ) - $total_discount; //--- ยอดรวมสุดท้าย
                $discount['amount'] = $total_discount;
                $discount['discLabel1'] = $discLabel[0];
                $discount['discLabel2'] = $discLabel[1];
                $discount['discLabel3'] = $discLabel[2];
              }

              $arr = array(
                      "id_order" =>  $order->id,
                      "order_code"	=> $order_code,
                      "style_code"		=> $item->style_code,
                      "product_code"	=> $item->code,
                      "product_name"	=> addslashes($item->name),
                      "cost"  => $item->cost,
                      "price"	=> $item->price,
                      "qty"		=> $qty,
                      "discount1"	=> $discount['discLabel1'],
                      "discount2" => $discount['discLabel2'],
                      "discount3" => $discount['discLabel3'],
                      "discount_amount" => $discount['amount'],
                      "total_amount"	=> ($item->price * $qty) - $discount['amount'],
                      "vat_code" => $item->sale_vat_code,
                      "vat_rate" => $item->sale_vat_rate,
                      "id_rule"	=> get_null($discount['id_rule']),
                      "is_count" => $item->count_stock
                    );

              if( $this->orders_model->add_detail($arr) === FALSE )
              {
                $result = FALSE;
                $error = "Error : Insert fail";
                $err_qty++;
              }
            }
            else  //--- ถ้ามีรายการในออเดอร์อยู่แล้ว
            {
              $qty			= $qty + $detail->qty;

              $discount = array(
                'amount' => 0,
                'id_rule' => NULL,
                'discLabel1' => 0,
                'discLabel2' => 0,
                'discLabel3' => 0
              );

              //---- คำนวณ ส่วนลดจากนโยบายส่วนลด
              if($order->role == 'S')
              {
                $discount 	= $this->discount_model->get_item_discount($item->code, $order->customer_code, $qty, $order->payment_code, $order->channels_code, $order->date_add, $order->code);
              }

              $arr = array(
                "qty"		=> $qty,
                "discount1"	=> $discount['discLabel1'],
                "discount2" => $discount['discLabel2'],
                "discount3" => $discount['discLabel3'],
                "discount_amount" => $discount['amount'],
                "total_amount"	=> ($item->price * $qty) - $discount['amount'],
                "id_rule"	=> get_null($discount['id_rule']),
                "valid" => 0,
                "valid_qc" => 0
              );

              if( $this->orders_model->update_detail($detail->id, $arr) === FALSE )
              {
                $result = FALSE;
                $error = "Error : Update Fail";
                $err_qty++;
              }
            }	//--- end if isExistsDetail
          }
          else 	// if getStock
          {
            $result = FALSE;
            $error = "Error : สินค้าไม่เพียงพอ : {$item->code}";
          } 	//--- if getStock
        }	//--- if qty > 0
      }

      if($result === TRUE)
      {
        $doc_total = $this->orders_model->get_order_total_amount($order_code);

        $arr = array(
        'doc_total' => $doc_total,
        'TotalBalance' => $doc_total,
        'status' => 0
        );

        $this->orders_model->update($order_code, $arr);
      }

      if($result === TRUE)
      {
        $this->orders_model->set_status($order_code, 0);
      }
    }

    echo $result === TRUE ? 'success' : ( $err_qty > 0 ? $error.' : '.$err_qty.' item(s)' : $error);
  }


  public function add_order_row()
  {
    $sc = TRUE;
    $ds = array();
    $auz = getConfig('ALLOW_UNDER_ZERO');
    $overStock = getConfig('ORDER_OVER_STOCK') == 1 ? TRUE : FALSE;
    $data = json_decode($this->input->post('data'));

    if( ! empty($data))
    {
      $order = $this->orders_model->get($data->order_code);

      if( ! empty($order))
      {
        if($order->state == 1)
        {
          $vat_type = $data->vat_type == 'N' ? 'I' : $data->vat_type; //$order->vat_type == 'N' ? 'I' : $order->vat_type;

          $item = $this->products_model->get($data->product_code);

          if( ! empty($item))
          {
            $qty = ceil($data->qty);

            $sumStock = ($item->count_stock == 0 OR $auz == 1 OR $overStock == TRUE) ? 100000 : $this->get_sell_stock($item->code, $order->warehouse_code);

            if($sumStock >= $qty)
            {
              //--- ชนิด VAT   E = Exclude, I = Include, N = No VAT
              //--- แต่ถ้าชนิด VAT = N จะคำนวนแบบ I
              $total_amount = $qty * $item->price;

              $discount = array(
                'amount' => 0,
                'id_rule' => NULL,
                'discLabel1' => 0,
                'discLabel2' => 0,
                'discLabel3' => 0
              );

              $discount = $this->discount_model->get_item_discount($item->code, $order->customer_code, $qty, $order->payment_code, $order->channels_code, $order->date_add, $order->code);

              $arr = array(
                "id_order" =>  $order->id,
                "order_code"	=> $order->code,
                "style_code"		=> $item->style_code,
                "product_code"	=> $item->code,
                "product_name"	=> addslashes($item->name),
                "cost"  => $item->cost,
                "price"	=> $item->price,
                "qty"		=> $qty,
                "discount1"	=> $discount['discLabel1'],
                "discount2" => $discount['discLabel2'],
                "discount3" => $discount['discLabel3'],
                "discount_amount" => $discount['amount'],
                "total_amount"	=> ($item->price * $qty) - $discount['amount'],
                "vat_code" => $item->sale_vat_code,
                "vat_rate" => $item->sale_vat_rate,
                "id_rule"	=> get_null($discount['id_rule']),
                "vat_amount" => get_vat_amount($total_amount, $item->sale_vat_rate, $vat_type),
                "is_count" => $item->count_stock
              );

              $id = $this->orders_model->add_detail($arr);

              if( ! $id)
              {
                $sc = FALSE;
                $this->error = "Failed to insert item";
              }

              if($sc === TRUE)
              {
                $doc_total = $this->orders_model->get_order_total_amount($order->code);

                $details = $this->orders_model->get_details($order->code);
                $totalAfDisc = 0;
                $docTotal = 0;
                $vatSum = 0;
                $billDiscAmount = $order->bDiscText > 0 ? $doc_total * ($order->bDiscText * 0.01) : $order->bDiscAmount;
                $avgBillDiscAmount = $billDiscAmount > 0 ? ($doc_total > 0 ? $billDiscAmount/$doc_total : 0) : 0;

                if( ! empty($details))
                {
                  foreach($details as $rs)
                  {
                    if($avgBillDiscAmount > 0) {
                      $sumBillDiscAmount = $avgBillDiscAmount * $rs->total_amount;
                      $totalAfDisc = $rs->total_amount - $sumBillDiscAmount;
                      $vatAmount = round(get_vat_amount($totalAfDisc, $rs->vat_rate, $vat_type), 6);

                      $arr = array(
                        'vat_type' => $data->vat_type,
                        'avgBillDiscAmount' => $avgBillDiscAmount,
                        'sumBillDiscAmount' => $sumBillDiscAmount,
                        'vat_amount' => $vatAmount
                      );

                      $this->orders_model->update_detail($rs->id, $arr);

                      $totalAfDisc += $rs->total_amount - $sumBillDiscAmount;
                      $docTotal += $rs->total_amount;
                      $vatSum += $vatAmount;
                    }
                  }
                }

                $totalAfDisc = $data->vat_type == 'I' ? ($totalAfDisc - $vatSum) : $totalAfDisc;
                $whtPrcnt = $data->vat_type == 'N' ? 0 : $order->WhtPrcnt;
                $whtAmount = $whtPrcnt > 0 ? $totalAfDisc * ($whtPrcnt * 0.01) : 0;

                $docTotal = $vat_type == 'E' ? $docTotal + $vatSum : $docTotal;

                $arr = array(
                  'bDiscAmount' => $billDiscAmount,
                  'doc_total' => $docTotal,
                  'VatSum' => $vatSum,
                  'isWht' => $whtPrcnt > 0 ? 1 : 0,
                  'WhtPrcnt' => $whtPrcnt,
                  'WhtAmount' => $whtAmount,
                  'status' => 0
                );

                $this->orders_model->update($order->code, $arr);
              }

              if($sc === TRUE)
              {
                $ds = $this->orders_model->get_detail($id);

                if( ! empty($ds))
                {
                  $ds->price = round($ds->price, 2);
                  $ds->qty = round($ds->qty);
                  $ds->total_amount = number($ds->total_amount, 2);
                  $ds->discLabel = discountLabel($ds->discount1, $ds->discount2, $ds->discount3);
                  $ds->hide = empty($ds->line_text) ? '' : 'hide';
                }
                else
                {
                  $sc = FALSE;
                  $this->error = "Insert success but cannot get order row data please refresh";
                }
              }
            }
          }
        }
        else
        {
          $sc = FALSE;
          $this->error = "Invalid order state";
        }
      }
      else
      {
        $sc = FALSE;
        $this->error = "Invalid order code";
      }
    }
    else
    {
      $sc = FALSE;
      $this->error = "Insert failed : Missing required parameter";
    }

    $arr = array(
      'status' => $sc === TRUE ? 'success' : 'failed',
      'message' => $sc === TRUE ? 'success' : $this->error,
      'row' => $sc === TRUE ? $ds : NULL
    );

    echo json_encode($arr);
  }


  public function update_vat_type()
  {
    $sc = TRUE;
    $code = $this->input->post('code');
    $vat_type = $this->input->post('vat_type');

    $doc = $this->orders_model->get($code);

    if( ! empty($doc))
    {

      $this->db->trans_begin();

      $details = $this->orders_model->get_details($code);
      $vatSum = 0;
      $docTotal = 0;


      if( ! empty($details))
      {
        foreach($details as $rs)
        {
          if($sc === FALSE)
          {
            break;
          }

          $totalAfDisc = $rs->total_amount - $rs->sumBillDiscAmount;
          $vatAmount = round(get_vat_amount($totalAfDisc, $rs->vat_rate, $vat_type), 6);

          $arr = array(
            'vat_amount' => $vatAmount
          );

          if( ! $this->orders_model->update_detail($rs->id, $arr) )
          {
            $sc = FALSE;
            $this->error = "Failed to update item vat";
          }

          $vatSum += $vatAmount;
          $docTotal += $totalAfDisc;
        }
      }

      if($sc === TRUE)
      {
        $arr = array(
          'vat_type' => $vat_type,
          'TaxStatus' => $vat_type == 'N' ? 'N' : 'Y',
          'doc_total' => $vat_type == 'E' ? $docTotal + $vatSum : $docTotal,
          'VatSum' => $vatSum
        );

        if( ! $this->orders_model->update($code, $arr))
        {
          $sc = FALSE;
          $this->error = "Failed to update order";
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
      $this->error = "Invalid order number";
    }

    echo $sc === TRUE ? 'success' : $this->error;
  }


  public function update_bill_discount()
  {
    $sc = TRUE;
    $code = $this->input->post('code');
    $discPrcnt = $this->input->post('DiscPrcnt');
    $discAmount = $this->input->post('DiscAmount');

    $doc = $this->orders_model->get($code);

    if( ! empty($doc))
    {
      $vat_type = $doc->vat_type == 'N' ? 'I' : $doc->vat_type;

      $docTotal = $doc->doc_total - $discAmount;

      $this->db->trans_begin();

      $details = $this->orders_model->get_details($code);
      $vatSum = 0;
      $avgBillDiscAmount = $discAmount == 0 ? 0 : ($docTotal > 0 ? $discAmount/$docTotal : 0);

      if( ! empty($details))
      {
        foreach($details as $rs)
        {
          if($sc === FALSE)
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

          if( ! $this->orders_model->update_detail($rs->id, $arr) )
          {
            $sc = FALSE;
            $this->error = "Failed to update bill discount rows";
          }

          $vatSum += $vatAmount;
        }
      }

      if($sc === TRUE)
      {
        $arr = array(
          'bDiscText' => $discPrcnt,
          'bDiscAmount' => $discAmount,
          'doc_total' => $docTotal,
          'VatSum' => $vatSum
        );

        if( ! $this->orders_model->update($code, $arr))
        {
          $sc = FALSE;
          $this->error = "Failed to update order bill discount";
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
      $this->error = "Invalid order number";
    }

    echo $sc === TRUE ? 'success' : $this->error;
  }


  public function update_with_holding_tax()
  {
    $sc = TRUE;
    $code = $this->input->post('code');
    $whtPrcnt = $this->input->post('whtPrcnt');
    $whtAmount = $this->input->post('whtAmount');

    $doc = $this->orders_model->get($code);

    if( ! empty($doc))
    {
      if($sc === TRUE)
      {
        $arr = array(
          'isWht' => $whtPrcnt > 0 ? 1 : 0,
          'WhtPrcnt' => $whtPrcnt,
          'WhtAmount' => $whtAmount
        );

        if( ! $this->orders_model->update($code, $arr))
        {
          $sc = FALSE;
          $this->error = "อัพเดตยอด หัก ณ ที่จ่าย ไม่สำเร็จ";
        }
      }
    }
    else
    {
      $sc = FALSE;
      $this->error = "Invalid order number";
    }

    echo $sc === TRUE ? 'success' : $this->error;
  }


  public function remove_item_row($id)
  {
    $sc = TRUE;
    $detail = $this->orders_model->get_detail($id);

    if( ! empty($detail))
    {
      $order = $this->orders_model->get($detail->order_code);

      if( ! empty($order))
      {
        //--- อนุญาติให้ลบได้แค่ 2 สถานะ
				if($order->state == 1 OR $order->state == 3)
				{
          $this->db->trans_begin();

          if( ! $this->orders_model->remove_detail($id))
          {
            $sc = FALSE;
            $this->error = "Delete filed";
          }
          else
          {
            if($this->log_delete)
            {
              $arr = array(
                'order_code' => $detail->order_code,
                'product_code' => $detail->product_code,
                'qty' => $detail->qty,
                'user' => $this->_user->uname
              );

              $this->orders_model->log_delete($arr);
            }
          }

          if($sc === TRUE)
          {
            $vat_type = $order->vat_type == 'N' ? 'I' : $order->vat_type;
            $whtPrcnt = $order->WhtPrcnt;

            $docTotal = $this->orders_model->get_order_total_amount($order->code);

            $billDiscPrcnt = $order->bDiscText;
            $billDiscAmount = $order->bDiscAmount;

            if($billDiscPrcnt > 0)
            {
              $billDiscAmount = $docTotal * ($billDiscPrcnt * 0.01);
            }

            $details = $this->orders_model->get_details($order->code);

            $avgBillDiscAmount = $billDiscAmount == 0 ? 0 : ($docTotal > 0 ? $billDiscAmount/$docTotal : 0);

            $DocTotal = 0;
            $VatSum = 0;

            if( ! empty($details))
            {
              foreach($details as $rs)
              {
                if($sc === FALSE)
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

                $VatSum += $vatAmount;
                $DocTotal += $vat_type == 'E' ? ($totalAfDisc + $vatAmount) : $totalAfDisc;

                if( ! $this->orders_model->update_detail($rs->id, $arr) )
                {
                  $sc = FALSE;
                  $this->error = "Failed to update bill discount rows";
                }
              }
            }

            $amountAfDisc = $DocTotal - $VatSum;
            $whtAmount = $whtPrcnt > 0 ? ($amountAfDisc * ($whtPrcnt * 0.01)) : 0;

            $arr = array(
              'bDiscText' => $billDiscPrcnt,
              'bDiscAmount' => $billDiscAmount,
              'isWht' => $whtPrcnt > 0 ? 1 : 0,
              'WhtPrcnt' => $whtPrcnt,
              'WhtAmount' => $whtAmount,
              'doc_total' => $DocTotal,
              'VatSum' => $VatSum
            );

            if( ! $this->orders_model->update($order->code, $arr))
            {
              $sc = FALSE;
              $this->error = "Failed to update order summary";
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
					$this->error = "Delete failed : Invalid order status";
				}
      }
      else
      {
        $sc = FALSE;
        $this->error = "Order not found";
      }
    }
    else
    {
      $sc = FALSE;
      $this->error = "Item not found";
    }

    echo $sc === TRUE ? 'success' : $this->error;
  }


  public function remove_detail($id)
  {
    $sc = TRUE;
    $detail = $this->orders_model->get_detail($id);

    if(!empty($detail))
    {
      $order = $this->orders_model->get($detail->order_code);

      if(! empty($order))
      {
        //--- อนุญาติให้ลบได้แค่ 2 สถานะ
        if($order->state == 1 OR $order->state == 3)
        {
          if( ! $this->orders_model->remove_detail($id))
          {
            $sc = FALSE;
            $this->error = "Delete filed";
          }
          else
          {
            //--- ถ้าเป็นออเดอร์แปรสถาพให้ลบการเชื่อมโยงไปด้วย
            if($order->role == 'T' OR $order->role == 'Q')
            {
              $this->load->model('inventory/transform_model');
              $this->transform_model->remove_transform_detail($id);
            }

            if($this->log_delete)
            {
              $arr = array(
                'order_code' => $detail->order_code,
                'product_code' => $detail->product_code,
                'qty' => $detail->qty,
                'user' => $this->_user->uname
              );

              $this->orders_model->log_delete($arr);
            }
          }

          if($sc === TRUE)
          {
            $doc_total = $this->orders_model->get_order_total_amount($detail->order_code);
            $totalBalance = $doc_total - $order->paidAmount;

            $arr = array(
              'doc_total' => $doc_total,
              'TotalBalance' => $totalBalance > 0 ? $totalBalance : 0
            );

            if( ! $this->orders_model->update($detail->order_code, $arr))
            {
              $sc = FALSE;
              $this->error = "Failed to update doc total amount";
            }
          }
        }
        else
        {
          $sc = FALSE;
          $this->error = "Delete failed : Invalid order status";
        }
      }
      else
      {
        $sc = FALSE;
        $this->error = "Order not found";
      }
    }
    else
    {
      $sc = FALSE;
      $this->error = "Item not found";
    }

    echo $sc === TRUE ? 'success' : $this->error;
  }


  public function remove_details()
  {
    $sc = TRUE;

    $code = $this->input->post('code');

    $ids = json_decode($this->input->post('ids'));

    if( ! empty($ids))
    {
      $order = $this->orders_model->get($code);

      if( ! empty($order))
      {
        //--- อนุญาติให้ลบได้แค่ 2 สถานะ
        if($order->state == 1 OR $order->state == 3)
        {
          if( ! $this->orders_model->remove_details($ids))
          {
            $sc = FALSE;
            $this->error = "Delete filed";
          }
          else
          {
            //--- ถ้าเป็นออเดอร์แปรสถาพให้ลบการเชื่อมโยงไปด้วย
            if($order->role == 'T' OR $order->role == 'Q')
            {
              $this->load->model('inventory/transform_model');
              $this->transform_model->remove_transform_details($ids);
            }
          }
        }
        else
        {
          $sc = FALSE;
          $this->error = "Delete failed : Invalid order status";
        }

        if($sc === TRUE)
        {
          $doc_total = $this->orders_model->get_order_total_amount($detail->order_code);
          $totalBalance = $doc_total - $order->paidAmount;

          $arr = array(
            'doc_total' => $doc_total,
            'TotalBalance' => $totalBalance > 0 ? $totalBalance : 0
          );

          if( ! $this->orders_model->update($detail->order_code, $arr))
          {
            $sc = FALSE;
            $this->error = "Failed to update doc total amount";
          }
        }
      }
      else
      {
        $sc = FALSE;
        $this->error = "Order not found";
      }
    }
    else
    {
      $sc = FALSE;
      $this->error = "Item not found";
    }

    echo $sc === TRUE ? 'success' : $this->error;
  }


  public function edit_order($code)
  {
    $this->load->model('address/address_model');
    $this->load->model('masters/bank_model');
    $this->load->model('orders/order_payment_model');
    $this->load->helper('bank');
		$this->load->helper('sender');
    $ds = array();
    $order = $this->orders_model->get($code);

    if(!empty($order))
    {
      $order->channels_name = $this->channels_model->get_name($order->channels_code);
      $order->payment_name  = $this->payment_methods_model->get_name($order->payment_code);
      $order->total_amount  = $this->orders_model->get_order_total_amount($order->code);
      $order->user          = $this->user_model->get_name($order->user);
      $order->state_name    = get_state_name($order->state);
      $order->has_payment   = $this->order_payment_model->is_exists($code);

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

      $ds['total_qty'] = 0;
      $ds['order_amount'] = 0;
      $ds['total_amount'] = 0;

      if( ! empty($details))
      {
        foreach($details as $ra)
        {
          $ds['total_qty'] += $ra->qty;
          $ds['order_amount'] += $ra->qty * $ra->price;
          $ds['total_amount'] += $ra->total_amount;
        }
      }

	    $ship_to = empty($order->customer_ref) ? $this->address_model->get_ship_to_address($order->customer_code) : $this->address_model->get_shipping_address($order->customer_ref);
	    $banks = $this->bank_model->get_active_bank();

      $ds['netAmount'] = ( $ds['total_amount'] - $order->bDiscAmount );
	    $ds['state'] = $ost;
	    $ds['order'] = $order;
	    $ds['details'] = $details;
	    $ds['addr']  = $ship_to;
	    $ds['banks'] = $banks;
			$ds['cancle_reason'] = ($order->state == 9 ? $this->orders_model->get_cancle_reason($code) : NULL);
	    $ds['allowEditDisc'] = getConfig('ALLOW_EDIT_DISCOUNT') == 1 ? TRUE : FALSE;
	    $ds['allowEditPrice'] = getConfig('ALLOW_EDIT_PRICE') == 1 ? TRUE : FALSE;
	    $ds['edit_order'] = TRUE; //--- ใช้เปิดปิดปุ่มแก้ไขราคาสินค้าไม่นับสต็อก
	    $this->load->view('orders/order_edit', $ds);
    }
		else
		{
			$err = "ไม่พบเลขที่เอกสาร : {$code}";
			$this->page_error($err);
		}
  }


  public function update_item()
	{
		$sc = TRUE;

    $code = $this->input->post('order_code');
		$id = $this->input->post('id');
		$qty = $this->input->post('qty');
		$price = $this->input->post('price');
		$discount_label = trim($this->input->post('discount_label'));
    $product_name = trim($this->input->post('product_name'));

    $amountBfDisc = $this->input->post('amountBfDisc');
    $billDiscPrcnt = $this->input->post('billDiscPrcnt');
    $billDiscAmount = $this->input->post('billDiscAmount');
    $whtPrcnt = $this->input->post('whtPrcnt');
    $whtAmount = $this->input->post('whtAmount');
    $vatSum = $this->input->post('vatSum');
    $docTotal = $this->input->post('docTotal');
    $vatType = $this->input->post('vatType');
    $vat_type = $vatType == 'N' ? 'I' : $vatType;

		if( ! empty($id))
		{
      $order = $this->orders_model->get($code);

      if( ! empty($order))
      {
        if( $order->state < 2)
        {
          $detail = $this->orders_model->get_detail($id);

    			if( ! empty($detail))
    			{
            $this->db->trans_begin();

    				//-- discount_helper
    				//-- return discount array per 1 item
    				$discount = parse_discount_text($discount_label, $price);
    				$sell_price = $price - $discount['discount_amount'];
    				$total_amount = $sell_price * $qty;
            $valid = $detail->valid == 1 && $qty > $detail->qty ? 0 : $detail->valid;
            $valid_qc = $detail->valid_qc == 1 && $qty > $detail->qty ? 0 : $detail->valid_qc;

    				$arr = array(
    					'qty' => $qty,
    					'price' => $price,
              'discount1' => $discount['discount1'],
              'discount2' => $discount['discount2'],
              'discount3' => $discount['discount3'],
    					'discount_amount' => $discount['discount_amount'] * $qty,
    					'total_amount' => $total_amount,
              'valid' => $valid,
              'valid_qc' => $valid_qc
    				);

            if( ! empty($product_name))
            {
              $arr['product_name'] = $product_name;
            }

    				if( ! $this->orders_model->update_detail($id, $arr))
    				{
    					$sc = FALSE;
    					$this->error = "Update failed";
    				}

            if($sc === TRUE)
            {
              $details = $this->orders_model->get_details($code);
              $avgBillDiscAmount = $billDiscAmount == 0 ? 0 : ($docTotal > 0 ? $billDiscAmount/$docTotal : 0);

              if( ! empty($details))
              {
                foreach($details as $rs)
                {
                  if($sc === FALSE)
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

                  if( ! $this->orders_model->update_detail($rs->id, $arr) )
                  {
                    $sc = FALSE;
                    $this->error = "Failed to update bill discount rows";
                  }
                }
              }

              $arr = array(
                'bDiscText' => $billDiscPrcnt,
                'bDiscAmount' => $billDiscAmount,
                'isWht' => $whtPrcnt > 0 ? 1 : 0,
                'WhtPrcnt' => $whtPrcnt,
                'WhtAmount' => $whtAmount,
                'doc_total' => $docTotal,
                'VatSum' => $vatSum,
                'status' => 0
              );

              if( ! $this->orders_model->update($code, $arr))
              {
                $sc = FALSE;
                $this->error = "Failed to update order summary";
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
    				$this->error = "Item Not found";
    			}
        }
        else
        {
          $sc = FALSE;
          $this->error = "Invalid order state";
        }
      }
      else
      {
        $sc = FALSE;
        $this->error = "Invalid order code";
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

    if($this->input->post('order_code'))
    {
      $this->load->model('inventory/invoice_model');
			$this->load->model('masters/warehouse_model');
      $code = $this->input->post('order_code');
      $recal = $this->input->post('recal');
      $has_term = $this->payment_methods_model->has_term($this->input->post('payment_code'));
      $sale_code = $this->input->post('sale_id'); //$this->customers_model->get_sale_code($this->input->post('customer_code'));

      $customer = $this->customers_model->get($this->input->post('customer_code'));

      //--- check over due
      $is_strict = getConfig('STRICT_OVER_DUE') == 1 ? TRUE : FALSE;
      $overDue = $is_strict ? $this->invoice_model->is_over_due($this->input->post('customer_code')) : FALSE;

      //--- ถ้ามียอดค้างชำระ และ เป็นออเดอร์แบบเครดิต
      //--- ไม่ให้เพิ่มออเดอร์
      if($overDue && $has_term && !($customer->skip_overdue))
      {
        $sc = FALSE;
        $message = 'มียอดค้างชำระเกินกำหนดไม่อนุญาติให้แก้ไขการชำระเงิน';
      }
      else
      {
				$wh = $this->warehouse_model->get($this->input->post('warehouse_code'));

        $ds = array(
          'reference' => $this->input->post('reference'),
          'customer_code' => $customer->code,
          'customer_name' => $customer->name,
          'customer_ref' => trim($this->input->post('customer_ref')),
          'phone' => trim($this->input->post('phone')),
          'channels_code' => $this->input->post('channels_code'),
          'payment_code' => $this->input->post('payment_code'),
          'sale_code' => $sale_code,
          'is_term' => $has_term,
          'date_add' => db_date($this->input->post('date_add')),
          'warehouse_code' => $wh->code,
          'remark' => $this->input->post('remark'),
					'transformed' => $this->input->post('transformed'),
          'status' => 0,
					'id_address' => NULL,
					'id_sender' => NULL
        );

        $rs = $this->orders_model->update($code, $ds);

        if($rs === TRUE)
        {
          if($recal == 1)
          {
            $order = $this->orders_model->get($code);

            //---- Recal discount
            $details = $this->orders_model->get_order_details($code);
            if(!empty($details))
            {
              foreach($details as $detail)
              {
                $qty	= $detail->qty;

                //---- คำนวณ ส่วนลดจากนโยบายส่วนลด
                $discount 	= $this->discount_model->get_item_recal_discount($detail->order_code, $detail->product_code, $detail->price, $order->customer_code, $qty, $order->payment_code, $order->channels_code, $order->date_add);

                $arr = array(
                  "qty"		=> $qty,
                  "discount1"	=> $discount['discLabel1'],
                  "discount2" => $discount['discLabel2'],
                  "discount3" => $discount['discLabel3'],
                  "discount_amount" => $discount['amount'],
                  "total_amount"	=> ($detail->price * $qty) - $discount['amount'],
                  "id_rule"	=> $discount['id_rule']
                );

                $this->orders_model->update_detail($detail->id, $arr);
              }
            }
          }
        }
        else
        {
          $sc = FALSE;
          $message = 'ปรับปรุงรายการไม่สำเร็จ';
        }
      }
    }
    else
    {
      $sc = FALSE;
      $message = 'ไม่พบเลขที่เอกสาร';
    }

    echo $sc === TRUE ? 'success' : $message;
  }


  public function edit_detail($code)
  {
    $this->load->helper('product_tab');
    $ds = array();
    $rs = $this->orders_model->get($code);
    if($rs->state <= 3)
    {
      $ds['order'] = $rs;

      $details = $this->orders_model->get_order_details($code);
      $ds['details'] = $details;
      $ds['allowEditDisc'] = getConfig('ALLOW_EDIT_DISCOUNT') == 1 ? TRUE : FALSE;
      $ds['allowEditPrice'] = getConfig('ALLOW_EDIT_PRICE') == 1 ? TRUE : FALSE;
      $ds['edit_order'] = FALSE; //--- ใช้เปิดปิดปุ่มแก้ไขราคาสินค้าไม่นับสต็อก
      $this->load->view('orders/order_edit_detail', $ds);
    }
  }


  public function save($code)
  {
    $sc = TRUE;

    $h = json_decode($this->input->post('data'));


    if( ! empty($h))
    {
      $this->db->trans_begin();

      $vat_type = $h->vat_type == 'N' ? 'I' : $h->vat_type;

      $arr = array(
        'is_term' => $h->is_term,
        'vat_type' => $h->vat_type,
        'TaxStatus' => $h->TaxStatus,
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
        'channels_code' => $h->channels_code,
        'reference' => get_null($h->reference),
        'warehouse_code' => $h->warehouse_code,
        'sale_code' => $h->sale_id,
        'remark' => get_null($h->remark),
        'bDiscText' => $h->bDiscText,
        'bDiscAmount' => $h->bDiscAmount,
        'isWht' => $h->WhtPrcnt > 0 ? 1 : 0,
        'WhtPrcnt' => $h->WhtPrcnt,
        'WhtAmount' => $h->WhtAmount,
        'doc_total' => $h->DocTotal,
        'VatSum' => $h->VatSum
      );

      if( ! $this->orders_model->update($code, $arr))
      {
        $sc = FALSE;
        $this->error = "Update failed";
      }

      //---- Calculate avgBillDiscAmount vat amount each row
      if($sc === TRUE)
      {
        $details = $this->orders_model->get_details($code);
        $avgBillDiscAmount = $h->bDiscAmount == 0 ? 0 : ($h->amountBfDisc > 0 ? $h->bDiscAmount/$h->amountBfDisc : 0);

        if( ! empty($details))
        {
          foreach($details as $rs)
          {
            if($sc === FALSE)
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

            if( ! $this->orders_model->update_detail($rs->id, $arr) )
            {
              $sc = FALSE;
              $this->error = "Failed to update bill discount rows";
            }
          } //-- end foreach
        } //-- if ! empty($details)
      } //--- $sc === TRUE


      if($sc === TRUE)
      {
        $order = $this->orders_model->get($code);

        //--- ถ้าออเดอร์เป็นแบบเครดิต
        if($order->is_term == 1 && $order->role === 'S')
        {
          //--- creadit used
          $credit_used = round($this->orders_model->get_sum_not_complete_amount($order->customer_code), 2);
          //--- credit balance from sap
          $credit_balance = round($this->customers_model->get_credit($order->customer_code), 2);

          $skip = getConfig('CONTROL_CREDIT');

          if($skip == 1)
          {
            if($credit_used > $credit_balance)
            {
              $diff = $credit_used - $credit_balance;
              $sc = FALSE;
              $this->error = 'เครดิตคงเหลือไม่พอ (ขาด : '.number($diff, 2).')';
            }
          }
        }

        if($order->role === 'C' OR $order->role === 'N')
        {
          $isLimit = $order->role == 'C' ? is_true(getConfig('LIMIT_CONSIGNMENT')) : is_true(getConfig('LIMIT_CONSIGN'));

          if($isLimit)
          {
            $this->load->model('masters/zone_model');
            $this->load->model('masters/warehouse_model');
            $whsCode = $this->zone_model->get_warehouse_code($order->zone_code);

            if(! empty($whsCode))
            {
              $limitAmount = $this->warehouse_model->get_limit_amount($whsCode);

              if($limitAmount > 0)
              {
                if($this->warehouse_model->is_stock_exists($order->role, $whsCode))
                {
                  $balanceAmount = $this->warehouse_model->get_balance_amount($order->role, $whsCode);

                  $diff = $limitAmount - $balanceAmount;

                  $amount = round($this->orders_model->get_consign_not_complete_amount($order->role, $whsCode), 2);

                  if($diff < $amount)
                  {
                    $dif_over = $amount - $diff;
                    $sc = FALSE;
                    $this->error = "มูลค่าสินค้าที่เบิก เกินกว่ามูลค่าคงเหลือสูงสุดที่ของคลัง {$whsCode} (เกิน : ".number($dif_over, 2).")";
                  }
                }
              }
            }
            else
            {
              $sc = FALSE;
              $this->error = "ไม่พบคลังสินค้า";
            }
          }
        }

        if($sc === TRUE)
        {
          if(! $this->orders_model->update($code, ['status' => 1]))
          {
            $sc = FALSE;
            $this->error = 'บันทึกออเดอร์ไม่สำเร็จ';
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
      $this->error = "Missing required parameter";
    }

    echo $sc === TRUE ? 'success' : $this->error;
  }


  public function update($code, $ds)
  {
    $sc = TRUE;

    if( ! empty($ds))
    {
      $this->load->model('inventory/invoice_model');
			$this->load->model('masters/warehouse_model');

      $recal = 0; //$ds->recal == 1 ? 1 : 0;
      $has_term = $this->payment_methods_model->has_term($ds->payment_code);

      $customer = $this->customers_model->get($ds->customer_code);

      //--- check over due
      $is_strict = getConfig('STRICT_OVER_DUE') == 1 ? TRUE : FALSE;
      $overDue = $is_strict ? $this->invoice_model->is_over_due($ds->customer_code) : FALSE;

      //--- ถ้ามียอดค้างชำระ และ เป็นออเดอร์แบบเครดิต
      //--- ไม่ให้เพิ่มออเดอร์
      if($overDue && $has_term && !($customer->skip_overdue))
      {
        $sc = FALSE;
        $this->error = 'มียอดค้างชำระเกินกำหนดไม่อนุญาติให้แก้ไขการชำระเงิน';
      }
      else
      {
				$wh = $this->warehouse_model->get($ds->warehouse_code);

        $arr = array(
          'reference' => $ds->reference,
          'customer_code' => $customer->code,
          'customer_name' => $customer->name,
          'customer_ref' => trim($ds->customer_ref),
          'phone' => trim($ds->phone),
          'channels_code' => $ds->channels_code,
          'payment_code' => $ds->payment_code,
          'sale_code' => $ds->sale_id,
          'is_term' => $has_term,
          'date_add' => db_date($ds->date_add),
          'warehouse_code' => $wh->code,
          'remark' => get_null(trim($ds->remark)),
					'id_sender' => get_null($ds->id_sender),
          'shipping_code' => get_null($ds->tracking),
          'bDiscText' => $ds->bDiscText,
          'bDiscAmount' => $ds->bDiscAmount
        );

        if($ds->current_customer != $ds->customer_code)
        {
          $arr['id_address'] = NULL;
        }

        if($this->orders_model->update($code, $arr))
        {
          if($recal == 1)
          {
            $order = $this->orders_model->get($code);

            //---- Recal discount
            $details = $this->orders_model->get_order_details($code);

            if(!empty($details))
            {
              foreach($details as $detail)
              {
                $qty	= $detail->qty;

                //---- คำนวณ ส่วนลดจากนโยบายส่วนลด
                $discount 	= $this->discount_model->get_item_recal_discount($detail->order_code, $detail->product_code, $detail->price, $order->customer_code, $qty, $order->payment_code, $order->channels_code, $order->date_add);

                $arr = array(
                  "qty"		=> $qty,
                  "discount1"	=> $discount['discLabel1'],
                  "discount2" => $discount['discLabel2'],
                  "discount3" => $discount['discLabel3'],
                  "discount_amount" => $discount['amount'],
                  "total_amount"	=> ($detail->price * $qty) - $discount['amount'],
                  "id_rule"	=> $discount['id_rule']
                );

                $this->orders_model->update_detail($detail->id, $arr);
              }
            }
          }
        }
        else
        {
          $sc = FALSE;
          $this->error = "Failed to update order header";
        }
      }
    }
    else
    {
      $sc = FALSE;
      $this->error = "Missing required data";
    }

    return $sc;
  }


	public function load_quotation()
	{
		$sc = TRUE;

		$code = $this->input->get('order_code');
		$qt_no = $this->input->get('qt_no');

		if(!empty($code))
		{
			//--- load model
			$this->load->model('orders/quotation_model');
			$order = $this->orders_model->get($code);
			if(!empty($order))
			{
				//---- order state ต้องยังไม่ถูกดึงไปจัด
				if($order->state <= 3)
				{

					//---- start transection
					$this->db->trans_begin();
					//--- มีอยู่แต่ต้องการเอาออก
					if(empty($qt_no) && !empty($order->quotation_no))
					{
						//--- 2. ลบรายการที่มีในออเดอร์แก่า
						if($this->orders_model->clear_order_detail($code))
						{
							//---- update qt no on order
							$arr = array(
								'quotation_no' => NULL,
								'status' => 0
							);

							if(! $this->orders_model->update($code, $arr))
							{
								$sc = FALSE;
								$this->error = "ลบเลขที่ใบเสนอราคาไม่สำเร็จ";
							}

						}
						else
						{
							$sc = FALSE;
							$this->error = "ลบรายการไม่สำเร็จ";
						}
					}
					else
					{
						if(!empty($qt_no))
						{
							//--- ยังไม่มี หรือ มีแล้วต้องการเปลี่ยน
							$docEntry = $this->quotation_model->get_id($qt_no);

							if(! empty($docEntry))
							{
								//---- 1. ดึงรายการในใบเสนอราคามาเช็คก่อนว่ามีรายการหรือไม่
								$is_exists = $this->quotation_model->is_exists_details($docEntry);

								if($is_exists === TRUE)
								{
									//--- 2. ลบรายการที่มีในออเดอร์แก่า
									if($this->orders_model->clear_order_detail($code))
									{
										//--- 3. เพิ่มรายการใหม่
										$details = $this->quotation_model->get_details($docEntry);

										if(!empty($details))
										{
											$auz = getConfig('ALLOW_UNDER_ZERO');

											foreach($details as $rs)
											{
												if($sc === FALSE)
												{
													break;
												}

												$item = $this->products_model->get($rs->code);

												if(!empty($item))
												{
													//---- ยอดสินค้าที่่สั่งได้
													$stock = $this->get_sell_stock($item->code, $order->warehouse_code);
													$qty = round($rs->qty, 2);
													//--- ถ้ามีสต็อกมากว่าที่สั่ง หรือ เป็นสินค้าไม่นับสต็อก
								          if( $stock >= $qty OR $item->count_stock == 0 OR $auz == 1)
								          {
														$price = add_vat($rs->price); //-- PriceBefDi
														$disc_amount = ($price * ($rs->discount * 0.01)) * $qty;
														$total_amount = ($qty * $price) - $disc_amount;

														$arr = array(
															'order_code' => $code,
															'style_code' => $item->style_code,
															'product_code' => $item->code,
															'product_name' => $item->name,
															'cost' => $item->cost,
															'price' => $price,
															'qty' => $qty,
															'discount1' => $rs->discount.'%',
															'discount_amount' => $disc_amount,
															'total_amount' => $total_amount,
															'is_count' => $item->count_stock
														);

														$this->orders_model->add_detail($arr);
													}
													else
													{
														$sc = FALSE;
														$this->error = "สินค้าไม่พอ : {$item->code} ต้องการ {$qty} คงเหลือ {$stock}";
													}
												}
												else
												{
													$sc = FALSE;
													$this->error = "ไม่พบรหัสสินค้า '{$rs->code}' ในระบบ";
												}

											} //--- end foreach

											$arr = array(
												'quotation_no' => $qt_no,
												'status' => 0
											);

											$this->orders_model->update($code, $arr);

										}
										else
										{
											$sc = FALSE;
											$this->error = "Error : ไม่พบรายการในใบเสนอราคา";
										}
									}
									else
									{
										$sc = FALSE;
										$this->error = "ลบรายการเก่าไม่สำเร็จ";
									}
								}
								else
								{
									$sc = FALSE;
									$this->error = "ไม่พบรายการในใบเสนอราคา";
								}
							}
							else
							{
								$sc = FALSE;
								$this->error = "ใบเสนอราคาไม่ถูกต้อง";
							} //--- end if empty qt
						}

					} //--- end if empty qt_no


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
					$this->error = "ออเดอร์อยุ๋ในสถานะที่ไม่สามารถแก้ไขรายการได้";
				}
			}
			else
			{
				$sc = FALSE;
				$this->error = "ไม่พบข้อมูลออเดอร์";
			}
		}
		else
		{
			$sc = FALSE;
			$this->error = "ไม่พบเลขที่เอกสาร";
		}

		echo $sc === TRUE ? 'success' : $this->error;
	}



  public function get_product_order_tab()
  {
    $ds = "";
  	$id_tab = $this->input->post('id');
    $whCode = get_null($this->input->post('warehouse_code'));
  	$qs     = $this->product_tab_model->getStyleInTab($id_tab);
    $showStock = getConfig('SHOW_SUM_STOCK');
  	if( $qs->num_rows() > 0 )
  	{
  		foreach( $qs->result() as $rs)
  		{
        $style = $this->product_style_model->get($rs->style_code);

  			if( $style->active == 1 && $this->products_model->is_disactive_all($style->code) === FALSE)
  			{
  				$ds 	.= 	'<div class="col-lg-2 col-md-2 col-sm-3 col-xs-4"	style="text-align:center;">';
  				$ds 	.= 		'<div class="product" style="padding:5px;">';
  				$ds 	.= 			'<div class="image">';
  				$ds 	.= 				'<a href="javascript:void(0)" onClick="getOrderGrid(\''.$style->code.'\')">';
  				$ds 	.=					'<img class="img-responsive" src="'.get_cover_image($style->code, 'default').'" />';
  				$ds 	.= 				'</a>';
  				$ds	.= 			'</div>';
  				$ds	.= 			'<div class="description" style="font-size:10px; min-height:50px;">';
  				$ds	.= 				'<a href="javascript:void(0)" onClick="getOrderGrid(\''.$style->code.'\')">';
  				$ds	.= 			$style->code.'<br/>'. number($style->price,2);
  				$ds 	.=  		($style->count_stock && $showStock) ? ' | <span style="color:red;">'.$this->get_style_sell_stock($style->code, $whCode).'</span>' : '';
  				$ds	.= 				'</a>';
  				$ds 	.= 			'</div>';
  				$ds	.= 		'</div>';
  				$ds 	.=	'</div>';
  			}
  		}
  	}
  	else
  	{
  		$ds = "no_product";
  	}

  	echo $ds;
  }



  public function get_style_sell_stock($style_code, $warehouse = NULL)
  {
    $sell_stock = $this->stock_model->get_style_sell_stock($style_code, $warehouse);
    $reserv_stock = $this->orders_model->get_reserv_stock_by_style($style_code, $warehouse);

    $available = $sell_stock - $reserv_stock;

    return $available >= 0 ? $available : 0;
  }


  public function get_order_grid()
  {
    $sc = TRUE;
    $ds = array();
    //----- Attribute Grid By Clicking image
    $style = $this->product_style_model->get_with_old_code($this->input->get('style_code'));

    if(!empty($style))
    {
      //--- ถ้าได้ style เดียว จะเป็น object ไม่ใช่ array
      if(! is_array($style))
      {
        if($style->active)
        {
          $warehouse = get_null($this->input->get('warehouse_code'));
          $zone = get_null($this->input->get('zone_code'));
          $view = $this->input->get('isView') == '0' ? FALSE : TRUE;
          $table = $this->getOrderGrid($style->code, $view, $warehouse, $zone);
          $tableWidth	= $this->products_model->countAttribute($style->code) == 1 ? 200 : $this->getOrderTableWidth($style->code);

          if($table == 'notfound') {
            $sc = FALSE;
            $this->error = "not found";
          }
          else
          {
            $tbs = '<table class="table table-bordered border-1" style="min-width:'.$tableWidth.'px;">';
            $tbe = '</table>';
            $ds = array(
              'status' => 'success',
              'message' => NULL,
              'table' => $tbs.$table.$tbe,
              'tableWidth' => $tableWidth + 40,
              'styleCode' => $style->code,
              'styleOldCode' => $style->old_code,
              'styleName' => $style->name
            );
          }
        }
        else
        {
          $sc = FALSE;
          $this->error = "สินค้า Inactive";
        }
      }
      else
      {
        $sc = FALSE;
        $this->error = "รหัสซ้ำ ";

        foreach($style as $rs)
        {
          $this->error .= " : {$rs->code} : {$rs->old_code}";
        }
      }
    }
    else
    {
      $sc = FALSE;
      $this->error = "not found";
    }

    echo $sc === TRUE ? json_encode($ds) : $this->error;
  }


  public function get_item_grid()
  {
    $sc = "";
    $item_code = $this->input->get('itemCode');
    $warehouse_code = get_null($this->input->get('warehouse_code'));
    $filter = getConfig('MAX_SHOW_STOCK');
    $item = $this->products_model->get_with_old_code($item_code);

    if(!empty($item))
    {
      if(! is_array($item))
      {
        $qty = $item->count_stock == 1 ? ($item->active == 1 ? $this->showStock($this->get_sell_stock($item->code, $warehouse_code)) : 0) : 1000000;
        $sc = "success | {$item_code} | {$qty}";
      }
      else
      {
        $this->error = "รหัสซ้ำ ";
        foreach($item as $rs)
        {
          $this->error .= " :{$rs->code}";
        }

        echo "Error : {$this->error} | {$item_code}";
      }

    }
    else
    {
      $sc = "Error | ไม่พบสินค้า | {$item_code}";
    }

    echo $sc;
  }


  public function get_item()
  {
    $sc = TRUE;

    $item_code = $this->input->post('item_code');

    if( ! empty($item_code))
    {
      $warehouse_code = get_null($this->input->post('warehouse_code'));

      $filter = getConfig('MAX_SHOW_STOCK');

      $item = $this->products_model->get($item_code);

      if( ! empty($item))
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


  public function getOrderGrid($style_code, $view = FALSE, $warehouse = NULL, $zone = NULL)
  {
    $sc = '';
    $style = $this->product_style_model->get($style_code);
    if(!empty($style))
    {
      if($style->active)
      {
        $isVisual = $style->count_stock == 1 ? FALSE : TRUE;
        $attrs = $this->getAttribute($style->code);

        if( count($attrs) == 1  )
        {
          $sc .= $this->orderGridOneAttribute($style, $attrs[0], $isVisual, $view, $warehouse, $zone);
        }
        else if( count( $attrs ) == 2 )
        {
          $sc .= $this->orderGridTwoAttribute($style, $isVisual, $view, $warehouse, $zone);
        }
      }
      else
      {
        $sc = 'Disactive';
      }

    }
    else
    {
      $sc = 'notfound';
    }

    return $sc;
  }


  public function showStock($qty)
	{
		return $this->filter == 0 ? $qty : ($this->filter < $qty ? $this->filter : $qty);
	}


  public function orderGridOneAttribute($style, $attr, $isVisual, $view, $warehouse = NULL, $zone = NULL)
	{
    $auz = getConfig('ALLOW_UNDER_ZERO');
    $overStock = getConfig('ORDER_OVER_STOCK') == 1 ? TRUE : FALSE;

    if($auz == 1)
    {
      $isVisual = TRUE;
    }

		$sc 		= '';
		$data 	= $attr == 'color' ? $this->getAllColors($style->code) : $this->getAllSizes($style->code);
		$items	= $this->products_model->get_style_items($style->code);
		//$sc 	 .= "<table class='table table-bordered'>";
		$i 		  = 0;

    foreach($items as $item )
    {
      $id_attr	= $item->size_code === NULL OR $item->size_code === '' ? $item->color_code : $item->size_code;
      $sc 	.= '<tr>';
      $active	= $item->active == 0 ? 'Disactive' : ( $item->can_sell == 0 ? 'Not for sell' : ( $item->is_deleted == 1 ? 'Deleted' : TRUE ) );
      $stock	= $isVisual === FALSE ? ( $active == TRUE ? $this->showStock( $this->stock_model->get_stock($item->code) )  : 0 ) : 0; //---- สต็อกทั้งหมดทุกคลัง
			$qty 		= $isVisual === FALSE ? ( $active == TRUE ? $this->showStock( $this->get_sell_stock($item->code, $warehouse, $zone) ) : 0 ) : FALSE; //--- สต็อกที่สั่งซื้อได้
			$disabled  = ($isVisual OR $overStock) && $active == TRUE ? '' : ( ($active !== TRUE OR ($qty < 1 && $overStock == FALSE)) ? 'disabled' : '');

      if( $qty < 1 && $active === TRUE )
			{
				$txt = '<p class="pull-right red">Sold out</p>';
			}
			else if( $qty > 0 && $active === TRUE )
			{
				$txt = '<p class="pull-right green">'. $qty .'  in stock</p>';
			}
			else
			{
				$txt = $active === TRUE ? '' : '<p class="pull-right blue">'.$active.'</p>';
			}

      $limit = $qty === FALSE OR $overStock === TRUE ? 1000000 : $qty;
      $code = $attr == 'color' ? $item->color_code.' ('.$data[$item->color_code].')' : $item->size_code;

			$sc 	.= '<td class="middle text-center">';
			$sc 	.= '<strong>' .	$code . '</strong>';
			$sc 	.= '</td>';

			$sc 	.= '<td class="order-grid fix-width-100 middle">';
			$sc 	.= $isVisual === FALSE ? '<center><span class="font-size-10 blue pointer" onClick="viewStock(\''.$item->code.'\')">('.($stock < 0 ? 0 : $stock).')</span></center>':'';

      if( $view === FALSE )
			{
			$sc 	.= '<input type="number" class="form-control input-sm order-grid display-block" name="qty[0]['.$item->code.']" id="qty_'.$item->code.'" onkeyup="valid_qty($(this), '.($qty === FALSE ? 1000000 : $qty).')" '.$disabled.' />';
			}

      $sc 	.= 	'<center>';
      $sc   .= '<span class="font-size-10">';
      $sc   .= $qty === FALSE && $active === TRUE ? '' : ( ($qty < 1 || $active !== TRUE ) ? $txt : $qty);
      $sc   .= '</span></center>';
			$sc 	.= '</td>';

			$i++;

			$sc 	.= '</tr>';

    }


		//$sc	.= "</table>";

		return $sc;
	}


  public function orderGridTwoAttribute($style, $isVisual, $view, $warehouse = NULL, $zone = NULL)
  {
    $auz = getConfig('ALLOW_UNDER_ZERO');

    $overStock = getConfig('ORDER_OVER_STOCK') == 1 ? TRUE : FALSE;

    if($auz == 1)
    {
      $isVisual = $view === TRUE ? $isVisual : TRUE;
    }

    $colors	= $this->getAllColors($style->code);
    $sizes 	= $this->getAllSizes($style->code);
    $sc 		= '';
    //$sc 		.= '<table class="table table-bordered">';
    $sc 		.= $this->gridHeader($colors);

    foreach( $sizes as $size_code => $size )
    {
      $bg_color = '';
      $sc 	.= '<tr style="font-size:12px; '.$bg_color.'">';
      $sc 	.= '<td class="text-center middle"><strong>'.$size_code.'</strong></td>';

      foreach( $colors as $color_code => $color )
      {
        $item = $this->products_model->get_item_by_color_and_size($style->code, $color_code, $size_code);

        if( !empty($item) )
        {
          $active	= $item->active == 0 ? 'Disactive' : ( $item->can_sell == 0 ? 'Not for sell' : ( $item->is_deleted == 1 ? 'Deleted' : TRUE ) );

          $stock	= $isVisual === FALSE ? ( $active == TRUE ? $this->showStock( $this->stock_model->get_stock($item->code) )  : 0 ) : 0; //---- สต็อกทั้งหมดทุกคลัง
          $qty 		= $isVisual === FALSE ? ( $active == TRUE ? $this->showStock( $this->get_sell_stock($item->code, $warehouse, $zone) ) : 0 ) : FALSE; //--- สต็อกที่สั่งซื้อได้
          //$disabled  = $isVisual === TRUE  && $active == TRUE ? '' : ( ($active !== TRUE OR $qty < 1 ) ? 'disabled' : '');
          $disabled  = ($isVisual OR $overStock) && $active == TRUE ? '' : ( ($active !== TRUE OR ($qty < 1 && $overStock == FALSE)) ? 'disabled' : '');

          if( $qty < 1 && $active === TRUE )
          {
            $txt = '<span class="font-size-12 red">Sold out</span>';
          }
          else
          {
            $txt = $active === TRUE ? '' : '<span class="font-size-12 blue">'.$active.'</span>';
          }

          $available = $qty === FALSE && $active === TRUE ? '' : ( ($qty < 1 || $active !== TRUE ) ? $txt : number($qty));
          $limit = $qty === FALSE OR $overStock === TRUE ? 1000000 : $qty;


          $sc 	.= '<td class="order-grid">';
            $sc .= $view === TRUE ? '<center><span <span class="font-size-10" style="color:#ccc;">'.$color_code.'-'.$size_code.'</span></center>' : '';
            $sc 	.= $isVisual === FALSE ? '<center><span class="font-size-10 blue pointer" onClick="viewStock(\''.$item->code.'\')">('.number($stock).')</span></center>' : '';

            if( $view === FALSE)
            {
              $sc .= '<input type="number" min="1" max="'.$limit.'" ';
              $sc .= 'class="form-control text-center order-grid" ';
              $sc .= 'name="qty['.$item->color_code.']['.$item->code.']" ';
              $sc .= 'id="qty_'.$item->code.'" ';
              $sc .= 'placeholder="'.$color_code.'-'.$size_code.'" ';
              $sc .= 'onkeyup="valid_qty($(this), '.$limit.')" '.$disabled.' />';
            }

            $sc 	.= $isVisual === FALSE ? '<center>'.$available.'</center>' : '';
            $sc 	.= '</td>';
          }
          else
          {
            $sc .= '<td class="order-grid middle">N/A</td>';
          }
        } //--- End foreach $colors

        $sc .= '</tr>';
      } //--- end foreach $sizes

      return $sc;
    }


  public function getAttribute($style_code)
  {
    $sc = array();
    $color = $this->products_model->count_color($style_code);
    $size  = $this->products_model->count_size($style_code);
    if( $color > 0 )
    {
      $sc[] = "color";
    }

    if( $size > 0 )
    {
      $sc[] = "size";
    }
    return $sc;
  }


  public function gridHeader(array $colors)
  {
    $sc = '<tr class="font-size-12"><td class="fix-width-80">&nbsp;</td>';
    foreach( $colors as $code => $name )
    {
      $sc .= '<td class="fix-width-80 text-center middle" style="white-space:normal;">'.$code . '<br/>'. $name.'</td>';
    }

    $sc .= '</tr>';

    return $sc;
  }


  public function getAllColors($style_code)
	{
		$sc = array();
    $colors = $this->products_model->get_all_colors($style_code);
    if($colors !== FALSE)
    {
      foreach($colors as $color)
      {
        $sc[$color->code] = $color->name;
      }
    }

    return $sc;
	}


  public function getAllSizes($style_code)
	{
		$sc = array();
		$sizes = $this->products_model->get_all_sizes($style_code);
		if( $sizes !== FALSE )
		{
      foreach($sizes as $size)
      {
        $sc[$size->code] = $size->name;
      }
		}
		return $sc;
	}


  public function getSizeColor($size_code)
  {
    $colors = array(
      'XS' => '#DFAAA9',
      'S' => '#DFC5A9',
      'M' => '#DEDFA9',
      'L' => '#C3DFA9',
      'XL' => '#A9DFAA',
      '2L' => '#A9DFC5',
      '3L' => '#A9DDDF',
      '5L' => '#A9C2DF',
      '7L' => '#ABA9DF'
    );

    if(isset($colors[$size_code]))
    {
      return $colors[$size_code];
    }

    return FALSE;
  }


  public function getOrderTableWidth($style_code)
  {
    $sc = 600; //--- ชั้นต่ำ
    $tdWidth = 80;  //----- แต่ละช่อง
    $padding = 80; //----- สำหรับช่องแสดงไซส์
    $color = $this->products_model->count_color($style_code);
    if($color > 0)
    {
      $sc = $color * $tdWidth + $padding;
    }

    return $sc;
  }


  public function get_new_code($date)
  {
    $date = $date == '' ? date('Y-m-d') : $date;
    $Y = date('y', strtotime($date));
    $M = date('m', strtotime($date));
    $prefix = getConfig('PREFIX_ORDER');
    $run_digit = getConfig('RUN_DIGIT_ORDER');
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


  public function print_order_sheet_barcode($code)
  {
    $this->load->model('masters/products_model');

    $this->load->library('printer');
    $order = $this->orders_model->get($code);
    $order->customer_name = $this->customers_model->get_name($order->customer_code);
    $details = $this->orders_model->get_order_details($code);

    if(!empty($details))
    {
      foreach($details as $rs)
      {
        $rs->barcode = $this->products_model->get_barcode($rs->product_code);
      }
    }

    $ds['order'] = $order;
    $ds['details'] = $details;
    $this->load->view('print/print_order_sheet_barcode', $ds);
  }


  public function print_order_sheet($code)
  {
    $this->load->model('masters/products_model');
    $this->load->model('masters/slp_model');
    $this->load->model('address/address_model');

    $doc = $this->orders_model->get($code);

    if( ! empty($doc))
    {
      $doc->customer_name = $this->customers_model->get_name($doc->customer_code);
      $details = $this->orders_model->get_order_details($code);
      $addr = $this->address_model->get_shipping_detail($doc->id_address);

      $this->load->library('xprinter');
      $this->load->helper('print');

      $doc->total_rows = 0;
      $row_text = 45;

      if( ! empty($details))
      {
        foreach($details as $rs)
        {
          $rs->use_rows = 1;

          if( ! empty($rs->line_text))
  				{
  					$lines = 1 + substr_count( $rs->line_text, "\n" );
  					$rs->product_name .= empty($rs->product_name) ? nl2br($rs->line_text) : "<br>".nl2br($rs->line_text);
            $length = mb_strlen($rs->product_name);
            $lines += $length > $row_text ? ceil($length/$row_text) * 0.25 : 0.5;
  					$rs->use_rows += $lines;
  				}
          else
          {
            $lines = 0;
            $length = mb_strlen($rs->product_name);
            $lines += $length > $row_text ? ceil($length/$row_text) * 0.25 : 0.5;
            $rs->use_rows += $lines;
          }

          $doc->total_rows += $rs->use_rows;
        }
      }

      $ds = array(
        'title' => "ออเดอร์",
        'order' => $doc,
        'details' => $details,
        'addr' => $addr,
        'sale' => $this->slp_model->get($doc->sale_code)
      );

      $this->load->view('print/print_order_sheet', $ds);
    }
    else
    {
      $this->error_page();
    }
  }


  public function get_sell_stock($item_code, $warehouse = NULL, $zone = NULL)
  {
    $sell_stock = $this->stock_model->get_sell_stock($item_code, $warehouse, $zone);
    $reserv_stock = $this->orders_model->get_reserv_stock($item_code, $warehouse, $zone);
    $availableStock = $sell_stock - $reserv_stock;
		return $availableStock < 0 ? 0 : $availableStock;
  }


  public function get_detail_table($order_code)
  {
    $sc = "no data found";
    $order = $this->orders_model->get($order_code);
    $details = $this->orders_model->get_order_details($order_code);
    if($details != FALSE )
    {
      $no = 1;
      $total_qty = 0;
      $total_discount = 0;
      $total_amount = 0;
      $total_order = 0;
      $ds = array();
      foreach($details as $rs)
      {
        $arr = array(
          "id"		=> $rs->id,
          "no"	=> $no,
          //"imageLink"	=> get_product_image($rs->product_code, 'mini'),
          "productCode"	=> $rs->product_code,
          "productName"	=> $rs->product_name,
          "cost" => round($rs->cost, 2),
          "price"	=> round($rs->price, 2),
          "qty"	=> round($rs->qty, 2),
          "discount"	=> discountLabel($rs->discount1, $rs->discount2, $rs->discount3),
          "discount_amount" => $rs->discount_amount,
          "amount"	=> number_format($rs->total_amount, 2)
        );
        array_push($ds, $arr);

        $total_qty += $rs->qty;
        $total_discount += $rs->discount_amount;
        $total_amount += $rs->total_amount;
        $total_order += $rs->qty * $rs->price;
        $no++;
      }

      $netAmount = ( $total_amount - $order->bDiscAmount ) + $order->shipping_fee + $order->service_fee;

      $arr = array(
        "total_qty" => number($total_qty),
        "order_amount" => number($total_order, 2),
        "total_discount" => number($total_discount, 2),
        "shipping_fee"	=> number($order->shipping_fee,2),
        "service_fee"	=> number($order->service_fee, 2),
        "total_amount" => number($total_amount, 2),
        "net_amount"	=> number($netAmount,2),
        "remark" => $order->remark
      );
      array_push($ds, $arr);

      $sc = json_encode($ds);
    }

    echo $sc;

  }


  public function get_pay_amount()
  {
    $sc = TRUE;
    $ds = array();

    if($this->input->get('order_code'))
    {
      $order = $this->orders_model->get($this->input->get('order_code'));

      if(!empty($order))
      {
        //--- ยอดรวมหลังหักส่วนลด ตาม item
        $amount = $this->orders_model->get_order_total_amount($order->code);

        //--- ส่วนลดท้ายบิล
        $bDisc = $order->bDiscAmount;

        $pay_amount = $amount - $bDisc;

        $ds = array(
          'pay_amount' => $pay_amount,
          'id_sender' => empty($order->id_sender) ? FALSE : $order->id_sender,
          'id_address' => empty($order->id_address) ? FALSE : $order->id_address
        );
      }
      else
      {
        $sc = FALSE;
        $this->error = "Invalid Order code";
      }
    }
    else
    {
      $sc = FALSE;
      $this->error = "Missing required parameter : order code";
    }

    echo $sc === TRUE ? json_encode($ds) : $this->error;
  }


  public function get_account_detail($id)
  {
    $sc = 'fail';
    $this->load->model('masters/bank_model');
    $this->load->helper('bank');
    $rs = $this->bank_model->get_account_detail($id);
    if($rs !== FALSE)
    {
      $ds = bankLogoUrl($rs->bank_code).' | '.$rs->bank_name.' สาขา '.$rs->branch.'<br/>เลขที่บัญชี '.$rs->acc_no.'<br/> ชื่อบัญชี '.$rs->acc_name;
      $sc = $ds;
    }

    echo $sc;
  }


  public function confirm_payment()
  {
    $sc = TRUE;

    if($this->input->post('order_code'))
    {
      $this->load->helper('bank');
      $this->load->model('orders/order_payment_model');

      $file = isset( $_FILES['image'] ) ? $_FILES['image'] : FALSE;
      $order_code = $this->input->post('order_code');
      $date = $this->input->post('payDate');
      $h = $this->input->post('payHour');
      $m = $this->input->post('payMin');
      $dhm = $date.' '.$h.':'.$m.':00';
      $pay_date = db_date($dhm, TRUE);

      $order = $this->orders_model->get($order_code);

      $arr = array(
        'order_code' => $order_code,
        'order_amount' => $this->input->post('orderAmount'),
        'pay_amount' => $this->input->post('payAmount'),
        'pay_date' => $pay_date,
        'id_account' => $this->input->post('id_account'),
        'acc_no' => $this->input->post('acc_no'),
        'user' => $this->_user->uname
      );

      //--- บันทึกรายการ
      if($this->order_payment_model->add($arr))
      {
        if($order->state == 1)
        {
          $rs = $this->orders_model->change_state($order_code, 2);  //--- แจ้งชำระเงิน

          if($rs)
          {
            $arr = array(
              'order_code' => $order_code,
              'state' => 2,
              'update_user' => get_cookie('uname')
            );
            $this->order_state_model->add_state($arr);
          }

          if($rs === FALSE)
          {
            $sc = FALSE;
            $message = 'เปลี่ยนสถานะออเดอร์ไม่สำเร็จ';
          }
        }
      }
      else
      {
        $sc = FALSE;
        $message = 'บันทึกรายการไม่สำเร็จ';
      }

      if($file !== FALSE)
      {
        $rs = $this->do_upload($file, $order_code);
        if($rs !== TRUE)
        {
          $sc = FALSE;
          $message = $rs;
        }
      }
    }

    echo $sc === TRUE ? 'success' : $message;
  }


  public function do_upload($file, $code)
  {
    $this->load->library('upload');
    $sc = TRUE;
    $image_path = $this->config->item('image_path').'payments/';
    $image 	= new Upload($file);
    if( $image->uploaded )
    {
      $image->file_new_name_body = $code; 		//--- เปลี่ยนชือ่ไฟล์ตาม order_code
      $image->image_resize			 = TRUE;		//--- อนุญาติให้ปรับขนาด
      $image->image_retio_fill	 = TRUE;		//--- เติกสีให้เต็มขนาดหากรูปภาพไม่ได้สัดส่วน
      $image->file_overwrite		 = TRUE;		//--- เขียนทับไฟล์เดิมได้เลย
      $image->auto_create_dir		 = TRUE;		//--- สร้างโฟลเดอร์อัตโนมัติ กรณีที่ไม่มีโฟลเดอร์
      $image->image_x					   = 500;		//--- ปรับขนาดแนวนอน
      //$image->image_y					   = 800;		//--- ปรับขนาดแนวตั้ง
      $image->image_ratio_y      = TRUE;  //--- ให้คงสัดส่วนเดิมไว้
      $image->image_background_color	= "#FFFFFF";		//---  เติมสีให้ตามี่กำหนดหากรูปภาพไม่ได้สัดส่วน
      $image->image_convert			= 'jpg';		//--- แปลงไฟล์

      $image->process($image_path);						//--- ดำเนินการตามที่ได้ตั้งค่าไว้ข้างบน

      if( ! $image->processed )	//--- ถ้าไม่สำเร็จ
      {
        $sc 	= $image->error;
      }
    } //--- end if

    $image->clean();	//--- เคลียร์รูปภาพออกจากหน่วยความจำ

    return $sc;
  }


  public function view_payment_detail()
  {
    $this->load->model('orders/order_payment_model');
    $this->load->model('masters/bank_model');
    $sc = TRUE;
    $code = $this->input->post('order_code');
    $rs = $this->order_payment_model->get($code);

    if(!empty($rs))
    {
      $bank = $this->bank_model->get_account_detail($rs->id_account);
      $img  = payment_image_url($code); //--- order_helper
      $ds   = array(
        'order_code' => $code,
        'orderAmount' => number($rs->order_amount, 2),
        'payAmount' => number($rs->pay_amount, 2),
        'payDate' => thai_date($rs->pay_date, TRUE, '/'),
        'bankName' => $bank->bank_name,
        'branch' => $bank->branch,
        'accNo' => $bank->acc_no,
        'accName' => $bank->acc_name,
        'date_add' => thai_date($rs->date_upd, TRUE, '/'),
        'imageUrl' => $img === FALSE ? '' : $img,
        'valid' => "no"
      );
    }
    else
    {
      $sc = FALSE;
    }

    echo $sc === TRUE ? json_encode($ds) : 'fail';
  }


  public function update_shipping_code()
  {
    $order_code = $this->input->post('order_code');
    $ship_code  = $this->input->post('shipping_code');
    if($order_code && $ship_code)
    {
      $rs = $this->orders_model->update_shipping_code($order_code, $ship_code);
      echo $rs === TRUE ? 'success' : 'fail';
    }
  }


  public function save_address()
  {
    $sc = TRUE;
		$customer_code = trim($this->input->post('customer_code'));
		$cus_ref = trim($this->input->post('customer_ref'));

    if(!empty($customer_code) OR !empty($cus_ref))
    {
      $this->load->model('address/address_model');
      $id = $this->input->post('id_address');

      if(!empty($id))
      {
        $arr = array(
          'code' => $cus_ref,
          'customer_code' => $customer_code,
          'name' => trim($this->input->post('name')),
          'address' => trim($this->input->post('address')),
          'sub_district' => trim($this->input->post('sub_district')),
          'district' => trim($this->input->post('district')),
          'province' => trim($this->input->post('province')),
          'postcode' => trim($this->input->post('postcode')),
					'country' => trim($this->input->post('country')),
          'phone' => trim($this->input->post('phone')),
          'email' => trim($this->input->post('email')),
          'alias' => trim($this->input->post('alias'))
        );

        if(! $this->address_model->update_shipping_address($id, $arr))
        {
          $sc = FALSE;
          $this->error = 'แก้ไขที่อยู่ไม่สำเร็จ';
        }

      }
      else
      {
        $arr = array(
          'address_code' => '0000', //$this->address_model->get_new_code($this->input->post('customer_ref')),
          'code' => $cus_ref,
          'customer_code' => $customer_code,
          'name' => trim($this->input->post('name')),
          'address' => trim($this->input->post('address')),
          'sub_district' => trim($this->input->post('sub_district')),
          'district' => trim($this->input->post('district')),
          'province' => trim($this->input->post('province')),
          'postcode' => trim($this->input->post('postcode')),
					'country' => trim($this->input->post('country')),
          'phone' => trim($this->input->post('phone')),
          'email' => trim($this->input->post('email')),
          'alias' => trim($this->input->post('alias'))
        );

        $rs = $this->address_model->add_shipping_address($arr);

        if($rs === FALSE)
        {
          $sc = FALSE;
          $this->error = 'เพิ่มที่อยู่ไม่สำเร็จ';
        }

      }
    }
    else
    {
      $sc = FALSE;
      $this->error = 'Missing required parameter : customer code';
    }

    echo $sc === TRUE ? 'success' : $this->error;
  }


  public function get_address_table()
  {
    $sc = TRUE;

		$customer_code = trim($this->input->post('customer_code'));
		$customer_ref = trim($this->input->post('customer_ref'));

    if(!empty($customer_code) OR !empty($customer_ref))
    {
			$ds = array();
			$this->load->model('address/address_model');
			$adrs = empty($customer_ref) ? $this->address_model->get_ship_to_address($customer_code) : $this->address_model->get_shipping_address($customer_ref);
			if(!empty($adrs))
			{
				foreach($adrs as $rs)
				{
					$arr = array(
						'id' => $rs->id,
						'name' => $rs->name,
						'address' => $rs->address.' '.$rs->sub_district.' '.$rs->district.' '.$rs->province.' '.$rs->postcode.' '.$rs->country,
						'phone' => $rs->phone,
						'email' => $rs->email,
						'alias' => $rs->alias,
						'default' => $rs->is_default == 1 ? 1 : ''
					);
					array_push($ds, $arr);
				}
			}
			else
			{
				$sc = FALSE;
			}
    }

    echo $sc === TRUE ? json_encode($ds) : 'noaddress';
  }


  public function set_default_address()
  {
    $this->load->model('address/address_model');
    $id = $this->input->post('id_address');
    $code = $this->input->post('customer_ref');
    //--- drop current
    $this->address_model->unset_default_shipping_address($code);

    //--- set new default
    $rs = $this->address_model->set_default_shipping_address($id);
    echo $rs === TRUE ? 'success' :'fail';
  }


	public function set_address()
	{
		$sc = TRUE;
		$order_code = $this->input->post('order_code');
		$id_address = $this->input->post('id_address');

		$arr = array(
			'id_address' => $id_address
		);

		if(! $this->orders_model->update($order_code, $arr))
		{
			$sc = FALSE;
			$this->error = "Update failed";
		}

		echo $sc === TRUE ? 'success' : $this->error;
	}


	public function set_sender()
	{
		$sc = TRUE;
		$order_code = trim($this->input->post('order_code'));
		$id_sender = trim($this->input->post('id_sender'));

		$arr = array(
			'id_sender' => $id_sender
		);

		if(! $this->orders_model->update($order_code, $arr))
		{
			$sc = FALSE;
			$this->error = "Update failed";
		}

		echo $sc === TRUE ? 'success' : $this->error;
	}


  public function get_shipping_address()
  {
    $this->load->model('address/address_model');
    $id = $this->input->post('id_address');
    $rs = $this->address_model->get_shipping_detail($id);
    if(!empty($rs))
    {
      $arr = array(
        'id' => $rs->id,
        'code' => $rs->code,
        'name' => $rs->name,
        'address' => $rs->address,
        'sub_district' => $rs->sub_district,
        'district' => $rs->district,
        'province' => $rs->province,
        'postcode' => $rs->postcode,
				'country' => $rs->country,
        'phone' => $rs->phone,
        'email' => $rs->email,
        'alias' => $rs->alias,
        'is_default' => $rs->is_default
      );

      echo json_encode($rs);
    }
    else
    {
      echo 'nodata';
    }
  }


  public function delete_shipping_address()
  {
    $this->load->model('address/address_model');
    $id = $this->input->post('id_address');
    $rs = $this->address_model->delete_shipping_address($id);
    echo $rs === TRUE ? 'success' : 'fail';
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
    $sc = TRUE;
    $code = $this->input->get('order_code');
    $order = $this->orders_model->get($code);

    if(!empty($order))
    {
      if($order->role == 'U' OR $order->role == 'P')
      {
        if($order->role == 'U')
        {
          $this->load->model('orders/support_model');
          $total_amount = $this->orders_model->get_order_total_amount($code);
          $current = $this->support_model->get_budget($order->customer_code);
          $used = $this->support_model->get_budget_used($order->customer_code);

          $balance = $current - $used;

          if($total_amount > $balance)
          {
            $sc = FALSE;
            $this->error = "งบประมาณไม่เพียงพอ";
          }
        }

        if($order->role == 'P')
        {
          $this->load->model('orders/sponsor_model');
          $total_amount = $this->orders_model->get_order_total_amount($code);
          $current = $this->sponsor_model->get_budget($order->customer_code);
          $used = $this->sponsor_model->get_budget_used($order->customer_code);

          $balance = $current - $used;

          if($total_amount > $balance)
          {
            $sc = FALSE;
            $this->error = "งบประมาณไม่เพียงพอ";
          }
        }
      }
    }
    else
    {
      $sc = FALSE;
      $this->error = "Invalid order number";
    }

    if($sc === TRUE)
    {
      if( ! $this->orders_model->un_expired($code))
      {
        $sc = FALSE;
        $this->error = "ทำรายการไม่สำเร็จ";
      }
    }

    echo $sc === TRUE ? 'success' : $this->error;
  }


  public function do_approve($code)
  {
    $sc = TRUE;
    $this->load->model('approve_logs_model');
    $order = $this->orders_model->get($code);
    if(!empty($order))
    {
      if($order->state == 1)
      {
        $user = get_cookie('uname');
        $rs = $this->orders_model->update_approver($code, $user);
        if(! $rs)
        {
          $sc = FALSE;
          $this->error = "อนุมัติไม่สำเร็จ";
        }
        else
        {
          $this->approve_logs_model->add($code, 1, $user);
        }
      }
      else
      {
        $sc = FALSE;
        $this->error = "สถานะเอกสารไม่ถูกต้อง";
      }
    }
    else
    {
      $sc = FALSE;
      $this->error = "ไม่พบเลขที่เอกสาร";
    }


    echo $sc === TRUE ? 'success' : $this->error;
  }


  public function un_approve($code)
  {
    $sc = TRUE;
    $this->load->model('approve_logs_model');
    $order = $this->orders_model->get($code);
    if(!empty($order))
    {
      if($order->state == 1 )
      {
        $user = get_cookie('uname');
        $rs = $this->orders_model->un_approver($code, $user);
        if(! $rs)
        {
          $sc = FALSE;
          $this->error = "อนุมัติไม่สำเร็จ";
        }
        else
        {
          $this->approve_logs_model->add($code, 0, $user);
        }
      }
      else
      {
        $sc = FALSE;
        $this->error = "สถานะเอกสารไม่ถูกต้อง";
      }
    }
    else
    {
      $sc = FALSE;
      $this->error = "ไม่พบเลขที่เอกสาร";
    }


    echo $sc === TRUE ? 'success' : $this->error;
  }


  public function order_state_change()
  {
    $sc = TRUE;

    if($this->input->post('order_code'))
    {
      $code = $this->input->post('order_code');
      $state = $this->input->post('state');
      $order = $this->orders_model->get($code);
			$reason = $this->input->post('cancle_reason');

      if(! empty($order))
      {
        if($order->role == 'S' OR $order->role == 'C' OR $order->role == 'P' OR $order->role == 'U')
        {
          $sap = $this->orders_model->get_sap_doc_num($order->code);
					if(!empty($sap))
					{
						echo 'กรุณายกเลิกใบส่งสินค้า SAP ก่อนย้อนสถานะ';
						exit;
					}

          if( ! empty($order->invoice_code))
          {
            echo "เอกสารเปิดใบกำกับแล้วไม่สามารถยกเลิกได้ : {$order->invoice_code}";
            exit;
          }
        }


				if($order->role == 'T' OR $order->role == 'L' OR $order->role == 'Q' OR $order->role == 'N')
				{
					$this->load->model('inventory/transfer_model');
					$sap = $this->transfer_model->get_sap_transfer_doc($code);
					if(! empty($sap))
					{
						echo "กรุณายกเลิกใบโอนสินค้าใน SAP ก่อนย้อนสถานะ";
						exit;
					}
				}


        //--- ถ้าเป็นเบิกแปรสภาพ จะมีการผูกสินค้าไว้
        if($order->role == 'T')
        {
          $this->load->model('inventory/transform_model');
          //--- หากมีการรับสินค้าที่ผูกไว้แล้วจะไม่อนุญาติให้เปลี่ยนสถานะใดๆ
          $is_received = $this->transform_model->is_received($code);
          if($is_received === TRUE)
          {
            echo 'ใบเบิกมีการรับสินค้าแล้วไม่อนุญาติให้ย้อนสถานะ';
						exit;
          }
        }

        //--- ถ้าเป็นยืมสินค้า
        if($order->role == 'L')
        {
          $this->load->model('inventory/lend_model');
          //--- หากมีการรับสินค้าที่ผูกไว้แล้วจะไม่อนุญาติให้เปลี่ยนสถานะใดๆ
          $is_received = $this->lend_model->is_received($code);
          if($is_received === TRUE)
          {
            echo 'ใบเบิกมีการรับคืนสินค้าแล้วไม่อนุญาติให้ย้อนสถานะ';
						exit;
          }
        }


        if($sc === TRUE)
        {
          $this->db->trans_begin();

          //--- ถ้าเปิดบิลแล้ว
          if($sc === TRUE && $order->state == 8)
          {

            if($state < 8)
            {
              if(! $this->roll_back_action($code, $order->role) )
              {
                $sc = FALSE;
              }
            }
            else if($state == 9)
            {
              if( ! $this->cancle_order($code, $order->role, $order->state, $reason) )
              {
                $sc = FALSE;
              }
            }
          }
          else if($sc === TRUE && $order->state != 8)
          {
            if($state == 9)
            {
              if(! $this->cancle_order($code, $order->role, $order->state, $reason) )
              {
                $sc = FALSE;
              }
            }
          }

          if($sc === TRUE)
          {

            $rs = $this->orders_model->change_state($code, $state);

            if($rs)
            {
              $arr = array(
                'order_code' => $code,
                'state' => $state,
                'update_user' => get_cookie('uname')
              );

              if(! $this->order_state_model->add_state($arr) )
              {
                $sc = FALSE;
                $this->error = "Add state failed";
              }

            }
            else
            {
              $sc = FALSE;
              $this->error = "เปลี่ยนสถานะไม่สำเร็จ";
            }
          }

          if($sc === TRUE)
          {
            $this->db->trans_commit();

            if($sc === TRUE && ! empty($order->so_code))
            {
              $this->unlink_so($order->so_code, $order->code);
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
        $this->error = 'ไม่พบข้อมูลออเดอร์';
      }
    }
    else
    {
      $sc = FALSE;
      $this->error = 'ไม่พบเลขที่เอกสาร';
    }

    echo $sc === TRUE ? 'success' : $this->error;
  }


  public function unlink_so($so_code, $order_code)
  {
    $this->load->model('orders/sales_order_model');

    $details = $this->sales_order_model->get_details($so_code);

    if( ! empty($details))
    {
      foreach($details as $rs)
      {
        if( ! $this->sales_order_model->has_linked($rs->id))
        {
          $arr = array(
            'linked' => 'N',
            'ref_code' => NULL
          );

          $this->sales_order_model->update_detail($rs->id, $arr);
        }
      }
    }
  }


  public function roll_back_action($code, $role)
  {
    $this->load->model('inventory/movement_model');
    $this->load->model('inventory/buffer_model');
    $this->load->model('inventory/cancle_model');
    $this->load->model('inventory/invoice_model');
    $this->load->model('inventory/transform_model');
    $this->load->model('inventory/transfer_model');
    $this->load->model('inventory/lend_model');
    $this->load->model('inventory/delivery_order_model');

    $sc = TRUE;

    //---- set is_complete = 0
    if( ! $this->orders_model->un_complete($code) )
    {
      $sc = FALSE;
      $this->error = "Uncomplete details failed";
    }

    //--- set inv_code to NULL
    if($sc === TRUE)
    {
      $arr = array(
        'is_valid' => 0,
        'is_exported' => 0,
        'inv_code' => NULL
      );

      if(! $this->orders_model->update($code, $arr))
      {
        $sc = FALSE;
        $this->error = "Clear Inv code failed";
      }
    }


    //---- move cancle product back to  buffer
    if($sc === TRUE)
    {
      if(! $this->cancle_model->restore_buffer($code) )
      {
        $sc = FALSE;
        $this->error = "Restore cancle failed";
      }
    }

    //--- remove movement
    if($sc === TRUE)
    {
      if(! $this->movement_model->drop_movement($code) )
      {
        $sc = FALSE;
        $this->error = "Drop movement failed";
      }
    }


    if($sc === TRUE)
    {
      //--- restore sold product back to buffer
      $sold = $this->invoice_model->get_details($code);

      if(!empty($sold))
      {
        foreach($sold as $rs)
        {
          if($sc === FALSE)
          {
            break;
          }

          if($rs->is_count == 1)
          {
            //---- restore_buffer
            if($this->buffer_model->is_exists($rs->reference, $rs->product_code, $rs->zone_code, $rs->order_detail_id) === TRUE)
            {
              if(! $this->buffer_model->update($rs->reference, $rs->product_code, $rs->zone_code, $rs->qty, $rs->order_detail_id))
              {
                $sc = FALSE;
                $this->error = "Restore buffer (update) failed";
              }
            }
            else
            {
              $ds = array(
                'order_code' => $rs->reference,
                'product_code' => $rs->product_code,
                'warehouse_code' => $rs->warehouse_code,
                'zone_code' => $rs->zone_code,
                'qty' => $rs->qty,
                'user' => $rs->user,
                'order_detail_id' => $rs->order_detail_id
              );

              if(! $this->buffer_model->add($ds) )
              {
                $sc = FALSE;
                $this->error = "Restore buffer (add) failed";
              }
            }

            //--- roll back billed_qty in order_details
            if($sc === TRUE)
            {
              if( ! $this->orders_model->update_billed_qty($rs->order_detail_id , ($rs->qty * -1)))
              {
                $sc = FALSE;
                $this->error = "Failed to roll back billed qty";
              }
            }
          }

          if($sc === TRUE)
          {
            if( !$this->invoice_model->drop_sold($rs->id) )
            {
              $sc = FALSE;
              $this->error = "Drop sold data failed";
            }

            //------ หากเป็นออเดอร์เบิกแปรสภาพ
            if($role == 'T')
            {
              if( ! $this->transform_model->reset_sold_qty($code) )
              {
                $sc = FALSE;
                $this->error = "Reset Transform sold qty failed";
              }
            }

            //-- หากเป็นออเดอร์ยืม
            if($role == 'L')
            {
              if(! $this->lend_model->drop_backlogs_list($code) )
              {
                $sc = FALSE;
                $this->error = "Drop lend backlogs failed";
              }
            }
          }

        } //--- end foreach
      } //---- end sold


      if($sc === TRUE)
      {
        //---- Delete Middle Temp
        //---- ถ้าเป็นฝากขายโอนคลัง ตามไปลบ transfer draft ที่ยังไม่เอาเข้าด้วย
        if($role == 'N')
        {
          $middle = $this->transfer_model->get_middle_transfer_draft($code);
          if(!empty($middle))
          {
            foreach($middle as $rows)
            {
              $this->transfer_model->drop_middle_transfer_draft($rows->DocEntry);
            }
          }
        }
        else if($role == 'T' OR $role == 'Q' OR $role == 'L')
        {
          $middle = $this->transfer_model->get_middle_transfer_doc($code);
          if(!empty($middle))
          {
            foreach($middle as $rows)
            {
              $this->transfer_model->drop_middle_exits_data($rows->DocEntry);
            }
          }
        }
        else
        {
          //---- ถ้าออเดอร์ยังไม่ถูกเอาเข้า SAP ลบออกจากถังกลางด้วย
          $middle = $this->delivery_order_model->get_middle_delivery_order($code);
          if(!empty($middle))
          {
            foreach($middle as $rows)
            {
              $this->delivery_order_model->drop_middle_exits_data($rows->DocEntry);
            }
          }
        }
      }
    }

    return $sc;
  }


  public function cancle_order($code, $role, $state, $cancle_reason = NULL)
  {
    $this->load->model('inventory/prepare_model');
    $this->load->model('inventory/qc_model');
    $this->load->model('inventory/transform_model');
    $this->load->model('inventory/transfer_model');
    $this->load->model('inventory/delivery_order_model');
    $this->load->model('inventory/invoice_model');
    $this->load->model('inventory/buffer_model');
    $this->load->model('inventory/cancle_model');
		$this->load->model('inventory/movement_model');
    $this->load->model('masters/zone_model');

    $sc = TRUE;

		if(!empty($cancle_reason))
		{
			//----- add reason to table order_cancle_reason
			$reason = array(
				'code' => $code,
				'reason' => $cancle_reason,
				'user' => $this->_user->uname
			);

			$this->orders_model->add_cancle_reason($reason);
		}

    if($state > 3 && $sc === TRUE)
    {
      //--- put prepared product to cancle zone
      $prepared = $this->prepare_model->get_details($code);

      if(!empty($prepared))
      {
        foreach($prepared AS $rs)
        {
          if($sc === FALSE)
          {
            break;
          }

          $zone = $this->zone_model->get($rs->zone_code);

          $arr = array(
            'order_code' => $rs->order_code,
            'product_code' => $rs->product_code,
            'warehouse_code' => empty($zone->warehouse_code) ? NULL : $zone->warehouse_code,
            'zone_code' => $rs->zone_code,
            'qty' => $rs->qty,
            'user' => $this->_user->uname,
            'order_detail_id' => $rs->order_detail_id
          );

          if( ! $this->cancle_model->add($arr) )
          {
            $sc = FALSE;
            $this->error = "Move Items to Cancle failed";
          }
        }
      }

      //--- drop sold data
      if($sc === TRUE)
      {
        if(! $this->invoice_model->drop_all_sold($code) )
        {
          $sc = FALSE;
          $this->error = "Drop sold data failed";
        }
      }
    }

    if($sc === TRUE)
    {
      //---- เมื่อมีการยกเลิกออเดอร์
      //--- 1. เคลียร์ buffer
      if(! $this->buffer_model->delete_all($code) )
      {
        $sc = FALSE;
        $this->error = "Delete buffer failed";
      }

      //--- 2. ลบประวัติการจัดสินค้า
      if($sc === TRUE)
      {
        if(! $this->prepare_model->clear_prepare($code) )
        {
          $sc = FALSE;
          $this->error = "Delete prepared data failed";
        }
      }


      //--- 3. ลบประวัติการตรวจสินค้า
      if($sc === TRUE)
      {
        if( ! $this->qc_model->clear_qc($code) )
        {
          $sc = FALSE;
          $this->error = "Delete QC failed";
        }
        else
        {
          $this->orders_model->update_details($code, ['valid_qc' => '0']);
        }
      }

			//--- remove movement
	    if($sc === TRUE)
	    {
	      if(! $this->movement_model->drop_movement($code) )
	      {
	        $sc = FALSE;
	        $this->error = "Drop movement failed";
	      }
	    }


      //--- 4. set รายการสั่งซื้อ ให้เป็น ยกเลิก
      if($sc === TRUE)
      {
        if(! $this->orders_model->cancle_order_detail($code) )
        {
          $sc = FALSE;
          $this->error = "Cancle Order details failed";
        }
      }


      //--- 5. ยกเลิกออเดอร์
      if($sc === TRUE)
      {
        $arr = array(
          'status' => 2,
          'inv_code' => NULL,
          'is_exported' => 0
        );

        if(! $this->orders_model->update($code, $arr) )
        {
          $sc = FALSE;
          $this->error = "Change order status failed";
        }
      }


      if($sc === TRUE)
      {
        //--- 6. ลบรายการที่ผู้ไว้ใน order_transform_detail (กรณีเบิกแปรสภาพ)
        if($role == 'T' OR $role == 'Q')
        {
          if(! $this->transform_model->clear_transform_detail($code) )
          {
            $sc = FALSE;
            $this->error = "Clear Transform backlogs failed";
          }

          $this->transform_model->close_transform($code);
        }

        //-- หากเป็นออเดอร์ยืม
        if($role == 'L')
        {
          if(! $this->lend_model->drop_backlogs_list($code) )
          {
            $sc = FALSE;
            $this->error = "Drop Lend backlogs failed";
          }
        }

        //---- ถ้าเป็นฝากขายโอนคลัง ตามไปลบ transfer draft ที่ยังไม่เอาเข้าด้วย
        if($role == 'N')
        {
          $middle = $this->transfer_model->get_middle_transfer_draft($code);
          if(!empty($middle))
          {
            foreach($middle as $rows)
            {
              $this->transfer_model->drop_middle_transfer_draft($rows->DocEntry);
            }
          }
        }
        else if($role == 'T' OR $role == 'Q' OR $role == 'L')
        {
          $middle = $this->transfer_model->get_middle_transfer_doc($code);
          if(!empty($middle))
          {
            foreach($middle as $rows)
            {
              $this->transfer_model->drop_middle_exits_data($rows->DocEntry);
            }
          }
        }
        else
        {
          //---- ถ้าออเดอร์ยังไม่ถูกเอาเข้า SAP ลบออกจากถังกลางด้วย
          $middle = $this->delivery_order_model->get_middle_delivery_order($code);
          if(!empty($middle))
          {
            foreach($middle as $rows)
            {
              $this->delivery_order_model->drop_middle_exits_data($rows->DocEntry);
            }
          }
        }
      }
    }

    return $sc;
  }


  //--- เคลียร์ยอดค้างที่จัดเกินมาไปที่ cancle หรือ เคลียร์ยอดที่เป็น 0
  public function clear_buffer($code)
  {
    $this->load->model('inventory/buffer_model');
    $this->load->model('inventory/cancle_model');

    $buffer = $this->buffer_model->get_all_details($code);
    //--- ถ้ายังมีรายการที่ค้างอยู่ใน buffer เคลียร์เข้า cancle
    if(!empty($buffer))
    {
      foreach($buffer as $rs)
      {
        if($rs->qty != 0)
        {
          $arr = array(
            'order_code' => $rs->order_code,
            'product_code' => $rs->product_code,
            'warehouse_code' => $rs->warehouse_code,
            'zone_code' => $rs->zone_code,
            'qty' => $rs->qty,
            'user' => $this->_user->uname,
            'order_detail_id' => $rs->order_detail_id
          );

          //--- move buffer to cancle
          $this->cancle_model->add($arr);
        }

        //--- delete cancle
        $this->buffer_model->delete($rs->id);
      }
    }
  }


  public function update_discount()
  {
    $code = $this->input->post('order_code');
    $discount = $this->input->post('discount');
    $approver = $this->input->post('approver');
    $order = $this->orders_model->get($code);
    $user = get_cookie('uname');
    $this->load->model('orders/discount_logs_model');
  	if(!empty($discount))
  	{
  		foreach( $discount as $id => $value )
  		{
  			//----- ข้ามรายการที่ไม่ได้กำหนดค่ามา
  			if( $value != "")
  			{
  				//--- ได้ Obj มา
  				$detail = $this->orders_model->get_detail($id);

  				//--- ถ้ารายการนี้มีอยู่
  				if( $detail !== FALSE )
  				{
  					//------ คำนวณส่วนลดใหม่
  					$step = explode('+', $value);
  					$discAmount = 0;
  					$discLabel = array(0, 0, 0);
  					$price = $detail->price;
  					$i = 0;
  					foreach($step as $discText)
  					{
  						if($i < 3) //--- limit ไว้แค่ 3 เสต็ป
  						{
                $discText = str_replace(' ', '', $discText);
                $discText = str_replace('๔', '%', $discText);
  							$disc = explode('%', $discText);
  							$disc[0] = trim($disc[0]); //--- ตัดช่องว่างออก
  							$discount = count($disc) == 1 ? floatval($disc[0]) : $price * (floatval($disc[0]) * 0.01); //--- ส่วนลดต่อชิ้น
  							$discLabel[$i] = count($disc) == 1 ? $disc[0] : number($disc[0], 2).'%';
  							$discAmount += $discount;
  							$price -= $discount;
  						}
  						$i++;
  					}

  					$total_discount = $detail->qty * $discAmount; //---- ส่วนลดรวม
  					$total_amount = ( $detail->qty * $detail->price ) - $total_discount; //--- ยอดรวมสุดท้าย

  					$arr = array(
  								"discount1" => $discLabel[0],
  								"discount2" => $discLabel[1],
  								"discount3" => $discLabel[2],
  								"discount_amount"	=> $total_discount,
  								"total_amount" => $total_amount ,
  								"id_rule"	=> NULL,
                  "update_user" => $user
  							);

  					$cs = $this->orders_model->update_detail($id, $arr);
            if($cs)
            {
              $log_data = array(
    												"order_code"		=> $code,
    												"product_code"	=> $detail->product_code,
    												"old_discount"	=> discountLabel($detail->discount1, $detail->discount2, $detail->discount3),
    												"new_discount"	=> discountLabel($discLabel[0], $discLabel[1], $discLabel[2]),
    												"user"	=> $user,
    												"approver"		=> $approver
    												);
    					$this->discount_logs_model->logs_discount($log_data);
            }

  				}	//--- end if detail
  			} //--- End if value
  		}	//--- end foreach

      $this->orders_model->set_status($code, 0);
  	}
    echo 'success';
  }


  public function update_gp()
  {
    $code = $this->input->post('code');
    $gp = $this->input->post('gp');
    $details = $this->orders_model->get_order_details($code);
    $user = get_cookie('uname');
    $this->load->model('orders/discount_logs_model');

    if(!empty($details))
    {
      foreach($details as $detail)
      {
        //------ คำนวณส่วนลดใหม่
        $step = explode('+', $gp);
        $discAmount = 0;
        $discLabel = array(0, 0, 0);
        $price = $detail->price;
        $i = 0;
        foreach($step as $discText)
        {
          if($i < 3) //--- limit ไว้แค่ 3 เสต็ป
          {
            $disc = explode('%', $discText);
            $disc[0] = trim($disc[0]); //--- ตัดช่องว่างออก
            $discount = count($disc) == 1 ? $disc[0] : $price * ($disc[0] * 0.01); //--- ส่วนลดต่อชิ้น
            $discLabel[$i] = count($disc) == 1 ? $disc[0] : $disc[0].'%';
            $discAmount += $discount;
            $price -= $discount;
          }
          $i++;
        }

        $total_discount = $detail->qty * $discAmount; //---- ส่วนลดรวม
        $total_amount = ( $detail->qty * $detail->price ) - $total_discount; //--- ยอดรวมสุดท้าย

        $arr = array(
              "discount1" => $discLabel[0],
              "discount2" => $discLabel[1],
              "discount3" => $discLabel[2],
              "discount_amount"	=> $total_discount,
              "total_amount" => $total_amount ,
              "id_rule"	=> NULL,
              "update_user" => $user
            );

        $cs = $this->orders_model->update_detail($detail->id, $arr);
        if($cs)
        {
          $log_data = array(
                        "order_code"		=> $code,
                        "product_code"	=> $detail->product_code,
                        "old_discount"	=> discountLabel($detail->discount1, $detail->discount2, $detail->discount3),
                        "new_discount"	=> discountLabel($discLabel[0], $discLabel[1], $discLabel[2]),
                        "user"	=> $user,
                        "approver"		=> get_cookie('uname')
                        );
          $this->discount_logs_model->logs_discount($log_data);
        }
      }

      $this->orders_model->set_status($code, 0);
    }

    echo 'success';
  }


  public function update_non_count_price()
  {
    $code = $this->input->post('order_code');
    $id = $this->input->post('id_order_detail');
    $price = $this->input->post('price');
    $user = get_cookie('uname');

    $order = $this->orders_model->get($code);
    if($order->state == 8) //--- ถ้าเปิดบิลแล้ว
    {
      echo 'ไม่สามารถแก้ไขราคาได้ เนื่องจากออเดอร์ถูกเปิดบิลไปแล้ว';
    }
    else
    {
        //----- ข้ามรายการที่ไม่ได้กำหนดค่ามา
        if( $price != "" )
        {
          //--- ได้ Obj มา
          $detail = $this->orders_model->get_detail($id);

          //--- ถ้ารายการนี้มีอยู่
          if( $detail !== FALSE )
          {
            //------ คำนวณส่วนลดใหม่
            $price_c = $price;
  					$discAmount = 0;
            $step = array($detail->discount1, $detail->discount2, $detail->discount3);
            foreach($step as $discount)
            {
              $disc 	= explode('%', $discount);
              $disc[0] = trim($disc[0]); //--- ตัดช่องว่างออก
              $discount = count($disc) == 1 ? $disc[0] : $price_c * ($disc[0] * 0.01); //--- ส่วนลดต่อชิ้น
              $discAmount += $discount;
              $price_c -= $discount;
            }

            $total_discount = $detail->qty * $discAmount; //---- ส่วนลดรวม
  					$total_amount = ( $detail->qty * $price ) - $total_discount; //--- ยอดรวมสุดท้าย

            $arr = array(
                  "price"	=> $price,
                  "discount_amount"	=> $total_discount,
                  "total_amount" => $total_amount,
                  "update_user" => $user
                );
            $cs = $this->orders_model->update_detail($id, $arr);
          }	//--- end if detail
        } //--- End if value

        $this->orders_model->set_status($code, 0);

      echo 'success';
    }
  }


  public function update_price()
  {
    $code = $this->input->post('order_code');
    $ds = $this->input->post('price');
  	$approver	= $this->input->post('approver');
  	$user = get_cookie('uname');
    $this->load->model('orders/discount_logs_model');
  	foreach( $ds as $id => $value )
  	{
  		//----- ข้ามรายการที่ไม่ได้กำหนดค่ามา
  		if( $value != "" )
  		{
  			//--- ได้ Obj มา
  			$detail = $this->orders_model->get_detail($id);

  			//--- ถ้ารายการนี้มีอยู่
  			if( $detail !== FALSE )
  			{
					//------ คำนวณส่วนลดใหม่
					$price 	= $value;
					$discAmount = 0;
					$step = array($detail->discount1, $detail->discount2, $detail->discount3);
					foreach($step as $discount_text)
					{
						$disc 	= explode('%', $discount_text);
						$disc[0] = trim($disc[0]); //--- ตัดช่องว่างออก
						$discount = count($disc) == 1 ? $disc[0] : $price * ($disc[0] * 0.01); //--- ส่วนลดต่อชิ้น
						$discAmount += $discount;
						$price -= $discount;
					}

					$total_discount = $detail->qty * $discAmount; //---- ส่วนลดรวม
					$total_amount = ( $detail->qty * $value ) - $total_discount; //--- ยอดรวมสุดท้าย

					$arr = array(
						'price' => $value,
						'discount_amount' => $total_discount,
						'total_amount' => $total_amount,
						'update_user' => $user
					);

					$cs = $this->orders_model->update_detail($id, $arr);
					if($cs)
					{
						$log_data = array(
							"order_code"		=> $code,
							"product_code"	=> $detail->product_code,
							"old_price"	=> $detail->price,
							"new_price"	=> $value,
							"user"	=> $user,
							"approver"		=> $approver
						);
						$this->discount_logs_model->logs_price($log_data);
					}

  			}	//--- end if detail
  		} //--- End if value
  	}	//--- end foreach

    $this->orders_model->set_status($code, 0);

  	echo 'success';
  }


  public function get_summary()
  {
    $this->load->model('masters/bank_model');
    $code = $this->input->post('order_code');
    $order = $this->orders_model->get($code);
    $details = $this->orders_model->get_order_details($code);
    $bank = $this->bank_model->get_active_bank();
    if(!empty($details))
    {
      echo get_summary($order, $details, $bank); //--- order_helper;
    }
  }


  public function get_available_stock($item)
  {
    $sell_stock = $this->stock_model->get_sell_stock($item);
    $reserv_stock = $this->orders_model->get_reserv_stock($item);
    $availableStock = $sell_stock - $reserv_stock;
    return $availableStock < 0 ? 0 : $availableStock;
  }


  public function load_so()
  {
    $sc = TRUE;
    $this->load->model('orders/sales_order_model');

    $code = $this->input->post('code');
    $so_code = $this->input->post('so_code');
    $details = json_decode($this->input->post('details'));

    if( ! empty($code) && ! empty($so_code))
    {
      $order = $this->orders_model->get($code);

      if( ! empty($order) && $order->state == 1)
      {
        $so = $this->sales_order_model->get($so_code);

        if( ! empty($so) && $so->status == 'O')
        {
          if( ! empty($details))
          {
            $this->db->trans_begin();

            $arr = array(
              'is_term' => $so->is_term,
              'vat_type' => $so->vat_type,
              'TaxStatus' => $so->TaxStatus,
              'customer_code' => $so->customer_code,
              'customer_name' => $so->customer_name,
              'customer_ref' => $so->customer_ref,
              'tax_id' => $so->tax_id,
              'branch_code' => $so->branch_code,
              'branch_name' => $so->branch_name,
              'address' => $so->address,
              'sub_district' => $so->sub_district,
              'district' => $so->district,
              'province' => $so->province,
              'postcode' => $so->postcode,
              'phone' => $so->phone,
              'bDiscText' => $so->DiscPrcnt,
              'WhtPrcnt' => $so->WhtPrcnt,
              'BaseRef' => $so->code,
              'BaseType' => 'SO',
              'BaseId' => $so->id,
              'so_code' => $so_code,
              'reference' => $so_code
            );

            if( ! $this->orders_model->update($code, $arr))
            {
              $sc = FALSE;
              $this->error = "Failed to link Sales order number";
            }

            if( $sc === TRUE)
            {
              //---- remove current rows
              $this->load->model('inventory/buffer_model');
              $this->load->model('inventory/prepare_model');
              $this->load->model('inventory/qc_model');

              if( ! $this->buffer_model->drop_buffer($code))
              {
                $sc = FALSE;
                $this->error = "Failed to delete current buffer";
              }

              if( $sc === TRUE && ! $this->prepare_model->drop_prepare($code))
              {
                $sc = FALSE;
                $this->error = "Failed to delete prepared logs";
              }

              if($sc === TRUE && ! $this->qc_model->drop_qc($code))
              {
                $sc = FALSE;
                $this->error = "Failed to delte qc data";
              }

              if($sc === TRUE && ! $this->orders_model->drop_details($code))
              {
                $sc = FAlSE;
                $this->error = "Failed to delete current rows";
              }
            }

            $docTotal = 0;
            $vatSum = 0;
            $discSum = 0;

            if($sc === TRUE)
            {
              foreach($details as $rs)
              {
                $discount_amount = $rs->discount_amount * $rs->qty;
                $total_amount = $rs->qty * $rs->sell_price;
                $sumBillDiscAmount = $rs->avgBillDiscAmount * $total_amount;
                $amountAfDisc = $total_amount - $sumBillDiscAmount;
                $vat_amount = get_vat_amount($amountAfDisc, $rs->vat_rate, $so->vat_type);

                $arr = array(
                  "id_order" =>  $order->id,
                  "order_code"	=> $order->code,
                  "style_code"		=> $rs->style_code,
                  "product_code"	=> $rs->product_code,
                  "product_name"	=> $rs->product_name,
                  "cost"  => $rs->cost,
                  "price"	=> $rs->price,
                  "qty"		=> $rs->qty,
                  "discount1"	=> $rs->discount_label,
                  "discount2" => 0,
                  "discount3" =>0,
                  "discount_amount" => $discount_amount,
                  "total_amount"	=> $total_amount,
                  "avgBillDiscAmount" => $rs->avgBillDiscAmount,
                  "sumBillDiscAmount" => $sumBillDiscAmount,
                  "vat_type" => $so->vat_type,
                  "vat_code" => $rs->vat_code,
                  "vat_rate" => $rs->vat_rate,
                  "vat_amount" => $vat_amount,
                  "baseCode" => $rs->baseCode, //--- sales_order_code
                  "baseLine" => $rs->baseLine, //--- sales_order_detail_id
                  "baseId" => $rs->baseId, //--- sales_order_id
                  "line_id" => $rs->line_id,
                  "is_count" => $rs->is_count
                );

                if( ! $this->orders_model->add_detail($arr) )
                {
                  $sc = FALSE;
                  $this->error = "Insert items failed";
                  break;
                }
                else
                {
                  $docTotal += $amountAfDisc;
                  $vatSum += $vat_amount;
                  $discSum += $sumBillDiscAmount;

                  $arr = array(
                    'linked' => 'Y',
                    'ref_code' => $order->code
                  );

                  if( ! $this->sales_order_model->update_detail($rs->baseLine, $arr))
                  {
                    $sc = FALSE;
                    $this->error = "Failed to link Sales order item";
                    break;
                  }
                }
              } //--- end foreach
            }

            if($sc === TRUE)
            {
              if($order->role == 'S')
              {
                $arr = array(
                  'bill_code' => $order->code
                );
              }
              else
              {
                $arr = array(
                  'ref_code' => $order->code
                );
              }

              if( ! $this->sales_order_model->update($so->code, $arr))
              {
                $sc = FALSE;
                $this->error = "Failed to update Sales order ref_code";
              }
              else
              {
                if($order->role == 'T' OR $order->role == 'Q')
                {
                  $this->load->model('inventory/transform_model');

                  if( ! $this->transform_model->update_so_code($order->code, $so->code))
                  {
                    $sc = FALSE;
                    $this->error = "Failed to link transform with so code";
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

            if($sc === TRUE)
            {
              $amountAfDisc = $so->vat_type == 'E' ? $docTotal : $docTotal - $vatSum;
              $WhtAmount = $so->WhtPrcnt > 0 ? ($amountAfDisc > 0 ? $amountAfDisc * ($so->WhtPrcnt * 0.01) : 0.00) : 0.00;
              $DocTotal = $so->vat_type == 'E' ? $docTotal + $vatSum : $docTotal;

              $arr = array(
                'doc_total' => $DocTotal,
                'TotalBalance' => $DocTotal,
                'VatSum' => $vatSum,
                'bDiscAmount' => $discSum,
                'WhtAmount' => $WhtAmount
              );

              $this->orders_model->update($code, $arr);
            }
          } //---- ! empty $details
          else
          {
            $sc = FALSE;
            $this->error = "No items in Sales order";
          }
        }
        else
        {
          $sc = FALSE;
          $this->error = "Invalid Sales order number or invalid Sales order status";
        }
      }
      else
      {
        $sc = FALSE;
        $this->error = "Invalid document number or invalid document state";
      }
    }
    else
    {
      $sc = FALSE;
      set_error('required');
    }

    echo $sc === TRUE ? 'success' : $this->error;
  }


  public function clear_so()
  {
    $sc = TRUE;
    $this->load->model('orders/sales_order_model');
    $this->load->model('inventory/transform_model');
    $this->load->model('inventory/buffer_model');
    $this->load->model('inventory/qc_model');

    $code = $this->input->post('code');

    $so_code = $this->input->post('so_code');

    if( ! empty($code) && ! empty($so_code))
    {
      $order = $this->orders_model->get($code);

      if( ! empty($order) && $order->state == 1)
      {

        $so = $this->sales_order_model->get($so_code);

        if( ! empty($so))
        {
          $this->db->trans_begin();

          $so_rows = $this->orders_model->get_so_rows($code, $so_code);

          if( ! empty($so_rows))
          {
            //--- remove transform link
            foreach($so_rows as $rs)
            {
              if($order->role == 'T' OR $order->role == 'Q')
              {
                if( ! $this->transform_model->remove_transform_detail($rs->id))
                {
                  $sc = FALSE;
                  $this->error = "Failed to remove product link";
                  break;
                }
              }

              if( ! $this->buffer_model->delete_by_order_detail_id($rs->id))
              {
                $sc = FALSE;
                $this->error = "Failed to remove buffer";
              }

              if( ! $this->qc_model->delete_by_order_detail_id($rs->id))
              {
                $sc = FALSE;
                $this->error = "Failed to remove qc data";
              }

              if($sc === TRUE && ! empty($rs->line_id))
              {
                $arr = array(
                  'linked' => $this->sales_order_model->has_linked($rs->id) ? 'Y' : 'N',
                  'ref_code' => NULL
                );

                $this->sales_order_model->update_detail($rs->line_id, $arr);
              }
            }
          }

          if($sc === TRUE)
          {
            if( ! $this->orders_model->remove_so_details($code, $so_code))
            {
              $sc = FALSE;
              $this->error = "Failed to remove item rows";
            }
          }

          if($sc === TRUE)
          {
            //---- remove ref_code
            if($order->role == 'T' OR $order->role == 'Q')
            {
              if( ! $this->sales_order_model->update_details($so_code, array('ref_code' => NULL)))
              {
                $sc = FALSE;
                $this->error = "Failed to remove ref_code in Sales order rows";
              }

              if($sc === TRUE)
              {
                if( ! $this->sales_order_model->update($so_code, array('ref_code' => NULL)))
                {
                  $sc = FALSE;
                  $this->error = "Failed to remove ref_code in Sales order";
                }
              }
            }

            if($order->role == 'S')
            {
              if($sc === TRUE)
              {
                if( ! $this->sales_order_model->update($so_code, array('bill_code' => NULL, 'status' => 'O')))
                {
                  $sc = FALSE;
                  $this->error = "Failed to remove bill_code in Sales order";
                }
              }
            }


            if($sc === TRUE)
            {
              $arr = array(
                'so_code' => NULL,
                'reference' => NULL,
                'bDiscText' => NULL,
                'bDiscAmount' => 0,
                'doc_total' => 0,
                'paidAmount' => 0,
                'DepAmount' => 0,
                'TotalBalance' => 0
              );

              if( ! $this->orders_model->update($code, $arr))
              {
                $sc = FALSE;
                $this->error = "Failed to remove so_code in Order";
              }
              else
              {
                if( ! $this->transform_model->update_so_code($code, NULL))
                {
                  $sc = FALSE;
                  $this->error = "Failed to remove link on tranform code";
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
          $this->error = "Invalid Sales order number";
        }
      }
      else
      {
        $sc = FALSE;
        $this->error = "Invalid document number or invalid document state";
      }
    }
    else
    {
      $sc = FALSE;
      set_error('required');
    }

    echo $sc === TRUE ? 'success' : $this->error;
  }


  public function get_so_and_job_title()
  {
    $ds = array();
    $req = trim($_REQUEST['term']);

    $this->db->select('code, job_title')->where('status', 'O');

    if($req != '*')
    {
      $this->db
      ->group_start()
      ->like('code', $this->db->escape_str($req))
      ->or_like('job_title', $this->db->escape_str($req))
      ->group_end();
    }

    $rs = $this->db->order_by('code', 'ASC')->limit(50)->get('sale_order');

    if($rs->num_rows() > 0)
    {
      foreach($rs->result() as $rd)
      {
        $ds[] = $rd->code.' | '.$rd->job_title;
      }
    }
    else
    {
      $ds[] = "Not found";
    }

    echo json_encode($ds);
  }


  public function update_line_text()
  {
    $sc = TRUE;
    $data = json_decode($this->input->post('data'));

    if( ! empty($data))
    {
      $arr = array(
        'line_text' => $data->line_text
      );

      if( ! $this->orders_model->update_detail($data->id, $arr))
      {
        $sc = FALSE;
        $this->error = "Failed to update Line Text";
      }
    }
    else
    {
      $sc = FALSE;
      $this->error = "Updte failed : Missing required parameter";
    }

    $this->_response($sc);
  }


  public function get_customer_bill_to_address()
  {
    $sc = TRUE;
    $code = $this->input->get('code');
    $this->load->model('address/customer_address_model');

    if( ! empty($code))
    {
      $customer = $this->customers_model->get($code);

      if( ! empty($customer))
      {
        $addr = $this->customer_address_model->get_bill_to_address($customer->code);

        if( ! empty($addr))
        {
          $no = 1;

          foreach($addr as $adr)
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
      'order_code',
			'so_code',
      'order_customer',
      'order_user',
      'order_sale_code',
      'order_reference',
      'order_shipCode',
      'order_channels',
      'order_payment',
      'order_fromDate',
      'order_toDate',
      'order_warehouse',
      'notSave',
      'onlyMe',
      'isExpire',
			'is_term',
			'invoice_code',
      'state_1',
      'state_2',
      'state_3',
      'state_4',
      'state_5',
      'state_6',
      'state_7',
      'state_8',
      'state_9'
    );

    clear_filter($filter);
  }


  public function export_ship_to_address($id)
  {
    $this->load->model('address/customer_address_model');
    $rs = $this->customer_address_model->get_customer_ship_to_address($id);
    if(!empty($rs))
    {
      $ex = $this->customer_address_model->is_sap_address_exists($rs->code, $rs->address_code, 'S');
      if(! $ex)
      {
        $ds = array(
          'Address' => $rs->address_code,
          'CardCode' => $rs->customer_code,
          'Street' => $rs->address,
          'Block' => $rs->sub_district,
          'ZipCode' => $rs->postcode,
          'City' => $rs->province,
          'County' => $rs->district,
          'LineNum' => ($this->customer_address_model->get_max_line_num($rs->code, 'S') + 1),
          'AdresType' => 'S',
          'Address2' => '0000',
          'Address3' => 'สำนักงานใหญ่',
          'F_E_Commerce' => $ex ? 'U' : 'A',
          'F_E_CommerceDate' => sap_date(now(), TRUE)
        );

        $this->customer_address_model->add_sap_ship_to($ds);
      }
      else
      {
        $ds = array(
          'Address' => $rs->address_code,
          'CardCode' => $rs->customer_code,
          'Street' => $rs->address,
          'Block' => $rs->sub_district,
          'ZipCode' => $rs->postcode,
          'City' => $rs->province,
          'County' => $rs->district,
          'AdresType' => 'S',
          'Address2' => '0000',
          'Address3' => 'สำนักงานใหญ่',
          'F_E_Commerce' => $ex ? 'U' : 'A',
          'F_E_CommerceDate' => sap_date(now(), TRUE)
        );

        $this->customer_address_model->update_sap_ship_to($rs->code, $rs->address_code, $ds);
      }
    }
  }


  public function get_template_file()
  {
    $path = $this->config->item('upload_path').'orders/';
    $file_name = $path."import_order_template.xlsx";

    if(file_exists($file_name))
    {
      header('Content-Description: File Transfer');
      header('Content-Type:Application/octet-stream');
      header('Cache-Control: no-cache, must-revalidate');
      header('Expires: 0');
      header('Content-Disposition: attachment; filename="'.basename($file_name).'"');
      header('Content-Length: '.filesize($file_name));
      header('Pragma: public');

      flush();
      readfile($file_name);
      die();
    }
    else
    {
      echo "File Not Found";
    }
  }

}
?>
