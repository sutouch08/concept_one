<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Sales_channels_details extends PS_Controller
{
  public $menu_code = 'RSOCDS';
	public $menu_group_code = 'RE';
  public $menu_sub_group_code = 'RESALE';
	public $title = 'รายงานออเดอร์ออนไลน์แสดงรายละเอียดการจัดส่ง(ไม่ใช่ยอดขาย)';
  public $filter;
  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'report/sales/sales_channels_details';
    $this->load->model('report/sales/sales_report_model');
  }

  public function index()
  {
    $this->load->model('masters/channels_model');
    $channels = $this->channels_model->get_online_list();
    $ds['channels_list'] = $channels;
    $this->load->view('report/sales/report_sales_channels_details', $ds);
  }


  public function do_export()
  {
    $allChannels = $this->input->post('allChannels');
    $channels = $this->input->post('channels');
    $pdFrom = $this->input->post('pdFrom');
    $pdTo = $this->input->post('pdTo');
    $refCodeFrom = $this->input->post('refCodeFrom');
    $refCodeTo = $this->input->post('refCodeTo');
    $fromDate = $this->input->post('fromDate');
    $toDate = $this->input->post('toDate');

    $allProduct = (!empty($pdFrom) && !empty($pdTo)) ? 0 : 1;

    $token = $this->input->post('token');

    $ds = array(
      'all_channels' => $this->input->post('allChannels'),
      'channels' => $this->input->post('channels'),
      'item_from' => $this->input->post('pdFrom'),
      'item_to' => $this->input->post('pdTo'),
      'from_reference' => $this->input->post('refCodeFrom'),
      'to_reference' => $this->input->post('refCodeTo'),
      'from_date' => $this->input->post('fromDate'),
      'to_date' => $this->input->post('toDate')
    );

    $result = $this->sales_report_model->get_online_channels_details($ds);


    $ch_title = "";
    if(!empty($channels))
    {
      $i = 1;
      foreach($channels as $id_channels)
      {
        $ch_title .= $i == 1 ? $id_channels :', '.$id_channels;
        $i++;
      }
    }

    //---  Report title
    $report_title = 'รายงาน ออเดอร์ออนไลน์ แสดงรายละเอียดการจัดส่ง วันที่ ' . thai_date($fromDate,'/') .' ถึง '.thai_date($toDate, '/');
    $channels_title = 'ช่องทางการขาย : '.($allChannels == 1 ? 'ทั้งหมด' : $ch_title);
    $pd_title     = 'สินค้า :  '. ($allProduct == 1 ? 'ทั้งหมด' : '('.$pdFrom.') - ('.$pdTo.')');

    //--- load excel library
    $this->load->library('excel');

    $this->excel->setActiveSheetIndex(0);
    $this->excel->getActiveSheet()->setTitle('Sales online channels Report');

    //--- set report title header
    $this->excel->getActiveSheet()->setCellValue('A1', $report_title);
    $this->excel->getActiveSheet()->mergeCells('A1:G1');
    $this->excel->getActiveSheet()->setCellValue('A2', $channels_title);
    $this->excel->getActiveSheet()->mergeCells('A2:G2');
    $this->excel->getActiveSheet()->setCellValue('A3', $pd_title);
    $this->excel->getActiveSheet()->mergeCells('A3:G3');
    $this->excel->getActiveSheet()->setCellValue('A4', 'วันที่เอกสาร : ('.thai_date($fromDate,'/') .') - ('.thai_date($toDate,'/').')');
    $this->excel->getActiveSheet()->mergeCells('A4:G4');

    //--- set Table header


    $this->excel->getActiveSheet()->setCellValue('A5', 'ลำดับ');
    $this->excel->getActiveSheet()->setCellValue('B5', 'วันที่');
    $this->excel->getActiveSheet()->setCellValue('C5', 'เอกสาร');
    $this->excel->getActiveSheet()->setCellValue('D5', 'อ้างอิง');
    $this->excel->getActiveSheet()->setCellValue('E5', 'เลขที่จัดส่ง');
    $this->excel->getActiveSheet()->setCellValue('F5', 'ชื่อลูกค้า');
    $this->excel->getActiveSheet()->setCellValue('G5', 'ที่อยู่บรรทัด 1');
    $this->excel->getActiveSheet()->setCellValue('H5', 'ที่อยู่บรรทัด 2');
    $this->excel->getActiveSheet()->setCellValue('I5', 'อำเภอ');
    $this->excel->getActiveSheet()->setCellValue('J5', 'จังหวัด');
    $this->excel->getActiveSheet()->setCellValue('K5', 'รหัสไปรษณีย์');
    $this->excel->getActiveSheet()->setCellValue('L5', 'เบอร์โทรศัพท์');
    $this->excel->getActiveSheet()->setCellValue('M5', 'ช่องทางขาย');
    $this->excel->getActiveSheet()->setCellValue('N5', 'ช่องทางการชำระเงิน');
    $this->excel->getActiveSheet()->setCellValue('O5', 'สินค้า');
    $this->excel->getActiveSheet()->setCellValue('P5', 'ราคา');
    $this->excel->getActiveSheet()->setCellValue('Q5', 'จำนวน');
    $this->excel->getActiveSheet()->setCellValue('R5', 'ส่วนลด');
    $this->excel->getActiveSheet()->setCellValue('S5', 'มูลค่า');
    $this->excel->getActiveSheet()->setCellValue('T5', 'ค่าจัดส่ง');
    $this->excel->getActiveSheet()->setCellValue('U5', 'ค่าบริการ');
    $this->excel->getActiveSheet()->setCellValue('V5', 'สถานะ');

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

    $row = 6;


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
        $this->excel->getActiveSheet()->setCellValue('M'.$row, $rs->channels);

        //--- ช่องทางการชำระเงิน
        $this->excel->getActiveSheet()->setCellValue('N'.$row, $rs->payment);

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
        $this->excel->getActiveSheet()->setCellValue('V'.$row, $rs->state);

        $no++;
        $row++;
      }

      $this->excel->getActiveSheet()->getStyle('B6:B'.$row)->getNumberFormat()->setFormatCode('dd/mm/yyyy');

    }


    setToken($token);
    $file_name = "Repor Sale Online Channels Details.xlsx";
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'); /// form excel 2007 XLSX
    header('Content-Disposition: attachment;filename="'.$file_name.'"');
    $writer = PHPExcel_IOFactory::createWriter($this->excel, 'Excel2007');
    $writer->save('php://output');
  }


} //--- end class








 ?>
