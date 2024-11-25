<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Change_password extends CI_Controller
{
  public $title = 'เปลี่ยนรหัสผ่าน';
	public $error;

	public function __construct()
	{
		parent::__construct();
		$this->home = base_url()."change_password";
	}


	public function index()
	{
    $code = get_cookie('uname');

    if(!empty($code))
    {
      $user = $this->user_model->get($code);
      if(!empty($user))
      {
        $ds['data'] = $user;
        $this->load->view('change_password', $ds);
      }
      else
      {
        //--- ถ้าไม่มีข้อมูล ให้ไป login ใหม่
        redirect(base_url().'users/authentication');
      }
    }
    else
    {
      //--- ถ้าไม่มีข้อมูล ให้ไป login ใหม่
  		redirect(base_url().'users/authentication');
    }

	}



	public function check_current_password()
	{
		$uname = $this->input->post('uname');
		$pwd = $this->input->post('pwd');

		$user = $this->user_model->get_user_credentials($uname);

		if(!empty($user))
		{
			if(password_verify($pwd, $user->pwd))
			{
				echo "valid";
			}
			else
			{
				echo "invalid";
			}
		}
		else
		{
			echo "Invalid user name : {$uname}";
		}

	}



  public function change()
	{
		$sc = TRUE;
		$uname = $this->input->post('uname');
		$pwd = $this->input->post('pwd');
		$new_pwd = $this->input->post('new_pwd');

		$user = $this->user_model->get_user_credentials($uname);

		if(!empty($user))
		{
			if(password_verify($pwd, $user->pwd))
			{
				//--- change password
				$password = password_hash($new_pwd, PASSWORD_DEFAULT);
				if(!$this->user_model->change_password($user->id, $password))
				{
					$sc = FALSE;
					$this->error = "เปลี่ยนรหัสผ่านไม่สำเร็จ";
				}
				else
				{
					$arr = array(
						'last_pass_change' => date('Y-m-d')
					);
					//--- update last pass change
					$this->user_model->update_user($user->id, $arr);
				}
			}
			else
			{
				$sc = FALSE;
				$this->error = "รหัสผ่านไม่ถูกต้อง";
			}
		}
		else
		{
			$sc = FALSE;
			$this->error = "Invalid Username : {$uname}";
		}

		echo $sc === TRUE ? 'success' : $this->error;
	}


  public function change_password()
	{
		if($this->input->post('user_id'))
		{
			$id = $this->input->post('user_id');
			$pwd = password_hash($this->input->post('pwd'), PASSWORD_DEFAULT);
			$rs = $this->user_model->change_password($id, $pwd);

			if($rs === TRUE)
			{
				$this->session->set_flashdata('success', 'Password changed');
			}
			else
			{
				$this->session->set_flashdata('error', 'Change password not successfull, please try again');
			}
		}

		redirect($this->home);
	}


}
 ?>
