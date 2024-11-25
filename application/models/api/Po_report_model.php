<?php
class Po_report_model extends CI_Model
{
  public function __construct()
  {
      parent::__construct();
  }



  public function get_open_po_details()
  {
    $rs = $this->ms
    ->select('ItemCode')
    ->select_sum('Quantity')
    ->select_sum('OpenQty')
    ->where('LineStatus', 'O')
    ->group_by('ItemCode')
    //->limit(100)
    ->get('POR1');

    if(!empty($rs))
    {
      return $rs->result();
    }

    return NULL;
  }

} //--- end class
?>
