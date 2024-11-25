<?php
class Auto_export_move extends CI_Controller
{
  public $home;
  public $mc;
  public $ms;
  public function __construct()
  {
    parent::__construct();
    $this->ms = $this->load->database('ms', TRUE); //--- SAP database
    $this->mc = $this->load->database('mc', TRUE); //--- Temp Database
    $this->home = base_url().'auto/auto_export_move';
    $this->load->model('inventory/move_model');
  }

  public function index()
  {
    $this->load->view('auto/auto_export_move');
  }


  public function auto_export_move()
  {

  }


  //--- export to SAP
  public function export_move()
  {
    $sc = TRUE;
    $code = $this->input->post('code');
    if(!empty($code))
    {
      $this->load->library('export');
      if(! $this->export->export_move($code))
      {
        $sc = FALSE;
        $this->error = trim($this->export->error);
      }
    }

    echo $sc === TRUE ? 'success' : $this->error;
  }


  public function get_move_list()
  {
    $from_date = from_date($this->input->get('from_date'));
    $to_date = to_date($this->input->get('to_date'));
    $limit = $this->input->get('limit');

    $list = $this->move_model->get_un_export_list($from_date, $to_date, $limit);
    if(!empty($list))
    {
      $ds = array();
      foreach($list as $rs)
      {
        $ds[] = $rs->code;
      }

      echo json_encode($ds);
    }
    else
    {
      echo 'not_found';
    }
  }


} //--- end class
 ?>
