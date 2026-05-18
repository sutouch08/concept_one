<?php
class Auto_cancel_invoice extends PS_Controller
{
  public $home;
  public $mc;
  public $ms;
  public $title = "Cancel Invoice";
  public $isViewer = FALSE;
  public $notibars = FALSE;
  public $menu_code = 'SOATCS';
  public $menu_group_code = 'SO';
  public $pm;
  public $error;

  public function __construct()
  {
    parent::__construct();
    //$this->ms = $this->load->database('ms', TRUE); //--- SAP database
    //$this->mc = $this->load->database('mc', TRUE); //--- Temp Database
    $this->home = base_url() . 'auto/auto_cancel_invoice';    
  }


  public function index()
  {    
    $limit = 100;

    $data = $this->get_all($limit);

    $ds['count'] = empty($data) ? 0 : count($data);
    $ds['all'] = $this->count_all();;
    $ds['limit'] = $limit;
    $ds['data'] = $data;

    $this->load->view('auto/auto_cancel_invoice', $ds);
  }


  public function get_all($limit = 100)
  {
    $rs = $this->db
    ->select('a.*, i.id AS invoice_id')
    ->from('auto_send_to_sap_order AS a')
    ->join('invoice AS i', 'a.code = i.code', 'left')
    ->where('a.status', 0)
    ->limit($limit)
    ->get();    

    if ($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function count_all()
  {
    $count = $this->db->where('status', 0)->count_all_results('auto_send_to_sap_order');

    return $count;
  }


  public function update_status()
  {
    $sc = TRUE;
    $code = $this->input->post('code');
    $status = $this->input->post('status');
    $message = $this->input->post('message');

    $ds = array(
      'status' => $status,
      'message' => $message
    );

    if (! $this->db->where('code', $code)->update('auto_send_to_sap_order', $ds))
    {
      $sc = FALSE;
      $this->error = "Update false";
    }

    echo $sc === TRUE ? 'success' : $this->error;
  }


  public function import_order()
  {
    ini_set('max_execution_time', 1200);
    ini_set('memory_limit', '1000M');

    $sc = TRUE;

    $file = isset($_FILES['uploadFile']) ? $_FILES['uploadFile'] : FALSE;
    $path = $this->config->item('upload_path') . 'import_files/';
    $file  = 'uploadFile';
    $config = array(   // initial config for upload class
      "allowed_types" => "xlsx",
      "upload_path" => $path,
      "file_name"  => "import_file",
      "max_size" => 5120,
      "overwrite" => TRUE
    );

    $this->load->library("upload", $config);

    if (! $this->upload->do_upload($file))
    {
      $sc = FALSE;
      $this->error = $this->upload->display_errors();
    }
    else
    {
      $info = $this->upload->data();
      $this->load->library('excel');
      /// read file
      $excel = PHPExcel_IOFactory::load($info['full_path']);
      //get only the Cell Collection
      $collection  = $excel->getActiveSheet()->toArray(NULL, TRUE, TRUE, TRUE);

      if (! empty($collection))
      {
        $i = 1;
        $j = 0;
        $ds = [];
        $ro = [];

        foreach ($collection as $rs)
        {
          if ($i > 1)
          {
            $j++;
            $ro[] = array('code' => trim($rs['A']));

            if ($j == 1000)
            {
              $j = 0;
              $ds[] = $ro;
              $ro = [];
            }
          }

          $i++;
        }

        $ds[] = $ro;

        if (! empty($ds))
        {
          foreach ($ds as $rows)
          {
            if (! $this->insert($rows))
            {
              $sc = FALSE;
              $this->error = "Cannot insert data";
            }
          }
        }
      }
      else
      {
        $sc = FALSE;
        $this->error = "Cannot get data from import file : empty data collection";
      }
    }

    $this->_response($sc);
  }



  public function insert(array $ds = array())
  {
    if (! empty($ds))
    {
      return $this->db->insert_batch('auto_send_to_sap_order', $ds);
    }

    return FALSE;
  }


  public function clear_data()
  {
    $sc = TRUE;

    $qr = "TRUNCATE TABLE auto_send_to_sap_order";

    if (! $this->db->query($qr))
    {
      $sc = FALSE;
      $this->error = "Failed to clear data";
    }

    $this->_response($sc);
  } 
} //--- end class
