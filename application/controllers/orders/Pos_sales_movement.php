<?php
class Pos_sales_movement extends PS_Controller
{
  public $menu_code = 'SOPOSMV';
  public $menu_group_code = 'POS';
  public $menu_sub_group_code = '';
  public $title = 'POS Sales Movement';
  public $segment = 5;

  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'orders/pos_sales_movement';
    $this->load->model('orders/order_pos_round_model');
    $this->load->model('orders/order_down_payment_model');
    $this->load->model('orders/order_pos_model');
    $this->load->model('orders/pos_sales_movement_model');
    $this->load->model('masters/shop_model');
    $this->load->model('masters/pos_model');
    $this->load->helper('shop');
    $this->load->helper('payment_method');
    $this->load->helper('order_pos');
    $this->load->helper('bank');
  }

  public function index()
  {
    $filter = array(
      'code' => get_filter('code', 'sv_code', ''),
      'round_code' => get_filter('round_code', 'sv_round_code', ''),
      'shop_id' => get_filter('shop_id', 'sv_shop_id', 'all'),
      'pos_id' => get_filter('pos_id', 'sv_pos_id', 'all'),
      'role' => get_filter('role', 'sv_role', 'all'),
      'type' => get_filter('type', 'sv_type', 'all'),
      'bank' => get_filter('bank', 'sv_bank', 'all'),
      'from_date' => get_filter('from_date', 'sv_from_date', ''),
      'to_date' => get_filter('to_date', 'sv_to_date', '')
    );

    if($this->input->post('search'))
    {
      redirect($this->home);
    }
    else
    {
      $perpage = get_rows();
      $rows = $this->pos_sales_movement_model->count_rows($filter);
      $filter['details'] = $this->pos_sales_movement_model->get_list($filter, $perpage, $this->uri->segment($this->segment));
      $init = pagination_config($this->home.'/index/', $rows, $perpage, $this->segment);
      $this->pagination->initialize($init);
      $this->load->view('pos_sales_movement/pos_sales_movement_list', $filter);
    }
  }


  public function export_filter()
  {
    $token = $this->input->post('token');
    $ds = json_decode($this->input->post('data'));

    $filter = array(
      'code' => $ds->code,
      'round_code' => $ds->round_code,
      'shop_id' => $ds->shop_id,
      'pos_id' => $ds->pos_id,
      'role' => $ds->role,
      'type' => $ds->type,
      'bank' => $ds->bank,
      'from_date' => $ds->from_date,
      'to_date' => $ds->to_date,
    );

    $res = $this->pos_sales_movement_model->get_export_list($filter);

    $report_title = "POS Sales Movement Report";

    //--- load excel library
    $this->load->library('excel');

    $this->excel->setActiveSheetIndex(0);
    $this->excel->getActiveSheet()->setTitle($report_title);

    //--- set report title header
    $this->excel->getActiveSheet()->setCellValue('A1', $report_title);
    $this->excel->getActiveSheet()->mergeCells('A1:K1');

    //--- set Table header
    $this->excel->getActiveSheet()->setCellValue('A3', 'ลำดับ');
    $this->excel->getActiveSheet()->setCellValue('B3', 'วันที่');
    $this->excel->getActiveSheet()->setCellValue('C3', 'เลขที่');
    $this->excel->getActiveSheet()->setCellValue('D3', 'ประเภท');
    $this->excel->getActiveSheet()->setCellValue('E3', 'ยอดเงิน');
    $this->excel->getActiveSheet()->setCellValue('F3', 'ช่องทาง');
    $this->excel->getActiveSheet()->setCellValue('G3', 'เลขที่บัญชี');
    $this->excel->getActiveSheet()->setCellValue('H3', 'รอบการขาย');
    $this->excel->getActiveSheet()->setCellValue('I3', 'จุดขาย');
    $this->excel->getActiveSheet()->setCellValue('J3', 'POS No.');
    $this->excel->getActiveSheet()->setCellValue('K3', 'พนักงาน');

    $row = 4;

    if( ! empty($res))
    {
      $no = 1;

      foreach($res as $rs)
      {
        $this->excel->getActiveSheet()->setCellValue("A{$row}", $no);
        $this->excel->getActiveSheet()->setCellValue("B{$row}", thai_date($rs->date_upd, TRUE, '/'));
        $this->excel->getActiveSheet()->setCellValue("C{$row}", $rs->code);
        $this->excel->getActiveSheet()->setCellValue("D{$row}", movement_type_label($rs->type));
        $this->excel->getActiveSheet()->setCellValue("E{$row}", $rs->amount);
        $this->excel->getActiveSheet()->setCellValue("F{$row}", payment_role_label($rs->payment_role));
        $this->excel->getActiveSheet()->setCellValue("G{$row}", $rs->acc_no);
        $this->excel->getActiveSheet()->setCellValue("H{$row}", $rs->round_code);
        $this->excel->getActiveSheet()->setCellValue("I{$row}", $rs->shop_code);
        $this->excel->getActiveSheet()->setCellValue("J{$row}", $rs->pos_code);
        $this->excel->getActiveSheet()->setCellValue("K{$row}", $rs->user);
        $no++;
        $row++;
      }

      $this->excel->getActiveSheet()->getStyle("E4:E{$row}")->getNumberFormat()->setFormatCode('#,##0.00');
    }

    setToken($token);
    $file_name = "{$report_title}.xlsx";
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'); /// form excel 2007 XLSX
    header('Content-Disposition: attachment;filename="'.$file_name.'"');
    $writer = PHPExcel_IOFactory::createWriter($this->excel, 'Excel2007');
    $writer->save('php://output');
  }


  public function clear_filter()
  {
    $filter = array(
      'sv_code',
      'sv_round_code',
      'sv_shop_id',
      'sv_pos_id',
      'sv_type',
      'sv_bank',
      'sv_role',
      'sv_from_date',
      'sv_to_date'
    );

    return clear_filter($filter);
  }
}

 ?>
