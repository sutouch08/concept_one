<?php
function select_unit($code = '')
{
  $sc = '';
  $CI =& get_instance();
  $CI->load->model('masters/unit_model');
  $options = $CI->unit_model->get_list(); //--- OUOM

  if(!empty($options))
  {
    foreach($options as $rs)
    {
      $group_id = empty($rs->group_id) ? $rs->id : $rs->group_id;

      $sc .= '<option value="'.$rs->code.'" data-id="'.$rs->id.'" data-groupid="'.$group_id.'" '.is_selected($code, $rs->code).'>'.$rs->code.' | '.$rs->name.'</option>';
    }
  }

  return $sc;
}


 ?>
