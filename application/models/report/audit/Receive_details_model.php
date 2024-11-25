<?php
class Receive_details_model extends CI_Model
{
  public function __construct()
  {
    parent::__construct();
  }

  public function WR(array $ds = array())
  {
    if( ! empty($ds))
    {
      $this->db
      ->where('date_add >=', from_date($ds['from_date']))
      ->where('date_add <=', to_date($ds['to_date']));

      if($ds['is_expired'] != 'all')
      {
        $this->db->where('is_expire', $ds['is_expired']);
      }

      if($ds['all_state'] == 0 && ! empty($ds['state']))
      {
        $this->db->where_in('status', $ds['state']);
      }

      if($ds['all_warehouse'] == 0 && ! empty($ds['warehouse']))
      {
        $this->db->where_in('warehouse_code', $ds['warehouse']);
      }

      $this->db->order_by('date_add', 'ASC')->order_by('code', 'ASC');

      $rs = $this->db->get('receive_product');

      if($rs->num_rows() > 0)
      {
        return $rs->result();
      }
    }

    return NULL;
  }

  public function RT(array $ds = array())
  {
    if( ! empty($ds))
    {
      $this->db
      ->select('rt.*, od.customer_code, c.name AS customer_name')
      ->from('receive_transform AS rt')
      ->join('orders AS od', "rt.order_code = od.code", 'left')
      ->join('customers AS c', "od.customer_code = c.code", 'left')
      ->where('rt.date_add >=', from_date($ds['from_date']))
      ->where('rt.date_add <=', to_date($ds['to_date']));

      if($ds['is_expired'] != 'all')
      {
        $this->db->where('rt.is_expire', $ds['is_expired']);
      }

      if($ds['all_state'] == 0 && ! empty($ds['state']))
      {
        $this->db->where_in('rt.status', $ds['state']);
      }

      if($ds['all_warehouse'] == 0 && ! empty($ds['warehouse']))
      {
        $this->db->where_in('rt.warehouse_code', $ds['warehouse']);
      }

      $this->db->order_by('rt.date_add', 'ASC')->order_by('rt.code', 'ASC');

      $rs = $this->db->get();

      if($rs->num_rows() > 0)
      {
        return $rs->result();
      }
    }

    return NULL;
  }


  public function RN(array $ds = array())
  {
    if( ! empty($ds))
    {
      $this->db
      ->where('date_add >=', from_date($ds['from_date']))
      ->where('date_add <=', to_date($ds['to_date']));

      if($ds['is_expired'] != 'all')
      {
        $this->db->where('is_expire', $ds['is_expired']);
      }

      if($ds['all_state'] == 0 && ! empty($ds['state']))
      {
        $this->db->where_in('status', $ds['state']);
      }

      if($ds['all_warehouse'] == 0 && ! empty($ds['warehouse']))
      {
        $this->db->where_in('to_warehouse', $ds['warehouse']);
      }

      $this->db->order_by('date_add', 'ASC')->order_by('code', 'ASC');

      $rs = $this->db->get('return_lend');

      if($rs->num_rows() > 0)
      {
        return $rs->result();
      }
    }

    return NULL;
  }


  public function SM(array $ds = array())
  {
    if( ! empty($ds))
    {
      $this->db
      ->where('date_add >=', from_date($ds['from_date']))
      ->where('date_add <=', to_date($ds['to_date']));

      if($ds['is_expired'] != 'all')
      {
        $this->db->where('is_expire', $ds['is_expired']);
      }

      if($ds['all_state'] == 0 && ! empty($ds['state']))
      {
        $this->db->where_in('status', $ds['state']);
      }

      if($ds['all_warehouse'] == 0 && ! empty($ds['warehouse']))
      {
        $this->db->where_in('warehouse_code', $ds['warehouse']);
      }

      $this->db->order_by('date_add', 'ASC')->order_by('code', 'ASC');

      $rs = $this->db->get('return_order');

      if($rs->num_rows() > 0)
      {
        return $rs->result();
      }
    }

    return NULL;
  }


  public function CN(array $ds = array())
  {
    if( ! empty($ds))
    {
      $this->db
      ->where('date_add >=', from_date($ds['from_date']))
      ->where('date_add <=', to_date($ds['to_date']));

      if($ds['all_state'] == 0 && ! empty($ds['state']))
      {
        $this->db->where_in('status', $ds['state']);
      }

      if($ds['all_warehouse'] == 0 && ! empty($ds['warehouse']))
      {
        $this->db->where_in('warehouse_code', $ds['warehouse']);
      }

      $this->db->order_by('date_add', 'ASC')->order_by('code', 'ASC');

      $rs = $this->db->get('return_consignment');

      if($rs->num_rows() > 0)
      {
        return $rs->result();
      }
    }

    return NULL;
  }


  public function get_doc_total($code, $role)
  {
    $tb = array(
      'WR' => 'receive_product_detail',
      'RT' => 'receive_transform_detail',
      'RN' => 'return_lend_detail',
      'SM' => 'return_order_detail',
      'CN' => 'return_consignment_detail'
    );

    $field = array(
      'WR' => 'receive_code',
      'RT' => 'receive_code',
      'RN' => 'return_code',
      'SM' => 'return_code',
      'CN' => 'return_code'
    );


    $rs = $this->db->select_sum('amount')->where($field[$role], $code)->get($tb[$role]);

    if($rs->num_rows() === 1)
    {
      return $rs->row()->amount;
    }

    return 0.00;
  }

} //-- end class

 ?>
