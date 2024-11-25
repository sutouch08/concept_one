<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Pos_sales_report extends PS_Controller
{
  public $menu_code = 'RSOPOS';
	public $menu_group_code = 'RE';
  public $menu_sub_group_code = 'RESALE';
	public $title = 'รายงานยอดขาย POS แยกตามเลขที่เอกสาร';
  public $filter;
  
  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'report/sales/pos_sales_report';
    $this->load->model('report/sales/pos_sales_report_model');
    $this->load->model('masters/pos_model');
    $this->load->model('masters/shop_model');
    $this->load->model('masters/payment_methods_model');
    $this->load->model('masters/warehouse_model');
    $this->load->helper('shop');
  }

  public function index()
  {
    $ds = array(
      'shop_list' => $this->shop_model->get_all(),
      'pos_list' => $this->pos_model->get_all()
    );

    $this->load->view('report/sales/pos_sales_report', $ds);
  }


  public function get_report()
  {
    $sc = TRUE;
    $res = array();
    $ds = json_decode($this->input->post('data'));

    $this->db
    ->select('od.*')
    ->select('sh.code AS shop_code, sh.name AS shop_name')
    ->select('po.code AS pos_code, po.name AS pos_name')
    ->select('u.name AS emp_name')
    ->from('order_pos AS od')
    ->join('shop AS sh', 'od.shop_id = sh.id', 'left')
    ->join('shop_pos AS po', 'od.pos_id = po.id', 'left')
    ->join('user AS u', 'od.uname = u.uname', 'left')
    ->where('od.status !=', 'D')
    ->where('od.date_add >=', from_date($ds->from_date))
    ->where('od.date_add <=', to_date($ds->to_date));

    if($ds->allShop == 0)
    {
      $this->db->where_in('od.shop_id', $ds->shopList);
    }

    if($ds->allPos == 0)
    {
      $this->db->where_in('od.pos_id', $ds->posList);
    }

    if( ! empty($ds->billFrom) &&  ! empty($ds->billTo))
    {
      $this->db->where('od.code >=', $ds->billFrom)->where('od.code <=', $ds->billTo);
    }

    if( ! empty($ds->uname) && $ds->uname != 'all')
    {
      $this->db->where('od.uname', $ds->uname);
    }

    $rows = $this->db->order_by('od.date_add', 'ASC')->order_by('od.code', 'ASC')->get();

    $totalAmount = 0;

    if($rows->num_rows() > 0)
    {
      $no = 1;

      foreach($rows->result() as $rs)
      {
        $rs->no = $no;
        $rs->date = thai_date($rs->date_add);
        $totalAmount += $rs->payAmount;
        $rs->payAmount = number($rs->payAmount, 2);
        $res[] = $rs;
        $no++;
      }
    }

    $res[] = ['totalAmount' => number($totalAmount, 2)];

    echo json_encode($res);
  }


  public function do_export()
  {
    $fromDate = $this->input->post('fromDate');
    $toDate = $this->input->post('toDate');
    $billFrom = $this->input->post('billFrom');
    $billTo = $this->input->post('billTo');
    $allShop = $this->input->post('allShop');
    $allPos = $this->input->post('allPos');
    $shopList = $this->input->post('shop');
    $posList = $this->input->post('pos');
    $uname = $this->input->post('uname');
    $token = $this->input->post('token');

    $this->db
    ->select('od.*')
    ->select('sh.code AS shop_code, sh.name AS shop_name')
    ->select('po.code AS pos_code, po.name AS pos_name')
    ->select('u.name AS emp_name')
    ->from('order_pos AS od')
    ->join('shop AS sh', 'od.shop_id = sh.id', 'left')
    ->join('shop_pos AS po', 'od.pos_id = po.id', 'left')
    ->join('user AS u', 'od.uname = u.uname', 'left')
    ->where('od.status !=', 'D')
    ->where('od.date_add >=', from_date($fromDate))
    ->where('od.date_add <=', to_date($toDate));

    if($allShop == 0)
    {
      $this->db->where_in('od.shop_id', $shopList);
    }

    if($allPos == 0)
    {
      $this->db->where_in('od.pos_id', $posList);
    }

    if( ! empty($billFrom) &&  ! empty($billTo))
    {
      $this->db->where('od.code >=', $billFrom)->where('od.code <=', $billTo);
    }

    if( ! empty($uname) && $uname != 'all')
    {
      $this->db->where('od.uname', $uname);
    }

    $rows = $this->db->order_by('od.date_add', 'ASC')->order_by('od.code', 'ASC')->get();

    //---  Report title
    $report_title = "รายงานยอดขาย POS แยกตามเลขที่เอกสาร";
        //--- load excel library
    $this->load->library('excel');

    $this->excel->setActiveSheetIndex(0);
    $this->excel->getActiveSheet()->setTitle('POS Sales Report');

    //--- set report title header
    $this->excel->getActiveSheet()->setCellValue('A1', $report_title);
    $this->excel->getActiveSheet()->setCellValue('A2', 'วันที่เอกสาร : ('.thai_date($fromDate,'/') .') - ('.thai_date($toDate,'/').')');

    //--- set Table header
    $this->excel->getActiveSheet()->setCellValue('A4', 'ลำดับ');
    $this->excel->getActiveSheet()->setCellValue('B4', 'วันที่');
    $this->excel->getActiveSheet()->setCellValue('C4', 'เลขที่เอกสาร');
    $this->excel->getActiveSheet()->setCellValue('D4', 'ใบตัดยอดขาย');
    $this->excel->getActiveSheet()->setCellValue('E4', 'จุดขาย');
    $this->excel->getActiveSheet()->setCellValue('F4', 'เครื่อง POS');
    $this->excel->getActiveSheet()->setCellValue('G4', 'พนักงานขาย');
    $this->excel->getActiveSheet()->setCellValue('H4', 'ยอดเงิน');

    //---- กำหนดความกว้างของคอลัมภ์
    $this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(10);
    $this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(12);
    $this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(15);
    $this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(15);
    $this->excel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
    $this->excel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
    $this->excel->getActiveSheet()->getColumnDimension('G')->setWidth(20);
    $this->excel->getActiveSheet()->getColumnDimension('H')->setWidth(20);

    $row = 5;


    if( $rows->num_rows() > 0)
    {
      $no = 1;

      foreach($rows->result() as $rs)
      {
        $y		= date('Y', strtotime($rs->date_add));
        $m		= date('m', strtotime($rs->date_add));
        $d		= date('d', strtotime($rs->date_add));

        $date = PHPExcel_Shared_Date::FormattedPHPToExcel($y, $m, $d);

        $this->excel->getActiveSheet()->setCellValue('A'.$row, $no);
        $this->excel->getActiveSheet()->setCellValue('B'.$row, $date);
        $this->excel->getActiveSheet()->setCellValue('C'.$row, $rs->code);
        $this->excel->getActiveSheet()->setCellValue('D'.$row, $rs->ref_code);
        $this->excel->getActiveSheet()->setCellValue('E'.$row, $rs->shop_name);
        $this->excel->getActiveSheet()->setCellValue('F'.$row, $rs->pos_name);
        $this->excel->getActiveSheet()->setCellValue('G'.$row, $rs->emp_name);
        $this->excel->getActiveSheet()->setCellValue('H'.$row, $rs->payAmount);

        $no++;
        $row++;
      }

      $this->excel->getActiveSheet()->getStyle('B5:B'.$row)->getNumberFormat()->setFormatCode('dd/mm/yyyy');
      $this->excel->getActiveSheet()->getStyle('D5:D'.$row)->getNumberFormat()->setFormatCode('#,##0.00');
    }

    $rx = $row - 1;

    $this->excel->getActiveSheet()->setCellValue('A'.$row, 'รวม');
    $this->excel->getActiveSheet()->getStyle("A{$row}")->getAlignment()->setHorizontal('right');
    $this->excel->getActiveSheet()->mergeCells("A{$row}:G{$row}");
    $this->excel->getActiveSheet()->setCellValue("H{$row}", "=SUM(H5:H{$rx})");
    $this->excel->getActiveSheet()->getStyle("H{$row}")->getNumberFormat()->setFormatCode('#,##0.00');


    setToken($token);
    $file_name = "POS Sales Report.xlsx";
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'); /// form excel 2007 XLSX
    header('Content-Disposition: attachment;filename="'.$file_name.'"');
    $writer = PHPExcel_IOFactory::createWriter($this->excel, 'Excel2007');
    $writer->save('php://output');

  }


  public function get_title($ds = array())
  {
    $list = "";
    if(!empty($ds))
    {
      $i = 1;
      foreach($ds as $rs)
      {
        $list .= $i === 1 ? $rs : ", {$rs}";
        $i++;
      }
    }

    return $list;
  }


} //--- end class








 ?>
