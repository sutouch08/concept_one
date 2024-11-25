<?php
class Stock_balance_report_model extends CI_Model
{
  private $priceList = 2;
  private $costList = 3;

  public function __construct()
  {
    parent::__construct();
    $this->priceList = getConfig('SAP_COST_LIST');
    $this->costList = getConfig('SAP_PRICE_LIST');
  }

  public function get_stock_balance_zone($allProduct, $pdFrom, $pdTo, $allWhouse, $warehouse, $allZone, $zoneCode)
  {
    $this->ms
    ->select('OITM.ItemCode AS product_code')
    ->select('OITM.ItemName AS product_name')
    ->select('ITM1.Price AS price')
    ->select('OBIN.WhsCode AS warehouse_code')
    ->select('OBIN.BinCode AS zone_code')
    ->select('OBIN.Descr AS zone_name')
    ->select('OIBQ.OnHandQty AS qty')
    ->from('OIBQ')
    ->join('OITM', 'OIBQ.ItemCode = OITM.ItemCode', 'left')
    ->join('ITM1', "OITM.ItemCode = ITM1.ItemCode AND ITM1.PriceList = {$this->costList}")
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

		 $this->ms->order_by('OITM.ItemCode', 'ASC');
    $this->ms->order_by('OBIN.WhsCode', 'ASC');
    $this->ms->order_by('OBIN.BinCode', 'ASC');


    $rs = $this->ms->get();

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return FALSE;
  }


}
 ?>
