<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Saleman extends PS_Controller{
	public $menu_code = 'DBSALE'; //--- Add/Edit Users
	public $menu_group_code = 'DB'; //--- System security
	public $title = 'พนักงานขาย';

  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'masters/saleman';
		$this->load->model('masters/slp_model');
  }



  public function index()
  {
		$filter = array(
			'name' => get_filter('name', 'name', ''),
			'active' => get_filter('active', 'active', 'all')
		);

		//--- แสดงผลกี่รายการต่อหน้า
		$perpage = get_filter('set_rows', 'rows', 20);
		//--- หาก user กำหนดการแสดงผลมามากเกินไป จำกัดไว้แค่ 300
		if($perpage > 300)
		{
			$perpage = get_filter('rows', 'rows', 300);
		}

		$segment = 4; //-- url segment
		$rows = $this->slp_model->count_rows($filter);

		//--- ส่งตัวแปรเข้าไป 4 ตัว base_url ,  total_row , perpage = 20, segment = 3
		$init	= pagination_config($this->home.'/index/', $rows, $perpage, $segment);

		$rs = $this->slp_model->get_list($filter, $perpage, $this->uri->segment($segment));
		$filter['data'] = $rs;

		$this->pagination->initialize($init);
    $this->load->view('masters/saleman/saleman_view', $filter);
  }



	public function syncData()
	{
		$ds = $this->slp_model->get_all_slp();
		if(!empty($ds))
		{
			foreach($ds as $rs)
			{
				$ex = $this->slp_model->is_exists($rs->id);
				if($ex)
				{
					$arr = array(
						'name' => $rs->name,
						'active' => $rs->active == 'Y' ? 1 : 0
					);

					$this->slp_model->update($rs->id, $arr);
				}
				else
				{
					$arr = array(
						'id' => $rs->id,
						'name' => $rs->name,
						'active' => $rs->active == 'Y' ? 1 : 0
					);

					$this->slp_model->add($arr);
				}
			}
		}

		echo 'success';
	}
	

	public function update_phone()
	{
		$sc = TRUE;
		$id = $this->input->post('id');
		$phone = get_null($this->input->post('phone'));

		$arr = array(
			'phone' => $phone
		);

		if( ! $this->slp_model->update($id, $arr))
		{
			$sc = FALSE;
			$this->error = "Failed to update data";
		}

		echo $sc === TRUE ? 'success' : $this->error;
	}


	public function clear_filter()
	{
		$filter = array('name', 'active');
		clear_filter($filter);
	}

}//--- end class


 ?>
