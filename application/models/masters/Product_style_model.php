<?php
class Product_style_model extends CI_Model
{
  public function __construct()
  {
    parent::__construct();
  }



  public function add(array $ds = array())
  {
    if(!empty($ds))
    {
      if($this->db->insert('product_style', $ds))
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
      return $this->db->update('product_style', $ds);
    }

    return FALSE;
  }


  public function delete($code)
  {
    $rs =  $this->db->where('code', $code)->delete('product_style');
    if($rs)
    {
      return TRUE;
    }

    return $this->db->_error_message();
  }

  public function count_sap_list($date_add, $date_upd)
  {
    $rs = $this->ms
    ->select('U_MODEL')
    ->where('U_MODEL IS NOT NULL', NULL, FALSE)
    ->where('U_MODEL !=', '')
    ->where('U_MODEL !=', '0')
    ->group_start()
    ->where('CreateDate >', $date_add)
    ->or_where('UpdateDate >', $date_upd)
    ->group_end()
    ->group_by('U_MODEL')
    ->get('OITM');

    return $rs->num_rows();
  }


  public function get_sap_list($date_add, $date_upd, $limit, $offset)
  {

    $rs = $this->ms
    ->select('U_MODEL')
    ->where('U_MODEL IS NOT NULL', NULL, FALSE)
    ->where('U_MODEL !=', '')
    ->where('U_MODEL !=', '0')
    ->group_start()
    ->where('CreateDate >=', $date_add)
    ->or_where('UpdateDate >=', $date_upd)
    ->group_end()
    ->group_by('U_MODEL')
    ->limit($limit, $offset)
     ->get('OITM');

    if($rs->num_rows() > 0){
      return $rs->result();
    }

    return FALSE;
  }

  public function get_sap_style($code)
  {
    $rs = $this->ms->distinct()
    ->select('OITM.U_MODEL, OITM.U_GROUP, OITM.U_MAJOR')
    ->select('OITM.U_CATE, OITM.U_SUBTYPE, OITM.U_TYPE')
    ->select('OITM.U_BRAND, OITM.U_YEAR, OITM.InvntItem, OITM.InvntryUom')
    ->select('ITM1.Price AS cost, ITM2.Price AS price')
    ->from('OITM')
    ->join('ITM1 AS ITM1', '(ITM1.ItemCode = OITM.ItemCode AND ITM1.PriceList = 13)','left')
    ->join('ITM1 AS ITM2', '(ITM2.ItemCode = OITM.ItemCode AND ITM2.PriceList = 11)', 'left')
    ->where('OITM.U_MODEL', $code)
    ->where('OITM.U_MODEL !=', '')
    ->where('OITM.U_MODEL !=','0')
    ->limit(1)
    ->get();

    if($rs->num_rows() > 0)
    {
      return $rs->row();
    }

    return FALSE;
  }

  public function count_rows(array $ds = array())
  {
    if(!empty($ds))
    {
      if(!empty($ds['code']))
      {
        $this->db->group_start();
        $this->db->like('code', $ds['code']);
        $this->db->or_like('old_code', $ds['code']);
        $this->db->group_end();
      }

      if(!empty($ds['name']))
      {
        $this->db->like('name', $ds['name']);
      }

      if($ds['group'] != 'all')
      {
        if($ds['group'] == 'NULL')
        {
          $this->db->where('group_code IS NULL', NULL, FALSE);
        }
        else
        {
          $this->db->where('group_code', $ds['group']);
        }
      }

			if($ds['main_group'] != 'all')
			{
        if($ds['main_group'] == 'NULL')
        {
          $this->db->where('main_group_code IS NULL', NULL, FALSE);
        }
        else
        {
          $this->db->where('main_group_code', $ds['main_group']);
        }
			}

      if($ds['sub_group'] != 'all')
      {
        if($ds['sub_group'] == 'NULL')
        {
          $this->db->where('sub_group_code IS NULL', NULL, FALSE);
        }
        else
        {
          $this->db->where('sub_group_code', $ds['sub_group']);
        }
      }

      if($ds['category'] != 'all')
      {
        if($ds['category'] == 'NULL')
        {
          $this->db->where('category_code IS NULL', NULL, FALSE);
        }
        else
        {
          $this->db->where('category_code', $ds['category']);
        }
      }

      if($ds['kind'] != 'all')
      {
        if($ds['kind'] == 'NULL')
        {
          $this->db->where('kind_code IS NULL', NULL, FALSE);
        }
        else
        {
          $this->db->where('kind_code', $ds['kind']);
        }
      }

      if($ds['type'] != 'all')
      {
        if($ds['type'] == 'NULL')
        {
          $this->db->where('type_code IS NULL', NULL, FALSE);
        }
        else
        {
          $this->db->where('type_code', $ds['type']);
        }
      }

      if($ds['brand'] != 'all')
      {
        if($ds['brand'] == 'NULL')
        {
          $this->db->where('brand_code IS NULL', NULL, FALSE);
        }
        else
        {
          $this->db->where('brand_code', $ds['brand']);
        }
      }

      if($ds['year'] != 'all')
      {
        $this->db->where('year', $ds['year']);
      }

      if($ds['sell'] != 'all')
      {
        $this->db->where('can_sell', $ds['sell']);
      }

      if($ds['active'] != 'all')
      {
        $this->db->where('active', $ds['active']);
      }
    }

    return $this->db->count_all_results('product_style');
  }




  public function get($code)
  {
    $rs = $this->db->where('code', $code)->get('product_style');
    if($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return FALSE;
  }

  public function get_by_id($id)
  {
    $rs = $this->db->where('id', $id)->get('product_style');
    if($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return FALSE;
  }


  public function get_with_old_code($code)
  {
    $rs = $this->db->where('code', $code)->or_where('old_code', $code)->get('product_style');
    if($rs->num_rows() == 1)
    {
      return $rs->row();
    }

    if($rs->num_rows() > 1)
    {
      return $rs->result();
    }

    return NULL;
  }



  public function get_name($code)
  {
    if($code === NULL OR $code === '')
    {
      return $code;
    }

    $rs = $this->db->select('name')->where('code', $code)->get('product_style');
    if($rs->num_rows() === 1)
    {
      return $rs->row()->name;
    }

    return NULL;
  }




  public function get_data(array $ds = array(), $perpage = '', $offset = '')
  {
    if(! empty($ds))
    {
      if(! empty($ds['code']))
      {
        $this->db->group_start();
        $this->db->like('code', $ds['code']);
        $this->db->or_like('old_code', $ds['code']);
        $this->db->group_end();
      }

      if(! empty($ds['name']))
      {
        $this->db->like('name', $ds['name']);
      }

      if($ds['group'] != 'all')
      {
        if($ds['group'] == 'NULL')
        {
          $this->db->where('group_code IS NULL', NULL, FALSE);
        }
        else
        {
          $this->db->where('group_code', $ds['group']);
        }
      }

			if($ds['main_group'] != 'all')
			{
        if($ds['main_group'] == 'NULL')
        {
          $this->db->where('main_group_code IS NULL', NULL, FALSE);
        }
        else
        {
          $this->db->where('main_group_code', $ds['main_group']);
        }
			}

      if($ds['sub_group'] != 'all')
      {
        if($ds['sub_group'] == 'NULL')
        {
          $this->db->where('sub_group_code IS NULL', NULL, FALSE);
        }
        else
        {
          $this->db->where('sub_group_code', $ds['sub_group']);
        }
      }

      if($ds['category'] != 'all')
      {
        if($ds['category'] == 'NULL')
        {
          $this->db->where('category_code IS NULL', NULL, FALSE);
        }
        else
        {
          $this->db->where('category_code', $ds['category']);
        }
      }

      if($ds['kind'] != 'all')
      {
        if($ds['kind'] == 'NULL')
        {
          $this->db->where('kind_code IS NULL', NULL, FALSE);
        }
        else
        {
          $this->db->where('kind_code', $ds['kind']);
        }
      }

      if($ds['type'] != 'all')
      {
        if($ds['type'] == 'NULL')
        {
          $this->db->where('type_code IS NULL', NULL, FALSE);
        }
        else
        {
          $this->db->where('type_code', $ds['type']);
        }
      }

      if($ds['brand'] != 'all')
      {
        if($ds['brand'] == 'NULL')
        {
          $this->db->where('brand_code IS NULL', NULL, FALSE);
        }
        else
        {
          $this->db->where('brand_code', $ds['brand']);
        }
      }

      if($ds['year'] != 'all')
      {
        $this->db->where('year', $ds['year']);
      }

      if($ds['sell'] != 'all')
      {
        $this->db->where('can_sell', $ds['sell']);
      }

      if($ds['active'] != 'all')
      {
        $this->db->where('active', $ds['active']);
      }
    }

    $this->db->order_by('date_upd', 'DESC');

    if($perpage != '')
    {
      $offset = $offset === NULL ? 0 : $offset;
      $this->db->limit($perpage, $offset);
    }

    $rs = $this->db->get('product_style');

    return $rs->result();
  }




  public function is_exists($code, $old_code = '')
  {
    if($old_code != '')
    {
      $this->db->where('code !=', $old_code);
    }

    $rs = $this->db->where('code', $code)->get('product_style');

    if($rs->num_rows() > 0)
    {
      return TRUE;
    }

    return FALSE;
  }



  public function is_middle_exists($code)
  {
    $rs = $this->mc->select('Code')->where('Code', $code)->get('MODEL');
    if($rs->num_rows() === 1)
    {
      return TRUE;
    }
    return FALSE;
  }


  public function is_sap_exists($code)
  {
    $rs = $this->ms->select('Code')->where('Code', $code)->get('MODEL');
    if($rs->num_rows() === 1)
    {
      return TRUE;
    }

    return FALSE;
  }


  public function add_sap_model(array $ds = array())
  {
    if(!empty($ds))
    {
      return $this->mc->insert('MODEL', $ds);
    }

    return FALSE;
  }



  public function update_sap_model($code, array $ds = array())
  {
    if(!empty($ds))
    {
      $this->mc->where('Code', $code);
      return $this->mc->update('MODEL', $ds);
    }

    return FALSE;
  }



  public function is_exists_name($name, $old_name = '')
  {
    if($old_name != '')
    {
      $this->db->where('name !=', $old_name);
    }

    $rs = $this->db->where('name', $name)->get('product_style');

    if($rs->num_rows() > 0)
    {
      return TRUE;
    }

    return FALSE;
  }



  public function count_members($code)
  {
    $this->db->select('active')->where('style_code', $code);
    $rs = $this->db->get('products');
    return $rs->num_rows();
  }


  public function get_style_last_sync()
  {
    $rs = $this->db->select_max('last_sync')->get('product_style');
    return $rs->row()->last_sync;
  }

}
?>
