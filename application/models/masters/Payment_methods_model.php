<?php
class Payment_methods_model extends CI_Model
{
  private $tb = "payment_method";

  public function __construct()
  {
    parent::__construct();
  }


  public function add(array $ds = array())
  {
    if(!empty($ds))
    {
      return  $this->db->insert($this->tb, $ds);
    }

    return FALSE;
  }



  public function update($code, array $ds = array())
  {
    if(!empty($ds))
    {
      $this->db->where('code', $code);
      return $this->db->update($this->tb, $ds);
    }

    return FALSE;
  }


  public function update_by_id($id, array $ds = array())
  {
    if( ! empty($ds))
    {
      return $this->db->where('id', $id)->update($this->tb, $ds);
    }

    return FALSE;
  }


  public function delete($code)
  {
    return $this->db->where('code', $code)->delete($this->tb);
  }


  public function get_payment_methods($code)
  {
    $rs = $this->db
    ->select('pm.*, pr.name AS payment_role')
    ->from('payment_method AS pm')
    ->join('payment_role AS pr', 'pm.role = pr.id', 'left')
    ->where('pm.code', $code)
    ->get();

    if($rs->num_rows() == 1)
    {
      return $rs->row();
    }

    return NULL;
  }


  public function get($code)
  {
    $rs = $this->db
    ->select('pm.*, pr.name AS payment_role')
    ->from('payment_method AS pm')
    ->join('payment_role AS pr', 'pm.role = pr.id', 'left')
    ->where('pm.code', $code)
    ->get();

    if($rs->num_rows() == 1)
    {
      return $rs->row();
    }

    return NULL;
  }


  public function get_by_id($id)
  {
    $rs = $this->db
    ->select('pm.*, pr.name AS payment_role')
    ->from('payment_method AS pm')
    ->join('payment_role AS pr', 'pm.role = pr.id', 'left')
    ->where('pm.id', $id)
    ->get();

    if($rs->num_rows() == 1)
    {
      return $rs->row();
    }

    return NULL;
  }


  public function get_default()
  {
    $rs = $this->db->where('is_default', 1)->get($this->tb);

    if($rs->num_rows() == 1)
    {
      return $rs->result();
    }

    return FALSE;
  }


  public function get_roles()
  {
    $rs = $this->db->get('payment_role');

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function get_data()
  {
    $rs = $this->db->get($this->tb);

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }

  public function get_list(array $ds = array(), $perpage = 20, $offset = 0)
  {
    $this->db
    ->select('pm.*, pr.name AS role_name')
    ->from('payment_method AS pm')
    ->join('payment_role AS pr', 'pm.role = pr.id', 'left');

    if(isset($ds['code']) && $ds['code'] != "" && $ds['code'] != NULL)
    {
      $this->db->like('pm.code', $ds['code']);
    }

    if(isset($ds['name']) && $ds['name'] != "" && $ds['name'] != NULL)
    {
      $this->db->like('pm.name', $ds['name']);
    }

    if(isset($ds['role']) && $ds['role'] != "all")
    {
      $this->db->where('pm.role', $ds['role']);
    }

    if(isset($ds['has_term']) && $ds['has_term'] != "all")
    {
      $this->db->where('pm.has_term', $ds['has_term']);
    }

    $rs = $this->db->limit($perpage, $offset)->get();

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function count_rows(array $ds = array())
  {

    if(isset($ds['code']) && $ds['code'] != "" && $ds['code'] != NULL)
    {
      $this->db->like('code', $ds['code']);
    }

    if(isset($ds['name']) && $ds['name'] != "" && $ds['name'] != NULL)
    {
      $this->db->like('name', $ds['name']);
    }

    if(isset($ds['role']) && $ds['role'] != "all")
    {
      $this->db->where('role', $ds['role']);
    }

    if(isset($ds['has_term']) && $ds['has_term'] != "all")
    {
      $this->db->where('has_term', $ds['has_term']);
    }

    return $this->db->count_all_results($this->tb);
  }


  public function get_all()
  {
    $rs = $this->db->get($this->tb);

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function is_exists($code, $id = NULL)
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


  public function get_name($code)
  {
    $rs = $this->db->select('name')->where('code', $code)->get($this->tb);
    if($rs->num_rows() == 1)
    {
      return $rs->row()->name;
    }

    return NULL;
  }


  public function has_term($code)
  {
    $count = $this->db->where('code', $code)->where('has_term', 1)->count_all_results($this->tb);

    return $count > 0 ? TRUE : FALSE;
  }


  public function get_pos_payment_list()
	{
    $rs = $this->db
    ->where('has_term', 0)
    ->order_by('position', 'ASC')
    ->get($this->tb);

		if($rs->num_rows() > 0)
		{
			return $rs->result();
		}

		return NULL;
	}

  public function get_list_by_role($role = 2)
  {
    $rs = $this->db->where('role', $role)->order_by('position', 'ASC')->get($this->tb);

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }

}
?>
