<?php
function get_state_name($state)
{
  $name = array(
    '1' => 'รอดำเนินการ',
    '2' => 'รอชำระเงิน',
    '3' => 'รอแบบ',
    '31' => 'ออกแบบเสร็จ',
    '32' => 'รอปริ้นเฟล็ก',
    '33' => 'ปริ้นเฟล็กเสร็จ',
    '4' => 'รอผลิต',
    '5' => 'กำลังผลิต',
    '6' => 'ผลิตเสร็จ',
    '7' => 'รอการจัดส่ง',
    '8' => 'จัดส่งแล้ว',
    '9' => 'ยกเลิก'
  );

  return empty($name[$state]) ? 'Unknow' : $name[$state];
}


function state_color($state, $status = 'O')
{
  if($status == 'P')
  {
    return '';
  }
  else
  {
    $color = array(
      '1' => 'color:#333; background-color:#AEE2FF;',
      '2' => 'color:#333; background-color:#B9F3FC;',
      '3' => 'color:#000; background-color:#FFE6E6;',
      '31' => 'color:#000; background-color:#E1AFD1;',
      '32' => 'color:#000; background-color:#D0BFFF;',
      '33' => 'color:#000; background-color:#BEADFA;',
      '4' => 'color:#000; background-color:#EFD595;',
      '5' => 'color:#000; background-color:#EFB495;',
      '6' => 'color:#000; background-color:#EF9595;',
      '7' => 'color:#000; background-color:#B5F1CC;',
      '8' => 'color:#000; background-color:#51af5b;',
      '9' => 'color:#000; background-color:#C9F4AA;'
    );

    return empty($color[$state]) ? $color[1] : $color[$state];
  }
}

?>
