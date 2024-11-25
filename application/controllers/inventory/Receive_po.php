<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Receive_po extends PS_Controller
{
  public $menu_code = 'ICPURC';
	public $menu_group_code = 'IC';
  public $menu_sub_group_code = 'RECEIVE';
	public $title = 'รับสินค้าจากการซื้อ';
  public $filter;
  public $error;
  public $required_remark = 0;

  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'inventory/receive_po';
    $this->load->model('inventory/receive_po_model');
    $this->load->model('stock/stock_model');
    $this->load->model('orders/orders_model');
    $this->load->model('masters/products_model');
  }


  public function index()
  {
    $this->load->helper('channels');

    $filter = array(
      'code'    => get_filter('code', 'receive_code', ''),
      'invoice' => get_filter('invoice', 'receive_invoice', ''),
      'po'      => get_filter('po', 'receive_po', ''),
      'vendor'  => get_filter('vendor', 'receive_vendor', ''),
      'user' => get_filter('user', 'receive_user', ''),
      'from_date' => get_filter('from_date', 'receive_from_date', ''),
      'to_date' => get_filter('to_date', 'receive_to_date', ''),
      'status' => get_filter('status', 'receive_status', 'all'),
      'sap' => get_filter('sap', 'receive_sap', 'all')
    );

    if($this->input->post('search'))
    {
      redirect($this->home);
    }
    else
    {
      //--- แสดงผลกี่รายการต่อหน้า
      $perpage = get_rows();

      $segment  = 4; //-- url segment
      $rows     = $this->receive_po_model->count_rows($filter);
      //--- ส่งตัวแปรเข้าไป 4 ตัว base_url ,  total_row , perpage = 20, segment = 3
      $init	    = pagination_config($this->home.'/index/', $rows, $perpage, $segment);
      $document = $this->receive_po_model->get_list($filter, $perpage, $this->uri->segment($segment));

      $filter['document'] = $document;

      $this->pagination->initialize($init);
      $this->load->view('inventory/receive_po/receive_po_list', $filter);
    }
  }


  public function view_detail($code)
  {
    $this->load->model('masters/zone_model');
    $this->load->model('masters/products_model');
    $this->load->model('approve_logs_model');

    $doc = $this->receive_po_model->get($code);

    if(! empty($doc))
    {
      $doc->zone_name = $this->zone_model->get_name($doc->zone_code);

      $details = $this->receive_po_model->get_details($code);

      $ds = array(
        'doc' => $doc,
        'details' => $details,
        'approve_logs' => $this->approve_logs_model->get($doc->request_code)
      );

      $this->load->view('inventory/receive_po/receive_po_view_detail', $ds);
    }
    else
    {
      $this->error_page();
    }
  }



  public function print_detail($code)
  {
    $this->load->library('printer');
    $this->load->model('masters/zone_model');
    $this->load->model('masters/products_model');

    $doc = $this->receive_po_model->get($code);

    if(!empty($doc))
    {
      $zone = $this->zone_model->get($doc->zone_code);
      $doc->zone_name = empty($zone) ? "" : $zone->name;
      $doc->warehouse_name = empty($zone) ? "" : $zone->warehouse_name;
    }

    $details = $this->receive_po_model->get_details($code);

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

    $this->load->view('print/print_received', $ds);
  }



	public function save()
  {
    $sc = TRUE;
    $ex = 1;
    $code = NULL;
    $isDraft = FALSE;

    if($this->input->post('header') && $this->input->post('items'))
    {
      $this->load->model('masters/products_model');
      $this->load->model('masters/zone_model');
      $this->load->model('inventory/movement_model');

			$header = json_decode($this->input->post('header'));

			if( ! empty($header))
			{
				$items = json_decode($this->input->post('items'));

				if( ! empty($items))
				{
          $date_add = db_date($header->date_add);
					$vendor_code = $header->vendor_code;
		      $vendor_name = $header->vendorName;
		      $po_code = $header->poCode;
		      $invoice = $header->invoice;
		      $zone_code = $header->zone_code;
          $zone = $this->zone_model->get($zone_code);
		      $warehouse_code = empty($zone) ? NULL : $zone->warehouse_code;
		      $approver = get_null($header->approver);
					$DocCur = $header->DocCur;
					$DocRate = $header->DocRate;
          $remark = $header->remark;
          $isDraft = $header->isDraft == 1 ? TRUE : FALSE;

          $code = empty($header->code) ? $this->get_new_code($date_add) : $header->code;

          $this->db->trans_begin();

          if(empty($header->code))
          {
            $arr = array(
              'code' => $code,
              'date_add' => $date_add,
              'bookcode' => getConfig('BOOK_CODE_RECEIVE_PO'),
              'vendor_code' => $vendor_code,
              'vendor_name' => $vendor_name,
              'po_code' => $po_code,
              'invoice_code' => $invoice,
              'zone_code' => $zone_code,
              'warehouse_code' => $warehouse_code,
              'user' => $this->_user->uname,
              'approver' => $approver,
              'currency' => empty($DocCur) ? "THB" : $DocCur,
              'rate' => empty($DocRate) ? 1 : $DocRate,
              'totalQty' => round($header->totalQty, 2),
              'DiscPrcnt' => $header->DiscPrcnt,
              'DiscAmount' => $header->DiscAmount,
              'VatSum' => $header->VatSum,
              'DocTotal' => $header->DocTotal,
              'remark' => $header->remark,
              'status' => $isDraft ? 0 : 1
            );

            if( ! $this->receive_po_model->add($arr))
            {
              $sc = FALSE;
              $this->error = "Create Document Failed";
            }
          }
          else
          {
            $doc = $this->receive_po_model->get($code);

            if( ! empty($doc))
            {
              if($doc->status == 0) {

                $arr = array(
                  'date_add' => $date_add,
                  'vendor_code' => $vendor_code,
                  'vendor_name' => $vendor_name,
                  'po_code' => $po_code,
                  'invoice_code' => $invoice,
                  'zone_code' => $zone_code,
                  'warehouse_code' => $warehouse_code,
                  'user' => $this->_user->uname,
                  'approver' => $approver,
                  'currency' => empty($DocCur) ? "THB" : $DocCur,
                  'rate' => empty($DocRate) ? 1 : $DocRate,
                  'totalQty' => round($header->totalQty, 2),
                  'DiscPrcnt' => $header->DiscPrcnt,
                  'DiscAmount' => $header->DiscAmount,
                  'VatSum' => $header->VatSum,
                  'DocTotal' => $header->DocTotal,
                  'remark' => $header->remark,
                  'status' => $isDraft ? 0 : 1
                );

                if( ! $this->receive_po_model->update($code, $arr))
                {
                  $sc = FALSE;
                  $this->error = "Update Document Failed";
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
              $this->error = "Invalid document number";
            }
          }

          if($sc === TRUE)
          {

            if( ! $this->receive_po_model->drop_details($code))
            {
              $sc === FALSE;
              $this->error = "Failed to delete previous rows";
            }
            else
            {
              //---- insert rows
              foreach($items as $rs)
		          {
                if($sc === FALSE) { break; }

		            if($rs->qty > 0)
		            {
		              $pd = $this->products_model->get($rs->product_code);

		              if( ! empty($pd))
		              {
                    $bf = get_zero($rs->backlogs); ///--- ยอดค้ารับ ก่อนรับ
		                $af = ($bf - $rs->qty) > 0 ? ($bf - $rs->qty) : 0;  //--- ยอดค้างรับหลังรับแล้ว

		                $ds = array(
		                  'receive_code' => $code,
                      'baseCode' => get_null($po_code),
                      'baseEntry' => get_null($rs->baseEntry),
                      'baseLine' => get_null($rs->baseLine),
		                  'style_code' => $pd->style_code,
		                  'product_code' => $pd->code,
		                  'product_name' => $pd->name,
                      'PriceBefDi' => $rs->PriceBefDi,
                      'DiscPrcnt' => $rs->DiscPrcnt,
		                  'price' => $rs->price,
		                  'qty' => $rs->qty,
                      'receive_qty' => $rs->qty,
		                  'amount' => round($rs->amount, 2),
                      'unit_code' => $rs->UomCode,
                      'unitMsr' => $rs->unitMsr,
                      'NumPerMsr' => $rs->NumPerMsr,
                      'unitMsr2' => $rs->unitMsr2,
                      'NumPerMsr2' => $rs->NumPerMsr2,
                      'UomEntry' => $rs->UomEntry,
                      'UomEntry2' => $rs->UomEntry2,
                      'UomCode' => $rs->UomCode,
                      'UomCode2' => $rs->UomCode2,
                      'billDiscPrcnt' => $header->DiscPrcnt,
                      'before_backlogs' => $bf,
                      'after_backlogs' => $af,
											'currency' => empty($DocCur) ? "THB" : $DocCur,
											'rate' => empty($DocRate) ? 1 : $DocRate,
                      'vatGroup' => $rs->vatGroup,
                      'vatRate' => $rs->vatRate,
                      'vatAmount' => $rs->vatAmount
		                );


		                if( ! $this->receive_po_model->add_detail($ds))
		                {
		                  $sc = FALSE;
		                  $this->error = 'Add Receive Row Fail';
		                  break;
		                }

		                if($sc === TRUE && ! $isDraft)
		                {
                      $move_in = $rs->qty * $rs->NumPerMsr;
                      //--- insert Movement in
                      $arr = array(
                        'reference' => $code,
                        'warehouse_code' => $warehouse_code,
                        'zone_code' => $zone_code,
                        'product_code' => $rs->product_code,
                        'move_in' => $move_in,
                        'move_out' => 0,
                        'date_add' => $date_add
                      );

                      if( ! $this->movement_model->add($arr))
                      {
                        $sc = FALSE;
                        $this->error = "Insert Movement Failed";
                      }
		                }
		              }
		              else
		              {
		                $sc = FALSE;
		                $this->error = 'ไม่พบรหัสสินค้า : '.$item.' ในระบบ';
		              }
		            } //-- qty > 0
		          } //--- foreach items
            } //-- if(add)
          }

          if($sc === TRUE)
          {
            $this->db->trans_commit();
          }
          else
          {
            $this->db->trans_rollback();
          }

          if($sc === TRUE && ! $isDraft)
          {
            $this->load->library('export');

            if(! $this->export->export_receive($code))
            {
              $ex = 0;
              $this->error = "บันทึกสำเร็จ แต่ส่งข้อมูลเข้า SAP ไม่สำเร็จ <br/> ".trim($this->export->error);
            }
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
      set_error('required');
    }

    $arr = array(
      'status' => $sc === TRUE ? 'success' : 'failed',
      'message' => $this->error,
      'ex' => $ex,
      'code' => $code
    );

    echo json_encode($arr);
  }


  public function do_export($code)
  {
    $rs = $this->export_receive($code);

    echo $rs === TRUE ? 'success' : $this->error;
  }


  private function export_receive($code)
  {
    $sc = TRUE;
    $this->load->library('export');
    if(! $this->export->export_receive($code))
    {
      $sc = FALSE;
      $this->error = trim($this->export->error);
    }

    return $sc;
  }


  public function cancle_received()
  {
    $sc = TRUE;

    if($this->input->post('receive_code'))
    {
      $this->load->model('inventory/movement_model');
      $code = $this->input->post('receive_code');
			$reason = $this->input->post('reason');

      //---- check doc status is open or close
      //---- if closed user cannot cancle document
      $sap = $this->receive_po_model->get_sap_receive_doc($code);

      if(empty($sap))
      {
        $middle = $this->receive_po_model->get_middle_receive_po($code);

        if(! empty($middle))
        {
          foreach($middle as $rs)
          {
            $this->receive_po_model->drop_sap_received($rs->DocEntry);
          }
        }

        $this->db->trans_begin();

        if( ! $this->receive_po_model->cancle_details($code))
        {
          $sc = FALSE;
          $this->error = "ยกเลิกรายการไม่สำเร็จ";
        }

        if($sc === TRUE)
        {
          $arr = array(
            'status' => 2, //--- 0 = ยังไม่บันทึก 1 = บันทึกแล้ว 2 = ยกเลิก
            'cancle_reason' => $reason,
            'cancle_user' => $this->_user->uname,
            'cancle_date' => now()
          );

          if( ! $this->receive_po_model->update($code, $arr))
          {
            $sc = FALSE;
            $this->error = "ยกเลิกเอกสารไม่สำเร็จ";
          }

          if($sc === TRUE)
          {
            if( ! $this->movement_model->drop_movement($code))
            {
              $sc = FALSE;
              $this->error = "Failed to delete stock movement";
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
        $this->error = 'กรุณายกเลิกใบรับสินค้าบน SAP ก่อนทำการยกเลิก';
      }
    }
    else
    {
      $sc = FALSE;
      $this->error = 'ไม่พบเลขทีเอกสาร';
    }

    echo $sc === TRUE ? 'success' : $this->error;
  }


  public function cancle_sap_doc($code)
  {
    $sc = TRUE;

    $middle = $this->receive_po_model->get_middle_receive_po($code);
    if(!empty($middle))
    {
      foreach($middle as $rs)
      {
        $this->receive_po_model->drop_sap_received($rs->DocEntry);
      }
    }

    return $sc;
  }



  public function get_po_detail()
  {
    $sc = TRUE;
    $ds = array();

    $po_code = $this->input->get('po_code');

    $po = $this->receive_po_model->get_po($po_code);

    if( ! empty($po))
    {
      $ro = getConfig('RECEIVE_OVER_PO');

      $rate = ($ro * 0.01);

      $details = $this->receive_po_model->get_po_details($po_code);

      if( ! empty($details))
      {
        $no = 1;

        foreach($details as $rs)
        {
  				if($rs->OpenQty > 0)
  				{
            $dif = $rs->Quantity - $rs->OpenQty;
            $onOrder = $this->receive_po_model->get_on_order_qty($rs->ItemCode, $po_code, $rs->DocEntry, $rs->LineNum);

            $qty = $rs->OpenQty - $onOrder;
  	        $arr = array(
  	          'no' => $no,
              'uid' => $rs->DocEntry.$rs->LineNum,
              'product_code' => $rs->ItemCode,
              'product_name' => $rs->Dscription.' '.$rs->Text,
              'baseCode' => $po_code,
              'baseEntry' => $rs->DocEntry,
              'baseLine' => $rs->LineNum,
              'vatCode' => $rs->VatGroup,
              'vatRate' => $rs->VatPrcnt,
              'unitCode' => $rs->unitMsr,
              'unitMsr' => $rs->unitMsr,
              'NumPerMsr' => $rs->NumPerMsr,
              'unitMsr2' => $rs->unitMsr2,
              'NumPerMsr2' => $rs->NumPerMsr2,
              'UomEntry' => $rs->UomEntry,
              'UomEntry2' => $rs->UomEntry2,
              'UomCode' => $rs->UomCode,
              'UomCode2' => $rs->UomCode2,
  	          'PriceBefDi' => round($rs->PriceBefDi, 3),
              'PriceBefDiLabel' => number($rs->PriceBefDi, 3),
              'DiscPrcnt' => round($rs->DiscPrcnt, 2),
              'Price' => round($rs->Price, 3),
              'PriceAfDiscLabel' => number($rs->Price, 3),
              'onOrder' => $onOrder,
              'qty' => $qty,
  	          'qtyLabel' => number($qty, 2),
              'backlogs' => $rs->OpenQty,
  	          'limit' => ($rs->Quantity + ($rs->Quantity * $rate)) - $dif,
  	          'isOpen' => $rs->LineStatus === 'O' ? TRUE : FALSE
  	        );

  	        array_push($ds, $arr);
  	        $no++;
  				}
        }
      }
      else
      {
        $sc = FALSE;
        $this->error = "ใบสั่งซื้อไม่ถูกต้อง หรือ ใบสั่งซื้อถูกปิดไปแล้ว";
      }
    }
    else
    {
      $sc = FALSE;
      $this->error = "ไม่พบใบสั่งซื้อ";
    }

    $arr = array(
      'status' => $sc === TRUE ? 'success' : 'failed',
      'message' => $sc === TRUE ? 'success' : $this->error,
      'DocNum' => $sc === TRUE ? $po->DocNum : NULL,
      'DocCur' => $sc === TRUE ? $po->DocCur : NULL,
      'DocRate' => $sc === TRUE ? $po->DocRate : NULL,
      'CardCode' => $sc === TRUE ? $po->CardCode : NULL,
      'CardName' => $sc === TRUE ? $po->CardName : NULL,
      'DiscPrcnt' => $sc === TRUE ? $po->DiscPrcnt : NULL,
      'details' => $sc === TRUE ? $ds : NULL
    );

    echo json_encode($arr);
  }


  public function edit($code)
  {
    $this->load->model('masters/zone_model');
		$this->load->helper('currency');

    $doc = $this->receive_po_model->get($code);

    if( ! empty($doc))
    {
      $doc->zone_name = empty($doc->zone_code) ? NULL : $this->zone_model->get_name($doc->zone_code);

      $details = $this->receive_po_model->get_details($code);

      if( ! empty($details) &&  ! empty($doc->po_code))
      {
        $ro = getConfig('RECEIVE_OVER_PO');
        $rate = $ro * 0.01;

        foreach($details as $rs)
        {
          if( ! empty($rs->baseEntry) && ! is_null($rs->baseLine) && $rs->baseLine != '')
          {
            $line = $this->receive_po_model->get_po_row($rs->baseEntry, $rs->baseLine);

            if( ! empty($line))
            {
              if($line->OpenQty > 0)
              {
                $rs->LineStatus = $line->LineStatus;
                $rs->limit = ($line->Quantity + ($line->Quantity * $rate)) - ($line->Quantity - $line->OpenQty);
                $rs->before_backlogs = $line->OpenQty;
              }
              else
              {
                $rs->LineStatus = 'C';
                $rs->limit = 0;
                $rs->receive_qty = 0;
                $rs->before_backlogs = 0;
                $rs->amount = 0;
              }
            }
          }
        }
      }

      $ds = array(
        'doc' => $doc,
        'is_strict' => getConfig('STRICT_RECEIVE_PO'),
        'allow_over_po' => getConfig('ALLOW_RECEIVE_OVER_PO'),
        'details' => $details
      );

      $this->load->view('inventory/receive_po/receive_po_edit', $ds);
    }
    else
    {
      $this->page_error();
    }
  }


  public function add_new()
  {
    $this->load->helper('currency');
    $doc = array(
      'user' => $this->_user->uname,
      'DiscPrcnt' => 0.00,
      'DiscAmount' => 0.00,
      'VatSum' => 0.00,
      'DocTotal' => 0.00,
      'remark' => NULL
    );

    $ds = array(
      'doc' => (object) $doc
    );

    $this->load->view('inventory/receive_po/receive_po_add', $ds);
  }


  public function update_header()
  {
    $sc = TRUE;
    $code = $this->input->post('code');
    $date = db_date($this->input->post('date_add'), TRUE);
    $remark = get_null(trim($this->input->post('remark')));

    if(!empty($code))
    {
      $doc = $this->receive_po_model->get($code);

      if(!empty($doc))
      {
        if($doc->status == 0)
        {
          $arr = array(
            'date_add' => $date,
            'remark' => $remark
          );

          if(! $this->receive_po_model->update($code, $arr))
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


  public function unsave()
  {
    $sc = TRUE;
    $code = $this->input->post('code');

    if( ! empty($code))
    {
      $this->load->model('inventory/movement_model');

      $doc = $this->receive_po_model->get($code);

      if( ! empty($doc))
      {
        if($doc->status == 1)
        {
          //---- check doc status is open or close
          //---- if closed user cannot cancle document
          $sap = $this->receive_po_model->get_sap_receive_doc($code);

          if( ! empty($sap))
          {
            $sc = FALSE;
            $this->error = 'กรุณายกเลิกใบรับสินค้าบน SAP ก่อนทำการย้อนสถาน';
          }

          if($sc === TRUE)
          {
            $middle = $this->receive_po_model->get_middle_receive_po($code);

            if(! empty($middle))
            {
              foreach($middle as $rs)
              {
                $this->receive_po_model->drop_sap_received($rs->DocEntry);
              }
            }
          }

          if($sc === TRUE)
          {
            $this->db->trans_begin();

            if( ! $this->movement_model->drop_movement($code))
            {
              $sc = FALSE;
              $this->error = "Failed to delete stock movement";
            }

            if($sc === TRUE)
            {
              $arr = array(
                'status' => 0,
                'update_user' => $this->_user->uname,
              );

              if( ! $this->receive_po_model->update($code, $arr))
              {
                $sc = FALSE;
                $this->error = "Failed to update document status";
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
          $this->error = "Invalid document status";
        }
      }
      else
      {
        $sc = FALSE;
        $this->error = "ไม่พบเลขที่เอกสาร";
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


  public function get_sell_stock($item_code, $warehouse = NULL, $zone = NULL)
  {
    $sell_stock = $this->stock_model->get_sell_stock($item_code, $warehouse, $zone);
    $reserv_stock = $this->orders_model->get_reserv_stock($item_code, $warehouse, $zone);
    $availableStock = $sell_stock - $reserv_stock;
    return $availableStock < 0 ? 0 : $availableStock;
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



  public function get_new_code($date)
  {
    $date = $date == '' ? date('Y-m-d') : $date;
    $Y = date('y', strtotime($date));
    $M = date('m', strtotime($date));
    $prefix = getConfig('PREFIX_RECEIVE_PO');
    $run_digit = getConfig('RUN_DIGIT_RECEIVE_PO');
    $pre = $prefix .'-'.$Y.$M;
    $code = $this->receive_po_model->get_max_code($pre);
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
      'receive_code',
      'receive_invoice',
      'receive_po',
      'receive_vendor',
      'receive_from_date',
      'receive_to_date',
      'receive_status',
      'receive_sap',
      'receive_user',
      'receive_must_accept'
    );

    clear_filter($filter);
    echo "done";
  }


  public function get_vender_by_po($po_code)
  {
    $rs = $this->receive_po_model->get_vender_by_po($po_code);
    if(!empty($rs))
    {
      $arr = array(
        'code' => $rs->CardCode,
        'name' => $rs->CardName
      );

      echo json_encode($arr);
    }
    else
    {
      echo 'Not found';
    }
  }

} //--- end class
