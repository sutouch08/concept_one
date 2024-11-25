<?php
$total_row 	= empty($details) ? 0 :count($details);
$row_span = 3;
$row = 9;

if($order->DiscSum > 0 || $dpmAmount == 0)
{
	$row_span++;
	$row--;
}

if( $dpmAmount > 0)
{
	$row_span++;
	$row--;
}

$config 		= array(
	"row" => $row,
	"total_row" => $total_row,
	"font_size" => 12,
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
	array("ลำดับ<span style='display:block;font-size:8px; margin-top:-3px'>No.</span>", "width:10mm; text-align:center; border:solid 1px #333; border-left:0px; border-top:0; border-top-left-radius:10px;"),
	array("รหัสสินค้า/รายละเอียด<span style='display:block;font-size:8px; margin-top:-3px'>Code/Descriptions.</span>", "width:100mm; text-align:center; border:solid 1px #333; border-top:0; border-left:0px;"),
	array("จำนวน<span style='display:block;font-size:8px; margin-top:-3px'>Quantity</span>", "width:25mm; text-align:center; border:solid 1px #333; border-top:0; border-left:0px;"),
	array("หน่วยละ<span style='display:block;font-size:8px; margin-top:-3px'>Unit Price</span>", "width:25mm; text-align:center; border:solid 1px #333; border-top:0; border-left:0px;"),
	array("จำนวนเงิน<span style='display:block;font-size:8px; margin-top:-3px'>Amount</span>", "width:30mm; text-align:center; border:solid 1px #333; border-top:0; border-left:0; border-right:0px; border-top-right-radius:10px;")
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
$footer  = '<div style="width:190mm; height:51.5mm; margin:auto; outline:solid 1px #333; border-radius:10px;">';
$footer .=  '<table class="table" style="margin-bottom:0px; font-size:12px; ">';
$footer .=    '<tr>';
$footer .=      '<td class="text-center" style="width:60mm; border:0; border-right:solid 1px #333; border-top-left-radius:10px; border-bottom-left-radius:10px;">';
$footer .= 				'<strong>ได้รับสินค้าตามรายการถูกต้องแล้ว</strong>';
$footer .= 				'<span style="display:block;">&nbsp;</span>';
$footer .= 				'<span style="display:block;">&nbsp;</span>';
$footer .= 				'<p style="display:inline-block; width:30mm; border-bottom:solid 1px #333; margin-bottom:5px;">&nbsp;</p>
									<p style="display:inline-block; width:7mm; border-bottom:solid 1px #333; margin-bottom:5px; text-align:left;">&nbsp;</p>
									<p style="display:inline-block; width:7mm; border-bottom:solid 1px #333; margin-bottom:5px; text-align:left;">/</p>
									<p style="display:inline-block; width:7mm; border-bottom:solid 1px #333; margin-bottom:5px; text-align:left;">/</p>';
$footer .= 				'<p class="text-center bold" style="width:35mm; display:inline-block; margin-bottom:5px; font-size:9px;">ผู้รับสินค้า/Goods Received By</p>';
$footer .= 				'<p class="text-center bold" style="width:20mm; display:inline-block; margin-bottom:5px; font-size:9px;">วันที่/Date</p>';
$footer .= 				'<span style="display:block;">&nbsp;</span>';
$footer .= 				'<span style="display:block;">&nbsp;</span>';
$footer .= 				'<p style="display:inline-block; width:30mm; border-bottom:solid 1px #333; margin-bottom:5px;">&nbsp;</p>
									<p style="display:inline-block; width:7mm; border-bottom:solid 1px #333; margin-bottom:5px; text-align:left;">&nbsp;</p>
									<p style="display:inline-block; width:7mm; border-bottom:solid 1px #333; margin-bottom:5px; text-align:left;">/</p>
									<p style="display:inline-block; width:7mm; border-bottom:solid 1px #333; margin-bottom:5px; text-align:left;">/</p>';
$footer .= 				'<p class="text-center bold" style="width:35mm; display:inline-block; margin-bottom:5px; font-size:9px;">ผู้ส่งสินค้า/Delivery By</p>';
$footer .= 				'<p class="text-center bold" style="width:20mm; display:inline-block; margin-bottom:5px; font-size:9px;">วันที่/Date</p>';
$footer .=      '</td>';


$footer .=      '<td class="text-left" style="width:70mm; border:0; border-right:solid 1px #333;">';
$footer .= 				'<p class="text-center">';
$footer .= 					'<span style="width:20mm; display:inline-block; font-weight:bold;">ชำระโดย</span>';
$footer .= 					'<span style="width:20mm; display:inline-block; font-weight:bold;"><label><input type="checkbox" class="ace"><span class="lbl">&nbsp;เงินสด</span></label></span>';
$footer .= 					'<span style="width:20mm; display:inline-block; font-weight:bold;"><label><input type="checkbox" class="ace"><span class="lbl">&nbsp;เช็ค</span></label></span>';
$footer .= 				'</p>';

$footer .= 				'<p class="text-left" style="font-size:9px;">';
$footer .= 					'<span style="width:15mm; display:inline-block; font-weight:bold;">เช็คธนาคาร</span>';
$footer .= 					'<span style="width:20mm; display:inline-block; font-weight:bold; border-bottom:solid 1px #333">&nbsp;</span>';
$footer .= 					'<span style="width:8mm; display:inline-block; font-weight:bold; text-align:right;">สาขา</span>';
$footer .= 					'<span style="width:20mm; display:inline-block; font-weight:bold; border-bottom:solid 1px #333">&nbsp;</span>';
$footer .= 				'</p>';

$footer .= 				'<p class="text-left" style="font-size:9px;">';
$footer .= 					'<span style="width:8mm; display:inline-block; font-weight:bold;">เลขที่</span>';
$footer .= 					'<span style="width:27mm; display:inline-block; font-weight:bold; border-bottom:solid 1px #333">&nbsp;</span>';
$footer .= 					'<span style="width:8mm; display:inline-block; font-weight:bold; text-align:right;">วันที่</span>';
$footer .= 					'<span style="width:7mm; display:inline-block; font-weight:bold; border-bottom:solid 1px #333; text-align:left;">&nbsp;</span>';
$footer .= 					'<span style="width:7mm; display:inline-block; font-weight:bold; border-bottom:solid 1px #333; text-align:left;">/</span>';
$footer .= 					'<span style="width:7mm; display:inline-block; font-weight:bold; border-bottom:solid 1px #333; text-align:left;">/</span>';
$footer .= 				'</p>';

$footer .= 				'<p class="text-left" style="font-size:9px;">';
$footer .= 					'<span style="width:12mm; display:inline-block; font-weight:bold;">จำนวนเงิน</span>';
$footer .= 					'<span style="width:52mm; display:inline-block; font-weight:bold; border-bottom:solid 1px #333">&nbsp;</span>';
$footer .= 				'</p>';

$footer .= 				'<p class="text-left" style="font-size:9px;">';
$footer .= 					'<span style="width:18mm; display:inline-block; font-weight:bold;">ภาษีหัก ณ ที่จ่าย</span>';
$footer .= 					'<span style="width:46mm; display:inline-block; font-weight:bold; border-bottom:solid 1px #333; text-align:center">'.number($order->WhtAmount, 2).'</span>';
$footer .= 				'</p>';


$footer .= 				'<p style="display:inline-block; width:40mm; border-bottom:solid 1px #333; margin-bottom:5px;">&nbsp;</p>
									<p style="display:inline-block; width:7mm; border-bottom:solid 1px #333; margin-bottom:5px; text-align:left;">&nbsp;</p>
									<p style="display:inline-block; width:7mm; border-bottom:solid 1px #333; margin-bottom:5px; text-align:left;">/</p>
									<p style="display:inline-block; width:7mm; border-bottom:solid 1px #333; margin-bottom:5px; text-align:left;">/</p>';
$footer .= 				'<p class="text-center bold" style="width:40mm; display:inline-block; margin-bottom:5px; font-size:9px;">ผู้รับเงิน/Collector</p>';
$footer .= 				'<p class="text-center bold" style="width:21mm; display:inline-block; margin-bottom:5px; font-size:9px;">วันที่/Date</p>';
$footer .=      '</td>';


$footer .=      '<td class="" style="width:60mm; border:0; border-top-right-radius:10px; border-bottom-right-radius:10px;">';
$footer .= 				'<span style="font-size:8px;">ในนาม</span>';
$footer .= 				'<strong style="display:block; font-size:8.5px;">'.$companyName.'</strong>';
$footer .= 				'<span style="display:block;">&nbsp;</span>';
$footer .= 				'<span style="display:block;">&nbsp;</span>';
$footer .= 				'<span style="display:block;">&nbsp;</span>';
$footer .= 				'<span style="display:block;">&nbsp;</span>';
$footer .= 				'<p style="border-bottom:solid 1px #333;">&nbsp;</p>';
$footer .= 				'<p class="text-center bold">ผู้รับมอบอำนาจ/Authorized Signature</p>';
$footer .=      '</td>';
$footer .=    '</tr>';
$footer .=  '</table>';
$footer .= '</div>';


$n = 1;
$index = 0;
while($total_page > 0 )
{
	$top = '';
	$top .= '<div style="width:190mm; margin:auto;">';
	$top .= 	'<div class="font-size-18 bold">'.$companyName.'</div>';
	$top .= 	'<div class="font-size-12">'.getConfig('COMPANY_ADDRESS1').' '.getConfig('COMPANY_ADDRESS2').' '.getConfig('COMPANY_POST_CODE').'</div>';
	$top .= 	'<div class="font-size-12">Tel : '.getConfig('COMPANY_PHONE').'&nbsp;&nbsp;&nbsp FAX : '.getConfig('COMPANY_FAX_NUMBER').'</div>';
	$top .= 	'<div class="font-size-12">เลขประจำตัวผู้เสียภาษี/TaxID : '.getConfig('COMPANY_TAX_ID').'</div>';
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
		$top .=     	'<div class="col-sm-2 padding-5"><span class="bold">ลูกค้า</span><span style="display:block;font-size:8px; margin-top:-3px;">customer</span></div>';
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
		$top .=     	'<div class="col-sm-5 padding-5"><span>เลขประจำตัวผู้เสียภาษี</span><span style="display:block;font-size:8px; margin-top:-3px;">Tax ID</span></div>';
		$top .=     	'<div class="col-sm-4 padding-5">'.$order->tax_id.'</div>';
		$top .=     	'<div class="col-sm-3 padding-5">'.$order->branch_name.'</div>';
		$top .= 		'</div>';
		$top .= 		'<div class="row">';
		$top .=     	'<div class="col-sm-2 padding-5"><span>อ้างอิง</span><span style="display:block;font-size:8px; margin-top:-3px;">reference</span></div>';
		$top .=     	'<div class="col-sm-10 padding-5"></div>';
		$top .= 		'</div>';
		$top .= 	'</div>';

		$top .= 	'<div style="float:left; width:84mm; height:50mm; font-size:14px; padding:10px 15px 10px 15px;  border:solid 1px; #666; border-radius:10px; margin-left:1mm">';
		$top .= 		'<div class="row">';
		$top .=     	'<div class="col-sm-4 padding-5"><span>เลขที่</span><span style="display:block;font-size:8px; margin-top:-3px;">No.</span></div>';
		$top .=     	'<div class="col-sm-8 padding-5">'.$order->code.'</div>';
		$top .= 		'</div>';
		$top .= 		'<div class="row">';
		$top .=     	'<div class="col-sm-4 padding-5"><span>วันที่</span><span style="display:block;font-size:8px; margin-top:-3px;">Date</span></div>';
		$top .=     	'<div class="col-sm-8 padding-5">'.thai_date($order->DocDate, FALSE, '/').'</div>';
		$top .= 		'</div>';
		$top .= 		'<div class="row">';
		$top .=     	'<div class="col-sm-4 padding-5"><span>เลขที่ใบสั่งขาย</span><span style="display:block;font-size:8px; margin-top:-3px;">Sales Order No.</span></div>';
		$top .=     	'<div class="col-sm-8 padding-5">'.$order->so_code.'</div>';
		$top .= 		'</div>';
		$top .= 		'<div class="row">';
		$top .=     	'<div class="col-sm-4 padding-5"><span>พนักงานขาย</span><span style="display:block;font-size:8px; margin-top:-3px;">Salesman</span></div>';
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
		// $totalBfTax = $order->vat_type == 'E' ? ($order->DocTotal - $order->VatSum) : remove_vat($order->DocTotal);
		// $totalBfTax = number($totalBfTax, 2);
		// $net_amount = number($order->DocTotal, 2)
		// $baht_text = "(".baht_text($order->DocTotal).")";
		$dpm_amount = number($dpmAmount, 2);
		$total_vat_amount = number($order->VatSum - $dpmVatSum, 2);
		$docTotal = $order->DocTotal - $dpmAmount;
		$totalBfTax = $order->vat_type == 'E' ? ($docTotal - ($order->VatSum - $dpmVatSum)) : remove_vat($docTotal);
		$totalBfTax = number($totalBfTax, 2);
		$net_amount = number($docTotal, 2);
    $remark = "";
		$baht_text = "(".baht_text($docTotal).")";
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
		$dpm_amount = "";
  }

  $subTotal = array();

	$sub_price  = '<td rowspan="'.$row_span.'" class="text-center" style="position:relative; border:solid 1px #333; border-left:0; border-right:0px; font-size:8px;" style="width:110mm; padding:3px 8px 3px 8px">';
	$sub_price .= 'ใบเสร็จรับเงินนี้จะสมบูรณ์ต่อเมื่อมีลายเซ็นผู้รับมอบอำนาจและลายเซ็นผู้รับเงิน และได้เรียกเก็บเงินตามเข็คเรียบร้อยแล้ว';
	$sub_price .= '<span style="width:30mm; position:absolute; bottom:1px; left:40mm;">ผิด ตก ยกเว้น E & O.E.</span>';
	$sub_price .= '</td>';
  $sub_price .= '<td style="width:50mm; border-top:solid 1px #333; padding:3px 8px 3px 8px">';
  $sub_price .=  '<strong>รวมเป็นเงิน</strong>';
	$sub_price .=  '<span style="display:block; font-size:8px; margin-top:-4px;">Gross Amount</span>';
  $sub_price .= '</td>';
  $sub_price .= '<td class="middle text-right" style="width:29.7mm; border:solid 1px #333; border-right:0;  border-bottom:0; padding:3px 8px 3px 8px">';
  $sub_price .=  $totalBfDisc;
  $sub_price .= '</td>';
  array_push($subTotal, array($sub_price));

	if( $order->DiscSum > 0 || $dpmAmount == 0)
	{
		//--- ส่วนลดท้ายบิล
		$sub_disc  = '<td style="border:0px; padding:3px 8px 3px 8px">';
		$sub_disc .=  '<strong>หักส่วนลด</strong>';
		$sub_disc .=  '<span style="display:block; font-size:8px; margin-top:-4px;">Less Discount</span>';
		$sub_disc .= '</td>';
		$sub_disc .= '<td class="middle text-right" style="border:solid 1px #333; border-right:0; border-bottom:0px; border-top:0px; padding:3px 8px 3px 8px">';
		$sub_disc .=  $billDiscAmount;
		$sub_disc .= '</td>';
		array_push($subTotal, array($sub_disc));

	}

	if( $dpmAmount > 0)
	{
		//--- หักมัดจำ
		$sub_dpm  = '<td style="border:0px; padding:3px 8px 3px 8px">';
		$sub_dpm .=  '<strong>หักมัดจำ</strong>';
		$sub_dpm .=  '<span style="display:block; font-size:8px; margin-top:-4px;">Less Discount</span>';
		$sub_dpm .= '</td>';
		$sub_dpm .= '<td class="middle text-right" style="border:solid 1px #333; border-right:0; border-bottom:0px; border-top:0px; padding:3px 8px 3px 8px">';
		$sub_dpm .=  $dpm_amount;
		$sub_dpm .= '</td>';
		array_push($subTotal, array($sub_dpm));
	}

	//--- มูลค่าหลังส่วนลด ก่อนภาษี
  $sub_disc  = '<td style="border:0; padding:3px 8px 3px 8px">';
  $sub_disc .=  '<strong>มูลค่าสินค้าก่อนภาษี</strong>';
	$sub_disc .=  '<span style="display:block; font-size:8px; margin-top:-4px;">Total Invoice</span>';
  $sub_disc .= '</td>';
  $sub_disc .= '<td class="middle text-right" style="border:solid 1px #333; border-right:0; border-bottom:0; border-top:0; padding:3px 8px 3px 8px">';
  $sub_disc .=  $totalBfTax;
  $sub_disc .= '</td>';
  array_push($subTotal, array($sub_disc));

  //--- ภาษี
	$taxType = $order->vat_type == 'E' ? '(Exclude)' : '(Include)';
  $sub_vat  = '<td style="border:0; border-bottom:solid 1px #333; padding:3px 8px 3px 8px">';
  $sub_vat .=  '<strong>ภาษีมูลค่าเพิ่ม &nbsp;&nbsp; 7%  <span style="font-size:9px;">&nbsp;&nbsp;'.$taxType.'</span></strong>';
	$sub_vat .=  '<span style="display:block; font-size:8px; margin-top:-4px;">VAT</span>';
  $sub_vat .= '</td>';
  $sub_vat .= '<td class="middle text-right" style="border:solid 1px #333; border-right:0; border-top:0; padding:3px 8px 3px 8px">';
  $sub_vat .=  $total_vat_amount;
  $sub_vat .= '</td>';
  array_push($subTotal, array($sub_vat));

	//--- ยอดสุทธิ
	$sub_net  = "";
	$sub_net .= '<td class="middle text-center" style="border-bottom-left-radius:10px;"><strong>'.$baht_text.'</strong></td>';
  $sub_net .= '<td>';
  $sub_net .=  '<strong>จำนวนเงินรวมทั้งสิ้น</strong>';
	$sub_net .=  '<span style="display:block; font-size:8px; margin-top:-4px;">Product Value</span>';
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
