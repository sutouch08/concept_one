<?php
class Shop_pc_model extends CI_Model
{
  private $tb = "shop_pc";

  public function __construct()
  {
    parent::__construct();
  }

  //--- add new zone (use with sync only)
  public function add(array $ds = array())
  {
    if(!empty($ds))
    {
      if($this->db->insert($this->tb, $ds))
			{
				return $this->db->insert_id();
			}
    }

    return FALSE;
  }

  //--- update zone with sync only
  public function update($id, $ds = array())
  {
    if(!empty($ds))
    {
      return $this->db->where('id', $id)->update($this->tb, $ds);
    }

    return FALSE;
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


	public function get_by_code($code = NULL)
	{
		if(! is_null($code))
		{
      $rs = $this->db->where('code', $code)->get($this->tb);

  		if($rs->num_rows() === 1)
  		{
  			return $rs->row();
  		}
		}

		return NULL;
	}

  //----
  public function delete($id)
  {
    return $this->db->where('id', $id)->delete($this->tb);
  }


	//---- checl transection
	public function has_transection($id)
	{
    $count = $this->db->where('pc_id', $id)->count_all_results('order_pos');

		return $count > 0 ? TRUE : FALSE;
	}


  public function is_exists_code($code, $id = NULL)
  {
    if( ! empty($id))
    {
      $this->db->where('id !=', $id);
    }

    $count = $this->db->where('code', $code)->count_all_results($this->tb);

    return $count > 0 ? TRUE : FALSE;
  }

  public function is_exists_name($name, $id = NULL)
  {
    if( ! empty($id))
    {
      $this->db->where('id !=', $id);
    }

    $count = $this->db->where('name', $name)->count_all_results($this->tb);

    return $count > 0 ? TRUE : FALSE;
  }


  public function count_rows(array $ds = array())
  {
  	if(! empty($ds))
		{
			if( ! empty($ds['code']))
			{
				$this->db->like('code', $ds['code']);
			}

			if(!empty($ds['name']))
			{
				$this->db->like('name', $ds['name']);
			}

			if($ds['status'] !== 'all')
			{
				$this->db->where('status', $ds['status']);
			}

			return $this->db->count_all_results($this->tb);
		}

		return 0;
  }



  public function get_list(array $ds = array(), $perpage = 20, $offset = 0)
  {
		if(! empty($ds))
		{
      if( ! empty($ds['code']))
			{
				$this->db->like('code', $ds['code']);
			}

			if(!empty($ds['name']))
			{
				$this->db->like('name', $ds['name']);
			}

			if($ds['status'] !== 'all')
			{
				$this->db->where('status', $ds['status']);
			}

			$rs = $this->db->limit($perpage, $offset)->get($this->tb);

			if($rs->num_rows() > 0)
			{
				return $rs->result();
			}
		}

		return NULL;

  }

} //--- end class

 ?>
