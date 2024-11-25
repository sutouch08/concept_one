<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Sales_consignment_report extends PS_Controller
{
  public $menu_code = 'RSOCMD';
	public $menu_group_code = 'RE';
  public $menu_sub_group_code = 'RESALE';
	public $title = 'รายงานตัดยอดฝากขายเทียม แสดงรายการสินค้า';
  public $filter;
  public $error;
  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'report/sales/sales_consignment_report';
    $this->load->model('report/sales/sales_consignment_report_model');
    $this->load->model('masters/products_model');
    $this->load->model('masters/customers_model');
  }

  public function index()
  {
    $this->load->model('masters/warehouse_model');
    $whList = $this->warehouse_model->get_consignment_list();
    $ds['whList'] = $whList;
    $this->load->view('report/sales/sales_consignment_report', $ds);
  }


  public function get_report()
  {
    $sc = TRUE;
    $allProduct = $this->input->get('allProduct');
    $pdFrom = $this->input->get('pdFrom');
    $pdTo = $this->input->get('pdTo');

    $allCustomer = $this->input->get('allCustomer');
    $cusFrom = $this->input->get('cusFrom');
    $cusTo = $this->input->get('cusTo');

    $allWhouse = $this->input->get('allWhouse');
    $warehouse = $this->input->get('warehouse');

    $allZone = $this->input->get('allZone');
    $zoneCode = $this->input->get('zoneCode');
    $zoneName = $this->input->get('zoneName');

    $fromDate = $this->input->get('fromDate');
    $toDate = $this->input->get('toDate');

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

    $bs = array();

    $filter = array(
      'fromDate' => $fromDate,
      'toDate' => $toDate,
      'allProduct' => $allProduct,
      'pdFrom' => $pdFrom,
      'pdTo' => $pdTo,
      'allCustomer' => $allCustomer,
      'cusFrom' => $cusFrom,
      'cusTo' => $cusTo,
      'allWarehouse' => $allWhouse,
      'warehouse_code' => $warehouse,
      'allZone' => $allZone,
      'zone_code' => $zoneCode
    );

    $result = $this->sales_consignment_report_model->get_data($filter);

    if(! empty($result))
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
        $totalDiscount = 0;
        $totalAmount = 0;
        $totalCost = 0;

        foreach($result as $rs)
        {
          $arr = array(
            'no' => number($no),
            'date_add' => thai_date($rs->date_add, FALSE),
            'reference' => $rs->reference,
            'product_code' => $rs->product_code,
            'product_name' => $rs->product_name,
            'cost' => number($rs->cost, 2),
            'price' => number($rs->price, 2),
            'discount_label' => $rs->discount_label,
            'sell' => number($rs->sell, 2),
            'qty' => number($rs->qty),
            'total_discount' => number(($rs->discount_amount * $rs->qty), 2),
            'total_amount' => number($rs->total_amount, 2),
            'total_cost' => number($rs->total_cost, 2),
            'customer_code' => $rs->customer_code,
            'customer_name' => $rs->customer_name,
            'warehouse_code' => $rs->warehouse_code,
            'warehouse_name' => $rs->warehouse_name,
            'zone_code' => $rs->zone_code,
            'zone_name' => $rs->zone_name
          );

          array_push($bs, $arr);
          $totalQty += $rs->qty;
          $totalDiscount += ($rs->qty * $rs->discount_amount);
          $totalAmount += $rs->total_amount;
          $totalCost += $rs->total_cost;
          $no++;
        }

        $arr = array(
          'totalQty' => number($totalQty),
          'totalDiscount' => number($totalDiscount, 2),
          'totalAmount' => number($totalAmount, 2),
          'totalCost' => number($totalCost, 2)
        );

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
    $sc = TRUE;
    $allProduct = $this->input->post('allProduct');
    $pdFrom = $this->input->post('pdFrom');
    $pdTo = $this->input->post('pdTo');

    $allCustomer = $this->input->post('allCustomer');
    $cusFrom = $this->input->post('cusFrom');
    $cusTo = $this->input->post('cusTo');

    $allWhouse = $this->input->post('allWhouse');
    $warehouse = $this->input->post('warehouse');

    $allZone = $this->input->post('allZone');
    $zoneCode = $this->input->post('zoneCode');
    $zoneName = $this->input->post('zoneName');

    $fromDate = $this->input->post('fromDate');
    $toDate = $this->input->post('toDate');

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
    $report_title = "รายงานตัดยอดฝากขายเทียม";
    $whList = $allWhouse == 1 ? 'ทั้งหมด' : $wh_list;
    $zoneList = $allZone == 1 ? 'ทั้งหมด' : $zoneCode." - ".$zoneName;
    $productList  = $allProduct == 1 ? 'ทั้งหมด' : '('.$pdFrom.') - ('.$pdTo.')';
    $dateRange = "วันที่ ".thai_date($fromDate, FALSE, '/').' - '.thai_date($toDate, FALSE, '/');
    $cusList = $allCustomer == 1 ? 'ทั้งหมด' : "({$cusFrom}) - ({$cusTo})";


    $filter = array(
      'fromDate' => $fromDate,
      'toDate' => $toDate,
      'allProduct' => $allProduct,
      'pdFrom' => $pdFrom,
      'pdTo' => $pdTo,
      'allCustomer' => $allCustomer,
      'cusFrom' => $cusFrom,
      'cusTo' => $cusTo,
      'allWarehouse' => $allWhouse,
      'warehouse_code' => $warehouse,
      'allZone' => $allZone,
      'zone_code' => $zoneCode
    );

    $result = $this->sales_consignment_report_model->get_data($filter);

    //--- load excel library
    $this->load->library('excel');

    $this->excel->setActiveSheetIndex(0);
    $this->excel->getActiveSheet()->setTitle('Sales Consignment Report (WD)');

    //--- set report title header
    $this->excel->getActiveSheet()->setCellValue('A1', $report_title);
    $this->excel->getActiveSheet()->setCellValue('A2', $dateRange);
    $this->excel->getActiveSheet()->setCellValue('A3', 'ลูกค้า : '.$cusList);
    $this->excel->getActiveSheet()->setCellValue('A4', 'คลัง : '.$whList);
    $this->excel->getActiveSheet()->setCellValue('A5', 'โซน : '.$zoneList);
    $this->excel->getActiveSheet()->setCellValue('A4', 'สินค้า : '.$productList);

    $row = 6;
    //--- set Table header
    $this->excel->getActiveSheet()->setCellValue("A{$row}", "วันที่");
    $this->excel->getActiveSheet()->setCellValue("B{$row}", "เลขที่");
    $this->excel->getActiveSheet()->setCellValue("C{$row}", "รหัส");
    $this->excel->getActiveSheet()->setCellValue("D{$row}", "สินค้า");
    $this->excel->getActiveSheet()->setCellValue("E{$row}", "ทุน");
    $this->excel->getActiveSheet()->setCellValue("F{$row}", "ราคา");
    $this->excel->getActiveSheet()->setCellValue("G{$row}", "ขาย");
    $this->excel->getActiveSheet()->setCellValue("H{$row}", "จำนวน");
    $this->excel->getActiveSheet()->setCellValue("I{$row}", "ส่วนลด");
    $this->excel->getActiveSheet()->setCellValue("J{$row}", "มูลค่าส่วนลด");
    $this->excel->getActiveSheet()->setCellValue("K{$row}", "มูลค่ารวม");
    $this->excel->getActiveSheet()->setCellValue("L{$row}", "ทุนรวม");
    $this->excel->getActiveSheet()->setCellValue("M{$row}", "รหัสลูกค้า");
    $this->excel->getActiveSheet()->setCellValue("N{$row}", "ชื่อลูกค้า");
    $this->excel->getActiveSheet()->setCellValue("O{$row}", "รหัสคลัง");
    $this->excel->getActiveSheet()->setCellValue("P{$row}", "ชื่อคลัง");
    $this->excel->getActiveSheet()->setCellValue("Q{$row}", "รหัสโซน");
    $this->excel->getActiveSheet()->setCellValue("R{$row}", "ชื่อโซน");

    $row++;

    if(! empty($result))
    {
      // print_r($result);
      foreach($result as $rs)
      {
        $this->excel->getActiveSheet()->setCellValue("A{$row}", thai_date($rs->date_add, FALSE, '/'));
        $this->excel->getActiveSheet()->setCellValue("B{$row}", $rs->reference);
        $this->excel->getActiveSheet()->setCellValue("C{$row}", $rs->product_code);
        $this->excel->getActiveSheet()->setCellValue("D{$row}", $rs->product_name);
        $this->excel->getActiveSheet()->setCellValue("E{$row}", $rs->cost);
        $this->excel->getActiveSheet()->setCellValue("F{$row}", $rs->price);
        $this->excel->getActiveSheet()->setCellValue("G{$row}", $rs->sell);
        $this->excel->getActiveSheet()->setCellValue("H{$row}", $rs->qty);
        $this->excel->getActiveSheet()->setCellValueExplicit("I{$row}", $rs->discount_label, PHPExcel_Cell_DataType::TYPE_STRING);
        $this->excel->getActiveSheet()->setCellValue("J{$row}", $rs->discount_amount);
        $this->excel->getActiveSheet()->setCellValue("K{$row}", $rs->total_amount);
        $this->excel->getActiveSheet()->setCellValue("L{$row}", $rs->total_cost);
        $this->excel->getActiveSheet()->setCellValue("M{$row}", $rs->customer_code);
        $this->excel->getActiveSheet()->setCellValue("N{$row}", $rs->customer_name);
        $this->excel->getActiveSheet()->setCellValue("O{$row}", $rs->warehouse_code);
        $this->excel->getActiveSheet()->setCellValue("P{$row}", $rs->warehouse_name);
        $this->excel->getActiveSheet()->setCellValue("Q{$row}", $rs->zone_code);
        $this->excel->getActiveSheet()->setCellValue("R{$row}", $rs->zone_name);
        $row++;
      }

      $this->excel->getActiveSheet()->getStyle("E7:L{$row}")->getAlignment()->setHorizontal('right');
    }

    setToken($token);
    $file_name = "Report Sales Consignment.xlsx";
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'); /// form excel 2007 XLSX
    header('Content-Disposition: attachment;filename="'.$file_name.'"');
    $writer = PHPExcel_IOFactory::createWriter($this->excel, 'Excel2007');
    $writer->save('php://output');

  }


} //--- end class








 ?>
