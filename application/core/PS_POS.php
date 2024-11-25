<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class PS_POS extends CI_Controller
{
  public $pm;
  public $home;
  public $close_system;
	public $_user;
	public $_SuperAdmin = FALSE;
  public $_dataDate = '2021-01-01';

  public function __construct()
  {
    parent::__construct();


    //--- check is user has logged in ?
    _check_pos_login();

    $uid = get_cookie('uid');

		$this->_user = $this->user_model->get_user_by_uid($uid);
		$this->_SuperAdmin = $this->_user->id_profile == -987654321 ? TRUE : FALSE;

		$this->close_system   = getConfig('CLOSE_SYSTEM'); //--- ปิดระบบทั้งหมดหรือไม่

    if($this->close_system == 1 && $this->_SuperAdmin === FALSE)
    {
      redirect(base_url().'setting/maintenance');
    }

    //--- get permission for user
    $this->pm = get_permission($this->menu_code, $uid, get_cookie('id_profile'));

    $dataDate = getConfig('DATA_DATE');

    if( ! empty($dataDate))
    {
      $this->_dataDate = $dataDate;
    }
  }

  public function _response($sc = TRUE)
  {
    echo $sc === TRUE ? 'success' : $this->error;
  }

  public function deny_page()
  {
    return $this->load->view('deny_page');
  }


  public function error_page($err = NULL)
  {
		$error = array('error_message' => $err);
    return $this->load->view('page_error', $error);
  }

	public function page_error($err = NULL)
  {
		$error = array('error_message' => $err);
    return $this->load->view('page_error', $error);
  }
}

?>
