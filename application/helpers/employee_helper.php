<?php
function employee_in($txt)
{
  $sc = array('0');
  $CI =& get_instance();
  $CI->load->model('masters/employee_model');
  $rs = $CI->employee_model->search($txt);

  if(!empty($rs))
  {
    foreach($rs as $cs)
    {
      $sc[] = $cs->empID;
    }
  }

  return $sc;
}

?>
