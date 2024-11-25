<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Sap_consignment_stock_model extends CI_Model
{
  public function __construct()
  {
    parent::__construct();

  }


  public function get_list(array $ds = array(), $perpage = NULL, $offset = NULL)
  {
    if(!empty($ds['item_code']) OR !empty($ds['zone_code']))
    {
      if(!empty($ds['item_code']) OR !empty($ds['zone_code']))
      {
        $itemCode = $ds['item_code'];
        $zoneCode = $ds['zone_code'];

        $qr  = "SELECT OITM.ItemCode, OITM.ItemName, OITM.U_OLDCODE, ";
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
          $qr .= "AND (OITM.ItemCode LIKE '%{$itemCode}%' OR OITM.U_OLDCODE LIKE '%{$itemCode}%') ";
        }

        if(!empty($ds['zone_code']))
        {
          $qr .= "AND (OBIN.BinCode LIKE '%{$zoneCode}%' OR OBIN.Descr LIKE N'%{$zoneCode}%') ";
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

        $rs = $this->cn->query($qr);

        if($rs->num_rows() > 0)
        {
          return $rs->result();
        }
      }

      return NULL;
    }
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
        $qr .= "AND (OITM.ItemCode LIKE '%{$itemCode}%' OR OITM.U_OLDCODE LIKE '%{$itemCode}%') ";
      }

      if(!empty($ds['zone_code']))
      {
        $qr .= "AND (OBIN.BinCode LIKE '%{$zoneCode}%' OR OBIN.Descr LIKE N'%{$zoneCode}%') ";
      }

      $rs = $this->cn->query($qr);

      return $rs->row()->numrows;
    }

    return 0;
  }


  public function get_stock_zone($zone_code, $pd_code)
  {
    $this->cn->select_sum('OIBQ.OnHandQty', 'qty')
    ->from('OBIN')
    ->join('OIBQ', 'OBIN.WhsCode = OIBQ.WhsCode AND OBIN.AbsEntry = OIBQ.BinAbs', 'left')
    ->where('OIBQ.ItemCode', $pd_code)
    ->where('OBIN.BinCode', $zone_code);
    $rs = $this->cn->get();
    if($rs->num_rows() == 1)
    {
      return intval($rs->row()->qty);
    }

    return 0;
  }




} //--- end class

?>
