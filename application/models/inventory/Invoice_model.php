<?php
class Invoice_model extends CI_Model
{
  public function __construct()
  {
    parent::__construct();
  }

  public function get_billed_detail($code, $useQc = TRUE)
  {
    $qr = "SELECT o.id, o.product_code, o.product_name, o.qty AS order_qty, o.is_count, ";
    $qr .= "o.price, o.discount1, o.discount2, o.discount3, o.vat_code, o.vat_rate, o.avgBillDiscAmount, ";
    $qr .= "(o.discount_amount / o.qty) AS discount_amount, ";
    $qr .= "(o.total_amount/o.qty) AS final_price, ";
    $qr .= "(SELECT SUM(qty) FROM prepare WHERE order_code = '{$code}' AND product_code = o.product_code AND (order_detail_id = o.id OR order_detail_id IS NULL)) AS prepared, ";

    if($useQc)
    {
      $qr .= "(SELECT SUM(qty) FROM qc WHERE order_code = '{$code}' AND product_code = o.product_code AND (order_detail_id = o.id OR order_detail_id IS NULL)) AS qc, ";
    }

    $qr .= "(SELECT SUM(qty) FROM order_sold WHERE reference = '{$code}' AND product_code = o.product_code AND (order_detail_id = o.id OR order_detail_id IS NULL)) AS sold ";
    $qr .= "FROM order_details AS o ";
    $qr .= "WHERE o.order_code = '{$code}'";

    $rs = $this->db->query($qr);
    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return FALSE;
  }


  //----- get sold qty from order sold
  public function get_billed_detail_qty($code)
  {
    $rs = $this->db
    ->select('product_code, product_name')
    ->select_sum('qty')
    ->where('is_count', 1)
    ->where('reference', $code)
    ->group_by('product_code')
    ->order_by('product_code', 'ASC')
    ->get('order_sold');

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function get_details($code)
  {
    $rs = $this->db->where('reference', $code)->get('order_sold');

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return FALSE;
  }



  public function get_total_sold_qty($code)
  {
    $rs = $this->db->select_sum('qty')->where('reference', $code)->get('order_sold');
    return intval($rs->row()->qty);
  }



  public function get_item_sold_qty($code, $product_code)
  {
    $rs = $this->db->select_sum('qty')->where('reference', $code)->where('product_code', $product_code)->get('order_sold');

    if($rs->num_rows() === 1)
    {
      return intval($rs->row()->qty);
    }

    return 0;
  }



  public function drop_sold($id)
  {
    return $this->db->where('id', $id)->delete('order_sold');
  }


  public function drop_all_sold($code)
  {
    return $this->db->where('reference', $code)->delete('order_sold');
  }



  public function is_over_due($customer_code)
  {
    $is_strict = getConfig('STRICT_OVER_DUE');
    if($is_strict == 0)
    {
      return FALSE;
    }
    else
    {
      $control_day = getConfig('OVER_DUE_DATE');
			$control_day++;

      $rs = $this->ms
      ->select('DocEntry', FALSE)
      ->where('CardCode', $customer_code)
      ->where('DocTotal >', 'PaidToDate', FALSE)
      ->where("DATEADD(day,{$control_day}, DocDueDate) < ", "GETDATE()", FALSE)
      ->get('OINV');

      if($rs->num_rows() > 0)
      {
        return TRUE;
      }
    }

    return FALSE;
  }


  public function is_received($code)
  {
    $rs = $this->mc
    ->select('F_Receipt')
    ->where('U_ECOMNO', $code)
    ->group_start()
    ->where('F_Sap IS NULL', NULL, FALSE)
    ->or_where('F_Sap', 'N')
    ->group_end()
    ->get('DFOWTR');

    if($rs->num_rows() === 1)
    {
      return $rs->row()->F_Receipt;
    }

    return FALSE;
  }

} //--- end class

 ?>
