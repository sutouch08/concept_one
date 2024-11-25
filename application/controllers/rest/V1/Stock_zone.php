<?php
require(APPPATH.'/libraries/REST_Controller.php');
use Restserver\Libraries\REST_Controller;

class Stock_zone extends REST_Controller
{
  public $ms;
  public $cn;
  public $error;

  public function __construct()
  {
    parent::__construct();
    $this->load->model('masters/zone_model');
    $this->load->model('stock/stock_model');
  }

  public function getStockZone_get()
  {
    //--- Get raw post data
    $json = file_get_contents("php://input");

    $data = json_decode($json);

    if(empty($data) OR empty($data->zone_code))
    {
      $arr = array(
        'status' => FALSE,
        'error' => 'empty data'
      );

      $this->response($arr, 400);
    }

    if( ! empty($data->zone_code))
    {
      $zone = $this->zone_model->get($data->zone_code);

      if( ! empty($zone))
      {
        $result = NULL;

        if($zone->is_consignment)
        {
          $this->cn = $this->load->database('cn', TRUE);
          $result = $this->stock_model->get_all_stock_consignment_zone($zone->code);
        }
        else
        {
          $this->ms = $this->load->database('ms', TRUE);
          $result = $this->stock_model->get_all_stock_in_zone($zone->code);
        }

        $arr = array(
          'status' => TRUE,
          'data' => $result,
          'count' => count($result),
          'error' => 'success'
        );

        $this->response($arr, 200);
      }
      else
      {
        $arr = array(
          'status' => FALSE,
          'error' => 'ไม่พบโซนในระบบ'
        );

        $this->response($arr, 200);
      }
    }
    else
    {
      $arr = array(
        'status' => FALSE,
        'error' => 'empty zone code'
      );

      $this->response($arr, 400);
    }
  }
}// End Class
