<?php
class Receive_po_by_doc_model extends CI_Model
{
  public function __construct()
  {
    parent::__construct();
  }



  public function get_data(array $ds = array())
  {
    if(!empty($ds))
    {
      $this->db
      ->select('rp.code, rp.po_code, rp.invoice_code, rp.inv_code')
      ->select('rp.vendor_code, rp.vendor_name, rp.date_add')
      ->select_sum('rd.qty')
      ->select_sum('rd.amount')
      ->from('receive_product_detail AS rd')
      ->join('receive_product AS rp', 'rd.receive_code = rp.code', 'left')
      ->where('rp.date_add >=', $ds['fromDate'])
      ->where('rp.date_add <=', $ds['toDate'])
      ->where('rp.inv_code IS NOT NULL', NULL, FALSE)
      ->where('rp.status', 1)
      ->where('rd.is_cancle', 0);

      if(empty($ds['allDoc']))
      {
        if(!empty($ds['docFrom']) && !empty($ds['docTo']))
        {
          $this->db->where('rp.code >=', $ds['docFrom']);
          $this->db->where('rp.code <=', $ds['docTo']);
        }
      }

      if(empty($ds['allVendor']))
      {
        if(!empty($ds['vendorFrom']) && !empty($ds['vendorTo']))
        {
          $this->db->where('rp.vendor_code >=', $ds['vendorFrom']);
          $this->db->where('rp.vendor_code <=', $ds['vendorTo']);
        }
      }

      if(empty($ds['allPO']))
      {
        if(!empty($ds['poFrom']) && !empty($ds['poTo']))
        {
          $this->db->where('rp.po_code >=', $ds['poFrom']);
          $this->db->where('rp.po_code <=', $ds['poTo']);
        }
      }


      if(empty($ds['allInvoice']))
      {
        if(!empty($ds['invoiceFrom']) && !empty($ds['invoiceTo']))
        {
          $this->db->where('rp.invoice_code >=', $ds['invoiceFrom']);
          $this->db->where('rp.invoice_code <=', $ds['invoiceTo']);
        }
      }

      $this->db->group_by('rd.receive_code');
      $this->db->order_by('rp.code', 'ASC');
      $rs = $this->db->get();

      if($rs->num_rows() > 0)
      {
        return $rs->result();
      }

      return NULL;
    }

    return FALSE;
  }



} //--- end class
 ?>
