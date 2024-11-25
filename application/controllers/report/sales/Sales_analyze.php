<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Sales_analyze extends PS_Controller
{
  public $menu_code = 'RSOANLS';
	public $menu_group_code = 'RE';
  public $menu_sub_group_code = 'RESALE';
	public $title = 'รายงานวิเคราะห์ขายแบบละเอียด';
  public $filter;
  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'report/sales/sales_analyze';
    $this->load->model('report/sales/sales_report_model');
    $this->load->helper('channels');
    $this->load->helper('saleman');
  }

  public function index()
  {
    $this->load->view('report/sales/report_sales_analyze');
  }


  public function do_export()
  {
    ini_set('memory_limit','512M'); // This also needs to be increased in some cases. Can be changed to a higher value as per need)
    ini_set('max_execution_time', 600); //-- set max execution time to 10 minutes

    $fromDate = $this->input->post('fromDate');
    $toDate = $this->input->post('toDate');
    $date_type = $this->input->post('date_type');
    $token = $this->input->post('token');

    //---  Report title
    $date_title = $date_type == 'D' ? 'วันที่เอกสาร' : 'ตามวันที่บันทึกขาย';
    $report_title = "รายงานวิเคราะห์ขายแบบละเอียด วันที่  {$fromDate} - {$toDate} {$date_title}";

    $details = $this->sales_report_model->get_sales_details($fromDate, $toDate, $date_type);

    //--- load excel library
    $this->load->library('excel');

    $this->excel->setActiveSheetIndex(0);
    $this->excel->getActiveSheet()->setTitle('Sales analyze Report');

    //--- set report title header
    $this->excel->getActiveSheet()->setCellValue('A1', $report_title);
    //--- set Table header

    $row = 3;

    $this->excel->getActiveSheet()->setCellValue("A{$row}",'#');
    $this->excel->getActiveSheet()->setCellValue("B{$row}",'วันที่เอกสาร');
    $this->excel->getActiveSheet()->setCellValue("C{$row}",'วันที่บันทึกขาย');
    $this->excel->getActiveSheet()->setCellValue("D{$row}",'เลขที่');
    $this->excel->getActiveSheet()->setCellValue("E{$row}",'เล่มเอกสาร');
    $this->excel->getActiveSheet()->setCellValue("F{$row}",'เครดิต');
    $this->excel->getActiveSheet()->setCellValue("G{$row}",'รหัสสินค้า');
    $this->excel->getActiveSheet()->setCellValue("H{$row}",'ชื่อสินค้า');
    $this->excel->getActiveSheet()->setCellValue("I{$row}",'qty');
    $this->excel->getActiveSheet()->setCellValue("J{$row}",'ราคาขายก่อนส่วนลด ไม่รวม VAT');
    $this->excel->getActiveSheet()->setCellValue("K{$row}",'ส่วนลดรายการ');
    $this->excel->getActiveSheet()->setCellValue("L{$row}",'ราคาขายหลังส่วนลด ไม่รวม VAT');
    $this->excel->getActiveSheet()->setCellValue("M{$row}",'ราคาขายหลังส่วนลดรวม VAT');
    $this->excel->getActiveSheet()->setCellValue("N{$row}",'มูลค่า (ไม่รวม VAT)');
    $this->excel->getActiveSheet()->setCellValue("O{$row}",'ชนิด VAT');
    $this->excel->getActiveSheet()->setCellValue("P{$row}",'VAT Rate');
    $this->excel->getActiveSheet()->setCellValue("Q{$row}",'มูลค่า VAT');
    $this->excel->getActiveSheet()->setCellValue("R{$row}",'เฉลี่ยส่วนลดท้ายบิล/บาท');
    $this->excel->getActiveSheet()->setCellValue("S{$row}",'เฉลี่ยส่วนลดท้ายบิล * มูลค่า');
    $this->excel->getActiveSheet()->setCellValue("T{$row}",'มูลค่าหลังส่วนลดท้ายบิล');
    $this->excel->getActiveSheet()->setCellValue("U{$row}",'BaseType');
    $this->excel->getActiveSheet()->setCellValue("V{$row}",'BaseRef');
    $this->excel->getActiveSheet()->setCellValue("W{$row}",'so code');
    $this->excel->getActiveSheet()->setCellValue("X{$row}",'พนักงานขาย');
    $this->excel->getActiveSheet()->setCellValue("Y{$row}",'คลังสินค้า');
    $this->excel->getActiveSheet()->setCellValue("Z{$row}",'ช่องทางขาย');
    $this->excel->getActiveSheet()->setCellValue("AA{$row}",'การชำระเงิน');
    $this->excel->getActiveSheet()->setCellValue("AB{$row}",'รหัสลูกค้า');
    $this->excel->getActiveSheet()->setCellValue("AC{$row}",'ชื่อลูกค้า');
    $this->excel->getActiveSheet()->setCellValue("AD{$row}",'อ้างอิงลูกค้า');

    $row++;

    $pmRole = array(
      '1' => 'เงินสด',
      '2' => 'เงินโอน',
      '3' => 'บัตรเครดิต',
      '4' => 'เก็บเงินปลายทาง',
      '5' => 'เครดิต',
      '6' => 'หลายช่องทาง',
      '7' => 'เช็ค'
    );

    $chList = channels_array();
    $salemanList = saleman_array();

    if( ! empty($details))
    {
      $no = 1;

      foreach($details as $rs)
      {
        //--- ลำดับ
        $this->excel->getActiveSheet()->setCellValue('A'.$row, $no);

        //--- วันที่เอกสาร
        $this->excel->getActiveSheet()->setCellValue('B'.$row, $rs->DocDate);

        //--- วันที่บันทึกขาย
        $this->excel->getActiveSheet()->setCellValue('C'.$row, $rs->shipped_date);

        //--- เลขที่ invoice
        $this->excel->getActiveSheet()->setCellValue('D'.$row, $rs->invoice_code);

        //--- เล่มเอกสาร
        $this->excel->getActiveSheet()->setCellValue('E'.$row, ($rs->bookcode == 'C' ? 'เงินสด' : ($rs->bookcode == 'T' ? 'เครดิต' : 'POS')));

        //--- เครดิต
        $this->excel->getActiveSheet()->setCellValue('F'.$row, $rs->is_term == 1 ? 'Y' : 'N');

        //--- รหัสสินค้า
        $this->excel->getActiveSheet()->setCellValue('G'.$row, $rs->ItemCode);

        //--- ชื่อสินค้า
        $this->excel->getActiveSheet()->setCellValue('H'.$row, $rs->Dscription);

        //--- จำนวน
        $this->excel->getActiveSheet()->setCellValue('I'.$row, $rs->Qty);

        //--- ราคาก่อนส่วนลด ไม่รวมภาษี
        $this->excel->getActiveSheet()->setCellValue('J'.$row, $rs->PriceBefDi);

        //--- ส่วนลดรายการ (%)
        $this->excel->getActiveSheet()->setCellValue('K'.$row, round($rs->DiscPrcnt, 2) . ' %');

        //--- ราคาขายหลังส่วนลดรายการ ไม่รวมภาษี
        $this->excel->getActiveSheet()->setCellValue('L'.$row, $rs->Price);

        //--- ราคาขายหลังส่วนลดรวมภาษี
        $this->excel->getActiveSheet()->setCellValue('M'.$row, $rs->PriceAfVAT);

        //--- มูลค่ารวม
        $this->excel->getActiveSheet()->setCellValue('N'.$row, $rs->LineTotal);

        //--- ชนิดภาษี
        $this->excel->getActiveSheet()->setCellValue('O'.$row, $rs->VatType == 'E' ? 'แยกนอก' : 'รวมใน');

        //--- อัตราภาษี
        $this->excel->getActiveSheet()->setCellValue('P'.$row, $rs->VatRate);

        //--- มูลค่าภาษี
        $this->excel->getActiveSheet()->setCellValue('Q'.$row, $rs->VatSum);

        //--- เฉลี่ยส่วนลดท้ายบิล/บาท
        $this->excel->getActiveSheet()->setCellValue('R'.$row, $rs->avgBillDiscAmount);

        //--- เฉลี่ยส่วนลดท้ายบิลรวมตามมูล่ค่า
        $this->excel->getActiveSheet()->setCellValue('S'.$row, $rs->sumBillDiscAmount);

        //--- มูลค่าหลังส่วนลดท้ายบิล
        $this->excel->getActiveSheet()->setCellValue('T'.$row, $rs->LineTotal - $rs->sumBillDiscAmount);

        //--- ชนิดเอกสาร
        $this->excel->getActiveSheet()->setCellValue('U'.$row, $rs->BaseType);

        //--- อ้างอิงเอกสาร
        $this->excel->getActiveSheet()->setCellValue('V'.$row, $rs->BaseRef);

        //--- ใบสั่งซื้อ
        $this->excel->getActiveSheet()->setCellValue('W'.$row, $rs->so_code);

        //--- พนักงานขาย
        $this->excel->getActiveSheet()->setCellValue('X'.$row, empty($salemanList[$rs->SlpCode]) ? $rs->SlpCode : $salemanList[$rs->SlpCode]);

        //--- คลัง
        $this->excel->getActiveSheet()->setCellValue('Y'.$row, $rs->WhsCode);

        //--- ช่องทางขาย
        $this->excel->getActiveSheet()->setCellValue('Z'.$row, empty($chList[$rs->channels_code]) ? $rs->channels_code : $chList[$rs->channels_code]);

        //--- การชำระเงิน
        $this->excel->getActiveSheet()->setCellValue('AA'.$row, $rs->is_term ? $pmRole[5] : $pmRole[$rs->payment_role]);

        //--- รหัสลูกค้า
        $this->excel->getActiveSheet()->setCellValue('AB'.$row, $rs->CardCode);

        //--- ชื่อลูกค้า
        $this->excel->getActiveSheet()->setCellValue('AC'.$row, $rs->CardName);

        //--- ชื่อลูกค้า
        $this->excel->getActiveSheet()->setCellValue('AD'.$row, $rs->NumAtCard);

        $no++;
        $row++;
      }
    }


    setToken($token);
    $file_name = "Report Sales Analyze.xlsx";
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'); /// form excel 2007 XLSX
    header('Content-Disposition: attachment;filename="'.$file_name.'"');
    $writer = PHPExcel_IOFactory::createWriter($this->excel, 'Excel2007');
    $writer->save('php://output');

  }

} //--- end class








 ?>
