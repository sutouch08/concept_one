<?php
class Order_pos_round_model extends CI_Model
{
  private $tb = 'order_pos_round';

  public function __construct()
  {
    parent::__construct();
  }

  public function get($id)
  {
    $rs = $this->db->where('id', $id)->get($this->tb);

    if($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return NULL;
  }


  public function get_open_round_by_pos_id($pos_id)
  {
    $rs = $this->db->where('pos_id', $pos_id)->where('status', 'O')->get($this->tb);

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


  public function update($id, array $ds = array())
  {
    if( ! empty($ds))
    {
      return $this->db->where('id', $id)->update($this->tb, $ds);
    }

    return FALSE;
  }


  public function count_rows(array $ds = array())
  {
    if( ! empty($ds['code']) && $ds['code'] != '')
    {
      $this->db->like('code', $ds['code']);
    }

    if( ! empty($ds['shop_id']) && $ds['shop_id'] != 'all')
    {
      $this->db->where('shop_id', $ds['shop_id']);
    }

    if( ! empty($ds['pos_id']) && $ds['pos_id'] != 'all')
    {
      $this->db->where('pos_id', $ds['pos_id']);
    }

    if( ! empty($ds['status']) && $ds['status'] != 'all')
    {
      $this->db->where('status', $ds['status']);
    }

    if( ! empty($ds['open_from_date']))
    {
      $this->db->where('open_date >=', from_date($ds['open_from_date']));
    }

    if( ! empty($ds['open_to_date']))
    {
      $this->db->where('open_date <=', to_date($ds['open_to_date']));
    }

    if( ! empty($ds['close_from_date']))
    {
      $this->db->where('close_date >=', from_date($ds['close_from_date']));
    }

    if( ! empty($ds['close_to_date']))
    {
      $this->db->where('close_date <=', to_date($ds['close_to_date']));
    }

    return $this->db->count_all_results($this->tb);
  }


  public function get_list(array $ds = array(), $perpage = 20, $offset = 0)
  {
    $order_by = empty($ds['order_by']) ? 'open_date' : $ds['order_by'];
    $sort_by = empty($ds['sort_by']) ? 'DESC' : $ds['sort_by'];

    $this->db
    ->select('pd.*, pos.code AS pos_code, pos.name AS pos_name, sh.code AS shop_code, sh.name AS shop_name')
    ->from('order_pos_round AS pd')
    ->join('shop AS sh', 'pd.shop_id = sh.id', 'left')
    ->join('shop_pos AS pos', 'pd.pos_id = pos.id', 'left');

    if( ! empty($ds['code']) && $ds['code'] != '')
    {
      $this->db->like('pd.code', $ds['code']);
    }

    if( ! empty($ds['shop_id']) && $ds['shop_id'] != 'all')
    {
      $this->db->where('pd.shop_id', $ds['shop_id']);
    }

    if( ! empty($ds['pos_id']) && $ds['pos_id'] != 'all')
    {
      $this->db->where('pd.pos_id', $ds['pos_id']);
    }

    if( ! empty($ds['status']) && $ds['status'] != 'all')
    {
      $this->db->where('pd.status', $ds['status']);
    }

    if( ! empty($ds['open_from_date']))
    {
      $this->db->where('open_date >=', from_date($ds['open_from_date']));
    }

    if( ! empty($ds['open_to_date']))
    {
      $this->db->where('pd.open_date <=', to_date($ds['open_to_date']));
    }

    if( ! empty($ds['close_from_date']))
    {
      $this->db->where('pd.close_date >=', from_date($ds['close_from_date']));
    }

    if( ! empty($ds['close_to_date']))
    {
      $this->db->where('pd.close_date <=', to_date($ds['close_to_date']));
    }

    $this->db
    ->order_by($order_by, $sort_by)
    ->limit($perpage, $offset);

    $rs = $this->db->get();

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }



  public function get_max_code($code)
  {
    $rs = $this->db->select_max('code')->like('code', $code)->order_by('code', 'DESC')->get($this->tb);

    if($rs->num_rows() === 1)
    {
      return $rs->row()->code;
    }

    return NULL;
  }


} //--- end class
 ?>
