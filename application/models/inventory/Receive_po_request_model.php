<?php
class Receive_po_request_model extends CI_Model
{
  public function __construct()
  {
    parent::__construct();
  }



  public function add(array $ds = array())
  {
    if(!empty($ds))
    {
      return $this->db->insert('receive_product_request', $ds);
    }

    return FALSE;
  }



  public function update($code, array $ds = array())
  {
    if(!empty($ds))
    {
      return $this->db->where('code', $code)->update('receive_product_request', $ds);
    }

    return FALSE;
  }


  public function add_detail(array $ds = array())
  {
    if(!empty($ds))
    {
      return $this->db->insert('receive_product_request_detail', $ds);
    }

    return FALSE;
  }



  public function get($code)
  {
    $rs = $this->db->where('code', $code)->get('receive_product_request');
    if($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return FALSE;
  }



  public function get_details($code)
  {
    $rs = $this->db->where('receive_code', $code)->get('receive_product_request_detail');
    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return FALSE;
  }



  public function drop_details($code)
  {
    return $this->db->where('receive_code', $code)->delete('receive_product_request_detail');
  }



  public function cancle_details($code)
  {
    return $this->db->set('is_cancle', 1)->where('receive_code', $code)->update('receive_product_request_detail');
  }



  public function get_po_details($po_code)
  {
    $rs = $this->ms
    ->select('POR1.DocEntry, POR1.LineNum, POR1.ItemCode, POR1.Dscription, POR1.Quantity, POR1.LineStatus, POR1.OpenQty, POR1.PriceAfVAT AS price')
    ->select('POR1.Currency, POR1.Rate, POR1.VatGroup, POR1.VatPrcnt')
    ->from('POR1')
    ->join('OPOR', 'POR1.DocEntry = OPOR.DocEntry', 'left')
    ->where('OPOR.DocNum', $po_code)
    ->where('OPOR.DocStatus', 'O')
    ->where('POR1.LineStatus', 'O')
    ->get();

    if(!empty($rs))
    {
      return $rs->result();
    }

    return FALSE;
  }


  public function get_backlogs($po_code, $item_code)
  {
    $rs = $this->ms
    ->select('POR1.Quantity')
    ->from('POR1')
    ->join('OPOR', 'POR1.DocEntry = OPOR.DocEntry', 'left')
    ->where('OPOR.DocNum', $po_code)
    ->where('OPOR.DocStatus', 'O')
    ->where('POR1.LineStatus', 'O')
    ->where('POR1.ItemCode', $item_code)
    ->get();

    if($rs->num_rows() === 1)
    {
      return $rs->row()->Quantity;
    }

    return 0;
  }


  public function get_sum_qty($code)
  {
    $rs = $this->db->select_sum('qty', 'qty')
    ->where('receive_code', $code)
    ->get('receive_product_request_detail');

    return intval($rs->row()->qty);
  }



  public function get_sum_amount($code)
  {
    $rs = $this->db->select_sum('amount')->where('receive_code', $code)->get('receive_product_request_detail');
    return $rs->row()->amount === NULL ? 0.00 : $rs->row()->amount;
  }




  public function set_status($code, $status)
  {
    return $this->db->set('status', $status)->where('code', $code)->update('receive_product_request');
  }



  public function count_rows(array $ds = array())
  {
    //---- เลขที่เอกสาร
    if($ds['code'] != '')
    {
      $this->db->like('code', $ds['code']);
    }

    //--- ใบสั่งซื้อ
    if($ds['po'] != '')
    {
      $this->db->like('po_code', $ds['po']);
    }

    //---- invoice
    if($ds['invoice'] != '')
    {
      $this->db->like('invoice_code', $ds['invoice']);
    }

    //--- vendor
    if($ds['vendor'] != '')
    {
      $this->db->group_start();
      $this->db->like('vendor_code', $ds['vendor']);
      $this->db->or_like('vendor_name', $ds['vendor']);
      $this->db->group_end();
    }

    if($ds['from_date'] != '' && $ds['to_date'] != '')
    {
      $this->db->where('date_add >=', from_date($ds['from_date']));
      $this->db->where('date_add <=', to_date($ds['to_date']));
    }


    if($ds['status'] !== 'all')
    {
      $this->db->where('status', $ds['status']);
    }
    else
    {
      if($ds['isApprove'] !== 'all')
      {
        $this->db->where('status !=', 2);
      }
    }

    if($ds['valid'] !== 'all')
    {
      $this->db->where('valid', $ds['valid']);
    }

    if($ds['isApprove'] !== 'all')
    {
      $this->db->where('is_approve', $ds['isApprove']);
    }

    return $this->db->count_all_results('receive_product_request');
  }





  public function get_data(array $ds = array(), $perpage = NULL, $offset = NULL)
  {
    //---- เลขที่เอกสาร
    if($ds['code'] != '')
    {
      $this->db->like('code', $ds['code']);
    }

    //--- ใบสั่งซื้อ
    if($ds['po'] != '')
    {
      $this->db->like('po_code', $ds['po']);
    }

    //---- invoice
    if($ds['invoice'] != '')
    {
      $this->db->like('invoice_code', $ds['invoice']);
    }


    //--- vendor
    if($ds['vendor'] != '')
    {
      $this->db->group_start();
      $this->db->like('vendor_code', $ds['vendor']);
      $this->db->or_like('vendor_name', $ds['vendor']);
      $this->db->group_end();
    }


    if($ds['from_date'] != '' && $ds['to_date'] != '')
    {
      $this->db->where('date_add >=', from_date($ds['from_date']));
      $this->db->where('date_add <=', to_date($ds['to_date']));
    }

    if($ds['status'] !== 'all')
    {
      $this->db->where('status', $ds['status']);
    }
    else
    {
      if($ds['isApprove'] !== 'all')
      {
        $this->db->where('status !=', 2);
      }
    }

    if($ds['isApprove'] !== 'all')
    {
      $this->db->where('is_approve', $ds['isApprove']);
    }

    if($ds['valid'] !== 'all')
    {
      $this->db->where('valid', $ds['valid']);
    }

    $this->db->order_by('date_add', 'DESC');
    $this->db->order_by('code', 'DESC');

    if(!empty($perpage))
    {
      $offset = empty($offset) ? 0 : $offset;
      $this->db->limit($perpage, $offset);
    }

    $rs = $this->db->get('receive_product_request');

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }



  public function count_un_approve_rows()
  {
    $this->db
    ->where('status', 1)
    ->where('is_approve', 0);

    return $this->db->count_all_results('receive_product_request');
  }



  public function get_un_approve_list($limit = NULL)
  {
    $this->db
    ->select('code, vendor_name')
    ->where('status', 1)
    ->where('is_approve', 0);

    if(! empty($limit))
    {
      $this->db->limit($limit);
    }

    $rs = $this->db->get('receive_product_request');

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return FALSE;
  }


  public function get_max_code($code)
  {
    $rs = $this->db
    ->select_max('code')
    ->like('code', $code)
    ->order_by('code', 'DESC')
    ->get('receive_product_request');

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


  public function get_vender_by_request_code($code)
  {
    $rs = $this->db->select('vendor_code, vendor_name')->where('code', $code)->get('receive_product_request');

    if($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return FALSE;
  }


  public function is_exists($code)
  {
    $rs = $this->db->select('status')->where('code', $code)->get('receive_product_request');
    if($rs->num_rows() > 0)
    {
      return TRUE;
    }

    return FALSE;
  }


  public function set_approve($code, $value)
  {
    return $this->db->set('is_approve', $value)->where('code', $code)->update('receive_product_request');
  }


  public function update_receive_code($code, $receive_code)
  {
    $arr = array(
      'valid' => 1,
      'receive_code' => $receive_code
    );

    return $this->db->where('code', $code)->update('receive_product_request', $arr);
  }

}

 ?>
