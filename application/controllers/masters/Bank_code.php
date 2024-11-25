<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Bank_code extends PS_Controller
{
  public $menu_code = 'DBBCOD';
	public $menu_group_code = 'DB';
  public $menu_sub_group_code = 'BANK';
	public $title = 'เพิ่ม/แก้ไข ธนาคาร';
	public $segment = 4;

  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'masters/bank_code';
    $this->load->model('masters/bank_code_model');
    $this->load->helper('bank');
  }



  public function index()
  {
    $filter = array(
      'code' => get_filter('code', 'bank_code_code', ''),
      'active' => get_filter('active', 'bank_code_active', 'all')
    );



		//--- แสดงผลกี่รายการต่อหน้า
		$perpage = get_rows();

		$rows     = $this->bank_code_model->count_rows($filter);
		//--- ส่งตัวแปรเข้าไป 4 ตัว base_url ,  total_row , perpage = 20, segment = 3
		$init	    = pagination_config($this->home.'/index/', $rows, $perpage, $this->segment);
		$banks = $this->bank_code_model->get_list($filter, $perpage, $this->uri->segment($this->segment));

    $filter['data'] = $banks;

		$this->pagination->initialize($init);
    $this->load->view('masters/bank_code/bank_code_list', $filter);
  }




  public function add_new()
  {
    $this->load->view('masters/bank/bank_account_add');
  }


  public function is_exists_code()
  {
    echo $this->bank_code_model->is_exists_code($this->input->post('code')) ? "exists" : "ok";
  }


	public function add()
	{
		$sc = TRUE;

		if($this->pm->can_add)
		{
			$code = trim($this->input->post('code'));
		  $name = trim($this->input->post('name'));
      $active = $this->input->post('active') == 1 ? 1 : 0;

			if( ! empty($code) && ! empty($name))
      {
        if( ! $this->bank_code_model->is_exists_code($code))
        {
          $arr = array(
            'code' => $code,
            'name' => $name,
            'active' => $active
          );

          if( ! $this->bank_code_model->add($arr))
          {
            $sc = FALSE;
            $this->error = "เพิ่มธนาคารไม่สำเร็จ";
          }
        }
        else
        {
          $sc = FALSE;
          $this->error = "รหัสธนาคาร {$code} มีในระบบแล้ว กรุณาใช้รหัสอื่น";
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


  public function get($id)
  {
    $sc = TRUE;

    if($this->pm->can_edit)
    {
      $bank = $this->bank_code_model->get($id);

      if(empty($bank))
      {
        $sc = FALSE;
        set_error('notfound');
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
      'data' => $sc === TRUE ? $bank : NULL
    );

    echo json_encode($arr);
  }



	public function update()
	{
		$sc = TRUE;

		if($this->pm->can_edit)
		{
      $ds = json_decode(file_get_contents('php://input'));

      if( ! empty($ds))
      {
        $arr = array(
          'name' => $ds->name,
          'active' => $ds->active == 1 ? 1 : 0
        );

        if( ! $this->bank_code_model->update($ds->id, $arr))
        {
          $sc = FALSE;
          set_error('update');
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
      'message' => $sc === TRUE ? 'success' : $this->error
    );

		echo json_encode($arr);
	}


	public function delete()
	{
		$sc = TRUE;

    $id = $this->input->post('id');

		if($this->pm->can_delete)
		{
			if( ! empty($id))
			{
        $bank = $this->bank_code_model->get($id);

        if( ! empty($bank))
        {
          if( ! $this->bank_code_model->transection_exists($bank->code))
          {
            if( ! $this->bank_code_model->delete($id))
            {
              $sc = FALSE;
              set_error('delete');
            }
          }
          else
          {
            $sc = FALSE;
            set_error('transection', $bank->code);
          }
        }
        else
        {
          $sc = FALSE;
          $this->error = "Bank Code not found";
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



  public function clear_filter()
  {
    $filter = ['bank_code_code', 'bank_code_active'];

    return clear_filter($filter);
  }


} //---- end class
?>
