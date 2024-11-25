<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Check_stock_diff_model extends CI_Model
{
  public function __construct()
  {
    parent::__construct();
  }

  public function get($id)
  {
    $rs = $this->db->where('id', $id)->get('stock_diff');
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
      return $this->db->insert('stock_diff', $ds);
    }

    return FALSE;
  }



  public function update($id, array $ds = array())
  {
    if(!empty($id) && !empty($ds))
    {
      return $this->db->where('id', $id)->update('stock_diff', $ds);
    }

    return FALSE;
  }


  public function delete($id)
  {
    return $this->db->where('id', $id)->delete('stock_diff');
  }




  public function get_list(array $ds = array(), $perpage = NULL, $offset = NULL)
  {
    $this->db
    ->select('diff.*')
    ->select('pd.name AS product_name, pd.old_code')
    ->select('zn.name AS zone_name')
    ->from('stock_diff AS diff')
    ->join('products AS pd', 'diff.product_code = pd.code', 'left')
    ->join('zone AS zn', 'diff.zone_code = zn.code', 'left');

    if($ds['status'] !== 'all')
    {
      $this->db->where('status', $ds['status']);
    }


    if(!empty($ds['product_code']))
    {
      $this->db->group_start();
      $this->db->like('diff.product_code', $ds['product_code']);
      $this->db->or_like('pd.name', $ds['product_code']);
      $this->db->group_end();
    }

    if(!empty($ds['zone_code']))
    {
      $this->db->group_start();
      $this->db->like('diff.zone_code', $ds['zone_code']);
      $this->db->or_like('zn.name', $ds['zone_code']);
      $this->db->group_end();
    }

    if(!empty($ds['user']))
    {
      $this->db->like('diff.user', $ds['user']);
    }


    if(!empty($ds['from_date']) && !empty($ds['to_date']))
    {
      $this->db->where('diff.date_upd >=', from_date($ds['from_date']));
      $this->db->where('diff.date_upd <=', to_date($ds['to_date']));
    }


    $this->db->order_by('diff.date_upd', 'DESC');

    if(!empty($perpage))
    {
      $offset = $offset === NULL ? 0 : $offset;
      $this->db->limit($perpage, $offset);
    }

    $rs = $this->db->get();

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;

  }


  public function count_rows(array $ds = array())
  {
    $this->db
    ->from('stock_diff AS diff')
    ->join('products AS pd', 'diff.product_code = pd.code', 'left')
    ->join('zone AS zn', 'diff.zone_code = zn.code', 'left');

    if($ds['status'] !== 'all')
    {
      $this->db->where('status', $ds['status']);
    }


    if(!empty($ds['product_code']))
    {
      $this->db->group_start();
      $this->db->like('diff.product_code', $ds['product_code']);
      $this->db->or_like('pd.name', $ds['product_code']);
      $this->db->group_end();
    }

    if(!empty($ds['zone_code']))
    {
      $this->db->group_start();
      $this->db->like('diff.zone_code', $ds['zone_code']);
      $this->db->or_like('zn.name', $ds['zone_code']);
      $this->db->group_end();
    }

    if(!empty($ds['user']))
    {
      $this->db->like('diff.user', $ds['user']);
    }


    if(!empty($ds['from_date']) && !empty($ds['to_date']))
    {
      $this->db->where('diff.date_upd >=', from_date($ds['from_date']));
      $this->db->where('diff.date_upd <=', to_date($ds['to_date']));
    }

    return $this->db->count_all_results();
  }


  public function get_stock_and_diff($zone_code, $product_code = NULL)
  {
    $this->ms
    ->select('OITM.ItemCode AS product_code')
    ->select('OITM.CodeBars AS barcode')
    ->select('OITM.U_OLDCODE AS old_code')
    ->select('OIBQ.OnHandQty')
    ->from('OIBQ')
    ->join('OBIN', 'OIBQ.BinAbs = OBIN.AbsEntry', 'left')
    ->join('OITM', 'OIBQ.ItemCode = OITM.ItemCode', 'left')
    ->where('OIBQ.OnHandQty !=', 0)
    ->where('OBIN.BinCode', $zone_code);

    if(!empty($product_code))
    {
      $this->ms->group_start();
      $this->ms->like('OITM.ItemCode', $product_code)->or_like('OITM.U_OLDCODE', $product_code);
      $this->ms->group_end();
    }

    $this->ms->order_by('OITM.ItemCode', 'ASC');

    $rs = $this->ms->get();

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;

  }


  public function get_active_diff($zone_code, $product_code)
  {
    $rs = $this->db
    ->select('qty')
    ->where('zone_code', $zone_code)
    ->where('product_code', $product_code)
    ->where('status', 0)
    ->get('stock_diff');

    if($rs->num_rows() === 1)
    {
      return $rs->row()->qty;
    }

    return 0;
  }



  public function get_active_diff_not_in_stock($zone_code, array $pd_in = array(), $pd_code = NULL)
  {
    if(!empty($pd_in))
    {
      $this->db
      ->where('zone_code', $zone_code)
      ->where_not_in('product_code', $pd_in)
      ->where('status', 0);
      if(!empty($pd_code))
      {
        $this->db->like('product_code', $pd_code);
      }

      $rs = $this->db->get('stock_diff');

      if($rs->num_rows() > 0)
      {
        return $rs->result();
      }
    }

    return FALSE;
  }


  public function get_active_diff_zone($zone_code)
  {
    $rs = $this->db
    ->where('zone_code', $zone_code)
    ->where('status', 0)
    ->get('stock_diff');

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function get_active_diff_detail($zone_code, $product_code)
  {
    $rs = $this->db
    ->where('zone_code', $zone_code)
    ->where('product_code', $product_code)
    ->where('status', 0)
    ->get('stock_diff');

    if($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return NULL;
  }

} //--- end class

?>
