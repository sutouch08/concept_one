<?php
function select_payment_method($code = '')
{
  $sc = '';
  $CI =& get_instance();
  $CI->load->model('masters/payment_methods_model');
  $payments = $CI->payment_methods_model->get_all();
  if(!empty($payments))
  {
    foreach($payments as $rs)
    {
      $sc .= '<option value="'.$rs->code.'" '.is_selected($rs->code, $code).'>'.$rs->name.'</option>';
    }
  }

  return $sc;
}


function select_payment_role($id='')
{
  $sc = "";
  $CI =& get_instance();
  $CI->load->model('masters/payment_methods_model');
  $payments = $CI->payment_methods_model->get_roles();
  if(!empty($payments))
  {
    foreach($payments as $rs)
    {
      $sc .= '<option value="'.$rs->id.'" '.is_selected($rs->id, $id).'>'.$rs->name.'</option>';
    }
  }

  return $sc;
}


function select_pos_payment_method($code = "")
{
	$sc = "";
	$CI =& get_instance();
	$CI->load->model('masters/payment_methods_model');
	$payments = $CI->payment_methods_model->get_pos_payment_list();

	if(!empty($payments))
	{
		foreach($payments as $rs)
		{
			$sc .= '<option value="'.$rs->code.'" data-role="'.$rs->role.'" '.is_selected($rs->code, $code).'>'.$rs->name.'</option>';
		}
	}

	return $sc;
}


function select_transfer_payment($code = NULL)
{
  $sc = "";
	$CI =& get_instance();
	$CI->load->model('masters/payment_methods_model');
	$payments = $CI->payment_methods_model->get_list_by_role(2);

	if(!empty($payments))
	{
		foreach($payments as $rs)
		{
			$sc .= '<option value="'.$rs->code.'" data-role="'.$rs->role.'" '.is_selected($rs->code, $code).'>'.$rs->code.' : '.$rs->name.'</option>';
		}
	}

	return $sc;
}


function payment_role_name($role)
{
  $name = array(
    '1' => "เงินสด",
    '2' => "เงินโอน",
    '3' => "บัตรเครดิต",
    '4' => "COD",
    '5' => "เครดิต",
    '6' => "หลายช่องทาง",
    '7' => "เช็ค"
  );

  return empty($name[$role]) ? "Unknow" : $name[$role];
}

 ?>
