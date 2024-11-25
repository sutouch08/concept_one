<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Warehouse extends PS_Controller
{
  public $menu_code = 'DBWRHS';
	public $menu_group_code = 'DB';
  public $menu_sub_group_code = 'WAREHOUSE';
	public $title = 'เพิ่ม/แก้ไข คลังสินค้า';
  public $segment = 4;

  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'masters/warehouse';
    $this->load->model('masters/warehouse_model');
    $this->load->helper('warehouse');
  }

  public function index()
  {
    $filter = array(
      'code' => get_filter('code', 'wh_code', ''),
      'name' => get_filter('name', 'wh_name', ''),
      'role' => get_filter('role', 'wh_role', 'all'),
      'is_consignment' => get_filter('is_consignment', 'is_consignment', 'all'),
      'active' => get_filter('active', 'wh_active', 'all'),
      'sell' => get_filter('sell', 'wh_sell', 'all'),
      'prepare' => get_filter('prepare', 'wh_prepare', 'all'),
      'auz' => get_filter('auz', 'wh_auz', 'all')
    );

		//--- แสดงผลกี่รายการต่อหน้า
		$perpage = get_rows();
		//--- หาก user กำหนดการแสดงผลมามากเกินไป จำกัดไว้แค่ 300
		if($perpage > 300)
		{
			$perpage = 20;
		}

		$rows = $this->warehouse_model->count_rows($filter);
		//--- ส่งตัวแปรเข้าไป 4 ตัว base_url ,  total_row , perpage = 20, segment = 3
		$init = pagination_config($this->home.'/index/', $rows, $perpage, $this->segment);
		$list = $this->warehouse_model->get_list($filter, $perpage, $this->uri->segment($this->segment));

    if(!empty($list))
    {
      foreach($list as $rs)
      {
        $rs->zone_count = $this->warehouse_model->count_zone($rs->code);
      }
    }

    $filter['list'] = $list;

		$this->pagination->initialize($init);
    $this->load->view('masters/warehouse/warehouse_list', $filter);
  }



  public function edit($code)
  {
    if($this->pm->can_edit)
    {
      $ds['ds'] = $this->warehouse_model->get($code);
      $this->load->view('masters/warehouse/warehouse_edit', $ds);
    }
    else
    {
      set_error("คุณไม่มีสิทธิ์แก้ไขคลังสินค้า");
      redirect($this->home);
    }
  }



  public function update()
  {
    if($this->pm->can_edit)
    {
      if($this->input->post('code'))
      {
        $code = $this->input->post('code');
        $arr = array(
          'role' => $this->input->post('role'),
          'sell' => $this->input->post('sell'),
          'prepare' => $this->input->post('prepare'),
          'auz' => $this->input->post('auz'),
          'active' => $this->input->post('active'),
          'is_consignment' => get_null($this->input->post('is_consignment')),
          'update_user' => get_cookie('uname')
        );

        if($this->warehouse_model->update($code, $arr))
        {
          set_message("Update Successfull");
          redirect($this->home.'/edit/'.$code);
        }
        else
        {
          set_error("Update Fail");
          redirect($this->home.'/edit/'.$code);
        }
      }
      else
      {
        set_error('ไม่พบรหัสคลังสินค้า');
        redirect($this->home);
      }
    }
    else
    {
      set_error('คุณไม่มีสิทธิ์แก้ไขคลังสินค้า');
      redirect($this->home);
    }
  }


  public function delete($code)
  {
    $sc = TRUE;

    if($this->pm->can_delete)
    {
      //---- count member if exists reject action
      if($this->warehouse_model->has_zone($code))
      {
        $sc = FALSE;
        $this->error = 'ไม่สามารถลบคลังได้เนื่องจากยังมีโซนอยู่';
      }
      //--- check warehouse in SAP if exists reject action
      else if($this->warehouse_model->is_sap_exists($code))
      {
        $sc = FALSE;
        $this->error = 'ไม่สามารถลบคลังได้เนื่องจากยังไม่ได้ลบคลังใน SAP';
      }
      else
      {
        if($this->warehouse_model->delete($code) === FALSE)
        {
          $sc = FALSE;
          $this->error = 'ลบคลังไม่สำเร็จ';
        }
      }
    }
    else
    {
      $sc = FALSE;
      $this->error = 'คุณไม่มีสิทธิ์ลบคลังสินค้า';
    }

    echo $sc === TRUE ? 'success' : $this->error;
  }


  public function syncData()
  {
    $last_sync = $this->warehouse_model->get_last_sync_date();
    //$last_sync = date('Y-m-d H:i:s', strtotime('2019-01-01 00:00:00'));
    $newData = $this->warehouse_model->get_new_data($last_sync);

    if(!empty($newData))
    {
      foreach($newData as $rs)
      {
        if($this->warehouse_model->is_exists($rs->code))
        {
          $ds = array(
            'name' => $rs->name,
            'active' => $rs->Inactive == 'Y' ? 0 : 1,
            'last_sync' => date('Y-m-d H:i:s'),
            'update_user' => 'SAP',
            'old_code' => $rs->old_code,
            'limit_amount' => 0 //$rs->limit_amount
          );

          $this->warehouse_model->update($rs->code, $ds);
        }
        else
        {
          $ds = array(
            'code' => $rs->code,
            'name' => $rs->name,
            'active' => $rs->Inactive == 'Y' ? 0 : 1,
            'last_sync' => date('Y-m-d H:i:s'),
            'update_user' => 'SAP',
            'old_code' => $rs->old_code,
            'limit_amount' => 0 //$rs->limit_amount
          );

          $this->warehouse_model->add($ds);
        }
      }
    }

    echo 'done';
  }


  public function syncAllData()
  {
    $last_sync = date('Y-m-d H:i:s', strtotime('2019-01-01 00:00:00'));
    $newData = $this->warehouse_model->get_new_data($last_sync);

    if(!empty($newData))
    {
      foreach($newData as $rs)
      {
        if($this->warehouse_model->is_exists($rs->code))
        {
          $ds = array(
            'name' => $rs->name,
            'active' => $rs->Inactive == 'Y' ? 0 : 1,
            'last_sync' => date('Y-m-d H:i:s'),
            'update_user' => 'SAP',
            'old_code' => $rs->old_code,
            'limit_amount' => 0 //$rs->limit_amount
          );

          $this->warehouse_model->update($rs->code, $ds);
        }
        else
        {
          $ds = array(
            'code' => $rs->code,
            'name' => $rs->name,
            'active' => $rs->Inactive == 'Y' ? 0 : 1,
            'last_sync' => date('Y-m-d H:i:s'),
            'update_user' => 'SAP',
            'old_code' => $rs->old_code,
            'limit_amount' => 0//$rs->limit_amount
          );

          $this->warehouse_model->add($ds);
        }
      }
    }

    echo 'done';
  }


  public function export_filter()
  {
    $filter = array(
      'code' => get_filter('whCode', 'wh_code', ''),
      'name' => get_filter('whName', 'wh_name', ''),
      'role' => get_filter('whRole', 'wh_role', 'all'),
      'is_consignment' => get_filter('whIsConsignment', 'is_consignment', 'all'),
      'active' => get_filter('whActive', 'wh_active', 'all'),
      'sell' => get_filter('whSell', 'wh_sell', 'all'),
      'prepare' => get_filter('whPrepare', 'wh_prepare', 'all'),
      'auz' => get_filter('whAuz', 'wh_auz', 'all')
    );

    $token = $this->input->post('token');

    $list = $this->warehouse_model->get_list($filter);

    //--- load excel library
    $this->load->library('excel');

    $this->excel->setActiveSheetIndex(0);
    $this->excel->getActiveSheet()->setTitle('Zone master data');

    //--- set Table header


    $this->excel->getActiveSheet()->setCellValue('A1', 'No.');
    $this->excel->getActiveSheet()->setCellValue('B1', 'Code');
    $this->excel->getActiveSheet()->setCellValue('C1', 'Description');
    $this->excel->getActiveSheet()->setCellValue('D1', 'Role');
    $this->excel->getActiveSheet()->setCellValue('E1', 'Bin Location');
    $this->excel->getActiveSheet()->setCellValue('F1', 'Sell');
    $this->excel->getActiveSheet()->setCellValue('G1', 'Pick');
    $this->excel->getActiveSheet()->setCellValue('H1', 'Can be negative');
    $this->excel->getActiveSheet()->setCellValue('I1', 'Active');
    $this->excel->getActiveSheet()->setCellValue('J1', 'Is Consignment');
    $this->excel->getActiveSheet()->setCellValue('K1', 'Limit Amount');


    //---- กำหนดความกว้างของคอลัมภ์
    $this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
    $this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(40);
    $this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
    $this->excel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
    $this->excel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
    $this->excel->getActiveSheet()->getColumnDimension('G')->setWidth(20);
    $this->excel->getActiveSheet()->getColumnDimension('H')->setWidth(20);
    $this->excel->getActiveSheet()->getColumnDimension('I')->setWidth(20);
    $this->excel->getActiveSheet()->getColumnDimension('J')->setWidth(20);
    $this->excel->getActiveSheet()->getColumnDimension('K')->setWidth(20);


    $row = 2;


    if(! empty($list))
    {
      $no = 1;

      foreach($list as $rs)
      {
        $this->excel->getActiveSheet()->setCellValue('A'.$row, $no);
        $this->excel->getActiveSheet()->setCellValue('B'.$row, $rs->code);
        $this->excel->getActiveSheet()->setCellValue('C'.$row, $rs->name);
        $this->excel->getActiveSheet()->setCellValue('D'.$row, $rs->role_name);
        $this->excel->getActiveSheet()->setCellValue('E'.$row, $this->warehouse_model->count_zone($rs->code));
        $this->excel->getActiveSheet()->setCellValue('F'.$row, ($rs->sell ? 'Y' : 'N'));
        $this->excel->getActiveSheet()->setCellValue('G'.$row, ($rs->prepare ? 'Y' : 'N'));
        $this->excel->getActiveSheet()->setCellValue('H'.$row, ($rs->auz ? 'Y' : 'N'));
        $this->excel->getActiveSheet()->setCellValue('I'.$row, ($rs->active ? 'Y' : 'N'));
        $this->excel->getActiveSheet()->setCellValue('J'.$row, ($rs->is_consignment ? 'Y' : 'N'));
        $this->excel->getActiveSheet()->setCellValue('K'.$row, $rs->limit_amount);
        $no++;
        $row++;
      }

      setToken($token);
      $file_name = "Warehouse Master Data.xlsx";
      header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'); /// form excel 2007 XLSX
      header('Content-Disposition: attachment;filename="'.$file_name.'"');
      $writer = PHPExcel_IOFactory::createWriter($this->excel, 'Excel2007');
      $writer->save('php://output');
    }
  }


  public function clear_filter()
  {
    $filter = array('wh_code', 'wh_name', 'wh_role', 'is_consignment', 'wh_active', 'wh_sell', 'wh_prepare', 'wh_auz');
    clear_filter($filter);
  }

} //--- end class

 ?>
