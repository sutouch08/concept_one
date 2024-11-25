<?php
class Order_invoice extends PS_Controller
{
  public $menu_code = 'SOARIV';
  public $menu_group_code = 'AC';
  public $menu_sub_group_code = '';
  public $title = 'AR/Invoice';
  public $segment = 4;

  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'orders/order_invoice';
    $this->load->model('orders/order_pos_model');
    $this->load->model('orders/order_invoice_model');
    $this->load->model('orders/orders_model');
    $this->load->model('orders/sales_order_model');
    $this->load->model('orders/order_down_payment_model');
    $this->load->model('orders/down_payment_invoice_model');
    $this->load->model('inventory/invoice_model');
    $this->load->model('masters/products_model');
    $this->load->model('masters/shop_model');
    $this->load->model('masters/pos_model');
    $this->load->model('masters/invoice_customer_model');
    $this->load->model('address/address_model');
    $this->load->helper('discount');
    $this->load->helper('shop');
    $this->load->helper('order_pos');
    $this->load->helper('warehouse');
    $this->load->helper('saleman');
    $this->load->helper('address');
    $this->load->helper('payment_method');
    $this->load->helper('invoice');
  }

  public function index()
  {
    $filter = array(
      'code' => get_filter('code', 'invoice_code', ''),
      'bookcode' => get_filter('bookcode', 'invoice_bookcode', 'all'),
      'reference' => get_filter('reference', 'invoice_reference', ''),
      'so_code' => get_filter('so_code', 'invoice_so_code', ''),
      'customer' => get_filter('customer', 'invoice_customer', ''),
      'status' => get_filter('status', 'invoice_status', 'all'),
      'tax_status' => get_filter('tax_status', 'tax_status', 'all'),
      'sale_id' => get_filter('sale_id', 'invoice_sale_id', 'all'),
      'user' => get_filter('user', 'invoice_user', 'all'),
      'is_export' => get_filter('is_export', 'invoice_is_export', 'all'),
      'from_date' => get_filter('fromDate', 'invoice_from_date', ''),
      'to_date' => get_filter('toDate', 'invoice_to_date', '')
    );

    if($this->input->post('search'))
    {
      redirect($this->home);
    }
    else
    {
      $perpage = get_rows();
      $rows = $this->order_invoice_model->count_rows($filter);
      $filter['orders'] = $this->order_invoice_model->get_list($filter, $perpage, $this->uri->segment($this->segment));
      $init = pagination_config($this->home.'/index/', $rows, $perpage, $this->segment);
      $this->pagination->initialize($init);
      $this->load->view('order_invoice/invoice_list', $filter);
    }
  }


  public function add_new()
  {
    $this->load->view('order_invoice/invoice_add');
  }


  public function get_invoice_customer_by_tax()
  {
    $sc = TRUE;
    $ds = array();

    $tax_id = trim($this->input->get('tax_id'));

    if( ! empty($tax_id))
    {
      $ds = $this->invoice_customer_model->get_by_tax_id($tax_id);

      if(empty($ds))
      {
        $sc = FALSE;
        $this->error = "ไม่พบข้อมูล";
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


  public function get_dp_code()
  {
    $code = $_REQUEST['term'];

    $ds = [];

    $this->db->select('code');

    if($code != '*')
    {
      $this->db->like('code', $code);
    }

    $rs = $this->db
    ->where('invoice_code IS NULL', NULL, FALSE)
    ->order_by('date_add', 'DESC')
    ->order_by('code', 'DESC')
    ->limit(50)
    ->get('order_down_payment');

    if($rs->num_rows() > 0)
    {
      foreach($rs->result() as $ro)
      {
        $ds[] = $ro->code;
      }
    }
    else
    {
      $ds[] = "ไม่พบรายการ";
    }

    echo json_encode($ds);
  }


  public function get_wo_code()
  {
    $code = $_REQUEST['term'];

    $ds = [];

    $this->db->select('code');

    if($code != '*')
    {
      $this->db->like('code', $code);
    }

    $rs = $this->db
    ->where('role', 'S')
    ->where('state', 8)
    ->where('isNew', 1)
    ->where('invoice_code IS NULL', NULL, FALSE)
    ->order_by('date_add', 'DESC')
    ->order_by('code', 'DESC')
    ->limit(50)
    ->get('orders');

    if($rs->num_rows() > 0)
    {
      foreach($rs->result() as $ro)
      {
        $ds[] = $ro->code;
      }
    }
    else
    {
      $ds[] = "ไม่พบรายการ";
    }

    echo json_encode($ds);
  }


  public function get_wu_code()
  {
    $code = $_REQUEST['term'];

    $ds = [];

    $this->db->select('code');

    if($code != '*')
    {
      $this->db->like('code', $code);
    }

    $rs = $this->db
    ->where('role', 'U')
    ->where('state', 8)
    ->where('isNew', 1)
    ->where('invoice_code IS NULL', NULL, FALSE)
    ->order_by('date_add', 'DESC')
    ->order_by('code', 'DESC')
    ->limit(50)
    ->get('orders');

    if($rs->num_rows() > 0)
    {
      foreach($rs->result() as $ro)
      {
        $ds[] = $ro->code;
      }
    }
    else
    {
      $ds[] = "ไม่พบรายการ";
    }

    echo json_encode($ds);
  }


  //--- get pos bill code
  public function get_bill_code()
  {
    $code = $_REQUEST['term'];

    $ds = [];

    $this->db->select('code');

    if($code != '*')
    {
      $this->db->like('code', $code);
    }

    $rs = $this->db
    ->where_in('status', ['O'])
    ->where('ref_code IS NULL', NULL, FALSE)
    ->order_by('date_add', 'DESC')
    ->order_by('code', 'DESC')
    ->limit(20)
    ->get('order_pos');

    if($rs->num_rows() > 0)
    {
      foreach($rs->result() as $ro)
      {
        $ds[] = $ro->code;
      }
    }

    echo json_encode($ds);
  }


  public function get_wo_details()
  {
    $this->load->model('inventory/delivery_order_model');
    $this->load->model('inventory/invoice_model');
    $this->load->model('orders/order_down_payment_model');

    $sc = TRUE;

    $totalQty = 0;
    $totalBfDisc = 0;
    $bDiscAmount = 0;
    $totalAfDisc = 0;
    $totalVat = 0;

    $code = $this->input->post('code');

    $order = $this->orders_model->get($code);
    $downPayment = [];
    $downPaymentAvailable = 0;
    $downPaymentUse = 0;

    if( ! empty($order))
    {
      if($order->isNew == 1)
      {
        if($order->state == 8)
        {
          if( empty($order->invoice_code))
          {
            $details = $this->invoice_model->get_details($code); //--- รายการที่มีการบันทึกขายไป

            if( ! empty($details))
            {
              $header = array(
                'code' => $order->code,
                'TaxStatus' => $order->TaxStatus,
                'DocDate' => $order->date_add,
                'CardCode' => $order->customer_code,
                'CardName' => $order->customer_name,
                'NumAtCard' => $order->customer_ref,
                'DiscPrcnt' => number($order->bDiscText, 2),
                'DiscSum' => 0,
                'VatSum' => 0,
                'DocTotal' => 0,
                'BaseType' => 'WO',
                'BaseRef' => $order->code,
                'so_code' => $order->so_code,
                'Comments' => get_null($order->remark) .( ! empty($order->so_code) ? "BaseOn Sales Order {$order->so_code} =>" : "")." ".("BaseOn Order {$order->code}"),
                'branch_code' => $order->branch_code,
                'branch_name' => $order->branch_name,
                'address' => $order->address,
                'sub_district' => $order->sub_district,
                'district' => $order->district,
                'province' => $order->province,
                'postcode' => $order->postcode,
                'phone' => $order->phone,
                'SlpCode' => $order->sale_code,
                'shipped_date' => now(),
                'LicTradNum' => $order->tax_id,
                'user' => $this->_user->uname,
                'channels_code' => $order->channels_code,
                'is_term' => $order->is_term,
                'vat_type' => $order->vat_type,
                'whtPrcnt' => number($order->WhtPrcnt, 2),
                'whtAmount' => number($order->WhtAmount, 2)
                );

                $rows = [];

                $no = 1;

                foreach($details as $rs)
                {
                  $arr = array(
                  'no' => $no,
                  'id' => $rs->id,
                  'reference' => $rs->reference,
                  'product_code' => $rs->product_code,
                  'product_name' => $rs->product_name,
                  'unitMsr' => $this->products_model->get_unit_code($rs->product_code),
                  'qty' => $rs->qty,
                  'qty_label' => number($rs->qty),
                  'price' => $rs->price,
                  'price_label' => number($rs->price, 2),
                  'vat_code' => $rs->VatCode,
                  'vat_rate' => $rs->VatRate,
                  'vat_type' => $rs->VatType,
                  'discount_label' => $rs->discount_label,
                  'line_total' => $rs->total_amount,
                  'line_total_label' => number($rs->total_amount, 2),
                  'order_detail_id' => $rs->order_detail_id,
                  'so_line_id' => $rs->so_line_id,
                  'DiscPrcnt' => discountAmountToPercent($rs->discount_amount, $rs->qty, $rs->price),
                  'PriceBefDi' => $rs->VatType == 'E' ? $rs->price : remove_vat($rs->price, $rs->VatRate),
                  'PriceBfVAT' => $rs->VatType == 'E' ? $rs->sell : remove_vat($rs->sell, $rs->VatRate), //-- ราคาขายหลังส่วนลดรายการ ก่อน VAT
                  'VatSum' => $rs->VatSum,
                  'avgBillDiscAmount' => $rs->avgBillDiscAmount,
                  'sumBillDiscAmount' => $rs->sumBillDiscAmount,
                  'LineTotal' => $rs->total_amount,
                  'zone_code' => $rs->zone_code,
                  'warehouse_code' => $rs->warehouse_code,
                  'line_text' => $rs->line_text,
                  'is_count' => $rs->is_count
                  );

                  array_push($rows, $arr);

                  $totalQty += $rs->qty;
                  $totalBfDisc += $rs->total_amount;
                  $bDiscAmount += $rs->sumBillDiscAmount;
                  $totalAfDisc += $rs->total_amount - $rs->sumBillDiscAmount;
                  $totalVat += $rs->VatSum;
                  $no++;
                }

                $DocTotal = $order->vat_type == 'E' ? $totalAfDisc + $totalVat : $totalAfDisc;

                $header['totalQty'] = number($totalQty);
                $header['TotalBfDisc'] = number($totalBfDisc, 2);
                $header['TotalAfDisc'] = $totalAfDisc;
                $header['DocTotal'] = number($DocTotal, 2);
                $header['DiscSum'] = number($bDiscAmount, 2);
                $header['VatSum'] = number($totalVat, 2);
              }
              else
              {
                $sc = FALSE;
                $this->error = "ไม่พบรายการบันทึกขาย";
              }

              if($sc === TRUE)
              {
                $reference = empty($order->so_code) ? $order->code : $order->so_code;

                $dps = $this->order_down_payment_model->get_by_reference($reference);

                if( ! empty($dps))
                {
                  $dp_no = 1;

                  $doc_total = $DocTotal;

                  foreach($dps as $dp)
                  {
                    $dp->no = $dp_no;
                    $dp->amount_label = number($dp->amount, 2);
                    $dp->used_amount = $dp->used;
                    $dp->used_label = number($dp->used, 2);
                    $dp->available_label = number($dp->available, 2);
                    $dp->payment_role_name = $dp->payment_role == 1 ? 'เงินสด' : ($dp->payment_role == 2 ? 'เงินโอน' : ($dp->payment_role == 3 ? 'บัตรเครดิต' : 'หลายช่องทาง'));

                    $use_amount = $dp->available > 0 ? round($dp->available <= $doc_total ? $dp->available : $doc_total, 2) : 0;
                    $dp->use_amount = $use_amount;
                    $dp->disabled = $dp->available > 0 ? '' : 'disabled';

                    array_push($downPayment, $dp);
                    $dp_no++;
                    $doc_total = $doc_total - $use_amount;
                    $downPaymentUse += $use_amount;
                    $downPaymentAvailable += $dp->available;
                  }
                }
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
          $this->error = "ออเดอร์อยู่ในระบบเก่าจะถูกส่งเข้า SAP เป็นเอกสาร Delivery ไม่สามารถสร้าง invoice บน web ได้";
        }
      }
      else
      {
        $sc = FALSE;
        $this->error = "Invalid document number";
      }

      $arr = array(
      'status' => $sc === TRUE ? 'success' : 'failed',
      'message' => $sc === TRUE ? 'success' : $this->error,
      'header' => $sc === TRUE ? $header : NULL,
      'details' => $sc === TRUE ? $rows : NULL,
      'down_payment_list' => $downPayment,
      'downPaymentAvailable' => round($downPaymentAvailable, 2),
      'downPaymentUse' => round($downPaymentUse, 2)
      );

      echo json_encode($arr);
    }


  public function get_wu_details()
  {
    $this->load->model('inventory/delivery_order_model');
    $this->load->model('inventory/invoice_model');
    $this->load->model('orders/order_down_payment_model');

    $sc = TRUE;

    $totalQty = 0;
    $totalBfDisc = 0;
    $bDiscAmount = 0;
    $totalAfDisc = 0;
    $totalVat = 0;

    $code = $this->input->post('code');

    $order = $this->orders_model->get($code);
    $downPayment = [];
    $downPaymentAvailable = 0;
    $downPaymentUse = 0;

    if( ! empty($order))
    {
      if($order->state == 8)
      {
        if( empty($order->invoice_code))
        {
          $details = $this->invoice_model->get_details($code); //--- รายการที่มีการบันทึกขายไป

          if( ! empty($details))
          {
            $header = array(
              'code' => $order->code,
              'TaxStatus' => $order->TaxStatus,
              'DocDate' => $order->date_add,
              'CardCode' => $order->customer_code,
              'CardName' => $order->customer_name,
              'NumAtCard' => $order->customer_ref,
              'DiscPrcnt' => number($order->bDiscText, 2),
              'DiscSum' => 0,
              'VatSum' => 0,
              'DocTotal' => 0,
              'BaseType' => 'WO',
              'BaseRef' => $order->code,
              'so_code' => $order->so_code,
              'Comments' => get_null($order->remark) .( ! empty($order->so_code) ? "BaseOn Sales Order {$order->so_code} =>" : "")." ".("BaseOn Order {$order->code}"),
              'branch_code' => $order->branch_code,
              'branch_name' => $order->branch_name,
              'address' => $order->address,
              'sub_district' => $order->sub_district,
              'district' => $order->district,
              'province' => $order->province,
              'postcode' => $order->postcode,
              'phone' => $order->phone,
              'SlpCode' => $order->sale_code,
              'shipped_date' => now(),
              'LicTradNum' => $order->tax_id,
              'user' => $this->_user->uname,
              'channels_code' => $order->channels_code,
              'is_term' => $order->is_term,
              'vat_type' => $order->TaxStatus == 'N' ? 'N' : $order->vat_type,
              'whtPrcnt' => number($order->WhtPrcnt, 2),
              'whtAmount' => number($order->WhtAmount, 2)
            );

            $rows = [];

            $no = 1;

            foreach($details as $rs)
            {
              $arr = array(
                'no' => $no,
                'id' => $rs->id,
                'reference' => $rs->reference,
                'product_code' => $rs->product_code,
                'product_name' => $rs->product_name,
                'unitMsr' => $this->products_model->get_unit_code($rs->product_code),
                'qty' => $rs->qty,
                'qty_label' => number($rs->qty),
                'price' => $rs->price,
                'price_label' => number($rs->price, 2),
                'vat_code' => $rs->VatCode,
                'vat_rate' => $rs->VatRate,
                'vat_type' => $rs->VatType,
                'discount_label' => $rs->discount_label,
                'line_total' => $rs->total_amount,
                'line_total_label' => number($rs->total_amount, 2),
                'order_detail_id' => $rs->order_detail_id,
                'so_line_id' => $rs->so_line_id,
                'DiscPrcnt' => discountAmountToPercent($rs->discount_amount, $rs->qty, $rs->price),
                'PriceBefDi' => $rs->VatType == 'E' ? $rs->price : remove_vat($rs->price, $rs->VatRate),
                'PriceBfVAT' => $rs->VatType == 'E' ? $rs->sell : remove_vat($rs->sell, $rs->VatRate), //-- ราคาขายหลังส่วนลดรายการ ก่อน VAT
                'VatSum' => $rs->VatSum,
                'avgBillDiscAmount' => $rs->avgBillDiscAmount,
                'sumBillDiscAmount' => $rs->sumBillDiscAmount,
                'LineTotal' => $rs->total_amount,
                'zone_code' => $rs->zone_code,
                'warehouse_code' => $rs->warehouse_code,
                'line_text' => $rs->line_text,
                'is_count' => $rs->is_count
              );

              array_push($rows, $arr);

              $totalQty += $rs->qty;
              $totalBfDisc += $rs->total_amount;
              $bDiscAmount += $rs->sumBillDiscAmount;
              $totalAfDisc += $rs->total_amount - $rs->sumBillDiscAmount;
              $totalVat += $rs->VatSum;
              $no++;
            }

            $DocTotal = $order->vat_type == 'E' ? $totalAfDisc + $totalVat : $totalAfDisc;

            $header['totalQty'] = number($totalQty);
            $header['TotalBfDisc'] = number($totalBfDisc, 2);
            $header['TotalAfDisc'] = $totalAfDisc;
            $header['DocTotal'] = number($DocTotal, 2);
            $header['DiscSum'] = number($bDiscAmount, 2);
            $header['VatSum'] = number($totalVat, 2);
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

    $arr = array(
      'status' => $sc === TRUE ? 'success' : 'failed',
      'message' => $sc === TRUE ? 'success' : $this->error,
      'header' => $sc === TRUE ? $header : NULL,
      'details' => $sc === TRUE ? $rows : NULL,
      'down_payment_list' => $downPayment,
      'downPaymentAvailable' => round($downPaymentAvailable, 2),
      'downPaymentUse' => round($downPaymentUse, 2)
    );

    echo json_encode($arr);
  }


  public function get_bill_details()
  {
    $this->load->model('orders/order_down_payment_model');

    $sc = TRUE;

    $totalQty = 0;
    $totalBfDisc = 0;
    $bDiscAmount = 0;
    $totalAfDisc = 0;
    $totalVat = 0;

    $code = $this->input->post('code');

    $order = $this->order_pos_model->get($code);
    $downPayment = [];
    $downPaymentUse = 0;

    if( ! empty($order))
    {
      if($order->status == 'O')
      {
        if( empty($order->invoice_code))
        {
          $details = $this->order_pos_model->get_details($code); //--- รายการที่มีการบันทึกขายไป

          if( ! empty($details))
          {
            $header = array(
              'code' => $order->code,
              'TaxStatus' => "",
              'DocDate' => $order->date_add,
              'CardCode' => $order->customer_code,
              'CardName' => $order->customer_name,
              'NumAtCard' => $order->customer_name,
              'TotalBfDisc' => $order->amount_bf_disc,
              'DiscPrcnt' => $order->discPrcnt,
              'DiscSum' => $order->disc_amount,
              'VatSum' => $order->vat_amount,
              'DocTotal' => number($order->amount, 2),
              'DownPaymentAmount' => $order->down_payment_amount,
              'PayAmount' => number($order->payAmount, 2),
              'BaseType' => 'POS',
              'BaseRef' => $order->code,
              'so_code' => $order->so_code,
              'Comments' => "BaseOn Bill No : {$order->code} ".( ! empty($order->so_code) ? ", BaseOn Sales Order {$order->so_code}" : ""),
              'branch_code' => $order->branch_code,
              'branch_name' => $order->branch_name,
              'address' => $order->address,
              'sub_district' => $order->sub_district,
              'district' => $order->district,
              'province' => $order->province,
              'postcode' => $order->postcode,
              'phone' => $order->phone,
              'SlpCode' => $order->sale_id,
              'shipped_date' => now(),
              'LicTradNum' => $order->tax_id,
              'user' => $this->_user->uname,
              'channels_code' => $order->channels_code,
              'is_term' => 0,
              'vat_type' => $order->vat_type,
              'whtPrcnt' => $order->WhtPrcnt,
              'whtAmount' => $order->WhtAmount
            );

            $rows = [];

            $no = 1;

            foreach($details as $rs)
            {
              $arr = array(
                'no' => $no,
                'id' => $rs->id,
                'reference' => $rs->order_code,
                'product_code' => $rs->product_code,
                'product_name' => $rs->product_name,
                'unitMsr' => $rs->unit_code,
                'qty' => $rs->qty,
                'qty_label' => number($rs->qty),
                'price' => $rs->price,
                'price_label' => number($rs->price, 2),
                'vat_code' => $rs->vat_code,
                'vat_rate' => $rs->vat_rate,
                'vat_type' => $rs->vat_type,
                'discount_label' => $rs->discount_label,
                'line_total' => $rs->total_amount,
                'line_total_label' => number($rs->total_amount, 2),
                'so_line_id' => $rs->line_id,
                'DiscPrcnt' => discountAmountToPercent($rs->discount_amount, $rs->qty, $rs->price),
                'PriceBefDi' => $rs->vat_type == 'E' ? $rs->price : remove_vat($rs->price, $rs->vat_rate),
                'PriceBfVAT' => $rs->vat_type == 'E' ? $rs->final_price : remove_vat($rs->final_price, $rs->vat_rate), //-- ราคาขายหลังส่วนลดรายการ ก่อน VAT
                'VatSum' => $rs->vat_amount,
                'avgBillDiscAmount' => $rs->avgBillDiscAmount,
                'sumBillDiscAmount' => $rs->sumBillDiscAmount,
                'LineTotal' => $rs->total_amount,
                'zone_code' => $order->zone_code,
                'warehouse_code' => $order->warehouse_code,
                'is_count' => $rs->is_count
              );

              array_push($rows, $arr);

              $totalQty += $rs->qty;
              $totalAfDisc += $rs->total_amount - $rs->sumBillDiscAmount;
              $no++;
            }


            $header['totalQty'] = number($totalQty);
            $header['totalAfDisc'] = $totalAfDisc;
          }
          else
          {
            $sc = FALSE;
            $this->error = "ไม่พบรายการบันทึกขาย";
          }


          if($sc === TRUE)
          {
            $dps = $this->order_down_payment_model->get_details_by_target($order->code);

            if( ! empty($dps))
            {
              $dp_no = 1;

              foreach($dps as $dp)
              {
                $dp->no = $dp_no;
                $dp->amount_label = number($dp->amount, 2);
                $dp->amountBfUse_label = number($dp->amountBfUse, 2);
                $dp->amountAfUse_label = number($dp->amountAfUse, 2);
                $dp->payment_role_name = $dp->payment_role == 1 ? 'เงินสด' : ($dp->payment_role == 2 ? 'เงินโอน' : ($dp->payment_role == 3 ? 'บัตรเครดิต' : 'หลายช่องทาง'));

                array_push($downPayment, $dp);
                $downPaymentUse += $dp->amount;
                $dp_no++;
              }
            }
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
        $this->error = "Invalid document status";
      }
    }
    else
    {
      $sc = FALSE;
      $this->error = "Invalid document number";
    }

    $arr = array(
      'status' => $sc === TRUE ? 'success' : 'failed',
      'message' => $sc === TRUE ? 'success' : $this->error,
      'header' => $sc === TRUE ? $header : NULL,
      'details' => $sc === TRUE ? $rows : NULL,
      'down_payment_list' => $downPayment
    );

    echo json_encode($arr);
  }


  public function get_down_payment_details()
  {
    $this->load->model('inventory/invoice_model');
    $this->load->model('orders/order_down_payment_model');

    $sc = TRUE;

    $totalQty = 0;
    $totalBfDisc = 0;
    $bDiscAmount = 0;
    $totalAfDisc = 0;
    $totalVat = 0;

    $code = $this->input->post('code');

    $order = $this->order_down_payment_model->get($code);
    $downPayment = [];
    $downPaymentAvailable = 0;
    $downPaymentUse = 0;

    $WhsCode = getConfig('SERVICE_WAREHOUSE');
    $currency = getConfig('CURRENCY');
    $vat_rate = getConfig('SALE_VAT_RATE');
    $vat_code = getConfig('SALE_VAT_CODE');
    $item_dummy = getConfig('DUMMY_ITEM');

    if( ! empty($order))
    {
      if($order->status == 'O')
      {
        if( empty($order->invoice_code))
        {
          if( ! empty($item_dummy))
          {
            $od = $order->ref_type == 'SO' ? $this->sales_order_model->get($order->reference) : $this->orders_model->get($order->reference);

            $header = array(
              'code' => $order->code,
              'TaxStatus' => 'Y',
              'DocDate' => $order->date_add,
              'CardCode' => $order->customer_code,
              'CardName' => $order->customer_name,
              'NumAtCard' => $order->customer_ref,
              'DiscPrcnt' => 0,
              'DiscSum' => 0,
              'VatSum' => 0,
              'DocTotal' => 0,
              'BaseType' => 'DP',
              'BaseRef' => $order->code,
              'so_code' => $order->ref_type == 'SO' ? $order->reference : NULL,
              'order_code' => $order->ref_type == 'WO' ? $order->reference : NULL,
              'Comments' => $order->ref_type == 'SO' ? "BaseOn Sales Order {$order->reference}" : "BaseOn Order {$order->reference}",
              'branch_code' => empty($od) ? NULL : $od->branch_code,
              'branch_name' => empty($od) ? NULL : $od->branch_name,
              'address' => empty($od) ? NULL : $od->address,
              'sub_district' => empty($od) ? NULL : $od->sub_district,
              'district' => empty($od) ? NULL : $od->district,
              'province' => empty($od) ? NULL : $od->province,
              'postcode' => empty($od) ? NULL : $od->postcode,
              'phone' => empty($od) ? NULL : $od->phone,
              'SlpCode' => $order->sale_id,
              'shipped_date' => now(),
              'LicTradNum' => empty($od) ? NULL : $od->tax_id,
              'user' => $this->_user->uname,
              'channels_code' => empty($od) ? NULL : $od->channels_code,
              'is_term' => 0,
              'vat_type' => 'I',
              'whtPrcnt' => empty($od) ? 0 : number($od->WhtPrcnt, 2),
              'whtAmount' => empty($od) ? 0 : number($od->WhtAmount, 2)
            );

            $rows = [];

            $no = 1;

            $description = "มัดจำค่าสินค้าจากเอกสารเลขที่ {$order->reference}";
            $PriceBefDi = remove_vat($order->amount, $vat_rate);
            $PriceAfVAT = $order->amount;
            $VatSum = get_vat_amount($order->amount, $vat_rate);

            $arr = array(
              'no' => $no,
              'id' => 1,
              'reference' => $order->code,
              'product_code' => $item_dummy,
              'product_name' => $description,
              'unitMsr' => 'PCS',
              'qty' => 1,
              'qty_label' => 1,
              'price' => $PriceAfVAT,
              'price_label' => number($PriceAfVAT, 2),
              'vat_code' => $vat_code,
              'vat_rate' => $vat_rate,
              'vat_type' => 'I',
              'discount_label' => 0,
              'line_total' => $PriceAfVAT,
              'line_total_label' => number($PriceAfVAT, 2),
              'DiscPrcnt' => 0,
              'PriceBefDi' => $PriceBefDi,
              'PriceBfVAT' => $PriceBefDi, //-- ราคาขายหลังส่วนลดรายการ ก่อน VAT
              'VatSum' => $VatSum,
              'avgBillDiscAmount' => 0,
              'sumBillDiscAmount' => 0,
              'LineTotal' => $order->amount,
              'zone_code' => NULL,
              'warehouse_code' => $WhsCode,
              'line_text' => NULL,
              'is_count' => 0
            );

            array_push($rows, $arr);

            $totalQty += 1;
            $totalBfDisc += $order->amount;
            $bDiscAmount += 0;
            $totalAfDisc += $order->amount;
            $totalVat += $VatSum;

            $DocTotal = $PriceAfVAT;

            $header['totalQty'] = number($totalQty);
            $header['TotalBfDisc'] = number($totalBfDisc, 2);
            $header['TotalAfDisc'] = $totalAfDisc;
            $header['DocTotal'] = number($DocTotal, 2);
            $header['DiscSum'] = number($bDiscAmount, 2);
            $header['VatSum'] = number($totalVat, 2);
          }
          else
          {
            $sc = FALSE;
            $this->error = "DUMMY_ITEM ยังไม่ถูกตั้งค่า กรุณาติดต่อผู้ดูแลระบบ";
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
        $this->error = "Invalid document status";
      }
    }
    else
    {
      $sc = FALSE;
      $this->error = "Invalid document number";
    }

    $arr = array(
      'status' => $sc === TRUE ? 'success' : 'failed',
      'message' => $sc === TRUE ? 'success' : $this->error,
      'header' => $sc === TRUE ? $header : NULL,
      'details' => $sc === TRUE ? $rows : NULL,
      'down_payment_list' => $downPayment,
      'downPaymentAvailable' => round($downPaymentAvailable, 2),
      'downPaymentUse' => round($downPaymentUse, 2)
    );

    echo json_encode($arr);
  }

  private function add_by_pos($ds)
  {
    $sc = TRUE;
    $this->load->model('inventory/movement_model');
    $order = $this->order_pos_model->get($ds->billCode);

    if( ! empty($order))
    {
      if($order->status == 'O')
      {
        if( empty($order->invoice_code))
        {
          $details = $this->order_pos_model->get_details($order->code); //--- รายการที่มีการบันทึกขายไป

          if( ! empty($details))
          {
            $VatSum = 0.00;
            $DocTotal = 0.00;
            $totalBefDisc = 0;
            $DiscSum = 0.00;

            $doc_date = date('Y-m-d');
            $type = empty($order->so_code) ? 'POS' : 'ORDER';
            $pre = $this->getInvoicePrefixData($type, is_true($ds->is_term), $ds->taxStatus);

            if( ! empty($pre))
            {
              $code = $this->get_new_code($pre->prefix, $pre->running, $doc_date);
              $bookcode = $pre->bookcode;

              if( ! empty($code))
              {
                $so = empty($order->so_code) ? NULL : $this->sales_order_model->get($order->so_code);

                $arr = array(
                  'code' => $code,
                  'bookcode' => $bookcode,
                  'is_term' => $ds->is_term,
                  'vat_type' => $ds->vat_type,
                  'TaxStatus' => $ds->taxStatus,
                  'tax_id' => $ds->tax_id,
                  'DocDate' => $doc_date,
                  'DocDueDate' => $doc_date,
                  'TaxDate' => $doc_date,
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
                  'DiscPrcnt' => $ds->billDiscPrcnt,
                  'DiscSum' => $ds->billDiscAmount,
                  'VatSum' => $ds->vatSum,
                  'DocTotal' => $ds->docTotal,
                  'BaseType' => 'POS',
                  'BaseRef' => $order->code,
                  'so_code' => $order->so_code,
                  'order_code' => NULL,
                  'bill_code' => $order->code,
                  'Comments' => "BaseOn Bill No : {$order->code} ".( ! empty($order->so_code) ? ", BaseOn Sales Order {$order->so_code}" : ""),
                  'WhsCode' => $order->warehouse_code,
                  'SlpCode' => $ds->sale_id,
                  'shipped_date' => now(),
                  'user' => $this->_user->uname,
                  'channels_code' => $order->channels_code,
                  'payment_role' => $order->payment_role,
                  'isWht' => $ds->whtPrcnt > 0 ? 1 : 0,
                  'WhtPrcnt' => $ds->whtPrcnt,
                  'WhtAmount' => $ds->whtAmount,
                  'downPaymentAmount' => $ds->totalDownAmount,
                  'shop_id' => $order->shop_id,
                  'pos_id' => $order->pos_id
                );

                $this->db->trans_begin();

                $id = $this->order_invoice_model->add($arr);

                if( $id )
                {
                  $lineNum = 0;

                  foreach($details as $rs)
                  {
                    if($sc === FALSE)
                    {
                      break;
                    }

                    $LineTotal = $ds->vat_type == 'E' ? $rs->total_amount : remove_vat($rs->total_amount, $rs->vat_rate);

                    $arr = array(
                      'bookcode' => $bookcode,
                      'invoice_id' => $id,
                      'invoice_code' => $code,
                      'LineNum' => $lineNum,
                      'BaseType' => 'POS',
                      'BaseRef' => $rs->order_code,
                      'BaseLine' => $rs->id,
                      'so_line_id' => $rs->line_id,
                      'ItemCode' => $rs->product_code,
                      'Dscription' => $rs->product_name,
                      'Qty' => $rs->qty,
                      'Price' => $ds->vat_type == 'E' ? $rs->final_price : remove_vat($rs->final_price, $rs->vat_rate), //-- ราคาขายหลังส่วนลดรายการ ไม่รวม VAT
                      'DiscPrcnt' => discountAmountToPercent($rs->discount_amount, $rs->qty, $rs->price),
                      'PriceBefDi' => $ds->vat_type == 'E' ? $rs->price : remove_vat($rs->price, $rs->vat_rate), //$rs->PriceBefDi,  //-- ราคาขายก่อนส่วนลดรายการและVAT
                      'LineTotal' => $LineTotal,
                      'VatType' => $ds->vat_type,
                      'VatCode' => $rs->vat_code,
                      'VatRate' => $rs->vat_rate,
                      'PriceAfVAT' => $ds->vat_type == 'E' ? add_vat($rs->final_price, $rs->vat_rate) : $rs->final_price,
                      'VatSum' => $rs->vat_amount,
                      'unitMsr' => $rs->unit_code,
                      'avgBillDiscAmount' => $rs->avgBillDiscAmount,
                      'sumBillDiscAmount' => $rs->sumBillDiscAmount,
                      'SlpCode' => $ds->sale_id,
                      'WhsCode' => $order->warehouse_code,
                      'BinCode' => $order->zone_code,
                      'DocDate' => $doc_date,
                      'shipped_date' => $doc_date,
                      'LineText' => NULL,
                      'is_count' => $rs->is_count
                    );

                    if($this->order_invoice_model->add_detail($arr))
                    {
                      $totalBefDisc += $rs->total_amount;
                      $VatSum += $rs->vat_amount;
                      $DiscSum += $rs->sumBillDiscAmount;
                      $lineNum++;

                      if( ! $this->order_pos_model->update_detail($rs->id, ['status' => 'C']))
                      {
                        $sc = FALSE;
                        $this->error = "Failed to update Order line status";
                      }

                      if($sc === TRUE)
                      {
                        $mv = array(
                          'reference' => $code,
                          'warehouse_code' => $order->warehouse_code,
                          'zone_code' => $order->zone_code,
                          'product_code' => $rs->product_code,
                          'move_in' => 0,
                          'move_out' => $rs->qty,
                          'date_add' => now()
                        );

                        if($this->movement_model->add($mv) === FALSE)
                        {
                          $sc = FALSE;
                          $this->error = 'บันทึก movement ขาออกไม่สำเร็จ';
                        }
                      }
                    }
                    else
                    {
                      $sc = FALSE;
                      $this->error = "Failed to insert invoice at line {$lineNum}";
                    } //--- end if add_detail
                  } //--- end foreach

                  if($sc === TRUE)
                  {
                    $DocTotal = $totalBefDisc - $DiscSum;

                    $arr = array(
                      'DiscSum' => $DiscSum,
                      'VatSum' => $VatSum,
                      'DocTotal' => $ds->vat_type == 'E' ? $DocTotal + $VatSum : $DocTotal
                    );

                    if( ! $this->order_invoice_model->update_by_id($id, $arr))
                    {
                      $sc = FALSE;
                      $this->error = "Failed to update Invoice Summary";
                    }
                  }

                  if($sc === TRUE)
                  {
                    $arr = array(
                      'invoice_code' => $code,
                      'status' => 'C',
                      'customer_name' => $ds->customer_name,
                      'branch_code' => $ds->branch_code,
                      'branch_name' => $ds->branch_name,
                      'isCompany' => $ds->is_company,
                      'tax_id' => $ds->tax_id,
                      'address' => $ds->address,
                      'sub_district' => $ds->sub_district,
                      'district' => $ds->district,
                      'province' => $ds->province,
                      'postcode' => $ds->postcode,
                      'phone' => $ds->phone
                    );

                    if( ! $this->order_pos_model->update($order->code, $arr))
                    {
                      $sc = FALSE;
                      $this->error = "Failed to update bill status";
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
                    $this->load->library('export');
                    $this->export->export_invoice($code);
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
                $this->error = "Cannot generate Document Number";
              }
            }
            else
            {
              $sc = FALSE;
              $this->error = "Cannot generate Document Prefix";
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
          $this->error = "เอกสารถูกเปิด invoice แล้ว";
        }
      }
      else
      {
        $sc = FALSE;
        $this->error = "Invalid bill status";
      }
    }
    else
    {
      $sc = FALSE;
      $this->error = "Invalid bill code";
    }

    $arr = array(
      'status' => $sc === TRUE ? 'success' : 'failed',
      'message' => $sc === TRUE ? 'success' : $this->error,
      'invoice_code' => $sc === TRUE ? $code : NULL,
      'invoice_id' => $sc === TRUE ? $id : NULL
    );

    return $arr;
  }


  private function add_by_wo($ds)
  {
    $sc = TRUE;
    $order = $this->orders_model->get($ds->billCode);

    if($order->state == 8)
    {
      if( empty($order->invoice_code))
      {
        $details = $this->invoice_model->get_details($order->code); //--- รายการที่มีการบันทึกขายไป

        if( ! empty($details))
        {
          $VatSum = 0.00;
          $DocTotal = 0.00;
          $totalBefDisc = 0;
          $DiscSum = 0.00;

          $doc_date = date('Ymd'); //db_date($ds->date_add);

          $pre = $this->getInvoicePrefixData('ORDER', is_true($ds->is_term), $ds->taxStatus);

          if( ! empty($pre))
          {
            $code = $this->get_new_code($pre->prefix, $pre->running, $doc_date);
            $bookcode = $pre->bookcode;

            if( ! empty($code))
            {
              $so = empty($order->so_code) ? NULL : $this->sales_order_model->get($order->so_code);

              $arr = array(
                'code' => $code,
                'bookcode' => $bookcode,
                'is_term' => $ds->is_term,
                'vat_type' => $ds->vat_type,
                'TaxStatus' => $ds->taxStatus,
                'tax_id' => $ds->tax_id,
                'DocDate' => $doc_date,
                'DocDueDate' => $doc_date,
                'TaxDate' => $doc_date,
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
                'DiscPrcnt' => $ds->billDiscPrcnt,
                'DiscSum' => $ds->billDiscAmount,
                'VatSum' => $ds->vatSum,
                'DocTotal' => $ds->docTotal,
                'BaseType' => 'WO',
                'BaseRef' => $order->code,
                'so_code' => $order->so_code,
                'order_code' => $order->code,
                'bill_code' => NULL,
                'Comments' => get_null($ds->remark) .( ! empty($order->so_code) ? "BaseOn Sales Order {$order->so_code}" : ""),
                'WhsCode' => $order->warehouse_code,
                'SlpCode' => $ds->sale_id,
                'shipped_date' => now(),
                'user' => $this->_user->uname,
                'channels_code' => $order->channels_code,
                'isWht' => $ds->whtPrcnt > 0 ? 1 : 0,
                'WhtPrcnt' => $ds->whtPrcnt,
                'WhtAmount' => $ds->whtAmount,
                'downPaymentAmount' => $ds->totalDownAmount
              );

              $this->db->trans_begin();

              $id = $this->order_invoice_model->add($arr);

              if( $id )
              {
                $lineNum = 0;

                foreach($details as $rs)
                {
                  if($sc === FALSE)
                  {
                    break;
                  }

                  $LineTotal = $ds->vat_type == 'E' ? $rs->total_amount : remove_vat($rs->total_amount, $rs->VatRate);

                  $arr = array(
                    'bookcode' => $bookcode,
                    'invoice_id' => $id,
                    'invoice_code' => $code,
                    'LineNum' => $lineNum,
                    'BaseType' => 'WO',
                    'BaseRef' => $rs->reference,
                    'BaseLine' => $rs->order_detail_id,
                    'so_line_id' => $rs->so_line_id,
                    'ItemCode' => $rs->product_code,
                    'Dscription' => $rs->product_name,
                    'Qty' => $rs->qty,
                    'Price' => $ds->vat_type == 'E' ? $rs->sell : remove_vat($rs->sell, $rs->VatRate), //-- ราคาขายหลังส่วนลดรายการ ไม่รวม VAT
                    'DiscPrcnt' => discountAmountToPercent($rs->discount_amount, $rs->qty, $rs->price),
                    'PriceBefDi' => $ds->vat_type == 'E' ? $rs->price : remove_vat($rs->price, $rs->VatRate), //$rs->PriceBefDi,  //-- ราคาขายก่อนส่วนลดรายการและVAT
                    'LineTotal' => $LineTotal,
                    'VatType' => $ds->vat_type,
                    'VatCode' => $rs->VatCode,
                    'VatRate' => $rs->VatRate,
                    'PriceAfVAT' => $ds->vat_type == 'E' ? add_vat($rs->sell, $rs->VatRate) : $rs->sell,
                    'VatSum' => $rs->VatSum,
                    'unitMsr' => $this->products_model->get_unit_code($rs->product_code),
                    'avgBillDiscAmount' => $rs->avgBillDiscAmount,
                    'sumBillDiscAmount' => $rs->sumBillDiscAmount,
                    'SlpCode' => $rs->sale_code,
                    'WhsCode' => $rs->warehouse_code,
                    'BinCode' => $rs->zone_code,
                    'DocDate' => $doc_date,
                    'shipped_date' => $doc_date,
                    'LineText' => $rs->line_text,
                    'is_count' => $rs->is_count
                  );

                  if($this->order_invoice_model->add_detail($arr))
                  {
                    $totalBefDisc += $rs->total_amount;
                    $VatSum += $rs->VatSum;
                    $DiscSum += $rs->sumBillDiscAmount;
                    $lineNum++;

                    if( ! $this->orders_model->update_detail($rs->order_detail_id, ['is_complete' => 1]))
                    {
                      $sc = FALSE;
                      $this->error = "Failed to update Order line status";
                    }

                    //--- update so open qty
                    if($sc === TRUE && ! empty($rs->so_line_id))
                    {
                      $sol = $this->sales_order_model->get_detail($rs->so_line_id);

                      if( ! empty($sol))
                      {
                        if($sol->line_status == 'O')
                        {
                          if($sol->OpenQty > 0 && $sol->OpenQty >= $rs->qty)
                          {
                            $OpenQty = $sol->OpenQty - $rs->qty;

                            $arr = array(
                              'OpenQty' => $OpenQty,
                              'line_status' => $OpenQty == 0 ? 'C' : 'O'
                            );

                            if( ! $this->sales_order_model->update_detail($rs->so_line_id, $arr))
                            {
                              $sc = FALSE;
                              $this->error = "Failed to update OpenQty On Line Id : {$rs->so_line_id}";
                            }
                          }
                          else
                          {
                            $sc = FALSE;
                            $this->error = "จำนวนที่เปิด invoice มากกว่าจำนวนคงค้างในใบสั่งขาย {$rs->so_code} : {$rs->product_code}";
                          }
                        }
                        else
                        {
                          $sc = FALSE;
                          $this->error = "สถานะรายการในใบสั่งขายถูกปิดไปแล้ว {$rs->so_code} : {$rs->product_code}";
                        }
                      }
                      else
                      {
                        $sc = FALSE;
                        $this->error = "ไม่พบรายการเชื่อมโยงในใบสั่งขาย {$rs->so_code} : {$rs->product_code}";
                      }
                    } //--- end if so_line_id
                  }
                  else
                  {
                    $sc = FALSE;
                    $this->error = "Failed to insert invoice at line {$lineNum}";
                  } //--- end if add_detail
                } //--- end foreach

                if($sc === TRUE)
                {
                  $DocTotal = $totalBefDisc - $DiscSum;

                  $arr = array(
                    'DiscSum' => $DiscSum,
                    'VatSum' => $VatSum,
                    'DocTotal' => $ds->vat_type == 'E' ? $DocTotal + $VatSum : $DocTotal
                  );

                  if( ! $this->order_invoice_model->update_by_id($id, $arr))
                  {
                    $sc = FALSE;
                    $this->error = "Failed to update Invoice Summary";
                  }
                }

                if($sc === TRUE)
                {
                  if( ! $this->orders_model->update($order->code, ['invoice_code' => $code]))
                  {
                    $sc = FALSE;
                    $this->error = "Failed to update invoice code On Orders";
                  }
                }

                if($sc === TRUE && ! empty($order->so_code))
                {
                  //---- close so if all line closed
                  $count = $this->sales_order_model->count_open_line($order->so_code);

                  if($count == 0)
                  {
                    $this->sales_order_model->update($order->so_code, ['status' => 'C']);
                  }
                }

                if($sc === TRUE)
                {
                  if( ! empty($ds->downPaymentUse))
                  {
                    foreach($ds->downPaymentUse as $rs)
                    {
                      if($sc === FALSE)
                      {
                        break;
                      }

                      $dp = $this->order_down_payment_model->get_by_id($rs->id);

                      if( ! empty($dp))
                      {
                        if($dp->status == 'O') {
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
                              'TargetRef' => $order->code,
                              'TargetType' => 'WO',
                              'so_code' => $order->so_code,
                              'order_code' => $order->code,
                              'bill_code' => NULL,
                              'invoice_code' => $code,
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
                          }
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

                if($sc === TRUE)
                {
                  $this->load->library('export');
                  $this->export->export_invoice($code);
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
              $this->error = "Cannot generate Document Number";
            }
          }
          else
          {
            $sc = FALSE;
            $this->error = "Cannot generate Document Prefix";
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
        $this->error = "เอกสารถูกเปิด invoice แล้ว";
      }
    }
    else
    {
      $sc = FALSE;
      $this->error = "Invalid document state";
    }

    $arr = array(
      'status' => $sc === TRUE ? 'success' : 'failed',
      'message' => $sc === TRUE ? 'success' : $this->error,
      'invoice_code' => $sc === TRUE ? $code : NULL,
      'invoice_id' => $sc === TRUE ? $id : NULL
    );

    return $arr;
  }



  private function add_pos_invoice($order)
  {
    $sc = TRUE;
    $this->load->model('inventory/movement_model');

    if( ! empty($order))
    {
      if($order->status == 'O')
      {
        if( empty($order->invoice_code))
        {
          $details = $this->order_pos_model->get_details($order->code); //--- รายการที่มีการบันทึกขายไป

          if( ! empty($details))
          {
            $VatSum = 0.00;
            $DocTotal = 0.00;
            $totalBefDisc = 0;
            $DiscSum = 0.00;

            $doc_date = date('Y-m-d');
            $type = empty($order->so_code) ? 'POS' : 'ORDER';
            $is_term = FALSE;
            $taxStatus = 'N';
            $pre = $this->getInvoicePrefixData($type, is_true($is_term), $taxStatus);

            if( ! empty($pre))
            {
              $code = $this->get_new_code($pre->prefix, $pre->running, $doc_date);
              $bookcode = $pre->bookcode;

              if( ! empty($code))
              {
                $so = empty($order->so_code) ? NULL : $this->sales_order_model->get($order->so_code);

                $arr = array(
                  'code' => $code,
                  'bookcode' => $bookcode,
                  'is_term' => $is_term,
                  'vat_type' => $order->vat_type,
                  'TaxStatus' => $taxStatus,
                  'tax_id' => $order->tax_id,
                  'DocDate' => $doc_date,
                  'DocDueDate' => $doc_date,
                  'TaxDate' => $doc_date,
                  'CardCode' => $order->customer_code,
                  'CardName' => $order->customer_name,
                  'isCompany' => $order->isCompany,
                  'branch_code' => $order->branch_code,
                  'branch_name' => $order->branch_name,
                  'NumAtCard' => $order->customer_name,
                  'address' => $order->address,
                  'sub_district' => $order->sub_district,
                  'district' => $order->district,
                  'province' => $order->province,
                  'postcode' => $order->postcode,
                  'phone' => $order->phone,
                  'DiscPrcnt' => $order->discPrcnt,
                  'DiscSum' => $order->disc_amount,
                  'VatSum' => $order->vat_amount,
                  'DocTotal' => $order->amount,
                  'BaseType' => 'POS',
                  'BaseRef' => $order->code,
                  'so_code' => $order->so_code,
                  'order_code' => NULL,
                  'bill_code' => $order->code,
                  'Comments' => "BaseOn Bill No : {$order->code} ".( ! empty($order->so_code) ? ", BaseOn Sales Order {$order->so_code}" : ""),
                  'WhsCode' => $order->warehouse_code,
                  'SlpCode' => $order->sale_id,
                  'shipped_date' => now(),
                  'user' => $this->_user->uname,
                  'channels_code' => $order->channels_code,
                  'isWht' => $order->WhtPrcnt > 0 ? 1 : 0,
                  'WhtPrcnt' => $order->WhtPrcnt,
                  'WhtAmount' => $order->WhtAmount,
                  'downPaymentAmount' => $order->down_payment_amount,
                  'shop_id' => $order->shop_id,
                  'pos_id' => $order->pos_id
                );

                $this->db->trans_begin();

                $id = $this->order_invoice_model->add($arr);

                if( $id )
                {
                  $lineNum = 0;

                  foreach($details as $rs)
                  {
                    if($sc === FALSE)
                    {
                      break;
                    }

                    $LineTotal = $order->vat_type == 'E' ? $rs->total_amount : remove_vat($rs->total_amount, $rs->vat_rate);

                    $arr = array(
                      'bookcode' => $bookcode,
                      'invoice_id' => $id,
                      'invoice_code' => $code,
                      'LineNum' => $lineNum,
                      'BaseType' => 'POS',
                      'BaseRef' => $rs->order_code,
                      'BaseLine' => $rs->id,
                      'so_line_id' => $rs->line_id,
                      'ItemCode' => $rs->product_code,
                      'Dscription' => $rs->product_name,
                      'Qty' => $rs->qty,
                      'Price' => $order->vat_type == 'E' ? $rs->final_price : remove_vat($rs->final_price, $rs->vat_rate), //-- ราคาขายหลังส่วนลดรายการ ไม่รวม VAT
                      'DiscPrcnt' => discountAmountToPercent($rs->discount_amount, $rs->qty, $rs->price),
                      'PriceBefDi' => $order->vat_type == 'E' ? $rs->price : remove_vat($rs->price, $rs->vat_rate), //$rs->PriceBefDi,  //-- ราคาขายก่อนส่วนลดรายการและVAT
                      'LineTotal' => $LineTotal,
                      'VatType' => $order->vat_type,
                      'VatCode' => $rs->vat_code,
                      'VatRate' => $rs->vat_rate,
                      'PriceAfVAT' => $order->vat_type == 'E' ? add_vat($rs->final_price, $rs->vat_rate) : $rs->final_price,
                      'VatSum' => $rs->vat_amount,
                      'unitMsr' => $rs->unit_code,
                      'avgBillDiscAmount' => $rs->avgBillDiscAmount,
                      'sumBillDiscAmount' => $rs->sumBillDiscAmount,
                      'SlpCode' => $order->sale_id,
                      'WhsCode' => $order->warehouse_code,
                      'BinCode' => $order->zone_code,
                      'DocDate' => $doc_date,
                      'shipped_date' => $doc_date,
                      'LineText' => NULL,
                      'is_count' => $rs->is_count
                    );

                    if($this->order_invoice_model->add_detail($arr))
                    {
                      $totalBefDisc += $rs->total_amount;
                      $VatSum += $rs->vat_amount;
                      $DiscSum += $rs->sumBillDiscAmount;
                      $lineNum++;

                      if( ! $this->order_pos_model->update_detail($rs->id, ['status' => 'C']))
                      {
                        $sc = FALSE;
                        $this->error = "Failed to update Order line status";
                      }

                      if($sc === TRUE)
                      {
                        $mv = array(
                          'reference' => $code,
                          'warehouse_code' => $order->warehouse_code,
                          'zone_code' => $order->zone_code,
                          'product_code' => $rs->product_code,
                          'move_in' => 0,
                          'move_out' => $rs->qty,
                          'date_add' => now()
                        );

                        if($this->movement_model->add($mv) === FALSE)
                        {
                          $sc = FALSE;
                          $this->error = 'บันทึก movement ขาออกไม่สำเร็จ';
                        }
                      }
                    }
                    else
                    {
                      $sc = FALSE;
                      $$this->error = "{$order->code} : Failed to insert invoice at line {$lineNum}";
                    } //--- end if add_detail
                  } //--- end foreach

                  if($sc === TRUE)
                  {
                    $DocTotal = $totalBefDisc - $DiscSum;

                    $arr = array(
                      'DiscSum' => $DiscSum,
                      'VatSum' => $VatSum,
                      'DocTotal' => $order->vat_type == 'E' ? $DocTotal + $VatSum : $DocTotal
                    );

                    if( ! $this->order_invoice_model->update_by_id($id, $arr))
                    {
                      $sc = FALSE;
                      $this->error = "Failed to update Invoice Summary : {$order->code}";
                    }
                  }

                  if($sc === TRUE)
                  {
                    $arr = array(
                      'invoice_code' => $code,
                      'status' => 'C'
                    );

                    if( ! $this->order_pos_model->update($order->code, $arr))
                    {
                      $sc = FALSE;
                      $this->error = "Failed to update bill status : {$order->code}";
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
                    $this->load->library('export');
                    $this->export->export_invoice($code);
                  }
                }
                else
                {
                  $sc = FALSE;
                  $this->error = "Failed to create Invoice : {$order->code}";
                }
              }
              else
              {
                $sc = FALSE;
                $this->error = "Cannot generate Document Number : {$order->code}";
              }
            }
            else
            {
              $sc = FALSE;
              $this->error = "Cannot generate Document Prefix : {$order->code}";
            }
          }
          else
          {
            $sc = FALSE;
            $this->error = "ไม่พบรายการบันทึกขาย : {$order->code}";
          }
        }
        else
        {
          $sc = FALSE;
          $this->error = "เอกสาร {$order->code} ถูกเปิด invoice แล้ว";
        }
      }
      else
      {
        $sc = FALSE;
        $this->error = "Invalid bill status : {$order->code}";
      }
    }
    else
    {
      $sc = FALSE;
      $this->error = "Invalid bill code";
    }

    $arr = array(
      'status' => $sc === TRUE ? 'success' : 'failed',
      'message' => $sc === TRUE ? 'success' : $this->error,
      'invoice_code' => $sc === TRUE ? $code : NULL,
      'invoice_id' => $sc === TRUE ? $id : NULL
    );

    return $arr;
  }


  public function add_each_invoice_by_bills()
  {
    $sc = TRUE;
    $msg = "";
    $bills = json_decode($this->input->post('bills'));

    if( ! empty($bills))
    {
      foreach($bills as $code)
      {
        $order = $this->order_pos_model->get($code);

        if( ! empty($order))
        {
          $result = $this->add_pos_invoice($order);

          if( ! empty($result))
          {
            if($result['status'] == 'failed')
            {
              $sc = FALSE;
              $msg .= $result['message'].PHP_EOL;
            }
          }
          else
          {
            $sc = FALSE;
            $msg .= "Not response from function".PHP_EOL;
          }
        }
        else
        {
          $sc = FALSE;
          $msg .= "{$code} not exists".PHP_EOL;
        }
      }
    }
    else
    {
      $sc = FALSE;
      $msg = "No data found";
    }

    $arr = array(
      'status' => $sc === TRUE ? 'success' : 'failed',
      'message' => $sc === TRUE ? 'success' : $msg
    );

    echo json_encode($arr);
  }


  public function add_each_invoice_by_delivery()
  {
    $sc = TRUE;
    $msg = "";
    $bills = json_decode($this->input->post('bills'));

    if( ! empty($bills))
    {
      foreach($bills as $code)
      {
        $order = $this->orders_model->get($code);

        if( ! empty($order))
        {
          $order->billCode = $order->code;
          $order->taxStatus = 'N';
          $order->refType = $order->role == 'S' ? 'WO' : ($order->role == 'U' ? 'WU' : ($order->role == 'C' ? 'WC' : 'WS'));
          $order->date_add = now();
          $order->is_company = $order->isCompany;
          $order->billDiscPrcnt = $order->bDiscText;
          $order->billDiscAmount = $order->bDiscAmount;
          $order->sale_id = $order->sale_code;
          $order->vatSum = 0;
          $order->docTotal = 0;
          $order->totalDownAmount = 0;
          $order->whtPrcnt = $order->WhtPrcnt;
          $order->whtAmount = $order->WhtAmount;

          $result = $this->add_by_order($order);

          if( ! empty($result))
          {
            if($result['status'] == 'failed')
            {
              $sc = FALSE;
              $msg .= $result['message'].PHP_EOL;
            }
          }
          else
          {
            $sc = FALSE;
            $msg .= "Not response from function".PHP_EOL;
          }
        }
        else
        {
          $sc = FALSE;
          $msg .= "{$code} not exists".PHP_EOL;
        }
      }
    }
    else
    {
      $sc = FALSE;
      $msg = "No data found";
    }

    $arr = array(
      'status' => $sc === TRUE ? 'success' : 'failed',
      'message' => $sc === TRUE ? 'success' : $msg
    );

    echo json_encode($arr);
  }



  public function add_invoice()
  {
    $sc = TRUE;

    $ds = json_decode($this->input->post('data'));

    if( ! empty($ds))
    {
      $result = NULL;

      switch($ds->refType)
      {
        case "WO" :
          $result = $this->add_by_wo($ds);
          break;
        case "WU" :
          $result = $this->add_by_order($ds);
          break;
        case "POS" :
          $result = $this->add_by_pos($ds);
          break;
      }

      if( ! empty($result))
      {
        if($result['status'] == 'failed')
        {
          $sc = FALSE;
          $this->error = $result['message'];
        }
      }
      else
      {
        $sc = FALSE;
        $this->error = "Not response from function";
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
      'invoice_code' => $sc === TRUE ? $result['invoice_code'] : NULL,
      'invoice_id' => $sc === TRUE ? $result['invoice_id'] : NULL
    );

    echo json_encode($arr);
  }


  public function add_invoice_customer()
  {
    $sc = TRUE;

    $ds = json_decode($this->input->post('data'));

    if( ! empty($ds))
    {
      $arr = array(
        'name' => $ds->customer_name,
        'tax_id' => $ds->tax_id,
        'branch_code' => get_null($ds->branch_code),
        'branch_name' => get_null($ds->branch_name),
        'address' => get_null($ds->address),
        'sub_district' => get_null($ds->sub_district),
        'district' => get_null($ds->district),
        'province' => get_null($ds->province),
        'postcode' => get_null($ds->postcode),
        'phone' => $ds->phone,
        'is_company' => $ds->is_company == 1 ? 1 : 0
      );

      if( empty($ds->customer_id) )
      {

        if( ! $this->invoice_customer_model->add($arr))
        {
          $sc = FALSE;
          $this->error = "เพิ่มข้อมูลลูกค้าไม่สำเร็จ";
        }
      }
      else
      {
        if( ! $this->invoice_customer_model->update($ds->customer_id, $arr))
        {
          $sc = FALSE;
          $this->error = "แก้ไขข้อมูลลูกค้าไม่สำเร็จ";
        }
      }
    }
    else
    {
      $sc = FALSE;
      $this->errorr = "Missing required parameter";
    }

    $arr = array(
      'status' => $sc === TRUE ? 'success' : 'failed',
      'message' => $sc === TRUE ? 'success' : $this->error
    );

    echo json_encode($arr);
  }


  private function add_by_order($ds)
  {
    $sc = TRUE;
    $ex = 0;
    $order = $this->orders_model->get($ds->billCode);

    if($order->state == 8)
    {
      if( empty($order->invoice_code))
      {
        $details = $this->invoice_model->get_details($order->code); //--- รายการที่มีการบันทึกขายไป

        if( ! empty($details))
        {
          $VatSum = 0.00;
          $DocTotal = 0.00;
          $totalBefDisc = 0;
          $DiscSum = 0.00;

          $doc_date = db_date($ds->date_add);

          $pre = $this->getInvoicePrefixData('ORDER', is_true($ds->is_term), $ds->taxStatus);

          if( ! empty($pre))
          {
            $code = $this->get_new_code($pre->prefix, $pre->running, $doc_date);
            $bookcode = $pre->bookcode;

            if( ! empty($code))
            {
              $so = empty($order->so_code) ? NULL : $this->sales_order_model->get($order->so_code);

              $arr = array(
                'code' => $code,
                'bookcode' => $bookcode,
                'is_term' => $ds->is_term,
                'vat_type' => $ds->vat_type,
                'TaxStatus' => $ds->taxStatus,
                'tax_id' => $ds->tax_id,
                'DocDate' => $doc_date,
                'DocDueDate' => $doc_date,
                'TaxDate' => $doc_date,
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
                'DiscPrcnt' => $ds->billDiscPrcnt,
                'DiscSum' => $ds->billDiscAmount,
                'VatSum' => $ds->vatSum,
                'DocTotal' => $ds->docTotal,
                'BaseType' => $ds->refType,
                'BaseRef' => $order->code,
                'so_code' => $order->so_code,
                'order_code' => $order->code,
                'bill_code' => NULL,
                'Comments' => get_null($ds->remark) .( ! empty($order->so_code) ? "BaseOn Sales Order {$order->so_code}" : ""),
                'WhsCode' => $order->warehouse_code,
                'SlpCode' => $ds->sale_id,
                'shipped_date' => now(),
                'user' => $this->_user->uname,
                'channels_code' => $order->channels_code,
                'isWht' => $ds->whtPrcnt > 0 ? 1 : 0,
                'WhtPrcnt' => $ds->whtPrcnt,
                'WhtAmount' => $ds->whtAmount,
                'downPaymentAmount' => $ds->totalDownAmount
              );

              $this->db->trans_begin();

              $id = $this->order_invoice_model->add($arr);

              if( $id )
              {
                $lineNum = 0;

                foreach($details as $rs)
                {
                  if($sc === FALSE)
                  {
                    break;
                  }

                  $LineTotal = $ds->vat_type == 'E' ? $rs->total_amount : remove_vat($rs->total_amount, $rs->VatRate);

                  $arr = array(
                    'bookcode' => $bookcode,
                    'invoice_id' => $id,
                    'invoice_code' => $code,
                    'LineNum' => $lineNum,
                    'BaseType' => $ds->refType,
                    'BaseRef' => $rs->reference,
                    'BaseLine' => $rs->order_detail_id,
                    'so_line_id' => $rs->so_line_id,
                    'ItemCode' => $rs->product_code,
                    'Dscription' => $rs->product_name,
                    'Qty' => $rs->qty,
                    'Price' => $ds->vat_type == 'E' ? $rs->sell : remove_vat($rs->sell, $rs->VatRate), //-- ราคาขายหลังส่วนลดรายการ ไม่รวม VAT
                    'DiscPrcnt' => discountAmountToPercent($rs->discount_amount, $rs->qty, $rs->price),
                    'PriceBefDi' => $ds->vat_type == 'E' ? $rs->price : remove_vat($rs->price, $rs->VatRate), //$rs->PriceBefDi,  //-- ราคาขายก่อนส่วนลดรายการและVAT
                    'LineTotal' => $LineTotal,
                    'VatType' => $ds->vat_type,
                    'VatCode' => $rs->VatCode,
                    'VatRate' => $rs->VatRate,
                    'PriceAfVAT' => $ds->vat_type == 'E' ? add_vat($rs->sell, $rs->VatRate) : $rs->sell,
                    'VatSum' => $rs->VatSum,
                    'unitMsr' => $this->products_model->get_unit_code($rs->product_code),
                    'avgBillDiscAmount' => $rs->avgBillDiscAmount,
                    'sumBillDiscAmount' => $rs->sumBillDiscAmount,
                    'SlpCode' => $rs->sale_code,
                    'WhsCode' => $rs->warehouse_code,
                    'BinCode' => $rs->zone_code,
                    'DocDate' => $doc_date,
                    'shipped_date' => $doc_date,
                    'LineText' => $rs->line_text,
                    'is_count' => $rs->is_count
                  );

                  if($this->order_invoice_model->add_detail($arr))
                  {
                    $totalBefDisc += $rs->total_amount;
                    $VatSum += $rs->VatSum;
                    $DiscSum += $rs->sumBillDiscAmount;
                    $lineNum++;

                    if( ! $this->orders_model->update_detail($rs->order_detail_id, ['is_complete' => 1]))
                    {
                      $sc = FALSE;
                      $this->error = "Failed to update Order line status";
                    }

                    //--- update so open qty
                    if($sc === TRUE && ! empty($rs->so_line_id))
                    {
                      $sol = $this->sales_order_model->get_detail($rs->so_line_id);

                      if( ! empty($sol))
                      {
                        if($sol->line_status == 'O')
                        {
                          if($sol->OpenQty > 0 && $sol->OpenQty >= $rs->qty)
                          {
                            $OpenQty = $sol->OpenQty - $rs->qty;

                            $arr = array(
                              'OpenQty' => $OpenQty,
                              'line_status' => $OpenQty == 0 ? 'C' : 'O'
                            );

                            if( ! $this->sales_order_model->update_detail($rs->so_line_id, $arr))
                            {
                              $sc = FALSE;
                              $this->error = "Failed to update OpenQty On Line Id : {$rs->so_line_id}";
                            }
                          }
                          else
                          {
                            $sc = FALSE;
                            $this->error = "จำนวนที่เปิด invoice มากกว่าจำนวนคงค้างในใบสั่งขาย {$rs->so_code} : {$rs->product_code}";
                          }
                        }
                        else
                        {
                          $sc = FALSE;
                          $this->error = "สถานะรายการในใบสั่งขายถูกปิดไปแล้ว {$rs->so_code} : {$rs->product_code}";
                        }
                      }
                      else
                      {
                        $sc = FALSE;
                        $this->error = "ไม่พบรายการเชื่อมโยงในใบสั่งขาย {$rs->so_code} : {$rs->product_code}";
                      }
                    } //--- end if so_line_id
                  }
                  else
                  {
                    $sc = FALSE;
                    $this->error = "Failed to insert invoice at line {$lineNum}";
                  } //--- end if add_detail
                } //--- end foreach

                if($sc === TRUE)
                {
                  $DocTotal = $totalBefDisc - $DiscSum;
                  $DocTotal = $ds->vat_type == 'E' ? $DocTotal + $VatSum : $DocTotal;

                  $arr = array(
                    'DiscSum' => $DiscSum,
                    'VatSum' => $VatSum,
                    'DocTotal' => $DocTotal
                  );

                  if(empty($ds->downPaymentUse))
                  {
                    $dps = $this->order_down_payment_model->get_by_reference($order->code);

                    if( ! empty($dps))
                    {
                      $doc_total = $ds->vat_type == 'E' ? $DocTotal + $VatSum : $DocTotal;
                      $downPaymentUse = array();
                      $downPaymentAmount = 0;

                      foreach($dps as $dp)
                      {
                        if($doc_total > 0)
                        {
                          if($dp->available > 0)
                          {
                            $amount = $dp->available <= $doc_total ? $dp->available : $doc_total;
                            $dpu = new stdClass();
                            $dpu->id = $dp->id;
                            $dpu->amount = $amount;

                            $downPaymentUse[] = $dpu;

                            $doc_total -= $amount;
                            $downPaymentAmount += $amount;
                          }
                        }
                      } //--- end foreach;

                      $ds->downPaymentUse = $downPaymentUse;

                      $arr['downPaymentAmount'] = $downPaymentAmount;
                    }
                  }


                  if( ! $this->order_invoice_model->update_by_id($id, $arr))
                  {
                    $sc = FALSE;
                    $this->error = "Failed to update Invoice Summary";
                  }
                }

                if($sc === TRUE)
                {
                  if( ! $this->orders_model->update($order->code, ['invoice_code' => $code]))
                  {
                    $sc = FALSE;
                    $this->error = "Failed to update invoice code On Orders";
                  }
                }

                if($sc === TRUE && ! empty($order->so_code))
                {
                  //---- close so if all line closed
                  $count = $this->sales_order_model->count_open_line($order->so_code);

                  if($count == 0)
                  {
                    $this->sales_order_model->update($order->so_code, ['status' => 'C']);
                  }
                }

                if($sc === TRUE)
                {

                  if( ! empty($ds->downPaymentUse))
                  {
                    foreach($ds->downPaymentUse as $rs)
                    {
                      if($sc === FALSE)
                      {
                        break;
                      }

                      $dp = $this->order_down_payment_model->get_by_id($rs->id);

                      if( ! empty($dp))
                      {
                        if($dp->status == 'O') {
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
                              'TargetRef' => $order->code,
                              'TargetType' => $ds->refType,
                              'so_code' => $order->so_code,
                              'order_code' => $order->code,
                              'bill_code' => NULL,
                              'invoice_code' => $code,
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
                          }
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

                if($sc === TRUE)
                {
                  $this->load->library('export');
                  $this->export->export_invoice($code);
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
              $this->error = "Cannot generate Document Number";
            }
          }
          else
          {
            $sc = FALSE;
            $this->error = "Cannot generate Document Prefix";
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
        $this->error = "เอกสารถูกเปิด invoice แล้ว";
      }
    }
    else
    {
      $sc = FALSE;
      $this->error = "Invalid document state";
    }

    $arr = array(
      'status' => $sc === TRUE ? 'success' : 'failed',
      'message' => $sc === TRUE ? 'success' : $this->error,
      'invoice_code' => $sc === TRUE ? $code : NULL,
      'invoice_id' => $sc === TRUE ? $id : NULL,
      'ex' => $ex
    );

    return $arr;
  }


  public function view_detail($code)
  {
    $order = $this->order_invoice_model->get($code);

    if( ! empty($order))
    {
      $details = $this->order_invoice_model->get_details($code);

      $order->totalBfDisc = $order->vat_type == 'E' ? ($order->DocTotal - $order->VatSum) - $order->DiscSum : $order->DocTotal - $order->DiscSum;

      $ds = array(
        'order' => $order,
        'details' => $details,
        'down_payment' => $this->order_down_payment_model->get_details_by_target($order->BaseRef)
      );

      $this->load->view('order_invoice/invoice_detail', $ds);
    }
    else
    {
      $this->page_error();
    }
  }


  public function cancel_invoice()
  {
    $sc = TRUE;
    $id = $this->input->post('id');
    $reason = trim($this->input->post('reason'));

    if($this->pm->can_delete)
    {
      $order = $this->order_invoice_model->get_by_id($id);

      if( ! empty($order))
      {
        if($order->status != 'D')
        {
          $sap = $this->order_invoice_model->get_sap_doc_num($order->code);

          if( ! empty($sap))
          {
            $sc = FALSE;
            $this->error = "กรุณายกเลิก Ivoice : {$sap} ใน SAP ก่อนทำการยกเลิก";
          }

          if($sc === TRUE)
          {
            $middle = $this->order_invoice_model->get_middle_invoice($order->code);

            if( ! empty($middle))
            {
              foreach($middle as $row)
              {
                $this->order_invoice_model->drop_middle_exits_data($row->DocEntry);
              }
            }

            $this->load->model('inventory/movement_model');

            $this->db->trans_begin();

            /* 1. cancel invoice
               2. cancel invoice rows
               3. remove order_sold
               4. remove movement
               5. roll back bill status
            */
            $arr = array(
              'status' => 'D',
              'cancel_reason' => $reason,
              'cancel_date' => now(),
              'cancel_user' => $this->_user->uname
            );

            //--1. cancel invoice
            if( ! $this->order_invoice_model->update_by_id($order->id, $arr))
            {
              $sc = FALSE;
              $this->error = "ยกเลิกเอกสารไม่สำเร็จ : Failed to update invoice status";
            }

            //--2. cancel invoice rows
            if($sc === TRUE)
            {
              if( ! $this->order_invoice_model->update_details_by_id($order->id, array('LineStatus' => 'D')))
              {
                $sc = FALSE;
                $this->error = 'ยกเลิกรายการไม่สำเร็จ : Failed to update invoice rows status';
              }
            }

            //---4. remove movement
            if($sc === TRUE)
            {
              if( ! $this->movement_model->drop_movement($order->code))
              {
                $sc = FALSE;
                $this->error = "ลบรายการเคลื่อนไหวสินค้าไม่สำเร็จ : Failed to delete stock movement";
              }
            }

            //---5. roll back bill status
            if($sc === TRUE)
            {
              if($order->BaseType == 'POS')
              {
                //--- roll back bill status
                if( ! $this->order_pos_model->update($order->BaseRef, ['invoice_code' => NULL, 'status' => 'O']))
                {
                  $sc = FALSE;
                  $this->error = "ย้อนสถานะบิลขายไม่สำเร็จ : {$order->BaseRef} : Failed to rollback bill status {$order->BaseRef}";
                }

                //--- roll back bill item status
                if($sc === TRUE)
                {
                  if( ! $this->order_pos_model->update_details($order->BaseRef, array('status' => 'O')))
                  {
                    $sc = FALSE;
                    $this->error = "ย้อนสถานะรายการบิลขายไม่สำเร็จ : {$order->BaseRef} : Failed to rollback bill item status {$order->BaseRef}";
                  }
                }
              }

              //--- roll back order
              if($order->BaseType == 'WO' OR $order->BaseType == 'WU' OR $order->BaseType == 'WC' OR $order->BaseType == 'WS')
              {
                if( ! $this->orders_model->update($order->BaseRef, ['invoice_code' => NULL]))
                {
                  $sc = FALSE;
                  $this->error = "ย้อนสถานะรายการบิลขายไม่สำเร็จ : {$order->BaseRef} : Failed to rollback bill item status {$order->BaseRef}";
                }

                if($sc === TRUE)
                {
                  //---- roll back so open qty
                  if( ! empty($order->so_code))
                  {
                    $details = $this->order_invoice_model->get_details($order->code);

                    if( ! empty($details))
                    {
                      foreach($details as $rs)
                      {
                        if($sc === FALSE)
                        {
                          break;
                        }

                        //--- roll back is completed
                        if( ! $this->orders_model->update_detail($rs->BaseLine, ['is_complete' => 0]))
                        {
                          $sc = FALSE;
                          $this->error = "Failed to rollback order complete status @ Line id {$rs->BaseLine}";
                        }

                        if($sc === TRUE)
                        {
                          if( ! empty($rs->so_line_id))
                          {
                            $so = $this->sales_order_model->get_detail($rs->so_line_id);

                            if( ! empty($so))
                            {
                              $openQty = $so->OpenQty + $rs->Qty;
                              $openQty = $so->qty >= $openQty ? $openQty : $so->Qty;

                              $arr = array(
                                'OpenQty' => $openQty,
                                'line_status' => 'O'
                              );

                              if( ! $this->sales_order_model->update_detail($rs->so_line_id, $arr))
                              {
                                $sc = FALSE;
                                $this->error = "Failed to rollback Sales order Open Qty : {$order->so_code}";
                              }
                            }
                          } //--- roll back so open qty
                        } //--- if $sc = TRUE
                      } //--- foreach ($details)
                    } //--- if( ! empty($details))
                  } //--- roll back sale order
                }//--- $sc = TRUE

                //--- roll back down payment
                if($sc === TRUE)
                {
                  if($order->downPaymentAmount > 0)
                  {
                    $downs = $this->order_down_payment_model->get_details_by_target($order->BaseRef);

                    $downs = empty($downs) ? $this->order_down_payment_model->get_details_by_target($order->code) : $downs;

                    if( ! empty($downs))
                    {
                      //--- cancel downs payment details
                      foreach($downs as $rs)
                      {
                        if($sc === FALSE)
                        {
                          break;
                        }

                        $dp = $this->order_down_payment_model->get_by_id($rs->down_payment_id);

                        if( ! empty($dp))
                        {
                          if( $this->order_down_payment_model->update_detail($rs->id, ['is_cancel' => 1]))
                          {
                            $used = $dp->used - $rs->amount;
                            $used = $dp->amount >= $used ? $used : $dp->amount;
                            $available = $dp->amount - $used;

                            $arr = array(
                              'used' => $used,
                              'available' => $available,
                              'status' => 'O'
                            );

                            if( ! $this->order_down_payment_model->update($dp->id, $arr))
                            {
                              $sc = FALSE;
                              $this->error = "Failed to update down payment available : {$dp->code}";
                            }
                          }
                          else
                          {
                            $sc = FALSE;
                            $this->error = "Failed to cancel downpayment used details : {$rs->down_payment_code}";
                          }
                        }
                      }
                    } //---
                  } //---- if(down_payment_amount > 0)
                } //--- $sc === TRUE  roll back down payment
              } //---- role back order
            }

            if( $sc === TRUE)
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
          $this->error = "เอกสารถูกยกเลิกแล้วโดย {$order->cancel_user} เมื่อวันที่ ".thai_date($order->cancel_date, TRUE);
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
      set_error('permission');
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
    $this->load->library('export');

    if( ! $this->export->export_invoice($code))
    {
      $sc = FALSE;
      $this->error = $this->export->error;
    }

    echo $sc === TRUE ? 'success' : $this->error;
  }


  private function do_export($code)
  {
    $sc = TRUE;

    $this->load->library('export');

    if(! $this->export->export_invoice($code))
    {
      $sc = FALSE;
      $this->error = trim($this->export->error);
    }

    return $sc;
  }

  //--- for export invoice to get data from ODPI for Invoice Downpayment Drawn
  public function get_dpm_by_base_ref($BaseRef)
  {
    $dpmInvData = NULL;
    $dpmInvCodes = [];
    $dpm = $this->order_down_payment_model->get_invoice_by_target($BaseRef);

    if( ! empty($dpm))
    {
      foreach($dpm as $dp)
      {
        $dpmInvCodes[] = $dp->invoice_code;
      }
    }

    if( ! empty($dpmInvCodes))
    {
      $dpmInvData = $this->down_payment_invoice_model->get_down_payments_by_array($dpmInvCodes);
    }

    return $dpmInvData;
  }

  public function print_invoice($invoice_code, $option = '')
  {
    $this->load->library('iprinter');
    $this->load->helper('print');

    $title = array(
      'DO' => 'ใบส่งสินค้า',
      'DIO' => 'ใบส่งสินค้า/ใบแจ้งหนี้',
      'DRO' => 'ใบส่งสินค้า/ใบเสร็จรับเงิน',
      'DTI' => 'ใบส่งสินค้า/ใบกำกับภาษี',
      'DTIN' => 'ใบส่งสินค้า/ใบกำกับภาษี', //- no date
      'DITI' => 'ใบส่งสินค้า/ใบแจ้งหนี้/ใบกำกับภาษี',
      'DITIN' => 'ใบส่งสินค้า/ใบแจ้งหนี้/ใบกำกับภาษี', //-- no date
      'DRTI' => 'ใบส่งสินค้า/ใบเสร็จรับเงิน/ใบกำกับภาษี',
      'DRTIN' => 'ใบส่งสินค้า/ใบเสร็จรับเงิน/ใบกำกับภาษี' //-- no date
    );

    $order = $this->order_invoice_model->get_by_code($invoice_code);

    if( ! empty($order))
    {
      if(empty($option))
      {
        switch($order->bookcode)
        {
          case 'P' :
            $option = $order->TaxStatus == 'Y' ? 'DRTI' : 'DRO';
          break;
          case 'C' :
            $option = $order->TaxStatus == 'Y' ? 'DRTI' : 'DRO';
          break;
          case 'T' :
            $option = $order->TaxStatus == 'Y' ? 'DITI' : 'DIO';
          break;
        }
      }

      $details = $this->order_invoice_model->get_details_by_code($invoice_code);

      //---- ต้องการเลขที่ใบกำกับภาษีเงินมัดจำ จากเอกสารรับเงินมัดจำ เพื่อไปดึงยอดรับเงินมัดจำจาก ODPI
      $dpm = $this->get_dpm_by_base_ref($order->BaseRef);

      $dpmAmount = 0;
      $dpmVatSum = 0;

      if( ! empty($dpm))
      {
        foreach($dpm as $dp)
        {
          $dpmAmount += $dp->DocTotal;
          $dpmVatSum += $dp->VatSum;
        }
      }

      $arr = array(
        'order' => $order,
        'details' => $details,
        'dpmAmount' => $dpmAmount,
        'dpmVatSum' => $dpmVatSum,
        'title' => $title[$option]
      );

      switch($option)
      {
        case 'DO' :
          $this->load->view('print/print_delivery_invoice', $arr);
        break;
        case 'DIO' :
          $this->load->view('print/print_delivery_invoice', $arr);
        break;
        case 'DRO' :
          $this->load->view('print/print_delivery_receipt_invoice', $arr);
        break;
        case 'DTI' :
          $this->load->view('print/print_delivery_tax_invoice', $arr);
        break;
        case 'DTIN' :
          $this->load->view('print/print_delivery_tax_invoice_no_date', $arr);
        break;
        case 'DITI' :
          $this->load->view('print/print_delivery_bill_tax_invoice', $arr);
        break;
        case 'DITIN' :
          $this->load->view('print/print_delivery_bill_tax_invoice_no_date', $arr);
        break;
        case 'DRTI' :
          $this->load->view('print/print_delivery_receipt_tax_invoice', $arr);
        break;
        case 'DRTIN' :
          $this->load->view('print/print_delivery_receipt_tax_invoice_no_date', $arr);
        break;
      }
    }
    else
    {
      $this->page_error();
    }
  }

  public function get_new_code($prefix, $run_digit = 3, $date = NULL)
  {
    $date = empty($date) ? date('Y-m-d') : ($date < '2020-01-01' ? date('Y-m-d') : $date);
		$Y = date('y', strtotime($date));
    $M = date('m', strtotime($date));
    $pre = $prefix .'-'.$Y.$M;
    $code = $this->order_invoice_model->get_max_code($pre);

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

  public function getInvoicePrefixData($type = 'ORDER', $is_term = FALSE, $tax_status = 'N')
  {
    $ds = new stdClass();

    switch($type)
    {
      case 'POS' :
        $ds->prefix = $tax_status == 'Y' ? getConfig('PREFIX_POS_TAX_INVOICE') : getConfig('PREFIX_POS_INVOICE');
        $ds->bookcode = 'P';
        $ds->running = $tax_status == 'Y' ? getConfig('RUN_DIGIT_POS_TAX_INVOICE') : getConfig('RUN_DIGIT_POS_INVOICE');
      break;
      case 'ORDER' :
        if($is_term)
        {
          $ds->prefix = $tax_status == 'Y' ? getConfig('PREFIX_TERM_TAX_INVOICE') : getConfig('PREFIX_TERM_INVOICE');
          $ds->bookcode = 'T';
          $ds->running = $tax_status == 'Y' ? getConfig('RUN_DIGIT_TERM_TAX_INVOICE') : getConfig('RUN_DIGIT_TERM_INVOICE');
        }
        else
        {
          $ds->prefix = $tax_status == 'Y' ? getConfig('PREFIX_CASH_TAX_INVOICE') : getConfig('PREFIX_CASH_INVOICE');
          $ds->bookcode = 'C';
          $ds->running = $tax_status == 'Y' ? getConfig('RUN_DIGIT_CASH_TAX_INVOICE') : getConfig('RUN_DIGIT_CASH_INVOICE');
        }
      break;
    }

    return $ds;
  }


  function clear_filter()
  {
    $filter = array(
      'invoice_code',
      'invoice_bookcode',
      'invoice_reference',
      'invoice_so_code',
      'invoice_customer',
      'invoice_status',
      'tax_status',
      'invoice_from_date',
      'invoice_to_date',
      'invoice_sale_id',
      'invoice_user',
      'invoice_is_export'
    );

    return clear_filter($filter);
  }
}
 ?>
