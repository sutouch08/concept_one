<?php
class Receive_po_model extends CI_Model
{
  public function __construct()
  {
    parent::__construct();
  }



  public function add(array $ds = array())
  {
    if(!empty($ds))
    {
      return $this->db->insert('receive_product', $ds);
    }

    return FALSE;
  }



  public function update($code, array $ds = array())
  {
    if(!empty($ds))
    {
      return $this->db->where('code', $code)->update('receive_product', $ds);
    }

    return FALSE;
  }


  public function add_detail(array $ds = array())
  {
    if(!empty($ds))
    {
      return $this->db->insert('receive_product_detail', $ds);
    }

    return FALSE;
  }



  public function get($code)
  {
    $rs = $this->db
    ->select('r.*, z.user_id')
    ->from('receive_product AS r')
    ->join('zone AS z', 'r.zone_code = z.code', 'left')
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
    ->where('receive_code', $code)
    ->get('receive_product_detail');

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


	public function get_detail_by_product($code, $product_code)
	{
		$rs = $this->db->where('receive_code', $code)->where('product_code', $product_code)->get('receive_product_detail');

		if($rs->num_rows() > 0)
		{
			return $rs->result();
		}

		return NULL;
	}


	public function update_detail($id, $ds = array())
	{
		if(!empty($ds))
		{
			return $this->db->where('id', $id)->update('receive_product_detail', $ds);
		}

		return FALSE;
	}

  public function drop_details($code)
  {
    return $this->db->where('receive_code', $code)->delete('receive_product_detail');
  }


	public function drop_not_valid_details($code)
	{
		return $this->db->where('receive_code', $code)->where('valid', 0)->delete('receive_product_detail');
	}



  public function cancle_details($code)
  {
    return $this->db->set('is_cancle', 1)->where('receive_code', $code)->update('receive_product_detail');
  }


  public function get_po($po_code)
  {
    $rs = $this->ms
    ->select('DocEntry, DocNum, DocStatus, CardCode, CardName, DocCur, DocRate, DiscPrcnt')
    ->where('DocNum', $po_code)
    ->where('CANCELED', 'N')
    ->get('OPOR');

    if($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return NULL;
  }

  public function get_po_details($po_code)
  {
    $rs = $this->ms
    ->select('d.DocEntry, d.LineNum, d.ItemCode, d.Dscription, d.Text')
    ->select('d.Quantity, d.LineStatus, d.OpenQty, d.Price')
    ->select('d.PriceBefDi, d.PriceAfVAT, d.DiscPrcnt')
		->select('d.Currency, d.Rate, d.VatGroup, d.VatPrcnt, d.unitMsr')
    ->select('d.unitMsr, d.NumPerMsr, d.unitMsr2, d.NumPerMsr2')
    ->select('d.UomEntry, d.UomEntry2, d.UomCode, d.UomCode2')
    ->from('POR1 AS d')
    ->join('OPOR AS o', 'd.DocEntry = o.DocEntry', 'left')
    ->where('o.DocNum', $po_code)
    ->where('o.DocStatus', 'O')
    ->where('d.LineStatus', 'O')
    ->get();

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }



	public function get_po_detail($po_code, $item_code)
  {
    $rs = $this->ms
    ->select('POR1.DocEntry, POR1.LineNum, POR1.ItemCode, POR1.Dscription, POR1.Quantity, POR1.LineStatus, POR1.OpenQty, POR1.PriceAfVAT AS price')
		->select('POR1.Currency, POR1.Rate, POR1.VatGroup, POR1.VatPrcnt')
    ->from('POR1')
    ->join('OPOR', 'POR1.DocEntry = OPOR.DocEntry', 'left')
    ->where('OPOR.DocNum', $po_code)
		->where('POR1.ItemCode', $item_code)
    ->where('OPOR.DocStatus', 'O')
    ->get();

    if($rs->num_rows() > 0)
    {
      return $rs->row();
    }

    return NULL;
  }


  public function get_po_row($docEntry, $lineNum)
  {
    $rs = $this->ms
    ->select('POR1.DocEntry, POR1.LineNum, POR1.CodeBars AS barcode')
    ->select('POR1.ItemCode, POR1.Dscription, POR1.Quantity, POR1.LineStatus')
    ->select('POR1.OpenQty, POR1.PriceAfVAT AS price')
		->select('POR1.Currency, POR1.Rate, POR1.VatGroup, POR1.VatPrcnt')
    ->from('POR1')
    ->where('DocEntry', $docEntry)
    ->where('LineNum', $lineNum)
    ->get();

    if($rs->num_rows() > 0)
    {
      return $rs->row();
    }

    return NULL;
  }



  public function get_po_data($po_code)
  {
    $rs = $this->ms
    ->select('POR1.Currency, POR1.VatGroup, POR1.VatPrcnt')
    ->from('POR1')
    ->join('OPOR', 'POR1.DocEntry = OPOR.DocEntry', 'left')
    ->where('OPOR.DocNum', $po_code)
    ->limit(1)
    ->get();

    if($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return NULL;
  }


  public function get_sap_receive_doc($code)
  {
    $rs = $this->ms
    ->select('DocEntry, DocStatus')
    ->where('U_ECOMNO', $code)
    ->where('CANCELED', 'N')
    ->order_by('DocEntry', 'DESC')
    ->get('OPDN');

    if($rs->num_rows() > 0)
    {
      return $rs->row();
    }

    return FALSE;
  }


  public function is_middle_exists($code)
  {
    $rs = $this->mc->select('U_ECOMNO')->where('U_ECOMNO', $code)->get('OPDN');
    if($rs->num_rows() === 1)
    {
      return TRUE;
    }

    return FALSE;
  }


  public function get_middle_receive_po($code)
  {
    $rs = $this->mc
    ->select('DocEntry')
    ->where('U_ECOMNO', $code)
    ->group_start()
    ->where('F_Sap', 'N')
    ->or_where('F_Sap IS NULL', NULL, FALSE)
    ->group_end()
    ->get('OPDN');

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return FALSE;
  }


  public function add_sap_receive_po(array $ds = array())
  {
    $rs = $this->mc->insert('OPDN', $ds);
    if($rs)
    {
      return $this->mc->insert_id();
    }

    return FALSE;
  }


  public function update_sap_receive_po($code, $ds)
  {
    return $this->mc->where('U_ECOMNO', $code)->update('OPDN', $ds);
  }


  public function add_sap_receive_po_detail(array $ds = array())
  {
    return $this->mc->insert('PDN1', $ds);
  }


  public function drop_sap_received($docEntry)
  {
    $this->mc->trans_start();
    $this->mc->where('DocEntry', $docEntry)->delete('PDN1');
    $this->mc->where('DocEntry', $docEntry)->delete('OPDN');
    $this->mc->trans_complete();
    return $this->mc->trans_status();
  }


  public function get_doc_status($code)
  {
    $rs = $this->ms
    ->select('DocStatus')
    ->where('U_ECOMNO', $code)
    ->where('CANCELED', 'N')
    ->get('OPDN');
    if($rs->num_rows() === 1)
    {
      return $rs->row()->DocStatus;
    }

    return 'O';
  }


  public function get_sum_qty($code)
  {
    $rs = $this->db->select_sum('qty', 'qty')
    ->where('receive_code', $code)
    ->get('receive_product_detail');

    return intval($rs->row()->qty);
  }



  public function get_sum_amount($code)
  {
    $rs = $this->db->select_sum('amount')->where('receive_code', $code)->get('receive_product_detail');
    return $rs->row()->amount === NULL ? 0.00 : $rs->row()->amount;
  }


	public function get_po_currency($code)
	{
		$rs = $this->ms
		->select('DocCur, DocRate')
		->where('DocNum', $code)
		->get('OPOR');

		if($rs->num_rows() == 1)
		{
			return $rs->row();
		}

		return NULL;
	}

  public function set_status($code, $status)
  {
    return $this->db->set('status', $status)->where('code', $code)->update('receive_product');
  }


	public function set_cancle_reason($code, $reason)
	{
		return $this->db->set('cancle_reason', $reason)->where('code', $code)->update('receive_product');
	}



  public function count_rows(array $ds = array())
  {
    $this->db
    ->from('receive_product AS r')
    ->join('user AS u', 'r.user = u.uname', 'left');

    //---- เลขที่เอกสาร
    if( ! empty($ds['code']))
    {
      $this->db->like('r.code', $ds['code']);
    }

    //--- ใบสั่งซื้อ
    if( ! empty($ds['po']))
    {
      $this->db->like('r.po_code', $ds['po']);
    }

    //---- invoice
    if( ! empty($ds['invoice']))
    {
      $this->db->like('r.invoice_code', $ds['invoice']);
    }


    //--- vendor
    if( ! empty($ds['vendor']))
    {
      $this->db
      ->group_start()
      ->like('r.vendor_code', $ds['vendor'])
      ->or_like('r.vendor_name', $ds['vendor'])
      ->group_end();
    }


    if( ! ($ds['from_date']) && ! empty($ds['to_date']))
    {
      $this->db->where('r.date_add >=', from_date($ds['from_date']));
      $this->db->where('r.date_add <=', to_date($ds['to_date']));
    }

    if($ds['status'] !== 'all')
    {
      if($ds['status'] == 5)
      {
        $this->db->where('r.is_expire', 1);
      }
      else
      {
        $this->db->where('r.is_expire', 0)->where('status', $ds['status']);
      }
    }


    if($ds['sap'] !== 'all')
    {
      if($ds['sap'] == '0')
      {
        $this->db->where('r.inv_code IS NULL', NULL, FALSE);
      }
      else
      {
        $this->db->where('r.inv_code IS NOT NULL', NULL, FALSE);
      }
    }

    if( ! empty($ds['user']))
    {
      $this->db
      ->group_start()
      ->like('r.user', $ds['user'])
      ->or_like('u.name', $ds['user'])
      ->group_end();
    }

    return $this->db->count_all_results();
  }





  public function get_list(array $ds = array(), $perpage = 20, $offset = 0)
  {
    $this->db
    ->select('r.*, u.name AS display_name')
    ->from('receive_product AS r')
    ->join('user AS u', 'r.user = u.uname', 'left');

    //---- เลขที่เอกสาร
    if( ! empty($ds['code']))
    {
      $this->db->like('r.code', $ds['code']);
    }

    //--- ใบสั่งซื้อ
    if( ! empty($ds['po']))
    {
      $this->db->like('r.po_code', $ds['po']);
    }

    //---- invoice
    if( ! empty($ds['invoice']))
    {
      $this->db->like('r.invoice_code', $ds['invoice']);
    }


    //--- vendor
    if( ! empty($ds['vendor']))
    {
      $this->db
      ->group_start()
      ->like('r.vendor_code', $ds['vendor'])
      ->or_like('r.vendor_name', $ds['vendor'])
      ->group_end();
    }


    if( ! ($ds['from_date']) && ! empty($ds['to_date']))
    {
      $this->db->where('r.date_add >=', from_date($ds['from_date']));
      $this->db->where('r.date_add <=', to_date($ds['to_date']));
    }

    if($ds['status'] !== 'all')
    {
      if($ds['status'] == 5)
      {
        $this->db->where('r.is_expire', 1);
      }
      else
      {
        $this->db->where('r.is_expire', 0)->where('status', $ds['status']);
      }
    }


    if($ds['sap'] !== 'all')
    {
      if($ds['sap'] == '0')
      {
        $this->db->where('r.inv_code IS NULL', NULL, FALSE);
      }
      else
      {
        $this->db->where('r.inv_code IS NOT NULL', NULL, FALSE);
      }
    }

    if( ! empty($ds['user']))
    {
      $this->db
      ->group_start()
      ->like('r.user', $ds['user'])
      ->or_like('u.name', $ds['user'])
      ->group_end();
    }

    $this->db->order_by('r.date_add', 'DESC');
    $this->db->order_by('code', 'DESC');

    $rs = $this->db->limit($perpage, $offset)->get();

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
    ->like('code', $code)
    ->order_by('code', 'DESC')
    ->get('receive_product');

    if($rs->num_rows() == 1)
    {
      return $rs->row()->code;
    }

    return FALSE;
  }


  public function get_vender_by_po($po_code)
  {
    $rs = $this->ms->select('CardCode, CardName')->where('DocNum', $po_code)->get('OPOR');
    if($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return FALSE;
  }


  public function is_exists($code)
  {
    $rs = $this->db->select('status')->where('code', $code)->get('receive_product');
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
    ->get('receive_product');

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return FALSE;
  }


  public function get_sap_doc_num($code)
  {
    $rs = $this->ms->select('DocNum')->where('U_ECOMNO', $code)->get('OPDN');
    if($rs->num_rows() === 1)
    {
      return $rs->row()->DocNum;
    }

    return FALSE;
  }


  public function update_inv($code, $doc_num)
  {
    return $this->db->set('inv_code', $doc_num)->where('code', $code)->update('receive_product');
  }

  public function get_on_order_qty($itemCode, $poCode, $baseEntry, $baseLine)
  {
    $rs = $this->db
    ->select_sum('rd.receive_qty')
    ->from('receive_product_detail AS rd')
    ->join('receive_product AS ro', 'rd.receive_code = ro.code', 'left')
    ->where('ro.po_code', $poCode)
    ->where('ro.status !=', 2)
    ->where('ro.inv_code IS NULL', NULL, FALSE)
    ->where('ro.is_expire', 0)
    ->where('rd.baseEntry', $baseEntry)
    ->where('rd.baseLine', $baseLine)
    ->where('rd.product_code', $itemCode)
    ->get();

    if($rs->num_rows() === 1)
    {
      return $rs->row()->receive_qty > 0 ? $rs->row()->receive_qty : 0;
    }

    return 0;
  }
}

 ?>
