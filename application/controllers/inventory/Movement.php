<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Movement extends PS_Controller
{
  public $menu_code = 'ICCKMV';
	public $menu_group_code = 'IC';
  public $menu_sub_group_code = 'CHECK';
	public $title = 'ตรวจสอบ Movement';
  public $filter;
  public $error;
  public $segment = 4;

  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'inventory/movement';
    $this->load->model('inventory/movement_model');
  }


  public function index()
  {
    if($this->input->post())
    {
      $filter = array(
        'reference' => get_filter('reference', 'mv_reference', ''),
        'warehouse_code' => get_filter('warehouse_code', 'mv_warehouse_code', ''),
        'zone_code' => get_filter('zone_code', 'mv_zone_code', ''),
        'product_code' => get_filter('product_code', 'mv_product_code', ''),
        'from_date' => get_filter('from_date', 'mv_from_date', ''),
        'to_date' => get_filter('to_date', 'mv_to_date', '')
      );

      $perpage = get_rows();

      $rows = $this->movement_model->count_rows($filter);
      $init = pagination_config($this->home.'/index/', $rows, $perpage, $this->segment);
      $this->pagination->initialize($init);
      $filter['data'] = $this->movement_model->get_list($filter, $perpage, $this->uri->segment($this->segment));
    }
    else
    {
      $filter = array(
        'reference' => '',
        'warehouse_code' => '',
        'zone_code' => '',
        'product_code' => '',
        'from_date' => '',
        'to_date' => ''
      );
    }

    $this->load->view('inventory/movement/movement_list', $filter);
  }


  public function clear_filter()
  {
    $filter = array(
      'mv_reference',
      'mv_warehouse_code',
      'mv_zone_code',
      'mv_product_code',
      'mv_from_date',
      'mv_to_date'
    );

    return clear_filter($filter);
  }

} // end class
?>
