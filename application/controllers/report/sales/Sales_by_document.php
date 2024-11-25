<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Sales_by_document extends PS_Controller
{
  public $menu_code = 'RSOBYDOC';
	public $menu_group_code = 'RE';
  public $menu_sub_group_code = 'RESALE';
	public $title = 'รายงานยอดขาย แยกตามเอกสาร';
  public $filter;
  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'report/sales/sales_by_document';
    $this->load->model('report/sales/sales_report_model');
    $this->load->model('masters/slp_model');
    $this->load->helper('saleman');
  }

  public function index()
  {
    $ds = array(
      'saleList' => $this->slp_model->get_all_slp()
    );

    $this->load->view('report/sales/report_sales_by_document', $ds);
  }

  public function bookcode_name($bookcode)
  {
    $arr = array(
      'C' => 'เงินสด',
      'T' => 'เครดิต',
      'P' => 'POS'
    );

    return empty($arr[$bookcode]) ? 'ไม่ระบุ' : $arr[$bookcode];
  }

  public function get_report()
  {
    $sc = TRUE;
    $ds = array();
    $option = json_decode($this->input->post('option'));

    if( ! empty($option))
    {
      $no = 1;
      $total_amount = 0; //-- doc total
      $total_slip = 0; //--- transfer slip
      $total_paid = 0; //--- pos_payment
      $total_cash = 0; //--- cash
      $total_transfer = 0;
      $total_card = 0;
      $total_cheque = 0;
      $total_down = 0; //---- downpayment
      $total_sales = 0; //--- ยอดชำระแล้ว
      $total_outstanding = 0; //--- ยอดค้างชำระ
      $total_ship_cost = 0;

      $invoices = $this->sales_report_model->get_sales_invoice($option);

      if( ! empty($invoices))
      {
        $saleman = saleman_array();

        foreach($invoices as $rs)
        {
          $paidRole = array(
            '1' => 0, //-- cache
            '2' => 0, //-- transfer
            '3' => 0, //-- card
            '7' => 0 //-- cheque
          );

          $sumPaid = 0;
          $slip = $rs->BaseType == 'WO' ? $this->getTransferSlipAmount($rs->BaseRef) : 0;
          $paid = $rs->BaseType == 'POS' ? $this->getPosRolePayment($rs->BaseRef) : 0;

          if( ! empty($paid))
          {
            foreach($paid as $pm)
            {
              $paidRole[$pm->payment_role] = $pm->amount;
              $sumPaid += $pm->amount;
            }
          }

          $docTotal = $rs->DocTotal;
          $totalPaid = $slip + $sumPaid + $rs->downPaymentAmount;
          $outstanding = $rs->DocTotal - $totalPaid;
          $outstanding = $outstanding <= 0 ? 0 : $outstanding;
          $shipCost = $this->getShipCost($rs->code);

          $arr = array(
            'no' => $no,
            'book' => $this->bookcode_name($rs->bookcode),
            'code' => $rs->code,
            'BaseRef' => $rs->BaseRef,
            'so_code' => $rs->so_code,
            'isCredit' => $rs->is_term == 1 ? 'Y' : 'N',
            'sale_name' => empty($saleman[$rs->SlpCode]) ? 'No sale employee' : $saleman[$rs->SlpCode],
            'transferSlip' => number($slip, 2),
            'cash' => number($paidRole['1'], 2),
            'transfer' => number($paidRole['2'], 2),
            'card' => number($paidRole['3'], 2),
            'cheque' => number($paidRole['7'],2),
            'paidAmount' => number($sumPaid, 2),
            'salesAmount' => number($totalPaid, 2),
            'shipCost' => number($shipCost, 2),
            'outstanding' => number($outstanding, 2),
            'downPayment' => number($rs->downPaymentAmount, 2),
            'DocTotal' => number($docTotal, 2),
            'DocDate' => thai_date($rs->DocDate),
            'shipped_date' => thai_date($rs->shipped_date)
          );

          array_push($ds, $arr);

          $no++;
          $total_amount += $docTotal;
          $total_slip += $slip;
          $total_paid += $sumPaid;
          $total_cash += $paidRole['1'];
          $total_transfer += $paidRole['2'];
          $total_card += $paidRole['3'];
          $total_cheque += $paidRole['7'];
          $total_down += $rs->downPaymentAmount;
          $total_sales += $totalPaid;
          $total_outstanding += $outstanding;
          $total_ship_cost += $shipCost;
        }

        $arr = array(
          'total_amount' => number($total_amount, 2),
          'total_slip' => number($total_slip, 2),
          'total_paid' => number($total_paid, 2),
          'total_cash' => number($total_cash, 2),
          'total_transfer' => number($total_transfer, 2),
          'total_card' => number($total_card, 2),
          'total_cheque' => number($total_cheque, 2),
          'total_down' => number($total_down, 2),
          'total_sales' => number($total_sales, 2),
          'total_outstanding' => number($total_outstanding, 2),
          'total_ship_cost' => number($total_ship_cost, 2)
        );

        array_push($ds, $arr);
      }
    }
    else
    {
      $sc = FALSE;
      $this->error = "Missing required parameter";
    }

    echo json_encode($ds);
  }

  public function getShipCost($code)
  {
    $qr = "SELECT SUM(Qty * PriceAfVAT) AS amount ";
    $qr .= "FROM invoice_details WHERE invoice_code = '{$code}' AND ItemCode = '05'";

    $rs = $this->db->query($qr);

    if($rs->num_rows() > 0)
    {
      return $rs->row()->amount;
    }

    return 0;
  }


  public function getPosRolePayment($code)
  {
    $rs = $this->db
    ->select('payment_role')
    ->select_sum('amount')
    ->where('code', $code)
    ->group_by('payment_role')
    ->get('order_pos_payment');

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }



  public function getPosPayment($code)
  {
    $rs = $this->db
    ->select_sum('amount')
    ->where('code', $code)
    ->get('order_pos_payment');

    if($rs->num_rows() === 1)
    {
      return $rs->row()->amount > 0 ? $rs->row()->amount : 0;
    }

    return 0;
  }


  public function getTransferSlipAmount($code)
  {
    $rs = $this->db
    ->select_sum('pay_amount')
    ->where('order_code', $code)
    ->where('valid', 1)
    ->get('order_payment');

    if($rs->num_rows() === 1)
    {
      return $rs->row()->pay_amount > 0 ? $rs->row()->pay_amount : 0;
    }

    return 0;
  }


  public function do_export()
  {
    $bookcode = $this->input->post('bookcode');
    $fromDate = $this->input->post('fromDate');
    $toDate = $this->input->post('toDate');
    $date_type = $this->input->post('date_type');
    $allSale = $this->input->post('allSale');
    $saleList = $this->input->post('sale');
    $token = $this->input->post('token');

    $ds = new stdClass();

    $ds->bookcode = $bookcode;
    $ds->fromDate = $fromDate;
    $ds->toDate = $toDate;
    $ds->dateType = $date_type;
    $ds->allSale = $allSale;
    $ds->saleList = $saleList;

    $no = 1;

    $invoices = $this->sales_report_model->get_sales_invoice($ds);

    if( ! empty($invoices))
    {
      $saleman = saleman_array();

      $saleNames = $allSale == 1 ? 'ทั้งหมด' : '';

      if($allSale == 0 && ! empty($saleList))
      {
        $i = 1;

        foreach($saleList as $sa)
        {
          if( ! empty($saleman[$sa]))
          {
            $saleNames .= $i == 1 ? $saleman[$sa] : ", {$saleman[$sa]}";
            $i++;
          }
        }
      }

      //---  Report title
      $report_title = 'รายงานยอดขายแยกตามเอกสาร วันที่ ' . thai_date($fromDate,'/') .' ถึง '.thai_date($toDate, '/');
      $sale_title = 'พนักงานขาย : '.$saleNames;
      $date_title = "กรองตามวันที่".($date_type == 'D' ? 'เอกสาร' : 'บันทึกขาย');

      //--- load excel library
      $this->load->library('excel');

      $this->excel->setActiveSheetIndex(0);
      $this->excel->getActiveSheet()->setTitle('Sales By Document Report');

      //--- set report title header
      $this->excel->getActiveSheet()->setCellValue('A1', $report_title);
      $this->excel->getActiveSheet()->setCellValue('A2', $sale_title);
      $this->excel->getActiveSheet()->setCellValue('A3', $date_title);

      $this->excel->getActiveSheet()->setCellValue('A5', '#');
      $this->excel->getActiveSheet()->setCellValue('B5', 'วันที่เอกสาร');
      $this->excel->getActiveSheet()->setCellValue('C5', 'วันที่บันทึกขาย');
      $this->excel->getActiveSheet()->setCellValue('D5', 'พนักงานขาย');
      $this->excel->getActiveSheet()->setCellValue('E5', 'เล่ม');
      $this->excel->getActiveSheet()->setCellValue('F5', 'Invoice');
      $this->excel->getActiveSheet()->setCellValue('G5', 'อ้างอิง');
      $this->excel->getActiveSheet()->setCellValue('H5', 'ใบสั่งขาย');
      $this->excel->getActiveSheet()->setCellValue('I5', 'เครดิต ?');
      $this->excel->getActiveSheet()->setCellValue('J5', 'มูลค่า');
      $this->excel->getActiveSheet()->setCellValue('K5', 'มัดจำ');
      $this->excel->getActiveSheet()->setCellValue('L5', 'ขำระด้วยเงินสด');
      $this->excel->getActiveSheet()->setCellValue('M5', 'ขำระด้วยเงินโอน');
      $this->excel->getActiveSheet()->setCellValue('N5', 'ขำระด้วยบัตรเครดิต');
      $this->excel->getActiveSheet()->setCellValue('O5', 'ขำระด้วยเช็ค');
      $this->excel->getActiveSheet()->setCellValue('P5', 'รวมชำระ');
      $this->excel->getActiveSheet()->setCellValue('Q5', 'สลิปโอน');
      $this->excel->getActiveSheet()->setCellValue('R5', 'มัดจำ + ชำระ + โอน');
      $this->excel->getActiveSheet()->setCellValue('S5', 'คงค้าง');
      $this->excel->getActiveSheet()->setCellValue('T5', 'ค่าขนส่ง');

      $row = 6;

      foreach($invoices as $rs)
      {
        $paidRole = array(
          '1' => 0, //-- cache
          '2' => 0, //-- transfer
          '3' => 0, //-- card
          '7' => 0 //-- cheque
        );

        $sumPaid = 0;
        $salename = empty($saleman[$rs->SlpCode]) ? 'No sale employee' : $saleman[$rs->SlpCode];
        $slip = $rs->BaseType == 'WO' ? $this->getTransferSlipAmount($rs->BaseRef) : 0;
        $paid = $rs->BaseType == 'POS' ? $this->getPosRolePayment($rs->BaseRef) : 0;

        if( ! empty($paid))
        {
          foreach($paid as $pm)
          {
            $paidRole[$pm->payment_role] = $pm->amount;
            $sumPaid += $pm->amount;
          }
        }

        $docTotal = $rs->DocTotal;
        $totalPaid = $slip + $sumPaid + $rs->downPaymentAmount;
        $outstanding = $rs->DocTotal - $totalPaid;
        $outstanding = $outstanding <= 0 ? 0 : $outstanding;
        $shipCost = $this->getShipCost($rs->code);

        $this->excel->getActiveSheet()->setCellValue("A{$row}", $no);
        $this->excel->getActiveSheet()->setCellValue("B{$row}", thai_date($rs->DocDate));
        $this->excel->getActiveSheet()->setCellValue("C{$row}", thai_date($rs->shipped_date));
        $this->excel->getActiveSheet()->setCellValue("D{$row}", $salename);
        $this->excel->getActiveSheet()->setCellValue("E{$row}", $this->bookcode_name($rs->bookcode));
        $this->excel->getActiveSheet()->setCellValue("F{$row}", $rs->code);
        $this->excel->getActiveSheet()->setCellValue("G{$row}", $rs->BaseRef);
        $this->excel->getActiveSheet()->setCellValue("H{$row}", $rs->so_code);
        $this->excel->getActiveSheet()->setCellValue("I{$row}", $rs->is_term == 1 ? 'Y' : 'N');
        $this->excel->getActiveSheet()->setCellValue("J{$row}", $docTotal);
        $this->excel->getActiveSheet()->setCellValue("K{$row}", $rs->downPaymentAmount);
        $this->excel->getActiveSheet()->setCellValue("L{$row}", $paidRole['1']);
        $this->excel->getActiveSheet()->setCellValue("M{$row}", $paidRole['2']);
        $this->excel->getActiveSheet()->setCellValue("N{$row}", $paidRole['3']);
        $this->excel->getActiveSheet()->setCellValue("O{$row}", $paidRole['7']);
        $this->excel->getActiveSheet()->setCellValue("P{$row}", $sumPaid);
        $this->excel->getActiveSheet()->setCellValue("Q{$row}", $slip);
        $this->excel->getActiveSheet()->setCellValue("R{$row}", $totalPaid);
        $this->excel->getActiveSheet()->setCellValue("S{$row}", $outstanding);
        $this->excel->getActiveSheet()->setCellValue("T{$row}", empty($shipCost) ? 0 : $shipCost);

        $no++;
        $row++;
      }

      $re = $row -1;
      $this->excel->getActiveSheet()->setCellValue("A{$row}", "รวม");
      $this->excel->getActiveSheet()->mergeCells("A{$row}:I{$row}");
      $this->excel->getActiveSheet()->setCellValue("J{$row}", "=SUM(J6:J{$re})");
      $this->excel->getActiveSheet()->setCellValue("K{$row}", "=SUM(K6:K{$re})");
      $this->excel->getActiveSheet()->setCellValue("L{$row}", "=SUM(L6:L{$re})");
      $this->excel->getActiveSheet()->setCellValue("M{$row}", "=SUM(M6:M{$re})");
      $this->excel->getActiveSheet()->setCellValue("N{$row}", "=SUM(N6:N{$re})");
      $this->excel->getActiveSheet()->setCellValue("O{$row}", "=SUM(O6:O{$re})");
      $this->excel->getActiveSheet()->setCellValue("P{$row}", "=SUM(P6:P{$re})");
      $this->excel->getActiveSheet()->setCellValue("Q{$row}", "=SUM(Q6:Q{$re})");
      $this->excel->getActiveSheet()->setCellValue("R{$row}", "=SUM(R6:R{$re})");
      $this->excel->getActiveSheet()->setCellValue("S{$row}", "=SUM(S6:S{$re})");
      $this->excel->getActiveSheet()->setCellValue("T{$row}", "=SUM(T6:T{$re})");

      $this->excel->getActiveSheet()->getStyle('J6:T'.$row)->getNumberFormat()->setFormatCode('#,##0.00');
    }

    setToken($token);
    $file_name = "Report Sale By Document.xlsx";
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'); /// form excel 2007 XLSX
    header('Content-Disposition: attachment;filename="'.$file_name.'"');
    $writer = PHPExcel_IOFactory::createWriter($this->excel, 'Excel2007');
    $writer->save('php://output');
  }

} //--- end class

 ?>
