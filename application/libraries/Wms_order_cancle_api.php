<?php
class Wms_order_cancle_api
{
	private $url;
  private $WH_NO; //--- Wharehouse no from WMS
	private $CUST_CODE; //---- Customer No from WMS
	public $wms;
	protected $ci;
  public $error;
	public $log_xml;
	public $type = 'cancle';

  public function __construct()
  {
		$this->ci =& get_instance();
		$this->ci->load->model('rest/V1/wms_error_logs_model');
		$this->url = getConfig('WMS_CN_URL');
		$this->WH_NO = getConfig('WMS_WH_NO');
		$this->CUST_CODE = getConfig('WMS_CUST_CODE');
		$this->log_xml = getConfig('LOG_XML');
  }


	public function send_data($code, $reason = NULL)
	{
		$sc = TRUE;

		$xml  = "<WCO>";
		$xml .= "<HEADER>";
		$xml .= "<WH_NO>{$this->WH_NO}</WH_NO>";
		$xml .= "<CUST_CODE>{$this->CUST_CODE}</CUST_CODE>";
		$xml .= "</HEADER>";
		$xml .= "<ORDERS>";
		$xml .= "<ORDER_NO>{$code}</ORDER_NO>";
		$xml .= "</ORDERS>";
		$xml .= "</WCO>";

		if($this->log_xml)
		{
			$arr = array(
				'order_code' => $code,
				'xml_text' => $xml
			);

			$this->ci->wms_error_logs_model->log_xml($arr);
		}

		if($sc === TRUE && !empty($xml))
    {
      $ch = curl_init();

      curl_setopt($ch, CURLOPT_URL, $this->url);
      curl_setopt($ch, CURLOPT_POST, TRUE);
      curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
      curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: text/xml'));

      $response = curl_exec($ch);

      curl_close($ch);


      $res = json_decode(json_encode(simplexml_load_string($response)));


			if(!empty($res))
			{

				if($res->SERVICE_RESULT->RESULT_STAUS != 'SUCCESS')
				{
					$sc = FALSE;
					$this->error = $res->SERVICE_RESULT->ERROR_CODE.' : '.$res->SERVICE_RESULT->ERROR_MESSAGE;
				}
				else
				{
					if($res->SERVICE_RESULT->RESULT_DETAIL->ORDERS->ORDER->CANCEL_STATUS != 'ACCEPT')
					{
						$sc = FALSE;
						$this->error = $res->SERVICE_RESULT->RESULT_DETAIL->ORDERS->ORDER->NOTE;
					}
				}
			}
			else
			{
				$sc = FALSE;
				$this->error = "No response";
			}
    }

		if($sc === TRUE)
		{
			$this->ci->wms_error_logs_model->add($code, 'S', NULL, $this->type);
		}
		else
		{
			$this->ci->wms_error_logs_model->add($code, 'E', $this->error, $this->type);
		}

		return $sc;
	}

}
 ?>
