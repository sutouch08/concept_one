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
      'reference' => get_filter('reference', 'sm_reference', ''),
      'customer_code' => get_filter('customer_code', 'sm_customer_code', ''),
      'from_date' => get_filter('from_date', 'sm_from_date', ''),
      'to_date' => get_filter('to_date', 'sm_to_date', ''),
      'status' => get_filter('status', 'sm_status', 'all'),
      'approve' => get_filter('approve', 'sm_approve', 'all'),
      'warehouse' => get_filter('warehouse', 'sm_warehouse', 'all'),
			'zone' => get_filter('zone', 'sm_zone', ''),
			'is_pos' => get_filter('is_pos', 'sm_is_pos', 'all'),
      'sap' => get_filter('sap', 'sm_sap', 'all'),
      'user' => get_filter('user', 'sm_user', 'all')
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


  public function save()
  {
    $sc = TRUE;

    $data = json_decode($this->input->post('data'));

    if( ! empty($data))
    {
      $header = empty($data->header) ? NULL : $data->header;
      $items = empty($data->items) ? NULL : $data->items;

      if( ! empty($header) && ! empty($items))
      {
        $date_add = db_date($header->date_add, TRUE);

        $code = $this->get_new_code($date_add);

        $arr = array(
          'code' => $code,
          'bookcode' => getConfig('BOOK_CODE_RETURN_ORDER'),
          'invoice' => $header->invoice_code,
          'reference' => get_null(trim($header->reference)),
          'customer_code' => $header->customer_code,
          'warehouse_code' => $header->warehouse_code,
          'zone_code' => $header->zone_code,
          'user' => $this->_user->uname,
          'date_add' => $date_add,
          'status' => $header->is_draft == 1 ? 0 : 1,
          'remark' => get_null($header->remark)
        );

        $this->db->trans_begin();

        if( ! $this->return_order_model->add($arr))
        {
          $sc = FALSE;
          $this->error = "เพิ่มเอกสารไม่สำเร็จ กรุณาลองใหม่อีกครั้ง";
        }

        if($sc === TRUE)
        {
          foreach($items as $rs)
          {
            if($sc === FALSE)
            {
              break;
            }

            $arr = array(
              'return_code' => $code,
              'invoice_code' => get_null($rs->invoice_code),
              'order_code' => get_null($rs->order_code),
              'product_code' => $rs->product_code,
              'product_name' => $rs->product_name,
              'sold_qty' => $rs->qty,
              'qty' => $rs->qty,
              'receive_qty' => $rs->receive_qty,
              'price' => $rs->price,
              'discount_percent' => $rs->discount_percent,
              'amount' => $rs->amount,
              'vat_rate' => $rs->vat_rate,
              'vat_amount' => get_vat_amount($rs->amount, $rs->vat_rate)
            );

            if( ! $this->return_order_model->add_detail($arr))
            {
              $sc = FALSE;
              $this->error = "Failed to add items {$rs->product_code}";
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
      else
      {
        $sc = FALSE;
        $this->error = "Missing header or items data";
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
      'code' => $sc === TRUE ? $code : NULL
    );

    echo json_encode($arr);
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


  public function edit($code)
  {
    $doc = $this->return_order_model->get($code);
    $doc->customer_name = $this->customers_model->get_name($doc->customer_code);
    $doc->zone_name = $this->zone_model->get_name($doc->zone_code);
    $doc->warehouse_name = $this->warehouse_model->get_name($doc->warehouse_code);
    $details = $this->return_order_model->get_details($code);

    if( ! empty($details))
    {
      foreach($details as $rs)
      {
        $returned_qty = $rs->input_type == 4 ? 0 : $this->return_order_model->get_returned_qty($doc->invoice, $rs->product_code, $doc->code);
        $qty = $rs->input_type == 4 ? $rs->sold_qty : $rs->sold_qty - $returned_qty;

				$rs->invoice_code = $doc->invoice;
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

    $code = $this->input->post('code');
    $data = json_decode($this->input->post('data'));

    if( ! empty($code))
    {
      if( ! empty($data))
      {
        $header = empty($data->header) ? NULL : $data->header;
        $items = empty($data->items) ? NULL : $data->items;

        if( ! empty($header) && ! empty($items))
        {
          $arr = array(
            'invoice' => $header->invoice_code,
            'reference' => get_null(trim($header->reference)),
            'customer_code' => $header->customer_code,
            'warehouse_code' => $header->warehouse_code,
            'zone_code' => $header->zone_code,
            'update_user' => $this->_user->uname,
            'date_add' => db_date($header->date_add, TRUE),
            'status' => $header->is_draft == 1 ? 0 : 1,
            'remark' => get_null($header->remark)
          );

          $this->db->trans_begin();

          if( ! $this->return_order_model->update($code, $arr))
          {
            $sc = FALSE;
            $this->error = "Failed to update header";
          }

          if($sc === TRUE)
          {
            //--- drop current items
            if( ! $this->return_order_model->drop_details($code))
            {
              $sc = FALSE;
              $this->error = "Failed to delete previous items";
            }

            if($sc === TRUE)
            {
              foreach($items as $rs)
              {
                if($sc === FALSE)
                {
                  break;
                }

                $arr = array(
                  'return_code' => $code,
                  'invoice_code' => get_null($rs->invoice_code),
                  'order_code' => get_null($rs->order_code),
                  'product_code' => $rs->product_code,
                  'product_name' => $rs->product_name,
                  'sold_qty' => $rs->qty,
                  'qty' => $rs->qty,
                  'receive_qty' => $rs->receive_qty,
                  'price' => $rs->price,
                  'discount_percent' => $rs->discount_percent,
                  'amount' => $rs->amount,
                  'vat_rate' => $rs->vat_rate,
                  'vat_amount' => get_vat_amount($rs->amount, $rs->vat_rate)
                );

                if( ! $this->return_order_model->add_detail($arr))
                {
                  $sc = FALSE;
                  $this->error = "Failed to add items {$rs->product_code}";
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
          $this->error = "Missing header or items data";
        }
      }
      else
      {
        $sc = FALSE;
        $this->error = "Missing required parameter";
      }
    }
    else
    {
      $sc = FALSE;
      $this->error = 'ไม่พบเลขที่เอกสาร';
    }

    $arr = array(
      'status' => $sc === TRUE ? 'success' : 'failed',
      'message' => $sc === TRUE ? 'success' : $this->error,
      'code' => $sc === TRUE ? $code : NULL
    );

    echo json_encode($arr);
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


  public function get_invoice()
  {
    $sc = TRUE;
    $invoice = $this->input->post('invoice_code');
    $code = $this->input->post('code');

    $details = $this->return_order_model->get_invoice_details($invoice);

    $ds = array();

    if( ! empty($details))
    {
      foreach($details as $rs)
      {
        $returned_qty = $this->return_order_model->get_returned_qty($invoice, $rs->product_code, $code);
        $qty = $rs->qty - $returned_qty;
        $row = new stdClass();
        if($qty > 0)
        {
          $row->no = genUid();
          $row->barcode = $this->products_model->get_barcode($rs->product_code);
          $row->invoice_code = $invoice;
					$row->order_code = $rs->order_code;
          $row->product_code = $rs->product_code;
          $row->product_name = $rs->product_name;
          $row->price = round($rs->price, 2);
          $row->discount = round($rs->discount, 2);
          $row->qty = round($qty, 2);
          $row->amount = 0;
          $ds[] = $row;
        }
      }
    }
    else
    {
      $sc = FALSE;
      $this->error = "ไม่พบข้อมูล";
    }

    echo $sc === TRUE ? json_encode($ds) : $this->error;
  }


  public function get_item()
  {
    $sc = TRUE;

    $code = trim($this->input->post('item_code'));

    if( ! empty($code))
    {
      $item = $this->products_model->get($code);

      if(empty($item))
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

    $arr = array(
      'status' => $sc === TRUE ? 'success' : 'failed',
      'message' => $sc === TRUE ? 'success' : $this->error,
      'item' => $sc === TRUE ? $item : NULL
    );

    echo json_encode($arr);
  }


  public function get_item_grid()
  {
    $this->load->model('masters/product_style_model');
    $this->load->library('purchase_grid');

		$sc = TRUE;

		$ds = array();
    //----- Attribute Grid By Clicking image
    $style = $this->product_style_model->get_with_old_code($this->input->post('style_code'));

    if( ! empty($style))
    {
      //--- ถ้าได้ style เดียว จะเป็น object ไม่ใช่ array
      if(! is_array($style))
      {
        if($style->active)
        {
        	$table = $this->purchase_grid->getProductGrid($style->code);
        	$tableWidth	= $this->products_model->countAttribute($style->code) == 1 ? 600 : $this->purchase_grid->getGridTableWidth($style->code);

					if($table == 'notfound') {
						$sc = FALSE;
						$this->error = "ไม่พบรายการสินค้า";
					}
					else
					{
            $tbs = '<table class="table table-bordered border-1" style="min-width:'.$tableWidth.'px;">';
            $tbe = '</table>';
						$ds = array(
							'status' => 'success',
							'message' => NULL,
							'table' => $tbs.$table.$tbe,
							'tableWidth' => $tableWidth + 20,
							'styleCode' => $style->code
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
      'sm_reference',
      'sm_customer_code',
      'sm_from_date',
      'sm_to_date',
      'sm_status',
      'sm_approve',
			'sm_warehouse',
      'sm_user',
      'sm_zone',
			'sm_is_pos',
      'sm_sap'
    );
    clear_filter($filter);
  }


} //--- end class
?>
