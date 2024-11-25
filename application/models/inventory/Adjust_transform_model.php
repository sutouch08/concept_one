<?php
class Adjust_transform_model extends CI_Model
{
  public function __construct()
  {
    parent::__construct();
  }


  public function get($code)
  {
    if(!empty($code))
    {
      $rs = $this->db->where('code', $code)->get('adjust_transform');
      if($rs->num_rows() === 1)
      {
        return $rs->row();
      }
    }

    return FALSE;
  }


	public function get_sum_qty($code)
	{
		if(!empty($code))
		{
			$rs = $this->db->select_sum('qty')->where('adjust_code', $code)->get('adjust_transform_detail');

			if($rs->num_rows() === 1)
			{
				return $rs->row()->qty;
			}
		}

		return 0;
	}


	public function get_sum_issued_qty($transform_code, $product_code)
	{
		$rs = $this->db
		->select_sum('qty')
		->from('adjust_transform_detail AS atd')
		->join('adjust_transform AS at', 'atd.adjust_code = at.code', 'left')
		->where('at.reference', $transform_code)
		->where('atd.product_code', $product_code)
		->where('atd.is_cancle', 0)
		->get();

		if($rs->num_rows() == 1)
		{
			return $rs->row()->qty;
		}

		return 0;
	}



  public function get_details($code)
  {
    if(!empty($code))
    {
      $rs = $this->db
      ->select('adjust_transform_detail.*')
      ->select('products.name AS product_name, products.cost, products.price, products.unit_code')
      ->from('adjust_transform_detail')
      ->join('products', 'adjust_transform_detail.product_code = products.code')
      ->where('adjust_transform_detail.adjust_code', $code)
      ->get();

      if($rs->num_rows() > 0)
      {
        return $rs->result();
      }
    }

    return FALSE;
  }


  public function add(array $ds = array())
  {
    if(!empty($ds))
    {
      return $this->db->insert('adjust_transform', $ds);
    }

    return FALSE;
  }



  public function add_detail(array $ds = array())
  {
    if(!empty($ds))
    {
      return $this->db->insert('adjust_transform_detail', $ds);
    }

    return FALSE;
  }



  public function update($code, array $ds = array())
  {
    if(!empty($ds))
    {
      return $this->db->where('code', $code)->update('adjust_transform', $ds);
    }
  }


  public function update_detail($id, $arr)
  {
    return $this->db->where('id', $id)->update('adjust_transform_detail', $arr);
  }



  public function update_detail_qty($id, $qty)
  {
    return $this->db->set("qty", "qty + {$qty}", FALSE)->where("id", $id)->update("adjust_transform_detail");
  }



  public function delete_detail($id)
  {
    return $this->db->where('id', $id)->delete('adjust_transform_detail');
  }


  public function delete_details($code)
  {
    return $this->db->where('adjust_code', $code)->delete('adjust_transform_detail');
  }




  public function valid_detail($id)
  {
    return $this->db->set('valid', '1')->where('id', $id)->update('adjust_transform_detail');
  }


  public function unvalid_details($code)
  {
    return $this->db->set('valid', '0')->where('adjust_code', $code)->update('adjust_transform_detail');
  }


  public function cancle_details($code)
  {
    return $this->db->set('is_cancle', 1)->where('adjust_code', $code)->update('adjust_transform_detail');
  }


  public function change_status($code, $status)
  {
    return $this->db->set('status', $status)->set('update_user', get_cookie('uname'))->where('code', $code)->update('adjust_transform');
  }



  public function get_non_issue_code($limit = 100)
  {
    $rs = $this->db
    ->select('code')
    ->from('adjust_transform')
    ->where('status', 1)
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


  public function update_issue_code($code, $issue_code)
  {
    if(!empty($issue_code))
    {
      return $this->db->set('issue_code', $issue_code)->where('code', $code)->update('adjust_transform');
    }

    return FALSE;
  }



  public function count_rows(array $ds = array())
  {
    if(!empty($ds))
    {
      $this->db
      ->from('adjust_transform')
      ->join('user', 'adjust_transform.user = user.uname','left');

      if(!empty($ds['code']))
      {
        $this->db->like('adjust_transform.code', $ds['code']);
      }

      if(!empty($ds['reference']))
      {
        $this->db->like('adjust_transform.reference', $ds['reference']);
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
        $this->db->where('adjust_transform.date_add >=', from_date($ds['from_date']));
        $this->db->where('adjust_transform.date_add <=', to_date($ds['to_date']));
      }

      if(!empty($ds['remark']))
      {
        $this->db->like('adjust_transform.remark', $ds['remark']);
      }


      if($ds['status'] !== 'all')
      {
        $this->db->where('adjust_transform.status', $ds['status']);
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
      ->select('adjust_transform.*')
      ->select('user.name AS user_name')
      ->from('adjust_transform')
      ->join('user', 'adjust_transform.user = user.uname', 'left');

      if(!empty($ds['code']))
      {
        $this->db->like('adjust_transform.code', $ds['code']);
      }

      if(!empty($ds['reference']))
      {
        $this->db->like('adjust_transform.reference', $ds['reference']);
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
        $this->db->where('adjust_transform.date_add >=', from_date($ds['from_date']));
        $this->db->where('adjust_transform.date_add <=', to_date($ds['to_date']));
      }

      if(!empty($ds['remark']))
      {
        $this->db->like('adjust_transform.remark', $ds['remark']);
      }


      if($ds['status'] !== 'all')
      {
        $this->db->where('adjust_transform.status', $ds['status']);
      }


      $this->db->order_by('adjust_transform.code', 'DESC');

      $this->db->limit($perpage, $offset);

      $rs = $this->db->get();

      if($rs->num_rows() > 0)
      {
        return $rs->result();
      }

    }

    return FALSE;
  }


  public function get_sap_issue_doc($code)
  {
    $rs = $this->ms
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



  public function get_middle_goods_issue($code)
  {
    $rs = $this->mc
    ->select('DocEntry')
    ->where('U_ECOMNO', $code)
    ->group_start()
    ->where('F_Sap', 'N')
    ->or_where('F_Sap IS NULL', NULL, FALSE)
    ->group_end()
    ->get('OIGE');

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
    $this->mc->where('DocEntry', $docEntry)->delete('IGE1');
    $this->mc->where('DocEntry', $docEntry)->delete('OIGE');
    $this->mc->trans_complete();
    return $this->mc->trans_status();
  }


  //--- add new doc
  public function add_sap_goods_issue($ds = array())
  {
    if(!empty($ds))
    {
      $rs = $this->mc->insert('OIGE', $ds);
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
      return $this->mc->insert('IGE1', $ds);
    }

    return FALSE;
  }





  public function get_max_code($code)
  {
    $rs = $this->db
    ->select_max('code')
    ->like('code', $code, 'after')
    ->order_by('code', 'DESC')
    ->get('adjust_transform');

    return $rs->row()->code;
  }


  public function is_exists_code($code)
  {
    $rs = $this->db->where('code', $code)->count_all_results('adjust_transform');

    if($rs > 0)
    {
      return TRUE;
    }

    return FALSE;
  }

} //--- End Model
 ?>
