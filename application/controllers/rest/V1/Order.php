<?php
require(APPPATH.'/libraries/REST_Controller.php');
use Restserver\Libraries\REST_Controller;

class Order extends REST_Controller
{
  public $error;
  public $user;
  public $ms;
	public $path;
	public $isAPI;
	public $wms;
	public $logs;
	public $log_json = FALSE;
	public $sync_chatbot_stock = FALSE;
	public $api = FALSE;

  public function __construct()
  {
    parent::__construct();
		$this->api = is_true(getConfig('CHATBOT_API'));

		if($this->api)
		{
			$this->logs = $this->load->database('logs', TRUE); //--- api logs database
			$this->ms = $this->load->database('ms', TRUE);
			$this->path = $this->config->item('image_file_path')."payments/";
			$this->isAPI = is_true(getConfig('WMS_API'));

	    $this->load->model('orders/orders_model');
	    $this->load->model('orders/order_state_model');
	    $this->load->model('masters/products_model');
	    $this->load->model('masters/customers_model');
	    $this->load->model('masters/channels_model');
			$this->load->model('masters/sender_model');
	    $this->load->model('masters/payment_methods_model');
			$this->load->model('masters/warehouse_model');
	    $this->load->model('address/address_model');

			$this->load->model('rest/V1/order_api_logs_model');
			$this->load->helper('sender');

	    $this->user = 'api@chatbot';
			$this->log_json = is_true(getConfig('CHATBOT_LOG_JSON'));
			$this->sync_chatbot_stock = is_true(getConfig('SYNC_CHATBOT_STOCK'));
		}
		else
		{
			$arr = array(
				'status' => FALSE,
				'error' => "Access denied"
			);

			$this->response($arr, 400);
		}
  }


	public function upload_post()
  {
    //--- Get raw post data
    $data = json_decode(file_get_contents("php://input"));

    if(!empty($data))
    {
      $img = explode(',', $data->image_data);
			$count = count($img);
			if($count == 1)
			{
				$imageData = base64_decode($img[0]);
			}
			else
			{
				$imageData = base64_decode($img[1]);
			}

			$source = imagecreatefromstring($imageData);
			$name = "{$this->path}WO-21060001.jpg";
			$save = imagejpeg($source, $name, 100);

			$this->response($save, 200);
    }

    //$this->response($data, 200);
  }


  public function status_get($code)
  {
    if(empty($code))
    {
      $arr = array(
        'status' => FALSE,
        'error' => "Order Number is required"
      );

      $this->response($arr, 400);
    }

    $state = $this->orders_model->get_state($code);

    if(empty($state))
    {
      $arr = array(
        'status' => FALSE,
        'error' => "Invalid Order Number"
      );

      $this->response($arr, 400);
    }
    else
    {
      //---- status name
      $state_name = array(
        '1' => 'pending',
        '2' => 'payment verify',
        '3' => 'in progress',
        '4' => 'in progress',
        '5' => 'in progress',
        '6' => 'in progress',
        '7' => 'packed',
        '8' => 'shipped',
        '9' => 'canceled'
      );

      $arr = array(
        'status' => $state_name[$state]
      );

      $this->response($arr, 200);
    }

  }




  public function create_post()
  {
    //--- Get raw post data
		$json = file_get_contents("php://input");

    $data = json_decode($json);

		$sync_stock = array();

    if(empty($data))
    {
      $arr = array(
        'status' => FALSE,
        'error' => 'empty data'
      );
      $this->response($arr, 400);
    }

		if(! property_exists($data, 'order_number') OR $data->order_number == '')
    {
			$this->error = 'order_number is required';
			$this->order_api_logs_model->logs("", "E", $this->error);

			$arr = array(
        'status' => FALSE,
        'error' => $this->error
      );

      $this->response($arr, 400);
    }

		$sc = $this->verify_data($data);

		//---- if any error return
    if($sc === FALSE)
    {
			$this->order_api_logs_model->logs($data->order_number, "E", $this->error);
      $arr = array(
        'status' => FALSE,
        'error' => $this->error
      );

      $this->response($arr, 400);
    }


    //--- check each item code
    $details = $data->details;

    if(empty($details))
    {
			$sc = FALSE;
			$this->error = "Items not found";
			$log = array(
				'code' => $data->order_number,
				'status' => 'E',
				'error_message' => $this->error,
				'json_text' => ($this->log_json ? $json : NULL)
			);

			$this->order_api_logs_model->logs_order($log);

      $arr = array(
        'status' => FALSE,
        'error' => $this->error
      );

      $this->response($arr, 400);
    }


    if(!empty($details))
    {
      foreach($details as $rs)
      {
        if($sc === FALSE)
        {
          break;
        }

        //---- check valid items
        $item = $this->products_model->get($rs->item);

        if(empty($item))
        {
          $sc = FALSE;
          $this->error = "Invalid SKU : {$rs->item}";
        }
				else
				{
					$rs->item = $item;
				}
      }
    }


    //---- if any error return
    if($sc === FALSE)
    {
			$log = array(
				'code' => $data->order_number,
				'status' => 'E',
				'error_message' => $this->error,
				'json_text' => ($this->log_json ? $json : NULL)
			);

			$this->order_api_logs_model->logs_order($log);

      $arr = array(
        'status' => FALSE,
        'error' => $this->error
      );

      $this->response($arr, 400);
    }

    //---- new code start
    if($sc === TRUE)
    {
      //--- รหัสเล่มเอกสาร [อ้างอิงจาก SAP]
      //--- ถ้าเป็นฝากขายแบบโอนคลัง ยืมสินค้า เบิกแปรสภาพ เบิกสินค้า (ไม่เปิดใบกำกับ เปิดใบโอนคลังแทน) นอกนั้น เปิด SO
      $bookcode = getConfig('BOOK_CODE_ORDER');

      $role = 'S';

      $date_add = date('Y-m-d H:i:s');

      //---- order code from chatbot
      $ref_code = $data->order_number;

      $customer = $this->customers_model->get($data->customer_code);

			$sale_code = empty($customer) ? -1 : $customer->sale_code;

			$state = $data->payment_status == "paid" ? 3 :($data->payment_status == "transferred" ? 2 : 1);

			$warehouse_code = getConfig('CHATBOT_WAREHOUSE_CODE');

			$warehouse = $this->warehouse_model->get($warehouse_code);

			$is_wms = empty($warehouse) ? 0 : $warehouse->is_wms;

			//---- id_sender
			$sender = $this->sender_model->get_id($data->shipping_method);

			$id_sender = empty($sender) ? NULL : $sender;

      //--- order code gen จากระบบ
      $order_code = $this->get_new_code($date_add);

			$tracking = get_tracking($id_sender, $order_code);

			$total_amount = 0;

      //--- เตรียมข้อมูลสำหรับเพิ่มเอกสารใหม่
      $ds = array(
        'code' => $order_code,
        'role' => $role,
        'bookcode' => $bookcode,
        'reference' => $data->order_number,
        'customer_code' => $data->customer_code,
        'customer_ref' => $data->customer_ref,
        'channels_code' => $data->channels,
        'payment_code' => $data->payment_method,
        'sale_code' => $sale_code,
        'state' => 1,
        'is_paid' => $state === 3 ? 1 : 0,
        'is_term' => $data->payment_method === "COD" ? 1 : 0,
        'status' => 1,
				'shipping_code' => $tracking,
        'user' => $this->user,
        'date_add' => $date_add,
        'warehouse_code' => $warehouse_code,
        'is_api' => 1,
				'id_sender' => $id_sender,
				'is_wms' => $is_wms
      );

    $this->db->trans_begin();

    $rs = $this->orders_model->add($ds);

    if(!$rs)
    {
      $sc = FALSE;
      $this->error = "Order create failed";
    }
    else
    {
			$arr = array(
				'order_code' => $order_code,
				'state' => 1,
				'update_user' => $this->user
			);

      //--- add state event
      $this->order_state_model->add_state($arr);

      $id_address = $this->address_model->get_id($data->customer_ref, $data->ship_to->address);

      if($id_address === FALSE)
      {
        $arr = array(
          'code' => $data->customer_ref,
          'name' => $data->ship_to->name,
          'address' => $data->ship_to->address,
          'sub_district' => $data->ship_to->sub_district,
          'district' => $data->ship_to->district,
          'province' => $data->ship_to->province,
          'postcode' => $data->ship_to->postcode,
          'phone' => $data->ship_to->phone,
					'email' => $data->ship_to->email,
          'alias' => empty($data->alias) ? 'Home' : $data->alias,
          'is_default' => 1
        );

        $id_address = $this->address_model->add_shipping_address($arr);
      }

      $this->orders_model->set_address_id($order_code, $id_address);

      //---- add order details
      $details = $data->details;

      if(! empty($details))
      {
        foreach($details as $rs)
        {
					if($sc === FALSE)
					{
						break;
					}

					if(!empty($rs->item))
					{
						//--- check item code
	          $item = $rs->item;

						//--- ถ้ายังไม่มีรายการอยู่ เพิ่มใหม่
						$arr = array(
							"order_code"	=> $order_code,
							"style_code"		=> $item->style_code,
							"product_code"	=> $item->code,
							"product_name"	=> $item->name,
							"cost"  => $item->cost,
							"price"	=> $rs->price,
							"qty"		=> $rs->qty,
							"discount1"	=> 0,
							"discount2" => 0,
							"discount3" => 0,
							"discount_amount" => 0,
							"total_amount"	=> round($rs->amount,2),
							"id_rule"	=> NULL,
							"is_count" => $item->count_stock,
							"is_api" => 1
						);

						if(!$this->orders_model->add_detail($arr))
						{
							$sc = FALSE;
							$this->error = "Order item insert failed : {$item->code}";
							break;
						}
						else
						{
							$total_amount += round($rs->amount, 2);
							if($this->sync_chatbot_stock && $item->count_stock && $item->is_api)
							{
								$sync_stock[] = $item->code;
							}
						}

					} //--- end if item
        }  //--- endforeach add details


				//---- แนบสลิป
				if($state == 2)
				{
					//--- if has pay slip
					if(!empty($data->payslip))
					{
						$img = explode(',', $data->payslip);
						if(count($img) == 1)
						{
							$imageData = base64_decode($img[0]);
						}
						else
						{
							$imageData = base64_decode($img[1]);
						}

						$source = imagecreatefromstring($imageData);
						$name = "{$this->path}{$order_code}.jpg";
						$save = imagejpeg($source, $name, 100);
						imagedestroy($source);
					}

					//---- create payment
					$this->load->model('masters/bank_model');
		      $this->load->model('orders/order_payment_model');

					$pay_date = now();

					if(!empty($data->payment_date_time))
					{
						$pay_date = date('Y-m-d H:i:s', strtotime($data->payment_date_time));
					}

					if(!empty($data->account_no))
					{
						$id_account = $this->bank_model->get_id($data->account_no);

						if(!empty($id_account))
						{
							$arr = array(
				        'order_code' => $order_code,
				        'order_amount' => $total_amount,
				        'pay_amount' => $total_amount,
				        'pay_date' => $pay_date,
				        'id_account' => $id_account,
				        'acc_no' => $data->account_no,
				        'user' =>$this->user
				      );

							if(!$this->order_payment_model->add($arr))
							{
								$sc = FALSE;
								$this->error = "Insert Payment data failed";
							}
							else
							{
								if(! $this->orders_model->change_state($order_code, 2)) //--- แจ้งชำระเงิน
								{
									$sc = FALSE;
									$this->error = "Cannot change order state to payment_verify";
								}
								else
								{
									//--- add state
									$arr = array(
			              'order_code' => $order_code,
			              'state' => 2,
			              'update_user' => $this->user
			            );

									$this->order_state_model->add_state($arr);
								}
							}
						}
						else
						{
							$sc = FALSE;
							$this->error = "Invalid account no";
						}

					}
					else
					{
						$sc = FALSE;
						$this->error = "Account no is required";
					}
				} //--- end state == 2 case แนบสลิป

				//--- if paid
				if($state == 3)
				{
					if($this->orders_model->change_state($order_code, 3))
					{
						$arr = array(
							'order_code' => $order_code,
							'state' => 3,
							'update_user' => $this->user
						);

						$this->order_state_model->add_state($arr);
					}

					if($this->isAPI && $is_wms)
					{
						$this->wms = $this->load->database('wms', TRUE);
						$this->load->library('wms_order_api');
						$ex = $this->wms_order_api->export_order($order_code);

						if(! $ex)
						{
							$arr = array(
								'wms_export' => 3,
								'wms_export_error' => $this->wms_order_api->error
							);

							$this->orders_model->update($order_code, $arr);
						}
						else
						{
							$arr = array(
								'wms_export' => 1,
								'wms_export_error' => NULL
							);

							$this->orders_model->update($order_code, $arr);
						}
					}
				}

				if($this->sync_chatbot_stock && !empty($sync_stock))
				{
					$this->load->library('chatbot_api');
					$this->chatbot_api->sync_stock($sync_stock);
				}
      }
      else
      {
        $sc = FALSE;
        $this->error = "Items not found";
      }
    }

    if($sc === TRUE)
    {
      $this->db->trans_commit();

			$log = array(
				'code' => $data->order_number,
				'status' => 'S',
				'error_message' => NULL,
				'json_text' => ($this->log_json ? $json : NULL)
			);

			$this->order_api_logs_model->logs_order($log);

      $arr = array(
        'status' => 'success',
        'order_code' => $order_code,
				'tracking_no' => $tracking
      );

      $this->response($arr, 200);
    }
    else
    {
      $this->db->trans_rollback();

			$log = array(
				'code' => $data->order_number,
				'status' => 'E',
				'error_message' => $this->error,
				'json_text' => ($this->log_json ? $json : NULL)
			);

			$this->order_api_logs_model->logs_order($log);

      $arr = array(
        'status' => FALSE,
        'error' => $this->error
      );

      $this->response($arr, 200);
    }
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



  public function verify_data($data)
	{
    if(! property_exists($data, 'customer_code') OR $data->customer_code == '')
    {
      $this->error = 'customer_code is required';
			return FALSE;
    }


		if(! property_exists($data, 'customer_ref') OR $data->customer_ref == '')
		{
			$this->error = "customer_ref is required";
			return FALSE;
		}

    if(! property_exists($data, 'channels') OR ($data->channels != 'Line@' && $data->channels != 'PAGE'))
    {
      $this->error = "Invalid channels code : {$data->channels}";
			return FALSE;
    }

    if(! property_exists($data, 'payment_method') OR ($data->payment_method != "TRANSFER" && $data->payment_method != "COD" && $data->payment_method != '2C2P'))
    {
      $this->error = 'Invalic payment_method code';
			return FALSE;
    }


		if(! property_exists($data, 'payment_status') OR ($data->payment_status != "pending" && $data->payment_status != "transferred" && $data->payment_status != "paid"))
    {
      $this->error = 'Invalid payment status';
			return FALSE;
    }


		if(!empty($data->customer_code))
		{
			$customer_code = "";

			if($data->channels == "Line@" && $data->payment_method == "TRANSFER")
			{
				$customer_code = "CLON02-0001";
			}

			if($data->channels == "Line@" && $data->payment_method == "COD")
			{
				$customer_code = "CLON02-0002";
			}

			if($data->channels == "Line@" && $data->payment_method == "2C2P")
			{
				$customer_code = "CLON03-0002";
			}

			if($data->channels == "PAGE" && $data->payment_method == "TRANSFER")
			{
				$customer_code = "CLON05-0001";
			}

			if($data->channels == "PAGE" && $data->payment_method == "COD")
			{
				$customer_code = "CLON05-0002";
			}

			if($data->channels == "PAGE" && $data->payment_method == "2C2P")
			{
				$customer_code = "CLON03-0002";
			}

			if($data->customer_code != $customer_code)
			{
				$this->error = "Invalid Customer Code";
				return FALSE;
			}
		}


		if(! property_exists($data, 'shipping_method'))
		{
			$this->error = "Invalid Shipping Code";
			return FALSE;
		}

		if(! property_exists($data, 'user_code') OR empty($data->user_code))
		{
			$this->error = "Invalid User";
			return FALSE;
		}

    if(! property_exists($data, 'ship_to'))
    {
      $this->error = 'missing shipping address';
			return FALSE;
    }

    if(! property_exists($data->ship_to, 'name'))
    {
      $this->error = 'missing shipping address';
			return FALSE;
    }

    if(! property_exists($data->ship_to, 'address'))
    {
      $this->error = 'missing shipping address';
			return FALSE;
    }

    if(! property_exists($data->ship_to, 'district'))
    {
      $this->error = 'district is required';
			return FALSE;
    }


    if(! property_exists($data->ship_to, 'province'))
    {
      $this->error = 'province is required';
			return FALSE;
    }

    if(! property_exists($data->ship_to, 'phone'))
    {
      $this->error = 'phone is required';
			return FALSE;
    }


    if($this->orders_model->is_active_order_reference($data->order_number) !== FALSE)
    {
      $this->error = 'Order number already exists';
			return FALSE;
    }


		return TRUE;
	}


} //--- end class
