<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Check_stock_diff extends PS_Controller
{
  public $menu_code = 'ICSTDF';
	public $menu_group_code = 'IC';
  public $menu_sub_group_code = '';
	public $title = 'ตรวจนับสต็อก(เก็บยอดต่าง)';
  public $filter;
  public $error;
  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'inventory/check_stock_diff';
    $this->load->model('inventory/check_stock_diff_model');
    $this->load->model('stock/stock_model');
    $this->load->model('inventory/buffer_model');
    $this->load->model('masters/products_model');
    $this->load->model('masters/zone_model');
  }


  public function index()
  {
    $filter = array(
      'product_code' => get_filter('product_code', 'check_product_code', ''),
      'zone_code' => get_filter('zone_code', 'check_zone_code', ''),
      'status' => get_filter('status', 'check_status', 'all'),
      'from_date' => get_filter('from_date', 'check_from_date', ''),
      'to_date' => get_filter('to_date', 'check_to_date', ''),
      'user' => get_filter('user', 'check_user', '')
    );

		//--- แสดงผลกี่รายการต่อหน้า
		$perpage = get_rows();
		//--- หาก user กำหนดการแสดงผลมามากเกินไป จำกัดไว้แค่ 300
		if($perpage > 300)
		{
			$perpage = 20;
		}

		$segment  = 4; //-- url segment
		$rows     = $this->check_stock_diff_model->count_rows($filter);
		//--- ส่งตัวแปรเข้าไป 4 ตัว base_url ,  total_row , perpage = 20, segment = 3
		$init	    = pagination_config($this->home.'/index/', $rows, $perpage, $segment);
		$ds   = $this->check_stock_diff_model->get_list($filter, $perpage, $this->uri->segment($segment));

    $filter['data'] = $ds;

		$this->pagination->initialize($init);
    $this->load->view('inventory/check_stock_diff/stock_diff_view', $filter);
  }


  //---- สำหรับโหลดยอดต่างเข้าเอกสาร ปรับยอด
  public function diff_list($adjust_code)
  {
    $filter = array(
      'product_code' => get_filter('product_code', 'check_product_code', ''),
      'zone_code' => get_filter('zone_code', 'check_zone_code', ''),
      'status' => 0
    );

		//--- แสดงผลกี่รายการต่อหน้า
		$perpage = get_rows();
		//--- หาก user กำหนดการแสดงผลมามากเกินไป จำกัดไว้แค่ 300
		if($perpage > 300)
		{
			$perpage = 20;
		}

		$segment  = 4; //-- url segment
		$rows     = $this->check_stock_diff_model->count_rows($filter);
		//--- ส่งตัวแปรเข้าไป 4 ตัว base_url ,  total_row , perpage = 20, segment = 3
		$init	    = pagination_config($this->home.'/diff_list/', $rows, $perpage, $segment);
		$ds   = $this->check_stock_diff_model->get_list($filter, $perpage, $this->uri->segment($segment));

    $filter['data'] = $ds;
    $filter['adjust_code'] = $adjust_code;

		$this->pagination->initialize($init);
    $this->load->view('inventory/check_stock_diff/diff_list_view', $filter);
  }




  public function check($zone_code = NULL, $is_checked = NULL)
  {
    //print_r($this->input->post());
    $zone_code = empty($zone_code) ? $this->input->post('zone_code') : $zone_code;
    $product_code = $this->input->post('product_code');
    $zone = !empty($zone_code) ? $this->zone_model->get($zone_code) : NULL;

    if(!empty($zone))
    {
      $details = $this->check_stock_diff_model->get_stock_and_diff($zone_code, $product_code);
      $pd_in = array();
      if(!empty($details))
      {
        //---- loop and add diff qty
        foreach($details as $rs)
        {
          $diff_qty = $this->check_stock_diff_model->get_active_diff($zone_code, $rs->product_code);
          $buffer_qty = $this->buffer_model->get_buffer_zone($zone_code, $rs->product_code);
          $rs->diff_qty = $diff_qty;
          $rs->count_qty = ($rs->OnHandQty - $buffer_qty) + $diff_qty;
          $rs->OnHandQty = ($rs->OnHandQty - $buffer_qty);
          $pd_in[] = $rs->product_code;
        }
      }

      $ex_diff = $this->check_stock_diff_model->get_active_diff_not_in_stock($zone_code, $pd_in, $product_code);
      if(!empty($ex_diff))
      {
        foreach($ex_diff as $rs)
        {
          $item = $this->products_model->get($rs->product_code);
          $rs->barcode = $item->barcode;
          $rs->old_code = $item->old_code;
          $onHandQty = $this->stock_model->get_stock_zone($zone_code, $rs->product_code);
          $buffer_qty = $this->buffer_model->get_buffer_zone($zone_code, $rs->product_code);

          $diff_qty = $rs->qty;
          $rs->diff_qty = $diff_qty;
          $rs->OnHandQty = ($onHandQty - $buffer_qty);
          $rs->count_qty = $rs->OnHandQty + $diff_qty;

          $details[] = $rs;
        }
      }
    }

    $ds['zone_code'] = !empty($zone) ? $zone->code : NULL;
    $ds['product_code'] = $product_code;
    $ds['zone_name'] = !empty($zone) ? $zone->name : NULL;
    $ds['details'] = !empty($details) ? $details : NULL;
    $ds['checked'] = $is_checked;
    $ds['enable_search'] = TRUE;
    $ds['enable_barcode'] = FALSE;

    $this->load->view('inventory/check_stock_diff/check_process', $ds);
  }



  public function check_barcode($zone_code = NULL, $is_checked = NULL)
  {
    $zone_code = empty($zone_code) ? $this->input->post('zone_code') : $zone_code;
    $product_code = $this->input->post('product_code');
    $zone = !empty($zone_code) ? $this->zone_model->get($zone_code) : NULL;

    if(!empty($zone))
    {
      $details = $this->check_stock_diff_model->get_stock_and_diff($zone_code, $product_code);
      $pd_in = array();
      if(!empty($details))
      {
        //---- loop and add diff qty
        foreach($details as $rs)
        {
          $item = $this->products_model->get($rs->product_code);
          $rs->barcode = $item->barcode;
          $rs->old_code = $item->old_code;
          $onHandQty = $this->stock_model->get_stock_zone($zone_code, $rs->product_code);
          $buffer_qty = $this->buffer_model->get_buffer_zone($zone_code, $rs->product_code);

          $diff_qty = $this->check_stock_diff_model->get_active_diff($zone_code, $rs->product_code);
          if(!empty($diff_qty))
          {
            $rs->diff_qty = $diff_qty;
            $rs->OnHandQty = ($onHandQty - $buffer_qty);
            $rs->count_qty = $rs->OnHandQty + $diff_qty;
          }
          else
          {
            $rs->count_qty = 0;
            $rs->OnHandQty = ($onHandQty - $buffer_qty);
            $rs->diff_qty = $rs->count_qty - $rs->OnHandQty;
          }


          $pd_in[] = $rs->product_code;
        }
      }

      $ex_diff = $this->check_stock_diff_model->get_active_diff_not_in_stock($zone_code, $pd_in, $product_code);
      if(!empty($ex_diff))
      {
        foreach($ex_diff as $rs)
        {
          $item = $this->products_model->get($rs->product_code);
          $rs->barcode = $item->barcode;
          $rs->old_code = $item->old_code;
          $onHandQty = $this->stock_model->get_stock_zone($zone_code, $rs->product_code);
          $buffer_qty = $this->buffer_model->get_buffer_zone($zone_code, $rs->product_code);

          $diff_qty = $rs->qty;
          $rs->diff_qty = $diff_qty;
          $rs->OnHandQty = ($onHandQty - $buffer_qty);
          $rs->count_qty = $rs->OnHandQty + $diff_qty;

          $details[] = $rs;
        }
      }
    }

    $ds['zone_code'] = !empty($zone) ? $zone->code : NULL;
    $ds['product_code'] = $product_code;
    $ds['zone_name'] = !empty($zone) ? $zone->name : NULL;
    $ds['details'] = !empty($details) ? $details : NULL;
    $ds['checked'] = $is_checked;
    $ds['enable_search'] = TRUE;
    $ds['enable_barcode'] = TRUE;
    $this->load->view('inventory/check_stock_diff/check_process', $ds);
  }


  // public function check_barcode($zone_code = NULL, $is_checked = NULL)
  // {
  //   $zone_code = empty($zone_code) ? $this->input->post('zone_code') : $zone_code;
  //   $zone = !empty($zone_code) ? $this->zone_model->get($zone_code) : NULL;
  //
  //   if(!empty($zone))
  //   {
  //     $details = $this->check_stock_diff_model->get_active_diff_zone($zone_code);
  //     if(!empty($details))
  //     {
  //       //---- loop and add diff qty
  //       foreach($details as $rs)
  //       {
  //         $item = $this->products_model->get($rs->product_code);
  //         $rs->barcode = $item->barcode;
  //         $rs->old_code = $item->old_code;
  //         $diff_qty = $rs->qty;
  //         $onHandQty = $this->stock_model->get_stock_zone($zone_code, $rs->product_code);
  //         $buffer_qty = $this->buffer_model->get_buffer_zone($zone_code, $rs->product_code);
  //         $rs->diff_qty = $diff_qty;
  //         $rs->count_qty = ($onHandQty - $buffer_qty) + $diff_qty;
  //         $rs->OnHandQty = ($onHandQty - $buffer_qty);
  //       }
  //     }
  //   }
  //
  //   $ds['zone_code'] = !empty($zone) ? $zone->code : NULL;
  //   $ds['product_code'] = NULL;
  //   $ds['zone_name'] = !empty($zone) ? $zone->name : NULL;
  //   $ds['details'] = !empty($details) ? $details : NULL;
  //   $ds['checked'] = $is_checked;
  //   $ds['enable_search'] = FALSE;
  //   $ds['enable_barcode'] = TRUE;
  //   $this->load->view('inventory/check_stock_diff/check_process', $ds);
  // }


  ///----- check zone_exists or not
  public function is_exists_zone()
  {
    $zone_code = $this->input->get('zone_code');
    if($this->zone_model->is_exists($zone_code))
    {
      echo "ok";
    }
    else
    {
      echo "not_exists";
    }
  }


  //---- Save row checked
  public function save_checked()
  {
    $sc = TRUE;
    $product_code = $this->input->post('product_code');
    $zone_code = $this->input->post('zone_code');
    $stock = $this->input->post('stock');
    $count = $this->input->post('count');
    $diff = $count - $stock;

    $item = $this->products_model->get($product_code);
    if(empty($item))
    {
      $sc = FALSE;
      $this->error = "รหัสสินค้าไม่ถูกต้อง";
    }
    else
    {
      $zone = $this->zone_model->get($zone_code);
      if(empty($zone))
      {
        $sc = FALSE;
        $this->error = "โซนไม่ถูกต้อง";
      }
      else
      {
        //--- check if active diff exists
        $row = $this->check_stock_diff_model->get_active_diff_detail($zone_code, $product_code);
        if(!empty($row))
        {
          if($diff != 0)
          {
            $arr = array(
              'qty' => $diff
            );

            if(! $this->check_stock_diff_model->update($row->id, $arr))
            {
              $sc = FALSE;
              $this->error = "Update ยอดต่างไม่สำเร็จ";
            }
          }
          else
          {
            $this->check_stock_diff_model->delete($row->id);
          }
        }
        else
        {
          if($diff != 0)
          {
            $arr = array(
              'zone_code' => $zone_code,
              'product_code' => $product_code,
              'qty' => $diff,
              'status' => 0,
              'user' => get_cookie('uname')
            );

            if(! $this->check_stock_diff_model->add($arr))
            {
              $sc = FALSE;
              $this->error = "เพิ่มรายการไม่สำเร็จ";
            }
          }
        }

      }
    }

    echo $sc === TRUE ? 'success' : $this->error;
  }




  function save_all()
  {
    $sc = TRUE;
    $zone_code = $this->input->post('zoneCode');
    $items = $this->input->post('item');
    $stock = $this->input->post('stock');
    $count = $this->input->post('qty');
    $is_barcode = $this->input->post('is_barcode');

    $zone = $this->zone_model->get($zone_code);
    if(!empty($zone))
    {
      if(!empty($items))
      {
        if(!empty($stock))
        {
          if(!empty($count))
          {
            foreach($items as $no => $item)
            {
              if($sc === FALSE)
              {
                break;
              }

              $in_stock = $stock[$no];
              $qty = $count[$no];
              $diff = $qty - $in_stock;

              //--- check if active diff exists
              $row = $this->check_stock_diff_model->get_active_diff_detail($zone_code, $item);
              if(!empty($row))
              {
                if($diff != 0)
                {
                  $arr = array(
                    'qty' => $diff
                  );

                  if(! $this->check_stock_diff_model->update($row->id, $arr))
                  {
                    $sc = FALSE;
                    $this->error = "Update ยอดต่างไม่สำเร็จ";
                  }
                }
                else
                {
                  $this->check_stock_diff_model->delete($row->id);
                }
              }
              else
              {
                if($diff != 0)
                {
                  $arr = array(
                    'zone_code' => $zone_code,
                    'product_code' => $item,
                    'qty' => $diff,
                    'status' => 0,
                    'user' => get_cookie('uname')
                  );

                  if(! $this->check_stock_diff_model->add($arr))
                  {
                    $sc = FALSE;
                    $this->error = "เพิ่มรายการไม่สำเร็จ";
                  }
                }
              }
            }
          }
          else
          {
            $sc = FALSE;
            $this->error = "ไม่พบรายการตรวจนับ";
          }
        }
        else
        {
          $sc = FALSE;
          $this->error = "ไม่พบสต็อกคงเหลือ";
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
      $this->error = "โซนไม่ถูกต้อง";
    }

    if($sc === TRUE)
    {
      set_message("Success");
    }
    else
    {
      set_error($this->error);
    }
    $page = $is_barcode == 1 ? 'check_barcode' : 'check';
    redirect("{$this->home}/{$page}/{$zone_code}/Y");
  }



  function remove_diff($id)
  {
    $sc = TRUE;
    if(! $this->check_stock_diff_model->delete($id))
    {
      $sc = FALSE;
      $this->error = "ลบรายการไม่สำเร็จ";
    }

    echo $sc === TRUE ? 'success' : $this->error;
  }


  public function get_item_by_barcode()
  {
    $barcode = $this->input->get('barcode');
    $zone_code = $this->input->get('zone_code');
    $qty = $this->input->get('qty');
    $no = $this->input->get('topRow');
    $item = $this->products_model->get_product_by_barcode($barcode);
    if(!empty($item))
    {
      $arr = array(
        'item' => (empty($item->old_code) ? $item->code : $item->code .' | '.$item->old_code),
        'itemCode' => $item->code,
        'onHandQty' => $this->stock_model->get_stock_zone($zone_code, $item->code),
        'barcode' => $barcode,
        'qty' => $qty,
        'no' => $no
      );

      echo json_encode($arr);
    }
    else
    {
      echo 'Invalid barcode';
    }
  }




  function clear_filter(){
    $filter = array(
      'check_product_code',
      'check_zone_code',
      'check_from_date',
      'check_to_date',
      'check_status',
      'check_user'
    );

    clear_filter($filter);
    echo 'done';
  }

} //--- end class
?>
