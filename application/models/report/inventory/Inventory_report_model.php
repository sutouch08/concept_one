<?php
class Inventory_report_model extends CI_Model
{
  public function __construct()
  {
    parent::__construct();
  }

  public function get_current_stock_balance($allProduct, $pdFrom, $pdTo, $allWhouse, $warehouse)
  {
    $this->ms
    ->select('OITM.ItemCode AS product_code')
    ->select_sum('OIBQ.OnHandQty', 'qty')
    ->from('OIBQ')
    ->join('OITM', 'OIBQ.ItemCode = OITM.ItemCode', 'left')
    ->join('OBIN', 'OIBQ.BinAbs = OBIN.AbsEntry','left')
    ->where('OIBQ.OnHandQty !=', 0, FALSE);

    if($allProduct == 0 && !empty($pdFrom) && !empty($pdTo))
    {
      $this->ms->where('OITM.U_MODEL >=', $pdFrom)->where('OITM.U_MODEL <=', $pdTo);
    }

    if($allWhouse == 0 && !empty($warehouse))
    {
      $this->ms->where_in('OIBQ.WhsCode', $warehouse);
    }

    $this->ms->group_by('OITM.ItemCode');
    $this->ms->order_by('OITM.ItemCode', 'ASC');
    $rs = $this->ms->get();

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return FALSE;
  }


  public function get_reserv_stock($item_code, $warehouse = NULL)
  {
    $this->db
    ->select_sum('order_details.qty', 'qty')
    ->from('order_details')
    ->join('orders', 'order_details.order_code = orders.code', 'left')
    ->where('order_details.product_code', $item_code)
    ->where('order_details.is_complete', 0)
    ->where('order_details.is_expired', 0)
		->where('order_details.is_cancle', 0)
    ->where('order_details.is_count', 1);

    if($warehouse !== NULL)
    {
      $this->db->where_in('orders.warehouse_code', $warehouse);
    }

    $rs = $this->db->get();

    if($rs->num_rows() == 1)
    {
      return $rs->row()->qty;
    }

    return 0;
  }

}
 ?>
