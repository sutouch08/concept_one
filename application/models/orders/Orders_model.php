<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Orders_model extends CI_Model
{

  public function __construct()
  {
    parent::__construct();
  }

  public function add(array $ds = array())
  {
    if( ! empty($ds))
    {
      if($this->db->insert('orders', $ds))
      {
        return $this->db->insert_id();
      }
    }

    return FALSE;
  }


  public function add_order(array $ds = array())
  {
    if( ! empty($ds))
    {
      if($this->db->insert('orders', $ds))
      {
        return $this->db->insert_id();
      }
    }

    return FALSE;
  }


  public function update($code, array $ds = array())
  {
    if(!empty($ds))
    {
      return $this->db->where('code', $code)->update('orders', $ds);
    }

    return FALSE;
  }


  public function update_billed_qty($id, $qty)
  {
    return $this->db->set("billed_qty", "billed_qty + {$qty}", FALSE)->where("id", $id)->update('order_details');
  }


  public function get($code)
  {
    $rs = $this->db->where('code', $code)->get('orders');

    if($rs->num_rows() == 1)
    {
      return $rs->row();
    }

    return FALSE;
  }


  public function get_order_by_reference($reference)
  {
    $rs = $this->db->where('reference', $reference)->order_by('id', 'DESC')->get('orders');

    if($rs->num_rows() > 0)
    {
      return $rs->row();
    }

    return NULL;
  }


  //---- for dispatch TikTok
  public function get_order_by_tracking($tracking_number)
  {
    $rs = $this->db
    ->where('shipping_code IS NOT NULL', NULL, FALSE)
    ->where('shipping_code', $tracking_number)
    ->order_by('id', 'DESC')
    ->limit(1)
    ->get('orders');

    if($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return NULL;
  }


  public function get_line_text($id)
  {
    $rs = $this->db->select('line_text')->where('id', $id)->get('order_details');

    if($rs->num_rows() === 1)
    {
      return $rs->row()->line_text;
    }

    return NULL;
  }


	public function get_cancle_reason($code)
	{
		$rs = $this->db->where('code', $code)->get('order_cancle_reason');

		if($rs->num_rows() > 0)
		{
			return $rs->result();
		}

		return NULL;
	}



  public function add_detail(array $ds = array())
  {
    if( ! empty($ds))
    {
      if($this->db->insert('order_details', $ds))
      {
        return $this->db->insert_id();
      }
    }

    return FALSE;
  }


	public function add_cancle_reason(array $ds = array())
	{
		if(!empty($ds))
		{
			return $this->db->insert('order_cancle_reason', $ds);
		}

		return FALSE;
	}



  public function update_detail($id, array $ds = array())
  {
    return $this->db->where('id', $id)->update('order_details', $ds);
  }


  public function update_details($code, array $ds = array())
  {
    return $this->db->where('order_code', $code)->update('order_details', $ds);
  }


  public function remove_detail($id)
  {
    return $this->db->where('id', $id)->delete('order_details');
  }

  public function remove_all_details($order_code)
  {
    return $this->db->where('order_code', $order_code)->delete('order_details');
  }


  public function remove_details(array $ds = array())
  {
    //--- list of ids
    if( ! empty($ds))
    {
      return $this->db->where_in('id', $ds)->delete('order_details');
    }

    return FALSE;
  }


  //--- delete all details
  public function drop_details($order_code)
  {
    return $this->db->where('order_code', $order_code)->delete('order_details');
  }


  public function remove_so_details($order_code, $so_code)
  {
    return $this->db->where('order_code', $order_code)->where('baseCode', $so_code)->delete('order_details');
  }


	public function log_delete(array $ds = array())
	{
		return $this->db->insert('order_delete_logs', $ds);
	}


  public function is_exists_detail($order_code, $item_code)
  {
    $rs = $this->db->select('id')
    ->where('order_code', $order_code)
    ->where('product_code', $item_code)
    ->get('order_details');
    if($rs->num_rows() > 0)
    {
      return TRUE;
    }

    return FALSE;
  }



  public function is_exists_order($code, $old_code = NULL)
  {
    if($old_code !== NULL)
    {
      $this->db->where('code !=', $old_code);
    }

    $rs = $this->db->where('code', $code)->get('orders');
    if($rs->num_rows() > 0)
    {
      return TRUE;
    }

    return FALSE;
  }


  public function get_detail_id($order_code, $item_code)
  {
    $rs = $this->db
    ->select('id')
    ->where('order_code', $order_code)
    ->where('product_code', $item_code)
    ->get('order_detaills');

    if($rs->num_rows() > 0)
    {
      return $rs->row()->id;
    }

    return NULL;
  }


  public function get_order_detail($order_code, $item_code)
  {
    $rs = $this->db
    ->where('order_code', $order_code)
    ->where('product_code', $item_code)
    ->get('order_details');

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function get_exists_detail($order_code, $item_code, $price)
  {
    $rs = $this->db
    ->where('order_code', $order_code)
    ->where('product_code', $item_code)
    ->where('price', $price)
    ->get('order_details');

    if($rs->num_rows() > 0)
    {
      return $rs->row();
    }

    return NULL;
  }


  public function get_unvalid_order_detail($order_code, $item_code)
  {
    $rs = $this->db
    ->where('order_code', $order_code)
    ->where('product_code', $item_code)
    ->where('valid', 0)
    ->get('order_details');

    if($rs->num_rows() > 0)
    {
      return $rs->row();
    }

    return NULL;
  }


  public function get_unvalid_qc_detail($order_code, $item_code)
  {
    $rs = $this->db
    ->where('order_code', $order_code)
    ->where('product_code', $item_code)
    ->where('valid_qc', 0)
    ->get('order_details');

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }

  //--- use in prepare to check item is exists in order or not and get sum of qty
  public function get_sum_item_qty($order_code, $item_code)
  {
    $rs = $this->db
    ->select_sum('qty')
    ->where('order_code', $order_code)
    ->where('product_code', $item_code)
    ->group_by('product_code')
    ->get('order_details');

    if($rs->num_rows() === 1)
    {
      return $rs->row()->qty;
    }

    return 0;
  }



	//--- use by wms api
	public function get_order_uncount_details($order_code)
	{
		$rs = $this->db
		->where('order_code', $order_code)
		->where('is_count', 0)
		->get('order_details');

		if($rs->num_rows() > 0)
		{
			return $rs->result();
		}

		return NULL;
	}


  public function get_detail($id)
  {
    $rs = $this->db->where('id', $id)->get('order_details');
    if($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return NULL;
  }



	public function get_only_count_stock_details($order_code)
	{
		$rs = $this->db
		->select('od.*, pd.unit_code')
		->from('order_details AS od')
		->join('products AS pd', 'od.product_code = pd.code', 'left')
		->where('od.order_code', $order_code)
		->get();

		if($rs->num_rows() > 0)
		{
			return $rs->result();
		}

		return NULL;
	}

  public function get_details($code)
  {
    $rs = $this->db->where('order_code', $code)->get('order_details');

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }

  public function get_order_details($code)
  {
    $rs = $this->db
    ->select('order_details.*, products.unit_code')
    ->from('order_details')
    ->join('products', 'order_details.product_code = products.code', 'left')
    ->join('product_size', 'products.size_code = product_size.code', 'left')
    ->where('order_code', $code)
    ->order_by('products.style_code', 'ASC')
    ->order_by('products.color_code', 'ASC')
    ->order_by('product_size.position', 'ASC')
    ->get();

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return FALSE;
  }


  public function get_so_rows($code, $so_code)
  {
    $rs = $this->db->where('order_code', $code)->where('baseCode', $so_code)->get('order_details');

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }



  public function get_unvalid_details($code)
  {
    $rs = $this->db
    ->select('ods.*, pd.old_code')
    ->from('order_details AS ods')
    ->join('products AS pd', 'ods.product_code = pd.code', 'left')
    ->join('product_size AS size', 'pd.size_code = size.code', 'left')
    ->where('ods.order_code', $code)
    ->where('ods.valid', 0)
    ->where('ods.is_count', 1)
    ->order_by('pd.color_code', 'ASC')
    ->order_by('size.position', 'ASC')
    ->get();

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return FALSE;
  }



  public function get_valid_details($code)
  {
    $rs = $this->db
    ->select('ods.*, pd.old_code')
    ->from('order_details AS ods')
    ->join('products AS pd', 'ods.product_code = pd.code', 'left')
    ->where('ods.order_code', $code)
    ->group_start()
    ->where('ods.valid', 1)
    ->or_where('ods.is_count', 0)
    ->group_end()
    ->get();

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return FALSE;
  }


  public function get_state($code)
  {
    $rs = $this->db->select('state')->where('code', $code)->get('orders');
    if($rs->num_rows() === 1)
    {
      return $rs->row()->state;
    }

    return FALSE;
  }


  public function get_order_code_by_reference($reference)
  {
    $rs = $this->db->select('code')->where('reference', $reference)->get('orders');

    if($rs->num_rows() > 0)
    {
      return $rs->row()->code;
    }

    return NULL;
  }  


  public function get_active_order_code_by_reference($reference)
  {
    $rs = $this->db->select('code')->where('reference', $reference)->where('state !=', 9)->get('orders');
    if($rs->num_rows() > 0)
    {
      return $rs->row()->code;
    }

    return FALSE;
  }


  //--- เช็คว่า reference นี้มีการเพิ่มเข้า order แล้ว และไม่ได้ยกเลิก เพื่อเพิ่มออเดอร์ใหม่โดยใช้ reference ได้ (chatbot api)
  public function is_active_order_reference($reference)
  {
    $rs = $this->db->select('code')->where('reference', $reference)->where('state !=', 9)->get('orders');

    if($rs->num_rows() > 0)
    {
      return TRUE;
    }

    return FALSE;
  }



  public function valid_detail($id)
  {
    return $this->db->set('valid', 1)->where('id', $id)->update('order_details');
  }

  public function unvalid_detail($id)
  {
    return $this->db->set('valid', 0)->where('id', $id)->update('order_details');
  }

  public function valid_all_details($code)
  {
    return $this->db->set('valid', 1)->where('order_code', $code)->update('order_details');
  }

  public function valid_qc($id)
  {
    return $this->db->set('valid_qc', 1)->where('id', $id)->update('order_details');
  }

  public function unvalid_qc($id)
  {
    return $this->db->set('valid_qc', 0)->where('id', $id)->update('order_details');
  }

  public function valid_all_qc_details($code)
  {
    return $this->db->set('valid_qc', 1)->where('order_code', $code)->update('order_details');
  }

  public function change_state($code, $state)
  {
    $arr = array(
      'state' => $state,
      'update_user' => get_cookie('uname')
    );

    return $this->db->where('code', $code)->update('orders', $arr);
  }




  public function update_shipping_code($code, $ship_code)
  {
    return $this->db->set('shipping_code', $ship_code)->where('code', $code)->update('orders');
  }




  public function set_never_expire($code, $option)
  {
    return $this->db->set('never_expire', $option)->where('code', $code)->update('orders');
  }


  public function un_expired($code)
  {
    $this->db->trans_start();
    $this->db->set('is_expired', 0)->where('code', $code)->update('orders');
    $this->db->set('is_expired', 0)->where('order_code', $code)->update('order_details');
    $this->db->trans_complete();
    if($this->db->trans_status() === FALSE)
    {
      return FALSE;
    }
    else
    {
      return TRUE;
    }

  }


  //---- เปิดบิลใน SAP เรียบร้อยแล้ว
  public function set_complete($code)
  {
    return $this->db->set('is_complete', 1)->where('order_code', $code)->update('order_details');
  }



  public function un_complete($code)
  {
    return $this->db->set('is_complete', 0)->where('order_code', $code)->update('order_details');
  }

  public function clear_inv_code($code)
  {
    return $this->db->set('inv_code', NULL)->set('is_exported', 0)->where('code', $code)->update('orders');
  }


  public function paid($code, $paid)
  {
    $paid = $paid === TRUE ? 1 : 0;
    return $this->db->set('is_paid', $paid)->where('code', $code)->update('orders');
  }


  public function count_rows(array $ds = array(), $role = 'S')
  {
    $this->db->where('role', $role);

    //---- เลขที่เอกสาร
    if( ! empty($ds['code']))
    {
      $this->db->like('code', $ds['code']);
    }


    if(!empty($ds['so_code']))
    {
      $this->db->like('so_code', $ds['so_code']);
    }

    if(!empty($ds['invoice_code']))
    {
      $this->db->like('invoice_code', $ds['invoice_code']);
    }

    //--- รหัส/ชื่อ ลูกค้า
    if( ! empty($ds['customer']))
    {
      $customer = $this->customer_in($ds['customer']);

      if( ! empty($customer))
      {
        $this->db
        ->group_start()
        ->where_in('customer_code', $customer)
        ->or_like('customer_ref', $ds['customer'])
        ->group_end();
      }
      else
      {
        $this->db
        ->group_start()
        ->where('customer_code', 'NULL')
        ->or_like('customer_ref', $ds['customer'])
        ->group_end();
      }
    }

    if( isset($ds['user']) && $ds['user'] != 'all')
    {
      $this->db->where('user', $ds['user']);
    }


    if( isset($ds['sale_code']) && $ds['sale_code'] != 'all')
    {
      $this->db->where('sale_code', $ds['sale_code']);
    }

    //---- เลขที่อ้างอิงออเดอร์ภายนอก
    if( ! empty($ds['reference']))
    {
      $this->db->like('reference', $ds['reference']);
    }

    //---เลขที่จัดส่ง
    if( ! empty($ds['ship_code']))
    {
      $this->db->like('shipping_code', $ds['ship_code']);
    }

    //--- ช่องทางการขาย
    if( isset($ds['channels']) && $ds['channels'] != 'all')
    {
      $this->db->where('channels_code', $ds['channels']);
    }

    if( isset($ds['is_term']) && $ds['is_term'] != 'all')
    {
      $this->db->where('is_term', $ds['is_term']);
    }

    if( ! empty($ds['from_date']))
    {
      $this->db->where('date_add >=', from_date($ds['from_date']));
    }

    if( ! empty($ds['to_date']))
    {
      $this->db->where('date_add <=', to_date($ds['to_date']));
    }

    if( isset($ds['warehouse']) && $ds['warehouse'] != 'all')
    {
      $this->db->where('warehouse_code', $ds['warehouse']);
    }

    if(!empty($ds['notSave']))
    {
      $this->db->where('status', 0);
    }

    if(!empty($ds['onlyMe']))
    {
      $this->db->where('user', $this->_user->uname);
    }

    if( ! empty($ds['state_list']))
    {
      $this->db->where_in('state', $ds['state_list']);
    }

    return $this->db->count_all_results('orders');
  }




  public function get_list(array $ds = array(), $perpage = 20, $offset = 0, $role = 'S')
  {
    $this->db->where('role', $role);

    //---- เลขที่เอกสาร
    if( ! empty($ds['code']))
    {
      $this->db->like('code', $ds['code']);
    }


    if(!empty($ds['so_code']))
    {
      $this->db->like('so_code', $ds['so_code']);
    }

    if(!empty($ds['invoice_code']))
    {
      $this->db->like('invoice_code', $ds['invoice_code']);
    }

    //--- รหัส/ชื่อ ลูกค้า
    if( ! empty($ds['customer']))
    {
      $customer = $this->customer_in($ds['customer']);

      if( ! empty($customer))
      {
        $this->db
        ->group_start()
        ->where_in('customer_code', $customer)
        ->or_like('customer_ref', $ds['customer'])
        ->group_end();
      }
      else
      {
        $this->db
        ->group_start()
        ->where('customer_code', 'NULL')
        ->or_like('customer_ref', $ds['customer'])
        ->group_end();
      }
    }

    if( isset($ds['user']) && $ds['user'] != 'all')
    {
      $this->db->where('user', $ds['user']);
    }


    if( isset($ds['sale_code']) && $ds['sale_code'] != 'all')
    {
      $this->db->where('sale_code', $ds['sale_code']);
    }

    //---- เลขที่อ้างอิงออเดอร์ภายนอก
    if( ! empty($ds['reference']))
    {
      $this->db->like('reference', $ds['reference']);
    }

    //---เลขที่จัดส่ง
    if( ! empty($ds['ship_code']))
    {
      $this->db->like('shipping_code', $ds['ship_code']);
    }

    //--- ช่องทางการขาย
    if( isset($ds['channels']) && $ds['channels'] != 'all')
    {
      $this->db->where('channels_code', $ds['channels']);
    }

    if( isset($ds['is_term']) && $ds['is_term'] != 'all')
    {
      $this->db->where('is_term', $ds['is_term']);
    }

    if( ! empty($ds['from_date']))
    {
      $this->db->where('date_add >=', from_date($ds['from_date']));
    }

    if( ! empty($ds['to_date']))
    {
      $this->db->where('date_add <=', to_date($ds['to_date']));
    }

    if( isset($ds['warehouse']) && $ds['warehouse'] != 'all')
    {
      $this->db->where('warehouse_code', $ds['warehouse']);
    }

    if(!empty($ds['notSave']))
    {
      $this->db->where('status', 0);
    }

    if(!empty($ds['onlyMe']))
    {
      $this->db->where('user', $this->_user->uname);
    }

    if( ! empty($ds['state_list']))
    {
      $this->db->where_in('state', $ds['state_list']);
    }

    $this->db->order_by('id', 'DESC');

    if($perpage != '')
    {
      $offset = $offset === NULL ? 0 : $offset;
      $this->db->limit($perpage, $offset);
    }

    $rs = $this->db->get('orders');

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  private function zone_in($zone)
  {
    $ds = array();

    $qr = "SELECT code FROM zone WHERE code LIKE '%{$zone}%' OR name LIKE '%{$zone}%'";
    $qs = $this->db->query($qr);

    if($qs->num_rows() > 0)
    {
      foreach($qs->result() as $rs)
      {
        $ds[] = $rs->code;
      }
    }

    return $ds;
  }


  private function customer_in($customer)
  {
    $ds = array();

    $qr = "SELECT code FROM customers WHERE code LIKE '%{$customer}%' OR name LIKE '%{$customer}%'";
    $qs = $this->db->query($qr);

    if($qs->num_rows() > 0)
    {
      foreach($qs->result() as $rs)
      {
        $ds[] = $rs->code;
      }
    }

    return $ds;
  }

  private function user_in($user)
  {
    $ds = array();

    $qr = "SELECT uname FROM user WHERE uname LIKE '%{$user}%' OR name LIKE '%{$user}%'";
    $qs = $this->db->query($qr);

    if($qs->num_rows() > 0)
    {
      foreach($qs->result() as $rs)
      {
        $ds[] = $rs->uname;
      }
    }

    return $ds;
  }

  private function getOrderStateChangeIn($state, $fromDate, $toDate, $startTime, $endTime)
  {
    $qr  = "SELECT order_code FROM order_state_change ";
    $qr .= "WHERE state = {$state} ";
    $qr .= "AND date_upd >= '{$fromDate}' ";
    $qr .= "AND date_upd <= '{$toDate}' ";
    $qr .= "AND time_upd >= '{$startTime}' ";
    $qr .= "AND time_upd <= '{$endTime}' ";

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


  public function get_un_approve_list($role = 'C', $perpage = '')
  {
    $this->db
    ->select('orders.date_add, orders.code, customers.name AS customer_name, empName')
    ->from('orders')
    ->join('customers', 'orders.customer_code = customers.code', 'left')
    ->where('orders.role', $role)
    ->where('orders.status', 1)
    ->where('orders.state <', 3)
    ->where('orders.is_expired', 0)
    ->where('orders.is_cancled', 0)
    ->where('orders.is_approved', 0)
    ->order_by('orders.date_add', 'ASC')
    ->order_by('orders.code', 'ASC');

    if($perpage != '')
    {
      $this->db->limit($perpage);
    }

    $rs = $this->db->get();
    //echo $this->db->get_compiled_select('orders');
    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return FALSE;
  }



  public function count_un_approve_rows($role = 'C')
  {
    $this->db
    ->where('role', $role)
    ->where('status', 1)
    ->where('state <', 3)
    ->where('is_expired', 0)
    ->where('is_cancled', 0)
    ->where('is_approved', 0);

    return $this->db->count_all_results('orders');
  }



  public function get_un_received_list($perpage = '', $offset = '')
  {
    $this->db
    ->select('orders.date_add, orders.code, customers.name AS customer_name')
    ->from('orders')
    ->join('customers', 'orders.customer_code = customers.code', 'left')
    ->where('orders.role', 'N')
    ->where('orders.status', 1)
    ->where('orders.state', 8)
    ->where('orders.is_expired', 0)
    ->where('orders.is_cancled', 0)
    ->where('orders.is_approved', 1)
    ->where('orders.is_valid', 0)
    ->order_by('orders.date_add', 'ASC')
    ->order_by('orders.code', 'ASC');

    if($perpage != '')
    {
      $offset = $offset === NULL ? 0 : $offset;
      $this->db->limit($perpage, $offset);
    }

    $rs = $this->db->get();
    //echo $this->db->get_compiled_select('orders');
    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return FALSE;
  }



  public function count_un_receive_rows()
  {
    $this->db
    ->where('role', 'N')
    ->where('status', 1)
    ->where('state', 8)
    ->where('is_expired', 0)
    ->where('is_cancled', 0)
    ->where('is_approved', 1)
    ->where('is_valid', 0);

    return $this->db->count_all_results('orders');
  }





  public function get_max_code($code)
  {
    $qr = "SELECT MAX(code) AS code FROM orders WHERE code LIKE '".$code."%' ORDER BY code DESC";
    $rs = $this->db->query($qr);
    return $rs->row()->code;
  }




  public function get_order_total_amount($code)
  {
    $this->db->select_sum('total_amount', 'amount');
    $this->db->where('order_code', $code);
    $rs = $this->db->get('order_details');
    return $rs->row()->amount;
  }


  public function get_bill_total_amount($code)
  {
    $rs = $this->db
    ->select_sum('total_amount', 'amount')
    ->where('reference', $code)
    ->get('order_sold');

    return $rs->row()->amount;
  }



  public function get_order_total_qty($code)
  {
    $this->db->select_sum('qty', 'qty');
    $this->db->where('order_code', $code);
    $rs = $this->db->get('order_details');
    return $rs->row()->qty;
  }

  public function get_sum_count_order_qty($code)
  {
    $rs = $this->db
    ->select_sum('qty', 'qty')
    ->where('order_code', $code)
    ->where('is_count', 1)
    ->get('order_details');

    return $rs->row()->qty;
  }

  public function get_sum_non_count_order_qty($code)
  {
    $rs = $this->db
    ->select_sum('qty', 'qty')
    ->where('order_code', $code)
    ->where('is_count', 0)
    ->get('order_details');

    return $rs->row()->qty;
  }


  //--- ใช้คำนวนยอดเครดิตคงเหลือ
  public function get_sum_not_complete_amount($customer_code)
  {
    $rs = $this->db
    ->select_sum('order_details.total_amount', 'amount')
    ->from('order_details')
    ->join('orders', 'orders.code = order_details.order_code', 'left')
    ->where_in('orders.role', array('S', 'C', 'N'))
		->where('orders.state !=', 9)
    ->where('orders.customer_code', $customer_code)
    ->where('order_details.is_complete', 0)
    ->where('orders.is_expired', 0)
		->where('order_details.is_cancle', 0)
    ->get();

    if($rs->num_rows() === 1)
    {
      return $rs->row()->amount;
    }

    return 0.00;
  }


  //---- คำนวนยอดมูลค่าคงเหลือที่่ยังไม่เข้า complete
  public function get_consign_not_complete_amount($role, $whsCode)
  {
    $qr  = "SELECT SUM(od.cost * od.qty) AS amount ";
    $qr .= "FROM order_details AS od ";
    $qr .= "LEFT JOIN orders AS o ON od.order_code = o.code ";
    $qr .= "LEFT JOIN zone AS zn ON o.zone_code = zn.code ";
    $qr .= "WHERE o.role = '{$role}' ";
    $qr .= "AND o.state != 9 ";
    $qr .= "AND o.status != 2 ";
    $qr .= "AND o.is_expired = 0 ";
    $qr .= "AND zn.warehouse_code = '{$whsCode}' ";
    $qr .= "AND od.is_complete = 0 ";
    $qr .= "AND od.is_count = 1 ";
    $qr .= "AND od.is_cancle = 0 ";
    $qr .= "AND od.is_expired = 0 ";

    $rs = $this->db->query($qr);

    if($rs->num_rows() === 1)
    {
      return $rs->row()->amount;
    }

    return 0.00;
  }



  public function get_bill_discount($code)
  {
    $rs = $this->db->select('bDiscAmount')
    ->where('code', $code)
    ->get('orders');
    if($rs->num_rows() === 1)
    {
      return $rs->row()->bDiscAmount;
    }

    return 0;
  }


  public function get_sum_style_qty($order_code, $style_code)
  {
    $rs = $this->db->select_sum('qty')
    ->where('order_code', $order_code)
    ->where('style_code', $style_code)
    ->get('order_details');

    return $rs->row()->qty;
  }




  public function get_reserv_stock($item_code, $warehouse = NULL, $zone = NULL)
  {
    $this->db
    ->select_sum('order_details.qty', 'qty')
    ->from('order_details')
    ->join('orders', 'order_details.order_code = orders.code', 'left')
    ->where('order_details.product_code', $item_code)
		->where('order_details.is_cancle', 0)
    ->where('order_details.is_complete', 0)
    ->where('order_details.is_expired', 0)
    ->where('order_details.is_count', 1);

    if($warehouse !== NULL)
    {
      $this->db->where('orders.warehouse_code', $warehouse);
    }

    if($zone !== NULL)
    {
      $this->db->where('orders.zone_code', $zone);
    }

    $rs = $this->db->get();

    if($rs->num_rows() == 1)
    {
      return $rs->row()->qty;
    }

    return 0;
  }



  public function get_reserv_stock_by_style($style_code, $warehouse = NULL)
  {
    $this->db
    ->select_sum('order_details.qty', 'qty')
    ->from('order_details')
    ->join('orders', 'order_details.order_code = orders.code', 'left')
    ->where('order_details.style_code', $style_code)
    ->where('order_details.is_cancle', 0)
    ->where('order_details.is_complete', 0)
    ->where('order_details.is_expired', 0)
    ->where('order_details.is_count', 1);
    if($warehouse !== NULL)
    {
      $this->db->where('warehouse_code', $warehouse);
    }
    $rs = $this->db->get();
    if($rs->num_rows() == 1)
    {
      return $rs->row()->qty;
    }

    return 0;
  }


  public function set_status($code, $status)
  {
    return $this->db->set('status', $status)->where('code', $code)->update('orders');
  }


  public function set_report_status($code, $status)
  {
    //--- NULL = not sent, 1 = sent, 3 = error;
    return $this->db->set('is_report', $status)->where('code', $code)->update('orders');
  }



  public function update_approver($code, $user)
  {
    return $this->db
    ->set('approver', $user)
    ->set('approve_date', now())
    ->set('is_approved', 1)
    ->where('code', $code)
    ->update('orders');
  }



  public function un_approver($code, $user)
  {
    return $this->db
    ->set('approver', NULL)
    ->set('approve_date', now())
    ->set('is_approved', 0)
    ->where('code', $code)
    ->update('orders');
  }

  //---- ระบุที่อยู่จัดส่งในออเดอร์นั้นๆ
  public function set_address_id($code, $id_address)
  {
    return $this->db->set('id_address', $id_address)->where('code', $code)->update('orders');
  }



  public function clear_order_detail($code)
  {
    return $this->db->where('order_code', $code)->delete('order_details');
  }



	public function cancle_order_detail($code)
	{
		return $this->db->set('is_cancle', 1)->where('order_code', $code)->update('order_details');
	}



  //--- Set is_valid = 1 when transfer draft is confirmed (use in Controller inventory/transfer->confirm_receipted)
  public function valid_transfer_draft($code)
  {
    return $this->db->set('is_valid', 1)->where('code', $code)->update('orders');
  }


  public function get_order_non_inv_code($limit = 100)
  {
    $rs = $this->db
    ->select('code')
    ->where_in('role', 'S')
    ->where('state', 8)
    ->where('status', 1)
    ->where('is_cancled', 0)
    ->where('is_expired', 0)
    ->where('inv_code IS NULL', NULL, FALSE)
    ->limit($limit)
    ->get('orders');

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function get_sponsor_non_inv_code($limit = 100)
  {
    $rs = $this->db
    ->select('code')
    ->where_in('role', array('P', 'U'))
    ->where('state', 8)
    ->where('status', 1)
    ->where('is_cancled', 0)
    ->where('is_expired', 0)
    ->where('inv_code IS NULL', NULL, FALSE)
    ->limit($limit)
    ->get('orders');

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }




  public function get_consignment_non_inv_code($limit = 100)
  {
    $rs = $this->db
    ->select('code')
    ->where('role', 'C')
    ->where('state', 8)
    ->where('status', 1)
    ->where('is_cancled', 0)
    ->where('is_expired', 0)
    ->where('inv_code IS NULL', NULL, FALSE)
    ->limit($limit)
    ->get('orders');

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }

  //--- WT
  public function get_order_transfer_non_inv_code($limit = 100)
  {
    $rs = $this->db
    ->select('code')
    ->where('role', 'N')
    ->where('state', 8)
    ->where('status', 1)
    ->where('is_cancled', 0)
    ->where('is_expired', 0)
    ->where('is_valid', 1)
    ->where('inv_code IS NULL', NULL, FALSE)
    ->limit($limit)
    ->get('orders');

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  //---- WL
  public function get_order_lend_non_inv_code($limit = 100)
  {
    $rs = $this->db
    ->select('code')
    ->where('role', 'L')
    ->where('state', 8)
    ->where('status', 1)
    ->where('is_cancled', 0)
    ->where('is_expired', 0)
    ->where('inv_code IS NULL', NULL, FALSE)
    ->limit($limit)
    ->get('orders');

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  //--- WQ, WV
  public function get_order_transform_non_inv_code($limit = 100)
  {
    $rs = $this->db
    ->select('code')
    ->where_in('role', array('Q','T'))
    ->where('state', 8)
    ->where('status', 1)
    ->where('is_cancled', 0)
    ->where('is_expired', 0)
    ->where('inv_code IS NULL', NULL, FALSE)
    ->limit($limit)
    ->get('orders');

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }



  public function get_sap_doc_num($code)
  {
    $rs = $this->ms
    ->select('DocNum')
    ->where('U_ECOMNO', $code)
    ->where('CANCELED', 'N')
    ->get('ODLN');

    if($rs->num_rows() > 0)
    {
      return $rs->row()->DocNum;
    }

    return NULL;
  }


  public function update_inv($code, $doc_num)
  {
    return $this->db->set('inv_code', $doc_num)->where('code', $code)->update('orders');
  }


  public function set_exported($code, $status, $error)
  {
    return $this->db->set('is_exported', $status)->set('export_error', $error)->where('code', $code)->update('orders');
  }


  public function get_expire_list($date, array $role = array('S'))
  {
    $rs = $this->db
    ->select('code')
    ->where('date_add <', $date)
    ->where_in('role', $role)
    ->where_in('state', array(1,2))
    ->where('is_paid', 0)
    ->where('never_expire', 0)
    ->get('orders');

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }

  public function set_expire_order($code)
  {
    if(!empty($code))
    {
      return $this->db
      ->set('is_expired', 1)
      ->where('code', $code)
      ->update('orders');
    }

    return FALSE;
  }

  public function set_expire_order_details($code)
  {
    if(!empty($code))
    {
      return $this->db
      ->set('is_expired', 1)
      ->where('order_code', $code)
      ->update('order_details');
    }

    return FALSE;
  }
} //--- End class


 ?>
