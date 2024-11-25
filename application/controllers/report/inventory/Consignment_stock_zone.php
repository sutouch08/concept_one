<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Consignment_stock_zone extends PS_Controller
{
  public $menu_code = 'RCMSTZ';
	public $menu_group_code = 'RE';
  public $menu_sub_group_code = 'REINVT';
	public $title = 'รายงานสินค้าคงเหลือฝากขาย(เทียม) แยกตามโซน';
  public $filter;
  public $error;
  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'report/inventory/consignment_stock_zone';
    $this->load->model('report/inventory/consignment_stock_report_model');
    $this->load->model('masters/products_model');
  }

  public function index()
  {
    $this->load->model('masters/warehouse_model');
    $whList = $this->warehouse_model->get_consignment_list();
    $ds['whList'] = $whList;
    $this->load->view('report/inventory/report_consignment_stock_zone', $ds);
  }


  public function get_report()
  {
    ini_set('memory_limit','512M'); // This also needs to be increased in some cases. Can be changed to a higher value as per need)
    ini_set('sqlsrv.ClientBufferMaxKBSize','524288'); // Setting to 512M
    ini_set('pdo_sqlsrv.client_buffer_max_kb_size','524288'); // Setting to 512M - for pdo_sqlsrv

    $sc = TRUE;
    $allProduct = $this->input->get('allProduct');
    $pdFrom = $this->input->get('pdFrom');
    $pdTo = $this->input->get('pdTo');

    $allWhouse = $this->input->get('allWhouse');
    $warehouse = $this->input->get('warehouse');

    $allZone = $this->input->get('allZone');
    $zoneCode = $this->input->get('zoneCode');
    $zoneName = $this->input->get('zoneName');

    $wh_list = '';
    if(!empty($warehouse) && empty($zoneCode))
    {
      $i = 1;
      foreach($warehouse as $wh)
      {
        $wh_list .= $i === 1 ? $wh : ', '.$wh;
        $i++;
      }
    }

    //---  Report title
    $ds['reportDate'] = thai_date(date('Y-m-d'),FALSE, '/');
    $ds['whList']   = $allWhouse == 1 ? 'ทั้งหมด' : $wh_list;
    $ds['zoneList'] = $allZone == 1 ? 'ทั้งหมด' : $zoneCode." - ".$zoneName;
    $ds['productList']   = $allProduct == 1 ? 'ทั้งหมด' : '('.$pdFrom.') - ('.$pdTo.')';

    $bs = array();

    $result = $this->consignment_stock_report_model->get_consignment_stock_zone($allProduct, $pdFrom, $pdTo, $allWhouse, $warehouse, $allZone, $zoneCode);

    if(!empty($result))
    {
      if(count($result) > 2000)
      {
        $sc = FALSE;
        $this->error = "ข้อมูลมีปริมาณมากเกินกว่าจะแสดงผลได้ กรุณาส่งออกข้อมูลแทนการแสดงผลหน้าจอ";
      }
      else
      {
        $no = 1;
        $totalQty = 0;

        foreach($result as $rs)
        {
          $arr = array(
            'no' => number($no),
            'warehouse' => $rs->warehouse_code,
            'zone' => $rs->zone_name,
            'pdCode' => $rs->product_code,
            'pdName' => $rs->product_name,
            'qty' => number($rs->qty)
          );

          array_push($bs, $arr);
          $totalQty += $rs->qty;
          $no++;
        }

        $arr = array( 'totalQty' => number($totalQty) );
        array_push($bs, $arr);
      }
    }
    else
    {
      $arr = array('nodata' => 'nodata');
      array_push($bs, $arr);
    }

    $ds['bs'] = $bs;

    echo $sc === TRUE ? json_encode($ds) : $this->error;
  }





  public function do_export()
  {
    ini_set('memory_limit','512M'); // This also needs to be increased in some cases. Can be changed to a higher value as per need)
    ini_set('sqlsrv.ClientBufferMaxKBSize','524288'); // Setting to 512M
    ini_set('pdo_sqlsrv.client_buffer_max_kb_size','524288'); // Setting to 512M - for pdo_sqlsrv

    $sc = TRUE;
    $allProduct = $this->input->post('allProduct');
    $pdFrom = $this->input->post('pdFrom');
    $pdTo = $this->input->post('pdTo');

    $allWhouse = $this->input->post('allWhouse');
    $warehouse = $this->input->post('warehouse');

    $allZone = $this->input->post('allZone');
    $zoneCode = $this->input->post('zoneCode');
    $zoneName = $this->input->post('zoneName');

    $token = $this->input->post('token');

    $wh_list = '';
    if(!empty($warehouse) && empty($zoneCode))
    {
      $i = 1;
      foreach($warehouse as $wh)
      {
        $wh_list .= $i === 1 ? $wh : ', '.$wh;
        $i++;
      }
    }

    //---  Report title
    $report_title = "รายงานสินค้าคงเหลือ(ฝากขาย) ณ วันที่ ".date('d/m/Y');
    $whList = $allWhouse == 1 ? 'ทั้งหมด' : $wh_list;
    $zoneList = $allZone == 1 ? 'ทั้งหมด' : $zoneCode." - ".$zoneName;
    $productList  = $allProduct == 1 ? 'ทั้งหมด' : '('.$pdFrom.') - ('.$pdTo.')';

    $bs = array();

    $result = $this->consignment_stock_report_model->get_consignment_stock_zone($allProduct, $pdFrom, $pdTo, $allWhouse, $warehouse, $allZone, $zoneCode);

    //--- load excel library
    $this->load->library('excel');

    $this->excel->setActiveSheetIndex(0);
    $this->excel->getActiveSheet()->setTitle('Stock Balance Report');

    //--- set report title header
    $this->excel->getActiveSheet()->setCellValue('A1', $report_title);
    $this->excel->getActiveSheet()->mergeCells('A1:G1');
    $this->excel->getActiveSheet()->setCellValue('A2', 'คลัง');
    $this->excel->getActiveSheet()->setCellValue('B2', $whList);
    $this->excel->getActiveSheet()->mergeCells('B2:G2');
    $this->excel->getActiveSheet()->setCellValue('A3', 'โซน');
    $this->excel->getActiveSheet()->setCellValue('B3', $zoneList);
    $this->excel->getActiveSheet()->mergeCells('B3:G3');
    $this->excel->getActiveSheet()->setCellValue('A4', 'สินค้า');
    $this->excel->getActiveSheet()->setCellValue('B4', $productList);
    $this->excel->getActiveSheet()->mergeCells('B4:G4');

    //--- set Table header
    $this->excel->getActiveSheet()->setCellValue('A5', 'ลำดับ');
    $this->excel->getActiveSheet()->setCellValue('B5', 'รหัสคลัง');
    $this->excel->getActiveSheet()->setCellValue('C5', 'รหัสโซน');
    $this->excel->getActiveSheet()->setCellValue('D5', 'ชื่อโซน');
    $this->excel->getActiveSheet()->setCellValue('E5', 'รหัสสินค้า');
    $this->excel->getActiveSheet()->setCellValue('F5', 'ชื่อสินค้า');
    $this->excel->getActiveSheet()->setCellValue('G5', 'จำนวน');

    $row = 6;
    if(!empty($result))
    {
      $no = 1;
      $totalQty = 0;
      foreach($result as $rs)
      {
        $this->excel->getActiveSheet()->setCellValue('A'.$row, $no);
        $this->excel->getActiveSheet()->setCellValue('B'.$row, $rs->warehouse_code);
        $this->excel->getActiveSheet()->setCellValue('C'.$row, $rs->zone_code);
        $this->excel->getActiveSheet()->setCellValue('D'.$row, $rs->zone_name);
        $this->excel->getActiveSheet()->setCellValue('E'.$row, $rs->product_code);
        $this->excel->getActiveSheet()->setCellValue('F'.$row, $rs->product_name);
        $this->excel->getActiveSheet()->setCellValue('G'.$row, $rs->qty);
        $totalQty += $rs->qty;
        $no++;
        $row++;
      }

      $this->excel->getActiveSheet()->setCellValue('A'.$row, 'รวม');
      $this->excel->getActiveSheet()->mergeCells('A'.$row.':F'.$row);
      $this->excel->getActiveSheet()->setCellValue('G'.$row, $totalQty);
      $this->excel->getActiveSheet()->getStyle('A'.$row)->getAlignment()->setHorizontal('right');
    }

    setToken($token);
    $file_name = "Report Consign Stock Zone.xlsx";
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'); /// form excel 2007 XLSX
    header('Content-Disposition: attachment;filename="'.$file_name.'"');
    $writer = PHPExcel_IOFactory::createWriter($this->excel, 'Excel2007');
    $writer->save('php://output');

  }


  public function export_to_check()
  {
    ini_set('memory_limit','512M'); // This also needs to be increased in some cases. Can be changed to a higher value as per need)
    ini_set('sqlsrv.ClientBufferMaxKBSize','524288'); // Setting to 512M
    ini_set('pdo_sqlsrv.client_buffer_max_kb_size','524288'); // Setting to 512M - for pdo_sqlsrv
    
    $allProduct = 1;
    $pdFrom = NULL;
    $pdTo = NULL;

    $allWhouse = 1;
    $warehouse = NULL;

    $allZone = 0;
    $zoneCode = $this->input->post('zone_code');
    $token = $this->input->post('token');

    $result = $this->consignment_stock_report_model->get_consignment_stock_zone($allProduct, $pdFrom, $pdTo, $allWhouse, $warehouse, $allZone, $zoneCode);

    //--- load excel library
    $this->load->library('excel');

    $this->excel->setActiveSheetIndex(0);
    $this->excel->getActiveSheet()->setTitle('Report Stock Zone');

    //--- set Table header
    $this->excel->getActiveSheet()->setCellValue('A1', 'barcode');
    $this->excel->getActiveSheet()->setCellValue('B1', 'item_code');
    $this->excel->getActiveSheet()->setCellValue('C1', 'qty');

    $row = 2;
    if(!empty($result))
    {
      foreach($result as $rs)
      {
        $this->excel->getActiveSheet()->setCellValue('A'.$row, $this->products_model->get_barcode($rs->product_code));
        $this->excel->getActiveSheet()->setCellValue('B'.$row, $rs->product_code);
        $this->excel->getActiveSheet()->setCellValue('C'.$row, $rs->qty);
        $row++;
      }

      $this->excel->getActiveSheet()->getStyle('A2:A'.($row -1))->getNumberFormat()->setFormatCode('0');
    }

    setToken($token);
    $file_name = "Report Consign Stock Zone.xlsx";
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'); /// form excel 2007 XLSX
    header('Content-Disposition: attachment;filename="'.$file_name.'"');
    $writer = PHPExcel_IOFactory::createWriter($this->excel, 'Excel2007');
    $writer->save('php://output');
  }


} //--- end class








 ?>
