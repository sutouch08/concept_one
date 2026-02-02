<?php
class Import_order extends CI_Controller
{
  public $ms;
  public $mc;
  public $_user;
  public $error;
  public $message;

  public function __construct()
  {
    parent::__construct();
    $this->ms = $this->load->database('ms', TRUE); //--- SAP database
    $this->mc = $this->load->database('mc', TRUE); //--- Temp Database

    $uid = get_cookie('uid');

		$this->_user = $this->user_model->get_user_by_uid($uid);

    $this->load->model('orders/orders_model');
    $this->load->model('masters/channels_model');
    $this->load->model('masters/payment_methods_model');
    $this->load->model('masters/products_model');
    $this->load->model('masters/customers_model');
    $this->load->model('orders/order_state_model');
    $this->load->model('masters/products_model');
		$this->load->model('masters/warehouse_model');
		$this->load->model('masters/sender_model');
    $this->load->model('address/address_model');
    $this->load->model('stock/stock_model');
    $this->load->model('orders/order_import_logs_model');

    $this->load->library('excel');
  }


  public function index()
  {
    ini_set('max_execution_time', 1200);
    ini_set('memory_limit','1000M');

    $sc = TRUE;

    $import = 0;
    $success = 0;
    $failed = 0;
    $skip = 0;

    $file = isset( $_FILES['uploadFile'] ) ? $_FILES['uploadFile'] : FALSE;
    $path = $this->config->item('upload_path').'orders/';
    $file	= 'uploadFile';
    $config = array(   // initial config for upload class
      "allowed_types" => "xlsx",
      "upload_path" => $path,
      "file_name"	=> "import_order-".date('YmdHis'),
      "max_size" => 5120,
      "overwrite" => TRUE
    );

    $this->load->library("upload", $config);

    if(! $this->upload->do_upload($file))
    {
      echo $this->upload->display_errors();
    }
    else
    {
      $info = $this->upload->data();
      /// read file
      $excel = PHPExcel_IOFactory::load($info['full_path']);
      $excel->setActiveSheetIndex(0);
      $sheet = $excel->getSheet(0);

      if( ! empty($sheet))
      {
        $count = $sheet->getHighestRow();
        $limit = intval(getConfig('IMPORT_ROWS_LIMIT')) + 1;

        if($count > $limit)
        {
          $sc = FALSE;
          $this->error = "ไฟล์มีจำนวนรายการเกิน {$limit} บรรทัด";
        }

        if($sc === TRUE)
        {
          $ds = $this->parse_order_data($sheet);

          if( ! empty($ds))
          {
            $shipping_item_code = getConfig('SHIPPING_ITEM_CODE');
            $shipping_item = ! empty($shipping_item_code) ? $this->products_model->get($shipping_item_code) : NULL;

            foreach($ds as $order)
            {
              $import++;

              $res = TRUE;
              $message = "";
              //---- เช็คว่ามีออเดอร์ที่สร้างด้วย reference แล้วหรือยัง
              //---- ถ้ายังไม่มีให้สร้างใหม่
              //---- ถ้ามีแล้วและยังไม่ได้ยกเลิก ไม่สามารถเพิ่มใหม่ได้
              $order_code = $this->orders_model->get_active_order_code_by_reference($order->reference);
              $total_amount = 0;
              $vat_sum = 0;
              $total_qty = 0;

              if( empty($order_code) )
              {
                $this->db->trans_begin();

                $order_code = $this->get_new_code($order->date_add);

                $arr = array(
                  'code' => $order_code,
                  'role' => $order->role,
                  'bookcode' => $order->bookcode,
                  'reference' => $order->reference,
                  'vat_type' => $order->vat_type,
                  'customer_code' => $order->customer_code,
                  'customer_name' => $order->customer_name,
                  'customer_ref' => $order->customer_ref,
                  'channels_code' => $order->channels_code,
                  'sale_code' => $order->sale_code,
                  'state' => $order->state,
                  'is_term' => $order->is_term,
                  'shipping_code' => $order->shipping_code,
                  'status' => $order->status,
                  'date_add' => $order->date_add,
                  'warehouse_code' => $order->warehouse_code,
                  'user' => $order->user,
                  'is_import' => $order->is_import,
                  'remark' => $order->remark,
                  'address' => $order->address,
                  'sub_district' => $order->sub_district,
                  'district' => $order->district,
                  'province' => $order->province,
                  'postcode' => $order->postcode,
                  'phone' => $order->phone
                );

                //--- add order
                $id_order = $this->orders_model->add($arr);

                if( ! $id_order)
                {
                  $res = FALSE;
                  $message = "Failed to create order for orderNumber {$order->reference}";
                }

                if($res === TRUE)
                {
                  if( ! empty($order->items))
                  {
                    foreach($order->items as $row)
                    {
                      $price = $row->qty > 0 ? round(($row->total_amount / $row->qty), 6) : 0;
                      $vat_amount = get_vat_amount($row->total_amount, $row->vat_rate, $order->vat_type);

                      $arr = array(
                        'id_order' => $id_order,
                        'order_code' => $order_code,
                        'style_code' => $row->style_code,
                        'product_code' => $row->product_code,
                        'product_name' => $row->product_name,
                        'cost' => $row->cost,
                        'price' => $price,
                        'qty' => $row->qty,
                        'discount1' => $row->discount1,
                        'discount2' => $row->discount2,
                        'discount3' => $row->discount3,
                        'discount_amount' => $row->discount_amount,
                        'total_amount' => $row->total_amount,
                        'vat_code' => $row->vat_code,
                        'vat_rate' => $row->vat_rate,
                        'vat_type' => $order->vat_type,
                        'vat_amount' => $vat_amount,
                        'is_count' => $row->is_count,
                        'is_import' => $row->is_import
                      );


                      if( ! $this->orders_model->add_detail($arr))
                      {
                        $res = FALSE;
                        $message = "Failed to add order row of {$order->reference} : {$row->product_code}";
                      }
                      else
                      {
                        $total_amount += $row->total_amount;
                        $vat_sum += $vat_amount;
                        $total_qty += $row->qty;
                      }

                      if($res == FALSE)
                      {
                        break;
                      }
                    } //--- end foreach

                    //---- if has shipping fee  add shipping sku to order
                    if($res === TRUE && $order->shipping_fee > 0 && ! empty($shipping_item))
                    {
                      $vat_amount += get_vat_amount($order->shipping_fee, $shipping_item->sale_vat_rate, $order->vat_type);

                      $arr = array(
                        "id_order" => $id_order,
                        "order_code" => $order_code,
                        "style_code" => $shipping_item->style_code,
                        "product_code" => $shipping_item->code,
                        "product_name" => $shipping_item->name,
                        "cost" => $shipping_item->cost,
                        "price"	=> $order->shipping_fee,
                        "qty"	=> 1,
                        "discount1"	=> 0,
                        "discount2" => 0,
                        "discount3" => 0,
                        "discount_amount" => 0,
                        "total_amount"	=> $order->shipping_fee,
                        "vat_code" => $shipping_item->sale_vat_code,
                        "vat_rate" => $shipping_item->sale_vat_rate,
                        "vat_type" => $order->vat_type,
                        "vat_amount" => $vat_amount,
                        "is_count" => $shipping_item->count_stock,
                        "is_import" => 1
                      );

                      if( ! $this->orders_model->add_detail($arr))
                      {
                        $res = FALSE;
                        $message = "Failed to insert shipping item row of {$order->reference}";
                      }
                      else
                      {
                        $total_amount += $order->shipping_fee;
                        $vat_sum += $vat_amount;
                        $total_qty += 1;
                      }
                    } //--- end if($order->shipping_fee)
                  } //--- end if ! empty($order->items)
                } //--- $sc === TRUE

                //-- add state
                if($res === TRUE)
                {
                  $arr = array(
                    'doc_total' => $total_amount,
                    'TotalBalance' => $total_amount,
                    'VatSum' => $vat_sum
                  );

                  $this->orders_model->update($order_code, $arr);

                  $arr = array(
                    'order_code' => $order_code,
                    'state' => $order->state,
                    'update_user' => $this->_user->uname
                  );

                  //--- add state event
                  $this->order_state_model->add_state($arr);
                }

                if($res === TRUE)
                {
                  $this->db->trans_commit();
                  $success++;
                }
                else
                {
                  $this->db->trans_rollback();
                  $failed++;
                }

                //--- add logs
                $logs = array(
                  'reference' => $order->reference,
                  'order_code' => $order_code,
                  'action' => 'A', //-- A = add , U = update
                  'status' => $res === TRUE ? 'S' : 'E', //-- S = success, E = error, D = duplication
                  'message' => $res === TRUE ? NULL : $message,
                  'user' => $this->_user->uname
                );

                $this->order_import_logs_model->add($logs);
              }
              else
              {
                if($order->force_update)
                {
                  $doc = $this->orders_model->get($order_code);

                  if( ! empty($doc) && $doc->state <= 3)
                  {
                    $this->db->trans_begin();

                    $arr = array(
                      'code' => $order_code,
                      'vat_type' => $order->vat_type,
                      'customer_code' => $order->customer_code,
                      'customer_name' => $order->customer_name,
                      'customer_ref' => $order->customer_ref,
                      'channels_code' => $order->channels_code,
                      'sale_code' => $order->sale_code,
                      'state' => $order->state,
                      'is_term' => $order->is_term,
                      'shipping_code' => $order->shipping_code,
                      'status' => $order->status,
                      'date_add' => $order->date_add,
                      'warehouse_code' => $order->warehouse_code,
                      'user' => $order->user,
                      'is_import' => $order->is_import,
                      'remark' => $order->remark,
                      'address' => $order->address,
                      'sub_district' => $order->sub_district,
                      'district' => $order->district,
                      'province' => $order->province,
                      'postcode' => $order->postcode,
                      'phone' => $order->phone,
                      'doc_total' => 0,
                      'TotalBalance' => 0,
                      'VatSum' => 0
                    );

                    if( ! $this->orders_model->update($order_code, $arr))
                    {
                      $res = FALSE;
                      $message = "Failed to update order {$order_code} for {$order->reference}";
                    }

                    if($res === TRUE)
                    {
                      //---- drop previous order rows
                      if( ! $this->orders_model->remove_all_details($order_code))
                      {
                        $res = FALSE;
                        $message = "Failed to remove previous order rows";
                      }
                      else
                      {
                        if( ! empty($order->items))
                        {
                          foreach($order->items as $row)
                          {
                            $price = $row->qty > 0 ? round(($row->total_amount / $row->qty), 6) : 0;
                            $vat_amount = get_vat_amount($row->total_amount, $row->vat_rate, $order->vat_type);

                            $arr = array(
                              'id_order' => $doc->id,
                              'order_code' => $order_code,
                              'style_code' => $row->style_code,
                              'product_code' => $row->product_code,
                              'product_name' => $row->product_name,
                              'cost' => $row->cost,
                              'price' => $price,
                              'qty' => $row->qty,
                              'discount1' => $row->discount1,
                              'discount2' => $row->discount2,
                              'discount3' => $row->discount3,
                              'discount_amount' => $row->discount_amount,
                              'total_amount' => $row->total_amount,
                              'vat_code' => $row->vat_code,
                              'vat_rate' => $row->vat_rate,
                              'vat_type' => $order->vat_type,
                              'vat_amount' => $vat_amount,
                              'is_count' => $row->is_count,
                              'is_import' => $row->is_import
                            );

                            if( ! $this->orders_model->add_detail($arr))
                            {
                              $res = FALSE;
                              $message = "Failed to add order row of {$order->reference} : {$row->product_code}";
                            }
                            else
                            {
                              $total_amount += $row->total_amount;
                              $vat_sum += $vat_amount;
                              $total_qty += $row->qty;
                            }

                            if($res == FALSE)
                            {
                              break;
                            }
                          } //--- end foreach

                          //---- if has shipping fee  add shipping sku to order
                          if($res === TRUE && $order->shipping_fee > 0 && ! empty($shipping_item))
                          {
                            $vat_amount += get_vat_amount($order->shipping_fee, $shipping_item->sale_vat_rate, $order->vat_type);

                            $arr = array(
                              "id_order" => $doc->id,
                              "order_code" => $order_code,
                              "style_code" => $shipping_item->style_code,
                              "product_code" => $shipping_item->code,
                              "product_name" => $shipping_item->name,
                              "cost" => $shipping_item->cost,
                              "price"	=> $order->shipping_fee,
                              "qty"	=> 1,
                              "discount1"	=> 0,
                              "discount2" => 0,
                              "discount3" => 0,
                              "discount_amount" => 0,
                              "total_amount"	=> $order->shipping_fee,
                              "vat_code" => $shipping_item->sale_vat_code,
                              "vat_rate" => $shipping_item->sale_vat_rate,
                              "vat_type" => $order->vat_type,
                              "vat_amount" => $vat_amount,
                              "is_count" => $shipping_item->count_stock,
                              "is_import" => 1
                            );

                            if( ! $this->orders_model->add_detail($arr))
                            {
                              $sc = FALSE;
                              $message = "Failed to insert shipping item row of {$order->reference}";
                            }
                            else
                            {
                              $total_amount += $order->shipping_fee;
                              $vat_sum += $vat_amount;
                              $total_qty += 1;
                            }
                          } //--- end if($order->shipping_fee)
                        } //--- end if ! empty($order->items)
                      } //--- end if remove all detail
                    } //--- if($res === TRUE)

                    //-- add state
                    if($res === TRUE)
                    {
                      $arr = array(
                        'doc_total' => $total_amount,
                        'TotalBalance' => $total_amount,
                        'VatSum' => $vat_sum
                      );

                      $this->orders_model->update($order_code, $arr);

                      $arr = array(
                        'order_code' => $order_code,
                        'state' => $order->state,
                        'update_user' => $this->_user->uname
                      );
                      //--- add state event
                      $this->order_state_model->add_state($arr);
                    }

                    if($res === TRUE)
                    {
                      $this->db->trans_commit();
                      $success++;
                    }
                    else
                    {
                      $this->db->trans_rollback();
                      $failed++;
                    }

                    //--- add logs
                    $logs = array(
                      'reference' => $order->reference,
                      'order_code' => $order_code,
                      'action' => 'U', //-- A = add , U = update
                      'status' => $res === TRUE ? 'S' : 'E', //-- S = success, E = error, D = duplication
                      'message' => $message,
                      'user' => $this->_user->uname
                    );

                    $this->order_import_logs_model->add($logs);
                  }
                  else
                  {
                    $failed++;
                    //--- add logs
                    $logs = array(
                      'reference' => $order->reference,
                      'order_code' => $order_code,
                      'action' => 'U', //-- A = add , U = update
                      'status' => 'E', //-- S = success, E = error, D = Skip (duplicated and not force to update)
                      'message' => "Invalid order state",
                      'user' => $this->_user->uname
                    );

                    $this->order_import_logs_model->add($logs);
                  }
                }
                else
                {
                  $skip++;
                  //--- add logs
                  $logs = array(
                    'reference' => $order->reference,
                    'order_code' => $order_code,
                    'action' => 'A', //-- A = add , U = update
                    'status' => 'D', //-- S = success, E = error, D = Skip (duplicated and not force to update)
                    'message' => "{$order->reference} already exists",
                    'user' => $this->_user->uname
                  );

                  $this->order_import_logs_model->add($logs);
                }
              } //--- end if order exists
            } //--- end foreach
          } //--- end if ! empty ds
          else
          {
            $sc = FALSE;
          }
        }
      }
      else
      {
        $sc = FALSE;
        $this->error = "Cannot get data from import file : empty data collection";
      }
    } //-- end upload success

    $message = "Imported : {$import} <br/> Success : {$success} <br/> Failed : {$failed} <br/> Skip : {$skip}";
    $message .= $failed > 0 ? "<br/><br/> พบรายการที่ไม่สำเร็จ กรุณาตรวจสอบ Import logs" : "";

    $arr = array(
      'status' => $sc === TRUE ? 'success' : 'failed',
      'message' => $sc === TRUE ? $message : $this->error
    );

    echo json_encode($arr);
  }


  private function parse_order_data($sheet)
  {
    $sc = TRUE;

    if( ! empty($sheet))
    {
      $rows = $sheet->getHighestRow();
      $bookcode = getConfig('BOOK_CODE_ORDER');
      $role = 'S';

      $default_warehouse = getConfig('DEFAULT_WAREHOUSE');
      $default_sale_code = getConfig('DEFAULT_SALES_ID');

      $ds = array(); //---- ได้เก็บข้อมูล orders

      $whsCache = array(); //--- ไว้เก็บ  warehouse cache

      //--- เก็บ channels cache
      $channelsCache = array();

      //--- เก็บ customer cache
      $customerCache = array();

      $senderCache = array();

      $addrCache = array();

      $itemsCache = array(); //--- เก็บ item cache

      $headCol = array(
        'A' => 'Order No',
        'B' => 'Date',
        'C' => 'VAT',
        'D' => 'Customer Code',
        'E' => 'Customer Ref',
        'F' => 'Channels',
        'G' => 'Item Code',
        'H' => 'Item Name',
        'I' => 'Qty',
        'J' => 'Sell Price',
        'K' => 'Total Amount',
        'L' => 'Shipping Fee',
        'M' => 'Warehouse Code',
        'N' => 'Address',
        'O' => 'Sub District',
        'P' => 'District',
        'Q' => 'Province',
        'R' => 'Post Code',
        'S' => 'Phone',
        'T' => 'Remark',
        'U' => 'Force Update',
        'V' => 'Tracking No'
      );

      $i = 1;

      while($i <= $rows)
      {
        if($sc === FALSE)
        {
          break;
        }

        if($i == 1)
        {
          foreach($headCol as $col => $field)
          {
            $value = $sheet->getCell($col.$i)->getValue();

            if(empty($value) OR $value !== $field)
            {
              $sc = FALSE;
              $this->error .= 'Column '.$col.' Should be '.$field.'<br/>';
            }
          }

          if($sc === FALSE)
          {
            $this->error .= "<br/><br/>You should download new template !";
            break;
          }

          $i++;
        }
        else
        {
          $rs = [];

          foreach($headCol as $col => $field)
          {
            $column = $col.$i;

            $rs[$col] = $sheet->getCell($column)->getValue();
          }

          if($sc === TRUE && ! empty($rs['A']))
          {
            //--- ใช้ orderNumber เป็น key array
            $ref_code = trim($rs['A']);

            //--- เช็คว่ามี key อยู่แล้วหรือไม่
            //--- ถ้ายังไม่มีให้สร้างใหม่ ถ้ามีแล้ว ให้เพิ่ม รายการสินค้าเข้าไป
            if( ! isset($ds[$ref_code]))
            {
              $cell = $sheet->getCell("B{$i}");
              $date = trim($cell->getValue());

              if (PHPExcel_Shared_Date::isDateTime($cell))
              {
                $dateTimeObject = PHPExcel_Shared_Date::ExcelToPHPObject($date);
                $date_add = $dateTimeObject->format('Y-m-d');
              }
              else
              {
                $date_add = db_date($date);
              }

              //--- check date format only check not convert
              if( ! is_valid_date($date_add))
              {
                $sc = FALSE;
                $this->error = "Invalid Date format at Line {$i} : {$date}";
              }

              $warehouse_code = empty(trim($rs['M'])) ? $default_warehouse : trim($rs['M']);

              //--- check warehouse cache
              //--- if not extsts get form database and add to cache
              if( ! isset($whsCache[$warehouse_code]))
              {
                $warehouse = $this->warehouse_model->get($warehouse_code);

                if( ! empty($warehouse))
                {
                  $whsCache[$warehouse_code] = $warehouse_code;
                }
                else
                {
                  $sc = FALSE;
                  $this->error .= "Invalid warehouse_code at Line {$i} <br/>";
                }
              }

              //---- กำหนดช่องทางการขายเป็นรหัส
              $channels_code = trim($rs['F']);

              if( ! empty($channels_code))
              {
                //--- check channels cache
                if( ! isset($channelsCache[$channels_code]))
                {
                  $channels = $this->channels_model->get($channels_code);

                  if( ! empty($channels))
                  {
                    $channelsCache[$channels_code] = $channels;
                  }
                  else
                  {
                    $sc = FALSE;
                    $this->error = "Invalid channels at Line {$i} <br/>";
                  }
                }

                $channels = $channelsCache[$channels_code];
              }
              else
              {
                $sc = FALSE;
                $this->error .= "Channels is required at Line {$i} <br/>";
              }

              //---- check customers
              $customer_code = empty(trim($rs['D'])) ? NULL : trim($rs['D']);

              if( ! empty($customer_code))
              {
                //--- check customer Cache
                if( ! isset($customerCache[$customer_code]))
                {
                  $customer = $this->customers_model->get($customer_code);

                  if( ! empty($customer))
                  {
                    $customerCache[$customer_code] = $customer;
                  }
                  else
                  {
                    $sc = FALSE;
                    $this->error .= "Invalid customer at Line {$i}";
                  }
                }

                $customer = $customerCache[$customer_code];
              }
              else
              {
                $sc = FALSE;
                $this->error .= "Customer Code is required at Line {$i} <br/>";
              }

              //--- check item cache
              $item_code = trim($rs['G']);
              $item_name = trim($rs['H']);

              if( ! empty($item_code))
              {
                if( ! isset($itemsCache[$item_code]))
                {
                  $item = $this->products_model->get($item_code);

                  if( ! empty($item))
                  {
                    if( ! empty($item_name))
                    {
                      $item->name = $item_name;
                    }

                    $itemsCache[$item->code] = $item;
                    $item_code = $item->code;
                  }
                  else
                  {
                    $sc = FALSE;
                    $this->error .= "Invalid Item code '{$item_code}' at Line {$i} <br/>";
                  }
                }
              }
              else
              {
                $sc = FALSE;
                $this->error .= "Item Code code is required at Line {$i} <br/>";
              }

              $item = empty($itemsCache[$item_code]) ? NULL : $itemsCache[$item_code];

              //--- tracking no
              $tracking = get_null(trim($rs['V']));

              if($channels_code == '0009' && empty($tracking))
              {
                $sc = FALSE;
                $this->error .= "Tracking No is Required for Tiktok order at Line {$i} <br/>";
              }

              if($sc === TRUE)
              {
                //---	ถ้าเป็นออเดอร์ขาย จะมี id_sale
                $sale_code = empty($this->_user->sale_id) ? $default_sale_code : $this->_user->sale_id;

                //---	หากเป็นออนไลน์ ลูกค้าออนไลน์ชื่ออะไร
                $customer_ref = addslashes(trim($rs['E']));

                //--- ค่าจัดส่ง
                $shipping_fee = empty($rs['L']) ? 0.00 : trim($rs['L']);
                $shipping_fee = is_numeric($shipping_fee) ? $shipping_fee : 0.00;

                //-- remark
                $remark = get_null(trim($rs['T']));

                $qty = empty(trim($rs['I'])) ? 1 : str_replace(',', '', $rs['I']);
                $qty = is_numeric($qty) ? $qty : 1;

                //--- ราคา
                $price = empty($rs['J']) ? 0.00 : str_replace(",", "", $rs['J']);
                $price = is_numeric($price) ? $price : 0;

                $vat_type = trim($rs['C']);
                $vat_type = empty($vat_type) ? 'N' : (($vat_type == 'I' OR $vat_type == 'E' OR $vat_type == 'N') ? $vat_type : 'N');

                //--- total_amount
                $total_amount = $price * $qty;

                //---- now create order data
                $ds[$ref_code] = (object) array(
                  'role' => $role,
                  'bookcode' => $bookcode,
                  'reference' => $ref_code,
                  'customer_code' => $customer_code,
                  'customer_name' => $customer->name,
                  'customer_ref' => $customer_ref,
                  'channels_code' => $channels_code,
                  'sale_code' => $sale_code,
                  'state' => 3,
                  'is_term' => 1,
                  'shipping_fee' => $shipping_fee,
                  'shipping_code' => $tracking,
                  'status' => 1,
                  'date_add' => $date_add,
                  'warehouse_code' => $warehouse_code,
                  'user' => $this->_user->uname,
                  'is_import' => 1,
                  'remark' => $remark,
                  'vat_type' => $vat_type,
                  'address' => get_null(trim($rs['N'])),
                  'sub_district' => get_null(trim($rs['O'])),
                  'district' => get_null(trim($rs['P'])),
                  'province' => get_null(trim($rs['Q'])),
                  'postcode' => get_null(trim($rs['R'])),
                  'phone' => get_null(trim($rs['S'])),
                  'force_update' => (trim($rs['U']) == 1 OR trim($rs['U']) == 'Y' OR trim($rs['U']) == 'y') ? TRUE : FALSE,
                  'items' => array()
                );

                $row = (object) array(
                  'style_code' => $item->style_code,
                  'product_code' => $item->code,
                  'product_name' => $item->name,
                  'cost' => $item->cost,
                  'price' => $price,
                  'qty' => $qty,
                  "discount1"	=> 0,
                  "discount2" => 0,
                  "discount3" => 0,
                  "discount_amount" => 0,
                  "total_amount"	=> $total_amount,
                  "is_count" => $item->count_stock,
                  "vat_code" => $item->sale_vat_code,
                  "vat_rate" => $item->sale_vat_rate,
                  "is_import" => 1
                );

                $ds[$ref_code]->items[$item->code] = $row;
              } //--- end if $sc == TRUE
            }
            else
            {
              //--- check item cache
              $item_code = trim($rs['G']);
              $item_name = trim($rs['H']);

              if( ! empty($item_code))
              {
                if( ! isset($itemsCache[$item_code]))
                {
                  $item = $this->products_model->get($item_code);

                  if( ! empty($item))
                  {
                    if( ! empty($item_name))
                    {
                      $item->name = $item_name;
                    }

                    $itemsCache[$item->code] = $item;
                    $item_code = $item->code;
                  }
                  else
                  {
                    $sc = FALSE;
                    $this->error .= "Invalid Item code '{$item_code}' at Line {$i} <br/>";
                  }
                }
              }
              else
              {
                $sc = FALSE;
                $this->error .= "Item Code code is required at Line {$i} <br/>";
              }


              if($sc === TRUE)
              {
                $item = $itemsCache[$item_code];

                $qty = empty(trim($rs['I'])) ? 1 : str_replace(',', '', $rs['I']);
                $qty = is_numeric($qty) ? $qty : 1;

                //--- ราคา
                $price = empty($rs['J']) ? 0.00 : str_replace(",", "", $rs['J']);
                $price = is_numeric($price) ? $price : 0;
                $total_amount = $qty * $price;

                if(isset($ds[$ref_code]->items[$item->code]))
                {
                  $row = $ds[$ref_code]->items[$item->code];
                  $newQty = $row->qty + $qty;
                  $newTotal = $row->total_amount + $total_amount;

                  $ds[$ref_code]->items[$item->code]->qty = $newQty;
                  $ds[$ref_code]->items[$item->code]->total_amount = $newTotal;
                }
                else
                {
                  $row = (object) array(
                  'style_code' => $item->style_code,
                  'product_code' => $item->code,
                  'product_name' => $item->name,
                  'cost' => $item->cost,
                  'price' => $price,
                  'qty' => $qty,
                  "discount1"	=> 0,
                  "discount2" => 0,
                  "discount3" => 0,
                  "discount_amount" => 0,
                  "total_amount"	=> $total_amount,
                  "vat_code" => $item->sale_vat_code,
                  "vat_rate" => $item->sale_vat_rate,
                  "is_count" => $item->count_stock,
                  "is_import" => 1
                  );

                  $ds[$ref_code]->items[$item->code] = $row;
                }
              }
            } //--- end if( ! isset($ds[$ref_code]));
          } //--- end i

          $i++;
        } //--- end if $i == 1
      } //---- end foreach collection
    }
    else
    {
      $sc = FALSE;
      $this->error = "Empty data collection";
    }

    return $sc === TRUE ? $ds : FALSE;
  }


  public function get_available_stock($item_code, $warehouse_code)
  {
    //---- สต็อกคงเหลือในคลัง
    $sell_stock = $this->stock_model->get_sell_stock($item_code, $warehouse_code);

    //---- ยอดจองสินค้า ไม่รวมรายการที่กำหนด
    $reserv_stock = $this->orders_model->get_reserv_stock($item_code, $warehouse_code);

    $available = $sell_stock - $reserv_stock;

    return $available < 0 ? 0 : $available;
  }


  public function get_new_code($date)
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
}

 ?>
