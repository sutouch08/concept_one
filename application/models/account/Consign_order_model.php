<?php
class Consign_order_model extends CI_Model
{

  public function __construct()
  {
    parent::__construct();
  }


  public function add($ds = array())
  {
    if(!empty($ds))
    {
      return $this->db->insert('consign_order', $ds);
    }

    return FALSE;
  }


  public function add_detail($ds = array())
  {
    if(!empty($ds))
    {
      if($this->db->insert('consign_order_detail', $ds))
      {
        return $this->db->insert_id();
      }
    }

    return FALSE;
  }


  public function update($code, $ds = array())
  {
    if(! empty($ds))
    {
      return $this->db->where('code', $code)->update('consign_order', $ds);
    }

    return FALSE;
  }


  public function update_detail($id, $ds = array())
  {
    if(! empty($ds))
    {
      return $this->db->where('id', $id)->update('consign_order_detail', $ds);
    }

    return FALSE;
  }



  public function update_ref_code($code, $check_code)
  {
    return $this->db->set('ref_code', $check_code)->where('code', $code)->update('consign_order');
  }



  public function drop_import_details($code, $check_code)
  {
    return $this->db->where('consign_code', $code)->where('ref_code', $check_code)->delete('consign_order_detail');
  }




  public function has_saved_imported($code, $check_code)
  {
    $rs = $this->db
    ->where('consign_code', $code)
    ->where('ref_code', $check_code)
    ->where('status', 1)
    ->limit(1)
    ->get('consign_order_detail');

    if($rs->num_rows() > 0)
    {
      return TRUE;
    }

    return FALSE;
  }



  public function get($code)
  {
    $rs = $this->db->where('code', $code)->get('consign_order');
    if($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return FALSE;
  }



  public function get_details($code)
  {
    $this->db
    ->select('consign_order_detail.*')
    ->from('consign_order_detail')
    ->join('products', 'consign_order_detail.product_code = products.code', 'left')
    ->join('product_size', 'products.size_code = product_size.code', 'left')
    ->where('consign_code', $code)
    ->order_by('products.style_code', 'ASC')
    ->order_by('products.color_code', 'ASC')
    ->order_by('product_size.position', 'ASC');
    $rs = $this->db->get();

    if($rs->num_rows() >0)
    {
      return $rs->result();
    }

    return FALSE;
  }


  public function get_detail($id)
  {
    $rs = $this->db->where('id', $id)->get('consign_order_detail');
    if($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return FALSE;
  }



  public function get_exists_detail($code, $product_code, $price, $discountLabel, $input_type)
  {
    $rs = $this->db
    ->where('consign_code', $code)
    ->where('product_code', $product_code)
    ->where('price', $price)
    ->where('discount', $discountLabel)
    ->where('input_type', $input_type)
    ->where('status', 0)
    ->get('consign_order_detail');
    if($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return FALSE;
  }



  public function delete_detail($id)
  {
    return $this->db->where('id', $id)->delete('consign_order_detail');
  }


  public function drop_details($code)
  {
    return $this->db->where('consign_code', $code)->delete('consign_order_detail');
  }


  public function cancle_details($code)
  {
    return $this->db->set('status', 2)->where('consign_code', $code)->update('consign_order_detail');
  }

  public function get_sum_amount($code)
  {
    $rs = $this->db->select_sum('amount')->where('consign_code', $code)->get('consign_order_detail');

    return $rs->row()->amount === NULL ? 0 : $rs->row()->amount;
  }



  public function get_sum_order_qty($code, $product_code)
  {
    $rs = $this->db
    ->select_sum('qty')
    ->where('consign_code', $code)
    ->where('product_code', $product_code)
    ->get('consign_order_detail');

    if($rs->num_rows() === 1)
    {
      return is_null($rs->row()->qty) ? 0 : $rs->row()->qty;
    }

    return 0;
  }



  public function get_item_gp($product_code, $zone_code)
  {
    $rs = $this->db
    ->select('order_sold.discount_label')
    ->from('order_sold')
    ->join('orders', 'order_sold.reference = orders.code', 'left')
    ->where_in('order_sold.role', array('C', 'N'))
    ->where('orders.zone_code', $zone_code)
    ->where('order_sold.product_code', $product_code)
    ->order_by('orders.date_add', 'DESC')
    ->limit(1)
    ->get();

    if($rs->num_rows() > 0)
    {
      return $rs->row()->discount_label;
    }

    return 0;
  }



  public function get_unsave_qty($code, $product_code, $price, $discount, $input_type)
  {
    $rs = $this->db
    ->select_sum('qty')
    ->where('consign_code', $code)
    ->where('product_code', $product_code)
    ->where('price', $price, FALSE)
    ->where('discount', $discount)
    ->where('status', 0)
    ->get('consign_order_detail');

    return $rs->row()->qty === NULL ? 0 : $rs->row()->qty;
  }



  public function change_detail_status($id, $status)
  {
    $this->db
    ->set('status', $status)
    ->where('id', $id);
    return $this->db->update('consign_order_detail');
  }

  public function change_all_detail_status($code, $status)
  {
    $this->db
    ->set('status', $status)
    ->where('consign_code', $code);
    return $this->db->update('consign_order_detail');
  }


  public function change_status($code, $status)
  {
    $this->db
    ->set('status', $status)
    ->set('inv_code', NULL)
    ->set('update_user', get_cookie('uname'))
    ->where('code', $code);
    return $this->db->update('consign_order');
  }


  public function get_list(array $ds = array(), $perpage = 20, $offset = 0)
  {
    $this->db
    ->select('od.*')
    ->select('ch.name AS channels_name, sa.name AS sale_name, wh.name AS warehouse_name')
    ->from('consign_order AS od')
    ->join('channels AS ch', 'od.channels_code = ch.code', 'left')
    ->join('warehouse AS wh', 'od.warehouse_code = wh.code', 'left')
    ->join('saleman AS sa', 'od.sale_code = sa.id', 'left');

    //--- status
    if($ds['status'] !== 'all')
    {
      $this->db->where('od.status', $ds['status']);
    }

    //--- document date
    if( ! empty($ds['from_date']))
    {
      $this->db->where('od.date_add >=', from_date($ds['from_date']));
    }

    if( ! empty($ds['to_date']))
    {
      $this->db->where('od.date_add <=', to_date($ds['to_date']));
    }


    if(!empty($ds['code']))
    {
      $this->db->like('od.code', $ds['code']);
    }

    //--- อ้างอิงเลขที่กระทบยอดสินค้า
    if(!empty($ds['ref_code']))
    {
      $this->db->like('od.ref_code', $ds['ref_code']);
    }

    if( ! empty($ds['so_code']))
    {
      $code_in = $this->so_in($ds['so_code']);
      $this->db->where_in('od.code', $code_in);
    }


    if( ! empty($ds['customer']))
    {
      $this->db
      ->group_start()
      ->like('od.customer_code', $ds['customer'])
      ->or_like('od.customer_name', $ds['customer'])
      ->group_end();
    }

    if(!empty($ds['zone']))
    {
      $this->db
      ->group_start()
      ->like('od.zone_code', $ds['zone'])
      ->or_like('od.zone_name', $ds['zone'])
      ->group_end();
    }

    if( isset($ds['warehouse']) && $ds['warehouse'] != 'all')
    {
      $this->db->where('od.warehouse_code', $ds['warehouse']);
    }

    if( isset($ds['sale_id']) && $ds['sale_id'] != 'all')
    {
      $this->db->where('od.sale_code', $ds['sale_id']);
    }

    if( isset($ds['channels']) && $ds['channels'] != 'all')
    {
      $this->db->where('od.channels_code', $ds['channels']);
    }

    $this->db->order_by('od.code', 'DESC')->limit($perpage, $offset);

    $rs = $this->db->get();

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return FALSE;
  }




  public function count_rows(array $ds = array())
  {
    //--- status
    if($ds['status'] !== 'all')
    {
      $this->db->where('status', $ds['status']);
    }

    //--- document date
    if( ! empty($ds['from_date']))
    {
      $this->db->where('date_add >=', from_date($ds['from_date']));
    }

    if( ! empty($ds['to_date']))
    {
      $this->db->where('date_add <=', to_date($ds['to_date']));
    }


    if( ! empty($ds['code']))
    {
      $this->db->like('code', $ds['code']);
    }

    //--- อ้างอิงเลขที่กระทบยอดสินค้า
    if(!empty($ds['ref_code']))
    {
      $this->db->like('ref_code', $ds['ref_code']);
    }

    if( ! empty($ds['so_code']))
    {
      $code_in = $this->so_in($ds['so_code']);
      $this->db->where_in('code', $code_in);
    }

    if(!empty($ds['customer']))
    {
      $this->db
      ->group_start()
      ->like('customer_code', $ds['customer'])
      ->or_like('customer_name', $ds['customer'])
      ->group_end();
    }

    if(!empty($ds['zone']))
    {
      $this->db
      ->group_start()
      ->like('zone_code', $ds['zone'])
      ->or_like('zone_name', $ds['zone'])
      ->group_end();
    }

    if( isset($ds['warehouse']) && $ds['warehouse'] != 'all')
    {
      $this->db->where('warehouse_code', $ds['warehouse']);
    }

    if( isset($ds['sale_id']) && $ds['sale_id'] != 'all')
    {
      $this->db->where('sale_code', $ds['sale_id']);
    }

    if( isset($ds['channels']) && $ds['channels'] != 'all')
    {
      $this->db->where('channels_code', $ds['channels']);
    }

    return $this->db->count_all_results('consign_order');
  }

  //--- ต้องการเลขที่ WM จากเลขที่ SO
  public function so_in($so_code)
  {
    $ds = ["notfound"];

    $qr = "SELECT consign_code FROM consign_order_detail WHERE so_code IS NOT NULL AND so_code LIKE '%{$so_code}%'";
    $qs = $this->db->query($qr);

    if($qs->num_rows() > 0)
    {
      foreach($qs->result() as $rs)
      {
        $ds[] = $rs->consign_code;
      }
    }

    return $ds;
  }

  public function get_max_code($code)
  {
    $qr = "SELECT MAX(code) AS code FROM consign_order WHERE code LIKE '".$code."%' ORDER BY code DESC";
    $rs = $this->db->query($qr);
    return $rs->row()->code;
  }


  public function is_exists($code, $old_code = NULL)
  {
    if($old_code !== NULL)
    {
      $this->db->where('code !=', $old_code);
    }

    $rs = $this->db->where('code', $code)->get('consign_order');
    if($rs->num_rows() > 0)
    {
      return TRUE;
    }

    return FALSE;
  }



	public function get_non_inv_code($limit = 100)
	{
		$rs = $this->db
    ->select('code')
    ->where('status', 1)
    ->where('inv_code IS NULL', NULL, FALSE)
    ->limit($limit)
    ->get('consign_order');

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
    return $this->db->set('inv_code', $doc_num)->where('code', $code)->update('consign_order');
  }


  public function get_reserv_stock($item_code, $warehouse_code = NULL, $zone_code = NULL)
  {
    $this->db
    ->select_sum('d.qty')
    ->from('consign_order_detail AS d')
    ->join('consign_order AS o', 'd.consign_code = o.code', 'left')
    ->where('d.input_type', 4)
    ->where('o.ref_type', 4)
    ->where('o.status !=', 2)
    ->where('o.inv_code IS NULL', NULL, FALSE)
    ->where('d.product_code', $item_code);

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

} //--- end class
?>
