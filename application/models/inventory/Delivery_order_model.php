<?php
class Delivery_order_model extends CI_Model
{
  public function __construct()
  {
    parent::__construct();
  }



  public function get_sold_details($reference)
  {
    $rs = $this->db->where('reference', $reference)->get('order_sold');
    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return FALSE;
  }




  public function count_rows(array $ds = array(), $state = 7)
  {
    $this->db->select('state')
    ->from('orders')
    ->join('channels', 'channels.code = orders.channels_code','left')
    ->join('customers', 'customers.code = orders.customer_code', 'left')
    ->where('orders.state', $state);

    if(!empty($ds['code']))
    {
      $this->db->like('orders.code', $ds['code']);
    }

    if( ! empty($ds['reference']))
    {
      $this->db->like('orders.reference', $ds['reference']);
    }

    if(!empty($ds['customer']))
    {
			$this->db->group_start();
			$this->db->like('orders.customer_code', $ds['customer']);
      $this->db->or_like('customers.name', $ds['customer']);
      $this->db->or_like('orders.customer_ref', $ds['customer']);
      $this->db->group_end();
    }

    //---- user name / display name
    if( ! empty($ds['user']) && $ds['user'] != 'all')
    {
      $this->db->where('user', $ds['user']);
    }


    if(!empty($ds['role']))
    {
      $this->db->where('role', $ds['role']);
    }


    if(!empty($ds['channels']))
    {
      $this->db->where('orders.channels_code', $ds['channels']);
    }

    if(!empty($ds['warehouse']) && $ds['warehouse'] != 'all')
    {
      $this->db->where('orders.warehouse_code', $ds['warehouse']);
    }

    if(isset($ds['is_valid']) && $ds['is_valid'] != 2)
    {
      $this->db->where('orders.is_valid', $ds['is_valid']);
    }


		if(isset($ds['is_exported']) && $ds['is_exported'] != 'all')
		{
			$this->db->where('orders.is_exported', $ds['is_exported']);
		}


    if($ds['from_date'] != '' && $ds['to_date'] != '')
    {
      $this->db->where('orders.date_add >=', from_date($ds['from_date']));
      $this->db->where('orders.date_add <=', to_date($ds['to_date']));
    }

    $rs = $this->db->get();

    return $rs->num_rows();
  }



  public function get_data(array $ds = array(), $perpage = NULL, $offset = NULL, $state = 7)
  {
    $total_query = "(SELECT SUM(total_amount) FROM order_details WHERE order_code = orders.code) AS total_amount";
    $this->db->select("orders.*, channels.name AS channels_name, customers.name AS customer_name, {$total_query}")
    ->from('orders')
    ->join('channels', 'channels.code = orders.channels_code','left')
    ->join('customers', 'customers.code = orders.customer_code', 'left')
    ->where('orders.state', $state);

    if(!empty($ds['code']))
    {
      $this->db->like('orders.code', $ds['code']);
    }

    if( ! empty($ds['reference']))
    {
      $this->db->like('orders.reference', $ds['reference']);
    }

    if(!empty($ds['customer']))
    {
      $this->db->group_start();
			$this->db->like('orders.customer_code', $ds['customer']);
      $this->db->or_like('customers.name', $ds['customer']);
      $this->db->or_like('orders.customer_ref', $ds['customer']);
      $this->db->group_end();
    }

    //---- user name / display name
    if( ! empty($ds['user']) && $ds['user'] != 'all')
    {
      $this->db->where('orders.user', $ds['user']);
    }


    if(!empty($ds['role']))
    {
      $this->db->where('orders.role', $ds['role']);
    }

    if(!empty($ds['channels']))
    {
      $this->db->where('orders.channels_code', $ds['channels']);
    }


    if(!empty($ds['warehouse']) && $ds['warehouse'] != 'all')
    {
      $this->db->where('orders.warehouse_code', $ds['warehouse']);
    }


    if(isset($ds['is_valid']) && $ds['is_valid'] != 'all')
    {
      $this->db->where('orders.is_valid', $ds['is_valid']);
    }

		if(isset($ds['is_exported']) && $ds['is_exported'] != 'all')
		{
			$this->db->where('orders.is_exported', $ds['is_exported']);
		}


    if($ds['from_date'] != '' && $ds['to_date'] != '')
    {
      $this->db->where('orders.date_add >=', from_date($ds['from_date']));
      $this->db->where('orders.date_add <=', to_date($ds['to_date']));
    }

    if(!empty($ds['order_by']))
    {
      $order_by = "orders.{$ds['order_by']}";
      $this->db->order_by($order_by, $ds['sort_by']);
    }
    else
    {
      $this->db->order_by('orders.date_add', 'DESC');
    }


    if(!empty($perpage))
    {
      $offset = $offset === NULL ? 0 : $offset;
      $this->db->limit($perpage, $offset);
    }

    $rs = $this->db->get();

    return $rs->result();
  }




    //------------------ สำหรับแสดงยอดที่มีการบันทึกขายไปแล้ว -----------//
    //--- รายการสั้งซื้อ รายการจัดสินค้า รายการตรวจสินค้า
    //--- เปรียบเทียบยอดที่มีการสั่งซื้อ และมีการตรวจสอนค้า
    //--- เพื่อให้ได้ยอดที่ต้องเปิดบิล บันทึกขายจริงๆ
    //--- ผลลัพธ์จะได้ยอดสั่งซื้อเป็นหลัก หากไม่มียอดตรวจ จะได้ยอดตรวจ เป็น NULL
    //--- กรณีสินค้าเป็นสินค้าที่ไม่นับสต็อกจะบันทึกตามยอดที่สั่งมา
    public function get_billed_detail($code, $use_qc = TRUE)
    {
      $qr = "SELECT o.id, o.product_code, o.product_name, o.qty AS order_qty, o.is_count, ";
      $qr .= "o.price, o.discount1, o.discount2, o.discount3, o.vat_code, o.vat_rate, avgBillDiscAmount, ";
      $qr .= "(o.discount_amount / o.qty) AS discount_amount, ";
      $qr .= "(o.total_amount/o.qty) AS final_price, ";
      $qr .= "(SELECT SUM(qty) FROM prepare WHERE order_code = '{$code}' AND product_code = o.product_code AND (order_detail_id = o.id OR order_detail_id IS NULL)) AS prepared ";

      if($use_qc)
      {
        $qr .= ", (SELECT SUM(qty) FROM qc WHERE order_code = '{$code}' AND product_code = o.product_code AND (order_detail_id = o.id OR order_detail_id IS NULL)) AS qc ";
      }

      $qr .= "FROM order_details AS o ";
      $qr .= "WHERE o.order_code = '{$code}'";

      $rs = $this->db->query($qr);

      if($rs->num_rows() > 0)
      {
        return $rs->result();
      }

      return FALSE;
    }



    //------------- สำหรับใช้ในการบันทึกขาย ---------//
    //--- รายการสั้งซื้อ รายการจัดสินค้า รายการตรวจสินค้า
    //--- เปรียบเทียบยอดที่มีการสั่งซื้อ และมีการตรวจสอนค้า
    //--- เพื่อให้ได้ยอดที่ต้องเปิดบิล บันทึกขายจริงๆ
    //--- ผลลัพธ์จะไม่ได้ยอดที่มีการสั่งซื้อแต่ไม่มียอดตรวจ หรือ มียอดตรวจแต่ไม่มียอดสั่งซื้อ (กรณีมีการแก้ไขออเดอร์)
    public function get_bill_detail($code, $useQc = TRUE)
    {
      $qr = "SELECT o.id, o.baseCode, o.baseLine, o.baseId, o.line_id, o.line_text, o.style_code, o.product_code, o.product_name, o.qty AS order_qty, ";
      $qr .= "o.cost, o.price, o.discount1, o.discount2, o.discount3, o.vat_code, o.vat_rate, avgBillDiscAmount, ";
      $qr .= "o.id_rule, ru.id_policy, o.is_count, ";
      $qr .= "(o.discount_amount / o.qty) AS discount_amount, ";
      $qr .= "(o.total_amount/o.qty) AS final_price, ";
      $qr .= "(SELECT SUM(qty) FROM buffer WHERE order_code = '{$code}' AND product_code = o.product_code AND (order_detail_id = o.id OR order_detail_id IS NULL)) AS prepared ";

      if($useQc)
      {
        $qr .= ", (SELECT SUM(qty) FROM qc WHERE order_code = '{$code}' AND product_code = o.product_code AND (order_detail_id = o.id OR order_detail_id IS NULL)) AS qc ";
      }

      $qr .= "FROM order_details AS o ";
      $qr .= "LEFT JOIN discount_rule AS ru ON ru.id = o.id_rule ";
      $qr .= "WHERE o.order_code = '{$code}' ";

      if($useQc)
      {
        $qr .= "HAVING qc IS NOT NULL";
      }

      $rs = $this->db->query($qr);

      if($rs->num_rows() > 0)
      {
        return $rs->result();
      }

      return FALSE;
    }

    public function get_non_count_bill_detail($code)
    {
      $qr  = "SELECT o.id, o.baseCode, o.baseLine, o.baseId, o.line_id, o.line_text, o.product_code, o.product_name, o.style_code, o.qty, ";
      $qr .= "o.cost, o.price, o.discount1, o.discount2, o.discount3, o.vat_code, o.vat_rate, avgBillDiscAmount, ";
      $qr .= "o.id_rule, ru.id_policy, o.is_count, ";
      $qr .= "(o.discount_amount / o.qty) AS discount_amount, ";
      $qr .= "(o.total_amount/o.qty) AS final_price ";
      $qr .= "FROM order_details AS o ";
      $qr .= "LEFT JOIN discount_rule AS ru ON ru.id = o.id_rule ";
      $qr .= "WHERE o.order_code = '{$code}' ";
      $qr .= "AND o.is_count = 0 ";

      $rs = $this->db->query($qr);
      if($rs->num_rows() > 0)
      {
        return $rs->result();
      }

      return FALSE;
    }


    public function sold(array $ds = array())
    {
      if(!empty($ds))
      {
        return $this->db->insert('order_sold', $ds);
      }

      return FALSE;
    }


    public function add_sap_delivery_order(array $ds = array())
    {
      $rs = $this->mc->insert('ODLN', $ds);
      if($rs)
      {
        return $this->mc->insert_id();
      }

      return FALSE;
    }


    public function update_sap_delivery_order($docEntry, $ds)
    {
      return $this->mc->where('DocEntry', $docEntry)->update('ODLN', $ds);
    }


    public function add_delivery_row(array $ds = array())
    {
      return $this->mc->insert('DLN1', $ds);
    }


    public function is_doc_exists($code)
    {
      $rs = $this->mc->select('U_ECOMNO')->where('U_ECOMNO', $code)->get('ODLN');
      if($rs->num_rows() > 0)
      {
        return TRUE;
      }

      return FALSE;
    }


    public function get_middle_delivery_order($code)
    {
      $rs = $this->mc
      ->select('DocEntry')
      ->where('U_ECOMNO', $code)
      ->group_start()
      ->where('F_Sap !=', 'Y')
      ->or_where('F_Sap IS NULL', NULL, FALSE)
      ->group_end()
      ->get('ODLN');

      if($rs->num_rows() > 0)
      {
        return $rs->result();
      }

      return NULL;
    }



    public function get_sap_delivery_order($code)
    {
      $rs = $this->ms
      ->select('DocEntry, DocStatus')
      ->where('U_ECOMNO', $code)
      ->where('CANCELED', 'N')
      ->get('ODLN');
      if($rs->num_rows() > 0)
      {
        return $rs->result();
      }

      return NULL;
    }


		public function exists_sap_delivery_order($code)
    {
      $rs = $this->ms
      ->where('U_ECOMNO', $code)
      ->where('CANCELED', 'N')
      ->count_all_results('ODLN');
      if($rs > 0)
      {
        return TRUE;
      }

      return FALSE;
    }



    public function sap_exists_details($code)
    {
      $rs = $this->mc->select('LineNum')->where('U_ECOMNO', $code)->get('DLN1');
      if($rs->num_rows() > 0)
      {
        return TRUE;
      }

      return FALSE;
    }


    //--- ลบรายการที่ค้างใน middle ที่ยังไม่ได้เอาเข้า SAP ออก
    public function drop_middle_exits_data($docEntry)
    {
      $this->mc->trans_start();
      $this->mc->where('DocEntry', $docEntry)->delete('DLN1');
      $this->mc->where('DocEntry', $docEntry)->delete('ODLN');
      $this->mc->trans_complete();
      return $this->mc->trans_status();
    }


    public function drop_sap_exists_details($code)
    {
      return $this->mc->where('U_ECOMNO', $code)->delete('DLN1');
    }




    public function update_delivery_row($DocEntry, $line, $ds = array())
    {

      return $this->mc->where('DocEntry', $DocEntry)->where('LineNum', $line)->update('DLN1', $ds);
    }



    public function getDocEntry($code)
    {
      $rs = $this->mc->select_max('DocEntry')->where('U_ECOMNO', $code)->get('ODLN');
      if($rs->num_rows() === 1)
      {
        return $rs->row()->DocEntry;
      }

      return FALSE;
    }


}

 ?>
