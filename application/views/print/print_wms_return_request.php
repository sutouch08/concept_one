<?php
$this->load->helper('print');
$total_row 	= empty($details) ? 0 :count($details);
$config 		= array(
	"row" => 17,
	"total_row" => $total_row,
	"font_size" => 10,
	"text_color" => "text-green" //--- hilight text color class
);

$this->xprinter->config($config);

$page  = '';
$page .= $this->xprinter->doc_header();

$this->xprinter->add_title('ใบส่งคืนสินค้า(RC)');


$header		= array();

//---- Header block Company details On Left side
$header['left'] = array();

$header['left']['A'] = array(
	'company_name' => "<span style='font-size:".($this->xprinter->font_size + 5)."px; font-weight:bolder;'>บริษัท วอริกซ์ สปอร์ต จำกัด (มหาชน)</span>",
	'address1' => "849/6-8 ถนนพระราม 6 แขวงวังใหม่",
	'address2' => "เขตปทุมวัน กรุงเทพมหานคร 10330",
	'phone' => "โทร. 0-2117-1300 แฟ็กซ์. 0-2117-1308",
	'taxid' => "เลขประจำตัวผู้เสียภาษีอากร 0107565000255"
);


$header['left']['B'] = array(
	"client" => "<span style='font-size:".($this->xprinter->font_size + 1)."px; font-weight:bolder; color:orange;'>ลูกค้า/ผู้รับ</span>",
	"customer" => "<span style='font-size:".($this->xprinter->font_size + 1)."px; font-weight:bolder;'>บริษัท เอบีพีโอ จำกัด</span>",
	"address1" => "3/394 ถนนวัชรพล แขวงท่าแร้ง เขตบางเขน กรุงเทพมหานคร 10220",
	"phone" => "โทร. -",
	"taxid" => "เลขประจำตัวผู้เสียภาษีอากร 0105564000233"
);



//--- Header block  Document details On the right side
$header['right'] = array();

$header['right']['A'] = array(
	array('label' => 'เลขที่', 'value' => "RC{$order->code}"),
	array('label' => 'วันที่', 'value' => thai_date($order->cancle_date, FALSE, '/')),
    array('label' => 'อ้างอิง', 'value' => $order->code)
);

$header['right']['B'] = array(
	array('label' => 'คลังปลายทาง', 'value' => $order->warehouse_name),
	array('label' => 'ลูกค้า', 'value' => $order->customer_name),
	array('label' => 'ผู้จัดทำ', 'value' => $this->user_model->get_name($order->user))
);

$this->xprinter->add_header($header);

$subtotal_row = 4;


$row 		     = $this->xprinter->row;
$total_page  = $this->xprinter->total_page;
$total_qty 	 = 0; //--  จำนวนรวม



//**************  กำหนดหัวตาราง  ******************************//
$thead	= array(
          array("ลำดับ", "width:5%; text-align:center; border-bottom:solid 2px #333; border-top:solid 2px #333;"),
          array("รหัส", "width:25%; text-align:left; border-bottom:solid 2px #333; border-top:solid 2px #333;"),
          array("สินค้า", "width:60%; text-align:left; border-bottom:solid 2px #333; border-top:solid 2px #333;"),
          array("จำนวน(หน่วย)", "width:10%; text-align:right; border-bottom:solid 2px #333; border-top:solid 2px #333;")
          );

$this->xprinter->add_subheader($thead);


//***************************** กำหนด css ของ td *****************************//
$pattern = array(
            "text-align:center;",
            "text-align:left;",
            "text-aligh:left",
            "text-align:right; padding-right:10px;"
            );

$this->xprinter->set_pattern($pattern);


//*******************************  กำหนดช่องเซ็นของ footer *******************************//
$footer	= array(
          array("ผู้รับสินค้า", "ได้รับสินค้าถูกต้องตามรายการแล้ว","วันที่"),
		  array("ผู้อนุมัติ", "","วันที่"),
          array("ผู้จัดทำ", "","วันที่", "โทร.")
          );

$this->xprinter->set_footer($footer);


$n = 1;
$index = 0;
while($total_page > 0 )
{
  $page .= $this->xprinter->page_start();
  $page .= $this->xprinter->top_page();
  $page .= $this->xprinter->content_start();
  $page .= $this->xprinter->table_start();
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


      //--- เตรียมข้อมูลไว้เพิ่มลงตาราง
      $data = array(
                    $n,
                    $rs->product_code,
                    inputRow($rs->product_name),
                    number($qty)
                );

      $total_qty      += $qty;
    }
    else
    {
      $data = array("", "", "", "");
    }

    $page .= $this->xprinter->print_row($data);

    $n++;
    $i++;
    $index++;
  }

  $page .= $this->xprinter->table_end();

  if($this->xprinter->current_page == $this->xprinter->total_page)
  {
    $qty  = number($total_qty);
    $remark = $order->remark;
  }
  else
  {
    $qty = "";
		$baht_text = "";
  }

  $subTotal = array();

	//--- จำนวนรวม   ตัว
  $sub_qty  = '<td rowspan="2" class="width-60" style="border-top:solid 2px #333 !important;">';
	$sub_qty .= '<strong>หมายเหตุ : </strong> '.$order->remark;
  $sub_qty .= '</td>';
  $sub_qty .= '<td class="width-20" style="border-top:solid 2px #333; border-bottom:solid 3px #333;">';
  $sub_qty .=  '<strong>จำนวนรวม</strong>';
  $sub_qty .= '</td>';
  $sub_qty .= '<td class="width-20 text-right" style="border-top:solid 2px #333; border-bottom:solid 3px #333;">';
  $sub_qty .=   $qty ." หน่วย";
  $sub_qty .= '</td>';

  array_push($subTotal, array($sub_qty));
	$sub_remark = '<td></td><td></td>';
	array_push($subTotal, array($sub_remark));

	$page .= $this->xprinter->print_sub_total($subTotal);
  $page .= $this->xprinter->content_end();

	$page .= "<div class='divider-hidden'></div>";
  $page .= $this->xprinter->footer;
  $page .= $this->xprinter->page_end();

  $total_page --;
  $this->xprinter->current_page++;
}

$page .= $this->xprinter->doc_footer();

echo $page;
 ?>
