<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Products_backlogs_summary extends PS_Controller
{
  public $menu_code = 'RADPDB';
	public $menu_group_code = 'RE';
  public $menu_sub_group_code = 'REAUDIT';
	public $title = 'รายงานสรุปยอดสินค้าค้างจัด';
  public $filter;
  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'report/audit/products_backlogs_summary';
    $this->load->model('report/audit/products_backlogs_model');
    $this->load->model('masters/products_model');
    $this->load->model('orders/orders_model');
    $this->load->model('masters/channels_model');
    $this->load->model('masters/warehouse_model');
    $this->load->helper('warehouse');
  }

  public function index()
  {
    $ds = array(
      'channels_list' => $this->channels_model->get_data()
    );

    $this->load->view('report/audit/report_products_backlogs_summary', $ds);
  }

  private function channels_array()
  {
    $ds = array();

    $rs = $this->db->get('channels');

    if($rs->num_rows() > 0)
    {
      foreach($rs->result() as $rd)
      {
        $ds[$rd->code] = $rd->name;
      }
    }

    return $ds;
  }


  public function get_report()
  {
    $sc = TRUE;
    $ds = json_decode($this->input->post('data'));
    $res = [];

    if( ! empty($ds))
    {
      $filter = array(
        'from_date' => $ds->fromDate,
        'to_date' => $ds->toDate,
        'all_role' => $ds->allRole,
        'role' => $ds->role,
        'all_channels' => $ds->allChannels,
        'channels' => $ds->channels,
        'warehouse_code' => $ds->warehouse_code
      );

      $details = $this->products_backlogs_model->get_data($filter);

      if( ! empty($details))
      {
        $this->load->model('stock/stock_model');

        $no = 1;

        foreach($details as $rs)
        {
          $res[] = array(
            'no' => number($no),
            'product_code' => $rs->product_code,
            'product_name' => $rs->product_name,
            'order_qty' => number($rs->order_qty),
            'stock_qty' => number($this->stock_model->get_sell_stock($rs->product_code, $ds->warehouse_code))
          );

          $no++;
        }
      }
      else
      {
        $res[] = ['nodata' => 'nodata'];
      }
    }
    else
    {
      $sc = FALSE;
      set_error('required');
    }

    $arr = array(
      'status' => $sc === TRUE ? 'success' : 'failed',
      'message' => $sc === TRUE ? 'success' : $this->error,
      'data' => $res
    );

    echo json_encode($arr);
  }



  public function do_export()
  {
    $ds = json_decode($this->input->post('data'));
    $token = $this->input->post('token');

    //--- load excel library
    $this->load->library('excel');

    $this->excel->setActiveSheetIndex(0);
    $this->excel->getActiveSheet()->setTitle('Products Backlogs Summary');

    if( ! empty($ds))
    {
      $filter = array(
        'from_date' => $ds->fromDate,
        'to_date' => $ds->toDate,
        'all_role' => $ds->allRole,
        'role' => $ds->role,
        'all_channels' => $ds->allChannels,
        'channels' => $ds->channels,
        'warehouse_code' => $ds->warehouse_code
      );

      $details = $this->products_backlogs_model->get_data($filter);

      //---  Report title
      $report_title = "รายงานสรุปยอดสินค้าค้างจัด ณ วันที่ ".thai_date(now(), TRUE, '/');
      $role_title = "เอกสาร : ". ($ds->allRole == 1 ? 'ทั้งหมด' : $this->get_role_title($ds->role));
      $wh_title = 'คลัง :  '. $ds->warehouse_code.' | '.warehouse_name($ds->warehouse_code);
      $ch_title = "ช่องทางขาย : ". ($ds->allChannels == 1 ? 'ทั้งหมด' : $this->get_title($ds->channels));

      //--- set report title header
      $this->excel->getActiveSheet()->setCellValue('A1', $report_title);
      $this->excel->getActiveSheet()->setCellValue('A2', $role_title);
      $this->excel->getActiveSheet()->setCellValue('A3', $ch_title);
      $this->excel->getActiveSheet()->setCellValue('A4', $wh_title);

      //--- set Table header
      $row = 6;

      $this->excel->getActiveSheet()->setCellValue("A{$row}", '#');
      $this->excel->getActiveSheet()->setCellValue("B{$row}", 'รหัสสินค้า');
      $this->excel->getActiveSheet()->setCellValue("C{$row}", 'สินค้า');
      $this->excel->getActiveSheet()->setCellValue("D{$row}", 'จำนวน');
      $this->excel->getActiveSheet()->setCellValue("E{$row}", 'สต็อก');
      $row++;

      //---- กำหนดความกว้างของคอลัมภ์
      $this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(30);
      $this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(60);
      $this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
      $this->excel->getActiveSheet()->getColumnDimension('E')->setWidth(20);

      if( ! empty($details))
      {
        $this->load->model('stock/stock_model');

        $no = 1;

        foreach($details as $rs)
        {
          $this->excel->getActiveSheet()->setCellValue("A{$row}", $no);
          $this->excel->getActiveSheet()->setCellValue("B{$row}", $rs->product_code);
          $this->excel->getActiveSheet()->setCellValue("C{$row}", $rs->product_name);
          $this->excel->getActiveSheet()->setCellValue("D{$row}", $rs->order_qty);
          $this->excel->getActiveSheet()->setCellValue("E{$row}", $this->stock_model->get_sell_stock($rs->product_code, $ds->warehouse_code));
          $no++;
          $row++;
        }

        $this->excel->getActiveSheet()->getStyle("D7:E{$row}")->getNumberFormat()->setFormatCode('#,##0');
      }
    }

    setToken($token);
    $file_name = "Report Products Backlogs Summary.xlsx";
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'); /// form excel 2007 XLSX
    header('Content-Disposition: attachment;filename="'.$file_name.'"');
    $writer = PHPExcel_IOFactory::createWriter($this->excel, 'Excel2007');
    $writer->save('php://output');

  }



  public function print_report()
  {
    $this->load->model('stock/stock_model');
    $this->load->library('printer');
    $ds = json_decode($this->input->post('data'));
    $res = [];

    if( ! empty($ds))
    {
      $filter = array(
        'from_date' => $ds->fromDate,
        'to_date' => $ds->toDate,
        'all_role' => $ds->allRole,
        'role' => $ds->role,
        'all_channels' => $ds->allChannels,
        'channels' => $ds->channels,
        'warehouse_code' => $ds->warehouse_code
      );

      //---  Report title
      $report_title = "รายงานสรุปยอดสินค้าค้างจัด ณ วันที่ ".thai_date(now(), TRUE, '/');
      $role_title = ($ds->allRole == 1 ? 'ทั้งหมด' : $this->get_role_title($ds->role));
      $wh_title = $ds->warehouse_code.' | '.warehouse_name($ds->warehouse_code);
      $ch_title = ($ds->allChannels == 1 ? 'ทั้งหมด' : $this->get_title($ds->channels));
      
      $arr = array(
        'warehouse_code' => $ds->warehouse_code,
        'report_title' => $report_title,
        'role_title' => $role_title,
        'wh_title' => $wh_title,
        'ch_title' => $ch_title,
        'details' => $this->products_backlogs_model->get_data($filter, TRUE)
      );

      $this->load->view('print/print_products_backlogs', $arr);
    }
    else
    {
      $this->page_error();
    }
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


  private function get_role_title($ds = array())
  {
    $list = "";

    $roles = array(
      'S' => 'WO',
      'C' => 'WC',
      'N' => 'WT',
      'P' => 'WS',
      'U' => 'WU',
      'T' => 'WQ',
      'Q' => 'WV',
      'L' => 'WL'
    );

    if( ! empty($ds))
    {
      $i = 1;
      foreach($ds as $rs)
      {
        if( ! empty($roles[$rs]))
        {
          $list .= $i=== 1 ? $roles[$rs] : ", {$roles[$rs]}";
          $i++;
        }
      }
    }

    return $list;
  }


  private function get_state_title($ds = array())
  {
    $list = "";

    $states = array(
      "1" => "รอดำเนินการ",
      "2" => "รอชำระเงิน",
      "3" => "รอจัดสินค้า",
      "4" => "กำลังจัดสินค้า",
      "5" => "รอตรวจ",
      "6" => "กำลังตรวจ",
      "7" => "รอเปิดบิล",
      "8" => "เปิดบิลแล้ว",
      "9" => "ยกเลิก"
    );

    if( ! empty($ds))
    {
      $i = 1;
      foreach($ds as $rs)
      {
        if( ! empty($states[$rs]))
        {
          $list .= $i === 1 ? $states[$rs] : ", {$states[$rs]}";
          $i++;
        }
      }
    }

    return $list;
  }


} //--- end class








 ?>
