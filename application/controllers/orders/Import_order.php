<?php
class Import_order extends CI_Controller
{
  public $ms;
  public $mc;
	public $isAPI = FALSE;
	public $wms;
  public $_user;
	public $sync_chatbot_stock = FALSE;


  public function __construct()
  {
    parent::__construct();
    $this->ms = $this->load->database('ms', TRUE); //--- SAP database
    $this->mc = $this->load->database('mc', TRUE); //--- Temp Database
		$this->wms = $this->load->database('wms', TRUE);

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

    $this->load->library('excel');

		$this->isAPI = is_true(getConfig('WMS_API'));

  }


  public function index()
  {
		ini_set('max_execution_time', 1200);

    $sc = TRUE;
    $import = 0;
    $file = isset( $_FILES['uploadFile'] ) ? $_FILES['uploadFile'] : FALSE;
  	$path = $this->config->item('upload_path').'orders/';
    $file	= 'uploadFile';
		$config = array(   // initial config for upload class
			"allowed_types" => "xlsx",
			"upload_path" => $path,
			"file_name"	=> "import_order",
			"max_size" => 5120,
			"overwrite" => TRUE
			);

			$this->load->library("upload", $config);
			$this->load->library("wms_order_api");

			if(! $this->upload->do_upload($file))
      {
				echo $this->upload->display_errors();
			}
      else
      {
        $info = $this->upload->data();
        /// read file
				$excel = PHPExcel_IOFactory::load($info['full_path']);
				//get only the Cell Collection
        $collection	= $excel->getActiveSheet()->toArray(NULL, TRUE, TRUE, TRUE);

        $i = 1;
        $count = count($collection);
        $limit = intval(getConfig('IMPORT_ROWS_LIMIT'))+1;

        if( $count <= $limit )
        {
          $ds = array();
          foreach($collection as $cs)
          {
            //---- order code from web site
            $key = $cs['I'];

            $str = substr($key, 0, 2);

            if($str == 'LA')
            {
              $key = substr($key, 2);
            }

            $cs['I'] = $key;

            $key = $key.$cs['M'];


            if(isset($ds[$key]))
            {
              $ds[$key]['N'] += $cs['N'];
              $ds[$key]['O'] += $cs['O'];
              $ds[$key]['P'] += $cs['P'];
            }
            else
            {
              $ds[$key] = $cs;
            }
          }

          //--- รหัสเล่มเอกสาร [อ้างอิงจาก SAP]
          //--- ถ้าเป็นฝากขายแบบโอนคลัง ยืมสินค้า เบิกแปรสภาพ เบิกสินค้า (ไม่เปิดใบกำกับ เปิดใบโอนคลังแทน) นอกนั้น เปิด SO
          $bookcode = getConfig('BOOK_CODE_ORDER');

          $role = 'S';

					//--- คลังสินค้า
					$warehouse_code = getConfig('WEB_SITE_WAREHOUSE_CODE');
          $chatbot_warehouse_code = getConfig('CHATBOT_WAREHOUSE_CODE');
          $this->sync_chatbot_stock = getConfig('SYNC_CHATBOT_STOCK') == 1 ? TRUE : FALSE; //--- sync stock chatbot
					$sync_items = array(); //--- ไว้เก็บรหัสสินค้าที่จะต้อง sync ตอนแรกอาจจะมีรหัสซ้ำ แต่จะทำให้ไม่ซ้ำก่อนแล้วค่อยไปดึงสต็อกแล้วใส่ไว้ใน sync_stock อีกที
					$wh = $this->warehouse_model->get($warehouse_code);

					$is_wms = empty($wh) ? 0 : $wh->is_wms;

          //---- กำหนดช่องทางขายสำหรับเว็บไซต์ เพราะมีลูกค้าแยกตามช่องทางการชำระเงินอีกที
          //---- เลยต้องกำหนดลูกค้าแยกตามช่องทางการชำระเงินต่างๆ สำหรับเว็บไซต์เท่านั้น
          //---- เพราะช่องทางอื่นๆในการนำเข้าจะใช้ช่องทางการชำระเงินแบบเดียวทั้งหมด
          //---- เช่น K plus จะจ่ายด้วยบัตรเครดิตทั้งหมด  LAZADA จะไปเรียกเก็บเงินกับทาง ลาซาด้า
          $web_channels = getConfig('WEB_SITE_CHANNELS_CODE');

          //--- รหัสลูกค้าสำหรับ COD เว็บไซต์
          $web_customer_cod = getConfig('CUSTOMER_CODE_COD');

          //--- รหัสลูกค้าสำหรับ 2c2p บนเว็บไซต์
          $web_customer_2c2p = getConfig('CUSTOMER_CODE_2C2P');

          //--- รหัสลูกค้าเริ่มต้น หากพอว่าไม่มีการระบุรหัสลูกค้าไว้ จะใช้รหัสนี้แทน
          $default_customer = getConfig('DEFAULT_CUSTOMER');

          $prefix = getConfig('PREFIX_SHIPPING_NUMBER');

          $shipping_item_code = getConfig('SHIPPING_ITEM_CODE');

          $shipping_item = !empty($shipping_item_code) ? $this->products_model->get($shipping_item_code) : NULL;

          //--- ไว้เช็คว่าเพิ่มรหัสค่าจัดส่งไปแล้วหรือยัง หากเพิ่มแล้วจะใส่ order_code ไว้ที่นี่
          //--- หาก order_code ไม่ตรงกันหมายถึงยังไม่ได้ใส่
          $shipping_added = NULL;

					$orderCode = NULL;
					$ref_code = NULL;
					$order_code = NULL;
					$date_add = NULL;
					$channels = NULL;
					$payment = NULL;
					$remark = NULL;
					$is_exists = FALSE;
					$hold = NULL;
          $isWMS = 0;
          foreach($ds as $rs)
          {
            //--- ถ้าพบ Error ให้ออกจากลูปทันที
            if($sc === FALSE)
            {
              break;
            }

            if($i == 1)
            {
              $i++;
              $headCol = array(
                'A' => 'Consignee Name',
                'B' => 'Address Line 1',
                'C' => 'Province',
                'D' => 'District',
                'E' => 'Sub District',
                'F' => 'postcode',
                'G' => 'email',
                'H' => 'tel',
                'I' => 'orderNumber',
                'J' => 'CreateDateTime',
                'K' => 'Payment Method',
                'L' => 'Channels',
                'M' => 'itemId',
                'N' => 'amount',
                'O' => 'price',
                'P' => 'discount amount',
                'Q' => 'shipping fee',
                'R' => 'service fee',
                'S' => 'force update',
                'T' => 'Shipping code',
								'U' => 'Hold',
								'V' => 'Remark',
								'W' => 'Carrier',
								'X' => 'Warehouse code',
								'Y' => 'Country'
              );

              foreach($headCol as $col => $field)
              {
                if($rs[$col] !== $field)
                {
                  $sc = FALSE;
                  $message = 'Column '.$col.' Should be '.$field;
                  break;
                }
              }

              if($sc === FALSE)
              {
                break;
              }
            }
            else if(!empty($rs['A']))
            {
							//--- check ref_code
							if($ref_code != $rs['I'])
							{
								//--- check ref_code exists
	              $date = PHPExcel_Style_NumberFormat::toFormattedString($rs['J'], 'YYYY-MM-DD');
	              $date_add = db_date($date, TRUE);

	              //---- order code from web site
	              $ref_code = $rs['I'];

	              //--- shipping Number
	              $shipping_code = $rs['T'];

	              //---- กำหนดช่องทางการขายเป็นรหัส
	              $channels = $this->channels_model->get($rs['L']);


	              //--- หากไม่ระบุช่องทางขายมา หรือ ช่องทางขายไม่ถูกต้องใช้ default
	              if(empty($channels))
	              {
	                $channels = $this->channels_model->get_default();
	              }

	              //--- กำหนดช่องทางการชำระเงิน
	              $payment = $this->payment_methods_model->get($rs['K']);

	              if(empty($payment))
	              {
	                $payment = $this->payment_methods_model->get_default();
	              }

								//-- remark
								$remark = $rs['V'];

								$order_code  = $this->orders_model->get_active_order_code_by_reference($ref_code);

	              $is_exists = empty($order_code) ? FALSE : TRUE;

							}
							else
							{
								$is_exists = TRUE;
							}


              //------ เช็คว่ามีออเดอร์นี้อยู่ในฐานข้อมูลแล้วหรือยัง
              //------ ถ้ามีแล้วจะได้ order_code กลับมา ถ้ายังจะได้ FALSE;

              if(empty($order_code) OR ($order_code != $orderCode))
              {
								if($this->isAPI && $isWMS == 1 && !empty($orderCode) && $hold === FALSE)
								{
									if(!$this->wms_order_api->export_order($orderCode))
									{
										$arr = array(
											'wms_export' => 3,
											'wms_export_error' => $this->wms_order_api->error
										);

										$this->orders_model->update($orderCode, $arr);
									}
									else
									{
										$arr = array(
											'wms_export' => 1,
											'wms_export_error' => NULL
										);

										$this->orders_model->update($orderCode, $arr);
									}
								}

								if(empty($order_code))
								{
									$order_code = $this->get_new_code($date_add);
								}
              }


              //-- state ของออเดอร์ จะมีการเปลี่ยนแปลงอีกที
              $state = empty($rs['U']) ? 3 : 1;

              //---- ถ้ายังไม่มีออเดอร์ ให้เพิ่มใหม่ หรือ มีออเดอร์แล้ว แต่ต้องการ update
              //---- โดยการใส่ force update มาเป็น 1
              if($is_exists === FALSE OR $rs['S'] == 1)
              {
                //---- รหัสลูกค้าจะมีการเปลี่ยนแปลงตามเงื่อนไขด้านล่างนี้
                $customer_code = empty($rs['Z']) ? 'NULL' : trim($rs['Z']);
                //---- ตรวจสอบว่าช่องทางขายที่กำหนดมา เป็นเว็บไซต์หรือไม่(เพราะจะมีช่องทางการชำระเงินหลายช่องทาง)

                $customer = $this->customers_model->get($customer_code);

                if(empty($customer))
                {
                  if($channels->code === $web_channels)
                  {
                    if($payment->code === '2C2P')
                    {
                      //---- กำหนดรหัสลูกค้าตามค่าที่ config สำหรับเว็บไซต์ที่ชำระโดยบัตรเครดติ(2c2p)
                      $customer_code = $web_customer_2c2p;
                    }
                    else if($payment->code === 'COD')
                    {
                      //---- กำหนดรหัสลูกค้าตามค่าที่ config สำหรับเว็บไซต์ที่ชำระแบบ COD
                      $customer_code = $web_customer_cod;
                    }
                    else
                    {
                      $customer_code = $channels->customer_code;
                    }
                  }
                  else
                  {
                    //--- หากไม่ใช่ช่องทางเว็บไซต์
                    //--- กำหนดรหัสลูกค้าตามช่องทางขายที่ได้ผูกไว้
                    //--- หากไม่มีการผูกไว้ให้
                    $customer_code = empty($channels->customer_code) ? $default_customer : $channels->customer_code;
                  }

                  $customer = $this->customers_model->get($customer_code);
                }


              	//---	ถ้าเป็นออเดอร์ขาย จะมี id_sale
              	$sale_code = $customer->sale_code;

              	//---	หากเป็นออนไลน์ ลูกค้าออนไลน์ชื่ออะไร
              	$customer_ref = addslashes(trim($rs['A']));

                //---	ช่องทางการชำระเงิน
                $payment_code = $payment->code;

                //---	ช่องทางการขาย
                $channels_code = $channels->code;

              	// //---	วันที่เอกสาร
              	// $date = PHPExcel_Style_NumberFormat::toFormattedString($rs['J'], 'YYYY-MM-DD');
                // $date_add = db_date($date_add, TRUE);

                //--- ค่าจัดส่ง
                $shipping_fee = empty($rs['Q']) ? 0.00 : $rs['Q'];

                //--- ค่าบริการอื่นๆ
                $service_fee = 0; //empty($rs['R']) ? 0.00 : $rs['R'];

								//--- กำหนดรหัสคลังมาหรือไม่ ถ้าไม่กำหนดมาให้ใช้ค่าตามที่ config ไว้
								$xWh = empty($rs['X']) ? NULL : $this->warehouse_model->get(trim($rs['X']));

                //---- กรณียังไม่มีออเดอร์
                if($is_exists === FALSE)
                {
                  //--- เตรียมข้อมูลสำหรับเพิ่มเอกสารใหม่
                  $ds = array(
                    'code' => $order_code,
                    'role' => $role,
                    'bookcode' => $bookcode,
                    'reference' => $ref_code,
                    'customer_code' => $customer_code,
                    'customer_ref' => $customer_ref,
                    'channels_code' => $channels_code,
                    'payment_code' => $payment_code,
                    'sale_code' => $sale_code,
                    'state' => $state,
                    'is_paid' => 0,
                    'is_term' => $payment->has_term,
                    'shipping_code' => $shipping_code,
                    'shipping_fee' => 0,
                    'status' => 1,
                    'date_add' => $date_add,
                    'warehouse_code' => (!empty($xWh) ? $xWh->code : $warehouse_code),
                    'user' => $this->_user->uname,
                    'is_import' => 1,
										'remark' => $remark,
										'is_wms' => (!empty($xWh) ? $xWh->is_wms : $is_wms),
										'id_sender' => empty($rs['W']) ? NULL : $this->sender_model->get_id($rs['W'])
                  );

                  //--- เพิ่มเอกสาร
                  if($this->orders_model->add($ds) === TRUE)
                  {
										$orderCode = $order_code;
										$hold = $state === 3 ? FALSE : TRUE;
                    $isWMS = (!empty($xWh) ? $xWh->is_wms : $is_wms);

                    $arr = array(
                      'order_code' => $order_code,
                      'state' => $state,
                      'update_user' => $this->_user->uname
                    );
                    //--- add state event
                    $this->order_state_model->add_state($arr);

                    $id_address = $this->address_model->get_id($customer_ref, trim($rs['B']));

                    if($id_address === FALSE)
                    {
                      $arr = array(
                        'code' => $customer_ref,
                        'name' => $customer_ref,
                        'address' => trim($rs['B']),
                        'sub_district' => trim($rs['E']),
                        'district' => trim($rs['D']),
                        'province' => trim($rs['C']),
                        'postcode' => trim($rs['F']),
                        'phone' => trim($rs['H']),
												'country' => empty(trim($rs['Y'])) ? "Thailand" : trim($rs['Y']),
                        'alias' => 'Home',
                        'is_default' => 1
                      );

                      $id_address = $this->address_model->add_shipping_address($arr);
                    }

										$this->orders_model->set_address_id($order_code, $id_address);

                    $import++;
                  }
                  else
                  {
                    $sc = FALSE;
                    $message = $ref_code.': เพิ่มออเดอร์ไม่สำเร็จ';
                  }
                }
                else
                {
                  $order = $this->orders_model->get($order_code);
                  if($order->state <= 3)
                  {
                    //--- เตรียมข้อมูลสำหรับเพิ่มเอกสารใหม่
                    $ds = array(
                      'customer_code' => $customer_code,
                      'customer_ref' => $customer_ref,
                      'channels_code' => $channels_code,
                      'payment_code' => $payment_code,
                      'sale_code' => $sale_code,
                      'state' => $state,
                      'is_term' => $payment->has_term,
                      'date_add' => $date_add,
                      'user' => $this->_user->uname
                    );

                    $this->orders_model->update($order_code, $ds);
                  }

                  $import++;
                }
              }


              //---- เตรียมข้อมูลสำหรับเพิมรายละเอียดออเดอร์
              $item = $this->products_model->get_with_old_code(trim($rs['M']));

              if(empty($item))
              {
                $sc = FALSE;
                $message = 'ไม่พบข้อมูลสินค้าในระบบ : '.$rs['M'];
                break;
              }
              else if($item->active != 1)
              {
                $sc = FALSE;
                $message = 'สินค้าถูก Disactive : '.$rs['M'];
                break;
              }


              $qty = $rs['N'] == '' ? 1 : str_replace(',', '', $rs['N']);

              //--- ราคา (เอาราคาที่ใส่มา / จำนวน + ส่วนลดต่อชิ้น)
              $price = empty($rs['O']) ? 0.00 : str_replace(",", "", $rs['O']); //--- ราคารวมไม่หักส่วนลด
              $price = $price > 0 ? ($price/$qty) : 0; //--- ราคาต่อชิ้น



              //--- ส่วนลด (รวม)
              $discount_amount = empty($rs['P']) ? 0.00 : str_replace(',', '', $rs['P']);

              //--- ส่วนลด (ต่อชิ้น)
              $discount = $discount_amount > 0 ? ($discount_amount / $qty) : 0;



              //--- total_amount
              $total_amount = ($price * $qty) - $discount_amount;

              //---- เช็คข้อมูล ว่ามีรายละเอียดนี้อยู่ในออเดอร์แล้วหรือยัง
              //---- ถ้ามีข้อมูลอยู่แล้ว (TRUE)ให้ข้ามการนำเข้ารายการนี้ไป
              if($this->orders_model->is_exists_detail($order_code, $item->code) === FALSE)
              {
                //--- ถ้ายังไม่มีรายการอยู่ เพิ่มใหม่
                $arr = array(
                  "order_code"	=> $order_code,
                  "style_code"		=> $item->style_code,
                  "product_code"	=> $item->code,
                  "product_name"	=> $item->name,
                  "cost"  => $item->cost,
                  "price"	=> $price,
                  "qty"		=> $qty,
                  "discount1"	=> $discount,
                  "discount2" => 0,
                  "discount3" => 0,
                  "discount_amount" => $discount_amount,
                  "total_amount"	=> round($total_amount,2),
                  "id_rule"	=> NULL,
                  "is_count" => $item->count_stock,
                  "is_import" => 1
                );

                if( $this->orders_model->add_detail($arr) === FALSE )
                {
                  $sc = FALSE;
                  $message = 'เพิ่มรายละเอียดรายการไม่สำเร็จ : '.$ref_code;
                  break;
                }
								else
								{
									if($this->sync_chatbot_stock && ($warehouse_code == $chatbot_warehouse_code))
									{
										if($item->count_stock && $item->is_api)
										{
											$sync_items[] = $item->code;
										}
									}
								}

              }
              else
              {
                //----  ถ้ามี force update และ สถานะออเดอร์ไม่เกิน 3 (รอจัดสินค้า)
                if($rs['S'] == 1 && $state <= 3)
                {
                  $row_id  = $this->orders_model->get_detail_id($order_code, $item->code);

                  $arr = array(
                    "style_code"		=> $item->style_code,
                    "product_code"	=> $item->code,
                    "product_name"	=> $item->name,
                    "cost"  => $item->cost,
                    "price"	=> $price,
                    "qty"		=> $qty,
                    "discount1"	=> $discount,
                    "discount2" => 0,
                    "discount3" => 0,
                    "discount_amount" => $discount_amount,
                    "total_amount"	=> round($total_amount,2),
                    "id_rule"	=> NULL,
                    "is_count" => $item->count_stock,
                    "is_import" => 1
                  );

                  if($this->orders_model->update_detail($row_id, $arr) === FALSE)
                  {
                    $sc = FALSE;
                    $message = 'เพิ่มรายละเอียดรายการไม่สำเร็จ : '.$ref_code;
                    break;
                  }
									else
									{
										if($this->sync_chatbot_stock && ($warehouse_code == $chatbot_warehouse_code))
										{
											if($item->count_stock && $item->is_api)
											{
												$sync_items[] = $item->code;
											}
										}
									}
                } //--- enf force update
              } //--- end if exists detail


              //----- ใส่รหัสค่าจัดส่งสินค้า
              if(!empty($shipping_fee) && !empty($shipping_item))
              {
                //---- เช็คข้อมูล ว่ามีรายละเอียดนี้อยู่ในออเดอร์แล้วหรือยัง
                //---- ถ้ามีข้อมูลอยู่แล้ว (TRUE)ให้ข้ามการนำเข้ารายการนี้ไป
                //---- เพิ่มรายได้ค่าจัดส่ง
                if($shipping_added != $order_code )
                {
                  $shipping_exists = $this->orders_model->is_exists_detail($order_code, $shipping_item->code);
                  if($shipping_exists === FALSE)
                  {
                    //--- ถ้ายังไม่มีรายการอยู่ เพิ่มใหม่
                    $arr = array(
                      "order_code"	=> $order_code,
                      "style_code"		=> $shipping_item->style_code,
                      "product_code"	=> $shipping_item->code,
                      "product_name"	=> $shipping_item->name,
                      "cost"  => $shipping_item->cost,
                      "price"	=> $shipping_fee,
                      "qty"		=> 1,
                      "discount1"	=> 0,
                      "discount2" => 0,
                      "discount3" => 0,
                      "discount_amount" => 0,
                      "total_amount"	=> $shipping_fee,
                      "id_rule"	=> NULL,
                      "is_count" => $shipping_item->count_stock,
                      "is_import" => 1
                    );

                    if( $this->orders_model->add_detail($arr) === FALSE )
                    {
                      $sc = FALSE;
                      $message = 'เพิ่มรายการ รายได้ค่าจัดส่ง ไม่สำเร็จ : '.$ref_code;
                      break;
                    }
                    else
                    {
                      $shipping_added = $order_code;
                    }
                  }
                  else
                  {
                    if($rs['S'] == 1)
                    {
                      $row_id  = $this->orders_model->get_detail_id($order_code, $shipping_item->code);
                      $arr = array(
                        "price"	=> $shipping_fee
                      );

                      if($this->orders_model->update_detail($row_id, $arr) === FALSE)
                      {
                        $sc = FALSE;
                        $message = 'update ค่าจัดส่ง ไม่สำเร็จ : '.$ref_code;
                        break;
                      }
                      else
                      {
                        $shipping_added = $order_code;
                      }
                    }
                  }

                } //--- end if shipping_added

              } //--- end shipping_fee = 0

							$orderCode = $order_code;

            } //--- end header column

          } //--- end foreach

					if($this->isAPI && $isWMS == 1 && !empty($orderCode) && $hold === FALSE)
					{
						if(!$this->wms_order_api->export_order($orderCode))
						{
							$arr = array(
								'wms_export' => 3,
								'wms_export_error' => $this->wms_order_api->error
							);

							$this->orders_model->update($orderCode, $arr);
						}
						else
						{
							$arr = array(
								'wms_export' => 1,
								'wms_export_error' => NULL
							);

							$this->orders_model->update($orderCode, $arr);
						}
					}

					//--- sync chatbot stock
					if($this->sync_chatbot_stock && ($warehouse_code == $chatbot_warehouse_code))
					{
						if(!empty($sync_items))
						{
							$sync_stock = array_unique($sync_items);
							$this->update_chatbot_stock($sync_stock);
						}
					}
        }
        else
        {
          $sc = FALSE;
          $message = "ไฟล์มีจำนวนรายการเกิน {$limit} บรรทัด";
        }
    } //-- end import success

    echo $sc === TRUE ? 'success' : $message;
  }


	public function get_sell_stock($item_code, $warehouse = NULL, $zone = NULL)
  {
    $sell_stock = $this->stock_model->get_sell_stock($item_code, $warehouse, $zone);
    $reserv_stock = $this->orders_model->get_reserv_stock($item_code, $warehouse, $zone);
    $availableStock = $sell_stock - $reserv_stock;
		return $availableStock < 0 ? 0 : $availableStock;
  }

	public function update_chatbot_stock(array $ds = array())
  {
    if($this->sync_chatbot_stock && !empty($ds))
    {
			$this->logs = $this->load->database('logs', TRUE);
      $this->load->library('chatbot_api');
      $this->chatbot_api->sync_stock($ds);
    }
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
