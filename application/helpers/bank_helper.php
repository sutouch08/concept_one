<?php
function bankLogoUrl($code)
{
  $CI =& get_instance();
  $img  = $code.'.png';
  $path	= base_url().$CI->config->item('image_path').'banks/';
  $image_path = $path.$img;
  $noimg = $path.'noimg.png';
 	$file = $CI->config->item('image_file_path').'banks/'.$img;
 	if( ! file_exists($file) )
 	{
 		return $noimg;
 	}

 	return $image_path;
}


function select_bank_account($id = '')
{
  $sc = '';
  $CI =& get_instance();
  $CI->load->model('masters/bank_model');
  $banks = $CI->bank_model->get_data();
  if(!empty($banks))
  {
    foreach($banks as $rs)
    {
      $sc .= '<option value="'.$rs->id.'" '.is_selected($rs->id, $id).'>'.$rs->acc_name.'#'.$rs->acc_no.'</option>';
    }
  }

  return $sc;
}


function select_bank($code = NULL)
{
  $sc = "";
  $CI =& get_instance();
  $CI->load->model('masters/bank_code_model');

  $bank = $CI->bank_code_model->get_all_active();

  if( ! empty($bank))
  {
    foreach($bank as $rs)
    {
      $sc .= '<option value="'.$rs->code.'" '.is_selected($code, $rs->code).'>'.$rs->code.' - '.$rs->name.'</option>';
    }
  }

  return $sc;
}


function account_name_array()
{
  $ds = array();
  $ci =& get_instance();
  $ci->load->model('masters/bank_model');

  $list = $ci->bank_model->get_all();

  if( ! empty($list))
  {
    foreach($list as $rs)
    {
      $ds[$rs->id] = $rs->acc_name;
    }
  }

  return $ds;
}

 ?>
