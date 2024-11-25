<?php
class Auto_expire_transfer extends CI_Controller
{

  public function __construct()
  {
    parent::__construct();
    $this->load->model('exp_model');
  }

  public function auto_expire()
	{
		$limit = getConfig('TRANSFER_EXPIRATION');
    $eom = getConfig('TRANSFER_EXPIRE_EOM');

    $today = date('Y-m-d');
    $endOfMonth = date('Y-m-t');
    $end_date = date('Y-m-d 23:59:59', strtotime("-{$limit} days"));
    $exp_date = ($eom && $today == $endOfMonth) ? date('Y-m-t 23:59:59') : $end_date;

    if($limit > 0 OR ($eom && $today == $endOfMonth))
    {
      //--- WW
      $this->exp_model->expire_ww($exp_date);
      //--- MV
      $this->exp_model->expire_mv($exp_date);
      //--- RT
      $this->exp_model->expire_rt($exp_date);
      //--- RN
      $this->exp_model->expire_rn($exp_date);
      //--- WR
      $this->exp_model->expire_wr($exp_date);
      //--- SM
      $this->exp_model->expire_sm($exp_date);      
    }
	}


} //--- end class
 ?>
