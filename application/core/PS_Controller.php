<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class PS_Controller extends CI_Controller
{
  public $pm;
  public $home;
  public $ms;
  public $mc;
  public $cn;
  public $close_system;
  public $notibars;
  public $WC;
  public $WT;
  public $WS;
  public $WU;
  public $WQ;
  public $WV;
  public $RR;
  public $WL;
  public $isViewer;
	public $_user;
	public $_SuperAdmin = FALSE;
  public $_dataDate = '2021-01-01';
  public $error;


  public function __construct()
  {
    parent::__construct();


    //--- check is user has logged in ?
    _check_login();

    $uid = get_cookie('uid');

		$this->_user = $this->user_model->get_user_by_uid($uid);
		$this->isViewer = $this->_user->is_viewer == 1 ? TRUE : FALSE;
		$this->_SuperAdmin = $this->_user->id_profile == -987654321 ? TRUE : FALSE;

		$this->close_system   = getConfig('CLOSE_SYSTEM'); //--- ปิดระบบทั้งหมดหรือไม่

    if($this->close_system == 1 && $this->_SuperAdmin === FALSE)
    {
      redirect(base_url().'setting/maintenance');
    }

		if(!$this->isViewer && $this->is_expire_password($this->_user->last_pass_change))
		{
			redirect(base_url().'change_password');
		}



    //--- get permission for user
    $this->pm = get_permission($this->menu_code, $uid, get_cookie('id_profile'));

    $this->ms = $this->load->database('ms', TRUE); //--- SAP database
    $this->mc = $this->load->database('mc', TRUE); //--- Temp Database

    $dataDate = getConfig('DATA_DATE');
    if( ! empty($dataDate))
    {
      $this->_dataDate = $dataDate;
    }


    if(empty($this->menu_code) && $this->isViewer === FALSE)
    {
			$this->notibars = getConfig('NOTI_BAR');
			if($this->notibars == 1 )
			{
				$this->WC = get_permission('SOCCSO', $uid);
	  		$this->WT = get_permission('SOCCTR', $uid);
	  		$this->WS = get_permission('SOODSP', $uid);
	  		$this->WU = get_permission('ICSUPP', $uid);
	  		$this->WQ = get_permission('ICTRFM', $uid);
	      $this->WV = get_permission('ICTRFS', $uid);
	      $this->RR = get_permission('ICRQRC', $uid);
	      $this->WL = get_permission('ICLEND', $uid);
			}

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

  public function is_expire_password($last_pass_change)
	{
		$today = date('Y-m-d');

		$last_change = empty($last_pass_change) ? date('2021-01-01') : $last_pass_change;

		$expire_days = intval(getConfig('USER_PASSWORD_AGE'));

		if($expire_days != 0)
		{
			$expire_date = date('Y-m-d', strtotime("+{$expire_days} days", strtotime($last_change)));

			if($today > $expire_date)
			{
				return true;
			}
		}

		return FALSE;
	}

}

?>
