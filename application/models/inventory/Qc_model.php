<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Qc_model extends CI_Model
{
  public function __construct()
  {
    parent::__construct();
  }


  public function add(array $ds = array())
  {
    return $this->db->insert('qc', $ds);
  }



  public function update($order_code, $product_code, $box_id, $qty, $detail_id = NULL)
  {
    $this->db
    ->set("qty", "qty + {$qty}", FALSE)
    ->set('user', $this->_user->uname)
    ->where('order_code', $order_code)
    ->where('product_code', $product_code)
    ->where('box_id', $box_id)
    ->group_start()
    ->where('order_detail_id', $detail_id)
    ->or_where('order_detail_id IS NULL', NULL, FALSE)
    ->group_end();

    return $this->db->update('qc');
  }


  public function get($id)
  {
    $rs = $this->db->where('id', $id)->get('qc');

    if($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return FALSE;
  }




  public function get_data(array $ds = array(), $state = 5, $perpage = NULL, $offset = NULL)
  {
    $this->db->select('orders.*, channels.name AS channels_name, customers.name AS customer_name')
    ->from('orders')
    ->join('channels', 'channels.code = orders.channels_code','left')
    ->join('customers', 'customers.code = orders.customer_code', 'left')
    ->where('orders.state', $state);

    if( ! empty($ds['code']))
    {
      $this->db->like('orders.code', $ds['code']);
    }

    if( ! empty($ds['reference']))
    {
      $this->db->like('orders.reference', $ds['reference']);
    }

    if(!empty($ds['customer']))
    {
      $this->db->like('customers.name', $ds['customer']);
      $this->db->or_like('orders.customer_ref', $ds['customer']);
    }

    //---- user name / display name
    if(!empty($ds['user']))
    {
      $users = user_in($ds['user']);
      $this->db->where_in('orders.user', $users);
    }

    if(!empty($ds['channels']))
    {
      $this->db->where('orders.channels_code', $ds['channels']);
    }

    if($ds['from_date'] != '' && $ds['to_date'] != '')
    {
      $this->db->where('orders.date_add >=', from_date($ds['from_date']));
      $this->db->where('orders.date_add <=', to_date($ds['to_date']));
    }

    if(!empty($ds['order_by']))
    {
      $order_by = "orders.{$ds['order_by']}";
      $this->db->order_by($order_by, $ds['sort_by']);
    }
    else
    {
      $this->db->order_by('orders.date_add', 'DESC');
    }

    if(!empty($perpage))
    {
      $offset = $offset === NULL ? 0 : $offset;
      $this->db->limit($perpage, $offset);
    }

    $rs = $this->db->get();

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return FALSE;
  }



  public function count_rows(array $ds = array(), $state = 5)
  {
    $this->db->select('orders.*, channels.name AS channels_name, customers.name AS customer_name')
    ->from('orders')
    ->join('channels', 'channels.code = orders.channels_code','left')
    ->join('customers', 'customers.code = orders.customer_code', 'left')
    ->where('orders.state', $state);

    if(!empty($ds['code']))
    {
      $this->db->like('orders.code', $ds['code']);
    }

    if( ! empty($ds['reference']))
    {
      $this->db->like('orders.reference', $ds['reference']);
    }

    if(!empty($ds['customer']))
    {
      $this->db->like('customers.name', $ds['customer']);
      $this->db->or_like('orders.customer_ref', $ds['customer']);
    }

    //---- user name / display name
    if(!empty($ds['user']))
    {
      $users = user_in($ds['user']);
      $this->db->where_in('orders.user', $users);
    }

    if(!empty($ds['channels']))
    {
      $this->db->where('orders.channels_code', $ds['channels']);
    }

    if($ds['from_date'] != '' && $ds['to_date'] != '')
    {
      $this->db->where('orders.date_add >=', from_date($ds['from_date']));
      $this->db->where('orders.date_add <=', to_date($ds['to_date']));
    }

    return $this->db->count_all_results();
  }


  //--- รายการที่ตรวจครบแล้ว
  public function get_complete_list($order_code)
  {
    $qr = "SELECT o.id, o.product_code, o.product_name, o.is_count, pd.old_code, ";
    $qr .= "(SELECT SUM(qty) FROM order_details WHERE order_code = '{$order_code}' AND product_code = o.product_code) AS order_qty, ";
    $qr .= "(SELECT SUM(qty) FROM buffer WHERE order_code = '{$order_code}' AND product_code = o.product_code) AS prepared, ";
    $qr .= "(SELECT SUM(qty) FROM qc WHERE order_code = '{$order_code}' AND product_code = o.product_code) AS qc ";
    $qr .= "FROM order_details AS o ";
    $qr .= "LEFT JOIN products AS pd ON o.product_code = pd.code ";
    $qr .= "WHERE o.order_code = '{$order_code}' GROUP BY o.product_code HAVING prepared <= qc OR order_qty <= qc";

    $rs = $this->db->query($qr);

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return FALSE;
  }


  //--- รายการที่ยังไม่ได้ตรวจหรือยังตรวจไม่ครบ
  public function get_in_complete_list($order_code)
  {
    $qr = "SELECT o.id, o.product_code, o.product_name, o.is_count, pd.old_code, ";
    $qr .= "(SELECT SUM(qty) FROM order_details WHERE order_code = '{$order_code}' AND product_code = o.product_code) AS order_qty, ";
    $qr .= "(SELECT SUM(qty) FROM buffer WHERE order_code = '{$order_code}' AND product_code = o.product_code) AS prepared, ";
    $qr .= "(SELECT SUM(qty) FROM qc WHERE order_code = '{$order_code}' AND product_code = o.product_code) AS qc ";
    $qr .= "FROM order_details AS o ";
    $qr .= "JOIN buffer AS b ON o.product_code = b.product_code ";
    $qr .= "LEFT JOIN products AS pd ON o.product_code = pd.code ";
    $qr .= "WHERE o.order_code = '{$order_code}' AND o.is_count = 1 ";
    $qr .= "GROUP BY o.product_code HAVING ( (prepared > qc AND order_qty > qc) OR qc IS NULL )";


    $rs = $this->db->query($qr);

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return FALSE;
  }


  //--- รายการกล่องทั้งหมดที่ตรวจในออเดอร์ที่กำหนด
  public function get_box_list($order_code)
  {
    $qr = "SELECT b.id, b.box_no, SUM(q.qty) AS qty FROM qc_box AS b ";
    $qr .= "LEFT JOIN qc AS q ON b.id = q.box_id AND b.order_code = q.order_code ";
    $qr .= "WHERE b.order_code = '{$order_code}' GROUP BY b.id";

    $rs = $this->db->query($qr);
    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return FALSE;
  }



  public function get_box($order_code, $barcode)
  {
    $rs = $this->db
    ->where('order_code', $order_code)
    ->where('code', $barcode)
    ->get('qc_box');
    if($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return FALSE;
  }


  public function get_last_box_no($order_code)
  {
    $rs = $this->db
    ->select_max('box_no', 'box_no')
    ->where('order_code', $order_code)
    ->get('qc_box');

    return intval($rs->row()->box_no);
  }


  public function add_new_box($order_code, $barcode, $box_no)
  {
    $arr = array(
      'code' => $barcode,
      'order_code' => $order_code,
      'box_no' => $box_no
    );

    $rs = $this->db->insert('qc_box', $arr);
    if($rs)
    {
      return $this->db->insert_id();
    }

    return FALSE;
  }



  //--- จำนวนรวมของสินค้าที่ตรวจแล้วทั้งออเดอร์(ไม่รวมที่ยังไม่ตรวจ)
  public function total_qc($order_code)
  {
    $qr  = "SELECT SUM(qty) AS qty FROM qc ";
    $qr .= "WHERE order_code = '{$order_code}' ";
    $qr .= "AND product_code IN((SELECT product_code FROM order_details WHERE order_code = '{$order_code}'))";

    $rs = $this->db->query($qr);

    return intval($rs->row()->qty);
  }

  public function get_sum_order_qty($order_code)
  {
    $qr  = "SELECT SUM(qty) AS qty FROM qc ";
    $qr .= "WHERE order_code = '{$order_code}' ";
    $qr .= "AND product_code IN((SELECT product_code FROM order_details WHERE order_code = '{$order_code}'))";

    $rs = $this->db->query($qr);

    return intval($rs->row()->qty);
  }


  //---- ยอดรวมสินค้าที่ตรวจไปแล้ว
  public function get_sum_qty($order_code, $product_code, $detail_id = NULL)
  {
    $rs = $this->db->select_sum('qty')
    ->where('order_code', $order_code)
    ->where('product_code', $product_code)
    ->group_start()
    ->where('order_detail_id', $detail_id)
    ->or_where('order_detail_id IS NULL', NULL, FALSE)
    ->group_end()
    ->get('qc');

    return intval($rs->row()->qty);
  }

  //----  ถ้ามีรายการที่ตรวจอยู่แล้ว
  public function is_exists($order_code, $product_code, $id_box, $detail_id = NULL)
  {
    $rows = $this->db
    ->where('order_code', $order_code)
    ->where('product_code', $product_code)
    ->where('box_id', $id_box)
    ->group_start()
    ->where('order_detail_id', $detail_id)
    ->or_where('order_detail_id IS NULL', NULL, FALSE)
    ->group_end()
    ->count_all_results('qc');

    if($rows > 0)
    {
      return TRUE;
    }

    return FALSE;
  }



  public function update_checked($order_code, $product_code, $box_id, $qty, $detail_id = NULL)
  {
    if( $this->is_exists($order_code, $product_code, $box_id, $detail_id))
    {
      return $this->update($order_code, $product_code, $box_id, $qty, $detail_id);
    }
    else
    {
			$arr = array(
				'order_code' => $order_code,
				'product_code' => $product_code,
				'box_id' => $box_id,
				'qty' => $qty,
        'order_detail_id' => $detail_id,
				'user' => $this->_user->uname
			);

      return $this->add($arr);
    }
  }



  public function update_qty($id, $qty)
  {
    return $this->db->set("qty", "qty + {$qty}", FALSE)->where('id', $id)->update('qc');
  }


	public function drop_qc($order_code)
	{
		return $this->db->where('order_code', $order_code)->delete('qc');
	}



  public function drop_zero_qc($order_code)
  {
    return $this->db->where('order_code', $order_code)->where('qty <=', 0)->delete('qc');
  }


  public function get_box_details($order_code, $box_id)
  {
    $rs = $this->db
    ->select('b.box_no')
    ->select('od.product_code, od.product_name')
    ->select_sum('qc.qty')
    ->from('qc')
    ->join('order_details AS od', 'od.order_code = qc.order_code AND od.product_code = qc.product_code AND (qc.order_detail_id = od.id OR qc.order_detail_id IS NULL)', 'left')
    ->join('qc_box AS b', 'b.id = qc.box_id')
    ->where('qc.order_code', $order_code)
    ->where('qc.box_id', $box_id)
    ->group_by('qc.product_code')
    ->get();

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return FALSE;
  }



  public function get_box_no($id)
  {
    $rs = $this->db->select('box_no')->where('id', $id)->get('qc_box');
    if($rs->num_rows() === 1)
    {
      return $rs->row()->box_no;
    }

    return FALSE;
  }



  public function get_checked_table($order_code, $product_code)
  {
    $this->db
    ->select('qc.*')
    ->select('qc_box.code AS barcode, qc_box.box_no')
    ->from('qc')
    ->join('qc_box', 'qc.box_id = qc_box.id', 'left')
    ->where('qc.order_code', $order_code)
    ->where('qc.product_code',$product_code)
    ->order_by('qc_box.box_no');

    $rs = $this->db->get();

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return FALSE;
  }

  public function count_box($code)
  {
    $rs = $this->db->select('box_id')
    ->where('order_code', $code)
    ->group_by('box_id')
    ->get('qc');

    return intval($rs->num_rows());
  }


  public function delete_qc($id)
  {
    return $this->db->where('id', $id)->delete('qc');
  }


  public function clear_qc($code)
  {
    return $this->db->where('order_code', $code)->delete('qc');
  }

  public function delete_by_order_detail_id($order_detail_id)
  {
    return $this->db->where('order_detail_id', $order_detail_id)->delete('qc');
  }

} //--- end class

 ?>
