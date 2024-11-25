<?php
class Order_pos_model extends CI_Model
{
  private $tb = "order_pos";
  private $td = "order_pos_detail";
  private $tmp = "order_pos_temp";
  private $tmd = "order_pos_temp_detail";

  public function __construct()
  {
    parent::__construct();
  }

  public function count_rows(array $ds = array())
  {
    if( ! empty($ds['shop_id']) && $ds['shop_id'] != "" && $ds['shop_id'] != NULL)
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

    if( ! empty($ds['ref_code']))
    {
      $this->db->like('ref_code', $ds['ref_code']);
    }

    if( ! empty($ds['so_code']))
    {
      $this->db->like('so_code', $ds['so_code']);
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

    if( ! empty($ds['user']) && $ds['user'] != 'all')
    {
      $this->db->where('uname', $ds['user']);
    }

    return $this->db->count_all_results($this->tb);
  }


  public function get_list(array $ds = array(), $perpage = 20, $offset = 0)
  {
    $this->db
    ->select('o.*, p.name AS payment_name, c.name AS channels_name, sa.name AS sale_name')
    ->from('order_pos AS o')
    ->join('payment_method AS p', 'o.payment_code = p.code', 'left')
    ->join('channels AS c', 'o.channels_code = c.code', 'left')
    ->join('saleman AS sa', 'o.sale_id = sa.id', 'left');

    if( ! empty($ds['shop_id']) && $ds['shop_id'] != "" && $ds['shop_id'] != NULL)
    {
      $this->db->where('o.shop_id', $ds['shop_id']);
    }

    if( ! empty($ds['pos_id']) && $ds['pos_id'] != 'all')
    {
      $this->db->where('pos_id', $ds['pos_id']);
    }

    if( ! empty($ds['code']))
    {
      $this->db->like('o.code', $ds['code']);
    }

    if( ! empty($ds['ref_code']))
    {
      $this->db->like('o.ref_code', $ds['ref_code']);
    }

    if( ! empty($ds['so_code']))
    {
      $this->db->like('o.so_code', $ds['so_code']);
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

    if( ! empty($ds['from_date']) && ! empty($ds['to_date']))
    {
      $this->db
      ->group_start()
      ->where('o.date_add >=', from_date($ds['from_date']))
      ->where('o.date_add <=', to_date($ds['to_date']))
      ->group_end();
    }

    if( ! empty($ds['user']) && $ds['user'] != 'all')
    {
      $this->db->where('uname', $ds['user']);
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


	public function get($code)
	{
		$rs = $this->db
    ->select('o.*, p.name AS payment_name')
    ->from('order_pos AS o')
    ->join('payment_method AS p', 'o.payment_code = p.code', 'left')
    ->where('o.code', $code)
    ->get();

		if($rs->num_rows() === 1)
		{
			return $rs->row();
		}

		return NULL;
	}

  public function get_order_payment_details($code)
  {
    $rs = $this->db->where('code', $code)->get('order_pos_payment');

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function get_by_id($id)
	{
    $rs = $this->db
    ->select('o.*, p.name AS payment_name')
    ->from('order_pos AS o')
    ->join('payment_method AS p', 'o.payment_code = p.code', 'left')
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


	public function get_detail($id)
	{
		$rs = $this->db->where('id', $id)->get($this->td);
		if($rs->num_rows() === 1)
		{
			return $rs->row();
		}

		return NULL;
	}


  public function get_so_temp_row($so_line_id)
  {
    $rs = $this->db->where('line_id', $so_line_id)->get($this->tmd);

    if($rs->num_rows() > 0)
    {
      return $rs->row();
    }

    return NULL;
  }


	public function get_details($order_code)
	{
    $rs = $this->db
    ->select('od.*, pd.barcode')
    ->from('order_pos_detail AS od')
    ->join('products AS pd', 'od.product_code = pd.code', 'left')
    ->where('od.order_code', $order_code)
    ->get();

		if($rs->num_rows() > 0)
		{
			return $rs->result();
		}

		return NULL;
	}


  public function get_details_by_id($order_id)
	{
    $rs = $this->db
    ->select('od.*, pd.barcode')
    ->from('order_pos_detail AS od')
    ->join('products AS pd', 'od.product_code = pd.code', 'left')
    ->where('od.order_id', $order_id)
    ->get();

		if($rs->num_rows() > 0)
		{
			return $rs->result();
		}

		return NULL;
	}


  public function group_channels_and_sales_in_bills(array $ds = array())
  {
    if( ! empty($ds))
    {
      $rs = $this->db
      ->select('channels_code, sale_id')
      ->where_in('id', $ds)
      ->group_by('channels_code')
      ->group_by('sale_id')
      ->get('order_pos');

      if($rs->num_rows() > 0)
      {
        return $rs->result();
      }
    }

    return NULL;
  }


  public function get_details_in_bills_group_by_channels_and_sales(array $ds = array(), $channels_code, $sale_id)
  {
    if( ! empty($ds))
    {
      $rs = $this->db
      ->select('td.*, tb.so_code, tb.sale_id, tb.channels_code')
      ->from('order_pos_detail AS td')
      ->join('order_pos AS tb', 'td.order_id = tb.id', 'left')
      ->where('td.status', 'O')
      ->where('tb.status', 'O')
      ->where('tb.channels_code', $channels_code)
      ->where('tb.sale_id', $sale_id)
      ->where_in('td.order_id', $ds)
      ->get();

      if($rs->num_rows() > 0)
      {
        return $rs->result();
      }
    }


    return NULL;
  }

  public function get_details_in_bills(array $ds = array(), $channels_code, $sale_id)
  {
    if( ! empty($ds))
    {
      $rs = $this->db
      ->select('td.*, tb.so_code, tb.sale_id, tb.channels_code')
      ->from('order_pos_detail AS td')
      ->join('order_pos AS tb', 'td.order_id = tb.id', 'left')
      ->where('td.status', 'O')
      ->where('tb.status', 'O')
      ->where_in('td.order_id', $ds)
      ->get();

      if($rs->num_rows() > 0)
      {
        return $rs->result();
      }
    }


    return NULL;
  }


	public function get_order_detail_by_product($order_code, $product_code)
	{
		$rs = $this->db->where('order_code', $order_code)->where('product_code', $product_code)->get($this->td);
		if($rs->num_rows() === 1)
		{
			return $rs->row();
		}

		return NULL;
	}


  public function get_active_temp($pos_id)
  {
    $rs = $this->db
    ->select('tmp.*, pc.id AS pc_id, pc.code AS pc_code, pc.name AS pc_name')
    ->from('order_pos_temp AS tmp')
    ->join('shop_pc AS pc', 'tmp.pc_id = pc.id', 'left')
    ->where('tmp.pos_id', $pos_id)
    ->where('tmp.status', 0)
    ->get();

    if($rs->num_rows() > 0)
    {
      return $rs->row();
    }

    return NULL;
  }


  public function get_temp($temp_id)
  {
    $rs = $this->db
    ->select('tmp.*, pc.id AS pc_id, pc.code AS pc_code, pc.name AS pc_name')
    ->from('order_pos_temp AS tmp')
    ->join('shop_pc AS pc', 'tmp.pc_id = pc.id', 'left')
    ->where('tmp.id', $temp_id)
    ->get();

    if($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return NULL;
  }


  public function get_temp_details($order_temp_id)
  {
    $rs = $this->db
    ->where('order_temp_id', $order_temp_id)
    ->get($this->tmd);

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function get_temp_detail_by_item_and_price($order_temp_id, $product_code, $price, $is_free)
  {
    $rs = $this->db
    ->where('order_temp_id', $order_temp_id)
    ->where('product_code', $product_code)
    ->where('price', $price)
    ->where('is_free', $is_free)
    ->where('line_id IS NULL', NULL, FALSE)
    ->where('is_edit', 0)
    ->get($this->tmd);

    if($rs->num_rows() > 0)
    {
      return $rs->row();
    }

    return NULL;
  }


  public function get_temp_detail_by_item_price_and_discount($order_temp_id, $product_code, $price, $discount_label)
  {
    $rs = $this->db
    ->where('order_temp_id', $order_temp_id)
    ->where('product_code', $product_code)
    ->where('price', $price)
    ->where('discount_label', $discount_label)
    ->get($this->tmd);

    if($rs->num_rows() > 0)
    {
      return $rs->row();
    }

    return NULL;
  }


  public function get_temp_detail_by_id($id)
  {
    $rs = $this->db->where('id', $id)->get($this->tmd);

    if($rs->num_rows() == 1)
    {
      return $rs->row();
    }

    return NULL;
  }


	public function add(array $ds = array())
	{
		if(!empty($ds))
		{
			if($this->db->insert($this->tb, $ds))
      {
        return $this->db->insert_id();
      }
		}

		return FALSE;
	}


	public function update($code, $ds = array())
	{
		if(!empty($ds))
		{
			return $this->db->where('code', $code)->update($this->tb, $ds);
		}

		return FALSE;
	}


  public function update_by_id($id, $ds = array())
  {
    if(!empty($ds))
		{
			return $this->db->where('id', $id)->update($this->tb, $ds);
		}

		return FALSE;
  }


  //-- update return_qty call at order_pos_return -> cancel_bill
  public function update_return_qty($id, $qty)
  {
    return $this->db->set("return_qty", "return_qty + {$qty}", FALSE)->where('id', $id)->update($this->td);
  }


  //---- ใช้ที่ Order_pos controller function create_delivery
  public function update_bills(array $bills = array(), array $ds = array())
  {
    if( ! empty($bills) && ! empty($ds))
    {
      return $this->db->where_in('id', $bills)->update($this->tb, $ds);
    }

    return FALSE;
  }


  public function update_temp($temp_id, $ds = array())
  {
    if( ! empty($ds))
    {
      return $this->db->where('id', $temp_id)->update($this->tmp, $ds);
    }

    return FALSE;
  }


  public function update_temp_detail($id, array $ds = array())
  {
    if( ! empty($ds))
    {
      return $this->db->where('id', $id)->update($this->tmd, $ds);
    }

    return FALSE;
  }


	public function add_detail(array $ds = array())
	{
		if(!empty($ds))
		{
			if($this->db->insert($this->td, $ds))
			{
				return $this->db->insert_id();
			}
		}

		return FALSE;
	}


	public function update_detail($id, $ds = array())
	{
		if(!empty($ds))
		{
			return $this->db->where('id', $id)->update($this->td, $ds);
		}

		return FALSE;
	}


  public function update_details($code, $ds = array())
  {
    if( ! empty($ds))
    {
      return $this->db->where('order_code', $code)->update($this->td, $ds);
    }

    return FALSE;
  }


  public function update_details_by_id($id, $ds = array())
  {
    if( ! empty($ds))
    {
      return $this->db->where('order_id', $id)->update($this->td, $ds);
    }

    return FALSE;
  }


  //---- ใช้ที่ Order_pos controller function create_delivery
  public function update_bills_details(array $bills = array(), array $ds = array())
  {
    if( ! empty($bills) && ! empty($ds))
    {
      return $this->db->where_in('order_id', $bills)->update($this->td, $ds);
    }

    return FALSE;
  }


  public function add_temp(array $ds = array())
  {
    if( ! empty($ds))
    {
      if( $this->db->insert($this->tmp, $ds))
      {
        return $this->db->insert_id();
      }
    }

    return FALSE;
  }


  public function drop_temp($temp_id)
  {
    return $this->db->where('id', $temp_id)->delete($this->tmp);
  }


  public function delete_temp_detail($id)
  {
    return $this->db->where('id', $id)->delete($this->tmd);
  }

  public function delete_temp_details_by_id_list(array $ds = array())
  {
    if( ! empty($ds))
    {
      return $this->db->where_in('id', $ds)->delete($this->tmd);
    }

    return FALSE;
  }


  public function drop_temp_details($temp_id)
  {
    return $this->db->where('order_temp_id', $temp_id)->delete($this->tmd);
  }


  public function drop_temp_so_rows($temp_id, $so_code)
  {
    return $this->db->where('order_temp_id', $temp_id)->where('baseCode', $so_code)->delete($this->tmd);
  }


  public function add_temp_detail(array $ds = array())
  {
    if( ! empty($ds))
    {
      if($this->db->insert($this->tmd, $ds))
      {
        return $this->db->insert_id();
      }
    }

    return FALSE;
  }


  public function add_order_payment(array $ds = array())
  {
    if( ! empty($ds))
    {
      return $this->db->insert('order_pos_payment', $ds);
    }

    return FALSE;
  }


  public function count_hold_bills($pos_id)
  {
    return $this->db->where('pos_id', $pos_id)->where('status', 1)->count_all_results($this->tmp);
  }


  public function get_hold_bills($pos_id)
  {
    $rs = $this->db->where('pos_id', $pos_id)->where('status', 1)->get($this->tmp);

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
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


  public function get_reserv_stock($item_code, $warehouse_code = NULL, $zone_code = NULL)
  {
    $this->db
    ->select_sum('d.qty')
    ->from('order_pos_detail AS d')
    ->join('order_pos AS o', 'd.order_id = o.id', 'left')
    ->where('d.product_code', $item_code)
    ->where('o.status', 'O')
    ->where('d.status', 'O');

    if( ! empty($warehouse_code))
    {
      $this->db->where('o.warehouse_code', $warehouse_code);
    }

    if( ! empty($zone_code))
    {
      $this->db->where('o.zone_code', $zone_code);
    }

    $rs = $this->db->get();

    if($rs->num_rows() == 1)
    {
      return $rs->row()->qty;
    }

    return 0;
  }


  public function get_sum_role_amount_by_round_id($pos_id, $role, $round_id)
  {
    $rs = $this->db
    ->select_sum('pm.amount')
    ->from('order_pos_payment AS pm')
    ->join('order_pos AS ps', 'pm.code = ps.code', 'left')
    ->where('pm.payment_role', $role)
    ->where('ps.pos_id', $pos_id)
    ->where('ps.round_id', $round_id)
    ->where_in('ps.status', array('O', 'C'))
    ->get();

    if($rs->num_rows() === 1)
    {
      return $rs->row()->amount;
    }

    return 0;
  }


  public function get_sum_amount_by_round_id($pos_id, $round_id)
  {
    $rs = $this->db
    ->select_sum('payAmount')
    ->where('pos_id', $pos_id)
    ->where('round_id', $round_id)
    ->where_in('status', array('O', 'C'))
    ->get($this->tb);

    if($rs->num_rows() === 1)
    {
      return $rs->row()->payAmount;
    }

    return 0;
  }

} //---- end model
?>
