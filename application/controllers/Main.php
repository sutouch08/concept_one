<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Main extends PS_Controller
{
	public $title = 'Welcome';
	public $menu_code = '';
	public $menu_group_code = '';
	public $error;

	public function __construct()
	{
		parent::__construct();
		_check_login();
		$this->pm = new stdClass();
		$this->pm->can_view = 1;
    $this->load->model('main_model');
		$this->load->helper('warehouse');
		$this->load->helper('product_color');
	}


	public function index()
	{
		if($this->isViewer)
		{
			redirect('view_stock');
		}
		else
		{
			$this->load->view('main_view');
		}

	}


  public function find_order()
  {
    $sc = array();
    $txt = $this->input->post('search_text');
		$warehouse = get_null($this->input->post('warehouse_code'));

    if(!empty($txt))
    {

      $limit = 100; //--- limit result
      $list = $this->main_model->get_search_order($txt, $warehouse, $limit);

      if(!empty($list))
      {
        foreach($list as $rs)
        {
          $arr = array(
            'pdCode' => $rs->product_code,
						'oldCode' => $rs->old_code,
            'reference' => $rs->code,
            'qty' => number($rs->qty),
            'state' => $rs->state,
            'cusName' => $rs->customer_name,
            'empName' => $rs->user
          );

          array_push($sc, $arr);
        }
      }
      else
      {
        $arr = array('nodata' => 'nocontent');
    		array_push($sc, $arr);
      }
    }

    echo json_encode($sc);
  }



  public function get_sell_items_stock()
  {
    $sc = array();
    $txt = trim($this->input->post('search_text'));
		$warehouse = get_null($this->input->post('warehouse_code'));
    if(!empty($txt))
    {
      $list = $this->main_model->search_items_list($txt);
      if(!empty($list))
      {
        $this->load->model('stock/stock_model');
        $this->load->model('orders/orders_model');
        $this->load->model('inventory/buffer_model');
        $this->load->model('inventory/cancle_model');
        $this->load->model('inventory/transfer_model');
        $this->load->model('inventory/move_model');
        $this->load->helper('product_images');

        $useSize = 'mini';

        foreach($list as $rs)
        {
          //---	stock in zone
    			$stockLabel = '';

    			//--- จำนวนคงเหลือทั้งหมด
    			$qty = 0; //$bfQty + $cnQty + $mvQty + $trQty;

    			//---- get data from database
    			$stock_in_zone = $this->stock_model->get_stock_in_zone($rs->code, $warehouse);

    			if(!empty($stock_in_zone))
    			{
            foreach($stock_in_zone as $zone)
            {
              if($zone->qty != 0)
              {
                $name = empty($zone->name) ? $zone->code : $zone->name;
                $stockLabel .= $name.' ='.number($zone->qty).' <br/>';
                $qty += $zone->qty;
              }
            }
    			}


    			if($qty > 0)
    			{
    				$arr = array(
    					'img' => '<img src="'.get_product_image($rs->code, $useSize).'" />',
    					'pdCode' => $rs->code,
    					'pdName' => $rs->name,
    					'qty' => number($qty),
    					'stockInZone' => $stockLabel
    				);

    				array_push($sc, $arr);
    			}
        }
      }
    }

    echo json_encode($sc);
  }

} //--- end class
