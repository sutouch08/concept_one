<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Outbound_document_audit extends PS_Controller
{
  public $menu_code = 'RAIXDO';
	public $menu_group_code = 'RE';
  public $menu_sub_group_code = 'REAUDIT';
	public $title = 'รายงาน กระทบเอกสารขาออก IX-WMS-SAP';
  public $filter;
	public $wms;
	public $limit = 2000;

  public function __construct()
  {
    parent::__construct();
		$this->wms = $this->load->database('wms', TRUE);
    $this->home = base_url().'report/audit/outbound_document_audit';
    $this->load->model('report/audit/document_audit_model');
		$this->load->model('masters/channels_model');
		$this->load->model('orders/orders_model');
		$this->load->model('inventory/transfer_model');
  }

  public function index()
  {
		$this->load->helper('channels');
    $this->load->view('report/audit/outbound_document_audit');
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

		$stateName = array(
			'1' => "รอดำเนินการ",
			'2' => "รอชำระเงิน",
			'3' => "รอจัดสินค้า",
			'4' => "กำลังจัด",
			'5' => "รอตรวจ",
			'6' => "กำลังตรวจ",
			'7' => "รอเปิดบิล",
			'8' => "เปิดบิลแล้ว",
			'9' => "ยกเลิก"
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

    $allRole = $this->input->get('allRole');

		if($allRole != 1)
		{
			$role = $this->input->get('role');
		}
		else
		{
			$role = array("S", "C", "N", "P", "U", "L", "T", "Q");
		}

		$allState = $this->input->get('allState');

		if($allState != 1)
		{
			$state = $this->input->get('state');
		}
		else
		{
			$state = array("1", "2", "3", "7", "8", "9");
		}

    $arr = array(
      'allDoc' => $allDoc,
      'docFrom' => $docFrom,
      'docTo' => $docTo,
      'role' => $role,
			'state' => $state,
      'fromDate' => from_date($fromDate),
      'toDate' => to_date($toDate),
			'channels' => $channels
    );

    $result = $this->document_audit_model->get_outbound_data($arr);

    if(!empty($result))
    {
			if(count($result) > $this->limit)
			{
				echo "จำนวนรายการมากกว่า {$this->limit} กรุณาส่งออกเป็นไฟล์แทนการแสดงผล";
				exit;
			}

      $no = 1;
      foreach($result as $rs)
      {
				$docNum = "";
				if($rs->state == 8 && ($rs->role == 'S' OR $rs->role == 'C' OR $rs->role == 'P' OR $rs->role == 'U'))
				{
					$docNum = $this->orders_model->get_sap_doc_num($rs->order_code);
				}

				if($rs->state == 8 && ($rs->role == 'N' OR $rs->role == 'N' OR $rs->role == 'L' OR $rs->role == 'T' OR $rs->role == 'Q'))
				{
					$docNum = $this->transfer_model->get_sap_doc_num($rs->order_code);
				}

        $ds = array(
          'no' => number($no),
          'date' => thai_date($rs->date_add, FALSE, '/'),
          'ix_code' => $rs->order_code,
					'ix_type' => $roleName[$rs->role],
					'wms_code' => $rs->temp_code,
					'wms_type' => 'OB',
					'sap_code' => $docNum,
					'sap_type' => $SapDoc[$rs->role],
					'ix_state' => $stateName[$rs->state],
					'channels' => (empty($rs->channels_code) ? "" : $channelsName[$rs->channels_code])
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

		$stateName = array(
			'1' => "รอดำเนินการ",
			'2' => "รอชำระเงิน",
			'3' => "รอจัดสินค้า",
			'4' => "กำลังจัด",
			'5' => "รอตรวจ",
			'6' => "กำลังตรวจ",
			'7' => "รอเปิดบิล",
			'8' => "เปิดบิลแล้ว",
			'9' => "ยกเลิก"
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

		$allState = $this->input->post('allState');


		$state_in = "";
		if($allState != 1)
		{
			$state = $this->input->post('state');
			$i = 1;
			foreach($state as $st)
			{
				$state_in .= $i === 1 ? $stateName[$st] : ", ".$stateName[$st];
				$i++;
			}
		}
		else
		{
			$state = array("1", "2", "3", "7", "8", "9");
		}

    $arr = array(
      'allDoc' => $allDoc,
      'docFrom' => $docFrom,
      'docTo' => $docTo,
      'role' => $role,
			'state' => $state,
      'fromDate' => from_date($fromDate),
      'toDate' => to_date($toDate),
			'channels' => $channels
    );

    $title = "รายงาน กระทบเลขเอกสารขาออก IX-WMS-SAP ";
		$dateTitle = "วันที่ (".thai_date($fromDate, FALSE, '/').") - (".thai_date($toDate, FALSE, '/').")";
    $docTitle = $allDoc == 1 ? 'ทั้งหมด' : "{$docFrom} - {$docTo}";
    $roleTitle = $allRole == 1 ? 'ทั้งหมด' : $role_in;
    $stateTitle = $allState == 1 ? 'ทั้งหมด' : $state_in;



    $result = $this->document_audit_model->get_outbound_data($arr);

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
		$this->excel->getActiveSheet()->setCellValue('J6', 'ช่องทางขาย');

    $row = 7;

    if(!empty($result))
    {
      $no = 1;

      foreach($result as $rs)
      {
				$docNum = "";
				if($rs->state == 8 && ($rs->role == 'S' OR $rs->role == 'C' OR $rs->role == 'P' OR $rs->role == 'U'))
				{
					$docNum = $this->orders_model->get_sap_doc_num($rs->order_code);
				}

				if($rs->state == 8 && ($rs->role == 'N' OR $rs->role == 'N' OR $rs->role == 'L' OR $rs->role == 'T' OR $rs->role == 'Q'))
				{
					$docNum = $this->transfer_model->get_sap_doc_num($rs->order_code);
				}

        $this->excel->getActiveSheet()->setCellValue('A'.$row, $no);
        $this->excel->getActiveSheet()->setCellValue('B'.$row, thai_date($rs->date_add, FALSE, '/'));
        $this->excel->getActiveSheet()->setCellValue('C'.$row, $rs->order_code);
        $this->excel->getActiveSheet()->setCellValue('D'.$row, $roleName[$rs->role]);
        $this->excel->getActiveSheet()->setCellValue('E'.$row, $rs->temp_code);
        $this->excel->getActiveSheet()->setCellValue('F'.$row, "OB");
        $this->excel->getActiveSheet()->setCellValue('G'.$row, $docNum);
        $this->excel->getActiveSheet()->setCellValue('H'.$row, $SapDoc[$rs->role]);
        $this->excel->getActiveSheet()->setCellValue('I'.$row, $stateName[$rs->state]);
				$this->excel->getActiveSheet()->setCellValue('J'.$row, empty($rs->channels_code) ? "" : $channelsName[$rs->channels_code]);

        $no++;
        $row++;
      }
    }

    setToken($token);
    $file_name = "รายงานกระทบเลขที่เอกสาร IX-WMS-SAP ".date('Ymd').".xlsx";
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'); /// form excel 2007 XLSX
    header('Content-Disposition: attachment;filename="'.$file_name.'"');
    $writer = PHPExcel_IOFactory::createWriter($this->excel, 'Excel2007');
    $writer->save('php://output');

  }


} //--- end class








 ?>
