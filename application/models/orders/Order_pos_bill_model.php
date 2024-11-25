<?php
class Order_pos_bill_model extends CI_Model
{
  private $tb = "order_pos";
  private $td = "order_pos_detail";

  public function __construct()
  {
    parent::__construct();
  }


  public function get_by_id($id)
  {
    $rs = $this->db->where('id', $id)->get($this->tb);

    if($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return NULL;
  }


  public function get($code)
  {
    $rs = $this->db->where('code', $code)->get($this->tb);

    if($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return NULL;
  }


  public function get_details($code)
  {
    $rs = $this->db->where('order_code', $code)->get($this->td);

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function count_rows(array $ds = array())
  {
    if( ! empty($ds['code']))
    {
      $this->db->like('code', $ds['code']);
    }

    if( ! empty($ds['ref_code']))
    {
      $this->db->like('ref_code', $ds['ref_code']);
    }

    if( ! empty($ds['so_code']))
    {
      $this->db->like('so_code', $ds['so_code']);
    }

    if( ! empty($ds['shop_id']) && $ds['shop_id'] != 'all')
    {
      $this->db->where('shop_id', $ds['shop_id']);
    }

    if( ! empty($ds['pos_id']) && $ds['pos_id'] != 'all')
    {
      $this->db->where('pos_id', $ds['pos_id']);
    }

    if( ! empty($ds['channels']) && $ds['channels'] != 'all')
    {
      $this->db->where('channels_code', $ds['channels']);
    }

    if( ! empty($ds['sale_id']) && $ds['sale_id'] != 'all')
    {
      $this->db->where('sale_id', $ds['sale_id']);
    }

    if( ! empty($ds['payment']) && $ds['payment'] != 'all')
    {
      $this->db->where('payment_code', $ds['payment']);
    }

    if( ! empty($ds['warehouse']) && $ds['warehouse'] != 'all')
    {
      $this->db->where('warehouse_code', $ds['warehouse']);
    }

    if( ! empty($ds['status']) && $ds['status'] != 'all')
    {
      $this->db->where('status', $ds['status']);
    }

    if( isset($ds['vat_type']) && $ds['vat_type'] != 'all')
    {
      $this->db->where('vat_type', $ds['vat_type']);
    }

    if( ! empty($ds['user']) && $ds['user'] != 'all')
    {
      $this->db->where('uname', $ds['user']);
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
    ->select('o.*, p.name AS payment_name, c.name AS channels_name, sa.name AS sale_name')
    ->select('pos.code AS pos_code, pos.name AS pos_name')
    ->from('order_pos AS o')
    ->join('shop_pos AS pos', 'o.pos_id = pos.id', 'left')
    ->join('payment_method AS p', 'o.payment_code = p.code', 'left')
    ->join('channels AS c', 'o.channels_code = c.code', 'left')
    ->join('saleman AS sa', 'o.sale_id = sa.id', 'left');


    if( ! empty($ds['code']))
    {
      $this->db->like('o.code', $ds['code']);
    }

    if( ! empty($ds['so_code']))
    {
      $this->db->like('o.so_code', $ds['so_code']);
    }

    if( ! empty($ds['ref_code']))
    {
      $this->db->like('o.ref_code', $ds['ref_code']);
    }

    if( ! empty($ds['shop_id']) && $ds['shop_id'] != 'all')
    {
      $this->db->where('o.shop_id', $ds['shop_id']);
    }

    if( ! empty($ds['pos_id']) && $ds['pos_id'] != 'all')
    {
      $this->db->where('o.pos_id', $ds['pos_id']);
    }

    if( ! empty($ds['channels']) && $ds['channels'] != 'all')
    {
      $this->db->where('o.channels_code', $ds['channels']);
    }

    if( ! empty($ds['sale_id']) && $ds['sale_id'] != 'all')
    {
      $this->db->where('o.sale_id', $ds['sale_id']);
    }

    if( ! empty($ds['payment']) && $ds['payment'] != 'all')
    {
      $this->db->where('o.payment_code', $ds['payment']);
    }

    if( ! empty($ds['status']) && $ds['status'] != 'all')
    {
      $this->db->where('o.status', $ds['status']);
    }

    if( isset($ds['vat_type']) && $ds['vat_type'] != 'all')
    {
      $this->db->where('o.vat_type', $ds['vat_type']);
    }

    if( ! empty($ds['user']) && $ds['user'] != 'all')
    {
      $this->db->where('o.uname', $ds['user']);
    }

    if( ! empty($ds['from_date']))
    {
      $this->db->where('o.date_add >=', from_date($ds['from_date']));
    }

    if( ! empty($ds['to_date']))
    {
      $this->db->where('o.date_add <=', to_date($ds['to_date']));
    }

    $rs = $this->db
    ->order_by('o.id', 'DESC')
    ->limit($perpage, $offset)
    ->get();

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }
} //--- end class
 ?>
