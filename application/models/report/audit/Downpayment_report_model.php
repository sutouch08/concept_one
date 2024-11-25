<?php
class Downpayment_report_model extends CI_Model
{
  public function __construct()
	{
		parent::__construct();
	}


  public function get_summary_by_date_and_payment_role($date, $payment_role, $shop_id = 'all', $pos_id = 'all')
  {
    $this->db
    ->select_sum('pm.amount')
    ->from('order_pos_payment AS pm')
    ->join('order_down_payment AS dp', 'pm.code = dp.code', 'left')
    ->where('dp.status !=', 'D')
    ->where('dp.date_add >=', from_date($date))
    ->where('dp.date_add <=', to_date($date))
    ->where('pm.payment_role', $payment_role);

    if($shop_id != 'all')
    {
      $this->db->where('dp.shop_id', $shop_id);
    }

    if($pos_id != 'all')
    {
      $this->db->where('dp.pos_id', $pos_id);
    }

    $rs = $this->db->get();

    return $rs->row()->amount;
  }


  public function get_summary_by_date($date, $shop_id = 'all', $pos_id = 'all')
  {
    $this->db
    ->select_sum('amount')
    ->where('status !=', 'D')
    ->where('date_add >=', from_date($date))
    ->where('date_add <=', to_date($date));

    if($shop_id != 'all')
    {
      $this->db->where('shop_id', $shop_id);
    }

    if($pos_id != 'all')
    {
      $this->db->where('pos_id', $pos_id);
    }

    $rs = $this->db->get('order_down_payment');

    return $rs->row()->amount;
  }


  public function get_list(array $ds = array())
  {
    $this->db
    ->select('dp.*, sh.name AS shop_name, ps.name as pos_name')
    ->from('order_down_payment AS dp')
    ->join('shop AS sh', 'dp.shop_id = sh.id', 'left')
    ->join('shop_pos AS ps', 'dp.pos_id = ps.id', 'left')
    ->where('dp.status !=', 'D');

    if( ! empty($ds['from_date']) && ! empty($ds['to_date']))
    {
      $this->db->where('dp.date_add >=', from_date($ds['from_date']))->where('dp.date_add <=', to_date($ds['to_date']));
    }

    if( ! empty($ds['shop_id']) && $ds['shop_id'] != 'all')
    {
      $this->db->where('dp.shop_id', $ds['shop_id']);
    }

    if( ! empty($ds['pos_id']) && $ds['pos_id'] != 'all')
    {
      $this->db->where('dp.pos_id', $ds['pos_id']);
    }

    $rs = $this->db->get();

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function get_payments($code)
  {
    $rs = $this->db->where('code', $code)->get('order_pos_payment');

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function get_down_payment($filter)
  {
    $this->db->where('status !=', 'D');

    if( ! empty($filter->fromDate))
    {
      $this->db->where('date_add >=', from_date($filter->fromDate));
    }

    if( ! empty($filter->toDate))
    {
      $this->db->where('date_add <=', to_date($filter->toDate));
    }

    if( ! empty($filter->status) && $filter->status != 'all')
    {
      $this->db->where('status', $filter->status);
    }

    if( ! empty($filter->code))
    {
      $this->db->like('code', $filter->code);
    }

    if( ! empty($filter->reference))
    {
      $this->db->like('reference', $filter->reference);
    }

    if( isset($filter->phone) && $filter->phone != "" && $filter->phone != NULL)
    {
      $this->db->like('customer_phone', $filter->phone);
    }

    if( ! empty($filter->customer_code))
    {
      $this->db->like('customer_code', $filter->customer_code);
    }

    if( ! empty($filter->customer_name))
    {
      $this->db
      ->group_start()
      ->like('customer_name', $filter->customer_name)
      ->or_like('customer_ref', $filter->customer_name)
      ->group_end();
    }

    $rs = $this->db->order_by('date_add', 'ASC')->get('order_down_payment');

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function get_down_payment_details($id)
  {
    $rs = $this->db->where('down_payment_id', $id)->where('is_cancel', 0)->get('order_down_payment_details');

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }

  public function get_invoice_code($BaseRef)
  {
    $rs = $this->db
    ->select('code')
    ->where('BaseRef', $BaseRef)
    ->where('status !=', 'D')
    ->get('invoice');

    if($rs->num_rows() === 1)
    {
      return $rs->row()->code;
    }

    return NULL;
  }
} //-- end class

 ?>
