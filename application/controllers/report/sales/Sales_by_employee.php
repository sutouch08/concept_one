<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Sales_by_employee extends PS_Controller
{
  public $menu_code = 'RSBYEMP';
	public $menu_group_code = 'RE';
  public $menu_sub_group_code = 'RESALE';
	public $title = 'รายงานยอดขายแยกตามพนักงานขาย';
  public $filter;

  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'report/sales/sales_by_employee';
    $this->load->model('report/sales/sales_report_model');
    $this->load->model('masters/slp_model');
  }

  public function index()
  {
    $slpList = $this->slp_model->get_data();
    $ds['slpList'] = $slpList;
    $this->load->view('report/sales/report_sales_by_employee', $ds);
  }


  public function get_report()
  {
    $sc = TRUE;

    $filter = json_decode($this->input->post('filter'));
    $ds = array();
    $totalAmount = 0;
    $no = 1;

    if( ! empty($filter))
    {
      if($filter->allSlp == 1)
      {
        $slpList = $this->slp_model->get_data();

        if( ! empty($slpList))
        {
          foreach($slpList as $slp)
          {
            $amount = $this->sales_report_model->get_invoice_summary_by_saleman($slp->id, $filter);

            $arr = array(
              'no' => $no,
              'name' => $slp->name,
              'amount' => number($amount, 2)
            );

            array_push($ds, $arr);

            $totalAmount += $amount;
            $no++;
          }
        }
      }

      if($filter->allSlp == 0 && ! empty($filter->slpList))
      {
        foreach($filter->slpList as $id)
        {
          $slp = $this->slp_model->get($id);

          if( ! empty($slp))
          {
            $amount = $this->sales_report_model->get_invoice_summary_by_saleman($slp->id, $filter);

            $arr = array(
              'no' => $no,
              'name' => $slp->name,
              'amount' => number($amount, 2)
            );

            array_push($ds, $arr);

            $totalAmount += $amount;
            $no++;
          }
        }
      }

      $arr = array(        
        'totalAmount' => number($totalAmount, 2)
      );

      array_push($ds, $arr);
    }
    else
    {
      $sc = FALSE;
      $this->error = "Missing required parameter";
    }

    echo $sc === TRUE ? json_encode($ds) : $this->error;
  }


  public function do_export()
  {
    $filter = new stdClass();
    $filter->from_date = $this->input->post('fromDate');
    $filter->to_date = $this->input->post('toDate');
    $filter->allSlp = $this->input->post('allSlp');
    $filter->slpList = $this->input->post('slp');

    $token = $this->input->post('token');

    $slpList = $filter->allSlp == 1 ? $this->slp_model->get_data() : $this->slp_model->get_in($filter->slpList);
        //---  Report title
    $report_title = 'รายงานยอดขายแยกตามพนักงานขาย วันที่ ' . thai_date($filter->from_date,'/') .' ถึง '.thai_date($filter->to_date, '/');
    $employee_title = 'พนักงานขาย : '.($filter->allSlp == 1 ? 'ทั้งหมด' : 'เฉพาะที่เลือก ('.count($slpList).')');

    //--- load excel library
    $this->load->library('excel');

    $excel = $this->excel;

    $excel->setActiveSheetIndex(0);
    $excel->getActiveSheet()->setTitle('Sales By Employee Report');

    $row = 1;
    //--- set report title header
    $excel->getActiveSheet()->setCellValue("A{$row}", $report_title);
    $excel->getActiveSheet()->mergeCells("A{$row}:D{$row}");
    $row++;
    $excel->getActiveSheet()->setCellValue("A{$row}", $employee_title);
    $excel->getActiveSheet()->mergeCells("A{$row}:D{$row}");
    $row++;
    $row++;

    $excel->getActiveSheet()->setCellValue("A{$row}", 'ลำดับ');
    $excel->getActiveSheet()->setCellValue("B{$row}", 'พนักงานขาย');
    $excel->getActiveSheet()->setCellValue("C{$row}", 'จำนวน');
    $excel->getActiveSheet()->setCellValue("D{$row}", 'ยอดเงิน');
    $row++;

    //---- กำหนดความกว้างของคอลัมภ์
    $this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(50);
    $this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
    $this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(20);

    if($filter->allSlp == 1)
    {
      if( ! empty($slpList))
      {
        $no = 1;

        foreach($slpList as $slp)
        {
          $sales = $this->sales_report_model->get_sum_total_amount_by_saleman($slp->id, $filter->from_date, $filter->to_date);

          $excel->getActiveSheet()->setCellValue("A{$row}", $no);
          $excel->getActiveSheet()->setCellValue("B{$row}", $slp->name);
          $excel->getActiveSheet()->setCellValue("C{$row}", $sales->qty);
          $excel->getActiveSheet()->setCellValue("D{$row}", $sales->amount);
          $row++;
          $no++;
        }
      }
    }

    if($filter->allSlp == 0 && ! empty($filter->slpList))
    {
      if( ! empty($slpList))
      {
        $no = 1;

        foreach($slpList as $slp)
        {
          $sales = $this->sales_report_model->get_sum_total_amount_by_saleman($slp->id, $filter->from_date, $filter->to_date);

          $excel->getActiveSheet()->setCellValue("A{$row}", $no);
          $excel->getActiveSheet()->setCellValue("B{$row}", $slp->name);
          $excel->getActiveSheet()->setCellValue("C{$row}", $sales->qty);
          $excel->getActiveSheet()->setCellValue("D{$row}", $sales->amount);
          $row++;
          $no++;
        }
      }
    }

    $re = $row - 1;
    $excel->getActiveSheet()->setCellValue("A{$row}", "รวม");
    $excel->getActiveSheet()->mergeCells("A{$row}:B{$row}");
    $excel->getActiveSheet()->setCellValue("C{$row}", "=SUM(C5:C{$re})");
    $excel->getActiveSheet()->setCellValue("D{$row}", "=SUM(D5:D{$re})");
    $excel->getActiveSheet()->getStyle("A4:D4")->getAlignment()->setHorizontal('center');
    $excel->getActiveSheet()->getStyle("A{$row}")->getAlignment()->setHorizontal('right');
    $excel->getActiveSheet()->getStyle("C5:D{$row}")->getNumberFormat()->setFormatCode('#,##0.00');

    setToken($token);
    $file_name = "Repor Sale By Employee.xlsx";
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'); /// form excel 2007 XLSX
    header('Content-Disposition: attachment;filename="'.$file_name.'"');
    $writer = PHPExcel_IOFactory::createWriter($this->excel, 'Excel2007');
    $writer->save('php://output');
  }


} //--- end class








 ?>
