<?php
function sender_in($txt)
{
  $sc = "9999999";
  $CI =& get_instance();
  $CI->load->model('masters/sender_model');
  $ds = $CI->sender_model->search($txt);

  if(!empty($ds))
  {
    foreach($ds as $rs)
    {
      $sc .= ", {$rs->id}";
    }
  }

  return $sc;
}



function select_common_sender($customer_code = NULL, $id = NULL)
{
	$sc = "";

	$CI =& get_instance();
  $CI->load->model('masters/sender_model');


	$sender = $CI->sender_model->get_customer_sender_list($customer_code);
	$list = array();

	if(!empty($sender))
	{

		if(!empty($sender->main_sender))
		{
			$list[] = $sender->main_sender;
		}

		if(!empty($sender->second_sender))
		{
			$list[] = $sender->second_sender;
		}


		if(!empty($sender->third_sender))
		{
			$list[] = $sender->third_sender;
		}


		if(!empty($list))
		{
			$ds = $CI->sender_model->get_sender_in($list);

			if(!empty($ds))
			{
				foreach($ds as $rs)
				{
					$sc .= '<option data-tracking="'.$rs->force_tracking.'" data-gen="'.$rs->auto_gen.'" data-prefix="'.$rs->prefix.'" value="'.$rs->id.'" '.(empty($id) ? is_selected($rs->id, $list[0]) : is_selected($rs->id, $id)).'>'.$rs->name.'</option>';
				}
			}
		}
	}

	$common = $CI->sender_model->get_common_list($list);

	if(!empty($common))
	{
		foreach($common as $rs)
		{
			$sc .= '<option data-tracking="'.$rs->force_tracking.'" data-gen="'.$rs->auto_gen.'" data-prefix="'.$rs->prefix.'"value="'.$rs->id.'" '.is_selected($rs->id, $id).'>'.$rs->name.'</option>';
		}
	}

	return $sc;
}



function get_tracking($id_sender, $orderCode)
{
	$ci =& get_instance();
	$ci->load->model('masters/sender_model');

	$tracking = "";

	$sender = $ci->sender_model->get($id_sender);

	if( ! empty($sender))
	{
		if($sender->auto_gen == 1)
		{
			$code = str_replace('-', '', $orderCode);
			$tracking = $sender->prefix.$code;
		}
	}

	return $tracking;
}


 ?>
