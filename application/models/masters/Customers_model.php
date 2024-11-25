<?php
class Customers_model extends CI_Model
{
  public function __construct()
  {
    parent::__construct();
  }



  public function add_sap_customer(array $ds = array())
  {
    if(!empty($ds))
    {
      return $this->mc->insert('OCRD', $ds);
    }

    return FALSE;
  }



  public function update_sap_customer($code, $ds = array())
  {
    if(!empty($ds))
    {
      return $this->mc->where('CardCode', $code)->update('OCRD', $ds);
    }

    return FALSE;
  }


  public function sap_customer_exists($code)
  {
    $rs = $this->mc->where('CardCode', $code)->get('OCRD');
    if($rs->num_rows() === 1)
    {
      return TRUE;
    }

    return FALSE;
  }




  public function get_credit($code)
  {
    $rs = $this->ms
    ->select('CreditLine, Balance, DNotesBal, OrdersBal')
    ->where('CardCode', $code)
    ->get('OCRD');
    if($rs->num_rows() === 1)
    {
      //$balance = $rs->row()->CreditLine - ($rs->row()->Balance + $rs->row()->DNotesBal + $rs->row()->OrdersBal);
      $balance = $rs->row()->CreditLine - ($rs->row()->Balance + $rs->row()->DNotesBal);
      return $balance;
    }

    return 0.00;
  }



  public function add(array $ds = array())
  {
    if(!empty($ds))
    {
      return  $this->db->insert('customers', $ds);
    }

    return FALSE;
  }



  public function update($code, array $ds = array())
  {
    if(!empty($ds))
    {
      $this->db->where('code', $code);
      return $this->db->update('customers', $ds);
    }

    return FALSE;
  }


  public function delete($code)
  {
    $rs = $this->db->where('code', $code)->delete('customers');
    if(!$rs)
    {
      return $this->db->error();
    }

    return TRUE;
  }


  public function get($code)
  {
    $rs = $this->db->where('code', $code)->get('customers');
    if($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return FALSE;
  }



  public function get_name($code)
  {
    $rs = $this->db->select('name')->where('code', $code)->get('customers');
    if($rs->num_rows() === 1)
    {
      return $rs->row()->name;
    }

    return NULL;
  }


  public function count_rows(array $ds = array())
  {
    if($ds['code'] != "" && $ds['code'] !== NULL)
    {
      $this->db
      ->group_start()
      ->like('code', $ds['code'])
      ->or_like('name', $ds['code'])
      ->group_end();
    }

    if($ds['group'] != 'all')
    {
      if($ds['group'] === "NULL")
      {
        $this->db->where('group_code IS NULL', NULL, FALSE);
      }
      else
      {
        $this->db->where('group_code', $ds['group']);
      }
    }

    if($ds['kind'] != "all")
    {
      if($ds['kind'] === "NULL")
      {
        $this->db->where('kind_code IS NULL', NULL, FALSE);
      }
      else
      {
        $this->db->where('kind_code', $ds['kind']);
      }
    }

    if($ds['type'] != "all")
    {
      if($ds['type'] === "NULL")
      {
        $this->db->where('type_code IS NULL', NULL, FALSE);
      }
      else
      {
        $this->db->where('type_code', $ds['type']);
      }

    }

    if($ds['class'] != "all")
    {
      if($ds['class'] === 'NULL')
      {
        $this->db->where('class_code IS NULL', NULL, FALSE);
      }
      else
      {
        $this->db->where('class_code', $ds['class']);
      }

    }

    if($ds['area'] != 'all')
    {
      if($ds['area'] === "NULL")
      {
        $this->db->where('area_code IS NULL', NULL, FALSE);
      }
      else
      {
        $this->db->where('area_code', $ds['area']);
      }
    }

    if($ds['status'] != 'all')
    {
      $this->db->where('active', $ds['status']);
    }

    return $this->db->count_all_results('customers');
  }



  public function get_list(array $ds = array(), $perpage = 20, $offset = 0)
  {
    $this->db
    ->select('cu.*, cg.name AS group, ck.name AS kind, ct.name AS type, cc.name AS class, ca.name AS area')
    ->from('customers AS cu')
    ->join('customer_group AS cg', 'cu.group_code = cg.code', 'left')
    ->join('customer_kind AS ck', 'cu.kind_code = ck.code', 'left')
    ->join('customer_type AS ct', 'cu.type_code = ct.code', 'left')
    ->join('customer_class AS cc', 'cu.class_code = cc.code', 'left')
    ->join('customer_area AS ca', 'cu.area_code = ca.code', 'left');

    if($ds['code'] != "" && $ds['code'] !== NULL)
    {
      $this->db
      ->group_start()
      ->like('cu.code', $ds['code'])
      ->or_like('cu.name', $ds['code'])
      ->group_end();
    }

    if($ds['group'] != 'all')
    {
      if($ds['group'] === "NULL")
      {
        $this->db->where('cu.group_code IS NULL', NULL, FALSE);
      }
      else
      {
        $this->db->where('cu.group_code', $ds['group']);
      }
    }

    if($ds['kind'] != "all")
    {
      if($ds['kind'] === "NULL")
      {
        $this->db->where('cu.kind_code IS NULL', NULL, FALSE);
      }
      else
      {
        $this->db->where('cu.kind_code', $ds['kind']);
      }
    }

    if($ds['type'] != "all")
    {
      if($ds['type'] === "NULL")
      {
        $this->db->where('cu.type_code IS NULL', NULL, FALSE);
      }
      else
      {
        $this->db->where('cu.type_code', $ds['type']);
      }

    }

    if($ds['class'] != "all")
    {
      if($ds['class'] === 'NULL')
      {
        $this->db->where('cu.class_code IS NULL', NULL, FALSE);
      }
      else
      {
        $this->db->where('cu.class_code', $ds['class']);
      }

    }

    if($ds['area'] != 'all')
    {
      if($ds['area'] === "NULL")
      {
        $this->db->where('cu.area_code IS NULL', NULL, FALSE);
      }
      else
      {
        $this->db->where('cu.area_code', $ds['area']);
      }
    }

    if($ds['status'] != 'all')
    {
      $this->db->where('cu.active', $ds['status']);
    }

    $rs = $this->db->order_by('cu.code', 'ASC')->limit($perpage, $offset)->get();

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function get_data($code = '', $name = '', $group = '', $kind = '', $type = '', $class = '', $area = '', $perpage = '', $offset = '')
  {
    if($code != '')
    {
      $this->db->group_start();
      $this->db->like('code', $code);
      $this->db->or_like('old_code', $code);
      $this->db->group_end();
    }

    if($name != '')
    {
      $this->db->like('name', $name);
    }


    if(!empty($group))
    {
      if($group === "NULL")
      {
        $this->db->where('group_code IS NULL', NULL, FALSE);
      }
      else
      {
        $this->db->where('group_code', $group);
      }

    }


    if(!empty($kind))
    {
      if($kind === "NULL")
      {
        $this->db->where('kind_code IS NULL', NULL, FALSE);
      }
      else
      {
        $this->db->where('kind_code', $kind);
      }
    }

    if(!empty($type))
    {
      if($type === "NULL")
      {
        $this->db->where('type_code IS NULL', NULL, FALSE);
      }
      else
      {
        $this->db->where('type_code', $type);
      }

    }

    if(!empty($class))
    {
      if($class === 'NULL')
      {
        $this->db->where('class_code IS NULL', NULL, FALSE);
      }
      else
      {
        $this->db->where('class_code', $class);
      }

    }

    if(! empty($area))
    {
      if($area === "NULL")
      {
        $this->db->where('area_code IS NULL', NULL, FALSE);
      }
      else
      {
        $this->db->where('area_code', $area);
      }

    }

    if($perpage != '')
    {
      $offset = $offset === NULL ? 0 : $offset;
      $this->db->limit($perpage, $offset);
    }

    $rs = $this->db->get('customers');

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return FALSE;
  }




  public function is_exists($code, $old_code = '')
  {
    if($old_code != '')
    {
      $this->db->where('code !=', $old_code);
    }

    $rs = $this->db->where('code', $code)->get('customers');

    if($rs->num_rows() > 0)
    {
      return TRUE;
    }

    return FALSE;
  }



  public function is_exists_name($name, $old_name = '')
  {
    if($old_name != '')
    {
      $this->db->where('name !=', $old_name);
    }

    $rs = $this->db->where('name', $name)->get('customers');

    if($rs->num_rows() > 0)
    {
      return TRUE;
    }

    return FALSE;
  }



  public function get_sale_code($code)
  {
    $rs = $this->db->select('sale_code')->where('code', $code)->get('customers');
    if($rs->num_rows() === 1)
    {
      return $rs->row()->sale_code;
    }

    return NULL;
  }


  public function get_update_data($last_sync)
  {
    $rs = $this->ms
    ->select("CardCode AS code")
    ->select("CardName AS name")
    ->select("LicTradNum AS Tax_Id")
    ->select("DebPayAcct, CardType")
    ->select("GroupCode, CmpPrivate")
    ->select("GroupNum, SlpCode AS sale_code")
    ->select("validFor")
    ->select("CreditLine")
    ->select("U_BEX_OLDCODE AS old_code")
    ->where('CardType', 'C')
    ->group_start()
    ->where("UpdateDate >=", sap_date($last_sync))
    ->or_where('CreateDate >=', sap_date($last_sync))
    ->group_end()
    ->get('OCRD');

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return FALSE;
  }




  public function search($txt)
  {
    $qr = "SELECT code FROM customers WHERE code LIKE '%".$txt."%' OR name LIKE '%".$txt."%'";
    $rs = $this->db->query($qr);
    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }
    else
    {
      return array();
    }

  }



  public function getGroupCode()
  {
    $rs = $this->ms
    ->select('GroupCode AS code, GroupName AS name')
    ->where('GroupType', 'C')
    ->get('OCRG');

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return FALSE;
  }




  public function getGroupNum()
  {
    $rs = $this->ms
    ->select('GroupNum AS code, PymntGroup AS name')
    ->order_by('GroupNum', 'ASC')
    ->get('OCTG');

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return FALSE;
  }


  public function getDebPayAcct()
  {
    $rs = $this->ms
    ->select('AcctCode AS code, AcctName AS name')
    ->order_by('AcctCode', 'ASC')
    ->get('OACT');

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return FALSE;
  }



  public function getSlp()
  {
    $rs = $this->ms
    ->select('SlpCode AS code, SlpName AS name')
    ->where('Active', 'Y')
    ->get('OSLP');

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return FALSE;
  }


  public function get_last_sync_date()
  {
    $rs = $this->db->select_max('last_sync')->get('customers');
    if($rs->num_rows() === 1)
    {
      return $rs->row()->last_sync === NULL ? date('2019-01-01 00:00:00') : from_date($rs->row()->last_sync);
    }

    return date('2019-01-01 00:00:00');
  }

}
?>
