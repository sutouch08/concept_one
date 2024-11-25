<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Receive_po_request extends PS_Controller
{
  public $menu_code = 'ICRQRC';
	public $menu_group_code = 'IC';
  public $menu_sub_group_code = 'RECEIVE';
	public $title = 'ใบขออนุมัติรับสินค้า';
  public $filter;
  public $error;
  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'inventory/receive_po_request';
    $this->load->model('inventory/receive_po_request_model');
    $this->load->model('stock/stock_model');
    $this->load->model('orders/orders_model');
    $this->load->model('masters/products_model');
    $this->load->library('api');
  }


  public function index()
  {
    $filter = array(
      'code'    => get_filter('code', 'request_code', ''),
      'invoice' => get_filter('invoice', 'request_invoice', ''),
      'po'      => get_filter('po', 'request_po', ''),
      'vendor'  => get_filter('vendor', 'request_vendor', ''),
      'from_date' => get_filter('from_date', 'request_from_date', ''),
      'to_date' => get_filter('to_date', 'request_to_date', ''),
      'status' => get_filter('status', 'request_status', 'all'),
      'valid' => get_filter('valid', 'request_valid', 'all'),
      'isApprove' => get_filter('isApprove', 'request_isApprove', 'all')
    );

		//--- แสดงผลกี่รายการต่อหน้า
		$perpage = get_rows();
		//--- หาก user กำหนดการแสดงผลมามากเกินไป จำกัดไว้แค่ 300
		if($perpage > 300)
		{
			$perpage = 20;
		}

		$segment  = 4; //-- url segment
		$rows     = $this->receive_po_request_model->count_rows($filter);
		//--- ส่งตัวแปรเข้าไป 4 ตัว base_url ,  total_row , perpage = 20, segment = 3
		$init	    = pagination_config($this->home.'/index/', $rows, $perpage, $segment);
		$document = $this->receive_po_request_model->get_data($filter, $perpage, $this->uri->segment($segment));

    if(!empty($document))
    {
      foreach($document as $rs)
      {
        $rs->qty = $this->receive_po_request_model->get_sum_qty($rs->code);
      }
    }

    $filter['document'] = $document;

		$this->pagination->initialize($init);
    $this->load->view('inventory/receive_po_request/receive_po_request_list', $filter);
  }



  public function view_detail($code, $approve_view = NULL)
  {
    $this->load->model('masters/products_model');
    $this->load->model('approve_logs_model');

    $doc = $this->receive_po_request_model->get($code);
    if(!empty($doc))
    {
      $details = $this->receive_po_request_model->get_details($code);
      if(!empty($details))
      {
        foreach($details as $rs)
        {
          $rs->backlogs = $this->receive_po_request_model->get_backlogs($doc->po_code, $rs->product_code);
        }
      }


      $ds = array(
        'doc' => $doc,
        'details' => $details,
        'approve_view' => $approve_view,
        'approve_list' => $this->approve_logs_model->get($doc->code)
      );

      $this->load->view('inventory/receive_po_request/receive_po_request_detail', $ds);
    }
    else
    {
      $this->load->view('page_error');
    }


  }



  public function print_detail($code)
  {
    $this->load->library('printer');

    $doc = $this->receive_po_request_model->get($code);
    if(!empty($doc))
    {
      $details = $this->receive_po_request_model->get_details($code);

      if(!empty($details))
      {
        foreach($details as $rs)
        {
          $rs->backlogs = $this->receive_po_request_model->get_backlogs($doc->po_code, $rs->product_code);
        }
      }

      $ds = array(
        'doc' => $doc,
        'details' => $details
      );

      $this->load->view('print/print_receive_request', $ds);
    }
    else
    {
      $this->load->view('page_error');
    }
  }



  public function save()
  {
    $sc = TRUE;
    if($this->input->post('receive_code'))
    {
      $this->load->model('masters/products_model');
      $this->load->model('inventory/movement_model');

      $code = $this->input->post('receive_code');

			$doc = $this->receive_po_request_model->get($code);

			$date_add = getConfig('ORDER_SOLD_DATE') == 'D' ? $doc->date_add : now();

			$header = json_decode($this->input->post('header'));

      if( ! empty($header))
      {
        $items = json_decode($this->input->post('items'));

        if( ! empty($items))
        {
          $vendor_code = $header->vendor_code;
		      $vendor_name = $header->vendorName;
		      $po_code = $header->poCode;
		      $invoice = $header->invoice;
		      $approver = get_null($header->approver);
					$DocCur = $header->DocCur;
					$DocRate = $header->DocRate;

					$arr = array(
		        'vendor_code' => $vendor_code,
		        'vendor_name' => $vendor_name,
		        'po_code' => $po_code,
		        'invoice_code' => $invoice,
		        'update_user' => get_cookie('uname'),
						'currency' => empty($DocCur) ? "THB" : $DocCur,
						'rate' => empty($DocRate) ? 1 : $DocRate
		      );

					$this->db->trans_begin();

          if($this->receive_po_request_model->update($code, $arr) === FALSE)
		      {
		        $sc = FALSE;
		        $this->error = 'Update Document Fail';
		      }
          else
		      {
            //--- ลบรายการเก่าก่อนเพิ่มรายการใหม่
            $this->receive_po_request_model->drop_details($code);

            foreach($items as $rs)
            {
              if($rs->qty > 0)
              {
                $pd = $this->products_model->get($rs->product_code);

                if(!empty($pd))
                {
                  $ds = array(
                    'receive_code' => $code,
                    'baseEntry' => $rs->baseEntry,
                    'baseLine' => $rs->baseLine,
                    'style_code' => $pd->style_code,
                    'product_code' => $rs->product_code,
                    'product_name' => $rs->product_name,
                    'price' => $rs->price,
                    'qty' => $rs->qty,
                    'amount' => $rs->qty * $rs->price,
                    'currency' => empty($DocCur) ? "THB" : $DocCur,
                    'rate' => empty($DocRate) ? 1 : $DocRate,
                    'vatGroup' => $rs->vatGroup,
                    'vatRate' => $rs->vatRate
                  );

                  if($this->receive_po_request_model->add_detail($ds) === FALSE)
                  {
                    $sc = FALSE;
                    $this->error = 'Add Receive Row Fail';
                    break;
                  }
                }
                else
                {
                  $sc = FALSE;
                  $this->error = 'ไม่พบรหัสสินค้า : '.$item.' ในระบบ';
                }
              } //--- if qty > 0
            } //-- end foreach

            $this->receive_po_request_model->set_status($code, 1);
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
					$this->error = "Items rows not found!";
				}
      }
      else
			{
				$sc = FALSE;
				$this->error = "Header data not found!";
			}
    }
    else
    {
      $sc = FALSE;
      $this->error = "Missing form data";
    }

    echo $sc === TRUE ? 'success' : $this->error;
  }




  public function cancle_received()
  {
    $sc = TRUE;
    if($this->input->post('receive_code'))
    {
      $code = $this->input->post('receive_code');
      $doc = $this->receive_po_request_model->get($code);
      if($doc->valid == 0)
      {
        $this->db->trans_begin();
        if(! $this->receive_po_request_model->cancle_details($code))
        {
          $sc = FALSE;
          $this->error = "ยกเลิกรายการไม่สำเร็จ";
        }

        //--- 0 = ยังไม่บันทึก 1 = บันทึกแล้ว 2 = ยกเลิก
        if(! $this->receive_po_request_model->set_status($code, 2))
        {
          $sc = FALSE;
          $this->error = "ยกเลิกเอกสารไม่สำเร็จ";
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
        $this->error = 'เอกสารถูกดึงไปรับเข้าแล้วไม่สามารถยกเลิกได้';
      }
    }
    else
    {
      $sc = FALSE;
      $this->error = 'ไม่พบเลขทีเอกสาร';
    }

    echo $sc === TRUE ? 'success' : $this->error;
  }





  public function get_po_detail()
  {
    $sc = '';
    $this->load->model('masters/products_model');
    $po_code = $this->input->get('po_code');
    $details = $this->receive_po_request_model->get_po_details($po_code);
    $allow_over_po = getConfig('ALLOW_RECEIVE_OVER_PO') == 1 ? TRUE : FALSE;
    $ro = getConfig('RECEIVE_OVER_PO');
    $rate = ($ro * 0.01);
    $ds = array();
    if(!empty($details))
    {
      $no = 1;
      $totalQty = 0;
      $totalBacklog = 0;

      foreach($details as $rs)
      {
        $dif = $rs->Quantity - $rs->OpenQty;
        $arr = array(
          'no' => $no,
          'uid' => $rs->DocEntry.$rs->LineNum,
          'docEntry' => $rs->DocEntry,
          'lineNum' => $rs->LineNum,
          'barcode' => $this->products_model->get_barcode($rs->ItemCode),
          'pdCode' => $rs->ItemCode,
          'pdName' => $rs->Dscription,
          'price' => $rs->price,
          'currency' => $rs->Currency,
          'Rate' => $rs->Rate,
          'vatGroup' => $rs->VatGroup,
          'vatRate' => $rs->VatPrcnt,
          'qty' => number($rs->Quantity),
          'limit' => $allow_over_po ? ($rs->Quantity + ($rs->Quantity * $rate)) - $dif : $rs->OpenQty,
          'backlog' => number($rs->OpenQty),
          'isOpen' => $rs->LineStatus === 'O' ? TRUE : FALSE
        );
        array_push($ds, $arr);
        $no++;
        $totalQty += $rs->Quantity;
        $totalBacklog += $rs->OpenQty;
      }

      $arr = array(
        'qty' => number($totalQty),
        'backlog' => number($totalBacklog)
      );
      array_push($ds, $arr);

      $sc = json_encode($ds);
    }
    else
    {
      $sc = 'ใบสั่งซื้อไม่ถูกต้อง หรือ ใบสั่งซื้อถูกปิดไปแล้ว';
    }

    echo $sc;
  }




  public function edit($code)
  {
    $this->load->helper('currency');
    $document = $this->receive_po_request_model->get($code);
    $ds['document'] = $document;
    $ds['allow_over_po'] = getConfig('ALLOW_RECEIVE_OVER_PO');
    $this->load->view('inventory/receive_po_request/receive_po_request_edit', $ds);
  }


  public function get_po_currency()
  {
    $this->load->model('inventory/receive_po_model');
    $po_code = $this->input->get('po_code');

    $rs = $this->receive_po_model->get_po_currency($po_code);

    if(!empty($rs))
    {
      echo json_encode($rs);
    }
    else
    {
      echo "not found";
    }
  }


  public function add_new()
  {
    $this->load->view('inventory/receive_po_request/receive_po_request_add');
  }



  //--- check exists document code
  public function is_exists($code)
  {
    $ext = $this->receive_po_request_model->is_exists($code);
    if($ext)
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
    $sc = array();

    if($this->input->post('date_add'))
    {
      $date_add = db_date($this->input->post('date_add'), TRUE);
      if($this->input->post('code'))
      {
        $code = $this->input->post('code');
      }
      else
      {
        $code = $this->get_new_code($date_add);
      }


      $arr = array(
        'code' => $code,
        'bookcode' => getConfig('BOOK_CODE_RECEIVE_PO'),
        'vendor_code' => NULL,
        'vendor_name' => NULL,
        'po_code' => NULL,
        'invoice_code' => NULL,
        'remark' => $this->input->post('remark'),
        'date_add' => $date_add,
        'user' => get_cookie('uname')
      );

      $rs = $this->receive_po_request_model->add($arr);
      if($rs)
      {
        redirect($this->home.'/edit/'.$code);
      }
      else
      {
        set_error('เพิ่มเอกสารไม่สำเร็จ กรุณาลองใหม่อีกครั้ง');
        redirect($this->home.'/add_new');
      }
    }
  }



  public function update_header()
  {
    $sc = TRUE;
    $code = $this->input->post('code');
    $date = db_date($this->input->post('date_add'), TRUE);
    $remark = trim($this->input->post('remark'));

    if(!empty($code))
    {
      $doc = $this->receive_po_request_model->get($code);
      if(!empty($doc))
      {
        if($doc->status == 0)
        {
          $arr = array(
            'date_add' => $date,
            'remark' => $remark
          );

          if(! $this->receive_po_request_model->update($code, $arr))
          {
            $sc = FALSE;
            $this->error = "ปรับปรุงข้อมูลไม่สำเร็จ";
          }
        }
        else
        {
          $sc = FALSE;
          $this->error = "เอกสารถูกบันทึกแล้วไม่สามารถแก้ไขได้";
        }
      }
      else
      {
        $sc = FALSE;
        $this->error = "ไม่พบข้อมูล";
      }
    }
    else
    {
      $sc = FALSE;
      $this->error = "ไม่พบเลขทีเอกสาร";
    }

    echo $sc === TRUE ? 'success' : $this->error;
  }





  public function get_un_approve_list()
  {
    $limit = empty($this->input->get('limit')) ? 10 : intval($this->input->get('limit'));
    $rows = $this->receive_po_request_model->count_un_approve_rows();
    $list = $this->receive_po_request_model->get_un_approve_list($limit);

    $result_rows = empty($list) ? 0 :count($list);

    $ds = array();
    if(!empty($list))
    {
      foreach($list as $rs)
      {
        $arr = array(
          'code' => $rs->code,
          'vendor_name' => $rs->vendor_name
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



  public function do_approve()
  {
    $sc = TRUE;
    $code = $this->input->post('code');
    if(!empty($code))
    {
      if($this->pm->can_approve)
      {
        $user = get_cookie('uname');
        $this->load->model('approve_logs_model');
        if(! $this->receive_po_request_model->set_approve($code, 1))
        {
          $sc = FALSE;
          $this->error = "อนุมัติเอกสารไม่สำเร็จ";
        }
        else
        {
          $this->approve_logs_model->add($code, 1, $user);
        }

      }
      else
      {
        $sc = FALSE;
        $this->error = "คุณไม่มีสิทธิ์ในการอนุมัติ";
      }
    }
    else
    {
      $sc = FALSE;
      $this->error = "ไม่พบเลขที่เอกสาร";
    }

    echo $sc === TRUE ? 'success' : $this->error;
  }



  public function un_approve()
  {
    $sc = TRUE;
    $code = $this->input->post('code');
    if(!empty($code))
    {
      if($this->pm->can_approve)
      {
        $user = get_cookie('uname');
        $this->load->model('approve_logs_model');
        if(! $this->receive_po_request_model->set_approve($code, 0))
        {
          $sc = FALSE;
          $this->error = "ยกเลิกการอนุมัติเอกสารไม่สำเร็จ";
        }
        else
        {
          $this->approve_logs_model->add($code, 0, $user);
        }

      }
      else
      {
        $sc = FALSE;
        $this->error = "คุณไม่มีสิทธิ์ในการอนุมัติ";
      }
    }
    else
    {
      $sc = FALSE;
      $this->error = "ไม่พบเลขที่เอกสาร";
    }

    echo $sc === TRUE ? 'success' : $this->error;
  }




  public function get_new_code($date)
  {
    $date = $date == '' ? date('Y-m-d') : $date;
    $Y = date('y', strtotime($date));
    $M = date('m', strtotime($date));
    $prefix = getConfig('PREFIX_RECEIVE_PO_REQUEST');
    $run_digit = getConfig('RUN_DIGIT_RECEIVE_PO_REQUEST');
    $pre = $prefix .'-'.$Y.$M;
    $code = $this->receive_po_request_model->get_max_code($pre);
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


  public function clear_filter()
  {
    $filter = array(
      'request_code',
      'request_invoice',
      'request_po',
      'request_vendor',
      'request_from_date',
      'request_to_date',
      'request_status',
      'request_valid',
      'request_isApprove'
    );

    clear_filter($filter);
    echo "done";
  }


  public function get_vender_by_request_code()
  {
    $code = $this->input->get('code');
    if(!empty($code))
    {
      $rs = $this->receive_po_request_model->get_vender_by_request_code($code);
      if(!empty($rs))
      {
        $arr = array(
          'code' => $rs->vendor_code,
          'name' => $rs->vendor_name
        );

        echo json_encode($arr);
      }
      else
      {
        echo 'Not found';
      }
    }
    else
    {
      echo 'Not found';
    }

  }

} //--- end class
