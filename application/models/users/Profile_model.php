<?php
class Profile_model extends CI_Model
{
  public function __construct()
  {
    parent::__construct();
  }


  public function get_name($id)
  {
    $rs = $this->db->where('id', $id)->get('profile');

    if($rs->num_rows() === 1)
    {
      return $rs->row()->name;
    }

    return NULL;
  }


  public function add($name)
  {
    return $this->db->insert('profile', array('name' => $name));
  }




  public function update($id, $name)
  {
    return $this->db->where('id', $id)->update('profile', array('name' => $name));
  }



  public function delete($id)
  {
    return $this->db->where('id', $id)->delete('profile');
  }




  public function is_extsts($name, $id = '')
  {
    if($id !== '')
    {
      $this->db->where('id !=', $id);
    }

    $rs = $this->db->where('name', $name)->get('profile');

    if($rs->num_rows() > 0)
    {
      return TRUE;
    }

    return FALSE;
  }





  public function count_members($id)
  {
    $this->db->select('id');
    $this->db->where('id_profile', $id);
    $rs = $this->db->get('user');
    return $rs->num_rows();
  }





  public function get_profile($id)
  {
    $rs = $this->db->where('id', $id)->get('profile');
    return $rs->row();
  }




  public function get_list(array $ds = array(), $perpage = 20, $offset = 0)
  {
    $this->db
    ->distinct()
    ->select('p.*')
    ->from('profile AS p')
    ->join('permission AS pm', 'pm.id_profile = p.id', 'left')
    ->join('menu AS m', 'pm.menu = m.code', 'left')
    ->where('p.id >', 0)
    ->where('p.name IS NOT NULL', NULL, FALSE);


    if(!empty($ds['name']))
    {
      $this->db->like('p.name', $ds['name']);
    }

    if(!empty($ds['menu']) && $ds['menu'] != 'all')
    {
      $this->db->where_in('pm.menu', $ds['menu']);
    }

    if(!empty($ds['permission']) && $ds['permission'] != 'all')
    {
      if($ds['permission'] == 'view')
      {
        $this->db->where('pm.can_view', 1);
      }

      if($ds['permission'] == 'add')
      {
        $this->db->where('pm.can_add', 1);
      }

      if($ds['permission'] == 'edit')
      {
        $this->db->where('pm.can_edit', 1);
      }

      if($ds['permission'] == 'delete')
      {
        $this->db->where('pm.can_delete', 1);
      }

      if($ds['permission'] == 'approve')
      {
        $this->db->where('pm.can_approve', 1);
      }
    }

    $rs = $this->db->limit($perpage, $offset)->get();

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }



  public function get_profiles($name = '', $perpage = 0, $offset = 0)
  {
    if($name != '')
    {
      $this->db->like('name', $name);
    }

    if($perpage > 0)
    {
      $offset = $offset === NULL ? 0 : $offset;
      $this->db->limit($perpage, $offset);
    }

    $rs = $this->db->get('profile');
    return $rs->result();
  }


  public function count_rows($ds = array())
  {
    $this->db
    ->distinct()
    ->select('p.*')
    ->from('profile AS p')
    ->join('permission AS pm', 'pm.id_profile = p.id', 'left')
    ->join('menu AS m', 'pm.menu = m.code', 'left')
    ->where('p.id >', 0)
    ->where('p.name IS NOT NULL', NULL, FALSE);

    if(!empty($ds['name']))
    {
      $this->db->like('p.name', $ds['name']);
    }

    if(!empty($ds['menu']) && $ds['menu'] != 'all')
    {
      $this->db->where_in('pm.menu', $ds['menu']);
    }

    if(!empty($ds['permission']) && $ds['permission'] != 'all')
    {
      if($ds['permission'] == 'view')
      {
        $this->db->where('pm.can_view', 1);
      }

      if($ds['permission'] == 'add')
      {
        $this->db->where('pm.can_add', 1);
      }

      if($ds['permission'] == 'edit')
      {
        $this->db->where('pm.can_edit', 1);
      }

      if($ds['permission'] == 'delete')
      {
        $this->db->where('pm.can_delete', 1);
      }

      if($ds['permission'] == 'approve')
      {
        $this->db->where('pm.can_approve', 1);
      }
    }

    return $this->db->count_all_results();
  }


} //--- End class


 ?>
