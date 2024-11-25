<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Receive_details extends PS_Controller
{
  public $menu_code = 'RARCDT';
	public $menu_group_code = 'RE';
  public $menu_sub_group_code = 'REAUDIT';
	public $title = 'รายงานสถานะเอกสารขาเข้า แสดงรายละเอียด';
  public $filter;
  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'report/audit/receive_details';
    $this->load->model('report/audit/receive_details_model');
    $this->load->model('masters/warehouse_model');
    $this->load->model('masters/customers_model');
  }

  public function index()
  {
    $ds = array(
      'warehouse_list' => $this->warehouse_model->get_all_warehouse_list()
    );

    $this->load->view('report/audit/report_receive_details', $ds);
  }

  private function warehouse_array()
  {
    $ds = array();
    $rs = $this->db->get('warehouse');

    if($rs->num_rows() > 0)
    {
      foreach($rs->result() as $rd)
      {
        $ds[$rd->code] = $rd->name;
      }
    }

    return $ds;
  }

  private function state_array()
  {
    $ds = array(
      '0' => 'ดราฟท์',
      '1' => 'สำเร็จ',
      '2' => 'ยกเลิก',
      '3' => 'รอรับสินค้า',
      '4' => 'รอยืนยันรับ'
    );

    return $ds;
  }

  public function get_report()
  {
    $sc = TRUE;

    if($this->input->get("json"))
    {
      $json = json_decode($this->input->get("json"));

      if( ! empty($json))
      {
        $role = $json->role;
        $all = $json->allRole ? TRUE : FALSE;
        $result = array();

        $state = $this->state_array();
        $wh = $this->warehouse_array();

        $filter = array(
          "from_date" => $json->fromDate,
          "to_date" => $json->toDate,
          "is_expired" => $json->isExpired,
          "all_state" => $json->allState,
          "state" => $json->state,
          "all_warehouse" => $json->allWarehouse,
          "warehouse" => $json->warehouse
        );

        $limit = 1000;
        $no = 1;

        if( ! empty($role->WR) OR $all)
        {
          $details = $this->receive_details_model->WR($filter);

          if( ! empty($details))
          {
            foreach($details as $rs)
            {
              $arr = array(
                'no' => number($no),
                'code' => $rs->code,
                'customer_code' => $rs->vendor_code,
                'customer_name' => $rs->vendor_name,
                'reference' => $rs->po_code,
                'expired' => $rs->is_expire == 1 ? 'Y' : 'N',
                'date_add' => thai_date($rs->date_add, FALSE, '/'),
                'date_upd' => thai_date($rs->date_upd, FALSE, '/'),
                'total_amount' => number($this->receive_details_model->get_doc_total($rs->code, 'WR'), 2),
                'warehouse_name' => empty($wh[$rs->warehouse_code]) ? NULL : $wh[$rs->warehouse_code],
                'state_name' => $state[$rs->status],
                'uname' => $rs->user,
                'emp_name' => $this->user_model->get_name($rs->user),
                'cancel_reason' => $rs->status == 2 ? $rs->cancle_reason : NULL
              );

              array_push($result, $arr);
              $no++;
            }
          }
        }


        if( ! empty($role->RT) OR $all)
        {
          $details = $this->receive_details_model->RT($filter);

          if( ! empty($details))
          {
            foreach($details as $rs)
            {
              $arr = array(
                'no' => number($no),
                'code' => $rs->code,
                'customer_code' => $rs->customer_code,
                'customer_name' => $rs->customer_name,
                'reference' => $rs->order_code,
                'expired' => $rs->is_expire == 1 ? 'Y' : 'N',
                'date_add' => thai_date($rs->date_add, FALSE, '/'),
                'date_upd' => thai_date($rs->date_upd, FALSE, '/'),
                'total_amount' => number($this->receive_details_model->get_doc_total($rs->code, 'RT'), 2),
                'warehouse_name' => empty($wh[$rs->warehouse_code]) ? NULL : $wh[$rs->warehouse_code],
                'state_name' => $state[$rs->status],
                'uname' => $rs->user,
                'emp_name' => $this->user_model->get_name($rs->user),
                'cancel_reason' => $rs->status == 2 ? $rs->cancle_reason : NULL
              );

              array_push($result, $arr);
              $no++;
            }
          }
        }

        if( ! empty($role->RN) OR $all)
        {
          $details = $this->receive_details_model->RN($filter);

          if( ! empty($details))
          {
            foreach($details as $rs)
            {
              $arr = array(
                'no' => number($no),
                'code' => $rs->code,
                'customer_code' => $rs->empID,
                'customer_name' => $rs->empName,
                'reference' => $rs->lend_code,
                'expired' => $rs->is_expire == 1 ? 'Y' : 'N',
                'date_add' => thai_date($rs->date_add, FALSE, '/'),
                'date_upd' => thai_date($rs->date_upd, FALSE, '/'),
                'total_amount' => number($this->receive_details_model->get_doc_total($rs->code, 'RN'), 2),
                'warehouse_name' => empty($wh[$rs->to_warehouse]) ? NULL : $wh[$rs->to_warehouse],
                'state_name' => $state[$rs->status],
                'uname' => $rs->user,
                'emp_name' => $this->user_model->get_name($rs->user),
                'cancel_reason' => $rs->status == 2 ? $rs->cancle_reason : NULL
              );

              array_push($result, $arr);
              $no++;
            }
          }
        }

        if( ! empty($role->SM) OR $all)
        {
          $details = $this->receive_details_model->SM($filter);

          if( ! empty($details))
          {
            $custs = [];

            foreach($details as $rs)
            {
              $customer_name = empty($custs[$rs->customer_code]) ? $this->customers_model->get_name($rs->customer_code) : $custs[$rs->customer_code];
              $custs[$rs->customer_code] = $customer_name;

              $arr = array(
                'no' => number($no),
                'code' => $rs->code,
                'customer_code' => $rs->customer_code,
                'customer_name' => $customer_name,
                'reference' => $rs->invoice,
                'expired' => $rs->is_expire == 1 ? 'Y' : 'N',
                'date_add' => thai_date($rs->date_add, FALSE, '/'),
                'date_upd' => thai_date($rs->date_upd, FALSE, '/'),
                'total_amount' => number($this->receive_details_model->get_doc_total($rs->code, 'SM'), 2),
                'warehouse_name' => empty($wh[$rs->warehouse_code]) ? NULL : $wh[$rs->warehouse_code],
                'state_name' => $state[$rs->status],
                'uname' => $rs->user,
                'emp_name' => $this->user_model->get_name($rs->user),
                'cancel_reason' => $rs->status == 2 ? $rs->cancle_reason : NULL
              );

              array_push($result, $arr);
              $no++;
            }
          }
        }

        if( ! empty($role->CN) OR $all)
        {
          $details = $this->receive_details_model->CN($filter);

          if( ! empty($details))
          {
            $custs = [];

            foreach($details as $rs)
            {
              $customer_name = empty($custs[$rs->customer_code]) ? $this->customers_model->get_name($rs->customer_code) : $custs[$rs->customer_code];
              $custs[$rs->customer_code] = $customer_name;

              $arr = array(
                'no' => number($no),
                'code' => $rs->code,
                'customer_code' => $rs->customer_code,
                'customer_name' => $customer_name,
                'reference' => $rs->invoice,
                'expired' => 'N',
                'date_add' => thai_date($rs->date_add, FALSE, '/'),
                'date_upd' => thai_date($rs->date_upd, FALSE, '/'),
                'total_amount' => number($this->receive_details_model->get_doc_total($rs->code, 'CN'), 2),
                'warehouse_name' => empty($wh[$rs->warehouse_code]) ? NULL : $wh[$rs->warehouse_code],
                'state_name' => $state[$rs->status],
                'uname' => $rs->user,
                'emp_name' => $this->user_model->get_name($rs->user),
                'cancel_reason' => $rs->status == 2 ? $rs->cancle_reason : NULL
              );

              array_push($result, $arr);
              $no++;
            }
          }
        }
      }
      else
      {
        $sc = FALSE;
        $this->error = "Invalid request data format";
      }
    }

    $arr = array(
      'status' => $sc === TRUE ? 'success' : 'failed',
      'message' => $sc === TRUE ? 'success' : $this->error,
      'result' => $sc === TRUE ? $result : NULL
    );

    echo json_encode($arr);
  }



  public function do_export()
  {
    $token = $this->input->post('token');

    //--- load excel library
    $this->load->library('excel');



    if($this->input->post('fromDate'))
    {
      $state = $this->state_array();
      $wh = $this->warehouse_array();

      $role = $this->input->post('role');
      $all = $this->input->post('allRole') == 1 ? TRUE : FALSE;


      $allRole = $this->input->post('allRole');
      $allWarehouse = $this->input->post('allWarehouse');
      $allState = $this->input->post('allState');
      $is_expired = $this->input->post('is_expired');
      $fromDate = $this->input->post('fromDate');
      $toDate = $this->input->post('toDate');
      $warehouse = $this->input->post('warehouse');
      $status = $this->input->post('state');

      $filter = array(
        "from_date" => $fromDate,
        "to_date" => $toDate,
        "all_role" => $allRole,
        "is_expired" => $is_expired,
        "all_state" => $allState,
        "state" => $status,
        "all_warehouse" => $allWarehouse,
        "warehouse" => $warehouse
      );

      $index = 0;

      if(! empty($role['WR']) OR $all)
  		{
  			$worksheet = new PHPExcel_Worksheet($this->excel, "WR");
  			$this->excel->addSheet($worksheet, $index);
  			$this->excel->setActiveSheetIndex($index);
  			$this->excel->getActiveSheet()->setTitle('WR');

  			$index++;

        //---  Report title
        $report_title = "รายงานสถานะเอกสารขาเข้า แสดงรายละเอียด";
        $role_title = "เอกสาร : รับสินค้าจากการซื้อ";
        $expire_title = "อายุเอกสาร : ".($is_expired == 'all' ? 'ทั้งหมด' : ($is_expired == 1 ? 'เฉพาะที่หมดอายุ' : 'เฉพาะที่ไม่หมดอายุ'));
        $wh_title     = 'คลัง :  '. ($allWarehouse == 1 ? 'ทั้งหมด' : $this->get_title($warehouse));
        $state_title = "สถานะ : ".($allState == 1 ? "ทั้งหมด" : $this->get_state_title($status));
        //--- set report title header
        $this->excel->getActiveSheet()->setCellValue('A1', $report_title);
        $this->excel->getActiveSheet()->setCellValue('A2', $role_title);
        $this->excel->getActiveSheet()->setCellValue('A3', $expire_title);
        $this->excel->getActiveSheet()->setCellValue('A4', $state_title);
        $this->excel->getActiveSheet()->setCellValue('A5', $wh_title);
        $this->excel->getActiveSheet()->setCellValue('A6', 'วันที่เอกสาร : ('.thai_date($fromDate,'/') .') - ('.thai_date($toDate,'/').')');

        //--- set Table header
        $row = 7;

        $this->excel->getActiveSheet()->setCellValue("A{$row}", 'ลำดับ');
        $this->excel->getActiveSheet()->setCellValue("B{$row}", 'วันที่');
        $this->excel->getActiveSheet()->setCellValue("C{$row}", 'เลขที่');
        $this->excel->getActiveSheet()->setCellValue("D{$row}", 'อ้างอิง');
        $this->excel->getActiveSheet()->setCellValue("E{$row}", 'มูลค่า');
        $this->excel->getActiveSheet()->setCellValue("F{$row}", 'รหัสลูกค้า');
        $this->excel->getActiveSheet()->setCellValue("G{$row}", 'ชื่อลูกค้า');
        $this->excel->getActiveSheet()->setCellValue("H{$row}", 'สถานะ');
        $this->excel->getActiveSheet()->setCellValue("I{$row}", 'หมดอายุ');
        $this->excel->getActiveSheet()->setCellValue("J{$row}", 'วันที่แก้ไข');
        $this->excel->getActiveSheet()->setCellValue("K{$row}", 'รหัสคลัง');
        $this->excel->getActiveSheet()->setCellValue("L{$row}", 'ชื่อคลัง');
        $this->excel->getActiveSheet()->setCellValue("M{$row}", 'Username');
        $this->excel->getActiveSheet()->setCellValue("N{$row}", 'พนักงาน');
        $this->excel->getActiveSheet()->setCellValue("O{$row}", 'cancel reason');
        $row++;

        //---- กำหนดความกว้างของคอลัมภ์
        $this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(15);
        $this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
        $this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
        $this->excel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
        $this->excel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
        $this->excel->getActiveSheet()->getColumnDimension('G')->setWidth(90);
        $this->excel->getActiveSheet()->getColumnDimension('H')->setWidth(15);
        $this->excel->getActiveSheet()->getColumnDimension('I')->setWidth(10);
        $this->excel->getActiveSheet()->getColumnDimension('J')->setWidth(15);
        $this->excel->getActiveSheet()->getColumnDimension('K')->setWidth(15);
        $this->excel->getActiveSheet()->getColumnDimension('L')->setWidth(40);
        $this->excel->getActiveSheet()->getColumnDimension('M')->setWidth(15);
        $this->excel->getActiveSheet()->getColumnDimension('N')->setWidth(15);
        $this->excel->getActiveSheet()->getColumnDimension('O')->setWidth(40);

        $details = $this->receive_details_model->WR($filter);

        if( ! empty($details))
        {
          $no = 1;
          foreach($details as $rs)
          {
            $ay		= date('Y', strtotime($rs->date_add));
            $am		= date('m', strtotime($rs->date_add));
            $ad		= date('d', strtotime($rs->date_add));
            $date_add = PHPExcel_Shared_Date::FormattedPHPToExcel($ay, $am, $ad);

            $uy		= date('Y', strtotime($rs->date_upd));
            $um		= date('m', strtotime($rs->date_upd));
            $ud		= date('d', strtotime($rs->date_upd));
            $date_upd = PHPExcel_Shared_Date::FormattedPHPToExcel($uy, $um, $ud);

            $amount = $this->receive_details_model->get_doc_total($rs->code, 'WR');

            $this->excel->getActiveSheet()->setCellValue("A{$row}", $no);
            $this->excel->getActiveSheet()->setCellValue("B{$row}", $date_add);
            $this->excel->getActiveSheet()->setCellValue("C{$row}", $rs->code);
            $this->excel->getActiveSheet()->setCellValue("D{$row}", $rs->po_code);
            $this->excel->getActiveSheet()->setCellValue("E{$row}", $amount);
            $this->excel->getActiveSheet()->setCellValue("F{$row}", $rs->vendor_code);
            $this->excel->getActiveSheet()->setCellValue("G{$row}", $rs->vendor_name);
            $this->excel->getActiveSheet()->setCellValue("H{$row}", $state[$rs->status]);
            $this->excel->getActiveSheet()->setCellValue("I{$row}", $rs->is_expire == 1 ? 'Y' : 'N');
            $this->excel->getActiveSheet()->setCellValue("J{$row}", $date_upd);
            $this->excel->getActiveSheet()->setCellValue("K{$row}", $rs->warehouse_code);
            $this->excel->getActiveSheet()->setCellValue("L{$row}", empty($wh[$rs->warehouse_code]) ? NULL : $wh[$rs->warehouse_code]);
            $this->excel->getActiveSheet()->setCellValue("M{$row}", $rs->user);
            $this->excel->getActiveSheet()->setCellValue("N{$row}", $this->user_model->get_name($rs->user));
            if($rs->status == 2)
            {
              $this->excel->getActiveSheet()->setCellValue("O{$row}", $rs->cancle_reason);
            }

            $no++;
            $row++;
          }

          $this->excel->getActiveSheet()->getStyle("B10:B{$row}")->getNumberFormat()->setFormatCode('dd/mm/yyyy');
          $this->excel->getActiveSheet()->getStyle("J10:J{$row}")->getNumberFormat()->setFormatCode('dd/mm/yyyy');
          $this->excel->getActiveSheet()->getStyle("E10:E{$row}")->getNumberFormat()->setFormatCode('#,##0.00');
        }
      } //--- endif WR

      if(! empty($role['RT']) OR $all)
  		{
  			$worksheet = new PHPExcel_Worksheet($this->excel, "RT");
  			$this->excel->addSheet($worksheet, $index);
  			$this->excel->setActiveSheetIndex($index);
  			$this->excel->getActiveSheet()->setTitle('RT');

  			$index++;

        //---  Report title
        $report_title = "รายงานสถานะเอกสารขาเข้า แสดงรายละเอียด";
        $role_title = "เอกสาร : รับสินค้าจากการแปรสภาพ";
        $expire_title = "อายุเอกสาร : ".($is_expired == 'all' ? 'ทั้งหมด' : ($is_expired == 1 ? 'เฉพาะที่หมดอายุ' : 'เฉพาะที่ไม่หมดอายุ'));
        $wh_title     = 'คลัง :  '. ($allWarehouse == 1 ? 'ทั้งหมด' : $this->get_title($warehouse));
        $state_title = "สถานะ : ".($allState == 1 ? "ทั้งหมด" : $this->get_state_title($status));
        //--- set report title header
        $this->excel->getActiveSheet()->setCellValue('A1', $report_title);
        $this->excel->getActiveSheet()->setCellValue('A2', $role_title);
        $this->excel->getActiveSheet()->setCellValue('A3', $expire_title);
        $this->excel->getActiveSheet()->setCellValue('A4', $state_title);
        $this->excel->getActiveSheet()->setCellValue('A5', $wh_title);
        $this->excel->getActiveSheet()->setCellValue('A6', 'วันที่เอกสาร : ('.thai_date($fromDate,'/') .') - ('.thai_date($toDate,'/').')');

        //--- set Table header
        $row = 7;

        $this->excel->getActiveSheet()->setCellValue("A{$row}", 'ลำดับ');
        $this->excel->getActiveSheet()->setCellValue("B{$row}", 'วันที่');
        $this->excel->getActiveSheet()->setCellValue("C{$row}", 'เลขที่');
        $this->excel->getActiveSheet()->setCellValue("D{$row}", 'อ้างอิง');
        $this->excel->getActiveSheet()->setCellValue("E{$row}", 'มูลค่า');
        $this->excel->getActiveSheet()->setCellValue("F{$row}", 'รหัสลูกค้า');
        $this->excel->getActiveSheet()->setCellValue("G{$row}", 'ชื่อลูกค้า');
        $this->excel->getActiveSheet()->setCellValue("H{$row}", 'สถานะ');
        $this->excel->getActiveSheet()->setCellValue("I{$row}", 'หมดอายุ');
        $this->excel->getActiveSheet()->setCellValue("J{$row}", 'วันที่แก้ไข');
        $this->excel->getActiveSheet()->setCellValue("K{$row}", 'รหัสคลัง');
        $this->excel->getActiveSheet()->setCellValue("L{$row}", 'ชื่อคลัง');
        $this->excel->getActiveSheet()->setCellValue("M{$row}", 'Username');
        $this->excel->getActiveSheet()->setCellValue("N{$row}", 'พนักงาน');
        $this->excel->getActiveSheet()->setCellValue("O{$row}", 'cancel reason');
        $row++;

        //---- กำหนดความกว้างของคอลัมภ์
        $this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(15);
        $this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
        $this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
        $this->excel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
        $this->excel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
        $this->excel->getActiveSheet()->getColumnDimension('G')->setWidth(90);
        $this->excel->getActiveSheet()->getColumnDimension('H')->setWidth(15);
        $this->excel->getActiveSheet()->getColumnDimension('I')->setWidth(10);
        $this->excel->getActiveSheet()->getColumnDimension('J')->setWidth(15);
        $this->excel->getActiveSheet()->getColumnDimension('K')->setWidth(15);
        $this->excel->getActiveSheet()->getColumnDimension('L')->setWidth(40);
        $this->excel->getActiveSheet()->getColumnDimension('M')->setWidth(15);
        $this->excel->getActiveSheet()->getColumnDimension('N')->setWidth(15);
        $this->excel->getActiveSheet()->getColumnDimension('O')->setWidth(40);

        $details = $this->receive_details_model->RT($filter);

        if( ! empty($details))
        {
          $no = 1;
          foreach($details as $rs)
          {
            $ay		= date('Y', strtotime($rs->date_add));
            $am		= date('m', strtotime($rs->date_add));
            $ad		= date('d', strtotime($rs->date_add));
            $date_add = PHPExcel_Shared_Date::FormattedPHPToExcel($ay, $am, $ad);

            $uy		= date('Y', strtotime($rs->date_upd));
            $um		= date('m', strtotime($rs->date_upd));
            $ud		= date('d', strtotime($rs->date_upd));
            $date_upd = PHPExcel_Shared_Date::FormattedPHPToExcel($uy, $um, $ud);

            $amount = $this->receive_details_model->get_doc_total($rs->code, 'RT');

            $this->excel->getActiveSheet()->setCellValue("A{$row}", $no);
            $this->excel->getActiveSheet()->setCellValue("B{$row}", $date_add);
            $this->excel->getActiveSheet()->setCellValue("C{$row}", $rs->code);
            $this->excel->getActiveSheet()->setCellValue("D{$row}", $rs->order_code);
            $this->excel->getActiveSheet()->setCellValue("E{$row}", $amount);
            $this->excel->getActiveSheet()->setCellValue("F{$row}", $rs->customer_code);
            $this->excel->getActiveSheet()->setCellValue("G{$row}", $rs->customer_name);
            $this->excel->getActiveSheet()->setCellValue("H{$row}", $state[$rs->status]);
            $this->excel->getActiveSheet()->setCellValue("I{$row}", $rs->is_expire == 1 ? 'Y' : 'N');
            $this->excel->getActiveSheet()->setCellValue("J{$row}", $date_upd);
            $this->excel->getActiveSheet()->setCellValue("K{$row}", $rs->warehouse_code);
            $this->excel->getActiveSheet()->setCellValue("L{$row}", empty($wh[$rs->warehouse_code]) ? NULL : $wh[$rs->warehouse_code]);
            $this->excel->getActiveSheet()->setCellValue("M{$row}", $rs->user);
            $this->excel->getActiveSheet()->setCellValue("N{$row}", $this->user_model->get_name($rs->user));
            if($rs->status == 2)
            {
              $this->excel->getActiveSheet()->setCellValue("O{$row}", $rs->cancle_reason);
            }

            $no++;
            $row++;
          }

          $this->excel->getActiveSheet()->getStyle("B10:B{$row}")->getNumberFormat()->setFormatCode('dd/mm/yyyy');
          $this->excel->getActiveSheet()->getStyle("J10:J{$row}")->getNumberFormat()->setFormatCode('dd/mm/yyyy');
          $this->excel->getActiveSheet()->getStyle("E10:E{$row}")->getNumberFormat()->setFormatCode('#,##0.00');
        }
      } //--- endif RT


      if(! empty($role['RN']) OR $all)
  		{
  			$worksheet = new PHPExcel_Worksheet($this->excel, "RN");
  			$this->excel->addSheet($worksheet, $index);
  			$this->excel->setActiveSheetIndex($index);
  			$this->excel->getActiveSheet()->setTitle('RN');

  			$index++;

        //---  Report title
        $report_title = "รายงานสถานะเอกสารขาเข้า แสดงรายละเอียด";
        $role_title = "เอกสาร : คืนสินค้าจากการยืม";
        $expire_title = "อายุเอกสาร : ".($is_expired == 'all' ? 'ทั้งหมด' : ($is_expired == 1 ? 'เฉพาะที่หมดอายุ' : 'เฉพาะที่ไม่หมดอายุ'));
        $wh_title     = 'คลัง :  '. ($allWarehouse == 1 ? 'ทั้งหมด' : $this->get_title($warehouse));
        $state_title = "สถานะ : ".($allState == 1 ? "ทั้งหมด" : $this->get_state_title($status));
        //--- set report title header
        $this->excel->getActiveSheet()->setCellValue('A1', $report_title);
        $this->excel->getActiveSheet()->setCellValue('A2', $role_title);
        $this->excel->getActiveSheet()->setCellValue('A3', $expire_title);
        $this->excel->getActiveSheet()->setCellValue('A4', $state_title);
        $this->excel->getActiveSheet()->setCellValue('A5', $wh_title);
        $this->excel->getActiveSheet()->setCellValue('A6', 'วันที่เอกสาร : ('.thai_date($fromDate,'/') .') - ('.thai_date($toDate,'/').')');

        //--- set Table header
        $row = 7;

        $this->excel->getActiveSheet()->setCellValue("A{$row}", 'ลำดับ');
        $this->excel->getActiveSheet()->setCellValue("B{$row}", 'วันที่');
        $this->excel->getActiveSheet()->setCellValue("C{$row}", 'เลขที่');
        $this->excel->getActiveSheet()->setCellValue("D{$row}", 'อ้างอิง');
        $this->excel->getActiveSheet()->setCellValue("E{$row}", 'มูลค่า');
        $this->excel->getActiveSheet()->setCellValue("F{$row}", 'รหัสลูกค้า');
        $this->excel->getActiveSheet()->setCellValue("G{$row}", 'ชื่อลูกค้า');
        $this->excel->getActiveSheet()->setCellValue("H{$row}", 'สถานะ');
        $this->excel->getActiveSheet()->setCellValue("I{$row}", 'หมดอายุ');
        $this->excel->getActiveSheet()->setCellValue("J{$row}", 'วันที่แก้ไข');
        $this->excel->getActiveSheet()->setCellValue("K{$row}", 'รหัสคลัง');
        $this->excel->getActiveSheet()->setCellValue("L{$row}", 'ชื่อคลัง');
        $this->excel->getActiveSheet()->setCellValue("M{$row}", 'Username');
        $this->excel->getActiveSheet()->setCellValue("N{$row}", 'พนักงาน');
        $this->excel->getActiveSheet()->setCellValue("O{$row}", 'cancel reason');
        $row++;

        //---- กำหนดความกว้างของคอลัมภ์
        $this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(15);
        $this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
        $this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
        $this->excel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
        $this->excel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
        $this->excel->getActiveSheet()->getColumnDimension('G')->setWidth(90);
        $this->excel->getActiveSheet()->getColumnDimension('H')->setWidth(15);
        $this->excel->getActiveSheet()->getColumnDimension('I')->setWidth(10);
        $this->excel->getActiveSheet()->getColumnDimension('J')->setWidth(15);
        $this->excel->getActiveSheet()->getColumnDimension('K')->setWidth(15);
        $this->excel->getActiveSheet()->getColumnDimension('L')->setWidth(40);
        $this->excel->getActiveSheet()->getColumnDimension('M')->setWidth(15);
        $this->excel->getActiveSheet()->getColumnDimension('N')->setWidth(15);
        $this->excel->getActiveSheet()->getColumnDimension('O')->setWidth(40);

        $details = $this->receive_details_model->RN($filter);

        if( ! empty($details))
        {
          $no = 1;
          foreach($details as $rs)
          {
            $ay		= date('Y', strtotime($rs->date_add));
            $am		= date('m', strtotime($rs->date_add));
            $ad		= date('d', strtotime($rs->date_add));
            $date_add = PHPExcel_Shared_Date::FormattedPHPToExcel($ay, $am, $ad);

            $uy		= date('Y', strtotime($rs->date_upd));
            $um		= date('m', strtotime($rs->date_upd));
            $ud		= date('d', strtotime($rs->date_upd));
            $date_upd = PHPExcel_Shared_Date::FormattedPHPToExcel($uy, $um, $ud);

            $amount = $this->receive_details_model->get_doc_total($rs->code, 'RN');

            $this->excel->getActiveSheet()->setCellValue("A{$row}", $no);
            $this->excel->getActiveSheet()->setCellValue("B{$row}", $date_add);
            $this->excel->getActiveSheet()->setCellValue("C{$row}", $rs->code);
            $this->excel->getActiveSheet()->setCellValue("D{$row}", $rs->lend_code);
            $this->excel->getActiveSheet()->setCellValue("E{$row}", $amount);
            $this->excel->getActiveSheet()->setCellValue("F{$row}", $rs->empID);
            $this->excel->getActiveSheet()->setCellValue("G{$row}", $rs->empName);
            $this->excel->getActiveSheet()->setCellValue("H{$row}", $state[$rs->status]);
            $this->excel->getActiveSheet()->setCellValue("I{$row}", $rs->is_expire == 1 ? 'Y' : 'N');
            $this->excel->getActiveSheet()->setCellValue("J{$row}", $date_upd);
            $this->excel->getActiveSheet()->setCellValue("K{$row}", $rs->to_warehouse);
            $this->excel->getActiveSheet()->setCellValue("L{$row}", empty($wh[$rs->to_warehouse]) ? NULL : $wh[$rs->to_warehouse]);
            $this->excel->getActiveSheet()->setCellValue("M{$row}", $rs->user);
            $this->excel->getActiveSheet()->setCellValue("N{$row}", $this->user_model->get_name($rs->user));
            if($rs->status == 2)
            {
              $this->excel->getActiveSheet()->setCellValue("O{$row}", $rs->cancle_reason);
            }

            $no++;
            $row++;
          }

          $this->excel->getActiveSheet()->getStyle("B10:B{$row}")->getNumberFormat()->setFormatCode('dd/mm/yyyy');
          $this->excel->getActiveSheet()->getStyle("J10:J{$row}")->getNumberFormat()->setFormatCode('dd/mm/yyyy');
          $this->excel->getActiveSheet()->getStyle("E10:E{$row}")->getNumberFormat()->setFormatCode('#,##0.00');
        }
      } //--- end if RN


      if(! empty($role['SM']) OR $all)
  		{
  			$worksheet = new PHPExcel_Worksheet($this->excel, "SM");
  			$this->excel->addSheet($worksheet, $index);
  			$this->excel->setActiveSheetIndex($index);
  			$this->excel->getActiveSheet()->setTitle('SM');

  			$index++;

        //---  Report title
        $report_title = "รายงานสถานะเอกสารขาเข้า แสดงรายละเอียด";
        $role_title = "เอกสาร : รับสินค้าจากการแปรสภาพ";
        $expire_title = "อายุเอกสาร : ".($is_expired == 'all' ? 'ทั้งหมด' : ($is_expired == 1 ? 'เฉพาะที่หมดอายุ' : 'เฉพาะที่ไม่หมดอายุ'));
        $wh_title     = 'คลัง :  '. ($allWarehouse == 1 ? 'ทั้งหมด' : $this->get_title($warehouse));
        $state_title = "สถานะ : ".($allState == 1 ? "ทั้งหมด" : $this->get_state_title($status));
        //--- set report title header
        $this->excel->getActiveSheet()->setCellValue('A1', $report_title);
        $this->excel->getActiveSheet()->setCellValue('A2', $role_title);
        $this->excel->getActiveSheet()->setCellValue('A3', $expire_title);
        $this->excel->getActiveSheet()->setCellValue('A4', $state_title);
        $this->excel->getActiveSheet()->setCellValue('A5', $wh_title);
        $this->excel->getActiveSheet()->setCellValue('A6', 'วันที่เอกสาร : ('.thai_date($fromDate,'/') .') - ('.thai_date($toDate,'/').')');

        //--- set Table header
        $row = 7;

        $this->excel->getActiveSheet()->setCellValue("A{$row}", 'ลำดับ');
        $this->excel->getActiveSheet()->setCellValue("B{$row}", 'วันที่');
        $this->excel->getActiveSheet()->setCellValue("C{$row}", 'เลขที่');
        $this->excel->getActiveSheet()->setCellValue("D{$row}", 'อ้างอิง');
        $this->excel->getActiveSheet()->setCellValue("E{$row}", 'มูลค่า');
        $this->excel->getActiveSheet()->setCellValue("F{$row}", 'รหัสลูกค้า');
        $this->excel->getActiveSheet()->setCellValue("G{$row}", 'ชื่อลูกค้า');
        $this->excel->getActiveSheet()->setCellValue("H{$row}", 'สถานะ');
        $this->excel->getActiveSheet()->setCellValue("I{$row}", 'หมดอายุ');
        $this->excel->getActiveSheet()->setCellValue("J{$row}", 'วันที่แก้ไข');
        $this->excel->getActiveSheet()->setCellValue("K{$row}", 'รหัสคลัง');
        $this->excel->getActiveSheet()->setCellValue("L{$row}", 'ชื่อคลัง');
        $this->excel->getActiveSheet()->setCellValue("M{$row}", 'Username');
        $this->excel->getActiveSheet()->setCellValue("N{$row}", 'พนักงาน');
        $this->excel->getActiveSheet()->setCellValue("O{$row}", 'cancel reason');
        $row++;

        //---- กำหนดความกว้างของคอลัมภ์
        $this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(15);
        $this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
        $this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
        $this->excel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
        $this->excel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
        $this->excel->getActiveSheet()->getColumnDimension('G')->setWidth(90);
        $this->excel->getActiveSheet()->getColumnDimension('H')->setWidth(15);
        $this->excel->getActiveSheet()->getColumnDimension('I')->setWidth(10);
        $this->excel->getActiveSheet()->getColumnDimension('J')->setWidth(15);
        $this->excel->getActiveSheet()->getColumnDimension('K')->setWidth(15);
        $this->excel->getActiveSheet()->getColumnDimension('L')->setWidth(40);
        $this->excel->getActiveSheet()->getColumnDimension('M')->setWidth(15);
        $this->excel->getActiveSheet()->getColumnDimension('N')->setWidth(15);
        $this->excel->getActiveSheet()->getColumnDimension('O')->setWidth(40);

        $details = $this->receive_details_model->SM($filter);

        if( ! empty($details))
        {
          $custs = [];
          $no = 1;
          foreach($details as $rs)
          {
            $ay		= date('Y', strtotime($rs->date_add));
            $am		= date('m', strtotime($rs->date_add));
            $ad		= date('d', strtotime($rs->date_add));
            $date_add = PHPExcel_Shared_Date::FormattedPHPToExcel($ay, $am, $ad);

            $uy		= date('Y', strtotime($rs->date_upd));
            $um		= date('m', strtotime($rs->date_upd));
            $ud		= date('d', strtotime($rs->date_upd));
            $date_upd = PHPExcel_Shared_Date::FormattedPHPToExcel($uy, $um, $ud);

            $amount = $this->receive_details_model->get_doc_total($rs->code, 'SM');

            $customer_name = empty($custs[$rs->customer_code]) ? $this->customers_model->get_name($rs->customer_code) : $custs[$rs->customer_code];
            $custs[$rs->customer_code] = $customer_name;

            $this->excel->getActiveSheet()->setCellValue("A{$row}", $no);
            $this->excel->getActiveSheet()->setCellValue("B{$row}", $date_add);
            $this->excel->getActiveSheet()->setCellValue("C{$row}", $rs->code);
            $this->excel->getActiveSheet()->setCellValue("D{$row}", $rs->invoice);
            $this->excel->getActiveSheet()->setCellValue("E{$row}", $amount);
            $this->excel->getActiveSheet()->setCellValue("F{$row}", $rs->customer_code);
            $this->excel->getActiveSheet()->setCellValue("G{$row}", $customer_name);
            $this->excel->getActiveSheet()->setCellValue("H{$row}", $state[$rs->status]);
            $this->excel->getActiveSheet()->setCellValue("I{$row}", $rs->is_expire == 1 ? 'Y' : 'N');
            $this->excel->getActiveSheet()->setCellValue("J{$row}", $date_upd);
            $this->excel->getActiveSheet()->setCellValue("K{$row}", $rs->warehouse_code);
            $this->excel->getActiveSheet()->setCellValue("L{$row}", empty($wh[$rs->warehouse_code]) ? NULL : $wh[$rs->warehouse_code]);
            $this->excel->getActiveSheet()->setCellValue("M{$row}", $rs->user);
            $this->excel->getActiveSheet()->setCellValue("N{$row}", $this->user_model->get_name($rs->user));
            if($rs->status == 2)
            {
              $this->excel->getActiveSheet()->setCellValue("O{$row}", $rs->cancle_reason);
            }

            $no++;
            $row++;
          }

          $this->excel->getActiveSheet()->getStyle("B10:B{$row}")->getNumberFormat()->setFormatCode('dd/mm/yyyy');
          $this->excel->getActiveSheet()->getStyle("J10:J{$row}")->getNumberFormat()->setFormatCode('dd/mm/yyyy');
          $this->excel->getActiveSheet()->getStyle("E10:E{$row}")->getNumberFormat()->setFormatCode('#,##0.00');
        }
      } //--- endif SM


      if(! empty($role['CN']) OR $all)
  		{
  			$worksheet = new PHPExcel_Worksheet($this->excel, "CN");
  			$this->excel->addSheet($worksheet, $index);
  			$this->excel->setActiveSheetIndex($index);
  			$this->excel->getActiveSheet()->setTitle('CN');

  			$index++;

        //---  Report title
        $report_title = "รายงานสถานะเอกสารขาเข้า แสดงรายละเอียด";
        $role_title = "เอกสาร : รับสินค้าจากการแปรสภาพ";
        $expire_title = "อายุเอกสาร : ".($is_expired == 'all' ? 'ทั้งหมด' : ($is_expired == 1 ? 'เฉพาะที่หมดอายุ' : 'เฉพาะที่ไม่หมดอายุ'));
        $wh_title     = 'คลัง :  '. ($allWarehouse == 1 ? 'ทั้งหมด' : $this->get_title($warehouse));
        $state_title = "สถานะ : ".($allState == 1 ? "ทั้งหมด" : $this->get_state_title($status));
        //--- set report title header
        $this->excel->getActiveSheet()->setCellValue('A1', $report_title);
        $this->excel->getActiveSheet()->setCellValue('A2', $role_title);
        $this->excel->getActiveSheet()->setCellValue('A3', $expire_title);
        $this->excel->getActiveSheet()->setCellValue('A4', $state_title);
        $this->excel->getActiveSheet()->setCellValue('A5', $wh_title);
        $this->excel->getActiveSheet()->setCellValue('A6', 'วันที่เอกสาร : ('.thai_date($fromDate,'/') .') - ('.thai_date($toDate,'/').')');

        //--- set Table header
        $row = 7;

        $this->excel->getActiveSheet()->setCellValue("A{$row}", 'ลำดับ');
        $this->excel->getActiveSheet()->setCellValue("B{$row}", 'วันที่');
        $this->excel->getActiveSheet()->setCellValue("C{$row}", 'เลขที่');
        $this->excel->getActiveSheet()->setCellValue("D{$row}", 'อ้างอิง');
        $this->excel->getActiveSheet()->setCellValue("E{$row}", 'มูลค่า');
        $this->excel->getActiveSheet()->setCellValue("F{$row}", 'รหัสลูกค้า');
        $this->excel->getActiveSheet()->setCellValue("G{$row}", 'ชื่อลูกค้า');
        $this->excel->getActiveSheet()->setCellValue("H{$row}", 'สถานะ');
        $this->excel->getActiveSheet()->setCellValue("I{$row}", 'หมดอายุ');
        $this->excel->getActiveSheet()->setCellValue("J{$row}", 'วันที่แก้ไข');
        $this->excel->getActiveSheet()->setCellValue("K{$row}", 'รหัสคลัง');
        $this->excel->getActiveSheet()->setCellValue("L{$row}", 'ชื่อคลัง');
        $this->excel->getActiveSheet()->setCellValue("M{$row}", 'Username');
        $this->excel->getActiveSheet()->setCellValue("N{$row}", 'พนักงาน');
        $this->excel->getActiveSheet()->setCellValue("O{$row}", 'cancel reason');
        $row++;

        //---- กำหนดความกว้างของคอลัมภ์
        $this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(15);
        $this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
        $this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
        $this->excel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
        $this->excel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
        $this->excel->getActiveSheet()->getColumnDimension('G')->setWidth(90);
        $this->excel->getActiveSheet()->getColumnDimension('H')->setWidth(15);
        $this->excel->getActiveSheet()->getColumnDimension('I')->setWidth(10);
        $this->excel->getActiveSheet()->getColumnDimension('J')->setWidth(15);
        $this->excel->getActiveSheet()->getColumnDimension('K')->setWidth(15);
        $this->excel->getActiveSheet()->getColumnDimension('L')->setWidth(40);
        $this->excel->getActiveSheet()->getColumnDimension('M')->setWidth(15);
        $this->excel->getActiveSheet()->getColumnDimension('N')->setWidth(15);
        $this->excel->getActiveSheet()->getColumnDimension('O')->setWidth(40);

        $details = $this->receive_details_model->CN($filter);

        if( ! empty($details))
        {
          $custs = [];
          $no = 1;
          foreach($details as $rs)
          {
            $ay		= date('Y', strtotime($rs->date_add));
            $am		= date('m', strtotime($rs->date_add));
            $ad		= date('d', strtotime($rs->date_add));
            $date_add = PHPExcel_Shared_Date::FormattedPHPToExcel($ay, $am, $ad);

            $uy		= date('Y', strtotime($rs->date_upd));
            $um		= date('m', strtotime($rs->date_upd));
            $ud		= date('d', strtotime($rs->date_upd));
            $date_upd = PHPExcel_Shared_Date::FormattedPHPToExcel($uy, $um, $ud);

            $amount = $this->receive_details_model->get_doc_total($rs->code, 'CN');

            $customer_name = empty($custs[$rs->customer_code]) ? $this->customers_model->get_name($rs->customer_code) : $custs[$rs->customer_code];
            $custs[$rs->customer_code] = $customer_name;

            $this->excel->getActiveSheet()->setCellValue("A{$row}", $no);
            $this->excel->getActiveSheet()->setCellValue("B{$row}", $date_add);
            $this->excel->getActiveSheet()->setCellValue("C{$row}", $rs->code);
            $this->excel->getActiveSheet()->setCellValue("D{$row}", $rs->invoice);
            $this->excel->getActiveSheet()->setCellValue("E{$row}", $amount);
            $this->excel->getActiveSheet()->setCellValue("F{$row}", $rs->customer_code);
            $this->excel->getActiveSheet()->setCellValue("G{$row}", $customer_name);
            $this->excel->getActiveSheet()->setCellValue("H{$row}", $state[$rs->status]);
            $this->excel->getActiveSheet()->setCellValue("I{$row}", 'N');
            $this->excel->getActiveSheet()->setCellValue("J{$row}", $date_upd);
            $this->excel->getActiveSheet()->setCellValue("K{$row}", $rs->warehouse_code);
            $this->excel->getActiveSheet()->setCellValue("L{$row}", empty($wh[$rs->warehouse_code]) ? NULL : $wh[$rs->warehouse_code]);
            $this->excel->getActiveSheet()->setCellValue("M{$row}", $rs->user);
            $this->excel->getActiveSheet()->setCellValue("N{$row}", $this->user_model->get_name($rs->user));
            if($rs->status == 2)
            {
              $this->excel->getActiveSheet()->setCellValue("O{$row}", $rs->cancle_reason);
            }

            $no++;
            $row++;
          }

          $this->excel->getActiveSheet()->getStyle("B10:B{$row}")->getNumberFormat()->setFormatCode('dd/mm/yyyy');
          $this->excel->getActiveSheet()->getStyle("J10:J{$row}")->getNumberFormat()->setFormatCode('dd/mm/yyyy');
          $this->excel->getActiveSheet()->getStyle("E10:E{$row}")->getNumberFormat()->setFormatCode('#,##0.00');
        }
      } //--- endif SM
    }

    setToken($token);
    $file_name = "Report Receive Details.xlsx";
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'); /// form excel 2007 XLSX
    header('Content-Disposition: attachment;filename="'.$file_name.'"');
    $writer = PHPExcel_IOFactory::createWriter($this->excel, 'Excel2007');
    $writer->save('php://output');

  }


  public function get_title($ds = array())
  {
    $list = "";
    if(!empty($ds))
    {
      $i = 1;
      foreach($ds as $rs)
      {
        $list .= $i === 1 ? $rs : ", {$rs}";
        $i++;
      }
    }

    return $list;
  }



  private function get_state_title($ds = array())
  {
    $list = "";

    $states = array(
      '0' => 'ดราฟท์',
      '1' => 'สำเร็จ',
      '2' => 'ยกเลิก',
      '3' => 'รอรับสินค้า',
      '4' => 'รอยืนยันรับ'
    );

    if( ! empty($ds))
    {
      $i = 1;
      foreach($ds as $rs)
      {
        if( ! empty($states[$rs]))
        {
          $list .= $i === 1 ? $states[$rs] : ", {$states[$rs]}";
          $i++;
        }
      }
    }

    return $list;
  }


} //--- end class








 ?>
