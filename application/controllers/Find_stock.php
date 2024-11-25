<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Find_stock extends PS_Controller
{
  public $menu_code = 'SOFNST';
	public $menu_group_code = 'SO';
	public $title = 'ค้นหาสินค้าคงเหลือ';
  public $error = '';

  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'find_stock';
    //--- load model
    $this->load->model('masters/products_model');
    $this->load->model('masters/product_group_model');
    $this->load->model('masters/product_kind_model');
    $this->load->model('masters/product_type_model');
    $this->load->model('masters/product_style_model');
    $this->load->model('masters/product_brand_model');
    $this->load->model('masters/product_category_model');
    $this->load->model('masters/product_color_model');
    $this->load->model('masters/product_size_model');
    $this->load->model('masters/product_image_model');
    $this->load->model('stock/stock_model');
    $this->load->model('orders/orders_model');

    //---- load helper
    $this->load->helper('product_tab');
    $this->load->helper('product_brand');
    $this->load->helper('product_kind');
    $this->load->helper('product_type');
    $this->load->helper('product_group');
    $this->load->helper('product_category');
    $this->load->helper('product_images');
    $this->load->helper('product_color');
    $this->load->helper('warehouse');
  }


  public function index()
  {
    $default_warehouse = getConfig('DEFAULT_WAREHOUSE');

    $filter = array(
      'code' => get_filter('code', 'code', ''),
      'name' => get_filter('name', 'name', ''),
      'size' => get_filter('size', 'size', ''),
      'operater' => get_filter('operater', 'operater', ''),
      'price' => get_filter('price', 'price', ''),
      'warehouse' => get_filter('warehouse', 'warehouse', ''),
      'color_group' => get_filter('color_group', 'color_group' ,''),
      'group' => get_filter('group', 'group', ''),
      'sub_group' => get_filter('sub_group', 'sub_group', ''),
      'category'  => get_filter('category', 'category', ''),
      'kind'  => get_filter('kind', 'kind', ''),
      'type'  => get_filter('type', 'type', ''),
      'brand' => get_filter('brand', 'brand', ''),
      'year'  => get_filter('year', 'year', '')
    );

    $i = 0;

    foreach($filter AS $rx)
    {
      if(!empty($rx))
      {
        $i++;
      }
    }


		//--- แสดงผลกี่รายการต่อหน้า
		$perpage = 1000;

		$segment  = 3; //-- url segment

		$rows = 0;

		//--- ส่งตัวแปรเข้าไป 4 ตัว base_url ,  total_row , perpage = 20, segment = 3
		$products = $i > 1 ? $this->products_model->get_list($filter, $perpage, $this->uri->segment($segment)) : NULL;

    $data = array();

    if( ! empty($products))
    {
      $warehouse = get_null($filter['warehouse']);

      foreach($products as $rs)
      {
        $sell_stock = $this->stock_model->get_sell_stock($rs->code, $warehouse, NULL);

        if($sell_stock > 0)
        {
          $reserv_stock = $this->orders_model->get_reserv_stock($rs->code, $warehouse, NULL);

          $availableStock = $sell_stock - $reserv_stock;

          if($availableStock > 0)
          {
            $rs->OnHand = $sell_stock;
            $rs->ordered = $reserv_stock;
            $rs->balance = $availableStock;
            $data[] = $rs;
            $rows++;
          }
        }
      }
    }

    $filter['data'] = $data;

    $init	= pagination_config($this->home.'/index/', $rows, $perpage, $segment);
		$this->pagination->initialize($init);
    $this->load->view('find_stock_view', $filter);
  }


  public function clear_filter()
	{
    $filter = array(
      'code',
      'name',
      'size',
      'operater',
      'price',
      'warehouse',
      'color_group',
      'group',
      'sub_group',
      'category',
      'kind',
      'type',
      'brand',
      'year');
    clear_filter($filter);
	}
}

?>
