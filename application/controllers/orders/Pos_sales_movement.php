<?php
class Pos_sales_movement extends PS_Controller
{
  public $menu_code = 'SOPOSMV';
  public $menu_group_code = 'POS';
  public $menu_sub_group_code = '';
  public $title = 'POS Sales Movement';
  public $segment = 5;

  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'orders/pos_sales_movement';
    $this->load->model('orders/order_pos_round_model');
    $this->load->model('orders/order_down_payment_model');
    $this->load->model('orders/order_pos_model');
    $this->load->model('orders/pos_sales_movement_model');
    $this->load->model('masters/shop_model');
    $this->load->model('masters/pos_model');
    $this->load->helper('shop');
    $this->load->helper('payment_method');
    $this->load->helper('order_pos');
    $this->load->helper('bank');
  }

  public function index()
  {
    $filter = array(
      'code' => get_filter('code', 'sv_code', ''),
      'round_code' => get_filter('round_code', 'sv_round_code', ''),
      'shop_id' => get_filter('shop_id', 'sv_shop_id', 'all'),
      'pos_id' => get_filter('pos_id', 'sv_pos_id', 'all'),
      'role' => get_filter('role', 'sv_role', 'all'),
      'type' => get_filter('type', 'sv_type', 'all'),
      'bank' => get_filter('bank', 'sv_bank', 'all'),
      'from_date' => get_filter('from_date', 'sv_from_date', ''),
      'to_date' => get_filter('to_date', 'sv_to_date', '')
    );

    if($this->input->post('search'))
    {
      redirect($this->home);
    }
    else
    {
      $perpage = get_rows();
      $rows = $this->pos_sales_movement_model->count_rows($filter);
      $filter['details'] = $this->pos_sales_movement_model->get_list($filter, $perpage, $this->uri->segment($this->segment));
      $init = pagination_config($this->home.'/index/', $rows, $perpage, $this->segment);
      $this->pagination->initialize($init);
      $this->load->view('pos_sales_movement/pos_sales_movement_list', $filter);
    }
  }



  public function clear_filter()
  {
    $filter = array(
      'sv_code',
      'sv_round_code',
      'sv_shop_id',
      'sv_pos_id',
      'sv_type',
      'sv_bank',
      'sv_role',
      'sv_from_date',
      'sv_to_date'
    );

    return clear_filter($filter);
  }
}

 ?>
