<?php
require(APPPATH.'/libraries/REST_Controller.php');
use Restserver\Libraries\REST_Controller;

class Delivery extends REST_Controller
{
	public $error;
  public $user;
  public $wms;

	public function __construct()
  {
    parent::__construct();
		$this->wms = $this->load->database('wms', TRUE); //--- Temp database

		$this->load->model('rest/V1/wms_temp_order_model');
		$this->load->model('rest/V1/wms_error_logs_model');
  }


	public function create_post()
  {

		$message = "";
		$error_mesage = array();
    //--- Get raw post data
		$json = file_get_contents("php://input");
		$data_set = json_decode($json);
		$logs = FALSE;

		if($logs)
		{
			$arr = array(
				'order_code' => "test",
				'xml_text' => $json
			);
			$this->wms_error_logs_model->log_xml($arr);
		}

    if(empty($data_set))
    {
      $arr = array(
        'status' => FALSE,
        'error' => 'empty data'
      );

			$this->wms_error_logs_model->add(NULL, 'E', 'empty data');

      $this->response($arr, 400);
    }


		if(!empty($data_set))
		{
			$trans_no = $data_set->order_list_no;

			if(empty($data_set->data))
			{
				$arr = array(
	        'status' => FALSE,
	        'error' => 'empty data'
	      );

				$this->wms_error_logs_model->add(NULL, 'E', 'empty data', $trans_no);

	      $this->response($arr, 400);
			}
			else
			{

				foreach($data_set->data as $ds)
				{
					$arr = array(
						'shipped_date' => (empty($ds->shipped_date) ? now() : $ds->shipped_date),
						'code' => $ds->order_number,
						'reference' => get_null($ds->reference)
					);

					$sc = TRUE;
					$err = "";

					$this->wms->trans_begin();

					$is_completed = $this->wms_temp_order_model->is_order_completed($ds->order_number);

					if($is_completed)
					{
						$sc = FALSE;
						$err = $ds->order_number.' already completed';
						array_push($error_mesage, array('order_number' => $ds->order_number, 'error_message' => $err));
					}
					else
					{
						$not_complete = $this->wms_temp_order_model->get_temp_notcomplete_order($ds->order_number);

						if(!empty($not_complete))
						{
							foreach($not_complete as $rows)
							{
								//--- drop not complete before add new data
								if(! $this->wms_temp_order_model->drop_temp_exists_data($rows->id))
								{
									$sc = FALSE;
									$this->error = "ลบข้อมูลเก่าใน Temp ไม่สำเร็จ";
								}
							}
						}

						if($sc === TRUE)
						{
							$id = $this->wms_temp_order_model->add($arr);
							if(! $id)
							{
								$sc = FALSE;
								$error = $this->wms->error();
								$err = $error['message'];
								array_push($error_mesage, array('order_number' => $ds->order_number, 'error_message' => $err));
							}
							else
							{
								$details = $ds->details;
								if(!empty($details))
								{
									foreach($details as $rs)
									{
										$arr = array(
											'id_order' => $id,
											'order_code' => $ds->order_number,
											'product_code' => $rs->item,
											'qty' => $rs->qty
										);

										if(! $this->wms_temp_order_model->add_detail($arr))
										{
											$sc = FALSE;
											$error = $this->wms->error();
											$err = $error['message'];
											array_push($error_mesage, array('order_number' => $ds->order_number, 'error_message' => $err));

										}
									}
								}
								else
								{
									$sc = FALSE;
									$err = "Empty Order details";
									array_push($error_mesage, array('order_number' => $ds->order_number, 'status' => 'Empty Order details'));
								}
							}
						}

					}

					if($sc === TRUE)
					{
						$this->wms_error_logs_model->add($ds->order_number, 'S', NULL, $trans_no);
						$this->wms->trans_commit();
					}
					else
					{
						$this->wms->trans_rollback();
						$this->wms_error_logs_model->add($ds->order_number, 'E', $err, $trans_no);
					}
				}
			}

		}

		if(!empty($error_mesage))
		{
			$arr = array(
        'status' => FALSE,
        'error' => $error_mesage
      );

			$this->response($arr, 200);
		}
		else
		{
			$arr = array(
        'status' => 'SUCCESS',
        'order_list_no' => $trans_no
      );

      $this->response($arr, 200);
		}

	}//-- end create


}

//--- end class
?>
