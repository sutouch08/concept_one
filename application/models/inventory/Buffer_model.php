<?php
class Buffer_model extends CI_Model
{
  public function __construct()
  {
    parent::__construct();
  }

  public function get($id)
  {
    $rs = $this->db->where('id', $id)->get('buffer');

    if($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return NULL;
  }


  public function delete_prepared($order_detail_id, $zone_code)
  {
    return $this->db->where('order_detail_id', $order_detail_id)->where('zone_code', $zone_code)->delete('prepare');
  }


  public function get_data(array $ds = array(), $perpage = NULL, $offset = NULL)
  {
    $this->db
    ->select('buffer.*')
    ->select('zone.name AS zone_name')
    ->select('order_state.name AS state_name')
    ->select('orders.state')
    ->select('order_details.product_name')
    ->from('buffer')
    ->join('order_details', 'buffer.order_detail_id = order_details.id', 'left')
    ->join('zone', 'buffer.zone_code = zone.code', 'left')
    ->join('orders', 'buffer.order_code = orders.code', 'left')
    ->join('order_state', 'orders.state = order_state.state');

    if(!empty($ds['order_code']))
    {
      $this->db->like('buffer.order_code',$ds['order_code']);
    }

    if(!empty($ds['pd_code']))
    {
      $this->db->like('buffer.product_code', $ds['pd_code']);
    }

    if(!empty($ds['zone_code']))
    {
      $this->db->group_start();
      $this->db->like('buffer.zone_code', $ds['zone_code']);
      $this->db->or_like('zone.name', $ds['zone_code']);
      $this->db->group_end();
    }

    if(!empty($ds['from_date']) && !empty($ds['to_date']))
    {
      $this->db->where('buffer.date_upd >=', from_date($ds['from_date']));
      $this->db->where('buffer.date_upd <=', to_date($ds['to_date']));
    }

    if( ! empty($ds['user']) && $ds['user'] != 'all')
    {
      $this->db->where('buffer.user', $ds['user']);
    }

    if($perpage > 0)
    {
      $offset = $offset === NULL ? 0 : $offset;
      $this->db->limit($perpage, $offset);
    }

    $rs = $this->db->get();

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return FALSE;
  }


  public function count_rows(array $ds = array(), $perpage = NULL, $offset = NULL)
  {
    $this->db
    ->from('buffer')
    ->join('zone', 'buffer.zone_code = zone.code', 'left')
    ->join('orders', 'buffer.order_code = orders.code', 'left')
    ->join('order_state', 'orders.state = order_state.state');

    if(!empty($ds['order_code']))
    {
      $this->db->like('buffer.order_code',$ds['order_code']);
    }

    if(!empty($ds['pd_code']))
    {
      $this->db->like('buffer.product_code', $ds['pd_code']);
    }

    if(!empty($ds['zone_code']))
    {
      $this->db->group_start();
      $this->db->like('buffer.zone_code', $ds['zone_code']);
      $this->db->or_like('zone.name', $ds['zone_code']);
      $this->db->group_end();
    }

    if(!empty($ds['from_date']) && !empty($ds['to_date']))
    {
      $this->db->where('buffer.date_upd >=', from_date($ds['from_date']));
      $this->db->where('buffer.date_upd <=', to_date($ds['to_date']));
    }

    if( ! empty($ds['user']) && $ds['user'] != 'all')
    {
      $this->db->where('buffer.user', $ds['user']);
    }

    return $this->db->count_all_results();
  }

  public function get_sum_buffer_product($order_code, $product_code, $detail_id = NULL)
  {
    $rs = $this->db
    ->select_sum('qty')
    ->where('order_code', $order_code)
    ->where('product_code', $product_code)
    ->group_start()
    ->where('order_detail_id', $detail_id)
    ->or_where('order_detail_id IS NULL', NULL, FALSE)
    ->group_end()
    ->get('buffer');

    if($rs->num_rows() === 1)
    {
      return $rs->row()->qty;
    }

    return 0;
  }


  public function get_sum_order_qty($order_code)
  {
    $qr  = "SELECT SUM(qty) AS qty FROM buffer ";
    $qr .= "WHERE order_code = '{$order_code}' ";
    $qr .= "AND product_code IN((SELECT product_code FROM order_details WHERE order_code = '{$order_code}'))";

    $rs = $this->db->query($qr);

    return intval($rs->row()->qty);
  }


  public function get_details($order_code, $product_code, $detail_id = NULL)
  {
    $this->db
    ->where('order_code', $order_code)
    ->where('product_code', $product_code)
    ->group_start()
    ->where('order_detail_id', $detail_id)
    ->or_where('order_detail_id IS NULL', NULL, FALSE)
    ->group_end();

    $rs = $this->db->get('buffer');

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return FALSE;
  }



  public function get_all_details($order_code)
  {
    $rs = $this->db->where('order_code', $order_code)->get('buffer');
    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return FALSE;
  }


  public function get_sum_stock($code)
  {
    $rs = $this->db->select_sum('qty')->where('product_code', $code)->get('buffer');
    if($rs->num_rows() == 1)
    {
      return $rs->row()->qty;
    }

    return 0;
  }

  ///--- เอาเฉพาะสินค้าและโซน
  public function get_buffer_zone($zone_code, $product_code)
  {
    $rs = $this->db
    ->select_sum('qty')
    ->where('zone_code', $zone_code)
    ->where('product_code', $product_code)
    ->get('buffer');

    return $rs->row()->qty;
  }

  //---- เอาเฉพาะสินค้าและโซน ใช้ตอน QC
  public function get_prepared_from_zone($order_code, $product_code)
  {
    $this->db
    ->select('buffer.*, zone.name')
    ->from('buffer')
    ->join('zone', 'zone.code = buffer.zone_code')
    ->where('order_code', $order_code)
    ->where('product_code', $product_code);

    $rs = $this->db->get();

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function add(array $ds = array())
  {
    if(!empty($ds))
    {
      return $this->db->insert('buffer', $ds);
    }

    return FALSE;
  }


  public function update($order_code, $product_code, $zone_code, $qty, $detail_id = NULL)
  {
    $this->db
    ->set("qty", "qty + {$qty}", FALSE)
    ->where('order_code', $order_code)
    ->where('product_code', $product_code)
    ->where('zone_code', $zone_code)
    ->group_start()
    ->where('order_detail_id', $detail_id)
    ->or_where('order_detail_id IS NULL', NULL, FALSE)
    ->group_end();

    return $this->db->update('buffer');
  }


  public function delete($id)
  {
    return $this->db->where('id', $id)->delete('buffer');
  }



  public function delete_all($code)
  {
    return $this->db->where('order_code', $code)->delete('buffer');
  }


	public function drop_buffer($order_code)
	{
		return $this->db->where('order_code', $order_code)->delete('buffer');
	}


  public function is_exists($order_code, $product_code, $zone_code, $detail_id = NULL)
  {
    $this->db
    ->where('order_code', $order_code)
    ->where('product_code', $product_code)
    ->where('zone_code', $zone_code)
    ->group_start()
    ->where('order_detail_id', $detail_id)
    ->or_where('order_detail_id IS NULL', NULL, FALSE)
    ->group_end();

    $count = $this->db->count_all_results('buffer');

    if($count > 0)
    {
      return TRUE;
    }

    return FALSE;
  }



  public function remove_zero_buffer($code)
  {
    return $this->db->where('order_code', $code)->where('qty', 0)->delete('buffer');
  }


  public function remove_buffer($order_code, $item_code, $detail_id = NULL)
  {
    $this->db
    ->where('order_code', $order_code)
    ->where('product_code', $item_code)
    ->group_start()
    ->where('order_detail_id', $detail_id)
    ->or_where('order_detail_id IS NULL', NULL, FALSE)
    ->group_end();

    return $this->db->delete('buffer');
  }

  public function delete_by_order_detail_id($order_detail_id)
  {
    return $this->db->where('order_detail_id', $order_detail_id)->delete('buffer');
  }
}
 ?>
