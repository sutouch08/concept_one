<?php
class Slp_model extends CI_Model
{
  public function __construct()
  {
    parent::__construct();
  }


  public function get($id)
  {
    $rs = $this->db->where('id', $id)->get('saleman');

    if($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return NULL;
  }

  public function get_all_slp()
  {
    $rs = $this->ms->select('SlpCode AS id, SlpName AS name, Active AS active')->get('OSLP');
    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return FALSE;
  }



  public function is_exists($id)
  {
    $rs = $this->db->where('id', $id)->get('saleman');
    if($rs->num_rows() === 1)
    {
      return TRUE;
    }

    return FALSE;
  }


  public function add($ds = array())
  {
    if(!empty($ds))
    {
      return $this->db->insert('saleman', $ds);
    }

    return FALSE;
  }


  public function update($id, $ds = array())
  {
    if(!empty($ds))
    {
      return $this->db->where('id', $id)->update('saleman', $ds);
    }

    return FALSE;
  }



  public function get_name($code)
  {
    $rs = $this->ms->select('SlpName')->where('SlpCode', $code)->get('OSLP');
    if($rs->num_rows() === 1)
    {
      return $rs->row()->SlpName;
    }

    return NULL;
  }



  public function count_rows($ds = array())
  {
    $this->db->where('id IS NOT NULL', NULL, FALSE);

    if($ds['active'] != 'all')
    {
      $this->db->where('active', $ds['active']);
    }


    if(!empty($ds['name']))
    {
      $this->db->like('name', $ds['name']);
    }

    return $this->db->count_all_results('saleman');
  }


  public function get_list($ds = array(), $limit = NULL, $offset = 0)
  {
    if($ds['active'] != 'all')
    {
      $this->db->where('active', $ds['active']);
    }

    if(!empty($ds['name']))
    {
      $this->db->like('name', $ds['name']);
    }


    if(!empty($limit))
    {
      $this->db->limit($limit, $offset);
    }

    $rs = $this->db->get('saleman');
    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return FALSE;
  }


  public function get_data($active = NULL)
  {
    if( ! empty($active))
    {
      $this->db->where('active', 1);
    }

    $rs = $this->db->where('id > ', 0)->get('saleman');

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function get_in(array $ds = array())
  {
    if( ! empty($ds))
    {
      $rs = $this->db->where_in('id', $ds)->get('saleman');

      if($rs->num_rows() > 0)
      {
        return $rs->result();
      }
    }


    return NULL;
  }

} //--- End class

 ?>
