<?php

function select_saleman($id = NULL)
{
  $CI =& get_instance();
  $CI->load->model('masters/slp_model');
  $result = $CI->slp_model->get_data(1);
  $ds = '';
  if(!empty($result))
  {
    foreach($result as $rs)
    {
      $ds .= '<option value="'.$rs->id.'" '.is_selected($rs->id, $id).'>'.$rs->name.'</option>';
    }
  }

  return $ds;
}

function get_sale_name($id = NULL)
{
  $ci =& get_instance();
  $ci->load->model('masters/slp_model');

  return $ci->slp_model->get_name($id);
}

function saleman_array()
{
  $ds = array();

  $ci =& get_instance();
  $ci->load->model('masters/slp_model');

  $list = $ci->slp_model->get_all_slp();

  if( ! empty($list))
  {
    foreach($list as $rs)
    {
      $ds[$rs->id] = $rs->name;
    }
  }

  return $ds;
}
 ?>
