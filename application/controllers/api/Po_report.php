<?php
class Po_report extends CI_Controller
{
  private $host = "https://report-uat.konsys.co";
  private $endpoint = "/api/v1/receive-report";
  private $url = "";
  public $app_id = "UkxsaUx4bzh0WXE4Qzg3MTNlMjR3MldIcHd1NUJhVm4=";
  public $app_secret = "bWsxaHVHNVF3Q0hOWFJhSw==";
  public $home;
  public $ms;

  public function __construct()
  {
    parent::__construct();
    $this->url = $this->host.$this->endpoint;
    $this->ms = $this->load->database('ms', TRUE); //--- SAP database
    $this->home = base_url().'api/po_report';
  }


  public function index()
  {
    $this->load->view('auto/po_report_api');
  }


  public function do_export()
  {
    $this->load->model('api/po_report_model');

    $ds = array();

    $data = $this->po_report_model->get_open_po_details();

    if(!empty($data))
    {
      foreach($data as $rs)
      {
        $arr = array(
          'date_add' => date('d/m/Y H:i:s'),
          'product_code' => $rs->ItemCode,
          'purchased_qty' => floatval($rs->Quantity),
          'outstanding_qty' => floatval($rs->OpenQty)
        );

        array_push($ds, $arr);
      } //--- end foreach


      $setHeaders = array(
        "Content-Type:application/json",
        "applicationID:{$this->app_id}",
        "applicationSecret:{$this->app_secret}"
      );

      $apiUrl = str_replace(" ","%20",$this->url);
      $method = 'POST';
      $data = array('data'=> $ds);
      $data_string = json_encode($data);

      //echo $data_string;

      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, $apiUrl);
      curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
      curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
      curl_setopt($ch, CURLOPT_HTTPHEADER, $setHeaders);
      $response = curl_exec($ch);
      curl_close($ch);

      echo $response;
    }
  }

} //---- end calss
?>
