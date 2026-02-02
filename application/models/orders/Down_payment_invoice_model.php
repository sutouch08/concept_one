<?php
class Down_payment_invoice_model extends CI_Model
{
  private $tb = "down_payment_invoice";
  private $td = "down_payment_invoice_details";

  public function __construct()
  {
    parent::__construct();
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


  public function get($code)
  {
    $rs = $this->db->where('code', $code)->get($this->tb);

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


  public function get_invoice_by_base_ref($baseRef)
  {
    $rs = $this->db
    ->where('BaseRef', $baseRef)
    ->where('status !=', 'D')
    ->get($this->tb);

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  //---- ODPI
  public function get_sap_down_payments_by_array(array $codes = array())
  {
    if( ! empty($codes))
    {
      $rs = $this->ms
      ->select('DocEntry, DocNum, DocTotal, VatSum')
      ->where_in('U_ECOMNO', $codes)
      ->where('DocStatus', 'O')
      ->where('CANCELED', 'N')
      ->get('ODPI');

      if($rs->num_rows() > 0)
      {
        return $rs->result();
      }
    }

    return NULL;
  }


  public function get_down_payments_by_array(array $codes = array())
  {
    if( ! empty($codes))
    {
      $rs = $this->db
      ->select('id, code, DocTotal, VatSum')
      ->where_in('code', $codes)
      ->where('status !=', 'D')
      ->get($this->tb);

      if($rs->num_rows() > 0)
      {
        return $rs->result();
      }
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


  public function get_details($code)
  {
    $rs = $this->db->where('invoice_code', $code)->get($this->td);

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function get_details_by_id($invoice_id)
  {
    $rs = $this->db->where('invoice_id', $invoice_id)->get($this->td);

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function update($code, array $ds = array())
  {
    return $this->db->where('code', $code)->update($this->tb, $ds);
  }

  public function update_by_code($code, array $ds = array())
  {
    return $this->db->where('code', $code)->update($this->tb, $ds);
  }

  public function update_by_id($id, array $ds = array())
  {
    return $this->db->where('id', $id)->update($this->tb, $ds);
  }


  public function update_detail($id, array $ds = array())
  {
    return $this->db->where('id', $id)->update($this->td, $ds);
  }


  public function update_details($code, array $ds = array())
  {
    return $this->db->where('invoice_code', $code)->update($this->td, $ds);
  }


  public function update_details_by_id($id, array $ds = array())
  {
    return $this->db->where('invoice_id', $id)->update($this->td, $ds);
  }


  public function count_rows(array $ds = array())
  {
    if( ! empty($ds['code']))
    {
      $this->db->like('code', $ds['code']);
    }

    if( ! empty($ds['baseDpm']))
    {
      $this->db->like('BaseDpm', $ds['baseDpm']);
    }

    if( ! empty($ds['baseRef']))
    {
      $this->db->like('BaseRef', $ds['baseRef']);
    }

    if( ! empty($ds['customer']))
    {
      $this->db
      ->group_start()
      ->like('CardCode', $ds['customer'])
      ->or_like('CardName', $ds['customer'])
      ->group_end();
    }

    if(isset($ds['sale_id']) && $ds['sale_id'] != 'all')
    {
      $this->db->where('SlpCode', $ds['sale_id']);
    }

    if(isset($ds['user']) && $ds['user'] != 'all')
    {
      $this->db->where('user', $ds['user']);
    }

    if(isset($ds['status']) && $ds['status'] != 'all')
    {
      $this->db->where('status', $ds['status']);
    }

    if(isset($ds['is_export']) && $ds['is_export'] != 'all')
    {
      $this->db->where('isExported', $ds['is_export']);
    }

    if( ! empty($ds['from_date']))
    {
      $this->db->where('DocDate >=', from_date($ds['from_date']));
    }

    if( ! empty($ds['to_date']))
    {
      $this->db->where('DocDate <=', to_date($ds['to_date']));
    }

    return $this->db->count_all_results($this->tb);
  }


  public function get_list(array $ds = array(), $perpage = 20, $offset = 0)
  {
    if( ! empty($ds['code']))
    {
      $this->db->like('code', $ds['code']);
    }

    if( ! empty($ds['baseDpm']))
    {
      $this->db->like('BaseDpm', $ds['baseDpm']);
    }

    if( ! empty($ds['baseRef']))
    {
      $this->db->like('BaseRef', $ds['baseRef']);
    }

    if( ! empty($ds['customer']))
    {
      $this->db
      ->group_start()
      ->like('CardCode', $ds['customer'])
      ->or_like('CardName', $ds['customer'])
      ->group_end();
    }

    if(isset($ds['sale_id']) && $ds['sale_id'] != 'all')
    {
      $this->db->where('SlpCode', $ds['sale_id']);
    }

    if(isset($ds['user']) && $ds['user'] != 'all')
    {
      $this->db->where('user', $ds['user']);
    }

    if(isset($ds['status']) && $ds['status'] != 'all')
    {
      $this->db->where('status', $ds['status']);
    }

    if(isset($ds['is_export']) && $ds['is_export'] != 'all')
    {
      $this->db->where('isExported', $ds['is_export']);
    }

    if( ! empty($ds['from_date']))
    {
      $this->db->where('DocDate >=', from_date($ds['from_date']));
    }

    if( ! empty($ds['to_date']))
    {
      $this->db->where('DocDate <=', to_date($ds['to_date']));
    }

    $rs = $this->db
    ->order_by('code', 'DESC')
    ->limit($perpage, $offset)
    ->get($this->tb);

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
    ->get('ODPI');

    if($rs->num_rows() > 0)
    {
      return $rs->row()->DocNum;
    }

    return NULL;
  }

  public function get_middle_exists_data($code)
  {
    $rs = $this->mc
    ->select('DocEntry')
    ->where('U_ECOMNO', $code)
    ->group_start()
    ->where('F_Sap !=', 'Y')
    ->or_where('F_Sap IS NULL', NULL, FALSE)
    ->group_end()
    ->get('ODPI');

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }

  //--- ลบรายการที่ค้างใน middle ที่ยังไม่ได้เอาเข้า SAP ออก
  public function drop_middle_exits_data($code)
  {
    $sc = TRUE;

    $rs = $this->mc
    ->select('DocEntry')
    ->where('U_ECOMNO', $code)
    ->group_start()
    ->where('F_Sap !=', 'Y')
    ->or_where('F_Sap IS NULL', NULL, FALSE)
    ->group_end()
    ->get('ODPI');

    if($rs->num_rows() > 0)
    {
      $this->mc->trans_begin();

      foreach($rs->result() as $row)
      {
        if($this->mc->where('DocEntry', $row->DocEntry)->delete('DPI1'))
        {
          if( ! $this->mc->where('DocEntry', $row->DocEntry)->delete('ODPI'))
          {
            $sc = FALSE;
          }
        }
        else
        {
          $sc = FALSE;
        }

        if($sc === FALSE)
        {
          break;
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
      if($this->mc->insert('ODPI', $ds))
      {
        return $this->mc->insert_id();
      }
    }

    return FALSE;
  }


  public function add_sap_row(array $ds = array())
  {
    if( ! empty($ds))
    {
      return $this->mc->insert('DPI1', $ds);
    }

    return FALSE;
  }


  public function get_sap_doc($code)
  {
    $rs = $this->ms
    ->select('DocEntry, DocNum')
    ->where('U_ECOMNO', $code)
    ->where('CANCELED', 'N')
    ->get('ODPI');

    if($rs->num_rows() > 0)
    {
      return $rs->row();
    }

    return NULL;
  }


  public function get_sync_list($limit = 100)
  {
    $rs = $this->db
    ->select('code')
    ->where('status !=', 'D')
    ->where('isExported', 'Y')
    ->where('DocEntry IS NULL', NULL, FALSE)
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
