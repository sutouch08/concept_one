<?php
class Order_down_payment_model extends CI_Model
{
  private $tb = "order_down_payment";
  private $td = "order_down_payment_details";

  public function __construct()
  {
    parent::__construct();
  }

  public function get_details($code)
  {
    $rs = $this->db->where('down_payment_code', $code)->get($this->td);

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function get_details_by_target($targetRef)
  {
    $rs = $this->db
    ->where('TargetRef', $targetRef)
    ->where('is_cancel', 0)
    ->get($this->td);

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function get_invoice_by_target($targetRef)
  {
    $rs = $this->db
    ->select('h.code, h.invoice_code, d.amount')
    ->from('order_down_payment_details AS d')
    ->join('order_down_payment AS h', 'd.down_payment_code = h.code', 'left')
    ->where('d.TargetRef', $targetRef)
    ->where('d.is_cancel', 0)
    ->where('h.status !=', 'D')
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
    ->select('dp.*, pm.name AS payment_name')
    ->from('order_down_payment AS dp')
    ->join('payment_method AS pm', 'dp.payment_code = pm.code', 'left')
    ->where('dp.code', $code)
    ->get();

    if($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return NULL;
  }

  public function get_by_id($id)
  {
    $rs = $this->db->where('id', $id)->get($this->tb);

    if($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return NULL;
  }


  //--- ดึงบิลทั้งหมดที่ถูกตัดยอดไปตามเอกสารตัดยอดที่ระบุ (ใช้สำหรับ rollback สถานะบิล เมื่อยกเลิก บิล)
  public function get_by_ref_code($ref_code)
  {
    $rs = $this->db->where('ref_code', $ref_code)->get($this->tb);

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function get_by_reference($reference)
  {
    $rs = $this->db->where('reference', $reference)->where('status !=', 'D')->get($this->tb);

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function get_sum_amount_by_reference($reference)
  {
    $rs = $this->db
    ->select_sum('amount')
    ->where('reference', $reference)
    ->where('status', 'O')
    ->get($this->tb);

    if($rs->num_rows() == 1)
    {
      return $rs->row()->amount;
    }

    return 0.00;
  }


  public function get_payments_logs_by_role($code, $role)
  {
    $rs = $this->db
    ->select('pm.*, ac.sapAcctCode')
    ->from('order_pos_payment AS pm')
    ->join('bank_account AS ac', 'pm.acc_id = ac.id', 'left')
    ->where('pm.code', $code)
    ->where('payment_role', $role)
    ->get();

    if($rs->num_rows() > 0)
    {
      return $rs->row();
    }

    return NULL;
  }

  public function get_sum_role_amount_by_round_id($pos_id, $role, $round_id)
  {
    $rs = $this->db
    ->select_sum('pm.amount')
    ->from('order_pos_payment AS pm')
    ->join('order_down_payment AS ps', 'pm.code = ps.code', 'left')
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


  public function update_by_code($code, array $ds = array())
  {
    if( ! empty($ds))
    {
      return $this->db->where('code', $code)->update($this->tb, $ds);
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

  public function update_by_reference($so_code, array $ds = array())
  {
    if( ! empty($ds))
    {
      return $this->db->where('reference', $so_code)->update($this->tb, $ds);
    }

    return FALSE;
  }


  public function close_by_reference($so_code, array $ds = array())
  {
    if( ! empty($ds))
    {
      return $this->db->where('reference', $so_code)->where('status', 'O')->update($this->tb, $ds);
    }

    return FALSE;
  }



  public function count_rows($ds = array())
  {
    if( ! empty($ds['shop_id']) && $ds['shop_id'] != "all")
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

    if( ! empty($ds['reference']))
    {
      $this->db->like('reference', $ds['reference']);
    }

    if( ! empty($ds['bill_code']))
    {
      $this->db->like('ref_code', $ds['bill_code']);
    }

    if( ! empty($ds['status']) && $ds['status'] != 'all')
    {
      $this->db->where('status', $ds['status']);
    }

    if( isset($ds['is_exported']) && $ds['is_exported'] != 'all')
    {
      $this->db->where('is_interface', 1)->where('is_exported', $ds['is_exported']);
    }

    if( ! empty($ds['payment']) && $ds['payment'] != 'all')
    {
      $this->db->where('payment_code', $ds['payment']);
    }

    if( isset($ds['has_slip']) && $ds['has_slip'] != 'all')
    {
      if($ds['has_slip'] == 'Y')
      {
        $this->db->where('image_path IS NOT NULL', NULL, FALSE);
      }
      else
      {
        $this->db->where('image_path IS NULL', NULL, FALSE);
      }
    }

    if( ! empty($ds['from_date']))
    {
      $this->db->where('date_add >=', from_date($ds['from_date']));
    }

    if( ! empty($ds['to_date']))
    {
      $this->db->where('date_add <=', to_date($ds['to_date']));
    }

    return $this->db->count_all_results($this->tb);
  }

  public function get_list(array $ds = array(), $perpage = 20, $offset = 0)
  {
    $this->db->select('dp.*')
    ->select('sh.code AS shop_code, sh.name AS shop_name')
    ->select('pos.code AS pos_code, pos.name AS pos_name')
    ->select('pm.code AS payment_code, pm.name AS payment_name')
    ->from('order_down_payment AS dp')
    ->join('shop AS sh', 'dp.shop_id = sh.id', 'left')
    ->join('shop_pos AS pos', 'dp.pos_id = pos.id', 'left')
    ->join('payment_method AS pm', 'dp.payment_code = pm.code', 'left');

    if( ! empty($ds['shop_id']) && $ds['shop_id'] != "all")
    {
      $this->db->where('dp.shop_id', $ds['shop_id']);
    }

    if( ! empty($ds['pos_id']) && $ds['pos_id'] != 'all')
    {
      $this->db->where('dp.pos_id', $ds['pos_id']);
    }

    if( ! empty($ds['code']))
    {
      $this->db->like('dp.code', $ds['code']);
    }

    if( ! empty($ds['reference']))
    {
      $this->db->like('dp.reference', $ds['reference']);
    }

    if( ! empty($ds['order_code']))
    {
      $this->db->like('dp.reference', $ds['order_code']);
    }

    if( ! empty($ds['bill_code']))
    {
      $this->db->like('dp.ref_code', $ds['bill_code']);
    }

    if( ! empty($ds['status']) && $ds['status'] != 'all')
    {
      $this->db->where('dp.status', $ds['status']);
    }

    if( isset($ds['is_exported']) && $ds['is_exported'] != 'all')
    {
      $this->db->where('dp.is_interface', 1)->where('dp.is_exported', $ds['is_exported']);
    }

    if( ! empty($ds['payment']) && $ds['payment'] != 'all')
    {
      $this->db->where('dp.payment_code', $ds['payment']);
    }

    if( isset($ds['has_slip']) && $ds['has_slip'] != 'all')
    {
      if($ds['has_slip'] == 'Y')
      {
        $this->db->where('dp.image_path IS NOT NULL', NULL, FALSE);
      }
      else
      {
        $this->db->where('dp.image_path IS NULL', NULL, FALSE);
      }
    }

    if( ! empty($ds['from_date']))
    {
      $this->db->where('dp.date_add >=', from_date($ds['from_date']));
    }

    if( ! empty($ds['to_date']))
    {
      $this->db->where('dp.date_add <=', to_date($ds['to_date']));
    }

    $rs = $this->db
    ->order_by('dp.code', 'DESC')
    ->limit($perpage, $offset)
    ->get();

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function get_export_data(array $ds = array())
  {
    $this->db->select('dp.*')
    ->select('ac.sapAcctCode')
    ->from('order_down_payment AS dp')
    ->join('bank_account AS ac', 'dp.acc_id = ac.id', 'left')
    ->where('status !=', 'D');

    if( ! empty($ds['shop_id']) && $ds['shop_id'] != "all")
    {
      $this->db->where('dp.shop_id', $ds['shop_id']);
    }

    if( ! empty($ds['pos_id']) && $ds['pos_id'] != 'all')
    {
      $this->db->where('dp.pos_id', $ds['pos_id']);
    }

    if( ! empty($ds['code']))
    {
      $this->db->like('dp.code', $ds['code']);
    }

    if( ! empty($ds['order_code']))
    {
      $this->db->like('dp.reference', $ds['order_code']);
    }

    if( ! empty($ds['bill_code']))
    {
      $this->db->like('dp.ref_code', $ds['bill_code']);
    }

    if( ! empty($ds['payment']) && $ds['payment'] != 'all')
    {
      $this->db->where('dp.payment_code', $ds['payment']);
    }

    if( ! empty($ds['from_date']))
    {
      $this->db->where('dp.date_add >=', from_date($ds['from_date']));
    }

    if( ! empty($ds['to_date']))
    {
      $this->db->where('dp.date_add <=', to_date($ds['to_date']));
    }

    $rs = $this->db->order_by('dp.code', 'DESC')->get();

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

  public function get_sap_doc_num($code)
  {
    $rs = $this->ms
    ->select('DocNum')
    ->where('U_ECOMNO', $code)
    ->where('CANCELED', 'N')
    ->get('ORCT');

    if($rs->num_rows() > 0)
    {
      return $rs->row()->DocNum;
    }

    return NULL;
  }

  public function get_middle_exists_data($code)
  {
    $rs = $this->mc
    ->select('DocEntry, DocNum, U_ECOMNO')
    ->where('U_ECOMNO', $code)
    ->group_start()
    ->where('F_Sap !=', 'Y')
    ->or_where('F_Sap IS NULL', NULL, FALSE)
    ->group_end()
    ->get('ORCT');

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function drop_middle_exists_data($code)
  {
    $sc = TRUE;

    $rows = $this->get_middle_exists_data($code);

    if( ! empty($rows))
    {
      $this->mc->trans_begin();

      foreach($rows as $rs)
      {
        if($this->mc->where('DocNum', $rs->DocEntry)->delete('RCT3'))
        {
          if( ! $this->mc->where('DocEntry', $rs->DocEntry)->delete('ORCT'))
          {
            $sc = FALSE;
          }
        }
        else
        {
          $sc = FALSE;
        }
      }

      if($sc === TRUE)
      {
        $this->mc->trans_commit();
      }
      else
      {
        $this->mc->trans_rollback();
      }
    }

    return $sc;
  }


  public function add_sap_doc(array $ds = array())
  {
    if( ! empty($ds))
    {
      if($this->mc->insert('ORCT', $ds))
      {
        return $this->mc->insert_id();
      }
    }

    return FALSE;
  }


  public function add_sap_card_row(array $ds = array())
  {
    if( ! empty($ds))
    {
      return $this->mc->insert('RCT3', $ds);
    }

    return FALSE;
  }


  public function get_sync_list($limit = 100)
  {
    $rs = $this->db
    ->select('code')
    ->where('status !=', 'D')
    ->where('is_interface', 1)
    ->where('is_exported', 1)
    ->where('DocNum IS NULL', NULL, FALSE)
    ->limit($limit)
    ->get($this->tb);

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }

} //--- end class
 ?>
