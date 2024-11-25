<?php
class Sales_consignment_report_model extends CI_Model
{
  public function __construct()
  {
    parent::__construct();
  }


  public function get_data(array $ds = array())
  {
    if( ! empty($ds))
    {
      $this->db
      ->select('od.date_add, od.reference, od.product_code, od.product_name')
      ->select('od.cost, od.price, od.sell, od.qty, od.discount_label, od.discount_amount')
      ->select('od.total_amount, od.total_cost')
      ->select('od.customer_code, cs.name AS customer_name')
      ->select('od.warehouse_code, wh.name AS warehouse_name')
      ->select('od.zone_code, zn.name AS zone_name')
      ->from('order_sold AS od')
      ->join('customers AS cs', 'od.customer_code = cs.code', 'left')
      ->join('warehouse AS wh', 'od.warehouse_code = wh.code', 'left')
      ->join('zone AS zn', 'od.zone_code = zn.code', 'left')
      ->where('od.role', 'D');

      if( ! empty($ds['fromDate']) && ! empty($ds['toDate']))
      {
        $this->db
        ->group_start()
        ->where('od.date_add >=', from_date($ds['fromDate']))
        ->where('od.date_add <=', to_date($ds['toDate']))
        ->group_end();
      }

      if(empty($ds['allProduct']) && ! empty($ds['pdFrom']) && ! empty($ds['pdTo']))
      {
        $this->db
        ->group_start()
        ->where('od.product_code >=', $ds['pdFrom'])
        ->where('od.product_code <=', $ds['pdTo'])
        ->group_end();
      }

      if(empty($ds['allCustomer']) && ! empty($ds['cusFrom']) && ! empty($ds['cusTo']))
      {
        $this->db
        ->group_start()
        ->where('od.customer_code >=', $ds['cusFrom'])
        ->where('od.customer_code <=', $ds['cusTo'])
        ->group_end();
      }

      if(empty($ds['allWarehouse']) && ! empty($ds['warehouse_code']))
      {
        $this->db->where_in('od.warehouse_code', $ds['warehouse_code']);
      }


      if(! empty($ds['allZone']) && ! empty($ds['zone_code']))
      {
        $this->db->where('od.zone_code', $ds['zone_code']);
      }

      $rs = $this->db->order_by('od.date_add', 'ASC')->order_by('od.reference', 'ASE')->get();

      if($rs->num_rows() > 0)
      {
        return $rs->result();
      }
    }

    return NULL;
  }


} //--- end class

 ?>
