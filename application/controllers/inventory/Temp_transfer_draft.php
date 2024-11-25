<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Temp_transfer_draft extends PS_Controller
{
  public $menu_code = 'TETDCK';
	public $menu_group_code = 'TE';
  public $menu_sub_group_code = 'TETRANSFER';
	public $title = 'ตรวจสอบ Transfer Draft';
  public $filter;
  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'inventory/temp_transfer_draft';
    $this->load->model('inventory/temp_transfer_draft_model');
  }


  public function index()
  {
    $filter = array(
      'code'          => get_filter('code', 'temp_tr_code', ''),
      'customer'      => get_filter('customer', 'temp_tr_customer', ''),
      'from_date'     => get_filter('from_date', 'temp_tr_from_date', ''),
      'to_date'       => get_filter('to_date', 'temp_tr_to_date', ''),
      'status'      => get_filter('status', 'temp_tr_status', 'all'),
      'is_received' => get_filter('is_received', 'temp_tr_is_received', 'all')
    );

		//--- แสดงผลกี่รายการต่อหน้า
		$perpage = get_rows();
		//--- หาก user กำหนดการแสดงผลมามากเกินไป จำกัดไว้แค่ 300
		if($perpage > 300)
		{
			$perpage = 20;
		}

		$segment  = 4; //-- url segment
		$rows     = $this->temp_transfer_draft_model->count_rows($filter, 8);
		//--- ส่งตัวแปรเข้าไป 4 ตัว base_url ,  total_row , perpage = 20, segment = 3
		$init	    = pagination_config($this->home.'/index/', $rows, $perpage, $segment);
		$orders   = $this->temp_transfer_draft_model->get_list($filter, $perpage, $this->uri->segment($segment), 8);

    $filter['orders'] = $orders;

		$this->pagination->initialize($init);
    $this->load->view('inventory/temp_transfer_draft/temp_list', $filter);
  }



  public function get_detail($docEntry)
  {
    $this->load->model('stock/stock_model');

    $code = "";

    $detail = $this->temp_transfer_draft_model->get_detail($docEntry);

    if(!empty($detail))
    {
      $i = 0;

      foreach($detail as $rs)
      {
        if($i === 0)
				{
					$code = $rs->U_ECOMNO;
          $i++;
				}

        $rs->onhand = $this->stock_model->get_stock_zone($rs->F_FROM_BIN, $rs->ItemCode);
      }
    }

    $ds['details'] = $detail;
    $ds['code'] = $code;
    $this->load->view('inventory/temp_transfer_draft/temp_detail', $ds);
  }


  public function delete()
	{
		$sc = TRUE;
		$docEntry = $this->input->post('docEntry'); //--- docEntry

		$doc = $this->temp_transfer_draft_model->get($docEntry);

		if(!empty($doc))
		{
			if($doc->F_Sap != 'Y')
			{
				if(! $this->temp_transfer_draft_model->delete($docEntry))
				{
					$sc = FALSE;
					$this->error = "Delete failed";
				}

			}
			else
			{
				$sc = FALSE;
				$this->error = "Document already in SAP";
			}
		}
		else
		{
			$sc = FALSE;
			$this->error = "Document not exists";
		}

		echo $sc === TRUE ? 'success' : $this->error;
	}



  public function export_diff()
  {
    $this->load->model('stock/stock_model');
    $token = $this->input->post('token');

    //---  Report title
    $report_title = 'ยอดต่างรายการสินค้าในโซน';
    $channels_title = 'ยอดต่าง = ยอดในโซน - ยอดที่ออเดอร์';
    $pd_title = 'ยอดติดลบคือยอดที่มีในโซนน้อยกว่าที่สั่งมา';

    //--- load excel library
    $this->load->library('excel');

    $this->excel->setActiveSheetIndex(0);
    $this->excel->getActiveSheet()->setTitle('Item Diff');

    //--- set report title header
    $this->excel->getActiveSheet()->setCellValue('A1', $report_title);
    $this->excel->getActiveSheet()->mergeCells('A1:G1');
    $this->excel->getActiveSheet()->setCellValue('A2', $channels_title);
    $this->excel->getActiveSheet()->mergeCells('A2:G2');
    $this->excel->getActiveSheet()->setCellValue('A3', $pd_title);
    $this->excel->getActiveSheet()->mergeCells('A3:G3');
    $this->excel->getActiveSheet()->setCellValue('A4', 'วันที่เอกสาร : '.thai_date(now()));
    $this->excel->getActiveSheet()->mergeCells('A4:G4');

    $this->excel->getActiveSheet()->setCellValue('A5', 'ลำดับ');
    $this->excel->getActiveSheet()->setCellValue('B5', 'BinCode');
    $this->excel->getActiveSheet()->setCellValue('C5', 'ItemCode');
    $this->excel->getActiveSheet()->setCellValue('D5', 'onhand - order');

    $ds = array();
    $orders = $this->temp_transfer_draft_model->get_error_list();
    if(!empty($orders))
    {
      foreach($orders as $order)
      {
        $details = $this->temp_transfer_draft_model->get_detail($order->code);
        if(!empty($details))
        {
          foreach($details as $rs)
          {
            $onhand = $this->stock_model->get_stock_zone($rs->F_FROM_BIN, $rs->ItemCode);
            if($rs->Quantity > $onhand)
            {
              $diff = $onhand - $rs->Quantity;
              if(isset($ds[$rs->F_FROM_BIN][$rs->ItemCode]))
              {
                $ds[$rs->F_FROM_BIN][$rs->ItemCode] += $diff;
              }
              else
              {
                $ds[$rs->F_FROM_BIN][$rs->ItemCode] = $diff;
              }
            }
          }
        }
      }

      $row = 6;

      if(!empty($ds))
      {
        $no = 1;
        foreach($ds as $bin => $items)
        {
          foreach($items as $item => $qty)
          {
            $this->excel->getActiveSheet()->setCellValue('A'.$row, $no);

            $this->excel->getActiveSheet()->setCellValue('B'.$row, $bin);

            $this->excel->getActiveSheet()->setCellValue('C'.$row, $item);

            $this->excel->getActiveSheet()->setCellValue('D'.$row, $qty);

            $no++;
            $row++;
          }
        }
      }
    }

    setToken($token);
    $file_name = "Transfer draft stock diff.xlsx";
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'); /// form excel 2007 XLSX
    header('Content-Disposition: attachment;filename="'.$file_name.'"');
    $writer = PHPExcel_IOFactory::createWriter($this->excel, 'Excel2007');
    $writer->save('php://output');
  }


  public function clear_filter()
  {
    $filter = array(
      'temp_tr_code',
      'temp_tr_supplier',
      'temp_tr_from_date',
      'temp_tr_to_date',
      'temp_tr_status',
      'temp_tr_is_received'
    );

    clear_filter($filter);

    echo 'done';
  }

}//--- end class
?>
