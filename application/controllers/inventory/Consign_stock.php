<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Consign_stock extends PS_Controller
{
  public $menu_code = 'ICCSST';
	public $menu_group_code = 'IC';
  public $menu_sub_group_code = 'CHECK';
	public $title = 'ตรวจสอบสต็อกฝากขาย(แท้)';
  public $filter;
  public $error;
  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'inventory/consign_stock';
    $this->load->model('inventory/sap_consign_stock_model');
  }


  public function index()
  {
    $filter = array(
      'item_code' => get_filter('item_code', 'item_code', ''),
      'zone_code' => get_filter('zone_code', 'zone_code', ''),
      'show_system' => get_filter('show_system', 'show_system', 'no')
    );

		//--- แสดงผลกี่รายการต่อหน้า
		$perpage = get_rows();
		//--- หาก user กำหนดการแสดงผลมามากเกินไป จำกัดไว้แค่ 300
		if($perpage > 300)
		{
			$perpage = 20;
		}

		$segment  = 4; //-- url segment
		$rows     = $this->sap_consign_stock_model->count_rows($filter);
		//--- ส่งตัวแปรเข้าไป 4 ตัว base_url ,  total_row , perpage = 20, segment = 3
		$init	    = pagination_config($this->home.'/index/', $rows, $perpage, $segment);
		$ds   = $this->sap_consign_stock_model->get_list($filter, $perpage, $this->uri->segment($segment));

    $filter['data'] = $ds;

		$this->pagination->initialize($init);
    $this->load->view('inventory/stock/consign_stock_view', $filter);
  }



  public function export()
  {
    $arr = array(
      'item_code' => $this->input->post('item'),
      'zone_code' => $this->input->post('zone'),
      'show_system' => $this->input->post('system')
    );

    $token = $this->input->post('token');

    $data = $this->sap_consign_stock_model->get_list($arr);
    if(!empty($data))
    {
      //--- load excel library
      $this->load->library('excel');

      $this->excel->setActiveSheetIndex(0);
      $this->excel->getActiveSheet()->setTitle('Stock Zone (SAP)');

      $this->excel->getActiveSheet()->setCellValue('A1', 'No.');
      $this->excel->getActiveSheet()->setCellValue('B1', 'ItemCode');
      $this->excel->getActiveSheet()->setCellValue('C1', 'OldCode');
      $this->excel->getActiveSheet()->setCellValue('D1', 'Description');
      $this->excel->getActiveSheet()->setCellValue('E1', 'BinCode');
      $this->excel->getActiveSheet()->setCellValue('F1', 'Bin Description');
      $this->excel->getActiveSheet()->setCellValue('G1', 'Qty');

      $no = 1;
      $row = 2;
      foreach($data as $rs)
      {
        $this->excel->getActiveSheet()->setCellValue('A'.$row, $no);
        $this->excel->getActiveSheet()->setCellValue('B'.$row, $rs->ItemCode);
        if(!empty($rs->U_OLDCODE))
        {
          $this->excel->getActiveSheet()->setCellValue('C'.$row, $rs->U_OLDCODE);
        }

        $this->excel->getActiveSheet()->setCellValue('D'.$row, $rs->ItemName);
        $this->excel->getActiveSheet()->setCellValue('E'.$row, $rs->BinCode);
        $this->excel->getActiveSheet()->setCellValue('F'.$row, $rs->Descr);
        $this->excel->getActiveSheet()->setCellValue('G'.$row, $rs->OnHandQty);
        $no++;
        $row++;
      }
    }

    setToken($token);
    $file_name = "ConsignStock(SAP).xlsx";
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'); /// form excel 2007 XLSX
    header('Content-Disposition: attachment;filename="'.$file_name.'"');
    $writer = PHPExcel_IOFactory::createWriter($this->excel, 'Excel2007');
    $writer->save('php://output');
  }


  function clear_filter(){
    $filter = array('item_code', 'zone_code', 'show_system');
    clear_filter($filter);
    echo 'done';
  }

} //--- end class
?>
