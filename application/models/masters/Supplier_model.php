<?php
class Supplier_model extends CI_Model
{
	public $tb = 'supplier';

	public function __construct()
	{
		parent::__construct();
	}


	public function get($id)
	{
		$rs = $this->db->where('id', $id)->get($this->tb);
		if($rs->num_rows() === 1)
		{
			return $rs->row();
		}

		return NULL;
	}


	public function add(array $ds = array())
	{
		if(!empty($ds))
		{
			return $this->db->insert($this->tb, $ds);
		}

		return FALSE;
	}


	public function update($id, array $ds = array())
	{
		if(!empty($ds))
		{
			return $this->db->where('id', $id)->update($this->tb, $ds);
		}

		return FALSE;
	}


	public function delete($id)
	{
		return $this->db->where('id', $id)->delete($this->tb);
	}



	public function get_list(array $ds = array(), $perpage = 20, $offset = 0)
	{
		if(isset($ds['code']) && $ds['code'] != '' && !is_null($ds['code']))
		{
			$this->db->like('code', $ds['code']);
		}

		if(isset($ds['name']) && $ds['name'] != '' && !is_null($ds['name']))
		{
			$this->db->like('name', $ds['name']);
		}

		if(isset($ds['phone']) && $ds['phone'] != '' && !is_null($ds['phone']))
		{
			$this->db->like('phone', $ds['phone']);
		}

		if(isset($ds['status']) && $ds['status'] != '' && $ds['status'] !== 'all')
		{
			$this->db->where('status', $ds['status']);
		}

		$this->db->order_by('code', 'ASC')->limit($perpage, $offset);

		$rs = $this->db->get($this->tb);

		if($rs->num_rows() > 0)
		{
			return $rs->result();
		}

		return NULL;
	}



	public function count_rows(array $ds = array())
	{
		if(isset($ds['code']) && $ds['code'] != '' && !is_null($ds['code']))
		{
			$this->db->like('code', $ds['code']);
		}

		if(isset($ds['name']) && $ds['name'] != '' && !is_null($ds['name']))
		{
			$this->db->like('name', $ds['name']);
		}

		if(isset($ds['phone']) && $ds['phone'] != '' && !is_null($ds['phone']))
		{
			$this->db->like('phone', $ds['phone']);
		}

		if(isset($ds['status']) && $ds['status'] != '' && $ds['status'] !== 'all')
		{
			$this->db->where('status', $ds['status']);
		}

		return $this->db->count_all_results($this->tb);
	}



	public function is_exists_code($code)
	{
		$rs = $this->db->where('code', $code)->count_all_results($this->tb);

		if($rs > 0)
		{
			return TRUE;
		}

		return FALSE;
	}




} //--- end class

 ?>
