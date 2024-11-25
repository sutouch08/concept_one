<?php
class Product_color_model extends CI_Model
{
  private $tb = "product_color";

  public function __construct()
  {
    parent::__construct();
  }


  public function add(array $ds = array())
  {
    if(! empty($ds))
    {
      if($this->db->insert($this->tb, $ds))
      {
        return $this->db->insert_id();
      }
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


  public function delete_by_id($id)
  {
    return $this->db->where('id', $id)->delete($this->tb);
  }


  public function count_rows(array $ds = array())
  {
    if(isset($ds['status']) && $ds['status'] != 'all')
    {
      $this->db->where('active', $ds['status']);
    }

    if(!empty($ds['code']))
    {
      $this->db->like('code', $ds['code']);
    }

    if(!empty($ds['name']))
    {
      $this->db->like('name', $ds['name']);
    }

    if(! empty($ds['color_group']) && $ds['color_group'] != 'all')
    {
      if($ds['color_group'] === 'NULL')
      {
        $this->db->where('id_group IS NULL', NULL, FALSE);
      }
      else
      {
        $this->db->where('id_group', $ds['color_group']);
      }
    }

    return $this->db->count_all_results($this->tb);
  }




  public function get($code)
  {
    $rs = $this->db->where('code', $code)->get($this->tb);
    if($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return FALSE;
  }


  public function get_by_id($id)
  {
    $rs = $this->db->where('id', $id)->get($this->tb);

    if($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return FALSE;
  }



  public function get_name($code)
  {
    if($code === NULL OR $code === '')
    {
      return $code;
    }

    $rs = $this->db->select('name')->where('code', $code)->get($this->tb);
    if($rs->num_rows() === 1)
    {
      return $rs->row()->name;
    }

    return NULL;
  }




  public function get_data(array $ds = array(), $perpage = '', $offset = '')
  {
    $this->db
    ->select('co.*')
    ->select('cg.code AS group_code, cg.name AS group_name')
    ->from('product_color AS co')
    ->join('product_color_group AS cg', 'co.id_group = cg.id', 'left');

    if(isset($ds['status']) && $ds['status'] != 'all')
    {
      $this->db->where('co.active', $ds['status']);
    }

    if(!empty($ds['code']))
    {
      $this->db->like('co.code', $ds['code']);
    }

    if(!empty($ds['name']))
    {
      $this->db->like('co.name', $ds['name']);
    }

    if( ! empty($ds['color_group']) && $ds['color_group'] != 'all')
    {
      if($ds['color_group'] === 'NULL')
      {
        $this->db->where('co.id_group IS NULL', NULL, FALSE);
      }
      else
      {
        $this->db->where('co.id_group', $ds['color_group']);
      }
    }

    if($perpage != '')
    {
      $offset = $offset === NULL ? 0 : $offset;
      $this->db->limit($perpage, $offset);
    }

    $rs = $this->db->get();

    return $rs->result();
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



  public function set_active($id, $active)
  {
    return $this->db->set('active', $active)->where('id', $id)->update($this->tb);
  }

  public function count_members($code)
  {
    $this->db->select('active')->where('color_code', $code);
    $rs = $this->db->get('products');
    return $rs->num_rows();
  }



  public function get_color_group()
  {
    $rs = $this->db->order_by('name', 'ASC')->get('product_color_group');
    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return FALSE;
  }


  public function is_sap_exists($code)
  {
    $rs = $this->mc->select('Code')->where('Code', $code)->get('COLOR');
    if($rs->num_rows() > 0)
    {
      return TRUE;
    }

    return FALSE;
  }


  public function add_sap_color(array $ds = array())
  {
    if(!empty($ds))
    {
      return $this->mc->insert('COLOR', $ds);
    }

    return FALSE;
  }


  public function update_sap_color($code, array $ds = array())
  {
    if(!empty($ds))
    {
      $this->mc->where('Code', $code);
      return $this->mc->update('COLOR', $ds);
    }

    return FALSE;
  }


}
?>
