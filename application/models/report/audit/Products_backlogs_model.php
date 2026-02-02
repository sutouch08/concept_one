<?php
class Products_backlogs_model extends CI_Model
{
  public function __construct()
  {
    parent::__construct();
  }


  public function get_data(array $ds = array(), $barcode = FALSE)
  {
    if(! empty($ds))
    {
      $this->db->select_sum('d.qty', 'order_qty')->select('d.product_code, d.product_name');

      if($barcode)
      {
        $this->db->select('p.barcode');
      }

      $this->db->from('order_details AS d')->join('orders AS o', 'd.order_code = o.code', 'left');

      if($barcode)
      {
        $this->db->join('products AS p', 'd.product_code = p.code', 'left');
      }

      $this->db
      ->where_in('o.state', ['1', '2', '3', '4'])
      ->where('o.date_add >=', from_date($ds['from_date']))
      ->where('o.date_add <=', to_date($ds['to_date']))
      ->where('o.warehouse_code', $ds['warehouse_code'])
      ->where('d.is_count', 1)
      ->where('d.is_complete', 0)
      ->where('d.valid', 0);

      if($ds['all_role'] == 0 && ! empty($ds['role']))
      {
        $this->db->where_in('o.role', $ds['role']);
      }

      if($ds['all_channels'] == 0 && ! empty($ds['channels']))
      {
        $this->db->where_in('o.channels_code', $ds['channels']);
      }

      $rs = $this->db
      ->group_by('d.product_code')
      ->order_by('d.product_code', 'ASC')
      ->get();

      if($rs->num_rows() > 0)
      {
        return $rs->result();
      }
    }

    return NULL;
  }



  public function get_doc_total($code)
  {
    $rs = $this->db->select_sum('total_amount')->where('order_code', $code)->get('order_details');

    if($rs->num_rows() === 1)
    {
      return $rs->row()->total_amount;
    }

    return 0.00;
  }

} //-- end class

 ?>
