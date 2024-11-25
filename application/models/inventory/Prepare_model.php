<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Prepare_model extends CI_Model
{
  public function __construct()
  {
    parent::__construct();
  }


  public function get_details($order_code)
  {
    $rs = $this->db->where('order_code', $order_code)->get('prepare');
    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }




  public function get_warehouse_code($zone_code)
  {
    $rs = $this->ms->select('WhsCode')->where('BinCode', $zone_code)->get('OBIN');
    if($rs->num_rows() === 1)
    {
      return $rs->row()->WhsCode;
    }

    return  NULL;
  }

  public function update_buffer($order_code, $product_code, $zone_code, $qty, $detail_id = NULL)
  {
    if( ! $this->is_exists_buffer($order_code, $product_code, $zone_code, $detail_id))
    {
      $arr = array(
        'order_code' => $order_code,
        'product_code' => $product_code,
        'warehouse_code' => $this->get_warehouse_code($zone_code),
        'zone_code' => $zone_code,
        'qty' => $qty,
        'order_detail_id' => $detail_id,
        'user' => get_cookie('uname')
      );

      return $this->db->insert('buffer', $arr);
    }
    else
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

    return FALSE;
  }


  public function is_exists_buffer($order_code, $item_code, $zone_code, $detail_id = NULL)
  {
    $this->db
    ->where('order_code', $order_code)
    ->where('product_code', $item_code)
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


	public function add(array $ds = array())
	{
		return $this->db->insert('prepare', $ds);
	}



	public function drop_prepare($order_code)
	{
		return $this->db->where('order_code', $order_code)->delete('prepare');
	}


  public function update_prepare($order_code, $product_code, $zone_code, $qty, $detail_id = NULL)
  {
    if( ! $this->is_exists_prepare($order_code, $product_code, $zone_code, $detail_id))
    {
      $arr = array(
        'order_code' => $order_code,
        'product_code' => $product_code,
        'zone_code' => $zone_code,
        'qty' => $qty,
        'order_detail_id' => $detail_id,
        'user' => $this->_user->uname
      );

      return $this->db->insert('prepare', $arr);
    }
    else
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

      return $this->db->update('prepare');
    }

    return FALSE;
  }



  public function is_exists_prepare($order_code, $item_code, $zone_code, $detail_id = NULL)
  {
    $this->db
    ->where('order_code', $order_code)
    ->where('product_code', $item_code)
    ->where('zone_code', $zone_code)->group_start()
    ->where('order_detail_id', $detail_id)
    ->or_where('order_detail_id IS NULL', NULL, FALSE)
    ->group_end();

    if($this->db->count_all_results('prepare') > 0)
    {
      return TRUE;
    }

    return FALSE;
  }


  public function get_sum_order_qty($order_code)
  {
    $rs = $this->db
    ->select_sum('qty')
    ->where('order_code', $order_code)
    ->where('is_count', 1)
    ->group_by('order_code')
    ->get('order_details');

    if($rs->num_rows() === 1)
    {
      return $rs->row()->qty;
    }

    return 0;
  }


  public function get_prepared($order_code, $product_code, $detail_id = NULL)
  {
    $this->db
    ->select_sum('qty')
    ->where('order_code', $order_code)
    ->where('product_code', $product_code)
    ->group_start()
    ->where('order_detail_id', $detail_id)
    ->or_where('order_detail_id IS NULL', NULL, FALSE)
    ->group_end();

    $rs = $this->db->get('buffer');

    if($rs->num_rows() > 0)
    {
      return $rs->row()->qty;
    }

    return 0;
  }


  public function remove_prepare($order_code, $item_code, $order_detail_id = NULL)
  {
    $this->db
    ->where('order_code', $order_code)
    ->where('product_code', $item_code)
    ->group_start()
    ->where('order_detail_id', $order_detail_id)
    ->or_where('order_detail_id IS NULL', NULL, FALSE)
    ->group_end();

    return $this->db->delete('prepare');
  }

  public function get_total_prepared($order_code)
  {
    $rs = $this->db
    ->select_sum('qty')
    ->where('order_code', $order_code)
    ->get('buffer');

    return is_null($rs->row()->qty) ? 0 : $rs->row()->qty;
  }


  //---- แสดงสินค้าว่าจัดมาจากโซนไหนบ้าง
  public function get_prepared_from_zone($order_code, $item_code, $detail_id = NULL)
  {
    $this->db
    ->select('buffer.*, zone.name')
    ->from('buffer')
    ->join('zone', 'zone.code = buffer.zone_code')
    ->where('order_code', $order_code)
    ->where('product_code', $item_code)
    ->group_start()
    ->where('order_detail_id', $detail_id)
    ->or_where('order_detail_id IS NULL', NULL, FALSE)
    ->group_end();

    $rs = $this->db->get();

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return FALSE;
  }


  //--- แสดงยอดรวมสินค้าที่ถูกจัดไปแล้วจากโซนนี้
  public function get_prepared_zone($zone_code, $item_code)
  {
    $rs = $this->db->select_sum('qty')
    ->where('zone_code', $zone_code)
    ->where('product_code', $item_code)
    ->get('buffer');

    return $rs->row()->qty;
  }





  public function get_buffer_zone($item_code, $zone_code)
  {
    $rs = $this->db->select_sum('qty')
    ->where('product_code', $item_code)
    ->where('zone_code', $zone_code)
    ->get('buffer');

    return $rs->row()->qty;
  }


  public function count_rows(array $ds = array(), $state = 3)
  {
    $this->db
    ->from('orders AS o')
    ->join('channels AS ch', 'ch.code = o.channels_code','left')
    ->join('customers AS c', 'c.code = o.customer_code', 'left')
    ->where('o.state', $state)
    ->where('o.status', 1);

    if( ! empty($ds['code']))
    {
      $this->db
			->group_start()
			->like('o.code', $ds['code'])
			->or_like('o.reference', $ds['code'])
			->group_end();
    }

    if( ! empty($ds['so_code']))
    {
      $this->db->like('o.so_code', $ds['so_code']);
    }


    if(!empty($ds['customer']))
    {
      $this->db->group_start();
      $this->db->like('c.name', $ds['customer']);
      $this->db->or_like('o.customer_ref', $ds['customer']);
      $this->db->group_end();
    }


    if( ! empty($ds['warehouse']) && $ds['warehouse'] !== 'all')
    {
      $this->db->where('o.warehouse_code', $ds['warehouse']);
    }

    //---- user name / display name
    if( ! empty($ds['user']) && $ds['user'] != 'all')
    {
      $this->db->where('o.user', $ds['user']);
    }

    if(! empty($ds['update_user']) && $ds['update_user'])
    {
      $this->db->where('o.update_user', $ds['update_user']);
    }


    if( ! empty($ds['channels']))
    {
      $this->db->where('o.channels_code', $ds['channels']);
    }

    if($ds['role'] != 'all')
    {
      $this->db->where('o.role', $ds['role']);
    }


    if( ! empty($ds['from_date']))
    {
      $this->db->where('o.date_add >=', from_date($ds['from_date']));
    }

    if( ! empty($ds['to_date']))
    {
      $this->db->where('o.date_add <=', to_date($ds['to_date']));
    }

    return $this->db->count_all_results();
  }



  public function get_data(array $ds = array(), $perpage = 20, $offset = 0, $state = 3)
  {
    $this->db
		->select('o.*, ch.name AS channels_name')
    ->select('c.name AS customer_name, u.name AS user_name, up.name AS upd_name')
    ->from('orders AS o')
    ->join('channels AS ch', 'ch.code = o.channels_code','left')
    ->join('customers AS c', 'c.code = o.customer_code', 'left')
    ->join('user AS u', 'o.user = u.uname', 'left')
    ->join('user AS up', 'o.update_user = up.uname', 'left')
    ->where('o.state', $state)
    ->where('o.status', 1);

    if( ! empty($ds['code']))
    {
      $this->db
			->group_start()
			->like('o.code', $ds['code'])
			->or_like('o.reference', $ds['code'])
			->group_end();
    }

    if( ! empty($ds['so_code']))
    {
      $this->db->like('o.so_code', $ds['so_code']);
    }


    if(!empty($ds['customer']))
    {
      $this->db->group_start();
      $this->db->like('c.name', $ds['customer']);
      $this->db->or_like('o.customer_ref', $ds['customer']);
      $this->db->group_end();
    }


    if( ! empty($ds['warehouse']) && $ds['warehouse'] != 'all')
    {
      $this->db->where('o.warehouse_code', $ds['warehouse']);
    }

    //---- user name / display name
    if( ! empty($ds['user']) && $ds['user'] != 'all')
    {
      $this->db->where('o.user', $ds['user']);
    }

    if( ! empty($ds['update_user']) && $ds['update_user'])
    {
      $this->db->where('o.update_user', $ds['update_user']);
    }


    if( ! empty($ds['channels']))
    {
      $this->db->where('o.channels_code', $ds['channels']);
    }

    if($ds['role'] != 'all')
    {
      $this->db->where('o.role', $ds['role']);
    }


    if( ! empty($ds['from_date']))
    {
      $this->db->where('o.date_add >=', from_date($ds['from_date']));
    }

    if( ! empty($ds['to_date']))
    {
      $this->db->where('o.date_add <=', to_date($ds['to_date']));
    }

    $this->db->order_by('o.date_upd', 'DESC')->limit($perpage, $offset);

    $rs = $this->db->get();

    if( $rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  private function getOrderStateChangeIn($state, $fromDate, $toDate, $startTime, $endTime)
  {
    $qr  = "SELECT order_code FROM order_state_change ";
    $qr .= "WHERE state = {$state} ";
    $qr .= "AND date_upd >= '{$fromDate}' ";
    $qr .= "AND date_upd <= '{$toDate}' ";
    $qr .= "AND time_upd >= '{$startTime}' ";
    $qr .= "AND time_upd <= '{$endTime}' ";
    $qr .= "LIMIT 1000";
    $rs = $this->db->query($qr);

  	$sc = array();

  	if($rs->num_rows() > 0)
  	{
  		foreach($rs->result() as $row)
  		{
  			$sc[] = $row->order_code;
  		}

      return $sc;
  	}

  	return 'xx';
  }


  public function clear_prepare($code)
  {
    return $this->db->where('order_code', $code)->delete('prepare');
  }



} //--- end class


 ?>
