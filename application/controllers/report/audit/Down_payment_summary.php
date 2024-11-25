<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Down_payment_summary extends PS_Controller
{
  public $menu_code = 'RADPSM';
	public $menu_group_code = 'RE';
  public $menu_sub_group_code = 'RESALE';
	public $title = 'รายงานรายงานสรุปยอดรับเงินมัดจำ';
  public $filter;
  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'report/audit/down_payment_summary';
    $this->load->model('report/audit/downpayment_report_model');
    $this->load->model('masters/shop_model');
    $this->load->model('masters/pos_model');
    $this->load->helper('shop');
  }

  public function index()
  {
    $this->load->view('report/audit/report_down_payment_summary');
  }


  public function get_report()
  {
    $sc = TRUE;
    $ds = array();
    $filter = json_decode($this->input->get('filter'));

    if( ! empty($filter))
    {
      if($filter->group_by == 'doc')
      {
        $arr = array(
          'from_date' => $filter->fromDate,
          'to_date' => $filter->toDate,
          'shop_id' => $filter->shop_id,
          'pos_id' => $filter->pos_id
        );

        $result = $this->downpayment_report_model->get_list($arr);

        if( ! empty($result))
        {
          $no = 1;

          foreach($result as $rs)
          {
            $row = array(
              'no' => $no,
              'date_add' => thai_date($rs->date_add),
              'code' => $rs->code,
              'so_code' => $rs->reference,
              'bill_code' => $rs->ref_code,
              'amount' => number($rs->amount, 2),
              'cash_amount' => $rs->payment_role == 1 ? number($rs->amount, 2) : 0,
              'transfer_amount' => $rs->payment_role == 2 ? number($rs->amount, 2) : 0,
              'card_amount' => $rs->payment_role == 3 ? number($rs->amount, 2) : 0,
              'shop_name' => $rs->shop_name,
              'pos_name' => $rs->pos_name
            );

            if($rs->payment_role == 6)
            {
              $payments = $this->downpayment_report_model->get_payments($rs->code);

              if( ! empty($payments))
              {
                foreach($payments as $pm)
                {
                  switch ($pm->payment_role) {
                    case '1':
                      $row['cash_amount'] = number($pm->amount, 2);
                      break;
                    case '2' :
                      $row['transfer_amount'] = number($pm->amount, 2);
                      break;
                    case '3' :
                      $row['card_amount'] = number($pm->amount, 2);

                    default:
                      // code...
                      break;
                  }
                }
              }
            }

            array_push($ds, $row);
            $no++;
          }
        }
      }
      else
      {
        $dates = date_to_array($filter->fromDate, $filter->toDate, 'Y-m-d'); //--- date_helper

        if( ! empty($dates))
        {
          $no = 1;

          foreach($dates as $date)
          {
            $row = array(
              'no' => $no,
              'date_add' => thai_date($date),
              'amount' => number($this->downpayment_report_model->get_summary_by_date($date, $filter->shop_id, $filter->pos_id), 2),
              'cash_amount' => number($this->downpayment_report_model->get_summary_by_date_and_payment_role($date, 1, $filter->shop_id, $filter->pos_id), 2),
              'transfer_amount' => number($this->downpayment_report_model->get_summary_by_date_and_payment_role($date, 2, $filter->shop_id, $filter->pos_id), 2),
              'card_amount' => number($this->downpayment_report_model->get_summary_by_date_and_payment_role($date, 3, $filter->shop_id, $filter->pos_id), 2)
            );

            array_push($ds, $row);
            $no++;
          }
        }
        else
        {
          $sc = FALSE;
          $this->error = "Invalid date format";
        }
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
    $excel->getActiveSheet()->setTitle('Down payment report');

    $from_date = $this->input->post('fromDate');
    $to_date = $this->input->post('toDate');
    $shop_id = $this->input->post('shop_id');
    $pos_id = $this->input->post('pos_id');
    $group_by = $this->input->post('group_by');
    $token = $this->input->post('token');

    if( ! empty($from_date) && ! empty($to_date))
    {
      if($group_by == 'doc')
      {
        $row = 1;
        $excel->getActiveSheet()->setCellValue("A{$row}", '#');
        $excel->getActiveSheet()->setCellValue("B{$row}", "วันที่");
        $excel->getActiveSheet()->setCellValue("C{$row}", "เลขที่");
        $excel->getActiveSheet()->setCellValue("D{$row}", "ใบสั่งขาย");
        $excel->getActiveSheet()->setCellValue("E{$row}", "บิลขาย");
        $excel->getActiveSheet()->setCellValue("F{$row}", "เงินสด");
        $excel->getActiveSheet()->setCellValue("G{$row}", "เงินโอน");
        $excel->getActiveSheet()->setCellValue("H{$row}", "บัตรเครดิต");
        $excel->getActiveSheet()->setCellValue("I{$row}", "ยอดรวม");
        $excel->getActiveSheet()->setCellValue("J{$row}", "จุดขาย");
        $excel->getActiveSheet()->setCellValue("K{$row}", "เครื่อง POS");

        $row++;

        $arr = array(
          'from_date' => $from_date,
          'to_date' => $to_date,
          'shop_id' => $shop_id,
          'pos_id' => $pos_id
        );

        $result = $this->downpayment_report_model->get_list($arr);

        if( ! empty($result))
        {
          $no = 1;

          foreach($result as $rs)
          {

            $excel->getActiveSheet()->setCellValue("A{$row}", $no);
            $excel->getActiveSheet()->setCellValue("B{$row}", thai_date($rs->date_add, FALSE, '/'));
            $excel->getActiveSheet()->setCellValue("C{$row}", $rs->code);
            $excel->getActiveSheet()->setCellValue("D{$row}", $rs->reference);
            $excel->getActiveSheet()->setCellValue("E{$row}", $rs->ref_code);
            $excel->getActiveSheet()->setCellValue("F{$row}", ($rs->payment_role == 1 ? $rs->amount : 0));
            $excel->getActiveSheet()->setCellValue("G{$row}", ($rs->payment_role == 2 ? $rs->amount : 0));
            $excel->getActiveSheet()->setCellValue("H{$row}", ($rs->payment_role == 3 ? $rs->amount : 0));
            $excel->getActiveSheet()->setCellValue("I{$row}", $rs->amount);
            $excel->getActiveSheet()->setCellValue("J{$row}", $rs->shop_name);
            $excel->getActiveSheet()->setCellValue("K{$row}", $rs->pos_name);

            if($rs->payment_role == 6)
            {
              $payments = $this->downpayment_report_model->get_payments($rs->code);

              if( ! empty($payments))
              {
                foreach($payments as $pm)
                {
                  switch ($pm->payment_role) {
                    case '1':
                      $excel->getActiveSheet()->setCellValue("F{$row}", $pm->amount);
                      break;
                    case '2' :
                      $excel->getActiveSheet()->setCellValue("G{$row}", $pm->amount);
                      break;
                    case '3' :
                      $excel->getActiveSheet()->setCellValue("H{$row}", $pm->amount);

                    default:
                      // code...
                      break;
                  }
                }
              }
            }

            $row++;
            $no++;
          }
        }
      }
      else
      {
        $no = 1;
        $row = 1;

        $excel->getActiveSheet()->setCellValue("A{$row}", '#');
        $excel->getActiveSheet()->setCellValue("B{$row}", "วันที่");
        $excel->getActiveSheet()->setCellValue("C{$row}", "เงินสด");
        $excel->getActiveSheet()->setCellValue("D{$row}", "เงินโอน");
        $excel->getActiveSheet()->setCellValue("E{$row}", "บัตรเครดิต");
        $excel->getActiveSheet()->setCellValue("F{$row}", "ยอดรวม");

        $row++;

        $dates = date_to_array($from_date, $to_date, 'Y-m-d'); //--- date_helper

        if( ! empty($dates))
        {
          foreach($dates as $date)
          {
            $excel->getActiveSheet()->setCellValue("A{$row}", $no);
            $excel->getActiveSheet()->setCellValue("B{$row}", thai_date($date, FALSE, '/'));
            $excel->getActiveSheet()->setCellValue("C{$row}", $this->downpayment_report_model->get_summary_by_date_and_payment_role($date, 1, $shop_id, $pos_id));
            $excel->getActiveSheet()->setCellValue("D{$row}", $this->downpayment_report_model->get_summary_by_date_and_payment_role($date, 2, $shop_id, $pos_id));
            $excel->getActiveSheet()->setCellValue("E{$row}", $this->downpayment_report_model->get_summary_by_date_and_payment_role($date, 3, $shop_id, $pos_id));
            $excel->getActiveSheet()->setCellValue("F{$row}", $this->downpayment_report_model->get_summary_by_date($date, $shop_id, $pos_id));
            $row++;
            $no++;
          }
        }
        else
        {
          $excel->getActiveSheet()->setCellValue("A{$row}", "Invalid date format");
        }
      }
    }

    setToken($token);
    $file_name = "รายงานสรุปยอดรับมัดจำ ".date('Ymd').".xlsx";
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'); /// form excel 2007 XLSX
    header('Content-Disposition: attachment;filename="'.$file_name.'"');
    $writer = PHPExcel_IOFactory::createWriter($excel, 'Excel2007');
    $writer->save('php://output');

  } //--- do export



} //--- end class
?>
