<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Sync_wms_items extends PS_Controller
{
  public $title = 'Sync Items';
	public $menu_code = 'PDSYNC';
	public $menu_group_code = 'DB';
  public $menu_sub_group_code = 'PRODUCT';
	public $wms;
  public $limit = 100;
  public $date;

  public function __construct()
  {
    parent::__construct();
    _check_login();
		$this->pm = new stdClass();
		$this->pm->can_view = 1;

    $this->wms = $this->load->database('wms', TRUE);
		$this->load->library('wms_product_api');
  }

  public function index()
  {
    $this->load->view('sync_wms_products_view');
  }


  public function count_update_items()
  {
		//echo 1000;
		echo $this->db->where('barcode IS NOT NULL', NULL, FALSE)->where('count_stock', 1)->count_all_results('products');
  }

	public function get_update_list($limit = 100, $offset = 0)
	{
		$rs = $this->db
		->select('code, name, barcode, unit_code')
		->where('barcode IS NOT NULL', NULL, FALSE)
		->where('count_stock', 1)
		->order_by('code', 'ASC')
		->limit($limit, $offset)
		->get('products');

		if($rs->num_rows() > 0)
		{
			return $rs->result();
		}

		return NULL;
	}


  public function get_update_items($offset)
  {
    $list = $this->get_update_list($this->limit, $offset);

    $count = 0;

    if(!empty($list))
    {
			$rs = $this->wms_product_api->export_items($list);

			if($rs)
			{
				$count = count($list);
			}
			else
			{
				echo $this->wms_product_api->error;
				exit();
			}
    }

    echo $count;
  }

} //--- end class

 ?>
