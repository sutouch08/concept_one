<?php
function select_channels($code = '')
{
  $sc = '';
  $CI =& get_instance();
  $CI->load->model('masters/channels_model');
  $channels = $CI->channels_model->get_data();
  if(!empty($channels))
  {
    foreach($channels as $rs)
    {
      $sc .= '<option value="'.$rs->code.'" '.is_selected($rs->code, $code).'>'.$rs->name.'</option>';
    }
  }

  return $sc;
}


function select_channels_type($is_online = NULL)
{
	$sc  = '<option value="0" '.is_selected('0', $is_online).'>Offline</option>';
	$sc .= '<option value="1" '.is_selected('1', $is_online).'>Online</option>';

	return $sc;
}

function channels_name($code)
{
  $ci =& get_instance();
  $rs = $ci->db->select('name')->where('code', $code)->get('channels');

  if($rs->num_rows() === 1)
  {
    return $rs->row()->name;
  }

  return NULL;
}

function channels_array()
{
  $ds = array();
  $ci =& get_instance();
  $ci->load->model('masters/channels_model');

  $list = $ci->channels_model->get_all();

  if( ! empty($list))
  {
    foreach($list as $rs)
    {
      $ds[$rs->code] = $rs->name;
    }
  }

  return $ds;
}
 ?>
