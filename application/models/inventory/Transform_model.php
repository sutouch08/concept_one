<?php
class Transform_model extends CI_Model
{
  public function __construct()
  {
    parent::__construct();
  }

  public function get($order_code)
  {
    $rs = $this->db->where('order_code', $order_code)->get('order_transform');

    if($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return NULL;
  }



  public function add(array $ds = array())
  {
    if( ! empty($ds))
    {
      return $this->db->insert('order_transform', $ds);
    }

    return FALSE;
  }


  public function update_so_code($code, $so_code)
  {
    return $this->db->set('so_code', $so_code)->where('order_code', $code)->update('order_transform');
  }


	public function get_sum_qty($order_code)
	{
		$rs = $this->db
		->select_sum('qty')
		->where('order_code', $order_code)
		->get('order_details');

		if($rs->num_rows() === 1)
		{
			return $rs->row()->qty;
		}

		return 0;
	}


  public function get_transform_product($id_order_detail)
  {
    $rs = $this->db->where('id_order_detail', $id_order_detail)->get('order_transform_detail');
    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return FALSE;
  }



  public function get_transform_product_by_code($order_code, $product_code)
  {
    $rs = $this->db
    ->where('order_code', $order_code)
    ->where('product_code', $product_code)
    ->get('order_transform_detail');

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return FALSE;
  }




  public function update_receive_qty($id, $qty)
  {
    return $this->db->set("receive_qty", "receive_qty + {$qty}", FALSE)->where('id', $id)->update('order_transform_detail');
  }



  public function reset_sold_qty($order_code)
  {
    return  $this->db->set('sold_qty', 0)->where('order_code', $order_code)->update('order_transform_detail');
  }



  public function hasTransformProduct($id_order_detail)
  {
    $rs = $this->db->where('id_order_detail', $id_order_detail)->get('order_transform_detail');
    if($rs->num_rows() > 0)
    {
      return TRUE;
    }

    return FALSE;

  }


  public function get_sum_transform_product_qty($id_order_detail)
  {
    $rs = $this->db
    ->select_sum('order_qty', 'qty')
    ->where('id_order_detail', $id_order_detail)
    ->get('order_transform_detail');

    return intval($rs->row()->qty);
  }



  public function is_received($order_code)
  {
    $rs = $this->db
    ->where('receive_qty >', 0)
    ->where('order_code', $order_code)
    ->limit(1)
    ->get('order_transform_detail');
    if($rs->num_rows() === 1)
    {
      return TRUE;
    }

    return FALSE;
  }

  public function is_exists($id_order_detail, $product_code)
  {
    $rs = $this->db->select('id')
    ->where('id_order_detail', $id_order_detail)
    ->where('product_code', $product_code)
    ->get('order_transform_detail');
    if($rs->num_rows() > 0)
    {
      return TRUE;
    }

    return FALSE;
  }



  public function is_complete($order_code)
  {
    $rs = $this->db
    ->where('order_code', $order_code)
    ->where('receive_qty < sold_qty')
    ->count_all_results('order_transform_detail');
    if($rs === 0)
    {
      return TRUE;
    }

    return FALSE;
  }


  public function update(array $ds = array())
  {
    if(!empty($ds))
    {
      if($this->is_exists($ds['id_order_detail'], $ds['product_code']))
      {
        return $this->update_detail($ds['id_order_detail'], $ds['product_code'], $ds['order_qty']);
      }
      else
      {
        return $this->add_detail($ds);
      }
    }

    return FALSE;
  }


  public function update_reference($code, $reference)
  {
    return $this->db->set('reference', $reference)->where('order_code', $code)->update('order_transform');
  }



  public function update_sold_qty($id, $qty)
  {
    $rs = $this->db
    ->set("sold_qty", "sold_qty + {$qty}", FALSE)
    ->set('valid', 1)
    ->where('id', $id)
    ->update('order_transform_detail');

    return $rs;
  }



  public function valid_detail($id)
  {
    return $this->db->set('valid', 1)->where('id', $id)->where('receive_qty >= sold_qty')->update('order_transform_detail');
  }


  public function unvalid_detail($id)
  {
    return $this->db->set('valid', 0)->where('id', $id)->update('order_transform_detail');
  }




  public function add_detail(array $ds = array())
  {
    return $this->db->insert('order_transform_detail', $ds);
  }


  public function update_detail($id_order_detail, $product_code, $order_qty)
  {
    $rs = $this->db
    ->set("order_qty", "order_qty + {$order_qty}", FALSE)
    ->where('id_order_detail', $id_order_detail)
    ->where('product_code', $product_code)
    ->update('order_transform_detail');

    return $rs;
  }


  public function remove_transform_product($id_order_detail, $product_code)
  {
    return $this->db
    ->where('id_order_detail', $id_order_detail)
    ->where('product_code', $product_code)
    ->delete('order_transform_detail');
  }


  public function remove_transform_detail($id_order_detail)
  {
    return $this->db->where('id_order_detail', $id_order_detail)->delete('order_transform_detail');
  }

  public function remove_transform_details(array $ds = array())
  {
    if( ! empty($ds))
    {
      //--- array of id_order_detail
      return $this->db->where_in('id_order_detail', $ds)->delete('order_transform_detail');
    }

    return FALSE;
  }



  public function clear_transform_detail($code)
  {
    return $this->db->where('order_code', $code)->delete('order_transform_detail');
  }


  public function close_transform($code)
  {
    return $this->db->set('is_closed', 1)->where('order_code', $code)->update('order_transform');
  }


  public function unclose_transform($code)
  {
    return $this->db->set('is_closed', 0)->where('order_code', $code)->update('order_transform');
  }


  public function is_closed($code)
  {
    $rs = $this->db->where('order_code', $code)->get('order_transform');

    if($rs->num_rows() === 1)
    {
      return $rs->row()->is_closed == 1 ? TRUE : FALSE;
    }
  }



  public function get_closed_transform_order($code, $limit = 50)
  {
    $sc = array();

    $this->db
    ->select('tf.order_code')
    ->from('order_transform AS tf')
    ->join('orders AS od', 'tf.order_code = od.code', 'left')
    ->where('od.state', 8)
    ->where('od.is_cancled', 0)
    ->where('od.is_expired', 0);
		// ->where('tf.is_closed', 1)
    // ->where('tf.reference IS NULL', NULL, FALSE);

    if($code !== '*')
    {
      $this->db->like('tf.order_code', $code);
    }

    $rs = $this->db->limit($limit)->get();

    if($rs->num_rows() > 0)
    {

      foreach($rs->result() as $rm)
      {
        $sc[] = $rm->order_code;
      }

    }

    return json_encode($sc);
  }


  public function get_list(array $ds = array(), $perpage = 20, $offset = 0, $role = "T")
  {
    $this->db
    ->select('od.*, ot.is_closed')
    ->from('orders AS od')
    ->join('order_transform AS ot', 'od.code = ot.order_code', 'left')
    ->where('od.role', $role);

    //---- เลขที่เอกสาร
    if( ! empty($ds['code']))
    {
      $this->db->like('od.code', $ds['code']);
    }

    //--- รหัส/ชื่อ ลูกค้า
    if( ! empty($ds['customer']))
    {
      if( ! empty($customer))
      {
        $this->db
        ->group_start()
        ->where_in('od.customer_code', $customer)
        ->or_like('od.customer_ref', $ds['customer'])
        ->group_end();
      }
      else
      {
        $this->db
        ->group_start()
        ->where('od.customer_code', 'NULL')
        ->or_like('od.customer_ref', $ds['customer'])
        ->group_end();
      }
    }

    //---- user name / display name
    if( ! empty($ds['user']))
    {
      $user = $this->user_in($ds['user']);

      if( ! empty($user))
      {
        $this->db->where_in('od.user', $user);
      }
      else
      {
        $this->db->where('od.user', 'NULL');
      }
    }

    //---- เลขที่อ้างอิงออเดอร์ภายนอก
    if( ! empty($ds['reference']))
    {
      $this->db->like('od.reference', $ds['reference']);
    }

    if( !empty($ds['user_ref']))
    {
      $this->db->like('od.user_ref', $ds['user_ref']);
    }

    if( ! empty($ds['from_date']) && ! empty($ds['to_date']))
    {
      $this->db->where('od.date_add >=', from_date($ds['from_date']));
      $this->db->where('od.date_add <=', to_date($ds['to_date']));
    }

    if(!empty($ds['warehouse']))
    {
      $this->db->where('od.warehouse_code', $ds['warehouse']);
    }

    if(!empty($ds['notSave']))
    {
      $this->db->where('od.status', 0);
    }
    else
    {
      if(isset($ds['isApprove']))
      {
        if($ds['isApprove'] !== 'all')
        {
          $this->db->where('od.status', 1);
        }
      }
    }

    if(!empty($ds['onlyMe']))
    {
      $this->db->where('od.user', $this->_user->uname);
    }

    if(!empty($ds['isExpire']))
    {
      $this->db->where('od.is_expired', 1);
    }

    if(!empty($ds['state_list']))
    {
      $this->db->where_in('od.state', $ds['state_list']);
    }

    //--- ใช้กับเอกสารที่ต้อง approve เท่านั้น
    if(isset($ds['isApprove']))
    {
      if($ds['isApprove'] !== 'all')
      {
        $this->db->where('od.is_approved', $ds['isApprove']);
      }
    }


		if(isset($ds['sap_status']) && $ds['sap_status'] !== 'all')
		{
			if($ds['sap_status'] == 0)
			{
				$this->db->where('od.is_exported',0);
			}
			else if($ds['sap_status'] == 1)
			{
				$this->db
				->group_start()
				->where('od.is_exported', 1)
				->where('od.inv_code IS NULL', NULL, FALSE)
				->group_end();
			}
			else if($ds['sap_status'] == 2)
			{
				$this->db
				->group_start()
				->where('od.is_exported', 1)
				->where('od.inv_code IS NOT NULL', NULL, FALSE)
				->group_end();
			}
			else if($ds['sap_status'] == 3)
			{
				$this->db->where('od.is_exported', 3);
			}
		}

    $this->db->order_by('od.code', 'DESC')->limit($perpage, $offset);

    $rs = $this->db->get();

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }

  public function count_rows(array $ds = array())
  {
    $this->db
    ->from('orders AS od')
    ->join('order_transform AS ot', 'od.code = ot.order_code', 'left')
    ->where('od.role', 'T');

    //---- เลขที่เอกสาร
    if( ! empty($ds['code']))
    {
      $this->db->like('od.code', $ds['code']);
    }

    //--- รหัส/ชื่อ ลูกค้า
    if( ! empty($ds['customer']))
    {
      if( ! empty($customer))
      {
        $this->db
        ->group_start()
        ->where_in('od.customer_code', $customer)
        ->or_like('od.customer_ref', $ds['customer'])
        ->group_end();
      }
      else
      {
        $this->db
        ->group_start()
        ->where('od.customer_code', 'NULL')
        ->or_like('od.customer_ref', $ds['customer'])
        ->group_end();
      }
    }

    //---- user name / display name
    if( ! empty($ds['user']))
    {
      $user = $this->user_in($ds['user']);

      if( ! empty($user))
      {
        $this->db->where_in('od.user', $user);
      }
      else
      {
        $this->db->where('od.user', 'NULL');
      }
    }

    //---- เลขที่อ้างอิงออเดอร์ภายนอก
    if( ! empty($ds['reference']))
    {
      $this->db->like('od.reference', $ds['reference']);
    }



    if( !empty($ds['user_ref']))
    {
      $this->db->like('od.user_ref', $ds['user_ref']);
    }

    if( ! empty($ds['from_date']) && ! empty($ds['to_date']))
    {
      $this->db->where('od.date_add >=', from_date($ds['from_date']));
      $this->db->where('od.date_add <=', to_date($ds['to_date']));
    }

    if(!empty($ds['warehouse']))
    {
      $this->db->where('od.warehouse_code', $ds['warehouse']);
    }

    if(!empty($ds['notSave']))
    {
      $this->db->where('od.status', 0);
    }
    else
    {
      if(isset($ds['isApprove']))
      {
        if($ds['isApprove'] !== 'all')
        {
          $this->db->where('od.status', 1);
        }
      }
    }

    if(!empty($ds['onlyMe']))
    {
      $this->db->where('od.user', $this->_user->uname);
    }

    if(!empty($ds['isExpire']))
    {
      $this->db->where('od.is_expired', 1);
    }

    if(!empty($ds['state_list']))
    {
      $this->db->where_in('od.state', $ds['state_list']);
    }

    //--- ใช้กับเอกสารที่ต้อง approve เท่านั้น
    if(isset($ds['isApprove']))
    {
      if($ds['isApprove'] !== 'all')
      {
        $this->db->where('od.is_approved', $ds['isApprove']);
      }
    }


		if(isset($ds['sap_status']) && $ds['sap_status'] !== 'all')
		{
			if($ds['sap_status'] == 0)
			{
				$this->db->where('od.is_exported',0);
			}
			else if($ds['sap_status'] == 1)
			{
				$this->db
				->group_start()
				->where('od.is_exported', 1)
				->where('od.inv_code IS NULL', NULL, FALSE)
				->group_end();
			}
			else if($ds['sap_status'] == 2)
			{
				$this->db
				->group_start()
				->where('od.is_exported', 1)
				->where('od.inv_code IS NOT NULL', NULL, FALSE)
				->group_end();
			}
			else if($ds['sap_status'] == 3)
			{
				$this->db->where('od.is_exported', 3);
			}
		}

    return $this->db->count_all_results();
  }

} //--- end class
