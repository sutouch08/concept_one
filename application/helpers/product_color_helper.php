<?php
function select_color_group($id = '')
{
  $CI =& get_instance();
  $CI->load->model('masters/product_color_model');
  $result = $CI->product_color_model->get_color_group();
  $ds = '';
  if(!empty($result))
  {
    foreach($result as $rs)
    {
      $ds .= '<option value="'.$rs->id.'" '.is_selected($rs->id, $id).'>'.$rs->code.' : '.$rs->name.'</option>';
    }
  }

  return $ds;
}


 ?>
