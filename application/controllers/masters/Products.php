<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Products extends PS_Controller
{
  public $menu_code = 'DBPROD';
	public $menu_group_code = 'DB';
  public $menu_sub_group_code = 'PRODUCT';
	public $title = 'เพิ่ม/แก้ไข สินค้า';
  public $error = '';

  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'masters/products';
    //--- load model
    $this->load->model('masters/products_model');
    $this->load->model('masters/product_group_model');
    $this->load->model('masters/product_main_group_model');
    $this->load->model('masters/product_sub_group_model');
    $this->load->model('masters/product_kind_model');
    $this->load->model('masters/product_type_model');
    $this->load->model('masters/product_style_model');
    $this->load->model('masters/product_brand_model');
    $this->load->model('masters/product_category_model');
    $this->load->model('masters/product_color_model');
    $this->load->model('masters/product_size_model');
    $this->load->model('masters/product_tab_model');
    $this->load->model('masters/product_image_model');
    $this->load->model('masters/vat_model');

    //---- load helper
    $this->load->helper('product_brand');
    $this->load->helper('product_tab');
    $this->load->helper('product_kind');
    $this->load->helper('product_type');
    $this->load->helper('product_group');
		$this->load->helper('product_main_group');
    $this->load->helper('product_category');
    $this->load->helper('product_sub_group');
    $this->load->helper('product_images');
    $this->load->helper('unit');
    $this->load->helper('vat');
  }


  public function index()
  {
    $filter = array(
      'code' => get_filter('code', 'pd_code', ''),
      'name' => get_filter('name', 'pd_name', ''),
      'group' => get_filter('group', 'pd_group', 'all'),
			'main_group' => get_filter('main_group', 'pd_main_group', 'all'),
      'sub_group' => get_filter('sub_group', 'pd_sub_group', 'all'),
      'category' => get_filter('category', 'pd_category', 'all'),
      'kind' => get_filter('kind', 'pd_kind', 'all'),
      'type' => get_filter('type', 'pd_type', 'all'),
      'brand' => get_filter('brand', 'pd_brand', 'all'),
      'year' => get_filter('year', 'pd_year', 'all'),
      'sell' => get_filter('sell', 'pd_sell', 'all'),
      'active' => get_filter('active', 'pd_active', 'all')
    );

    if($this->input->post('search'))
    {
      redirect($this->home);
    }
    else
    {
      //--- แสดงผลกี่รายการต่อหน้า
  		$perpage = get_rows();
  		//--- หาก user กำหนดการแสดงผลมามากเกินไป จำกัดไว้แค่ 300
  		if($perpage > 300)
  		{
  			$perpage = 20;
  		}

  		$segment  = 4; //-- url segment
  		$rows     = $this->product_style_model->count_rows($filter);
  		//--- ส่งตัวแปรเข้าไป 4 ตัว base_url ,  total_row , perpage = 20, segment = 3
  		$init	    = pagination_config($this->home.'/index/', $rows, $perpage, $segment);
  		$products = $this->product_style_model->get_data($filter, $perpage, $this->uri->segment($segment));
      $ds       = array();
      if(!empty($products))
      {
        foreach($products as $rs)
        {
          $product = new stdClass();
          $product->id = $rs->id;
          $product->code    = $rs->code;
          $product->old_code = $rs->old_code;
          $product->name    = $rs->name;
          $product->price   = $rs->price;
          $product->group   = $this->product_group_model->get_name($rs->group_code);
          $product->main_group = $this->product_main_group_model->get_name($rs->main_group_code);
          $product->sub_group = $this->product_sub_group_model->get_name($rs->sub_group_code);
          $product->kind    = $this->product_kind_model->get_name($rs->kind_code);
          $product->type    = $this->product_type_model->get_name($rs->type_code);
          $product->category  = $this->product_category_model->get_name($rs->category_code);
          $product->brand   = $this->product_brand_model->get_name($rs->brand_code);
          $product->year    = $rs->year;
          $product->sell    = $rs->can_sell;
          $product->active  = $rs->active;
          $product->api     = $rs->is_api;
          $product->date_upd = $rs->date_upd;

          $ds[] = $product;
        }
      }

      $filter['data'] = $ds;

  		$this->pagination->initialize($init);
      $this->load->view('masters/products/products_view', $filter);
    }
  }


  public function export_filter()
  {
    ini_set('memory_limit','2048M'); // This also needs to be increased in some cases. Can be changed to a higher value as per need)

    $token = $this->input->post('token');

    $ds = array(
      'code' => $this->input->post('export_code'),
      'name' => $this->input->post('export_name'),
      'group' => $this->input->post('export_group'),
      'main_group' => $this->input->post('export_main_group'),
      'sub_group' => $this->input->post('export_sub_group'),
      'category' => $this->input->post('export_category'),
      'kind' => $this->input->post('export_kind'),
      'type' => $this->input->post('export_type'),
      'brand' => $this->input->post('export_brand'),
      'year' => $this->input->post('export_year'),
      'sell' => $this->input->post('export_sell'),
      'active' => $this->input->post('export_active')
    );

    $header = array(
      'Code', 'Name', 'Barcode', 'Model', 'Color', 'Size', 'Group', 'MainGroup',
      'SubGroup', 'Category', 'Kind', 'Type', 'Brand', 'Year','Cost','Price',
      'Unit', 'CountStock', 'IsAPI', 'OldModel', 'OldCode', 'IsActive', 'Sell'
    );

    $products = $this->products_model->get_products_list($ds);

    // Create a file pointer
    $f = fopen('php://memory', 'w');
    $delimiter = ",";
    fputs($f, $bom = ( chr(0xEF) . chr(0xBB) . chr(0xBF) ));
    fputcsv($f, $header, $delimiter);

    if(!empty($products))
    {

      foreach($products as $rs)
      {
        $count_stock = $rs->count_stock == 1 ? 'Y' : 'N';
        $is_active = $rs->active == 1 ? 'Y' : 'N';
        $is_api = $rs->is_api == 1 ? 'Y' : 'N';
        $is_sell = $rs->can_sell == 1 ? 'Y' : 'N';

        $arr = array(
          $rs->code, $rs->name, $rs->barcode, $rs->style_code, $rs->color_code, $rs->size_code,
          $rs->group_code, $rs->main_group_code, $rs->sub_group_code, $rs->category_code,
          $rs->kind_code, $rs->type_code, $rs->brand_code, $rs->year, $rs->cost,
          $rs->price, $rs->unit_code, $count_stock, $is_api, $rs->old_style, $rs->old_code, $is_active, $is_sell
        );

        fputcsv($f, $arr, $delimiter);
      }

      $memuse = (memory_get_usage() / 1024) / 1024;
      $arr = array('memory usage', round($memuse, 2).' MB');

      fputcsv($f, $arr, $delimiter);
    }

    //--- Move to begin of file
    fseek($f, 0);

    setToken($token);

    $file_name = "Products Master.csv";
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment;filename="'.$file_name.'"');

    //output all remaining data on a file pointer
    fpassthru($f); ;

    exit();

  }


  public function add_new()
  {
    $this->load->view('masters/products/products_add_view');
  }


  public function add_style()
  {
    if($this->input->post('code'))
    {
      $code     = trim($this->input->post('code')); //--- ตัดช่องว่างหัว-ท้าย
      $name     = addslashes(trim($this->input->post('name'))); //--- escape string
      $group    = get_null($this->input->post('group_code'));
			$main_group = get_null($this->input->post('main_group_code'));
      $sub_group = get_null($this->input->post('sub_group_code'));
      $category = get_null($this->input->post('category_code'));
      $kind     = get_null($this->input->post('kind_code'));
      $type     = get_null($this->input->post('type_code'));
      $old_code = NULL;

      $brand    = get_null($this->input->post('brand_code'));
      $year     = get_null($this->input->post('year'));
      $cost     = $this->input->post('cost');
      $price    = $this->input->post('price');
      $unit     = $this->input->post('unit_code');
      $unit_id = $this->input->post('unit_id');
      $unit_group = $this->input->post('unit_group');
      $sale_vat_code = $this->input->post('sale_vat_code');
      $purchase_vat_code = $this->input->post('purchase_vat_code');
      $count_stock = $this->input->post('count_stock') === NULL ? 0 :1;
      $can_sell = $this->input->post('can_sell') === NULL ? 0 : 1;
      $is_api   = $this->input->post('is_api') === NULL ? 0 : 1;
      $active   = $this->input->post('active')=== NULL ? 0 : 1;
      $tabs     = $this->input->post('tabs');

      $ds = array(
        'code' => $code,
        'name' => $name,
        'group_code' => $group,
				'main_group_code' => $main_group,
        'sub_group_code' => $sub_group,
        'category_code' => $category,
        'kind_code' => $kind,
        'type_code' => $type,
        'brand_code' => $brand,
        'year' => $year,
        'cost' => $cost,
        'price' => $price,
        'unit_code' => $unit,
        'unit_id' => $unit_id,
        'unit_group' => $unit_group,
        'sale_vat_code' => $sale_vat_code,
        'sale_vat_rate' => $this->vat_model->get_rate($sale_vat_code),
        'purchase_vat_code' => $purchase_vat_code,
        'purchase_vat_rate' => $this->vat_model->get_rate($purchase_vat_code),
        'count_stock' => $count_stock,
        'can_sell' => $can_sell,
        'active' => $active,
        'is_api' => $is_api,
        'update_user' => get_cookie('uname'),
        'old_code' => $old_code
      );

      if($this->product_style_model->is_exists($code))
      {
        set_error("'".$code."' มีในระบบแล้ว");
      }
      else
      {
        $id = $this->product_style_model->add($ds);
        
        if($id)
        {
          //--- export tot sap
          $this->export_style($code);

          //---
          if(!empty($tabs))
          {
            $this->product_tab_model->updateTabsProduct($code, $tabs);
          }

          redirect($this->home.'/edit/'.$id);
        }
        else
        {
          set_error("เพิ่มข้อมูลไม่สำเร็จ");
          $this->session->set_userdata($ds);
          redirect($this->home.'/add_new');
        }
      }
    }
    else
    {
      set_error("No content");
      redirect($this->home.'/add_new');
    }

  }


  public function export_style($style_code)
  {
    $style = $this->product_style_model->get($style_code);

    if(!empty($style))
    {
      $ext = $this->product_style_model->is_middle_exists($style_code);
      $flag = $ext === TRUE ? 'U' : 'A';
      $arr = array(
        'Code' => $style->code,
        'Name' => $style->name,
        'UpdateDate' => sap_date(now(), TRUE),
        'Flag' => $flag
      );

      return $this->product_style_model->add_sap_model($arr);
    }

    return FALSE;
  }



  public function edit($id, $tab = 'styleTab')
  {
    //$code = urldecode($code);
    // $style = $this->product_style_model->get($code);
    $style = $this->product_style_model->get_by_id($id);

    if(!empty($style))
    {
      $code = $style->code;
      $style->edit = TRUE;

      $data = array(
        'style'  => $style,
        'items'   => $this->products_model->get_style_items($code),
        'images'  => $this->product_image_model->get_style_images($code),
        'sizes' => $this->products_model->get_style_sizes_cost_price($code),
        'tab'     => $tab
      );

      $this->load->view('masters/products/products_edit_view', $data);
    }
    else
    {
      set_error("ไม่พบข้อมูล '".$code."' ในระบบ");
      redirect($this->home);
    }
  }


  public function view_detail($id, $tab = 'styleTab')
  {
    $style = $this->product_style_model->get_by_id($id);

    if( ! empty($style))
    {
      $style->edit = FALSE;

      $data = array(
        'style'  => $style,
        'items'   => $this->products_model->get_style_items($style->code),
        'images'  => $this->product_image_model->get_style_images($style->code),
        'sizes' => $this->products_model->get_style_sizes_cost_price($style->code),
        'tab'     => $tab
      );

      $this->load->view('masters/products/products_view_detail', $data);
    }
    else
    {
      set_error("ไม่พบข้อมูล '".$code."' ในระบบ");
      redirect($this->home);
    }
  }




  //--- update item data
  public function update_item()
  {
    $sc = TRUE;

    if($this->input->post('id'))
    {
      $id = $this->input->post('id');
      $barcode = get_null(trim($this->input->post('barcode')));
      $cost = get_null($this->input->post('cost'));
      $price = get_null($this->input->post('price'));

      $item = $this->products_model->get_by_id($id);

      if( ! empty($item))
      {
        $ds = array(
          'barcode' => ($barcode === '' ? NULL: $barcode),
          'cost' => ($cost === NULL ? 0.00 : $cost),
          'price' => ($price === NULL ? 0.00 : $price)
        );

        if( ! $this->products_model->update($item->code, $ds))
        {
          $sc = FALSE;
          $this->error = "Failed to update item";
        }

        if($sc === TRUE)
        {
          $this->do_export($item->code, 'U');
        }
      }
      else
      {
        $sc = FALSE;
        $this->error = "Item not found";
      }
    }
    else
    {
      $sc = FALSE;
      $this->error = "Missign required parameter";
    }

    $this->_response($sc);
  }



  public function update_style()
  {
    if($this->input->post('code'))
    {
      $code = $this->input->post('code'); //--- style code
      $name = $this->input->post('name'); //--- style name

      $style = $this->product_style_model->get($code);

      $old_code = get_null($this->input->post('old_style'));
      $old_code = empty($old_code) ? $code : $old_code;
      $cost = $this->input->post('cost'); //--- style cost
      $price = $this->input->post('price'); //--- style price
      $unit = $this->input->post('unit_code');
      $unit_id = $this->input->post('unit_id');
      $unit_group = $this->input->post('unit_group');
      $brand = $this->input->post('brand_code');
      $group = $this->input->post('group_code');
			$main_group = $this->input->post('main_group_code');
      $sub_group = $this->input->post('sub_group_code');
      $category = $this->input->post('category_code');
      $kind = $this->input->post('kind_code');
      $type = $this->input->post('type_code');
      $year = $this->input->post('year');
      $count = $this->input->post('count_stock');
      $sell = $this->input->post('can_sell');
      $api = $this->input->post('is_api');
      $active = $this->input->post('active');
      $user = get_cookie('uname');

      $flag_cost = $this->input->post('cost_update');
      $flag_price = $this->input->post('price_update');

      $tabs = $this->input->post('tabs');

      $ds = array(
        'name' => addslashes(trim($name)),
        'group_code' => get_null($group),
				'main_group_code' => get_null($main_group),
        'sub_group_code' => get_null($sub_group),
        'category_code' => get_null($category),
        'kind_code' => get_null($kind),
        'type_code' => get_null($type),
        'brand_code' => get_null($brand),
        'year' => $year,
        'cost' => ($cost === NULL ? 0.00 : $cost),
        'price' => ($price === NULL ? 0.00 : $price),
        'unit_code' => $unit,
        'unit_id' => $unit_id,
        'unit_group' => $unit_group,
        'count_stock' => ($count === NULL ? 0 : 1),
        'can_sell' => ($sell === NULL ? 0 : 1),
        'active' => ($active === NULL ? 0 : 1),
        'is_api' => ($api === NULL ? 0 : 1),
        'update_user' => get_cookie('uname'),
        'old_code' => $old_code
      );


      $rs = $this->product_style_model->update($code, $ds);


      if($rs)
      {
        //--- export tot sap
        $this->export_style($code);

        if(!empty($tabs))
        {
          $this->product_tab_model->updateTabsProduct($code, $tabs);
        }

        //----
        $items = $this->products_model->get_style_items($code);
        if(!empty($items))
        {
          $ds = array(
            'group_code' => get_null($group),
						'main_group_code' => get_null($main_group),
            'sub_group_code' => get_null($sub_group),
            'category_code' => get_null($category),
            'kind_code' => get_null($kind),
            'type_code' => get_null($type),
            'brand_code' => get_null($brand),
            'year' => $year,
            'unit_code' => $unit,
            'count_stock' => ($count === NULL ? 0 : 1),
            'can_sell' => ($sell === NULL ? 0 : 1),
            'active' => ($active === NULL ? 0 : 1),
            'is_api' => ($api === NULL ? 0 : 1),
            'update_user' => get_cookie('uname'),
            'old_style' => $old_code
          );

          //--- ถ้าติกให้ updte cost มาด้วย
          if(!empty($flag_cost))
          {
            $ds['cost'] = ($cost === NULL ? 0.00 : $cost);
          }

          //--- ถ้าติกให้ updte price มาด้วย
          if(!empty($flag_price))
          {
            $ds['price'] = ($price === NULL ? 0.00 : $price);
          }

          foreach($items as $item)
          {
            $this->products_model->update($item->code, $ds);
          }
        }

        set_message('ปรับปรุงเรียบร้อยแล้ว');
      }
      else
      {
        set_error('ปรับปรุงข้อมูลไม่สำเร็จ');
      }

      redirect($this->home.'/edit/'.$style->id.'/styleTab');

    }
    else
    {
      set_error("ไม่พบข้อมูลสินค้า");
      redirect($this->home);
    }
  }



  //---- update style data
  public function update()
  {
    $sc = TRUE;

    if($this->input->post('code'))
    {
      $old_code = $this->input->post('products_code');
      $old_name = $this->input->post('products_name');
      $code = $this->input->post('code');
      $name = $this->input->post('name');

      $ds = array(
        'code' => $code,
        'name' => $name,
        'group_code' => $this->input->post('group'),
        'kind_code' => $this->input->post('kind'),
        'type_code' => $this->input->post('type'),
        'class_code' => $this->input->post('class'),
        'area_code' => $this->input->post('area')
      );

      if($sc === TRUE && $this->products_model->is_exists($code, $old_code) === TRUE)
      {
        $sc = FALSE;
        set_error("'".$code."' มีอยู่ในระบบแล้ว โปรดใช้รหัสอื่น");
      }

      if($sc === TRUE && $this->products_model->is_exists_name($name, $old_name) === TRUE)
      {
        $sc = FALSE;
        set_error("'".$name."' มีอยู่ในระบบแล้ว โปรดใช้ชื่ออื่น");
      }

      if($sc === TRUE)
      {
        if($this->products_model->update($old_code, $ds) === TRUE)
        {
          set_message('ปรับปรุงข้อมูลเรียบร้อยแล้ว');
        }
        else
        {
          $sc = FALSE;
          set_error('ปรับปรุงข้อมูลไม่สำเร็จ');
        }
      }

    }
    else
    {
      $sc = FALSE;
      set_error('ไม่พบข้อมูล');
    }

    if($sc === FALSE)
    {
      $code = $this->input->post('products_code');
    }

    redirect($this->home.'/edit/'.$code);
  }




  public function update_cost_price_by_size()
  {
    $sc = TRUE;
    if($this->input->post('style_code'))
    {
      $code = $this->input->post('style_code');
      $size = $this->input->post('size');
      $cost = empty($this->input->post('cost')) ? 0 : $this->input->post('cost');
      $price = empty($this->input->post('price')) ? 0 : $this->input->post('price');

      if(!empty($size))
      {
        $rs = $this->products_model->update_cost_price_by_size($code, $size, $cost, $price);
        if(!$rs)
        {
          $sc = FALSE;
          $this->error = "Update failed";
        }
      }
      else
      {
        $sc = FALSE;
        $this->error = "ไม่พบไซส์";
      }
    }
    else
    {
      $sc = FALSE;
      $this->error = "ไม่พบรหัสสินค้า";
    }

    echo $sc === TRUE ? 'success' : $this->error;
  }


  public function update_all_cost_price_by_size()
  {
    $sc = TRUE;

    $ds = json_decode($this->input->post('data'));

    if( ! empty($ds))
    {
      $this->db->trans_begin();

      foreach($ds as $rs)
      {
        if($sc === FALSE)
        {
          break;
        }

        if( ! $this->products_model->update_cost_price_by_size($rs->style_code, $rs->size_code, $rs->cost, $rs->price))
        {
          $sc = FALSE;
          $this->error = "Failed to update item cost and price";
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
    }
    else
    {
      $sc = FALSE;
      set_error('required');
    }

    $this->_response($sc);
  }

  public function toggle_can_sell($code)
  {
    $status = $this->products_model->get_status('can_sell', $code);
    $status = $status == 1 ? 0 : 1;

    if($this->products_model->set_status('can_sell', $code, $status))
    {
      echo $status;
    }
    else
    {
      echo 'fail';
    }
  }


  public function toggle_active($code)
  {
    $status = $this->products_model->get_status('active', $code);
    $status = $status == 1 ? 0 : 1;

    if($this->products_model->set_status('active', $code, $status))
    {
      echo $status;
    }
    else
    {
      echo 'fail';
    }
  }



  public function toggle_api($code)
  {
    $status = $this->products_model->get_status('is_api', $code);
    $status = $status == 1 ? 0 : 1;

    if($this->products_model->set_status('is_api', $code, $status))
    {
      echo $status;
    }
    else
    {
      echo 'fail';
    }
  }


  public function item_gen($id)
  {
    $style = $this->product_style_model->get_by_id($id);
    $data = array(
      'style' => $style,
      'colors' => $this->product_color_model->get_data(),
      'sizes' => $this->product_size_model->get_data(),
      'images' => $this->product_image_model->get_style_images($style->code)
    );

    $this->load->view('masters/products/product_generater', $data);
  }

  public function gen_items()
  {
    $sc = TRUE;
    $h = json_decode($this->input->post('data'));

    if( ! empty($h))
    {
      $style = $this->product_style_model->get_by_id($h->id);

      if( ! empty($style))
      {
        if( ! empty($h->items))
        {
          $this->db->trans_begin();

          foreach($h->items as $rs)
          {
            if($sc === FALSE)
            {
              break;
            }

            $arr = array(
              'code' => $rs->code,
              'name' => $rs->name,
              'style_code' => $style->code,
              'color_code' => get_null($rs->color_code),
              'size_code' => get_null($rs->size_code),
              'group_code' => $style->group_code,
              'main_group_code' => $style->main_group_code,
              'sub_group_code' => $style->sub_group_code,
              'category_code' => $style->category_code,
              'kind_code' => $style->kind_code,
              'type_code' => $style->type_code,
              'brand_code' => $style->brand_code,
              'year' => $style->year,
              'cost' => get_zero($rs->cost),
              'price' => get_zero($rs->price),
              'unit_code' => $style->unit_code,
              'unit_id' => $style->unit_id,
              'unit_group' => $style->unit_group,
              'sale_vat_code' => $style->sale_vat_code,
              'sale_vat_rate' => $style->sale_vat_rate,
              'purchase_vat_code' => $style->purchase_vat_code,
              'purchase_vat_rate' => $style->purchase_vat_rate,
              'count_stock' => $style->count_stock,
              'can_sell' => $style->can_sell,
              'active' => $style->active,
              'is_api' => $style->is_api,
              'old_style' => NULL,
              'old_code' => NULL,
              'update_user' => $this->_user->uname
            );

            if($this->products_model->is_exists($rs->code))
            {
              if( ! $this->products_model->update($rs->code, $arr))
              {
                $sc = FALSE;
                $this->error = "Failed to update item : {$rs->code} /n";
              }
            }
            else
            {
              if( ! $this->products_model->add($arr))
              {
                $sc = FALSE;
                $this->error = "Insert failed : {$rs->code} /n";
              }
            }

            if($sc === TRUE)
            {
              if( ! empty($rs->id_image))
              {
                $arr = array(
                  'code' => $rs->code,
                  'id_image' => $rs->id_image
                );

                $this->product_image_model->update_product_imag($arr);
              }
            }
          } //-- end foreach

          if($sc === TRUE)
          {
            $this->db->trans_commit();
          }
          else
          {
            $this->db->trans_rollback();
          }
        }
        else
        {
          $sc = FALSE;
          $this->error = "Items data not found";
        }
      }
      else
      {
        $sc = FALSE;
        $this->error = "Invalid Product Model";
      }
    }
    else
    {
      $sc = FALSE;
      $this->error = "Missing required parameter";
    }

    $this->_response($sc);
  }

  // public function gen_items()
  // {
  //   if($this->input->post('id'))
  //   {
  //     $style = $this->product_style_model->get($this->input->post('id'));
  //
  //     if( ! empty($style))
  //     {
  //
  //     }
  //     else
  //     {
  //       $sc = FALSE;
  //     }
  //     $code = $this->input->post('style');
  //     $this->input->post('old_code'); //--- array of old_code
  //     $colors = $this->input->post('colors');
  //     $sizes = $this->input->post('sizes');
  //     $images = $this->input->post('image');
  //     $cost = $this->input->post('cost');
  //     $price = $this->input->post('price');
  //
  //     if($colors !== NULL && $sizes !== NULL)
  //     {
  //       $rs = $this->gen_color_and_size($code, $colors, $sizes, $cost, $price, $old_code);
  //     }
  //
  //     if($colors !== NULL && $sizes === NULL)
  //     {
  //       $rs = $this->gen_color_only($code, $colors, $old_code);
  //     }
  //
  //
  //     if($colors === NULL && $sizes !== NULL)
  //     {
  //       $rs = $this->gen_size_only($code, $sizes, $old_code);
  //     }
  //
  //     if($rs === TRUE && $colors !== NULL && $images !== NULL)
  //     {
  //       foreach($images as $key => $val)
  //       {
  //         if($val !== '')
  //         {
  //           $items = $this->products_model->get_items_by_color($code, $val);
  //           if(!empty($items))
  //           {
  //             foreach($items as $item)
  //             {
  //               //--- insert or update image product
  //               $arr = array(
  //                 'code' => $item->code,
  //                 'id_image' => $key
  //               );
  //
  //               $this->product_image_model->update_product_imag($arr);
  //             }
  //           }
  //         }
  //       }
  //
  //       set_message('Done');
  //     }
  //     else
  //     {
  //       set_error($this->error);
  //     }
  //   }
  //
  //   redirect($this->home.'/edit/'.$code.'/itemTab');
  //
  // }



  public function gen_color_and_size($style, $colors, $sizes, $cost, $price, $old_code)
  {
    $sc = TRUE;
    foreach($colors as $color)
    {
      foreach($sizes as $size)
      {
        $code = $style . '-' . $color . '-' . $size;
        //--- duplicate basic data from product style
        $ds = $this->product_style_model->get($style);

        $data = array(
          'code' => $code,
          'name' => ($ds->name.' '.$code),
          'style_code' => $style,
          'color_code' => $color,
          'size_code' => $size,
          'group_code' => $ds->group_code,
					'main_group_code' => $ds->main_group_code,
          'sub_group_code' => $ds->sub_group_code,
          'category_code' => $ds->category_code,
          'kind_code' => $ds->kind_code,
          'type_code' => $ds->type_code,
          'brand_code' => $ds->brand_code,
          'year' => $ds->year,
          'cost' => (isset($cost[$size]) ? $cost[$size] :$ds->cost),
          'price' => (isset($price[$size]) ? $price[$size] : $ds->price),
          'unit_code' => $ds->unit_code,
          'unit_id' => $ds->unit_id,
          'unit_group' => $ds->unit_group,
          'sale_vat_code' => $ds->sale_vat_code,
          'sale_vat_rate' => $ds->sale_vat_rate,
          'purchase_vat_code' => $ds->purchase_vat_code,
          'purchase_vat_rate' => $ds->purchase_vat_rate,
          'count_stock' => $ds->count_stock,
          'can_sell' => $ds->can_sell,
          'active' => $ds->active,
          'is_api' => $ds->is_api,
          'old_style' => $ds->old_code,
          'old_code' => (isset($old_code[$code]) ? $old_code[$code] : $code),
          'update_user' => get_cookie('uname')
        );

        if($this->products_model->is_exists($code))
        {
          $rs = $this->products_model->update($code, $data);
        }
        else
        {
          $rs = $this->products_model->add($data);
        }

        if($rs === FALSE)
        {
          $this->error .= 'Insert fail : '.$code.' /n' ;
        }
      }
    }

    return $sc;
  }




  public function gen_color_only($style, $colors)
  {
    $sc = TRUE;
    foreach($colors as $color)
    {
      $code = $style . '-' . $color;
      //--- duplicate basic data from product style
      $ds = $this->product_style_model->get($style);
      $data = array(
        'code' => $code,
        'name' => ($ds->name.' '.$code),
        'style_code' => $style,
        'color_code' => $color,
        'size_code' => NULL,
        'group_code' => $ds->group_code,
				'main_group_code' => $ds->main_group_code,
        'sub_group_code' => $ds->sub_group_code,
        'category_code' => $ds->category_code,
        'kind_code' => $ds->kind_code,
        'type_code' => $ds->type_code,
        'brand_code' => $ds->brand_code,
        'year' => $ds->year,
        'cost' => $ds->cost,
        'price' => $ds->price,
        'unit_code' => $ds->unit_code,
        'unit_id' => $ds->unit_id,
        'unit_group' => $ds->uint_group,
        'sale_vat_code' => $ds->sale_vat_code,
        'sale_vat_rate' => $ds->sale_vat_rate,
        'purchase_vat_code' => $ds->purchase_vat_code,
        'purchase_vat_rate' => $ds->purchase_vat_rate,
        'count_stock' => $ds->count_stock,
        'can_sell' => $ds->can_sell,
        'active' => $ds->active,
        'is_api' => $ds->is_api,
        'old_style' => $ds->old_code,
        'old_code' => (isset($old_code[$code]) ? $old_code[$code] : $code),
        'update_user' => get_cookie('uname')
      );

      $rs = $this->products_model->add($data);

      if($rs === FALSE)
      {
        $this->error .= 'Insert fail : '.$code.' /n' ;
      }
    }
  }




  public function gen_size_only($style, $sizes)
  {
    $sc = TRUE;
    foreach($sizes as $size)
    {
      $code = $style . '-' . $size;
      //--- duplicate basic data from product style
      $ds = $this->product_style_model->get($style);

      $data = array(
        'code' => $code,
        'name' => ($ds->name.' '.$code),
        'style_code' => $style,
        'color_code' => NULL,
        'size_code' => $size,
        'group_code' => $ds->group_code,
				'main_group_code' => $ds->main_group_code,
        'sub_group_code' => $ds->sub_group_code,
        'category_code' => $ds->category_code,
        'kind_code' => $ds->kind_code,
        'type_code' => $ds->type_code,
        'brand_code' => $ds->brand_code,
        'year' => $ds->year,
        'cost' => (isset($cost[$size]) ? $cost[$size] :$ds->cost),
        'price' => (isset($price[$size]) ? $price[$size] : $ds->price),
        'unit_code' => $ds->unit_code,
        'unit_id' => $ds->unit_id,
        'unit_group' => $ds->uint_group,
        'sale_vat_code' => $ds->sale_vat_code,
        'sale_vat_rate' => $ds->sale_vat_rate,
        'purchase_vat_code' => $ds->purchase_vat_code,
        'purchase_vat_rate' => $ds->purchase_vat_rate,
        'count_stock' => $ds->count_stock,
        'can_sell' => $ds->can_sell,
        'active' => $ds->active,
        'is_api' => $ds->is_api,
        'old_style' => $ds->old_code,
        'old_code' => (isset($old_code[$code]) ? $old_code[$code] : $code),
        'update_user' => get_cookie('uname')
      );

      $rs = $this->products_model->add($data);

      if($rs === FALSE)
      {
        $this->error .= 'Insert fail : '.$code.' /n' ;
      }
    }
  }




  public function generate_old_code_item()
  {
    $sc = TRUE;
    $style_code = $this->input->post('style_code');
    if(!empty($style_code))
    {
      $style = $this->product_style_model->get($style_code);
      if(!empty($style))
      {
        if(!empty($style->old_code))
        {
          $items = $this->products_model->get_style_items($style->code);
          if(!empty($items))
          {
            foreach($items as $item)
            {
              $color = ($item->color_code === NULL OR $item->color_code === '') ? '' : '-'.$item->color_code;
              $size = ($item->size_code === NULL OR $item->size_code === '') ? '' : '-'.$item->size_code;
              $old_code = $style->old_code . $color . $size;

              $arr = array(
                'old_style' => $style->old_code,
                'old_code' => $old_code
              );

              if($this->products_model->update($item->code, $arr) === TRUE)
              {
                $this->do_export($item->code);
              }
            }
          }
        }
        else
        {
          $sc = FALSE;
          $this->error = "ไม่พบรหัสรุ่นเก่า กรุณาตรวจสอบ";
        }
      }
      else
      {
        $sc = FALSE;
        $this->error = "ไม่พบข้อมูลรุ่นสินค้า";
      }
    }
    else
    {
      $sc = FALSE;
      $this->error = "ไม่พบข้อมูลรุ่นสินค้า";
    }

    echo $sc === TRUE ? 'success' : $this->error;
  }


  public function delete_item($id)
  {
    $sc = TRUE;

    $item = $this->products_model->get_by_id($id);

    if(! empty($item))
    {
      if(! $this->products_model->has_transection($item->code))
      {
        if(! $this->products_model->delete_item($item->code))
        {
          $sc = FALSE;
          $message = "ลบรายการไม่สำเร็จ";
        }
      }
      else
      {
        $sc = FALSE;
        $message = "ไม่สามารถลบ {$item} ได้ เนื่องจากสินค้ามี Transcetion เกิดขึ้นแล้ว";
      }
    }
    else
    {
      $sc = FALSE;
      $message = 'ไม่พบข้อมูล';
    }

    echo $sc === TRUE ? 'success' : $message;
  }




  public function delete_style()
  {
    $sc = TRUE;

    $id = $this->input->get('id');

    $style = $this->product_style_model->get_by_id($id);

    if( ! empty($style))
    {
      if($this->products_model->is_exists_style($style->code) === TRUE)
      {
        $sc = FALSE;
        $this->error = 'ไม่สามารถลบรุ่นสินค้าได้เนื่องจากมีรายการสินค้าที่เชื่อมโยงอยู่';
      }
      else
      {
        $rs = $this->product_style_model->delete($style->code);

        if($rs !== TRUE)
        {
          $sc = FALSE;
          $this->error = 'ลบข้อมูลรุ่นสินค้าไม่สำเร็จ';
        }
      }
    }
    else
    {
      $sc = FALSE;
      $this->error = 'ไม่พบข้อมูลสินค้า';
    }

    echo $sc === TRUE ? 'success' : $this->error;
  }



  //--- ดึง items และรูปภาพ เพื่อทำการเชื่อมโยงรูปภาพ
  public function get_image_items($id)
  {
    $sc = TRUE;

    $style = $this->product_style_model->get_by_id($id);

    if( ! empty($style))
    {
      //---- จำนวนรายการสินค้า ทั้งหมด
      $items = $this->products_model->get_style_items($style->code);

      //--- จำนวนรูปภาพ
      $images = $this->product_image_model->get_style_images($style->code);

      $ds = "";

      if( ! empty($items))
      {
        if( ! empty($images))
        {
          $imgs = array();
          $ds .= '<table class="table table-bordered">';
          //---- image header
      		$ds .= '<tr><td></td>';
          foreach($images as $img)
          {
            $ds .= '<td>';
      			$ds .= '<img src="'.get_image_path($img->id, 'default').'" class="width-100" />';
      			$ds .= '</td>';
      			$imgs[$img->id] = $img->id;
          }
          $ds .= '</tr>';


          foreach( $items as $item )
          {
            $ds .= '<tr>';
            $ds .= '<td>'.$item->code.'</td>';

            foreach($imgs as $id)
            {
              $ds .= '<td>
              <label style="width:100%; text-align:center;">
              <input type="radio" class="ace chk-img"
              name="items['.$item->id.']"
              data-id="'.$item->id.'"
              data-code="'.$item->code.'"
              value="'.$id.'" '.is_checked( $id, $this->product_image_model->get_id_image($item->code) ).' />
              <span class="lbl"></span>
              </label>
              </td>';
            }
            $ds .= '</tr>';
          }
          $ds .= '</table>';
        }
        else
        {
          $sc = FALSE;
          $this->error = "ไม่พบรูปภาพ";
        }
      }
      else
      {
        $sc = FALSE;
        $this->error = "ไม่พบรายการสินค้า";
      }
    }
    else
    {
      $sc = FALSE;
      $this->error = "Invalid style id";
    }


    $arr = array(
      'status' => $sc === TRUE ? 'success' : 'failed',
      'message' => $sc === TRUE ? 'success' : $this->error,
      'data' => $sc === TRUE ? $ds : NULL
    );

    echo json_encode($arr);
  }





  public function mapping_image()
  {
    $sc = TRUE;
    $ds = json_decode($this->input->post('data'));

    if( ! empty($ds))
    {
      foreach($ds as $rs)
      {
        $arr = array(
          'code' => $rs->product_code,
          'id_image' => $rs->id_image
        );

        $this->product_image_model->update_product_image($arr);
      }
    }
    else
    {
      $sc = FALSE;
      set_error('required');
    }

    $this->_response($sc);
  }


  public function generate_barcode()
  {
    $sc = TRUE;
    $this->load->model('masters/product_barcode_model');
    $this->load->helper('barcode');
    $id = $this->input->post('style');
    $type  = $this->input->post('barcodeType');

    $style = $this->product_style_model->get_by_id($id);

    if( ! empty($style))
    {
      $items = $this->products_model->get_unbarcode_items($style->code);

      if( ! empty($items))
      {
        foreach($items as $item)
        {
          //--- type   1 = บาร์โค้ดภายใน  2 = บาร์โค้ดสากล
          if($type == 1)
          {
            $barcode = $this->product_barcode_model->get_last_barcode();
            $barcode += 1;
            $arr = array(
              'barcode' => $barcode,
              'item_code' => $item->code
            );

            if($this->product_barcode_model->addLocal($arr))
            {
              $this->products_model->update_barcode($item->code, $barcode);
            }
          }
          else
          {
            $running = $this->product_barcode_model->get_last_ean_barcode();
            $running += 1;
            $barcode = generateEAN($running);
            $arr = array(
              'barcode' => $barcode,
              'running' => $running,
              'item_code' => $item->code
            );

            if($this->product_barcode_model->addEan13($arr))
            {
              $this->products_model->update_barcode($item->code, $barcode);
            }
          }
        }
      }
      else
      {
        $sc = FALSE;
        $this->error = 'ไม่พบรายการที่ไม่มีบาร์โค้ด';
      }
    }
    else
    {
      $sc = FALSE;
      $this->error = "Invalid product model";
    }

    $this->_response($sc);
  }




  public function is_style_exists($code)
  {
    $rs = $this->product_style_model->is_exists($code);
    if($rs === TRUE)
    {
      echo 'exists';
    }
    else
    {
      echo 'ok';
    }
  }



  public function do_export($code, $method = 'A')
  {
		$sc = TRUE;

    $item = $this->products_model->get($code);
    //--- เช็คข้อมูลในฐานข้อมูลจริง
    $exst = $this->products_model->is_sap_exists($item->code);

    $method = $exst === TRUE ? 'U' : $method;

    //--- เช็คข้อมูลในถังกลาง
    $middle = $this->products_model->get_un_import_middle($item->code);
    if(!empty($middle))
    {
      foreach($middle as $mid)
      {
        $this->products_model->drop_middle_item($mid->DocEntry);
      }
    }


    $ds = array(
      'ItemCode' => $item->code, //--- รหัสสินค้า
      'ItemName' => limitText($item->name, 97),//--- ชื่อสินค้า
      'FrgnName' => NULL,   //--- ชื่อสินค้าภาษาต่างประเทศ
      'ItmsGrpCod' => getConfig('ITEM_GROUP_CODE'),  //--- กลุ่มสินค้า (ต้องตรงกับ SAP)
      'VatGourpSa' => getConfig('SALE_VATE_CODE'), //--- รหัสกลุ่มภาษีขาย
      'CodeBars' => $item->barcode, //--- บาร์โค้ด
      'VATLiable' => 'Y', //--- มี vat หรือไม่
      'PrchseItem' => 'Y', //--- สินค้าสำหรับซื้อหรือไม่
      'SellItem' => 'Y', //--- สินค้าสำหรับขายหรือไม่
      'InvntItem' => $item->count_stock == 1 ? 'Y' : 'N', //--- นับสต้อกหรือไม่
      'SalUnitMsr' => $item->unit_code, //--- หน่วยขาย
      'BuyUnitMsr' => $item->unit_code, //--- หน่วยซื้อ
      'CntUnitMsr' => $item->unit_code,
      'InvntryUom' => $item->unit_code, //--- หน่วยในการนับสต็อก
      'SUomEntry' => $item->unit_id,
      'PUomEntry' => $item->unit_id,
      'UgpEntry' => $item->unit_group,
      'VatGroupPu' => getConfig('PURCHASE_VAT_CODE'), //---- รหัสกลุ่มภาษีซื้อ (ต้องตรงกับ SAP)
      'ItemType' => 'I', //--- ประเภทของรายการ F=Fixed Assets, I=Items, L=Labor, T=Travel
      'validFor' => $item->active == 1 ? 'Y' : 'N',
      'U_MODEL' => $item->style_code,
      'U_COLOR' => $item->color_code,
      'U_SIZE' => $item->size_code,
      'U_GROUP' => $item->group_code,
			'U_MAINGROUP' => $item->main_group_code,
      'U_MAJOR' => $item->sub_group_code,
      'U_CATE' => $item->category_code,
      'U_SUBTYPE' => $item->kind_code,
      'U_TYPE' => $item->type_code,
      'U_BRAND' => $item->brand_code,
      'U_YEAR' => $item->year,
      'U_COST' => $item->cost,
      'U_PRICE' => $item->price,
      'U_OLDCODE' => $item->old_code,
      'F_E_Commerce' => $method,
      'F_E_CommerceDate' => sap_date(now(), TRUE)
    );

    if( ! $this->products_model->add_item($ds))
		{
      $sc = FALSE;
			$this->error = "Update Item failed";
		}

		return $sc;
  }


  public function export_products($style_code)
  {
    $sc = TRUE;
    $success = 0;
    $fail = 0;

    $products = $this->products_model->get_style_items($style_code);

    if(!empty($products))
    {
      foreach($products as $item)
      {
        if($this->do_export($item->code))
        {
          $success++;
        }
        else
        {
          $sc = FALSE;
          $fail++;
        }
      }
    }

    echo $sc === TRUE ? 'success' : "Success : {$success}, Fail : {$fail}";
  }



  public function export_barcode($id, $token)
  {
    //--- load excel library
    $this->load->library('excel');

    $this->excel->setActiveSheetIndex(0);
    $this->excel->getActiveSheet()->setTitle('Stock Balance Report');

    //--- set report title header
    $this->excel->getActiveSheet()->setCellValue('A1', 'Barcode');
    $this->excel->getActiveSheet()->setCellValue('B1', 'Item Code');
    $this->excel->getActiveSheet()->setCellValue('C1', 'Item Name');

    $style = $this->product_style_model->get_by_id($id);
    $code = "notfound";

    if( ! empty($style))
    {
      $code = $style->code;

      $products = $this->products_model->get_style_items($style->code);

      $row = 2;

      if(!empty($products))
      {
        foreach($products as $rs)
        {
          $this->excel->getActiveSheet()->setCellValue('A'.$row, $rs->barcode);
          $this->excel->getActiveSheet()->setCellValue('B'.$row, $rs->code);
          $this->excel->getActiveSheet()->setCellValue('C'.$row, $rs->name);
          $row++;
        }

        $this->excel->getActiveSheet()->getStyle('A2:A'.$row)->getNumberFormat()->setFormatCode('0');
      }
    }

    setToken($token);

    $file_name = "{$code}_barcode.xlsx";
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'); /// form excel 2007 XLSX
    header('Content-Disposition: attachment;filename="'.$file_name.'"');
    $writer = PHPExcel_IOFactory::createWriter($this->excel, 'Excel2007');
    $writer->save('php://output');

  }


  public function clear_filter()
	{
    $filter = array(
      'pd_code',
      'pd_name',
      'pd_group',
      'pd_main_group',
      'pd_sub_group',
      'pd_category',
      'pd_kind',
      'pd_type',
      'pd_brand',
      'pd_year',
      'pd_sell',
      'pd_active'
    );

    clear_filter($filter);
	}
}

?>
