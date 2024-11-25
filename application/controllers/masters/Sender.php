<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Sender extends PS_Controller{
	public $menu_code = 'DBSEND'; //--- Add/Edit Users
	public $menu_group_code = 'DB';
	public $menu_sub_group_code = 'TRANSPORT'; //--- System security
	public $title = 'เพิ่ม/แก้ไข ขนส่ง';

  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'masters/sender';
		$this->load->model('masters/sender_model');
  }



  public function index()
  {
		$filter = array(
			'code' => get_filter('code', 'sender_code', ''),
			'name' => get_filter('name', 'sender_name', ''),
			'addr' => get_filter('addr', 'sender_addr', ''),
			'phone' => get_filter('phone', 'sender_phone', ''),
			'type' => get_filter('type', 'sender_type', 'all')
		);

		//--- แสดงผลกี่รายการต่อหน้า
		$perpage = get_filter('set_rows', 'rows', 20);
		//--- หาก user กำหนดการแสดงผลมามากเกินไป จำกัดไว้แค่ 300
		if($perpage > 300)
		{
			$perpage = get_filter('rows', 'rows', 300);
		}

		$segment = 4; //-- url segment
		$rows = $this->sender_model->count_rows($filter);

		//--- ส่งตัวแปรเข้าไป 4 ตัว base_url ,  total_row , perpage = 20, segment = 3
		$init	= pagination_config($this->home.'/index/', $rows, $perpage, $segment);

		$rs = $this->sender_model->get_list($filter, $perpage, $this->uri->segment($segment));
		$filter['data'] = $rs;

		$this->pagination->initialize($init);
    $this->load->view('masters/sender/sender_view', $filter);
  }





	public function add_new()
	{
		$this->load->view('masters/sender/sender_add');
	}



	public function add()
	{
		$sc = TRUE;

		$code = trim($this->input->post('code'));
		$name = trim($this->input->post('name'));

		if($code !== NULL && $code !== "")
		{
			if($name !== NULL && $name !== "")
			{
				//--- check duplicate code
				if(! $this->sender_model->is_exists_code($code))
				{
					if(! $this->sender_model->is_exists_name($name))
					{
						$arr = array(
							'code' => $code,
							'name' => $name,
							'address1' => get_null(trim($this->input->post('address1'))),
							'address2' => get_null(trim($this->input->post('address2'))),
							'phone' => get_null(trim($this->input->post('phone'))),
							'open' => trim($this->input->post('open')),
							'close' => trim($this->input->post('close')),
							'type' => trim($this->input->post('type')),
							'show_in_list' => empty($this->input->post('show_in_list')) ? 0 : 1,
							'force_tracking' => empty($this->input->post('force_tracking')) ? 0 : 1,
							'auto_gen' => empty($this->input->post('auto_gen')) ? 0 :1,
							'prefix' => get_null(trim($this->input->post('prefix')))
						);

						if(! $this->sender_model->add($arr))
						{
							$sc = FALSE;
							$this->error = "เพิ่มรายการไม่สำเร็จ";
						}
					}
					else
					{
						$sc = FALSE;
						$this->error = "ชื่อซ้ำ กรุณากำหนดชื่อใหม่";
					}
				}
				else
				{
					$sc = FALSE;
					$this->error = "รหัสซ้ำ กรุณากำหนดรหัสใหม่";
				}
			}
			else
			{
				$sc = FALSE;
				$this->error = "Missing required parameter : name";
			}
		}
		else
		{
			$sc = FALSE;
			$this->error = "Missing required parameter : code";
		}

		echo $sc === TRUE ? 'success' : $this->error;

	}


	public function edit($id)
	{
		$rs = $this->sender_model->get($id);
		$this->load->view('masters/sender/sender_edit', $rs);
	}

	public function update($id)
	{
		$sc = TRUE;

		$code = trim($this->input->post('code'));
		$name = trim($this->input->post('name'));

		if($code !== NULL && $code !== "")
		{
			if($name !== NULL && $name !== "")
			{
				//--- check duplicate code
				if(! $this->sender_model->is_exists_code($code, $id))
				{
					if(! $this->sender_model->is_exists_name($name, $id))
					{
						$arr = array(
							'code' => $code,
							'name' => $name,
							'address1' => get_null(trim($this->input->post('address1'))),
							'address2' => get_null(trim($this->input->post('address2'))),
							'phone' => get_null(trim($this->input->post('phone'))),
							'open' => trim($this->input->post('open')),
							'close' => trim($this->input->post('close')),
							'type' => trim($this->input->post('type')),
							'show_in_list' => empty($this->input->post('show_in_list')) ? 0 : 1,
							'force_tracking' => empty($this->input->post('force_tracking')) ? 0 : 1,
							'auto_gen' => empty($this->input->post('auto_gen')) ? 0 :1,
							'prefix' => get_null(trim($this->input->post('prefix')))
						);

						if(! $this->sender_model->update($id, $arr))
						{
							$sc = FALSE;
							$this->error = "แก้ไขรายการไม่สำเร็จ";
						}
					}
					else
					{
						$sc = FALSE;
						$this->error = "ชื่อซ้ำ กรุณากำหนดชื่อใหม่";
					}
				}
				else
				{
					$sc = FALSE;
					$this->error = "รหัสซ้ำ กรุณากำหนดรหัสใหม่";
				}
			}
			else
			{
				$sc = FALSE;
				$this->error = "Missing required parameter : name";
			}
		}
		else
		{
			$sc = FALSE;
			$this->error = "Missing required parameter : code";
		}

		echo $sc === TRUE ? 'success' : $this->error;

	}




	public function delete($id)
	{
		if($this->pm->can_delete)
		{
			if($this->sender_model->delete($id))
			{
				set_message('ลบรายการเรียบร้อยแล้ว');
			}
			else
			{
				set_error('ลบรายการไม่สำเร็จ');
			}
		}
		else
		{
			set_error('คุณไม่มีอำนาจในการลบ');
		}

		redirect($this->home);
	}




	public function clear_filter()
	{
		$filter = array('sender_code', 'sender_name', 'sender_addr', 'sender_phone', 'sender_type');
		clear_filter($filter);
	}

}//--- end class


 ?>
