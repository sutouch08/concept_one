<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Supplier extends PS_Controller
{
  public $menu_code = 'DBSUPL';
	public $menu_group_code = 'DB';
  public $menu_sub_group_code = '';
	public $title = 'เพิ่ม/แก้ไข ผู้ผลิต';

  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'masters/supplier';
    $this->load->model('masters/supplier_model');
  }


  public function index()
  {
		$filter = array(
			'code' => get_filter('code', 'sup_code', ''),
			'name' => get_filter('name', 'sup_name', ''),
			'phone' => get_filter('phone', 'sup_phone', ''),
			'status' => get_filter('status', 'sup_status', 'all')
		);

		//--- แสดงผลกี่รายการต่อหน้า
		$perpage = get_rows();
		//--- หาก user กำหนดการแสดงผลมามากเกินไป จำกัดไว้แค่ 300
		if($perpage > 300)
		{
			$perpage = 20;
		}

		$segment = 4; //-- url segment
		$rows = $this->supplier_model->count_rows($filter);
		//--- ส่งตัวแปรเข้าไป 4 ตัว base_url ,  total_row , perpage = 20, segment = 3
		$init	= pagination_config($this->home.'/index/', $rows, $perpage, $segment);
		$supplier = $this->supplier_model->get_list($filter, $perpage, $this->uri->segment($segment));

		$filter['data'] = $supplier;

		$this->pagination->initialize($init);
    $this->load->view('masters/supplier/supplier_list', $filter);
  }



	public function add_new()
	{
		$this->load->view('masters/supplier/supplier_add');
	}


	public function add()
	{
		$sc = TRUE;

		if($this->input->post())
		{
			$ds = $this->input->post();

			if($this->supplier_model->is_exists_code($ds['code']))
			{
				$sc = FALSE;
				$this->error = "รหัสซ้ำ กรุณากำหนดรหัสใหม่";
			}
			else
			{
				$arr = array(
					'code' => $ds['code'],
					'name' => trim($ds['name']),
					'address1' => get_null(trim($ds['address1'])),
					'address2' => get_null(trim($ds['address2'])),
					'phone' => get_null(trim($ds['phone'])),
					'status' => trim($ds['active'])
				);

				if(!$this->supplier_model->add($arr))
				{
					$sc = FALSE;
					$error = $this->db->error();
					$this->error = "Insert failed : ".$error['message'];
				}
			}
		}
		else
		{
			$sc = FALSE;
			$this->error = "No Data";
		}

		echo $sc === TRUE ? 'success' : $this->error;
	}


  public function edit($id)
	{
		$sup = $this->supplier_model->get($id);

		$ds['sup'] = $sup;

		$this->load->view('masters/supplier/supplier_edit', $ds);
	}


	public function update()
	{
		$sc = TRUE;

		if($this->input->post())
		{
			$ds = $this->input->post();

			$arr = array(
				'name' => trim($ds['name']),
				'address1' => trim($ds['address1']),
				'address2' => get_null(trim($ds['address2'])),
				'phone' => get_null(trim($ds['phone'])),
				'status' => trim($ds['status'])
			);

			if(! $this->supplier_model->update($ds['id'], $arr))
			{
				$sc = FALSE;
				$error = $this->db->error();
				$this->error = "Update Failed : ".$error['message'];
			}
		}
		else
		{
			$sc = FALSE;
			$this->error = "No data";
		}

		echo $sc === TRUE ? 'success' : $this->error;
	}



	public function delete()
	{
		$sc = TRUE;
		if($this->input->post('id'))
		{
			$id = $this->input->post('id');

			if(! $this->supplier_model->delete($id))
			{
				$sc = FALSE;
				$error = $this->db->error();
				$this->error = "Delete failed : ".$error['message'];
			}
		}
		else
		{
			$sc = FALSE;
			$this->error = "Missing required parameter : id";
		}

		echo $sc === TRUE ? 'success' : $this->error;
	}

  public function clear_filter()
	{
		$filter = array('sup_code', 'sup_name', 'sup_phone', 'sup_status');
		clear_filter($filter);
		echo 'done';
	}
}

?>
