<?php
class Exp_model extends CI_Model
{
  public $exp_status = array(-1, 0, 4);

  public function __construct()
  {
    parent::__construct();
  }

  public function expire_ww($exp_date)
  {
    $arr = array('is_expire' => 1);

    $this->db
    ->where_in('status', $this->exp_status)
    ->where('date_add <=', $exp_date)
    ->where('is_expire', 0);

    return $this->db->update('transfer', $arr);
  }

  public function expire_mv($exp_date)
  {
    $arr = array('is_expire' => 1);

    $this->db
    ->where_in('status', $this->exp_status)
    ->where('date_add <=', $exp_date)
    ->where('is_expire', 0);

    return $this->db->update('move', $arr);
  }


  public function expire_rt($exp_date)
  {
    $arr = array('is_expire' => 1);

    $this->db
    ->where_in('status', $this->exp_status)
    ->where('date_add <=', $exp_date)
    ->where('is_expire', 0);

    return $this->db->update('receive_transform', $arr);
  }

  public function expire_rn($exp_date)
  {
    $arr = array('is_expire' => 1);

    $this->db
    ->where_in('status', $this->exp_status)
    ->where('date_add <=', $exp_date)
    ->where('is_expire', 0);

    return $this->db->update('return_lend', $arr);
  }

  public function expire_wr($exp_date)
  {
    $arr = array('is_expire' => 1);

    $this->db
    ->where_in('status', $this->exp_status)
    ->where('date_add <=', $exp_date)
    ->where('is_expire', 0);

    return $this->db->update('receive_product', $arr);
  }

  public function expire_sm($exp_date)
  {
    $arr = array('is_expire' => 1);

    $this->db
    ->where_in('status', $this->exp_status)
    ->where('date_add <=', $exp_date)
    ->where('is_expire', 0);

    return $this->db->update('return_order', $arr);
  }
} //-- end class

 ?>
