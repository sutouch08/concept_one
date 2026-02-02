<?php
function pick_list_status_text($status = 'P')
{
  $list = [
    'P' => 'Pending',
    'R' => 'Released',
    'C' => 'Closed',
    'D' => 'Canceled'
  ];

  return empty($list[$status]) ? 'Pending' : $list[$status];
}


 ?>
