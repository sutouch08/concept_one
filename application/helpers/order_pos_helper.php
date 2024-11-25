<?php
function bill_status_label($status = 'O')
{
  $list = array(
    'O' => 'Open',
    'C' => 'Closed',
    'D' => 'Canceled'
  );

  return ! empty($list[$status]) ? $list[$status] : "Open";
}

function movement_type_label($type)
{
  $list = array(
    'S' => 'ขาย',
    'C' => 'ยกเลิก',
    'R' => 'คืน',
    'CR' => 'ยกเลิกการคืน',
    'CI' => 'นำเงินเข้า',
    'CO' => 'นำเงินออก',
    'DP' => 'รับมัดจำ',
    'DC' => 'ยกเลิกเงินมัดจำ',
    'RO' => 'เปิดรอบขาย',
    'RC' => 'ปิดรอบขาย'
  );

  return empty($list[$type]) ? 'Unknow' : $list[$type];
}

function payment_role_label($role)
{
  $list = array(
    '1' => 'เงินสด',
    '2' => 'เงินโอน',
    '3' => 'บัตรเครดิต',
    '4' => 'COD',
    '5' => 'เครดิตเทอม',
    '6' => 'หลายช่องทาง',
    '7' => 'เช็ค'
  );

  return ! empty($list[$role]) ? $list[$role] : 'Unknow';
}

 ?>
