<?php
class Order_pos_invoice_model extends CI_Model
{
  private $tb = "order_pos_invoice";
  private $td = "order_pos_invoice_detail";

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

    if( ! empty($ds['reference']))
    {
      $this->db->like('reference', $ds['reference']);
    }

    if( ! empty($ds['customer']))
    {
      $this->db->like('customer_name', $ds['customer']);
    }

    if( ! empty($ds['tax_id']))
    {
      $this->db->like('tax_id', $ds['tax_id']);
    }

    if( ! empty($ds['inv_code']))
    {
      $this->db->like('inv_code', $ds['inv_code']);
    }

    if($ds['shop_id'] != 'all')
    {
      $this->db->where('shop_id', $ds['shop_id']);
    }

    if($ds['pos_id'] != 'all')
    {
      $this->db->where('pos_id', $ds['pos_id']);
    }

    if($ds['warehouse'] != 'all')
    {
      $this->db->where('warehouse_code', $ds['warehouse']);
    }

    if($ds['status'] != 'all')
    {
      $this->db->where('status', $ds['status']);
    }

    if( ! empty($ds['from_date']))
    {
      $this->db->where('doc_date >=', from_date($ds['from_date']));
    }

    if( ! empty($ds['to_date']))
    {
      $this->db->where('doc_date <=', to_date($ds['to_date']));
    }

    return $this->db->count_all_results($this->tb);
  }


  public function get_list(array $ds = array(), $perpage = 20, $offset = 0)
  {
    if( ! empty($ds['code']))
    {
      $this->db->like('code', $ds['code']);
    }

    if( ! empty($ds['reference']))
    {
      $this->db->like('reference', $ds['reference']);
    }

    if( ! empty($ds['customer']))
    {
      $this->db->like('customer_name', $ds['customer']);
    }

    if( ! empty($ds['tax_id']))
    {
      $this->db->like('tax_id', $ds['tax_id']);
    }

    if( ! empty($ds['inv_code']))
    {
      $this->db->like('inv_code', $ds['inv_code']);
    }

    if($ds['shop_id'] != 'all')
    {
      $this->db->where('shop_id', $ds['shop_id']);
    }

    if($ds['pos_id'] != 'all')
    {
      $this->db->where('pos_id', $ds['pos_id']);
    }

    if($ds['warehouse'] != 'all')
    {
      $this->db->where('warehouse_code', $ds['warehouse']);
    }

    if($ds['status'] != 'all')
    {
      $this->db->where('status', $ds['status']);
    }

    if( ! empty($ds['from_date']))
    {
      $this->db->where('doc_date >=', from_date($ds['from_date']));
    }

    if( ! empty($ds['to_date']))
    {
      $this->db->where('doc_date <=', to_date($ds['to_date']));
    }

    $rs = $this->db
    ->order_by('doc_date', 'DESC')
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


  public function update_details_($code, array $ds = array())
  {
    if( ! empty($ds))
    {
      return $this->db->where('invlice_code', $code)->update($this->td, $ds);
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
    return $this->db->set('status', 'O')->where('invoice_code', $code)->update($this->td);
  }


  public function get_order_non_inv_code($limit = 100)
  {
    $rs = $this->db
    ->select('code')
    ->where('status', 'O')
    ->where('inv_code IS NULL', NULL, FALSE)
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
    ->get('ODLN');

    if($rs->num_rows() > 0)
    {
      return $rs->row()->DocNum;
    }

    return NULL;
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
