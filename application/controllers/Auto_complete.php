<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Auto_complete extends CI_Controller
{
  public $ms;
  public function __construct()
  {
    parent::__construct();
    $this->ms = $this->load->database('ms', TRUE);
  }

  public function get_uninvoice_down_payment()
  {
    $ds = array();
    $req = $this->db->escape_str(trim($_REQUEST['term']));

    $this->db
    ->where('status', 'O')
    ->where('invoice_code IS NULL', NULL, FALSE);

    if($req != '*')
    {
      $this->db
      ->group_start()
      ->like('code', $req)
      ->or_like('customer_code', $req)
      ->or_like('customer_name', $req)
      ->or_like('customer_ref', $req)
      ->group_end();
    }

    $rs = $this->db    
    ->order_by('code', 'DESC')
    ->limit(50)
    ->get('order_down_payment');

    if($rs->num_rows() > 0)
    {
      foreach($rs->result() as $rd)
      {
        $name = empty($rd->customer_ref) ? $rd->customer_name : $rd->customer_ref;

        $ds[] = array(
          'label' => $rd->code." | ({$rd->customer_code}) {$name}",
          'code' => $rd->code,
          'name' => $name
        );
      }
    }
    else
    {
      $ds[] = "Not found";
    }

    echo json_encode($ds);
  }


  public function get_down_payment_code()
  {
    $ds = array();

    $req = $_REQUEST['term'];

    $this->db->select('code');

    if($req != '*')
    {
      $this->db->like('code', $this->db->escape_str($req));
    }

    $rs = $this->db->order_by('code', 'ASC')->limit(100)->get('order_down_payment');

    if($rs->num_rows() > 0)
    {
      foreach($rs->result() as $rd)
      {
        $ds[] = $rd->code;
      }
    }
    else
    {
      $ds[] = "Not found";
    }

    echo json_encode($ds);
  }


  public function get_so_code()
  {
    $ds = array();

    $req = $_REQUEST['term'];

    $this->db->select('code')->where('status', 'O');

    if($req != '*')
    {
      $this->db->like('code', $this->db->escape_str($req));
    }

    $rs = $this->db->order_by('code', 'ASC')->limit(50)->get('sale_order');

    if($rs->num_rows() > 0)
    {
      foreach($rs->result() as $rd)
      {
        $ds[] = $rd->code;
      }
    }

    echo json_encode($ds);
  }


	public function get_wx_code()
	{
		$txt = trim($_REQUEST['term']);
		$sc = array();

		$this->db->select('code');
		if($txt != '*')
		{
			$this->db->like('code', $txt);
		}

		$rs = $this->db->order_by('code', 'DESC')->limit(20)->get('consign_check');

		if($rs->num_rows() > 0)
    {
      foreach($rs->result() as $rd)
      {
        $sc[] = $rd->code;
      }
    }
		else
		{
			$sc[] = "not found";
		}

    echo json_encode($sc);
	}


	public function get_active_quotation()
	{
		$txt = $this->ms->escape_str(trim($_REQUEST['term']));

		$sc = array();
		$qr  = "SELECT DocNum, CardCode, CardName ";
		$qr .= "FROM OQUT WHERE DocStatus = 'O' ";

		if($txt != '*')
		{
			$sub  = "DocNum LIKE N'%".$txt."%' ";
			$sub .= "OR CardCode LIKE N'%".$txt."%' ";
			$sub .= "OR CardName LIKE N'%".$txt."%'";

			$qr .= "AND ({$sub})";
		}

		$qr .= "ORDER BY DocNum DESC ";
		$qr .= "OFFSET 0 ROWS FETCH NEXT 50 ROWS ONLY";

		$qs = $this->ms->query($qr);

		if($qs->num_rows() > 0)
		{
			foreach($qs->result() as $rs)
			{
				$sc[] = $rs->DocNum.' | '.$rs->CardCode.' : '.$rs->CardName;
			}
		}
		else
		{
			$sc[] = "not found";
		}

		echo json_encode($sc);
	}


  public function get_sender()
  {
    $txt = $_REQUEST['term'];
    $sc = array();
    $rs = $this->db
    ->select('id, name')
    ->like('name', $txt)
    ->limit(20)
    ->get('address_sender');

    if($rs->num_rows() > 0)
    {
      foreach($rs->result() as $rd)
      {
        $sc[] = $rd->id.' | '.$rd->name;
      }
    }

    echo json_encode($sc);
  }



  public function get_customer_code_and_name()
  {
    $txt = $this->db->escape_str($_REQUEST['term']);

    $sc = array();

    $this->db->select('code, name, Tax_id')->where('CardType', 'C')->where('active', 1);

    if($txt != '*')
    {
      $this->db ->group_start()->like('code', $txt)->or_like('name', $txt)->group_end();
    }

    $rs = $this->db->order_by('code', 'ASC')->limit(20)->get('customers');

    if($rs->num_rows() > 0)
    {
      foreach($rs->result() as $rd)
      {
        $sc[] = array(
          'label' => $rd->code.' | '.$rd->name,
          'code' => $rd->code,
          'name' => $rd->name,
          'tax_id' => $rd->Tax_id
        );
      }
    }

    echo json_encode($sc);
  }

  public function get_invoice_customer()
  {
    $txt = $this->db->escape_str($_REQUEST['term']);
    $ds = array();

    if($txt != '*')
    {
      $this->db->group_start()->like('name', $txt)->or_like('tax_id', $txt)->group_end();
    }

    $rs = $this->db->order_by('tax_id', 'ASC')->limit(50)->get('order_invoice_customer');

    if($rs->num_rows() > 0)
    {
      foreach($rs->result() as $rd)
      {
        $rd->label = $rd->tax_id." | ".$rd->name;

        $ds[] = $rd;
      }
    }

    echo json_encode($ds);
  }


  public function get_invoice_customer_by_tax()
  {
    $txt = $this->db->escape_str($_REQUEST['term']);
    $ds = array();

    if($txt != '*')
    {
      $this->db->group_start()->like('name', $txt)->or_like('tax_id', $txt)->group_end();
    }

    $rs = $this->db->order_by('tax_id', 'ASC')->limit(50)->get('order_invoice_customer');

    if($rs->num_rows() > 0)
    {
      foreach($rs->result() as $rd)
      {
        $rd->label = $rd->tax_id." | ".$rd->name;

        $ds[] = $rd;
      }
    }

    echo json_encode($ds);
  }

  public function get_invoice_customer_by_phone()
  {
    $txt = $this->db->escape_str($_REQUEST['term']);
    $ds = array();

    if($txt != '*')
    {
      $this->db->group_start()->like('name', $txt)->or_like('phone', $txt)->group_end();
    }

    $rs = $this->db->order_by('phone', 'ASC')->limit(50)->get('order_invoice_customer');

    if($rs->num_rows() > 0)
    {
      foreach($rs->result() as $rd)
      {
        $rd->label = $rd->phone." | ".$rd->name;

        $ds[] = $rd;
      }
    }

    echo json_encode($ds);
  }



  public function get_sales_order_customer()
  {
    $txt = $this->db->escape_str($_REQUEST['term']);

    $ds = array();
    $this->db->distinct();
    $this->db->select('customer_ref AS name, customer_address, branch_code, branch_name, address, sub_district, district, province, postcode, phone');
    //$this->db->select('prefix, customer_ref AS name, customer_address AS address, phone');

    if($txt != '*') {
      $this->db->like('customer_ref', $txt);
    }

    $rs = $this->db->order_by('customer_ref', 'ASC')->limit(50)->get('sale_order');

    if($rs->num_rows() > 0)
    {
      foreach($rs->result() as $rd)
      {
        $ds[] = array(
          'name' => $rd->name,
          'customer_address' => $rd->customer_address,
          'branch_code' => $rd->branch_code,
          'branch_name' => $rd->branch_name,
          'address' => $rd->address,
          'sub_district' => $rd->sub_district,
          'district' => $rd->district,
          'province' => $rd->province,
          'postcode' => $rd->postcode,
          'phone' => $rd->phone,
          'label' => $rd->name ." | ".$rd->customer_address
        );
      }
    }

    echo json_encode($ds);
  }


  public function get_sales_order_customer_by_phone()
  {
    $txt = $this->db->escape_str($_REQUEST['term']);

    $ds = array();
    $this->db->distinct();
    $this->db->select('customer_ref AS name, tax_id, branch_code, branch_name, address, sub_district, district, province, postcode, phone');

    if($txt != '*') {
      $this->db->like('phone', $txt);
    }

    $rs = $this->db->order_by('phone', 'ASC')->limit(20)->get('sale_order');

    if($rs->num_rows() > 0)
    {
      foreach($rs->result() as $rd)
      {
        $ds[] = array(
          'name' => $rd->name,
          'tax_id' => $rd->tax_id,
          'branch_code' => $rd->branch_code,
          'branch_name' => $rd->branch_name,
          'address' => $rd->address,
          'sub_district' => $rd->sub_district,
          'district' => $rd->district,
          'province' => $rd->province,
          'postcode' => $rd->postcode,
          'phone' => $rd->phone,
          'label' => $rd->phone .' | '.$rd->name
        );
      }
    }

    echo json_encode($ds);
  }


public function get_style_code()
{
  $sc = array();
  $this->db
  ->select('code, old_code')
  ->where('active', 1)
  ->where('can_sell', 1)
  ->where('is_deleted', 0)
  ->group_start()
  ->like('code', $_REQUEST['term'])
  ->or_like('old_code', $_REQUEST['term'])
  ->group_end()
  ->order_by('code', 'ASC')
  ->limit(50);
  $qs = $this->db->get('product_style');

  if($qs->num_rows() > 0)
  {
    foreach($qs->result() as $rs)
    $sc[] = $rs->code .' | '.$rs->old_code;
  }

	echo json_encode($sc);
}



public function get_style_code_and_name()
{
  $sc = array();
  $this->db
  ->select('code, name')
  ->where('active', 1)
  ->where('can_sell', 1)
  ->where('is_deleted', 0)
  ->group_start()
  ->like('code', $_REQUEST['term'])
  ->or_like('name', $_REQUEST['term'])
  ->or_like('barcode', $_REQUEST['term'])
  ->group_end()
  ->order_by('code', 'ASC')
  ->limit(50);

  $qs = $this->db->get('product_style');

  if($qs->num_rows() > 0)
  {
    foreach($qs->result() as $rs)
    $sc[] = $rs->code .' | '.$rs->name;
  }

	echo json_encode($sc);
}



public function get_prepare_style_code()
{
  $sc = array();
  $this->db
  ->select('code, old_code')
  ->where('active', 1)
  ->where('can_sell', 1)
  ->where('is_deleted', 0)
  ->group_start()
  ->like('code', $_REQUEST['term'])
  ->or_like('old_code', $_REQUEST['term'])
  ->group_end()
  ->order_by('code', 'ASC')
  ->limit(20);
  $qs = $this->db->get('product_style');

  if($qs->num_rows() > 0)
  {
    foreach($qs->result() as $rs)
    $sc[] = $rs->code .' | '.$rs->old_code;
  }

	echo json_encode($sc);
}


public function get_prepare_item_code()
{
  $sc = array();
  $this->db
  ->select('code, old_code')
  ->where('active', 1)
  ->where('can_sell', 1)
  ->where('is_deleted', 0)
  ->group_start()
  ->like('code', $_REQUEST['term'])
  ->or_like('old_code', $_REQUEST['term'])
  ->group_end()
  ->order_by('code', 'ASC')
  ->limit(50);
  $qs = $this->db->get('products');

  if($qs->num_rows() > 0)
  {
    foreach($qs->result() as $rs)
    $sc[] = $rs->code .' | '.$rs->old_code;
  }

	echo json_encode($sc);
}




  public function sub_district()
  {
    $sc = array();
    $adr = $this->db->like('tumbon', $_REQUEST['term'])->limit(20)->get('address_info');
    if($adr->num_rows() > 0)
    {
      foreach($adr->result() as $rs)
      {
        $sc[] = $rs->tumbon.'>>'.$rs->amphur.'>>'.$rs->province.'>>'.$rs->zipcode;
      }
    }

    echo json_encode($sc);
  }


  public function district()
  {
    $sc = array();
    $adr = $this->db->select("amphur, province, zipcode")
    ->like('amphur', $_REQUEST['term'])
    ->group_by('amphur')
    ->group_by('province')
    ->limit(20)->get('address_info');
    if($adr->num_rows() > 0)
    {
      foreach($adr->result() as $rs)
      {
        $sc[] = $rs->amphur.'>>'.$rs->province.'>>'.$rs->zipcode;
      }
    }

    echo json_encode($sc);
  }


	public function province()
  {
    $sc = array();
    $adr = $this->db->select("province")
    ->like('province', $_REQUEST['term'])
    ->group_by('province')
    ->limit(20)->get('address_info');
    if($adr->num_rows() > 0)
    {
      foreach($adr->result() as $rs)
      {
        $sc[] = $rs->province;
      }
    }

    echo json_encode($sc);
  }


	public function postcode()
  {
    $sc = array();
    $adr = $this->db->like('zipcode', $_REQUEST['term'])->limit(20)->get('address_info');
    if($adr->num_rows() > 0)
    {
      foreach($adr->result() as $rs)
      {
        $sc[] = $rs->tumbon.'>>'.$rs->amphur.'>>'.$rs->province.'>>'.$rs->zipcode;
      }
    }

    echo json_encode($sc);
  }




  public function get_vendor_code_and_name()
  {
    $txt = $_REQUEST['term'];

    $sc = array();

    $qr = "SELECT CardCode, CardName FROM OCRD WHERE CardType = 'S' ";

    if($txt != '*')
    {
      $qr .= "AND (CardCode LIKE N'%{$txt}%' OR CardName LIKE N'%{$txt}%') ";
    }

    $qr .= "ORDER BY 1 OFFSET 0 ROWS FETCH NEXT 20 ROWS ONLY";

    $vendor = $this->ms->query($qr);

    if($vendor->num_rows() > 0)
    {
      foreach($vendor->result() as $rs)
      {
        $sc[] = $rs->CardCode.' | '.$rs->CardName;
      }
    }

    echo json_encode($sc);
  }



  //---- ค้นหาใบเบิกสินค้าแปรสภาพ
  //---- $all : TRUE => ทุกสถานะ
  //---- $all : FALSE => เฉพาะที่ยังไม่ปิด
  public function get_transform_code($all = FALSE)
  {
    $txt = $_REQUEST['term'];
    $sc = array();

    if($all === FALSE)
    {
      $this->db->where('is_closed', 0);
    }

    if($txt != '*')
    {
      $this->db->like('order_code', $txt);
    }

    $this->db->limit(20);
    $code = $this->db->get('order_transform');
    if($code->num_rows() > 0)
    {
      foreach($code->result() as $rs)
      {
        $sc[] = $rs->order_code;
      }
    }
    else
    {
      $sc[] = 'ไม่พบข้อมูล';
    }

    echo json_encode($sc);
  }


  //---- ค้นหาใบเบิกสินค้าแปรสภาพ
  public function get_open_transform_and_so_code()
  {
    $txt = $_REQUEST['term'];

    $sc = array();

    $this->db->where('is_closed', 0);

    if($txt != '*')
    {
      $this->db
      ->like('order_code', $txt)
      ->or_like('so_code', $txt);
    }

    $this->db->limit(50);

    $ds = $this->db->get('order_transform');

    if($ds->num_rows() > 0)
    {
      foreach($ds->result() as $rs)
      {
        $sc[] = $rs->order_code.' | '.$rs->so_code;
      }
    }
    else
    {
      $sc[] = 'ไม่พบข้อมูล';
    }

    echo json_encode($sc);
  }


  //---- ค้นหาใบเบิกสินค้าแปรสภาพ
  public function get_open_so_and_transform_code()
  {
    $txt = $_REQUEST['term'];

    $sc = array();

    $this->db
    ->where('is_closed', 0)
    ->where('so_code IS NOT NULL', NULL, FALSE);

    if($txt != '*')
    {
      $this->db
      ->group_start()
      ->like('order_code', $txt)
      ->or_like('so_code', $txt)
      ->group_end();
    }

    $this->db->limit(50);

    $ds = $this->db->get('order_transform');

    if($ds->num_rows() > 0)
    {
      foreach($ds->result() as $rs)
      {
        $sc[] = $rs->so_code.' | '.$rs->order_code;
      }
    }
    else
    {
      $sc[] = 'ไม่พบข้อมูล';
    }

    echo json_encode($sc);
  }


  public function get_po_code($vendor = FALSE)
  {
    $sc = array();
    $txt = convert($_REQUEST['term']);

    //---- receive product if over due date or not
    $receive_due = getConfig('RECEIVE_OVER_DUE'); //--- 1 = receive , 0 = not receive
    $qr = "SELECT DocNum, CardName, Comments FROM OPOR WHERE DocStatus = 'O' ";

    if($vendor !== FALSE)
    {
      $qr .= "AND CardCode = '{$vendor}' ";
    }

    if($txt != '*')
    {
      $qr .= "AND (DocNum LIKE '%{$txt}%' OR Comments LIKE N'%{$txt}%' OR NumAtCard LIKE N'%{$txt}%') ";
    }


    if($receive_due == 0)
    {
      //--- not receive
      $days = getConfig('PO_VALID_DAYS');
      $date = date('Y-m-d',strtotime("-{$days} day")); //--- ย้อนไป $days วัน
      $qr .= "AND DocDueDate >= '".sap_date($date)."' ";
      // $this->ms->where('DocDueDate >=', sap_date($date));
    }
    //echo $this->ms->get_compiled_select('OPOR');
    // $po = $this->ms->get('OPOR');
    $po = $this->ms->query($qr);

    if($po->num_rows() > 0)
    {
      foreach($po->result() as $rs)
      {
        $sc[] = $rs->DocNum." | ". (empty($rs->Comments) ? $rs->CardName : $rs->Comments);
      }
    }
		else
		{
			$sc[] = "not found";
		}

    echo json_encode($sc);
  }



  public function get_request_receive_po_code($vendor = NULL)
  {
    $sc = array();
    $txt = $_REQUEST['term'];

    $this->db
    ->select('code, po_code')
    ->where('status', 1)
    ->where('valid', 0);

    if(!empty($vendor))
    {
      $this->db->where('vendor_code', $vendor);
    }

    if($txt != '*')
    {
      $this->db->like('code', $txt);
    }

    $rq = $this->db->get('receive_product_request');


    if($rq->num_rows() > 0)
    {
      foreach($rq->result() as $rs)
      {
        $sc[] = $rs->code.' | '.$rs->po_code;
      }
    }
    else
    {
      $sc[] = 'not found';
    }

    echo json_encode($sc);
  }



  public function get_valid_lend_code($empID = NULL)
  {
    $sc = array();
    $txt = $_REQUEST['term'];
    $this->db->select('order_code');
    if($txt != '*')
    {
      $this->db->like('order_code', $txt);
    }

    if(!empty($empID))
    {
      $this->db->where('empID', $empID);
    }

    $this->db->where('valid' , 0)->group_by('order_code')->limit(20);
    $rs = $this->db->get('order_lend_detail');
    if($rs->num_rows() > 0)
    {
      foreach($rs->result() as $ds)
      {
        $sc[] = $ds->order_code;
      }
    }

    echo json_encode($sc);
  }


  public function get_zone_code_and_name($warehouse = NULL)
  {
    $sc = array();
    $txt = $_REQUEST['term'];
    $this->db->select('code, name')->where('active', 1);

    if(!empty($warehouse))
    {
      $warehouse = urldecode($warehouse);
      $arr = explode('|', $warehouse);
      $this->db->where_in('warehouse_code', $arr);
    }

    if($txt != '*')
    {
      $this->db
      ->group_start()
      ->like('code', $txt)
      ->or_like('old_code', $txt)
      ->or_like('name', $txt)
      ->group_end();
    }

    $this->db
    ->order_by('warehouse_code', 'ASC')
    ->order_by('code', 'ASC')
    ->limit(20);

    $rs = $this->db->get('zone');

    if($rs->num_rows() > 0)
    {
      foreach($rs->result() as $zone)
      {
        $sc[] = $zone->code.' | '.$zone->name;
      }
    }
    else
    {
      $sc[] = 'ไม่พบรายการ';
    }

    echo json_encode($sc);
  }



	public function get_common_zone_code_and_name($warehouse = NULL)
  {
    $sc = array();
    $txt = $_REQUEST['term'];
    $this->db
		->select('zone.code AS code, zone.name AS name')
		->from('zone')
		->join('warehouse', 'zone.warehouse_code = warehouse.code', 'left')
		->where_in('warehouse.role', array(1, 3, 4, 5))
    ->where('zone.active', 1)
		->where('warehouse.active', 1);

    if(!empty($warehouse))
    {
      $warehouse = urldecode($warehouse);
      $arr = explode('|', $warehouse);
      $this->db->where_in('zone.warehouse_code', $arr);
    }

    if($txt != '*')
    {
      $this->db
      ->group_start()
      ->like('zone.code', $txt)
      ->or_like('zone.old_code', $txt)
      ->or_like('zone.name', $txt)
      ->group_end();
    }

    $this->db
    ->order_by('zone.warehouse_code', 'ASC')
    ->order_by('zone.code', 'ASC')
    ->limit(20);

    $rs = $this->db->get();

    if($rs->num_rows() > 0)
    {
      foreach($rs->result() as $zone)
      {
        $sc[] = $zone->code.' | '.$zone->name;
      }
    }
    else
    {
      $sc[] = 'ไม่พบรายการ';
    }

    echo json_encode($sc);
  }



  public function get_zone_code()
  {
    $sc = array();
    $txt = $_REQUEST['term'];
    $this->db->select('code, name')->where('active', 1);
    if($txt != '*')
    {
      $this->db->group_start();
      $this->db->like('code', $txt)->or_like('old_code', $txt)->or_like('name', $txt);
      $this->db->group_end();
    }

    $rs = $this->db->limit(20)->get('zone');

    if($rs->num_rows() > 0)
    {
      foreach($rs->result() as $cs)
      {
        $sc[] = $cs->code.' | '.(empty($cs->name) ? $cs->code : $cs->name);
      }
    }

    echo json_encode($sc);
  }



  public function get_transform_zone()
  {
    $sc = array();
    $txt = $_REQUEST['term'];
    $this->db
    ->select('zone.code AS code, zone.name AS name')
    ->from('zone')
    ->join('warehouse', 'warehouse.code = zone.warehouse_code', 'left')
    ->where('zone.active', 1)
    ->where('warehouse.role', 7); //--- 7 =  คลังระหว่างทำ ดู table warehouse_role

    if($txt != '*')
    {
      $this->db->group_start();
      $this->db->like('zone.code', $txt);
      $this->db->or_like('zone.name', $txt);
      $this->db->group_end();
    }

    $this->db->limit(20);

    $zone = $this->db->get();

    if($zone->num_rows() > 0)
    {
      foreach($zone->result() as $rs)
      {
        $sc[] = $rs->code.' | '.$rs->name;
      }
    }
    else
    {
      $sc[] = "not found";
    }

    echo json_encode($sc);
  }




  public function get_lend_zone($empID)
  {
    $sc = array();
    if(!empty($empID))
    {
      $txt = $_REQUEST['term'];
      $this->db
      ->select('zone.code AS code, zone.name AS name')
      ->from('zone')
      ->join('warehouse', 'warehouse.code = zone.warehouse_code', 'left')
      ->join('zone_employee', 'zone_employee.zone_code = zone.code')
      ->where('zone.active', 1)
      ->where('warehouse.role', 8) //--- 8 =  คลังยืมสินค้า ดู table warehouse_role
      ->where('zone_employee.empID', $empID);

      if($txt != '*')
      {
        $this->db->like('zone.code', $txt);
        $this->db->or_like('zone.name', $txt);
      }

      $this->db->limit(20);

      $zone = $this->db->get();

      if($zone->num_rows() > 0)
      {
        foreach($zone->result() as $rs)
        {
          $sc[] = $rs->code.' | '.$rs->name;
        }
      }
      else
      {
        $sc[] = "not found";
      }
    }
    else
    {
      $sc[] = "กรุณาระบุผู้ยืม";
    }

    echo json_encode($sc);
  }





  public function get_sponsor()
  {
    $sc = array();
    $txt = convert($_REQUEST['term']);
    $qr = "SELECT BpCode, BpName FROM OOAT ";
    $qr .= "WHERE StartDate <= '".now()."' AND EndDate >= '".now()."' ";
		$qr .= "AND Cancelled = 'N' ";

    if($txt != '*')
    {
      $qr .= "AND (BpCode LIKE N'%{$txt}%' OR BpName LIKE N'%{$txt}%') ";
    }

    $qr .= "ORDER BY 1 OFFSET 0 ROWS FETCH NEXT 20 ROWS ONLY";

    $sponsor = $this->ms->query($qr);

    if($sponsor->num_rows() > 0)
    {
      foreach($sponsor->result() as $rs)
      {
        $sc[] = $rs->BpCode.' | '.$rs->BpName;
      }
    }
    else
    {
      $sc[] = 'ไม่พบรายการ';
    }

    echo json_encode($sc);
  }


  public function get_support()
  {
    $sc = array();
    $txt = trim($_REQUEST['term']);

    $qr = "SELECT BpCode, BpName FROM OOAT ";
		$qr .= "WHERE StartDate <= '".now()."' AND EndDate >= '".now()."' ";

    if($txt != '*')
    {
      $qr .= "AND (BpCode LIKE N'%{$txt}%' OR BpName LIKE N'%{$txt}%') ";
    }

    $qr .= "ORDER BY 1 OFFSET 0 ROWS FETCH NEXT 20 ROWS ONLY";

    $sponsor = $this->ms->query($qr);

    if($sponsor->num_rows() > 0)
    {
      foreach($sponsor->result() as $rs)
      {
        $sc[] = $rs->BpCode.' | '.$rs->BpName;
      }
    }
    else
    {
      $sc[] = 'ไม่พบรายการ';
    }

    echo json_encode($sc);
  }



  public function get_employee()
  {
    $sc = array();
    $txt = $_REQUEST['term'];
    $qr  = "SELECT firstName, lastName, empID FROM OHEM ";
    if($txt != '*')
    {
      $qr .= "WHERE firstName LIKE N'%{$txt}%' OR lastName LIKE N'%{$txt}%' ";
    }

    $qr .= "ORDER BY 1 OFFSET 0 ROWS FETCH NEXT 20 ROWS ONLY";

    $emp = $this->ms->query($qr);

    if($emp->num_rows() > 0)
    {
      foreach($emp->result() as $rs)
      {
        $sc[] = $rs->firstName.' '.$rs->lastName.' | '.$rs->empID;
      }
    }
    else
    {
      $sc[] = 'ไม่พบรายการ';
    }

    echo json_encode($sc);
  }



  public function get_user()
  {
    $sc = array();
    $txt = $_REQUEST['term'];
    $this->db->select('uname, name');
    if($txt != '*')
    {
      $this->db->like('uname', $txt)->or_like('name', $txt);
    }
    $this->db->limit(20);

    $sponsor = $this->db->get('user');

    if($sponsor->num_rows() > 0)
    {
      foreach($sponsor->result() as $rs)
      {
        $sc[] = $rs->uname.' | '.$rs->name;
      }
    }
    else
    {
      $sc[] = 'ไม่พบรายการ';
    }

    echo json_encode($sc);
  }


  public function get_active_user_by_uname()
  {
    $sc = array();
    $txt = $_REQUEST['term'];
    $this->db->select('id, uname, name')->where('active', 1);

    if($txt != '*')
    {
      $this->db->like('uname', $txt);
    }

    $rs = $this->db->limit(20)->get('user');

    if($rs->num_rows() > 0)
    {
      foreach($rs->result() as $ds)
      {
        $arr = array(
          'label' => $ds->uname,
          'id' => $ds->id,
          'uname' => $ds->uname,
          'dname' => $ds->name
        );

        array_push($sc, $arr);
      }
    }

    echo json_encode($sc);
  }


  public function get_consign_zone($customer_code = '')
  {
    if($customer_code == '')
    {
      echo json_encode(array('เลือกลูกค้าก่อน'));
    }
    else
    {
      $this->db
      ->select('zone.code, zone.name')
      ->from('zone_customer')
      ->join('zone', 'zone.code = zone_customer.zone_code', 'left')
      ->join('warehouse', 'zone.warehouse_code = warehouse.code', 'left')
      ->where('warehouse.role', 2) //--- 2 = คลังฝากขาย
      ->where('zone_customer.customer_code', $customer_code)
      ->where('zone.active', 1);

      if($_REQUEST['term'] != '*')
      {
        $this->db->group_start();
        $this->db->like('zone.code', $_REQUEST['term']);
        $this->db->or_like('zone.name', $_REQUEST['term']);
        $this->db->group_end();
      }

      $this->db->limit(20);
      $rs = $this->db->get();

      if($rs->num_rows() > 0)
      {
        $ds = array();
        foreach($rs->result() as $rd)
        {
          $ds[] = $rd->code.' | '.$rd->name;
        }

        echo json_encode($ds);
      }
      else
      {
        echo json_encode(array('ไม่พบโซน'));
      }
    }
  }


  public function getConsignmentZone($warehouse_code = NULL)
  {
    $this->db
    ->select('zone.code, zone.name')
    ->from('zone')
    ->join('warehouse', 'zone.warehouse_code = warehouse.code', 'left')
    ->where('warehouse.role', 2)
    ->where('warehouse.is_consignment', 1)
    ->where('zone.active', 1)
    ->limit(20);

    if($_REQUEST['term'] != '*')
    {
      $this->db->group_start();
      $this->db->like('zone.code', $_REQUEST['term']);
      $this->db->or_like('zone.name', $_REQUEST['term']);
      $this->db->group_end();
    }

    if(!empty($warehouse_code))
    {
      $this->db->where('zone.warehouse_code', $warehouse_code);
    }

    $rs = $this->db->get();

    if($rs->num_rows() > 0)
    {
      $ds = array();
      foreach($rs->result() as $rd)
      {
        $ds[] = $rd->code.' | '.$rd->name;
      }

      echo json_encode($ds);
    }
    else
    {
      echo json_encode(array('ไม่พบโซน'));
    }

  }


  public function get_consignment_zone($customer_code = NULL)
  {
    if(empty($customer_code))
    {
      echo json_encode(array('เลือกลูกค้าก่อน'));
    }
    else
    {
      $this->db
      ->select('zone.code, zone.name')
      ->from('zone_customer')
      ->join('zone', 'zone.code = zone_customer.zone_code', 'left')
      ->join('warehouse', 'zone.warehouse_code = warehouse.code', 'left')
      ->where('warehouse.role', 2) //--- 2 = คลังฝากขาย
      ->where('is_consignment', 1)
      ->where('zone.active', 1)
      ->where('zone_customer.customer_code', $customer_code);

      if($_REQUEST['term'] != '*')
      {
        $this->db->group_start();
        $this->db->like('zone.code', $_REQUEST['term']);
        $this->db->or_like('zone.name', $_REQUEST['term']);
        $this->db->group_end();
      }

      $this->db->limit(20);

      $rs = $this->db->get();

      if($rs->num_rows() > 0)
      {
        $ds = array();
        foreach($rs->result() as $rd)
        {
          $ds[] = $rd->code.' | '.$rd->name;
        }

        echo json_encode($ds);
      }
      else
      {
        echo json_encode(array('ไม่พบโซน'));
      }
    }
  }


  public function get_product_code()
  {
    $sc = array();
    $txt = $_REQUEST['term'];
    $rs = $this->db
    ->select('code, old_code')
    ->where('active', 1)
    ->group_start()
    ->like('code', $txt)
    ->or_like('old_code', $txt)
    ->group_end()
    ->limit(20)
    ->get('products');

    if($rs->num_rows() > 0)
    {
      foreach($rs->result() as $pd)
      {
        $sc[] = $pd->code;
      }
    }


    echo json_encode($sc);
  }


  public function get_product_code_and_name()
  {
    $sc = array();
    $txt = $_REQUEST['term'];
    $rs = $this->db
    ->select('code, old_code, name')
    ->where('active', 1)
    ->group_start()
    ->like('code', $txt)
    ->or_like('name', $txt)
    ->group_end()
    ->limit(100)
    ->get('products');

    if($rs->num_rows() > 0)
    {
      foreach($rs->result() as $pd)
      {
        $sc[] = $pd->code.' | '.$pd->name;
      }
    }
    else
    {
      $sc[] = "not found";
    }


    echo json_encode($sc);
  }


  public function get_item_barcode()
  {
    $sc = array();
    $txt = $_REQUEST['term'];
    $rs = $this->db
    ->select('code, name, barcode')
    ->where('active', 1)
    ->group_start()
    ->like('code', $txt)
    ->or_like('name', $txt)
    ->or_like('barcode', $txt)
    ->group_end()
    ->limit(100)
    ->get('products');

    if($rs->num_rows() > 0)
    {
      foreach($rs->result() as $pd)
      {
        $sc[] = (empty($pd->barcode) ? $pd->code : $pd->barcode).' | '.$pd->code.' | '.$pd->name;
      }
    }
    else
    {
      $sc[] = "not found";
    }


    echo json_encode($sc);
  }




  public function get_item_code()
  {
    $sc = array();
    $txt = $_REQUEST['term'];
    $rs = $this->db
    ->select('code, old_code')
    ->where('active', 1)
    ->group_start()
    ->like('code', $txt)
    ->or_like('old_code', $txt)
    ->group_end()
    ->limit(20)
    ->get('products');

    if($rs->num_rows() > 0)
    {
      foreach($rs->result() as $pd)
      {
        $sc[] = $pd->code .' | '.$pd->old_code;
      }
    }
    else
    {
      $sc[] = 'no item found';
    }

    echo json_encode($sc);
  }


  public function get_warehouse_code_and_name()
  {
    $txt = $_REQUEST['term'];
    $sc  = array();
    $qr  = "SELECT WhsCode, WhsName FROM OWHS ";

    if($txt != '*')
    {
      $qr .= "WHERE WhsCode LIKE N'%{$txt}%' OR WhsName LIKE N'%{$txt}%' ";
    }

    $qr .= "ORDER BY WhsCode ASC OFFSET 0 ROWS FETCH NEXT 20 ROWS ONLY";

    $rs = $this->ms->query($qr);

    if($rs->num_rows() > 0)
    {
      foreach($rs->result() as $wh)
      {
        $sc[] = $wh->WhsCode.' | '.$wh->WhsName;
      }
    }
    else
    {
      $sc[] = 'not found';
    }

    echo json_encode($sc);
  }


  public function get_color_code_and_name()
  {
    $txt = $_REQUEST['term'];
    $sc = array();
    $this->db->select('code, name');
    if($txt != '*')
    {
      $this->db->like('code', $txt);
      $this->db->or_like('name', $txt);
    }
    $rs = $this->db->order_by('code', 'ASC')->limit(20)->get('product_color');

    if($rs->num_rows() > 0)
    {
      foreach($rs->result() as $co)
      {
        $sc[] = $co->code.' | '.$co->name;
      }
    }
    else
    {
      $sc[] = "not_fount";
    }

    echo json_encode($sc);
  }


  public function get_size_code_and_name()
  {
    $txt = $_REQUEST['term'];
    $sc = array();
    $this->db->select('code, name');
    if($txt != '*')
    {
      $this->db->like('code', $txt, 'after');
      $this->db->or_like('name', $txt, 'after');
    }
    $rs = $this->db->order_by('position', 'ASC')->limit(20)->get('product_size');

    if($rs->num_rows() > 0)
    {
      foreach($rs->result() as $co)
      {
        $sc[] = $co->code.' | '.$co->name;
      }
    }
    else
    {
      $sc[] = "not_found";
    }

    echo json_encode($sc);
  }


  public function get_warehouse_by_role($role = 1)
  {
    $txt = $_REQUEST['term'];
    $sc = array();

    $rs = $this->db
    ->select('code, name')
    ->where('role', $role)
    ->group_start()
    ->like('code', $txt)
    ->or_like('name', $txt)
    ->group_end()
    ->order_by('code', 'ASC')
    ->limit(20)
    ->get('warehouse');

    if($rs->num_rows() > 0)
    {
      foreach($rs->result() as $row)
      {
        $sc[] = $row->code .' | '. $row->name;
      }
    }
    else
    {
      $sc[] = 'not found';
    }

    echo json_encode($sc);
  }


  public function get_invoice_code($customer_code = NULL)
	{
		$txt = trim($_REQUEST['term']);
		$ds = array();

		$this->ms
		->select('DocNum, CardCode, CardName')
    ->where('CANCELED', 'N')
    ->where('DocStatus', 'O');

    if( ! empty($customer_code))
    {
      $this->ms->where('CardCode', $customer_code);
    }

		if($txt != '*')
		{
			$this->ms->like('DocNum', $txt);
		}

		$this->ms->order_by('DocNum', 'DESC')->limit(50);

		$iv = $this->ms->get('OINV');

		if($iv->num_rows() > 0)
		{
			foreach($iv->result() as $rs)
			{
        $arr = array(
          'label' => $rs->DocNum.' | '.$rs->CardName,
          'inv_code' => $rs->DocNum,
          'customer_code' => $rs->CardCode,
          'customer_name' => $rs->CardName
        );

				array_push($ds, $arr);
			}
		}
		else
		{
			$ds[] = "not found";
		}

		echo json_encode($ds);
	}

	// public function get_sap_invoice_code($customer_code = NULL)
	// {
	// 	$txt = trim($_REQUEST['term']);
	// 	$sc = array();
  //
	// 	$this->ms
	// 	->select('DocNum, U_ECOMNO')
	// 	->where('CardCode', $customer_code)
	// 	->where('CANCELED', 'N')
	// 	->where('DocStatus', 'O');
  //
	// 	if($txt != '*')
	// 	{
	// 		$this->ms->like('DocNum', $txt);
	// 	}
  //
	// 	$this->ms->order_by('DocNum', 'DESC')->limit(20);
	// 	$rs = $this->ms->get('OINV');
  //
	// 	if($rs->num_rows() > 0)
	// 	{
	// 		foreach($rs->result() as $row)
	// 		{
	// 			$sc[] = $row->DocNum .' | '.$row->U_ECOMNO;
	// 		}
	// 	}
	// 	else
	// 	{
	// 		$sc[] = "not found";
	// 	}
  //
	// 	echo json_encode($sc);
	// }



} //-- end class
?>
