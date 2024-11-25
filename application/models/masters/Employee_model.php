<?php
class Employee_model extends CI_Model
{
  public function __construct()
  {
    parent::__construct();
  }


  public function get($id)
  {
    $rs = $this->ms
    ->select('empID, lastName, firstName')
    ->where('empID', $id)->get('OHEM');
    if($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return FALSE;
  }



  public function get_name($id)
  {
    $rs = $this->ms->select('firstName, lastName')->where('empID', $id)->get('OHEM');
    if($rs->num_rows() === 1)
    {
      return $rs->row()->firstName.' '.$rs->row()->lastName;
    }

    return FALSE;
  }


  public function search($txt)
  {
    $qr  = "SELECT empID, firstName, lastName FROM OHEM ";
    $qr .= "WHERE firstName LIKE N'%{$txt}%' OR lastName LIKE N'%{$txt}%'";
    $rs = $this->ms->query($qr);
    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }
    else
    {
      return array();
    }
  }


}//--- end class
 ?>
