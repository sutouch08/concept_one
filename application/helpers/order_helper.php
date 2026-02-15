<?php

function paymentLabel($payments = NULL)
{
	$sc = "";

	if( ! empty($payments))
	{
		foreach($payments as $rs)
		{
			if($rs->valid ==1)
			{
				$sc .= '<button type="button" class="btn btn-sm btn-success margin-right-5" onClick="viewPaymentDetail('.$rs->id.')">';
				$sc .= 'จ่ายเงินแล้ว | '.number($rs->pay_amount,2);
				$sc .= '</button>';
			}
			else
			{
				$sc .= '<button type="button" class="btn btn-sm btn-primary margin-right-5" onClick="viewPaymentDetail('.$rs->id.')">';
				$sc .= 'แจ้งชำระแล้ว | '.number($rs->pay_amount,2);
				$sc .= '</button>';
			}

		}
	}


	return $sc;
}



function paymentExists($order_code)
{
  $CI =& get_instance();
  $CI->load->model('orders/order_payment_model');
  return $CI->order_payment_model->is_exists($order_code);
}


function payment_image_url($order_code)
{
  $CI =& get_instance();
	$link	= base_url().'images/payments/'.$order_code.'.jpg';
  $file = $CI->config->item('image_file_path').'payments/'.$order_code.'.jpg';
	if( ! file_exists($file) )
	{
		$link = FALSE;
	}

	return $link;
}


function getSpace($amount, $length)
{
	$sc = '';
	$i	= strlen($amount);
	$m	= $length - $i;
	while($m > 0 )
	{
		$sc .= '&nbsp;';
		$m--;
	}
	return $sc.$amount;
}




function get_summary($order, $details, $banks)
{
	$payAmount = 0;
	$orderAmount = 0;
	$discount = 0;
	$totalAmount = 0;

	$orderTxt = '<div>สรุปการสั่งซื้อ</div>';
	$orderTxt .= '<div>Order No : '.$order->code.'</div>';
	$orderTxt .= '<div style="width:100%; border-bottom:solid 1px #CCC;">&nbsp;</div>';

	foreach($details as $rs)
	{
		$orderTxt .= '<div class="width-100">';
		$orderTxt .=   $rs->product_code.' <span class="pull-right">('.number($rs->qty).') x '.number($rs->price, 2);
		$orderTxt .= '</div>';
		$orderAmount += $rs->qty * $rs->price;
		$discount += $rs->discount_amount;
		$totalAmount += $rs->total_amount;
	}

	$orderTxt .= "<br/>";
	$orderTxt .= 'ค่าสินค้ารวม'.getSpace(number( $orderAmount, 2), 24).'<br/><br/>';

	if( ($discount + $order->bDiscAmount) > 0 )
	{
		$orderTxt .= 'ส่วนลดรวม'.getSpace('- '.number( ($discount + $order->bDiscAmount), 2), 27).'<br/>';
		$orderTxt .= '<br/>';
	}



	$payAmount = $orderAmount - ($discount + $order->bDiscAmount);
	$orderTxt .= 'ยอดชำระ' . getSpace(number( $payAmount, 2), 29).'<br/>';


	$orderTxt .= '====================<br/><br/>';

	if(!empty($banks))
	{
		$orderTxt .= 'สามารถชำระได้ที่ <br/>';
		$orderTxt .= '<br/>';
		foreach($banks as $rs)
		{
			$orderTxt .= '- '.$rs->bank_name.'<br/>';
			$orderTxt .= '&nbsp;&nbsp;&nbsp;&nbsp;สาขา '.$rs->branch.'<br/>';
			$orderTxt .= '&nbsp;&nbsp;&nbsp;&nbsp;ชื่อบัญชี '.$rs->acc_name.'<br/>';
			$orderTxt .= '&nbsp;&nbsp;&nbsp;&nbsp;เลขที่บัญชี '.$rs->acc_no.'<br/>';
			$orderTxt .= '--------------------<br/>';
		}
	}

	$orderTxt .= "<br/>";
	$orderTxt .= '** หากต้องการใบกำกับภาษี รบกวนแจ้งขอใบกำกับภาษีภายใน 5 วันทำการ';

	return $orderTxt;
}


function select_order_role($role = '')
{
	$sc = '';
	$CI =& get_instance();
	$rs = $CI->db->query("SELECT * FROM order_role");
	if($rs->num_rows() > 0)
	{
		foreach($rs->result() as $rd)
		{
			$sc .= '<option value="'.$rd->code.'" '.is_selected($role, $rd->code).'>'.$rd->name.'</option>';
		}
	}

	return $sc;
}


function role_name($role)
{
	$ds = array(
		'C' => 'ฝากขาย',
		'L'	=> 'ยิม',
		'M'	=> 'ตัดยอดฝากขาย',
		'N' => 'ฝากขายโอนคลัง',
		'P'	=> 'สปอนเซอร์',
		'R'	=> 'เบิก',
		'S'	=> 'ขาย',
		'T'	=> 'แปรสภาพ',
		'U'	=> 'อภินันท์',
	);

	return isset($ds[$role]) ? $ds[$role] : NULL;
}

function state_name($state)
{
	$stateList = array(
		'1' => 'รอดำเนินการ',
		'2' => 'รอชำระเงิน',
		'3' => 'รอจัดสินค้า',
		'4' => 'กำลังจัดสินค้า',
		'5' => 'รอตรวจสินค้า',
		'6' => 'กำลังตรวจสินค้า',
		'7' => 'รอการจัดส่ง',
		'8' => 'จัดส่งแล้ว',
		'9' => 'ยกเลิก'
	);

	return empty($stateList[$state]) ? "Unknow" : $stateList[$state];
}


function get_order_images($code)
{
	$ci =& get_instance();

	$rs = $ci->db->where('order_code', $code)->get('order_images');

	if($rs->num_rows() > 0)
	{
		return $rs->result();
	}

	return NULL;
}

 ?>
