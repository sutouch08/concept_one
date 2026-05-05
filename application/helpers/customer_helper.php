<?php
function select_GroupCode($code = NULL)
{
  $sc = '';
  $CI =& get_instance();
  $CI->load->model('masters/customers_model');
  $options = $CI->customers_model->getGroupCode(); //--- OCRG

  if(!empty($options))
  {
    foreach($options as $rs)
    {
      $sc .= '<option value="'.$rs->code.'" '.is_selected(strval($code), strval($rs->code)).'>'.$rs->name.'</option>';
    }
  }

  return $sc;
}



function select_GroupNum($code = NULL)
{
  $sc = '';
  $CI =& get_instance();
  $CI->load->model('masters/customers_model');
  $options = $CI->customers_model->getGroupNum(); //--- OCRG

  if(!empty($options))
  {
    foreach($options as $rs)
    {
      $sc .= '<option value="'.$rs->code.'" '.is_selected(strval($code), strval($rs->code)).'>'.$rs->name.'</option>';
    }
  }

  return $sc;
}



function select_DebPayAcct($code = NULL)
{
  $sc = '';
  $CI =& get_instance();
  $CI->load->model('masters/customers_model');
  $options = $CI->customers_model->getDebPayAcct(); //--- OCRG

  if(!empty($options))
  {
    foreach($options as $rs)
    {
      $sc .= '<option value="'.$rs->code.'" '.is_selected(strval($code), strval($rs->code)).'>'.$rs->code.' => '.$rs->name.'</option>';
    }
  }

  return $sc;
}



function select_sale($code = NULL)
{
  $sc = '';
  $CI =& get_instance();
  $CI->load->model('masters/customers_model');
  $options = $CI->customers_model->getSlp();

  if(!empty($options))
  {
    foreach($options as $rs)
    {
      $sc .= '<option value="'.$rs->code.'" '.is_selected(strval($code), strval($rs->code)).'>'.$rs->name.'</option>';
    }
  }

  return $sc;
}

function select_customer_group($code = NULL)
{
  $sc = '';
  $CI =& get_instance();
  $CI->load->model('masters/customer_group_model');
  $options = $CI->customer_group_model->get_data();

  if(!empty($options))
  {
    foreach($options as $rs)
    {
      $sc .= '<option value="'.$rs->code.'" '.is_selected(strval($code), strval($rs->code)).'>'.$rs->name.'</option>';
    }
  }

  return $sc;

}


function select_customer_kind($code = NULL)
{
  $sc = '';
  $CI =& get_instance();
  $CI->load->model('masters/customer_kind_model');
  $options = $CI->customer_kind_model->get_data();

  if(!empty($options))
  {
    foreach($options as $rs)
    {
      $sc .= '<option value="'.$rs->code.'" '.is_selected(strval($code), strval($rs->code)).'>'.$rs->name.'</option>';
    }
  }
  return $sc;
}



function select_customer_type($code = NULL)
{
  $sc = '';
  $CI =& get_instance();
  $CI->load->model('masters/customer_type_model');
  $options = $CI->customer_type_model->get_data();

  if(!empty($options))
  {
    foreach($options as $rs)
    {
      $sc .= '<option value="'.$rs->code.'" '.is_selected(strval($code), strval($rs->code)).'>'.$rs->name.'</option>';
    }
  }
  return $sc;
}



function select_customer_class($code = NULL)
{
  $sc = '';
  $CI =& get_instance();
  $CI->load->model('masters/customer_class_model');
  $options = $CI->customer_class_model->get_data();

  if(!empty($options))
  {
    foreach($options as $rs)
    {
      $sc .= '<option value="'.$rs->code.'" '.is_selected(strval($code), strval($rs->code)).'>'.$rs->name.'</option>';
    }
  }
  return $sc;
}



function select_customer_area($code = NULL)
{
  $sc = '';
  $CI =& get_instance();
  $CI->load->model('masters/customer_area_model');
  $options = $CI->customer_area_model->get_data();

  if(!empty($options))
  {
    foreach($options as $rs)
    {
      $sc .= '<option value="'.$rs->code.'" '.is_selected(strval($code), strval($rs->code)).'>'.$rs->name.'</option>';
    }
  }
  return $sc;
}




function customer_in($txt)
{
  $sc = array('0');
  $CI =& get_instance();
  $CI->load->model('masters/customers_model');
  $rs = $CI->customers_model->search($txt);

  if(!empty($rs))
  {
    foreach($rs as $cs)
    {
      $sc[] = $cs->code;
    }
  }

  return $sc;
}



 ?>
