<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Stock_balance_year extends PS_Controller
{
  public $menu_code = 'RICSBY';
	public $menu_group_code = 'RE';
  public $menu_sub_group_code = 'REINVT';
	public $title = 'รายงานสินค้าคงเหลือ แยกตามปีสินค้า';
  public $filter;
  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'report/inventory/stock_balance_year';
    $this->load->model('report/inventory/stock_balance_year_report_model');
    $this->load->model('masters/products_model');
  }

  public function index()
  {
    $this->load->model('masters/warehouse_model');
    $whList = $this->warehouse_model->get_all_warehouse();
    $ds['whList'] = $whList;
    $this->load->view('report/inventory/report_stock_balance_year', $ds);
  }


  public function get_report()
  {
    $sc = TRUE;
    $allProduct = $this->input->get('allProduct');
    $pdFrom = $this->input->get('pdFrom');
    $pdTo = $this->input->get('pdTo');

    $allWarehouse = $this->input->get('allWhouse');
    $warehouse = $this->input->get('warehouse');

    $ds = array(
      'allProduct' => $allProduct,
      'pdFrom' => $pdFrom,
      'pdTo' => $pdTo,
      'allWarehouse' => $allWarehouse,
      'warehouse' => $warehouse
    );

    $Years = array();
    $fYear = getConfig('START_YEAR');
    $cYear = date('Y');

    while($fYear <= $cYear)
    {
      $Years[] = $fYear;
      $fYear++;
    }

    $Years[] = '0000';

    $this->stock_balance_year_report_model->get_data($ds);

    // if($allProduct == 0)
    // {
    //   $qr .= "AND pd.code >= '".$pdFrom."' ";
    //   $qr .= "AND pd.code <= '".$pdTo."' ";
    // }
    //
    // $qr .= "GROUP BY st.id_product ";
    // $qr .= "ORDER BY ps.code ASC , co.code ASC, si.position ASC ";
    //
    // $qs = dbQuery($qr);
    //
    // if(dbNumRows($qs) < 2001)
    // {
    //   $ds = array();
    //   $no = 1;
    //   $total = array();
    //   foreach($Years as $year)
    //   {
    //     $total[$year.'_sum'] = 0;
    //   }
    //
    //   while($rs = dbFetchObject($qs))
    //   {
    //     $arr = array(
    //       'no' => $no,
    //       'pdCode' => $rs->code,
    //       'pdName' => $rs->name
    //     );
    //
    //     foreach($Years as $year)
    //     {
    //       $arr[$year.'_qty'] = $rs->year == $year ? number($rs->qty) : '-';
    //       $total[$year.'_sum'] += $rs->year == $year ? $rs->qty : 0;
    //     }
    //
    //     $no++;
    //     array_push($ds, $arr);
    //     unset($arr);
    //   }
    //
    //   array_push($ds, $total);
    // }
    // else
    // {
    //   $sc = FALSE;
    //   $message = 'ผลลัพธ์มีมากกว่า 2000 รายการ กรุณาส่งออกข้อมูลแทนการแสดงผลหน้าจอ';
    // }
    //
    // echo $sc === TRUE ? json_encode($ds) : $message;
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


} //--- end class








 ?>
