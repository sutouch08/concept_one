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

function select_employee($id = NULL)
{
  $ds = "";

  $ci = &get_instance();
  $ci->load->model('masters/employee_model');

  $list = $ci->employee_model->get_all();

  if (! empty($list))
  {
    foreach ($list as $rs)
    {
      $ds .= '<option value="' . $rs->empID . '" data-name="' . $rs->name . '" data-active="' . $rs->Active . '" ' . is_selected($id, $rs->empID) . '>' . $rs->firstName . '  ' . $rs->lastName . '</option>';
    }
  }

  return $ds;
}


function select_active_employee($id = NULL)
{
  $ds = "";

  $ci = &get_instance();
  $ci->load->model('masters/employee_model');

  $list = $ci->employee_model->get_all(1);

  if (! empty($list))
  {
    foreach ($list as $rs)
    {
      $rs->name = $rs->firstName . ' ' . $rs->lastName;

      $ds .= '<option value="' . $rs->empID . '" data-name="' . $rs->name . '" data-active="' . $rs->Active . '" ' . is_selected($id, $rs->empID) . '>' . $rs->name . '</option>';
    }
  }

  return $ds;
}


function employee_name($id)
{
  $ci = &get_instance();
  $ci->load->model('masters/employee_model');

  return $ci->employee_model->get_name($id);
}

?>
