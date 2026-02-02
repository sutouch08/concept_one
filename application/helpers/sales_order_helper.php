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


function select_job_type($code = NULL)
{
  $ds = "";
  $ci =& get_instance();
  $qs = $ci->db->get('job_type');

  if($qs->num_rows() > 0)
  {
    foreach($qs->result() as $rs)
    {
      $ds .= '<option value="'.$rs->code.'" '.is_selected($rs->code, $code).'>'.$rs->name.'</option>';
    }
  }

  return $ds;
}


function job_name($code)
{
  $ci =& get_instance();
  $qs = $ci->db->select('name')->where('code', $code)->get('job_type');

  if($qs->num_rows() === 1)
  {
    return $qs->row()->name;
  }

  return NULL;
}


function select_so_state($state = NULL)
{
  $states = [
    '1' => 'รอดำเนินการ',
    '2' => 'รอชำระเงิน',
    '3' => 'รอแบบ',
    '31' => 'ออกแบบเสร็จ',
    '32' => 'รอปริ้นเฟล็ก',
    '33' => 'ปริ้นเฟล็กเสร็จ',
    '4' => 'รอผลิต',
    '5' => 'กำลังผลิต',
    '6' => 'ผลิตเสร็จ',
    '7' => 'รอจัดส่ง',
    '8' => 'จัดส่งแล้ว'
  ];

  $ds = "";

  foreach($states as $st => $name)
  {
    $ds .= '<option value="'.$st.'" '.is_selected(strval($st), strval($state)).'>'.$name.'</option>';
  }

  return $ds;
}

 ?>
