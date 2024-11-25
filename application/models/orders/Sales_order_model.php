<?php
class Sales_order_model extends CI_Model
{
  private $tb = "sale_order";
  private $td = "sale_order_detail";
  private $tl = "sale_order_logs";

  public function __construct()
  {
    parent::__construct();
  }


  public function add_logs(array $ds = array())
  {
    if( ! empty($ds))
    {
      return $this->db->insert($this->tl, $ds);
    }

    return FALSE;
  }

  public function get_logs($code)
  {
    $rs = $this->db->where('code', $code)->get($this->tl);

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function get_order_ref($code)
  {
    $rs = $this->db
    ->select('code')
    ->where_in('role', ['S', 'Q', 'T'])
    ->where('so_code', $code)
    ->where('status !=', 2)
    ->get('orders');

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }

  public function get_bill_ref($code)
  {
    $rs = $this->db
    ->select('code')
    ->where('so_code', $code)
    ->where('status !=', 'D')
    ->get('order_pos');

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function get_wo_ref($code)
  {
    $rs = $this->db
    ->select('code')
    ->where('role', 'S')
    ->where('so_code', $code)
    ->where('status !=', 2)
    ->get('orders');

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function get_wq_ref($code)
  {
    $rs = $this->db
    ->select('code')
    ->where_in('role', ['Q', 'T'])
    ->where('so_code', $code)
    ->where('status !=', 2)
    ->get('orders');

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


  public function get_details($code)
  {
    $rs = $this->db->where('order_code', $code)->get($this->td);

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function get_detail($id)
  {
    $rs = $this->db->where('id', $id)->get($this->td);

    if($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return NULL;
  }


  public function get_open_details($code)
  {
    $rs = $this->db
    ->where('order_code', $code)
    ->where('line_status', 'O')
    ->where('OpenQty >', 0)
    ->get($this->td);

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function get_commit_qty($so_line_id)
  {
    $rs = $this->db
    ->select('od.qty, od.billed_qty')
    ->from('order_details AS od')
    ->join('orders AS o', 'od.id_order = o.id', 'left')
    ->where('od.line_id', $so_line_id)
    ->where('o.role', 'S')
    ->where('od.is_complete', 0)
    ->where('od.is_expired', 0)
    ->where('od.is_cancle', 0)
    ->get();

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }

  public function get_transform_list($code)
  {
    $rs = $this->db->where('so_code', $code)->get('order_transform');

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

    return FALSE;
  }


  public function update_detail($id, array $ds = array())
  {
    if( ! empty($ds))
    {
      return $this->db->where('id', $id)->update($this->td, $ds);
    }

    return FALSE;
  }


  public function update_details($code, array $ds = array())
  {
    if( ! empty($ds))
    {
      return $this->db->where('order_code', $code)->update($this->td, $ds);
    }

    return FALSE;
  }


  public function drop_details($code)
  {
    return $this->db->where('order_code', $code)->delete($this->td);
  }


  public function drop_not_exists_id($sales_order_id, array $detail_ids = array())
  {
    return $this->db->where('id_order', $sales_order_id)->where_not_in('id', $detail_ids)->delete($this->td);
  }


  public function get_list(array $ds = array(), $perpage = 20, $offset = 0)
  {
    if( ! empty($ds['code']))
    {
      $this->db->like('code', $ds['code']);
    }

    if( ! empty($ds['ref_code']))
    {
      $this->db->like('ref_code', $ds['ref_code']);
    }

    if( ! empty($ds['bill_code']))
    {
      $this->db->like('bill_code', $ds['bill_code']);
    }

    if( ! empty($ds['job_title']))
    {
      $this->db->like('job_title', $ds['job_title']);
    }

    if( ! empty($ds['customer_ref']))
    {
      $this->db->like('customer_ref', $ds['customer_ref']);
    }

    if( ! empty($ds['phone']))
    {
      $this->db->like('phone', $ds['phone']);
    }

    if( ! empty($ds['job_type']) && $ds['job_type'] != 'all')
    {
      $this->db->where('job_type', $ds['job_type']);
    }

    if( ! empty($ds['user']) && $ds['user'] != 'all')
    {
      $this->db->where('user', $ds['user']);
    }

    if( ! empty($ds['status']) && $ds['status'] != 'all')
    {
      $this->db->where('status', $ds['status']);
    }

    if( ! empty($ds['from_date']))
    {
      $this->db->where('date_add >=', from_date($ds['from_date']));
    }

    if( ! empty($ds['to_date']))
    {
      $this->db->where('date_add <=', to_date($ds['to_date']));
    }

    if( ! empty($ds['due_from_date']))
    {
      $this->db->where('due_date >=', from_date($ds['due_from_date']));
    }

    if( ! empty($ds['due_to_date']))
    {
      $this->db->where('due_date <=', to_date($ds['due_to_date']));
    }

    if(!empty($ds['onlyMe']))
    {
      $this->db->where('user', $this->_user->uname);
    }

    if( ! empty($ds['state']) && $ds['state'] != 'all')
    {
      $this->db->where('state', $ds['state']);
    }

    $rs = $this->db->order_by('code', 'DESC')->limit($perpage, $offset)->get($this->tb);

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;

  } //-- get_list


  public function count_rows(array $ds = array())
  {
    if( ! empty($ds['code']))
    {
      $this->db->like('code', $ds['code']);
    }

    if( ! empty($ds['ref_code']))
    {
      $this->db->like('ref_code', $ds['ref_code']);
    }

    if( ! empty($ds['bill_code']))
    {
      $this->db->like('bill_code', $ds['bill_code']);
    }

    if( ! empty($ds['job_title']))
    {
      $this->db->like('job_title', $ds['job_title']);
    }

    if( ! empty($ds['customer_ref']))
    {
      $this->db->like('customer_ref', $ds['customer_ref']);
    }

    if( ! empty($ds['phone']))
    {
      $this->db->like('phone', $ds['phone']);
    }

    if( ! empty($ds['job_type']) && $ds['job_type'] != 'all')
    {
      $this->db->where('job_type', $ds['job_type']);
    }

    if( ! empty($ds['user']) && $ds['user'] != 'all')
    {
      $this->db->where('user', $ds['user']);
    }

    if( ! empty($ds['status']) && $ds['status'] != 'all')
    {
      $this->db->where('status', $ds['status']);
    }

    if( ! empty($ds['from_date']))
    {
      $this->db->where('date_add >=', from_date($ds['from_date']));
    }

    if( ! empty($ds['to_date']))
    {
      $this->db->where('date_add <=', to_date($ds['to_date']));
    }

    if( ! empty($ds['due_from_date']))
    {
      $this->db->where('due_date >=', from_date($ds['due_from_date']));
    }

    if( ! empty($ds['due_to_date']))
    {
      $this->db->where('due_date <=', to_date($ds['due_to_date']));
    }

    if(!empty($ds['onlyMe']))
    {
      $this->db->where('user', $this->_user->uname);
    }

    if( ! empty($ds['state']) && $ds['state'] != 'all')
    {
      $this->db->where('state', $ds['state']);
    }

    return $this->db->count_all_results($this->tb);

  } //--- count_rows


  //--- เช็คว่า sales_order_detail_id นี้ link ไปที่เอกสารอื่นหรือเปล่า (ใช้กรณียกเลิก หรือ unlink เอกสาร)
  public function has_linked($id)
  {
    $order = $this->db->where('line_id', $id)->where('is_cancle', 0)->where('is_expired', 0)->count_all_results('order_details');
    $pos = $this->db->where('line_id', $id)->where('status !=', 'D')->count_all_results('order_pos_detail');

    return ($order > 0 OR $pos > 0) ? TRUE : FALSE;
  }


  public function count_open_line($code)
  {
    return $this->db->where('order_code', $code)->where('line_status', 'O')->count_all_results($this->td);
  }


  public function get_max_code($code)
  {
    $rs = $this->db->select_max('code')->like('code', $code)->order_by('code', 'DESC')->get('sale_order');

    if($rs->num_rows() === 1)
    {
      return $rs->row()->code;
    }

    return NULL;
  }
} //-- end class

 ?>
