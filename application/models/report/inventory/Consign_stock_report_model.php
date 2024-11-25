<?php
class Consign_stock_report_model extends CI_Model
{
  public function __construct()
  {
    parent::__construct();
  }

  public function get_consign_stock_zone($allProduct, $pdFrom, $pdTo, $allWhouse, $warehouse, $allZone, $zoneCode)
  {
    $this->ms
    ->select('OITM.ItemCode AS product_code')
    ->select('OITM.ItemName AS product_name')
    ->select('OBIN.WhsCode AS warehouse_code')
    ->select('OBIN.BinCode AS zone_code')
    ->select('OBIN.Descr AS zone_name')
    ->select('OIBQ.OnHandQty AS qty')
    ->from('OIBQ')
    ->join('OITM', 'OIBQ.ItemCode = OITM.ItemCode', 'left')
    ->join('OBIN', 'OIBQ.BinAbs = OBIN.AbsEntry','left')
    ->where('OIBQ.OnHandQty !=', 0, FALSE);

    if($allProduct == 0 && !empty($pdFrom) && !empty($pdTo))
    {
      $this->ms->where('OITM.U_MODEL >=', $pdFrom)->where('OITM.U_MODEL <=', $pdTo);
    }

    if($allZone == 1 && empty($zoneCode))
    {
      if($allWhouse == 0 && !empty($warehouse))
      {
        $this->ms->where_in('OIBQ.WhsCode', $warehouse);
      }
    }

    if($allZone == 0 && !empty($zoneCode))
    {
      $this->ms->where('OBIN.BinCode', $zoneCode);
    }

    $this->ms->order_by('OBIN.WhsCode', 'ASC');
    $this->ms->order_by('OBIN.BinCode', 'ASC');
    $this->ms->order_by('OITM.ItemCode', 'ASC');

    $rs = $this->ms->get();

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return FALSE;
  }
}
 ?>
