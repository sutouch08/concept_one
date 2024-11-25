<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Sales_channels_items extends PS_Controller
{
  public $menu_code = 'RSOCHPD';
	public $menu_group_code = 'RE';
  public $menu_sub_group_code = 'RESALE';
	public $title = 'รายงานยอดขายแยกตามช่องทางขายแสดงรายการสินค้า';
  public $filter;
  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'report/sales/sales_channels_items';
    $this->load->model('report/sales/sales_report_model');
    $this->load->model('masters/products_model');
    $this->load->model('orders/orders_model');
    $this->load->model('masters/channels_model');
    $this->load->model('masters/payment_methods_model');
    $this->load->model('masters/warehouse_model');
  }

  public function index()
  {
    $ds = array(
      'channels_list' => $this->channels_model->get_data()
    );

    $this->load->view('report/sales/report_sales_channels_items', $ds);
  }


  public function do_export()
  {
    $allProducts = $this->input->post('allProducts');
    $pdFrom = $this->input->post('pdFrom');
    $pdTo = $this->input->post('pdTo');

    $allWarehouse = $this->input->post('allWarhouse');
    $warehouse = $this->input->post('warehouse');

    $allChannels = $this->input->post('allChannels');
    $channels = $this->input->post('channels');

    $allPayments = $this->input->post('allPayments');
    $payments = $this->input->post('payments');

    $fromDate = $this->input->post('fromDate');
    $toDate = $this->input->post('toDate');

    $token = $this->input->post('token');

    $post_state = $this->input->post('state');

    $state_name = array(
      '1' => 'รอดำเนินการ',
      '2' => 'รอชำระเงิน',
      '3' => 'รอจัด',
      '4' => 'กำลังจัด',
      '5' => 'รอตรวจ',
      '6' => 'กำลังตรวจ',
      '7' => 'รอเปิดบิล',
      '8' => 'เปิดบิลแล้ว',
      '9' => 'ยกเลิก'
    );

    $ch_name = $this->order_channels_items_model->channels_array();
    $pm_name = $this->order_channels_items_model->payment_array();

    $state = array();
    $state_list = array();

    if(!empty($post_state))
    {
      foreach($post_state AS $key => $val)
      {
        if($val == 1)
        {
          $state[] = $key;
          $state_list[] = $state_name[$key];
        }
      }
    }





    //---  Report title
    $report_title = "รายงานออเดอร์แยกตามช่องทางขายแสดงรายการสินค้า";
    $wh_title     = 'คลัง :  '. ($allWarehouse == 1 ? 'ทั้งหมด' : $this->get_title($warehouse));
    $pd_title     = 'สินค้า :  '. ($allProducts == 1 ? 'ทั้งหมด' : '('.$pdFrom.') - ('.$pdTo.')');
    $ch_title = "ช่องทางขาย : ". ($allChannels == 1 ? 'ทั้งหมด' : $this->get_title($channels));
    $pay_title = "การชำระเงิน : ". ($allPayments == 1 ? "ทั้งหมด" : $this->get_title($payments));
    $state_title = "สถานะ : ".(empty($post_state) ? "ทั้งหมด" : $this->get_title($state_list));

    $ds = array(
      'allChannels' => $allChannels,
      'channels' => $channels,
      'allPayments' => $allPayments,
      'payments' => $payments,
      'allWarehouse' => $allWarehouse,
      'warehouse' => $warehouse,
      'allProducts' => $allProducts,
      'pdFrom' => $pdFrom,
      'pdTo' => $pdTo,
      'fromDate' => $fromDate,
      'toDate' => $toDate,
      'state' => $state
    );

    $result = $this->order_channels_items_model->get_data($ds);

    //--- load excel library
    $this->load->library('excel');

    $this->excel->setActiveSheetIndex(0);
    $this->excel->getActiveSheet()->setTitle('Sales online channels Report');

    //--- set report title header
    $this->excel->getActiveSheet()->setCellValue('A1', $report_title);
    $this->excel->getActiveSheet()->mergeCells('A1:G1');
    $this->excel->getActiveSheet()->setCellValue('A2', $ch_title);
    $this->excel->getActiveSheet()->mergeCells('A2:G2');
    $this->excel->getActiveSheet()->setCellValue('A3', $pay_title);
    $this->excel->getActiveSheet()->mergeCells('A3:G3');
    $this->excel->getActiveSheet()->setCellValue('A4', $pd_title);
    $this->excel->getActiveSheet()->mergeCells('A4:G4');
    $this->excel->getActiveSheet()->setCellValue('A5', $wh_title);
    $this->excel->getActiveSheet()->mergeCells('A5:G5');
    $this->excel->getActiveSheet()->setCellValue('A6', $state_title);
    $this->excel->getActiveSheet()->mergeCells('A6:G6');
    $this->excel->getActiveSheet()->setCellValue('A7', 'วันที่เอกสาร : ('.thai_date($fromDate,'/') .') - ('.thai_date($toDate,'/').')');
    $this->excel->getActiveSheet()->mergeCells('A7:G7');

    //--- set Table header


    $this->excel->getActiveSheet()->setCellValue('A8', 'ลำดับ');
    $this->excel->getActiveSheet()->setCellValue('B8', 'วันที่');
    $this->excel->getActiveSheet()->setCellValue('C8', 'เอกสาร');
    $this->excel->getActiveSheet()->setCellValue('D8', 'อ้างอิง');
    $this->excel->getActiveSheet()->setCellValue('E8', 'เลขที่จัดส่ง');
    $this->excel->getActiveSheet()->setCellValue('F8', 'ชื่อลูกค้า');
    $this->excel->getActiveSheet()->setCellValue('G8', 'ที่อยู่บรรทัด 1');
    $this->excel->getActiveSheet()->setCellValue('H8', 'ที่อยู่บรรทัด 2');
    $this->excel->getActiveSheet()->setCellValue('I8', 'อำเภอ');
    $this->excel->getActiveSheet()->setCellValue('J8', 'จังหวัด');
    $this->excel->getActiveSheet()->setCellValue('K8', 'รหัสไปรษณีย์');
    $this->excel->getActiveSheet()->setCellValue('L8', 'เบอร์โทรศัพท์');
    $this->excel->getActiveSheet()->setCellValue('M8', 'ช่องทางขาย');
    $this->excel->getActiveSheet()->setCellValue('N8', 'ช่องทางการชำระเงิน');
    $this->excel->getActiveSheet()->setCellValue('O8', 'สินค้า');
    $this->excel->getActiveSheet()->setCellValue('P8', 'ราคา');
    $this->excel->getActiveSheet()->setCellValue('Q8', 'จำนวน');
    $this->excel->getActiveSheet()->setCellValue('R8', 'ส่วนลด');
    $this->excel->getActiveSheet()->setCellValue('S8', 'มูลค่า');
    $this->excel->getActiveSheet()->setCellValue('T8', 'ค่าจัดส่ง');
    $this->excel->getActiveSheet()->setCellValue('U8', 'ค่าบริการ');
    $this->excel->getActiveSheet()->setCellValue('V8', 'สถานะ');
    $this->excel->getActiveSheet()->setCellValue('W8', 'เหตุผลในการยกเลิก');

    //---- กำหนดความกว้างของคอลัมภ์
    $this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(15);
    $this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
    $this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
    $this->excel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
    $this->excel->getActiveSheet()->getColumnDimension('F')->setWidth(25);
    $this->excel->getActiveSheet()->getColumnDimension('G')->setWidth(90);
    $this->excel->getActiveSheet()->getColumnDimension('H')->setWidth(50);
    $this->excel->getActiveSheet()->getColumnDimension('I')->setWidth(20);
    $this->excel->getActiveSheet()->getColumnDimension('J')->setWidth(20);
    $this->excel->getActiveSheet()->getColumnDimension('K')->setWidth(15);
    $this->excel->getActiveSheet()->getColumnDimension('L')->setWidth(15);
    $this->excel->getActiveSheet()->getColumnDimension('M')->setWidth(15);
    $this->excel->getActiveSheet()->getColumnDimension('N')->setWidth(15);
    $this->excel->getActiveSheet()->getColumnDimension('O')->setWidth(25);
    $this->excel->getActiveSheet()->getColumnDimension('T')->setWidth(15);
    $this->excel->getActiveSheet()->getColumnDimension('U')->setWidth(15);
    $this->excel->getActiveSheet()->getColumnDimension('V')->setWidth(15);
    $this->excel->getActiveSheet()->getColumnDimension('W')->setWidth(15);

    $row = 9;


    if(!empty($result))
    {
      $no = 1;
      $prev_code = NULL;
      $this->load->model('address/address_model');
      $adr = NULL;
      foreach($result as $rs)
      {
        $y		= date('Y', strtotime($rs->date_add));
        $m		= date('m', strtotime($rs->date_add));
        $d		= date('d', strtotime($rs->date_add));
        $date = PHPExcel_Shared_Date::FormattedPHPToExcel($y, $m, $d);

        if($prev_code != $rs->code)
        {
          if(!empty($rs->id_address))
          {
            $adr = $this->address_model->get_shipping_detail($rs->id_address);
          }
          else
          {
            $adr = $this->address_model->get_shipping_address_by_code($rs->customer_ref);
          }
        }

        if(empty($adr))
        {
          $adr = new stdClass();
          $adr->name = NULL;
          $adr->address = NULL;
          $adr->sub_district = NULL;
          $adr->district = NULL;
          $adr->province = NULL;
          $adr->postcode = NULL;
          $adr->phone = NULL;
        }


        //--- ลำดับ
        $this->excel->getActiveSheet()->setCellValue('A'.$row, $no);

        //--- วันที่เอกสาร
        $this->excel->getActiveSheet()->setCellValue('B'.$row, $date);

        //--- เลขที่เอกสาร (SO)
        $this->excel->getActiveSheet()->setCellValue('C'.$row, $rs->code);

        //--- เลขที่อ้างอิง
        $this->excel->getActiveSheet()->setCellValueExplicit('D'.$row, $rs->reference, PHPExcel_Cell_DataType::TYPE_STRING);

        //--- เลขที่จัดส่ง
        $this->excel->getActiveSheet()->setCellValueExplicit('E'.$row, $rs->shipping_code, PHPExcel_Cell_DataType::TYPE_STRING);

        //--- ชือผู้รับสินค้า
        $this->excel->getActiveSheet()->setCellValue('F'.$row, $adr->name);

        //--- ที่อยู่บรรทัดที่ 1
        $this->excel->getActiveSheet()->setCellValue('G'.$row, $adr->address);

        //--- ที่อยู่บรรทัดที่ 2
        $this->excel->getActiveSheet()->setCellValue('H'.$row, $adr->sub_district);

        //--- อำเภอ / เขต
        $this->excel->getActiveSheet()->setCellValue('I'.$row, $adr->district);

        //--- จังหวัด
        $this->excel->getActiveSheet()->setCellValue('J'.$row, $adr->province);

        //--- รหัรหัสไปรษณีย์
        $this->excel->getActiveSheet()->setCellValueExplicit('K'.$row, $adr->postcode, PHPExcel_Cell_DataType::TYPE_STRING);

        //--- เบอร์โทรศัพท์
        $this->excel->getActiveSheet()->setCellValueExplicit('L'.$row, $adr->phone, PHPExcel_Cell_DataType::TYPE_STRING);
        //--- ช่องทางการขาย
        $this->excel->getActiveSheet()->setCellValue('M'.$row, $ch_name[$rs->channels_code]);

        //--- ช่องทางการชำระเงิน
        $this->excel->getActiveSheet()->setCellValue('N'.$row, $pm_name[$rs->payment_code]);

        //--- รหัสสินค้า
        $this->excel->getActiveSheet()->setCellValue('O'.$row, $rs->product_code);

        //--- ราคาสินค้า
        $this->excel->getActiveSheet()->setCellValue('P'.$row, $rs->price);

        //--- จำนวน
        $this->excel->getActiveSheet()->setCellValue('Q'.$row, $rs->qty);

        //--- ส่วนลดรวมเป้นจำนวนเงิน
        $this->excel->getActiveSheet()->setCellValue('R'.$row, $rs->discount_amount);

        //--- ยอดเงินรวม
        $this->excel->getActiveSheet()->setCellValue('S'.$row, $rs->total_amount);

        //--- ค่าจัดส่ง
        $this->excel->getActiveSheet()->setCellValue('T'.$row, $rs->shipping_fee);

        //--- ค่าบริการ
        $this->excel->getActiveSheet()->setCellValue('U'.$row, $rs->service_fee);

        //--- สถานะออเดอร์
        $this->excel->getActiveSheet()->setCellValue('V'.$row, $state_name[$rs->state]);

        //--- เหตุผลในการยกเลิก
        if($rs->state == 9)
        {
          $this->excel->getActiveSheet()->setCellValue('W'.$row, $this->order_channels_items_model->cancel_reason($rs->code));
        }

        $no++;
        $row++;
      }

      $this->excel->getActiveSheet()->getStyle('B6:B'.$row)->getNumberFormat()->setFormatCode('dd/mm/yyyy');

    }


    setToken($token);
    $file_name = "Report Order Channels Details.xlsx";
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


} //--- end class








 ?>
