<?php
class Transport_model extends CI_Model
{
  public function __construct()
  {
    parent::__construct();
  }

  public function add(array $ds = array())
  {
    if(!empty($ds))
    {
      return $this->db->insert('address_transport', $ds);
    }

    return FALSE;
  }


  public function update($id, array $ds = array())
  {
    if(!empty($ds))
    {
      return $this->db->where('id', $id)->update('address_transport', $ds);
    }

    return FALSE;
  }



  public function delete($id)
  {
    return $this->db->where('id', $id)->delete('address_transport');
  }


  public function get($id)
  {
    $rs = $this->db->where('id', $id)->get('address_transport');
    if($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return FALSE;
  }



  public function get_name($id)
  {
    $rs = $this->db->where('id', $id)->get('address_transport');
    if($rs->num_rows() === 1)
    {
      return $rs->row()->name;
    }

    return NULL;
  }



  public function is_exists($customer_code, $id = NULL)
  {
    if(! empty($id))
    {
      $rs = $this->db->where('customer_code', $customer_code)->where('id !=',$id)->get('address_transport');
    }
    else
    {
      $rs = $this->db->where('customer_code', $customer_code)->get('address_transport');
    }

    if($rs->num_rows() > 0)
    {
      return TRUE;
    }

    return FALSE;
  }



  public function count_rows(array $ds = array())
  {
    $this->db
    ->from('address_transport AS t')
    ->join('customers AS c', 't.customer_code = c.code', 'left')
    ->join('address_sender AS s1', 't.main_sender = s1.id', 'left')
    ->join('address_sender AS s2', 't.second_sender = s2.id', 'left')
    ->join('address_sender AS s3', 't.third_sender = s3.id', 'left');

    if(!empty($ds['name']))
    {
      $this->db
      ->group_start()
      ->like('t.customer_code', $ds['name'])
      ->or_like('c.name', $ds['name'])
      ->group_end();
    }

    if( ! empty($ds['sender']))
    {
      $this->db
      ->group_start()
      ->like('s1.name', $ds['sender'])
      ->or_like('s2.name', $ds['sender'])
      ->or_like('s3.name', $ds['sender'])
      ->group_end();
    }

    return $this->db->count_all_results();
  }


  public function get_list(array $ds = array(), $perpage = 20, $offset = 0)
  {
    $this->db
    ->select('t.*, c.name AS customer_name, s1.name AS main_name, s2.name AS second_name, s3.name AS third_name')
    ->from('address_transport AS t')
    ->join('customers AS c', 't.customer_code = c.code', 'left')
    ->join('address_sender AS s1', 't.main_sender = s1.id', 'left')
    ->join('address_sender AS s2', 't.second_sender = s2.id', 'left')
    ->join('address_sender AS s3', 't.third_sender = s3.id', 'left');

    if(!empty($ds['name']))
    {
      $this->db
      ->group_start()
      ->like('t.customer_code', $ds['name'])
      ->or_like('c.name', $ds['name'])
      ->group_end();
    }

    if( ! empty($ds['sender']))
    {
      $this->db
      ->group_start()
      ->like('s1.name', $ds['sender'])
      ->or_like('s2.name', $ds['sender'])
      ->or_like('s3.name', $ds['sender'])
      ->group_end();
    }

    $rs = $this->db->order_by('t.customer_code', 'ASC')->limit($perpage, $offset)->get();

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


}
 ?>
