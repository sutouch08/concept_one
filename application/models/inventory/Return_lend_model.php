<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Return_lend_model extends CI_Model
{
  public function __construct()
  {
    parent::__construct();
  }


  public function add(array $ds = array())
  {
    if(!empty($ds))
    {
      return $this->db->insert('return_lend', $ds);
    }

    return FALSE;
  }




  public function update($code, $ds = array())
  {
    if(!empty($ds))
    {
      return $this->db->where('code', $code)->update('return_lend', $ds);
    }

    return FALSE;
  }



	public function update_detail($id, $ds = array())
	{
		if(!empty($ds))
		{
			return $this->db->where('id', $id)->update('return_lend_detail', $ds);
		}

		return FALSE;
	}




  public function get($code)
  {
    $rs = $this->db
    ->select('r.*, fz.name AS from_zone_name, tz.name AS to_zone_name, u.name AS display_name, uz.uname AS owner')
    ->from('return_lend AS r')
    ->join('zone AS fz', 'r.from_zone = fz.code', 'left')
    ->join('zone AS tz', 'r.to_zone = tz.code', 'left')
    ->join('user AS u', 'r.user = u.uname', 'left')
    ->join('user AS uz', 'tz.user_id = uz.id', 'left')
    ->where('r.code', $code)
    ->get();

    if($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return NULL;
  }



  public function get_details($code)
  {
    $rs = $this->db
    ->select('r.*')
    ->select('(l.qty - l.receive) AS backlogs, l.qty AS lend_qty, l.receive AS receive')
    ->select('p.barcode, p.unit_code')
    ->from('return_lend_detail AS r')
    ->join('order_lend_detail AS l', 'r.lend_code = l.order_code AND r.product_code = l.product_code', 'left')
    ->join('products AS p', 'r.product_code = p.code', 'left')
    ->where('r.return_code', $code)
    ->get();

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return FALSE;
  }



  public function get_lend_details($code)
  {
    $rs = $this->db->where('return_code', $code)->get('return_lend_detail');
    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return FALSE;
  }



	public function get_detail_by_product($code, $product_code)
	{
		$rs = $this->db
		->select('rd.*, pd.unit_code')
		->from('return_lend_detail AS rd')
		->join('products AS pd', 'rd.product_code = pd.code', 'left')
		->where('rd.return_code', $code)
		->where('rd.product_code', $product_code)
		->get();

		if($rs->num_rows() === 1)
		{
			return $rs->row();
		}

		return NULL;
	}


  public function get_detail_rows($code, $product_code)
	{
		$rs = $this->db
		->select('rd.*, pd.unit_code')
		->from('return_lend_detail AS rd')
		->join('products AS pd', 'rd.product_code = pd.code', 'left')
		->where('rd.return_code', $code)
		->where('rd.product_code', $product_code)
		->get();

		if($rs->num_rows() > 0)
		{
			return $rs->result();
		}

		return NULL;
	}


  //--- delete received details
  public function drop_details($code)
  {
    return $this->db->where('return_code', $code)->delete('return_lend_detail');
  }

	//--- drop detail that not receive qty from wms
	public function drop_not_valid_details($code)
	{
		return $this->db->where('return_code', $code)->where('valid', 0)->delete('return_lend_detail');
	}

  //--- get return backlogs
  public function get_backlogs($code)
  {
    $this->db
    ->where('order_code', $code)
    ->where('receive <', 'qty', FALSE)
    ->where('valid', 0);
    $rs = $this->db->get('order_lend_detail');
    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return FALSE;
  }


  //---- get return qty on return lend row
  public function get_return_qty($return_code, $product_code)
  {
    $rs = $this->db
    ->select('qty, receive_qty')
    ->where('return_code', $return_code)
    ->where('product_code', $product_code)
    ->get('return_lend_detail');
    if($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return NULL;
  }


  //---- update received qty on return backlogs  list (table : order_lend_detail)
  public function update_receive($code, $product_code, $qty)
  {
    $rs = $this->get_detail($code, $product_code);

    if(! empty($rs))
    {
      $new_qty = $rs->receive + $qty;

      $arr = array('receive' => $new_qty);

      if($new_qty >= $rs->qty)
      {
        $arr['valid'] = 1;
      }
      else
      {
        $arr['valid'] = 0;
      }

      return $this->db->where('id', $rs->id)->update('order_lend_detail', $arr);
    }

    return FALSE;
  }


  ///---- change document status  0 = not save, 1 = saved , 2 = cancle
  public function change_status($code, $status)
  {
    return $this->db->where('code', $code)->update('return_lend', array('status' => $status, 'update_user' => get_cookie('uname')));
  }



  ///---- change details status  0 = not save, 1 = saved , 2 = cancle
  public function change_details_status($code, $status)
  {
    return $this->db->where('return_code', $code)->update('return_lend_detail', array('status' => $status));
  }




  //--- get return lend detail one row
  public function get_detail($code, $product_code)
  {
    $rs = $this->db->where('order_code', $code)->where('product_code', $product_code)->get('order_lend_detail');
    if($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return FALSE;
  }



  //--- insert new row
  public function add_detail($ds)
  {
    return $this->db->insert('return_lend_detail', $ds);
  }





  public function count_rows(array $ds = array())
  {
    $this->db
    ->from('return_lend AS r')
    ->join('zone AS z', 'r.to_zone = z.code', 'left')
    ->join('user AS u', 'r.user = u.uname', 'left');

    //---- เลขที่เอกสาร
    if( ! empty($ds['code']))
    {
      $this->db->like('r.code', $ds['code']);
    }

    //---- invoice
    if( ! empty($ds['lend_code']))
    {
      $this->db->like('r.lend_code', $ds['lend_code']);
    }

    //--- emp
    if( ! empty($ds['empName']))
    {
      $this->db->like('r.empName', $ds['empName']);
    }

    if($ds['status'] != 'all')
    {
      if($ds['status'] == 5)
      {
        $this->db->where('r.is_expire', 1);
      }
      else
      {
        $this->db->where('r.status', $ds['status']);
      }
    }

    if($ds['must_accept'] != 'all')
    {
      $this->db->where('r.must_accept', $ds['must_accept']);
    }

		if( ! empty($ds['zone']))
    {
      $this->db
      ->group_start()
      ->like('r.to_zone', $ds['zone'])
      ->or_like('z.name', $ds['zone'])
      ->group_end();
    }

    if(!empty($ds['from_date']) && !empty($ds['to_date']))
    {
      $this->db->where('r.date_add >=', from_date($ds['from_date']));
      $this->db->where('r.date_add <=', to_date($ds['to_date']));
    }


    if( isset($ds['sap']) && $ds['sap'] != 'all')
    {
      if($ds['sap'] == 0)
      {
        $this->db->where('r.inv_code IS NULL', NULL, FALSE);
      }
      else
      {
        $this->db->where('r.inv_code IS NOT NULL', NULL, FALSE);
      }
    }

    return $this->db->count_all_results();
  }





  public function get_list(array $ds = array(), $perpage = 20, $offset = 0)
  {
    $this->db
    ->select('r.*, z.name AS zone_name, u.name AS display_name')
    ->from('return_lend AS r')
    ->join('zone AS z', 'r.to_zone = z.code', 'left')
    ->join('user AS u', 'r.user = u.uname', 'left');

    //---- เลขที่เอกสาร
    if( ! empty($ds['code']))
    {
      $this->db->like('r.code', $ds['code']);
    }

    //---- invoice
    if( ! empty($ds['lend_code']))
    {
      $this->db->like('r.lend_code', $ds['lend_code']);
    }

    //--- emp
    if( ! empty($ds['empName']))
    {
      $this->db->like('r.empName', $ds['empName']);
    }

    if($ds['status'] != 'all')
    {
      if($ds['status'] == 5)
      {
        $this->db->where('r.is_expire', 1);
      }
      else
      {
        $this->db->where('r.status', $ds['status']);
      }
    }

    if($ds['must_accept'] != 'all')
    {
      $this->db->where('r.must_accept', $ds['must_accept']);
    }

		if( ! empty($ds['zone']))
    {
      $this->db
      ->group_start()
      ->like('r.to_zone', $ds['zone'])
      ->or_like('z.name', $ds['zone'])
      ->group_end();
    }

    if(!empty($ds['from_date']) && !empty($ds['to_date']))
    {
      $this->db->where('r.date_add >=', from_date($ds['from_date']));
      $this->db->where('r.date_add <=', to_date($ds['to_date']));
    }


    if( isset($ds['sap']) && $ds['sap'] != 'all')
    {
      if($ds['sap'] == 0)
      {
        $this->db->where('r.inv_code IS NULL', NULL, FALSE);
      }
      else
      {
        $this->db->where('r.inv_code IS NOT NULL', NULL, FALSE);
      }
    }

    $rs = $this->db->order_by('r.code', 'DESC')->limit($perpage, $offset)->get();

    return $rs->result();
  }



  public function get_sum_qty($code)
  {
    $rs = $this->db->select_sum('qty')->where('return_code', $code)->get('return_lend_detail');

    return $rs->row()->qty === NULL ? 0 : $rs->row()->qty;
  }




  public function get_sum_amount($code)
  {
    $rs = $this->db->select_sum('amount')->where('return_code', $code)->get('return_lend_detail');
    return $rs->row()->amount === NULL ? 0 : $rs->row()->amount;
  }




  public function get_max_code($code)
  {
    $rs = $this->db
    ->select_max('code')
    ->like('code', $code, 'after')
    ->order_by('code', 'DESC')
    ->get('return_lend');

    if($rs->num_rows() == 1)
    {
      return $rs->row()->code;
    }

    return FALSE;
  }


  public function is_exists($code, $old_code = NULL)
  {
    if(!empty($old_code))
    {
      $this->db->where('code !=', $old_code);
    }

    $rs = $this->db->where('code', $code)->get('return_lend');

    if($rs->num_rows() === 1)
    {
      return TRUE;
    }

    return FALSE;
  }


  public function get_sap_doc_num($code)
  {
    $rs = $this->ms
    ->select('DocNum')
    ->where('U_ECOMNO', $code)
    ->where('CANCELED', 'N')
    ->get('OWTR');

    if($rs->num_rows() > 0)
    {
      return $rs->row()->DocNum;
    }

    return NULL;
  }


  public function is_middle_exists($code)
  {
    $rs = $this->mc->select('DocStatus')->where('U_ECOMNO', $code)->get('OWTR');
    if($rs->num_rows() === 1)
    {
      return TRUE;
    }

    return FALSE;
  }


  public function get_middle_transfer_doc($code)
  {
    $rs = $this->mc
    ->select('DocEntry')
    ->where('U_ECOMNO', $code)
    ->group_start()
    ->where('F_Sap', 'N')
    ->or_where('F_Sap IS NULL',NULL, FALSE)
    ->group_end()
    ->get('OWTR');

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function drop_sap_exists_details($code)
  {
    return $this->mc->where('U_ECOMNO', $code)->delete('WTR1');
  }


  public function drop_middle_exits_data($docEntry)
  {
    $ds = $this->mc->where('DocEntry', $docEntry)->delete('WTR1');
    $do = $this->mc->where('DocEntry', $docEntry)->delete('OWTR');

    $sc = ($ds === TRUE && $do === TRUE) ? TRUE : FALSE;
    return $sc;
  }


  public function get_non_inv_code($limit = 100)
  {
    $rs = $this->db
    ->select('code')
    ->where('status', 1)
    ->where('inv_code IS NULL', NULL, FALSE)
    ->get('return_lend');

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }

  public function update_inv($code, $doc_num)
  {
    return $this->db->set('inv_code', $doc_num)->where('code', $code)->update('return_lend');
  }


} //--- end class

 ?>
