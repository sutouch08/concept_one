<?php
class Customer_address_model extends CI_Model
{
  public function __construct()
  {
    parent::__construct();
  }

  public function get_customer_bill_to_address($customer_code)
  {
    $rs = $this->db->where('customer_code', $customer_code)->get('address_bill_to');
    if($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return FALSE;
  }


  public function get_customer_ship_to_address($id)
  {
    $rs = $this->db->where('id', $id)->get('address_ship_to');
    if($rs->num_rows() > 0)
    {
      return $rs->row();
    }

    return FALSE;
  }


  public function get_bill_to_address($customer_code)
  {
    $rs = $this->ms
    ->select('CardCode AS customer_code, Address AS address_code, Address2 AS branch_code, Address3 AS branch_name')
    ->select('Street AS address, Block AS sub_district, County AS district, City AS province, ZipCode AS postcode')
    ->where('AdresType', 'B')
    ->where('CardCode', $customer_code)
    ->get('CRD1');

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  // public function get_bill_to_address($customer_code)
  // {
  //   $rs = $this->db->where('customer_code', $customer_code)->get('address_bill_to');
  //
  //   if($rs->num_rows() > 0)
  //   {
  //     return $rs->row();
  //   }
  //
  //   return FALSE;
  // }


  public function get_ship_to_address($code)
  {
    $rs = $this->db->where('customer_code', $code)->get('address_ship_to');
    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return array();
  }


  public function add_bill_to(array $ds = array())
  {
    if(!empty($ds))
    {
      return $this->db->insert('address_bill_to', $ds);
    }

    return FALSE;
  }


  public function add_sap_bill_to(array $ds = array())
  {
    if(!empty($ds))
    {
      return $this->mc->insert('CRD1', $ds);
    }

    return FALSE;
  }



  public function update_bill_to($customer_code, array $ds = array())
  {
    if(!empty($ds))
    {
      return $this->db->where('customer_code', $customer_code)->update('address_bill_to', $ds);
    }

    return FALSE;
  }



  public function update_sap_bill_to($code, $address, array $ds = array())
  {
    if(!empty($ds))
    {
      return $this->mc->where('CardCode', $code)->where('Address', $address)->where('AdresType', 'B')->update('CRD1', $ds);
    }

    return FALSE;
  }






  //----- Ship To
  public function add_sap_ship_to(array $ds = array())
  {
    if(!empty($ds))
    {
      return $this->mc->insert('CRD1', $ds);
    }

    return FALSE;
  }



  public function update_sap_ship_to($code, $address, array $ds = array())
  {
    if(!empty($ds))
    {
      return $this->mc->where('CardCode', $code)->where('Address', $address)->where('AdresType', 'S')->update('CRD1', $ds);
    }

    return FALSE;
  }



  public function get_max_line_num($code, $type = 'B')
  {
    $rs = $this->mc->select_max('LineNum')->where('CardCode', $code)->where('AdresType', $type)->get('CRD1');
    return $rs->row()->LineNum;
  }


  public function is_sap_address_exists($code, $address, $type = 'B')
  {
    $rs = $this->mc
    ->where('Address', $address)
    ->where('CardCode', $code)
    ->where('AdresType', $type)
    ->get('CRD1');
    if($rs->num_rows() > 0)
    {
      return TRUE;
    }

    return FALSE;
  }


  public function get_max_code($code)
  {
    $qr = "SELECT MAX(address_code) AS code FROM address_ship_to WHERE code = '{$code}' ORDER BY address_code DESC";
    $rs = $this->db->query($qr);
    return $rs->row()->code;
  }



} //--- end class

 ?>
