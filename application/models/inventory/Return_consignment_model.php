<?php
class Return_consignment_model extends CI_Model
{
  public function __construct()
  {
    parent::__construct();
  }


  //--- เพิ่มเอกสารใหม่เข้าถังกลาง
  public function add_sap_return_consignment(array $ds = array())
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
    ->group_start()
    ->where('F_Sap', 'N')
    ->or_where('F_Sap IS NULL', NULL, FALSE)
    ->group_end()
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
  public function update_sap_return_consignment($code, $ds = array())
  {
    if(! empty($code) && ! empty($ds))
    {
      return $this->mc->where('U_ECOMNO', $code)->update('ORDN', $ds);
    }

    return FALSE;
  }



  //---- เช็คเอกสารในว่ามีอยู่หรือยัง
  public function get_sap_return_consignment($code)
  {
    $rs = $this->ms
    ->select('DocEntry')
    ->where('U_ECOMNO', $code)
    ->where('CANCELED', 'N')
    ->get('ORDN');
    if($rs->num_rows() > 0)
    {
      return $rs->row();
    }

    return FALSE;
  }


  public function get_sap_invoice_amount($code, $card_code)
  {
    $rs = $this->ms
    ->select('DocTotal')
    ->where('DocNum', $code)
    ->where('CardCode', $card_code)
    ->where('CANCELED', 'N')
		->where('DocStatus', 'O')
    ->get('OINV');

    if($rs->num_rows() === 1)
    {
      return $rs->row()->DocTotal; //---- ยอดรวมทั้งบิลรวม vat
    }

    return 0;
  }



  public function is_exists_sap_doc($code)
  {
    $rs = $this->ms
    ->select('DocEntry')
    ->where('DocNum', $code)
    ->get('OINV');

    if($rs->num_rows() === 1)
    {
      return TRUE;
    }

    return FALSE;
  }


  public function is_exists_invoice($invoice, $return_code)
  {
    $rs = $this->db->where('return_code', $return_code)->where('invoice_code', $invoice)->get('return_consignment_invoice');
    if($rs->num_rows() > 0)
    {
      return TRUE;
    }

    return FALSE;
  }



  public function add_invoice(array $ds = array())
  {
    if(!empty($ds))
    {
      return $this->db->insert('return_consignment_invoice', $ds);
    }

    return FALSE;
  }


  public function delete_invoice($code, $invoice_code)
  {
    return $this->db->where('return_code', $code)->where('invoice_code', $invoice_code)->delete('return_consignment_invoice');
  }


  //--- return invoice_code, invoice_amount in return order
  public function get_all_invoice($code)
  {
    $rs = $this->db
    ->select('invoice_code, invoice_amount')
    ->where('return_code', $code)
    ->get('return_consignment_invoice');

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return FALSE;
  }


  public function get_sum_invoice_amount($code)
  {
    $rs = $this->db->select_sum('invoice_amount')->where('return_code', $code)->get('return_consignment_invoice');
    if($rs->num_rows() === 1)
    {
      return $rs->row()->invoice_amount;
    }

    return 0;
  }


  public function get_total_return($code)
  {
    $rs = $this->db
    ->select_sum('amount')
    ->where('return_code', $code)
    ->get('return_consignment_detail');

    return $rs->row()->amount === NULL ? 0 : $rs->row()->amount;
  }



  public function add(array $ds = array())
  {
    if(!empty($ds))
    {
      return $this->db->insert('return_consignment', $ds);
    }

    return FALSE;
  }



  public function update($code, array $ds = array())
  {
    if(!empty($ds))
    {
      return $this->db->where('code', $code)->update('return_consignment', $ds);
    }

    return FALSE;
  }


  public function update_ref_code($code, $ref_code)
  {
    return $this->db->set('ref_code', $ref_code)->where('code', $code)->update('return_consignment');
  }


  public function add_detail(array $ds = array())
  {
    if(!empty($ds))
    {
      return $this->db->insert('return_consignment_detail', $ds);
    }

    return FALSE;
  }


	public function update_detail($id, $ds = array())
	{
		if(!empty($ds))
		{
			return $this->db->where('id', $id)->update('return_consignment_detail', $ds);
		}

		return FALSE;
	}



  public function get($code)
  {
		$rs = $this->db
    ->select('return_consignment.*')
    ->select('customers.name AS customer_name')
    ->from('return_consignment')
    ->join('customers', 'return_consignment.customer_code = customers.code', 'left')
    ->where('return_consignment.code', $code)
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
		->select('rd.*, pd.unit_code AS unit_code, pd.count_stock')
		->from('return_consignment_detail AS rd')
		->join('products AS pd', 'rd.product_code = pd.code', 'left')
		->where('rd.return_code', $code)
		->get();

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;

  }


	public function get_detail_by_product($code, $product_code)
	{
		$rs = $this->db
		->select('rd.*, pd.unit_code')
		->from('return_consignment_detail AS rd')
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



	public function get_count_item_details($code)
	{
		$rs = $this->db
		->select('rd.*, pd.unit_code AS unit_code')
		->from('return_consignment_detail AS rd')
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
		->from('return_consignment_detail AS rd')
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



  public function get_invoice_details($invoice, $customer_code)
  {
    $rs = $this->ms
    ->select('OINV.DocEntry, OINV.DocNum, OINV.NumAtCard')
    ->select('INV1.LineNum, INV1.ItemCode AS product_code')
    ->select('INV1.Dscription AS product_name')
    ->select('INV1.Quantity AS qty')
    ->select('INV1.PriceBefDi AS price')
    ->select('INV1.DiscPrcnt AS discount')
    ->select('INV1.VatGroup AS vat_code')
    ->select('INV1.VatPrcnt AS vat_rate')
    ->from('INV1')
    ->join('OINV', 'INV1.DocEntry = OINV.DocEntry')
    ->where('OINV.DocNum', $invoice)
    ->where('OINV.CardCode', $customer_code)
    ->get();

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return FALSE;
  }


  public function search_invoice_code($customer_code, $txt)
  {
    $rs = $this->ms
    ->select('DocNum, DocTotal')
    ->where('CardCode', $customer_code)
    ->like('DocNum', $txt)
    ->get('OINV');

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
    ->get('return_consignment_detail');

    return $rs->row()->amount === NULL ? 0 : $rs->row()->amount;
  }



  public function get_customer_invoice($invoice)
  {
    $rs = $this->cn->select('CardCode AS customer_code, CardName AS customer_name')->where('DocNum', $invoice)->get('OINV');
    if($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return FALSE;
  }



  public function delete_detail($id)
  {
    return $this->db->where('id', $id)->delete('return_consignment_detail');
  }




  public function drop_details($code)
  {
    return $this->db->where('return_code', $code)->delete('return_consignment_detail');
  }



  public function cancle_details($code)
  {
    return $this->db->set('is_cancle', 1)->where('return_code', $code)->update('return_consignment_detail');
  }


  //--- จำนวนรวมของสินค้าที่เคยคืนไปแล้ว ในใบกำกับนี้
  public function get_returned_qty($invoice, $product_code)
  {
    $rs = $this->db
    ->select_sum('qty')
    ->where('invoice_code', $invoice)
    ->where('product_code', $product_code)
    ->get('return_consignment_detail');

    return $rs->row()->qty === NULL ? 0 : $rs->row()->qty;
  }




  public function get_sum_qty($code)
  {
    $rs = $this->db->select_sum('qty', 'qty')
    ->where('return_code', $code)
    ->get('return_consignment_detail');

    return $rs->row()->qty === NULL ? 0 : $rs->row()->qty;
  }


  public function get_sum_amount($code)
  {
    $rs = $this->db->select_sum('amount')
    ->where('return_code', $code)
    ->get('return_consignment_detail');

    return $rs->row()->amount === NULL ? 0 : $rs->row()->amount;
  }



  public function set_status($code, $status)
  {
    return $this->db->set('status', $status)->where('code', $code)->update('return_consignment');
  }



  public function approve($code)
  {
    $arr = array('is_approve' => 1, 'approver' => get_cookie('uname'));
    return $this->db->where('code', $code)->update('return_consignment', $arr);
  }


  public function count_rows(array $ds = array())
  {
    //---- เลขที่เอกสาร
    if(!empty($ds['code']))
    {
      $this->db->like('code', $ds['code']);
    }

    //---- invoice
    if(!empty($ds['invoice']))
    {
      $this->db->like('invoice', $ds['invoice']);
    }

    //--- customer
    if(!empty($ds['customer_code']))
    {
      $this->db->where_in('customer_code', $this->customer_in($ds['customer_code']));
    }

		if($ds['from_warehouse'] != 'all')
		{
			$this->db->where('from_warehouse_code', $ds['from_warehouse']);
		}


		if($ds['to_warehouse'] != 'all')
		{
			$this->db->where('warehouse_code', $ds['to_warehouse']);
		}


    if(!empty($ds['status']) && $ds['status'] != 'all')
    {
      $this->db->where('status', $ds['status']);
    }


    if(!empty($ds['approve']) && $ds['approve'] != 'all')
    {
      $this->db->where('is_approve', $ds['approve']);
    }

		if($ds['api'] != 'all')
		{
			$this->db->where('is_api', $ds['api']);
		}


    if(isset($ds['sap']) && $ds['sap'] != 'all')
    {
      if($ds['sap'] == 0)
      {
        $this->db->where('inv_code IS NULL', NULL, FALSE);
      }
      else
      {
        $this->db->where('inv_code IS NOT NULL', NULL, FALSE);
      }
    }


    if(!empty($ds['from_date']) && !empty($ds['to_date']))
    {
      $this->db->where('date_add >=', from_date($ds['from_date']));
      $this->db->where('date_add <=', to_date($ds['to_date']));
    }

    return $this->db->count_all_results('return_consignment');
  }





  public function get_list(array $ds = array(), $perpage = 20, $offset = 0)
  {
    //---- เลขที่เอกสาร
    if(!empty($ds['code']))
    {
      $this->db->like('code', $ds['code']);
    }

    //---- invoice
    if(!empty($ds['invoice']))
    {
      $this->db->like('invoice', $ds['invoice']);
    }

    //--- customer
    if(!empty($ds['customer_code']))
    {
      $this->db->where_in('customer_code', $this->customer_in($ds['customer_code']));
    }

		if($ds['from_warehouse'] != 'all')
		{
			$this->db->where('from_warehouse_code', $ds['from_warehouse']);
		}


		if($ds['to_warehouse'] != 'all')
		{
			$this->db->where('warehouse_code', $ds['to_warehouse']);
		}


    if($ds['status'] != 'all')
    {
      $this->db->where('status', $ds['status']);
    }


    if($ds['approve'] != 'all')
    {
      $this->db->where('is_approve', $ds['approve']);
    }

		if($ds['api'] != 'all')
		{
			$this->db->where('is_api', $ds['api']);
		}

    if(isset($ds['sap']) && $ds['sap'] != 'all')
    {
      if($ds['sap'] == 0)
      {
        $this->db->where('inv_code IS NULL', NULL, FALSE);
      }
      else
      {
        $this->db->where('inv_code IS NOT NULL', NULL, FALSE);
      }
    }
    
    if(!empty($ds['from_date']) && !empty($ds['to_date']))
    {
      $this->db->where('date_add >=', from_date($ds['from_date']));
      $this->db->where('date_add <=', to_date($ds['to_date']));
    }

		$this->db->order_by('date_add', 'DESC')->limit($perpage, $offset);

    $rs = $this->db->get('return_consignment');

    return $rs->result();
  }






  public function get_max_code($code)
  {
    $rs = $this->db
    ->select_max('code')
    ->like('code', $code, 'after')
    ->order_by('code', 'DESC')
    ->get('return_consignment');

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


	public function get_non_inv_code($limit = 100)
  {
    $rs = $this->db
    ->select('code')
    ->where('status', 1)
		->where('is_approve', 1)
    ->where('inv_code IS NULL', NULL, FALSE)
    ->get('return_consignment');

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


	public function update_inv($code, $doc_num)
  {
    return $this->db->set('inv_code', $doc_num)->where('code', $code)->update('return_consignment');
  }

}

 ?>
