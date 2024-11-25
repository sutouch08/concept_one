<?php
class Order_pos_return_model extends CI_Model
{
  private $tb = "order_pos_return";
  private $td = "order_pos_return_detail";

  public function __construct()
  {
    parent::__construct();
  }

  public function get($code)
  {
    $rs = $this->db
    ->select('o.*, z.name AS zone_name')
    ->from('order_pos_return AS o')
    ->join('zone AS z', 'o.zone_code = z.code', 'left')
    ->where('o.code', $code)
    ->get();

    if($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return NULL;
  }

  public function get_by_id($id)
  {
    $rs = $this->db
    ->select('o.*, z.name AS zone_name')
    ->from('order_pos_return AS o')
    ->join('zone AS z', 'o.zone_code = z.code', 'left')
    ->where('o.id', $id)
    ->get();

    if($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return NULL;
  }


  //--- ดึงบิลทั้งหมดที่ถูกตัดยอดไปตามเอกสารตัดยอดที่ระบุ (ใช้สำหรับ rollback สถานะบิล เมื่อยกเลิก WM)
  public function get_bills_by_ref_code($ref_code)
  {
    $rs = $this->db->select('id, code')->where('ref_code', $ref_code)->get($this->tb);

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function get_details($code)
  {
    $rs = $this->db->where('return_code', $code)->get($this->td);

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function get_details_by_id($return_id)
  {
    $rs = $this->db->where('return_id', $return_id)->get($this->td);

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function get_details_in_bills(array $ds = array())
  {
    if( ! empty($ds))
    {
      $rs = $this->db
      ->select('td.*')
      ->from('order_pos_return_detail AS td')
      ->join('order_pos_return AS tb', 'td.return_id = tb.id', 'left')
      ->where('td.status', 'O')
      ->where('tb.status', 'O')
      ->where_in('td.return_id', $ds)
      ->get();

      if($rs->num_rows() > 0)
      {
        return $rs->result();
      }
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


  public function update($id, array $ds = array())
  {
    if( ! empty($ds))
    {
      return $this->db->where('id', $id)->update($this->tb, $ds);
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


  public function update_details($id, array $ds = array())
  {
    if( ! empty($ds))
    {
      return $this->db->where('return_id', $id)->update($this->td, $ds);
    }

    return FALSE;
  }


  //---- ใช้ที่ Order_pos_return controller function create_cn
  public function update_bills(array $bills = array(), array $ds = array())
  {
    if( ! empty($bills) && ! empty($ds))
    {
      return $this->db->where_in('id', $bills)->update($this->tb, $ds);
    }

    return FALSE;
  }


  //---- ใช้ที่ Order_pos_return controller function create_cn
  public function update_bills_details(array $bills = array(), array $ds = array())
  {
    if( ! empty($bills) && ! empty($ds))
    {
      return $this->db->where_in('return_id', $bills)->update($this->td, $ds);
    }

    return FALSE;
  }


  public function count_rows($ds = array())
  {
    if( ! empty($ds['shop_id']) && $ds['shop_id'] != 'all')
    {
      $this->db->where('shop_id', $ds['shop_id']);
    }

    if( ! empty($ds['pos_id']) && $ds['pos_id'] != 'all')
    {
      $this->db->where('pos_id', $ds['pos_id']);
    }

    if( ! empty($ds['code']))
    {
      $this->db->like('code', $ds['code']);
    }

    if( ! empty($ds['order_code']))
    {
      $this->db->like('order_code', $ds['order_code']);
    }

    if( ! empty($ds['ref_code']))
    {
      $this->db->like('ref_code', $ds['ref_code']);
    }

    if( ! empty($ds['status']) && $ds['status'] != 'all')
    {
      $this->db->where('status', $ds['status']);
    }

    if( ! empty($ds['from_date']) && ! empty($ds['to_date']))
    {
      $this->db
      ->group_start()
      ->where('date_add >=', from_date($ds['from_date']))
      ->where('date_add <=', to_date($ds['to_date']))
      ->group_end();
    }

    return $this->db->count_all_results($this->tb);
  }

  public function get_list(array $ds = array(), $perpage = 20, $offset = 0)
  {
    if( ! empty($ds['shop_id']) && $ds['shop_id'] != 'all')
    {
      $this->db->where('shop_id', $ds['shop_id']);
    }

    if( ! empty($ds['pos_id']) && $ds['pos_id'] != 'all')
    {
      $this->db->where('pos_id', $ds['pos_id']);
    }

    if( ! empty($ds['code']))
    {
      $this->db->like('code', $ds['code']);
    }

    if( ! empty($ds['order_code']))
    {
      $this->db->like('order_code', $ds['order_code']);
    }

    if( ! empty($ds['ref_code']))
    {
      $this->db->like('ref_code', $ds['ref_code']);
    }

    if( ! empty($ds['status']) && $ds['status'] != 'all')
    {
      $this->db->where('status', $ds['status']);
    }

    if( ! empty($ds['from_date']) && ! empty($ds['to_date']))
    {
      $this->db
      ->group_start()
      ->where('date_add >=', from_date($ds['from_date']))
      ->where('date_add <=', to_date($ds['to_date']))
      ->group_end();
    }

    $rs = $this->db
    ->order_by('code', 'DESC')
    ->limit($perpage, $offset)
    ->get($this->tb);

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }

  public function get_sum_role_amount_by_round_id($pos_id, $role, $round_id)
  {
    $rs = $this->db
    ->select_sum('amount')
    ->where('payment_role', $role)
    ->where('pos_id', $pos_id)
    ->where('round_id', $round_id)
    ->where_in('status', ['O', 'C'])
    ->get($this->tb);    

    if($rs->num_rows() === 1)
    {
      return $rs->row()->amount;
    }

    return 0;
  }


  public function get_sum_amount_by_round_id($pos_id, $round_id)
  {
    $rs = $this->db
    ->select_sum('amount')
    ->where('pos_id', $pos_id)
    ->where('round_id', $round_id)
    ->where_in('status', array('O', 'C'))
    ->get($this->tb);

    if($rs->num_rows() === 1)
    {
      return $rs->row()->amount;
    }

    return 0.00;
  }


  public function get_max_code($pre)
  {
    $rs = $this->db
    ->select_max('code')
    ->like('code', $pre, 'after')
    ->order_by('code', 'DESC')
    ->get($this->tb);

		if($rs->num_rows() == 1)
		{
			return $rs->row()->code;
		}

		return  NULL;
  }
}
 ?>
