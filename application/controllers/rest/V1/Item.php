<?php
require(APPPATH.'/libraries/REST_Controller.php');
use Restserver\Libraries\REST_Controller;

class Item extends REST_Controller
{
  public $error;
  public $user;

  public function __construct()
  {
    parent::__construct();

    $this->load->model('masters/products_model');
    $this->user = 'api@warrix';
  }


  public function index_get($code)
  {
    if(empty($code))
    {
      $arr = array(
        'status' => FALSE,
        'error' => "Item code is required"
      );

      $this->response($arr, 400);
    }

		$item = $this->products_model->get_attribute($code);

		if(!empty($item))
		{
			$ds = array(
				'status' => 'success',
				'data' => array(
					'code' => $item->code,
					'name' => $item->name,
					'barcode' => $item->barcode,
					'price' => $item->price,
					'unit_code' => $item->unit_code,
					'count_stock' => $item->count_stock,
					'style_code' => $item->style_code,
					'color_code' => $item->color_code,
					'color_name' => $item->color_name,
					'size_code' => $item->size_code,
					'size_name' => $item->size_name,
					'group_code' => $item->group_code,
					'group_name' => $item->group_name,
					'sub_group_code' => $item->sub_group_code,
					'sub_group_name' => $item->sub_group_name,
					'category_code' => $item->category_code,
					'category_name' => $item->category_name,
					'kind_code' => $item->kind_code,
					'kind_name' => $item->kind_name,
					'type_code' => $item->type_name,
					'brand_code' => $item->brand_code,
					'brand_name' => $item->brand_name,
					'year' => $item->year,
					'active' => $item->active == 1 ? 'Y' : 'N'
				)
			);

			$this->response($ds, 200);
		}
		else
		{
			$arr = array(
        'status' => FALSE,
        'error' => "Item code not found"
      );

      $this->response($arr, 400);
		}

  }



	public function countUpdateItem_get()
	{
		$json = file_get_contents("php://input");
		$data = json_decode($json);

		if(! empty($data))
		{
			$date = empty($data->date) ? '2020-01-01 00:00:00' : $data->date;

			$rs = $this->db
			->where('date_upd >=', $date)
			->order_by('code', 'ASC')
			->count_all_results('products');

			$arr = array(
				'status' => TRUE,
				'count' => $rs
			);

			$this->response($arr, 200);
		}
		else
		{
			$arr = array(
				'status' => FALSE,
				'error' => 'Missing required parameter'
			);

			$this->response($arr, 400);
		}

	}


	public function getUpdateItem_get()
	{
		$json = file_get_contents("php://input");
		$ds = json_decode($json);

		if(! empty($ds))
		{
			$date = $ds->date;
			$limit = $ds->limit;
			$offset = $ds->offset;

			$data = $this->db
			->where('date_upd >=', $date)
			->order_by('code', 'ASC')
			->limit($limit, $offset)
			->get('products');

			$items = array();

			if($data->num_rows() > 0)
			{
				foreach($data->result() as $rs)
				{
					$arr = array(
						'code' => $rs->code,
						'name' => $rs->name,
						'barcode' => $rs->barcode,
						'style_code' => $rs->style_code,
						'color_code' => $rs->color_code,
						'size_code' => $rs->size_code,
						'group_code' => $rs->group_code,
						'main_group_code' => $rs->main_group_code,
						'sub_group_code' => $rs->sub_group_code,
						'category_code' => $rs->category_code,
						'type_code' => $rs->type_code,
						'kind_code' => $rs->kind_code,
						'brand_code' => $rs->brand_code,
						'year' => $rs->year,
						'cost' => $rs->cost,
						'price' => $rs->price,
						'unit_code' => $rs->unit_code,
						'count_stock' => $rs->count_stock,
            'old_code' => $rs->old_code
					);

					array_push($items, $arr);
				}
			}

			$arr = array(
				'status' => TRUE,
				'rows' => $data->num_rows(),
				'items' => $items
			);

			$this->response($arr, 200);
		}
		else
		{
			$arr = array(
				'status' => FALSE,
				'error' => 'Missing required parameter'
			);

			$this->response($arr, 400);
		}
	}


  public function getUpdateItemGroup_get()
  {
    $items = array();
    $data = $this->db->get('product_group');

    if($data->num_rows() > 0)
    {
      foreach($data->result() as $rs)
      {
        $ds = array(
          'code' => $rs->code,
          'name' => $rs->name
        );

        array_push($items, $ds);
      }
    }

    $arr = array(
      'status' => TRUE,
      'rows' => $data->num_rows(),
      'items' => $items
    );

    $this->response($arr, 200);
  }


  public function getUpdateItemMainGroup_get()
  {
    $items = array();
    $data = $this->db->get('product_main_group');

    if($data->num_rows() > 0)
    {
      foreach($data->result() as $rs)
      {
        $ds = array(
          'code' => $rs->code,
          'name' => $rs->name
        );

        array_push($items, $ds);
      }
    }

    $arr = array(
      'status' => TRUE,
      'rows' => $data->num_rows(),
      'items' => $items
    );

    $this->response($arr, 200);
  }

  public function getUpdateItemSubGroup_get()
  {
    $items = array();
    $data = $this->db->get('product_sub_group');

    if($data->num_rows() > 0)
    {
      foreach($data->result() as $rs)
      {
        $ds = array(
          'code' => $rs->code,
          'name' => $rs->name
        );

        array_push($items, $ds);
      }
    }

    $arr = array(
      'status' => TRUE,
      'rows' => $data->num_rows(),
      'items' => $items
    );

    $this->response($arr, 200);
  }


  public function getUpdateItemCategory_get()
  {
    $items = array();
    $data = $this->db->get('product_category');

    if($data->num_rows() > 0)
    {
      foreach($data->result() as $rs)
      {
        $ds = array(
          'code' => $rs->code,
          'name' => $rs->name
        );

        array_push($items, $ds);
      }
    }

    $arr = array(
      'status' => TRUE,
      'rows' => $data->num_rows(),
      'items' => $items
    );

    $this->response($arr, 200);
  }


  public function getUpdateItemBrand_get()
  {
    $items = array();
    $data = $this->db->get('product_brand');

    if($data->num_rows() > 0)
    {
      foreach($data->result() as $rs)
      {
        $ds = array(
          'code' => $rs->code,
          'name' => $rs->name
        );

        array_push($items, $ds);
      }
    }

    $arr = array(
      'status' => TRUE,
      'rows' => $data->num_rows(),
      'items' => $items
    );

    $this->response($arr, 200);
  }


  public function getUpdateItemKind_get()
  {
    $items = array();
    $data = $this->db->get('product_kind');

    if($data->num_rows() > 0)
    {
      foreach($data->result() as $rs)
      {
        $ds = array(
          'code' => $rs->code,
          'name' => $rs->name
        );

        array_push($items, $ds);
      }
    }

    $arr = array(
      'status' => TRUE,
      'rows' => $data->num_rows(),
      'items' => $items
    );

    $this->response($arr, 200);
  }


  public function getUpdateItemType_get()
  {
    $items = array();
    $data = $this->db->get('product_type');

    if($data->num_rows() > 0)
    {
      foreach($data->result() as $rs)
      {
        $ds = array(
          'code' => $rs->code,
          'name' => $rs->name
        );

        array_push($items, $ds);
      }
    }

    $arr = array(
      'status' => TRUE,
      'rows' => $data->num_rows(),
      'items' => $items
    );

    $this->response($arr, 200);
  }


  public function getUpdateItemColorGroup_get()
  {
    $items = array();
    $data = $this->db->get('product_color_group');

    if($data->num_rows() > 0)
    {
      foreach($data->result() as $rs)
      {
        $ds = array(
          'id' => $rs->id,
          'code' => $rs->code,
          'name' => $rs->name
        );

        array_push($items, $ds);
      }
    }

    $arr = array(
      'status' => TRUE,
      'rows' => $data->num_rows(),
      'items' => $items
    );

    $this->response($arr, 200);
  }


  public function getUpdateItemColor_get()
  {
    $items = array();
    $data = $this->db->get('product_color');

    if($data->num_rows() > 0)
    {
      foreach($data->result() as $rs)
      {
        $ds = array(
          'code' => $rs->code,
          'name' => $rs->name,
          'active' => $rs->active,
          'id_group' => $rs->id_group
        );

        array_push($items, $ds);
      }
    }

    $arr = array(
      'status' => TRUE,
      'rows' => $data->num_rows(),
      'items' => $items
    );

    $this->response($arr, 200);
  }


  public function getUpdateItemSize_get()
  {
    $items = array();
    $data = $this->db->get('product_size');

    if($data->num_rows() > 0)
    {
      foreach($data->result() as $rs)
      {
        $ds = array(
          'code' => $rs->code,
          'name' => $rs->name,
          'position' => $rs->position,
          'active' => $rs->active
        );

        array_push($items, $ds);
      }
    }

    $arr = array(
      'status' => TRUE,
      'rows' => $data->num_rows(),
      'items' => $items
    );

    $this->response($arr, 200);
  }


  public function getUpdateItemStyle_get()
  {
    $items = array();
    $data = $this->db->get('product_style');

    if($data->num_rows() > 0)
    {
      foreach($data->result() as $rs)
      {
        $ds = array(
          'code' => $rs->code,
          'name' => $rs->name
        );

        array_push($items, $ds);
      }
    }

    $arr = array(
      'status' => TRUE,
      'rows' => $data->num_rows(),
      'items' => $items
    );

    $this->response($arr, 200);
  }


  public function getUpdateUom_get()
  {
    $items = array();
    $data = $this->db->get('uom');

    if($data->num_rows() > 0)
    {
      foreach($data->result() as $rs)
      {
        $ds = array(
          'code' => $rs->code,
          'name' => $rs->name
        );

        array_push($items, $ds);
      }
    }

    $arr = array(
      'status' => TRUE,
      'rows' => $data->num_rows(),
      'items' => $items
    );

    $this->response($arr, 200);
  }

} //--- end class
