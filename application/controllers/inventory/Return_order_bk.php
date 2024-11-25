<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Return_order extends PS_Controller
{
  public $menu_code = 'ICRTOR';
	public $menu_group_code = 'IC';
  public $menu_sub_group_code = 'RETURN';
	public $title = 'คืนสินค้า(ลดหนี้ขาย)';
  public $filter;
  public $error;
	public $wms;
	public $isAPI;
  public $segment = 4;
  public $required_remark = 1;
  public $allow_no_inv = FALSE;

  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'inventory/return_order';
    $this->load->model('inventory/return_order_model');
    $this->load->model('masters/warehouse_model');
    $this->load->model('masters/zone_model');
    $this->load->model('masters/customers_model');
    $this->load->model('masters/products_model');
		$this->load->helper('warehouse');
    $this->allow_no_inv = getConfig('ALLOW_RETURN_NO_INV') == 1 ? TRUE : FALSE;
  }


  public function index()
  {
		$this->load->helper('warehouse');

    $filter = array(
      'code'    => get_filter('code', 'sm_code', ''),
      'invoice' => get_filter('invoice', 'sm_invoice', ''),
      'customer_code' => get_filter('customer_code', 'sm_customer_code', ''),
      'from_date' => get_filter('from_date', 'sm_from_date', ''),
      'to_date' => get_filter('to_date', 'sm_to_date', ''),
      'status' => get_filter('status', 'sm_status', 'all'),
      'approve' => get_filter('approve', 'sm_approve', 'all'),
			'zone' => get_filter('zone', 'sm_zone', ''),
			'is_pos' => get_filter('is_pos', 'sm_is_pos', 'all'),
      'must_accept' => get_filter('must_accept', 'sm_must_accept', 'all'),
      'sap' => get_filter('sap', 'sm_sap', 'all')
    );

		//--- แสดงผลกี่รายการต่อหน้า
		$perpage = get_rows();

		$rows = $this->return_order_model->count_rows($filter);
		$init	= pagination_config($this->home.'/index/', $rows, $perpage, $this->segment);
		$document = $this->return_order_model->get_list($filter, $perpage, $this->uri->segment($this->segment));

    if(!empty($document))
    {
      foreach($document as $rs)
      {
        $rs->qty = $this->return_order_model->get_sum_qty($rs->code);
        $rs->amount = $this->return_order_model->get_sum_amount($rs->code);
      }
    }

    $filter['docs'] = $document;
		$this->pagination->initialize($init);
    $this->load->view('inventory/return_order/return_order_list', $filter);
  }




  public function add_details($code)
  {
    $sc = TRUE;

    if($this->input->post())
    {
      //--- start transection
      $this->db->trans_begin();

      $doc = $this->return_order_model->get($code);

      if( ! empty($doc))
      {
        if($doc->ref_type != 4)
        {
          $qtys = $this->input->post('qty');
          $item = $this->input->post('item');
          $sold_qtys = $this->input->post('sold_qty');
          $prices = $this->input->post('price');
          $discounts = $this->input->post('discount');
          $orders = $this->input->post('order');

          $vat = getConfig('SALE_VAT_RATE'); //--- 0.07

          //--- drop old detail
          $this->return_order_model->drop_details($code);

          if(count($qtys) > 0)
          {
            foreach($qtys as $row => $qty)
            {
              if($qty > 0)
              {
                $price = round($prices[$row], 2);
                $discount = $discounts[$row];
                $disc_amount = $discount == 0 ? 0 : $qty * ($price * ($discount * 0.01));
                $amount = ($qty * $price) - $disc_amount;
                $arr = array(
                  'return_code' => $code,
                  'invoice_code' => $doc->invoice,
                  'order_code' => get_null($orders[$row]),
                  'product_code' => $item[$row],
                  'product_name' => $this->products_model->get_name($item[$row]),
                  'sold_qty' => $sold_qtys[$row],
                  'qty' => $qty,
                  'receive_qty' => ($doc->is_wms == 1 ? ($doc->api == 1? 0 : $qty) : $qty),
                  'price' => $price,
                  'discount_percent' => $discount,
                  'amount' => $amount,
                  'vat_amount' => get_vat_amount($amount)
                );

                if($this->return_order_model->add_detail($arr) === FALSE)
                {
                  $sc = FALSE;
                  $this->error = 'บันทึกรายการไม่สำเร็จ';
                  break;
                }
              } //--- end if qty > 0
            } //--- end foreach
          } //-- end if count($qtys)
        }

        if($sc === TRUE)
        {
          if( ! $this->return_order_model->set_status($code, 1))
          {
            $sc = FALSE;
            $this->error = "เปลี่ยนสถานะเอกสารไม่สำเร็จ";
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
        //--- empty document
        $sc = FALSE;
        set_error('ไม่พบเลขที่เอกสาร');
      }
    }
    else
    {
      $sc = FALSE;
      set_error('ไม่พบจำนวนในการรับคืน');
    }

    if($sc === TRUE)
    {
      set_message('Success');
      redirect($this->home.'/view_detail/'.$code);
    }
    else
    {
      redirect($this->home.'/edit/'.$code);
    }
  }


  public function delete_detail($id)
  {
    $rs = $this->return_order_model->delete_detail($id);
    echo $rs === TRUE ? 'success' : 'ลบรายการไม่สำเร็จ';
  }


  public function unsave($code)
  {
    $sc = TRUE;

    if($this->pm->can_edit)
    {
      $docNum = $this->return_order_model->get_sap_doc_num($code);
      if(empty($docNum))
      {
        $arr = array(
          'status' => 0,
          'is_approve' => 0,
          'approver' => NULL,
          'inv_code' => NULL
        );

        if( ! $this->return_order_model->update($code, $arr))
        {
          $sc = FALSE;
          $this->error = 'ยกเลิกการบันทึกไม่สำเร็จ';
        }
      }
      else
      {
        $sc = FALSE;
        $this->error = "กรุณายกเลิกเอกสาร ลดหนี้เลขที่ {$docNum} ใน SAP ก่อนยกเลิกการบันทึก";
      }

    }
    else
    {
      $sc = FALSE;
      $this->error = 'คุณไม่มีสิทธิ์ในการยกเลิกการบันทึก';
    }

    echo $sc === TRUE ? 'success' : $this->error;
  }



  public function approve($code)
  {
    $this->load->model('inventory/movement_model');

		$sc = TRUE;

    if($this->pm->can_approve)
    {
      $this->load->model('approve_logs_model');
			$doc = $this->return_order_model->get($code);

			if(!empty($doc))
			{
				if($doc->status == 1 ) //--- status บันทึกแล้วเท่านั้น
				{
          $this->db->trans_begin();

          if( ! $this->return_order_model->approve($code))
          {
            $sc = FALSE;
            $this->error = "Approve Faiiled";
          }

					if($sc === TRUE)
					{
            $this->approve_logs_model->add($code, 1, $this->_user->uname);

            $arr = array('shipped_date' => now());

            $this->return_order_model->update($code, $arr);

            $details = $this->return_order_model->get_details($doc->code);

            if(!empty($details))
            {
              //---- add movement
              foreach($details as $rs)
              {
                if($sc === FALSE) { break; }

                $arr = array(
                  'reference' => $doc->code,
                  'warehouse_code' => $doc->warehouse_code,
                  'zone_code' => $doc->zone_code,
                  'product_code' => $rs->product_code,
                  'move_in' => $rs->receive_qty,
                  'date_add' => db_date($doc->date_add, TRUE)
                );

                if($this->movement_model->add($arr) === FALSE)
                {
                  $sc = FALSE;
                  $this->error = 'บันทึก movement ไม่สำเร็จ';
                }
              }

              if($sc === TRUE)
              {
                $this->return_order_model->update($code, array('is_complete' => 1));
              }
            }
            else
            {
              $sc = FALSE;
              $this->error = "ไม่พบรายการรับคืน";
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
            $export = $this->do_export($code);

            if(! $export)
            {
              $sc = FALSE;
              $this->error = "อนุมัติสำเร็จ แต่ส่งข้อมูลไป SAP ไม่สำเร็จ กรุณา refresh หน้าจอแล้วกดส่งข้อมูลอีกครั้ง";
            }
          }
				}
				else
				{
					$sc = FALSE;
					$this->error = "Invalid status";
				}
			}
			else
			{
				$sc = FALSE;
				$this->error = 'เลขที่เอกสารไม่ถูกต้อง';
			}
    }
    else
    {
			$sc = FALSE;
			$this->error = 'คุณไม่มีสิทธิ์อนุมัติ';
    }

		echo $sc === TRUE ? 'success' : $this->error;
  }


  public function unapprove($code)
  {
		$sc = TRUE;

    if($this->pm->can_approve)
    {
      //--- check document in SAP
      $sap = $this->return_order_model->get_sap_return_order($code);

      if(empty($sap))
      {
        //-- delete temp data
        $temp = $this->return_order_model->get_middle_return_doc($code);

        if( ! empty($temp))
        {
          foreach($temp as $tmp)
          {
            $this->return_order_model->drop_middle_exits_data($tmp->DocEntry);
          }
        }

        $this->load->model('inventory/movement_model');
        $this->load->model('approve_logs_model');

        $arr = array(
          'status' => 1,
          'is_approve' => 0,
          'is_accept' => NULL,
          'accept_on' => NULL,
          'accept_by' => NULL,
          'accept_remark' => NULL
        );


        if($this->return_order_model->update($code, $arr))
        {
          $this->approve_logs_model->add($code, 0, $this->_user->uname);

          $this->movement_model->drop_movement($code);
        }
        else
        {
					$sc = FALSE;
          $this->error = 'ยกเลิกอนุมัติเอกสารไม่สำเร็จ';
        }
      }
			else
			{
				$sc = FALSE;
				$this->error = "เอกสารเข้า SAP แล้ว กรุณายกเลิกเอกสารใน SAP ก่อน";
			}
    }
    else
    {
			$sc = FALSE;
      $this->error = 'คุณไม่มีสิทธิ์อนุมัติ';
    }

		echo $sc === TRUE ? 'success' : $this->error;
  }


  public function add_new()
  {
    $this->load->view('inventory/return_order/return_order_add');
  }


  public function add()
  {
    $sc = TRUE;

    $data = json_decode($this->input->post('data'));

    if( ! empty($data))
    {
      $date_add = db_date($data->date_add, TRUE);
      $invoice = trim($data->invoice);
      $customer_code = trim($data->customer_code);
			$zone_code = trim($data->zone_code);
      $remark = trim($data->remark);
			$zone = $this->zone_model->get($zone_code);

      $code = $this->get_new_code($date_add);

      $arr = array(
        'code' => $code,
        'bookcode' => getConfig('BOOK_CODE_RETURN_ORDER'),
        'invoice' => $invoice,
        'customer_code' => $customer_code,
        'warehouse_code' => $zone->warehouse_code,
        'zone_code' => $zone->code,
        'user' => $this->_user->uname,
        'date_add' => $date_add,
        'remark' => $remark
      );

      if( ! $this->return_order_model->add($arr))
      {
        $sc = FALSE;
        $this->error = "เพิ่มเอกสารไม่สำเร็จ กรุณาลองใหม่อีกครั้ง";
      }      
    }
    else
    {
      $sc = FALSE;
      $this->error = "ไม่พบข้อมูลเอกสารหรือฟอร์มว่างเปล่า กรุณาตรวจสอบ";      
    }

    $arr = array(
      'status' => $sc === TRUE ? 'success' : 'failed',
      'message' => $sc === TRUE ? 'success' : $this->error,
      'code' => $sc === TRUE ? $code : NULL
    );

    echo json_encode($arr);
  }


  public function edit($code)
  {
    $doc = $this->return_order_model->get($code);
    $doc->customer_name = $this->customers_model->get_name($doc->customer_code);
    $doc->zone_name = $this->zone_model->get_name($doc->zone_code);
    $doc->warehouse_name = $this->warehouse_model->get_name($doc->warehouse_code);
    $details = $this->return_order_model->get_details($code);

    $detail = array();
      //--- ถ้าไม่มีรายละเอียดให้ไปดึงจากใบกำกับมา
    if(empty($details))
    {
      $details = $this->return_order_model->get_invoice_details($doc->invoice);

      if(! empty($details))
      {
        //--- ถ้าได้รายการ ให้ทำการเปลี่ยนรหัสลูกค้าให้ตรงกับเอกสาร
        $cust = $this->return_order_model->get_customer_invoice($doc->invoice);

        if(!empty($cust))
        {
          $this->return_order_model->update($doc->code, array('customer_code' => $cust->customer_code));
        }
        //--- เปลี่ยนข้อมูลที่จะแสดงให้ตรงกันด้วย
        $doc->customer_code = $cust->customer_code;
        $doc->customer_name = $cust->customer_name;

        foreach($details as $rs)
        {
          $rs->id = 0;
          $rs->invoice_code = $doc->invoice;
          $rs->barcode = $this->products_model->get_barcode($rs->product_code);
          $rs->sold_qty = $rs->qty > 0 ? round($rs->qty, 2) : 0;
          $rs->discount_percent = round($rs->discount, 2);
          $rs->qty = round($rs->qty, 2);
          $rs->price = round(add_vat($rs->price), 2);
          $rs->amount = $rs->qty > 0 ? round((get_price_after_discount($rs->price, $rs->discount_percent) * $rs->qty), 2) : 0;          
        }
      }
    }
    else
    {
      foreach($details as $rs)
      {
        $returned_qty = $rs->input_type == 4 ? 0 : $this->return_order_model->get_returned_qty($doc->invoice, $rs->product_code);
        $qty = $rs->input_type == 4 ? $rs->sold_qty : $rs->sold_qty - ($returned_qty - $rs->qty);

				$rs->invoice_code = $doc->invoice;
				$rs->barcode = $this->products_model->get_barcode($rs->product_code);
				$rs->sold_qty = $qty;
				$rs->price = round($rs->price,2);
				$rs->amount = round($rs->amount,2);
      }
    }


    $ds = array(
      'doc' => $doc,
      'details' => $details
    );

    if($doc->status == 0)
    {
      $this->load->view('inventory/return_order/return_order_edit', $ds);
    }
    else
    {
      $this->load->view('inventory/return_order/return_order_view_detail', $ds);
    }

  }



  public function update()
  {
    $sc = TRUE;
    if($this->input->post('return_code'))
    {
      $code = $this->input->post('return_code');
      $date_add = db_date($this->input->post('date_add'), TRUE);
      $invoice = trim($this->input->post('invoice'));
      $customer_code = $this->input->post('customer_code');
			$zone_code = $this->input->post('zone_code');
      $zone = $this->zone_model->get($zone_code);
      $remark = $this->input->post('remark');

      $arr = array(
        'date_add' => $date_add,
        'invoice' => $invoice,
        'customer_code' => $customer_code,
        'warehouse_code' => $zone->warehouse_code,
        'zone_code' => $zone->code,
        'remark' => $remark,
        'update_user' => $this->_user->uname
      );

      if($this->return_order_model->update($code, $arr) === FALSE)
      {
        $sc = FALSE;
        $message = 'ปรับปรุงข้อมูลไม่สำเร็จ';
      }
    }
    else
    {
      $sc = FALSE;
      $message = 'ไม่พบเลขที่เอกสาร';
    }

    echo $sc === TRUE ? 'success' : $message;
  }



  public function view_detail($code)
  {
    $this->load->model('approve_logs_model');
    $doc = $this->return_order_model->get($code);
    $details = $this->return_order_model->get_details($code);
    $ds = array(
      'doc' => $doc,
      'details' => $details,
      'approve_list' => $this->approve_logs_model->get($code)
    );

    $this->load->view('inventory/return_order/return_order_view_detail', $ds);
  }


  public function get_invoice($invoice)
  {
    $sc = TRUE;
    $details = $this->return_order_model->get_invoice_details($invoice);
    $ds = array();
    if(empty($details))
    {
      $sc = FALSE;
      $message = 'ไม่พบข้อมูล';
    }

    if(!empty($details))
    {
      foreach($details as $rs)
      {
        $returned_qty = $this->return_order_model->get_returned_qty($invoice, $rs->product_code);
        $qty = $rs->qty - $returned_qty;
        $row = new stdClass();
        if($qty > 0)
        {
          $row->barcode = $this->products_model->get_barcode($rs->product_code);
          $row->invoice = $invoice;
					$row->order_code = $rs->order_code;
          $row->code = $rs->product_code;
          $row->name = $rs->product_name;
          $row->price = round($rs->price, 2);
          $row->discount = round($rs->discount, 2);
          $row->qty = round($qty, 2);
          $row->amount = 0;
          $ds[] = $row;
        }
      }
    }

    echo $sc === TRUE ? json_encode($ds) : $message;
  }


	//--- print received
  public function print_detail($code)
  {
    $this->load->library('printer');
    $doc = $this->return_order_model->get($code);
    $doc->customer_name = $this->customers_model->get_name($doc->customer_code);
    $doc->warehouse_name = $this->warehouse_model->get_name($doc->warehouse_code);
    $doc->zone_name = $this->zone_model->get_name($doc->zone_code);
    $details = $this->return_order_model->get_details($code);

    if(!empty($details))
    {
      foreach($details as $rs)
      {
        $rs->barcode = $this->products_model->get_barcode($rs->product_code);
      }
    }
    $ds = array(
      'doc' => $doc,
      'details' => $details
    );

    $this->load->view('print/print_return', $ds);
  }



  public function cancle_return($code)
  {
    $sc = TRUE;

    if($this->pm->can_delete)
    {
			$doc = $this->return_order_model->get($code);

			if(!empty($doc))
			{
				if($doc->status == 1 OR $doc->status == 0 OR $this->_SuperAdmin)
				{
					//--- check sap
					$sap = $this->return_order_model->get_sap_doc_num($code);

					if(empty($sap))
					{
						//--- cancle middle
						if($sc === TRUE)
						{
							if($this->drop_middle_exits_data($code))
							{

								$this->db->trans_begin();

                if( ! $this->return_order_model->update_details($code, array('is_cancle' => 1)))
                {
                  $sc = FALSE;
                  $this->error = "เปลี่ยนสถานะรายการไม่สำเร็จ";
                }

                if($sc === TRUE)
                {
                  $arr = array(
                    'inv_code' => NULL,
                    'status' => 2,
                    'cancle_reason' => trim($this->input->post('reason')),
                    'cancle_user' => $this->_user->uname
                  );

                  if( ! $this->return_order_model->update($code, $arr))
                  {
                    $sc = FALSE;
                    $this->error = "เปลียนสถานะเอกสารไม่สำเร็จ";
                  }

                  if($sc === TRUE && $doc->ref_type == 4)
                  {
                    $this->load->model('orders/order_pos_return_model');

                    //--- roll back status pos return to O again and remove ref_code
                    $bills = $this->order_pos_return_model->get_bills_by_ref_code($code);

                    if( ! empty($bills))
                    {
                      $arr = array(
                        'ref_code' => NULL,
                        'status' => 'O'
                      );

                      foreach($bills as $bill)
                      {
                        if( ! $this->order_pos_return_model->update_details($bill->id, array('status' => 'O')))
                        {
                          $sc = FALSE;
                          $this->error = "Failed to update Pos return bill item status.";
                          break;
                        }

                        if( ! $this->order_pos_return_model->update($bill->id, $arr))
                        {
                          $sc = FALSE;
                          $this->error = "Failed to update POS return bill status";
                          break;
                        }
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
								$this->error = "Cannot Delete Middle Temp data";
							}
						}
					}
					else
					{
						$sc = FALSE;
						$this->error = "กรุณายกเลิกเอกสารใน SAP ก่อนดำเนินการ";
					}
				}
				else
				{
					$sc = FALSE;
          $this->error = "เอกสารถูกยกเลิกไปแล้ว";
				}
			}
			else
			{
				$sc = FALSE;
				$this->error = "Invalid Document Number";
			}
    }
    else
    {
      $sc = FALSE;
      $this->error = 'คุณไม่มีสิทธิ์ในการยกเลิกเอกสาร';
    }

    echo $sc === TRUE ? 'success' : $this->error;
  }



	public function drop_middle_exits_data($code)
  {
    $sc = TRUE;
    $middle = $this->return_order_model->get_middle_return_doc($code);

    if(!empty($middle))
    {
      foreach($middle as $rs)
      {
        if( ! $this->return_order_model->drop_middle_exits_data($rs->DocEntry))
				{
					$sc = FALSE;
				}
      }
    }

    return $sc;
  }


  public function get_item()
  {
    if($this->input->post('barcode'))
    {
      $barcode = trim($this->input->post('barcode'));
      $item = $this->products_model->get_product_by_barcode($barcode);
      if(!empty($item))
      {
        echo json_encode($item);
      }
      else
      {
        echo 'not-found';
      }
    }
  }


  public function do_export($code)
  {
    $sc = TRUE;
    $this->load->library('export');
    if(! $this->export->export_return($code))
    {
      $sc = FALSE;
      $this->error = trim($this->export->error);
    }

    return $sc;
  }


  //---- เรียกใช้จากภายนอก
  public function export_return($code)
  {
    if($this->do_export($code))
    {
      echo 'success';
    }
    else
    {
      echo $this->error;
    }
  }


  public function get_new_code($date)
  {
    $date = $date == '' ? date('Y-m-d') : $date;
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
      'sm_code',
      'sm_invoice',
      'sm_customer_code',
      'sm_from_date',
      'sm_to_date',
      'sm_status',
      'sm_approve',
			'sm_warehouse',
      'sm_zone',
      'sm_must_accept',
			'sm_is_pos',
      'sm_sap'
    );
    clear_filter($filter);
  }


} //--- end class
?>
