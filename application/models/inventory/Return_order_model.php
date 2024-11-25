<?php
class Return_order_model extends CI_Model
{
  public function __construct()
  {
    parent::__construct();
  }


  //--- เพิ่มเอกสารใหม่เข้าถังกลาง
  public function add_sap_return_order(array $ds = array())
  {
    if(!empty($ds))
    {
      $rs = $this->mc->insert('ORDN', $ds);
      if($rs)
      {
        return $this->mc->insert_id();
      }
    }

    return FALSE;
  }


  public function get_middle_return_doc($code)
  {
    $rs = $this->mc
    ->select('DocEntry')
    ->where('U_ECOMNO', $code)
    ->where('F_Sap', 'N')
    ->get('ORDN');

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  //--- เพิ่มรายการรับคืน
  public function add_sap_return_detail(array $ds = array())
  {
    if(!empty($ds))
    {
      return $this->mc->insert('RDN1', $ds);
    }

    return FALSE;
  }


  //---- อัพเดตเอกสารในถังกลาง
  public function update_sap_return_order($code, $ds = array())
  {
    if(! empty($code) && ! empty($ds))
    {
      return $this->mc->where('U_ECOMNO', $code)->update('ORDN', $ds);
    }

    return FALSE;
  }



  //---- ดึงข้อมูลจากถังกลางมาเช็คสถานะ
  public function get_sap_return_order($code)
  {
    $rs = $this->ms
    ->select('DocEntry')
    ->where('U_ECOMNO', $code)
    ->where('CANCELED', 'N')
    ->get('ORDN');
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
    ->get('ORDN');

    if($rs->num_rows() > 0)
    {
      return $rs->row()->DocNum;
    }

    return NULL;
  }


  public function get_total_return($code)
  {
    $rs = $this->db
    ->select_sum('amount')
    ->where('return_code', $code)
    ->get('return_order_detail');

    return $rs->row()->amount === NULL ? 0 : $rs->row()->amount;
  }



  public function add(array $ds = array())
  {
    if(!empty($ds))
    {
      return $this->db->insert('return_order', $ds);
    }

    return FALSE;
  }



  public function update($code, array $ds = array())
  {
    if(!empty($ds))
    {
      return $this->db->where('code', $code)->update('return_order', $ds);
    }

    return FALSE;
  }


	public function update_detail($id, $ds = array())
	{
		if(!empty($ds))
		{
			return $this->db->where('id', $id)->update('return_order_detail', $ds);
		}

		return FALSE;
	}


  public function update_details($code, array $ds = array())
  {
    if( ! empty($ds))
    {
      return $this->db->where('return_code', $code)->update('return_order_detail', $ds);
    }

    return FALSE;
  }


  public function update_inv($code, $doc_num)
  {
    return $this->db->set('inv_code', $doc_num)->where('code', $code)->update('return_order');
  }


  public function add_detail(array $ds = array())
  {
    if(!empty($ds))
    {
      return $this->db->insert('return_order_detail', $ds);
    }

    return FALSE;
  }



  public function get_non_inv_code($limit = 100)
  {
    $rs = $this->db
    ->select('code')
    ->where('status', 1)
		->where('is_approve', 1)
    ->where('inv_code IS NULL', NULL, FALSE)
    ->get('return_order');

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function get($code)
  {
    $rs = $this->db
    ->select('r.*')
    ->select('c.name AS customer_name')
    ->select('wh.name AS warehouse_name, z.name AS zone_name')
    ->select('u.uname, u.name AS display_name')
    ->from('return_order AS r')
    ->join('customers AS c', 'r.customer_code = c.code', 'left')
    ->join('warehouse AS wh', 'r.warehouse_code = wh.code', 'left')
    ->join('zone AS z', 'r.zone_code = z.code', 'left')
    ->join('user AS u', 'z.user_id = u.id', 'left')
    ->where('r.code', $code)
    ->get();

    if($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return NULL;
  }



  public function get_details($code)
  {
    $rs = $this->db
		->select('rd.*, pd.unit_code AS unit_code, pd.count_stock, pd.barcode')
		->from('return_order_detail AS rd')
		->join('products AS pd', 'rd.product_code = pd.code', 'left')
		->where('rd.return_code', $code)
		->get();

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function get_count_item_details($code)
  {
    $rs = $this->db
		->select('rd.*, pd.unit_code AS unit_code')
		->from('return_order_detail AS rd')
		->join('products AS pd', 'rd.product_code = pd.code', 'left')
		->where('rd.return_code', $code)
    ->where('pd.count_stock', 1)
		->get();

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


	public function get_non_count_details($code)
	{
		$rs = $this->db
		->select('rd.*, pd.unit_code AS unit_code')
		->from('return_order_detail AS rd')
		->join('products AS pd', 'rd.product_code = pd.code', 'left')
		->where('rd.return_code', $code)
    ->where('pd.count_stock', 0)
		->get();

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return FALSE;
	}


	public function get_detail_by_product($code, $product_code)
	{
		$rs = $this->db
		->select('rd.*, pd.unit_code')
		->from('return_order_detail AS rd')
		->join('products AS pd', 'rd.product_code = pd.code', 'left')
		->where('rd.return_code', $code)
		->where('rd.product_code', $product_code)
		->get();

		if($rs->num_rows() > 0)
		{
			return $rs->result();
		}

		return NULL;
	}


	public function drop_not_valid_details($code)
	{
		return $this->db->where('return_code', $code)->where('valid', 0)->delete('return_order_detail');
	}



  public function drop_sap_exists_details($code)
  {
    return $this->mc->where('U_ECOMNO', $code)->delete('RDN1');
  }


  public function drop_middle_exits_data($docEntry)
  {
    $this->mc->trans_start();
    $this->mc->where('DocEntry', $docEntry)->delete('RDN1');
    $this->mc->where('DocEntry', $docEntry)->delete('ORDN');
    $this->mc->trans_complete();

    return $this->mc->trans_status();
  }


  public function get_invoice_details($invoice)
  {
    $this->ms
    ->select('iv.DocNum, iv.NumAtCard')
    ->select('ivd.DocEntry, ivd.U_ECOMNO AS order_code')
    ->select('ivd.ItemCode AS product_code, ivd.Dscription AS product_name')
    ->select('ivd.Quantity AS qty, ivd.PriceBefDi AS price, ivd.DiscPrcnt AS discount, ivd.VatPrcnt AS vat_rate')
    ->from('INV1 AS ivd')
    ->join('OINV AS iv', 'ivd.DocEntry = iv.DocEntry', 'left')
    ->where('iv.DocNum', $invoice);

    $rs = $this->ms->get();

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }




  public function get_total_return_vat($code)
  {
    $rs = $this->db
    ->select_sum('vat_amount', 'amount')
    ->where('return_code', $code)
    ->get('return_order_detail');

    return $rs->row()->amount === NULL ? 0 : $rs->row()->amount;
  }



  public function get_customer_invoice($invoice)
  {
    $rs = $this->ms->select('CardCode AS customer_code, CardName AS customer_name')->where('DocNum', $invoice)->get('OINV');
    if($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return FALSE;
  }



  public function delete_detail($id)
  {
    return $this->db->where('id', $id)->delete('return_order_detail');
  }




  public function drop_details($code)
  {
    return $this->db->where('return_code', $code)->delete('return_order_detail');
  }



  public function cancle_details($code)
  {
    return $this->db->set('is_cancle', 1)->where('return_code', $code)->update('return_order_detail');
  }


  //--- จำนวนรวมของสินค้าที่เคยคืนไปแล้ว ในใบกำกับนี้
  public function get_returned_qty($invoice, $product_code, $return_code = NULL)
  {
    $this->db
    ->select_sum('qty')
    ->where('invoice_code', $invoice)
    ->where('product_code', $product_code)
		->where('is_cancle', 0);

    if( ! empty($return_code))
    {
      $this->db->where('return_code !=', $return_code);
    }

    $rs = $this->db->get('return_order_detail');

    return $rs->row()->qty === NULL ? 0 : $rs->row()->qty;
  }




  public function get_sum_qty($code)
  {
    $rs = $this->db->select_sum('qty', 'qty')
    ->where('return_code', $code)
    ->get('return_order_detail');

    return $rs->row()->qty === NULL ? 0 : $rs->row()->qty;
  }


  public function get_sum_amount($code)
  {
    $rs = $this->db->select_sum('amount')
    ->where('return_code', $code)
    ->get('return_order_detail');

    return $rs->row()->amount === NULL ? 0 : $rs->row()->amount;
  }



  public function set_status($code, $status)
  {
    return $this->db->set('status', $status)->where('code', $code)->update('return_order');
  }



  public function approve($code)
  {
    $arr = array('is_approve' => 1, 'approver' => get_cookie('uname'));
    return $this->db->where('code', $code)->update('return_order', $arr);
  }


  public function unapprove($code)
  {
    $arr = array('is_approve' => 0, 'approver' => NULL);
    return $this->db->where('code', $code)->update('return_order', $arr);
  }


  public function count_rows(array $ds = array())
  {
    $this->db
    ->from('return_order AS r')
    ->join('customers AS c', 'r.customer_code = c.code', 'left')
    ->join('zone AS z', 'r.zone_code = z.code', 'left');

    //---- เลขที่เอกสาร
    if(!empty($ds['code']))
    {
      $this->db->like('r.code', $ds['code']);
    }

    //---- invoice
    if(!empty($ds['invoice']))
    {
      $this->db->like('r.invoice', $ds['invoice']);
    }

    if( ! empty($ds['reference']))
    {
      $this->db->like('r.reference', $ds['reference']);
    }

    //--- customer
    if(!empty($ds['customer_code']))
    {
      $this->db
      ->group_start()
      ->like('r.customer_code', $ds['customer_code'])
      ->or_like('c.name', $ds['customer_code'])
      ->group_end();
    }

    if( ! empty($ds['warehouse']) && $ds['warehouse'] != 'all')
    {
      $this->db->where('r.warehouse_code', $ds['warehouse']);
    }

    if( ! empty($ds['user']) && $ds['user'] != 'all')
    {
      $this->db->where('user', $ds['user']);
    }

		if( ! empty($ds['zone']))
    {
      $this->db
      ->group_start()
      ->like('r.zone_code', $ds['zone'])
      ->or_like('z.name', $ds['zone'])
      ->group_end();
    }

    if($ds['status'] != 'all')
    {
      if($ds['status'] == 5)
      {
        $this->db->where('r.is_expire', 1);
      }
      else
      {
        $this->db->where('r.is_expire', 0)->where('r.status', $ds['status']);
      }
    }

    if($ds['approve'] != 'all')
    {
      $this->db->where('r.is_approve', $ds['approve']);
    }


    if(isset($ds['is_pos']) && $ds['is_pos'] !== 'all')
		{
      if($ds['is_pos'] == 1)
      {
        $this->db->where('r.ref_type', 4);
      }
      else
      {
        $this->db->where('r.ref_type !=', 4);
      }
		}

    if(!empty($ds['from_date']) && !empty($ds['to_date']))
    {
      $this->db->where('r.date_add >=', from_date($ds['from_date']));
      $this->db->where('r.date_add <=', to_date($ds['to_date']));
    }

    if(isset($ds['sap']) && $ds['sap'] != 'all')
    {
      if($ds['sap'] == 0)
      {
        $this->db->where('r.inv_code IS NULL', NULL, FALSE);
      }
      else
      {
        $this->db->where('r.inv_code IS NOT NULL', NULL, FALSE);
      }
    }

    return $this->db->count_all_results();
  }





  public function get_list(array $ds = array(), $perpage = 20, $offset = 0)
  {
    $this->db
    ->select('r.*, c.name AS customer_name, z.name AS zone_name, u.name AS display_name')
    ->from('return_order AS r')
    ->join('customers AS c', 'r.customer_code = c.code', 'left')
    ->join('zone AS z', 'r.zone_code = z.code', 'left')
    ->join('user AS u', 'r.user = u.uname', 'left');

    //---- เลขที่เอกสาร
    if(!empty($ds['code']))
    {
      $this->db->like('r.code', $ds['code']);
    }

    //---- invoice
    if(!empty($ds['invoice']))
    {
      $this->db->like('r.invoice', $ds['invoice']);
    }

    if( ! empty($ds['reference']))
    {
      $this->db->like('r.reference', $ds['reference']);
    }

    //--- customer
    if(!empty($ds['customer_code']))
    {
      $this->db
      ->group_start()
      ->like('r.customer_code', $ds['customer_code'])
      ->or_like('c.name', $ds['customer_code'])
      ->group_end();
    }

    if( ! empty($ds['warehouse']) && $ds['warehouse'] != 'all')
    {
      $this->db->where('r.warehouse_code', $ds['warehouse']);
    }

    if( ! empty($ds['user']) && $ds['user'] != 'all')
    {
      $this->db->where('user', $ds['user']);
    }

		if( ! empty($ds['zone']))
    {
      $this->db
      ->group_start()
      ->like('r.zone_code', $ds['zone'])
      ->or_like('z.name', $ds['zone'])
      ->group_end();
    }

    if($ds['status'] != 'all')
    {
      if($ds['status'] == 5)
      {
        $this->db->where('r.is_expire', 1);
      }
      else
      {
        $this->db->where('r.is_expire', 0)->where('r.status', $ds['status']);
      }
    }

    if($ds['approve'] != 'all')
    {
      $this->db->where('r.is_approve', $ds['approve']);
    }


		if(isset($ds['is_pos']) && $ds['is_pos'] !== 'all')
		{
			if($ds['is_pos'] == 1)
      {
        $this->db->where('r.ref_type', 4);
      }
      else
      {
        $this->db->where('r.ref_type !=', 4);
      }
		}

    if(!empty($ds['from_date']) && !empty($ds['to_date']))
    {
      $this->db->where('r.date_add >=', from_date($ds['from_date']));
      $this->db->where('r.date_add <=', to_date($ds['to_date']));
    }

    if(isset($ds['sap']) && $ds['sap'] != 'all')
    {
      if($ds['sap'] == 0)
      {
        $this->db->where('r.inv_code IS NULL', NULL, FALSE);
      }
      else
      {
        $this->db->where('r.inv_code IS NOT NULL', NULL, FALSE);
      }
    }

    $rs = $this->db->order_by('r.code', 'DESC')->limit($perpage, $offset)->get();

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
    ->get('return_order');

    if($rs->num_rows() == 1)
    {
      return $rs->row()->code;
    }

    return FALSE;
  }


  public function customer_in($txt)
  {
    $sc = array('0');
    $rs = $this->db
    ->select('code')->
    like('code', $txt)
    ->or_like('name', $txt)
    ->get('customers');

    if($rs->num_rows() > 0)
    {
      foreach($rs->result() as $rs)
      {
        $sc[] = $rs->code;
      }
    }

    return $sc;
  }

}

 ?>
