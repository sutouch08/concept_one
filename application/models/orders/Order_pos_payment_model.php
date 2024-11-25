<?php
class Order_pos_payment_model extends CI_Model
{
  private $tb = "order_pos_payment";

  public function __construct()
  {
    parent::__construct();
  }


  public function add(array $ds = array())
  {
    if( ! empty($ds))
    {
      return $this->db->insert($this->tb, $ds);
    }

    return FALSE;
  }

  public function delete_by_code($code)
  {
    return $this->db->where('code', $code)->delete($this->tb);
  }


  public function delete_by_id($id)
  {
    return $this->db->where('id', $id)->delete($this->tb);
  }


  public function get_payments($code)
  {
    $rs = $this->db->where('code', $code)->get($this->tb);

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function get_cash_payment($code)
  {
    $rs = $this->db->where('code', $code)->where('payment_role', 1)->get($this->tb);

    if($rs->num_rows() > 0)
    {
      return $rs->row();
    }

    return NULL;
  }
} //--- end class

 ?>
