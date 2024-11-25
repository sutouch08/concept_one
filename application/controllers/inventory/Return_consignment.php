<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Return_consignment extends PS_Controller
{
  public $menu_code = 'ICRTSM';
	public $menu_group_code = 'IC';
  public $menu_sub_group_code = 'RETURN';
	public $title = 'ลดหนี้ฝากขายเทียม';
  public $filter;
  public $error;
	public $isAPI;
	public $wms;
  public $required_remark = 1;

  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'inventory/return_consignment';
    $this->load->model('inventory/return_consignment_model');
    $this->load->model('masters/warehouse_model');
    $this->load->model('masters/zone_model');
    $this->load->model('masters/customers_model');
    $this->load->model('masters/products_model');

		$this->isAPI = is_true(getConfig('WMS_API'));
  }


  public function index()
  {
		$this->load->helper('warehouse');
		$this->load->helper('print');
    $filter = array(
      'code'    => get_filter('code', 'cn_code', ''),
      'invoice' => get_filter('invoice', 'cn_invoice', ''),
      'customer_code' => get_filter('customer_code', 'cn_customer_code', ''),
			'from_warehouse' => get_filter('from_warehouse', 'cn_from_warehouse', 'all'),
			'to_warehouse' => get_filter('to_warehouse', 'cn_to_warehouse', 'all'),
      'from_date' => get_filter('from_date', 'cn_from_date', ''),
      'to_date' => get_filter('to_date', 'cn_to_date', ''),
      'status' => get_filter('status', 'cn_status', 'all'),
      'approve' => get_filter('approve', 'cn_approve', 'all'),
			'api' => get_filter('api', 'cn_api', 'all'),
      'sap' => get_filter('sap', 'cn_sap', 'all')
    );

		//--- แสดงผลกี่รายการต่อหน้า
		$perpage = get_rows();
		//--- หาก user กำหนดการแสดงผลมามากเกินไป จำกัดไว้แค่ 300
		if($perpage > 300)
		{
			$perpage = 20;
		}

		$segment  = 4; //-- url segment
		$rows     = $this->return_consignment_model->count_rows($filter);
		//--- ส่งตัวแปรเข้าไป 4 ตัว base_url ,  total_row , perpage = 20, segment = 3
		$init	    = pagination_config($this->home.'/index/', $rows, $perpage, $segment);
		$document = $this->return_consignment_model->get_list($filter, $perpage, $this->uri->segment($segment));

    if(!empty($document))
    {
      foreach($document as $rs)
      {
        $rs->qty = $this->return_consignment_model->get_sum_qty($rs->code);
        $rs->amount = $this->return_consignment_model->get_sum_amount($rs->code);
        $rs->customer_name = $this->customers_model->get_name($rs->customer_code);
      }
    }

    $filter['docs'] = $document;
		$this->pagination->initialize($init);
    $this->load->view('inventory/return_consignment/return_consignment_list', $filter);
  }


	public function test_add_details()
	{
		$data = file_get_contents("php://input");
		$ds = json_decode($data);

		print_r($ds);
	}

  public function add_details()
  {
    $sc = TRUE;
		$data = json_decode(file_get_contents("php://input"));

    if(!empty($data))
    {
      //--- start transection
      $this->db->trans_begin();
			$code = $data->code;
			$details = $data->items;
      $doc = $this->return_consignment_model->get($code);
      if(!empty($doc))
      {
        $vat = getConfig('SALE_VAT_RATE'); //--- 0.07
				$date_add = $doc->date_add;

				if(!empty($details))
				{
					//--- drop old detail
	        $this->return_consignment_model->drop_details($code);

					foreach($details as $rs)
					{
						if($sc === FALSE)
						{
							break;
						}

						if($rs->qty > 0)
						{
							$disc_amount = $rs->qty * ($rs->price * ($rs->discount * 0.01));
							$amount = ($rs->qty * $rs->price) - $disc_amount;
							$arr = array(
								'return_code' => $code,
								'invoice_code' => $doc->invoice,
								'product_code' => $rs->item_code,
								'product_name' => $rs->item_name,
								'qty' => $rs->qty,
								'price' => $rs->price,
								'discount_percent' => $rs->discount,
								'amount' => $amount,
								'vat_amount' => get_vat_amount($amount)
							);

							if($this->isAPI === FALSE OR $doc->is_wms == 0 OR $doc->is_api == 0)
							{
								$arr['receive_qty'] = $rs->qty;
							}

							if($this->return_consignment_model->add_detail($arr) === FALSE)
							{
								$sc = FALSE;
								$this->error = "บันทึกรายการ {$rs->product_code} ไม่สำเร็จ";
							}
						}

					} //--- endforeach

					if($sc === TRUE)
					{
						$this->return_consignment_model->set_status($code, 1);
					}
				}
        else
				{
					$sc = FALSE;
					$this->error = "ไม่พบรายการรับคืน";
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
				$this->error = 'ไม่พบเลขที่เอกสาร';
      }
    }
    else
    {
      $sc = FALSE;
      $this->error = 'ไม่พบข้อมูลในฟอร์ม';
    }

		echo $sc === TRUE ? 'success' : $this->error;

  }




  public function delete_detail($id = NULL)
  {
    if($id === NULL)
    {
      echo 'success';
    }
    else
    {
      $rs = $this->return_consignment_model->delete_detail($id);
      echo $rs === TRUE ? 'success' : 'ลบรายการไม่สำเร็จ';
    }
  }


  public function unsave($code)
  {
    $sc = TRUE;
    if($this->pm->can_edit)
    {
			$doc = $this->return_consignment_model->get($code);

			if(!empty($doc))
			{
				if($doc->status != 1)
				{
					$sc = FALSE;
					$this->error = "Invalid Document Status";
				}
				else
				{
					if($doc->is_approve == 1)
					{
						$sc = FALSE;
						$this->error = "เอกสารถูกอนุมัติแล้ว ไม่สามารถย้อนการบันทึกได้";
					}
					else
					{
						if($this->return_consignment_model->set_status($code, 0) === FALSE)
			      {
			        $sc = FALSE;
			        $this->error = 'ยกเลิกการบันทึกไม่สำเร็จ';
			      }
					}
				}
			}
			else
			{
				$sc = FALSE;
				$this->error = "เลขที่เอกสารไม่ถูกต้อง";
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
		$sc = TRUE;

    if($this->pm->can_approve)
    {
			$doc = $this->return_consignment_model->get($code);

			if(!empty($doc))
			{
				if($doc->status == 1)
				{
					if($doc->is_approve == 0)
					{
						if(!$this->return_consignment_model->approve($code))
						{
							$sc = FALSE;
							$this->error = "อนุมัติเอกสารไม่สำเร็จ";
						}
						else
						{
							$this->load->model('approve_logs_model');
							$this->approve_logs_model->add($code, 1, $this->_user->uname);

							if($this->isAPI === FALSE OR $doc->is_wms == 0 OR $doc->is_api == 0)
							{
								$date_add = getConfig('ORDER_SOLD_DATE') === 'D' ? $doc->date_add : now();
								$this->return_consignment_model->update($code, array('shipped_date' => now()));
								$qr = "UPDATE return_consignment_detail SET receive_qty = qty, valid = 1 WHERE return_code = '{$code}'";
								$this->db->qurey($qr);
							}
						}
					}

					if($sc === TRUE)
					{
						$details = $this->return_consignment_model->get_details($code);

						if(!empty($details))
						{
							if($this->isAPI && $doc->is_wms == 1 && $doc->is_api == 1)
							{
								$this->wms = $this->load->database('wms', TRUE);
								$this->load->library('wms_receive_api');
								$exported = $this->wms_receive_api->export_return_consignment($doc, $details);  //--- send data to WMS ;

								if($exported)
								{
									$this->return_consignment_model->set_status($code, 3); //--- on wms process;
								}
								else
								{
									$sc = FALSE;
									$this->error = "อนุมัติสำเร็จ แต่ส่งข้อมูลเข้า WMS ไม่สำเร็จ กรุณา refresh หน้าจอแล้วกดส่งข้อมูลอีกครั้ง";
								}
							}
							else
							{
								$export = $this->do_export($code);

								if(! $export)
								{
									$sc = FALSE;
									$this->error = "อนุมัติสำเร็จ แต่ส่งข้อมูลไป SAP ไม่สำเร็จ กรุณา refresh หน้าจอแล้วกดส่งข้อมูลอีกครั้ง";
								}
							}
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
				$this->error = "ไม่พบเอกสาร";
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
    $this->load->view('inventory/return_consignment/return_consignment_add');
  }


  public function add()
  {
    $sc = TRUE;

    if($this->input->post('date_add'))
    {
      $date_add = db_date($this->input->post('date_add'), TRUE);
      $invoice = trim($this->input->post('invoice'));
			$is_wms = $this->input->post('is_wms');
			$is_api = $this->input->post('is_api');
      $customer_code = trim($this->input->post('customer_code'));
      $from_zone = $this->zone_model->get($this->input->post('from_zone'));
      $remark = trim($this->input->post('remark'));
      $gp = empty($this->input->post('gp')) ? 0 : $this->input->post('gp');

			//--- check zone
			if($is_wms == 1)
			{
				$zone_code = getConfig('WMS_ZONE');
				$warehouse_code = getConfig('WMS_WAREHOUSE');
			}
			else
			{
				  $zone = $this->zone_model->get($this->input->post('zone_code'));
					$zone_code = $zone->code;
					$warehouse_code = $zone->warehouse_code;
			}

      if($this->input->post('code'))
      {
        $code = trim($this->input->post('code'));
      }
      else
      {
        $code = $this->get_new_code($date_add);
      }

      $arr = array(
        'code' => $code,
        'bookcode' => getConfig('BOOK_CODE_RETURN_CONSIGNMENT'),
        'invoice' => $invoice,
        'customer_code' => $customer_code,
        'from_warehouse_code' => $from_zone->warehouse_code,
        'from_zone_code' => $from_zone->code,
        'warehouse_code' => $warehouse_code,
        'zone_code' => $zone_code,
        'gp' => $gp,
        'user' => $this->_user->uname,
        'date_add' => $date_add,
        'remark' => $remark,
				'is_wms' => $is_wms,
				'is_api' => $is_api
      );

      if(! $this->return_consignment_model->add($arr))
      {
        $sc = FALSE;
        $this->error = "เพิ่มเอกสารไม่สำเร็จ";
      }
      else
      {
        if(!empty($invoice))
        {
          $inv_amount = $this->return_consignment_model->get_sap_invoice_amount($invoice, $customer_code);
          if(!empty($inv_amount))
          {
            $inv_arr = array(
              'return_code' => $code,
              'invoice_code' => $invoice,
              'invoice_amount' => $inv_amount
            );

            $this->return_consignment_model->add_invoice($inv_arr);
          }

        }

      }
    }
    else
    {
      $sc = FALSE;
      $this->error = "ไม่พบข้อมูลเอกสารหรือฟอร์มว่างเปล่า กรุณาตรวจสอบ";
    }

    if($sc === TRUE)
    {
      $ds = array(
        'status' => 'success',
        'code' => $code
      );
    }
    else
    {
      $ds = array(
        'status' => 'error',
        'message' => $this->error
      );
    }

    echo json_encode($ds);

  }


  public function edit($code)
  {
    $this->load->helper('return_consignment');
    $doc = $this->return_consignment_model->get($code);
    $doc->customer_name = $this->customers_model->get_name($doc->customer_code);
    $doc->zone_name = $this->zone_model->get_name($doc->zone_code);
    $doc->from_zone_name = $this->zone_model->get_name($doc->from_zone_code);

    $invoice_list = $this->return_consignment_model->get_all_invoice($code);
    $doc->invoice_amount = round($this->return_consignment_model->get_sum_invoice_amount($code), 2);
    $doc->invoice_list = getInvoiceList($code, $invoice_list, $doc->status);

    $details = $this->return_consignment_model->get_details($code);

    $detail = array();
      //--- ถ้าไม่มีรายละเอียดให้ไปดึงจากใบกำกับมา
    if(empty($details))
    {
      $details = NULL;
    }
    else
    {
      foreach($details as $rs)
      {
        $returned_qty = $this->return_consignment_model->get_returned_qty($doc->invoice, $rs->product_code);

        if($rs->qty > 0)
        {
          $dt = new stdClass();
          $dt->id = $rs->id;
          $dt->invoice_code = $doc->invoice;
          $dt->barcode = $this->products_model->get_barcode($rs->product_code);
          $dt->product_code = $rs->product_code;
          $dt->product_name = $rs->product_name;
          $dt->discount_percent = $rs->discount_percent;
          $dt->qty = $rs->qty;
          $dt->price = round($rs->price,2);
          $dt->amount = round($rs->amount,2);
          $detail[] = $dt;
        }
      }
    }


    $ds = array(
      'doc' => $doc,
      'details' => $detail
    );

    if($doc->status == 0)
    {
      $this->load->view('inventory/return_consignment/return_consignment_edit', $ds);
    }
    else
    {
      $this->load->view('inventory/return_consignment/return_consignment_view_detail', $ds);
    }

  }


  public function get_invoice_list($code)
  {
    $arr = array(
      'invoice_list' => '',
      'amount' => 0
    );

    $invoice = $this->return_consignment_model->get_all_invoice($code);
    if(!empty($invoice))
    {
      $list = "";
      $amount = 0;
      $i = 1;
      foreach($invoice as $rs)
      {
        $list .= $i === 1 ? $rs->invoice_code : ", {$rs->invoice_code}";
        $amount += $rs->invoice_amount;
        $i++;
      }

      $arr['invoice_list'] = $list;
      $arr['amount'] = $amount;
    }

    return $arr;
  }



  public function update()
  {
    $sc = TRUE;
    if($this->input->post('return_code'))
    {
      $code = $this->input->post('return_code');
      $date_add = db_date($this->input->post('date_add'), TRUE);
      $invoice = trim($this->input->post('invoice'));

			$is_wms = $this->input->post('is_wms');
			$is_api = $is_wms == 1 ? $this->input->post('is_api') : 0;

      $customer_code = trim($this->input->post('customer_code'));
      $from_zone = $this->zone_model->get($this->input->post('from_zone'));
      $remark = trim($this->input->post('remark'));
      $gp = empty($this->input->post('gp')) ? 0 : $this->input->post('gp');

			//--- check zone
			if($is_wms == 1)
			{
				$zone_code = getConfig('WMS_ZONE');
				$warehouse_code = getConfig('WMS_WAREHOUSE');
			}
			else
			{
				  $zone = $this->zone_model->get($this->input->post('zone_code'));
					$zone_code = $zone->code;
					$warehouse_code = $zone->warehouse_code;
			}

      $arr = array(
        'date_add' => $date_add,
        'invoice' => $invoice,
        'customer_code' => $customer_code,
				'is_wms' => $is_wms,
				'is_api' => $is_api,
        'from_warehouse_code' => $from_zone->warehouse_code,
        'from_zone_code' => $from_zone->code,
        'warehouse_code' => $warehouse_code,
        'zone_code' => $zone_code,
        'gp' => $gp,
        'remark' => $remark,
        'update_user' => get_cookie('uname')
      );

      if($this->return_consignment_model->update($code, $arr) === FALSE)
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
		$this->load->helper('print');
		$this->load->helper('return_consignment');
    $doc = $this->return_consignment_model->get($code);
    $doc->customer_name = $this->customers_model->get_name($doc->customer_code);
    $doc->zone_name = $this->zone_model->get_name($doc->zone_code);
    $doc->from_zone_name = $this->zone_model->get_name($doc->from_zone_code);

    $invoice_list = $this->return_consignment_model->get_all_invoice($code);
    $doc->invoice_amount = round($this->return_consignment_model->get_sum_invoice_amount($code), 2);
    $doc->invoice_list = getInvoiceList($code, $invoice_list, $doc->status);

    $details = $this->return_consignment_model->get_details($code);

    $ds = array(
      'doc' => $doc,
      'details' => $details
    );

    $this->load->view('inventory/return_consignment/return_consignment_view_detail', $ds);
  }


	public function load_stock_in_zone()
	{
		$sc = TRUE;
		$code = trim($this->input->post('code'));

		if(!empty($code))
		{
			$doc = $this->return_consignment_model->get($code);
			if(!empty($doc))
			{
				//--- check zone customer
				if($this->zone_model->is_exists_customer($doc->from_zone_code, $doc->customer_code))
				{
					//-- check warehouse is consignment ?
					if($this->warehouse_model->is_consignment($doc->from_warehouse_code))
					{
						//--- load stock
						$this->load->model('stock/stock_model');

						$details = $this->stock_model->get_all_stock_consignment_zone($doc->from_zone_code);

						if(!empty($details))
						{
							$this->db->trans_begin();
							if($this->return_consignment_model->drop_details($code))
							{
								foreach($details as $rs)
								{
									if($sc === FALSE)
									{
										break;
									}

									$item = $this->products_model->get($rs->product_code);
									if(!empty($item))
									{
										$discount = $item->price * ($doc->gp * 0.01);
										$amount = $rs->qty * ($item->price - $discount);
										$vat_amount = get_vat_amount($amount, NULL); //-- tool_helper
										$arr = array(
											'return_code' => $doc->code,
											'invoice_code' => $doc->invoice,
											'product_code' => $item->code,
											'product_name' => $item->name,
											'qty' => $rs->qty,
											'price' => $item->price,
											'discount_percent' => $doc->gp,
											'amount' => $amount,
											'vat_amount' => $vat_amount
										);

										if(!$this->return_consignment_model->add_detail($arr))
										{
											$sc = FALSE;
											$this->error = "เพิ่มรายการไม่สำเร็จ : {$item->code} => {$rs->qty}";
										}
									}
									else
									{
										$sc = FALSE;
										$this->error = "Invalid item code : {$rs->product_code}";
									}
								}
							}
							else
							{
								$sc = FALSE;
								$this->error = "ลบรายการปัจจุบันไม่สำเร็จ";
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
							$this->error = "ไม่พบสต็อกคงเหลือในโซน {$doc->from_zone_code}";
						}
					}
					else
					{
						$sc = FALSE;
						$this->error = "รหัสคลังไม่ใช้คลังฝากขายเทียม";
					}
				}
				else
				{
					$sc = FALSE;
					$this->error = "รหัสโซนกับรหัสลูกค้าไม่ตรงกัน";
				}
			}
			else
			{
				$sc = FALSE;
				$this->error = "เลขที่เอกสารไม่ถูกต้อง";
			}
		}
		else
		{
			$sc = FALSE;
			$this->error = "ไม่พบเลขที่เอกสาร";
		}

		echo $sc === TRUE ? 'success' : $this->error;
	}


  public function add_invoice()
  {
    $sc = TRUE;
    $this->load->helper('return_consignment');
    $invoice = $this->input->post('invoice');
    $customer_code = $this->input->post('customer_code');
    $code = $this->input->post('return_code');
    $doc = $this->return_consignment_model->get($code);

    if(!empty($doc))
    {
      //--- check invoice with customer
      $amount = $this->return_consignment_model->get_sap_invoice_amount($invoice, $customer_code);
      if(!empty($amount))
      {
        //--- check invoice in table
        $isExists = $this->return_consignment_model->is_exists_invoice($invoice, $code);
        if($isExists === FALSE)
        {
          //-- เตรียมข้อมูลเพิ่มเข้าตาราง
          $arr = array(
            'return_code' => $code,
            'invoice_code' => $invoice,
            'invoice_amount' => $amount
          );

          if($this->return_consignment_model->add_invoice($arr))
          {
            $invoice_list = $this->return_consignment_model->get_all_invoice($code);
            $amount = $this->return_consignment_model->get_sum_invoice_amount($code);

            $ds = array(
              'invoice' => getInvoiceList($code,$invoice_list, $doc->status),
              'amount' => $amount
            );

          }
          else
          {
            $sc = FALSE;
            $this->error = "เพิ่มบิลไม่สำเร็จ";
          }
        }
        else
        {
          $sc = FALSE;
          $this->error = "เลขที่บิลซ้ำ";
        }
      }
      else
      {
        $sc = FALSE;
        $this->error = "เลขที่บิลไม่ถูกต้อง";
      }
    }
    else
    {
      $sc = FALSE;
      $this->error = "เลขที่เอกสารไม่ถูกต้อง";
    }

    echo $sc === FALSE ? $this->error : json_encode($ds);
  }




  function remove_invoice()
  {
    $this->load->helper('return_consignment');
    $sc = TRUE;
    $code = $this->input->get('return_code');
    $invoice_code = $this->input->get('invoice_code');
    $doc = $this->return_consignment_model->get($code);

    if(!empty($doc))
    {
      if(!empty($invoice_code))
      {
        if($this->return_consignment_model->delete_invoice($code, $invoice_code))
        {
          $amount = $this->return_consignment_model->get_sum_invoice_amount($code);
          $invoice_list = $this->return_consignment_model->get_all_invoice($code);

          $arr = array(
            'invoice' => getInvoiceList($code, $invoice_list, $doc->status),
            'amount' => $amount
          );
        }
        else
        {
          $sc = FALSE;
          $this->error = "ลบ Invoice ไม่สำเร็จ";
        }
      }
      else
      {
        $sc = FALSE;
        $this->error = "ไม่พบเลขที่ invoice";
      }
    }
    else
    {
      $sc = FALSE;
      $this->error = "ไม่พบเลขที่เอกสาร";
    }


    echo $sc === TRUE ? json_encode($arr) : $this->error;
  }



  public function get_invoice()
  {
    $sc = TRUE;
    $invoice = $this->input->get('invoice');
    $customer_code = $this->input->get('customer_code');
    $no = empty($this->input->get('no')) ? 0 : $this->input->get('no');

    $details = $this->return_consignment_model->get_invoice_details($invoice, $customer_code);
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
        $returned_qty = $this->return_consignment_model->get_returned_qty($invoice, $rs->product_code);
        $qty = $rs->qty - $returned_qty;
        $row = new stdClass();
        if($qty > 0)
        {
          $no++;
          $row->barcode = $this->products_model->get_barcode($rs->product_code);
          $row->invoice = $invoice;
          $row->code = $rs->product_code;
          $row->name = $rs->product_name;
          $row->price = round(add_vat($rs->price, $rs->vat_rate), 2);
          $row->discount = round($rs->discount, 2);
          $row->qty = round($qty, 2);
          $row->amount = 0;
          $row->no = $no;
          $ds[] = $row;

        }
      }
    }

    $data = array(
      'top' => $no,
      'data' => $ds
    );

    echo $sc === TRUE ? json_encode($data) : $message;
  }




  public function search_invoice_code($customer_code = NULL)
  {
    $sc = array();

    if(!empty($customer_code))
    {
      $txt = $_REQUEST['term'];
      $result = $this->return_consignment_model->search_invoice_code($customer_code, $txt);
      if(!empty($result))
      {
        foreach($result as $rs)
        {
          $sc[] = $rs->DocNum.' | '.number($rs->DocTotal, 2);
        }
      }
      else
      {
        $sc[] = 'not found';
      }
    }
    else
    {
      $sc[] = 'กรุณาระบุลูกค้า';
    }

    echo json_encode($sc);
  }




  public function print_detail($code)
  {
    $this->load->library('printer');
    $this->load->helper('return_consignment');
    $doc = $this->return_consignment_model->get($code);
    $doc->invoice_text = getAllInvoiceText($this->return_consignment_model->get_all_invoice($code));
    $doc->customer_name = $this->customers_model->get_name($doc->customer_code);
    $doc->warehouse_name = $this->warehouse_model->get_name($doc->warehouse_code);
    $doc->zone_name = $this->zone_model->get_name($doc->zone_code);
    $details = $this->return_consignment_model->get_details($code);

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

    $this->load->view('print/print_return_consignment', $ds);
  }


	public function print_wms_return($code)
  {
    $this->load->library('xprinter');
    $doc = $this->return_consignment_model->get($code);
    $doc->customer_name = $this->customers_model->get_name($doc->customer_code);
    $doc->warehouse_name = $this->warehouse_model->get_name($doc->warehouse_code);
    $doc->zone_name = $this->zone_model->get_name($doc->zone_code);
    $details = $this->return_consignment_model->get_count_item_details($code); //--- get only count item

    $ds = array(
      'order' => $doc,
      'details' => $details
    );

    $this->load->view('print/print_wms_return_consignment', $ds);
  }



  public function cancle_return($code)
  {
    $sc = TRUE;
    if($this->pm->can_delete)
    {
			$doc = $this->return_consignment_model->get($code);
			if(!empty($doc))
			{
				if($doc->status != 2)
				{
					if($doc->status == 1)
					{
						//-- check SAP return doc ORDN
						$sap = $this->return_consignment_model->get_sap_return_consignment($code);
						//--- if document exists in sap, reject cancelation
						if(!empty($sap))
						{
							$sc = FALSE;
							$this->error = "เอกสารเข้า SAP แล้ว กรุณายกเลิกเอกสารใน SAP ก่อนดำเนินการต่อไป";
						}
					}

					if($sc === TRUE)
					{
						$this->cancle_sap_doc($code);

						$this->db->trans_begin();

            $arr = array(
              'status' => 2,
              'inv_code' => NULL,
              'cancle_reason' => trim($this->input->post('reason')),
              'cancle_user' => $this->_user->uname              
            );

			      if(! $this->return_consignment_model->update($code, $arr))
						{
							$sc = FALSE;
							$this->error = "Change document status failed";
						}


						if($sc === TRUE)
						{
							if(! $this->return_consignment_model->cancle_details($code))
							{
								$sc = FALSE;
								$this->error = "Change return items status failed";
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
				$this->error = "เลขที่เอกสารไม่ถูกต้อง";
			}
    }
    else
    {
      $sc = FALSE;
      $this->error = 'คุณไม่มีสิทธิ์ในการยกเลิกเอกสาร';
    }

    echo $sc === TRUE ? 'success' : $this->error;
  }




	public function pull_back()
	{
		$sc = TRUE;
		$code = trim($this->input->post('code'));

		if($this->_SuperAdmin)
		{
			$doc = $this->return_consignment_model->get($code);

			if(!empty($doc))
			{
				if($doc->status == 2)
				{
					$arr = array(
						'status' => 0,
						'inv_code' => NULL
					);

					$this->db->trans_begin();
					$ds = $this->db->set('is_cancle', 0)->where('return_code', $code)->update('return_consignment_detail');
					$os = $this->db->where('code', $code)->update('return_consignment', $arr);

					if($ds && $os)
					{
						$this->db->trans_commit();
					}
					else
					{
						$sc = FALSE;
						$this->error = "Update Status Failed";
						$this->db->trans_rollback();
					}
				}
				else
				{
					$sc = FALSE;
					$this->error = "Invalid Document Status";
				}
			}
			else
			{
				$sc = FALSE;
				$this->error = "Invalid Document number";
			}
		}
		else
		{
			$sc = FALSE;
			$this->error = "Permission Required";
		}

		echo $sc === TRUE ? 'success' : $this->error;
	}



	public function cancle_sap_doc($code)
  {
    $sc = TRUE;

    $middle = $this->return_consignment_model->get_middle_return_doc($code);
    if(!empty($middle))
    {
      foreach($middle as $rs)
      {
        $this->return_consignment_model->drop_middle_exits_data($rs->DocEntry);
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


	public function get_item_by_code()
  {
    if($this->input->post('item_code'))
    {
      $code = trim($this->input->post('item_code'));
      $item = $this->products_model->get($code);
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




	public function get_invoice_gp()
	{
		$invoice = trim($this->input->get('invoice'));
		if(!empty($invoice))
		{
			$qr  = "SELECT DISTINCT OINV.DocTotal, INV1.DiscPrcnt
			FROM INV1 JOIN OINV ON INV1.DocEntry = OINV.DocEntry
			WHERE OINV.DocNum = {$invoice}
			ORDER BY 1 OFFSET 0 ROWS FETCH NEXT 1 ROW ONLY";

			$rs = $this->ms->query($qr);

			if($rs->num_rows() === 1)
			{
				echo round($rs->row()->DocTotal, 2).' | '.round($rs->row()->DiscPrcnt, 2);
			}
			else
			{
				echo "not found";
			}
		}
	}





  public function do_export($code)
  {
    $sc = TRUE;
    $this->load->library('export');
    if(! $this->export->export_return_consignment($code))
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


  public function send_to_wms()
	{
		$sc = TRUE;

		if($this->input->post('code'))
		{
			$code = trim($this->input->post('code'));

			$doc = $this->return_consignment_model->get($code);

			if(!empty($doc))
			{
				if($doc->status != 2 && $doc->status != 0)
				{
					$details = $this->return_consignment_model->get_details($doc->code);

					if(!empty($details))
					{
						$this->wms = $this->load->database('wms', TRUE);
						$this->load->library('wms_receive_api');
						$rs = $this->wms_receive_api->export_return_consignment($doc, $details);

						if($rs)
						{
							$this->return_consignment_model->set_status($doc->code, 3);
						}
						else
						{
							$sc = FALSE;
							$this->error = $this->wms_receive_api->error;
						}
					}
					else
					{
						$sc = FALSE;
						$this->error = "ไม่พบรายการคืนสินค้า";
					}
				}
				else
				{
					$sc = FALSE;
					$this->error = "สถานะเอกสารไม่ถุกต้อง";
				}
			}
			else
			{
				$sc = FALSE;
				$this->error = "รหัสเอกสารไม่ถูกต้อง";
			}
		}
		else
		{
			$sc = FALSE;
			$this->error = "Missing required parameter: code";
		}

		echo $sc === TRUE ? 'success' : $this->error;
	}


  public function get_new_code($date)
  {
    $date = $date == '' ? date('Y-m-d') : $date;
    $Y = date('y', strtotime($date));
    $M = date('m', strtotime($date));
    $prefix = getConfig('PREFIX_RETURN_CONSIGNMENT');
    $run_digit = getConfig('RUN_DIGIT_RETURN_CONSIGNMENT');
    $pre = $prefix .'-'.$Y.$M;
    $code = $this->return_consignment_model->get_max_code($pre);
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
      'cn_code',
      'cn_invoice',
      'cn_customer_code',
			'cn_from_warehouse',
			'cn_to_warehouse',
			'cn_warehouse',
      'cn_from_date',
      'cn_to_date',
      'cn_status',
			'cn_api',
      'cn_approve',
      'cn_sap'
    );

    clear_filter($filter);
  }


} //--- end class
?>
