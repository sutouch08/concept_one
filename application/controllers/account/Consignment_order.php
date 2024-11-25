<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Consignment_order extends PS_Controller
{
  public $menu_code = 'ACCMOD';
	public $menu_group_code = 'AC';
  public $menu_sub_group_code = '';
	public $title = 'ตัดยอดฝากขาย(เทียม)';
  public $filter;
  public $error;
  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'account/consignment_order';
    $this->load->model('account/consignment_order_model');
    $this->load->model('masters/zone_model');
    $this->load->model('masters/warehouse_model');
    $this->load->model('masters/products_model');
    $this->load->helper('discount');
  }


  public function index()
  {
    $filter = array(
      'code' => get_filter('code', 'consign_code', ''),
      'customer' => get_filter('customer', 'consign_customer', ''),
      'zone' => get_filter('zone', 'consign_zone', ''),
      'from_date' => get_filter('from_date', 'consign_from_date', ''),
      'to_date' => get_filter('to_date', 'consign_to_date', ''),
      'status' => get_filter('status', 'consign_status', 'all'),
      'ref_code' => get_filter('ref_code', 'consign_ref_code', '')
    );

    //--- แสดงผลกี่รายการต่อหน้า
		$perpage = get_rows();
		//--- หาก user กำหนดการแสดงผลมามากเกินไป จำกัดไว้แค่ 300
		if($perpage > 300)
		{
			$perpage = 20;
		}

		$segment  = 4; //-- url segment
		$rows     = $this->consignment_order_model->count_rows($filter);
		//--- ส่งตัวแปรเข้าไป 4 ตัว base_url ,  total_row , perpage = 20, segment = 3
		$init	= pagination_config($this->home.'/index/', $rows, $perpage, $segment);
		$docs = $this->consignment_order_model->get_list($filter, $perpage, $this->uri->segment($segment));
    if(!empty($docs))
    {
      foreach($docs as $rs)
      {
        $rs->amount = $this->consignment_order_model->get_sum_amount($rs->code);
      }
    }

    $filter['docs'] = $docs;

		$this->pagination->initialize($init);
    $this->load->view('account/consignment_order/consignment_order_list', $filter);
  }



  public function add_new()
  {
    $this->load->view('account/consignment_order/consignment_order_add');
  }

  public function is_exists($code, $old_code = NULL)
  {
    $exists = $this->consignment_order_model->is_exists($code, $old_code);
    if($exists)
    {
      echo 'เลขที่เอกสารซ้ำ';
    }
    else
    {
      echo 'not_exists';
    }
  }

  public function add()
  {
    $sc = TRUE;
    if($this->pm->can_add)
    {
      if($this->input->post('date_add'))
      {
        $date_add = db_date($this->input->post('date_add'), TRUE);
        $zone = $this->zone_model->get($this->input->post('zone_code'));
        if($this->input->post('code'))
        {
          $code = $this->input->post('code');
        }
        else
        {
          $code = $this->get_new_code($date_add);
        }

        $bookcode = getConfig('BOOK_CODE_CONSIGNMENT_SOLD');

        $arr = array(
          'code' => $code,
          'bookcode' => $bookcode,
          'customer_code' => $this->input->post('customerCode'),
          'customer_name' => $this->input->post('customer'),
          'zone_code' => $zone->code,
          'zone_name' => $zone->name,
          'warehouse_code' => $zone->warehouse_code,
          'remark' => $this->input->post('remark'),
          'date_add' => $date_add,
          'user' => get_cookie('uname')
        );

        if(! $this->consignment_order_model->add($arr))
        {
          $sc = FALSE;
          set_error("เพิ่มเอกสารไม่สำเร็จ");
        }
      }
      else
      {
        $sc = FALSE;
        set_error('ไม่พบข้อมูล/ข้อมูลไม่ครบถ้วน');
      }
    }
    else
    {
      $sc = FALSE;
      set_error('คุณไม่มีสิทธิ์ในการเพิ่มเอกสาร');
    }

    if($sc === TRUE)
    {
      redirect($this->home.'/edit/'.$code);
    }
    else
    {
      redirect($this->home.'/add_new');
    }

  }



  public function edit($code)
  {
    $this->load->helper('print');
    $doc = $this->consignment_order_model->get($code);
    $details = $this->consignment_order_model->get_details($code);

    if(!empty($details))
    {
      foreach($details as $rs)
      {
        $rs->barcode = $this->products_model->get_barcode($rs->product_code);
      }
    }

    $gb_auz = getConfig('ALLOW_UNDER_ZERO');
    $wh_auz = $this->warehouse_model->is_auz($doc->warehouse_code);
    $auz = $gb_auz == 1 ? 1 : ($wh_auz === TRUE ? 1 : 0);
    $ds = array(
      'doc' => $doc,
      'details' => $details,
      'auz' => $auz
    );

    $this->load->view('account/consignment_order/consignment_order_edit', $ds);
  }


  //--- updte header data
  public function update()
  {
    $sc = TRUE;
    $code = $this->input->post('code');
    if($code)
    {
      if($this->pm->can_edit)
      {
        $arr = array(
          'date_add' => db_date($this->input->post('date'), TRUE),
          'remark' => trim($this->input->post('remark'))
        );

        if(! $this->consignment_order_model->update($code, $arr))
        {
          $sc = FALSE;
          $this->error = "ปรับปรุงข้อมูลไม่สำเร็จ";
        }
      }
      else
      {
        $sc = FALSE;
        $this->error = "คุณไม่มีสิทธิ์แก้ไขข้อมูล";
      }
    }
    else
    {
      $sc = FALSE;
      $this->error = "ไม่พบเลขที่เอกสาร";
    }

    echo $sc === TRUE ? 'success' : $this->error;
  }


  public function cancle($code)
  {
    $sc = TRUE;
    if($this->pm->can_delete)
    {
      $doc = $this->consignment_order_model->get($code);
      //--- check status
      if($doc->status == 1)
      {
        $sc = FALSE;
        $this->error = "คุณต้องยกเลิกการบันทึกก่อนยกเลิกเอกสาร";
      }
      else
      {
        if(! $this->consignment_order_model->drop_details($code))
        {
          $sc = FALSE;
          $this->error = "ลบรายการไม่สำเร็จ";
        }
        else
        {
          $arr = array(
            'status' => 2,
            'cancle_reason' => $this->input->post('reason'),
            'cancle_user' => $this->_user->uname
          );

          if(! $this->consignment_order_model->update($code, $arr))
          {
            $sc = FALSE;
            $this->error = "เปลี่ยนสถานะเอกสารไม่สำเร็จ";
          }
        }
      }
    }
    else
    {
      $sc = FALSE;
      $this->error = "คุณไม่มีสิทธิ์ยกเลิกเอกสาร";
    }

    echo $sc === TRUE ? 'success' : $this->error;
  }



  public function view_detail($code)
  {
    $this->load->helper('print');
    $doc = $this->consignment_order_model->get($code);
    $details = $this->consignment_order_model->get_details($code);
    if(!empty($details))
    {
      foreach($details as $rs)
      {
        $rs->barcode = $this->products_model->get_barcode($rs->product_code);
      }
    }

    $ds = array(
      'doc' => $doc,
      'details' => $details
    );

    $this->load->view('account/consignment_order/consignment_order_view_detail', $ds);
  }


  //---- add or update detail row by key in
  public function add_detail($code)
  {
    $sc = TRUE;
    if($this->input->post('product_code'))
    {
      $doc = $this->consignment_order_model->get($code);
      if(!empty($doc))
      {
        $this->load->model('stock/stock_model');

        $product_code = $this->input->post('product_code');
        $price = $this->input->post('price');
        $qty = $this->input->post('qty');
        $discLabel = $this->input->post('disc');
        $disc = parse_discount_text($discLabel, $price);
        $discount = $disc['discount_amount'];
        $amount = ($price - $discount) * $qty;

        $gb_auz = getConfig('ALLOW_UNDER_ZERO');
        $wh_auz = $this->warehouse_model->is_auz($doc->warehouse_code);
        $auz = $gb_auz == 1 ? TRUE : $wh_auz;

        $item = $this->products_model->get($product_code);
        $input_type = 1;  //--- 1 = key in , 2 = load diff, 3 = excel
        $stock = $item->count_stock == 1 ? $this->stock_model->get_consign_stock_zone($doc->zone_code, $item->code) : 10000000;
        $c_qty = $item->count_stock == 1 ? $this->consignment_order_model->get_unsave_qty($code, $item->code) : 0;
        $detail = $this->consignment_order_model->get_exists_detail($code, $product_code, $price, $discLabel, $input_type);
        $sum_qty = $qty + $c_qty;
        $id;
        if(empty($detail))
        {
          //--- ถ้าจำนวนที่ยังไม่บันทึก รวมกับจำนวนใหม่ไม่เกินยอดในโซน หรือ คลังสามารถติดลบได้
          if($sum_qty <= $stock OR $auz === TRUE)
          {
            //--- add new row
            $arr = array(
              'consign_code' => $code,
              'style_code' => $item->style_code,
              'product_code' => $item->code,
              'product_name' => $item->name,
              'cost' => $item->cost,
              'price' => $price,
              'qty' => $qty,
              'discount' => discountLabel($disc['discount1'], $disc['discount2'], $disc['discount3']),
              'discount_amount' => $discount * $qty,
              'amount' => $amount,
              'ref_code' => $doc->ref_code,
              'input_type' => $input_type
            );

            $id = $this->consignment_order_model->add_detail($arr); //-- return id if success

            if($id === FALSE )
            {
              $sc = FALSE;
              $this->error = "เพิ่มรายการไม่สำเร็จ";
            }

          }
          else
          {
            $sc = FALSE;
            $this->error = "<span>{$item->code} ยอดในโซนไม่พอตัด  ในโซน: {$stock} ยอดตัด : {$sum_qty} </span><br/>";
          }

        }
        else
        {
          //-- update new rows
          //--- ถ้าจำนวนที่ยังไม่บันทึก รวมกับจำนวนใหม่ไม่เกินยอดในโซน หรือ คลังสามารถติดลบได้
          $id = $detail->id;
          $new_qty = $qty + $detail->qty;
          if($sum_qty <= $stock OR $auz === TRUE)
          {
            //--- add new row
            $arr = array(
              'qty' => $new_qty,
              'discount_amount' => $discount * $new_qty,
              'amount' => ($price - $discount) * $new_qty
            );

            if(! $this->consignment_order_model->update_detail($id, $arr))
            {
              $sc = FALSE;
              $this->error = "ปรับปรุงรายการไม่สำเร็จ";
            }

          }
          else
          {
            $sc = FALSE;
            $this->error = "<span>{$item->code} ยอดในโซนไม่พอตัด  ในโซน: {$stock} ยอดตัด : {$sum_qty} </span><br/>";
          }
        }
      }
      else
      {
        $sc = FALSE;
        $this->error = "เลขที่เอกสารไม่ถูกต้อง";
      }
    }
    else
    {
      $sc = FALSE;
      $this->error = "รหัสสินค้าไม่ถูกต้อง";
    }

    if($sc === TRUE)
    {
      $rs = $this->consignment_order_model->get_detail($id);
      $ds = array(
        'id' => $rs->id,
        'barcode' => $item->barcode,
        'product' => $rs->product_code.' : '.$rs->product_name,
        'price' => round($rs->price,2),
        'qty' => $rs->qty,
        'discount' => $rs->discount,
        'amount' => $rs->amount
      );
    }

    echo $sc === TRUE ? json_encode($ds) : $this->error;
  }



  //---- add or update detail row by key in
  public function add_details($code)
  {
    $sc = TRUE;
    $data = $this->input->post('data');

    if(!empty($data))
    {
      $doc = $this->consignment_order_model->get($code);
      if(!empty($doc))
      {
        $this->load->model('stock/stock_model');
        $gb_auz = getConfig('ALLOW_UNDER_ZERO');
        $wh_auz = $this->warehouse_model->is_auz($doc->warehouse_code);
        $auz = $gb_auz == 1 ? TRUE : $wh_auz;
        $discLabel = $this->input->post('discount');

        foreach($data as $rs)
        {
          $product_code = $rs['code']; //-- รหัสสินค้า
          $qty = $rs['qty'];
          $item = $this->products_model->get($product_code);
          $price = $item->price;
          $disc = parse_discount_text($discLabel, $price);
          $discount = $disc['discount_amount'];
          $amount = ($price - $discount) * $qty;
          $input_type = 1;  //--- 1 = key in , 2 = load diff, 3 = excel
          $stock = $item->count_stock == 1 ? $this->stock_model->get_stock_zone($doc->zone_code, $item->code) : 10000000;
          $c_qty = $item->count_stock == 1 ? $this->consignment_order_model->get_unsave_qty($code, $item->code) : 0;
          $detail = $this->consignment_order_model->get_exists_detail($code, $product_code, $price, $discLabel, $input_type);
          $sum_qty = $qty + $c_qty;
          $id;

          if(empty($detail))
          {
            //--- ถ้าจำนวนที่ยังไม่บันทึก รวมกับจำนวนใหม่ไม่เกินยอดในโซน หรือ คลังสามารถติดลบได้
            if($sum_qty <= $stock OR $auz === TRUE)
            {
              //--- add new row
              $arr = array(
                'consign_code' => $code,
                'style_code' => $item->style_code,
                'product_code' => $item->code,
                'product_name' => $item->name,
                'cost' => $item->cost,
                'price' => $price,
                'qty' => $qty,
                'discount' => discountLabel($disc['discount1'], $disc['discount2'], $disc['discount3']),
                'discount_amount' => $discount * $qty,
                'amount' => $amount,
                'ref_code' => $doc->ref_code,
                'input_type' => $input_type
              );

              $id = $this->consignment_order_model->add_detail($arr); //-- return id if success

              if($id === FALSE )
              {
                $sc = FALSE;
                $this->error = "เพิ่มรายการไม่สำเร็จ";
              }

            }
            else
            {
              $sc = FALSE;
              $this->error .= "<span>{$item->code} ยอดในโซนไม่พอตัด  ในโซน: {$stock} ยอดตัด : {$sum_qty} </span><br/>";
            }
          }
          else
          {
            //-- update new rows
            //--- ถ้าจำนวนที่ยังไม่บันทึก รวมกับจำนวนใหม่ไม่เกินยอดในโซน หรือ คลังสามารถติดลบได้
            $id = $detail->id;
            $new_qty = $qty + $detail->qty;
            if($sum_qty <= $stock OR $auz === TRUE)
            {
              //--- add new row
              $arr = array(
                'qty' => $new_qty,
                'discount_amount' => $discount * $new_qty,
                'amount' => ($price - $discount) * $new_qty
              );

              if(! $this->consignment_order_model->update_detail($id, $arr))
              {
                $sc = FALSE;
                $this->error = "ปรับปรุงรายการไม่สำเร็จ";
              }

            }
            else
            {
              $sc = FALSE;
              $this->error = "ยอดในโซนไม่พอตัด {$product_code}";
            }
          }
        }

      }
      else
      {
        $sc = FALSE;
        $this->error = "เลขที่เอกสารไม่ถูกต้อง";
      }
    }
    else
    {
      $sc = FALSE;
      $this->error = "ไม่พบข้อมูล";
    }


    echo $sc === TRUE ? 'success' : $this->error;
  }





  public function save_consign($code)
  {
    $sc = TRUE;
    $this->load->model("stock/stock_model");
    $this->load->model("masters/warehouse_model");
    $this->load->model('inventory/movement_model');
    $this->load->model('inventory/delivery_order_model');
    $doc = $this->consignment_order_model->get($code);
    $gb_auz = getConfig('ALLOW_UNDER_ZERO');
    $wh_auz = $this->warehouse_model->is_auz($doc->warehouse_code);
    $auz = $gb_auz == 1 ? TRUE : $wh_auz ;

    if($doc->status == 0)
    {
      $details = $this->consignment_order_model->get_details($code);
      if(!empty($details))
      {
        $this->db->trans_begin();

        //--- check stock and update status each row
        foreach($details as $rs)
        {
          //--- get item info
          $item = $this->products_model->get($rs->product_code);

          if(!empty($item))
          {
            $stock = $item->count_stock == 1 ?$this->stock_model->get_consign_stock_zone($doc->zone_code, $item->code) : 1000000;
            $all_qty = $this->consignment_order_model->get_sum_order_qty($doc->code, $item->code);
            if($all_qty <= $stock OR $auz)
            {
              $final_price = $rs->amount/$rs->qty;
              //--- ข้อมูลสำหรับบันทึกยอดขาย
              $arr = array(
                      'reference' => $doc->code,
                      'role'   => $doc->role, ///--- ตัดยอดฝากขาย(shop)
                      'product_code'  => $rs->product_code,
                      'product_name'  => $rs->product_name,
                      'product_style' => $rs->style_code,
                      'cost'  => $rs->cost,
                      'price'  => $rs->price,
                      'sell'  => $final_price,
                      'qty'   => $rs->qty,
                      'discount_label'  => $rs->discount,
                      'discount_amount' => $rs->discount_amount,
                      'total_amount'   => $rs->amount,
                      'total_cost'   => $rs->cost * $rs->qty,
                      'margin'  =>  ($final_price * $rs->qty) - ($rs->cost * $rs->qty),
                      'id_policy'   => NULL,
                      'id_rule'     => NULL,
                      'customer_code' => $doc->customer_code,
                      'customer_ref' => NULL,
                      'sale_code'   => NULL,
                      'user' => $doc->user,
                      'date_add'  => $doc->date_add,
                      'zone_code' => $doc->zone_code,
                      'warehouse_code'  => $doc->warehouse_code,
                      'update_user' => get_cookie('uname'),
                      'budget_code' => NULL
              );

              //--- 1.บันทึกขาย
              if(! $this->delivery_order_model->sold($arr))
              {
                $sc = FALSE;
                $message = 'บันทึกขายไม่สำเร็จ';
                break;
              }

              //--- 2. update movement
              $arr = array(
                'reference' => $doc->code,
                'warehouse_code' => $doc->warehouse_code,
                'zone_code' => $doc->zone_code,
                'product_code' => $rs->product_code,
                'move_in' => 0,
                'move_out' => $rs->qty,
                'date_add' => $doc->date_add
              );

              if(! $this->movement_model->add($arr))
              {
                $sc = FALSE;
                $message = 'บันทึก movement ขาออกไม่สำเร็จ';
                break;
              }

              if(! $this->consignment_order_model->change_detail_status($rs->id, 1))
              {
                $sc = FALSE;
                $this->error = "บันทึกรายการไม่สำเร็จ : {$item->code}";
              }
            }
            else
            {
              $sc = FALSE;
              $this->error .= "<span>{$item->code} ยอดในโซนไม่พอตัด  ในโซน: {$stock} ยอดตัด : {$all_qty} </span><br/>";
            }
          }
          else
          {
            $sc = FALSE;
            $this->error = "ไม่พบรายการสินค้า : {$rs->product_code}";
          }
        }

        //--- if no error
        if($sc === TRUE)
        {
          if( ! $this->consignment_order_model->change_status($code, 1))
          {
            $sc = FALSE;
            $this->error = "บันทึกสถานะเอกสารไม่สำเร็จ";
          }
        }

        if($sc === FALSE)
        {
          $this->db->trans_rollback();
        }
        else
        {
          $this->db->trans_commit();
        }


        if($sc === TRUE )
        {
          $this->export_consignment_order($code);
        }
      }
    }
    else
    {
      $sc = FALSE;
      $this->error = "เอกสารถูกบันทึกไปแล้ว";
    }

    echo $sc === TRUE ? 'success' : $this->error;
  }



  public function unsave_consign($code)
  {
    $sc = TRUE;
    $this->load->model("stock/stock_model");
    $this->load->model("masters/warehouse_model");
    $this->load->model('inventory/movement_model');
    $this->load->model('inventory/invoice_model');
    $doc = $this->consignment_order_model->get($code);
    if(!empty($doc))
    {
      if($doc->status == 1)
      {
        //--- check order exists on SAP Consignment database;
        $sap = $this->consignment_order_model->get_sap_consignment_order_doc($code);
        if(!empty($sap))
        {
          $sc = FALSE;
          $this->error = "กรุณายกเลิกเอกสารใน SAP ก่อนย้อนสถานะ";
        }
        else
        {
          $middle = $this->consignment_order_model->get_middle_consignment_order_doc($code);
          if(!empty($middle))
          {
            foreach($middle as $rows)
            {
              if($this->consignment_order_model->drop_middle_exits_data($rows->DocEntry) === FALSE)
              {
                $sc = FALSE;
                $this->error = "ลบรายการที่ค้างใน Temp ไม่สำเร็จ";
              }
            }
          }

          $this->db->trans_begin();

          //--- remove movement
          if(! $this->movement_model->drop_movement($code))
          {
            $sc = FALSE;
            $this->error = "ลบ movement ไม่สำเร็จ";
          }
          //--- Remove sold data
          if(!$this->invoice_model->drop_all_sold($code))
          {
            $sc = FALSE;
            $this->error = "ลบยอดขาย ไม่สำเร็จ";
          }

          //--- change status details
          if(! $this->consignment_order_model->change_all_detail_status($code, 0))
          {
            $sc = FALSE;
            $this->error = "เปลี่ยนสถานะรายการไม่สำเร็จ";
          }

          //--- change document status
          if($sc === TRUE)
          {
            if(! $this->consignment_order_model->change_status($code, 0))
            {
              $sc = FALSE;
              $this->error = "เปลี่ยนสถานะเอกสารไม่สำเร็จ";
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
      }
    }
    else
    {
      $sc = FALSE;
      $this->error = "เลขที่เอกสารไม่ถูกต้อง";
    }



    echo $sc === TRUE ? 'success' : $this->error;
  }



  public function delete_detail($id)
  {
    $sc = TRUE;
    $ds = $this->consignment_order_model->get_detail($id);
    if(!empty($ds))
    {
      if($ds->status == 1)
      {
        $sc = FALSE;
        $this->error = "รายการถูกบันทึกแล้วไม่สามารถลบได้";
      }
      else
      {
        if(! $this->consignment_order_model->delete_detail($id))
        {
          $sc = FALSE;
          $this->error = "ลบรายการไม่สำเร็จ";
        }
      }
    }
    else
    {
      $sc = FALSE;
      $this->error = "ไม่พบรายการที่ต้องการลบ";
    }

    echo $sc === TRUE ? 'success' : $this->error;
  }



  public function delete_all_details($code)
  {
    $sc = TRUE;
    if($this->pm->can_add OR $this->pm->can_edit OR $this->pm->can_delete)
    {
      $doc = $this->consignment_order_model->get($code);
      if(!empty($doc))
      {
        if($doc->status == 1)
        {
          $sc = FALSE;
          $this->error = "เอกสารถูกบันทึกแล้วไม่สามารถลบได้";
        }
        else
        {
          $details = $this->consignment_order_model->get_details($code);
          if(!empty($details))
          {
            foreach($details as $rs)
            {
              if($rs->status == 1)
              {
                $sc = FALSE;
                $this->error = $rs->product_code." : รายการถูกบันทึกแล้วไม่สามารถลบได้";
              }
              else
              {
                if(!$this->consignment_order_model->delete_detail($rs->id))
                {
                  $sc = FALSE;
                  $this->error = $rs->product_code." : ลบรายการไม่สำเร็จ";
                }
              }
            }
          }
        }
      }
    }
    else
    {
      $sc = FALSE;
      $this->error = "คุณไม่มีสิทธิ์ดำเนินการ";
    }

    echo $sc === TRUE ? 'success' : $this->error;
  }


  public function import_excel_file($code)
  {
    $sc = TRUE;
    $this->load->library('excel');
    $this->load->model('stock/stock_model');

    $file = isset( $_FILES['excel'] ) ? $_FILES['excel'] : FALSE;

    if($file !== FALSE)
    {
      $file	= 'excel';
  		$config = array(   // initial config for upload class
  			"allowed_types" => "xlsx",
  			"upload_path" => $this->config->item('consign_file_path'),
  			"file_name"	=> $code.'-'.date('YmdHis'),
  			"max_size" => 5120,
  			"overwrite" => TRUE
  			);

  			$this->load->library("upload", $config);

  			if(! $this->upload->do_upload($file))
        {
          $sc = FALSE;
  				$this->error = $this->upload->display_errors();
  			}
        else
        {
          $info = $this->upload->data();
          /// read file
  				$excel = PHPExcel_IOFactory::load($info['full_path']);
  				//get only the Cell Collection
          $collection	= $excel->getActiveSheet()->toArray(NULL, TRUE, TRUE, TRUE);

          $i = 1;

          $doc = $this->consignment_order_model->get($code);
          $gb_auz = getConfig('ALLOW_UNDER_ZERO');
          $wh_auz = $this->warehouse_model->is_auz($doc->warehouse_code);
          $auz = $gb_auz == 1 ? TRUE : $wh_auz;

          $this->db->trans_begin();

          foreach($collection as $rs)
          {
            if($i > 1)
            {
              //--- skip hrader row
              $product_code = $rs['A'];
              $price = floatval(preg_replace('/[^\d.]/', '', $rs['B']));
              $qty = $rs['C'];
              $discLabel = $rs['D'];

              if(!empty($product_code))
              {
                $item = $this->products_model->get($product_code);

                if(!empty($item))
                {
                  if($item->active)
                  {
                    $disc = parse_discount_text($discLabel, $price);
                    $discount = $disc['discount_amount'];
                    $amount = ($price - $discount) * $qty;
                    $input_type = 3;  //--- 1 = key in , 2 = load diff, 3 = excel
                    $stock = $item->count_stock == 1 ? $this->stock_model->get_consign_stock_zone($doc->zone_code, $item->code) : 10000000;
                    $c_qty = $item->count_stock == 1 ? $this->consignment_order_model->get_unsave_qty($code, $item->code) : 0;
                    $detail = $this->consignment_order_model->get_exists_detail($code, $product_code, $price, $discLabel, $input_type);
                    $sum_qty = $qty + $c_qty;
                    if(empty($detail))
                    {
                      //--- ถ้าจำนวนที่ยังไม่บันทึก รวมกับจำนวนใหม่ไม่เกินยอดในโซน หรือ คลังสามารถติดลบได้
                      if($sum_qty <= $stock OR $auz === TRUE)
                      {
                        //--- add new row
                        $arr = array(
                          'consign_code' => $code,
                          'style_code' => $item->style_code,
                          'product_code' => $item->code,
                          'product_name' => $item->name,
                          'cost' => $item->cost,
                          'price' => $price,
                          'qty' => $qty,
                          'discount' => discountLabel($disc['discount1'], $disc['discount2'], $disc['discount3']),
                          'discount_amount' => $discount * $qty,
                          'amount' => $amount,
                          'ref_code' => NULL,
                          'input_type' => $input_type
                        );

                        $add = $this->consignment_order_model->add_detail($arr); //-- return id if success

                        if($add === FALSE )
                        {
                          $sc = FALSE;
                          $this->error = "เพิ่มรายการไม่สำเร็จ";
                        }

                      }
                      else
                      {
                        $sc = FALSE;
                        $this->error .= "<span>{$item->code} ยอดในโซนไม่พอตัด  ในโซน: {$stock} ยอดตัด : {$sum_qty} </span><br/>";

                      }
                    }
                    else
                    {
                      //-- update new rows
                      //--- ถ้าจำนวนที่ยังไม่บันทึก รวมกับจำนวนใหม่ไม่เกินยอดในโซน หรือ คลังสามารถติดลบได้
                      $sum_qty = $qty + $c_qty;
                      if($sum_qty <= $stock OR $auz === TRUE)
                      {
                        //--- add new row
                        $new_qty = $qty + $detail->qty;
                        $arr = array(
                          'qty' => $new_qty,
                          'discount_amount' => $discount * $new_qty,
                          'amount' => ($price - $discount) * $new_qty
                        );

                        if(! $this->consignment_order_model->update_detail($detail->id, $arr))
                        {
                          $sc = FALSE;
                          $this->error = "ปรับปรุงรายการไม่สำเร็จ";
                        }
                      }
                      else
                      {
                        $sc = FALSE;
                        $this->error .= "<span>{$item->code} ยอดในโซนไม่พอตัด  ในโซน: {$stock} ยอดตัด : {$sum_qty} </span><br/>";

                      }
                    } //--- end if empty detail
                  }
                  else
                  {
                    $sc = FALSE;
                    $this->error .= "<span>{$item->code} is Inactive </span><br/>";
                  }
                }
                else
                {
                  $sc = FALSE;
                  $this->error = "รหัสสินค้าไม่ถูกต้อง : {$product_code}";
                } //--- end if $item
              }
            } //--- end if $i

            $i++;
          } //--- endforeach

          if($sc === FALSE)
          {
            $this->db->trans_rollback();
          }
          else
          {
            $this->db->trans_commit();
          }
        }
    }
  	else
    {
      $sc = FALSE;
      $this->error = "Upload file not found";
    }

    echo $sc === TRUE ? 'success' : $this->error;
  }


  public function get_active_check_list($zone_code)
  {
    $ds = array();
    $this->load->model('inventory/consign_check_model');
    $list = $this->consign_check_model->get_active_check_list($zone_code); //--- saved and not valid

    if(!empty($list))
    {
      foreach($list as $rs)
      {
        $arr = array(
          'code' => $rs->code,
          'date_add' => thai_date($rs->date_add)
        );

        array_push($ds, $arr);
      }
    }
    else
    {
      array_push($ds, array('nodata' => 'nodata'));
    }

    echo json_encode($ds);
  }



  function load_check_diff($code)
  {
    $sc = TRUE;
    if($this->input->post('check_code'))
    {
      $this->load->model('inventory/consign_check_model');
      $doc = $this->consignment_order_model->get($code);
      $check_code = $this->input->post('check_code');
      $input_type = 2; //---- load diff
      $details = $this->consign_check_model->get_diff_details($check_code);
      if(!empty($details))
      {
        $this->db->trans_start();
        $this->consignment_order_model->update_ref_code($code, $check_code);
        foreach($details as $rs)
        {
          $item = $this->products_model->get($rs->product_code);
          $discLabel = $this->consignment_order_model->get_item_gp($item->code, $doc->zone_code);
          $disc = parse_discount_text($discLabel, $item->price);
          $discount = $disc['discount_amount'];
          $amount = ($item->price - $discount) * $rs->diff;
          $detail = $this->consignment_order_model->get_exists_detail($code, $item->code, $item->price, $discLabel, $input_type);
          if(empty($detail))
          {
            //--- add new row
            $arr = array(
              'consign_code' => $code,
              'style_code' => $item->style_code,
              'product_code' => $item->code,
              'product_name' => $item->name,
              'cost' => $item->cost,
              'price' => $item->price,
              'qty' => $rs->diff,
              'discount' => $discLabel,
              'discount_amount' => $discount * $rs->diff,
              'amount' => $amount,
              'ref_code' => $check_code,
              'input_type' => $input_type
            );

            $this->consignment_order_model->add_detail($arr);
          }
          else
          {

            //-- update new rows
            //--- ถ้าจำนวนที่ยังไม่บันทึก รวมกับจำนวนใหม่ไม่เกินยอดในโซน หรือ คลังสามารถติดลบได้
            $new_qty = $rs->diff + $detail->qty;
            //--- add new row
            $arr = array(
              'qty' => $new_qty,
              'discount_amount' => $discount * $new_qty,
              'amount' => ($item->price - $discount) * $new_qty
            );

            $this->consignment_order_model->update_detail($detail->id, $arr);
          }
        }
      }

      $this->consign_check_model->update_ref_code($check_code, $code, 1);

      $this->db->trans_complete();

      if($this->db->trans_status() === FALSE)
      {
        $this->error = "เพิ่มรายการไม่สำเร็จ";
        $sc = FALSE;
      }
    }
    else
    {
      $sc = FALSE;
      $this->error = "ไม่พบเลขที่เอกสารกระทบยอด";
    }

    echo $sc === TRUE ? 'success' : $this->error;
  }


  public function remove_import_details($code)
  {
    $sc = TRUE;
    if($this->input->post('check_code'))
    {
      $this->load->model('inventory/consign_check_model');
      $doc = $this->consignment_order_model->get($code);
      $check_code = $this->input->post('check_code');
      $input_type = 2; //---- load diff

      $saved = $this->consignment_order_model->has_saved_imported($code, $check_code);

      if($saved === FALSE)
      {
        $this->db->trans_start();

        //--- delete details
        $this->consignment_order_model->drop_import_details($code, $check_code);

        //--- update ref_code
        $this->consignment_order_model->update_ref_code($code, NULL);

        //-- unlink consign_check
        $this->consign_check_model->update_ref_code($check_code, NULL, 0);

        $this->db->trans_complete();

        if($this->db->trans_status() === FALSE)
        {
          $sc = FALSE;
          $this->error = "ลบรายการไม่สำเร็จ";
        }

      }
      else
      {
        $sc = FALSE;
        $this->error = "ไม่สามารถลบได้เนื่องจากรายการถูกบันทึกแล้ว";
      }

    }
    else
    {
      $sc = FALSE;
      $this->error = "ไม่พบเลขที่เอกสารกระทบยอด";
    }

    echo $sc === TRUE ? 'success' : $this->error;
  }




  public function update_price($code)
  {
    $price_list = $this->input->post('price');
    if(! empty($price_list))
    {
      foreach($price_list as $id => $price)
      {
        $detail = $this->consignment_order_model->get_detail($id);
        if(!empty($detail))
        {
          $disc = parse_discount_text($detail->discount, $price);
          $discount = $disc['discount_amount']; //-- discount amount per pcs

          $arr = array(
            'price' => $price,
            'discount_amount' => $discount * $detail->qty,
            'amount' => ($price - $discount) * $detail->qty
          );

          $this->consignment_order_model->update_detail($id, $arr);
        }
      }
    }

    echo 'success';
  }



  public function update_discount($code)
  {
    $dis_list = $this->input->post('disc');
    if(!empty($dis_list))
    {
      foreach($dis_list as $id => $discLabel)
      {
        $detail = $this->consignment_order_model->get_detail($id);
        if(!empty($detail))
        {
          $disc = parse_discount_text($discLabel, $detail->price);
          $discount = $disc['discount_amount'];

          $arr = array(
            'discount' => discountLabel($disc['discount1'], $disc['discount2'], $disc['discount3']),
            'discount_amount' => $discount * $detail->qty,
            'amount' => ($detail->price - $discount) * $detail->qty
          );

          $this->consignment_order_model->update_detail($id, $arr);
        }
      }
    }

    echo 'success';
  }


  public function get_item_by_code()
  {
    if($this->input->get('code'))
    {
      $this->load->model('stock/stock_model');

      $product_code = $this->input->get('code');
      $zone_code = $this->input->get('zone_code');
      $item = $this->products_model->get($product_code);
      if(!empty($item))
      {
        $gp  = $this->consignment_order_model->get_item_gp($item->code, $zone_code);
        $stock = $item->count_stock == 1 ? $this->stock_model->get_consign_stock_zone($zone_code, $item->code) : 0;

        $arr = array(
          'pdCode' => $item->code,
          'barcode' => $item->barcode,
          'product' => $item->code,
          'price' => round($item->price, 2),
          'disc' => $gp,
          'stock' => $stock,
          'count_stock' => $item->count_stock
        );

        $sc = json_encode($arr);
      }
      else
      {
        $sc = 'สินค้าไม่ถูกต้อง';
      }

      echo $sc;
    }
    else
    {
      echo "สินค้าไม่ถูกต้อง";
    }
  }


  public function get_item_by_barcode()
  {
    if($this->input->get('barcode'))
    {
      $this->load->model('stock/stock_model');

      $barcode = $this->input->get('barcode');
      $zone_code = $this->input->get('zone_code');
      $item = $this->products_model->get_product_by_barcode($barcode);
      if(!empty($item))
      {
        $gp  = $this->consignment_order_model->get_item_gp($item->code, $zone_code);
        $stock = $item->count_stock == 1 ? $this->stock_model->get_consign_stock_zone($zone_code, $item->code) : 0;

        $arr = array(
          'pdCode' => $item->code,
          'barcode' => $item->barcode,
          'product' => $item->code,
          'price' => round($item->price, 2),
          'disc' => $gp,
          'stock' => $stock,
          'count_stock' => $item->count_stock
        );

        $sc = json_encode($arr);
      }
      else
      {
        $sc = 'สินค้าไม่ถูกต้อง';
      }

      echo $sc;
    }
    else
    {
      echo "สินค้าไม่ถูกต้อง";
    }
  }



  public function get_sample_file($token)
  {
    //--- load excel library
    $this->load->library('excel');

    $this->excel->setActiveSheetIndex(0);
    $this->excel->getActiveSheet()->setTitle('Sample');

    //--- header
    $this->excel->getActiveSheet()->setCellValue('A1', 'Items');
    $this->excel->getActiveSheet()->setCellValue('B1', 'Price');
    $this->excel->getActiveSheet()->setCellValue('C1', 'Qty');
    $this->excel->getActiveSheet()->setCellValue('D1', 'Discount');

    //--- sample data
    $this->excel->getActiveSheet()->setCellValue('A2', 'WA-1234-AA-L');
    $this->excel->getActiveSheet()->setCellValue('B2', '399');
    $this->excel->getActiveSheet()->setCellValue('C2', '2');
    $this->excel->getActiveSheet()->setCellValue('D2', '20%+5%');


    setToken($token);

    $file_name = "Consign_sample.xlsx";
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'); /// form excel 2007 XLSX
    header('Content-Disposition: attachment;filename="'.$file_name.'"');
    $writer = PHPExcel_IOFactory::createWriter($this->excel, 'Excel2007');
    $writer->save('php://output');
  }


  public function export_consign($code)
  {
    $rs = $this->export_consignment_order($code);
    if($rs === FALSE)
    {
      echo $this->error;
    }
    else
    {
      echo 'success';
    }
  }



  public function print_consign($code)
  {
    $this->load->library('printer');

    $doc = $this->consignment_order_model->get($code);
    if(!empty($doc))
    {
      $doc->warehouse_name = $this->warehouse_model->get_name($doc->warehouse_code);
    }

    $details = $this->consignment_order_model->get_details($code);
    if(!empty($details))
    {
      foreach($details as $rs)
      {
        $rs->barcode = $this->products_model->get_barcode($rs->product_code);
      }
    }

    $ds = array(
      'doc' => $doc,
      'details' => $details
    );

    $this->load->view('print/print_consign_sold', $ds);
  }



  public function get_new_code($date)
  {
    $date = $date == '' ? date('Y-m-d') : $date;
    $Y = date('y', strtotime($date));
    $M = date('m', strtotime($date));
    $prefix = getConfig('PREFIX_CONSIGNMENT_SOLD');
    $run_digit = getConfig('RUN_DIGIT_CONSIGNMENT_SOLD');
    $pre = $prefix .'-'.$Y.$M;
    $code = $this->consignment_order_model->get_max_code($pre);
    if(!empty($code))
    {
      $run_no = mb_substr($code, ($run_digit*-1), NULL, 'UTF-8') + 1;
      $new_code = $prefix . '-' . $Y . $M . sprintf('%0'.$run_digit.'d', $run_no);
    }
    else
    {
      $new_code = $prefix . '-' . $Y . $M . sprintf('%0'.$run_digit.'d', '001');
    }

    return $new_code;
  }


  public function export_consignment_order($code)
  {
    $sc = TRUE;
    $this->load->library('export');
    if(! $this->export->export_consignment_order($code))
    {
      $sc = FALSE;
      $this->error = trim($this->export->error);
    }

    return $sc;
  }



  public function clear_filter()
  {
    $filter = array(
      'consign_code',
      'consign_customer',
      'consign_zone',
      'consign_from_date',
      'consign_to_date',
      'consign_status',
      'consign_ref_code'
    );
    clear_filter($filter);
  }


} //---- end class
 ?>
