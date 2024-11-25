<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Order_details extends PS_Controller
{
  public $menu_code = 'RAODDT';
	public $menu_group_code = 'RE';
  public $menu_sub_group_code = 'REAUDIT';
	public $title = 'รายงานสถานะเอกสารขาออก แสดงรายละเอียด';
  public $filter;
  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'report/audit/order_details';
    $this->load->model('report/audit/order_details_model');
    $this->load->model('masters/products_model');
    $this->load->model('orders/orders_model');
    $this->load->model('masters/channels_model');
    $this->load->model('masters/payment_methods_model');
    $this->load->model('masters/warehouse_model');
    $this->load->model('masters/customers_model');
  }

  public function index()
  {
    $ds = array(
      'channels_list' => $this->channels_model->get_data(),
      'payment_list' => $this->payment_methods_model->get_data(),
      'warehouse_list' => $this->warehouse_model->get_sell_warehouse_list()
    );

    $this->load->view('report/audit/report_order_details', $ds);
  }

  private function channels_array()
  {
    $ds = array();

    $rs = $this->db->get('channels');
    if($rs->num_rows() > 0)
    {
      foreach($rs->result() as $rd)
      {
        $ds[$rd->code] = $rd->name;
      }
    }

    return $ds;
  }

  private function payment_array()
  {
    $ds = array();
    $rs = $this->db->get('payment_method');

    if($rs->num_rows() > 0)
    {
      foreach($rs->result() as $rd)
      {
        $ds[$rd->code] = $rd->name;
      }
    }

    return $ds;
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
      '1' => 'รอดำเนินการ',
      '2' => 'รอชำระเงิน',
      '3' => 'รอจัดสินค้า',
      '4' => 'กำลังจัดสินค้า',
      '5' => 'รอตรวจสินค้า',
      '6' => 'กำลังตรวจสินค้า',
      '7' => 'รอการจัดส่ง',
      '8' => 'จัดส่งแล้ว',
      '9' => 'ยกเลิก'
    );

    return $ds;
  }

  private function cancel_reason($code)
  {
    $rs = $this->db->where('code', $code)->order_by('id', 'DESC')->limit(1)->get('order_cancle_reason');

    if($rs->num_rows() == 1)
    {
      return $rs->row()->reason;
    }

    return NULL;
  }


  public function get_report()
  {
    $sc = TRUE;

    if($this->input->get("json"))
    {
      $json = json_decode($this->input->get("json"));

      if( ! empty($json))
      {
        $filter = array(
          "from_date" => $json->fromDate,
          "to_date" => $json->toDate,
          "all_role" => $json->allRole,
          "role" => $json->role,
          "is_expired" => $json->isExpired,
          "all_state" => $json->allState,
          "state" => $json->state,
          "all_channels" => $json->allChannels,
          "channels" => $json->channels,
          "all_payment" => $json->allPayments,
          "payment" => $json->payment,
          "all_warehouse" => $json->allWarehouse,
          "warehouse" => $json->warehouse
        );

        $limit = 1000;
        $rows = $this->order_details_model->count_rows($filter);
        $details = $this->order_details_model->get_data($filter, $limit);
        $ds = array();

        if( ! empty($details))
        {
          $state = $this->state_array();
          $wh = $this->warehouse_array();
          $channels = $this->channels_array();
          $payment = $this->payment_array();
          $cust = array();

          $no = 1;

          foreach($details as $rs)
          {
            $rs->no = number($no);
            $rs->expired = $rs->is_expired == 1 ? 'Y' : 'N';
            $rs->date_add = thai_date($rs->date_add, FALSE, '/');
            $rs->date_upd = thai_date($rs->date_upd, FALSE, '/');
            $rs->total_amount = number($this->order_details_model->get_doc_total($rs->code), 2);
            $rs->channels_name = empty($channels[$rs->channels_code]) ? NULL : $channels[$rs->channels_code];
            $rs->payment_name = empty($payment[$rs->payment_code]) ? NULL : $payment[$rs->payment_code];
            $rs->warehouse_name = empty($wh[$rs->warehouse_code]) ? NULL : $wh[$rs->warehouse_code];
            $rs->state_name = $state[$rs->state];
            $rs->uname = $rs->user;
            $rs->emp_name = $this->user_model->get_name($rs->user);
            $rs->cancel_reason = $rs->state == 9 ? $this->cancel_reason($rs->code) : NULL;

            if(empty($cust[$rs->customer_code]))
            {
              $customer_name = $this->customers_model->get_name($rs->customer_code);
              $cust[$rs->customer_code] = $customer_name;
            }

            $rs->customer_name = $cust[$rs->customer_code];
            $no++;
          }
        }

        $ds = array(
          "limit" => $limit,
          "rows" => $rows,
          "details" => $details
        );
      }
      else
      {
        $sc = FALSE;
        $this->error = "Invalid request data format";
      }
    }

    echo $sc === TRUE ? json_encode($ds) : $this->error;
  }



  public function do_export()
  {
    $token = $this->input->post('token');

    //--- load excel library
    $this->load->library('excel');

    $this->excel->setActiveSheetIndex(0);
    $this->excel->getActiveSheet()->setTitle('Order details Report');

    if($this->input->post('fromDate'))
    {
      $allRole = $this->input->post('allRole');
      $allWarehouse = $this->input->post('allWarehouse');
      $allChannels = $this->input->post('allChannels');
      $allPayments = $this->input->post('allPayments');
      $allState = $this->input->post('allState');
      $is_expired = $this->input->post('is_expired');
      $fromDate = $this->input->post('fromDate');
      $toDate = $this->input->post('toDate');
      $role = $this->input->post('role');
      $channels = $this->input->post('channels');
      $payments = $this->input->post('payments');
      $warehouse = $this->input->post('warehouse');
      $state = $this->input->post('state');

      $filter = array(
        "from_date" => $fromDate,
        "to_date" => $toDate,
        "all_role" => $allRole,
        "role" => $role,
        "is_expired" => $is_expired,
        "all_state" => $allState,
        "state" => $state,
        "all_channels" => $allChannels,
        "channels" => $channels,
        "all_payment" => $allPayments,
        "payment" => $payments,
        "all_warehouse" => $allWarehouse,
        "warehouse" => $warehouse
      );

      $details = $this->order_details_model->get_data($filter);

      //---  Report title
      $report_title = "รายงานสถานะเอกสารแสดงรายละเอียด";
      $role_title = "เอกสาร : ". ($allRole == 1 ? 'ทั้งหมด' : $this->get_role_title($role));
      $expire_title = "อายุเอกสาร : ".($is_expired == 'all' ? 'ทั้งหมด' : ($is_expired == 1 ? 'เฉพาะที่หมดอายุ' : 'เฉพาะที่ไม่หมดอายุ'));
      $wh_title     = 'คลัง :  '. ($allWarehouse == 1 ? 'ทั้งหมด' : $this->get_title($warehouse));
      $ch_title = "ช่องทางขาย : ". ($allChannels == 1 ? 'ทั้งหมด' : $this->get_title($channels));
      $pay_title = "การชำระเงิน : ". ($allPayments == 1 ? "ทั้งหมด" : $this->get_title($payments));
      $state_title = "สถานะ : ".($allState == 1 ? "ทั้งหมด" : $this->get_state_title($state));

      //--- set report title header
      $this->excel->getActiveSheet()->setCellValue('A1', $report_title);
      $this->excel->getActiveSheet()->setCellValue('A2', $role_title);
      $this->excel->getActiveSheet()->setCellValue('A3', $expire_title);
      $this->excel->getActiveSheet()->setCellValue('A4', $state_title);
      $this->excel->getActiveSheet()->setCellValue('A5', $ch_title);
      $this->excel->getActiveSheet()->setCellValue('A6', $pay_title);
      $this->excel->getActiveSheet()->setCellValue('A7', $wh_title);
      $this->excel->getActiveSheet()->setCellValue('A8', 'วันที่เอกสาร : ('.thai_date($fromDate,'/') .') - ('.thai_date($toDate,'/').')');

      //--- set Table header
      $row = 9;

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
      $this->excel->getActiveSheet()->setCellValue("K{$row}", 'ช่องทางขาย');
      $this->excel->getActiveSheet()->setCellValue("L{$row}", 'การชำระเงิน');
      $this->excel->getActiveSheet()->setCellValue("M{$row}", 'รหัสคลัง');
      $this->excel->getActiveSheet()->setCellValue("N{$row}", 'ชื่อคลัง');
      $this->excel->getActiveSheet()->setCellValue("O{$row}", 'Username');
      $this->excel->getActiveSheet()->setCellValue("P{$row}", 'พนักงาน');
      $this->excel->getActiveSheet()->setCellValue("Q{$row}", 'cancel reason');
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
      $this->excel->getActiveSheet()->getColumnDimension('K')->setWidth(20);
      $this->excel->getActiveSheet()->getColumnDimension('L')->setWidth(20);
      $this->excel->getActiveSheet()->getColumnDimension('M')->setWidth(15);
      $this->excel->getActiveSheet()->getColumnDimension('N')->setWidth(40);
      $this->excel->getActiveSheet()->getColumnDimension('O')->setWidth(15);
      $this->excel->getActiveSheet()->getColumnDimension('P')->setWidth(15);
      $this->excel->getActiveSheet()->getColumnDimension('Q')->setWidth(40);

      if( ! empty($details))
      {
        $state = $this->state_array();
        $wh = $this->warehouse_array();
        $channels = $this->channels_array();
        $payment = $this->payment_array();
        $cust = array();

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

          $amount = $this->order_details_model->get_doc_total($rs->code);

          if(empty($cust[$rs->customer_code]))
          {
            $customer_name = $this->customers_model->get_name($rs->customer_code);
            $cust[$rs->customer_code] = $customer_name;
          }

          $this->excel->getActiveSheet()->setCellValue("A{$row}", $no);
          $this->excel->getActiveSheet()->setCellValue("B{$row}", $date_add);
          $this->excel->getActiveSheet()->setCellValue("C{$row}", $rs->code);
          $this->excel->getActiveSheet()->setCellValue("D{$row}", $rs->reference);
          $this->excel->getActiveSheet()->setCellValue("E{$row}", $amount);
          $this->excel->getActiveSheet()->setCellValue("F{$row}", $rs->customer_code);
          $this->excel->getActiveSheet()->setCellValue("G{$row}", $cust[$rs->customer_code]);
          $this->excel->getActiveSheet()->setCellValue("H{$row}", $state[$rs->state]);
          $this->excel->getActiveSheet()->setCellValue("I{$row}", $rs->is_expired == 1 ? 'Y' : 'N');
          $this->excel->getActiveSheet()->setCellValue("J{$row}", $date_upd);
          $this->excel->getActiveSheet()->setCellValue("K{$row}", empty($channels[$rs->channels_code]) ? NULL : $channels[$rs->channels_code]);
          $this->excel->getActiveSheet()->setCellValue("L{$row}", empty($payment[$rs->payment_code]) ? NULL : $payment[$rs->payment_code]);
          $this->excel->getActiveSheet()->setCellValue("M{$row}", $rs->warehouse_code);
          $this->excel->getActiveSheet()->setCellValue("N{$row}", empty($wh[$rs->warehouse_code]) ? NULL : $wh[$rs->warehouse_code]);
          $this->excel->getActiveSheet()->setCellValue("O{$row}", $rs->user);
          $this->excel->getActiveSheet()->setCellValue("P{$row}", $this->user_model->get_name($rs->user));
          if($rs->state == 9)
          {
            $this->excel->getActiveSheet()->setCellValue("Q{$row}", $this->cancel_reason($rs->code));
          }


          $no++;
          $row++;
        }

        $this->excel->getActiveSheet()->getStyle("B10:B{$row}")->getNumberFormat()->setFormatCode('dd/mm/yyyy');
        $this->excel->getActiveSheet()->getStyle("J10:J{$row}")->getNumberFormat()->setFormatCode('dd/mm/yyyy');
        $this->excel->getActiveSheet()->getStyle("E10:E{$row}")->getNumberFormat()->setFormatCode('#,##0.00');
      }
    }

    setToken($token);
    $file_name = "Report Order Details.xlsx";
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


  private function get_role_title($ds = array())
  {
    $list = "";

    $roles = array(
      'S' => 'WO',
      'C' => 'WC',
      'N' => 'WT',
      'P' => 'WS',
      'U' => 'WU',
      'T' => 'WQ',
      'Q' => 'WV',
      'L' => 'WL'
    );

    if( ! empty($ds))
    {
      $i = 1;
      foreach($ds as $rs)
      {
        if( ! empty($roles[$rs]))
        {
          $list .= $i=== 1 ? $roles[$rs] : ", {$roles[$rs]}";
          $i++;
        }
      }
    }

    return $list;
  }


  private function get_state_title($ds = array())
  {
    $list = "";

    $states = array(
      "1" => "รอดำเนินการ",
      "2" => "รอชำระเงิน",
      "3" => "รอจัดสินค้า",
      "4" => "กำลังจัดสินค้า",
      "5" => "รอตรวจ",
      "6" => "กำลังตรวจ",
      "7" => "รอเปิดบิล",
      "8" => "เปิดบิลแล้ว",
      "9" => "ยกเลิก"
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
