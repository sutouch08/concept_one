<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Product_size extends PS_Controller
{
  public $menu_code = 'DBPDSI';
	public $menu_group_code = 'DB';
  public $menu_sub_group_code = 'PRODUCT';
	public $title = 'เพิ่ม/แก้ไข ไซส์';

  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'masters/product_size';
    $this->load->model('masters/product_size_model');
  }


  public function index()
  {
		$filter = [
      'code' => get_filter('code', 'size_code', ''),
      'name' => get_filter('name', 'size_name', '')
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
  		$rows = $this->product_size_model->count_rows($filter);
  		//--- ส่งตัวแปรเข้าไป 4 ตัว base_url ,  total_row , perpage = 20, segment = 3
  		$init	= pagination_config($this->home.'/index/', $rows, $perpage, $segment);
  		$size = $this->product_size_model->get_list($filter, $perpage, $this->uri->segment($segment));

      if(! empty($size))
      {
        foreach($size as $rs)
        {
          $rs->member = $this->product_size_model->count_members($rs->code);
        }
      }

      $filter['data'] = $size;

  		$this->pagination->initialize($init);
      $this->load->view('masters/product_size/product_size_view', $filter);
    }

  }


  public function add_new()
  {
    $this->load->view('masters/product_size/product_size_add_view');
  }


  public function add()
  {
    $sc = TRUE;

    if($this->pm->can_add)
    {
      $code = trim($this->input->post('code'));
      $name = trim($this->input->post('name'));
      $position = trim($this->input->post('position'));

      if( ! empty($code) && ! empty($name))
      {
        if($this->product_size_model->is_exists($code))
        {
          $sc = FALSE;
          $this->error = "'{$code}' มีในระบแล้ว";
        }

        if($sc === TRUE)
        {
          $arr = array(
            'code' => $code,
            'name' => $name,
            'position' => $position
          );

          if( ! $this->product_size_model->add($arr))
          {
            $sc = FALSE;
            $this->error = "เพิ่มรายการไม่สำเร็จ";
          }
        }

        if($sc === TRUE)
        {
          $this->export_to_sap($code, $code);
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

    $data = $this->product_size_model->get_by_id($id);

    if( ! empty($data))
    {
      $this->load->view('masters/product_size/product_size_edit_view', $data);
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
    $position = $this->input->post('position');

    $size = $this->product_size_model->get_by_id($id);

    if( ! empty($size))
    {
      if( ! empty($code) && ! empty($name))
      {
        if($this->product_size_model->is_exists($code, $id))
        {
          $sc = FALSE;
          $this->error = "'{$code}' มีในระบบแล้ว";
        }

        if($sc === TRUE)
        {
          $arr = array(
            'code' => $code,
            'name' => $name,
            'position' => $position
          );

          if( ! $this->product_size_model->update_by_id($id, $arr))
          {
            $sc = FALSE;
            $this->error = "ปรับปรุงข้อมูลไม่สำเร็จ";
          }
        }

        if($sc === TRUE)
        {
          $this->export_to_sap($code, $size->code);
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
        if( ! $this->product_size_model->delete_by_id($id))
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



  public function export_to_sap($code, $old_code)
  {
    $rs = $this->product_size_model->get($code);

    if(! empty($rs))
    {
      $ext = $this->product_size_model->is_sap_exists($old_code);

      $arr = array(
        'Code' => $rs->code,
        'Name' => $rs->name,
        'UpdateDate' => sap_date(now(), TRUE)
      );

      if($ext)
      {
        $arr['Flag'] = 'U';
        if($code !== $old_code)
        {
          $arr['OLDCODE'] = $old_code;
        }
      }
      else
      {
        $arr['Flag'] = 'A';
      }

      return $this->product_size_model->add_sap_size($arr);
    }

    return FALSE;
  }



  public function clear_filter()
	{
		$filter = array('size_code', 'size_name');
    clear_filter($filter);
		echo 'done';
	}



  public function export_api()
  {
    $code = $this->input->post('code');

    if(!empty($code))
    {
      $this->load->library('api');
      $rs = json_decode($this->api->create_size($code), TRUE);
      if(count($rs) === 1){
        echo $rs['message'];
      }else{
        echo 'success';
      }
    }
  }

}//--- end class
 ?>
