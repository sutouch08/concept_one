<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Lend_backlogs extends PS_Controller
{
  public $menu_code = 'RALNBL';
	public $menu_group_code = 'RE';
  public $menu_sub_group_code = 'REAUDIT';
	public $title = 'รายงานสินค้ายืมยังไม่คืน';
  public $filter;
  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'report/audit/lend_backlogs';
    $this->load->model('report/audit/lend_backlogs_model');
    $this->load->model('masters/products_model');
  }

  public function index()
  {
    $this->load->view('report/audit/report_lend_backlogs');
  }


	public function get_report()
	{
		$allEmp = $this->input->get('allEmp');
		$empId = $this->input->get('empId');
		$allPd = $this->input->get('allProduct');
		$pdFrom = $this->input->get('pdFrom');
		$pdTo = $this->input->get('pdTo');
		$fromDate = from_date($this->input->get('fromDate'));
		$toDate = to_date($this->input->get('toDate'));

		$arr = array(
			'allEmp' => $allEmp,
			'empId' => $empId,
			'allPd' => $allPd,
			'pdFrom' => $pdFrom,
			'pdTo' => $pdTo,
			'from_date' => $fromDate,
			'to_date' => $toDate
		);

		$ds = array();
		$total_lend = 0;
		$total_return  = 0;
		$total_balance = 0;
		$total_amount = 0;

		$data = $this->lend_backlogs_model->get_data($arr);

		if(!empty($data))
		{
			$no = 1;

			foreach($data as $rs)
			{
				$arr = array(
					'no' => number($no),
					'emp_name' => $rs->empName, //--- ผู้เบิก
					'user_ref' => $rs->user_ref, //--- ผู้รับ
					'user' => $rs->user_name, //--- ผู้ทำรายการ
					'order_code' => $rs->order_code,
					'product_code' => $rs->product_code,
					'price' => number($rs->price,2),
					'lend' => number($rs->qty),
					'return' => number($rs->receive),
					'balance' => number($rs->balance),
					'amount' => number($rs->balance * $rs->price, 2)
				);

				array_push($ds, $arr);

				$total_lend += $rs->qty;
				$total_return += $rs->receive;
				$total_balance += $rs->balance;
				$total_amount += ($rs->balance * $rs->price);
				$no++;
			}

			$arr = array(
				'total_lend' => number($total_lend),
				'total_return' => number($total_return),
				'total_balance' => number($total_balance),
				'total_amount' => number($total_amount, 2)
			);

			array_push($ds, $arr);

		}
		else
		{
			$arr = array(
				'total_lend' => number($total_lend),
				'total_return' => number($total_return),
				'total_balance' => number($total_balance),
				'total_amount' => number($total_amount, 2)
			);

			array_push($ds, $arr);
		}

		echo json_encode($ds);
	}

  public function do_export()
  {
		$allEmp = $this->input->post('allEmp');
		$empId = $this->input->post('empId');
		$empName = $this->input->post('empName');
		$allPd = $this->input->post('allProduct');
		$pdFrom = $this->input->post('pdFrom');
		$pdTo = $this->input->post('pdTo');
		$fromDate = from_date($this->input->post('fromDate'));
		$toDate = to_date($this->input->post('toDate'));
		$token = $this->input->post('token');

		//---  Report title
    $report_title = "รายงานสินค้ายืมยังไม่คืน (วันที่พิมพ์รายงาน : ".date('d/m/Y H:i').")";
    $emp_title = 'ผู้ยืม :  '. ($allEmp == 1 ? 'ทั้งหมด' : $empName);
    $pd_title = 'สินค้า :  '. ($allPd == 1 ? 'ทั้งหมด' : '('.$pdFrom.') - ('.$pdTo.')');
		$date_title = 'วันที่เอกสาร : '.thai_date($fromDate, '/').' - '.thai_date($toDate, '/');

		$arr = array(
			'allEmp' => $allEmp,
			'empId' => $empId,
			'allPd' => $allPd,
			'pdFrom' => $pdFrom,
			'pdTo' => $pdTo,
			'from_date' => $fromDate,
			'to_date' => $toDate
		);

    $data = $this->lend_backlogs_model->get_data($arr);

    //--- load excel library
    $this->load->library('excel');

    $this->excel->setActiveSheetIndex(0);
    $this->excel->getActiveSheet()->setTitle('Lend backlogs Report');

    //--- set report title header
    $this->excel->getActiveSheet()->setCellValue('A1', $report_title);
    $this->excel->getActiveSheet()->mergeCells('A1:K1');
    $this->excel->getActiveSheet()->setCellValue('A2', $emp_title);
    $this->excel->getActiveSheet()->mergeCells('A2:K2');
    $this->excel->getActiveSheet()->setCellValue('A3', $pd_title);
    $this->excel->getActiveSheet()->mergeCells('A3:K3');
    $this->excel->getActiveSheet()->setCellValue('A4', $date_title);
    $this->excel->getActiveSheet()->mergeCells('A4:K4');

    //--- set Table header
		$row = 5;

    $this->excel->getActiveSheet()->setCellValue('A'.$row, '#');
    $this->excel->getActiveSheet()->setCellValue('B'.$row, 'ผู้ยืม');
    $this->excel->getActiveSheet()->setCellValue('C'.$row, 'ผู้รับ');
    $this->excel->getActiveSheet()->setCellValue('D'.$row, 'ผู้ทำรายการ');
    $this->excel->getActiveSheet()->setCellValue('E'.$row, 'เลขที่เอกสาร');
    $this->excel->getActiveSheet()->setCellValue('F'.$row, 'รหัสสินค้า');
    $this->excel->getActiveSheet()->setCellValue('G'.$row, 'ราคา');
    $this->excel->getActiveSheet()->setCellValue('H'.$row, 'ยืม');
    $this->excel->getActiveSheet()->setCellValue('I'.$row, 'คืน');
    $this->excel->getActiveSheet()->setCellValue('J'.$row, 'คงค้าง');
    $this->excel->getActiveSheet()->setCellValue('K'.$row, 'มูลค่าคงค้าง');

    //---- กำหนดความกว้างของคอลัมภ์
    $this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
    $this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
    $this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
    $this->excel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
    $this->excel->getActiveSheet()->getColumnDimension('F')->setWidth(30);
    $this->excel->getActiveSheet()->getColumnDimension('G')->setWidth(15);
    $this->excel->getActiveSheet()->getColumnDimension('H')->setWidth(15);
    $this->excel->getActiveSheet()->getColumnDimension('I')->setWidth(15);
    $this->excel->getActiveSheet()->getColumnDimension('J')->setWidth(15);
    $this->excel->getActiveSheet()->getColumnDimension('K')->setWidth(15);

		$row++;


    if(!empty($data))
    {
      $no = 1;

      foreach($data as $rs)
      {
        $this->excel->getActiveSheet()->setCellValue('A'.$row, $no);
        $this->excel->getActiveSheet()->setCellValue('B'.$row, $rs->empName);
        $this->excel->getActiveSheet()->setCellValue('C'.$row, $rs->user_ref);
        $this->excel->getActiveSheet()->setCellValue('D'.$row, $rs->user_name);
        $this->excel->getActiveSheet()->setCellValue('E'.$row, $rs->order_code);
        $this->excel->getActiveSheet()->setCellValue('F'.$row, $rs->product_code);
        $this->excel->getActiveSheet()->setCellValue('G'.$row, $rs->price);
        $this->excel->getActiveSheet()->setCellValue('H'.$row, $rs->qty);
        $this->excel->getActiveSheet()->setCellValue('I'.$row, $rs->receive);
        $this->excel->getActiveSheet()->setCellValue('J'.$row, $rs->balance);
        $this->excel->getActiveSheet()->setCellValue('K'.$row, ($rs->balance * $rs->price));

        $no++;
        $row++;
      }
    }

		$re = $row - 1;

		$this->excel->getActiveSheet()->setCellValue("A{$row}", 'รวม');
		$this->excel->getActiveSheet()->mergeCells("A{$row}:G{$row}");
		$this->excel->getActiveSheet()->getStyle("A{$row}")->getAlignment()->setHorizontal('right');

		$this->excel->getActiveSheet()->setCellValue("H{$row}", "=SUM(H6:H{$re})");
		$this->excel->getActiveSheet()->setCellValue("I{$row}", "=SUM(I6:I{$re})");
		$this->excel->getActiveSheet()->setCellValue("J{$row}", "=SUM(J6:J{$re})");
		$this->excel->getActiveSheet()->setCellValue("K{$row}", "=SUM(K6:K{$re})");

    setToken($token);
    $file_name = "Lend_backlogs_".date('dmY').".xlsx";
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'); /// form excel 2007 XLSX
    header('Content-Disposition: attachment;filename="'.$file_name.'"');
    $writer = PHPExcel_IOFactory::createWriter($this->excel, 'Excel2007');
    $writer->save('php://output');

  }

} //--- end class








 ?>
