<?php
require(APPPATH.'/libraries/REST_Controller.php');
use Restserver\Libraries\REST_Controller;

class Zone extends REST_Controller
{
  public $error;
  public $user;

  public function __construct()
  {
    parent::__construct();
    $this->user = 'api@warrix';
  }

	public function countUpdateZone_get()
	{
    $rs = $this->db
    ->from('zone AS z')
    ->join('warehouse AS w', 'z.warehouse_code = w.code', 'left')
    //->where('w.role', 2)
    ->where('z.name !=', '')
    ->where('z.name IS NOT NULL', NULL, FALSE)
    ->count_all_results();

    $arr = array(
      'status' => TRUE,
      'count' => $rs
    );

    $this->response($arr, 200);
	}


	public function getUpdateZone_get()
	{
		$json = file_get_contents("php://input");
		$ds = json_decode($json);

		if(! empty($ds))
		{
			$limit = $ds->limit;
			$offset = $ds->offset;

      $rs = $this->db
      ->select('z.*')
      ->select('w.code AS warehouse_code, w.name AS warehouse_name, w.is_consignment')
      ->from('zone AS z')
      ->join('warehouse AS w', 'z.warehouse_code = w.code', 'left')
      //->where('w.role', 2)
      ->where('z.name !=', '')
      ->where('z.name IS NOT NULL', NULL, FALSE)
      ->limit($limit, $offset)
			->get();

			if($rs->num_rows() > 0)
			{
        $arr = array(
          'status' => TRUE,
          'rows' => $rs->num_rows(),
          'items' => $rs->result()
        );
			}
      else
      {
        $arr = array(
          'status' => TRUE,
          'rows' => 0,
          'items' => NULL
        );
      }

      $this->response($arr, 200);
		}
		else
		{
			$arr = array(
				'status' => FALSE,
				'error' => 'Missing required parameter'
			);

			$this->response($arr, 400);
		}
	}

} //--- end class
