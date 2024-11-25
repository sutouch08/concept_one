<?php
class Down_payment_invoice extends PS_Controller
{
  public $menu_code = 'SOARDP';
  public $menu_group_code = 'AC';
  public $menu_sub_group_code = '';
  public $title = 'ใบกำกับภาษีเงินมัดจำ';
  public $segment = 4;

  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'orders/down_payment_invoice';
    $this->load->model('orders/down_payment_invoice_model');
    $this->load->model('orders/orders_model');
    $this->load->model('orders/sales_order_model');
    $this->load->model('orders/order_down_payment_model');

    $this->load->helper('saleman');
  }

  public function index()
  {
    $filter = array(
      'code' => get_filter('code', 'dpm_code', ''),
      'baseDpm' => get_filter('baseDpm', 'baseDpm', ''),
      'baseRef' => get_filter('baseRef', 'baseRef', ''),
      'customer' => get_filter('customer', 'dpm_customer', ''),
      'sale_id' => get_filter('sale_id', 'dpm_sale_id', 'all'),
      'user' => get_filter('user', 'dpm_user', 'all'),
      'status' => get_filter('status', 'dpm_status', 'all'),
      'is_export' => get_filter('is_export', 'dpm_is_export', 'all'),
      'from_date' => get_filter('fromDate', 'dpm_from_date', ''),
      'to_date' => get_filter('toDate', 'dpm_to_date', '')
    );

    if($this->input->post('search'))
    {
      redirect($this->home);
    }
    else
    {
      $perpage = get_rows();
      $rows = $this->down_payment_invoice_model->count_rows($filter);
      $filter['orders'] = $this->down_payment_invoice_model->get_list($filter, $perpage, $this->uri->segment($this->segment));
      $init = pagination_config($this->home.'/index/', $rows, $perpage, $this->segment);
      $this->pagination->initialize($init);
      $this->load->view('down_payment_invoice/invoice_list', $filter);
    }
  }


  public function add_new()
  {
    $this->load->view('down_payment_invoice/invoice_add');
  }


  public function add()
  {
    $sc = TRUE;

    $ex = 1; //-- if export failed change to 0;

    $ds = json_decode($this->input->post('data'));

    if( ! empty($ds))
    {
      $prefix = getConfig('PREFIX_DOWN_PAYMENT_INVOICE');
      $run_digit = getConfig('RUN_DIGIT_DOWN_PAYMENT_INVOICE');
      $date_add = db_date($ds->date, FALSE);

      $code = $this->get_new_code($prefix, $run_digit, $date_add);

      if( ! empty($code))
      {
        $dummy_item = getConfig('DUMMY_ITEM');

        if(empty($dummy_item))
        {
          $sc = FALSE;
          $this->error = "DUMMY_ITEM ยังไม่ถูกตั้งค่า กรุณาติดต่อผู้ดูแลระบบ";
        }

        $dp = $this->order_down_payment_model->get($ds->dpCode); //--- dp

        if(empty($dp))
        {
          $sc = FALSE;
          $this->error = "ไม่พบใบรับเงินมัดจำ";
        }

        if($sc === TRUE)
        {
          $vat_rate = getConfig('SALE_VAT_RATE');
          $vat_code = getConfig('SALE_VAT_CODE');
          $currency = getConfig('CURRENCY');
          $whs_code = getConfig('DEFAULT_WAREHOUSE');
          $VatSum = $ds->VatSum;

          $arr = array(
            'code' => $code,
            'tax_id' => $ds->tax_id,
            'DocDate' => $date_add,
            'DocDueDate' => $date_add,
            'TaxDate' => $date_add,
            'CardCode' => $ds->customer_code,
            'CardName' => $ds->customer_name,
            'isCompany' => $ds->is_company,
            'branch_code' => $ds->branch_code,
            'branch_name' => $ds->branch_name,
            'NumAtCard' => $ds->customer_ref,
            'address' => $ds->address,
            'sub_district' => $ds->sub_district,
            'district' => $ds->district,
            'province' => $ds->province,
            'postcode' => $ds->postcode,
            'phone' => $ds->phone,
            'DiscPrcnt' => 0,
            'DiscSum' => 0,
            'VatSum' => $ds->VatSum,
            'DocTotal' => $ds->DocTotal,
            'BaseDpm' => $dp->code,
            'BaseType' => $dp->ref_type,
            'BaseRef' => $dp->reference,
            'Comments' => get_null($ds->remark),
            'SlpCode' => $ds->sale_id,
            'shipped_date' => now(),
            'user' => $this->_user->uname,
            'payment_role' => $dp->payment_role
          );

          $this->db->trans_begin();

          $id = $this->down_payment_invoice_model->add($arr);

          if($id)
          {
            $description = "รับเงินมัดจำค่าสินค้า ใบสั่งขายเลขที่ {$dp->reference}";
            $PriceBefDi = $ds->PriceBefDi;
            $PriceAfVAT = $ds->DocTotal;

            $arr = array(
              'invoice_id' => $id,
              'invoice_code' => $code,
              'LineNum' => 0,
              'BaseDpm' => $dp->code,
              'BaseType' => $dp->ref_type,
              'BaseRef' => $dp->reference,
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
              'SlpCode' => $ds->sale_id,
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

              if( ! $this->order_down_payment_model->update($dp->id, $arr))
              {
                $sc = FALSE;
                $this->error = "Failed to link with down payment";
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
        $this->error = "Failed to generate document number";
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

  public function view_detail($code)
  {
    $doc = $this->down_payment_invoice_model->get($code);

    if( ! empty($doc))
    {
      $details = $this->down_payment_invoice_model->get_details($code);

      $ds = array(
        'doc' => $doc,
        'details' => $details
      );

      $this->load->view('down_payment_invoice/invoice_detail', $ds);
    }
    else
    {
      $this->page_error();
    }
  }


  public function cancel()
  {
    $sc = TRUE;

    $id = $this->input->post('id');
    $code = $this->input->post('code');
    $reason = $this->input->post('reason');

    if( ! empty($code) && ! empty($code))
    {
      $doc = $this->down_payment_invoice_model->get($code);

      if( ! empty($doc))
      {
        $sap = $this->down_payment_invoice_model->get_sap_doc_num($code);

        if( ! empty($sap))
        {
          $sc = FALSE;
          $this->error = "เอกสารเข้าระบบ SAP แล้ว กรุณายกเลิกเอกสารในระบบ SAP ก่อน";
        }
        else
        {
          if( ! $this->down_payment_invoice_model->drop_middle_exits_data($code))
          {
            $sc = FALSE;
            $this->error = "Failed to delete exists temp data";
          }
        }

        if($sc === TRUE)
        {
          $this->db->trans_begin();

          $arr = array(
            'LineStatus' => 'D'
          );

          if($this->down_payment_invoice_model->update_details_by_id($doc->id, $arr))
          {
            $arr = array(
              'Status' => 'D',
              'cancel_reason' => $reason,
              'cancel_date' => now(),
              'cancel_user' => $this->_user->uname
            );

            if($this->down_payment_invoice_model->update_by_id($doc->id, $arr))
            {
              $arr = array(
                'invoice_code' => NULL
              );

              if( ! $this->order_down_payment_model->update_by_code($doc->BaseDpm, $arr))
              {
                $sc = FALSE;
                $this->error = "Failed to unlink with base down payment : {$doc->BaseDpm}";
              }
            }
            else
            {
              $sc = FALSE;
              $this->error = "ยกเลิกเอกสารไม่สำเร็จ";
            }
          }
          else
          {
            $sc = FALSE;
            $this->error = "ยกเลิกรายการไม่สำเร็จ";
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
      'message' => $sc === TRUE ? 'success' : $this->error
    );

    echo json_encode($arr);
  }


  public function send_to_sap($code)
  {
    $sc = TRUE;

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


  public function print_invoice($code, $option = '')
  {
    $this->load->library('iprinter');
    $this->load->helper('print');

    $title = array(
      'RTI' => 'ใบรับมัดจำ/ใบกำกับภาษี',
      'RTIN' => 'ใบรับมัดจำ/ใบกำกับภาษี', //-- no date
    );

    $doc = $this->down_payment_invoice_model->get($code);

    if( ! empty($doc))
    {
      $option = empty($option) ? 'RTI' : $option;

      $details = $this->down_payment_invoice_model->get_details($code);

      $arr = array(
        'doc' => $doc,
        'details' => $details,
        'title' => $title[$option]
      );

      switch($option)
      {
        case 'RTI' :
          $this->load->view('print/print_down_payment_receipt_invoice', $arr);
        break;
        case 'RTIN' :
          $this->load->view('print/print_down_payment_receipt_invoice_no_date', $arr);
        break;
      }
    }
    else
    {
      $this->page_error();
    }
  }


  public function get_new_code($prefix, $run_digit = 4, $date = NULL)
  {
    $date = empty($date) ? date('Y-m-d') : ($date < '2020-01-01' ? date('Y-m-d') : $date);
		$Y = date('y', strtotime($date));
    $M = date('m', strtotime($date));
    $pre = $prefix .'-'.$Y.$M;
    $code = $this->down_payment_invoice_model->get_max_code($pre);

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


  function clear_filter()
  {
    $filter = array(
      'dpm_code',
      'baseDpm',
      'baseRef',
      'dpm_customer',
      'dpm_status',
      'dpm_sale_id',
      'dpm_user',
      'dpm_is_export',
      'dpm_from_date',
      'dpm_to_date'
    );

    return clear_filter($filter);
  }
}
 ?>
