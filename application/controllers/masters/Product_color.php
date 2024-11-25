<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Product_color extends PS_Controller
{
  public $menu_code = 'DBPDCL';
	public $menu_group_code = 'DB';
  public $menu_sub_group_code = 'PRODUCT';
	public $title = 'เพิ่ม/แก้ไข สีสินค้า';

  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'masters/product_color';
    $this->load->model('masters/product_color_model');
    $this->load->helper('product_color');
  }


  public function index()
  {
    $filter = array(
      'code' => get_filter('code', 'color_code', ''),
      'name' => get_filter('name', 'color_name', ''),
      'color_group' => get_filter('color_group', 'color_group', 'all'),
      'status' => get_filter('status', 'color_status', 'all')
    );

    if($this->input->post('search'))
    {
      redirect($this->home);
    }
    else
    {
      //--- แสดงผลกี่รายการต่อหน้า
      $perpage = get_filter('set_rows', 'rows', 20);
      //--- หาก user กำหนดการแสดงผลมามากเกินไป จำกัดไว้แค่ 300
      if($perpage > 300)
      {
        $perpage = get_filter('rows', 'rows', 300);
      }

      $segment = 4; //-- url segment
      $rows = $this->product_color_model->count_rows($filter);
      //--- ส่งตัวแปรเข้าไป 4 ตัว base_url ,  total_row , perpage = 20, segment = 3
      $init	= pagination_config($this->home.'/index/', $rows, $perpage, $segment);
      $color = $this->product_color_model->get_data($filter, $perpage, $this->uri->segment($segment));

      if(!empty($color))
      {
        foreach($color as $rs)
        {
          $rs->member = $this->product_color_model->count_members($rs->code);
        }
      }

      $filter['data'] = $color;

      $this->pagination->initialize($init);
      $this->load->view('masters/product_color/product_color_view', $filter);
    }

  }



  public function set_active()
  {
    $id = $this->input->post('id');
    $active = $this->input->post('active') == 1 ? 0 :1;

    if($id)
    {
      $rs = $this->product_color_model->set_active($id, $active);

      if($rs)
      {
        $sc = "<span class=\"pointer\" onClick=\"toggleActive({$active}, '{$id}')\">";
        $sc .= is_active($active);
        $sc .= "</span>";

        echo $sc;
      }
    }
  }


  public function add_new()
  {
    $this->load->view('masters/product_color/product_color_add_view');
  }


  public function add()
  {
    if($this->input->post('code'))
    {
      $sc = TRUE;
      $code = $this->input->post('code');
      $name = $this->input->post('name');
      $id_group = get_null($this->input->post('color_group'));

      $ds = array(
        'code' => $code,
        'name' => $name,
        'id_group' => $id_group
      );

      if($this->product_color_model->is_exists_code($code) === TRUE)
      {
        $sc = FALSE;
        $this->error = "'{$code}' มีในระบบแล้ว";
      }

      if($sc === TRUE)
      {
        $id = $this->product_color_model->add($ds);

        if( ! $id)
        {
          $sc = FALSE;
          $this->error = "เพิ่มข้อมูลไม่สำเร็จ";
        }

        if($sc === TRUE)
        {
          $this->export_to_sap($id, $code, NULL);
        }
      }
    }
    else
    {
      $sc = FALSE;
      set_error('required');
    }

    echo $sc === TRUE ? 'success' : $this->error;
  }



  public function edit($id)
  {
    $this->title = 'แก้ไข สีสินค้า';
    $data = $this->product_color_model->get_by_id($id);
    $this->load->view('masters/product_color/product_color_edit_view', $data);
  }



  public function update($id)
  {
    $sc = TRUE;

    if($this->input->post('code'))
    {
      $code = $this->input->post('code');
      $name = $this->input->post('name');
      $id_group = get_null($this->input->post('color_group'));
      $color = $this->product_color_model->get_by_id($id);

      if( ! empty($color))
      {
        $ds = array(
          'code' => $code,
          'name' => $name,
          'id_group' => $id_group
        );

        if($this->product_color_model->is_exists_code($code, $id))
        {
          $sc = FALSE;
          $this->error = "'{$code}' มีอยู่ในระบบแล้ว โปรดใช้รหัสอื่น";
        }

        if($sc === TRUE)
        {
          if( ! $this->product_color_model->update_by_id($id, $ds))
          {
            $sc = FALSE;
            $this->error = "ปรับปรุงข้อมูลไม่สำเร็จ";
          }
        }

        if($sc === TRUE)
        {
          $this->export_to_sap($id, $code, $color->code);
        }
      }
      else
      {
        $sc = FALSE;
        $this->error = "Invalid color id";
      }
    }
    else
    {
      $sc = FALSE;
      set_error('required');
    }

    echo $sc === TRUE ? 'success' : $this->error;
  }



  public function delete($id)
  {
    if($id != '')
    {
      if($this->product_color_model->delete_by_id($id))
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



  public function export_to_sap($id, $code, $old_code = NULL)
  {
    $rs = $this->product_color_model->get_by_id($id);

    if( ! empty($rs))
    {
      $ext = empty($old_code) ? FALSE : $this->product_color_model->is_sap_exists($old_code);

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

      return $this->product_color_model->add_sap_color($arr);
    }

    return FALSE;
  }



  public function export_api()
  {
    $code = $this->input->post('code');

    if(!empty($code))
    {
      $this->load->library('api');
      $rs = json_decode($this->api->create_color($code), TRUE);
      if(count($rs) === 1){
        echo $rs['message'];
      }else{
        echo 'success';
      }
    }
  }

  public function clear_filter()
	{
    $filter = array('color_code', 'color_name', 'color_group', 'color_status');
    clear_filter($filter);
	}

}//--- end class
 ?>
