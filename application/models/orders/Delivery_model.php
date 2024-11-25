<?php
class Delivery_model extends CI_Model
{
  private $tb = "orders";
  private $td = "order_details";
  private $ts = "order_sold";

  public function __construct()
  {
    parent::__construct();
  }


  public function get($code)
  {
    $rs = $this->db->where('code', $code)->where('state', 8)->get($this->tb);

    if($rs->num_rows() == 1)
    {
      return $rs->row();
    }

    return NULL;
  }


  public function get_details($code)
  {
    $rs = $this->db->where('reference', $code)->get($this->ts);

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function count_rows(array $ds = array())
  {
    $this->db
    ->where('isNew', 1)
    ->where('state', 8)
    ->where_in('role', ['S', 'U', 'P', 'C']);

    if( ! empty($ds['code']))
    {
      $this->db->like('code', $ds['code']);
    }

    if( ! empty($ds['so_code']))
    {
      $this->db->like('so_code', $ds['so_code']);
    }

    if( ! empty($ds['reference']))
    {
      $this->db->like('reference', $ds['reference']);
    }

    if( ! empty($ds['invoice_code']))
    {
      $this->db->like('invoice_code', $ds['invoice_code']);
    }

    if( ! empty($ds['customer']))
    {
      $cust = $this->db->escape_str($ds['customer']);

      $this->db
      ->group_start()
      ->like('customer_code', $cust)
      ->or_like('customer_name', $cust)
      ->or_like('customer_ref', $cust)
      ->group_end();
    }

    if( isset($ds['channels']) && $ds['channels'] != 'all')
    {
      $this->db->where('channels_code', $ds['channels']);
    }

    if( isset($ds['warehouse']) && $ds['warehouse'] != 'all')
    {
      $this->db->where('warehouse_code', $ds['warehouse']);
    }

    if( isset($ds['is_term']) && $ds['is_term'] != 'all')
    {
      $this->db->where('is_term', $ds['is_term']);
    }

    if( isset($ds['tax_status']) && $ds['tax_status'] != 'all')
    {
      $this->db->where('TaxStatus', $ds['tax_status']);
    }

    if( isset($ds['status']) && $ds['status'] != 'all')
    {
      if($ds['status'] == 'O')
      {
        $this->db->where('invoice_code IS NULL', NULL, FALSE);
      }

      if($ds['status'] == 'C')
      {
        $this->db->where('invoice_code IS NOT NULL', NULL, FALSE);
      }
    }

    if( isset($ds['sale_code']) && $ds['sale_code'] != 'all')
    {
      $this->db->where('sale_code', $ds['sale_code']);
    }

    if( isset($ds['user']) && $ds['user'] != 'all')
    {
      $this->db->where('user', $ds['user']);
    }

    if( ! empty($ds['from_date']))
    {
      $this->db->where('date_add >=', from_date($ds['from_date']));
    }

    if( ! empty($ds['to_date']))
    {
      $this->db->where('date_add <=', to_date($ds['to_date']));
    }

    return $this->db->count_all_results($this->tb);
  }



  public function get_list(array $ds = array(), $perpage = 20, $offset = 0)
  {
    $this->db
    ->where('isNew', 1)
    ->where('state', 8)
    ->where_in('role', ['S', 'U', 'P', 'C']);

    if( ! empty($ds['code']))
    {
      $this->db->like('code', $ds['code']);
    }

    if( ! empty($ds['so_code']))
    {
      $this->db->like('so_code', $ds['so_code']);
    }

    if( ! empty($ds['reference']))
    {
      $this->db->like('reference', $ds['reference']);
    }

    if( ! empty($ds['invoice_code']))
    {
      $this->db->like('invoice_code', $ds['invoice_code']);
    }

    if( ! empty($ds['customer']))
    {
      $cust = $this->db->escape_str($ds['customer']);

      $this->db
      ->group_start()
      ->like('customer_code', $cust)
      ->or_like('customer_name', $cust)
      ->or_like('customer_ref', $cust)
      ->group_end();
    }

    if( isset($ds['channels']) && $ds['channels'] != 'all')
    {
      $this->db->where('channels_code', $ds['channels']);
    }

    if( isset($ds['warehouse']) && $ds['warehouse'] != 'all')
    {
      $this->db->where('warehouse_code', $ds['warehouse']);
    }

    if( isset($ds['is_term']) && $ds['is_term'] != 'all')
    {
      $this->db->where('is_term', $ds['is_term']);
    }

    if( isset($ds['tax_status']) && $ds['tax_status'] != 'all')
    {
      $this->db->where('TaxStatus', $ds['tax_status']);
    }

    if( isset($ds['status']) && $ds['status'] != 'all')
    {
      if($ds['status'] == 'O')
      {
        $this->db->where('invoice_code IS NULL', NULL, FALSE);
      }

      if($ds['status'] == 'C')
      {
        $this->db->where('invoice_code IS NOT NULL', NULL, FALSE);
      }
    }

    if( isset($ds['sale_code']) && $ds['sale_code'] != 'all')
    {
      $this->db->where('sale_code', $ds['sale_code']);
    }

    if( isset($ds['user']) && $ds['user'] != 'all')
    {
      $this->db->where('user', $ds['user']);
    }

    if( ! empty($ds['from_date']))
    {
      $this->db->where('date_add >=', from_date($ds['from_date']));
    }

    if( ! empty($ds['to_date']))
    {
      $this->db->where('date_add <=', to_date($ds['to_date']));
    }


    $rs = $this->db
    ->order_by('id', 'DESC')
    ->limit($perpage, $offset)
    ->get($this->tb);

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }
}

 ?>
