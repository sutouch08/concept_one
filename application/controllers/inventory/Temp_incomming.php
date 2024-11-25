<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Temp_incomming extends PS_Controller
{
  public $menu_code = 'TEDPIN';
	public $menu_group_code = 'TE';
  public $menu_sub_group_code = 'TEDPM';
	public $title = 'ใบรับมัดจำ - ถังกลาง';
  public $filter;
  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'inventory/Temp_incomming';
    $this->load->model('inventory/temp_incomming_model');
  }


  public function index()
  {
    $filter = array(
      'code' => get_filter('code', 'temp_code', ''),
      'customer' => get_filter('customer', 'temp_customer', ''),
      'from_date' => get_filter('from_date', 'temp_from_date', ''),
      'to_date' => get_filter('to_date', 'temp_to_date', ''),
      'status' => get_filter('status', 'temp_status', 'all')
    );

		//--- แสดงผลกี่รายการต่อหน้า
		$perpage = get_rows();

		$segment  = 4; //-- url segment
		$rows     = $this->temp_incomming_model->count_rows($filter);
		//--- ส่งตัวแปรเข้าไป 4 ตัว base_url ,  total_row , perpage = 20, segment = 3
		$init	    = pagination_config($this->home.'/index/', $rows, $perpage, $segment);
		$orders   = $this->temp_incomming_model->get_list($filter, $perpage, $this->uri->segment($segment));

    $filter['orders'] = $orders;

		$this->pagination->initialize($init);
    $this->load->view('inventory/temp_incomming/temp_list', $filter);
  }



  public function get_detail($id)
  {
    $ds['doc'] = $this->temp_incomming_model->get($id);
    $ds['card'] = $this->temp_incomming_model->get_card($id);
    $this->load->view('inventory/temp_incomming/temp_detail', $ds);
  }


	function delete_temp($id)
	{
		$sc = TRUE;
		$doc = $this->temp_incomming_model->get($id);

		if( ! empty($doc))
		{
			if($doc->F_Sap === 'N' OR $doc->F_Sap == NULL)
			{
				$this->mc->trans_begin();
				$ds = $this->temp_incomming_model->delete_temp_details($id);
				$rs = $this->temp_incomming_model->delete_temp($id);

				if($ds && $rs)
				{
					$this->mc->trans_commit();
				}
				else
				{
					$this->mc->trans_rollback();
					$sc = FALSE;
					$this->error = "Delete Temp failed";
				}
			}
		}
		else
		{
			$sc = FALSE;
			$this->error = "ยกเลิกไม่สำเร็จ เอกสารถูกนำเข้า SAP แล้ว";
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
    $orders = $this->temp_incomming_model->get_error_list();

    if(!empty($orders))
    {
      foreach($orders as $order)
      {
        $details = $this->temp_incomming_model->get_detail($order->id);

        if(!empty($details))
        {
          foreach($details as $rs)
          {
            $onhand = $this->stock_model->get_stock_zone($rs->BinCode, $rs->ItemCode);

            if($rs->Quantity > $onhand)
            {
              $diff = $onhand - $rs->Quantity;

              if(isset($ds[$rs->BinCode][$rs->ItemCode]))
              {
                $ds[$rs->BinCode][$rs->ItemCode] += $diff;
              }
              else
              {
                $ds[$rs->BinCode][$rs->ItemCode] = $diff;
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
            //--- ลำดับ
            $this->excel->getActiveSheet()->setCellValue('A'.$row, $no);

            $this->excel->getActiveSheet()->setCellValue('B'.$row, $bin);

            //--- เลขที่เอกสาร (SO)
            $this->excel->getActiveSheet()->setCellValue('C'.$row, $item);

            //--- เลขที่อ้างอิง
            $this->excel->getActiveSheet()->setCellValue('D'.$row, $qty);

            $no++;
            $row++;
          }
        }
      }
    }

    setToken($token);
    $file_name = "Stock Diff.xlsx";
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'); /// form excel 2007 XLSX
    header('Content-Disposition: attachment;filename="'.$file_name.'"');
    $writer = PHPExcel_IOFactory::createWriter($this->excel, 'Excel2007');
    $writer->save('php://output');
  }

  public function clear_filter()
  {
    $filter = array(
      'temp_code',
      'temp_customer',
      'temp_from_date',
      'temp_to_date',
      'temp_status'
    );

    clear_filter($filter);

    echo 'done';
  }

}//--- end class
?>
