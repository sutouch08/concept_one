<?php
class Sales_order extends PS_Controller
{
  public $menu_code = 'SOODSR';
	public $menu_group_code = 'SO';
  public $menu_sub_group_code = 'ORDER';
	public $title = 'ใบสั่งงาน';
  public $img_folder = "sales_order";
	public $segment = 4;

  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'orders/sales_order';
    $this->load->model('orders/sales_order_model');
    $this->load->model('orders/sales_order_state_model');
    $this->load->model('masters/customers_model');
    $this->load->model('address/customer_address_model');
    $this->load->model('masters/products_model');
    $this->load->model('stock/stock_model');

    $this->load->helper('sales_order_state');
    $this->load->helper('sales_order');
    $this->load->helper('saleman');
    $this->load->helper('warehouse');
    $this->load->helper('image');
    $this->load->helper('payment_method');
    $this->load->helper('channels');
    $this->load->helper('sender');
  }

  public function index()
  {
    $filter = array(
      'code' => get_filter('code', 'so_code', ''),
      'ref_code' => get_filter('ref_code', 'so_ref_code', ''),
      'bill_code' => get_filter('bill_code', 'so_bill_code', ''),
      'customer_ref' => get_filter('customer_ref', 'so_customer_ref', ''),
      'job_title' => get_filter('job_title', 'so_job_title', ''),
      'job_type' => get_filter('job_type', 'so_job_type', 'all'),
      'phone' => get_filter('phone', 'so_phone', ''),
      'status' => get_filter('status', 'so_status', 'all'),
      'from_date' => get_filter('from_date', 'so_from_date', ''),
      'to_date' => get_filter('to_date', 'so_to_date', ''),
      'due_from_date' => get_filter('due_from_date', 'so_due_from_date', ''),
      'due_to_date' => get_filter('due_to_date', 'so_due_to_date', ''),
      'user' => get_filter('user', 'so_user', 'all'),
      'onlyMe' => get_filter('onlyMe', 'onlyMe', NULL),
      'state' => get_filter('state', 'so_state', 'all')
    );

    if($this->input->post('search'))
    {
      redirect($this->home);
    }
    else
    {
      //--- แสดงผลกี่รายการต่อหน้า
  		$perpage = get_rows();

  		$rows = $this->sales_order_model->count_rows($filter);
  		//--- ส่งตัวแปรเข้าไป 4 ตัว base_url ,  total_row , perpage = 20, segment = 3
  		$init	= pagination_config($this->home.'/index/', $rows, $perpage, $this->segment);

      $filter['orders'] = $this->sales_order_model->get_list($filter, $perpage, $this->uri->segment($this->segment));
      $filter['btnOnlyMe'] = empty($filter['onlyMe']) ? '' : 'btn-info';

      $this->pagination->initialize($init);

      $this->load->view('sales_order/sales_order_list', $filter);
    }
  }


  public function add_new()
  {
    $arr = array(
      'code' => NULL,
      'customer_code' => NULL,
      'customer_name' => NULL,
      'isCompany' => 0,
      'tax_id' => NULL,
      'branch_code' => NULL,
      'branch_name' => NULL,
      'address' => NULL,
      'sub_district' => NULL,
      'district' => NULL,
      'province' => NULL,
      'postcode' => NULL,
      'date_add' => date('Y-m-d'),
      'due_date' => date('Y-m-d', strtotime("+1 days")),
      'status' => 'P',
      'vat_type' => '',
      'vat_rate' => 7,
      'VatSum' => 0.00,
      'TotalBfDisc' => 0.00,
      'DiscPrcnt' =>  0.00,
      'DiscAmount' => 0.00,
      'DocTotal' => 0.00,
      'TotalBalance' => 0.00,
      'TaxStatus' => '',
      'prefix' => "",
      'customer_ref' => NULL,
      'customer_address' => NULL,
      'phone' => NULL,
      'job_type' => NULL,
      'job_title' => NULL,
      'channels_code' => NULL,
      'DepAmount' => 0.00,
      'id_sender' => NULL,
      'ship_code' => NULL,
      'user' => $this->_user->uname,
      'sale_id' => empty($this->_user->sale_id) ? getConfig('DEFAULT_SALES_ID') : $this->_user->sale_id,
      'remark' => NULL,
      'whsCode' => getConfig('DEFAULT_WAREHOUSE'),
      'ref_code' => NULL,
      'WhtPrcnt' => 0.00,
      'WhtAmount' => 0.00,
      'is_term' => "",
      'design' => "",
      'isLinked' => 0
    );

    $ds = array(
      'mode' => 'Add',
      'doc' => (object) $arr,
      'image' => no_image_path(),
      'no_image_path' => no_image_path(),
      'details' => NULL,
      'count_rows' => 0
    );

    $this->load->view('sales_order/sales_order_add', $ds);
  }


  public function get_customer_bill_to_address()
  {
    $sc = TRUE;
    $code = $this->input->get('code');

    if( ! empty($code))
    {
      $customer = $this->customers_model->get($code);

      if( ! empty($customer))
      {
        $addr = $this->customer_address_model->get_bill_to_address($customer->code);

        if( ! empty($addr))
        {
          $no = 1;
          foreach($addr as $adr)
          {
            $adr->no = $no;
            $adr->name = $customer->name;
            $no++;
          }
        }
        else
        {
          $sc = FALSE;
          $this->error = "No address found";
        }
      }
      else
      {
        $sc = FALSE;
        $this->error = "Invalid customer code";
      }
    }
    else
    {
      $sc = FALSE;
      $this->error = "Missing required parameter";
    }

    $arr = array(
      'status' => $sc === TRUE ? 'success' : 'failed',
      'message' => $sc === TRUE ? 'success' : $this->error,
      'address' => $sc === TRUE ? $addr : NULL
    );

    echo json_encode($arr);
  }

  public function add()
  {
    $sc = TRUE;
    $data = json_decode($this->input->post('data'));

    $header = $data->header;
    $items = $data->items;
    $hasImgge = FALSE;


    if( ! empty($header))
    {
      $date_add = db_date($header->date_add);
      $due_date = db_date($header->due_date);

      $code = $this->get_new_code($date_add);

      $arr = array(
        'code' => $code,
        'customer_code' => $header->customer_code,
        'customer_name' => $header->customer_name,
        'branch_code' => get_null($header->branch_code),
        'branch_name' => get_null($header->branch_name),
        'tax_id' => get_null($header->tax_id),
        'date_add' => $date_add,
        'due_date' => $due_date,
        'status' => $header->isDraft == 1 ? 'P' : 'O',
        'TaxStatus' => $header->TaxStatus,
        'vat_type' => $header->vat_type,
        'vat_rate' => 0.00,
        'vatSum' => $header->vatSum,
        'TotalBfDisc' => $header->totalAmount,
        'DiscPrcnt' =>  $header->discPrcnt,
        'DiscAmount' => $header->discAmount,
        'DocTotal' => $header->docTotal,
        'TotalBalance' => $header->docTotal,
        'customer_ref' => trim($header->customer_ref),
        'customer_address' => trim($header->customer_address),
        'address' => get_null($header->address),
        'sub_district' => get_null($header->sub_district),
        'district' => get_null($header->district),
        'province' => get_null($header->province),
        'postcode' => get_null($header->postcode),
        'phone' => $header->phone,
        'job_type' => $header->job_type,
        'job_title' => $header->job_title,
        'channels_code' => $header->channels_code,
        'DepAmount' => $header->depAmount > 0 ? $header->depAmount : $header->docTotal,
        'isWht' => $header->vat_type == 'N' ? 0 : ($header->whtPrcnt > 0 ? 1 : 0),
        'WhtPrcnt' => $header->vat_type == 'N' ? 0 : $header->whtPrcnt,
        'WhtAmount' => $header->vat_type == 'N' ? 0 : $header->whtAmount,
        'user' => $this->_user->uname,
        'sale_id' => $header->sale_id,
        'is_term' => $header->is_term,
        'remark' => get_null($header->remark),
        'whsCode' => $header->whsCode,
        'design' => $header->design
      );

      $this->db->trans_begin();

      $id = $this->sales_order_model->add($arr);

      if( ! $id)
      {
        $sc = FALSE;
        $this->error = "เพิ่มเอกสารไม่สำเร็จ";
      }

      //----- Save images
      if($sc === TRUE && ! empty($header->img))
      {
        $path = $this->config->item('image_path')."sales_order/{$code}.jpg";

        if(createImage($header->img, $path) === FALSE)
        {
          $sc = FALSE;
          set_error(0, "Create Image Failed");
        }
        else
        {
          $arr = array('image_path' => $path);

          $this->sales_order_model->update($code, $arr);
        }
      }

      if($sc === TRUE)
      {
        if( ! empty($items))
        {
          $no = 1;

          $vat_type = $header->vat_type == 'E' ? 'E' : 'I';

          foreach($items as $rs)
          {
            if($sc === FALSE)
            {
              break;
            }

            $avgBillDiscAmount = $header->totalAmount > 0 ? $header->discAmount/$header->totalAmount : 0;
            $sumBillDiscAmount = $rs->totalAmount * $avgBillDiscAmount;
            $vatSum = get_vat_amount($rs->totalAmount - $sumBillDiscAmount, $rs->vat_rate, $vat_type);

            $arr = array(
              'id_order' => $id,
              'order_code' => $code,
              'lineNum' => $no,
              'product_code' => $rs->product_code,
              'product_name' => $rs->product_name,
              'style_code' => $rs->style_code,
              'unit_code' => $rs->unit_code,
              'cost' => $rs->cost,
              'price' => $rs->price,
              'sell_price' => $rs->price - $rs->discAmount,
              'qty' => $rs->qty,
              'OpenQty' => $rs->qty,
              'vat_type' => $header->vat_type,
              'vat_code' => $rs->vat_code,
              'vat_rate' => $rs->vat_rate,
              'vat_amount' => $vatSum,
              'discount_label' => $rs->discLabel,
              'discount_amount' => $rs->discAmount,
              'total_amount' => $rs->totalAmount,
              'avgBillDiscAmount' => $avgBillDiscAmount,
              'sumBillDiscAmount' => $sumBillDiscAmount,
              'user' => $this->_user->uname,
              'is_count' => $rs->is_count
            );

            if( ! $this->sales_order_model->add_detail($arr))
            {
              $sc = FALSE;
              $this->error = "เพิ่มรายการไม่สำเร็จ : {$rs->product_code} @ Line Number {$no}";
            }

            $no++;
          } //--- end foreach
        }
      }

      if($sc === TRUE)
      {
        $arr = array(
          'code' => $code,
          'action' => 'add',
          'uname' => $this->_user->uname,
          'name' => $this->_user->name,
          'date_upd' => now()
        );

        $this->sales_order_model->add_logs($arr);

        $this->db->trans_commit();
      }
      else
      {
        $this->db->trans_rollback();
      }
    }
    else
    {
      $sc = FALSE;
      $this->error = "Missing header data";
    }

    $arr = array(
      'status' => $sc === TRUE ? 'success' : 'failed',
      'message' => $sc === TRUE ? 'success' : $this->error,
      'code' => $sc === TRUE ? $code : NULL
    );

    echo json_encode($arr);
  }


  public function save_image()
  {
    $sc = TRUE;
    $code = $this->input->post('code');
    $img = $this->input->post('imageData'); //---- base64_data

    $doc = $this->sales_order_model->get($code);

    if( ! empty($doc))
    {
      $path = $this->config->item('image_path')."sales_order/{$code}.jpg";

      if(createImage($img, $path) === FALSE)
      {
        $sc = FALSE;
        $this->error = "Create Image Failed";
      }
      else
      {
        $arr = array('image_path' => $path);

        $this->sales_order_model->update($code, $arr);
      }
    }
    else
    {
      $sc = FALSE;
      $this->error = "Invalid document code";
    }

    echo $sc === TRUE ? 'success' : $this->error;
  }



  public function delete_image()
  {
    $sc = TRUE;
    $code = $this->input->post('code');

    $folder = "sales_order";

    if( ! delete_image($code, $folder))
    {
      $sc = FALSE;
      $this->error = "Delete image failed";
    }
    else
    {
      $arr = array('image_path' => NULL);

      $this->sales_order_model->update($code, $arr);
    }

    echo $sc === TRUE ? 'success' : $this->error;
  }


  public function get_details()
  {
    $sc = TRUE;
    $code = $this->input->get('code');

    if( ! empty($code))
    {
      $so = $this->sales_order_model->get($code);

      if( ! empty($so))
      {
        $details = $this->sales_order_model->get_open_details($code);

        if( ! empty($details))
        {
          $no = 1;
          foreach($details as $rs)
          {
            $commit = $this->sales_order_model->get_commit_qty($rs->id);

            $commit_qty = 0;


            if( ! empty($commit))
            {
              foreach($commit as $cmt)
              {
                $commit_qty += $cmt->billed_qty > 0 ? $cmt->billed_qty : $cmt->qty;
              }
            }

            $available = $rs->OpenQty - $commit_qty;

            $rs->no = $no;
            $rs->available = $available > 0 ? $available : 0;
            $rs->commit_qty = $commit_qty;
            $rs->disabled = $available > 0 ? '' : 'disabled';

            $rs->qty_label = number($rs->qty, 2);
            $rs->sell_price_label = number($rs->sell_price, 2);
            $rs->open_label = number($rs->OpenQty, 2);
            $rs->commit_label = number($rs->commit_qty, 2);
            $rs->available_label = number($rs->available, 2);

            $no++;
          }
        }
      }
      else
      {
        $sc = FALSE;
        $this->error = "Invalid Document No";
      }
    }
    else
    {
      $sc = FALSE;
      $this->error = "Missing require parameter";
    }

    $arr = array(
      'status' => $sc === TRUE ? 'success' : 'failed',
      'message' => $sc === TRUE ? 'success' : $this->error,
      'details' => $sc === TRUE ? $details : NULL
    );

    echo json_encode($arr);
  }

  public function edit($code)
  {
    $doc = $this->sales_order_model->get($code);

    if( ! empty($doc))
    {
      $details = $this->sales_order_model->get_details($code);
      $state = $this->sales_order_state_model->get_order_state($code);
	    $ost = array();

	    if(!empty($state))
	    {
	      foreach($state as $st)
	      {
	        $ost[] = $st;
	      }
	    }

      $doc->isLinked = 0;

      $ds = array(
        'mode' => 'Edit',
        'doc' => $doc,
        'state' => $ost,
        'image' => get_image_path($doc->code, $this->img_folder),
        'no_image_path' => no_image_path(),
        'details' => $details,
        'count_rows' => count($details)
      );

      $this->load->view('sales_order/sales_order_edit', $ds);
    }
    else
    {
      $this->page_error();
    }
  }


  public function update()
  {
    $sc = TRUE;
    $code = $this->input->post('code');
    $data = json_decode($this->input->post('data'));

    $header = $data->header;
    $items = $data->items;

    if( ! empty($header))
    {
      $date_add = db_date($header->date_add);
      $due_date = db_date($header->due_date);

      $doc = $this->sales_order_model->get($code);

      if( ! empty($doc))
      {
        if($doc->status == 'P' OR $doc->status == 'O')
        {
          $balance = $header->docTotal - $doc->paidAmount;

          $edition = empty($doc->edition) ? 1 : $doc->edition + 1;

          $arr = array(
            'customer_code' => $header->customer_code,
            'customer_name' => $header->customer_name,
            'branch_code' => get_null($header->branch_code),
            'branch_name' => get_null($header->branch_name),
            'tax_id' => get_null($header->tax_id),
            'date_add' => $date_add,
            'due_date' => $due_date,
            'status' => $header->isDraft == 1 ? 'P' : 'O',
            'TaxStatus' => $header->TaxStatus,
            'vat_type' => $header->vat_type,
            'vat_rate' => 0.00,
            'vatSum' => $header->vatSum,
            'TotalBfDisc' => $header->totalAmount,
            'DiscPrcnt' =>  $header->discPrcnt,
            'DiscAmount' => $header->discAmount,
            'DocTotal' => $header->docTotal,
            'DepAmount' => $header->depAmount > 0 ? $header->depAmount : $header->docTotal,
            'TotalBalance' => $balance,
            'customer_ref' => trim($header->customer_ref),
            'customer_address' => get_null($header->customer_address),
            'address' => get_null($header->address),
            'sub_district' => get_null($header->sub_district),
            'district' => get_null($header->district),
            'province' => get_null($header->province),
            'postcode' => get_null($header->postcode),
            'phone' => $header->phone,
            'job_type' => $header->job_type,
            'job_title' => $header->job_title,
            'channels_code' => $header->channels_code,
            'update_user' => $this->_user->uname,
            'sale_id' => $header->sale_id,
            'remark' => get_null($header->remark),
            'whsCode' => $header->whsCode,
            'is_term' => $header->is_term,
            'isWht' => $header->whtPrcnt > 0 ? 1 : 0,
            'WhtPrcnt' => $header->whtPrcnt,
            'WhtAmount' => $header->whtAmount,
            'design' => $header->design,
            'edition' => $edition,
            'edit_time' => now(),
            'edit_by' => $this->_user->uname
          );

          $this->db->trans_begin();

          if( ! $this->sales_order_model->update($code, $arr))
          {
            $sc = FALSE;
            $this->error = "Failed to update document";
          }

          //----- Save images
          if($sc === TRUE && ! empty($header->img))
          {
            $path = $this->config->item('image_path')."sales_order/{$doc->code}.jpg";

            if(createImage($header->img, $path) === FALSE)
            {
              $sc = FALSE;
              set_error(0, "Create Image Failed");
            }
          }

          if($sc === TRUE && ! empty($items))
          {
            if($sc === TRUE)
            {
              $no = 1;
              $existsIds = []; //---- รวม id ที่มีการ update นอกจากนั้นจะทำการลบออก
              $vat_type = $header->vat_type == 'E' ? 'E' : 'I';

              foreach($items as $rs)
              {
                if($sc === FALSE)
                {
                  break;
                }

                $avgBillDiscAmount = $header->totalAmount > 0 ? $header->discAmount/$header->totalAmount : 0;
                $sumBillDiscAmount = $rs->totalAmount * $avgBillDiscAmount;
                $vatSum = get_vat_amount($rs->totalAmount - $sumBillDiscAmount, $rs->vat_rate, $vat_type);

                if($rs->id != 0 )
                {
                  if($rs->line_status == 'O')
                  {
                    $arr = array(
                      'product_code' => $rs->product_code,
                      'product_name' => $rs->product_name,
                      'style_code' => $rs->style_code,
                      'unit_code' => $rs->unit_code,
                      'cost' => $rs->cost,
                      'price' => $rs->price,
                      'sell_price' => $rs->price - $rs->discAmount,
                      'qty' => $rs->qty,
                      'OpenQty' => $rs->openQty,
                      'vat_type' => $header->vat_type,
                      'vat_code' => $rs->vat_code,
                      'vat_rate' => $rs->vat_rate,
                      'vat_amount' => $vatSum,
                      'discount_label' => $rs->discLabel,
                      'discount_amount' => $rs->discAmount,
                      'total_amount' => $rs->totalAmount,
                      'avgBillDiscAmount' => $avgBillDiscAmount,
                      'sumBillDiscAmount' => $sumBillDiscAmount,
                      'user' => $this->_user->uname,
                      'is_count' => $rs->is_count
                    );

                    if( ! $this->sales_order_model->update_detail($rs->id, $arr))
                    {
                      $sc = FALSE;
                      $this->error = "แก้ไขรายการไม่สำเร็จ : {$rs->product_code} @ Line Number {$no}";
                    }
                    else
                    {
                      $existsIds[] = $rs->id;
                    }
                  }

                  if($rs->line_status == 'C')
                  {
                    $existsIds[] = $rs->id;
                  }
                }
                else
                {
                  $arr = array(
                    'id_order' => $doc->id,
                    'order_code' => $doc->code,
                    'lineNum' => $rs->no,
                    'product_code' => $rs->product_code,
                    'product_name' => $rs->product_name,
                    'style_code' => $rs->style_code,
                    'unit_code' => $rs->unit_code,
                    'cost' => $rs->cost,
                    'price' => $rs->price,
                    'sell_price' => $rs->price - $rs->discAmount,
                    'qty' => $rs->qty,
                    'OpenQty' => $rs->openQty,
                    'vat_type' => $header->vat_type,
                    'vat_code' => $rs->vat_code,
                    'vat_rate' => $rs->vat_rate,
                    'vat_amount' => $vatSum,
                    'discount_label' => $rs->discLabel,
                    'discount_amount' => $rs->discAmount,
                    'total_amount' => $rs->totalAmount,
                    'avgBillDiscAmount' => $avgBillDiscAmount,
                    'sumBillDiscAmount' => $sumBillDiscAmount,
                    'user' => $this->_user->uname,
                    'is_count' => $rs->is_count
                  );

                  $id = $this->sales_order_model->add_detail($arr);
                  if( ! $id)
                  {
                    $sc = FALSE;
                    $this->error = "เพิ่มรายการไม่สำเร็จ : {$rs->product_code} @ Line Number {$no}";
                  }
                  else
                  {
                    $existsIds[] = $id;
                  }
                }

                $no++;
              } //--- end foreach

              //--- now delete not exists id
              if( $sc === TRUE && ! empty($existsIds))
              {
                if( ! $this->sales_order_model->drop_not_exists_id($doc->id, $existsIds))
                {
                  $sc = FALSE;
                  $this->error = "ลบรายการไม่สำเร็จ";
                }
              }
            }
          }

          if($sc === TRUE)
          {
            $arr = array(
              'code' => $doc->code,
              'action' => 'edit',
              'uname' => $this->_user->uname,
              'name' => $this->_user->name,
              'date_upd' => now()
            );

            $this->sales_order_model->add_logs($arr);

            $this->db->trans_commit();
          }
          else
          {
            $this->db->trans_rollback();
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
        $this->error = "Invalid document number";
      }
    }
    else
    {
      $sc = FALSE;
      $this->error = "Missing header data";
    }

    $arr = array(
      'status' => $sc === TRUE ? 'success' : 'failed',
      'message' => $sc === TRUE ? 'success' : $this->error
    );

    echo json_encode($arr);
  }


  public function update_ship_code()
  {
    $sc = TRUE;
    $code = $this->input->post('code');
    $ship_code = $this->input->post('ship_code');

    $arr = ['ship_code' => $ship_code];

    if( ! $this->sales_order_model->update($code, $arr))
    {
      $sc = FALSE;
      $this->error = "Failed to update tracking number";
    }

    echo $sc === TRUE ? 'success' : $this->error;
  }

  public function view_detail($code)
  {
    $this->load->model('orders/orders_model');
    $this->load->model('address/address_model');
    $this->load->model('masters/bank_model');
    $this->load->helper('bank');

    $doc = $this->sales_order_model->get($code);

    if( ! empty($doc))
    {
      $state = $this->sales_order_state_model->get_order_state($code);
	    $ost = array();
	    if(!empty($state))
	    {
	      foreach($state as $st)
	      {
	        $ost[] = $st;
	      }
	    }

      $ds = array(
        'doc' => $doc,
        'state' => $ost,
        'image' => get_image_path($doc->code, $this->img_folder),
        'details' => $this->sales_order_model->get_details($code),
        'wo' => $this->sales_order_model->get_wo_ref($code),
        'wq' => $this->sales_order_model->get_wq_ref($code),
        'bi' => $this->sales_order_model->get_bill_ref($code),
        'banks' => $this->bank_model->get_active_bank(),
        'addr' => empty($doc->customer_ref) ? $this->address_model->get_ship_to_address($doc->customer_code) : $this->address_model->get_shipping_address($doc->customer_ref),
        'no_image_path' => no_image_path(),
        'logs' => $this->sales_order_model->get_logs($doc->code)
      );

      $this->load->view('sales_order/sales_order_view_detail', $ds);
    }
    else
    {
      $this->page_error();
    }
  }


  public function print_sales_order($code)
  {
    $this->load->model('masters/slp_model');
    $doc = $this->sales_order_model->get($code);

    if( ! empty($doc))
    {
      $details = $this->sales_order_model->get_details($code);

      $this->load->library('xprinter');
      $this->load->helper('print');
      $job = [
        'A' => 'งาน ปัก',
        'B' => 'งาน รีด',
        'C' => 'งาน สกรีน',
        'D' => 'งาน ปัก + รีด',
        'E' => 'งาน ปัก + สกรีน',
        'F' => 'งาน รีด + สกรีน',
        'G' => 'งาน ปักผสมรีด',
        'H' => 'งาน GTX',
        'I' => 'งานสั่งผลิด'
      ];

      $wqText = "";
      $wqList = $this->sales_order_model->get_transform_list($doc->code);

      if( ! empty($wqList))
      {
        $i = 1;
        foreach($wqList as $wq)
        {
          $wqText .= $i === 1 ? "{$wq->order_code}" : ", {$wq->order_code}";
          $i++;
        }
      }

      $ds = array(
        'title' => "ใบสั่งขาย - {$job[$doc->job_type]}",
        'order' => $doc,
        'details' => $details,
        'sale' => $this->slp_model->get($doc->sale_id),
        'wq' => $wqText
      );

      $this->load->view('print/print_sales_order', $ds);
    }
    else
    {
      $this->error_page();
    }
  }


  public function cancle_order()
  {
    $sc = TRUE;
    $code = $this->input->post('code');
    $reason = $this->input->post('reason');

    if($this->pm->can_delete)
    {
      if( ! empty($code))
      {
        $doc = $this->sales_order_model->get($code);

        if( ! empty($doc))
        {
          if($doc->status == 'P' OR $doc->status == 'O')
          {
            //--- ตรวจสอบออเดอร์ ที่มีการเชื่อมโยงกันไว้
            $orders = $this->sales_order_model->get_order_ref($code);

            if( ! empty($orders))
            {
              $sc = FALSE;
              $this->error = "ไม่สามารถยกเลิกเอกสารได้ เนื่องจากมีรายการที่ถูกสร้างจากเอกสารนี้อยู่";
            }

            if($sc === TRUE)
            {
              $bills = $this->sales_order_model->get_bill_ref($code);

              if( ! empty($bills))
              {
                $sc = FALSE;
                $this->error = "ไม่สามารถยกเลิกเอกสารได้ เนื่องจากมีรายการที่ถูกสร้างจากเอกสารนี้อยู่";
              }
            }

            if($sc === TRUE)
            {
              $this->db->trans_begin();

              $arr = array(
                'status' => 'D',
                'cancle_user' => $this->_user->uname,
                'cancle_reason' => $reason,
                'cancle_date' => now()
              );

              if( ! $this->sales_order_model->update($code, $arr))
              {
                $sc = FALSE;
                $this->error = "Failed to update document status";
              }

              if($sc === TRUE)
              {
                $arr = array(
                  'line_status' => 'D'
                );

                if( ! $this->sales_order_model->update_details($code, $arr))
                {
                  $sc = FALSE;
                  $this->error = "Failed to update document rows";
                }
              }

              if($sc === TRUE)
              {
                $arr = array(
                  'code' => $doc->code,
                  'action' => 'cancel',
                  'uname' => $this->_user->uname,
                  'name' => $this->_user->name,
                  'date_upd' => now()
                );

                $this->sales_order_model->add_logs($arr);

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
            $this->error = "Invalid document status";
          }
        }
        else
        {
          $sc = FALSE;
          set_error('notfound');
        }
      }
      else
      {
        $sc = FALSE;
        set_error('required');
      }
    }
    else
    {
      $sc = FALSE;
      set_error('permission');
    }

    echo $sc === TRUE ? 'success' : $this->error;
  }



  public function get_item()
  {
    $sc = TRUE;
    $stock = 0;
    $code = trim($this->input->post('item_code'));

    if( ! empty($code))
    {
      $item = $this->products_model->get($code);

      if(empty($item))
      {
        $sc = FALSE;
        set_error('notfound');
      }
      else
      {
        $item->stock = $item->count_stock ? $this->stock_model->get_stock($item->code) : 100000;
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
      'item' => $sc === TRUE ? $item : NULL
    );

    echo json_encode($arr);
  }


  public function get_item_grid()
  {
    $this->load->model('masters/product_style_model');
    $this->load->library('items_grid');

		$sc = TRUE;

		$ds = array();
    //----- Attribute Grid By Clicking image
    $style = $this->product_style_model->get_with_old_code($this->input->post('style_code'));
    $whsCode = get_null($this->input->post('warehouse_code'));

    if( ! empty($style))
    {
      //--- ถ้าได้ style เดียว จะเป็น object ไม่ใช่ array
      if(! is_array($style))
      {
        if($style->active)
        {
        	$table = $this->items_grid->getProductGrid($style->code, $whsCode);
        	$tableWidth	= $this->products_model->countAttribute($style->code) == 1 ? 200 : $this->items_grid->getGridTableWidth($style->code);

					if($table == 'notfound') {
						$sc = FALSE;
						$this->error = "ไม่พบรายการสินค้า";
					}
					else
					{
            $tbs = '<table class="table table-bordered border-1" style="min-width:'.$tableWidth.'px;">';
            $tbe = '</table>';
						$ds = array(
							'status' => 'success',
							'message' => NULL,
							'table' => $tbs.$table.$tbe,
							'tableWidth' => $tableWidth + 40,
							'styleCode' => $style->code
						);
					}
        }
        else
        {
					$sc = FALSE;
          $this->error = "สินค้า Inactive";
        }

      }
      else
      {
				$sc = FALSE;
        $this->error = "รหัสซ้ำ ";

        foreach($style as $rs)
        {
          $this->error .= " : {$rs->code} : {$rs->old_code}";
        }
      }

    }
    else
    {
      $sc = FALSE;
			$this->error = "not found";
    }


		echo $sc === TRUE ? json_encode($ds) : $this->error;
  }


  public function get_pay_amount()
  {
    $sc = TRUE;
    $code = $this->input->get('code');

    $doc = $this->sales_order_model->get($code);

    if( ! empty($doc))
    {

      $ds = array(
        'pay_amount' => $doc->TotalBalance
      );
    }
    else
    {
      $sc = FALSE;
      $this->error = "Invalid Document number";
    }

    echo $sc === TRUE ? json_encode($ds) : $this->error;
  }

  public function set_address()
	{
		$sc = TRUE;
		$code = $this->input->post('code');
		$id_address = $this->input->post('id_address');

		$arr = array(
			'id_address' => $id_address
		);

		if(! $this->sales_order_model->update($code, $arr))
		{
			$sc = FALSE;
			$this->error = "Update failed";
		}

		echo $sc === TRUE ? 'success' : $this->error;
	}


  public function set_sender()
	{
		$sc = TRUE;
		$code = trim($this->input->post('code'));
		$id_sender = trim($this->input->post('id_sender'));

		$arr = array(
			'id_sender' => $id_sender
		);

		if(! $this->sales_order_model->update($code, $arr))
		{
			$sc = FALSE;
			$this->error = "Update failed";
		}

		echo $sc === TRUE ? 'success' : $this->error;
	}


  public function change_state()
  {
    $sc = TRUE;

    if($this->input->post('code'))
    {
      $code = $this->input->post('code');
      $state = $this->input->post('state');
      $doc = $this->sales_order_model->get($code);

      if(! empty($doc))
      {
        if($sc === TRUE)
        {
          $this->db->trans_begin();

          if($this->sales_order_model->update($code, ['state' => $state]))
          {
            $arr = array(
              'code' => $code,
              'state' => $state,
              'user' => $this->_user->uname
            );

            if( ! $this->sales_order_state_model->add_state($arr) )
            {
              $sc = FALSE;
              $this->error = "Add state failed";
            }
          }
          else
          {
            $sc = FALSE;
            $this->error = "เปลี่ยนสถานะไม่สำเร็จ";
          }

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
        $this->error = 'Sales order not found';
      }
    }
    else
    {
      $sc = FALSE;
      $this->error = 'Missing required parameter';
    }

    echo $sc === TRUE ? 'success' : $this->error;
  }


  public function close_order()
  {
    $sc = TRUE;

    $code = $this->input->post('code');

    if($code)
    {
      $doc = $this->sales_order_model->get($code);

      if( ! empty($doc))
      {
        if($doc->status == 'O')
        {
          $this->db->trans_begin();

          //--- close details
          $arr = array('line_status' => 'C');

          if( ! $this->sales_order_model->update_details($code, $arr))
          {
            $sc = FALSE;
            $this->error = "Close รายการไม่สำเร็จ";
          }
          else
          {
            $arr = array(
              'status' => 'C',
              'date_upd' => now(),
              'update_user' => $this->_user->uname
            );

            if( ! $this->sales_order_model->update($code, $arr))
            {
              $sc = FALSE;
              $this->error = "Close เอกสารไม่สำเร็จ";
            }
            else
            {
              $logs = array(
                'code' => $doc->code,
                'action' => 'close',
                'uname' => $this->_user->uname,
                'name' => $this->_user->name,
                'date_upd' => now()
              );

              $this->sales_order_model->add_logs($logs);
            }
          }

          if($sc === TRUE)
          {
            $this->db->trans_commit();
          }
          else
          {
            $this->db->trans_rollback();
          }
        }
        else
        {
          if($doc->status == 'D')
          {
            $sc = FALSE;
            $this->error = "เอกสารนี้ถูกยกเลิกไปแล้ว";
          }
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

    echo $sc === TRUE ? 'success' : $this->error;
  }


  public function confirm_payment()
  {
    $sc = TRUE;

    if($this->input->post('code'))
    {
      $this->load->helper('bank');
      $this->load->model('orders/order_payment_model');

      $file = isset( $_FILES['image'] ) ? $_FILES['image'] : FALSE;
      $code = $this->input->post('code');
      $date = $this->input->post('payDate');
      $h = $this->input->post('payHour');
      $m = $this->input->post('payMin');
      $dhm = $date.' '.$h.':'.$m.':00';
      $pay_date = db_date($dhm, TRUE);

      $doc = $this->sales_order_model->get($code);

      $arr = array(
        'order_code' => $code,
        'order_amount' => $this->input->post('orderAmount'),
        'pay_amount' => $this->input->post('payAmount'),
        'pay_date' => $pay_date,
        'id_account' => $this->input->post('id_account'),
        'acc_no' => $this->input->post('acc_no'),
        'user' => $this->_user->uname,
        'type' => $this->input->post('type')
      );

      //--- บันทึกรายการ
      if($this->order_payment_model->add($arr))
      {
        if($doc->state == 1)
        {
          if( $this->sales_order_model->update($code, ['state' => 2]) )  //--- แจ้งชำระเงิน
          {
            $arr = array(
              'code' => $code,
              'state' => 2,
              'user' => $this->_user->uname
            );

            $this->sales_order_state_model->add_state($arr);
          }
          else
          {
            $sc = FALSE;
            $this->error = "เปลี่ยนสถานะไม่สำเร็จ";
          }
        }
      }
      else
      {
        $sc = FALSE;
        $this->error = 'บันทึกรายการไม่สำเร็จ';
      }

      if($file !== FALSE)
      {
        $rs = $this->do_upload($file, $order_code);

        if($rs !== TRUE)
        {
          $sc = FALSE;
          $this->error = $rs;
        }
      }
    }

    echo $sc === TRUE ? 'success' : $this->error;
  }



  public function createWo()
  {
    $sc = TRUE;
    $so_code = $this->input->post('so_code');
    $details = json_decode($this->input->post('details'));

    if( ! empty($so_code) && ! empty($details))
    {
      $this->load->model('orders/orders_model');
      $this->load->model('orders/sales_order_model');
      $so = $this->sales_order_model->get($so_code);

      if( ! empty($so))
      {
        if($so->status == 'O')
        {
          if( ! empty($details))
          {
            $book_code = getConfig('BOOK_CODE_ORDER');
            $date_add = now();

            $code = $this->get_new_order_code($date_add);

            $docTotal = 0;
            $vatSum = 0;
            $discSum = 0;

            $ds = array(
              'date_add' => $date_add,
              'code' => $code,
              'role' => 'S',
              'bookcode' => $book_code,
              'BaseRef' => $so->code,
              'BaseId' => $so->id,
              'BaseType' => 'SO',
              'reference' => $so->code,
              'so_code' => $so->code,
              'customer_code' => $so->customer_code,
              'customer_name' => $so->customer_name,
              'customer_ref' => $so->customer_ref,
              'tax_id' => $so->tax_id,
              'TaxStatus' => $so->TaxStatus,
              'vat_type' => $so->vat_type,
              'branch_code' => $so->branch_code,
              'branch_name' => $so->branch_name,
              'address' => $so->address,
              'sub_district' => $so->sub_district,
              'district' => $so->district,
              'province' => $so->province,
              'postcode' => $so->postcode,
              'phone' => $so->phone,
              'channels_code' => $so->channels_code,
              'sale_code' => $so->sale_id,
              'state' => 1,
              'is_term' => $so->is_term,
              'bDiscText' => $so->DiscPrcnt,
              'bDiscAmount' => $so->DiscAmount,
              'status' => 1,
              'warehouse_code' => $so->whsCode,
              'isWht' => $so->isWht, //-- หัก ณ ที่จ่ายหรือไม่
              'WhtPrcnt' => $so->WhtPrcnt, //---- หัก ณ ที่จ่าย %
              'WhtAmount' => $so->WhtAmount, //--- หัก ณ ที่จ่าย มูลค่า
              'user' => $this->_user->uname,
    					'id_address' => $so->id_address,
    					'id_sender' => $so->id_sender,
              'remark' => $so->remark
            );

            $this->db->trans_begin();

            $id = $this->orders_model->add_order($ds);

            if($id)
            {
              //--- add details
              foreach($details as $ro)
              {
                $rs = $this->sales_order_model->get_detail($ro->id);

                if( ! empty($rs))
                {
                  if($rs->OpenQty >= $ro->qty)
                  {
                    $discount_amount = $rs->discount_amount * $ro->qty;
                    $total_amount = $ro->qty * $rs->sell_price;
                    $sumBillDiscAmount = $rs->avgBillDiscAmount * $total_amount;
                    $amountAfDisc = $total_amount - $sumBillDiscAmount;
                    $vat_amount = get_vat_amount($amountAfDisc, $rs->vat_rate, $so->vat_type);

                    $arr = array(
                      "id_order" =>  $id,
                      "order_code"	=> $code,
                      "style_code"		=> $rs->style_code,
                      "product_code"	=> $rs->product_code,
                      "product_name"	=> $rs->product_name,
                      "cost"  => $rs->cost,
                      "price"	=> $rs->price,
                      "qty"		=> $ro->qty,
                      "discount1"	=> $rs->discount_label,
                      "discount2" => 0,
                      "discount3" =>0,
                      "discount_amount" => $discount_amount,
                      "total_amount"	=> $total_amount,
                      "avgBillDiscAmount" => $rs->avgBillDiscAmount,
                      "sumBillDiscAmount" => $sumBillDiscAmount,
                      "vat_type" => $so->vat_type,
                      "vat_code" => $rs->vat_code,
                      "vat_rate" => $rs->vat_rate,
                      "vat_amount" => $vat_amount,
                      "baseCode" => $rs->order_code, //--- sales_order_code
                      "baseLine" => $rs->id, //--- sales_order_detail_id
                      "baseId" => $rs->id_order, //--- sales_order_id
                      "line_id" => $rs->id,
                      "is_count" => $rs->is_count
                    );


                    if( ! $this->orders_model->add_detail($arr))
                    {
                      $sc = FALSE;
                      $this->error = "Failed to insert order row";
                    }
                    else
                    {
                      $docTotal += $amountAfDisc;
                      $vatSum += $vat_amount;
                      $discSum += $sumBillDiscAmount;

                      $arr = array(
                        'ref_code' => $code,
                        'linked' => 'Y'
                      );

                      $this->sales_order_model->update_detail($rs->id, $arr);
                    }
                  }
                  else
                  {
                    $sc = FALSE;
                    $this->error = "Order qty exceeds sales order open qty";
                  }
                }
                else
                {
                  $sc = FALSE;
                  $this->error = "Invalid line status : {$ro->product_code} @line_id {$ro->id}";
                }
              } //-- end foreach

              if($sc === TRUE)
              {
                $amountAfDisc = $so->vat_type == 'E' ? $docTotal : $docTotal - $vatSum;
                $WhtAmount = $so->WhtPrcnt > 0 ? ($amountAfDisc > 0 ? $amountAfDisc * ($so->WhtPrcnt * 0.01) : 0.00) : 0.00;
                $DocTotal = $so->vat_type == 'E' ? $docTotal + $vatSum : $docTotal;
                $arr = array(
                  'doc_total' => $DocTotal,
                  'TotalBalance' => $DocTotal,
                  'VatSum' => $vatSum,
                  'bDiscAmount' => $discSum,
                  'WhtAmount' => $WhtAmount
                );

                $this->orders_model->update($code, $arr);
              }
            }
            else
            {
              $sc = FALSE;
              $this->error = "Failed to create new order";
            }

            if($sc === TRUE)
            {
              $arr = array('ref_code' => $code);

              if( ! $this->sales_order_model->update($so->code, $arr))
              {
                $sc = FALSE;
                $this->error = "Failed to update ref_code";
              }
            }


            if($sc === TRUE)
            {
              $this->db->trans_commit();
            }
            else
            {
              $this->db->trans_rollback();
            }
          }
          else
          {
            $sc = FALSE;
            $this->error = "No items found";
          }
        }
        else
        {
          $sc = FALSE;
          $this->error = "Invalid Sales order status";
        }
      }
      else
      {
        $sc = FALSE;
        $this->error = "Invalid Sales Order Code";
      }
    }
    else
    {
      $sc = FALSE;
      $this->error = "Missing required parameter";
    }

    $arr = array(
      'status' => $sc === TRUE ? 'success' : 'failed',
      'message' => $sc === TRUE ? 'success' : $this->error,
      'code' => $sc === TRUE ? $code : NULL
    );


    echo json_encode($arr);
  }


  public function createWq()
  {
    $sc = TRUE;
    $so_code = $this->input->post('so_code');
    $details = json_decode($this->input->post('details'));

    if( ! empty($so_code) && ! empty($details))
    {
      $this->load->model('orders/orders_model');
      $this->load->model('orders/sales_order_model');
      $this->load->model('inventory/transform_model');

      $so = $this->sales_order_model->get($so_code);

      if( ! empty($so))
      {
        if($so->status == 'O')
        {
          if( ! empty($details))
          {
            $book_code = getConfig('BOOK_CODE_TRANSFORM');
            $date_add = now();
            $code = $this->get_new_transform_code($date_add);
            $zone = getConfig('TRANSFORM_WAREHOUSE');

            if( ! empty($zone))
            {
              $zone_code = $zone .'-SYSTEM-BIN-LOCATION';

              $docTotal = 0;
              $vatSum = 0;
              $discSum = 0;

              $ds = array(
                'date_add' => $date_add,
                'code' => $code,
                'role' => 'T',
                'bookcode' => $book_code,
                'BaseRef' => $so->code,
                'BaseId' => $so->id,
                'BaseType' => 'SO',
                'reference' => $so->code,
                'so_code' => $so->code,
                'customer_code' => $so->customer_code,
                'customer_name' => $so->customer_name,
                'customer_ref' => $so->customer_ref,
                'tax_id' => $so->tax_id,
                'TaxStatus' => $so->TaxStatus,
                'vat_type' => $so->vat_type,
                'branch_code' => $so->branch_code,
                'branch_name' => $so->branch_name,
                'address' => $so->address,
                'sub_district' => $so->sub_district,
                'district' => $so->district,
                'province' => $so->province,
                'postcode' => $so->postcode,
                'phone' => $so->phone,
                'channels_code' => $so->channels_code,
                'sale_code' => $so->sale_id,
                'state' => 1,
                'is_term' => $so->is_term,
                'bDiscText' => $so->DiscPrcnt,
                'bDiscAmount' => $so->DiscAmount,
                'status' => 1,
                'zone_code' => $zone_code,
                'warehouse_code' => $so->whsCode,
                'isWht' => $so->isWht, //-- หัก ณ ที่จ่ายหรือไม่
                'WhtPrcnt' => $so->WhtPrcnt, //---- หัก ณ ที่จ่าย %
                'WhtAmount' => $so->WhtAmount, //--- หัก ณ ที่จ่าย มูลค่า
                'user' => $this->_user->uname,
                'user_ref' => $this->_user->name,
                'remark' => $so->remark
              );

              $this->db->trans_begin();

              $id = $this->orders_model->add_order($ds);
              $count = 0;

              if($id)
              {
                //--- add details
                foreach($details as $ro)
                {
                  $rs = $this->sales_order_model->get_detail($ro->id);

                  if( ! empty($rs))
                  {
                    if($rs->is_count)
                    {
                      if($rs->OpenQty >= $ro->qty)
                      {
                        $discount_amount = $rs->discount_amount * $ro->qty;
                        $total_amount = $ro->qty * $rs->sell_price;
                        $sumBillDiscAmount = $rs->avgBillDiscAmount * $total_amount;
                        $amountAfDisc = $total_amount - $sumBillDiscAmount;
                        $vat_amount = get_vat_amount($amountAfDisc, $rs->vat_rate, $so->vat_type);

                        $arr = array(
                          "id_order" =>  $id,
                          "order_code"	=> $code,
                          "style_code"		=> $rs->style_code,
                          "product_code"	=> $rs->product_code,
                          "product_name"	=> $rs->product_name,
                          "cost"  => $rs->cost,
                          "price"	=> $rs->price,
                          "qty"		=> $ro->qty,
                          "discount1"	=> $rs->discount_label,
                          "discount2" => 0,
                          "discount3" =>0,
                          "discount_amount" => $discount_amount,
                          "total_amount"	=> $total_amount,
                          "avgBillDiscAmount" => $rs->avgBillDiscAmount,
                          "sumBillDiscAmount" => $sumBillDiscAmount,
                          "vat_type" => $so->vat_type,
                          "vat_code" => $rs->vat_code,
                          "vat_rate" => $rs->vat_rate,
                          "vat_amount" => $vat_amount,
                          "baseCode" => $rs->order_code, //--- sales_order_code
                          "baseLine" => $rs->id, //--- sales_order_detail_id
                          "baseId" => $rs->id_order, //--- sales_order_id
                          "line_id" => $rs->id,
                          "is_count" => $rs->is_count
                        );

                        if( ! $this->orders_model->add_detail($arr))
                        {
                          $sc = FALSE;
                          $this->error = "Failed to insert order row";
                        }
                        else
                        {
                          $docTotal += $amountAfDisc;
                          $vatSum += $vat_amount;
                          $discSum += $sumBillDiscAmount;

                          $arr = array(
                            'ref_code' => $code,
                            'linked' => 'Y'
                          );

                          $this->sales_order_model->update_detail($rs->id, $arr);
                          $count++;
                        }
                      }
                      else
                      {
                        $sc = FALSE;
                        $this->error = "Order qty exceeds sales order open qty";
                      }
                    }
                  }
                  else
                  {
                    $sc = FALSE;
                    $this->error = "Invalid line status : {$ro->product_code} @line_id {$ro->id}";
                  }
                } //-- end foreach

                if($sc === TRUE && $count == 0)
                {
                  $sc = FALSE;
                  $this->error = "ไม่พบรายการเบิก รายการที่เลือกอาจไม่ใช่รายการที่ต้องเบิก";
                }

                if($sc === TRUE)
                {
                  $amountAfDisc = $so->vat_type == 'E' ? $docTotal : $docTotal - $vatSum;
                  $WhtAmount = $so->WhtPrcnt > 0 ? ($amountAfDisc > 0 ? $amountAfDisc * ($so->WhtPrcnt * 0.01) : 0.00) : 0.00;
                  $DocTotal = $so->vat_type == 'E' ? $docTotal + $vatSum : $docTotal;
                  $arr = array(
                    'doc_total' => $DocTotal,
                    'TotalBalance' => $DocTotal,
                    'VatSum' => $vatSum,
                    'bDiscAmount' => $discSum,
                    'WhtAmount' => $WhtAmount
                  );

                  $this->orders_model->update($code, $arr);
                }

                if($sc === TRUE)
                {
                  $this->transform_model->add(['order_code' => $code, 'so_code' => $so->code]);
                }
              }
              else
              {
                $sc = FALSE;
                $this->error = "Failed to create new order";
              }

              if($sc === TRUE)
              {
                $arr = array('ref_code' => $code);

                if( ! $this->sales_order_model->update($so->code, $arr))
                {
                  $sc = FALSE;
                  $this->error = "Failed to update ref_code";
                }
              }

              if($sc === TRUE)
              {
                $this->db->trans_commit();
              }
              else
              {
                $this->db->trans_rollback();
              }
            }
            else
            {
              $sc = FALSE;
              $this->error = "ไม่พบคลังระหว่างทำ กรุณาตรวจสอบการตั้งค่าคลังระหว่างทำ";
            }
          }
          else
          {
            $sc = FALSE;
            $this->error = "No items found";
          }
        }
        else
        {
          $sc = FALSE;
          $this->error = "Invalid Sales order status";
        }
      }
      else
      {
        $sc = FALSE;
        $this->error = "Invalid Sales Order Code";
      }
    }
    else
    {
      $sc = FALSE;
      $this->error = "Missing required parameter";
    }

    $arr = array(
      'status' => $sc === TRUE ? 'success' : 'failed',
      'message' => $sc === TRUE ? 'success' : $this->error,
      'code' => $sc === TRUE ? $code : NULL
    );


    echo json_encode($arr);
  }


  public function get_new_code($date)
  {
    $date = $date == '' ? date('Y-m-d') : $date;
    $Y = date('y', strtotime($date));
    $M = date('m', strtotime($date));
    $prefix = getConfig('PREFIX_SALES_ORDER');
    $run_digit = getConfig('RUN_DIGIT_SALES_ORDER');
    $pre = $prefix .'-'.$Y.$M;
    $code = $this->sales_order_model->get_max_code($pre);

    if(! empty($code))
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


  public function get_new_order_code($date)
  {
    $date = $date == '' ? date('Y-m-d') : $date;
    $Y = date('y', strtotime($date));
    $M = date('m', strtotime($date));
    $prefix = getConfig('PREFIX_ORDER');
    $run_digit = getConfig('RUN_DIGIT_ORDER');
    $pre = $prefix .'-'.$Y.$M;
    $code = $this->orders_model->get_max_code($pre);

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


  public function get_new_transform_code($date)
  {
    $date = $date == '' ? date('Y-m-d') : $date;
    $Y = date('y', strtotime($date));
    $M = date('m', strtotime($date));
    $prefix = getConfig('PREFIX_TRANSFORM');
    $run_digit = getConfig('RUN_DIGIT_TRANSFORM');
    $pre = $prefix .'-'.$Y.$M;
    $code = $this->orders_model->get_max_code($pre);

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


  public function clear_filter()
  {
    $filter = array(
      'so_code',
      'so_customer_ref',
      'so_ref_code',
      'so_bill_code',
      'so_job_type',
      'so_job_title',
      'so_phone',
      'so_status',
      'so_from_date',
      'so_to_date',
      'so_due_from_date',
      'so_due_to_date',
      'so_user',
      'so_state',
      'onlyMe'
    );

    return clear_filter($filter);
  }
} //--- end class

 ?>
