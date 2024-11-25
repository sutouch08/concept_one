<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Inbound_document_audit extends PS_Controller
{
  public $menu_code = 'RAIXIB';
	public $menu_group_code = 'RE';
  public $menu_sub_group_code = 'REAUDIT';
	public $title = 'รายงาน กระทบเลขที่เอกสารขาเข้า IX-WMS-SAP';
  public $filter;
	public $wms;
	public $limit = 2000;

  public function __construct()
  {
    parent::__construct();
		$this->wms = $this->load->database('wms', TRUE);
    $this->home = base_url().'report/audit/inbound_document_audit';
    $this->load->model('report/audit/document_audit_model');
  }

  public function index()
  {
    $this->load->view('report/audit/inbound_document_audit');
  }


  public function get_report()
  {
    $sc = array();

		$SapDoc = array(
			"RT" => "GR", //--- Goods receipt OIGN
			"RN" => "TR", //--- transfer OWTR
			"SM" => "DN", //--- Return order ORDN
			"WR" => "GRPO", //--- Goods receipt PO  OPDN
			"WX" => "", //--- ไม่มีเอกสารใน SAP
			"WW" => "TR" //---- OWTR
		);

		$stateName = array(
			'0' => "ยังไม่บันทึก",
			'1' => "รับเข้าแล้ว",
			'2' => "ยกเลิก",
			'3' => "รอรับสินค้า"
		);

    $allDoc = $this->input->get('allDoc');
    $docFrom = $this->input->get('docFrom');
    $docTo = $this->input->get('docTo');

    if($docFrom > $docTo)
		{
      $sp = $docTo;
      $docTo = $docFrom;
      $docFrom = $sp;
    }

    $fromDate = $this->input->get('fromDate');
    $toDate = $this->input->get('toDate');

    $allRole = $this->input->get('allRole');

		if($allRole != 1)
		{
			$role = $this->input->get('role');
		}
		else
		{
			$role = array("RT", "RN", "SM", "WR", "WX", "WW");
		}

		$allState = $this->input->get('allState');

		if($allState != 1)
		{
			$state = $this->input->get('state');
		}
		else
		{
			$state = array("0", "1", "2", "3");
		}

    $arr = array(
      'allDoc' => $allDoc,
      'docFrom' => $docFrom,
      'docTo' => $docTo,
			'state' => $state,
      'fromDate' => from_date($fromDate),
      'toDate' => to_date($toDate)
    );

    if(!empty($role))
    {
			$table = array(
				"RT" => "receive_transform",
				"RN" => "return_lend",
				"SM" => "return_order",
				"WR" => "receive_product",
				"WX" => "consign_check",
				"WW" => "transfer"
			);

			$sapTable = array(
				"RT" => "OIGN",
				"RN" => "OWTR",
				"SM" => "ORDN",
				"WR" => "OPDN",
				"WW" => "OWTR"
			);

			$no = 1;
			foreach($role as $doc_type)
			{
				$result = $this->document_audit_model->get_ix_receive_data($table[$doc_type], $arr);

				if(!empty($result))
				{
					foreach($result as $rs)
		      {
						if($no > $this->limit)
						{
							echo "จำนวนรายการมากกว่า {$this->limit} กรุณาส่งออกเป็นไฟล์แทนการแสดงผล";
							exit;
						}

						$docNum = "";

						if($rs->status == 1 && $doc_type !== "WX")
						{
							$docNum = $this->document_audit_model->get_doc_num($sapTable[$doc_type], $rs->order_code);
						}


		        $ds = array(
		          'no' => number($no),
		          'date' => thai_date($rs->date_add, FALSE, '/'),
		          'ix_code' => $rs->order_code,
							'ix_type' => $doc_type,
							'wms_code' => $rs->temp_code,
							'wms_type' => $rs->temp_type,
							'sap_code' => $docNum,
							'sap_type' => empty($docNum) ? "" : $SapDoc[$doc_type],
							'ix_state' => $stateName[$rs->status]
		        );

		        array_push($sc, $ds);

		        $no++;
		      }
				}
			}

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

		$SapDoc = array(
			"RT" => "GR", //--- Goods receipt OIGN
			"RN" => "TR", //--- transfer OWTR
			"SM" => "DN", //--- Return order ORDN
			"WR" => "GRPO", //--- Goods receipt PO  OPDN
			"WX" => "", //--- ไม่มีเอกสารใน SAP
			"WW" => "TR" //---- OWTR
		);

		$stateName = array(
			'0' => "ยังไม่บันทึก",
			'1' => "รับเข้าแล้ว",
			'2' => "ยกเลิก",
			'3' => "รอรับสินค้า"
		);

    $allDoc = $this->input->post('allDoc');
    $docFrom = $this->input->post('docFrom');
    $docTo = $this->input->post('docTo');

    if($docFrom > $docTo)
		{
      $sp = $docTo;
      $docTo = $docFrom;
      $docFrom = $sp;
    }

    $fromDate = $this->input->post('fromDate');
    $toDate = $this->input->post('toDate');

    $allRole = $this->input->post('allRole');

		if($allRole != 1)
		{
			$role = $this->input->post('role');
		}
		else
		{
			$role = array("RT", "RN", "SM", "WR", "WX", "WW");
		}

		$allState = $this->input->post('allState');

		if($allState != 1)
		{
			$state = $this->input->post('state');
		}
		else
		{
			$state = array("0", "1", "2", "3");
		}


		$role_in = "";

		if($allRole != 1)
		{
			$i = 1;
			foreach($role as $ro)
			{
				$role_in .= $i === 1 ? $ro : ", ".$ro;
				$i++;
			}
		}


		$state_in = "";

		if($allState != 1)
		{
			$i = 1;
			foreach($state as $st)
			{
				$state_in .= $i === 1 ? $stateName[$st] : ", ".$stateName[$st];
				$i++;
			}
		}


		$arr = array(
      'allDoc' => $allDoc,
      'docFrom' => $docFrom,
      'docTo' => $docTo,
			'state' => $state,
      'fromDate' => from_date($fromDate),
      'toDate' => to_date($toDate)
    );

    $title = "รายงาน กระทบเลขที่เอกสารขาเข้า IX-WMS-SAP ";
		$dateTitle = "วันที่ (".thai_date($fromDate, FALSE, '/').") - (".thai_date($toDate, FALSE, '/').")";
    $docTitle = $allDoc == 1 ? 'ทั้งหมด' : "{$docFrom} - {$docTo}";
    $roleTitle = $allRole == 1 ? 'ทั้งหมด' : $role_in;
    $stateTitle = $allState == 1 ? 'ทั้งหมด' : $state_in;

    //--- load excel library
    $this->load->library('excel');

    $this->excel->setActiveSheetIndex(0);
    $this->excel->getActiveSheet()->setTitle('Receive PO BY Document');

    //--- set report title header
    $this->excel->getActiveSheet()->setCellValue('A1', $title);
    $this->excel->getActiveSheet()->mergeCells('A1:I1');
		$this->excel->getActiveSheet()->setCellValue('A2', $dateTitle);
    $this->excel->getActiveSheet()->mergeCells('A2:I2');
    $this->excel->getActiveSheet()->setCellValue('A3', "เลขที่เอกสาร : {$docTitle}");
    $this->excel->getActiveSheet()->mergeCells('A3:I3');
    $this->excel->getActiveSheet()->setCellValue('A4', "ประเภทเอกสาร : {$roleTitle}");
    $this->excel->getActiveSheet()->mergeCells('A4:I4');
    $this->excel->getActiveSheet()->setCellValue('A5', "สถานะเอกสาร : {$stateTitle}");
    $this->excel->getActiveSheet()->mergeCells('A5:I5');

    //--- set Table header
    $this->excel->getActiveSheet()->setCellValue('A6', 'ลำดับ');
    $this->excel->getActiveSheet()->setCellValue('B6', 'วันที่');
    $this->excel->getActiveSheet()->setCellValue('C6', 'IX');
    $this->excel->getActiveSheet()->setCellValue('D6', 'Type(IX)');
    $this->excel->getActiveSheet()->setCellValue('E6', 'WMS');
    $this->excel->getActiveSheet()->setCellValue('F6', 'Type(WMS)');
    $this->excel->getActiveSheet()->setCellValue('G6', 'SAP');
    $this->excel->getActiveSheet()->setCellValue('H6', 'Type(SAP)');
    $this->excel->getActiveSheet()->setCellValue('I6', 'สถานะ (IX)');

    $row = 7;

		$table = array(
			"RT" => "receive_transform",
			"RN" => "return_lend",
			"SM" => "return_order",
			"WR" => "receive_product",
			"WX" => "consign_check",
			"WW" => "transfer"
		);

		$sapTable = array(
			"RT" => "OIGN",
			"RN" => "OWTR",
			"SM" => "ORDN",
			"WR" => "OPDN",
			"WW" => "OWTR"
		);

		$no = 1;

		foreach($role as $doc_type)
		{
			$result = $this->document_audit_model->get_ix_receive_data($table[$doc_type], $arr);

			if(!empty($result))
			{
				foreach($result as $rs)
				{

					$docNum = "";

					if($rs->status == 1 && $doc_type !== "WX")
					{
						$docNum = $this->document_audit_model->get_doc_num($sapTable[$doc_type], $rs->order_code);
					}


					$this->excel->getActiveSheet()->setCellValue('A'.$row, $no);
	        $this->excel->getActiveSheet()->setCellValue('B'.$row, thai_date($rs->date_add, FALSE, '/'));
	        $this->excel->getActiveSheet()->setCellValue('C'.$row, $rs->order_code);
	        $this->excel->getActiveSheet()->setCellValue('D'.$row, $doc_type);
	        $this->excel->getActiveSheet()->setCellValue('E'.$row, $rs->temp_code);
	        $this->excel->getActiveSheet()->setCellValue('F'.$row, $rs->temp_type);
	        $this->excel->getActiveSheet()->setCellValue('G'.$row, $docNum);
	        $this->excel->getActiveSheet()->setCellValue('H'.$row, (empty($docNum) ? NULL :$SapDoc[$doc_type]));
	        $this->excel->getActiveSheet()->setCellValue('I'.$row, $stateName[$rs->status]);

	        $no++;
	        $row++;
				}
			}
		}


    setToken($token);
    $file_name = "รายงานกระทบเลขที่เอกสารขาเข้า IX-WMS-SAP ".date('Ymd').".xlsx";
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'); /// form excel 2007 XLSX
    header('Content-Disposition: attachment;filename="'.$file_name.'"');
    $writer = PHPExcel_IOFactory::createWriter($this->excel, 'Excel2007');
    $writer->save('php://output');

  }


} //--- end class








 ?>
