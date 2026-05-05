<?php

function bookcode_name($bookcode)
{
  $name = 'Unknow';

  switch($bookcode)
  {
    case 'C' :
      $name = 'เงินสด';
      break;
    case 'T' :
      $name = 'เงินเชื่อ';
      break;
    case 'U' :
      $name = 'อภินันท์';
      break;
  }

  return $name;
}
 ?>
