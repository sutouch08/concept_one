<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Vat extends PS_Controller
{
  public $menu_code = 'DBVATG';
	public $menu_group_code = 'DB';
  public $menu_sub_group_code = '';
	public $title = 'เพิ่ม/แก้ไข กลุ่มภาษี';

  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'masters/vat';
    $this->load->model('masters/vat_model');
  }


  public function index()
  {
    $filter = array(
      'code' => get_filter('code', 'vat_code', ''),
      'name' => get_filter('name', 'vat_name', ''),
      'active' => get_filter('active', 'vat_active', 'all')
    );

		//--- แสดงผลกี่รายการต่อหน้า
		$perpage = get_rows();
		//--- หาก user กำหนดการแสดงผลมามากเกินไป จำกัดไว้แค่ 300
		if($perpage > 300)
		{
			$perpage = 20;
		}

		$segment = 4; //-- url segment
		$rows = $this->vat_model->count_rows($filter);
		//--- ส่งตัวแปรเข้าไป 4 ตัว base_url ,  total_row , perpage = 20, segment = 3
		$init	= pagination_config($this->home.'/index/', $rows, $perpage, $segment);
		$list = $this->vat_model->get_list($filter, $perpage, $this->uri->segment($segment));

    $filter['data'] = $list;

		$this->pagination->initialize($init);
    $this->load->view('masters/vat/vat_list', $filter);
  }


  public function add_new()
  {
    $this->load->view('masters/vat/vat_add');
  }


	public function add()
  {
		$sc = TRUE;

		$code = trim($this->input->post('code'));
		$name = trim($this->input->post('name'));
		$rate = floatval($this->input->post('rate'));
		$active = $this->input->post('active');

		if(!empty($code))
		{
			if($this->pm->can_add)
			{
				//--- check duplicate code;

				$is_exists = $this->vat_model->is_exists_code($code);
				if(! $is_exists)
				{
					$is_exists = $this->vat_model->is_exists_name($name);

					if(! $is_exists)
					{
						if($rate < 0 OR $rate > 100)
						{
							$sc = FALSE;
							$this->error = "อัตราภาษีต้องอยู่ในช่วง 0 - 100";
						}
						else
						{
							$arr = array(
								'code' => $code,
								'name' => $name,
								'rate' => $rate,
								'active' => $active
							);

							if(! $this->vat_model->add($arr))
							{
								$sc = FALSE;
								$this->error = "เพิ่มรายการไม่สำเร็จ";
							}
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
				$this->error = "Missing permission";
			}
		}
		else
		{
			$sc = FALSE;
			$this->error = "Missing required parameter : code";
		}

		echo $sc === TRUE ? 'success' : $this->error;
  }



  public function edit($code)
  {
    $data['data'] = $this->vat_model->get($code);
    $this->load->view('masters/vat/vat_edit', $data);
  }



  public function update()
  {
    $sc = TRUE;
		$code = trim($this->input->post('code'));
		$name = trim($this->input->post('name'));
		$active = $this->input->post('active');
		$rate = floatval($this->input->post('rate'));
		$old_name = trim($this->input->post('old_name'));

		if(!empty($code))
		{
			if($this->pm->can_edit)
			{
				if(!empty($name))
				{
					$is_exists = $this->vat_model->is_exists_name($name, $old_name);

					if(! $is_exists)
					{
						if($rate < 0 OR $rate > 100)
						{
							$sc = FALSE;
							$this->error = "อัตราภาษีต้องอยู่ในช่วง 0 - 100";
						}
						else
						{
							$arr = array(
								'name' => $name,
								'rate' => $rate,
								'active' => $active
							);

							if(! $this->vat_model->update($code, $arr))
							{
								$sc = FALSE;
								$this->error = "Update failed";
							}
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
					$this->error = "Missing required parameter : name";
				}
			}
			else
			{
				$sc = FALSE;
				$this->error = "Missing Permission";
			}
		}
		else
		{
			$sc = FALSE;
			$this->error = "Missing required parameter : code";
		}

		echo $sc === TRUE ? 'success' : $this->error;
  }



  public function delete()
  {
		$sc = TRUE;
    $code = $this->input->post('code');

		if(!empty($code))
		{
			if($this->pm->can_delete)
			{
				//--- check transection used
				$has_trans = $this->vat_model->has_transection($code);

				if(! $has_trans)
				{
					if(!$this->vat_model->delete($code))
					{
						$sc = FALSE;
						$this->error = "Delete failed";
					}
				}
				else
				{
					$sc = FALSE;
					$this->error = "{$code} มีการใช้งานแล้ว ไม่อนุญาติให้ลบ";
				}
			}
			else
			{
				$sc = FALSE;
				$this->error = "Missing permission";
			}
		}
		else
		{
			$sc = FALSE;
			$this->error = "Missing required parameter : code";
		}

		echo $sc === TRUE ? 'success' : $this->error;
  }



	function set_default()
	{
		$sc = TRUE;
		$code = $this->input->post('code');
		if($code !== NULL)
		{
			//----
			$this->db->trans_begin();

			//--- remove current default
			if(!$this->vat_model->clear_default_state())
			{
				$sc = FALSE;
				$this->error = "Clear current default state failed";
			}
			else
			{
				if(! $this->vat_model->set_default_state($code))
				{
					$sc = FALSE;
					$this->error = "Set default state failed : {$code}";
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
			$this->error = "Missing required parameter : Code";
		}

		echo $sc === TRUE ? 'success' : $this->error;
	}


  public function clear_filter()
	{
		clear_filter(array('vat_code', 'vat_name', 'vat_active'));
		echo 'done';
	}
}

?>
