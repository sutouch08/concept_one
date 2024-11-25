
<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Delivery_order extends PS_Controller
{
  public $menu_code = 'ICODDO';
	public $menu_group_code = 'IC';
  public $menu_sub_group_code = 'PICKPACK';
	public $title = 'รายการรอเปิดบิล';
  public $filter;
  public $error;
  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'inventory/delivery_order';
    $this->load->model('inventory/delivery_order_model');
    $this->load->model('orders/orders_model');
    $this->load->model('orders/order_state_model');
  }


  public function index()
  {
    $this->load->model('masters/customers_model');
    $this->load->helper('channels');
    $this->load->helper('order');
    $this->load->helper('warehouse');
    
    $filter = array(
      'code' => get_filter('code', 'ic_code', ''),
      'reference' => get_filter('reference', 'ic_reference', ''),
      'customer' => get_filter('customer', 'ic_customer', ''),
      'user' => get_filter('user', 'ic_user', 'all'),
      'role' => get_filter('role', 'ic_role', ''),
      'channels' => get_filter('channels', 'ic_channels', ''),
      'from_date' => get_filter('from_date', 'ic_from_date', ''),
      'to_date' => get_filter('to_date', 'ic_to_date', ''),
      'sort_by' => get_filter('sort_by', 'ic_sort_by', ''),
      'order_by' => get_filter('order_by', 'ic_order_by', ''),
      'warehouse' => get_filter('warehouse', 'ic_warehouse', 'all')
    );

		//--- แสดงผลกี่รายการต่อหน้า
		$perpage = get_rows();
		//--- หาก user กำหนดการแสดงผลมามากเกินไป จำกัดไว้แค่ 300
		if($perpage > 300)
		{
			$perpage = 20;
		}

		$segment  = 4; //-- url segment
		$rows     = $this->delivery_order_model->count_rows($filter, 7);
		//--- ส่งตัวแปรเข้าไป 4 ตัว base_url ,  total_row , perpage = 20, segment = 3
		$init	    = pagination_config($this->home.'/index/', $rows, $perpage, $segment);
		$orders   = $this->delivery_order_model->get_data($filter, $perpage, $this->uri->segment($segment), 7);

    $filter['orders'] = $orders;

		$this->pagination->initialize($init);
    $this->load->view('inventory/delivery_order/delivery_list', $filter);
  }


  public function confirm_order()
  {
    $sc = TRUE;

    $this->load->model('masters/products_model');
    $this->load->model('inventory/qc_model');
    $this->load->model('inventory/buffer_model');
    $this->load->model('inventory/cancle_model');
    $this->load->model('inventory/movement_model');
    $this->load->helper('discount');

    $useQc = getConfig('USE_QC') == 1 ? TRUE : FALSE;

    $code = $this->input->post('order_code');

    if($code)
    {
      $order = $this->orders_model->get($code);

      $vat_type = $order->vat_type;

			$date_add = getConfig('ORDER_SOLD_DATE') == 'D' ? $order->date_add : now();

      if($order->role == 'T' OR $order->role == 'Q')
      {
        $this->load->model('inventory/transform_model');
      }

      if($order->role == 'L')
      {
        $this->load->model('inventory/lend_model');
      }

      if($order->state == 7)
      {
        $this->db->trans_begin();

        //--- change state
       $this->orders_model->change_state($code, 8);

			 if(empty($order->shipped_date))
			 {
				 $this->orders_model->update($code, array('shipped_date' => now())); //--- update shipped date
			 }


        //--- add state event
        $arr = array(
          'order_code' => $code,
          'state' => 8,
          'update_user' => get_cookie('uname')
        );

        $this->order_state_model->add_state($arr);

        //---- รายการทีรอการเปิดบิล (เฉพาะที่นับสต็อก)
        $bill = $this->delivery_order_model->get_bill_detail($code, $useQc);

        if( ! empty($bill))
        {
          foreach($bill as $rs)
          {
            //--- ถ้ามีรายการที่ไมสำเร็จ ออกจาก loop ทันที
            if($sc === FALSE)
            {
              break;
            }


            //--- ถ้ายอดตรวจ น้อยกว่า หรือ เท่ากับ ยอดสั่ง ใช้ยอดตรวจในการตัด buffer
            //--- ถ้ายอดตวจ มากกว่า ยอดสั่ง ให้ใช้ยอดสั่งในการตัด buffer (บางทีอาจมีการแก้ไขออเดอร์หลังจากมีการตรวจสินค้าแล้ว)
            $sell_qty = $useQc ? ($rs->order_qty >= $rs->qc ? $rs->qc : $rs->order_qty) : ($rs->order_qty >= $rs->prepared ? $rs->prepared : $rs->order_qty);

            //--- ดึงข้อมูลสินค้าที่จัดไปแล้วตามสินค้า
            $buffers = $this->buffer_model->get_details($code, $rs->product_code, $rs->id);

            if( ! empty($buffers))
            {
              $no = 0;

              foreach($buffers as $rm)
              {
                if($sell_qty > 0)
                {
                //--- ถ้ายอดใน buffer น้อยกว่าหรือเท่ากับยอดสั่งซื้อ (แยกแต่ละโซน น้อยกว่าหรือเท่ากับยอดสั่ง (ซึ่งควรเป็นแบบนี้))
                  $buffer_qty = $rm->qty <= $sell_qty ? $rm->qty : $sell_qty;

                  //--- ทำยอดให้เป็นลบเพื่อตัดยอดออก เพราะใน function  ใช้การบวก
                  $qty = $buffer_qty * (-1);

                  //--- 1. ตัดยอดออกจาก buffer
                  //--- นำจำนวนติดลบบวกกลับเข้าไปใน buffer เพื่อตัดยอดให้น้อยลง

                  if($this->buffer_model->update($rm->order_code, $rm->product_code, $rm->zone_code, $qty, $rs->id) !== TRUE)
                  {
                    $sc = FALSE;
                    $this->error = 'ปรับยอดใน buffer ไม่สำเร็จ';
                    break;
                  }

                  //--- ลดยอด sell qty ลงตามยอด buffer ทีลดลงไป
                  $sell_qty += $qty;

                  //--- 2. update movement
                  $arr = array(
                    'reference' => $order->code,
                    'warehouse_code' => $rm->warehouse_code,
                    'zone_code' => $rm->zone_code,
                    'product_code' => $rm->product_code,
                    'move_in' => 0,
                    'move_out' => $buffer_qty,
                    'date_add' => $date_add
                  );

                  if($this->movement_model->add($arr) === FALSE)
                  {
                    $sc = FALSE;
                    $this->error = 'บันทึก movement ขาออกไม่สำเร็จ';
                    break;
                  }

                  $priceBefDi = $vat_type == 'E' ? $rs->final_price : remove_vat($rs->final_price, $rs->vat_rate);
                  $totalBfDisc = $rs->final_price * $buffer_qty; //--- มูลค่าก่อนส่วนลดท้ายบิล (ลดในรายการแล้ว)
                  $avgBillDiscAmount = $rs->avgBillDiscAmount; //--- มูลค่าส่วนลดท้ายบิลเฉลี่ยต่อ 1 ชิ้น
                  $sumBillDiscAmount = $totalBfDisc * $avgBillDiscAmount;
                  $PriceAfDisc = $rs->final_price - $avgBillDiscAmount; //---- ราคาหลังส่วนลดท้ายบิล
                  $totalDiscount = $rs->discount_amount * $buffer_qty;
                  $total_amount = $totalBfDisc - $sumBillDiscAmount; //---- มูลค่ารวมหลังส่วนลดท้ายบิล
                  $total_cost = $rs->cost * $buffer_qty;

                  //--- ข้อมูลสำหรับบันทึกยอดขาย
                  $arr = array(
                    'reference' => $order->code,
                    'role'   => $order->role,
                    'payment_code'   => $order->payment_code,
                    'channels_code'  => $order->channels_code,
                    'product_code'  => $rs->product_code,
                    'product_name'  => $rs->product_name,
                    'product_style' => $rs->style_code,
                    'unit_code' => $this->products_model->get_unit_code($rs->product_code),
                    'cost'  => $rs->cost,
                    'price'  => $rs->price,
                    'sell'  => $rs->final_price,
                    'qty'   => $buffer_qty,
                    'discount_label'  => discountLabel($rs->discount1, $rs->discount2, $rs->discount3),
                    'avgBillDiscAmount' => $avgBillDiscAmount, //--- มูลค่ารวมส่วนลดท้ายบิล1เฉลี่ยนต่อ 1 ชิ้นของบรรทัดนี้
                    'sumBillDiscAmount' => $sumBillDiscAmount, //--- มูลค่ารวมส่วนลดท้ายบิลเฉลี่ยของบรรทัดนี้
                    'discount_amount' => $totalDiscount, //---- ส่วนลดในรายการ ยังไม่รวมส่วนลดท้ายบิล
                    'total_amount'   => $totalBfDisc, //----- มูลค่ารวมต่อรายการ ยังไม่รวมส่วนลดท้ายบิล (รวม VAT)
                    'total_cost'   => $total_cost,
                    'margin'  =>  $totalBfDisc - $total_cost,
                    'VatType' => $order->vat_type,
                    'VatCode' => $rs->vat_code,
                    'VatRate' => $rs->vat_rate,
                    'PriceBefDi' => $priceBefDi,
                    'PriceBfVAT' => $vat_type == 'E' ? $PriceAfDisc : remove_vat($PriceAfDisc, $rs->vat_rate), //--- ราคาหลังส่วนลดท้ายบิล ไม่รวม VAT
                    'TotalBfVAT' => $vat_type == 'E' ? $total_amount : remove_vat($total_amount, $rs->vat_rate), //---- มูลค่ารวมหลังส่วนลดท้ายบิล ไม่รวม VAT
                    'VatSum' => get_vat_amount($total_amount, $rs->vat_rate, $vat_type),
                    'id_policy'   => $rs->id_policy,
                    'id_rule'     => $rs->id_rule,
                    'customer_code' => $order->customer_code,
                    'customer_ref' => $order->customer_ref,
                    'sale_code'   => $order->sale_code,
                    'user' => $order->user,
                    'date_add'  => $date_add, //---- เปลี่ยนไปตาม config ORDER_SOLD_DATE
                    'zone_code' => $rm->zone_code,
                    'warehouse_code'  => $rm->warehouse_code,
                    'update_user' => get_cookie('uname'),
                    'budget_code' => $order->budget_code,
                    'empID' => $order->empID,
                    'empName' => $order->empName,
                    'approver' => $order->approver,
                    'order_detail_id' => $rs->id,
                    'so_line_id' => $rs->line_id,
                    'so_code' => $rs->baseCode,
                    'line_text' => $rs->line_text
                  );

                  //--- 3. บันทึกยอดขาย
                  if( ! $this->delivery_order_model->sold($arr))
                  {
                    $sc = FALSE;
                    $this->error = 'บันทึกขายไม่สำเร็จ';
                    break;
                  }

                  //---- update order billed qty
                  if($sc === TRUE)
                  {
                    if( ! $this->orders_model->update_billed_qty($rs->id, $buffer_qty))
                    {
                      $sc = FALSE;
                      $this->error = "Failed to update order billed qty";
                    }
                  }
                } //--- end if sell_qty > 0
              } //--- end foreach $buffers
            } //--- end if wmpty ($buffers)


            //------ ส่วนนี้สำหรับโอนเข้าคลังระหว่างทำ
            //------ หากเป็นออเดอร์เบิกแปรสภาพ
            if($order->role == 'T' OR $order->role == 'Q')
            {
              //--- ตัวเลขที่มีการเปิดบิล
              // $sold_qty = ($rs->order_qty >= $rs->qc) ? $rs->qc : $rs->order_qty;
              $sold_qty = $useQc ? ($rs->order_qty >= $rs->qc ? $rs->qc : $rs->order_qty) : ($rs->order_qty >= $rs->prepared ? $rs->prepared : $rs->order_qty);

              //--- ยอดสินค้าที่มีการเชื่อมโยงไว้ในตาราง tbl_order_transform_detail (เอาไว้โอนเข้าคลังระหว่างทำ รอรับเข้า)
              //--- ถ้ามีการเชื่อมโยงไว้ ยอดต้องมากกว่า 0 ถ้ายอดเป็น 0 แสดงว่าไม่ได้เชื่อมโยงไว้
              $trans_list = $this->transform_model->get_transform_product($rs->id);

              if(!empty($trans_list))
              {
                //--- ถ้าไม่มีการเชื่อมโยงไว้
                foreach($trans_list as $ts)
                {
                  //--- ถ้าจำนวนที่เชื่อมโยงไว้ น้อยกว่า หรือ เท่ากับ จำนวนที่ตรวจได้ (ไม่เกินที่สั่งไป)
                  //--- แสดงว่าได้ของครบตามที่ผูกไว้ ให้ใช้ตัวเลขที่ผูกไว้ได้เลย
                  //--- แต่ถ้าได้จำนวนที่ผูกไว้มากกว่าที่ตรวจได้ แสดงว่า ได้สินค้าไม่ครบ ให้ใช้จำนวนที่ตรวจได้แทน
                  $move_qty = $ts->order_qty <= $sold_qty ? $ts->order_qty : $sold_qty;

                  if( $move_qty > 0)
                  {
                    //--- update ยอดเปิดบิลใน tbl_order_transform_detail field sold_qty
                    if($this->transform_model->update_sold_qty($ts->id, $move_qty) === TRUE )
                    {
                      $sold_qty -= $move_qty;
                    }
                    else
                    {
                      $sc = FALSE;
                      $this->error = 'ปรับปรุงยอดรายการค้างรับไม่สำเร็จ';
                    }
                  }
                }
              }
            }


            //--- if lend
            if($order->role == 'L')
            {
              //--- ตัวเลขที่มีการเปิดบิล
              //$sold_qty = ($rs->order_qty >= $rs->qc) ? $rs->qc : $rs->order_qty;
              $sold_qty = $useQc ? ($rs->order_qty >= $rs->qc ? $rs->qc : $rs->order_qty) : ($rs->order_qty >= $rs->prepared ? $rs->prepared : $rs->order_qty);

              $arr = array(
                'order_code' => $code,
                'product_code' => $rs->product_code,
                'product_name' => $rs->product_name,
                'qty' => $sold_qty,
                'empID' => $order->empID
              );

              if($this->lend_model->add_detail($arr) === FALSE)
              {
                $sc = FALSE;
                $this->error = 'เพิ่มรายการค้างรับไม่สำเร็จ';
              }
            }

          } //--- end foreach $bill
        } //--- end if empty($bill)




        //--- เคลียร์ยอดค้างที่จัดเกินมาไปที่ cancle หรือ เคลียร์ยอดที่เป็น 0
        $buffer = $this->buffer_model->get_all_details($code);
        //--- ถ้ายังมีรายการที่ค้างอยู่ใน buffer เคลียร์เข้า cancle
        if(!empty($buffer))
        {
          foreach($buffer as $rs)
          {
            if($rs->qty != 0)
            {
              $arr = array(
                'order_code' => $rs->order_code,
                'product_code' => $rs->product_code,
                'warehouse_code' => $rs->warehouse_code,
                'zone_code' => $rs->zone_code,
                'qty' => $rs->qty,
                'user' => get_cookie('uname'),
                'order_detail_id' => $rs->order_detail_id
              );

              if($this->cancle_model->add($arr) === FALSE)
              {
                $sc = FALSE;
                $this->error = 'เคลียร์ยอดค้างเข้า cancle ไม่สำเร็จ';
                break;
              }
            }

            if($this->buffer_model->delete($rs->id) === FALSE)
            {
              $sc = FALSE;
              $this->error = 'ลบ Buffer ที่ค้างอยู่ไม่สำเร็จ';
              break;
            }
          }
        }


        //--- บันทึกขายรายการที่ไม่นับสต็อก
        $bill = $this->delivery_order_model->get_non_count_bill_detail($order->code);

        if(!empty($bill))
        {
          foreach($bill as $rs)
          {
            //--- ข้อมูลสำหรับบันทึกยอดขาย
            $priceBefDi = $vat_type == 'E' ? $rs->final_price : remove_vat($rs->final_price, $rs->vat_rate);
            $totalBfDisc = $rs->final_price * $rs->qty; //--- มูลค่าก่อนส่วนลดท้ายบิล (ลดในรายการแล้ว)
            $avgBillDiscAmount = $rs->avgBillDiscAmount; //--- มูลค่าส่วนลดท้ายบิลเฉลี่ยต่อ 1 ชิ้น
            $sumBillDiscAmount = $totalBfDisc * $avgBillDiscAmount; //--- มูลค่าส่วนลดท้ายบิลต่อรายการ
            $PriceAfDisc = $rs->final_price - $avgBillDiscAmount; //---- ราคาหลังส่วนลดท้ายบิล
            $totalDiscount = $rs->discount_amount * $rs->qty;
            $total_amount = $totalBfDisc - $sumBillDiscAmount; //--- มูลค่าหลังส่วนลดท้ายบิล
            $total_cost = $rs->cost * $rs->qty;

            $arr = array(
              'reference' => $order->code,
              'role'   => $order->role,
              'payment_code'   => $order->payment_code,
              'channels_code'  => $order->channels_code,
              'product_code'  => $rs->product_code,
              'product_name'  => $rs->product_name,
              'product_style' => $rs->style_code,
              'unit_code' => $this->products_model->get_unit_code($rs->product_code),
              'cost'  => $rs->cost,
              'price'  => $rs->price,
              'sell'  => $rs->final_price,
              'qty'   => $rs->qty,
              'discount_label'  => discountLabel($rs->discount1, $rs->discount2, $rs->discount3),
              'sumBillDiscAmount' => $sumBillDiscAmount, //---- ส่วนลดท้ายบิลเฉลี่ย รวมของบรรทัดนี้
              'avgBillDiscAmount' => $avgBillDiscAmount, //--- ส่วนลดท้ายบิลเฉลี่ย ต่อ 1 ชิ้น ของบรรทัดนี้
              'discount_amount' => $totalDiscount, //--- ส่วนลดรวม ก่อนส่วนลดท้ายบิล
              'total_amount'   => $totalBfDisc,
              'total_cost'   => $total_cost,
              'margin'  => $total_amount - $total_cost,
              'VatType' => $order->vat_type,
              'VatCode' => $rs->vat_code,
              'VatRate' => $rs->vat_rate,
              'PriceBefDi' => $priceBefDi,
              'PriceBfVAT' => $vat_type == 'E' ? $PriceAfDisc : remove_vat($PriceAfDisc, $rs->vat_rate), //--- ราคาหลังส่วนลดท้ายบิล ไม่รวม VAT
              'TotalBfVAT' => $vat_type == 'E' ? $total_amount : remove_vat($total_amount, $rs->vat_rate), //---- มูลค่ารวมหลังส่วนลดท้ายบิล ไม่รวม VAT
              'VatSum' => get_vat_amount($total_amount, $rs->vat_rate, $vat_type), //--- มูลค่า VAT หลังส่วนลดท้ายบิล
              'id_policy'   => $rs->id_policy,
              'id_rule'     => $rs->id_rule,
              'customer_code' => $order->customer_code,
              'customer_ref' => $order->customer_ref,
              'sale_code'   => $order->sale_code,
              'user' => $order->user,
              'date_add'  => $date_add, //--- เปลี่ยนตาม Config ORDER_SOLD_DATE
              'zone_code' => NULL,
              'warehouse_code'  => NULL,
              'update_user' => get_cookie('uname'),
              'budget_code' => $order->budget_code,
              'is_count' => 0,
              'empID' => $order->empID,
              'empName' => $order->empName,
              'approver' => $order->approver,
              'order_detail_id' => $rs->id,
              'so_line_id' => $rs->line_id,
              'so_code' => $rs->baseCode,
              'line_text' => $rs->line_text
            );

            //--- 3. บันทึกยอดขาย
            if($this->delivery_order_model->sold($arr) !== TRUE)
            {
              $sc = FALSE;
              $this->error = 'บันทึกขายไม่สำเร็จ';
              break;
            }

            //---- update order billed qty
            if($sc === TRUE)
            {
              if( ! $this->orders_model->update_billed_qty($rs->id, $rs->qty))
              {
                $sc = FALSE;
                $this->error = "Failed to update order billed qty";
              }
            }
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
      } //--- end if state == 7
      else
      {
        $sc = FALSE;
        $this->error = "Invalid order status";
      }
    }
    else
    {
      $sc = FALSE;
      $this->error = 'order code not found';
    }

    if($sc === TRUE)
    {
      $role = array(
        'S' => 'S',
        'U' => 'U',
        'P' => 'P'
      );

      if(empty($role[$order->role]) OR empty($order->isNew))
      {
        $this->do_export($code);
      }
    }

    echo $sc === TRUE ? 'success' : $this->error;
  }


  public function view_detail($code)
  {
    $this->load->model('masters/customers_model');
    $this->load->model('inventory/qc_model');
		$this->load->model('masters/warehouse_model');
		$this->load->model('masters/channels_model');
		$this->load->model('masters/payment_methods_model');
    $this->load->helper('order');
    $this->load->helper('discount');
    $this->load->helper('saleman');
    $this->load->helper('channels');
    $this->load->helper('warehouse');

    $useQc = getConfig('USE_QC') == 1 ? TRUE : FALSE;

    $order = $this->orders_model->get($code);
    $order->customer_name = $this->customers_model->get_name($order->customer_code);
		$order->warehouse_name = $this->warehouse_model->get_name($order->warehouse_code);
		$order->channels_name = $this->channels_model->get_name($order->channels_code);


    if($order->role == 'C' OR $order->role == 'N' OR $order->role == 'L')
    {
      $this->load->model('masters/zone_model');
      $order->zone_name = $this->zone_model->get_name($order->zone_code);
    }

    $details = $this->delivery_order_model->get_billed_detail($code, $useQc);

    $box_list = $useQc ? $this->qc_model->get_box_list($code) : NULL;

    $ds['order'] = $order;
    $ds['details'] = $details;
    $ds['box_list'] = $box_list;
    $ds['useQc'] = $useQc;
    $this->load->view('inventory/delivery_order/bill_detail', $ds);
  }



	public function update_shipped_date()
	{
		$sc = TRUE;
		$code = $this->input->post('order_code');
		$date = db_date($this->input->post('shipped_date'), FALSE);

		$arr = array(
			'shipped_date' => $date,
			'update_user' => get_cookie('uname')
		);

		if( ! $this->orders_model->update($code, $arr))
		{
			$sc = FALSE;
			$this->error = "Update Shipped data failed";
		}

		echo $sc === TRUE ? 'success' : $this->error;
	}


  public function get_state()
  {
    $code = $this->input->get('order_code');
    $state = $this->orders_model->get_state($code);
    echo $state;
  }


  private function export_order($code, $option = 'A')
  {
    $sc = TRUE;
    $this->load->library('export');
    if(! $this->export->export_order($code, $option))
    {
      $sc = FALSE;
      $this->error = trim($this->export->error);
    }

    return $sc;
  }


  private function export_transfer_order($code, $option = 'A')
  {
    $sc = TRUE;
    $this->load->library('export');
    if(! $this->export->export_transfer_order($code))
    {
      $sc = FALSE;
      $this->error = trim($this->export->error);
    }

    return $sc;
  }


  private function export_transfer_draft($code, $option = 'A')
  {
    $sc = TRUE;
    $this->load->library('export');
    if(! $this->export->export_transfer_draft($code))
    {
      $sc = FALSE;
      $this->error = trim($this->export->error);
    }

    return $sc;
  }


  private function export_transform($code, $option = 'A')
  {
    $sc = TRUE;
    $this->load->library('export');
    if(! $this->export->export_transform($code))
    {
      $sc = FALSE;
      $this->error = trim($this->export->error);
    }

    return $sc;
  }


  //--- manual export by client
  public function do_export($code, $option = 'A')
  {
    $order = $this->orders_model->get($code);
    $sc = TRUE;
    if(!empty($order))
    {
      switch($order->role)
      {
        case 'C' : //--- Consign (SO)
          $sc = $this->export_order($code, $option);
          break;

        case 'L' : //--- Lend
          $sc = $this->export_transfer_order($code, $option);
          break;

        case 'N' : //--- Consign (TR)
          $sc = $this->export_transfer_draft($code, $option);
          break;

        case 'P' : //--- Sponsor
          $sc = $this->export_order($code, $option);
          break;

        case 'Q' : //--- Transform for stock
          $sc = $this->export_transform($code, $option);
          break;

        case 'S' : //--- Sale order
          $sc = $this->export_order($code, $option);
          break;

        case 'T' : //--- Transform for sell
          $sc = $this->export_transform($code, $option);
          break;

        case 'U' : //--- Support
          $sc = $this->export_order($code, $option);
          break;

        default : ///--- sale order
          $sc = $this->export_order($code, $option);
          break;
      }
    }
    else
    {
      $sc = FALSE;
      $this->error = "ไม่พบเลขที่เอกสาร {$code}";
    }

    return $sc;
  }



  public function manual_export($code)
  {
    $rs = $this->do_export($code);
    echo $rs === TRUE ? 'success' : $this->error;
  }


  public function manual_export_update($code)
  {
    $rs = $this->do_export($code, 'U');
    echo $rs === TRUE ? 'success' : $this->error;
  }



  public function clear_filter()
  {
    $filter = array(
      'ic_code',
      'ic_reference',
      'ic_customer',
      'ic_user',
      'ic_role',
      'ic_channels',
      'ic_from_date',
      'ic_to_date',
      'ic_sort_by',
      'ic_order_by',
      'ic_warehouse'
    );

    clear_filter($filter);
  }

} //--- end class
?>
