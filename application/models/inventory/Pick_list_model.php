<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Pick_list_model extends CI_Model
{
  private $tb = "pick_list";
  private $td = "pick_details";
  private $tr = "pick_rows";
  private $to = "pick_orders";

  public function __construct()
  {
    parent::__construct();
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

  public function get_by_id($id)
  {
    $rs = $this->db->where('id', $id)->get($this->tb);

    if($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return NULL;
  }


  public function get_details($code)
  {
    $rs = $this->db
    ->select('p.*, c.name AS channels_name')
    ->from('pick_details AS p')
    ->join('channels AS c', 'p.channels_code = c.code', 'left')
    ->where('p.pick_code', $code)
    ->get();

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function get_pick_orders($code)
  {
    $rs = $this->db
    ->select('p.*, c.name AS channels_name')
    ->from('pick_orders AS p')
    ->join('channels AS c', 'p.channels_code = c.code', 'left')
    ->where('p.pick_code', $code)
    ->order_by('p.order_code', 'ASC')
    ->get();

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function get_pick_rows($code)
  {
    $rs = $this->db
    ->where('pick_code', $code)
    ->order_by('product_code', 'ASC')
    ->get($this->tr);

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function get_pick_row($code, $product_code)
  {
    $rs = $this->db
    ->where('pick_code', $code)
    ->where('product_code', $product_code)
    ->get($this->tr);

    if($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return NULL;
  }


  public function get_detail($id)
  {
    $rs = $this->db->where('id', $id)->get($this->td);

    if($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return NULL;
  }


  public function get_row($id)
  {
    $rs = $this->db->where('id', $id)->get($this->tr);

    if($rs->num_rows() > 0)
    {
      return $rs->row();
    }

    return NULL;
  }


  public function get_sum_release_qty($code)
  {
    $rs = $this->db->select_sum('release_qty')->where('pick_code', $code)->get($this->tr);

    if($rs->num_rows() > 0)
    {
      return $rs->row()->release_qty;
    }

    return 0;
  }


  public function get_total_process_qty($code)
  {
    $rs = $this->db
    ->select_sum('release_qty')
    ->select_sum('pick_qty')
    ->where('pick_code', $code)
    ->get($this->tr);

    if($rs->num_rows() > 0)
    {
      return $rs->row();
    }

    return NULL;
  }


  public function add(array $ds = array())
  {
    if( ! empty($ds))
    {
      if($this->db->insert($this->tb, $ds))
      {
        return $this->db->insert_id();
      }
    }

    return FALSE;
  }


  public function add_detail(array $ds = array())
  {
    if( ! empty($ds))
    {
      if($this->db->insert($this->td, $ds))
      {
        return $this->db->insert_id();
      }
    }

    return FALSE;
  }


  public function add_pick_order(array $ds = array())
  {
    if( ! empty($ds))
    {
      return $this->db->insert($this->to, $ds);
    }

    return FALSE;
  }


  public function add_row(array $ds = array())
  {
    if( ! empty($ds))
    {
      return $this->db->insert($this->tr, $ds);
    }

    return FALSE;
  }


  public function update($code, array $ds = array())
  {
    if( ! empty($ds))
    {
      return $this->db->where('code', $code)->update($this->tb, $ds);
    }

    return FALSE;
  }


  public function update_detail($id, array $ds = array())
  {
    if( ! empty($ds))
    {
      return $this->db->where('id', $id)->update($this->td, $ds);
    }

    return FALSE;
  }


  public function update_details($code, array $ds = array())
  {
    if( ! empty($ds))
    {
      return $this->db->where('pick_code', $code)->update($this->td, $ds);
    }

    return FALSE;
  }


  public function update_row($id, array $ds = array())
  {
    if( ! empty($ds))
    {
      return $this->db->where('id', $id)->update($this->tr, $ds);
    }

    return FALSE;
  }


  public function update_rows($code, array $ds = array())
  {
    if( ! empty($ds))
    {
      return $this->db->where('pick_code', $code)->update($this->tr, $ds);
    }

    return FALSE;
  }


  public function delete_row($id)
  {
    return $this->db->where('id', $id)->delete($this->tr);
  }


  public function delete_order($code, $order_code)
  {
    return $this->db->where('pick_code', $code)->where('order_code', $order_code)->delete($this->to);
  }


  public function delete_detail($id)
  {
    return $this->db->where('id', $id)->delete($this->td);
  }


  public function delete_detail_by_order($code, $order_code)
  {
    return $this->db->where('pick_code', $code)->where('order_code', $order_code)->delete($this->td);
  }


  public function delete_rows($code)
  {
    return $this->db->where('pick_code', $code)->delete($this->tr);
  }


  public function get_list(array $ds = array(), $perpage = 20, $offset = 0, $is_mobile = FALSE)
  {
    if( ! empty($ds['code']))
    {
      $this->db->like('code', $ds['code']);
    }

    if(isset($ds['warehouse']) && $ds['warehouse'] != 'all')
    {
      $this->db->where('warehouse_code', $ds['warehouse']);
    }

    if(isset($ds['channels']) && $ds['channels'] != 'all')
    {
      $this->db->where('channels_code', $ds['channels']);
    }

    if(isset($ds['status']) && $ds['status'] != 'all')
    {
      $this->db->where('status', $ds['status']);
    }

    if( ! empty($ds['from_date']))
    {
      $this->db->where('date_add >=', from_date($ds['from_date']));
    }

    if( ! empty($ds['to_date']))
    {
      $this->db->where('date_add <=', to_date($ds['to_date']));
    }

    $rs = $this->db->order_by('id', 'DESC')->limit($perpage, $offset)->get($this->tb);

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function count_rows(array $ds = array(), $is_mobile = FALSE)
  {
    if( ! empty($ds['code']))
    {
      $this->db->like('code', $ds['code']);
    }

    if(isset($ds['warehouse']) && $ds['warehouse'] != 'all')
    {
      $this->db->where('warehouse_code', $ds['warehouse']);
    }

    if(isset($ds['channels']) && $ds['channels'] != 'all')
    {
      $this->db->where('channels_code', $ds['channels']);
    }

    if(isset($ds['sender_id']) && $ds['sender_id'] != 'all')
    {
      $this->db->where('sender_id', $ds['sender_id']);
    }

    if(isset($ds['status']) && $ds['status'] != 'all')
    {
      $this->db->where('status', $ds['status']);
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


  public function get_order_list(array $ds = array())
  {
    $this->db
    ->from('orders AS o')
    ->select('o.id, o.code, o.reference, o.customer_code, o.customer_name, o.channels_code')
    ->select('o.pick_list_id, o.date_add, c.name AS channels_name')
    ->join('channels AS c', 'o.channels_code = c.code', 'left');

    $this->db
    ->where_in('o.state', 3)
    ->where('o.is_cancled', 0)
    ->where('o.warehouse_code', $ds['warehouse_code']);

    if( ! empty($ds['from_date']))
    {
      $this->db->where('o.date_add >=', from_date($ds['from_date']));
    }

    if( ! empty($ds['to_date']))
    {
      $this->db->where('o.date_add <=', to_date($ds['to_date']));
    }

    if( isset($ds['channels']) && $ds['channels'] != 'all')
    {
      $this->db->where('o.channels_code', $ds['channels']);
    }

    if(isset($ds['is_pick_list']) && $ds['is_pick_list'] != 'all')
    {
      if($ds['is_pick_list'] == 1)
      {
        $this->db->where('o.pick_list_id IS NOT NULL', NULL, FALSE);
      }
      else
      {
        $this->db->where('o.pick_list_id IS NULL', NULL, FALSE);
      }
    }

    if( ! empty($ds['code']))
    {
      $this->db->like('o.code', $ds['code']);
    }

    if( ! empty($ds['customer']))
    {
      $this->db
      ->group_start()
      ->like('o.customer_code', $ds['customer'])
      ->or_like('o.customer_name', $ds['customer'])
      ->group_end();
    }

    $rs = $this->db->order_by('o.id', 'ASC')->limit($ds['limit'])->get();

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function is_order_in_correct_state($order_code)
  {
    $count = $this->db->where('code', $order_code)->where_in('state', ['3', '4'])->where('is_cancled', 0)->count_all_results('orders');

    return $count === 1 ? TRUE : FALSE;
  }


  public function get_order_details($order_code)
  {
    $rs = $this->db
    ->select('order_code, product_code, product_name, is_count')
    ->select_sum('qty')
    ->where('order_code', $order_code)
    ->where('is_count', 1)
    ->group_by('product_code')
    ->get('order_details');

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function is_exists_order_detail($pick_id, $order_code, $product_code)
  {
    $count = $this->db
    ->where('pick_id', $pick_id)
    ->where('order_code', $order_code)
    ->where('product_code', $product_code)
    ->where('line_status !=', 'D')
    ->count_all_results($this->td);

    return $count > 0 ? TRUE : FALSE;
  }


  public function get_status_by_id($id)
  {
    $rs = $this->db->select('status')->where('id', $id)->get($this->tb);

    if($rs->num_rows() === 1)
    {
      return $rs->row()->status;
    }

    return NULL;
  }


  public function remove_order_pick_list_id($pick_list_id)
  {
    return $this->db->set('pick_list_id', NULL)->where('pick_list_id', $pick_list_id)->update('orders');
  }


  public function get_max_code($pre)
  {
    $rs = $this->db->select_max('code')->like('code', $pre, 'after')->get($this->tb);

    if($rs->num_rows() === 1)
    {
      return $rs->row()->code;
    }

    return NULL;
  }
} //--- end class


 ?>
