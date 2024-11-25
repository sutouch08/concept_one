<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Customers extends PS_Controller
{
  public $menu_code = 'DBCUST';
	public $menu_group_code = 'DB';
  public $menu_sub_group_code = 'CUSTOMER';
	public $title = 'เพิ่ม/แก้ไข รายชื่อลูกค้า';

  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'masters/customers';
    $this->load->model('masters/customers_model');
    $this->load->model('masters/customer_group_model');
    $this->load->model('masters/customer_kind_model');
    $this->load->model('masters/customer_type_model');
    $this->load->model('masters/customer_class_model');
    $this->load->model('masters/customer_area_model');
    $this->load->helper('customer');
  }


  public function index()
  {
		$code = get_filter('code', 'code', '');
		$name = get_filter('name', 'name', '');
    $group = get_filter('group', 'group', '');
    $kind = get_filter('kind', 'kind', '');
    $type = get_filter('type', 'type', '');
    $class = get_filter('class', 'class', '');
    $area = get_filter('area', 'area', '');

		//--- แสดงผลกี่รายการต่อหน้า
		$perpage = get_rows();
		//--- หาก user กำหนดการแสดงผลมามากเกินไป จำกัดไว้แค่ 300
		if($perpage > 300)
		{
			$perpage = 20;
		}

		$segment = 4; //-- url segment
		$rows = $this->customers_model->count_rows($code, $name, $group, $kind, $type, $class, $area);
		//--- ส่งตัวแปรเข้าไป 4 ตัว base_url ,  total_row , perpage = 20, segment = 3
		$init	= pagination_config($this->home.'/index/', $rows, $perpage, $segment);
		$customers = $this->customers_model->get_data($code, $name, $group, $kind, $type, $class, $area, $perpage, $this->uri->segment($segment));
    if(!empty($customers))
    {
      foreach($customers as $rs)
      {
        $rs->group  = $this->customer_group_model->get_name($rs->group_code);
        $rs->kind   = $this->customer_kind_model->get_name($rs->kind_code);
        $rs->type   = $this->customer_type_model->get_name($rs->type_code);
        $rs->class  = $this->customer_class_model->get_name($rs->class_code);
        //$rs->area   = $this->customer_area_model->get_name($rs->area_code);
      }
    }

    $data = array(
      'code' => $code,
      'name' => $name,
      'group' => $group,
      'kind' => $kind,
      'type' => $type,
      'class' => $class,
      'area' => $area,
			'data' => $customers
    );

		$this->pagination->initialize($init);
    $this->load->view('masters/customers/customers_view', $data);
  }


  public function add_new()
  {
    $data['code'] = $this->session->flashdata('code');
    $data['name'] = $this->session->flashdata('name');
    $data['group'] = $this->session->flashdata('group');
    $data['kind'] = $this->session->flashdata('kind');
    $data['type'] = $this->session->flashdata('type');
    $data['class'] = $this->session->flashdata('class');
    $data['area'] = $this->session->flashdata('area');
    $this->title = 'เพิ่ม รายชื่อลูกค้า';
    $this->load->view('masters/customers/customers_add_view', $data);
  }


  public function add()
  {
    if($this->input->post('code'))
    {
      $sc = TRUE;
      $code = $this->input->post('code');
      $name = $this->input->post('name');

      $ds = array(
        'code' => $code,
        'name' => $name,
        'group_code' => $this->input->post('group'),
        'kind_code' => $this->input->post('kind'),
        'type_code' => $this->input->post('type'),
        'class_code' => $this->input->post('class'),
        'area_code' => $this->input->post('area')
      );

      if($this->customers_model->is_exists($code) === TRUE)
      {
        $sc = FALSE;
        set_error("'".$code."' มีในระบบแล้ว");
      }

      if($this->customers_model->is_exists_name($name) === TRUE)
      {
        $sc = FALSE;
        set_error("'".$name."' มีในระบบแล้ว");
      }

      if($sc === TRUE)
      {
        if($this->customers_model->add($ds))
        {
          set_message('เพิ่มข้อมูลเรียบร้อยแล้ว');
        }
        else
        {
          $sc = FALSE;
          set_error('เพิ่มข้อมูลไม่สำเร็จ');
        }
      }


      if($sc === FALSE)
      {
        $this->session->set_flashdata('code', $code);
        $this->session->set_flashdata('name', $name);
        $this->session->set_flashdata('group', $this->input->post('group'));
        $this->session->set_flashdata('kind', $this->input->post('kind'));
        $this->session->set_flashdata('type', $this->input->post('type'));
        $this->session->set_flashdata('class', $this->input->post('class'));
        $this->session->set_flashdata('area', $this->input->post('area'));
      }
    }
    else
    {
      set_error('ไม่พบข้อมูล');
    }

    redirect($this->home.'/add_new');
  }



  public function edit($code)
  {
    $this->title = 'แก้ไข ข้อมูลลูกค้า';
    $rs = $this->customers_model->get($code);
    $data = array(
      'code' => $rs->code,
      'name' => $rs->name,
      'group' => $rs->group_code,
      'kind' => $rs->kind_code,
      'type' => $rs->type_code,
      'class' => $rs->class_code,
      'area' => $rs->area_code
    );

    $this->load->view('masters/customers/customers_edit_view', $data);
  }



  public function update()
  {
    $sc = TRUE;

    if($this->input->post('code'))
    {
      $old_code = $this->input->post('customers_code');
      $old_name = $this->input->post('customers_name');
      $code = $this->input->post('code');
      $name = $this->input->post('name');

      $ds = array(
        'code' => $code,
        'name' => $name,
        'group_code' => $this->input->post('group'),
        'kind_code' => $this->input->post('kind'),
        'type_code' => $this->input->post('type'),
        'class_code' => $this->input->post('class'),
        'area_code' => $this->input->post('area')
      );

      if($sc === TRUE && $this->customers_model->is_exists($code, $old_code) === TRUE)
      {
        $sc = FALSE;
        set_error("'".$code."' มีอยู่ในระบบแล้ว โปรดใช้รหัสอื่น");
      }

      if($sc === TRUE && $this->customers_model->is_exists_name($name, $old_name) === TRUE)
      {
        $sc = FALSE;
        set_error("'".$name."' มีอยู่ในระบบแล้ว โปรดใช้ชื่ออื่น");
      }

      if($sc === TRUE)
      {
        if($this->customers_model->update($old_code, $ds) === TRUE)
        {
          set_message('ปรับปรุงข้อมูลเรียบร้อยแล้ว');
        }
        else
        {
          $sc = FALSE;
          set_error('ปรับปรุงข้อมูลไม่สำเร็จ');
        }
      }

    }
    else
    {
      $sc = FALSE;
      set_error('ไม่พบข้อมูล');
    }

    if($sc === FALSE)
    {
      $code = $this->input->post('customers_code');
    }

    redirect($this->home.'/edit/'.$code);
  }



  public function delete($code)
  {
    if($code != '')
    {
      if($this->customers_model->delete($code))
      {
        set_message('ลบข้อมูลเรียบร้อยแล้ว');
      }
      else
      {
        set_error('ลบข้อมูลไม่สำเร็จ');
      }
    }
    else
    {
      set_error('ไม่พบข้อมูล');
    }

    redirect($this->home);
  }



  public function syncData()
  {
    $ds = $this->customers_model->get_updte_data();
    if(!empty($ds))
    {
      foreach($ds as $rs)
      {

        $arr = array(
          'code' => $rs->CardCode,
          'name' => $rs->CardName,
          'CardType' => $rs->CardType,
          'sale_code' => $rs->SlpCode
        );

        if($this->customers_model->is_exists($rs->CardCode) === TRUE)
        {
          $this->customers_model->update($rs->CardCode, $arr);
        }
        else
        {
          $this->customers_model->add($arr);
        }
      }
    }

    set_message('Sync completed');
  }


  public function clear_filter()
	{
		$this->session->unset_userdata('code');
    $this->session->unset_userdata('name');
    $this->session->unset_userdata('group');
    $this->session->unset_userdata('kind');
    $this->session->unset_userdata('type');
    $this->session->unset_userdata('class');
    $this->session->unset_userdata('area');

		echo 'done';
	}
}

?>
