<?php
class Pos_sales_movement_model extends CI_Model
{
  private $tb = "pos_sales_movement";

  public function __construct()
  {
    parent::__construct();
  }


  public function add(array $ds = array())
  {
    if( ! empty($ds))
    {
      if( $this->db->insert($this->tb, $ds))
      {
        return $this->db->insert_id();
      }
    }

    return FALSE;
  }

  public function get($id)
  {
    $rs = $this->db
    ->select('mv.*, u.name AS emp_name')
    ->from('pos_sales_movement AS mv')
    ->join('user AS u', 'mv.user = u.uname', 'left')
    ->where('mv.id', $id)
    ->get();

    if($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return NULL;
  }


  public function get_sum_cash_in_by_round_id($pos_id, $round_id)
  {
    $rs = $this->db
    ->select_sum('amount')
    ->where('type', 'CI')
    ->where('payment_role', 1)
    ->where('pos_id', $pos_id)
    ->where('round_id', $round_id)
    ->get($this->tb);

    if($rs->num_rows() === 1)
    {
      return $rs->row()->amount;
    }

    return 0.00;
  }


  public function get_sum_cash_out_by_round_id($pos_id, $round_id)
  {
    $rs = $this->db
    ->select_sum('amount')
    ->where('type', 'CO')
    ->where('payment_role', 1)
    ->where('pos_id', $pos_id)
    ->where('round_id', $round_id)
    ->get($this->tb);

    if($rs->num_rows() === 1)
    {
      return $rs->row()->amount;
    }

    return 0.00;
  }


  public function get_movement_by_round_id($pos_id, $round_id)
  {
    $rs = $this->db
    ->where('pos_id', $pos_id)
    ->where('round_id', $round_id)
    ->order_by('date_upd', 'ASC')
    ->get($this->tb);

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function count_rows(array $ds = array())
  {
    $this->db
    ->from('pos_sales_movement AS mv')
    ->join('order_pos_round AS r', 'mv.round_id = r.id', 'left');

    if( ! empty($ds['code']))
    {
      $this->db->like('mv.code', $ds['code']);
    }

    if( ! empty($ds['round_code']))
    {
      $this->db->like('r.code', $ds['round_code']);
    }

    if( ! empty($ds['shop_id']) && $ds['shop_id'] != 'all')
    {
      $this->db->where('mv.shop_id', $ds['shop_id']);
    }

    if( ! empty($ds['pos_id']) && $ds['pos_id'] != 'all')
    {
      $this->db->where('mv.pos_id', $ds['pos_id']);
    }

    if( ! empty($ds['type']) && $ds['type'] != 'all')
    {
      $this->db->where('mv.type', $ds['type']);
    }

    if( ! empty($ds['role']) && $ds['role'] != 'all')
    {
      $this->db->where('mv.payment_role', $ds['role']);
    }

    if( ! empty($ds['bank']) && $ds['bank'] != 'all')
    {
      $this->db->where('mv.acc_id', $ds['bank']);
    }

    if( ! empty($ds['from_date']))
    {
      $this->db->where('mv.date_upd >=', from_date($ds['from_date']));
    }

    if( ! empty($ds['to_date']))
    {
      $this->db->where('mv.date_upd <=', to_date($ds['to_date']));
    }

    return $this->db->count_all_results();
  }

  public function get_list(array $ds = array(), $perpage = 20, $offset = 0)
  {
    $this->db
    ->select('mv.*')
    ->select('ac.acc_no')
    ->select('pos.code AS pos_code, pos.name AS pos_name')
    ->select('sh.code AS shop_code, sh.name AS shop_name')
    ->select('r.code AS round_code')
    ->from('pos_sales_movement AS mv')
    ->join('bank_account AS ac', 'mv.acc_id = ac.id', 'left')
    ->join('shop_pos AS pos', 'mv.pos_id = pos.id', 'left')
    ->join('shop AS sh', 'mv.shop_id = sh.id', 'left')
    ->join('order_pos_round AS r', 'mv.round_id = r.id', 'left');

    if( ! empty($ds['code']))
    {
      $this->db->like('mv.code', $ds['code']);
    }

    if( ! empty($ds['round_code']))
    {
      $this->db->like('r.code', $ds['round_code']);
    }

    if( ! empty($ds['shop_id']) && $ds['shop_id'] != 'all')
    {
      $this->db->where('mv.shop_id', $ds['shop_id']);
    }

    if( ! empty($ds['pos_id']) && $ds['pos_id'] != 'all')
    {
      $this->db->where('mv.pos_id', $ds['pos_id']);
    }

    if( ! empty($ds['type']) && $ds['type'] != 'all')
    {
      $this->db->where('mv.type', $ds['type']);
    }

    if( ! empty($ds['role']) && $ds['role'] != 'all')
    {
      $this->db->where('mv.payment_role', $ds['role']);
    }

    if( ! empty($ds['bank']) && $ds['bank'] != 'all')
    {
      $this->db->where('mv.acc_id', $ds['bank']);
    }

    if( ! empty($ds['from_date']))
    {
      $this->db->where('mv.date_upd >=', from_date($ds['from_date']));
    }

    if( ! empty($ds['to_date']))
    {
      $this->db->where('mv.date_upd <=', to_date($ds['to_date']));
    }

    $rs = $this->db
    ->order_by('mv.date_upd', 'DESC')
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
