<?php

$total_row 	= empty($details) ? 0 :count($details);
$row_span = 4;

$config 		= array(
	"row" => 11, //8,
	"total_row" => $total_row,
	"font_size" => 11,
	"text_color" => "text-green",
	"page_height" => 278,
	"content_border" => 1
);

$this->iprinter->config($config);

$companyName = getConfig('COMPANY_FULL_NAME');

$page  = '';
$page .= $this->iprinter->doc_header($order->code);

$this->iprinter->add_title($title);


//--- ถ้าเป็นฝากขาย(2) หรือ เบิกแปรสภาพ(5) หรือ ยืมสินค้า(6)
//--- รายการพวกนี้ไม่มีการบันทึกขาย ใช้การโอนสินค้าเข้าคลังแต่ละประเภท
//--- ฝากขาย โอนเข้าคลังฝากขาย เบิกแปรสภาพ เข้าคลังแปรสภาพ  ยืม เข้าคลังยืม
//--- รายการที่จะพิมพ์ต้องเอามาจากการสั่งสินค้า เปรียบเทียบ กับยอดตรวจ ที่เท่ากัน หรือ ตัวที่น้อยกว่า

$subtotal_row = 4;


$row 		     = $this->iprinter->row;
$total_page  = $this->iprinter->total_page;
$total_qty 	 = 0; //--  จำนวนรวม
$total_amount = 0;


//**************  กำหนดหัวตาราง  ******************************//
$thead	= array(
	array("ลำดับ<span style='display:block;font-size:8px;'>No.</span>", "width:10mm; text-align:center; border:solid 1px #333; border-left:0px; border-top:0; border-top-left-radius:10px;"),
	array("รหัสสินค้า/รายละเอียด<span style='display:block;font-size:8px;'>Code/Descriptions.</span>", "width:100mm; text-align:center; border:solid 1px #333; border-top:0; border-left:0px;"),
	array("จำนวน<span style='display:block;font-size:8px;'>Quantity</span>", "width:25mm; text-align:center; border:solid 1px #333; border-top:0; border-left:0px;"),
	array("หน่วยละ<span style='display:block;font-size:8px;'>Unit Price</span>", "width:25mm; text-align:center; border:solid 1px #333; border-top:0; border-left:0px;"),
	array("จำนวนเงิน<span style='display:block;font-size:8px;'>Amount</span>", "width:30mm; text-align:center; border:solid 1px #333; border-top:0; border-left:0; border-right:0px; border-top-right-radius:10px;")
);

$this->iprinter->add_subheader($thead);


//***************************** กำหนด css ของ td *****************************//
$pattern = array(
            "text-align:center; border-right:solid 1px #333;",
            "text-align:left; border-right:solid 1px #333;",
            "text-align:right; border-right:solid 1px #333;",
            "text-align:right; border-right:solid 1px #333;",
            "text-align:right;"
            );

$this->iprinter->set_pattern($pattern);


//*******************************  กำหนดช่องเซ็นของ footer *******************************//
$footer  = '<div style="width:190mm; height:51.5mm; margin:auto;">';
$footer .=  '<table class="table" style="margin-bottom:0px; font-size:12px; ">';
$footer .=    '<tr>';
$footer .=      '<td class="text-center" style="width:60mm; border:0; border-top-left-radius:10px; border-bottom-left-radius:10px;">';
$footer .= 				'<strong>ผู้รับสินค้า</strong>';
$footer .= 				'<span style="display:block;">&nbsp;</span>';
$footer .= 				'<span style="display:block;">&nbsp;</span>';
$footer .= 				'<p class="text-center" style="width:55mm; display:inline-block; border-bottom:solid 1px #333;">&nbsp;</p>';
$footer .= 				'<span style="display:block;">&nbsp;</span>';
$footer .= 				'<p style="display:inline-block; width:10mm; text-align:right;">วันที่</p>
										<p style="display:inline-block; width:10mm; border-bottom:solid 1px #333; text-align:left;">&nbsp;</p>
										<p style="display:inline-block; width:10mm; border-bottom:solid 1px #333; text-align:left;">/</p>
										<p style="display:inline-block; width:10mm; border-bottom:solid 1px #333; text-align:left;">/</p>';
$footer .=      '</td>';

$footer .=      '<td class="text-left" style="width:70mm; border:0; ">&nbsp;</td>';

$footer .=      '<td class="text-center" style="width:60mm; border:0; border-top-right-radius:10px; border-bottom-right-radius:10px;">';
$footer .= 				'<strong>ผู้รับมอบอำนาจ</strong>';
$footer .= 				'<span style="display:block;">&nbsp;</span>';
$footer .= 				'<span style="display:block;">&nbsp;</span>';
$footer .= 				'<p class="text-center" style="width:55mm; display:inline-block; border-bottom:solid 1px #333;">&nbsp;</p>';
$footer .= 				'<span style="display:block;">&nbsp;</span>';
$footer .= 				'<p style="display:inline-block; width:10mm; text-align:right;">วันที่</p>
										<p style="display:inline-block; width:10mm; border-bottom:solid 1px #333; text-align:left;">&nbsp;</p>
										<p style="display:inline-block; width:10mm; border-bottom:solid 1px #333; text-align:left;">/</p>
										<p style="display:inline-block; width:10mm; border-bottom:solid 1px #333; text-align:left;">/</p>';
$footer .=      '</td>';
$footer .=    '</tr>';
$footer .=  '</table>';
$footer .= '</div>';


$n = 1;
$index = 0;
while($total_page > 0 )
{
	$top = '';
	$top .= '<div style="width:190mm; margin:auto;" class="hide">';
	$top .= 	'<div class="font-size-18 bold">'.$companyName.'</div>';
	$top .= 	'<div class="font-size-12">'.getConfig('COMPANY_ADDRESS1').' '.getConfig('COMPANY_ADDRESS2').' '.getConfig('COMPANY_POST_CODE').'</div>';
	$top .= 	'<div class="font-size-12">Tel : '.getConfig('COMPANY_PHONE').'&nbsp;&nbsp;&nbsp FAX : '.getConfig('COMPANY_FAX_NUMBER').'</div>';
	$top .= 	'<div class="font-size-12 bold">เลขประจำตัวผู้เสียภาษี/TaxID : '.getConfig('COMPANY_TAX_ID').'</div>';
	$top .=   '<div class="text-right" style="position:absolute; top:20px; right:20px;">Page '.$this->iprinter->current_page.'/'.$this->iprinter->total_page.'</div>';
	$top .= '</div>';

	$top .= '<div style="width:190mm; margin:auto;">';
	$top .= 	'<div class="row" style="margin-left:0px; margin-right:0px;">';
	$top .= 		'<div class="col-lg-12 col-md-12 col-sm-12 text-right" style="padding:12px 0px 12px 0px;">';
	$top .= 			'<span style="font-size:18px; padding:10px; border:solid 1px; #666; border-radius:10px;">'.$title.'</span>';
	$top .= 		'</div>';
	$top .=  	'</div>';
	$top .= '</div>';

	$top .= '<div style="width:190mm; margin:auto; margin-bottom:2mm;">';
	$top .= 	'<div class="row" style="margin-left:0px; margin-right:0px;">';
		$top .= 	'<div style="float:left; width:105mm; height:50mm; font-size:14px; padding:10px 15px 10px 15px;  border:solid 1px; #666; border-radius:10px;">';
		$top .= 		'<div class="row">';
		$top .=     	'<div class="col-sm-2 padding-5"><span class="bold">ลูกค้า</span><span style="display:block;font-size:7px;">customer</span></div>';
		$top .=     	'<div class="col-sm-10 padding-5">'.$order->CardCode.'</div>';
		$top .= 		'</div>';
		$top .= 		'<div class="row">';
		$top .=     	'<div class="col-sm-12 padding-5">'.$order->CardName.'</div>';
		$top .= 		'</div>';
		$top .= 		'<div class="row">';
		$top .=     	'<div class="col-sm-12 padding-5">'.$order->address.'</div>';
		$top .= 		'</div>';
		$top .= 		'<div class="row">';
		$top .=     	'<div class="col-sm-12 padding-5">'.parseSubDistrict($order->sub_district, $order->province).'&nbsp;&nbsp;'.parseDistrict($order->district, $order->province).'&nbsp;&nbsp;'.parseProvince($order->province, ).'&nbsp;&nbsp;'.$order->postcode.'</div>';
		$top .= 		'</div>';
		$top .= 		'<div class="row">';
		$top .=     '<div class="col-sm-12 padding-5">โทร.&nbsp;&nbsp;'.$order->phone.'</div>';
		$top .= 		'</div>';
		$top .= 		'<div class="row">';
		$top .=     	'<div class="col-sm-5 padding-5"><span>เลขประจำตัวผู้เสียภาษี</span><span style="display:block;font-size:7px;">Tax ID</span></div>';
		$top .=     	'<div class="col-sm-4 padding-5">'.$order->tax_id.'</div>';
		$top .=     	'<div class="col-sm-3 padding-5">'.$order->branch_name.'</div>';
		$top .= 		'</div>';
		$top .= 		'<div class="row">';
		$top .=     	'<div class="col-sm-2 padding-5"><span>อ้างอิง</span><span style="display:block;font-size:7px;">reference</span></div>';
		$top .=     	'<div class="col-sm-10 padding-5"></div>';
		$top .= 		'</div>';
		$top .= 	'</div>';

		$billLabel = $order->BaseType == 'POS' ? 'เลขที่บิล' : ($order->BaseType == 'WO' ? 'เลขที่ออเดอร์' : 'เลขที่ใบสั่งขาย');
		$billCode = $order->BaseRef;

		$top .= 	'<div style="float:left; width:84mm; height:50mm; font-size:14px; padding:10px 15px 10px 15px;  border:solid 1px; #666; border-radius:10px; margin-left:1mm">';
		$top .= 		'<div class="row">';
		$top .=     	'<div class="col-sm-4 padding-5"><span>เลขที่</span><span style="display:block;font-size:7px;">No.</span></div>';
		$top .=     	'<div class="col-sm-8 padding-5">'.$order->code.'</div>';
		$top .= 		'</div>';
		$top .= 		'<div class="row">';
		$top .=     	'<div class="col-sm-4 padding-5"><span>วันที่</span><span style="display:block;font-size:7px;">Date</span></div>';
		$top .=     	'<div class="col-sm-8 padding-5">'.thai_date($order->DocDate, FALSE, '/').'</div>';
		$top .= 		'</div>';
		$top .= 		'<div class="row">';
		$top .=     	'<div class="col-sm-4 padding-5"><span>'.$billLabel.'</span><span style="display:block;font-size:7px;">Sales Order No.</span></div>';
		$top .=     	'<div class="col-sm-8 padding-5">'.$billCode.'</div>';
		$top .= 		'</div>';
		$top .= 		'<div class="row">';
		$top .=     	'<div class="col-sm-4 padding-5"><span>พนักงานขาย</span><span style="display:block;font-size:7px;">Salesman</span></div>';
		$top .=     	'<div class="col-sm-8 padding-5">'.get_sale_name($order->SlpCode).'</div>';
		$top .= 		'</div>';
		$top .= 	'</div>';
	$top .=   '</div>';
	$top .= '</div>';


  $page .= $this->iprinter->page_start();
  $page .= $top;
	$page .= '<div style="width:190mm; margin:auto; margin-bottom:2mm; border-radius:10px; outline:solid 1px #333;">';

  $page .= $this->iprinter->table_start();
	if($order->status == 'D')
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
			$price = $rs->VatType == 'E' ? $rs->Price : $rs->PriceAfVAT;
			$lineTotal = $rs->VatType == 'E' ? $rs->LineTotal : add_vat($rs->LineTotal, $rs->VatRate, 'E');

			$data = array(
				$n,
				$rs->Dscription,
				number($rs->Qty, 2).' '.$rs->unitMsr,
				number($price, 2),
				number($lineTotal, 2)
			);

      $total_qty += $rs->Qty;
			$total_amount += $lineTotal;
    }
    else
    {
      $data = array("", "","", "","");
    }

    $page .= $this->iprinter->print_row($data);

    $n++;
    $i++;
    $index++;
  }

  $page .= $this->iprinter->table_end();

  if($this->iprinter->current_page == $this->iprinter->total_page)
  {
    $qty  = "<b>*** จำนวนรวม  ".number($total_qty)."  หน่วย ***</b>";
		$totalBfDisc = number($total_amount, 2);
		$billDiscAmount = number($order->DiscSum, 2);
		$total_vat_amount = number($order->VatSum, 2);
		$totalBfTax = $order->vat_type == 'E' ? ($order->DocTotal - $order->VatSum) - $order->DiscSum : $order->DocTotal - $order->DiscSum;
		$totalBfTax = number($totalBfTax, 2);
		$net_amount = number($order->DocTotal, 2);
    $remark = "";
		$baht_text = "(".baht_text($order->DocTotal).")";
  }
  else
  {
		$qty  = "";
		$totalBfDisc = "";
		$totalBfTax = "";
		$billDiscAmount = "";
		$total_vat_amount = "";
		$net_amount = "";
    $remark = "";
		$baht_text = "&nbsp;";
  }

  $subTotal = array();

	$sub_price  = '<td rowspan="'.$row_span.'" class="text-left" style="position:relative; border:solid 1px #333; border-left:0; border-right:0px; font-size:8px;" style="width:110mm; padding:3px 8px 3px 8px">';
	$sub_price .= '<b>หมายเหตุ</b> : '.$order->Comments;
	$sub_price .= '</td>';
  $sub_price .= '<td style="width:50mm; border-top:solid 1px #333; padding:3px 8px 3px 8px">';
  $sub_price .=  '<strong>รวมเป็นเงิน</strong>';
	$sub_price .=  '<span style="display:block; font-size:8px; margin-top:-4px;">Gross Amount</span>';
  $sub_price .= '</td>';
  $sub_price .= '<td class="middle text-right" style="width:29.7mm; border:solid 1px #333; border-right:0;  border-bottom:0; padding:3px 8px 3px 8px">';
  $sub_price .=  $totalBfDisc;
  $sub_price .= '</td>';
  array_push($subTotal, array($sub_price));

	//--- ส่วนลดท้ายบิล
  $sub_disc  = '<td style="border:0px; padding:3px 8px 3px 8px">';
  $sub_disc .=  '<strong>ส่วนลด</strong>';
	$sub_disc .=  '<span style="display:block; font-size:8px; margin-top:-4px;">Discount</span>';
  $sub_disc .= '</td>';
  $sub_disc .= '<td class="middle text-right" style="border:solid 1px #333; border-right:0; border-bottom:0px; border-top:0px; padding:3px 8px 3px 8px">';
  $sub_disc .=  $billDiscAmount;
  $sub_disc .= '</td>';
  array_push($subTotal, array($sub_disc));

	//--- มูลค่าหลังส่วนลด ก่อนภาษี
  $sub_disc  = '<td style="border:0; padding:3px 8px 3px 8px">';
  $sub_disc .=  '<strong>มูลค่าหลังหักส่วนลด</strong>';
	$sub_disc .=  '<span style="display:block; font-size:8px; margin-top:-4px;">Total After Discount</span>';
  $sub_disc .= '</td>';
  $sub_disc .= '<td class="middle text-right" style="border:solid 1px #333; border-right:0; border-bottom:0; border-top:0; padding:3px 8px 3px 8px">';
  $sub_disc .=  $totalBfTax;
  $sub_disc .= '</td>';
  array_push($subTotal, array($sub_disc));

  //--- ส่วนลดรวม
  $sub_vat  = '<td style="border:0; border-bottom:solid 1px #333; padding:3px 8px 3px 8px">';
  $sub_vat .=  '<strong>ภาษีมูลค่าเพิ่ม &nbsp;&nbsp; 0%</strong>';
	$sub_vat .=  '<span style="display:block; font-size:8px; margin-top:-4px;">VAT</span>';
  $sub_vat .= '</td>';
  $sub_vat .= '<td class="middle text-right" style="border:solid 1px #333; border-right:0; border-top:0; padding:3px 8px 3px 8px">';
  $sub_vat .=  '';
  $sub_vat .= '</td>';
  array_push($subTotal, array($sub_vat));

	//--- ยอดสุทธิ
	$sub_net  = "";
	$sub_net .= '<td class="middle text-center" style="border-bottom-left-radius:10px;"><strong>'.$baht_text.'</strong></td>';
  $sub_net .= '<td>';
  $sub_net .=  '<strong>มูลค่าสุทธิ</strong>';
	$sub_net .=  '<span style="display:block; font-size:8px; margin-top:-4px;">Net Total</span>';
  $sub_net .= '</td>';
  $sub_net .= '<td class="middle text-right" style="font-size:14px; border-left:solid 1px #333; border-bottom-right-radius:10px;">';
  $sub_net .=  '<strong>'.$net_amount.'</strong>';
  $sub_net .= '</td>';

  array_push($subTotal, array($sub_net));


	$page .= $this->iprinter->print_sub_total($subTotal);
  $page .= $this->iprinter->content_end();

  $page .= $footer; //$this->iprinter->footer;
  $page .= $this->iprinter->page_end();

  $total_page --;
  $this->iprinter->current_page++;
}

$page .= $this->iprinter->doc_footer();

echo $page;
 ?>
