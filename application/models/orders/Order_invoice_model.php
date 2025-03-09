<?php
  class Order_invoice_model extends CI_Model
  {
    private $tb = "invoice";
    private $td = "invoice_details";
    private $bc = "bookcode";

    public function __construct()
    {
      parent::__construct();
    }

    public function count_rows(array $ds = array())
    {
      if( ! empty($ds['code']))
      {
        $this->db->like('code', $ds['code']);
      }

      if( ! empty($ds['bookcode']) && $ds['bookcode'] != 'all')
      {
        $this->db->where('bookcode', $ds['bookcode']);
      }

      if( ! empty($ds['customer']))
      {
        $this->db
        ->group_start()
        ->like('CardCode', $ds['customer'])
        ->or_like('CardName', $ds['customer'])
        ->or_like('NumAtCard', $ds['customer'])
        ->group_end();
      }

      if( ! empty($ds['reference']))
      {
        $this->db->like('baseRef', $ds['reference']);
      }

      if( ! empty($ds['so_code']))
      {
        $this->db->like('so_code', $ds['so_code']);
      }

      if( ! empty($ds['status']) && $ds['status'] != 'all')
      {
        $this->db->where('status', $ds['status']);
      }

      if( ! empty($ds['tax_status']) && $ds['tax_status'] != 'all')
      {
        $this->db->where('TaxStatus', $ds['tax_status']);
      }

      if( ! empty($ds['is_export']) && $ds['is_export'] != 'all')
      {
        $this->db->where('isExported', $ds['is_export']);
      }

      if( ! empty($ds['sale_id']) && $ds['sale_id'] != 'all')
      {
        $this->db->where('SlpCode', $ds['sale_id']);
      }

      if( ! empty($ds['user']) && $ds['user'] != 'all')
      {
        $this->db->where('user', $ds['user']);
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

      if( ! empty($ds['bookcode']) && $ds['bookcode'] != 'all')
      {
        $this->db->where('bookcode', $ds['bookcode']);
      }

      if( ! empty($ds['customer']))
      {
        $this->db
        ->group_start()
        ->like('CardCode', $ds['customer'])
        ->or_like('CardName', $ds['customer'])
        ->or_like('NumAtCard', $ds['customer'])
        ->group_end();
      }

      if( ! empty($ds['reference']))
      {
        $this->db->like('baseRef', $ds['reference']);
      }

      if( ! empty($ds['so_code']))
      {
        $this->db->like('so_code', $ds['so_code']);
      }

      if( ! empty($ds['status']) && $ds['status'] != 'all')
      {
        $this->db->where('status', $ds['status']);
      }

      if( ! empty($ds['tax_status']) && $ds['tax_status'] != 'all')
      {
        $this->db->where('TaxStatus', $ds['tax_status']);
      }

      if( ! empty($ds['is_export']) && $ds['is_export'] != 'all')
      {
        $this->db->where('isExported', $ds['is_export']);
      }

      if( ! empty($ds['sale_id']) && $ds['sale_id'] != 'all')
      {
        $this->db->where('SlpCode', $ds['sale_id']);
      }

      if( ! empty($ds['user']) && $ds['user'] != 'all')
      {
        $this->db->where('user', $ds['user']);
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
      ->order_by('id', 'DESC')
      ->order_by('code', 'ASC')
      ->limit($perpage, $offset)
      ->get($this->tb);

      if($rs->num_rows() > 0)
      {
        return $rs->result();
      }

      return NULL;
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


    public function get_by_code($code)
    {
      $rs = $this->db->where('code', $code)->get($this->tb);

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


    public function get_details_by_code($code)
    {
      $rs = $this->db->where('invoice_code', $code)->get($this->td);

      if($rs->num_rows() > 0)
      {
        return $rs->result();
      }

      return NULL;
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


    public function update($code, array $ds = array())
    {
      if( ! empty($ds))
      {
        return $this->db->where('code', $code)->update($this->tb, $ds);
      }

      return  FALSE;
    }

    public function update_by_code($code, array $ds = array())
    {
      if( ! empty($ds))
      {
        return $this->db->where('code', $code)->update($this->tb, $ds);
      }

      return  FALSE;
    }


    public function update_by_id($id, array $ds = array())
    {
      if( ! empty($ds))
      {
        return $this->db->where('id', $id)->update($this->tb, $ds);
      }

      return  FALSE;
    }


    public function update_detail($id, array $ds = array())
    {
      if( ! empty($ds))
      {
        return $this->db->where('id', $id)->update($this->td, $ds);
      }

      return  FALSE;
    }


    public function update_details($code, array $ds = array())
    {
      if( ! empty($ds))
      {
        return $this->db->where('invoice_code', $code)->update($this->td, $ds);
      }

      return  FALSE;
    }


    public function update_details_by_id($id, array $ds = array())
    {
      if( ! empty($ds))
      {
        return $this->db->where('invoice_id', $id)->update($this->td, $ds);
      }

      return  FALSE;
    }


    public function unclose($code)
    {
      return $this->db->set('LineStatus', 'O')->where('invoice_code', $code)->update($this->td);
    }


    public function get_order_non_inv_code($limit = 100)
    {
      $sync_days = getConfig('INVOICE_SYNC_DAYS');
      $sync_days = empty($sync_days) ? 30 : $sync_days;
      $end_date = date('Y-m-d H:i:s', strtotime("-{$sync_days} days"));

      $rs = $this->db
      ->select('code')
      ->where('status', 'O')
      ->where('isExported', 'Y')
      ->where('DocNum IS NULL', NULL, FALSE)
      ->where('shipped_date >', $end_date)
      ->order_by('last_sync', 'ASC')
      ->limit($limit)
      ->get($this->tb);

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
      ->get('OINV');

      if($rs->num_rows() > 0)
      {
        return $rs->row()->DocNum;
      }

      return NULL;
    }


    public function get_sap_invoice($code)
    {
      $rs = $this->ms
      ->select('DocEntry, DocStatus')
      ->where('U_ECOMNO', $code)
      ->where('CANCELED', 'N')
      ->get('OINV');
      if($rs->num_rows() > 0)
      {
        return $rs->result();
      }

      return NULL;
    }


		public function exists_sap_invoice($code)
    {
      $rs = $this->ms
      ->where('U_ECOMNO', $code)
      ->where('CANCELED', 'N')
      ->count_all_results('OINV');
      if($rs > 0)
      {
        return TRUE;
      }

      return FALSE;
    }

    public function add_sap_invoice(array $ds = array())
    {
      $rs = $this->mc->insert('OINV', $ds);
      if($rs)
      {
        return $this->mc->insert_id();
      }

      return FALSE;
    }


    public function update_sap_invoice($docEntry, $ds)
    {
      return $this->mc->where('DocEntry', $docEntry)->update('OINV', $ds);
    }


    public function add_invoice_row(array $ds = array())
    {
      return $this->mc->insert('INV1', $ds);
    }


    public function add_dpm_drawn(array $ds = array())
    {
      return $this->mc->insert('INV9', $ds);
    }


    public function is_doc_exists($code)
    {
      $rs = $this->mc->select('U_ECOMNO')->where('U_ECOMNO', $code)->get('OINV');
      if($rs->num_rows() > 0)
      {
        return TRUE;
      }

      return FALSE;
    }


    public function get_middle_invoice($code)
    {
      $rs = $this->mc
      ->select('DocEntry')
      ->where('U_ECOMNO', $code)
      ->group_start()
      ->where('F_Sap !=', 'Y')
      ->or_where('F_Sap IS NULL', NULL, FALSE)
      ->group_end()
      ->get('OINV');

      if($rs->num_rows() > 0)
      {
        return $rs->result();
      }

      return NULL;
    }


    public function sap_exists_details($code)
    {
      $rs = $this->mc->select('LineNum')->where('U_ECOMNO', $code)->get('INV1');
      if($rs->num_rows() > 0)
      {
        return TRUE;
      }

      return FALSE;
    }


    //--- ลบรายการที่ค้างใน middle ที่ยังไม่ได้เอาเข้า SAP ออก
    public function drop_middle_exits_data($docEntry)
    {
      $this->mc->trans_start();
      $this->mc->where('DocEntry', $docEntry)->delete('INV9');
      $this->mc->where('DocEntry', $docEntry)->delete('INV1');
      $this->mc->where('DocEntry', $docEntry)->delete('OINV');
      $this->mc->trans_complete();
      return $this->mc->trans_status();
    }


    public function drop_sap_exists_details($code)
    {
      return $this->mc->where('U_ECOMNO', $code)->delete('INV1');
    }




    public function update_invoice_row($DocEntry, $line, $ds = array())
    {

      return $this->mc->where('DocEntry', $DocEntry)->where('LineNum', $line)->update('INV1', $ds);
    }



    public function getDocEntry($code)
    {
      $rs = $this->mc->select_max('DocEntry')->where('U_ECOMNO', $code)->get('OINV');
      if($rs->num_rows() === 1)
      {
        return $rs->row()->DocEntry;
      }

      return FALSE;
    }


    public function get_max_code($pre)
    {
      // $pre = DN-2301
      $rs = $this->db
      ->select_max('code')
      ->like('code', $pre)
      ->order_by('code', 'DESC')
      ->get($this->tb);

      if($rs->num_rows() === 1)
      {
        return $rs->row()->code;
      }

      return NULL;
    }

  } //--- end class

 ?>
