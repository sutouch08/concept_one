<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Bank extends PS_Controller
{
  public $menu_code = 'DBBANK';
  public $menu_sub_group_code = 'BANK';
	public $menu_group_code = 'DB';
	public $title = 'บัญชีธนาคาร';
	public $segment = 4;

  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'masters/bank';
    $this->load->model('masters/bank_model');
    $this->load->model('masters/bank_code_model');
    $this->load->helper('bank');
  }



  public function index()
  {
    $filter = array(
      'account_name' => get_filter('account_name', 'account_name', ''),
      'account_no' => get_filter('account_no', 'account_no', ''),
      'branch' => get_filter('branch', 'branch', ''),
      'bank_code' => get_filter('bank_code', 'bank_code', 'all')
    );

		//--- แสดงผลกี่รายการต่อหน้า
		$perpage = get_rows();

		$rows     = $this->bank_model->count_rows($filter);
		//--- ส่งตัวแปรเข้าไป 4 ตัว base_url ,  total_row , perpage = 20, segment = 3
		$init	    = pagination_config($this->home.'/index/', $rows, $perpage, $this->segment);
    $filter['data'] = $this->bank_model->get_list($filter, $perpage, $this->uri->segment($this->segment));

		$this->pagination->initialize($init);

    $this->load->view('masters/bank/bank_account_list', $filter);
  }




  public function add_new()
  {
    $this->load->view('masters/bank/bank_account_add');
  }


	public function add()
	{
		$sc = TRUE;

		if($this->pm->can_add)
		{
      $ds = json_decode($this->input->post('data'));

      if( ! empty($ds))
      {
        $bank = $this->bank_code_model->get_by_code($ds->bank_code);

        if( ! empty($bank))
        {
          if( ! $this->bank_model->is_exists($ds->account_no))
          {
            $arr = array(
              'bank_code' => $bank->code,
              'bank_name' => $bank->name,
              'branch' => $ds->branch,
              'acc_name' => $ds->account_name,
              'acc_no' => $ds->account_no,
              'sapAcctCode' => get_null($ds->sap_code),
              'active' => $ds->active == 1 ? 1 : 0
            );

            if( ! $this->bank_model->add($arr))
            {
              $sc = FALSE;
              $this->error = "เพิ่มบัญชีธนาคารไม่สำเร็จ";
            }
          }
          else
          {
            $sc = FALSE;
            $this->error = "เลขที่บัญชีซ้ำ";
          }
        }
        else
        {
          $sc = FALSE;
          $this->error = "กรุณาเลือกธนาคาร";
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


	public function edit($id)
	{
    if($this->pm->can_edit)
    {
      $account = $this->bank_model->get($id);

      if( ! empty($account))
      {
        $this->load->view('masters/bank/bank_account_edit', ['data' => $account]);
      }
      else
      {
        $this->page_error();
      }
    }
    else
    {
      $this->deny_page();
    }
	}



	public function update()
	{
		$sc = TRUE;

		if($this->pm->can_edit)
		{
      $ds = json_decode($this->input->post('data'));

      if( ! empty($ds))
      {
        $bank = $this->bank_code_model->get_by_code($ds->bank_code);

        if( ! empty($bank))
        {
          if( ! empty($ds->id))
          {
            if( ! $this->bank_model->is_exists($ds->account_no, $ds->id))
            {
              $arr = array(
                'bank_code' => $bank->code,
                'bank_name' => $bank->name,
                'branch' => $ds->branch,
                'acc_name' => $ds->account_name,
                'acc_no' => $ds->account_no,
                'sapAcctCode' => get_null($ds->sap_code),
                'active' => $ds->active == 1 ? 1 : 0
              );

              if( ! $this->bank_model->update($ds->id, $arr))
              {
                $sc = FALSE;
                $this->error = "แก้ไขบัญชีธนาคารไม่สำเร็จ";
              }
            }
            else
            {
              $sc = FALSE;
              $this->error = "เลขที่บัญชีซ้ำ";
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
          $this->error = "กรุณาเลือกธนาคาร";
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


	public function delete()
	{
		$sc = TRUE;

		if($this->pm->can_delete)
		{
      $id = $this->input->post('id');

			if( ! empty($id))
			{

        $account = $this->bank_model->get($id);

        if( ! empty($account))
        {
          //--- check transection
  				$this->load->model('orders/order_payment_model');

  				if($this->order_payment_model->has_account_transection($id))
  				{
  					$sc = FALSE;
  					set_error('transection', $account->acc_no);
  				}
  				else
  				{
  					if(! $this->bank_model->delete($id))
  					{
  						$sc = FALSE;
  						set_error('delete', $account->acc_no);
  					}
  				}
        }
        else
        {
          $sc = FALSE;
          set_error('notfound');
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



  public function clear_filter()
  {
    $filter = array(
      'account_name',
      'account_no',
      'branch',
      'bank_code'
    );

    clear_filter($filter);

    echo "done!";
  }


} //---- end class
?>
