<?php
class Transfer_model extends CI_Model
{
  public function __construct()
  {
    parent::__construct();
  }

  public function get_sap_qty($item_code, $zone_code)
  {
    $qr  = "SELECT OIBQ.OnHandQty ";
    $qr .= "FROM OIBQ ";
    $qr .= "LEFT JOIN OBIN ON OIBQ.BinAbs = OBIN.AbsEntry ";
    $qr .= "WHERE OIBQ.ItemCode = '{$item_code}' AND OBIN.BinCode = '{$zone_code}' ";

    $qs = $this->ms->query($qr);

    if($qs->num_rows() === 1)
    {
      return $qs->row()->OnHandQty;
    }

    return 0;
  }


  public function get_sap_transfer_doc($code)
  {
    $rs = $this->ms
    ->select('DocEntry, DocStatus')
    ->where('U_ECOMNO', $code)
    ->where('CANCELED', 'N')
    ->get('OWTR');
    if($rs->num_rows() === 1)
    {
      return $rs->row();
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


  public function get_middle_transfer_draft($code)
  {
    $rs = $this->mc
    ->select('DocEntry')
    ->where('U_ECOMNO', $code)
    ->group_start()
    ->where('F_Sap', 'N')
    ->or_where('F_Sap IS NULL',NULL, FALSE)
    ->group_end()
    ->get('DFOWTR');

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function get_transfer_draft($code)
  {
    $rs = $this->mc
    ->where('U_ECOMNO', $code)
    ->group_start()
    ->where_in('F_Sap', array('N', 'D'))
    ->or_where('F_Sap IS NULL', NULL, FALSE)
    ->group_end()
    ->get('DFOWTR');

    if($rs->num_rows() > 0)
    {
      return $rs->row();
    }

    return NULL;
  }


  public function add_sap_transfer_doc(array $ds = array())
  {
    if(!empty($ds))
    {
      $rs = $this->mc->insert('OWTR', $ds);
      if($rs)
      {
        return $this->mc->insert_id();
      }
    }

    return FALSE;
  }



  public function add_sap_transfer_draft(array $ds = array())
  {
    if(!empty($ds))
    {
      $rs = $this->mc->insert('DFOWTR', $ds);
      if($rs)
      {
        return $this->mc->insert_id();
      }
    }

    return FALSE;
  }




  public function update_sap_transfer_doc($code, $ds = array())
  {
    if(! empty($code) && ! empty($ds))
    {
      return $this->mc->where('U_ECOMNO', $code)->update('OWTR', $ds);
    }

    return FALSE;
  }


  public function confirm_draft_receipted($docEntry)
  {
    $ds = array(
      'F_Receipt' => 'Y',
      'F_ReceiptDate' => sap_date(now(), TRUE)
    );

    return $this->mc->where('DocEntry', $docEntry)->update('DFOWTR', $ds);
  }



  public function add_sap_transfer_detail(array $ds = array())
  {
    if(!empty($ds))
    {
      return $this->mc->insert('WTR1', $ds);
    }

    return FALSE;
  }


  public function add_sap_transfer_draft_detail(array $ds = array())
  {
    if(!empty($ds))
    {
      return $this->mc->insert('DFWTR1', $ds);
    }

    return FALSE;
  }



  public function drop_sap_exists_details($code)
  {
    return $this->mc->where('U_ECOMNO', $code)->delete('WTR1');
  }


  public function drop_middle_exits_data($docEntry)
  {
    $this->mc->trans_start();
    $this->mc->where('DocEntry', $docEntry)->delete('WTR1');
    $this->mc->where('DocEntry', $docEntry)->delete('OWTR');
    $this->mc->trans_complete();

    return $this->mc->trans_status();
  }

  //---- transfer draft
  public function drop_middle_transfer_draft($docEntry)
  {
    $this->mc->trans_start();
    $this->mc->where('DocEntry', $docEntry)->delete('DFWTR1');
    $this->mc->where('DocEntry', $docEntry)->delete('DFOWTR');
    $this->mc->trans_complete();

    return $this->mc->trans_status();
  }




  public function add(array $ds = array())
  {
    if(!empty($ds))
    {
      return $this->db->insert('transfer', $ds);
    }

    return FALSE;
  }



  public function update($code, array $ds = array())
  {
    if(!empty($ds))
    {
      return $this->db->where('code', $code)->update('transfer', $ds);
    }

    return FALSE;
  }



  public function add_detail(array $ds = array())
  {
    if(!empty($ds))
    {
      if($this->db->insert('transfer_detail', $ds))
      {
        return $this->db->insert_id();
      }
    }

    return FALSE;
  }


  public function get_transfer($code)
  {
    $rs = $this->db->where('code', $code)->get('transfer');

    if($rs->num_rows() === 1)
    {
      return $rs->row();
    }
  }


  public function get($code)
  {
    $rs = $this->db
    ->select('t.*')
    ->select('fw.name AS from_warehouse_name, tw.name AS to_warehouse_name')
    ->select('u.uname, u.name AS display_name')
    ->from('transfer AS t')
    ->join('warehouse AS fw', 't.from_warehouse = fw.code', 'left')
    ->join('warehouse AS tw', 't.to_warehouse = tw.code', 'left')
    ->join('user AS u', 't.accept_by = u.uname', 'left')
    ->where('t.code', $code)
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
    ->select('td.*')
    ->select('pd.barcode, pd.unit_code')
    ->select('fz.name AS from_zone_name, tz.name AS to_zone_name')
    ->from('transfer_detail AS td')
    ->join('products AS pd', 'td.product_code = pd.code', 'left')
    ->join('zone AS fz', 'td.from_zone = fz.code', 'left')
    ->join('zone AS tz', 'td.to_zone = tz.code', 'left')
    ->where('td.transfer_code', $code)
    ->get();

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }



  public function get_detail($id)
  {
    $rs = $this->db
		->select('td.*, pd.barcode, pd.unit_code')
		->from('transfer_detail AS td')
		->join('products AS pd', 'td.product_code = pd.code', 'left')
		->where('td.id', $id)
		->get();

    if($rs->num_rows() === 1)
    {
      return $rs->row();
    }

		return NULL;
  }


  public function get_temp_in(array $ds = array())
  {
    if( ! empty($ds))
    {
      $rs = $this->db
      ->select('tmp.*, pd.barcode, pd.name as product_name')
      ->from('transfer_temp AS tmp')
      ->join('products AS pd', 'tmp.product_code = pd.code', 'left')
      ->where_in('tmp.id', $ds)
      ->get();

      if($rs->num_rows() > 0)
      {
        return $rs->result();
      }
    }

    return NULL;
  }


  public function get_details_in(array $ds = array())
  {
    if( ! empty($ds))
    {
      $rs = $this->db
      ->select('td.*, pd.barcode')
      ->from('transfer_detail AS td')
      ->join('products AS pd', 'td.product_code = pd.code', 'left')
      ->where_in('td.id', $ds)
      ->get();

      if($rs->num_rows() > 0)
      {
        return $rs->result();
      }
    }

    return NULL;
  }

	public function update_detail($id, $ds = array())
	{
		if(!empty($ds))
		{
			return $this->db->where('id', $id)->update('transfer_detail', $ds);
		}

		return FALSE;
	}


	public function get_detail_by_product($code, $product_code)
  {
    $rs = $this->db
		->select('td.*, pd.barcode, pd.unit_code')
		->from('transfer_detail AS td')
		->join('products AS pd', 'td.product_code = pd.code', 'left')
		->where('td.transfer_code', $code)
		->where('td.product_code', $product_code)
		->get();

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

		return NULL;
  }



  public function get_id($transfer_code, $product_code, $from_zone, $to_zone)
  {
    $rs = $this->db
    ->select('id')
    ->where('transfer_code', $transfer_code)
    ->where('product_code', $product_code)
    ->where('from_zone', $from_zone)
    ->where('to_zone', $to_zone)
    ->get('transfer_detail');

    if($rs->num_rows() === 1)
    {
      return $rs->row()->id;
    }

    return FALSE;
  }


  public function update_qty($id, $qty)
  {
    return $this->db->set("qty", "qty + {$qty}", FALSE)->where('id', $id)->update('transfer_detail');
  }



  public function update_temp(array $ds = array())
  {
    if(!empty($ds))
    {
      $id = $this->get_temp_id($ds['transfer_code'], $ds['product_code'], $ds['zone_code']);

      if(!empty($id))
      {
        return $this->update_temp_qty($id, $ds['qty']);
      }
      else
      {
        return $this->add_temp($ds);
      }
    }

    return FALSE;
  }


  public function get_temp_row($code, $product_code, $zone_code)
  {
    $rs = $this->db
    ->where('transfer_code', $code)
    ->where('product_code', $product_code)
    ->where('zone_code', $zone_code)
    ->get('transfer_temp');

    if($rs->num_rows() > 0)
    {
      return $rs->row();
    }

    return NULL;
  }


  public function get_temp_id($code, $product_code, $zone_code)
  {
    $rs = $this->db
    ->select('id')
    ->where('transfer_code', $code)
    ->where('product_code', $product_code)
    ->where('zone_code', $zone_code)
    ->get('transfer_temp');

    if($rs->num_rows() === 1)
    {
      return $rs->row()->id;
    }

    return FALSE;
  }


  public function add_temp(array $ds = array())
  {
    if(!empty($ds))
    {
      if($this->db->insert('transfer_temp', $ds))
      {
        return $this->db->insert_id();
      }
    }

    return FALSE;
  }


  public function update_temp_qty($id, $qty)
  {
    return $this->db->set("qty", "qty + {$qty}", FALSE)->where('id', $id)->update('transfer_temp');
  }


  public function delete_temp($id)
  {
    return $this->db->where('id', $id)->delete('transfer_temp');
  }


  public function get_temp($id)
  {
    $rs = $this->db
    ->select('tmp.*, pd.barcode, pd.name as product_name')
    ->from('transfer_temp AS tmp')
    ->join('products AS pd', 'tmp.product_code = pd.code', 'left')
    ->where('tmp.id', $id)
    ->get();

    if($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return NULL;
  }


  public function get_transfer_temp($code)
  {
    $rs = $this->db
    ->select('transfer_temp.*, products.barcode')
    ->from('transfer_temp')
    ->join('products', 'products.code = transfer_temp.product_code', 'left')
    ->where('transfer_code', $code)
    ->get();

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return FALSE;
  }



  public function get_temp_product($code, $product_code)
  {
    $rs = $this->db
    ->where('transfer_code', $code)
    ->where('product_code', $product_code)
    ->get('transfer_temp');

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return FALSE;
  }


  public function get_temp_qty($transfer_code, $product_code, $zone_code)
  {
    $rs = $this->db
    ->select('qty')
    ->where('transfer_code', $transfer_code)
    ->where('product_code', $product_code)
    ->where('zone_code', $zone_code)
    ->get('transfer_temp');

    if($rs->num_rows() === 1)
    {
      return $rs->row()->qty;
    }

    return 0;
  }


  public function get_sum_temp_stock($product_code)
  {
    $rs = $this->db->select_sum('qty')->where('product_code', $product_code)->get('transfer_temp');
    if($rs->num_rows() === 1)
    {
      return $rs->row()->qty;
    }

    return 0;
  }



  public function get_transfer_qty($transfer_code, $product_code, $from_zone)
  {
    $rs = $this->db
    ->select_sum('qty')
    ->where('transfer_code', $transfer_code)
    ->where('product_code', $product_code)
    ->where('from_zone', $from_zone)
    ->where('valid', 0)
    ->get('transfer_detail');

    return intval($rs->row()->qty);
  }


  public function drop_zero_temp()
  {
    return $this->db->where('qty <', 1)->delete('transfer_temp');
  }


  public function drop_all_temp($code)
  {
    return $this->db->where('transfer_code', $code)->delete('transfer_temp');
  }



  public function drop_all_detail($code)
  {
    return $this->db->where('transfer_code', $code)->delete('transfer_detail');
  }


  public function drop_detail($id)
  {
    return $this->db->where('id', $id)->delete('transfer_detail');
  }


  public function delete_rows(array $ds = array())
  {
    return $this->db->where_in('id', $ds)->delete('transfer_detail');
  }



  public function is_exists($code, $old_code = NULL)
  {
    if(!empty($old_code))
    {
      $this->db->where('code !=', $old_code);
    }

    $rs = $this->db->where('code', $code)->get('transfer');
    if($rs->num_rows() === 1)
    {
      return TRUE;
    }

    return FALSE;
  }



  public function is_exists_detail($code)
  {
    $rs = $this->db->select('id')->where('transfer_code', $code)->get('transfer_detail');
    if($rs->num_rows() > 0)
    {
      return TRUE;
    }

    return FALSE;
  }



  public function is_exists_temp($code)
  {
    $rs = $this->db->select('id')->where('transfer_code', $code)->get('transfer_temp');
    if($rs->num_rows() > 0)
    {
      return TRUE;
    }

    return FALSE;
  }


  public function set_status($code, $status)
  {
    return $this->db->set('status', $status)->where('code', $code)->update('transfer');
  }



  public function valid_all_detail($code, $valid)
  {
    return $this->db->set('valid', $valid)->where('transfer_code', $code)->update('transfer_detail');
  }


  public function count_rows(array $ds = array())
  {
    $this->db
    ->from('transfer AS tr')
    ->join('user AS u', 'tr.user = u.uname', 'left')
    ->join('warehouse AS fwh', 'tr.from_warehouse = fwh.code', 'left')
    ->join('warehouse AS twh', 'tr.to_warehouse = twh.code', 'left');

    if(!empty($ds['code']))
    {
      $this->db->like('tr.code', $ds['code']);
    }

    if( ! empty($ds['from_warehouse']) && $ds['from_warehouse'] != 'all')
    {
      $this->db->where('tr.from_warehouse', $ds['from_warehouse']);
    }

    if( ! empty($ds['to_warehouse']) && $ds['to_warehouse'] != 'all')
    {
      $this->db->where('tr.to_warehouse', $ds['to_warehouse']);
    }

    if( ! empty($ds['user']) && $ds['user'] != 'all')
    {
      $this->db->where('u.uname', $ds['user']);
    }

    if($ds['status'] != 'all')
    {
      if($ds['status'] == 5)
      {
        $this->db->where('tr.is_expire', 1)->where('tr.status !=', 2);
      }
      else
      {
        $this->db->where('tr.status', $ds['status']);
      }
    }


    if($ds['is_approve'] != 'all')
    {
      if($ds['is_approve'] < 0)
      {
        $this->db->where('must_approve', 0);
      }
      else
      {
        $this->db->where('must_approve', 1)->where('is_approve', $ds['is_approve']);
      }
    }

    if($ds['valid'] != 'all')
    {
      $this->db->where('tr.valid', $ds['valid']);
    }

    if(isset($ds['is_export']) && $ds['is_export'] != 'all')
    {
      $this->db->where('tr.is_export', $ds['is_export']);
    }

    if(isset($ds['sap']) && $ds['sap'] != 'all')
    {
      if($ds['sap'] == 0)
      {
        $this->db->where('tr.inv_code IS NULL', NULL, FALSE);
      }
      else
      {
        $this->db->where('tr.inv_code IS NOT NULL', NULL, FALSE);
      }
    }

    if( ! empty($ds['from_date']) && ! empty($ds['to_date']))
    {
      $this->db->where('tr.date_add >=', from_date($ds['from_date']));
      $this->db->where('tr.date_add <=', to_date($ds['to_date']));
    }

    return $this->db->count_all_results();
  }


  public function get_list(array $ds = array(), $perpage = 20, $offset = 0)
  {
    $this->db
    ->select('tr.*, u.name AS display_name')
    ->select('fwh.name AS from_warehouse_name, twh.name AS to_warehouse_name')
    ->from('transfer AS tr')
    ->join('user AS u', 'tr.user = u.uname', 'left')
    ->join('warehouse AS fwh', 'tr.from_warehouse = fwh.code', 'left')
    ->join('warehouse AS twh', 'tr.to_warehouse = twh.code', 'left');

    if(!empty($ds['code']))
    {
      $this->db->like('tr.code', $ds['code']);
    }

    if( ! empty($ds['from_warehouse']) && $ds['from_warehouse'] != 'all')
    {
      $this->db->where('tr.from_warehouse', $ds['from_warehouse']);
    }

    if( ! empty($ds['to_warehouse']) && $ds['to_warehouse'] != 'all')
    {
      $this->db->where('tr.to_warehouse', $ds['to_warehouse']);
    }

    if( ! empty($ds['user']) && $ds['user'] != 'all')
    {
      $this->db->where('u.uname', $ds['user']);
    }

    if($ds['status'] != 'all')
    {
      if($ds['status'] == 5)
      {
        $this->db->where('tr.is_expire', 1)->where('tr.status !=', 2);
      }
      else
      {
        $this->db->where('tr.status', $ds['status']);
      }
    }

    if($ds['is_approve'] != 'all')
    {
      if($ds['is_approve'] < 0)
      {
        $this->db->where('must_approve', 0);
      }
      else
      {
        $this->db->where('must_approve', 1)->where('is_approve', $ds['is_approve']);
      }
    }


    if($ds['valid'] != 'all')
    {
      $this->db->where('tr.valid', $ds['valid']);
    }

    if(isset($ds['is_export']) && $ds['is_export'] != 'all')
    {
      $this->db->where('tr.is_export', $ds['is_export']);
    }

    if(isset($ds['sap']) && $ds['sap'] != 'all')
    {
      if($ds['sap'] == 0)
      {
        $this->db->where('tr.inv_code IS NULL', NULL, FALSE);
      }
      else
      {
        $this->db->where('tr.inv_code IS NOT NULL', NULL, FALSE);
      }
    }

    if( ! empty($ds['from_date']) && ! empty($ds['to_date']))
    {
      $this->db->where('tr.date_add >=', from_date($ds['from_date']));
      $this->db->where('tr.date_add <=', to_date($ds['to_date']));
    }

    $rs = $this->db->order_by('tr.code', 'DESC')->limit($perpage, $offset)->get();

		if($rs->num_rows() > 0)
		{
			return $rs->result();
		}

		return NULL;
  }



  public function get_warehouse_in($txt)
  {
    $rs = $this->ms
    ->select('WhsCode')
    ->like('WhsCode', $txt)
    ->or_like('WhsName', $txt)
    ->get('OWHS');

    $arr = array('none');

    if($rs->num_rows() > 0)
    {
      foreach($rs->result() as $wh)
      {
        $arr[] = $wh->WhsCode;
      }
    }

    return $arr;
  }



  public function get_max_code($code)
  {
    $rs = $this->db
    ->select_max('code')
    ->like('code', $code, 'after')
    ->order_by('code', 'DESC')
    ->get('transfer');

    return $rs->row()->code;
  }




  public function set_export($code, $value)
  {
    return $this->db->set('is_export', $value)->where('code', $code)->update('transfer');
  }


  public function get_non_inv_code($limit = 100)
  {
    $rs = $this->db
    ->select('code')
    ->where('status', 1)
		->where('is_export', 1)
    ->where('inv_code IS NULL', NULL, FALSE)
    ->limit($limit)
    ->get('transfer');

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return  NULL;
  }


  public function update_inv($code, $doc_num)
  {
    return $this->db->set('inv_code', $doc_num)->where('code', $code)->update('transfer');
  }


  public function is_document_avalible($code, $uuid)
  {
    $rs = $this->db
    ->where('code', $code)
    ->where('session_uuid !=', $uuid)
    ->where('session_expire >=', date('Y-m-d H:i:s'))
    ->count_all_results('transfer');

    if($rs == 0)
    {
      return TRUE;
    }

    return FALSE;
  }


  public function update_uuid($code, $uuid)
  {
    $expiration = date('Y-m-d H:i:s', time() + 1 * 60);
    $ds = array(
      'session_uuid' => $uuid,
      'session_expire' => $expiration
    );

    return $this->db->where('code', $code)->update('transfer', $ds);
  }


  public function must_accept($code)
  {
    $count = $this->db
    ->from('transfer_detail AS t')
    ->join('zone AS z', 't.to_zone = z.code', 'left')
    ->where('t.transfer_code', $code)
    ->where('z.user_id IS NOT NULL', NULL, FALSE)
    ->count_all_results();

    if($count > 0)
    {
      return TRUE;
    }

    return FALSE;
  }

  public function get_accept_list($code)
  {
    $rs = $this->db
    ->select('t.is_accept, t.accept_by, t.accept_on')
    ->select('u.uname, u.name AS display_name')
    ->from('transfer_detail AS t')
    ->join('zone AS z', 't.to_zone = z.code', 'left')
    ->join('user AS u', 'z.user_id = u.id', 'left')
    ->where('t.transfer_code', $code)
    ->where('t.must_accept', 1)
    ->where('z.user_id IS NOT NULL', NULL, FALSE)
    ->group_by('z.user_id')
    ->get();

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }

  public function is_accept_all($code)
  {
    $count = $this->db
    ->where('transfer_code', $code)
    ->where('must_accept', 1)
    ->where('is_accept', 0)
    ->count_all_results('transfer_detail');

    if($count == 0)
    {
      return TRUE;
    }

    return FALSE;
  }


  public function accept_all($code, $uname)
  {
    $arr = array(
      'is_accept' => 1,
      'accept_by' => $uname,
      'accept_on' => now()
    );

    $this->db
    ->where('transfer_code', $code)
    ->where('must_accept', 1)
    ->where('is_accept', 0);

    return $this->db->update('transfer_detail', $arr);
  }


  public function is_owner_zone($code, $user_id)
  {
    $count = $this->db
    ->from('transfer_detail AS t')
    ->join('zone AS zn', 't.to_zone = zn.code', 'left')
    ->where('t.move_code', $code)
    ->where('t.must_accept', 1)
    ->where('zn.user_id', $this->_user->id)
    ->count_all_results();

    if($count > 0)
    {
      return TRUE;
    }

    return FALSE;
  }


  public function accept_zone($code, $zone_code, $uname)
  {
    $arr = array(
      'is_accept' => 1,
      'accept_by' => $uname,
      'accept_on' => now()
    );

    return $this->db->where('transfer_code', $code)->where('to_zone', $zone_code)->update('transfer_detail', $arr);
  }


  public function get_my_zone($code, $user_id)
  {
    $rs = $this->db
    ->select('t.to_zone')
    ->from('transfer_detail AS t')
    ->join('zone AS zn', 't.to_zone = zn.code', 'left')
    ->where('transfer_code', $code)
    ->where('t.must_accept', 1)
    ->where('zn.user_id', $user_id)
    ->group_by('t.to_zone')
    ->get();

    if($rs->num_rows() > 0)
    {
      return $rs->row()->to_zone;
    }

    return NULL;
  }

}
 ?>
