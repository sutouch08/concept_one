<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Items extends PS_Controller
{
  public $menu_code = 'DBITEM';
	public $menu_group_code = 'DB';
  public $menu_sub_group_code = 'PRODUCT';
	public $title = 'เพิ่ม/แก้ไข รายการสินค้า';
  public $error = '';

  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'masters/items';

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
    $this->load->helper('product_tab');
    $this->load->helper('product_brand');
    $this->load->helper('product_tab');
    $this->load->helper('product_kind');
    $this->load->helper('product_type');
    $this->load->helper('product_group');
    $this->load->helper('product_category');
		$this->load->helper('product_main_group');
    $this->load->helper('product_sub_group');
    $this->load->helper('product_images');
    $this->load->helper('unit');
    $this->load->helper('vat');

  }


  public function index()
  {
    $filter = array(
      'code'      => get_filter('code', 'item_code', ''),
      'name'      => get_filter('name', 'item_name', ''),
      'barcode'   => get_filter('barcode', 'item_barcode', ''),
      'color'     => get_filter('color', 'color' ,''),
      'size'      => get_filter('size', 'size', ''),
      'group'     => get_filter('group', 'group', ''),
      'sub_group' => get_filter('sub_group', 'sub_group', ''),
      'category'  => get_filter('category', 'category', ''),
      'kind'      => get_filter('kind', 'kind', ''),
      'type'      => get_filter('type', 'type', ''),
      'brand'     => get_filter('brand', 'brand', ''),
      'year'      => get_filter('year', 'year', ''),
      'active' => get_filter('active', 'active', 'all')
    );

		//--- แสดงผลกี่รายการต่อหน้า
		$perpage = get_rows();
		//--- หาก user กำหนดการแสดงผลมามากเกินไป จำกัดไว้แค่ 300
		if($perpage > 300)
		{
			$perpage = 20;
		}

		$segment  = 4; //-- url segment
		$rows     = $this->products_model->count_rows($filter);
		//--- ส่งตัวแปรเข้าไป 4 ตัว base_url ,  total_row , perpage = 20, segment = 3
		$init	    = pagination_config($this->home.'/index/', $rows, $perpage, $segment);
		$products = $this->products_model->get_list($filter, $perpage, $this->uri->segment($segment));
    $ds       = array();
    if(!empty($products))
    {
      foreach($products as $rs)
      {
        $rs->group   = $this->product_group_model->get_name($rs->group_code);
        $rs->kind    = $this->product_kind_model->get_name($rs->kind_code);
        $rs->type    = $this->product_type_model->get_name($rs->type_code);
        $rs->category  = $this->product_category_model->get_name($rs->category_code);
        $rs->brand   = $this->product_brand_model->get_name($rs->brand_code);
      }
    }

    $filter['data'] = $products;

		$this->pagination->initialize($init);
    $this->load->view('masters/product_items/items_list', $filter);
  }



  public function add_new()
  {
    $this->load->view('masters/product_items/items_add_view');
  }


  public function add()
  {
    $sc = TRUE;
    $ex = 0;

    $ds = json_decode($this->input->post('data'));

    if( ! empty($ds) && ! empty($ds->code) && ! empty($ds->name))
    {
      if($this->products_model->is_exists($ds->code))
      {
        $sc = FALSE;
        set_error('exists', $ds->code);
      }

      if($sc === TRUE)
      {
        $arr = array(
          'code' => $ds->code,
          'name' => $ds->name,
          'barcode' => get_null($ds->barcode),
          'style_code' => get_null($ds->style),
          'color_code' => get_null($ds->color),
          'size_code' => get_null($ds->size),
          'group_code' => get_null($ds->group_code),
					'main_group_code' => get_null($ds->main_group_code),
          'sub_group_code' => get_null($ds->sub_group_code),
          'category_code' => get_null($ds->category_code),
          'kind_code' => get_null($ds->kind_code),
          'type_code' => get_null($ds->type_code),
          'brand_code' => get_null($ds->brand_code),
          'year' => $ds->year,
          'cost' => round(floatval($ds->cost), 2),
          'price' => round(floatval($ds->price), 2),
          'unit_code' => get_null($ds->unit_code),
          'unit_id' => get_null($ds->unit_id),
          'unit_group' => get_null($ds->unit_group),
          'sale_vat_code' => get_null($ds->sale_vat_code),
          'sale_vat_rate' => floatval($ds->sale_vat_rate),
          'purchase_vat_code' => get_null($ds->purchase_vat_code),
          'purchase_vat_rate' => floatval($ds->purchase_vat_rate),
          'count_stock' => empty($ds->count_stock) ? 0 : 1,
          'can_sell' => empty($ds->can_sell) ? 0 : 1,
          'active' => empty($ds->active) ? 0 : 1,
          'update_user' => $this->_user->uname
        );

        $id = $this->products_model->add($arr);

        if($id)
        {
          if( ! $this->do_export($id))
          {
            $ex = 1;
            $this->error = "เพิ่มรายการสินค้าสำเร็จ แต่ส่งข้อมุลเข้า SAP ไม่สำเร็จ";
          }
        }
        else
        {
          $sc = FALSE;
          $this->error = "เพิ่มรายการสินค้าไม่สำเร็จ";
        }
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
      'ex' => $ex
    );

    echo json_encode($arr);
  }


  public function edit($id)
  {
    $item = $this->products_model->get_by_id($id);

    if(!empty($item))
    {
      $this->load->view('masters/product_items/items_edit_view', $item);
    }
    else
    {
      $this->page_error();
    }
  }


  public function update()
  {
    $sc = TRUE;
    $ex = 0;

    $ds = json_decode($this->input->post('data'));

    if( ! empty($ds) && ! empty($ds->id) && ! empty($ds->code) && ! empty($ds->name))
    {
      $arr = array(
        'code' => $ds->code,
        'name' => $ds->name,
        'barcode' => get_null($ds->barcode),
        'style_code' => get_null($ds->style),
        'color_code' => get_null($ds->color),
        'size_code' => get_null($ds->size),
        'group_code' => get_null($ds->group_code),
        'main_group_code' => get_null($ds->main_group_code),
        'sub_group_code' => get_null($ds->sub_group_code),
        'category_code' => get_null($ds->category_code),
        'kind_code' => get_null($ds->kind_code),
        'type_code' => get_null($ds->type_code),
        'brand_code' => get_null($ds->brand_code),
        'year' => $ds->year,
        'cost' => round(floatval($ds->cost), 2),
        'price' => round(floatval($ds->price), 2),
        'unit_code' => get_null($ds->unit_code),
        'unit_id' => get_null($ds->unit_id),
        'unit_group' => get_null($ds->unit_group),
        'sale_vat_code' => get_null($ds->sale_vat_code),
        'sale_vat_rate' => floatval($ds->sale_vat_rate),
        'purchase_vat_code' => get_null($ds->purchase_vat_code),
        'purchase_vat_rate' => floatval($ds->purchase_vat_rate),
        'count_stock' => empty($ds->count_stock) ? 0 : 1,
        'can_sell' => empty($ds->can_sell) ? 0 : 1,
        'active' => empty($ds->active) ? 0 : 1,
        'update_user' => $this->_user->uname
      );

      if( ! $this->products_model->update_by_id($ds->id, $arr))
      {
        $sc = FALSE;
        set_error('update');
      }

      if($sc === TRUE)
      {
        if( ! $this->do_export($ds->id))
        {
          $ex = 1;
          $this->error = "เพิ่มรายการสินค้าสำเร็จ แต่ส่งข้อมุลเข้า SAP ไม่สำเร็จ";
        }
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
      'ex' => $ex
    );

    echo json_encode($arr);
  }


  public function view_detail($id)
  {
    $item = $this->products_model->get_by_id($id);

    if(!empty($item))
    {
      $this->load->view('masters/product_items/items_view_detail', $item);
    }
    else
    {
      $this->page_error();
    }
  }


  public function is_exists_code($code, $old_code = '')
  {
    if($this->products_model->is_exists($code, $old_code))
    {
      echo 'รหัสซ้ำ';
    }
    else
    {
      echo 'ok';
    }
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


  public function delete_item()
  {
    $sc = TRUE;
    $id = $this->input->get('id');

    $item = $this->products_model->get_by_id($id);

    if( ! empty($item))
    {
      if(! $this->products_model->has_transection($item->code))
      {
        if(! $this->products_model->delete_item($item->code))
        {
          $sc = FALSE;
          $this->error = "ลบรายการไม่สำเร็จ";
        }
      }
      else
      {
        $sc = FALSE;
        $this->error = "ไม่สามารถลบ {$item->code} ได้ เนื่องจากสินค้ามี Transcetion เกิดขึ้นแล้ว";
      }
    }
    else
    {
      $sc = FALSE;
      $this->error = "ไม่พบรายการสินค้า";
    }

    echo $sc === TRUE ? 'success' : $this->error;
  }


  public function do_export($id, $method = 'A')
  {
		$sc = TRUE;

    $item = $this->products_model->get_by_id($id);
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
      'SUomEntry' => $item->unit_id, //---
      'BuyUnitMsr' => $item->unit_code, //--- หน่วยซื้อ
      'PUomEntry' => $item->unit_id,
      'CntUnitMsr' => $item->unit_code,
      'UgpEntry' => $item->unit_group,
      'VatGroupPu' => getConfig('PURCHASE_VAT_CODE'), //---- รหัสกลุ่มภาษีซื้อ (ต้องตรงกับ SAP)
      'ItemType' => 'I', //--- ประเภทของรายการ F=Fixed Assets, I=Items, L=Labor, T=Travel
      'InvntryUom' => $item->unit_code, //--- หน่วยในการนับสต็อก
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


  public function send_to_sap($id, $method = 'A')
  {
		$sc = TRUE;

    $item = $this->products_model->get_by_id($id);
    //--- เช็คข้อมูลในฐานข้อมูลจริง
    $exst = $this->products_model->is_sap_exists($item->code);

    $method = $exst === TRUE ? 'U' : 'A';

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
      'SUomEntry' => $item->unit_id, //---
      'BuyUnitMsr' => $item->unit_code, //--- หน่วยซื้อ
      'PUomEntry' => $item->unit_id,
      'CntUnitMsr' => $item->unit_code,
      'UgpEntry' => $item->unit_group,
      'VatGroupPu' => getConfig('PURCHASE_VAT_CODE'), //---- รหัสกลุ่มภาษีซื้อ (ต้องตรงกับ SAP)
      'ItemType' => 'I', //--- ประเภทของรายการ F=Fixed Assets, I=Items, L=Labor, T=Travel
      'InvntryUom' => $item->unit_code, //--- หน่วยในการนับสต็อก
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

    $this->_response($sc);
  }


  public function download_template($token)
  {
    //--- load excel library
    $this->load->library('excel');

    $this->excel->setActiveSheetIndex(0);
    $this->excel->getActiveSheet()->setTitle('Items Master Template');

    //--- set report title header
    $this->excel->getActiveSheet()->setCellValue('A1', 'Code');
    $this->excel->getActiveSheet()->setCellValue('B1', 'Name');
    $this->excel->getActiveSheet()->setCellValue('C1', 'Barcode');
    $this->excel->getActiveSheet()->setCellValue('D1', 'Model');
    $this->excel->getActiveSheet()->setCellValue('E1', 'Color');
    $this->excel->getActiveSheet()->setCellValue('F1', 'Size');
    $this->excel->getActiveSheet()->setCellValue('G1', 'Group');
    $this->excel->getActiveSheet()->setCellValue('H1', 'SubGroup');
    $this->excel->getActiveSheet()->setCellValue('I1', 'Category');
    $this->excel->getActiveSheet()->setCellValue('J1', 'Kind');
    $this->excel->getActiveSheet()->setCellValue('K1', 'Type');
    $this->excel->getActiveSheet()->setCellValue('L1', 'Brand');
    $this->excel->getActiveSheet()->setCellValue('M1', 'Year');
    $this->excel->getActiveSheet()->setCellValue('N1', 'Cost');
    $this->excel->getActiveSheet()->setCellValue('O1', 'Price');
    $this->excel->getActiveSheet()->setCellValue('P1', 'Unit');
    $this->excel->getActiveSheet()->setCellValue('Q1', 'CountStock');
    $this->excel->getActiveSheet()->setCellValue('R1', 'IsAPI');
    $this->excel->getActiveSheet()->setCellValue('S1', 'OldModel');
    $this->excel->getActiveSheet()->setCellValue('T1', 'OldCode');


    setToken($token);

    $file_name = "Items_master_template.xlsx";
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'); /// form excel 2007 XLSX
    header('Content-Disposition: attachment;filename="'.$file_name.'"');
    $writer = PHPExcel_IOFactory::createWriter($this->excel, 'Excel2007');
    $writer->save('php://output');
  }


  public function clear_filter()
	{
    $filter = array('item_code','item_name','item_barcode','color', 'size','group','sub_group','category','kind','type','brand','year', 'active');
    clear_filter($filter);
	}
}

?>
