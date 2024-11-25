<?php
class Vat_model extends CI_Model
{
  public function __construct()
  {
    parent::__construct();
  }

	public function get_rate($code)
	{
		$rs = $this->db->select('rate')->where('code', $code)->get('vat');
		if($rs->num_rows() === 1)
		{
			return $rs->row()->rate;
		}

		return 0.00;
	}


	public function get($code)
  {
    $rs = $this->db->where('code', $code)->get('vat');
    if($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return FALSE;
  }


	public function add(array $ds = array())
	{
		return $this->db->insert('vat', $ds);
	}



	public function update($code, $ds = array())
	{
		return $this->db->where('code', $code)->update('vat', $ds);
	}



	public function delete($code)
	{
		return $this->db->where('code', $code)->delete('vat');
	}



	public function has_transection($code)
	{
		$pd = $this->has_product($code);
		$st = $this->has_style($code);
		$order = $this->has_order($code);
		$sold = $this->has_order_sold($code);

		$rs = $pd + $st + $order + $sold;

		return $rs > 0 ? TRUE : FALSE;
	}



	private function has_product($code)
	{
		return $this->db->where('vat_code', $code)->limit(1)->count_all_results('products');
	}

	private function has_style($code)
	{
		return $this->db->where('vat_code', $code)->limit(1)->count_all_results('product_style');
	}

	private function has_order($code)
	{
		return $this->db->where('vat_code', $code)->limit(1)->count_all_results('order_details');
	}

	private function has_order_sold($code)
	{
		return $this->db->where('vat_code', $code)->limit(1)->count_all_results('order_sold');
	}


	public function count_rows(array $ds = array())
	{
		if(!empty($ds['code']))
		{
			$this->db->like('code', $ds['code']);
		}

		if(!empty($ds['name']))
		{
			$this->db->like('name', $ds['name']);
		}

		if($ds['active'] !== 'all')
		{
			$this->db->where('active', $ds['active']);
		}

		return $this->db->count_all_results('vat');
	}


	public function get_list(array $ds = array(), $perpage = 20, $offset = 0)
	{
		if(!empty($ds['code']))
		{
			$this->db->like('code', $ds['code']);
		}

		if(!empty($ds['name']))
		{
			$this->db->like('name', $ds['name']);
		}

		if($ds['active'] !== 'all')
		{
			$this->db->where('active', $ds['active']);
		}

		$this->db->order_by('code', 'ASC')->limit($perpage, $offset);

		$rs = $this->db->get('vat');

		if($rs->num_rows() > 0)
		{
			return $rs->result();
		}

		return NULL;
	}


  public function get_active_data($type = NULL)
  {
    if($type != NULL)
    {
      $this->db->where('type', $type); //--- S or P
    }

    $rs = $this->db
		->where('active', 1)
		->order_by('is_default', 'DESC')
		->order_by('code', 'ASC')
		->get('vat');

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return FALSE;
  }


	public function is_exists_code($code, $old_code = NULL)
	{
		$this->db->where('code', $code);

		if(!empty($old_code))
		{
			$this->db->where('code !=', $old_code);
		}

		return $this->db->count_all_results('vat');
	}


	public function is_exists_name($name, $old_name = NULL)
	{
		$this->db->where('name', $name);
		if(!empty($old_name))
		{
			$this->db->where('name !=', $old_name);
		}

		return $this->db->count_all_results('vat');
	}


	public function clear_default_state()
	{
		return $this->db->set('is_default', 0)->update('vat');
	}


	public function set_default_state($code)
	{
		return $this->db->set('is_default', 1)->where('code', $code)->update('vat');
	}

}
?>
