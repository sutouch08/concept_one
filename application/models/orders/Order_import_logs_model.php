<?php
class Order_import_logs_model extends CI_Model
{
  private $tb = "order_import_logs";

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


  public function get_list(array $ds = array(), $perpage = 20, $offset = 0)
	{
    if( ! empty($ds['reference']))
    {
      $this->db->like('reference', $ds['reference']);
    }

		if( ! empty($ds['order_code']))
		{
			$this->db->like('order_code', $ds['order_code']);
		}

		if( ! empty($ds['status']) && $ds['status'] !== 'all')
		{
			$this->db->where('status', $ds['status']);
		}

		if( ! empty($ds['action']) && $ds['action'] !== 'all')
    {
      $this->db->where('action', $ds['action']);
    }

    if( ! empty($ds['from_date']))
    {
      $this->db->where('date_upd >=', from_date($ds['from_date']));
    }

    if( ! empty($ds['to_date']))
    {
      $this->db->where('date_upd <=', to_date($ds['to_date']));
    }

		$this->db->order_by('id', 'DESC');
		$this->db->limit($perpage, $offset);
		$rs = $this->db->get($this->tb);

		if($rs->num_rows() > 0)
		{
			return $rs->result();
		}

		return NULL;
	}



	public function count_rows(array $ds = array())
	{
    if( ! empty($ds['reference']))
    {
      $this->db->like('reference', $ds['reference']);
    }

		if( ! empty($ds['order_code']))
		{
			$this->db->like('order_code', $ds['order_code']);
		}

		if( ! empty($ds['status']) && $ds['status'] !== 'all')
		{
			$this->db->where('status', $ds['status']);
		}

		if( ! empty($ds['action']) && $ds['action'] !== 'all')
    {
      $this->db->where('action', $ds['action']);
    }

    if( ! empty($ds['from_date']))
    {
      $this->db->where('date_upd >=', from_date($ds['from_date']));
    }

    if( ! empty($ds['to_date']))
    {
      $this->db->where('date_upd <=', to_date($ds['to_date']));
    }

		return $this->db->count_all_results($this->tb);
	}
}

 ?>
