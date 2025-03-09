<?php
class Order_down_payment extends PS_Controller
{
  public $menu_code = 'SOPOSDP';
  public $menu_group_code = 'POS';
  public $menu_sub_group_code = '';
  public $title = 'ใบรับเงินมัดจำ';
  public $img_folder = "payments";
  public $segment = 4;

  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'orders/order_down_payment';
    $this->load->model('orders/order_down_payment_model');
    $this->load->model('orders/order_pos_payment_model');
    $this->load->model('orders/down_payment_invoice_model');
    $this->load->model('orders/order_pos_model');
    $this->load->model('orders/sales_order_model');
    $this->load->model('orders/orders_model');
    $this->load->model('orders/pos_sales_movement_model');
    $this->load->model('masters/shop_model');
    $this->load->model('masters/pos_model');
    $this->load->model('masters/slp_model');
    $this->load->helper('shop');
    $this->load->helper('payment_method');
    $this->load->helper('order_pos');
    $this->load->helper('image');
  }

  public function index()
  {
    $filter = array(
      'shop_id' => get_filter('shop_id', 'dp_shop_id', 'all'),
      'pos_id' => get_filter('pos_id', 'dp_pos_id', 'all'),
      'code' => get_filter('code', 'dp_code', ''),
      'order_code' => get_filter('order_code', 'dp_order_code', ''),
      'bill_code' => get_filter('bill_code', 'dp_bill_code', ''),
      'payment' => get_filter('payment', 'dp_payment', 'all'),
      'has_slip' => get_filter('has_slip', 'dp_has_slip', 'all'),
      'status' => get_filter('status', 'dp_status', 'all'),
      'is_exported' => get_filter('is_exported', 'dp_is_exported', 'all'),
      'from_date' => get_filter('from_date', 'dp_from_date', ''),
      'to_date' => get_filter('to_date', 'dp_to_date', '')
    );

    if($this->input->post('search'))
    {
      redirect($this->home);
    }
    else
    {
      $perpage = get_rows();
      $rows = $this->order_down_payment_model->count_rows($filter);
      $filter['orders'] = $this->order_down_payment_model->get_list($filter, $perpage, $this->uri->segment($this->segment));
      $init = pagination_config($this->home.'/index/', $rows, $perpage, $this->segment);
      $this->pagination->initialize($init);
      $this->load->view('order_down_payment/order_down_payment_list', $filter);
    }
  }


  public function get($code)
  {
    $sc = TRUE;
    $doc = $this->order_down_payment_model->get($code);
    $row = NULL;

    if( ! empty($doc))
    {
      $row = new stdClass();
      $row->no = 1;
      $row->ItemCode = getConfig('DUMMY_ITEM');
      $row->Dscription = "รับเงินมัดจำค่าสินค้า ใบสั่งขายเลขที่ {$doc->reference}";
      $row->Qty = 1.00;
      $row->Price = number($doc->amount, 2);
      $row->Amount = number($doc->amount, 2);

      $doc->VatSum = get_vat_amount($doc->amount);
      $doc->PriceBefDi = $doc->amount - $doc->VatSum;
      $doc->DocTotal = round($doc->amount, 2);
    }
    else
    {
      $sc = FALSE;
      set_error('notfound');
    }

    $arr = array(
      'status' => $sc === TRUE ? 'success' : 'failed',
      'message' => $sc === TRUE ? 'success' : $this->error,
      'doc' => $doc,
      'row' => $row
    );

    echo json_encode($arr);
  }


  public function update_header()
  {
    $sc = TRUE;
    $ds = json_decode($this->input->post('data'));

    if( ! empty($ds))
    {
      $doc = $this->order_down_payment_model->get($ds->code);

      if( ! empty($doc))
      {
        $arr = array(
          'customer_name' => $ds->customer_name,
          'customer_phone' => $ds->phone,
          'isCompany' => $ds->is_company,
          'tax_id' => $ds->tax_id,
          'branch_code' => $ds->branch_code,
          'branch_name' => $ds->branch_name,
          'address' => $ds->address,
          'sub_district' => $ds->sub_district,
          'district' => $ds->district,
          'province' => $ds->province,
          'postcode' => $ds->postcode
        );

        if( ! $this->order_down_payment_model->update($doc->id, $arr))
        {
          $sc = FALSE;
          $this->error = "แก้ไขข้อมูลสำหร้บเปิดใบกำกับภาษีไม่สำเร็จ กรุณาลองใหม่อีกครั้ง";
        }
      }
      else
      {
        $sc = FALSE;
        $this->error = "Document not found!";
      }
    }
    else
    {
      $sc = FALSE;
      set_error('required');
    }

    $this->_response($sc);
  }


  public function create_invoice()
  {
    $sc = TRUE;

    $ex = 1; //-- if export failed change to 0;

    $dpCode = $this->input->post('code');

    if( ! empty($dpCode))
    {
      $doc = $this->order_down_payment_model->get($dpCode);

      if( ! empty($doc))
      {
        $pre = getConfig('PREFIX_DOWN_PAYMENT_INVOICE');
        $run_digit = getConfig('RUN_DIGIT_DOWN_PAYMENT_INVOICE');

        if(empty($pre))
        {
          $sc = FALSE;
          $this->error = "Document prefix is not defined";
        }

        $dummy_item = getConfig('DUMMY_ITEM');

        if(empty($dummy_item))
        {
          $sc = FALSE;
          $this->error = "DUMMY_ITEM ยังไม่ถูกตั้งค่า กรุณาติดต่อผู้ดูแลระบบ";
        }

        if($sc === TRUE)
        {
          $doc->date_add = db_date($doc->date_add, FALSE);
          $code = $this->get_new_down_payment_invoice_code($pre, $run_digit, $doc->date_add);
          $vat_rate = getConfig('SALE_VAT_RATE');
          $vat_code = getConfig('SALE_VAT_CODE');
          $currency = getConfig('CURRENCY');
          $whs_code = getConfig('DEFAULT_WAREHOUSE');
          $VatSum = get_vat_amount($doc->amount, $vat_rate);

          $arr = array(
            'code' => $code,
            'tax_id' => $doc->tax_id,
            'DocDate' => $doc->date_add,
            'DocDueDate' => $doc->date_add,
            'TaxDate' => $doc->date_add,
            'CardCode' => $doc->customer_code,
            'CardName' => $doc->customer_name,
            'isCompany' => $doc->isCompany,
            'branch_code' => $doc->branch_code,
            'branch_name' => $doc->branch_name,
            'NumAtCard' => $doc->customer_ref,
            'address' => $doc->address,
            'sub_district' => $doc->sub_district,
            'district' => $doc->district,
            'province' => $doc->province,
            'postcode' => $doc->postcode,
            'phone' => $doc->customer_phone,
            'DiscPrcnt' => 0,
            'DiscSum' => 0,
            'VatSum' => $VatSum,
            'DocTotal' => $doc->amount,
            'BaseDpm' => $doc->code,
            'BaseType' => $doc->ref_type,
            'BaseRef' => $doc->reference,
            'Comments' => "Base On {$doc->code}/{$doc->reference}",
            'SlpCode' => $doc->sale_id,
            'shipped_date' => now(),
            'user' => $this->_user->uname,
            'payment_role' => $doc->payment_role
          );

          $this->db->trans_begin();

          $id = $this->down_payment_invoice_model->add($arr);

          if($id)
          {
            $description = "รับเงินมัดจำค่าสินค้า ใบสั่งขายเลขที่ {$doc->reference}";
            $PriceBefDi = remove_vat($doc->amount, $vat_rate);
            $PriceAfVAT = $doc->amount;

            $arr = array(
              'invoice_id' => $id,
              'invoice_code' => $code,
              'LineNum' => 0,
              'BaseDpm' => $doc->code,
              'BaseType' => $doc->ref_type,
              'BaseRef' => $doc->reference,
              'ItemCode' => $dummy_item,
              'Dscription' => $description,
              'Qty' => 1,
              'Price' => $PriceBefDi,
              'Currency' => $currency,
              'Rate' => 1,
              'DiscPrcnt' => 0,
              'PriceBefDi' => $PriceBefDi,
              'LineTotal' => $PriceBefDi,
              'VatCode' => $vat_code,
              'VatRate' => $vat_rate,
              'PriceAfVAT' => $PriceAfVAT,
              'VatSum' => $VatSum,
              'unitMsr' => 'PCS',
              'SlpCode' => $doc->sale_id,
              'WhsCode' => $whs_code
            );

            if( ! $this->down_payment_invoice_model->add_detail($arr))
            {
              $sc = FALSE;
              $this->error = "Failed to insert transection";
            }

            //---- update order_down_payment
            if($sc === TRUE)
            {
              $arr = array(
                'TaxStatus' => 'Y',
                'invoice_code' => $code,
                'vat_rate' => $vat_rate,
                'VatSum' => $VatSum
              );

              if( ! $this->order_down_payment_model->update($doc->id, $arr))
              {
                $sc = FALSE;
                $this->error = "Failed to link downpayment invoice";
              }
            }
          }
          else
          {
            $sc = FALSE;
            $this->error = "Failed to create invoice";
          }

          if($sc === TRUE)
          {
            $this->db->trans_commit();
          }
          else
          {
            $this->db->trans_rollback();
          }

          //--- export down payment to ODPI, DPI1
          if($sc === TRUE)
          {
            $this->load->library('export');

            if( ! $this->export->export_down_payment($code))
            {
              $ex = 0;
              $this->error = "สร้างใบกำกับสำเร็จแต่ส่งเข้า SAP ไม่สำเร็จ";
            }
          }
        }
      }
      else
      {
        $sc = FALSE;
        $this->error = "Document not found!";
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
      'code' => $sc === TRUE ? $code : NULL
    );

    echo json_encode($arr);
  }


  public function print_receipt($code, $option = '')
  {
    $this->load->library('iprinter');
    $this->load->helper('print');
    $this->load->helper('saleman');

    $title = array(
      'RE' => 'ใบรับเงินมัดจำ/ใบเสร็จรับเงิน',
      'RN' => 'รับเงินมัดจำ/ใบเสร็จรับเงิน', //-- no date
    );

    $doc = $this->order_down_payment_model->get($code);

    if( ! empty($doc))
    {
      $details = array();
      $row = new stdClass();
      $row->Dscription = "รับเงินมัดจำค่าสินค้า ใบสั่งขายเลขที่ {$doc->reference}";
      $row->LineTotal = $doc->amount;
      $details[] = $row;
      $doc->DocTotal = $doc->amount;

      $arr = array(
        'doc' => $doc,
        'details' => $details,
        'title' => 'ใบรับเงินมัดจำ'
      );

      $this->load->view('print/print_down_payment_receipt', $arr);
    }
    else
    {
      $this->page_error();
    }
  }


  public function export_to_sap()
  {
    $sc = TRUE;

    $list = json_decode($this->input->post('list'));

    if( ! empty($list))
    {
      $this->load->library('export');

      foreach($list as $code)
      {
        $this->export->export_incomming($code);
      }
    }
    else
    {
      $sc = FALSE;
      set_error('required');
    }

    $this->_response($sc);
  }


  public function export_incomming()
  {
    $sc = TRUE;
    $code = $this->input->post('code');

    $doc = $this->order_down_payment_model->get($code);

    if( ! empty($doc))
    {
      if($doc->status != 'D')
      {
        $sap = $this->order_down_payment_model->get_sap_doc_num($code);

        //--- check exists on sap
        if(empty($sap))
        {
          $this->load->library('export');

          if($sc === TRUE)
          {
            $payments = $this->order_pos_payment_model->get_payments($code);

            if( ! empty($payments))
            {
              if( ! $this->export->export_incomming($code, 'DP'))
              {
                $sc = FALSE;
                $this->error = $this->export->error;
              }
            }
            else
            {
              $sc = FALSE;
              $this->error = "ไม่พบรายการชำระเงิน";
            }
          }
        }
        else
        {
          $sc = FALSE;
          $this->error = "เอกสารเข้าระบบ SAP แล้ว หากต้องการแก้ไขกรุณายกเลิกเอกสารบน SAP ก่อน";
        }
      }
      else
      {
        $sc = FALSE;
        $this->error = "This document already cancelled";
      }
    }
    else
    {
      $sc = FALSE;
      $this->error = "Document not found";
    }

    $this->_response($sc);
  }


  public function export_down_payment_invoice()
  {
    $sc = TRUE;
    $code = $this->input->post('code');

    $doc = $this->down_payment_invoice_model->get($code);

    if( ! empty($doc))
    {
      if($doc->status != 'D')
      {
        $sap = $this->down_payment_invoice_model->get_sap_doc_num($code);

        //--- check exists on sap
        if(empty($sap))
        {
          $this->load->library('export');

          if( ! $this->export->export_down_payment($code))
          {
            $sc = FALSE;
            $this->error = $this->export->error;
          }
        }
        else
        {
          $sc = FALSE;
          $this->error = "เอกสารเข้าระบบ SAP แล้ว หากต้องการแก้ไขกรุณายกเลิกเอกสารบน SAP ก่อน";
        }
      }
      else
      {
        $sc = FALSE;
        $this->error = "This document already cancelled";
      }
    }
    else
    {
      $sc = FALSE;
      $this->error = "Document not found";
    }

    $this->_response($sc);
  }


  public function view_detail($code)
  {
    $this->load->model('masters/bank_model');

    $order = $this->order_down_payment_model->get($code);

    if( ! empty($order))
    {
      $pos = empty($order->pos_id) ? NULL : $this->pos_model->get_pos($order->pos_id);

      $payments = $this->order_pos_payment_model->get_payments($code);
      $image_path = get_image_url($order->reference, $this->img_folder);

      $ds = array(
        'pos' => $pos,
        'doc' => $order,
        'details' => $this->order_down_payment_model->get_details($code),
        'payments' => $payments,
        'image' => empty($image_path) ? get_image_path($code, $this->img_folder) : $image_path,
        'no_image_path' => no_image_path()
      );

      $this->load->view('order_down_payment/order_down_payment_view_detail', $ds);
    }
    else
    {
      $this->page_error();
    }
  }


  public function save_image()
  {
    $sc = TRUE;
    $code = $this->input->post('code');
    $img = $this->input->post('imageData'); //---- base64_data

    $doc = $this->order_down_payment_model->get($code);

    if( ! empty($doc))
    {
      $path = $this->config->item('image_path').$this->img_folder.'/'.$code.'.jpg';

      if(createImage($img, $path) === FALSE)
      {
        $sc = FALSE;
        $this->error = "Create Image Failed";
      }
      else
      {
        $arr = array('image_path' => $path);

        $this->order_down_payment_model->update($doc->id, $arr);
      }
    }
    else
    {
      $sc = FALSE;
      $this->error = "Invalid document code";
    }

    echo $sc === TRUE ? 'success' : $this->error;
  }


  public function delete_image()
  {
    $sc = TRUE;
    $code = $this->input->post('code');

    $doc = $this->order_down_payment_model->get($code);

    $folder = $this->img_folder;

    if( ! delete_image($code, $folder))
    {
      $sc = FALSE;
      $this->error = "Delete image failed";
    }
    else
    {
      $arr = array('image_path' => NULL);

      $this->order_down_payment_model->update($doc->id, $arr);
    }

    echo $sc === TRUE ? 'success' : $this->error;
  }


  public function export_filter()
  {
    ini_set('memory_limit','512M'); // This also needs to be increased in some cases. Can be changed to a higher value as per need)
    ini_set('max_execution_time', 600);

    //--- load excel library
    $this->load->library('excel');

    $this->excel->setActiveSheetIndex(0);
    $this->excel->getActiveSheet()->setTitle('incomming');
    $header = array(
      'A' => 'DocNum',
      'B' => 'DocDate',
      'C' => 'CardCode',
      'D' => 'CashAcctCode',
      'E' => 'CashSum',
      'F' => 'TransferAccount',
      'G' => 'TransferSum',
      'H' => 'TransferDate',
      'I' => 'TransferReference',
      'J' => 'CreditCardCode',
      'K' => 'CreditCardSum',
      'L' => 'Reference1',
      'M' => 'Reference2',
      'N' => 'CounterReference',
      'O' => 'Remarks',
      'P' => 'JournalRemarks',
      'Q' => 'BankCode',
      'R' => 'BankAccount',
      'S' => 'DiscountPercent',
      'T' => 'ProjectCode',
      'U' => 'CurrencyIsLocal',
      'V' => 'DeductionPercent',
      'W' => 'DeductionSum',
      'X' => 'BoeAccount',
      'Y' => 'BillOfExchangeAmount',
      'Z' => 'BillofExchangeStatus',
      'AA' => 'BillOfExchangeAgent',
      'AB' => 'WTCode',
      'AC' => 'WTAmount',
      'AD' => 'Proforma',
      'AE' => 'PayToBankCode',
      'AF' => 'PayToBankBranch',
      'AG' => 'PayToBankAccountNo',
      'AH' => 'PayToCode',
      'AI' => 'PayToBankCountry',
      'AJ' => 'IsPayToBank',
      'AK' => 'PaymentPriority',
      'AL' => 'TaxGroup',
      'AM' => 'BankChargeAmount',
      'AN' => 'BankChargeAmountInSC',
      'AO' => 'WtBaseSum',
      'AP' => 'VatDate',
      'AQ' => 'TransactionCode',
      'AR' => 'PaymentType',
      'AS' => 'TransferRealAmount',
      'AT' => 'DocObjectCode',
      'AU' => 'DocTypte',
      'AV' => 'DueDate',
      'AW' => 'LocationCode',
      'AX' => 'ControlAccount'
    );

    $header2 = array(
      'A' => 'DocNum',
      'B' => 'DocDate',
      'C' => 'CardCode',
      'D' => 'CashAcct',
      'E' => 'CashSum',
      'F' => 'TrsfrAcct',
      'G' => 'TrsfrSum',
      'H' => 'TrsfrDate',
      'I' => 'TrsfrRef',
      'J' => 'CrCardAcct',
      'K' => 'CrCardSum',
      'L' => 'Ref1',
      'M' => 'Ref2',
      'N' => 'CounterRef',
      'O' => 'Comments',
      'P' => 'JrnlMemo',
      'Q' => 'BankCode',
      'R' => 'BankAcct',
      'S' => 'Dcount',
      'T' => 'PrjCode',
      'U' => 'DiffCurr',
      'V' => 'DdctPrcnt',
      'W' => 'DdctSum',
      'X' => 'BoeAcc',
      'Y' => 'BoeSum',
      'Z' => 'BoeStatus',
      'AA' => 'BoeAgent',
      'AB' => 'WtCode',
      'AC' => 'WtSum',
      'AD' => 'Proforma',
      'AE' => 'PBnkCode',
      'AF' => 'PBnkBranch',
      'AG' => 'PBnkAccnt',
      'AH' => 'PayToCode',
      'AI' => 'PBnkCnt',
      'AJ' => 'IsPaytoBnk',
      'AK' => 'PaPriority',
      'AL' => 'VatGroup',
      'AM' => 'BcgSum',
      'AN' => 'BcgSumSy',
      'AO' => 'WtBaseSum',
      'AP' => 'VatDate',
      'AQ' => 'TransCode',
      'AR' => 'PaymType',
      'AS' => 'TfrRealAmt',
      'AT' => 'ObjType',
      'AU' => 'DocType',
      'AV' => 'DocDueDate',
      'AW' => 'LocCode',
      'AX' => 'BpAct'
    );

    $row = 1;

    foreach($header as $key => $val)
    {
      $cell = "{$key}{$row}";
      $this->excel->getActiveSheet()->setCellValue($cell, $val);
    }

    $row++;

    foreach($header2 as $key => $val)
    {
      $cell = "{$key}{$row}";
      $this->excel->getActiveSheet()->setCellValue($cell, $val);
    }

    $row++;

    $filter = array(
      'shop_id' => $this->input->post('exShopId'),
      'pos_id' => $this->input->post('exPosId'),
      'code' => $this->input->post('exCode'),
      'order_code' => $this->input->post('exOrderCode'),
      'bill_code' => $this->input->post('exBillCode'),
      'payment' => $this->input->post('exPayment'),
      'from_date' => $this->input->post('exFromDate'),
      'to_date' => $this->input->post('exToDate')
    );

    $token = $this->input->post('token');

    $data = $this->order_down_payment_model->get_export_data($filter);

    if( ! empty($data))
    {
      $no = 1;
      $cashAcctCode = getConfig('SAP_CASH_ACCT_CODE');
      $cardAcctCode = getConfig('SAP_CREDIT_CARD_ACCT_CODE');

      foreach($data as $rs)
      {
        $this->excel->getActiveSheet()->setCellValue("A{$row}", $no);
        $this->excel->getActiveSheet()->setCellValue("B{$row}", date('Ymd', strtotime($rs->date_add)));
        $this->excel->getActiveSheet()->setCellValue("C{$row}", $rs->customer_code);

        if($rs->payment_role == 1)
        {
          $this->excel->getActiveSheet()->setCellValue("D{$row}", $cashAcctCode);
          $this->excel->getActiveSheet()->setCellValue("E{$row}", $rs->amount);
        }

        if($rs->payment_role == 2)
        {
          $this->excel->getActiveSheet()->setCellValue("F{$row}", $rs->sapAcctCode);
          $this->excel->getActiveSheet()->setCellValue("G{$row}", $rs->amount);
          $this->excel->getActiveSheet()->setCellValue("H{$row}", date('Ymd', strtotime($rs->date_add)));
        }

        if($rs->payment_role == 3)
        {
          $this->excel->getActiveSheet()->setCellValue("J{$row}", $cardAcctCode);
          $this->excel->getActiveSheet()->setCellValue("K{$row}", $rs->amount);
        }

        if($rs->payment_role == 6)
        {
          $cash = $this->order_down_payment_model->get_payments_logs_by_role($rs->code, 1);
          $tran = $this->order_down_payment_model->get_payments_logs_by_role($rs->code, 2);
          $card = $this->order_down_payment_model->get_payments_logs_by_role($rs->code, 3);

          if( ! empty($cash) && $cash->amount > 0)
          {
            $this->excel->getActiveSheet()->setCellValue("D{$row}", $cashAcctCode);
            $this->excel->getActiveSheet()->setCellValue("E{$row}", $rs->amount);
          }

          if( ! empty($tran) && $tran->amount > 0)
          {
            $this->excel->getActiveSheet()->setCellValue("F{$row}", $rs->sapAcctCode);
            $this->excel->getActiveSheet()->setCellValue("G{$row}", $rs->amount);
            $this->excel->getActiveSheet()->setCellValue("H{$row}", date('Ymd', strtotime($rs->date_add)));
          }

          if( ! empty($card) && $card->amount > 0)
          {
            $this->excel->getActiveSheet()->setCellValue("J{$row}", $cardAcctCode);
            $this->excel->getActiveSheet()->setCellValue("K{$row}", $rs->amount);
          }
        }

        $this->excel->getActiveSheet()->setCellValue("L{$row}", $rs->code);
        $this->excel->getActiveSheet()->setCellValue("M{$row}", $rs->reference);
        $this->excel->getActiveSheet()->setCellValue("O{$row}", $rs->customer_ref);
        $this->excel->getActiveSheet()->setCellValue("P{$row}", $rs->code." : ".$rs->reference." : ".$rs->customer_ref);

        $row++;
        $no++;
      }
    }

    setToken($token);
    $file_name = "Incomming.xlsx";
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'); /// form excel 2007 XLSX
    header('Content-Disposition: attachment;filename="'.$file_name.'"');
    $writer = PHPExcel_IOFactory::createWriter($this->excel, 'Excel2007');
    $writer->save('php://output');

  }


  public function print_down_payment($code)
  {
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


  public function cancel_payment()
  {
    $this->load->model('orders/order_pos_payment_model');

    $sc = TRUE;
    $id = $this->input->post('id');
    $code = $this->input->post('code');
    $reason = $this->input->post('reason');
    $pos_id = $this->input->post('pos_id');

    $doc = $this->order_down_payment_model->get($code);

    if( ! empty($doc))
    {
      if($doc->status == 'O')
      {
        if( ! empty($doc->invoice_code))
        {
          $sc = FALSE;
          $this->error = "เอกสารถูกดึงไปเปิดใบกำกับภาษีเลขที่ {$doc->invoice_code} หากต้องการแก้ไขกรุณายกเลิกใบกำกับภาษีก่อน";
        }

        if($sc === TRUE)
        {
          $sap = $this->order_down_payment_model->get_sap_doc_num($doc->code);

          if( ! empty($sap))
          {
            $sc = FALSE;
            $this->error = "เอกสารเข้า SAP แล้วกรุณายกเลิกเอกสารในระบบ SAP ก่อนยกเลิก";
          }

          if($sc === TRUE)
          {
            if( ! $this->order_down_payment_model->drop_middle_exists_data($doc->code))
            {
              $sc = FALSE;
              $this->error = "ลบ Incomming ใน Temp ไม่สำเร็จ";
            }
          }
        }

        if($sc === TRUE)
        {
          $arr = array(
            'status' => 'D',
            'cancle_date' => now(),
            'cancle_user' => $this->_user->uname,
            'cancle_reason' => $reason
          );

          $this->db->trans_begin();

          $pos = empty($pos_id) ? NULL : $this->pos_model->get_pos($pos_id);

          if( ! $this->order_down_payment_model->update($doc->id, $arr))
          {
            $sc = FALSE;
            $this->error = "Failed to update document";
          }

          //--- update sales order
          if($sc === TRUE && ! empty($doc->reference))
          {
            $so = $doc->ref_type == 'WO' ? $this->orders_model->get($doc->reference) : $this->sales_order_model->get($doc->reference);

            if( ! empty($so))
            {
              //---- roll back paidAmount
              //---- recalculate total balance
              if($doc->ref_type == 'WO')
              {
                //--- กรณีเป็น ออเดอร์
                $paidAmount = $so->paidAmount - $doc->amount;
                $balance = $so->doc_total - $paidAmount;

                $arr = array(
                  'paidAmount' => $paidAmount,
                  'TotalBalance' => $balance
                );

                if( ! $this->orders_model->update($doc->reference, $arr))
                {
                  $sc = FALSE;
                  $this->error = "Failed to update order outstanding balance";
                }

                //--- remove dp_code in order payment
                $this->load->model('orders/order_payment_model');

                $op = $this->order_payment_model->get_by_dp_code($doc->code);

                if( ! empty($op))
                {
                  if( ! $this->order_payment_model->update($op->id, array('dp_code' => NULL)))
                  {
                    $sc = FALSE;
                    $this->error = "Failed to unlink with Order Payment";
                  }
                }
              }
              else
              {
                //-- กรณีเป็นใบสั่งขาย
                $paidAmount = $so->paidAmount - $doc->amount;
                $balance = $so->DocTotal - $paidAmount;

                $arr = array(
                  'paidAmount' => $paidAmount,
                  'TotalBalance' => $balance
                );

                if( ! $this->sales_order_model->update($doc->reference, $arr))
                {
                  $sc = FALSE;
                  $this->error = "Failed to update Sales order outstanding balance";
                }
              }
            }
          }

          //--- insert sales movement
          if($sc === TRUE && ! empty($pos))
          {
            $payments = $this->order_pos_payment_model->get_payments($doc->code);

            if( ! empty($payments))
            {
              foreach($payments as $pm)
              {
                $arr = array(
                  'code' => $doc->code,
                  'type' => 'DC',
                  'shop_id' => $pos->shop_id,
                  'pos_id' => $pos->id,
                  'amount' => $pm->amount * -1,
                  'payment_role' => $pm->payment_role,
                  'acc_id' => $pm->payment_role == 2 ? $pm->acc_id : NULL,
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
            else
            {
              $arr = array(
                'code' => $doc->code,
                'type' => 'DC',
                'shop_id' => $pos->shop_id,
                'pos_id' => $pos->id,
                'amount' => $doc->amount * -1,
                'payment_role' => $doc->payment_role,
                'acc_id' => $doc->payment_role == 2 ? $doc->account_id : NULL,
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

          //-- if paymet by cash , update cash amount in cash drawer
          if($sc === TRUE && ! empty($pos))
          {
            $pm = $this->order_pos_payment_model->get_cash_payment($doc->code);

            if( ! empty($pm))
            {
              $cash_amount = $pos->cash_amount + $pm->amount;

              $arr = array('cash_amount' => $cash_amount);

              if( ! $this->pos_model->update_by_id($pos->id, $arr))
              {
                $sc = FALSE;
                $this->error = "บันทึกยอดเงินเข้าลิ้นชักไม่สำเร็จ";
              }
            }
            else
            {
              if($doc->payment_role == 1)
              {
                $cash_amount = $pos->cash_amount + $doc->amount;

                $arr = array('cash_amount' => $cash_amount);

                if( ! $this->pos_model->update_by_id($pos->id, $arr))
                {
                  $sc = FALSE;
                  $this->error = "บันทึกยอดเงินเข้าลิ้นชักไม่สำเร็จ";
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
        } //--- $sc = TRUE
      }
      else
      {
        $sc = FALSE;
        $this->error = "Document already 'Closed' OR 'Canceled'";
      }
    }
    else
    {
      $sc = FALSE;
      $this->error = "Document not found";
    }

    $arr = array(
      'status' => $sc === TRUE ? 'success' : 'failed',
      'message' => $sc === TRUE ? 'success' : $this->error
    );

    echo json_encode($arr);
  }

  //
  // //---- For POS only
  // public function cancel_payment()
  // {
  //   $this->load->model('orders/order_pos_payment_model');
  //
  //   $sc = TRUE;
  //   $id = $this->input->post('id');
  //   $code = $this->input->post('code');
  //   $reason = $this->input->post('reason');
  //   $shop_id = $this->input->post('shop_id');
  //   $pos_id = $this->input->post('pos_id');
  //
  //   $doc = $this->order_down_payment_model->get($code);
  //
  //   if( ! empty($doc))
  //   {
  //     if($doc->status == 'O')
  //     {
  //       $sap_dp = $this->order_down_payment_model->get_sap_doc_num($doc->code);
  //       $sap_inc = $this->order_down_payment_model->get_sap_incomming_doc_num($doc->code);
  //
  //       if( ! empty($sap_dp) OR ! empty($sap_inc))
  //       {
  //         $sc = FALSE;
  //         $this->error = "เอกสารเข้า SAP แล้วกรุณายกเลิกเอกสารในระบบ SAP ก่อนยกเลิก";
  //       }
  //
  //       if($sc === TRUE)
  //       {
  //         if( ! $this->order_down_payment_model->drop_middle_exits_data($doc->code))
  //         {
  //           $sc = FALSE;
  //           $this->error = "ลบ Downpayment ใน Temp ไม่สำเร็จ";
  //         }
  //         else
  //         {
  //           $this->mc->trans_begin();
  //           if($this->order_down_payment_model->drop_middle_incomming_data($doc->code))
  //           {
  //             $this->mc->trans_commit();
  //           }
  //           else
  //           {
  //             $sc = FALSE;
  //             $this->error = "ลบ Incomming ใน Temp ไม่สำเร็จ";
  //             $this->mc->trans_rollback();
  //           }
  //         }
  //       }
  //
  //       if($sc === TRUE)
  //       {
  //         $arr = array(
  //           'status' => 'D',
  //           'cancle_date' => now(),
  //           'cancle_user' => $this->_user->uname,
  //           'cancle_reason' => $reason
  //         );
  //
  //         $this->db->trans_begin();
  //
  //         $pos = $this->pos_model->get_pos($pos_id);
  //
  //         if( ! $this->order_down_payment_model->update($doc->id, $arr))
  //         {
  //           $sc = FALSE;
  //           $this->error = "Failed to update document";
  //         }
  //
  //         //--- update sales order
  //         if($sc === TRUE && ! empty($doc->reference))
  //         {
  //           $so = $doc->ref_type == 'WO' ? $this->orders_model->get($doc->reference) : $this->sales_order_model->get($doc->reference);
  //
  //           if( ! empty($so))
  //           {
  //             //---- roll back paidAmount
  //             //---- recalculate total balance
  //             if($doc->ref_type == 'WO')
  //             {
  //               //--- กรณีเป็น ออเดอร์
  //               $paidAmount = $so->paidAmount - $doc->amount;
  //               $balance = $so->doc_total - $paidAmount;
  //
  //               $arr = array(
  //                 'paidAmount' => $paidAmount,
  //                 'TotalBalance' => $balance
  //               );
  //
  //               if( ! $this->orders_model->update($doc->reference, $arr))
  //               {
  //                 $sc = FALSE;
  //                 $this->error = "Failed to update order outstanding balance";
  //               }
  //             }
  //             else
  //             {
  //               //-- กรณีเป็นใบสั่งขาย
  //               $paidAmount = $so->paidAmount - $doc->amount;
  //               $balance = $so->DocTotal - $paidAmount;
  //
  //               $arr = array(
  //                 'paidAmount' => $paidAmount,
  //                 'TotalBalance' => $balance
  //               );
  //
  //               if( ! $this->sales_order_model->update($doc->reference, $arr))
  //               {
  //                 $sc = FALSE;
  //                 $this->error = "Failed to update Sales order outstanding balance";
  //               }
  //             }
  //           }
  //         }
  //
  //         //--- insert sales movement
  //         if($sc === TRUE)
  //         {
  //           $payments = $this->order_pos_payment_model->get_payments($doc->code);
  //
  //           if( ! empty($payments))
  //           {
  //             foreach($payments as $pm)
  //             {
  //               $arr = array(
  //                 'code' => $doc->code,
  //                 'type' => 'DC',
  //                 'shop_id' => $pos->shop_id,
  //                 'pos_id' => $pos->id,
  //                 'amount' => $pm->amount * -1,
  //                 'payment_role' => $pm->payment_role,
  //                 'acc_id' => $pm->payment_role == 2 ? $pm->acc_id : NULL,
  //                 'user' => $this->_user->uname,
  //                 'round_id' => $pos->round_id
  //               );
  //
  //               if( ! $this->pos_sales_movement_model->add($arr))
  //               {
  //                 $sc = FALSE;
  //                 $this->error = "Insert sales movement failed";
  //               }
  //             }
  //           }
  //           else
  //           {
  //             $arr = array(
  //               'code' => $doc->code,
  //               'type' => 'DC',
  //               'shop_id' => $pos->shop_id,
  //               'pos_id' => $pos->id,
  //               'amount' => $doc->amount * -1,
  //               'payment_role' => $doc->payment_role,
  //               'acc_id' => $doc->payment_role == 2 ? $doc->account_id : NULL,
  //               'user' => $this->_user->uname,
  //               'round_id' => $pos->round_id
  //             );
  //
  //             if( ! $this->pos_sales_movement_model->add($arr))
  //             {
  //               $sc = FALSE;
  //               $this->error = "Insert sales movement failed";
  //             }
  //           }
  //         }
  //
  //         //-- if paymet by cash , update cash amount in cash drawer
  //         if($sc === TRUE)
  //         {
  //           $pm = $this->order_pos_payment_model->get_cash_payment($doc->code);
  //
  //           if( ! empty($pm))
  //           {
  //             $cash_amount = $pos->cash_amount + $pm->amount;
  //
  //             $arr = array('cash_amount' => $cash_amount);
  //
  //             if( ! $this->pos_model->update_by_id($pos->id, $arr))
  //             {
  //               $sc = FALSE;
  //               $this->error = "บันทึกยอดเงินเข้าลิ้นชักไม่สำเร็จ";
  //             }
  //           }
  //           else
  //           {
  //             if($doc->payment_role == 1)
  //             {
  //               $cash_amount = $pos->cash_amount + $doc->amount;
  //
  //               $arr = array('cash_amount' => $cash_amount);
  //
  //               if( ! $this->pos_model->update_by_id($pos->id, $arr))
  //               {
  //                 $sc = FALSE;
  //                 $this->error = "บันทึกยอดเงินเข้าลิ้นชักไม่สำเร็จ";
  //               }
  //             }
  //           }
  //         }
  //
  //         if($sc === TRUE)
  //         {
  //           $this->db->trans_commit();
  //         }
  //         else
  //         {
  //           $this->db->trans_rollback();
  //         }
  //       } //--- $sc = TRUE
  //     }
  //     else
  //     {
  //       $sc = FALSE;
  //       $this->error = "Document already 'Closed' OR 'Canceled'";
  //     }
  //   }
  //   else
  //   {
  //     $sc = FALSE;
  //     $this->error = "Document not found";
  //   }
  //
  //   $arr = array(
  //     'status' => $sc === TRUE ? 'success' : 'failed',
  //     'message' => $sc === TRUE ? 'success' : $this->error
  //   );
  //
  //   echo json_encode($arr);
  // }


  public function get_new_code($prefix, $run_digit = 4, $date = NULL)
  {
    $date = empty($date) ? date('Y-m-d') : ($date < '2020-01-01' ? date('Y-m-d') : $date);
		$Y = date('y', strtotime($date));
    $M = date('m', strtotime($date));
    $pre = $prefix .'-'.$Y.$M;
    $code = $this->order_down_payment_model->get_max_code($pre);

    if( ! empty($code))
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

  public function get_new_down_payment_invoice_code($prefix, $run_digit = 4, $date = NULL)
  {
    $date = empty($date) ? date('Y-m-d') : ($date < '2020-01-01' ? date('Y-m-d') : $date);
		$Y = date('y', strtotime($date));
    $M = date('m', strtotime($date));
    $pre = $prefix .'-'.$Y.$M;
    $code = $this->down_payment_invoice_model->get_max_code($pre);

    if( ! empty($code))
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
      'dp_shop_id',
      'dp_pos_id',
      'dp_code',
      'dp_order_code',
      'dp_bill_code',
      'dp_payment',
      'dp_has_slip',
      'dp_status',
      'dp_is_exported',
      'dp_from_date',
      'dp_to_date'
    );

    return clear_filter($filter);
  }
}

 ?>
