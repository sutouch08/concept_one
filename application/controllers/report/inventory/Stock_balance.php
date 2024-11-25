<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Stock_balance extends PS_Controller
{
  public $menu_code = 'RICSTB';
	public $menu_group_code = 'RE';
  public $menu_sub_group_code = 'REINVT';
	public $title = 'รายงานสินค้าคงเหลือ';
  public $filter;
  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'report/inventory/stock_balance';
    $this->load->model('report/inventory/inventory_report_model');
    $this->load->model('masters/products_model');
  }

  public function index()
  {
    $this->load->model('masters/warehouse_model');
    $whList = $this->warehouse_model->get_all_warehouse();
    $ds['whList'] = $whList;
    $this->load->view('report/inventory/report_stock_balance', $ds);
  }


  public function get_report()
  {
    $allProduct = $this->input->get('allProduct');
    $pdFrom = $this->input->get('pdFrom');
    $pdTo = $this->input->get('pdTo');

    $allWhouse = $this->input->get('allWhouse');
    $warehouse = $this->input->get('warehouse');

    $wh_list = '';
    if(!empty($warehouse))
    {
      $i = 1;
      foreach($warehouse as $wh)
      {
        $wh_list .= $i === 1 ? $wh : ', '.$wh;
        $i++;
      }
    }

    //---  Report title
    $sc['reportDate'] = thai_date(date('Y-m-d'),FALSE, '/');
    $sc['whList']   = $allWhouse == 1 ? 'ทั้งหมด' : $wh_list;
    $sc['productList']   = $allProduct == 1 ? 'ทั้งหมด' : '('.$pdFrom.') - ('.$pdTo.')';


    $result = $this->inventory_report_model->get_current_stock_balance($allProduct, $pdFrom, $pdTo, $allWhouse, $warehouse);

    $bs = array();

    if(!empty($result))
    {
      $no = 1;
      $totalQty = 0;
      $totalAmount = 0;
      foreach($result as $rs)
      {
        $item = $this->products_model->get_item($rs->product_code);
        if(!empty($item))
        {
          $arr = array(
            'no' => number($no),
            'barcode' => $item->barcode,
            'pdCode' => $item->code,
            'pdName' => $item->name,
            'cost' => number($item->cost, 2),
            'qty' => number($rs->qty),
            'amount' => number($item->cost * $rs->qty, 2)
          );

          array_push($bs, $arr);
          $no++;

          $totalQty += $rs->qty;
          $totalAmount += ($rs->qty * $item->cost);
        }

      }

      $arr = array(
        'totalQty' => number($totalQty),
        'totalAmount' => number($totalAmount, 2)
      );

      array_push($bs, $arr);

      $bs;
    }
    else
    {
      $arr = array('nodata' => 'nodata');
      array_push($bs, $arr);
    }

    $sc['bs'] = $bs;

    echo json_encode($sc);
  }





  public function do_export()
  {
    $allProduct = $this->input->post('allProduct');
    $pdFrom = $this->input->post('pdFrom');
    $pdTo = $this->input->post('pdTo');

    $allWhouse = $this->input->post('allWhouse');
    $warehouse = $this->input->post('warehouse');


    $wh_list = '';
    if(!empty($warehouse))
    {
      $i = 1;
      foreach($warehouse as $wh)
      {
        $wh_list .= $i === 1 ? $wh : ', '.$wh;
        $i++;
      }
    }


    //---  Report title
    $report_title = 'รายงานสินค้าคงเหลือ ณ วันที่  '.thai_date(date('Y-m-d'), '/');
    $wh_title     = 'คลัง :  '. ($allWhouse == 1 ? 'ทั้งหมด' : $wh_list);
    $pd_title     = 'สินค้า :  '. ($allProduct == 1 ? 'ทั้งหมด' : '('.$pdFrom.') - ('.$pdTo.')');

    $result = $this->inventory_report_model->get_current_stock_balance($allProduct, $pdFrom, $pdTo, $allWhouse, $warehouse);

    //--- load excel library
    $this->load->library('excel');

    $this->excel->setActiveSheetIndex(0);
    $this->excel->getActiveSheet()->setTitle('Stock Balance Report');

    //--- set report title header
    $this->excel->getActiveSheet()->setCellValue('A1', $report_title);
    $this->excel->getActiveSheet()->mergeCells('A1:G1');
    $this->excel->getActiveSheet()->setCellValue('A2', $wh_title);
    $this->excel->getActiveSheet()->mergeCells('A2:G2');
    $this->excel->getActiveSheet()->setCellValue('A3', $pd_title);
    $this->excel->getActiveSheet()->mergeCells('A3:G3');

    //--- set Table header
    $this->excel->getActiveSheet()->setCellValue('A4', 'ลำดับ');
    $this->excel->getActiveSheet()->setCellValue('B4', 'บาร์โค้ด');
    $this->excel->getActiveSheet()->setCellValue('C4', 'รหัส');
    $this->excel->getActiveSheet()->setCellValue('D4', 'สินค้า');
    $this->excel->getActiveSheet()->setCellValue('E4', 'ทุน');
    $this->excel->getActiveSheet()->setCellValue('F4', 'จำนวน');
    $this->excel->getActiveSheet()->setCellValue('G4', 'มูลค่า');

    $row = 5;
    if(!empty($result))
    {
      $no = 1;
      foreach($result as $rs)
      {
        $item = $this->products_model->get_item($rs->product_code);
        if(!empty($item))
        {
          $this->excel->getActiveSheet()->setCellValue('A'.$row, $no);
          $this->excel->getActiveSheet()->setCellValue('B'.$row, $item->barcode);
          $this->excel->getActiveSheet()->setCellValue('C'.$row, $item->code);
          $this->excel->getActiveSheet()->setCellValue('D'.$row, $item->name);
          $this->excel->getActiveSheet()->setCellValue('E'.$row, $item->cost);
          $this->excel->getActiveSheet()->setCellValue('F'.$row, $rs->qty);
          $this->excel->getActiveSheet()->setCellValue('G'.$row, ($item->cost * $rs->qty));
          $no++;
          $row++;
        }
      }

      $res = $row -1;

      $this->excel->getActiveSheet()->setCellValue('A'.$row, 'รวม');
      $this->excel->getActiveSheet()->mergeCells('A'.$row.':E'.$row);
      $this->excel->getActiveSheet()->setCellValue('F'.$row, '=SUM(F5:F'.$res.')');
      $this->excel->getActiveSheet()->setCellValue('G'.$row, '=SUM(G5:G'.$res.')');

      $this->excel->getActiveSheet()->getStyle('A'.$row)->getAlignment()->setHorizontal('right');
      $this->excel->getActiveSheet()->getStyle('B5:B'.$res)->getNumberFormat()->setFormatCode('0');
      $this->excel->getActiveSheet()->getStyle('F5:G'.$row)->getAlignment()->setHorizontal('right');
      $this->excel->getActiveSheet()->getStyle('F5:F'.$row)->getNumberFormat()->setFormatCode('#,##0');
      $this->excel->getActiveSheet()->getStyle('G5:G'.$row)->getNumberFormat()->setFormatCode('#,##0.00');
    }


    $file_name = "Report Stock Balance.xlsx";
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'); /// form excel 2007 XLSX
    header('Content-Disposition: attachment;filename="'.$file_name.'"');
    $writer = PHPExcel_IOFactory::createWriter($this->excel, 'Excel2007');
    $writer->save('php://output');

  }


  public function get_report_by_array()
  {
    $arr = array(
      'WA-204PLABG30-AA-2L',
      'WA-204PLABG30-AA-M',
      'WA-204PLABG30-EE-M',
      'WA-204PLABG30-EE-S',
      'WA-204PLABG30-EE-XL',
      'WA-204PLABG30-EE-L',
      'WA-20FBM03-DD-L',
      'WA-20FBM03-DD-M',
      'WA-18FTT-FBW0300-RA-34',
      'WA-19FTT-PLM3612-DE-M',
      'WA-19FTT-FBM0300-DD-S',
      'WA-18FTT-FBM0300-GOLD-EA-M',
      'WA-18FTT-FBM0400-GOLD-EA-M',
      'WA-19FTT-PLM3512-AS-M',
      'WA-19FTT-PLM3512-BN-M',
      'WA-19FTT-PLM3512-WN-M',
      'WA-20FBM03-DY-M',
      'WA-PLA024-FTM2-EE-S',
      'WA-19FTT-PLM3512-AS-S',
      'WA-19FTT-FBM0300-LD-XS',
      'WA-PLA024-FTM2-YY-M',
      'WA-PLA024-FTM2-YY-XL',
      'WA-19FTT-FBW0300-RR-34',
      'WA-204PLABG30-DD-S',
      'WA-204PLABG30-DD-XL',
      'WA-20FBM03-RR-XL',
      'WA-204PLABG30-GG-3L',
      'WA-204PLABG30-RR-XL',
      'WA-FBA090-BG299-XX-XL',
      'WA-FBA009-BGG199-XX-L',
      'WA-20FBM03-DY-L',
      'WA-20FBM03-YD-2L',
      'WP-FBA009-BGG99-XX-XL',
      'WA-19FTT-PLM3512-AN-XL',
      'WA-19FTT-PLM3612-DE-S',
      'WA-18FTT-FBM0300-ULT-WA-L',
      'WA-18FTT-FBM0300-ULT-WA-M',
      'WA-18FTT-FBM0200-RA-M',
      'WA-18FTT-FBM0300-GOLD-AE-2L',
      'WA-18FTT-FBM0300-GOLD-AE-M',
      'WA-18FTT-FBM0300-GOLD-EA-L',
      'WA-18FTT-FBM0300-GOLD-EA-XL',
      'WA-19FTT-PLM3512-AS-XL',
      'WA-19FTT-PLM3512-ES-XL',
      'WA-FBA047-BG-DB-S',
      'WP-FBA009-BGG99-XX-2L',
      'WS-204MKABG01-LB-L',
      'WA-211BGAIN53-PH-M',
      'WA-211BGAIN53-PH-S',
      'WA-211BGAIN53-RR-2L',
      'WA-211BGAIN53-WW-M',
      'WA-18FTT-FBM0300BG-BR-S',
      'WA-19FTT-FBM0300-PD-S',
      'WA-19FTT-PLM3512-AN-2L',
      'WA-19FTT-PLM3512-AN-L',
      'WA-19FTT-PLM3512-AS-3L',
      'WA-19FTT-PLM3512-AS-L',
      'WA-19FTT-PLM3612-YE-XS',
      'WA-18FTT-FBM0300-ULT-WA-3L',
      'WA-18FTT-FBW0400-BR-36',
      'WA-19FTT-FBM0300-DD-L',
      'WA-17FTT-FBM0200-AA-3L',
      'WA-17FTT-FBM0200-AA-L',
      'WA-18FTT-FBM0200-RA-2L',
      'WA-19FTT-PLM3512-BN-S',
      'WA-19FTT-PLM3512-BN-XS',
      'WA-19FTT-PLM3512-RN-XL',
      'WP-BCG506-RL-XL',
      'WA-204THABG53-AA-L',
      'WA-204THABG53-AA-M',
      'WA-19FTT-PLM3612-DE-L',
      'WP-FBA009-BGG99-XX-3L',
      'WA-19FTT-FBW0300-RR-36',
      'WA-19FTT-FBW0300-WD-34',
      'WA-19FTT-FBW0300-WD-36',
      'WA-212WRABG31-AA-3L',
      'WA-212WRABG31-AA-XL',
      'WA-20FBM03-RD-L',
      'WA-19FTT-FBM0300-WD-XS',
      'WA-18FTT-FBM0300-GOLD-EA-S',
      'WA-PLA024-FTM2-BB-M',
      'WA-20FBM03-RD-M',
      'WA-212WRABG31-AA-S',
      'WA-19FTT-FBM0300-WD-M',
      'WA-20FBM03-YD-7L',
      'WA-20FBM03-DD-XS',
      'WA-18FTT-FBM0200-RA-XL',
      'WP-223BIGC109-AA-L',
      'WA-20FBM03-DD-XL',
      'WA-20FBM03-RR-L',
      'WP-223BIGC109-AA-2L',
      'WA-17FTT-FBM0200-AA-M',
      'WA-19FTT-PLM3512-AS-XS',
      'WP-FBA009-BGG99-XX-S',
      'WA-20FBM03-RR-M',
      'WA-18FTT-FBW0300-BR-36',
      'WA-19FTT-PLM3612-DE-XS',
      'WA-PLA024-FTM2-WW-L',
      'WA-18FTT-FBM0300-GOLD-EA-XS',
      'WA-19FTT-PLM3512-WN-3L',
      'WA-19FTT-PLM3512-WN-5L',
      'WA-19FTT-PLM3512-WN-L',
      'WA-19FTT-PLM3512-WN-XL',
      'WA-204THABG53-AA-2L',
      'WA-19FTT-FBM0300-RR-XL',
      'WA-20FBM03-RD-XL',
      'WA-20FBM03-RD-2L',
      'WA-204THABG53-AA-S',
      'WA-223BIGC129-AA-XL',
      'WA-17FTT-FBK0300-WW-4',
      'WA-18FTT-FBM0300-GOLD-EA-2L',
      'WA-PLA299-BGG00-XX-L',
      'WA-FBA090-BG149-XX-S',
      'WA-204THABG53-AA-XL',
      'WA-18FTT-FBK0400-BR-10',
      'WA-18FTT-FBW0300-RA-36',
      'WA-18FTT-FBM0200-RA-XS',
      'WP-BCG506-RL-L',
      'WA-204PLABG30-BB-S',
      'WA-204PLABG30-RR-S',
      'WA-204PLABG30-DD-M',
      'WA-204PLABG30-RR-2L',
      'WA-20FBM03-RD-7L',
      'WA-212WRABG30-AA-L',
      'WA-212WRABG30-AA-M',
      'WA-212WRABG30-DD-M',
      'WA-212WRABG31-AA-M',
      'WA-PLAN15-AA-S',
      'WA-19FTT-PLM3512-RN-3L',
      'WS-202MKACH01-BB-M',
      'WA-18FTT-FBM0400-GOLD-AE-XL',
      'WS-204MKABG01-AA-L',
      'WA-18FTT-FBM0400BG-BR-M',
      'WA-19FTT-FBM0300-RR-2L',
      'WA-204PLABG30-DD-L',
      'WA-211BGAIN53-WW-L',
      'WA-PLA024-FTM2-YY-2L',
      'WA-204PLABG30-WW-2L',
      'WA-18FTT-FBW0300-BNK-BR-34',
      'WA-FBA090-BG199-XX-2L',
      'WA-FBA090-BG199-XX-XL',
      'WA-20FBM03-YD-XL',
      'WA-18FTT-FBW0300-BR-34',
      'WA-18FTT-FBK0400-BR-8',
      'WA-PLA024-FTM2-EE-XS',
      'WA-204PLABG30-AA-S',
      'WP-223BIGC109-AA-M',
      'WP-212WRABG31-AA-S',
      'WA-18FTT-FBW0400-BR-34',
      'WA-204PLABG30-BB-XL',
      'WA-20FBM03-RR-S',
      'WA-20FBM03-RD-XS',
      'WA-19FTT-FBM0300-LD-S',
      'WA-PLA024-FTM2-EE-2L',
      'WA-211BGAIN53-PH-2L',
      'WA-211BGAIN53-PH-L',
      'WA-211BGAIN53-WW-2L',
      'WA-211BGAIN53-WW-XL',
      'WA-18FTT-FBM0300-ULT-WA-XL',
      'WA-PLA024-FTM2-RR-3L',
      'WA-20FBM03-DD-S',
      'WP-223BIGC109-AA-XL',
      'WP-223BIGC109-VV-2L',
      'WA-214FBATH53-WW-2L',
      'WA-211BGAIN53-RR-XL',
      'WA-17FTT-FBM0300BG-AA-L',
      'WA-17FTT-FBM0300BG-AA-M',
      'WS-204MKABG01-LB-M',
      'WP-223BIGC109-AA-3L',
      'WP-223BIGC109-DD-XL',
      'WA-17FTT-FBK0300-WW-12',
      'WA-PLA024-FTM2-WW-XL',
      'WA-PLA024-FTM2-BB-S',
      'WA-19FTT-FBM0300-RR-L',
      'WA-19FTT-FBM0300-RR-M',
      'WA-19FTT-FBM0300-RR-XS',
      'WA-222PLACL31-MM-XL',
      'WP-BCG506-AA-S',
      'WS-204MKABG01-AA-M',
      'WS-204MKABG01-EA-M',
      'WA-19FTT-PLM3512-BN-L',
      'WA-PLAN15-AA-M',
      'WA-17FTT-FBM0200-AA-XL',
      'WA-PLA024-FTM2-AA-M',
      'WA-PLA199-BGG00-XX-XL',
      'WA-PLA024-FTM2-RR-2L',
      'WS-204MKABG01-BD-M',
      'WP-212WRABG31-AA-L',
      'WA-FBA090-BG299-XX-L',
      'WA-212WRABG31-DD-3L',
      'WP-FBA009-BGG99-XX-L',
      'WA-211BGAIN53-GG-L',
      'WA-FBA063-BG-GA-L',
      'WA-17FTT-FBK0300-WW-8',
      'WS-204MKABG01-LD-M',
      'WA-20FBM03-YD-XS',
      'WP-223BIGC109-LL-L',
      'WP-223BIGC109-VV-L',
      'WP-223BIGC109-DD-2L',
      'WP-223BIGC109-DD-L',
      'WA-17FTT-FBK0300-AA-4',
      'WA-222PLACL31-AA-XL',
      'WA-222PLACL31-DD-M',
      'WA-222PLACL31-DD-XL',
      'WP-223BIGC109-VV-3L',
      'WP-223BIGC109-VV-XL',
      'WP-BCG506-RL-M',
      'WA-20FBM03-DY-XS',
      'WP-223BIGC109-WW-2L',
      'WA-214FBATH53-GG-2L',
      'WA-FBA090-BG149-XX-M',
      'WA-223BIGC129-AA-S',
      'WA-222PLACL31-AA-M',
      'WA-222PLACL31-MM-M',
      'WP-223BIGC109-LL-3L',
      'WP-223BIGC109-LL-M',
      'WP-223BIGC109-LL-XL',
      'WP-223BIGC109-VV-M',
      'WA-223BIGC129-GG-L',
      'WA-223BIGC129-GG-M',
      'WA-223BIGC129-LL-L',
      'WA-223BIGC129-LL-M',
      'WA-223BIGC129-VV-L',
      'WA-223BIGC129-VV-M',
      'WP-223BIGC109-WW-XL',
      'WP-FBA009-BGG99-XX-M',
      'WA-20FBM03-DY-XL',
      'WA-20FBM03-RD-S',
      'WA-223BIGC129-AA-M',
      'WP-223BIGC109-DD-3L',
      'WA-224FBATH53-BB-5L',
      'WA-222PLACL31-MM-L',
      'WA-PLAN15-R9-7L',
      'WA-20FBM03-YD-S',
      'WA-214FBATH53-DD-L',
      'WA-214FBATH53-GG-XL',
      'WA-214FBATH53-RR-L',
      'WA-214FBATH53-RR-XL',
      'WA-222PLACL31-DD-L',
      'WA-PLA024-FTM2-AA-XS',
      'WA-20FBM03-DY-S',
      'WA-223PLACL36-YI-2L',
      'WA-223PLACL36-YI-3L',
      'WA-223PLACL36-YI-L',
      'WA-223PLACL36-YI-M',
      'WA-223PLACL36-YI-S',
      'WA-223PLACL36-YI-XL',
      'WA-214FBATH53-DD-2L',
      'WA-214FBATH53-DD-XL',
      'WA-214FBATH53-GG-L',
      'WA-214FBATH53-WW-L',
      'WA-214FBATH53-WW-XL',
      'WA-PLAN15-AA-2L',
      'WA-PLAN15-AA-3L',
      'WA-PLAN15-AA-5L',
      'WA-PLAN15-AA-L',
      'WA-PLAN15-DD-2L',
      'WA-PLAN15-DD-3L',
      'WA-PLAN15-DD-5L',
      'WA-PLAN15-DD-L',
      'WA-PLAN15-AA-XL',
      'WA-PLAN15-CC-2L',
      'WA-PLAN15-CC-L',
      'WA-PLAN15-DD-XL',
      'WA-PLAN15-EE-2L',
      'WA-PLAN15-EE-L',
      'WA-PLAN15-EE-XL',
      'WA-PLAN15-CC-XL',
      'WA-224FBATH53-BB-2L',
      'WA-224FBATH53-BB-3L',
      'WA-224FBATH53-BB-L',
      'WA-224FBATH53-BB-M',
      'WA-224FBATH53-BB-XL',
      'WA-224FBATH53-GG-2L',
      'WA-224FBATH53-GG-3L',
      'WA-224FBATH53-GG-5L',
      'WA-224FBATH53-GG-L',
      'WA-224FBATH53-GG-M',
      'WA-224FBATH53-GG-XL',
      'WA-224FBATH53-RR-2L',
      'WA-224FBATH53-RR-3L',
      'WA-224FBATH53-RR-L',
      'WA-224FBATH53-RR-M',
      'WA-224FBATH53-RR-XL',
      'WA-224FBATH53-VV-2L',
      'WA-224FBATH53-VV-3L',
      'WA-224FBATH53-VV-5L',
      'WA-224FBATH53-VV-L',
      'WA-224FBATH53-VV-M',
      'WA-224FBATH53-VV-XL',
      'WA-PLAN15-LL-2L',
      'WA-PLAN15-LL-L',
      'WA-PLAN15-LL-M',
      'WA-PLAN15-LL-XL',
      'WA-PLAN15-NN-2L',
      'WA-PLAN15-NN-3L',
      'WA-PLAN15-NN-5L',
      'WA-PLAN15-NN-L',
      'WA-PLAN15-NN-M',
      'WA-PLAN15-NN-XL',
      'WA-PLAN15-RR-2L',
      'WA-PLAN15-RR-L',
      'WA-PLAN15-RR-M',
      'WA-PLAN15-RR-XL',
      'WA-PLAN15-WW-2L',
      'WA-PLAN15-WW-3L',
      'WA-PLAN15-WW-5L',
      'WA-PLAN15-WW-L',
      'WA-PLAN15-WW-M',
      'WA-PLAN15-WW-XL',
      'WA-PLAN15-YY-2L',
      'WA-PLAN15-YY-L',
      'WA-PLAN15-YY-XL',
      'WA-222PLACL31-AA-L',
      'WA-20FBM03-YD-L',
      'WA-FBA090-BG199-XX-S',
      'WP-223BIGC109-AA-S',
      'WA-FBA063-BG-GA-M',
      'WA-FBA090-BG299-XX-M',
      'WP-223BIGC109-WW-S',
      'WA-FBA090-BG199-XX-L',
      'WP-223BIGC109-DD-S',
      'WA-20FBM03-YD-M',
      'WA-FBA090-BG199-XX-M',
      'WP-223BIGC109-DD-M'
      );

      $table = "<table><tr><td>code</td><td>qty</td></tr>";

      if(! empty($arr))
      {
        foreach($arr as $code)
        {
          $qty = 0;
          $rs = $this->ms
          ->select('OIBQ.OnHandQty AS qty')
          ->from('OIBQ')
          ->join('OBIN', 'OIBQ.BinAbs = OBIN.AbsEntry','left')
          ->where('OIBQ.ItemCode', $code)
          ->where('OBIN.BinCode', 'AFG-0010-00001')
          ->get();

          if($rs->num_rows() == 1)
          {
            $qty = $rs->row()->qty;
          }

          $table .= "<tr><td>{$code}</td><td>{$qty}</td></tr>";
        }
      }

      $table .= "</table>";

      echo $table;
  }


} //--- end class








 ?>
