<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Payment_methods extends PS_Controller
{
  public $menu_code = 'DBPAYM';
	public $menu_group_code = 'DB';
	public $title = 'ช่องทางการชำระเงิน';

  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'masters/payment_methods';
    $this->load->model('masters/payment_methods_model');
    $this->load->helper('payment_method');
    $this->load->helper('bank');
  }


  public function index()
  {
    $filter = array(
      'code' => get_filter('code', 'pm_code', ''),
      'name' => get_filter('name', 'pm_name', ''),
      'role' => get_filter('role', 'pm_role', 'all'),
      'has_term' => get_filter('has_term', 'pm_term', 'all')
    );

		//--- แสดงผลกี่รายการต่อหน้า
		$perpage = get_filter('set_rows', 'rows', 20);
		//--- หาก user กำหนดการแสดงผลมามากเกินไป จำกัดไว้แค่ 300
		if($perpage > 300)
		{
			$perpage = get_filter('rows', 'rows', 300);
		}

		$segment = 4; //-- url segment
		$rows = $this->payment_methods_model->count_rows($filter);
		//--- ส่งตัวแปรเข้าไป 4 ตัว base_url ,  total_row , perpage = 20, segment = 3
		$init	= pagination_config($this->home.'/index/', $rows, $perpage, $segment);
    $filter['data'] = $this->payment_methods_model->get_list($filter, $perpage, $this->uri->segment($segment));

		$this->pagination->initialize($init);
    $this->load->view('masters/payment_methods/payment_methods_list', $filter);
  }


  public function add_new()
  {
    $this->load->view('masters/payment_methods/payment_methods_add');
  }


  public function add()
  {
    $sc = TRUE;

    if($this->input->post('code'))
    {
      $code = $this->input->post('code');
      $name = $this->input->post('name');
      $role = $this->input->post('role');
      $account_id = get_null($this->input->post('account'));
      $has_term = ($role == 4 OR $role == 5) ? 1 : 0;

      $ds = array(
        'code' => $code,
        'name' => $name,
        'has_term' => $has_term,
        'role' => $role,
        'account_id' => $account_id
      );

      if($sc === TRUE && $this->payment_methods_model->is_exists($code))
      {
        $sc = FALSE;
        set_error('exists', $code);
      }

      if($sc === TRUE && $this->payment_methods_model->is_exists_name($name))
      {
        $sc = FALSE;
        set_error('exists', $name);
      }

      if($sc === TRUE)
      {
        if( ! $this->payment_methods_model->add($ds))
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

    $this->_response($sc);
  }


  public function edit($code)
  {
    $data = $this->payment_methods_model->get_payment_methods($code);

    if( ! empty($data))
    {
      $this->load->view('masters/payment_methods/payment_methods_edit', $data);
    }
    else
    {
      $this->error_page();
    }
  }



  public function update()
  {
    $sc = TRUE;

    if($this->input->post('id'))
    {
      $id = $this->input->post('id');
      $code = $this->input->post('code');
      $name = trim($this->input->post('name'));
      $role = $this->input->post('role');
      $account_id = get_null($this->input->post('account'));
      $has_term = ($role == 4 OR $role == 5) ? 1 : 0;

      $ds = array(
        'code' => $code,
        'name' => $name,
        'role' => $role,
        'account_id' => $account_id,
        'has_term' => $has_term
      );

      if($sc === TRUE && $this->payment_methods_model->is_exists($code, $id))
      {
        $sc = FALSE;
        set_error('exists', $code);
      }

      if($sc === TRUE && $this->payment_methods_model->is_exists_name($name, $id))
      {
        $sc = FALSE;
        set_error('exists', $name);
      }

      if($sc === TRUE)
      {
        if( ! $this->payment_methods_model->update_by_id($id, $ds))
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

    $this->_response($sc);
  }



  public function delete($code)
  {
    if($code != '')
    {
      if($this->payment_methods_model->delete($code))
      {
        set_message('Payment channels deleted');
      }
      else
      {
        set_error('Cannot delete payment channels');
      }
    }
    else
    {
      set_error('payment channels not found');
    }

    redirect($this->home);
  }



  //--- เช็คว่าการชำระเงินเป็นแบบเครดิตหรือไม่
  public function is_credit_payment($code)
  {
    //---- ตรวจสอบว่าเป็นเครดิตหรือไม่
    $rs = $this->payment_methods_model->has_term($code);
    echo $rs === TRUE ? 1 : 0;
  }


  public function clear_filter()
	{

		return clear_filter(array('pm_code', 'pm_name', 'pm_role', 'pm_term'));

	}

}//--- end class
 ?>
