<?php
class Sender_model extends CI_Model
{
  public function __construct()
  {
    parent::__construct();
  }

  public function add(array $ds = array())
  {
    if(!empty($ds))
    {
      return $this->db->insert('address_sender', $ds);
    }

    return FALSE;
  }


  public function update($id, array $ds = array())
  {
    if(!empty($ds))
    {
      return $this->db->where('id', $id)->update('address_sender', $ds);
    }

    return FALSE;
  }



  public function delete($id)
  {
    return $this->db->where('id', $id)->delete('address_sender');
  }


  public function get($id)
  {
    $rs = $this->db->where('id', $id)->get('address_sender');
    if($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return FALSE;
  }


	public function get_id($code)
	{
		$rs = $this->db->select('id')->where('code', $code)->get('address_sender');

		if($rs->num_rows() === 1)
		{
			return $rs->row()->id;
		}

		return NULL;
	}



	public function get_common_list($list =  array())
	{

		$this->db->where('show_in_list', 1);

		if(!empty($list))
		{
			$this->db->where_not_in('id', $list);
		}

		$rs = $this->db->get('address_sender');

		if($rs->num_rows() > 0)
		{
			return $rs->result();
		}

		return NULL;
	}



	public function get_customer_sender_list($customer_code)
	{
		$rs = $this->db->where('customer_code', $customer_code)->get('address_transport');

		if($rs->num_rows() == 1)
		{
			return $rs->row();
		}

		return NULL;
	}



	public function get_sender_in($arr)
	{
		$rs = $this->db->where_in('id', $arr)->get('address_sender');

		if($rs->num_rows() > 0)
		{
			return $rs->result();
		}

		return NULL;
	}

  public function get_sender($id)
  {
    $rs = $this->db->where('id', $id)->get('address_sender');
    if($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return FALSE;
  }


  public function get_name($id)
  {
    $rs = $this->db->where('id', $id)->get('address_sender');
    if($rs->num_rows() === 1)
    {
      return $rs->row()->name;
    }

    return NULL;
  }




	public function is_exists_code($code, $id = NULL)
	{
		$this->db->where('code', $code);
		if(!empty($id))
		{
			$this->db->where('id !=', $id);
		}

		$rs = $this->db->count_all_results('address_sender');

		if($rs > 0)
		{
			return TRUE;
		}

		return FALSE;
	}


	public function is_exists_name($name, $id = NULL)
	{
		$this->db->where('name', $name);
		if(!empty($id))
		{
			$this->db->where('id !=', $id);
		}

		$rs = $this->db->count_all_results('address_sender');

		if($rs > 0)
		{
			return TRUE;
		}

		return FALSE;
	}



  public function is_exists($name, $id = NULL)
  {
    if(! empty($id))
    {
      $rs = $this->db->where('name', $name)->where('id !=',$id)->get('address_sender');
    }
    else
    {
      $rs = $this->db->where('name', $name)->get('address_sender');
    }

    if($rs->num_rows() > 0)
    {
      return TRUE;
    }

    return FALSE;
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

		if(!empty($addr))
		{
			$this->db->group_start();
			$this->db->like('address1', $ds['addr'])->or_like('address2', $ds['addr']);
			$this->db->group_end();
		}

		if(isset($ds['phone']) && $ds['phone'] != '')
		{
			$this->db->like('phone', $ds['phone']);
		}

		if(!empty($ds['type']) && $ds['type'] !== 'all')
		{
			$this->db->where('type', $ds['type']);
		}

		return $this->db->count_all_results('address_sender');

  }


  public function get_list(array $ds = array(), $perpage = 20, $offset = 0)
  {
    if(!empty($ds))
    {

			if(!empty($ds['code']))
			{
				$this->db->like('code', $ds['code']);
			}

			if(!empty($ds['name']))
			{
				$this->db->like('name', $ds['name']);
			}

			if(!empty($addr))
			{
				$this->db->group_start();
				$this->db->like('address1', $ds['addr'])->or_like('address2', $ds['addr']);
				$this->db->group_end();
			}

			if(isset($ds['phone']) && $ds['phone'] != '')
			{
				$this->db->like('phone', $ds['phone']);
			}

			if(!empty($ds['type']) && $ds['type'] !== 'all')
			{
				$this->db->where('type', $ds['type']);
			}

			$this->db->order_by('code', 'DESC')->limit($perpage, $offset);

			$rs = $this->db->get('address_sender');

      if($rs->num_rows() > 0)
      {
        return $rs->result();
      }

    }

    return FALSE;
  }



  public function search($txt)
  {
    $qr = "SELECT id FROM address_sender WHERE name LIKE '%".$txt."%'";
    $rs = $this->db->query($qr);
    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }
    else
    {
      return array();
    }

  }


	public function get_main_sender($customer_code)
	{
		$rs = $this->db->select('main_sender')->where('customer_code', $customer_code)->get('address_transport');
		if($rs->num_rows() === 1)
		{
			return $rs->row()->main_sender;
		}

		return NULL;
	}

}
 ?>
