<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Return_lend extends PS_Controller
{
  public $menu_code = 'ICRTLD';
	public $menu_group_code = 'IC';
  public $menu_sub_group_code = 'RETURN';
	public $title = 'คืนสินค้าจากการยืม';
  public $filter;
  public $error;
	public $wms;
	public $isAPI;
  public $required_remark = 1;

  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'inventory/return_lend';
    $this->load->model('inventory/return_lend_model');
    $this->load->model('masters/warehouse_model');
    $this->load->model('masters/zone_model');
    $this->load->model('masters/employee_model');
    $this->load->model('masters/products_model');

    $this->load->helper('employee');
		$this->isAPI = is_true(getConfig('WMS_API'));
  }


  public function index()
  {
		$this->load->helper('warehouse');
    $filter = array(
      'code'    => get_filter('code', 'rl_code', ''),
      'lend_code' => get_filter('lend_code', 'lend_code', ''),
      'empName' => get_filter('empName', 'empName', ''),
      'from_date' => get_filter('from_date', 'rl_from_date', ''),
      'to_date' => get_filter('to_date', 'rl_to_date', ''),
      'zone' => get_filter('zone', 'rl_zone', ''),
      'status' => get_filter('status', 'rl_status', 'all'),
      'must_accept' => get_filter('must_accept', 'rl_must_accept', 'all'),
      'sap' => get_filter('sap', 'rl_sap', 'all')
    );

		//--- แสดงผลกี่รายการต่อหน้า
		$perpage = get_rows();
		//--- หาก user กำหนดการแสดงผลมามากเกินไป จำกัดไว้แค่ 300
		if($perpage > 300)
		{
			$perpage = 20;
		}

		$segment  = 4; //-- url segment
		$rows     = $this->return_lend_model->count_rows($filter);
		//--- ส่งตัวแปรเข้าไป 4 ตัว base_url ,  total_row , perpage = 20, segment = 3
		$init	    = pagination_config($this->home.'/index/', $rows, $perpage, $segment);
		$document = $this->return_lend_model->get_list($filter, $perpage, $this->uri->segment($segment));

    if(!empty($document))
    {
      foreach($document as $rs)
      {
        $rs->qty = $this->return_lend_model->get_sum_qty($rs->code);
        $rs->amount = $this->return_lend_model->get_sum_amount($rs->code);
      }
    }

    $filter['docs'] = $document;
		$this->pagination->initialize($init);
    $this->load->view('inventory/return_lend/return_lend_list', $filter);
  }



  public function add_new()
  {
    $ds['new_code'] = $this->get_new_code();
    $this->load->view('inventory/return_lend/return_lend_add', $ds);
  }


  public function add()
  {
    $sc = TRUE;
    $ex = 1;
    if($this->input->post('header') && $this->input->post('details'))
    {
      $this->load->model('inventory/lend_model');
      $this->load->model('inventory/movement_model');
			$this->load->model('masters/warehouse_model');

      //--- retrive data form
      $header = json_decode($this->input->post('header'));
      $details = json_decode($this->input->post('details'));
      $date_add = db_date($header->date_add, TRUE);

      if(empty($header) OR empty($details))
      {
        $sc = FALSE;
        $this->error = empty($header) ? "Missing Header Data" : "No item found";
      }
      else
      {
        $lend = $this->lend_model->get($header->lendCode);
        $zone = $this->zone_model->get($header->zone_code); //--- โซนปลายทาง

        if(empty($lend))
        {
          $sc = FALSE;
          $this->error = "เลขที่เอกสารยืมสินค้าไม่ถูกต้อง";
        }

        if(empty($zone))
        {
          $sc = FALSE;
          $this->error = "โซนรับสินค้าไม่ถูกต้อง";
        }
      }


      if( $sc === TRUE)
      {
        $from_warehouse = $this->zone_model->get_warehouse_code($lend->zone_code);
        $wh = $this->warehouse_model->get($zone->warehouse_code); //--- คลังปลายทาง
        $must_accept = empty($zone->user_id) ? 0 : 1;

        $isManual = getConfig('MANUAL_DOC_CODE');

        if( ! empty($header->code))
        {
          $code = trim($header->code);
        }
        else
        {
          $code = $this->get_new_code($date_add);
        }

        $is_wms = $wh->is_wms == 1 ? 1 : 0;

        $arr = array(
          'code' => $code,
          'bookcode' => getConfig('BOOK_CODE_RETURN_LEND'),
          'lend_code' => $header->lendCode,
          'empID' => $header->empID,
          'empName' => $header->empName,
          'from_warehouse' => $from_warehouse, //--- warehouse ต้นทาง ดึงจากเอกสารยืม
          'from_zone' => $lend->zone_code, //--- zone ต้นทาง ดึงจากเอกสารยืม
          'to_warehouse' => $zone->warehouse_code,
          'to_zone' => $zone->code,
          'date_add' => $date_add,
          'user' => $this->_user->uname,
          'remark' => $header->remark,
          'must_accept' => $must_accept,
          'is_wms' => $is_wms,
          'status' => $must_accept == 1 ? 4 : ($this->isAPI && $is_wms == 1 ? 3 : 1) //--- ถ้าต้องรับเข้าที่ wms ให้ set สถานะเป็น 3
        );

        //--- start transection;
        $this->db->trans_begin();

        if($this->return_lend_model->add($arr))
        {
          foreach($details as $row)
          {
            if($sc === FALSE) { break; }

            if($row->qty > 0)
            {
              $item = $this->products_model->get($row->product_code);

              if( ! empty($item))
              {
                $amount = $row->qty * $item->price;

                $ds = array(
                  'return_code' => $code,
                  'lend_code' => $header->lendCode,
                  'product_code' => $item->code,
                  'product_name' => $item->name,
                  'qty' => $row->qty,
                  'receive_qty' => ($this->isAPI === TRUE && $is_wms == 1) ? 0 : $row->qty,
                  'price' => $item->price,
                  'amount' => $amount,
                  'vat_amount' => get_vat_amount($amount)
                );

                if(! $this->return_lend_model->add_detail($ds))
                {
                  $sc = FALSE;
                  $this->error = "เพิ่มรายการไม่สำเร็จ : {$item->code}";
                }
                else
                {
                  if($must_accept == 0)
                  {
                    if($this->isAPI === FALSE OR $is_wms == 0)
                    {
                      //--- insert Movement out
                      $arr = array(
                        'reference' => $code,
                        'warehouse_code' => $lend->warehouse_code,
                        'zone_code' => $lend->zone_code,
                        'product_code' => $item->code,
                        'move_in' => 0,
                        'move_out' => $row->qty,
                        'date_add' => db_date($this->input->post('date_add'), TRUE)
                      );

                      $this->movement_model->add($arr);

                      //--- insert Movement in
                      $arr = array(
                        'reference' => $code,
                        'warehouse_code' => $zone->warehouse_code,
                        'zone_code' => $zone->code,
                        'product_code' => $item->code,
                        'move_in' => $row->qty,
                        'move_out' => 0,
                        'date_add' => db_date($this->input->post('date_add'), TRUE)
                      );

                      $this->movement_model->add($arr);

                      if( ! $this->return_lend_model->update_receive($header->lendCode, $item->code, $row->qty))
                      {
                        $sc = FALSE;
                        $this->error = "Update ยอดรับไม่สำเร็จ {$item->code}";
                      }
                    }
                  } //--- if must_accept
                }
              }
              else
              {
                $sc = FALSE;
                $this->error = "Invalid Item Code : {$row->product_code}";
              }
            }
          } //--- end foreach

          if($sc === TRUE && ($must_accept == 0 OR $this->isAPI === FALSE OR $is_wms == 0))
          {
            $arr = array(
            'shipped_date' => now()
            );

            $this->return_lend_model->update($code, $arr);
          }
        }
        else
        {
          $sc = FALSE;
          $this->error = "เพิ่มเอกสารไม่สำเร็จ";
        }

        if($sc === FALSE)
        {
          $this->db->trans_rollback();
        }
        else
        {
          $this->db->trans_commit();
        }

        if($sc === TRUE)
        {
          if($must_accept == 0)
          {
            if($this->isAPI === TRUE && $is_wms == 1)
            {
              //--- send to wms
              $this->wms = $this->load->database('wms', TRUE);
              $this->load->library('wms_receive_api');

              $doc = $this->return_lend_model->get($code);
              $details = $this->return_lend_model->get_details($code);

              if( ! $this->wms_receive_api->export_return_lend($doc, $details))
              {
                $ex = 0;
                $this->error = "บันทึกข้อมูลสำเร็จ แต่ส่งข้อมูลไป WMS ไม่สำเร็จ";
                $arr = array(
                  'wms_export' => 3,
                  'wms_export_error' => $this->wms_receive_api->error
                );

                $this->return_lend_model->update($code, $arr);
              }
              else
              {
                $arr = array(
                  'wms_export' => 1,
                  'wms_export_error' => NULL
                );

                $this->return_lend_model->update($code, $arr);
              }
            }
            else
            {
              if( ! $this->export_return_lend($code))
              {
                $arr = array(
                  'is_export' => 3,
                  'export_error' => $this->error
                );

                $this->return_lend_model->update($code, $arr);
                $ex = 0;
                $this->error = "บันทึกข้อมูลสำเร็จ แต่ส่งข้อมูลไป SAP ไม่สำเร็จ";
              }
              else
              {
                $arr = array(
                  'is_export' => 1,
                  'export_error' => NULL
                );

                $this->return_lend_model->update($code, $arr);
              }
            }
          }
        }
      }
    }
    else
    {
      $sc = FALSE;
      $this->error = "Missing form data";
    }

    $arr = array(
      'status' => $sc === TRUE ? ($ex == 1 ? 'success' : 'warning') : 'failed',
      'message' => $this->error,
      "code" => $sc === TRUE ? $code : ""
    );

    echo json_encode($arr);
  }


  public function accept_confirm()
  {
    $this->load->model('inventory/lend_model');
    $this->load->model('inventory/movement_model');

    $sc = TRUE;
    $ex = 1;

    $code = $this->input->post('code');
    $remark = $this->input->post('accept_remark');

    if( ! empty($code))
    {
      $doc = $this->return_lend_model->get($code);

      if( ! empty($doc))
      {
        if( $doc->status == 4)
        {
          $arr = array(
            'status' => $doc->is_wms == 1 ? 3 : 1,
            'is_accept' => 1,
            'accept_by' => $this->_user->uname,
            'accept_on' => now(),
            'accept_remark' => $remark
          );

          $this->db->trans_begin();

          if($this->return_lend_model->update($code, $arr))
          {
            $details = $this->return_lend_model->get_details($code);

            if( ! empty($details))
            {
              if($this->isAPI == FALSE OR $doc->is_wms == 0)
              {
                foreach($details as $rs)
                {
                  if($sc === FALSE) { break; }

                  if($rs->receive_qty > 0)
                  {
                    //--- move out
                    $arr = array(
                      'reference' => $doc->code,
                      'warehouse_code' => $doc->from_warehouse,
                      'zone_code' => $doc->from_zone,
                      'product_code' => $rs->product_code,
                      'move_in' => 0,
                      'move_out' => $rs->receive_qty,
                      'date_add' => $doc->date_add
                    );

                    if( ! $this->movement_model->add($arr))
                    {
                      $sc = FALSE;
                      $this->error = "Insert Movement (OUT) Failed";
                    }

                    //-- move in
                    if($sc === TRUE)
                    {
                      $arr = array(
                        'reference' => $doc->code,
                        'warehouse_code' => $doc->to_warehouse,
                        'zone_code' => $doc->to_zone,
                        'product_code' => $rs->product_code,
                        'move_in' => $rs->receive_qty,
                        'move_out' => 0,
                        'date_add' => $doc->date_add
                      );

                      if( ! $this->movement_model->add($arr))
                      {
                        $sc = FALSE;
                        $this->error = "Insert Movement (IN) Failed";
                      }
                    }

                    if( $sc === TRUE)
                    {
                      if( ! $this->return_lend_model->update_receive($doc->lend_code, $rs->product_code, $rs->receive_qty))
                      {
                        $sc = FALSE;
                        $this->error = "Update ยอดรับไม่สำเร็จ {$rs->product_code}";
                      }
                    }
                  } //-- if receive qty > 0
                } //--- foreach
              } //-- if is_wms
            }
          }
          else
          {
            $sc = FALSE;
            $this->error = "Update Status Failed";
          }

          if($sc === TRUE)
          {
            $this->db->trans_commit();
          }
          else
          {
            $this->db->trans_rollback();
          }

          //--- Interface data
          if($sc === TRUE)
          {
            if($this->isAPI === TRUE && $doc->is_wms == 1)
            {
              //--- send to wms
              $this->wms = $this->load->database('wms', TRUE);
              $this->load->library('wms_receive_api');

              $doc = $this->return_lend_model->get($code);
              $details = $this->return_lend_model->get_details($code);

              if( ! $this->wms_receive_api->export_return_lend($doc, $details))
              {
                $ex = 0;
                $this->error = "บันทึกข้อมูลสำเร็จ แต่ส่งข้อมูลไป WMS ไม่สำเร็จ";
                $arr = array(
                  'wms_export' => 3,
                  'wms_export_error' => $this->wms_receive_api->error
                );

                $this->return_lend_model->update($code, $arr);
              }
              else
              {
                $arr = array(
                  'wms_export' => 1,
                  'wms_export_error' => NULL
                );

                $this->return_lend_model->update($code, $arr);
              }
            }
            else
            {
              $this->return_lend_model->update($code, array('shipped_date' => now()));

              if( ! $this->export_return_lend($code))
              {
                $arr = array(
                  'is_export' => 3,
                  'export_error' => $this->error
                );

                $this->return_lend_model->update($code, $arr);
                $ex = 0;
                $this->error = "บันทึกข้อมูลสำเร็จ แต่ส่งข้อมูลไป SAP ไม่สำเร็จ";
              }
              else
              {
                $arr = array(
                  'is_export' => 1,
                  'export_error' => NULL
                );

                $this->return_lend_model->update($code, $arr);
              }
            }
          }
        }
        else
        {
          $sc = FALSE;
          $this->error = "Invalid Document Status";
        }
      }
      else
      {
        $sc = FALSE;
        $this->error = "Invalid Document Number";
      }
    }
    else
    {
      $sc = FALSE;
      $this->error = "Missing required parameter";
    }

    $arr = array(
      'status' => $sc === TRUE ? ($ex == 1 ? 'success' : 'warning') : 'failed',
      'message' => $this->error
    );

    echo json_encode($arr);
  }


	public function send_to_wms($code)
	{
		$sc = TRUE;
		$doc = $this->return_lend_model->get($code);
		if(!empty($doc))
		{
			if($doc->status == 3)
			{
				$details = $this->return_lend_model->get_details($code);

				if(!empty($details))
				{
					$this->wms = $this->load->database('wms', TRUE);
					$this->load->library('wms_receive_api');

					$rs = $this->wms_receive_api->export_return_lend($doc, $details);

					if(! $rs)
					{
						$sc = FALSE;
						$this->error = "ส่งข้อมูลไป WMS ไม่สำเร็จ <br/>({$this->wms_receive_api->error})";

            if($doc->wms_export != 1)
            {
              $arr = array(
                'wms_export' => 3,
                'wms_export_error' => $this->wms_receive_api->error
              );

              $this->return_lend_model->update($code, $arr);
            }
					}
          else
          {
            $arr = array(
              'wms_export' => 1,
              'wms_export_error' => NULL
            );

            $this->return_lend_model->update($code, $arr);
          }
				}
				else
				{
					$sc = FALSE;
					$this->error = "Return items not found";
				}
			}
			else
			{
				$sc = FALSE;
				$this->error = "Invalid document status";
			}
		}
		else
		{
			$sc = FALSE;
			$this->error = "Invalid document code";
		}

		echo $sc === TRUE ? 'success' : $this->error;
	}



  public function edit($code)
  {
    $doc = $this->return_lend_model->get($code);

    if(!empty($doc))
    {
      $doc->zone_name = $this->zone_model->get_name($doc->to_zone);
      $doc->empName = $this->employee_model->get_name($doc->empID);
    }

    $details = $this->return_lend_model->get_details($code);


    $ds['doc'] = $doc;
    $ds['details'] = $details;

    $this->load->view('inventory/return_lend/return_lend_edit', $ds);
  }


  public function cancle_return()
  {
    $sc = TRUE;
    $code = $this->input->post('return_code');
    $reason = $this->input->post('reason');

    if( ! empty($code))
    {
      $doc = $this->return_lend_model->get($code);

      if( ! empty($doc))
      {
        //--- if document saved
        if($doc->status == 1 OR $doc->status == 3)
        {
          $this->load->model('inventory/movement_model');
          $this->load->model('inventory/lend_model');

          if($doc->status == 3 && $this->_SuperAdmin === FALSE)
          {
            $sc = FALSE;
            $this->error = "สินค้าอยู่ระหว่างการรับเข้า ไม่สามารถยกเลิกได้";
          }


          if($sc === TRUE)
          {
            //--- check sap doc
            $sap = $this->return_lend_model->get_sap_doc_num($code);

            if( empty($sap))
            {
              $middle = $this->return_lend_model->get_middle_transfer_doc($code);

              if( ! empty($middle))
              {
                $this->mc->trans_begin();

                foreach($middle as $md)
                {
                  if($sc === FALSE)
                  {
                    break;
                  }

                  if( ! $this->return_lend_model->drop_middle_exits_data($md->DocEntry))
                  {
                    $sc = FALSE;
                    $this->error = "Drop Temp Data Failed";
                  }
                }

                if($sc === TRUE)
                {
                  $this->mc->trans_commit();
                }
                else
                {
                  $this->mc->trans_rollback();
                }
              }

              if($sc === TRUE)
              {
                //--- start transection
                $this->db->trans_begin();

                //--- 1 remove movement
                if( ! $this->movement_model->drop_movement($code) )
                {
                  $sc = FALSE;
                  $this->error = "ลบ movement ไม่สำเร็จ";
                }

                //--- 2 update order_lend_detail
                if($sc === TRUE && $doc->status == 1)
                {

                  $details = $this->return_lend_model->get_lend_details($code);

                  if( ! empty($details))
                  {
                    foreach($details as $rs)
                    {

                      //--- exit loop if any error
                      if($sc === FALSE)
                      {
                        break;
                      }

                      $qty = $rs->receive_qty * -1;  //--- convert to negative for add in function

                      if( ! $this->return_lend_model->update_receive($rs->lend_code, $rs->product_code, $qty))
                      {
                        $sc = FALSE;
                        $this->error = "ปรับปรุง ยอดรับ {$rs->product_code} ไม่สำเร็จ";
                      }
                    } //-- end foreach
                  } //--- end if !empty $details
                } //--- end if $sc

                //--- 3. change lend_details status to 2 (cancle)
                if($sc === TRUE)
                {
                  if( ! $this->return_lend_model->change_details_status($code, 2))
                  {
                    $sc = FALSE;
                    $this->error = "เปลี่ยนสถานะรายการไม่สำเร็จ";
                  }
                }

                //--- 4. change return_lend document to 0 (not save)
                if($sc === TRUE)
                {
                  $arr = array(
                    'inv_code' => NULL,
                    'status' => 2,
                    'cancle_reason' => $reason,
                    'cancle_user' => $this->_user->uname
                  );

                  if( ! $this->return_lend_model->update($code, $arr))
                  {
                    $sc = FALSE;
                    $this->error = "เปลี่ยนสถานะเอกสารไม่สำเร็จ";
                  }
                }

                //--- commit or rollback transection
                if($sc === TRUE)
                {
                  $this->db->trans_commit();
                }
                else
                {
                  $this->db->trans_rollback();
                }

              } //-- if $sc === TRUE
            }
            else
            {
              $sc = FALSE;
              $this->error = "กรุณายกเลิกเอกสาร Inventory Transfer บน SAP ก่อน (เมื่อยกเลิกแล้วต้องแก้ไขเลข RN โดยเติม -X ต่อท้าย)";
            }
          }
        }
        else if($doc->status == 0)  //--- if not save
        {
          //--- just change status
          $this->db->trans_begin();

          if($sc === TRUE)
          {
            //--- change lend_details status to 2 (cancle)
            if( ! $this->return_lend_model->change_details_status($code, 2))
            {
              $sc = FALSE;
              $this->error = "เปลี่ยนสถานะรายการไม่สำเร็จ";
            }
          }

          //--- change return_lend document to 2 (cancle)
          if($sc === TRUE)
          {
            $arr = array(
              'status' => 2,
              'cancle_reason' => $reason,
              'cancle_user' => $this->_user->uname
            );

            if( ! $this->return_lend_model->update($code, $arr))
            {
              $sc = FALSE;
              $this->error = "เปลี่ยนสถานะเอกสารไม่สำเร็จ";
            }
          }

          //--- commit or rollback transection
          if($sc === TRUE)
          {
            $this->db->trans_commit();
          }
          else
          {
            $this->db->trans_rollback();
          }
        }
      }
      else
      {
        $sc = FALSE;
        $this->error = "ไม่พบเลขที่เอกสาร";
      }
    }
    else
    {
      $sc = FALSE;
      $this->error = "Missing required parameter";
    }

    echo $sc === TRUE ? 'success' : $this->error;
  }




  public function view_detail($code)
  {
    $this->load->model('inventory/lend_model');
    $doc = $this->return_lend_model->get($code);

    $details = $this->lend_model->get_backlogs_list($doc->lend_code);

    if(!empty($details))
    {
      foreach($details as $rs)
      {
        $Qtys =  $this->return_lend_model->get_return_qty($doc->code, $rs->product_code);
        $rs->return_qty = empty($Qtys) ? 0 : $Qtys->qty;
        $rs->receive_qty = empty($Qtys) ? 0 : $Qtys->receive_qty;
      }
    }

    $data['doc'] = $doc;
    $data['details'] = $details;
    $this->load->view('inventory/return_lend/return_lend_view_detail', $data);
  }




  public function get_lend_details($code)
  {
    $sc = TRUE;
    $this->load->model('inventory/lend_model');
    $doc = $this->lend_model->get($code);

    if(!empty($doc))
    {
      $ds = array(
        'empID' => $doc->empID,
        'empName' => $doc->empName
      );

      $details = $this->return_lend_model->get_backlogs($code);

      $rows = array();

      if(!empty($details))
      {
        $no = 1;
        $totalLend = 0;
        $totalReceived = 0;
        $totalBacklogs = 0;

        foreach($details as $rs)
        {
          $barcode = $this->products_model->get_barcode($rs->product_code);
          $backlogs = $rs->qty - $rs->receive;

          if($backlogs > 0)
          {
            $arr = array(
              'no' => $no,
              'itemCode' => $rs->product_code,
              'barcode' => (!empty($barcode) ? $barcode : $rs->product_code), //--- หากไม่มีบาร์โค้ดให้ใช้รหัสสินค้าแทน
              'lendQty' => $rs->qty,
              'lendQtyLabel' => number($rs->qty, 2),
              'received' => $rs->receive,
              'receivedLabel' => number($rs->receive, 2),
              'backlogs' => $backlogs,
              'backlogsLabel' => number($backlogs, 2)
            );

            array_push($rows, $arr);
            $no++;
            $totalLend += $rs->qty;
            $totalReceived += $rs->receive;
            $totalBacklogs += $backlogs;
          }
        }

        $arr = array(
          'totalLend' => $totalLend,
          'totalReceived' => $totalReceived,
          'totalBacklogs' => $totalBacklogs
        );

        array_push($rows, $arr);
      }
      else
      {
        array_push($rows, array('nodata' => 'nodata'));
      }

      $ds['details'] = $rows;
    }
    else
    {
      $sc = FALSE;
      $this->error = "ไม่พบเลขที่ใบยืมสินค้า";
    }

    echo $sc === TRUE ? json_encode($ds) : $this->error;
  }



  private function export_return_lend($code)
  {
    $sc = TRUE;
    $this->load->library('export');
    if(! $this->export->export_return_lend($code))
    {
      $sc = FALSE;
      $this->error = trim($this->export->error);
    }

    return $sc;
  }
//--- end export transfer


 public function do_export($code)
 {
   $rs = $this->export_return_lend($code);
   echo $rs === TRUE ? 'success' : $this->error;
 }



  public function print_return($code)
  {
    $this->load->model('inventory/lend_model');
    $this->load->library('printer');
    $doc = $this->return_lend_model->get($code);
    $doc->from_warehouse_name = $this->warehouse_model->get_name($doc->from_warehouse);
    $doc->to_warehouse_name = $this->warehouse_model->get_name($doc->to_warehouse);
    $doc->from_zone_name = $this->zone_model->get_name($doc->from_zone);
    $doc->to_zone_name = $this->zone_model->get_name($doc->to_zone);
    $doc->empName = $this->employee_model->get_name($doc->empID);

    $details = $this->lend_model->get_backlogs_list($doc->lend_code);

    if(!empty($details))
    {
      foreach($details as $rs)
      {
        $Qtys =  $this->return_lend_model->get_return_qty($doc->code, $rs->product_code);
        $rs->return_qty = empty($Qtys) ? 0 : $Qtys->qty;
        $rs->receive_qty = empty($Qtys) ? 0 : $Qtys->receive_qty;
      }
    }

    $ds = array(
      'doc' => $doc,
      'details' => $details
    );

    $this->load->view('print/print_return_lend', $ds);
  }



  public function print_wms_return($code)
  {
    $this->load->model('inventory/lend_model');
    $this->load->library('xprinter');
    $doc = $this->return_lend_model->get($code);
    $doc->from_warehouse_name = $this->warehouse_model->get_name($doc->from_warehouse);
    $doc->to_warehouse_name = $this->warehouse_model->get_name($doc->to_warehouse);
    $doc->from_zone_name = $this->zone_model->get_name($doc->from_zone);
    $doc->to_zone_name = $this->zone_model->get_name($doc->to_zone);
    $doc->empName = $this->employee_model->get_name($doc->empID);

    $details = $this->lend_model->get_backlogs_list($doc->lend_code);

    if(!empty($details))
    {
      foreach($details as $rs)
      {
        $Qtys =  $this->return_lend_model->get_return_qty($doc->code, $rs->product_code);
        $rs->return_qty = empty($Qtys) ? 0 : $Qtys->qty;
        $rs->receive_qty = empty($Qtys) ? 0 : $Qtys->receive_qty;
      }
    }

    $ds = array(
      'order' => $doc,
      'details' => $details
    );

    $this->load->view('print/print_wms_return_lend', $ds);
  }



  public function get_new_code($date = '')
  {
    $date = $date == '' ? date('Y-m-d') : $date;
    $Y = date('y', strtotime($date));
    $M = date('m', strtotime($date));
    $prefix = getConfig('PREFIX_RETURN_LEND');
    $run_digit = getConfig('RUN_DIGIT_RETURN_LEND');
    $pre = $prefix .'-'.$Y.$M;
    $code = $this->return_lend_model->get_max_code($pre);
    if(! is_null($code))
    {
      $run_no = mb_substr($code, ($run_digit*-1), NULL, 'UTF-8') + 1;
      $new_code = $prefix . '-' . $Y . $M . sprintf('%0'.$run_digit.'d', $run_no);
    }
    else
    {
      $new_code = $prefix . '-' . $Y . $M . sprintf('%0'.$run_digit.'d', '001');
    }

    return $new_code;
  }



  public function is_exists($code, $old_code = NULL)
  {
    $exists = $this->return_lend_model->is_exists($code, $old_code);
    if($exists)
    {
      echo 'เลขที่เอกสารซ้ำ';
    }
    else
    {
      echo 'not_exists';
    }
  }



  public function clear_filter()
  {
    $filter = array(
      'rl_code',
      'lend_code',
      'empName',
      'rl_from_date',
      'rl_to_date',
      'rl_status',
      'rl_must_accept',
      'rl_zone',
      'rl_sap'
    );

    clear_filter($filter);
  }


} //--- end class
?>
