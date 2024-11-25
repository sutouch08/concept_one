<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Transfer_acception extends PS_Controller
{
  public $menu_code = 'RADACT';
	public $menu_group_code = 'RE';
  public $menu_sub_group_code = 'REAUDIT';
	public $title = 'รายงานเอกสารโอนคลังที่ต้องกดรับ';
  public $filter;
	public $wms;
	public $limit = 2000;

  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'report/audit/Transfer_acception';
    $this->load->model('report/audit/transfer_acception_model');
  }

  public function index()
  {
    $this->load->view('report/audit/report_transfer_acception');
  }


  public function get_report()
  {
    $sc = TRUE;

    $json = json_decode($this->input->post('json'));

    if( ! empty($json))
    {
      $docList = array('WW', 'MV', 'RT', 'RN', 'WR', 'SM');

      if($json->allRole == 0 && ! empty($json->role))
      {
        $docList = array();

        foreach($json->role as $role)
        {
          $docList[] = $role;
        }
      }

      $filter = array(
        'from_date' => $json->fromDate,
        'to_date' => $json->toDate,
        'is_accept' => $json->is_accept
      );

      $ds = array();

      $no = 1;

      foreach($docList as $doc)
      {
        $list = $this->transfer_acception_model->get($doc, $filter);

        if( ! empty($list))
        {
          foreach($list as $rs)
          {
            $name = ($doc != 'WW' && $doc != 'MV') ? $rs->accept_name : NULL;
            $owner = ($doc != 'WW' && $doc != 'MV') ? $rs->owner_name : $this->transfer_acception_model->get_owner_list($doc, $rs->code);

            if($rs->is_accept == 1 && empty($rs->accept_by))
            {
              $name = $this->transfer_acception_model->get_accept_list($doc, $rs->code);
            }

            $arr = array(
              'no' => $no,
              'date_add' => thai_date($rs->date_add),
              'code' => $rs->code,
              'is_accept' => $rs->is_accept == 1 ? 'กดรับแล้ว' : 'ยังไม่กดรับ',
              'owner_name' => $owner,
              'accept_by' => $name,
              'accept_on' => thai_date($rs->accept_on, TRUE),
              'accept_remark' => $rs->accept_remark
            );

            array_push($ds, $arr);

            $no++;
          }
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
    $token = $this->input->post('token');
		$roles = $this->input->post('role');
		$all  = $this->input->post('allRole') ? TRUE : FALSE;
    $is_accept = $this->input->post('is_accept');
		$fromDate = $this->input->post('fromDate');
    $toDate = $this->input->post('toDate');

    $docList = array('WW', 'MV', 'RT', 'RN', 'WR', 'SM');

    if($all == 0 && ! empty($roles))
    {
      $docList = array();

      foreach($roles as $role)
      {
        $docList[] = $role;
      }
    }

    $filter = array(
      'from_date' => $fromDate,
      'to_date' => $toDate,
      'is_accept' => $is_accept
    );

    //--- load excel library
    $this->load->library('excel');

    $this->excel->setActiveSheetIndex(0);
    $this->excel->getActiveSheet()->setTitle('Transfer Acception Report');

    //--- set report title header
    $this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(8);
    $this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(15);
    $this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
    $this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(30);
    $this->excel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
    $this->excel->getActiveSheet()->getColumnDimension('F')->setWidth(30);
    $this->excel->getActiveSheet()->getColumnDimension('G')->setWidth(25);
    $this->excel->getActiveSheet()->getColumnDimension('H')->setWidth(50);

    $this->excel->getActiveSheet()->setCellValue('A1', 'รายงานเอกสารโอนคลังที่ต้องกดรับ');

    //--- set Table header
    $this->excel->getActiveSheet()->setCellValue('A2', 'ลำดับ');
    $this->excel->getActiveSheet()->setCellValue('B2', 'วันที่');
    $this->excel->getActiveSheet()->setCellValue('C2', 'เลขที่');
    $this->excel->getActiveSheet()->setCellValue('D2', 'เจ้าของโซน');
    $this->excel->getActiveSheet()->setCellValue('E2', 'การกดรับ');
    $this->excel->getActiveSheet()->setCellValue('F2', 'กดรับโดย');
    $this->excel->getActiveSheet()->setCellValue('G2', 'วันที่กดรับ');
    $this->excel->getActiveSheet()->setCellValue('H2', 'หมายเหตุ');


		if(! empty($docList))
		{
      $no = 1;
      $row = 3;

      foreach($docList as $doc)
      {
        $list = $this->transfer_acception_model->get($doc, $filter);

        if( ! empty($list))
        {
          foreach($list as $rs)
          {
            $name = ($doc != 'WW' && $doc != 'MV') ? $rs->accept_name : NULL;
            $owner = ($doc != 'WW' && $doc != 'MV') ? $rs->owner_name : $this->transfer_acception_model->get_owner_list($doc, $rs->code);

            if($rs->is_accept == 1 && empty($rs->accept_by))
            {
              $name = $this->transfer_acception_model->get_accept_list($doc, $rs->code);
            }

            $this->excel->getActiveSheet()->setCellValue('A'.$row, $no);
  	        $this->excel->getActiveSheet()->setCellValue('B'.$row, thai_date($rs->date_add, FALSE, '/'));
  					$this->excel->getActiveSheet()->setCellValue('C'.$row, $rs->code);
            $this->excel->getActiveSheet()->setCellValue('D'.$row, $owner);
  	        $this->excel->getActiveSheet()->setCellValue('E'.$row, $rs->is_accept == 1 ? 'กดรับแล้ว' : 'ยังไม่กดรับ');
  	        $this->excel->getActiveSheet()->setCellValue('F'.$row, $name);
  					$this->excel->getActiveSheet()->setCellValue('G'.$row, thai_date($rs->accept_on, TRUE, '/'));
            $this->excel->getActiveSheet()->setCellValue('H'.$row, $rs->accept_remark);

  					$no++;
  					$row++;
          }
        }
      }
		} //--- end WO

    setToken($token);
    $file_name = "รายงานเอกสารโอนคลังที่ต้องกดรับ ".date('Ymd').".xlsx";
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'); /// form excel 2007 XLSX
    header('Content-Disposition: attachment;filename="'.$file_name.'"');
    $writer = PHPExcel_IOFactory::createWriter($this->excel, 'Excel2007');
    $writer->save('php://output');

  }

} //--- end class








 ?>
