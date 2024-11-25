<?php

$total_row 	= empty($details) ? 0 :count($details);
$row_span = 3;

$config 		= array(
	"row" => 12,
	"total_row" => $total_row,
	"font_size" => 12,
	"text_color" => "text-green" //--- hilight text color class
);

$this->xprinter->config($config);

$page  = '';
$page .= $this->xprinter->doc_header();

$this->xprinter->add_title($title);


$header		= array();

//---- Header block Company details On Left side
$header['left'] = array();

$header['left']['A'] = array(
	'company_name' => "<span style='font-size:".($this->xprinter->font_size + 1)."px; font-weight:bolder; white-space:normal;'>".getConfig('COMPANY_FULL_NAME')."</span>",
	'address1' => getConfig('COMPANY_ADDRESS1').' '.getConfig('COMPANY_ADDRESS2').' '.getConfig('COMPANY_POST_CODE'),
	'phone' => 'โทร: '. getConfig('COMPANY_PHONE')
);

//$header['left']['A']['taxid'] = "เลขประจำตัวผู้เสียภาษี  ".getConfig('COMPANY_TAX_ID');

$header['left']['B'] = array(
	"client" => "<span style='font-size:".($this->xprinter->font_size + 1)."px; font-weight:bolder; white-space:normal; color:green;'>ลูกค้า</span>",
	"customer" => "<span style='font-size:".($this->xprinter->font_size + 1)."px; font-weight:bolder; white-space:normal;'>".((empty($order->customer_ref) ? $order->customer_name: $order->customer_ref))."</span>",
	"address1" => "{$order->customer_address}",
	"phone" => "โทร. {$order->phone}"
	// "taxid" => "เลขประจำตัวผู้เสียภาษี {$order->tax_id} "
);


//--- Header block  Document details On the right side
$header['right'] = array();

$header['right']['A'] = array(
	array('label' => 'เลขที่', 'value' => $order->code),
	array('label' => 'วันที่', 'value' => thai_date($order->date_add, FALSE, '/')),
	array('label' => 'วันนัดส่ง', 'value' => thai_date($order->due_date, FALSE, '/')),
	array('label' => 'พนักงานขาย', 'value' => empty($sale) ? NULL : $sale->name ."&nbsp;&nbsp;".$sale->phone),
	array('label' => 'ใบเบิก', 'value' => $wq)
);
//
// $header['right']['B'] = array(
// 	array('label' => 'อ้างอิง', 'value' => '')
// );

$this->xprinter->add_header($header);


//--- ถ้าเป็นฝากขาย(2) หรือ เบิกแปรสภาพ(5) หรือ ยืมสินค้า(6)
//--- รายการพวกนี้ไม่มีการบันทึกขาย ใช้การโอนสินค้าเข้าคลังแต่ละประเภท
//--- ฝากขาย โอนเข้าคลังฝากขาย เบิกแปรสภาพ เข้าคลังแปรสภาพ  ยืม เข้าคลังยืม
//--- รายการที่จะพิมพ์ต้องเอามาจากการสั่งสินค้า เปรียบเทียบ กับยอดตรวจ ที่เท่ากัน หรือ ตัวที่น้อยกว่า

$subtotal_row = 4;


$row = $this->xprinter->row;
$total_page  = $this->xprinter->total_page;
$total_qty 	 = 0; //--  จำนวนรวม


//**************  กำหนดหัวตาราง  ******************************//
$thead	= array(
          array("#", "width:5%; text-align:center;"),
          array("รหัสสินค้า", "width:20%; text-align:center;"),
          array("รายละเอียด", "width:35%; text-align:center;"),
          array("จำนวน", "width:10%; text-align:right;"),
          array("ราคา/หน่วย", "width:10%; text-align:right;"),
          array("ส่วนลด(%)", "width:10%; text-align:center;"),
					array("จำนวนเงิน", "width:10%; text-align:right;")
          );

$this->xprinter->add_subheader($thead);


//***************************** กำหนด css ของ td *****************************//
$pattern = array(
            "text-align:center;",
            "text-align:left;",
            "text-aligh:left",
            "text-align:right;",
            "text-align:right;",
            "text-align:center;",
            "text-align:right;"
            );

$this->xprinter->set_pattern($pattern);


//*******************************  กำหนดช่องเซ็นของ footer *******************************//

$footer	= array(
	array("ผู้รับของ", "ได้รับสินค้าถูกต้องตามรายการแล้ว","วันที่"),
	array("ผู้ส่งของ", "","วันที่"),
	array("ผู้อนุมัติ", "","วันที่")
);

$end_content = '<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 font-size-11 text-center">';
$end_content .= $order->edition > 0 ? "แก้ไขครั้งที่ &nbsp;&nbsp;".$order->edition." &nbsp;&nbsp; โดย &nbsp;&nbsp; ".display_name($order->edit_by)." &nbsp;&nbsp; เวลา &nbsp;&nbsp; ".thai_date($order->edit_time, TRUE, '/') : "";
$end_content .= '</div>';

$this->xprinter->set_footer($footer, $end_content);


$n = 1;
$index = 0;
while($total_page > 0 )
{
  $page .= $this->xprinter->page_start();
  $page .= $this->xprinter->top_page();
  $page .= $this->xprinter->content_start();
  $page .= $this->xprinter->table_start();
	if($order->status == 2)
	{
		$page .= '
		<div style="width:0px; height:0px; position:relative; left:30%; line-height:0px; top:300px;color:red; text-align:center; z-index:100000; opacity:0.1; transform:rotate(-45deg)">
				<span style="font-size:150px; border-color:red; border:solid 10px; border-radius:20px; padding:0 20 0 20;">ยกเลิก</span>
		</div>';
	}

  $i = 0;

  while($i<$row)
  {
    $rs = isset($details[$index]) ? $details[$index] : FALSE;

    if( ! empty($rs) )
    {
			$data = array(
				$n,
				$rs->product_code,
				inputRow($rs->product_name),
				number($rs->qty, 2), //.' '.$rs->unit_code,
				number($rs->price, 2),
				$rs->discount_label,
				number($rs->total_amount, 2)
			);

      $total_qty += $rs->qty;
    }
    else
    {
      $data = array("", "", "", "","", "","");
    }

    $page .= $this->xprinter->print_row($data);

    $n++;
    $i++;
    $index++;
  }

  $page .= $this->xprinter->table_end();

  if($this->xprinter->current_page == $this->xprinter->total_page)
  {
    $qty  = "<b>*** จำนวนรวม  ".number($total_qty)."  หน่วย ***</b>";
		$totalBfDisc = number($order->TotalBfDisc, 2);
		$discAmount = number($order->DiscAmount, 2);
		$vatSum = number($order->VatSum, 2);
		$docTotal = number($order->DocTotal, 2);
    $remark = $order->remark;
		$baht_text = "(".baht_text($order->DocTotal).")";
  }
  else
  {
		$qty  = "";
		$totalBfDisc = "";
		$discAmount = "";
		$vatSum = "";
		$docTotal = "";
    $remark = "";
		$baht_text = "&nbsp;";
  }

  $subTotal = array();

	if($this->xprinter->current_page == $this->xprinter->total_page)
  {
		//--- จำนวนรวม   ตัว
	  $sub_qty  = '<td class="width-60 text-center" style="border:0;">';
		$sub_qty .= $qty;
	  $sub_qty .= '</td>';
	  $sub_qty .= '<td class="width-20" style="border:0;">';
	  $sub_qty .= '</td>';
		$sub_qty .= '<td class="width-20 text-right" style="border:0;"></td>';

	  array_push($subTotal, array($sub_qty));
	}

  $sub_price  = '<td rowspan="'.$row_span.'" class="width-60 subtotal-first-row middle text-center">'.$baht_text.'</td>';
  $sub_price .= '<td class="width-20 subtotal subtotal-first-row">';
  $sub_price .=  '<strong>มูลค่ารวมก่อนส่วนลด</strong>';
  $sub_price .= '</td>';
  $sub_price .= '<td class="width-20 subtotal subtotal-first-row text-right">';
  $sub_price .=  $totalBfDisc;
  $sub_price .= '</td>';
  array_push($subTotal, array($sub_price));

	//--- ส่วนลดรวม
	$sub_disc  = '<td class="subtotal">';
	$sub_disc .=  '<strong>ส่วนลด</strong>';
	$sub_disc .= '</td>';
	$sub_disc .= '<td class="subtotal text-right">';
	$sub_disc .=  $discAmount;
	$sub_disc .= '</td>';
	array_push($subTotal, array($sub_disc));

  //--- Vat
	// if($order->vat_type != 'N')
	// {
	// 	$sub_disc  = '<td class="subtotal">';
	// 	$sub_disc .=  '<strong>ภาษีมูลค่าเพิ่ม &nbsp;'.$order->vat_rate.' %</strong>';
	// 	$sub_disc .= '</td>';
	// 	$sub_disc .= '<td class="subtotal text-right">';
	// 	$sub_disc .=  $vatSum;
	// 	$sub_disc .= '</td>';
	// 	array_push($subTotal, array($sub_disc));
	// }

  $first_row = ""; //$use_vat ? "" : "subtotal-first-row";
	//--- ยอดสุทธิ
	$sub_net  = "";
  $sub_net .= '<td class="subtotal subtotal-last-row '.$first_row.'">';
  $sub_net .=  '<strong>จำนวนเงินรวมทั้งสิ้น</strong>';
  $sub_net .= '</td>';
  $sub_net .= '<td class="subtotal subtotal-last-row '.$first_row.' text-right">';
  $sub_net .=  $docTotal;
  $sub_net .= '</td>';

  array_push($subTotal, array($sub_net));

	// //--- หมายเหตุ
	$sub_remark  = '<td colspan="3" class="no-border" style="white-space:normal;"><span class="green"><b>หมายเหตุ : </b></span>'.$remark.'</td>';
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
