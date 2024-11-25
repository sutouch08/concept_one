<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Down_payment_details extends PS_Controller
{
  public $menu_code = 'RADPDT';
	public $menu_group_code = 'RE';
  public $menu_sub_group_code = 'RESALE';
	public $title = 'รายงานรายละเอียดเงินมัดจำ';
  public $filter;
  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'report/audit/down_payment_details';
    $this->load->model('report/audit/downpayment_report_model');
    $this->load->model('masters/shop_model');
    $this->load->model('masters/pos_model');
    $this->load->helper('shop');
  }

  public function index()
  {
    $ds['shopList'] = $this->shop_model->get_all();
    $this->load->view('report/audit/report_down_payment_details', $ds);
  }


  public function get_report()
  {
    $sc = TRUE;
    $ds = array();
    $filter = json_decode($this->input->get('filter'));

    if( ! empty($filter))
    {
      $data = $this->downpayment_report_model->get_down_payment($filter);

      if( ! empty($data))
      {
        $no = 1;

        foreach($data as $rs)
        {
          $details = $this->downpayment_report_model->get_down_payment_details($rs->id);

          if( ! empty($details))
          {
            foreach($details as $rd)
            {
              $invoice_code = empty($rd->invoice_code) ? $this->downpayment_report_model->get_invoice_code($rd->TargetRef) : $rd->invoice_code;

              $row = array(
                'no' => $no,
                'date_add' => thai_date($rs->date_add),
                'use_date' => thai_date($rd->date_add),
                'code' => $rd->down_payment_code,
                'so_code' => empty($rd->so_code) ? $rs->reference : $rd->so_code,
                'bill_code' => $rd->TargetRef,
                'invoice_code' => $invoice_code,
                'amountBfUse' => number($rd->amountBfUse, 2),
                'usedAmount' => number($rd->amount, 2),
                'available' => number($rd->amountAfUse, 2),
                'statusLabel' => $rs->status == 'C' ? 'Closed' : 'Open',
                'color' => $rs->status == 'C' ? 'green' : '',
                'customer_code' => $rs->customer_code,
                'customer_name' => $rs->customer_name,
                'customer_ref' => $rs->customer_ref,
                'customer_phone' => $rs->customer_phone
              );

              array_push($ds, $row);
              $no++;
            }
          }
          else
          {
            $invoice_code = empty($rs->ref_code) ? "" : $this->downpayment_report_model->get_invoice_code($rs->ref_code);

            $row = array(
              'no' => $no,
              'date_add' => thai_date($rs->date_add),
              'use_date' => NULL,
              'code' => $rs->code,
              'so_code' => $rs->reference,
              'bill_code' => $rs->ref_code,
              'invoice_code' => $invoice_code,
              'amountBfUse' => number($rs->amount, 2),
              'usedAmount' => number($rs->used, 2),
              'available' => number($rs->available, 2),
              'statusLabel' => $rs->status == 'C' ? 'Closed' : 'Open',
              'color' => $rs->status == 'C' ? 'green' : '',
              'customer_code' => $rs->customer_code,
              'customer_name' => $rs->customer_name,
              'customer_ref' => $rs->customer_ref,
              'customer_phone' => $rs->customer_phone
            );

            array_push($ds, $row);

            $no++;
          }
        } //--- end foreach data
      }
    }
    else
    {
      $sc = FALSE;
      $this->error = "Missing required parameter";
    }

    $arr = array(
      'status' => $sc === TRUE ? 'success' : 'failed',
      'message' => $sc === TRUE ? 'success' : $this->error,
      'data' => $sc === TRUE ? $ds : NULL
    );

    echo json_encode($arr);
  }




  public function do_export()
  {

    //--- load excel library
    $this->load->library('excel');
    $excel = $this->excel;

    $excel->setActiveSheetIndex(0);
    $excel->getActiveSheet()->setTitle('Down payment Details');

    $filter = new stdClass();

    $filter->fromDate = $this->input->post('fromDate');
    $filter->toDate = $this->input->post('toDate');
    $filter->code = trim($this->input->post('code'));
    $filter->customer_code = trim($this->input->post('customer_code'));
    $filter->customer_name = trim($this->input->post('customer_name'));
    $filter->phone = trim($this->input->post('phone'));
    $filter->reference = trim($this->input->post('reference'));
    $filter->status = $this->input->post('status');

    $token = $this->input->post('token');

    $data = $this->downpayment_report_model->get_down_payment($filter);

    $excel->getActiveSheet()->getColumnDimension('A')->setWidth(8);
    $excel->getActiveSheet()->getColumnDimension('B')->setWidth(12);
    $excel->getActiveSheet()->getColumnDimension('C')->setWidth(12);
    $excel->getActiveSheet()->getColumnDimension('D')->setWidth(15);
    $excel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
    $excel->getActiveSheet()->getColumnDimension('F')->setWidth(15);
    $excel->getActiveSheet()->getColumnDimension('G')->setWidth(15);
    $excel->getActiveSheet()->getColumnDimension('H')->setWidth(15);
    $excel->getActiveSheet()->getColumnDimension('I')->setWidth(15);
    $excel->getActiveSheet()->getColumnDimension('J')->setWidth(15);
    $excel->getActiveSheet()->getColumnDimension('K')->setWidth(10);
    $excel->getActiveSheet()->getColumnDimension('L')->setWidth(15);
    $excel->getActiveSheet()->getColumnDimension('M')->setWidth(20);
    $excel->getActiveSheet()->getColumnDimension('N')->setWidth(20);
    $excel->getActiveSheet()->getColumnDimension('O')->setWidth(40);

    $row = 1;


    $excel->getActiveSheet()->setCellValue("A{$row}", '#');
    $excel->getActiveSheet()->setCellValue("B{$row}", "วันที่รับ");
    $excel->getActiveSheet()->setCellValue("C{$row}", "วันที่ใช้");
    $excel->getActiveSheet()->setCellValue("D{$row}", "เลขที่");
    $excel->getActiveSheet()->setCellValue("E{$row}", "อ้างอิง");
    $excel->getActiveSheet()->setCellValue("F{$row}", "บิลขาย");
    $excel->getActiveSheet()->setCellValue("G{$row}", "ใบกำกับ");
    $excel->getActiveSheet()->setCellValue("H{$row}", "ก่อนใช้");
    $excel->getActiveSheet()->setCellValue("I{$row}", "ใช้ไป");
    $excel->getActiveSheet()->setCellValue("J{$row}", "คงเหลือ");
    $excel->getActiveSheet()->setCellValue("K{$row}", "สถานะ");
    $excel->getActiveSheet()->setCellValue("L{$row}", "รหัสลูกค้า");
    $excel->getActiveSheet()->setCellValue("M{$row}", "ชื่อลูกค้า");
    $excel->getActiveSheet()->setCellValue("N{$row}", "เบอร์โทร");
    $excel->getActiveSheet()->setCellValue("O{$row}", "อ้างอิงลูกค้า");

    $excel->getActiveSheet()->getStyle("A{$row}:O{$row}")->getAlignment()->setHorizontal('center');

    $row++;

    if( ! empty($data))
    {
      $no = 1;

      foreach($data as $rs)
      {
        $details = $this->downpayment_report_model->get_down_payment_details($rs->id);

        if( ! empty($details))
        {
          foreach($details as $rd)
          {
            $invoice_code = empty($rd->invoice_code) ? $this->downpayment_report_model->get_invoice_code($rd->TargetRef) : $rd->invoice_code;

            $excel->getActiveSheet()->setCellValue("A{$row}", $no);
            $excel->getActiveSheet()->setCellValue("B{$row}", thai_date($rs->date_add, FALSE, '/'));
            $excel->getActiveSheet()->setCellValue("C{$row}", thai_date($rd->date_add, FALSE, '/'));
            $excel->getActiveSheet()->setCellValue("D{$row}", $rd->down_payment_code);
            $excel->getActiveSheet()->setCellValue("E{$row}", empty($rd->so_code) ? $rs->reference : $rd->so_code);
            $excel->getActiveSheet()->setCellValue("F{$row}", $rd->TargetRef);
            $excel->getActiveSheet()->setCellValue("G{$row}", $invoice_code);
            $excel->getActiveSheet()->setCellValue("H{$row}", round($rd->amountBfUse, 2));
            $excel->getActiveSheet()->setCellValue("I{$row}", round($rd->amount, 2));
            $excel->getActiveSheet()->setCellValue("J{$row}", round($rd->amountAfUse, 2));
            $excel->getActiveSheet()->setCellValue("K{$row}", $rs->status == 'C' ? 'Closed' : 'Open');
            $excel->getActiveSheet()->setCellValue("L{$row}", $rs->customer_code);
            $excel->getActiveSheet()->setCellValue("M{$row}", $rs->customer_name);
            $excel->getActiveSheet()->setCellValue("N{$row}", $rs->customer_phone);
            $excel->getActiveSheet()->setCellValue("O{$row}", $rs->customer_ref);

            $no++;
            $row++;
          }
        }
        else
        {
          $invoice_code = empty($rs->ref_code) ? "" : $this->downpayment_report_model->get_invoice_code($rs->ref_code);

          $excel->getActiveSheet()->setCellValue("A{$row}", $no);
          $excel->getActiveSheet()->setCellValue("B{$row}", thai_date($rs->date_add, FALSE, '/'));
          $excel->getActiveSheet()->setCellValue("C{$row}", NULL);
          $excel->getActiveSheet()->setCellValue("D{$row}", $rs->code);
          $excel->getActiveSheet()->setCellValue("E{$row}", $rs->reference);
          $excel->getActiveSheet()->setCellValue("F{$row}", $rs->ref_code);
          $excel->getActiveSheet()->setCellValue("G{$row}", $invoice_code);
          $excel->getActiveSheet()->setCellValue("H{$row}", round($rs->amount, 2));
          $excel->getActiveSheet()->setCellValue("I{$row}", round($rs->used, 2));
          $excel->getActiveSheet()->setCellValue("J{$row}", round($rs->available, 2));
          $excel->getActiveSheet()->setCellValue("K{$row}", $rs->status == 'C' ? 'Closed' : 'Open');
          $excel->getActiveSheet()->setCellValue("L{$row}", $rs->customer_code);
          $excel->getActiveSheet()->setCellValue("M{$row}", $rs->customer_name);
          $excel->getActiveSheet()->setCellValue("N{$row}", $rs->customer_phone);
          $excel->getActiveSheet()->setCellValue("O{$row}", $rs->customer_ref);

          $no++;
          $row++;
        }
      }

      $excel->getActiveSheet()->getStyle("H2:J{$row}")->getNumberFormat()->setFormatCode('#,##0.00');
      $excel->getActiveSheet()->getStyle("A2:G{$row}")->getAlignment()->setHorizontal('center');
    }

    setToken($token);
    $file_name = "รายงานรายละเอียดเงินมัดจำ ".date('Ymd').".xlsx";
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'); /// form excel 2007 XLSX
    header('Content-Disposition: attachment;filename="'.$file_name.'"');
    $writer = PHPExcel_IOFactory::createWriter($excel, 'Excel2007');
    $writer->save('php://output');

  } //--- do export



} //--- end class
?>
