<?php
require(APPPATH.'/libraries/REST_Controller.php');
use Restserver\Libraries\REST_Controller;

class Customers extends REST_Controller
{
  public $error;
  public $user;

  public function __construct()
  {
    parent::__construct();

    $this->load->model('masters/customers_model');
    $this->user = 'api@warrix';
  }

  public function index_get($code)
  {
    if(empty($code))
    {
      $arr = array(
        'status' => FALSE,
        'error' => "Customer code is required"
      );

      $this->response($arr, 400);
    }
  }
} //--- end class
?>
