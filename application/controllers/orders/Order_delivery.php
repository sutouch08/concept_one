<?php
class Order_delivery extends PS_Controller
{
  public $menu_code = 'SOARDO';
  public $menu_group_code = 'AC';
  public $menu_sub_group_code = '';
  public $title = 'Delivery';
  public $segment = 4;

  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'orders/order_delivery';
    $this->load->model('orders/delivery_model');
    $this->load->model('orders/order_down_payment_model');
    $this->load->model('inventory/invoice_model');

    $this->load->helper('warehouse');
    $this->load->helper('channels');
    $this->load->helper('saleman');
    $this->load->helper('discount');
  }

  public function index()
  {
    $filter = array(
      'code' => get_filter('code', 'do_code', ''),
      'is_term' => get_filter('is_term', 'do_is_term', 'all'),
      'reference' => get_filter('reference', 'do_reference', ''),
      'so_code' => get_filter('so_code', 'do_so_code', ''),
      'invoice_code' => get_filter('invoice_code', 'do_invoice_code', ''),
      'customer' => get_filter('customer', 'do_customer', ''),
      'status' => get_filter('status', 'do_status', 'O'), //--- O = invoice_code IS NULL, C = invoice_code IS NOT NULL
      'tax_status' => get_filter('tax_status', 'do_tax_status', 'all'),
      'sale_code' => get_filter('sale_code', 'do_sale_code', 'all'),
      'user' => get_filter('user', 'do_user', 'all'),
      'from_date' => get_filter('fromDate', 'do_from_date', ''),
      'channels' => get_filter('channels', 'do_channels', 'all'),
      'warehouse' => get_filter('warehouse', 'do_warehouse', 'all'),
      'to_date' => get_filter('toDate', 'do_to_date', '')
    );

    if($this->input->post('search'))
    {
      redirect($this->home);
    }
    else
    {
      $perpage = get_rows();
      $rows = $this->delivery_model->count_rows($filter);
      $filter['orders'] = $this->delivery_model->get_list($filter, $perpage, $this->uri->segment($this->segment));
      $init = pagination_config($this->home.'/index/', $rows, $perpage, $this->segment);
      $this->pagination->initialize($init);
      $this->load->view('order_delivery/delivery_list', $filter);
    }
  }


  public function view_detail($code)
  {
    $order = $this->delivery_model->get($code);

    if( ! empty($order))
    {
      $totalQty = 0;
      $totalBfDisc = 0;
      $bDiscAmount = 0;
      $totalAfDisc = 0;
      $totalVat = 0;

      $downPayment = [];
      $downPaymentList = [];
      $downPaymentAvailable = 0;
      $downPaymentUse = 0;

      $details = $this->delivery_model->get_details($code); //--- รายการที่มีการบันทึกขายไป

      if( ! empty($details))
      {
        $no = 1;

        foreach($details as $rs)
        {
          $rs->no = $no;
          $rs->vat_code = $rs->VatCode;
          $rs->vat_rate = $rs->VatRate;
          $rs->vat_type = $rs->VatType;
          $rs->DiscPrcnt = discountAmountToPercent($rs->discount_amount, $rs->qty, $rs->price);
          $rs->PriceBefDi = $rs->VatType == 'E' ? $rs->price : remove_vat($rs->price, $rs->VatRate);
          $rs->PriceBfVAT = $rs->VatType == 'E' ? $rs->sell : remove_vat($rs->sell, $rs->VatRate);

          $totalQty += $rs->qty;
          $totalBfDisc += $rs->total_amount;
          $bDiscAmount += $rs->sumBillDiscAmount;
          $totalAfDisc += $rs->total_amount - $rs->sumBillDiscAmount;
          $totalVat += $rs->VatSum;
          $no++;
        }

        $DocTotal = $order->vat_type == 'E' ? $totalAfDisc + $totalVat : $totalAfDisc;

        $order->totalQty = round($totalQty, 2);
        $order->TotalBfDisc = round($totalBfDisc, 2);
        $order->TotalAfDisc = round($totalAfDisc, 2);
        $order->DocTotal = round($DocTotal, 2);
        $order->DiscSum = round($bDiscAmount, 2);
        $order->VatSum = round($totalVat, 2);
        $order->WhtAmount = $order->WhtPrcnt > 0 ? ($order->vat_type == 'E' ? $order->TotalAfDisc * ($order->WhtPrcnt * 0.01) : ($order->TotalAfDisc - $order->VatSum) * ($order->WhtPrcnt * 0.01)) : 0;

        if(empty($order->invoice_code))
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

          $downPaymentList = $dps;
        }
        else
        {
          $downPayment = $this->order_down_payment_model->get_details_by_target($code);
        }
      }
      else
      {
        $sc = FALSE;
        $this->error = "ไม่พบรายการบันทึกขาย";
      }

      $ds = array(
        'doc' => $order,
        'details' => $details,
        'down_payment_list' => $downPaymentList,
        'downPayment' => $downPayment,
        'downPaymentAvailable' => round($downPaymentAvailable, 2),
        'downPaymentUse' => round($downPaymentUse, 2)
      );

      $this->load->view('order_delivery/delivery_detail', $ds);
    }
    else
    {
      $this->page_error();
    }
  }


  function clear_filter()
  {
    $filter = array(
      'do_code',
      'do_is_term',
      'do_reference',
      'do_so_code',
      'do_invoice_code',
      'do_customer',
      'do_status', //--- O = invoice_code IS NULL, C = invoice_code IS NOT NULL
      'do_tax_status',
      'do_sale_code',
      'do_user',
      'do_channels',
      'do_warehouse',
      'do_from_date',
      'do_to_date'
    );

    return clear_filter($filter);
  }
}
 ?>
