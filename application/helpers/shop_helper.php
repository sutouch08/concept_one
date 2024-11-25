<?php

	function select_shop_id($id = NULL)
	{
		$sc = '';
	  $CI =& get_instance();
	  $CI->load->model('masters/shop_model');
	  $options = $CI->shop_model->get_all();

	  if( ! empty($options))
	  {
	    foreach($options as $rs)
	    {
	      $sc .= '<option value="'.$rs->id.'" '.is_selected($id, $rs->id).'>'.$rs->name.'</option>';
	    }
	  }

	  return $sc;
	}

	function select_shop_pos($shop_id, $pos_id = NULL)
	{
		$sc = "";
		$ci =&get_instance();
		$ci->load->model('masters/pos_model');

		$options = $ci->pos_model->get_shop_pos($shop_id);

		if( ! empty($options))
		{
			foreach($options as $rs)
			{
				$sc .= '<option value="'.$rs->id.'" '.is_selected($pos_id, $rs->id).'>'.$rs->name.'</option>';
			}
		}

		return $sc;
	}

	function select_pos_id($id = NULL)
	{
		$sc = "";
		$ci =&get_instance();
		$ci->load->model('masters/pos_model');

		$options = $ci->pos_model->get_all();

		if( ! empty($options))
		{
			foreach($options as $rs)
			{
				$sc .= '<option value="'.$rs->id.'" '.is_selected($id, $rs->id).'>'.$rs->name.'</option>';
			}
		}

		return $sc;
	}


	function select_shop_payments($shop_id, $code)
	{
		$sc = "";
		$ci =& get_instance();
		$ci->load->model('masters/shop_model');
		$options = $ci->shop_model->get_shop_payments($shop_id);

		if( ! empty($options))
		{
			foreach($options as $rs)
			{
				$sc .= '<option value="'.$rs->code.'" '.is_selected($rs->code, $code).'>'.$rs->name.'</option>';
			}
		}

		return $sc;
	}

	function select_shop_user($shop_id = NULL, $uname = NULL)
	{
		$sc = "";
		$ci =& get_instance();
		$ci->load->model('masters/shop_model');
		$options = $ci->shop_model->get_shop_user($shop_id);

		if( ! empty($options))
		{
			foreach($options as $rs)
			{
				$sc .= '<option value="'.$rs->uname.'" '.is_selected($rs->uname, $uname).'>'.$rs->name.'</option>';
			}
		}

		return $sc;
	}
 ?>
