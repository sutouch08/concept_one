<?php
function select_wq($so_code = NULL, $order_code = NULL)
{
  $sc = "";
  $ci =& get_instance();
  if( ! empty($so_code))
  {
    $qs = $ci->db->where('so_code', $so_code)->get('order_transform');

    if($qs->num_rows() > 0)
    {
      foreach($qs->result() as $rs)
      {
        $sc .= '<option value="'.$rs->order_code.'"
        data-socode="'.$rs->so_code.'"
        data-closed="'.$rs->is_closed.'"
        data-reference="'.$rs->reference.'" '.is_selected($order_code, $rs->order_code).'>'.$rs->order_code.'</option>';
      }
    }
  }

  return $sc;
}


function sale_order_log_label($action = NULL)
{
  $arr = array(
    'add' => 'สร้างโดย',
    'edit' => 'แก้ไขโดย',
    'cancel' => 'ยกเลิกโดย',
    'close' => 'ปิดโดย'
  );

  return empty($arr[$action]) ? 'unknow' : $arr[$action];
}

 ?>
