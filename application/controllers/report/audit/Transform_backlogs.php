<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Transform_backlogs extends PS_Controller
{
  public $menu_code = 'RAWQBL';
	public $menu_group_code = 'RE';
  public $menu_sub_group_code = 'REAUDIT';
	public $title = 'รายงานสินค้าแปรสภาพค้างรับ';
  public $filter;
  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'report/audit/transform_backlogs';
    $this->load->model('report/audit/transform_backlogs_model');
  }

  public function index()
  {
    $this->load->view('report/audit/report_transform_backlogs');
  }


	public function get_report()
	{
		$allUser = $this->input->get('allUser');
		$dname = $this->input->get('dname');
		$allPd = $this->input->get('allProduct');
		$pdFrom = $this->input->get('pdFrom');
		$pdTo = $this->input->get('pdTo');
		$fromDate = empty($this->input->get('fromDate')) ? "" : from_date($this->input->get('fromDate'));
		$toDate = empty($this->input->get('toDate')) ? "" : to_date($this->input->get('toDate'));

		$arr = array(
			'allUser' => $allUser,
			'dname' => $dname,
			'allPd' => $allPd,
			'pdFrom' => $pdFrom,
			'pdTo' => $pdTo,
			'from_date' => $fromDate,
			'to_date' => $toDate
		);

		$ds = array();
		$total_qty = 0;
		$total_return  = 0;
		$total_balance = 0;
		$total_amount = 0;

		$data = $this->transform_backlogs_model->get_data($arr);

		if(!empty($data))
		{
			$no = 1;

			foreach($data as $rs)
			{
				$arr = array(
					'no' => number($no),
					'user_ref' => $rs->user_ref, //--- ผู้เบิก
					'user' => $rs->user_name, //--- ผู้ทำรายการ
          'date_add' => thai_date($rs->date_add, FALSE),
					'order_code' => $rs->order_code,
          'original_code' => $rs->original_code,
					'product_code' => $rs->product_code,
					'price' => number($rs->price,2),
					'qty' => number($rs->qty),
					'return' => number($rs->receive),
					'balance' => number($rs->balance),
					'amount' => number($rs->balance * $rs->price, 2),
          'so_code' => $rs->so_code,
          'bill_code' => $rs->bill_code
				);

				array_push($ds, $arr);

				$total_qty += $rs->qty;
				$total_return += $rs->receive;
				$total_balance += $rs->balance;
				$total_amount += ($rs->balance * $rs->price);
				$no++;
			}

			$arr = array(
				'total_qty' => number($total_qty),
				'total_return' => number($total_return),
				'total_balance' => number($total_balance),
				'total_amount' => number($total_amount, 2)
			);

			array_push($ds, $arr);

		}
		else
		{
			$arr = array(
				'total_qty' => number($total_qty),
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
		$allUser = $this->input->post('allUser');
		$dname = $this->input->post('dname'); //--- orders.user_ref
		$allPd = $this->input->post('allProduct');
		$pdFrom = $this->input->post('pdFrom');
		$pdTo = $this->input->post('pdTo');
		$fromDate = empty($this->input->post('fromDate')) ? "" : from_date($this->input->post('fromDate'));
		$toDate = empty($this->input->post('toDate'))? "" : to_date($this->input->post('toDate'));
		$token = $this->input->post('token');

		//---  Report title
    $report_title = "รายงานสินค้าแปรสภาพค้างร้บ (วันที่พิมพ์รายงาน : ".date('d/m/Y H:i').")";
    $emp_title = 'ผู้เบิก :  '. ($allUser == 1 ? 'ทั้งหมด' : $dname);
    $pd_title = 'สินค้า :  '. ($allPd == 1 ? 'ทั้งหมด' : '('.$pdFrom.') - ('.$pdTo.')');
		$date_title = 'วันที่เอกสาร : '.((empty($fromDate) OR empty($toDate)) ? 'ทั้งหมด' : thai_date($fromDate, '/').' - '.thai_date($toDate, '/'));

		$arr = array(
			'allUser' => $allUser,
			'dname' => $dname, //--- user_ref
			'allPd' => $allPd,
			'pdFrom' => $pdFrom,
			'pdTo' => $pdTo,
			'from_date' => $fromDate,
			'to_date' => $toDate
		);

    $data = $this->transform_backlogs_model->get_data($arr);

    //--- load excel library
    $this->load->library('excel');

    $this->excel->setActiveSheetIndex(0);
    $this->excel->getActiveSheet()->setTitle('WQ-WV ค้างรับ');

    //--- set report title header
    $this->excel->getActiveSheet()->setCellValue('A1', $report_title);
    $this->excel->getActiveSheet()->mergeCells('A1:L1');
    $this->excel->getActiveSheet()->setCellValue('A2', $emp_title);
    $this->excel->getActiveSheet()->mergeCells('A2:L2');
    $this->excel->getActiveSheet()->setCellValue('A3', $pd_title);
    $this->excel->getActiveSheet()->mergeCells('A3:L3');
    $this->excel->getActiveSheet()->setCellValue('A4', $date_title);
    $this->excel->getActiveSheet()->mergeCells('A4:L4');

    //--- set Table header
		$row = 5;

    $this->excel->getActiveSheet()->setCellValue('A'.$row, '#');
    $this->excel->getActiveSheet()->setCellValue('B'.$row, 'ผู้เบิก(User)');
    $this->excel->getActiveSheet()->setCellValue('C'.$row, 'ผู้ทำรายการ');
    $this->excel->getActiveSheet()->setCellValue('D'.$row, 'วันที่');
    $this->excel->getActiveSheet()->setCellValue('E'.$row, 'เลขที่เอกสาร');
    $this->excel->getActiveSheet()->setCellValue('F'.$row, 'รหัสสินค้า(เบิก)');
    $this->excel->getActiveSheet()->setCellValue('G'.$row, 'รหัสสินค้า(รับ)');
    $this->excel->getActiveSheet()->setCellValue('H'.$row, 'ราคา');
    $this->excel->getActiveSheet()->setCellValue('I'.$row, 'เบิก');
    $this->excel->getActiveSheet()->setCellValue('J'.$row, 'รับ');
    $this->excel->getActiveSheet()->setCellValue('K'.$row, 'ค้างรับ');
    $this->excel->getActiveSheet()->setCellValue('L'.$row, 'มูลค่าคงรับ');
    $this->excel->getActiveSheet()->setCellValue('M'.$row, 'ใบสั่งขาย');
    $this->excel->getActiveSheet()->setCellValue('N'.$row, 'บิลขาย');

    //---- กำหนดความกว้างของคอลัมภ์
    $this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
    $this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
    $this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
    $this->excel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
    $this->excel->getActiveSheet()->getColumnDimension('F')->setWidth(30);
    $this->excel->getActiveSheet()->getColumnDimension('G')->setWidth(30);
    $this->excel->getActiveSheet()->getColumnDimension('H')->setWidth(15);
    $this->excel->getActiveSheet()->getColumnDimension('I')->setWidth(15);
    $this->excel->getActiveSheet()->getColumnDimension('J')->setWidth(15);
    $this->excel->getActiveSheet()->getColumnDimension('K')->setWidth(15);
    $this->excel->getActiveSheet()->getColumnDimension('L')->setWidth(15);
    $this->excel->getActiveSheet()->getColumnDimension('M')->setWidth(15);
    $this->excel->getActiveSheet()->getColumnDimension('N')->setWidth(15);

		$row++;


    if(!empty($data))
    {
      $no = 1;

      foreach($data as $rs)
      {
        $this->excel->getActiveSheet()->setCellValue('A'.$row, $no);
        $this->excel->getActiveSheet()->setCellValue('B'.$row, $rs->user_ref);
        $this->excel->getActiveSheet()->setCellValue('C'.$row, $rs->user_name);
        $this->excel->getActiveSheet()->setCellValue('D'.$row, thai_date($rs->date_add));
        $this->excel->getActiveSheet()->setCellValue('E'.$row, $rs->order_code);
        $this->excel->getActiveSheet()->setCellValue('F'.$row, $rs->original_code);
        $this->excel->getActiveSheet()->setCellValue('G'.$row, $rs->product_code);
        $this->excel->getActiveSheet()->setCellValue('H'.$row, $rs->price);
        $this->excel->getActiveSheet()->setCellValue('I'.$row, $rs->qty);
        $this->excel->getActiveSheet()->setCellValue('J'.$row, $rs->receive);
        $this->excel->getActiveSheet()->setCellValue('K'.$row, $rs->balance);
        $this->excel->getActiveSheet()->setCellValue('L'.$row, ($rs->balance * $rs->price));
        $this->excel->getActiveSheet()->setCellValue('M'.$row, $rs->so_code);
        $this->excel->getActiveSheet()->setCellValue('N'.$row, $rs->bill_code);

        $no++;
        $row++;
      }
    }
		else
		{
			$this->excel->getActiveSheet()->setCellValue('A'.$row, $no);
			$row++;
		}

		$re = $row - 1;

		$this->excel->getActiveSheet()->setCellValue("A{$row}", 'รวม');
		$this->excel->getActiveSheet()->mergeCells("A{$row}:G{$row}");
		$this->excel->getActiveSheet()->getStyle("A{$row}")->getAlignment()->setHorizontal('right');

		$this->excel->getActiveSheet()->setCellValue("I{$row}", "=SUM(I6:I{$re})");
		$this->excel->getActiveSheet()->setCellValue("J{$row}", "=SUM(J6:J{$re})");
		$this->excel->getActiveSheet()->setCellValue("K{$row}", "=SUM(K6:K{$re})");
		$this->excel->getActiveSheet()->setCellValue("L{$row}", "=SUM(L6:L{$re})");

    setToken($token);
    $file_name = "รายงานสินค้าแปรสภาพค้างรับ_".date('dmY').".xlsx";
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'); /// form excel 2007 XLSX
    header('Content-Disposition: attachment;filename="'.$file_name.'"');
    $writer = PHPExcel_IOFactory::createWriter($this->excel, 'Excel2007');
    $writer->save('php://output');

  }

} //--- end class








 ?>
