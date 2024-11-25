<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Shop extends PS_Controller
{
  public $menu_code = 'DBPOSS';
	public $menu_group_code = 'DB';
  public $menu_sub_group_code = 'POS';
	public $title = 'เพิ่ม/แก้ไข จุดขาย';

  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'masters/shop';
    $this->load->model('masters/shop_model');
    $this->load->helper('payment_method');
    $this->load->helper('channels');
  }

  public function index()
  {
    $filter = array(
      'code' => get_filter('code', 'shop_code', ''),
			'name' => get_filter('name', 'shop_name', ''),
			'zone' => get_filter('zone', 'shop_zone', ''),
			'status' => get_filter('status', 'shop_status', 'all')
    );

		//--- แสดงผลกี่รายการต่อหน้า
		$perpage = get_rows();
		//--- หาก user กำหนดการแสดงผลมามากเกินไป จำกัดไว้แค่ 300
		if($perpage > 300)
		{
			$perpage = 20;
		}

		$segment  = 4; //-- url segment
		$rows     = $this->shop_model->count_rows($filter);
		//--- ส่งตัวแปรเข้าไป 4 ตัว base_url ,  total_row , perpage = 20, segment = 3
		$init = pagination_config($this->home.'/index/', $rows, $perpage, $segment);
		$list = $this->shop_model->get_list($filter, $perpage, $this->uri->segment($segment));

    $filter['list'] = $list;

		$this->pagination->initialize($init);
    $this->load->view('masters/shop/shop_list', $filter);
  }



	public function add_new()
	{
		$this->load->view('masters/shop/shop_add');

	}


	public function add()
	{
		$sc = TRUE;

		if($this->pm->can_add)
		{
      $shop = json_decode($this->input->post('data'));

			if( ! empty($shop) && ! empty($shop->name) && ! empty($shop->zone_code))
			{
        if($this->shop_model->is_exists_code(trim($shop->code)))
        {
          $sc = FALSE;
          $this->error = "รหัสซ้ำ กรุณากำหนดรหัสจุดขายใหม่";
        }

        if($sc === TRUE && $this->shop_model->is_exists_name(trim($shop->name)))
        {
          $sc = FALSE;
          $this->error = "ชื่อซ้ำ กรุณากำหนดชื่อจุดขายใหม่";
        }

        if($sc === TRUE && $this->shop_model->is_exists_zone(trim($shop->zone_code)))
        {
          $sc = FALSE;
          $this->error = "โซนซ้ำ โซนนี้ถูกใช้งานแล้ว กรุณากำหนดโซนอื่น";
        }

        if($sc === TRUE && empty($shop->channels))
        {
          $sc = FALSE;
          $this->error = "กรุณาระบุช่องทางขาย";
        }

        if($sc === TRUE && empty($shop->cash_payment))
        {
          $sc = FALSE;
          $this->error = "กรุณาระบุช่องทางการชำระเงินด้วยเงินสด";
        }

        if($sc === TRUE && $this->shop_model->is_exists_prefix(trim($shop->prefix)))
        {
          $sc = FALSE;
          $this->error = "Prefix '{$shop->prefix}' มีในระบบแล้ว";
        }


        if($sc === TRUE)
        {
          $arr = array(
            'code' => trim($shop->code),
            'name' => trim($shop->name),
            'zone_code' => trim($shop->zone_code),
            'customer_code' => $shop->customer_code,
            'prefix' => trim($shop->prefix),
            'running' => $shop->running,
            'channels_code' => $shop->channels,
            'cash_payment' => $shop->cash_payment,
            'transfer_payment' => get_null($shop->transfer_payment),
            'card_payment' => get_null($shop->card_payment),
            'bill_header_1' => get_null(trim($shop->bill_header_1)),
            'bill_header_2' => get_null(trim($shop->bill_header_2)),
            'bill_header_3' => get_null(trim($shop->bill_header_3)),
            'header_size_1' => empty(trim($shop->header_size_1)) ? 14 : trim($shop->header_size_1),
            'header_size_2' => empty(trim($shop->header_size_2)) ? 14 : trim($shop->header_size_2),
            'header_size_3' => empty(trim($shop->header_size_3)) ? 14 : trim($shop->header_size_3),
            'header_align_1' => $shop->header_align_1,
            'header_align_2' => $shop->header_align_2,
            'header_align_3' => $shop->header_align_3,
            'bill_footer' => get_null(trim($shop->bill_footer)),
            'footer_size' => empty(trim($shop->footer_size)) ? 14 : trim($shop->footer_size),
            'font_size' => $shop->font_size,
            'use_vat' => $shop->use_vat,
            'tax_id' => get_null(trim($shop->tax_id)),
            'active' => $shop->active == 1 ? 1 : 0,
            'barcode' => $shop->barcode == 1 ? 1 : 0
          );


          $shop_id = $this->shop_model->add($arr);

          if($shop_id === FALSE)
          {
            $sc = FALSE;
            $error = $this->db->error();
            $this->error = $error['message'];
          }
        }
			}
			else
			{
				$sc = FALSE;
				$this->error = "Missing required parameter";
			}
		}
		else
		{
			$sc = FALSE;
			set_error('permission');
		}

		$this->_response($sc);
	}




	public function edit($id)
	{
		if($this->pm->can_edit)
		{
			$shop = $this->shop_model->get_by_id($id);

			if(!empty($shop))
			{
				$ds = array(
					'shop' => $shop,
					'users' => $this->shop_model->get_shop_user($shop->id),
          'payments' => $this->shop_model->get_shop_payments($shop->id)
				);

				$this->load->view('masters/shop/shop_edit', $ds);
			}
			else
			{
				$this->page_error();
			}
		}
		else
		{
			$this->deny_page();
		}
	}


	public function update()
	{
		$sc = TRUE;

		if($this->pm->can_add OR $this->pm->can_edit)
		{
      $shop = json_decode($this->input->post('data'));

			if( ! empty($shop))
			{
				$code = trim($shop->code);

        if( ! empty($shop->name) && ! empty($shop->zone_code))
				{

					if($sc === TRUE && $this->shop_model->is_exists_name(trim($shop->name), $shop->id))
					{
						$sc = FALSE;
						$this->error = "ชื่อซ้ำ กรุณากำหนดชื่อจุดขายใหม่";
					}

					if($sc === TRUE && $this->shop_model->is_exists_zone(trim($shop->zone_code), $shop->id))
					{
						$sc = FALSE;
						$this->error = "โซนซ้ำ โซนนี้ถูกใช้งานแล้ว กรุณากำหนดโซนอื่น";
					}

          if($sc === TRUE && empty($shop->channels))
          {
            $sc = FALSE;
            $this->error = "กรุณาระบุช่องทางขาย";
          }

          if($sc === TRUE && empty($shop->cash_payment))
          {
            $sc = FALSE;
            $this->error = "กรุณาระบุช่องทางการชำระเงินด้วยเงินสด";
          }

          if($sc === TRUE && $this->shop_model->is_exists_prefix(trim($shop->prefix), $shop->id))
          {
            $sc = FALSE;
            $this->error = "Prefix '{$shop->prefix}' มีในระบบแล้ว";
          }

					if($sc === TRUE)
					{
						$arr = array(
							'name' => trim($shop->name),
							'zone_code' => trim($shop->zone_code),
							'customer_code' => $shop->customer_code,
              'prefix' => trim($shop->prefix),
              'running' => $shop->running,
              'channels_code' => $shop->channels,
              'cash_payment' => $shop->cash_payment,
              'transfer_payment' => get_null($shop->transfer_payment),
              'card_payment' => get_null($shop->card_payment),
							'bill_header_1' => get_null(trim($shop->bill_header_1)),
							'bill_header_2' => get_null(trim($shop->bill_header_2)),
							'bill_header_3' => get_null(trim($shop->bill_header_3)),
              'header_size_1' => empty(trim($shop->header_size_1)) ? 14 : trim($shop->header_size_1),
              'header_size_2' => empty(trim($shop->header_size_2)) ? 14 : trim($shop->header_size_2),
              'header_size_3' => empty(trim($shop->header_size_3)) ? 14 : trim($shop->header_size_3),
              'header_align_1' => $shop->header_align_1,
              'header_align_2' => $shop->header_align_2,
              'header_align_3' => $shop->header_align_3,
							'bill_footer' => get_null(trim($shop->bill_footer)),
              'footer_size' => empty(trim($shop->footer_size)) ? 14 : trim($shop->footer_size),
              'font_size' => $shop->font_size,
							'use_vat' => $shop->use_vat,
							'tax_id' => get_null(trim($shop->tax_id)),
							'active' => $shop->active == 1 ? 1 : 0,
              'barcode' => $shop->barcode == 1 ? 1 : 0
						);

						if(! $this->shop_model->update($shop->id, $arr))
						{
							$sc = FALSE;
							$this->error = "Failed to update data";
						}
					}
				}
				else
				{
					$sc = FALSE;
					$this->error = "Missing Required Parameter";
				}
			}
			else
			{
				$sc = FALSE;
				$this->error = "Missing required parameter : code";
			}
		}
		else
		{
			$sc = FALSE;
			$this->error = "Missing Permission";
		}

		$this->_response($sc);
	}


  public function view_detail($id)
	{
    $shop = $this->shop_model->get_by_id($id);

    if( ! empty($shop))
    {
      $ds = array(
        'shop' => $shop,
        'users' => $this->shop_model->get_shop_user($shop->id),
        'payments' => $this->shop_model->get_shop_payments($shop->id)
      );

      $this->load->view('masters/shop/shop_detail', $ds);
    }
    else
    {
      $this->page_error();
    }
	}


	public function delete()
	{
		$sc = TRUE;

		$id = $this->input->post('id');

		if( ! empty($id))
		{
			if($this->pm->can_delete)
			{
				//---- check transection
				$transection = $this->shop_model->has_transection($id);

				if(! $transection)
				{
					if( ! $this->shop_model->delete($id))
					{
						$sc = FALSE;
						$error = $this->db->error();
						$this->error = "Delete Failed : ".$error['message'];
					}
          else
          {
            $this->shop_model->drop_shop_payment($id);
          }
				}
				else
				{
					$sc = FALSE;
					set_error('transection', 'Shop');
				}
			}
			else
			{
				$sc = FALSE;
				set_error('permission');
			}
		}
		else
		{
			$sc = FALSE;
			set_error('required');
		}


		$this->_response($sc);
	}



	public function add_user()
	{
		$sc = TRUE;
		if($this->pm->can_edit)
		{
			$shop_id = $this->input->post('shop_id');
			$uname  = $this->input->post('uname');

			if(! is_null($shop_id))
			{
				if( ! is_null($uname))
				{
					$user = $this->user_model->get($uname);

					if(!empty($user))
					{
						$exists = $this->shop_model->is_exists_user($shop_id, $user->uname);

						if(!$exists)
						{
							$date_add = date('Y-m-d');
							$arr = array(
								'shop_id' => $shop_id,
								'uname' => $user->uname,
								'date_add' => $date_add
							);

							if($this->shop_model->add_user($arr))
							{
								$id = $this->db->insert_id();

								$data = array(
									'id' => $id,
									'uname' => $user->uname,
									'name' => $user->name,
									'date_add' => $date_add
								);

								echo json_encode($data);
							}
							else
							{
								$sc = FALSE;
								$error = $this->db->error();
								$this->error = "Insert Error : ".$error['message'];
							}
						}
						else
						{
							$sc = FALSE;
							$this->error = "User already exists";
						}
					}
					else
					{
						$sc = FALSE;
						$this->error = "Invalid User Name";
					}
				}
				else
				{
					$sc = FALSE;
					$this->error = "Missing required parameter: User Name";
				}
			}
			else
			{
				$sc = FALSE;
				$this->error = "Missing required parameter: Shop ID";
			}
		}
		else
		{
			$sc = FALSE;
			$this->error = "Missing Permission";
		}

		if($sc === FALSE)
		{
			echo $this->error;
		}
	}



	public function remove_user()
	{
		$sc = TRUE;
		if($this->pm->can_edit)
		{
			$id = $this->input->post('id');

			if(! is_null($id))
			{
				if(! $this->shop_model->delete_shop_user($id))
				{
					$sc = FALSE;
					$error = $this->db->error();
					$this->error = "Delete failed : ".$error['message'];
				}
			}
			else
			{
				$sc = FALSE;
				$this->error = "Missing required parameter: ID";
			}
		}
		else
		{
			$sc = FALSE;
			$this->error = "Missing Permission";
		}

		$this->_response($sc);
	}


  public function add_payment_method()
	{
		$sc = TRUE;

		if($this->pm->can_edit OR $this->pm->can_add)
		{
			$shop_id = $this->input->post('shop_id');
			$payment_code = $this->input->post('payment_code');

			if(! empty($shop_id))
			{
				if( ! empty($payment_code))
				{
          $this->load->model('masters/payment_methods_model');

          $payment = $this->payment_methods_model->get($payment_code);

          if( ! empty($payment))
          {
            if( ! $this->shop_model->is_exists_payment_method($shop_id, $payment->id))
            {
              $arr = array(
                'shop_id' => $shop_id,
                'payment_id' => $payment->id,
                'user' => $this->_user->uname
              );

              $id = $this->shop_model->add_payment_method($arr);

              if( ! $id)
              {
                $sc = FALSE;
                set_error('insert');
              }
              else
              {
                $payment->id = $id;
              }
            }
            else
            {
              $sc = FALSE;
              $this->error = "Payment method alerady exists";
            }
          }
          else
          {
            $sc = FALSE;
            $this->error = "Payment method not found !";
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
				set_error('required');
			}
		}
		else
		{
			$sc = FALSE;
			set_error('permission');
		}

		$arr = array(
      'status' => $sc === TRUE ? 'success' : 'failed',
      'message' => $sc === TRUE ? 'success' : $this->error,
      'data' => $sc === TRUE ? $payment : NULL
    );

    echo json_encode($arr);
	}



	public function remove_payment_method()
	{
		$sc = TRUE;
		if($this->pm->can_edit)
		{
			$id = $this->input->post('id');

			if(! empty($id))
			{
				if(! $this->shop_model->delete_shop_payment($id))
				{
					$sc = FALSE;
					$error = $this->db->error();
					$this->error = "Delete failed : ".$error['message'];
				}
			}
			else
			{
				$sc = FALSE;
				$this->error = "Missing required parameter: ID";
			}
		}
		else
		{
			$sc = FALSE;
			$this->error = "Missing Permission";
		}

		$this->_response($sc);
	}



	public function get_zone_code_and_name()
	{
		$txt = trim($this->input->get('term'));
		$ds = array();
		if(! is_null($txt))
		{
			if($txt !== '*')
			{
				$this->db->group_start();
				$this->db->like('code', $txt);
				$this->db->or_like('name', $txt);
				$this->db->group_end();
			}

			$rs = $this->db->limit(20)->get('zone');

			if($rs->num_rows() > 0)
			{
				foreach($rs->result() as $zone)
				{
					$ds[] = $zone->code.' | '.$zone->name;
				}
			}
			else
			{
				$ds[] = 'not found';
			}

		}

		echo json_encode($ds);

	}




	public function get_customer_code_and_name()
	{
		$txt = trim($this->input->get('term'));
		$ds = array();
    $this->db->where('active', 1);
		if(! is_null($txt))
		{
			if($txt !== '*')
			{
				$this->db->group_start();
				$this->db->like('code', $txt);
				$this->db->or_like('name', $txt);
				$this->db->group_end();
			}

			$rs = $this->db->limit(20)->get('customers');

			if($rs->num_rows() > 0)
			{
				foreach($rs->result() as $customer)
				{
					$ds[] = $customer->code.' | '.$customer->name;
				}
			}
			else
			{
				$ds[] = 'not found';
			}

		}

		echo json_encode($ds);

	}



	public function get_user_and_name()
	{
		$txt = trim($this->input->get('term'));
		$ds = array();
		if(! is_null($txt))
		{
			if($txt !== '*')
			{
				$this->db->group_start();
				$this->db->like('uname', $txt);
				$this->db->or_like('name', $txt);
				$this->db->group_end();
			}

			$rs = $this->db->limit(20)->get('user');

			if($rs->num_rows() > 0)
			{
				foreach($rs->result() as $user)
				{
					$ds[] = $user->uname.' | '.$user->name;
				}
			}
			else
			{
				$ds[] = 'not found';
			}
		}

		echo json_encode($ds);
	}



  public function clear_filter()
  {
    $filter = array(
			'shop_code',
			'shop_name',
			'shop_zone',
			'shop_customer',
			'shop_status'
		);


    clear_filter($filter);
  }

} //--- end class

 ?>
