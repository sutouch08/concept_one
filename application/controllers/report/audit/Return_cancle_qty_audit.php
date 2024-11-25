<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Return_cancle_qty_audit extends PS_Controller
{
  public $menu_code = 'RAIXRC';
	public $menu_group_code = 'RE';
  public $menu_sub_group_code = 'REAUDIT';
	public $title = 'รายงาน กระทบยอดรับเข้าจากการยกเลิกออเดอร์ WMS';
  public $filter;
	public $wms;
	public $limit = 2000;

  public function __construct()
  {
    parent::__construct();
		$this->wms = $this->load->database('wms', TRUE);
    $this->home = base_url().'report/audit/return_cancle_qty_audit';
    $this->load->model('report/audit/document_audit_model');
		$this->load->model('masters/channels_model');
  }

  public function index()
  {
		$this->load->helper('channels');
    $this->load->view('report/audit/return_cancle_qty_audit');
  }


	public function get_report()
  {
    $sc = array();
		$roleName = array(
			"S" => "WO",
			"C" => "WC",
			"N" => "WT",
			"P" => "WS",
			"U" => "WU",
			"L" => "WL",
			"T" => "WQ",
			"Q" => "WV"
		);

		$SapDoc = array(
			"S" => "DO",
			"C" => "DO",
			"N" => "TR",
			"P" => "DO",
			"U" => "DO",
			"L" => "TR",
			"T" => "TR",
			"Q" => "TR"
		);

		$channelsName = $this->channels_model->get_channels_array();

		$channels = $this->input->get('channels');

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
		$cancleFromDate = $this->input->get('cancleFromDate');
		$cancleToDate = $this->input->get('cancleToDate');

    $allRole = $this->input->get('allRole');

		if($allRole != 1)
		{
			$role = $this->input->get('role');
		}
		else
		{
			$role = array("S", "C", "N", "P", "U", "L", "T", "Q");
		}

    $arr = array(
      'allDoc' => $allDoc,
      'docFrom' => $docFrom,
      'docTo' => $docTo,
      'role' => $role,
      'fromDate' => (empty($fromDate) ? NULL : from_date($fromDate)),
      'toDate' => (empty($toDate) ? NULL : to_date($toDate)),
			'cancleFromDate' => (empty($cancleFromDate) ? NULL : from_date($cancleFromDate)),
			'cancleToDate' => (empty($cancleToDate) ? NULL : to_date($cancleToDate)),
			'channels' => $channels
    );

    $result = $this->document_audit_model->get_ix_return_cancle_data_qty($arr);

    if(!empty($result))
    {
			if(count($result) > $this->limit)
			{
				echo "จำนวนรายการมากกว่า {$this->limit} รายการ กรุณาส่งออกเป็นไฟล์แทนการแสดงผลหน้าจอ";
				exit;
			}

      $no = 1;
      foreach($result as $rs)
      {
				$sap = NULL;

				if($rs->role == 'S' OR $rs->role == 'C' OR $rs->role == 'P' OR $rs->role == 'U')
				{
					$sap = $this->document_audit_model->get_do_code_and_qty($rs->order_code);
				}

				if($rs->role == 'N' OR $rs->role == 'L' OR $rs->role == 'T' OR $rs->role == 'Q')
				{
					$sap = $this->document_audit_model->get_tr_code_and_qty($rs->order_code);
				}

				$sap_qty = (empty($sap) ? 0 : number($sap->qty));
				$hilight = "";

        $ds = array(
          'no' => number($no),
          'date' => thai_date($rs->date_add, FALSE, '/'),
					'cancle_date' => thai_date($rs->cancle_date, FALSE, '/'),
          'ix_code' => $rs->order_code,
					'ix_type' => $roleName[$rs->role],
					'wms_code' => $rs->temp_code,
					'wms_type' => 'RC',
					'sap_code' => (empty($sap) ? "" : $sap->DocNum),
					'sap_type' => $SapDoc[$rs->role],
					'channels' => (empty($rs->channels_code) ? "" : $channelsName[$rs->channels_code]),
					'ix_qty' => number($rs->order_qty),
					'wms_qty' => number($rs->temp_qty),
					'sap_qty' => $sap_qty,
					'hilight' => $hilight
        );

        array_push($sc, $ds);

        $no++;
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
		$roleName = array(
			"S" => "WO",
			"C" => "WC",
			"N" => "WT",
			"P" => "WS",
			"U" => "WU",
			"L" => "WL",
			"T" => "WQ",
			"Q" => "WV"
		);

		$SapDoc = array(
			"S" => "DO",
			"C" => "DO",
			"N" => "TR",
			"P" => "DO",
			"U" => "DO",
			"L" => "TR",
			"T" => "TR",
			"Q" => "TR"
		);


		$channelsName = $this->channels_model->get_channels_array();

		$channels = $this->input->post('channels');

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
		$cancleFromDate = $this->input->post('cancleFromDate');
		$cancleToDate = $this->input->post('cancleToDate');

    $allRole = $this->input->post('allRole');
		$role_in = "";
		if($allRole != 1)
		{
			$role = $this->input->post('role');
			$i = 1;
			foreach($role as $ro)
			{
				$role_in .= $i === 1 ? $roleName[$ro] : ", ".$roleName[$ro];
				$i++;
			}
		}
		else
		{
			$role = array("S", "C", "N", "P", "U", "L", "T", "Q");
		}

		$arr = array(
      'allDoc' => $allDoc,
      'docFrom' => $docFrom,
      'docTo' => $docTo,
      'role' => $role,
      'fromDate' => (empty($fromDate) ? NULL : from_date($fromDate)),
      'toDate' => (empty($toDate) ? NULL : to_date($toDate)),
			'cancleFromDate' => (empty($cancleFromDate) ? NULL : from_date($cancleFromDate)),
			'cancleToDate' => (empty($cancleToDate) ? NULL : to_date($cancleToDate)),
			'channels' => $channels
    );

    $title = "รายงาน กระทบยอดรับเข้าจากการยกเลิกออเดอร์ WMS";
		$dateTitle = "วันที่ (".thai_date($fromDate, FALSE, '/').") - (".thai_date($toDate, FALSE, '/').")";
    $docTitle = $allDoc == 1 ? 'ทั้งหมด' : "{$docFrom} - {$docTo}";
    $roleTitle = $allRole == 1 ? 'ทั้งหมด' : $role_in;
		$channelsTitle = $channels == 'all' ? 'ทั้งหมด' : $channelsName[$channels];


    $result = $this->document_audit_model->get_ix_return_cancle_data_qty($arr);

    //--- load excel library
    $this->load->library('excel');

    $this->excel->setActiveSheetIndex(0);
    $this->excel->getActiveSheet()->setTitle('Document Qty audit');

    //--- set report title header
    $this->excel->getActiveSheet()->setCellValue('A1', $title);
    $this->excel->getActiveSheet()->mergeCells('A1:I1');
		$this->excel->getActiveSheet()->setCellValue('A2', $dateTitle);
    $this->excel->getActiveSheet()->mergeCells('A2:I2');
    $this->excel->getActiveSheet()->setCellValue('A3', "เลขที่เอกสาร : {$docTitle}");
    $this->excel->getActiveSheet()->mergeCells('A3:I3');
    $this->excel->getActiveSheet()->setCellValue('A4', "ประเภทเอกสาร : {$roleTitle}");
    $this->excel->getActiveSheet()->mergeCells('A4:I4');
    $this->excel->getActiveSheet()->setCellValue('A5', "ช่องทางขาย : {$channelsTitle}");
    $this->excel->getActiveSheet()->mergeCells('A5:I5');

    //--- set Table header
    $this->excel->getActiveSheet()->setCellValue('A6', 'ลำดับ');
    $this->excel->getActiveSheet()->setCellValue('B6', 'วันที่เอกสาร(IX)');
		$this->excel->getActiveSheet()->setCellValue('C6', 'วันที่ยกเลิก(IX)');
    $this->excel->getActiveSheet()->setCellValue('D6', 'IX');
    $this->excel->getActiveSheet()->setCellValue('E6', 'WMS');
    $this->excel->getActiveSheet()->setCellValue('F6', 'SAP');
    $this->excel->getActiveSheet()->setCellValue('G6', 'QTY(IX)');
    $this->excel->getActiveSheet()->setCellValue('H6', 'QTY(WMS)');
    $this->excel->getActiveSheet()->setCellValue('I6', 'QTY(SAP)');
    $this->excel->getActiveSheet()->setCellValue('J6', 'สถานะ (IX)');
		$this->excel->getActiveSheet()->setCellValue('K6', 'ช่องทางขาย');

    $row = 7;

    if(!empty($result))
    {
      $no = 1;

      foreach($result as $rs)
      {
				$sap = NULL;

				if($rs->role == 'S' OR $rs->role == 'C' OR $rs->role == 'P' OR $rs->role == 'U')
				{
					$sap = $this->document_audit_model->get_do_code_and_qty($rs->order_code);
				}

				if($rs->role == 'N' OR $rs->role == 'L' OR $rs->role == 'T' OR $rs->role == 'Q')
				{
					$sap = $this->document_audit_model->get_tr_code_and_qty($rs->order_code);
				}

        $this->excel->getActiveSheet()->setCellValue('A'.$row, $no);
        $this->excel->getActiveSheet()->setCellValue('B'.$row, thai_date($rs->date_add, FALSE, '/'));
				$this->excel->getActiveSheet()->setCellValue('C'.$row, thai_date($rs->cancle_date, FALSE, '/'));
        $this->excel->getActiveSheet()->setCellValue('D'.$row, $rs->order_code);
        $this->excel->getActiveSheet()->setCellValue('E'.$row, $rs->temp_code);
        $this->excel->getActiveSheet()->setCellValue('F'.$row, (empty($sap) ? "" : $sap->DocNum));
        $this->excel->getActiveSheet()->setCellValue('G'.$row, $rs->order_qty);
        $this->excel->getActiveSheet()->setCellValue('H'.$row, $rs->temp_qty);
        $this->excel->getActiveSheet()->setCellValue('I'.$row, (empty($sap) ? 0 : $sap->qty));
				$this->excel->getActiveSheet()->setCellValue('J'.$row, empty($rs->channels_code) ? "" : $channelsName[$rs->channels_code]);

        $no++;
        $row++;
      }
    }

    setToken($token);
    $file_name = "รายงานกระทบยอดรับเข้าจากการยกเลิกออเดอร์ WMS ".date('Ymd').".xlsx";
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'); /// form excel 2007 XLSX
    header('Content-Disposition: attachment;filename="'.$file_name.'"');
    $writer = PHPExcel_IOFactory::createWriter($this->excel, 'Excel2007');
    $writer->save('php://output');

  }


} //--- end class








 ?>
