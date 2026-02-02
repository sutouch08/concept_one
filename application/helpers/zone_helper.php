<?php
function zone_in($txt)
{
  $sc = array('0');
  $CI =& get_instance();
  $CI->load->model('inventory/zone_model');
  $zone = $CI->zone_model->search($txt);
  if(!empty($zone))
  {
    foreach($zone as $rs)
    {
      $sc[] = $rs->code;
    }
  }

  return $sc;
}

function select_pickface_zone($code = NULL)
{
  $sc = "";

  $ci =& get_instance();
  $ci->load->model('maters/zone_model');
  $zone = $ci->zone_model->get_pickface_zone();

  if( ! empty($zone))
  {
    foreach($zone as $rs)
    {
      $sc .= '<option value="'.$rs->code.'" data-warehouse="'.$rs->warehouse_code.'" '.is_selected($rs->code, $code).'>'.$rs->name.'</option>';
    }
  }

  return $sc;
}


function get_default_zone($whsCode = NULL)
{
  $zone = NULL;

  if( ! empty($whsCode))
  {
    $ci =& get_instance();
    $ci->load->model('masters/zone_model');

    $zone = $ci->zone_model->get_default_zone($whsCode);
  }

  return $zone;
}

 ?>
