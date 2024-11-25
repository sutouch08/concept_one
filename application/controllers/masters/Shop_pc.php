<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Shop_pc extends PS_Controller
{
  public $menu_code = 'DBPSPC';
	public $menu_group_code = 'DB';
  public $menu_sub_group_code = 'POS';
	public $title = 'เพิ่ม/แก้ไข พนักงานขาย (PC)';

  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'masters/shop_pc';
    $this->load->model('masters/shop_pc_model');
  }

  public function index()
  {
    $filter = array(
      'code' => get_filter('code', 'pc_code', ''),
			'name' => get_filter('name', 'pc_name', ''),
			'status' => get_filter('status', 'pc_status', 'all')
    );

		//--- แสดงผลกี่รายการต่อหน้า
		$perpage = get_rows();
		//--- หาก user กำหนดการแสดงผลมามากเกินไป จำกัดไว้แค่ 300
		if($perpage > 300)
		{
			$perpage = 20;
		}

		$segment  = 4; //-- url segment
		$rows     = $this->shop_pc_model->count_rows($filter);
		//--- ส่งตัวแปรเข้าไป 4 ตัว base_url ,  total_row , perpage = 20, segment = 3
		$init = pagination_config($this->home.'/index/', $rows, $perpage, $segment);
		$list = $this->shop_pc_model->get_list($filter, $perpage, $this->uri->segment($segment));

    $filter['list'] = $list;

		$this->pagination->initialize($init);
    $this->load->view('masters/shop_pc/shop_pc_list', $filter);
  }



	public function add_new()
	{
		$this->load->view('masters/shop_pc/shop_pc_add');
	}


	public function add()
	{
		$sc = TRUE;

		if($this->pm->can_add)
		{
      $data = json_decode($this->input->post('data'));

      if( ! empty($data))
      {
        if( $this->shop_pc_model->is_exists_code(trim($data->code)))
        {
          $sc = FALSE;
          $this->error = "รหัสซ้ำ กรุณากำหนดรหัสพนักงานขายใหม่";
        }

        if($sc === TRUE && $this->shop_pc_model->is_exists_name(trim($data->name)))
        {
          $sc = FALSE;
          $this->error = "ชื่อซ้ำ กรุณากำหนดชื่อพนักงานใหม่";
        }

        if($sc === TRUE)
        {
          $arr = array(
            'code' => $data->code,
            'name' => $data->name,
            'active' => $data->active == 1 ? 1 : 0
          );

          if( ! $this->shop_pc_model->add($arr))
          {
            $sc = FALSE;
            set_error('insert');
          }
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

		$this->_response($sc);
	}




	public function edit($id)
	{
		if($this->pm->can_edit)
		{
			$pc = $this->shop_pc_model->get($id);

			if( ! empty($pc))
			{
				$ds = array(
					'pc' => $pc
				);

				$this->load->view('masters/shop_pc/shop_pc_edit', $ds);

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
      $data = json_decode($this->input->post('data'));

			if( ! empty($data))
			{
        $id = $data->id;
				$code = trim($data->code);
        $name = trim($data->name);
        $active = $data->active == 1 ? 1 : 0;

        if( ! empty($code) && ! empty($name))
				{
          if( $this->shop_pc_model->is_exists_code($code, $id))
          {
            $sc = FALSE;
            $this->error = "รหัสซ้ำ กรุณากำหนดรหัสพนักงานขายใหม่";
          }

          if($sc === TRUE && $this->shop_pc_model->is_exists_name($name, $id))
          {
            $sc = FALSE;
            $this->error = "ชื่อซ้ำ กรุณากำหนดชื่อพนักงานใหม่";
          }

          if($sc === TRUE)
          {
            $arr = array(
              'code' => $data->code,
              'name' => $data->name,
              'active' => $data->active == 1 ? 1 : 0
            );

            if( ! $this->shop_pc_model->update($id, $arr))
            {
              $sc = FALSE;
              set_error('update');
            }
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

		$this->_response($sc);
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
				$transection = $this->shop_pc_model->has_transection($id);

				if(! $transection)
				{
					if( ! $this->shop_pc_model->delete($id))
					{
						$sc = FALSE;
						$error = $this->db->error();
						$this->error = "Delete Failed : ".$error['message'];
					}
				}
				else
				{
					$sc = FALSE;
					set_error('transection', 'PC');
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
						$exists = $this->shop_pc_model->is_exists_user($shop_id, $user->uname);

						if(!$exists)
						{
							$date_add = date('Y-m-d');
							$arr = array(
								'shop_id' => $shop_id,
								'uname' => $user->uname,
								'date_add' => $date_add
							);

							if($this->shop_pc_model->add_user($arr))
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
				if(! $this->shop_pc_model->delete_shop_user($id))
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
            if( ! $this->shop_pc_model->is_exists_payment_method($shop_id, $payment->id))
            {
              $arr = array(
                'shop_id' => $shop_id,
                'payment_id' => $payment->id,
                'user' => $this->_user->uname
              );

              $id = $this->shop_pc_model->add_payment_method($arr);

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
				if(! $this->shop_pc_model->delete_shop_payment($id))
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
			'pc_code',
			'pc_name',
			'pc_status'
		);


    clear_filter($filter);
  }

} //--- end class

 ?>
