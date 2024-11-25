<?php
class Pos_sales_report_model extends CI_Model
{
  public function __construct()
  {
    parent::__construct();
  }

  public function get_online_channels_details(array $ds = array())
  {
    if(!empty($ds))
    {
      $this->db
      ->select('o.code AS code, o.date_add, o.reference')
      ->select('o.shipping_code, o.shipping_fee, o.service_fee')
      ->select('o.customer_ref, o.id_address, c.name AS channels')
      ->select('pm.name AS payment, st.name AS state')
      ->select('od.product_code, od.price')
      ->select('od.qty, od.discount_amount, od.total_amount')
      ->from('order_details AS od')
      ->join('orders AS o', 'od.order_code = o.code', 'left')
      ->join('channels AS c', 'o.channels_code = c.code', 'left')
      ->join('payment_method AS pm', 'o.payment_code = pm.code', 'left')
      ->join('order_state AS st', 'o.state = st.state', 'left')
      ->where('o.date_add >=', from_date($ds['from_date']))
      ->where('o.date_add <=', to_date($ds['to_date']))
      ->where('o.role', 'S')
      ->where('c.is_online', 1)
      ->where('o.is_expired', 0);

      if($ds['all_channels'] == 0 && !empty($ds['channels']))
      {
        $this->db->where_in('o.channels_code', $ds['channels']);
      }

      if(!empty($ds['item_from']) && !empty($ds['item_to']))
      {
        $this->db
        ->where('od.product_code >=', $ds['item_from'])
        ->where('od.product_code <=', $ds['item_to']);
      }

      if(!empty($ds['from_reference']) && !empty($ds['to_reference']))
      {
        $this->db
        ->where('o.reference >=', $ds['from_reference'])
        ->where('o.reference <=', $ds['to_reference']);
      }

      $this->db->order_by('o.code', 'ASC')->order_by('od.product_code', 'ASC');

      $rs = $this->db->get();

      if($rs->num_rows() > 0)
      {
        return $rs->result();
      }
    }

    return NULL;
  }

}
 ?>
