<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Sap_stock_model extends CI_Model
{
  public function __construct()
  {
    parent::__construct();
  }

  public function get_stock_item_group_by_warehouse($item_code)
  {
    $rs = $this->ms
    ->select('I.ItemCode, I.ItemName')
    ->select('W.WhsCode, W.WhsName')
    ->select_sum('Q.OnHandQty')
    ->from('OIBQ AS Q')
    ->join('OITM AS I', 'Q.ItemCode = I.ItemCode', 'left')
    ->join('OWHS AS W', 'Q.WhsCode = W.WhsCode', 'left')
    ->where('Q.ItemCode', $item_code)
    ->where('Q.OnHandQty >', 0)
    ->group_by('I.ItemCode, I.ItemName, W.WhsCode, W.WhsName')
    ->get();

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function get_list(array $ds = array(), $perpage = NULL, $offset = NULL)
  {
    if(!empty($ds['item_code']) OR !empty($ds['zone_code']))
    {
      $itemCode = $ds['item_code'];
      $zoneCode = $ds['zone_code'];

      $qr  = "SELECT OITM.ItemCode, OITM.ItemName, OITM.CodeBars, OITM.U_OLDCODE, ";
      $qr .= "OBIN.BinCode, OBIN.Descr, OIBQ.OnHandQty ";
      $qr .= "FROM OIBQ ";
      $qr .= "LEFT JOIN OBIN ON OIBQ.BinAbs = OBIN.AbsEntry ";
      $qr .= "LEFT JOIN OITM ON OIBQ.ItemCode = OITM.ItemCode ";
      $qr .= "WHERE OIBQ.OnHandQty != 0 ";

      if(!empty($ds['show_system']))
      {
        if($ds['show_system'] == 'no')
        {
          $qr .= "AND OBIN.SysBin = 'N' ";
        }
      }

      if(!empty($ds['item_code']))
      {
        $qr .= "AND (OITM.ItemCode LIKE N'%{$itemCode}%' OR OITM.CodeBars LIKE N'%{$itemCode}%' OR OITM.ItemName LIKE N'%{$this->ms->escape_str($itemCode)}%') ";
      }

      if(!empty($ds['zone_code']))
      {
        $qr .= "AND (OBIN.BinCode LIKE N'%{$this->ms->escape_str($zoneCode)}%' OR OBIN.Descr LIKE N'%{$this->ms->escape_str($zoneCode)}%') ";
      }

      $qr .= "ORDER BY OIBQ.ItemCode ASC, OBIN.BinCode ASC ";

      if(!empty($perpage))
      {
        if(!empty($offset))
        {
          $qr .= "OFFSET {$offset} ROWS FETCH NEXT {$perpage} ROWS ONLY";
        }
        else
        {
          $qr .= "OFFSET 0 ROWS FETCH FIRST {$perpage} ROWS ONLY";
        }
      }

      $rs = $this->ms->query($qr);

      if($rs->num_rows() > 0)
      {
        return $rs->result();
      }
    }

    return NULL;
  }


  public function count_rows(array $ds = array())
  {
    if(!empty($ds['item_code']) OR !empty($ds['zone_code']))
    {
      $itemCode = $ds['item_code'];
      $zoneCode = $ds['zone_code'];

      $qr  = "SELECT COUNT(*) AS numrows ";
      $qr .= "FROM OIBQ ";
      $qr .= "LEFT JOIN OBIN ON OIBQ.BinAbs = OBIN.AbsEntry ";
      $qr .= "LEFT JOIN OITM ON OIBQ.ItemCode = OITM.ItemCode ";
      $qr .= "WHERE OIBQ.OnHandQty != 0 ";

      if(!empty($ds['show_system']))
      {
        if($ds['show_system'] == 'no')
        {
          $qr .= "AND OBIN.SysBin = 'N' ";
        }
      }

      if(!empty($ds['item_code']))
      {
        $qr .= "AND (OITM.ItemCode LIKE N'%{$itemCode}%' OR OITM.U_OLDCODE LIKE N'%{$itemCode}%' OR OITM.ItemName LIKE N'%{$this->ms->escape_str($itemCode)}%') ";
      }

      if(!empty($ds['zone_code']))
      {
        $qr .= "AND (OBIN.BinCode LIKE N'%{$this->ms->escape_str($zoneCode)}%' OR OBIN.Descr LIKE N'%{$this->ms->escape_str($zoneCode)}%') ";
      }

      $rs = $this->ms->query($qr);

      return $rs->row()->numrows;
    }

    return 0;
  }




} //--- end class

?>
