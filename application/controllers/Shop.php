<?php
class Shop extends CI_Controller
{
  public function __construct()
  {
    parent::__construct();
  }

  public function get_shop_user_by_device_id()
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

      if( ! empty($userList))
      {
        foreach($userList as $rs)
        {
          $arr = array('uname' => $rs->uname);

          array_push($ds, $arr);
        }
      }
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
}

 ?>
