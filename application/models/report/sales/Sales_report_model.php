<?php
class Sales_report_model extends CI_Model
{
  public function __construct()
  {
    parent::__construct();
  }

  public function get_sales_invoice($ds)
  {
    $this->db
    ->select('bookcode, code, is_term, vat_type, DocDate, CardCode, CardName')
    ->select('VatSum, DiscSum, DocTotal, BaseType, BaseRef, so_code, SlpCode')
    ->select('shipped_date, channels_code, WhtAmount, downPaymentAmount')
    ->where('status !=', 'D');

    if($ds->bookcode != 'all')
    {
      $this->db->where('bookcode', $ds->bookcode); //-- all ทั้งหมด , C = เงินสด, T = เครดิต, P = POS (สด)
    }

    if($ds->dateType == 'D')
    {
      $this->db
      ->where('DocDate >=', from_date($ds->fromDate))
      ->where('DocDate <=', to_date($ds->toDate));
    }
    else
    {
      $this->db
      ->where('shipped_date >=', from_date($ds->fromDate))
      ->where('shipped_date <=', to_date($ds->toDate));
    }

    if($ds->allSale == 0 && ! empty($ds->saleList))
    {
      $this->db->where_in('SlpCode', $ds->saleList);
    }

    $rs = $this->db->order_by('DocDate', 'ASC')->get('invoice');

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }



  public function get_sales_details($from_date, $to_date, $date_type = 'S')
  {
    $this->db
    ->select('d.*')
    ->select('o.is_term, o.so_code, o.CardCode, o.CardName, o.NumAtCard, o.tax_id, o.channels_code, o.payment_role')
    ->from('invoice_details AS d')
    ->join('invoice AS o', 'd.invoice_id = o.id', 'left')
    ->where('o.status', 'C')
    ->where('d.LineStatus', 'C');

    if($date_type == 'D')
    {
      $this->db
      ->where('d.DocDate >=', from_date($from_date))
      ->where('d.DocDate <=', to_date($to_date))
      ->order_by('d.DocDate', 'ASC');
    }
    else
    {
      $this->db
      ->where('d.shipped_date >=', from_date($from_date))
      ->where('d.shipped_date <=', to_date($to_date))
      ->order_by('d.shipped_date', 'ASC');
    }

    $rs = $this->db->get();

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }



  public function get_online_channels_details(array $ds = array())
  {
    if(!empty($ds))
    {
      $this->db
      ->select('o.code AS code, o.date_add, o.reference')
      ->select('o.shipping_code, o.shipping_fee, o.service_fee')
      ->select('o.customer_ref, o.id_address, c.name AS channels')
      ->select('pm.name AS payment, st.name AS state')
      ->select('od.product_code, od.price')
      ->select('od.qty, od.discount_amount, od.total_amount')
      ->from('order_details AS od')
      ->join('orders AS o', 'od.order_code = o.code', 'left')
      ->join('channels AS c', 'o.channels_code = c.code', 'left')
      ->join('payment_method AS pm', 'o.payment_code = pm.code', 'left')
      ->join('order_state AS st', 'o.state = st.state', 'left')
      ->where('o.date_add >=', from_date($ds['from_date']))
      ->where('o.date_add <=', to_date($ds['to_date']))
      ->where('o.role', 'S')
      ->where('c.is_online', 1)
      ->where('o.is_expired', 0);

      if($ds['all_channels'] == 0 && !empty($ds['channels']))
      {
        $this->db->where_in('o.channels_code', $ds['channels']);
      }

      if(!empty($ds['item_from']) && !empty($ds['item_to']))
      {
        $this->db
        ->where('od.product_code >=', $ds['item_from'])
        ->where('od.product_code <=', $ds['item_to']);
      }

      if(!empty($ds['from_reference']) && !empty($ds['to_reference']))
      {
        $this->db
        ->where('o.reference >=', $ds['from_reference'])
        ->where('o.reference <=', $ds['to_reference']);
      }

      $this->db->order_by('o.code', 'ASC')->order_by('od.product_code', 'ASC');

      $rs = $this->db->get();

      if($rs->num_rows() > 0)
      {
        return $rs->result();
      }
    }

    return NULL;
  }


  public function get_invoice_summary_by_saleman($sale_id, $ds)
  {
    $this->db
    ->select_sum('DocTotal')
    ->where('status !=', 'D')
    ->where('SlpCode', $sale_id);

    if( ! empty($ds))
    {
      if($ds->bookcode != 'all')
      {
        $this->db->where('bookcode', $ds->bookcode); //-- all ทั้งหมด , C = เงินสด, T = เครดิต, P = POS (สด)
      }

      if($ds->dateType == 'D')
      {
        $this->db
        ->where('DocDate >=', from_date($ds->fromDate))
        ->where('DocDate <=', to_date($ds->toDate));
      }
      else
      {
        $this->db
        ->where('shipped_date >=', from_date($ds->fromDate))
        ->where('shipped_date <=', to_date($ds->toDate));
      }
    }

    $rs = $this->db->get('invoice');

    if($rs->num_rows() === 1)
    {
      return $rs->row()->DocTotal;
    }

    return 0;
  }
  

  public function get_sum_total_amount_by_saleman($sale_code, $from_date, $to_date)
  {
    $rs = $this->db
    ->select_sum('total_amount', 'amount')
    ->select_sum('qty', 'qty')
    ->where_in('role', ['S', 'M'])
    ->where('sale_code', $sale_code)
    ->where('date_add >=', from_date($from_date))
    ->where('date_add <=', to_date($to_date))
    ->get('order_sold');

    if($rs->num_rows() == 1)
    {
      return $rs->row();
    }

    return 0;
  }

}
 ?>
