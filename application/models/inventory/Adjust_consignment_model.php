<?php
class Adjust_consignment_model extends CI_Model
{
  public function __construct()
  {
    parent::__construct();
  }


  public function get($code)
  {
    if(!empty($code))
    {
      $rs = $this->db->where('code', $code)->get('adjust_consignment');
      if($rs->num_rows() === 1)
      {
        return $rs->row();
      }
    }

    return FALSE;
  }


  public function get_detail($id)
  {
    $rs = $this->db->where('id', $id)->get('adjust_consignment_detail');
    if($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return FALSE;
  }


  public function get_not_save_detail($code, $product_code, $zone_code)
  {
    $rs = $this->db
    ->where('adjust_code', $code)
    ->where('zone_code', $zone_code)
    ->where('product_code', $product_code)
    ->where('valid', 0)
    ->where('is_cancle', 0)
    ->get('adjust_consignment_detail');

    if($rs->num_rows() > 0)
    {
      return $rs->row();
    }

    return NULL;
  }


  public function get_details($code)
  {
    if(!empty($code))
    {
      $rs = $this->db
      ->select('adjust_consignment_detail.*')
      ->select('products.name AS product_name')
      ->select('zone.name AS zone_name')
      ->select('warehouse.name AS warehouse_name')
      ->from('adjust_consignment_detail')
      ->join('products', 'adjust_consignment_detail.product_code = products.code')
      ->join('zone', 'adjust_consignment_detail.zone_code = zone.code', 'left')
      ->join('warehouse', 'adjust_consignment_detail.warehouse_code = warehouse.code', 'left')
      ->where('adjust_consignment_detail.adjust_code', $code)
      ->get();

      if($rs->num_rows() > 0)
      {
        return $rs->result();
      }
    }

    return FALSE;
  }



  public function get_exists_detail($code, $product_code, $zone_code)
  {
    $rs = $this->db
    ->select('adjust_consignment_detail.*')
    ->select('products.name AS product_name')
    ->select('zone.name AS zone_name')
    ->select('warehouse.name AS warehouse_name')
    ->from('adjust_consignment_detail')
    ->join('products', 'adjust_consignment_detail.product_code = products.code')
    ->join('zone', 'adjust_consignment_detail.zone_code = zone.code', 'left')
    ->join('warehouse', 'adjust_consignment_detail.warehouse_code = warehouse.code', 'left')
    ->where('adjust_consignment_detail.adjust_code', $code)
    ->where('adjust_consignment_detail.product_code', $product_code)
    ->where('adjust_consignment_detail.zone_code', $zone_code)
    ->get();

    if($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return FALSE;
  }



  public function add(array $ds = array())
  {
    if(!empty($ds))
    {
      return $this->db->insert('adjust_consignment', $ds);
    }

    return FALSE;
  }



  public function add_detail(array $ds = array())
  {
    if(!empty($ds))
    {
      return $this->db->insert('adjust_consignment_detail', $ds);
    }

    return FALSE;
  }



  public function update($code, array $ds = array())
  {
    if(!empty($ds))
    {
      return $this->db->where('code', $code)->update('adjust_consignment', $ds);
    }
  }


  public function update_detail($id, $arr)
  {
    return $this->db->where('id', $id)->update('adjust_consignment_detail', $arr);
  }



  public function update_detail_qty($id, $qty)
  {
    return $this->db->set("qty", "qty + {$qty}", FALSE)->where("id", $id)->update("adjust_consignment_detail");
  }



  public function delete_detail($id)
  {
    return $this->db->where('id', $id)->delete('adjust_consignment_detail');
  }


  public function delete_details($code)
  {
    return $this->db->where('adjust_code', $code)->delete('adjust_consignment_detail');
  }




  public function valid_detail($id)
  {
    return $this->db->set('valid', '1')->where('id', $id)->update('adjust_consignment_detail');
  }


  public function unvalid_details($code)
  {
    return $this->db->set('valid', '0')->where('adjust_code', $code)->update('adjust_consignment_detail');
  }


  public function cancle_details($code)
  {
    return $this->db->set('is_cancle', 1)->where('adjust_code', $code)->update('adjust_consignment_detail');
  }


  public function change_status($code, $status)
  {
    return $this->db->set('status', $status)->set('update_user', get_cookie('uname'))->where('code', $code)->update('adjust_consignment');
  }


  public function get_issue_details($code)
  {
    $rs = $this->db
    ->select('ad.*')
    ->select('pd.name AS product_name, pd.cost, pd.price, pd.unit_code')
    ->from('adjust_consignment_detail AS ad')
    ->join('products AS pd', 'ad.product_code = pd.code', 'left')
    ->where('ad.adjust_code', $code)
    ->where('ad.qty <', 0, FALSE)
    ->where('ad.valid', 1)
    ->where('ad.is_cancle', 0)
    ->get();

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }



  public function get_receive_details($code)
  {
    $rs = $this->db
    ->select('ad.*')
    ->select('pd.name AS product_name, pd.cost, pd.price, pd.unit_code')
    ->from('adjust_consignment_detail AS ad')
    ->join('products AS pd', 'ad.product_code = pd.code', 'left')
    ->where('ad.adjust_code', $code)
    ->where('ad.qty >', 0, FALSE)
    ->where('ad.valid', 1)
    ->where('ad.is_cancle', 0)
    ->get();

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }



  public function get_non_issue_code($limit = 100)
  {
    $rs = $this->db
    ->select('code')
    ->from('adjust_consignment')
    ->where('status', 1)
    ->where('is_approved', 1)
    ->where('issue_code IS NULL', NULL, FALSE)
    ->order_by('code', 'ASC')
    ->limit($limit)
    ->get();

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function get_non_receive_code($limit = 100)
  {
    $rs = $this->db
    ->select('code')
    ->from('adjust_consignment')
    ->where('status', 1)
    ->where('is_approved', 1)
    ->where('receive_code IS NULL', NULL, FALSE)
    ->order_by('code', 'ASC')
    ->limit($limit)
    ->get();

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function update_issue_code($code, $issue_code)
  {
    if(!empty($issue_code))
    {
      return $this->db->set('issue_code', $issue_code)->where('code', $code)->update('adjust_consignment');
    }

    return FALSE;
  }



  public function update_receive_code($code, $receive_code)
  {
    if(!empty($receive_code))
    {
      return $this->db->set('receive_code', $receive_code)->where('code', $code)->update('adjust_consignment');
    }

    return FALSE;
  }



  public function count_rows(array $ds = array())
  {
    if(!empty($ds))
    {
      $this->db
      ->from('adjust_consignment')
      ->join('user', 'adjust_consignment.user = user.uname', 'left');

      if(!empty($ds['code']))
      {
        $this->db->like('adjust_consignment.code', $ds['code']);
      }

      if(!empty($ds['reference']))
      {
        $this->db->like('adjust_consignment.reference', $ds['reference']);
      }

      if(!empty($ds['user']))
      {
        $this->db->group_start();
        $this->db->like('user.uname', $ds['user']);
        $this->db->or_like('user.name', $ds['user']);
        $this->db->group_end();
      }

      if(!empty($ds['from_date']) && !empty($ds['to_date']))
      {
        $this->db->where('adjust_consignment.date_add >=', from_date($ds['from_date']));
        $this->db->where('adjust_consignment.date_add <=', to_date($ds['to_date']));
      }

      if(!empty($ds['remark']))
      {
        $this->db->like('adjust_consignment.remark', $ds['remark']);
      }


      if($ds['status'] !== 'all')
      {
        $this->db->where('adjust_consignment.status', $ds['status']);
      }
      else
      {
        if($ds['isApprove'] !== 'all')
        {
          $this->db->where('adjust_consignment.status !=', 2);
        }
      }

      if($ds['isApprove'] !== 'all')
      {
        $this->db->where('adjust_consignment.is_approved', $ds['isApprove']);
      }

      return $this->db->count_all_results();
    }

    return FALSE;
  }


  public function get_list(array $ds = array(), $perpage = 20, $offset = 0)
  {
    if(!empty($ds))
    {

      $this->db
      ->select('adjust_consignment.*')
      ->select('user.name AS user_name')
      ->from('adjust_consignment')
      ->join('user', 'adjust_consignment.user = user.uname', 'left');

      if(!empty($ds['code']))
      {
        $this->db->like('adjust_consignment.code', $ds['code']);
      }

      if(!empty($ds['reference']))
      {
        $this->db->like('adjust_consignment.reference', $ds['reference']);
      }

      if(!empty($ds['user']))
      {
        $this->db->group_start();
        $this->db->like('user.uname', $ds['user']);
        $this->db->or_like('user.name', $ds['user']);
        $this->db->group_end();
      }

      if(!empty($ds['from_date']) && !empty($ds['to_date']))
      {
        $this->db->where('adjust_consignment.date_add >=', from_date($ds['from_date']));
        $this->db->where('adjust_consignment.date_add <=', to_date($ds['to_date']));
      }

      if(!empty($ds['remark']))
      {
        $this->db->like('adjust_consignment.remark', $ds['remark']);
      }


      if($ds['status'] !== 'all')
      {
        $this->db->where('adjust_consignment.status', $ds['status']);
      }
      else
      {
        if($ds['isApprove'] !== 'all')
        {
          $this->db->where('adjust_consignment.status !=', 2);
        }
      }

      if($ds['isApprove'] !== 'all')
      {
        $this->db->where('adjust_consignment.is_approved', $ds['isApprove']);
      }


      $this->db->order_by('adjust_consignment.code', 'DESC');

      $this->db->limit($perpage, $offset);

      $rs = $this->db->get();

      if($rs->num_rows() > 0)
      {
        return $rs->result();
      }

    }

    return FALSE;
  }



  public function do_approve($code, $user)
  {
    $arr = array(
      'is_approved' => 1,
      'approver' => $user,
      'approve_date' => now()
    );

    return $this->db->where('code', $code)->update('adjust_consignment', $arr);
  }


  public function un_approve($code)
  {
    $arr = array(
      'is_approved' => 0,
      'approver' => NULL,
      'approve_date' => now()
    );

    return $this->db->where('code', $code)->update('adjust_consignment', $arr);
  }



  public function get_sap_issue_doc($code)
  {
    $rs = $this->cn
    ->select('DocEntry, DocNum')
    ->where('U_ECOMNO', $code)
    ->where('CANCELED', 'N')
    ->get('OIGE');
    if($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return FALSE;
  }


  public function get_sap_receive_doc($code)
  {
    $rs = $this->cn
    ->select('DocEntry, DocNum')
    ->where('U_ECOMNO', $code)
    ->where('CANCELED', 'N')
    ->get('OIGN');
    if($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return FALSE;
  }


  public function get_middle_goods_issue($code)
  {
    $rs = $this->mc
    ->select('DocEntry')
    ->where('U_ECOMNO', $code)
    ->group_start()
    ->where('F_Sap', 'N')
    ->or_where('F_Sap IS NULL', NULL, FALSE)
    ->group_end()
    ->get('CNOIGE');

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }



  public function get_middle_goods_receive($code)
  {
    $rs = $this->mc
    ->select('DocEntry')
    ->where('U_ECOMNO', $code)
    ->group_start()
    ->where('F_Sap', 'N')
    ->or_where('F_Sap IS NULL', NULL, FALSE)
    ->group_end()
    ->get('CNOIGN');

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  //--- ลบรายการที่ค้างใน middle ที่ยังไม่ได้เอาเข้า SAP ออก
  public function drop_middle_issue_data($docEntry)
  {
    $this->mc->trans_start();
    $this->mc->where('DocEntry', $docEntry)->delete('CNIGE1');
    $this->mc->where('DocEntry', $docEntry)->delete('CNOIGE');
    $this->mc->trans_complete();
    return $this->mc->trans_status();
  }


  //--- ลบรายการที่ค้างใน middle ที่ยังไม่ได้เอาเข้า SAP ออก
  public function drop_middle_receive_data($docEntry)
  {
    $this->mc->trans_start();
    $this->mc->where('DocEntry', $docEntry)->delete('CNIGN1');
    $this->mc->where('DocEntry', $docEntry)->delete('CNOIGN');
    $this->mc->trans_complete();
    return $this->mc->trans_status();
  }



  //--- add new doc
  public function add_sap_goods_issue($ds = array())
  {
    if(!empty($ds))
    {
      $rs = $this->mc->insert('CNOIGE', $ds);
      if($rs)
      {
        return $this->mc->insert_id();
      }
    }

    return FALSE;
  }


  public function add_sap_goods_issue_row($ds = array())
  {
    if(!empty($ds))
    {
      return $this->mc->insert('CNIGE1', $ds);
    }

    return FALSE;
  }



  //--- add new doc
  public function add_sap_goods_receive($ds = array())
  {
    if(!empty($ds))
    {
      $rs = $this->mc->insert('CNOIGN', $ds);
      if($rs)
      {
        return $this->mc->insert_id();
      }
    }

    return FALSE;
  }


  public function add_sap_goods_receive_row($ds = array())
  {
    if(!empty($ds))
    {
      return $this->mc->insert('CNIGN1', $ds);
    }

    return FALSE;
  }




  public function get_max_code($code)
  {
    $rs = $this->db
    ->select_max('code')
    ->like('code', $code, 'after')
    ->order_by('code', 'DESC')
    ->get('adjust_consignment');

    return $rs->row()->code;
  }
} //--- End Model
 ?>
