<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Wms_order_status extends CI_Controller
{
	public $wms;

  public function __construct()
  {
    parent::__construct();
		$this->wms = $this->load->database('wms', TRUE);
		$this->load->library('wms_order_status_api');
		$this->load->model('orders/order_state_model');
  }


	public function update_wms_status()
	{
		$sc = TRUE;
		$code = $this->input->get('order_code');

		$rs = $this->wms_order_status_api->get_wms_status($code);

		if(!empty($rs))
		{
			if($rs->SERVICE_RESULT->RESULT_STAUS === 'SUCCESS')
			{
				$status = $rs->SERVICE_RESULT->RESULT_DETAIL->ORDERS->ORDER->ORDER_STATUS;
				$state = $status == "CANCELED" ? 23 : ($status == "SHIPPED" ? 22 : ($status == "PACKED" ? 21 : ($status == "PACKING" ? 20 : ($status == "IN PROGRESS" ? 19 : 0))));

				if($state == 22)
				{
					$date = $rs->SERVICE_RESULT->RESULT_DETAIL->ORDERS->ORDER->SHIPMENT_DATETIME;
					$date = !empty($date) ? str_replace("/","-", $date ) : $date;
					$date_upd = date('Y-m-d H:i:s', strtotime($date));
				}
				else
				{
					$date_upd = date('Y-m-d H:i:s');
				}


				if(!empty($state))
				{
					if(!$this->order_state_model->is_exists_state($code, $state))
					{
						$arr = array(
							'order_code' => $code,
							'state' => $state,
							'update_user' => "api@wms",
							'date_upd' => $date_upd
						);

						$this->order_state_model->add_wms_state($arr);
					}
					else
					{
						$sc = FALSE;
						$this->error = "สถานะไม่มีการเปลี่ยนแปลง";
					}
				}
				else
				{
					$sc = FALSE;
					$this->error = "{$stats} : ไม่พบสถานะเอกสาร";
				}
			}
			else
			{
				$sc = FALSE;
				$this->error = $rs->SERVICE_RESULT->ERROR_CODE.' : '.$rs->SERVICE_RESULT->ERROR_MESSAGE;
			}
		}
		else
		{
			$sc = FALSE;
			$this->error = "No response";
		}

		echo $sc === TRUE ? 'success' : $this->error;
	}

} //--- end class

?>
