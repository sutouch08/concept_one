<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Pos extends CI_Controller
{
  public $pm;
  public $home;
  public $ms;
  public $close_system;
	public $_user;
	public $_SuperAdmin = FALSE;

  public function __construct()
  {
    parent::__construct();
  }

  public function index()
  {
    $uid = get_cookie('uid');

    if( ! $uid OR ! $this->user_model->verify_uid($uid))
    {
      redirect(base_url().'users/authentication/pos_login');
    }
    else
    {
      redirect(base_url().'orders/order_pos');
    }
  }

  public function get_pos_data()
  {
    $this->load->model('masters/shop_model');
    $this->load->model('masters/pos_model');

    $sc = TRUE;
    $ds = array();
    $deviceId = $this->input->get('deviceId');

    $pos = $this->pos_model->get_pos_by_device_id($deviceId);

    if( ! empty($pos))
    {
      $userList = $this->shop_model->get_shop_user($pos->shop_id);
      $user_list = array();

      if( ! empty($userList))
      {
        foreach($userList as $rs)
        {
          $arr = array('uname' => $rs->uname);
          $user_list[] = $arr;
        }        
      }

      $ds = array(
        'pos' => $pos,
        'users' => $user_list
      );
    }
    else
    {
      $sc = FALSE;
      $this->error = "ไม่พบข้อมูลเครื่อง POS";
    }

    $arr = array(
      'status' => $sc === TRUE ? 'success' : 'failed',
      'message' => $sc === TRUE ? 'success' : $this->error,
      'data' => $sc === TRUE ? $ds : NULL
    );

    echo json_encode($arr);
  }


} //--- end class
?>
