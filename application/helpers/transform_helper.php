<?php
function getTransformProducts($transform_product, $state = 1, $is_expired = 0, $is_approved = 0, $can_approve = 0)
{
	$sc = '';

	if(!empty($transform_product))
	{
		foreach($transform_product as $rs)
		{
			$sc .= '<div class="display-block">';
			$sc .= $rs->product_code.' : '.$rs->order_qty.'/'.$rs->sold_qty.'/'.$rs->receive_qty;

			if($is_expired == 0 && (($is_approved == 0 && $state == 1) OR $can_approve))
			{
				//---	ถ้ายังไม่ได้รับสินค้า สามารถลบได้
				if($rs->receive_qty == 0)
				{
					$sc .= '<span class="red pointer" onclick="removeTransformProduct('.$rs->id_order_detail.', \''.$rs->product_code.'\')">  <i class="fa fa-times fa-lg">';
					$sc .= '</i></span>';
				}
			}

			$sc .= '</div>';
		}
	}

	return $sc;
}

 ?>
