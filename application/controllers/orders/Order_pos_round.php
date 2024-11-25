<?php
class Order_pos_round extends PS_Controller
{
  public $menu_code = 'SOPOSRD';
  public $menu_group_code = 'POS';
  public $menu_sub_group_code = '';
  public $title = 'รอบการขาย POS';
  public $segment = 5;

  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'orders/order_pos_round';
    $this->load->model('orders/order_pos_round_model');
    $this->load->model('orders/order_down_payment_model');
    $this->load->model('orders/order_pos_model');
    $this->load->model('orders/pos_sales_movement_model');
    $this->load->model('masters/shop_model');
    $this->load->model('masters/pos_model');
    $this->load->helper('shop');
    $this->load->helper('payment_method');
    $this->load->helper('order_pos');
  }

  public function index()
  {
    $filter = array(
      'shop_id' => get_filter('shop_id', 'rd_shop_id', 'all'),
      'pos_id' => get_filter('pos_id', 'rd_pos_id', 'all'),
      'code' => get_filter('code', 'rd_code', ''),
      'status' => get_filter('status', 'rd_status', 'all'),
      'open_from_date' => get_filter('open_from_date', 'rd_o_from_date', ''),
      'open_to_date' => get_filter('open_to_date', 'rd_o_to_date', ''),
      'close_from_date' => get_filter('close_from_date', 'rd_c_from_dte', ''),
      'close_to_date' => get_filter('close_to_date', 'rd_c_to_date', '')
    );

    if($this->input->post('search'))
    {
      redirect($this->home);
    }
    else
    {
      $perpage = get_rows();
      $rows = $this->order_pos_round_model->count_rows($filter);
      $filter['orders'] = $this->order_pos_round_model->get_list($filter, $perpage, $this->uri->segment($this->segment));
      $init = pagination_config($this->home.'/index/', $rows, $perpage, $this->segment);
      $this->pagination->initialize($init);
      $this->load->view('order_pos_round/order_pos_round_list', $filter);
    }
  }


  public function view_detail($id)
  {
    $this->load->model('orders/order_pos_return_model');

    $round = $this->order_pos_round_model->get($id);

    if( ! empty($round))
    {
      //--- get summary
      if($round->status == 'O')
      {
        $sm = $this->get_round_summary($round->pos_id, $round->id);
        $round_total = $sm->totalAmount + $sm->totalDownPayment + ($sm->totalReturn * -1); //--- ยอดรวมทั้งรอบ

        $round->close_cash = 0.00;
        $round->close_user = NULL;
        $round->close_date = NULL;
        $round->down_cash = $sm->totalCashDownPayment;
        $round->down_transfer = $sm->totalTransferDownPayment;
        $round->down_card = $sm->totalCardDownPayment;
        $round->cash_in = $sm->totalCashIn;
        $round->cash_out = $sm->totalCashOut;
        $round->return_cash = $sm->totalReturnCash * -1;
        $round->return_transfer = $sm->totalReturnTransfer * -1;
        $round->total_cash = $sm->totalCash;
        $round->total_transfer = $sm->totalTransfer;
        $round->total_card = $sm->totalCard;
        $round->round_total = $round_total;
      }

      $ds = array(
        'pos' => $this->pos_model->get_pos($round->pos_id),
        'round' => $round,
        'details' => $this->pos_sales_movement_model->get_movement_by_round_id($round->pos_id, $round->id)
      );

      $this->load->view('order_pos_round/order_pos_round_details', $ds);
    }
    else
    {
      $this->page_error();
    }
  }

  public function recalSummary($round_id)
  {
    $this->load->model('orders/order_pos_return_model');
    $sc = TRUE;

    $round = $this->order_pos_round_model->get($round_id);

    if( ! empty($round))
    {
      $sm = $this->get_round_summary($round->pos_id, $round->id);
      $round_total = $sm->totalAmount + $sm->totalDownPayment + ($sm->totalReturn * -1); //--- ยอดรวมทั้งรอบ

      if( ! empty($sm))
      {
        $arr = array(
          'down_cash' => $sm->totalCashDownPayment,
          'down_transfer' => $sm->totalTransferDownPayment,
          'down_card' => $sm->totalCardDownPayment,
          'cash_in' => $sm->totalCashIn, //--- นำเงินเข้าลิ้นชัก
          'cash_out' => $sm->totalCashOut, //--- นำเงินออกจากลิ้นชัก
          'return_cash' => $sm->totalReturnCash * -1, ///---- คืนเงินสด
          'return_transfer' => $sm->totalReturnTransfer * -1, //--- คืนเงินด้วยการโอน
          'total_cash' => $sm->totalCash, //-- pos total cash without down payment
          'total_transfer' => $sm->totalTransfer, //--- pos total transfer without down payment
          'total_card' => $sm->totalCard, //-- pos total credit card without down payment
          'round_total' => $round_total
        );

        if( ! $this->order_pos_round_model->update($round->id, $arr))
        {
          $sc = FALSE;
          $this->error = "Failed to update summary";
        }
      }
      else
      {
        $sc = FALSE;
        $this->error = "No summary data found";
      }
    }
    else
    {
      $sc = FALSE;
      $this->error = "Round id not found";
    }

    echo $sc === TRUE ? 'success' : $this->error;
  }


  public function get_round_summary($pos_id, $round_id)
  {
    //---- total order amount
    $arr = array(
      'totalAmount' => $this->order_pos_model->get_sum_amount_by_round_id($pos_id, $round_id),
      'totalDownPayment' => $this->order_down_payment_model->get_sum_amount_by_round_id($pos_id, $round_id),
      'totalReturn' => $this->order_pos_return_model->get_sum_amount_by_round_id($pos_id, $round_id),
      'totalCashDownPayment' => $this->order_down_payment_model->get_sum_role_amount_by_round_id($pos_id, 1, $round_id),
      'totalTransferDownPayment' => $this->order_down_payment_model->get_sum_role_amount_by_round_id($pos_id, 2, $round_id),
      'totalCardDownPayment' => $this->order_down_payment_model->get_sum_role_amount_by_round_id($pos_id, 3, $round_id),
      'totalCash' => $this->order_pos_model->get_sum_role_amount_by_round_id($pos_id, 1, $round_id),
      'totalTransfer' => $this->order_pos_model->get_sum_role_amount_by_round_id($pos_id, 2, $round_id),
      'totalCard' => $this->order_pos_model->get_sum_role_amount_by_round_id($pos_id, 3, $round_id),
      'totalReturnCash' => $this->order_pos_return_model->get_sum_role_amount_by_round_id($pos_id, 1, $round_id),
      'totalReturnTransfer' => $this->order_pos_return_model->get_sum_role_amount_by_round_id($pos_id, 2, $round_id),
      'totalCashIn' => $this->pos_sales_movement_model->get_sum_cash_in_by_round_id($pos_id, $round_id),
      'totalCashOut' => $this->pos_sales_movement_model->get_sum_cash_out_by_round_id($pos_id, $round_id)
    );

    return (object) $arr;
  }

  public function print_pos_round($id)
  {
    $round = $this->order_pos_round_model->get($id);

    if( ! empty($round))
    {
      $ds = array(
        'pos' => $this->pos_model->get_pos($round->pos_id),
        'order' => $round,
        'details' => $this->pos_sales_movement_model->get_movement_by_round_id($round->pos_id, $round->id)
      );

      $this->load->library('xprinter');
      $this->load->view('print/print_pos_round', $ds);
    }
    else
    {
      $this->page_error();
    }
  }



  public function clear_filter()
  {
    $filter = array(
      'rd_shop_id',
      'rd_pos_id',
      'rd_code',
      'rd_order_code',
      'rd_bill_code',
      'rd_payment',
      'rd_status',
      'rd_o_from_date',
      'rd_o_to_date',
      'rd_c_from_dte',
      'rd_c_to_date'
    );

    return clear_filter($filter);
  }
}

 ?>
