<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Receive_po_by_doc extends PS_Controller
{
  public $menu_code = 'RPURPO';
	public $menu_group_code = 'RE';
  public $menu_sub_group_code = 'REPO';
	public $title = 'รายงาน การรับสินค้าแยกตามเอกสาร';
  public $filter;
  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'report/purchase/receive_po_by_doc';
    $this->load->model('report/purchase/receive_po_by_doc_model');
  }

  public function index()
  {
    $this->load->view('report/purchase/report_receive_po_by_doc');
  }


  public function get_report()
  {
    $sc = array();

    $allDoc = $this->input->get('allDoc');
    $docFrom = $this->input->get('docFrom');
    $docTo = $this->input->get('docTo');

    if($docFrom > $docTo){
      $sp = $docTo;
      $docTo = $docFrom;
      $docFrom = $sp;
    }

    $fromDate = $this->input->get('fromDate');
    $toDate = $this->input->get('toDate');

    $allVendor = $this->input->get('allVendor');
    $vendorFrom = $this->input->get('vendorFrom');
    $vendorTo = $this->input->get('vendorTo');

    if($vendorFrom > $vendorTo){
      $sp = $vendorTo;
      $vendorTo = $vendorFrom;
      $vendorFrom = $sp;
    }

    $allPO = $this->input->get('allPO');
    $poFrom = $this->input->get('poFrom');
    $poTo = $this->input->get('poTo');

    if($poFrom > $poTo){
      $sp = $poTo;
      $poTo = $poFrom;
      $poFrom = $sp;
    }

    $allInvoice = $this->input->get('allInvoice');
    $invoiceFrom = $this->input->get('invoiceFrom');
    $invoiceTo = $this->input->get('invoiceTo');

    if($invoiceFrom > $invoiceTo){
      $sp = $invoiceTo;
      $invoiceTo = $invoiceFrom;
      $invoiceFrom = $sp;
    }

    $arr = array(
      'allDoc' => $allDoc,
      'docFrom' => $docFrom,
      'docTo' => $docTo,
      'allVendor' => $allVendor,
      'vendorFrom' => $vendorFrom,
      'vendorTo' => $vendorTo,
      'allPO' => $allPO,
      'poFrom' => $poFrom,
      'poTo' => $poTo,
      'allInvoice' => $allInvoice,
      'invoiceFrom' => $invoiceFrom,
      'invoiceTo' => $invoiceTo,
      'fromDate' => from_date($fromDate),
      'toDate' => to_date($toDate)
    );

    $result = $this->receive_po_by_doc_model->get_data($arr);

    if(!empty($result))
    {
      $no = 1;
      $totalQty = 0;
      $totalAmount = 0;
      foreach($result as $rs)
      {

        $ds = array(
          'no' => number($no),
          'date' => thai_date($rs->date_add, FALSE, '/'),
          'code' => $rs->code,
          'vendor' => $rs->vendor_code.' : '.$rs->vendor_name,
          'invoice' => $rs->invoice_code,
          'po' => $rs->po_code,
          'sapNo' => $rs->inv_code,
          'qty' => number($rs->qty),
          'amount' => number($rs->amount, 2)
        );

        array_push($sc, $ds);

        $no++;
        $totalQty += $rs->qty;
        $totalAmount += $rs->amount;

      }

      $ds = array(
        'totalQty' => number($totalQty),
        'totalAmount' => number($totalAmount, 2)
      );

      array_push($sc, $ds);
    }
    else
    {
      $arr = array('nodata' => 'nodata');
      array_push($sc, $arr);
    }

    echo json_encode($sc);
  }





  public function do_export()
  {
    $token = $this->input->post('token');
    $allDoc = $this->input->post('allDoc');
    $docFrom = $this->input->post('docFrom');
    $docTo = $this->input->post('docTo');

    if($docFrom > $docTo){
      $sp = $docTo;
      $docTo = $docFrom;
      $docFrom = $sp;
    }

    $fromDate = $this->input->post('fromDate');
    $toDate = $this->input->post('toDate');

    $allVendor = $this->input->post('allVendor');
    $vendorFrom = $this->input->post('vendorFrom');
    $vendorTo = $this->input->post('vendorTo');

    if($vendorFrom > $vendorTo){
      $sp = $vendorTo;
      $vendorTo = $vendorFrom;
      $vendorFrom = $sp;
    }

    $allPO = $this->input->post('allPO');
    $poFrom = $this->input->post('poFrom');
    $poTo = $this->input->post('poTo');

    if($poFrom > $poTo){
      $sp = $poTo;
      $poTo = $poFrom;
      $poFrom = $sp;
    }

    $allInvoice = $this->input->post('allInvoice');
    $invoiceFrom = $this->input->post('invoiceFrom');
    $invoiceTo = $this->input->post('invoiceTo');

    if($invoiceFrom > $invoiceTo){
      $sp = $invoiceTo;
      $invoiceTo = $invoiceFrom;
      $invoiceFrom = $sp;
    }

    $title = "รายงาน การรับสินค้า แยกตามเลขที่เอกสาร วันที่ (".thai_date($fromDate, FALSE, '/').") - (".thai_date($toDate, FALSE, '/').")";
    $document = $allDoc == 1 ? 'ทั้งหมด' : "{$docFrom} - {$docTo}";
    $vendor = $allVendor == 1 ? 'ทั้งหมด' : "{$vendorFrom} - {$vendorTo}";
    $po = $allPO == 1 ? 'ทั้งหมด' : "{$poFrom} - {$poTo}";
    $invoice = $allInvoice == 1 ? 'ทั้งหมด' : "{$invoiceFrom} - {$invoiceTo}";

    $arr = array(
      'allDoc' => $allDoc,
      'docFrom' => $docFrom,
      'docTo' => $docTo,
      'allVendor' => $allVendor,
      'vendorFrom' => $vendorFrom,
      'vendorTo' => $vendorTo,
      'allPO' => $allPO,
      'poFrom' => $poFrom,
      'poTo' => $poTo,
      'allInvoice' => $allInvoice,
      'invoiceFrom' => $invoiceFrom,
      'invoiceTo' => $invoiceTo,
      'fromDate' => from_date($fromDate),
      'toDate' => to_date($toDate)
    );

    $result = $this->receive_po_by_doc_model->get_data($arr);

    //--- load excel library
    $this->load->library('excel');

    $this->excel->setActiveSheetIndex(0);
    $this->excel->getActiveSheet()->setTitle('Receive PO BY Document');

    //--- set report title header
    $this->excel->getActiveSheet()->setCellValue('A1', $title);
    $this->excel->getActiveSheet()->mergeCells('A1:I1');
    $this->excel->getActiveSheet()->setCellValue('A2', "เลขที่เอกสาร : {$document}");
    $this->excel->getActiveSheet()->mergeCells('A2:I2');
    $this->excel->getActiveSheet()->setCellValue('A3', "รหัสผู้ขาย : {$vendor}");
    $this->excel->getActiveSheet()->mergeCells('A3:I3');
    $this->excel->getActiveSheet()->setCellValue('A4', "ใบสั่งซื้อ : {$po}");
    $this->excel->getActiveSheet()->mergeCells('A4:I4');
    $this->excel->getActiveSheet()->setCellValue('A5', "ใบส่งของ : {$invoice}");
    $this->excel->getActiveSheet()->mergeCells('A5:I5');

    //--- set Table header
    $this->excel->getActiveSheet()->setCellValue('A6', 'ลำดับ');
    $this->excel->getActiveSheet()->setCellValue('B6', 'วันที่');
    $this->excel->getActiveSheet()->setCellValue('C6', 'เลขที่เอกสาร');
    $this->excel->getActiveSheet()->setCellValue('D6', 'ใบสั่งซื้อ');
    $this->excel->getActiveSheet()->setCellValue('E6', 'ใบส่งของ');
    $this->excel->getActiveSheet()->setCellValue('F6', 'SAP No.');
    $this->excel->getActiveSheet()->setCellValue('G6', 'ผู้ขาย');
    $this->excel->getActiveSheet()->setCellValue('H6', 'จำนวน');
    $this->excel->getActiveSheet()->setCellValue('I6', 'มูลค่า');

    $row = 7;
    if(!empty($result))
    {
      $no = 1;
      $totalQty = 0;
      $totalAmount = 0;
      foreach($result as $rs)
      {
        $this->excel->getActiveSheet()->setCellValue('A'.$row, $no);
        $this->excel->getActiveSheet()->setCellValue('B'.$row, thai_date($rs->date_add, FALSE, '/'));
        $this->excel->getActiveSheet()->setCellValue('C'.$row, $rs->code);
        $this->excel->getActiveSheet()->setCellValue('D'.$row, $rs->po_code);
        $this->excel->getActiveSheet()->setCellValue('E'.$row, $rs->invoice_code);
        $this->excel->getActiveSheet()->setCellValue('F'.$row, $rs->inv_code); //--- SAP No.
        $this->excel->getActiveSheet()->setCellValue('G'.$row, $rs->vendor_code.' : '.$rs->vendor_name);
        $this->excel->getActiveSheet()->setCellValue('H'.$row, $rs->qty);
        $this->excel->getActiveSheet()->setCellValue('I'.$row, $rs->amount);
        $totalQty += $rs->qty;
        $totalAmount += $rs->amount;
        $no++;
        $row++;
      }



      $this->excel->getActiveSheet()->setCellValue('A'.$row, 'รวม');
      $this->excel->getActiveSheet()->mergeCells('A'.$row.':G'.$row);
      $this->excel->getActiveSheet()->setCellValue('H'.$row, $totalQty);
      $this->excel->getActiveSheet()->setCellValue('I'.$row, $totalAmount);

      $this->excel->getActiveSheet()->getStyle('A'.$row)->getAlignment()->setHorizontal('right');
      $this->excel->getActiveSheet()->getStyle('D6:D'.$row)->getNumberFormat()->setFormatCode('0');
      $this->excel->getActiveSheet()->getStyle('F6:F'.$row)->getNumberFormat()->setFormatCode('0');
      $this->excel->getActiveSheet()->getStyle('H6:I'.$row)->getAlignment()->setHorizontal('right');
      $this->excel->getActiveSheet()->getStyle('H6:H'.$row)->getNumberFormat()->setFormatCode('#,##0');
      $this->excel->getActiveSheet()->getStyle('I6:I'.$row)->getNumberFormat()->setFormatCode('#,##0.00');
    }

    setToken($token);
    $file_name = "รายงานการรับสินค้าแยกตามเอกสาร.xlsx";
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'); /// form excel 2007 XLSX
    header('Content-Disposition: attachment;filename="'.$file_name.'"');
    $writer = PHPExcel_IOFactory::createWriter($this->excel, 'Excel2007');
    $writer->save('php://output');

  }


} //--- end class








 ?>
