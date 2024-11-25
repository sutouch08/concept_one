<?php
class Pack_model extends CI_Model
{
  public function __construct()
  {
    parent::__construct();
  }

  public function get_data(array $ds = array(), $perpage = NULL, $offset = NULL)
  {
		$this->db
		->select('qc.*')
		->select('qc_box.box_no, order_state.name AS state_name')
    ->select('orders.state')
    ->select('order_details.product_name')
		->from('qc')
		->join('qc_box', 'qc.box_id = qc_box.id', 'left')
		->join('orders', 'qc.order_code = orders.code', 'left')
    ->join('order_state', 'orders.state = order_state.state')
    ->join('order_details', 'qc.order_detail_id = order_details.id', 'left');

    if(!empty($ds['order_code']))
    {
      $this->db->like('qc.order_code',$ds['order_code']);
    }

    if(!empty($ds['pd_code']))
    {
      $this->db->like('qc.product_code', $ds['pd_code']);
    }

    if(!empty($ds['from_date']) && !empty($ds['to_date']))
    {
      $this->db->where('qc.date_upd >=', from_date($ds['from_date']));
      $this->db->where('qc.date_upd <=', to_date($ds['to_date']));
    }

    if($perpage > 0)
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


  public function count_rows(array $ds = array(), $perpage = NULL, $offset = NULL)
  {
		$this->db
		->from('qc')
		->join('qc_box', 'qc.box_id = qc_box.id', 'left');

    if(!empty($ds['order_code']))
    {
      $this->db->like('qc.order_code',$ds['order_code']);
    }

    if(!empty($ds['pd_code']))
    {
      $this->db->like('qc.product_code', $ds['pd_code']);
    }

    if(!empty($ds['from_date']) && !empty($ds['to_date']))
    {
      $this->db->where('qc.date_upd >=', from_date($ds['from_date']));
      $this->db->where('qc.date_upd <=', to_date($ds['to_date']));
    }

    return $this->db->count_all_results();
  }



  public function delete($id)
  {
    return $this->db->where('id', $id)->delete('qc');
  }


  public function get($id)
  {
    $rs = $this->db->where('id', $id)->get('qc');

    if($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return NULL;
  }
}
 ?>
