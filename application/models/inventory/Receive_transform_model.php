<?php
class Receive_transform_model extends CI_Model
{
  public function __construct()
  {
    parent::__construct();
  }


  public function get_sap_receive_transform($code)
  {
    $rs = $this->ms
    ->select('DocEntry, DocStatus')
    ->where('U_ECOMNO', $code)
    ->where('CANCELED', 'N')
    ->get('OIGN');
    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return FALSE;
  }


  public function is_middle_exists($code)
  {
    $rs = $this->mc->select('U_ECOMNO')->where('U_ECOMNO', $code)->get('OIGN');
    if($rs->num_rows() === 1)
    {
      return TRUE;
    }

    return FALSE;
  }



  public function get_middle_receive_transform($code)
  {
    $rs = $this->mc
    ->select('DocEntry')
    ->where('U_ECOMNO', $code)
    ->group_start()
    ->where('F_Sap', 'N')
    ->or_where('F_Sap IS NULL', NULL, FALSE)
    ->group_end()
    ->get('OIGN');

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function drop_middle_exits_data($docEntry)
  {
    $this->mc->trans_start();
    $this->mc->where('DocEntry', $docEntry)->delete('IGN1');
    $this->mc->where('DocEntry', $docEntry)->delete('OIGN');
    $this->mc->trans_complete();

    return $this->mc->trans_status();
  }


  public function add_sap_receive_transform(array $ds = array())
  {
    $rs = $this->mc->insert('OIGN', $ds);
    if($rs)
    {
      return $this->mc->insert_id();
    }

    return FALSE;
  }


  public function update_sap_receive_transform($code, $ds)
  {
    return $this->mc->where('U_ECOMNO', $code)->update('OIGN', $ds);
  }


  public function add_sap_receive_transform_detail(array $ds = array())
  {
    return $this->mc->insert('IGN1', $ds);
  }


  public function drop_sap_exists_details($code)
  {
    return $this->mc->where('U_ECOMNO', $code)->delete('IGN1');
  }



  public function add(array $ds = array())
  {
    if(!empty($ds))
    {
      return $this->db->insert('receive_transform', $ds);
    }

    return FALSE;
  }



  public function update($code, array $ds = array())
  {
    if(!empty($ds))
    {
      return $this->db->where('code', $code)->update('receive_transform', $ds);
    }

    return FALSE;
  }


  public function update_detail($id, array $ds = array())
  {
    if( ! empty($ds))
    {
      return $this->db->where('id', $id)->update('receive_transform_detail', $ds);
    }

    return FALSE;
  }


  public function add_detail(array $ds = array())
  {
    if(!empty($ds))
    {
      return $this->db->insert('receive_transform_detail', $ds);
    }

    return FALSE;
  }


  public function get_detail_row($receive_code, $product_code)
  {
    $rs = $this->db->where('receive_code', $receive_code)->where('product_code', $product_code)->get('receive_transform_detail');

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }



  public function get($code)
  {
    $rs = $this->db
    ->select('r.*, z.name AS zone_name, u.uname, u.name AS display_name')
    ->from('receive_transform AS r')
    ->join('zone AS z', 'r.zone_code = z.code', 'left')
    ->join('user AS u', 'z.user_id = u.id', 'left')
    ->where('r.code', $code)
    ->get();

    if($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return FALSE;
  }



  public function get_details($code)
  {
    $rs = $this->db
    ->select('receive_transform_detail.*, products.barcode, products.unit_code')
    ->from('receive_transform_detail')
    ->join('products', 'products.code = receive_transform_detail.product_code', 'left')
    ->where('receive_code', $code)
    ->get();

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return FALSE;
  }



  public function drop_details($code)
  {
    return $this->db->where('receive_code', $code)->delete('receive_transform_detail');
  }



  public function cancle_details($code)
  {
    return $this->db->set('is_cancle', 1)->where('receive_code', $code)->update('receive_transform_detail');
  }




  public function get_transform_details($order_code)
  {
    $rs = $this->db
    ->select('order_transform_detail.*, products.name, products.cost AS price, products.barcode, products.unit_code')
    ->select_sum('order_transform_detail.sold_qty', 'sold_qty')
    ->select_sum('order_transform_detail.receive_qty', 'receive_qty')
    ->from('order_transform_detail')
    ->join('order_transform', 'order_transform.order_code = order_transform_detail.order_code', 'left')
    ->join('products', 'products.code = order_transform_detail.product_code', 'left')
    ->where('order_transform_detail.order_code', $order_code)
    ->where('order_transform.is_closed', 0)
    ->group_by('order_transform_detail.product_code')
    ->get();

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return FALSE;
  }


  public function get_sum_uncomplete_qty($order_code, $product_code, $receive_code)
  {
    $rs = $this->db
    ->select_sum('rd.qty')
    ->from('receive_transform_detail AS rd')
    ->join('receive_transform AS rt', 'rd.receive_code = rt.code', 'left')
    ->where('rt.order_code', $order_code)
    ->where_in('rt.status', array(0, 3, 4))
    ->where('rt.is_expire', 0)
    ->where('rd.product_code', $product_code)
    ->where('rd.receive_code !=', $receive_code)
    ->where('rd.is_cancle', 0)
    ->get();

    if($rs->num_rows() === 1)
    {
      return $rs->row()->qty;
    }

    return 0;
  }


  public function get_sum_qty($code)
  {
    $rs = $this->db->select_sum('qty', 'qty')
    ->where('receive_code', $code)
    ->get('receive_transform_detail');

    return intval($rs->row()->qty);
  }



  public function get_sum_amount($code)
  {
    $rs = $this->db->select_sum('amount')->where('receive_code', $code)->get('receive_transform_detail');
    return $rs->row()->amount === NULL ? 0.00 : $rs->row()->amount;
  }




	public function get_transform_backlogs($code, $product_code)
	{
		$rs = $this->db
		->select_sum('sold_qty')
		->select_sum('receive_qty')
		->where('order_code', $code)
		->where('product_code', $product_code)
		->get('order_transform_detail');

		if($rs->num_rows() === 1)
		{
			return $rs->row();
		}

		return NULL;
	}


  public function set_status($code, $status)
  {
    return $this->db->set('status', $status)->where('code', $code)->update('receive_transform');
  }



  public function count_rows(array $ds = array())
  {
    //---- เลขที่เอกสาร
    if($ds['code'] != '')
    {
      $this->db->like('code', $ds['code']);
    }

    //--- ใบสั่งซื้อ
    if($ds['order_code'] != '')
    {
      $this->db->like('order_code', $ds['order_code']);
    }

    //---- ใบสั่งขาย
    if( ! empty($ds['so_code']))
    {
      $this->db->like('so_code', $ds['so_code']);
    }

    //---- invoice
    if($ds['invoice'] != '')
    {
      $this->db->like('invoice_code', $ds['invoice']);
    }

    if($ds['from_date'] != '' && $ds['to_date'] != '')
    {
      $this->db->where('date_add >=', from_date($ds['from_date']));
      $this->db->where('date_add <=', to_date($ds['to_date']));
    }

    if($ds['status'] !== 'all')
    {
      if($ds['status'] == 5)
      {
        $this->db->where('is_expire', 1);
      }
      else
      {
        $this->db->where('is_expire', 0)->where('status', $ds['status']);
      }
    }

    if(isset($ds['sap_status']) && $ds['sap_status'] != 'all')
    {
      if($ds['sap_status'] == 0) {
        $this->db->where('inv_code IS NULL', NULL, FALSE);
      }
      else
      {
        $this->db->where('inv_code IS NOT NULL', NULL, FALSE);
      }
    }

    if(isset($ds['zone']) && $ds['zone'] != "")
    {
      $this->db->like('zone_code', $ds['zone']);
    }


    return $this->db->count_all_results('receive_transform');
  }





  public function get_data(array $ds = array(), $perpage = 20, $offset = 0)
  {
    //---- เลขที่เอกสาร
    if($ds['code'] != '')
    {
      $this->db->like('code', $ds['code']);
    }

    //--- ใบสั่งซื้อ
    if($ds['order_code'] != '')
    {
      $this->db->like('order_code', $ds['order_code']);
    }

    //---- ใบสั่งขาย
    if( ! empty($ds['so_code']))
    {
      $this->db->like('so_code', $ds['so_code']);
    }


    //---- invoice
    if($ds['invoice'] != '')
    {
      $this->db->like('invoice_code', $ds['invoice']);
    }


    if($ds['from_date'] != '' && $ds['to_date'] != '')
    {
      $this->db->where('date_add >=', from_date($ds['from_date']));
      $this->db->where('date_add <=', to_date($ds['to_date']));
    }

    if($ds['status'] !== 'all')
    {
      if($ds['status'] == 5)
      {
        $this->db->where('is_expire', 1);
      }
      else
      {
        $this->db->where('is_expire', 0)->where('status', $ds['status']);
      }
    }

    if(isset($ds['sap_status']) && $ds['sap_status'] != 'all')
    {
      if($ds['sap_status'] == 0) {
        $this->db->where('inv_code IS NULL', NULL, FALSE);
      }
      else
      {
        $this->db->where('inv_code IS NOT NULL', NULL, FALSE);
      }
    }

    if(isset($ds['zone']) && $ds['zone'] != "")
    {
      $this->db->like('zone_code', $ds['zone']);
    }

    $rs = $this->db->order_by('code', 'DESC')->limit($perpage, $offset)->get('receive_transform');

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function get_max_code($code)
  {
    $rs = $this->db
    ->select_max('code')
    ->like('code', $code, 'after')
    ->order_by('code', 'DESC')
    ->get('receive_transform');

    if($rs->num_rows() == 1)
    {
      return $rs->row()->code;
    }

    return FALSE;
  }


  public function is_exists($code)
  {
    $rs = $this->db->select('status')->where('code', $code)->get('receive_transform');
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
    ->get('receive_transform');

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
    ->get('OIGN');

    if($rs->num_rows() > 0)
    {
      return $rs->row()->DocNum;
    }

    return NULL;
  }



  public function update_inv($code, $doc_num)
  {
    return $this->db->set('inv_code', $doc_num)->where('code', $code)->update('receive_transform');
  }

}

 ?>
