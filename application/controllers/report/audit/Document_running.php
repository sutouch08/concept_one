<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Document_running extends PS_Controller
{
  public $menu_code = 'RADRUN';
	public $menu_group_code = 'RE';
  public $menu_sub_group_code = 'REAUDIT';
	public $title = 'รายงานทะเบียนคุมเอกสารแยกตามประเภท';
  public $filter;
	public $wms;
	public $limit = 2000;

  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'report/audit/document_running';
    $this->load->model('report/audit/document_model');
  }

  public function index()
  {
    $this->load->view('report/audit/document_running');
  }



  public function do_export()
  {
    $token = $this->input->post('token');
		$role = $this->input->post('role');
		$all  = $this->input->post('allRole') ? TRUE : FALSE;
		$fromDate = $this->input->post('fromDate');
    $toDate = $this->input->post('toDate');

    //--- load excel library
    $this->load->library('excel');

		$index = 0;

		if(!empty($role['WO']) OR $all)
		{
			$worksheet = new PHPExcel_Worksheet($this->excel, "WO");
			$this->excel->addSheet($worksheet, $index);
			$this->excel->setActiveSheetIndex($index);
			$this->excel->getActiveSheet()->setTitle('WO');

			$index++;

			//--- set report title header
			$this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(8);
			$this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(15);
			$this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(15);
			$this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(15);
			$this->excel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
			$this->excel->getActiveSheet()->getColumnDimension('F')->setWidth(15);
			$this->excel->getActiveSheet()->getColumnDimension('G')->setWidth(20);

			//--- set Table header
			$this->excel->getActiveSheet()->setCellValue('A1', 'ลำดับ');
			$this->excel->getActiveSheet()->setCellValue('B1', 'วันที่');
			$this->excel->getActiveSheet()->setCellValue('C1', 'เลขที่');
			$this->excel->getActiveSheet()->setCellValue('D1', 'SAP DO');
			$this->excel->getActiveSheet()->setCellValue('E1', 'ช่องทางขาย');
			$this->excel->getActiveSheet()->setCellValue('F1', 'สถานะ');
			$this->excel->getActiveSheet()->setCellValue('G1', 'หมายเหตุ');

			$data = $this->document_model->getOrder('S', $fromDate, $toDate);

			if(!empty($data))
			{
				$no = 1;
				$row = 2;

				foreach($data as $rs)
				{
					$this->excel->getActiveSheet()->setCellValue('A'.$row, $no);
	        $this->excel->getActiveSheet()->setCellValue('B'.$row, thai_date($rs->date_add, FALSE, '/'));
					$this->excel->getActiveSheet()->setCellValue('C'.$row, $rs->code);
	        $this->excel->getActiveSheet()->setCellValue('D'.$row, $rs->inv_code);
	        $this->excel->getActiveSheet()->setCellValue('E'.$row, $rs->channels_name);
					$this->excel->getActiveSheet()->setCellValue('F'.$row, $rs->state_name);
					if(!empty($rs->reason))
					{
						$this->excel->getActiveSheet()->setCellValue('G'.$row, $rs->reason);
					}

					$no++;
					$row++;
				}
			}
		} //--- end WO


		if(! empty($role['WS']) OR $all)
		{
			$worksheet = new PHPExcel_Worksheet($this->excel, "WS");
			$this->excel->addSheet($worksheet, $index);
			$this->excel->setActiveSheetIndex($index);
			$this->excel->getActiveSheet()->setTitle('WS');

			$index++;

			//--- set Table header
			$this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(8);
			$this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(15);
			$this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(15);
			$this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(15);
			$this->excel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
			$this->excel->getActiveSheet()->getColumnDimension('F')->setWidth(15);
			$this->excel->getActiveSheet()->getColumnDimension('G')->setWidth(20);

			$this->excel->getActiveSheet()->setCellValue('A1', 'ลำดับ');
			$this->excel->getActiveSheet()->setCellValue('B1', 'วันที่');
			$this->excel->getActiveSheet()->setCellValue('C1', 'เลขที่');
			$this->excel->getActiveSheet()->setCellValue('D1', 'SAP DO');
			$this->excel->getActiveSheet()->setCellValue('E1', 'สถานะ');
			$this->excel->getActiveSheet()->setCellValue('F1', 'หมายเหตุ');

			$data = $this->document_model->getOrder('P', $fromDate, $toDate);

			if(!empty($data))
			{
				$no = 1;
				$row = 2;

				foreach($data as $rs)
				{
					$this->excel->getActiveSheet()->setCellValue('A'.$row, $no);
	        $this->excel->getActiveSheet()->setCellValue('B'.$row, thai_date($rs->date_add, FALSE, '/'));
					$this->excel->getActiveSheet()->setCellValue('C'.$row, $rs->code);
	        $this->excel->getActiveSheet()->setCellValue('D'.$row, $rs->inv_code);
	        $this->excel->getActiveSheet()->setCellValue('E'.$row, $rs->state_name);
					if(!empty($rs->reason))
					{
						$this->excel->getActiveSheet()->setCellValue('F'.$row, $rs->reason);
					}

					$no++;
					$row++;
				}
			}
		} //--- end WS


		if(!empty($role['WU']) OR $all)
		{
			$worksheet = new PHPExcel_Worksheet($this->excel, "WU");
			$this->excel->addSheet($worksheet, $index);
			$this->excel->setActiveSheetIndex($index);
			$this->excel->getActiveSheet()->setTitle('WU');

			$index++;

			//--- set Table header
			$this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(8);
			$this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(15);
			$this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(15);
			$this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(15);
			$this->excel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
			$this->excel->getActiveSheet()->getColumnDimension('F')->setWidth(15);
			$this->excel->getActiveSheet()->getColumnDimension('G')->setWidth(20);


			$this->excel->getActiveSheet()->setCellValue('A1', 'ลำดับ');
			$this->excel->getActiveSheet()->setCellValue('B1', 'วันที่');
			$this->excel->getActiveSheet()->setCellValue('C1', 'เลขที่');
			$this->excel->getActiveSheet()->setCellValue('D1', 'SAP DO');
			$this->excel->getActiveSheet()->setCellValue('E1', 'สถานะ');
			$this->excel->getActiveSheet()->setCellValue('F1', 'หมายเหตุ');

			$data = $this->document_model->getOrder('U', $fromDate, $toDate);

			if(!empty($data))
			{
				$no = 1;
				$row = 2;

				foreach($data as $rs)
				{
					$this->excel->getActiveSheet()->setCellValue('A'.$row, $no);
	        $this->excel->getActiveSheet()->setCellValue('B'.$row, thai_date($rs->date_add, FALSE, '/'));
					$this->excel->getActiveSheet()->setCellValue('C'.$row, $rs->code);
	        $this->excel->getActiveSheet()->setCellValue('D'.$row, $rs->inv_code);
	        $this->excel->getActiveSheet()->setCellValue('E'.$row, $rs->state_name);
					if(!empty($rs->reason))
					{
						$this->excel->getActiveSheet()->setCellValue('F'.$row, $rs->reason);
					}

					$no++;
					$row++;
				}
			}
		} //--- end WU


		if(!empty($role['WC']) OR $all)
		{
			$worksheet = new PHPExcel_Worksheet($this->excel, "WC");
			$this->excel->addSheet($worksheet, $index);
			$this->excel->setActiveSheetIndex($index);
			$this->excel->getActiveSheet()->setTitle('WC');

			$index++;

			//--- set Table header
			$this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(8);
			$this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(15);
			$this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(15);
			$this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(15);
			$this->excel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
			$this->excel->getActiveSheet()->getColumnDimension('F')->setWidth(15);
			$this->excel->getActiveSheet()->getColumnDimension('G')->setWidth(20);


			$this->excel->getActiveSheet()->setCellValue('A1', 'ลำดับ');
			$this->excel->getActiveSheet()->setCellValue('B1', 'วันที่');
			$this->excel->getActiveSheet()->setCellValue('C1', 'เลขที่');
			$this->excel->getActiveSheet()->setCellValue('D1', 'SAP DO');
			$this->excel->getActiveSheet()->setCellValue('E1', 'สถานะ');
			$this->excel->getActiveSheet()->setCellValue('F1', 'หมายเหตุ');

			$data = $this->document_model->getOrder('C', $fromDate, $toDate);

			if(!empty($data))
			{
				$no = 1;
				$row = 2;

				foreach($data as $rs)
				{
					$this->excel->getActiveSheet()->setCellValue('A'.$row, $no);
	        $this->excel->getActiveSheet()->setCellValue('B'.$row, thai_date($rs->date_add, FALSE, '/'));
					$this->excel->getActiveSheet()->setCellValue('C'.$row, $rs->code);
	        $this->excel->getActiveSheet()->setCellValue('D'.$row, $rs->inv_code);
	        $this->excel->getActiveSheet()->setCellValue('E'.$row, $rs->state_name);
					if(!empty($rs->reason))
					{
						$this->excel->getActiveSheet()->setCellValue('F'.$row, $rs->reason);
					}

					$no++;
					$row++;
				}
			}
		} //--- end WC


		if(!empty($role['WT']) OR $all)
		{
			$worksheet = new PHPExcel_Worksheet($this->excel, "WT");
			$this->excel->addSheet($worksheet, $index);
			$this->excel->setActiveSheetIndex($index);
			$this->excel->getActiveSheet()->setTitle('WT');

			$index++;

			//--- set Table header
			$this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(8);
			$this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(15);
			$this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(15);
			$this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(15);
			$this->excel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
			$this->excel->getActiveSheet()->getColumnDimension('F')->setWidth(15);
			$this->excel->getActiveSheet()->getColumnDimension('G')->setWidth(20);

			$this->excel->getActiveSheet()->setCellValue('A1', 'ลำดับ');
			$this->excel->getActiveSheet()->setCellValue('B1', 'วันที่');
			$this->excel->getActiveSheet()->setCellValue('C1', 'เลขที่');
			$this->excel->getActiveSheet()->setCellValue('D1', 'SAP DO');
			$this->excel->getActiveSheet()->setCellValue('E1', 'สถานะ');
			$this->excel->getActiveSheet()->setCellValue('F1', 'หมายเหตุ');

			$data = $this->document_model->getOrder('N', $fromDate, $toDate);

			if(!empty($data))
			{
				$no = 1;
				$row = 2;

				foreach($data as $rs)
				{
					$this->excel->getActiveSheet()->setCellValue('A'.$row, $no);
	        $this->excel->getActiveSheet()->setCellValue('B'.$row, thai_date($rs->date_add, FALSE, '/'));
					$this->excel->getActiveSheet()->setCellValue('C'.$row, $rs->code);
	        $this->excel->getActiveSheet()->setCellValue('D'.$row, $rs->inv_code);
	        $this->excel->getActiveSheet()->setCellValue('E'.$row, $rs->state_name);
					if(!empty($rs->reason))
					{
						$this->excel->getActiveSheet()->setCellValue('F'.$row, $rs->reason);
					}

					$no++;
					$row++;
				}
			}
		} //--- end WT


		if(!empty($role['WQ']) OR $all)
		{
			$worksheet = new PHPExcel_Worksheet($this->excel, "WQ");
			$this->excel->addSheet($worksheet, $index);
			$this->excel->setActiveSheetIndex($index);
			$this->excel->getActiveSheet()->setTitle('WQ');

			$index++;

			//--- set Table header
			$this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(8);
			$this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(15);
			$this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(15);
			$this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(15);
			$this->excel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
			$this->excel->getActiveSheet()->getColumnDimension('F')->setWidth(15);
			$this->excel->getActiveSheet()->getColumnDimension('G')->setWidth(20);

			$this->excel->getActiveSheet()->setCellValue('A1', 'ลำดับ');
			$this->excel->getActiveSheet()->setCellValue('B1', 'วันที่');
			$this->excel->getActiveSheet()->setCellValue('C1', 'เลขที่');
			$this->excel->getActiveSheet()->setCellValue('D1', 'SAP TR');
			$this->excel->getActiveSheet()->setCellValue('E1', 'สถานะ');
			$this->excel->getActiveSheet()->setCellValue('F1', 'หมายเหตุ');

			$data = $this->document_model->getOrder('T', $fromDate, $toDate);

			if(!empty($data))
			{
				$no = 1;
				$row = 2;

				foreach($data as $rs)
				{
					$this->excel->getActiveSheet()->setCellValue('A'.$row, $no);
	        $this->excel->getActiveSheet()->setCellValue('B'.$row, thai_date($rs->date_add, FALSE, '/'));
					$this->excel->getActiveSheet()->setCellValue('C'.$row, $rs->code);
	        $this->excel->getActiveSheet()->setCellValue('D'.$row, $rs->inv_code);
	        $this->excel->getActiveSheet()->setCellValue('E'.$row, $rs->state_name);
					if(!empty($rs->reason))
					{
						$this->excel->getActiveSheet()->setCellValue('F'.$row, $rs->reason);
					}

					$no++;
					$row++;
				}
			}
		} //--- end WQ

		if(!empty($role['WV']) OR $all)
		{
			$worksheet = new PHPExcel_Worksheet($this->excel, "WV");
			$this->excel->addSheet($worksheet, $index);
			$this->excel->setActiveSheetIndex($index);
			$this->excel->getActiveSheet()->setTitle('WV');

			$index++;

			//--- set Table header
			$this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(8);
			$this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(15);
			$this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(15);
			$this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(15);
			$this->excel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
			$this->excel->getActiveSheet()->getColumnDimension('F')->setWidth(15);
			$this->excel->getActiveSheet()->getColumnDimension('G')->setWidth(20);

			$this->excel->getActiveSheet()->setCellValue('A1', 'ลำดับ');
			$this->excel->getActiveSheet()->setCellValue('B1', 'วันที่');
			$this->excel->getActiveSheet()->setCellValue('C1', 'เลขที่');
			$this->excel->getActiveSheet()->setCellValue('D1', 'SAP TR');
			$this->excel->getActiveSheet()->setCellValue('E1', 'สถานะ');
			$this->excel->getActiveSheet()->setCellValue('F1', 'หมายเหตุ');

			$data = $this->document_model->getOrder('Q', $fromDate, $toDate);

			if(!empty($data))
			{
				$no = 1;
				$row = 2;

				foreach($data as $rs)
				{
					$this->excel->getActiveSheet()->setCellValue('A'.$row, $no);
	        $this->excel->getActiveSheet()->setCellValue('B'.$row, thai_date($rs->date_add, FALSE, '/'));
					$this->excel->getActiveSheet()->setCellValue('C'.$row, $rs->code);
	        $this->excel->getActiveSheet()->setCellValue('D'.$row, $rs->inv_code);
	        $this->excel->getActiveSheet()->setCellValue('E'.$row, $rs->state_name);
					if(!empty($rs->reason))
					{
						$this->excel->getActiveSheet()->setCellValue('F'.$row, $rs->reason);
					}

					$no++;
					$row++;
				}
			}
		} //--- end WV


		if(!empty($role['WL']) OR $all)
		{
			$worksheet = new PHPExcel_Worksheet($this->excel, "WL");
			$this->excel->addSheet($worksheet, $index);
			$this->excel->setActiveSheetIndex($index);
			$this->excel->getActiveSheet()->setTitle('WL');

			$index++;

			//--- set Table header
			$this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(8);
			$this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(15);
			$this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(15);
			$this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(15);
			$this->excel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
			$this->excel->getActiveSheet()->getColumnDimension('F')->setWidth(15);
			$this->excel->getActiveSheet()->getColumnDimension('G')->setWidth(20);

			$this->excel->getActiveSheet()->setCellValue('A1', 'ลำดับ');
			$this->excel->getActiveSheet()->setCellValue('B1', 'วันที่');
			$this->excel->getActiveSheet()->setCellValue('C1', 'เลขที่');
			$this->excel->getActiveSheet()->setCellValue('D1', 'SAP TR');
			$this->excel->getActiveSheet()->setCellValue('E1', 'สถานะ');
			$this->excel->getActiveSheet()->setCellValue('F1', 'หมายเหตุ');

			$data = $this->document_model->getOrder('L', $fromDate, $toDate);

			if(!empty($data))
			{
				$no = 1;
				$row = 2;

				foreach($data as $rs)
				{
					$this->excel->getActiveSheet()->setCellValue('A'.$row, $no);
	        $this->excel->getActiveSheet()->setCellValue('B'.$row, thai_date($rs->date_add, FALSE, '/'));
					$this->excel->getActiveSheet()->setCellValue('C'.$row, $rs->code);
	        $this->excel->getActiveSheet()->setCellValue('D'.$row, $rs->inv_code);
	        $this->excel->getActiveSheet()->setCellValue('E'.$row, $rs->state_name);
					if(!empty($rs->reason))
					{
						$this->excel->getActiveSheet()->setCellValue('F'.$row, $rs->reason);
					}

					$no++;
					$row++;
				}
			}
		} //--- end WL


		if(!empty($role['WM']) OR $all)
		{
			$worksheet = new PHPExcel_Worksheet($this->excel, "WM");
			$this->excel->addSheet($worksheet, $index);
			$this->excel->setActiveSheetIndex($index);
			$this->excel->getActiveSheet()->setTitle('WM');

			$index++;

			//--- set Table header
			$this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(8);
			$this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(15);
			$this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(15);
			$this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(15);
			$this->excel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
			$this->excel->getActiveSheet()->getColumnDimension('F')->setWidth(15);
			$this->excel->getActiveSheet()->getColumnDimension('G')->setWidth(20);

			$this->excel->getActiveSheet()->setCellValue('A1', 'ลำดับ');
			$this->excel->getActiveSheet()->setCellValue('B1', 'วันที่');
			$this->excel->getActiveSheet()->setCellValue('C1', 'เลขที่');
			$this->excel->getActiveSheet()->setCellValue('D1', 'SAP DO');
			$this->excel->getActiveSheet()->setCellValue('E1', 'สถานะ');
      $this->excel->getActiveSheet()->setCellValue('F1', 'หมายเหตุ');

			$data = $this->document_model->WM($fromDate, $toDate);

			if(!empty($data))
			{
				$no = 1;
				$row = 2;

				foreach($data as $rs)
				{
					$this->excel->getActiveSheet()->setCellValue('A'.$row, $no);
	        $this->excel->getActiveSheet()->setCellValue('B'.$row, thai_date($rs->date_add, FALSE, '/'));
					$this->excel->getActiveSheet()->setCellValue('C'.$row, $rs->code);
	        $this->excel->getActiveSheet()->setCellValue('D'.$row, $rs->inv_code);
	        $this->excel->getActiveSheet()->setCellValue('E'.$row, ($rs->status == 0 ? 'Pending' : ($rs->status == 1 ? 'Success' : ($rs->status == 2 ? 'Canceled' : 'Unknow'))));
          if(!empty($rs->reason))
					{
						$this->excel->getActiveSheet()->setCellValue('F'.$row, $rs->reason);
					}
					$no++;
					$row++;
				}
			}
		} //--- end WM


		if(!empty($role['WD']) OR $all)
		{
			$worksheet = new PHPExcel_Worksheet($this->excel, "WD");
			$this->excel->addSheet($worksheet, $index);
			$this->excel->setActiveSheetIndex($index);
			$this->excel->getActiveSheet()->setTitle('WD');

			$index++;

			//--- set Table header
			$this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(8);
			$this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(15);
			$this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(15);
			$this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(15);
			$this->excel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
			$this->excel->getActiveSheet()->getColumnDimension('F')->setWidth(15);
			$this->excel->getActiveSheet()->getColumnDimension('G')->setWidth(20);

			$this->excel->getActiveSheet()->setCellValue('A1', 'ลำดับ');
			$this->excel->getActiveSheet()->setCellValue('B1', 'วันที่');
			$this->excel->getActiveSheet()->setCellValue('C1', 'เลขที่');
			$this->excel->getActiveSheet()->setCellValue('D1', 'SAP DO');
			$this->excel->getActiveSheet()->setCellValue('E1', 'สถานะ');
      $this->excel->getActiveSheet()->setCellValue('F1', 'หมายเหตุ');

			$data = $this->document_model->WD($fromDate, $toDate);

			if(!empty($data))
			{
				$no = 1;
				$row = 2;

				foreach($data as $rs)
				{
					$this->excel->getActiveSheet()->setCellValue('A'.$row, $no);
	        $this->excel->getActiveSheet()->setCellValue('B'.$row, thai_date($rs->date_add, FALSE, '/'));
					$this->excel->getActiveSheet()->setCellValue('C'.$row, $rs->code);
	        $this->excel->getActiveSheet()->setCellValue('D'.$row, $rs->inv_code);
	        $this->excel->getActiveSheet()->setCellValue('E'.$row, ($rs->status == 0 ? 'Pending' : ($rs->status == 1 ? 'Success' : ($rs->status == 2 ? 'Canceled' : 'Unknow'))));
          if(!empty($rs->reason))
					{
						$this->excel->getActiveSheet()->setCellValue('F'.$row, $rs->reason);
					}
					$no++;
					$row++;
				}
			}
		} //--- end WD


		if(!empty($role['WR']) OR $all)
		{
			$worksheet = new PHPExcel_Worksheet($this->excel, "WR");
			$this->excel->addSheet($worksheet, $index);
			$this->excel->setActiveSheetIndex($index);
			$this->excel->getActiveSheet()->setTitle('WR');

			$index++;

			//--- set Table header
			$this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(8);
			$this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(15);
			$this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(15);
			$this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(15);
			$this->excel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
			$this->excel->getActiveSheet()->getColumnDimension('F')->setWidth(15);
			$this->excel->getActiveSheet()->getColumnDimension('G')->setWidth(20);

			$this->excel->getActiveSheet()->setCellValue('A1', 'ลำดับ');
			$this->excel->getActiveSheet()->setCellValue('B1', 'วันที่');
			$this->excel->getActiveSheet()->setCellValue('C1', 'เลขที่');
			$this->excel->getActiveSheet()->setCellValue('D1', 'SAP GRPO');
			$this->excel->getActiveSheet()->setCellValue('E1', 'สถานะ');
      $this->excel->getActiveSheet()->setCellValue('F1', 'หมายเหตุ');

			$data = $this->document_model->WR($fromDate, $toDate);

			if(!empty($data))
			{
				$no = 1;
				$row = 2;

				foreach($data as $rs)
				{
					$this->excel->getActiveSheet()->setCellValue('A'.$row, $no);
	        $this->excel->getActiveSheet()->setCellValue('B'.$row, thai_date($rs->date_add, FALSE, '/'));
					$this->excel->getActiveSheet()->setCellValue('C'.$row, $rs->code);
	        $this->excel->getActiveSheet()->setCellValue('D'.$row, $rs->inv_code);
	        $this->excel->getActiveSheet()->setCellValue('E'.$row, $this->statusLabel($rs->status, $rs->is_expire));
          if($rs->status == 2)
          {
            $this->excel->getActiveSheet()->setCellValue('F'.$row, $rs->reason);
          }

					$no++;
					$row++;
				}
			}
		} //--- end WR


		if(!empty($role['WW']) OR $all)
		{
			$worksheet = new PHPExcel_Worksheet($this->excel, "WW");
			$this->excel->addSheet($worksheet, $index);
			$this->excel->setActiveSheetIndex($index);
			$this->excel->getActiveSheet()->setTitle('WW');

			$index++;

			//--- set Table header
			$this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(8);
			$this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(15);
			$this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(15);
			$this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(15);
			$this->excel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
			$this->excel->getActiveSheet()->getColumnDimension('F')->setWidth(15);
			$this->excel->getActiveSheet()->getColumnDimension('G')->setWidth(20);

			$this->excel->getActiveSheet()->setCellValue('A1', 'ลำดับ');
			$this->excel->getActiveSheet()->setCellValue('B1', 'วันที่');
			$this->excel->getActiveSheet()->setCellValue('C1', 'เลขที่');
			$this->excel->getActiveSheet()->setCellValue('D1', 'SAP TR');
			$this->excel->getActiveSheet()->setCellValue('E1', 'สถานะ');
      $this->excel->getActiveSheet()->setCellValue('F1', 'หมายเหตุ');

			$data = $this->document_model->WW($fromDate, $toDate);

			if(!empty($data))
			{
				$no = 1;
				$row = 2;

				foreach($data as $rs)
				{
					$this->excel->getActiveSheet()->setCellValue('A'.$row, $no);
	        $this->excel->getActiveSheet()->setCellValue('B'.$row, thai_date($rs->date_add, FALSE, '/'));
					$this->excel->getActiveSheet()->setCellValue('C'.$row, $rs->code);
	        $this->excel->getActiveSheet()->setCellValue('D'.$row, $rs->inv_code);
	        $this->excel->getActiveSheet()->setCellValue('E'.$row, $this->statusLabel($rs->status, $rs->is_expire));
          if(!empty($rs->reason))
					{
						$this->excel->getActiveSheet()->setCellValue('F'.$row, $rs->reason);
					}
					$no++;
					$row++;
				}
			}
		} //--- end WW


    if(!empty($role['MV']) OR $all)
		{
			$worksheet = new PHPExcel_Worksheet($this->excel, "MV");
			$this->excel->addSheet($worksheet, $index);
			$this->excel->setActiveSheetIndex($index);
			$this->excel->getActiveSheet()->setTitle('MV');

			$index++;

			//--- set Table header
			$this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(8);
			$this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(15);
			$this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(15);
			$this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(15);
			$this->excel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
			$this->excel->getActiveSheet()->getColumnDimension('F')->setWidth(15);
			$this->excel->getActiveSheet()->getColumnDimension('G')->setWidth(20);

			$this->excel->getActiveSheet()->setCellValue('A1', 'ลำดับ');
			$this->excel->getActiveSheet()->setCellValue('B1', 'วันที่');
			$this->excel->getActiveSheet()->setCellValue('C1', 'เลขที่');
			$this->excel->getActiveSheet()->setCellValue('D1', 'SAP TR');
			$this->excel->getActiveSheet()->setCellValue('E1', 'สถานะ');
      $this->excel->getActiveSheet()->setCellValue('F1', 'หมายเหตุ');

			$data = $this->document_model->MV($fromDate, $toDate);

			if(!empty($data))
			{
				$no = 1;
				$row = 2;

				foreach($data as $rs)
				{
					$this->excel->getActiveSheet()->setCellValue('A'.$row, $no);
	        $this->excel->getActiveSheet()->setCellValue('B'.$row, thai_date($rs->date_add, FALSE, '/'));
					$this->excel->getActiveSheet()->setCellValue('C'.$row, $rs->code);
	        $this->excel->getActiveSheet()->setCellValue('D'.$row, $rs->inv_code);
	        $this->excel->getActiveSheet()->setCellValue('E'.$row, $this->statusLabel($rs->status, $rs->is_expire));          

					$no++;
					$row++;
				}
			}
		} //--- end MV


		if(!empty($role['WG']) OR $all)
		{
			$worksheet = new PHPExcel_Worksheet($this->excel, "WG");
			$this->excel->addSheet($worksheet, $index);
			$this->excel->setActiveSheetIndex($index);
			$this->excel->getActiveSheet()->setTitle('WG');

			$index++;

			//--- set Table header
			$this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(8);
			$this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(15);
			$this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(15);
			$this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(15);
			$this->excel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
			$this->excel->getActiveSheet()->getColumnDimension('F')->setWidth(15);
			$this->excel->getActiveSheet()->getColumnDimension('G')->setWidth(20);

			$this->excel->getActiveSheet()->setCellValue('A1', 'ลำดับ');
			$this->excel->getActiveSheet()->setCellValue('B1', 'วันที่');
			$this->excel->getActiveSheet()->setCellValue('C1', 'เลขที่');
			$this->excel->getActiveSheet()->setCellValue('D1', 'Base Ref');
			$this->excel->getActiveSheet()->setCellValue('E1', 'SAP Goods Issue');
			$this->excel->getActiveSheet()->setCellValue('F1', 'สถานะ');
      $this->excel->getActiveSheet()->setCellValue('G1', 'หมายเหตุ');

			$data = $this->document_model->WG($fromDate, $toDate);

			if(!empty($data))
			{
				$no = 1;
				$row = 2;

				foreach($data as $rs)
				{
					$this->excel->getActiveSheet()->setCellValue('A'.$row, $no);
	        $this->excel->getActiveSheet()->setCellValue('B'.$row, thai_date($rs->date_add, FALSE, '/'));
					$this->excel->getActiveSheet()->setCellValue('C'.$row, $rs->code);
					$this->excel->getActiveSheet()->setCellValue('D'.$row, $rs->reference);
	        $this->excel->getActiveSheet()->setCellValue('E'.$row, $rs->inv_code);
	        $this->excel->getActiveSheet()->setCellValue('F'.$row, ($rs->status == 0 ? 'Pending' : ($rs->status == 1 ? 'Success' : ($rs->status == 2 ? 'Canceled' : 'WMS'))));
          if(!empty($rs->reason))
					{
						$this->excel->getActiveSheet()->setCellValue('G'.$row, $rs->reason);
					}
					$no++;
					$row++;
				}
			}
		} //--- end WG


		if(!empty($role['RT']) OR $all)
		{
			$worksheet = new PHPExcel_Worksheet($this->excel, "RT");
			$this->excel->addSheet($worksheet, $index);
			$this->excel->setActiveSheetIndex($index);
			$this->excel->getActiveSheet()->setTitle('RT');

			$index++;

			//--- set Table header
			$this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(8);
			$this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(15);
			$this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(15);
			$this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(15);
			$this->excel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
			$this->excel->getActiveSheet()->getColumnDimension('F')->setWidth(15);
			$this->excel->getActiveSheet()->getColumnDimension('G')->setWidth(20);

			$this->excel->getActiveSheet()->setCellValue('A1', 'ลำดับ');
			$this->excel->getActiveSheet()->setCellValue('B1', 'วันที่');
			$this->excel->getActiveSheet()->setCellValue('C1', 'เลขที่');
			$this->excel->getActiveSheet()->setCellValue('D1', 'Base Ref');
			$this->excel->getActiveSheet()->setCellValue('E1', 'SAP Goods Issue');
			$this->excel->getActiveSheet()->setCellValue('F1', 'สถานะ');
      $this->excel->getActiveSheet()->setCellValue('G1', 'หมายเหตุ');

			$data = $this->document_model->RT($fromDate, $toDate);

			if(!empty($data))
			{
				$no = 1;
				$row = 2;

				foreach($data as $rs)
				{
					$this->excel->getActiveSheet()->setCellValue('A'.$row, $no);
	        $this->excel->getActiveSheet()->setCellValue('B'.$row, thai_date($rs->date_add, FALSE, '/'));
					$this->excel->getActiveSheet()->setCellValue('C'.$row, $rs->code);
					$this->excel->getActiveSheet()->setCellValue('D'.$row, $rs->reference);
	        $this->excel->getActiveSheet()->setCellValue('E'.$row, $rs->inv_code);
	        $this->excel->getActiveSheet()->setCellValue('F'.$row, $this->statusLabel($rs->status, $rs->is_expire));
          if(!empty($rs->reason))
					{
						$this->excel->getActiveSheet()->setCellValue('G'.$row, $rs->reason);
					}
					$no++;
					$row++;
				}
			}
		} //--- end RT


		if(!empty($role['RN']) OR $all)
		{
			$worksheet = new PHPExcel_Worksheet($this->excel, "RN");
			$this->excel->addSheet($worksheet, $index);
			$this->excel->setActiveSheetIndex($index);
			$this->excel->getActiveSheet()->setTitle('RN');

			$index++;

			//--- set Table header
			$this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(8);
			$this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(15);
			$this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(15);
			$this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(15);
			$this->excel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
			$this->excel->getActiveSheet()->getColumnDimension('F')->setWidth(15);
			$this->excel->getActiveSheet()->getColumnDimension('G')->setWidth(20);

			$this->excel->getActiveSheet()->setCellValue('A1', 'ลำดับ');
			$this->excel->getActiveSheet()->setCellValue('B1', 'วันที่');
			$this->excel->getActiveSheet()->setCellValue('C1', 'เลขที่');
			$this->excel->getActiveSheet()->setCellValue('D1', 'Base Ref');
			$this->excel->getActiveSheet()->setCellValue('E1', 'SAP TR');
			$this->excel->getActiveSheet()->setCellValue('F1', 'สถานะ');
      $this->excel->getActiveSheet()->setCellValue('G1', 'หมายเหตุ');

			$data = $this->document_model->RN($fromDate, $toDate);

			if(!empty($data))
			{
				$no = 1;
				$row = 2;

				foreach($data as $rs)
				{
					$this->excel->getActiveSheet()->setCellValue('A'.$row, $no);
	        $this->excel->getActiveSheet()->setCellValue('B'.$row, thai_date($rs->date_add, FALSE, '/'));
					$this->excel->getActiveSheet()->setCellValue('C'.$row, $rs->code);
					$this->excel->getActiveSheet()->setCellValue('D'.$row, $rs->reference);
	        $this->excel->getActiveSheet()->setCellValue('E'.$row, $rs->inv_code);
	        $this->excel->getActiveSheet()->setCellValue('F'.$row, $this->statusLabel($rs->status, $rs->is_expire));
          if(!empty($rs->reason))
					{
						$this->excel->getActiveSheet()->setCellValue('G'.$row, $rs->reason);
					}
					$no++;
					$row++;
				}
			}
		} //--- end RT


		if(!empty($role['SM']) OR $all)
		{
			$worksheet = new PHPExcel_Worksheet($this->excel, "SM");
			$this->excel->addSheet($worksheet, $index);
			$this->excel->setActiveSheetIndex($index);
			$this->excel->getActiveSheet()->setTitle('SM');

			$index++;

			//--- set Table header
			$this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(8);
			$this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(15);
			$this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(15);
			$this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(15);
			$this->excel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
			$this->excel->getActiveSheet()->getColumnDimension('F')->setWidth(15);
			$this->excel->getActiveSheet()->getColumnDimension('G')->setWidth(20);

			$this->excel->getActiveSheet()->setCellValue('A1', 'ลำดับ');
			$this->excel->getActiveSheet()->setCellValue('B1', 'วันที่');
			$this->excel->getActiveSheet()->setCellValue('C1', 'เลขที่');
			$this->excel->getActiveSheet()->setCellValue('D1', 'Base Ref');
			$this->excel->getActiveSheet()->setCellValue('E1', 'SAP SM');
			$this->excel->getActiveSheet()->setCellValue('F1', 'สถานะ');
      $this->excel->getActiveSheet()->setCellValue('G1', 'หมายเหตุ');

			$data = $this->document_model->SM($fromDate, $toDate);

			if(!empty($data))
			{
				$no = 1;
				$row = 2;

				foreach($data as $rs)
				{
					$this->excel->getActiveSheet()->setCellValue('A'.$row, $no);
	        $this->excel->getActiveSheet()->setCellValue('B'.$row, thai_date($rs->date_add, FALSE, '/'));
					$this->excel->getActiveSheet()->setCellValue('C'.$row, $rs->code);
					$this->excel->getActiveSheet()->setCellValue('D'.$row, $rs->reference);
	        $this->excel->getActiveSheet()->setCellValue('E'.$row, $rs->inv_code);
	        $this->excel->getActiveSheet()->setCellValue('F'.$row, $this->statusLabel($rs->status, $rs->is_expire));
          if(!empty($rs->reason))
					{
						$this->excel->getActiveSheet()->setCellValue('G'.$row, $rs->reason);
					}
					$no++;
					$row++;
				}
			}
		} //--- end SM


		if(!empty($role['CN']) OR $all)
		{
			$worksheet = new PHPExcel_Worksheet($this->excel, "CN");
			$this->excel->addSheet($worksheet, $index);
			$this->excel->setActiveSheetIndex($index);
			$this->excel->getActiveSheet()->setTitle('CN');

			$index++;

			//--- set Table header
			$this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(8);
			$this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(15);
			$this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(15);
			$this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(15);
			$this->excel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
			$this->excel->getActiveSheet()->getColumnDimension('F')->setWidth(15);
			$this->excel->getActiveSheet()->getColumnDimension('G')->setWidth(20);

			$this->excel->getActiveSheet()->setCellValue('A1', 'ลำดับ');
			$this->excel->getActiveSheet()->setCellValue('B1', 'วันที่');
			$this->excel->getActiveSheet()->setCellValue('C1', 'เลขที่');
			$this->excel->getActiveSheet()->setCellValue('D1', 'Base Ref');
			$this->excel->getActiveSheet()->setCellValue('E1', 'SAP SM');
			$this->excel->getActiveSheet()->setCellValue('F1', 'สถานะ');
      $this->excel->getActiveSheet()->setCellValue('G1', 'หมายเหตุ');

			$data = $this->document_model->CN($fromDate, $toDate);

			if(!empty($data))
			{
				$no = 1;
				$row = 2;

				foreach($data as $rs)
				{
					$this->excel->getActiveSheet()->setCellValue('A'.$row, $no);
	        $this->excel->getActiveSheet()->setCellValue('B'.$row, thai_date($rs->date_add, FALSE, '/'));
					$this->excel->getActiveSheet()->setCellValue('C'.$row, $rs->code);
					$this->excel->getActiveSheet()->setCellValue('D'.$row, $rs->reference);
	        $this->excel->getActiveSheet()->setCellValue('E'.$row, $rs->inv_code);
	        $this->excel->getActiveSheet()->setCellValue('F'.$row, ($rs->status == 0 ? 'Pending' : ($rs->status == 1 ? 'Success' : ($rs->status == 2 ? 'Canceled' : 'WMS'))));
          if(!empty($rs->reason))
					{
						$this->excel->getActiveSheet()->setCellValue('G'.$row, $rs->reason);
					}
					$no++;
					$row++;
				}
			}
		} //--- end CN


		if(!empty($role['WA']) OR $all)
		{
			$worksheet = new PHPExcel_Worksheet($this->excel, "WA");
			$this->excel->addSheet($worksheet, $index);
			$this->excel->setActiveSheetIndex($index);
			$this->excel->getActiveSheet()->setTitle('WA');

			$index++;

			//--- set Table header
			$this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(8);
			$this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(15);
			$this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(15);
			$this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(15);
			$this->excel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
			$this->excel->getActiveSheet()->getColumnDimension('F')->setWidth(15);
			$this->excel->getActiveSheet()->getColumnDimension('G')->setWidth(20);

			$this->excel->getActiveSheet()->setCellValue('A1', 'ลำดับ');
			$this->excel->getActiveSheet()->setCellValue('B1', 'วันที่');
			$this->excel->getActiveSheet()->setCellValue('C1', 'เลขที่');
			$this->excel->getActiveSheet()->setCellValue('D1', 'SAP Goods Issue');
			$this->excel->getActiveSheet()->setCellValue('E1', 'SAP Goods Receipt');
			$this->excel->getActiveSheet()->setCellValue('F1', 'สถานะ');
      $this->excel->getActiveSheet()->setCellValue('G1', 'หมายเหตุ');

			$data = $this->document_model->WA($fromDate, $toDate);

			if(!empty($data))
			{
				$no = 1;
				$row = 2;

				foreach($data as $rs)
				{
					$this->excel->getActiveSheet()->setCellValue('A'.$row, $no);
	        $this->excel->getActiveSheet()->setCellValue('B'.$row, thai_date($rs->date_add, FALSE, '/'));
					$this->excel->getActiveSheet()->setCellValue('C'.$row, $rs->code);
					$this->excel->getActiveSheet()->setCellValue('D'.$row, $rs->issue_code);
	        $this->excel->getActiveSheet()->setCellValue('E'.$row, $rs->receive_code);
	        $this->excel->getActiveSheet()->setCellValue('F'.$row, ($rs->status == 0 ? 'Pending' : ($rs->status == 1 ? 'Success' : ($rs->status == 2 ? 'Canceled' : 'Unknow'))));
          if(!empty($rs->reason))
					{
						$this->excel->getActiveSheet()->setCellValue('G'.$row, $rs->reason);
					}
					$no++;
					$row++;
				}
			}
		} //--- end WA

		if(!empty($role['AC']) OR $all)
		{
			$worksheet = new PHPExcel_Worksheet($this->excel, "AC");
			$this->excel->addSheet($worksheet, $index);
			$this->excel->setActiveSheetIndex($index);
			$this->excel->getActiveSheet()->setTitle('AC');

			$index++;

			//--- set Table header
			$this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(8);
			$this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(15);
			$this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(15);
			$this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(15);
			$this->excel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
			$this->excel->getActiveSheet()->getColumnDimension('F')->setWidth(15);
			$this->excel->getActiveSheet()->getColumnDimension('G')->setWidth(15);
      $this->excel->getActiveSheet()->getColumnDimension('H')->setWidth(15);

			$this->excel->getActiveSheet()->setCellValue('A1', 'ลำดับ');
			$this->excel->getActiveSheet()->setCellValue('B1', 'วันที่');
			$this->excel->getActiveSheet()->setCellValue('C1', 'เลขที่');
			$this->excel->getActiveSheet()->setCellValue('D1', 'Base ref');
			$this->excel->getActiveSheet()->setCellValue('E1', 'SAP Goods Issue');
			$this->excel->getActiveSheet()->setCellValue('F1', 'SAP Goods Receipt');
			$this->excel->getActiveSheet()->setCellValue('G1', 'สถานะ');
      $this->excel->getActiveSheet()->setCellValue('H1', 'หมายเหตุ');

			$data = $this->document_model->AC($fromDate, $toDate);

			if(!empty($data))
			{
				$no = 1;
				$row = 2;

				foreach($data as $rs)
				{
					$this->excel->getActiveSheet()->setCellValue('A'.$row, $no);
	        $this->excel->getActiveSheet()->setCellValue('B'.$row, thai_date($rs->date_add, FALSE, '/'));
					$this->excel->getActiveSheet()->setCellValue('C'.$row, $rs->code);
					$this->excel->getActiveSheet()->setCellValue('D'.$row, $rs->reference);
	        $this->excel->getActiveSheet()->setCellValue('E'.$row, $rs->issue_code);
					$this->excel->getActiveSheet()->setCellValue('F'.$row, $rs->receive_code);
	        $this->excel->getActiveSheet()->setCellValue('G'.$row, ($rs->status == 0 ? 'Pending' : ($rs->status == 1 ? 'Success' : ($rs->status == 2 ? 'Canceled' : 'Unknow'))));
          if(!empty($rs->reason))
					{
						$this->excel->getActiveSheet()->setCellValue('H'.$row, $rs->reason);
					}
					$no++;
					$row++;
				}
			}
		} //--- end AC


    setToken($token);
    $file_name = "รายงานทะเบียนคุมเอกสารแยกตามประเภท ".date('Ymd').".xlsx";
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'); /// form excel 2007 XLSX
    header('Content-Disposition: attachment;filename="'.$file_name.'"');
    $writer = PHPExcel_IOFactory::createWriter($this->excel, 'Excel2007');
    $writer->save('php://output');

  }


  public function statusLabel($status, $is_expire = 0)
  {
    $label = "Unknow";

    if($is_expire == 1)
    {
      $label = "Expired";
    }
    else
    {
      switch($status)
      {
        case -1 :
          $label = "Draft";
          break;
        case 0 :
          $label = "Pending";
          break;
        case 1 :
          $label = "Success";
          break;
        case 2 :
          $label = "Canceled";
          break;
        case 3 :
          $label = "WMS";
          break;
        case 4 :
          $label = "Acception";
          break;
      }
    }

    return $label;
  }

} //--- end class








 ?>
