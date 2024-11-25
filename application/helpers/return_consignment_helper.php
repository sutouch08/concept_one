<?php
function getInvoiceList($code, $invoice_list, $status = 0)
{
	$sc = '';

	if(!empty($invoice_list))
	{
		$i = 1;
		$count = count($invoice_list);
		foreach($invoice_list as $rs)
		{
			$sc .= '<div class="inline">';
			$sc .= $rs->invoice_code;

			//---	ถ้ายังไม่ได้รับสินค้า สามารถลบได้
			if($status == 0)
			{
				$sc .= '<span class="red pointer margin-right-5" onclick="removeInvoice(\''.$code.'\', \''.$rs->invoice_code.'\')">  <i class="fa fa-times">';
				$sc .= '</i></span>';
			}
			else
			{
				$sc .= $count > 1 ? ($i < $count ? ", " : "") : "";
			}
			$sc .= '</div>';
			$i++;
		}
	}

	return $sc;
}


//---- invoice list for export document
function getAllInvoiceText($invoice_list)
{
	$sc = '';

	if(!empty($invoice_list))
	{
		$i = 1;
		$count = count($invoice_list);
		foreach($invoice_list as $rs)
		{
			$sc .= $i === 1 ? $rs->invoice_code : ", {$rs->invoice_code}";
			$i++;
		}
	}

	return $sc;
}

 ?>
