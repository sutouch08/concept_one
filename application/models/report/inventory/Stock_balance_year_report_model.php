<?php
class Stock_balance_year_report_model extends CI_Model
{
  public function __construct()
  {
    parent::__construct();
  }


  public function get_data(array $ds = array())
  {
    $this->ms
    ->select('OITM.ItemCode AS product_code')
    ->select('OITM.ItemName AS product_name')
    ->select('OITM.U_YEAR AS productyear')
    ->select_sum('OIBQ.OnHandQty', 'qty')
    ->from('OIBQ')
    ->join('OITM', 'OIBQ.ItemCode = OITM.ItemCode', 'left')
    ->join('OBIN', 'OIBQ.BinAbs = OBIN.AbsEntry','left')
    ->where('OIBQ.OnHandQty !=', 0, FALSE);

    if(empty($ds['allWarehouse']) && !empty($ds['warehouse']))
    {
      $this->ms->where_in('OIBQ.WhsCode', $ds['warehouse']);
    }

    if(empty($ds['allProduct']) && !empty($ds['pdFrom']) && !empty($ds['pdTo']))
    {
      $this->ms->where('OITM.U_MODEL >=', $ds['pdFrom'])->where('OITM.U_MODEL <=', $ds['pdTo']);
    }

    $this->ms->order_by('OITM.ItemCode', 'ASC');

    //echo $this->ms->get_compiled_select();

    $rs = $this->ms->get();

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


}
 ?>
