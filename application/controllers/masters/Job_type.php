<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Job_type extends PS_Controller
{
  public $menu_code = 'DBJOBT';
	public $menu_group_code = 'DB';
  public $menu_sub_group_code = '';
	public $title = 'เพิ่ม/แก้ไข ประเภทงาน';

  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'masters/job_type';
    $this->load->model('masters/job_type_model');
  }


  public function index()
  {
		$filter = [
      'code' => get_filter('code', 'job_code', ''),
      'name' => get_filter('name', 'job_name', '')
    ];

    if($this->input->post('search'))
    {
      redirect($this->home);
    }
    else
    {
      //--- แสดงผลกี่รายการต่อหน้า
  		$perpage = get_rows();
  		$segment = 4; //-- url segment
  		$rows = $this->job_type_model->count_rows($filter);
  		//--- ส่งตัวแปรเข้าไป 4 ตัว base_url ,  total_row , perpage = 20, segment = 3
  		$init	= pagination_config($this->home.'/index/', $rows, $perpage, $segment);

      $filter['data'] = $this->job_type_model->get_list($filter, $perpage, $this->uri->segment($segment));

  		$this->pagination->initialize($init);
      $this->load->view('masters/job_type/job_type_view', $filter);
    }

  }


  public function add_new()
  {
    $this->load->view('masters/job_type/job_type_add_view');
  }


  public function add()
  {
    $sc = TRUE;

    if($this->pm->can_add)
    {
      $code = trim($this->input->post('code'));
      $name = trim($this->input->post('name'));

      if( ! empty($code) && ! empty($name))
      {
        if($this->job_type_model->is_exists($code))
        {
          $sc = FALSE;
          $this->error = "'{$code}' มีในระบแล้ว";
        }

        if($sc === TRUE)
        {
          $arr = array(
            'code' => $code,
            'name' => $name
          );

          if( ! $this->job_type_model->add($arr))
          {
            $sc = FALSE;
            $this->error = "เพิ่มรายการไม่สำเร็จ";
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

    echo $sc === TRUE ? 'success' : $this->error;
  }



  public function edit($id)
  {
    $this->title = 'แก้ไข ไซส์';

    $data = $this->job_type_model->get_by_id($id);

    if( ! empty($data))
    {
      $this->load->view('masters/job_type/job_type_edit_view', $data);
    }
    else
    {
      $this->page_error();
    }
  }



  public function update()
  {
    $sc = TRUE;
    $id = $this->input->post('id');
    $code = $this->input->post('code');
    $name = $this->input->post('name');

    $size = $this->job_type_model->get_by_id($id);

    if( ! empty($size))
    {
      if( ! empty($code) && ! empty($name))
      {
        if($this->job_type_model->is_exists($code, $id))
        {
          $sc = FALSE;
          $this->error = "'{$code}' มีในระบบแล้ว";
        }

        if($sc === TRUE)
        {
          $arr = array(
            'code' => $code,
            'name' => $name
          );

          if( ! $this->job_type_model->update_by_id($id, $arr))
          {
            $sc = FALSE;
            $this->error = "ปรับปรุงข้อมูลไม่สำเร็จ";
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
      $this->error = "Invalid size id";
    }

    echo $sc === TRUE ? 'success' : $this->error;
  }



  public function delete($id)
  {
    $sc = TRUE;

    if($id)
    {
      if($this->pm->can_delete)
      {
        if( ! $this->job_type_model->delete_by_id($id))
        {
          $sc = FALSE;
          $this->error = "Delete failed";
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

    echo $sc === TRUE ? 'success' : $this->error;
  }


  public function clear_filter()
	{
		$filter = array('job_code', 'job_name');
    clear_filter($filter);
		echo 'done';
	}

}//--- end class
 ?>
