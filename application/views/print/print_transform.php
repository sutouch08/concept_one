<?php
$this->load->helper('print');

$doc = doc_type($order->role); //--- print_helper

$page  = '';
$page .= $this->printer->doc_header();

$this->printer->add_title("ใบเบิกสินค้าเพื่อแปรสภาพ");

$header	= array(
  "ลูกค้า" => empty($so) ? $order->customer_name : $so->customer_ref." ({$so->phone})",
  "วันที่" => thai_date($order->date_add, FALSE, '/'),
  "ผู้เบิก" => display_name($order->user),
  "เลขที่" => $order->code,
  "ชื่องาน" => empty($so) ? "" : $so->job_title,
  "ใบสั่งขาย" => $order->so_code,
);

$this->printer->add_header($header);

$total_row 	= empty($details) ? 0 :count($details);

$subtotal_row = 3;

$config 		= array(
  "total_row" => $total_row,
  "font_size" => 10,
  "header_rows" => 3,
  "sub_total_row" => $subtotal_row
);

$this->printer->config($config);

$row 		     = $this->printer->row;
$total_page  = $this->printer->total_page;
$total_qty 	 = 0; //--  จำนวนรวม
$total_amount 		= 0;  //--- มูลค่ารวม(หลังหักส่วนลด)
$total_discount 	= 0; //--- ส่วนลดรวม
$total_order  = 0;    //--- มูลค่าราคารวม

$bill_discount		= $order->bDiscAmount;


//**************  กำหนดหัวตาราง  ******************************//
$thead	= array(
          array("ลำดับ", "width:5%; text-align:center; border-top:0px; border-top-left-radius:10px;"),
          array("รหัส", "width:20%; text-align:center;border-left: solid 1px #ccc; border-top:0px;"),
          array("สินค้า", "width:40%; text-align:center;border-left: solid 1px #ccc; border-top:0px;"),
          array("ราคา", "width:10%; text-align:center; border-left: solid 1px #ccc; border-top:0px;"),
          array("จำนวน", "width:10%; text-align:center; border-left: solid 1px #ccc; border-top:0px;"),
          array("มูลค่า", "width:15%; text-align:center; border-left: solid 1px #ccc; border-top:0px; border-top-right-radius:10px")
          );

$this->printer->add_subheader($thead);


//***************************** กำหนด css ของ td *****************************//
$pattern = array(
            "text-align: center; border-top:0px;",
            "border-left: solid 1px #ccc; border-top:0px; padding:5px; ",
            "border-left: solid 1px #ccc; border-top:0px;",
            "text-align:center; border-left: solid 1px #ccc; border-top:0px;",
            "text-align:center; border-left: solid 1px #ccc; border-top:0px;",
            "text-align:right; border-left: solid 1px #ccc; border-top:0px;"
            );

$this->printer->set_pattern($pattern);


//*******************************  กำหนดช่องเซ็นของ footer *******************************//
$footer	= array(
          array("ผู้เบิก", "","วันที่............................."),
          array("ผู้ตรวจสอบ", "","วันที่............................."),
          array("ผู้อนุมัติ", "","วันที่.............................")
          );

$this->printer->set_footer($footer);


$n = 1;
$index = 0;
while($total_page > 0 )
{
  $page .= $this->printer->page_start();
  $page .= $this->printer->top_page();
  $page .= $this->printer->content_start();
  $page .= $this->printer->table_start();
  $i = 0;

  while($i<$row)
  {
    $rs = isset($details[$index]) ? $details[$index] : FALSE;

    if( ! empty($rs) )
    {
      //--- จำนวนสินค้า ถ้ามีการบันทึกขาย จะได้ข้อมูลจาก tbl_order_sold ซึ่งเป็น qty
      //--- แต่ถ้าไม่มีการบันทึกขายจะได้ข้อมูลจาก tbl_order_detail Join tbl_qc
      //--- ซึ่งได้จำนวน มา 3 ฟิลด์ คือ oreder_qty, prepared, qc
      //--- ต้องเอา order_qty กับ qc มาเปรียบเทียบกัน ถ้าเท่ากัน อันไหนก็ได้ ถ้าไม่เท่ากัน เอาอันที่น้อยกว่า
      $qty = $rs->qty;

      //--- ราคาสินค้า
      $price = $rs->price;

      //--- ส่วนลดสินค้า (ไว้แสดงไม่มีผลในการคำนวณ)
      $discount = discountLabel($rs->discount1, $rs->discount2, $rs->discount3);

      //--- ส่วนลดสินค้า (มีผลในการคำนวณ)
      //--- ทั้งสองตารางใช้ชือฟิลด์ เดียวกัน
      $discount_amount = $rs->discount_amount;

      //--- มูลค่าสินค้า หลังหักส่วนลดตามรายการสินค้า
      $amount = $rs->total_amount;

      //--- เตรียมข้อมูลไว้เพิ่มลงตาราง
      $data = array(
        $n,
        $rs->product_code,
        inputRow($rs->product_name),
        number($price, 2),
        number($qty),
        number($amount, 2)
      );

      $total_qty      += $qty;
      $total_amount   += $amount;
      $total_order    += ($qty * $price);
    }
    else
    {
      $data = array("", "", "", "","","");
    }

    $page .= $this->printer->print_row($data);

    $n++;
    $i++;
    $index++;
  }

  $page .= $this->printer->table_end();

  if($this->printer->current_page == $this->printer->total_page)
  {
    $qty  = number($total_qty);
    $total_order = number($total_order, 2);
    $total_discount_amount = number(($total_discount + $bill_discount),2);
    $net_amount = number( ($total_amount + $order->shipping_fee + $order->service_fee) - $bill_discount, 2);
    $service_fee = number($order->service_fee, 2);
    $shipping_fee = number($order->shipping_fee, 2);
    $remark = $order->remark;
  }
  else
  {
    $qty = "";
    $amount = "";
    $shipping_fee = "";
    $service_fee = "";
    $total_discount_amount = "";
    $net_amount = "";
    $remark = "";
  }


  //--- จำนวนรวม   ตัว
  $sub_qty  = '<td class="width-60 subtotal-first text-center" style="height:'.$this->printer->row_height.'mm;">';
  // $sub_qty .=  '**** ส่วนลดท้ายบิล '.$bill_discount.' ****';
  $sub_qty .= '</td>';
  $sub_qty .= '<td class="width-20 subtotal">';
  $sub_qty .=  '<strong>จำนวนรวม</strong>';
  $sub_qty .= '</td>';
  $sub_qty .= '<td class="width-20 subtotal text-right">';
  $sub_qty .=    $qty;
  $sub_qty .= '</td>';

  //--- ราคารวม
  $sub_price  = '<td rowspan="'.($subtotal_row).'" class="subtotal-first font-size-10" style="height:'.$this->printer->row_height.'mm; white-space:normal;">';
  $sub_price .=  '<strong>หมายเหตุ : </strong> '.$order->remark;
  $sub_price .= '</td>';
  $sub_price .= '<td class="subtotal">';
  $sub_price .=  '<strong>ราคารวม</strong>';
  $sub_price .= '</td>';
  $sub_price .= '<td class="subtotal text-right">';
  $sub_price .=  $total_order;
  $sub_price .= '</td>';

  // //--- ส่วนลดรวม
  // $sub_disc  = '<td class="subtotal" style="height:'.$this->printer->row_height.'mm;">';
  // $sub_disc .=  '<strong>ส่วนลดรวม</strong>';
  // $sub_disc .= '</td>';
  // $sub_disc .= '<td class="subtotal text-right">';
  // $sub_disc .=  $total_discount_amount;
  // $sub_disc .= '</td>';

  //--- ยอดสุทธิ
  $sub_net  = '<td class="subtotal" style="height:'.$this->printer->row_height.'mm;">';
  $sub_net .=  '<strong>ยอดเงินสุทธิ</strong>';
  $sub_net .= '</td>';
  $sub_net .= '<td class="subtotal text-right">';
  $sub_net .=  $net_amount;
  $sub_net .= '</td>';

  $subTotal = array(
    array($sub_qty),
    array($sub_price),
    // array($sub_disc),
    array($sub_net)
  );

  $page .= $this->printer->print_sub_total($subTotal);
  $page .= $this->printer->content_end();
  $page .= $this->printer->footer;
  $page .= $this->printer->page_end();

  $total_page --;
  $this->printer->current_page++;
}

$page .= $this->printer->doc_footer();

echo $page;
 ?>
