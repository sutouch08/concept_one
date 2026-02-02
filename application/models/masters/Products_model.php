<?php
class Products_model extends CI_Model
{
  public function __construct()
  {
    parent::__construct();
  }

	public function get_sap_item_avg_cost($code)
	{
		$rs = $this->ms->select('LstEvlPric AS cost')->where('ItemCode', $code)->get('OITM');
		if($rs->num_rows() === 1)
		{
			return $rs->row()->cost;
		}

		return NULL;
	}


	public function get_product_cost($code)
	{
		$rs = $this->db->select('cost')->where('code', $code)->get('products');

		if($rs->num_rows() === 1)
		{
			return $rs->row()->cost;
		}

		return 0;
	}


  public function count_sap_update_list($date_add, $date_upd)
  {
    $rs = $this->ms->select('ItemCode')
    ->group_start()
    ->where('CreateDate >', $date_add)
    ->or_where('UpdateDate >', $date_upd)
    ->group_end()
    ->get('OITM');

    return $rs->num_rows();
  }



  public function get_sap_list($date_add, $date_upd, $limit, $offset)
  {
    $costList = getConfig("SAP_COST_LIST");
    $priceList = getConfig("SAP_PRICE_LIST");

    $rs = $this->ms
    ->select("OITM.ItemCode, OITM.ItemName, OITM.CodeBars, OITM.U_MODEL, OITM.U_COLOR, OITM.U_SIZE, OITM.U_GROUP, OITM.U_MAJOR")
    ->select("OITM.U_CATE, OITM.U_SUBTYPE, OITM.U_TYPE, OITM.U_BRAND, OITM.U_YEAR")
    ->select("OITM.InvntItem, OITM.InvntryUom, OITM.U_OLDCODE, ITM1.Price AS cost, ITM2.Price AS price")
    ->from("OITM")
    ->join("ITM1 AS ITM1", "ITM1.ItemCode = OITM.ItemCode AND ITM1.PriceList = {$costList}")
    ->join("ITM1 AS ITM2", "ITM2.ItemCode = OITM.ItemCode AND ITM2.PriceList = {$priceList}")
    ->group_start()
    ->where("OITM.CreateDate >", $date_add)
    ->or_where("OITM.UpdateDate >", $date_upd)
    ->group_end()
    ->limit($limit, $offset)
    ->get();

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return FALSE;
  }



  public function is_middle_exists($code)
  {
    $rs = $this->mc->select('ItemCode')->where('ItemCode', $code)->get('OITM');
    if($rs->num_rows() === 1)
    {
      return TRUE;
    }

    return FALSE;
  }


  public function get_un_import_middle($code)
  {
    $rs = $this->mc
    ->select('DocEntry, ItemCode')
    ->where('ItemCode', $code)
    ->group_start()
    ->where('F_Sap', 'N')
    ->or_where('F_Sap IS NULL', NULL, FALSE)
    ->group_end()
    ->get('OITM');

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }



  public function drop_middle_item($docEntry)
  {
    return $this->mc->where('DocEntry', $docEntry)->delete('OITM');
  }


  public function is_sap_exists($code)
  {
    $rs = $this->ms->select('ItemCode')->where('ItemCode', $code)->get('OITM');
    if($rs->num_rows() === 1)
    {
      return TRUE;
    }

    return FALSE;
  }


  public function get_sap_price($code)
  {
    $costList = getConfig("SAP_COST_LIST");
    $priceList = getConfig("SAP_PRICE_LIST");

    $rs = $this->ms
    ->select("OITM.ItemCode, ITM1.Price AS cost, ITM2.Price AS price")
    ->from("OITM")
    ->join("ITM1 AS ITM1", "ITM1.ItemCode = OITM.ItemCode AND ITM1.PriceList = {$costList}")
    ->join("ITM1 AS ITM2", "ITM2.ItemCode = OITM.ItemCode AND ITM2.PriceList = {$priceList}")
    ->where("OITM.ItemCode", $code)
    ->get();

    if($rs->num_rows() == 1)
    {
      return $rs->row();
    }

    return FALSE;
  }


  public function count_rows(array $ds = array())
  {

    $this->db
    ->from('products')
    ->join('product_color', 'products.color_code = product_color.code', 'left')
    ->join('product_size', 'products.size_code = product_size.code', 'left');

    if(!empty($ds))
    {
      if(!empty($ds['code']))
      {
        $this->db->group_start();
        $this->db->like('products.code', $ds['code']);
        $this->db->or_like('products.old_code', $ds['code']);
        $this->db->group_end();
      }

      if(!empty($ds['name']))
      {
        $this->db->like('products.name', $ds['name']);
      }

      if(!empty($ds['barcode']))
      {
        $this->db->like('products.barcode', $ds['barcode']);
      }

      if( isset($ds['price']) && isset($ds['operater']) && $ds['price'] != '')
      {
        if($ds['operater'] == 'equal')
        {
          $this->db->where('products.price', $ds['price']);
        }
        else if($ds['operater'] == 'less_than')
        {
          $this->db->where('products.price <', $ds['price']);
        }
        else if($ds['operater'] == 'more_than')
        {
          $this->db->where('products.price >', $ds['price']);
        }
      }

      if(!empty($ds['color']))
      {
        if($ds['color'] === 'NULL')
        {
          $this->db->where('products.color_code IS NULL', NULL, FALSE);
        }
        else
        {
          $this->db->group_start();
          $this->db->like('product_color.code', $ds['color'])->or_like('product_color.name', $ds['color']);
          $this->db->group_end();
        }
      }


      if(!empty($ds['color_group']))
      {
        $this->db->where('product_color.id_group', $ds['color_group']);
      }

      if(!empty($ds['size']))
      {
        if($ds['size'] === 'NULL')
        {
          $this->db->where('products.size_code IS NULL', NULL, FALSE);
        }
        else
        {
          $this->db->where('product_size.code', $ds['size']);
        }
      }

      if(!empty($ds['group']))
      {
        $this->db->where('group_code', $ds['group']);
      }

      if(!empty($ds['sub_group']))
      {
        $this->db->where('sub_group_code', $ds['sub_group']);
      }

      if(!empty($ds['category']))
      {
        $this->db->where('category_code', $ds['category']);
      }

      if(!empty($ds['kind']))
      {
        $this->db->where('kind_code', $ds['kind']);
      }

      if(!empty($ds['type']))
      {
        $this->db->where('type_code', $ds['type']);
      }

      if(!empty($ds['brand']))
      {
        $this->db->where('brand_code', $ds['brand']);
      }

      if(!empty($ds['year']))
      {
        $this->db->where('year', $ds['year']);
      }

      if(isset($ds['active']) && $ds['active'] != 'all')
      {
        $this->db->where('products.active', $ds['active']);
      }
    }

    return $this->db->count_all_results();

  }



  public function get_list(array $ds = array(), $perpage = '', $offset = '')
  {
    $this->db
    ->select('products.*')
    ->from('products')
    ->join('product_color', 'products.color_code = product_color.code', 'left')
    ->join('product_size', 'products.size_code = product_size.code', 'left');

    if( ! empty($ds))
    {
      if( ! empty($ds['code']))
      {
        $this->db->group_start();
        $this->db->like('products.code', $ds['code']);
        $this->db->or_like('products.old_code', $ds['code']);
        $this->db->group_end();
      }

      if( ! empty($ds['name']))
      {
        $this->db->like('products.name', $ds['name']);
      }


      if( ! empty($ds['barcode']))
      {
        $this->db->like('products.barcode', $ds['barcode']);
      }

      if( isset($ds['price']) && isset($ds['operater']) && $ds['price'] != '')
      {
        if($ds['operater'] == 'equal')
        {
          $this->db->where('products.price', $ds['price']);
        }

        if($ds['operater'] == 'less_than')
        {
          $this->db->where('products.price <', $ds['price']);
        }

        if($ds['operater'] == 'more_than')
        {
          $this->db->where('products.price >', $ds['price']);
        }
      }

      if( ! empty($ds['color']))
      {
        if($ds['color'] === 'NULL')
        {
          $this->db->where('products.color_code IS NULL', NULL, FALSE);
        }
        else
        {
          $this->db->group_start();
          $this->db->like('product_color.code', $ds['color'])->or_like('product_color.name', $ds['color']);
          $this->db->group_end();
        }
      }

      if( ! empty($ds['color_group']))
      {
        $this->db->where('product_color.id_group', $ds['color_group']);
      }

      if( ! empty($ds['size']))
      {
        if($ds['size'] === 'NULL')
        {
          $this->db->where('products.size_code IS NULL', NULL, FALSE);
        }
        else
        {
          $this->db->where('product_size.code', $ds['size']);
        }
      }

      if( ! empty($ds['price']))
      {
        $operater = ! empty($ds['operater']) ? $ds['operater'] : 'less_than';

        if($operater === 'more_than')
        {
          $this->db->where('products.price >=', $ds['price'], FALSE);
        }
        else
        {
          $this->db->where('products.price <=', $ds['price'], FALSE);
        }
      }

      if( ! empty($ds['group']))
      {
        $this->db->where('group_code', $ds['group']);
      }

      if( ! empty($ds['sub_group']))
      {
        $this->db->where('sub_group_code', $ds['sub_group']);
      }

      if( ! empty($ds['category']))
      {
        $this->db->where('category_code', $ds['category']);
      }

      if( ! empty($ds['kind']))
      {
        $this->db->where('kind_code', $ds['kind']);
      }

      if( ! empty($ds['type']))
      {
        $this->db->where('type_code', $ds['type']);
      }

      if( ! empty($ds['brand']))
      {
        $this->db->where('brand_code', $ds['brand']);
      }

      if( ! empty($ds['year']))
      {
        $this->db->where('year', $ds['year']);
      }

      if(isset($ds['active']) && $ds['active'] != 'all')
      {
        $this->db->where('products.active', $ds['active']);
      }
    }

    $this->db->order_by('style_code', 'ASC');
    $this->db->order_by('color_code', 'ASC');
    $this->db->order_by('product_size.position', 'ASC');


    if($perpage != '')
    {
      $offset = $offset === NULL ? 0 : $offset;
      $this->db->limit($perpage, $offset);
    }
    // echo $this->db->get_compiled_select();
    $rs = $this->db->get();

    return $rs->result();
  }



  public function get_products_list(array $ds = array())
  {
    $this->db
    ->select('products.*')
    ->from('products')
    ->join('product_color', 'products.color_code = product_color.code', 'left')
    ->join('product_size', 'products.size_code = product_size.code', 'left');

    if(!empty($ds))
    {
      if(! empty($ds['code']))
      {
        $this->db->group_start();
        $this->db->like('products.code', $ds['code']);
        $this->db->or_like('products.old_code', $ds['code']);
        $this->db->group_end();
      }

      if(! empty($ds['name']))
      {
        $this->db->like('products.name', $ds['name']);
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
        $this->db->where('products.active', $ds['active']);
      }
    }

    $this->db->order_by('style_code', 'ASC');
    $this->db->order_by('color_code', 'ASC');
    $this->db->order_by('product_size.position', 'ASC');

    $rs = $this->db->get();

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function add(array $ds = array())
  {
    if( ! empty($ds))
    {
      if($this->db->insert('products', $ds))
      {
        return $this->db->insert_id();
      }
    }

    return FALSE;
  }


  //--- Export item to SAP
  public function add_item(array $ds = array())
  {
    if(!empty($ds))
    {
      return $this->mc->insert('OITM', $ds);
    }

    return FALSE;
  }


  //--- Export item tot SAP
  public function update_item($code, array $ds = array())
  {
    if(!empty($ds))
    {
      return $this->mc->where('ItemCode', $code)->update('OITM', $ds);
    }

    return FALSE;
  }


  public function update($code, array $ds = array())
  {
    if(!empty($ds))
    {
      $this->db->where('code', $code);
      return $this->db->update('products', $ds);
    }

    return FALSE;
  }


  public function update_by_id($id, array $ds = array())
  {
    if( ! empty($ds))
    {
      return $this->db->where('id', $id)->update('products', $ds);
    }

    return FALSE;
  }


  public function get_status($field, $item)
  {
    $rs = $this->db->select($field)->where('code', $item)->get('products');
    if($rs->num_rows() == 1)
    {
      return $rs->row()->$field;
    }

    return 0;
  }



  public function get_barcode($code)
  {
    $rs = $this->db->select('barcode')->where('code', $code)->order_by('id', 'DESC')->get('products');

    if($rs->num_rows() > 0)
    {
      return $rs->row()->barcode;
    }

    return NULL;
  }



  public function get_product_by_barcode($barcode)
  {
    $rs = $this->db->where('barcode', $barcode)->order_by('id', 'DESC')->get('products');

    if($rs->num_rows() > 0)
    {
      return $rs->row();
    }

    return NULL;
  }




  public function set_status($field, $item, $val)
  {
    return $this->db->set($field, $val)->where('code', $item)->update('products');
  }







  public function delete_item($code)
  {
    return $this->db->where('code', $code)->delete('products');
  }



  public function get($code)
  {
    $rs = $this->db->where('code', $code)->get('products');

    if($rs->num_rows() == 1)
    {
      return $rs->row();
    }

    return NULL;
  }


  public function get_by_id($id)
  {
    $rs = $this->db->where('id', $id)->get('products');

    if($rs->num_rows() == 1)
    {
      return $rs->row();
    }

    return NULL;
  }


  //---
  public function get_item($code)
  {
      $rs = $this->db->where('code', $code)->get('products');

      if($rs->num_rows() === 1)
      {
        return $rs->row();
      }

      return NULL;
  }


  public function get_with_old_code($code)
  {
    //$rs = $this->db->where('code', $code)->or_where('old_code', $code)->get('products');
    $rs = $this->db->where('code', $code)->or_where('old_code', $code)->get('products');
    if($rs->num_rows() > 0)
    {
      return $rs->num_rows() == 1 ? $rs->row() : $rs->result();
    }


    return NULL;
  }



  public function get_name($code)
  {
    $rs = $this->db->select('name')->where('code', $code)->get('products');
    if($rs->num_rows() == 1)
    {
      return $rs->row()->name;
    }

    return NULL;
  }



  public function get_style_code($code)
  {
    $rs = $this->db->select('style_code')->where('code', $code)->get('products');
    if($rs->num_rows() == 1)
    {
      return $rs->row()->style_code;
    }

    return NULL;
  }



  public function get_data($ds, $perpage = '', $offset = '')
  {
    if(!empty($ds))
    {
      foreach($ds as $field => $val)
      {
        if($val != '')
        {
          $this->db->like($field, $val);
        }
      }

      if($perpage != '')
      {
        $offset = $offset === NULL ? 0 : $offset;
        $this->db->limit($perpage, $offset);
      }

      $rs = $this->db->get('products');

      return $rs->result();
    }

    return FALSE;
  }



  public function get_style_items($code)
  {
    $this->db
    ->select('p.*')
    ->from('products AS p')
    ->join('product_color AS c', 'p.color_code = c.code', 'left')
    ->join('product_size AS s', 'p.size_code = s.code', 'left')
    ->where('p.style_code', $code)
    ->order_by('c.code', 'ASC')
    ->order_by('s.position', 'ASC');

    $rs = $this->db->get();
    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return FALSE;
  }



  public function get_items_by_color($style, $color)
  {
    $rs = $this->db->where('style_code', $style)->where('color_code', $color)->get('products');
    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return array();
  }




  public function get_item_by_color_and_size($style, $color, $size)
  {
    $rs = $this->db
    ->where('style_code', $style)
    ->where('color_code', $color)
    ->where('size_code', $size)
    ->limit(1)
    ->get('products');

    if($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return array();
  }




  public function countAttribute($style_code)
	{
		$color = $this->db->where('style_code', $style_code)->where('color_code is NOT NULL')->where('color_code !=', '')->group_by('style_code')->get('products');
		$size  = $this->db->where('style_code', $style_code)->where('size_code is NOT NULL')->where('size_code !=', '')->group_by('style_code')->get('products');
		return $color->num_rows() + $size->num_rows();
	}


  public function get_unbarcode_items($style)
  {
    $this->db->select('code');
    $this->db->where('style_code', $style);
    $this->db->where('barcode IS NULL', NULL, FALSE);
    $rs = $this->db->get('products');
    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return array();
  }



  public function update_barcode($code, $barcode)
  {
    $this->db->set('barcode', $barcode);
    return $this->db->where('code', $code)->update('products');
  }




  public function is_exists($code, $old_code = '')
  {
    if($old_code != '')
    {
      $this->db->where('code !=', $old_code);
    }

    $rs = $this->db->where('code', $code)->get('products');

    if($rs->num_rows() > 0)
    {
      return TRUE;
    }

    return FALSE;
  }



  public function is_exists_name($name, $old_name = '')
  {
    if($old_name != '')
    {
      $this->db->where('name !=', $old_name);
    }

    $rs = $this->db->where('name', $name)->get('products');

    if($rs->num_rows() > 0)
    {
      return TRUE;
    }

    return FALSE;
  }



  public function is_count_stock($code)
  {
    $rs = $this->db->where('code', $code)->where('count_stock', 1)->get('products');
    if($rs->num_rows() > 0)
    {
      return TRUE;
    }

    return FALSE;

  }


  public function is_exists_style($style)
  {
    $rs = $this->db->select('code')->where('style_code', $style)->get('products');
    if($rs->num_rows() > 0)
    {
      return TRUE;
    }

    return FALSE;
  }




  public function is_disactive_all($style_code)
  {
    $this->db->select('code')->where('style_code', $style_code)->where('active', 1);
    $rs = $this->db->get('products');
    if($rs->num_rows() > 0)
    {
      return FALSE;
    }

    return TRUE;
  }




  public function count_color($style_code)
  {
    return $this->db
    ->where('style_code', $style_code)
    ->where('color_code IS NOT NULL', NULL, FALSE)
    ->where('color_code !=', '')
    ->group_by('color_code')
    ->count_all_results('products');
  }



  public function count_size($style_code)
  {
    return $this->db
    ->where('style_code', $style_code)
    ->where('size_code IS NOT NULL', NULL, FALSE)
    ->where('size_code !=', '')
    ->group_by('size_code')
    ->count_all_results('products');
  }



  public function get_all_colors($style_code)
  {
    $rs = $this->db
    ->select('c.code, c.name')
    ->from('products AS p')
    ->join('product_color AS c', 'p.color_code = c.code', 'left')
    ->where('p.style_code', $style_code)
    ->where('p.color_code IS NOT NULL', NULL, FALSE)
    ->group_by('p.color_code')
    ->order_by('p.color_code', 'ASC')
    ->get();

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return FALSE;
  }


  public function get_all_sizes($style_code)
  {
    $rs = $this->db
    ->select('s.code, s.name')
    ->from('products AS p')
    ->join('product_size AS s', 'p.size_code = s.code', 'left')
    ->where('p.style_code', $style_code)
    ->where('p.size_code IS NOT NULL', NULL, FALSE)
    ->group_by('p.size_code')
    ->order_by('s.position', 'ASC')
    ->get();

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return FALSE;
  }


  public function get_style_sizes_cost_price($style_code)
  {
    $qr = "SELECT DISTINCT s.code, p.cost, p.price FROM products AS p
          LEFT JOIN product_size AS s ON p.size_code = s.code
          WHERE p.style_code = '{$style_code}'
          ORDER BY s.position ASC";
    $rs = $this->db->query($qr);
    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return FALSE;
  }




  public function update_cost_price_by_size($code, $size, $cost, $price)
  {
    if(!empty($code))
    {
      $this->db
      ->set('cost', $cost)
      ->set('price', $price)
      ->where('style_code', $code)
      ->where('size_code', $size);

      return $this->db->update('products');
    }

    return FALSE;
  }


  public function get_unit_code($code)
  {
    $rs = $this->db
    ->select('unit_code')
    ->where('code', $code)
    ->get('products');
    if($rs->num_rows() === 1)
    {
      return $rs->row()->unit_code;
    }

    return NULL;
  }



  public function has_transection($code)
  {
    $od = $this->db->select('product_code')->where('product_code', $code)->count_all_results('order_details');
    $oc = $this->db->select('product_code')->where('product_code', $code)->count_all_results('order_transform_detail');
    $rc = $this->db->select('product_code')->where('product_code', $code)->count_all_results('receive_product_detail');
    $rt = $this->db->select('product_code')->where('product_code', $code)->count_all_results('receive_transform_detail');
    $tf = $this->db->select('product_code')->where('product_code', $code)->count_all_results('transfer_detail');
    $cn = $this->db->select('product_code')->where('product_code', $code)->count_all_results('return_order_detail');

    $all = $od+$oc+$rc+$rt+$tf+$cn;
    if($all > 0)
    {
      return TRUE;
    }

    return FALSE;
  }


	public function get_attribute($code)
	{
		$rs = $this->db
		->select('pd.*')
		->select('co.name AS color_name, si.name AS size_name')
		->select('pg.name AS group_name, pu.name AS sub_group_name')
		->select('pc.name AS category_name, pk.name AS kind_name')
		->select('pt.name AS type_name, br.name AS brand_name')
		->from('products AS pd')
		->join('product_color AS co', 'pd.color_code = co.code', 'left')
		->join('product_size AS si', 'pd.size_code = si.code', 'left')
		->join('product_group AS pg', 'pd.group_code = pg.code', 'left')
		->join('product_sub_group AS pu', 'pd.sub_group_code = pu.code', 'left')
		->join('product_category AS pc', 'pd.category_code = pc.code', 'left')
		->join('product_kind AS pk', 'pd.kind_code = pk.code', 'left')
		->join('product_type AS pt', 'pd.type_code = pt.code', 'left')
		->join('product_brand AS br', 'pd.brand_code = br.code', 'left')
		->where('pd.code', $code)
		->get();

		if($rs->num_rows() === 1)
		{
			return $rs->row();
		}

		return NULL;
	}

  public function get_items_last_sync()
  {
    $rs = $this->db->select_max('last_sync')->get('products');

    return $rs->row()->last_sync;
  }
}
?>
