<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Invoice extends PS_Controller
{
  public $menu_code = 'ICODIV';
	public $menu_group_code = 'IC';
  public $menu_sub_group_code = 'PICKPACK';
	public $title = 'รายการเปิดบิลแล้ว';
  public $filter;
  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'inventory/invoice';
    $this->load->model('inventory/invoice_model');
    $this->load->model('orders/orders_model');
    $this->load->model('masters/customers_model');
    $this->load->model('inventory/delivery_order_model');
    $this->load->helper('order');
  }


  public function index()
  {
    $this->load->helper('channels');
    $this->load->helper('warehouse');
    $filter = array(
      'code'          => get_filter('code', 'ic_code', ''),
      'customer'      => get_filter('customer', 'ic_customer', ''),
      'user'          => get_filter('user', 'ic_user', ''),
      'role'          => get_filter('role', 'ic_role', ''),
      'channels'      => get_filter('channels', 'ic_channels', ''),
      'from_date'     => get_filter('from_date', 'ic_from_date', ''),
      'to_date'       => get_filter('to_date', 'ic_to_date', ''),
      'order_by'      => get_filter('order_by', 'ic_order_by', ''),
      'sort_by'       => get_filter('sort_by', 'ic_sort_by', ''),
      'is_valid'      => get_filter('is_valid', 'ic_valid', 'all'),
      'warehouse'     => get_filter('warehouse', 'ic_warehouse', 'all'),
			'is_exported'   => get_filter('is_exported', 'ic_is_exported', 'all')
    );

		//--- แสดงผลกี่รายการต่อหน้า
		$perpage = get_rows();
		//--- หาก user กำหนดการแสดงผลมามากเกินไป จำกัดไว้แค่ 300
		if($perpage > 300)
		{
			$perpage = 20;
		}

		$segment  = 4; //-- url segment
		$rows     = $this->delivery_order_model->count_rows($filter, 8);
		//--- ส่งตัวแปรเข้าไป 4 ตัว base_url ,  total_row , perpage = 20, segment = 3
		$init	    = pagination_config($this->home.'/index/', $rows, $perpage, $segment);
		$orders   = $this->delivery_order_model->get_data($filter, $perpage, $this->uri->segment($segment), 8);

    $filter['orders'] = $orders;

		$this->pagination->initialize($init);
    $this->load->view('inventory/order_closed/closed_list', $filter);
  }


  public function create_invoice()
  {
    $this->load->model('orders/order_invoice_model');
    $this->load->model('orders/sales_order_model');
    $this->load->model('orders/order_down_payment_model');
    $this->load->model('address/address_model');
    $this->load->helper('discount');
    $this->load->helper('address');

    $sc = TRUE;

    $order_code = $this->input->post('code');
    $taxStatus = 'N';

    $order = $this->orders_model->get($order_code);

    if( ! empty($order))
    {
      if($order->state == 8)
      {
        if( empty($order->invoice_code))
        {
          $details = $this->invoice_model->get_details($order_code); //--- รายการที่มีการบันทึกขายไป

          if( ! empty($details))
          {

            $VatSum = 0.00;
            $DocTotal = 0.00;
            $DiscSum = 0.00;

            $doc_date = date('Y-m-d');
            $code = $this->get_new_invoice_code($doc_date);
            $so = empty($order->so_code) ? NULL : $this->sales_order_model->get($order->so_code);

            $addr = empty($order->id_address) ? NULL : $this->address_model->get_shipping_detail($order->id_address);

            $address = empty($addr) ? NULL : parse_address($addr);

            $arr = array(
              'code' => $code,
              'TaxStatus' => $taxStatus,
              'DocDate' => $doc_date,
              'DocDueDate' => $doc_date,
              'TaxDate' => $doc_date,
              'CardCode' => $order->customer_code,
              'CardName' => $order->customer_name,
              'NumAtCard' => $order->customer_ref,
              'Address' => $address,
              'DiscPrcnt' => empty($so) ? $order->bDiscText : $so->DiscPrcnt,
              'DiscSum' => 0,
              'VatSum' => 0,
              'DocTotal' => 0,
              'BaseType' => 'WO',
              'BaseRef' => $order->code,
              'so_code' => empty($so) ? NULL : $so->code,
              'order_code' => $order->code,
              'bill_code' => NULL,
              'Comments' => get_null($order->remark) .( ! empty($so) ? "BaseOn Sales Order {$so->code}" : ""),
              'SlpCode' => $order->sale_code,
              'shipped_date' => now(),
              'LicTradNum' => $order->tax_id,
              'user' => $this->_user->uname,
              'channels_code' => $order->channels_code,
              'is_term' => $order->is_term
            );

            $this->db->trans_begin();

            $id = $this->order_invoice_model->add($arr);

            if( $id )
            {
              $lineNum = 0;

              foreach($details as $rs)
              {
                $arr = array(
                  'invoice_id' => $id,
                  'invoice_code' => $code,
                  'LineNum' => $lineNum,
                  'BaseType' => 'WO',
                  'BaseRef' => $rs->reference,
                  'BaseLine' => $rs->order_detail_id,
                  'ItemCode' => $rs->product_code,
                  'Dscription' => $rs->product_name,
                  'Qty' => $rs->qty,
                  'Price' => remove_vat($rs->price, $rs->VatRate),
                  'DiscPrcnt' => discountAmountToPercent($rs->discount_amount, $rs->qty, $rs->price),
                  'PriceBefDi' => remove_vat($rs->sell, $rs->VatRate),
                  'LineTotal' => remove_vat($rs->total_amount, $rs->VatRate),
                  'VatCode' => $rs->VatCode,
                  'VatRate' => $rs->VarRate,
                  'PriceAfVAT' => $rs->price,
                  'VatSum' => $rs->VatSum,
                  'unitMsr' => $this->products_model->get_unit_code($rs->product_code),
                  'SlpCode' => $rs->sale_code,
                  'WhsCode' => $rs->warehouse_code,
                  'BinCode' => $rs->zone_code,
                  'DocDate' => $doc_date,
                  'shipped_date' => $doc_date,
                  'LineText' => $rs->line_text
                );


              }
            }
            else
            {
              $sc = FALSE;
              $this->error = "Failed to create Invoice";
            }
          }
          else
          {
            $sc = FALSE;
            $this->error = "ไม่พบรายการบันทึกขาย";
          }
        }
        else
        {
          $sc = FALSE;
          $this->error = "เอกสารใบนี้เคยถูกเปิด Invoice ไปแล้ว";
        }
      }
      else
      {
        $sc = FALSE;
        $this->error = "Invalid document state";
      }
    }
    else
    {
      $sc = FALSE;
      $this->error = "Invalid document number";
    }
  }


  public function view_detail($code)
  {
    $this->load->model('masters/customers_model');
    $this->load->model('inventory/qc_model');
		$this->load->model('masters/warehouse_model');
		$this->load->model('masters/channels_model');
		$this->load->model('masters/payment_methods_model');
    $this->load->helper('order');
    $this->load->helper('discount');
    $this->load->helper('saleman');
    $this->load->helper('channels');
    $this->load->helper('warehouse');
  
    $useQc = getConfig('USE_QC') == 1 ? TRUE : FALSE;

    $order = $this->orders_model->get($code);

    $order->customer_name = $this->customers_model->get_name($order->customer_code);

    if($order->role == 'C' OR $order->role == 'N')
    {
      $this->load->model('masters/zone_model');

      $order->zone_name = $this->zone_model->get_name($order->zone_code);

      if($order->role == 'N')
      {
        $order->is_received = $this->invoice_model->is_received($order->code);
      }
    }

    $details = $this->invoice_model->get_billed_detail($code);
    $box_list = $useQc ? $this->qc_model->get_box_list($code) : NULL;

    $ds['order'] = $order;
    $ds['details'] = $details;
    $ds['box_list'] = $box_list;
    $ds['useQc'] = $useQc;
    $this->load->view('inventory/order_closed/closed_detail', $ds);
  }




  public function print_order($code, $barcode = '')
  {
    $this->load->model('masters/products_model');
    $this->load->library('printer');
    $this->load->helper('print');

    $order = $this->orders_model->get($code);
    $order->customer_name = $this->customers_model->get_name($order->customer_code);
    $details = $this->invoice_model->get_details($code); //--- รายการที่มีการบันทึกขายไป

    if(!empty($details))
    {
      foreach($details as $rs)
      {
        $rs->barcode = $this->products_model->get_barcode($rs->product_code);
      }
    }

    $doc = doc_type($order->role);

    $ds['order'] = $order;
    $ds['details'] = $details;
    $ds['title'] = $doc['title'];
    $ds['is_barcode'] = $barcode != '' ? TRUE : FALSE;
    $this->load->view('print/print_invoice', $ds);
  }


  public function print_delivery($code)
  {
    $this->load->model('masters/products_model');
    $this->load->model('masters/slp_model');
    $this->load->model('address/address_model');

    $doc = $this->orders_model->get($code);

    if( ! empty($doc))
    {
      $doc->customer_name = $this->customers_model->get_name($doc->customer_code);
      $details = $this->invoice_model->get_details($code); //--- รายการที่มีการบันทึกขายไป
      $addr = $this->address_model->get_shipping_detail($doc->id_address);

      $this->load->library('xprinter');
      $this->load->helper('print');
      $this->load->helper('discount');

      $doc->total_rows = 0;
      $row_text = 45;
      if( ! empty($details))
      {
        foreach($details as $rs)
        {
          $rs->use_rows = 1;
          $line_text = empty($rs->order_detail_id) ? NULL : $this->orders_model->get_line_text($rs->order_detail_id);

          if( ! empty($line_text))
  				{
  					$lines = 1 + substr_count( $line_text, "\n" );
  					$rs->product_name .= empty($rs->product_name) ? nl2br($line_text) : "<br>".nl2br($line_text);
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
        'title' => "ใบส่งของ",
        'order' => $doc,
        'details' => $details,
        'addr' => $addr,
        'sale' => $this->slp_model->get($doc->sale_code)
      );

      $this->load->view('print/print_delivery_order', $ds);
    }
    else
    {
      $this->error_page();
    }
  }


  public function get_new_invoice_code($date = NULL)
  {
    $date = empty($date) ? date('Y-m-d') : $date;

    $Y = date('y', strtotime($date));
    $M = date('m', strtotime($date));
    $prefix = getConfig('PREFIX_INVOICE');
    $run_digit = getConfig('RUN_DIGIT_INVOICE');
    $pre = $prefix .'-'.$Y.$M;
    $code = $this->order_invoice_model->get_max_code($pre);

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

  public function get_new_tax_invoice_code($date = NULL)
  {
    $date = empty($date) ? date('Y-m-d') : $date;

    $Y = date('y', strtotime($date));
    $M = date('m', strtotime($date));
    $prefix = getConfig('PREFIX_TAX_INVOICE');
    $run_digit = getConfig('RUN_DIGIT_TAX_INVOICE');
    $pre = $prefix .'-'.$Y.$M;
    $code = $this->order_invoice_model->get_max_code($pre);

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
      'ic_code',
      'ic_customer',
      'ic_user',
      'ic_role',
      'ic_channels',
      'ic_from_date',
      'ic_to_date',
      'ic_order_by',
      'ic_sort_by',
      'ic_valid',
      'ic_warehouse',
			'ic_is_exported'
    );
    clear_filter($filter);
  }


} //--- end class
?>
