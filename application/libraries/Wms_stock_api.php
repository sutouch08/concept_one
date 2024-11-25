<?php
class Wms_stock_api
{

  private $url;
  private $WH_NO; //--- Wharehouse no from WMS
	private $CUS_CODE; //---- Customer No from WMS
  public $home;
	protected $ci;
  public $error;

  public function __construct()
  {
		$this->ci =& get_instance();
		$this->ci->load->model('rest/V1/wms_error_logs_model');
		$this->ci->load->model('stock/stock_model');
		$this->url = getConfig('WMS_STOCK_URL');
		$this->WH_NO = getConfig('WMS_WH_NO');
		$this->CUST_CODE = getConfig('WMS_CUST_CODE');

  }

	public function send_stock($warehouse_code)
	{
		$sc = TRUE;
		ini_set('memory_limit','512M'); // This also needs to be increased in some cases. Can be changed to a higher value as per need)
    ini_set('sqlsrv.ClientBufferMaxKBSize','524288'); // Setting to 512M
    ini_set('pdo_sqlsrv.client_buffer_max_kb_size','524288'); // Setting to 512M - for pdo_sqlsrv
    ini_set('max_execution_time', 600);

		$data = $this->ci->stock_model->get_items_stock($warehouse_code);

		if(!empty($data))
		{
			$xml  = "";
			$xml .= "<WSTOCK>";
			$xml .= "<HEADER>";
			$xml .= 	"<WH_NO>".getConfig('WMS_WH_NO')."</WH_NO>";
			$xml .= 	"<CUST_CODE>".getConfig('WMS_CUST_CODE')."</CUST_CODE>";
			$xml .= 	"<STOCK_DATE>".date('Y-m-d')."</STOCK_DATE>";
			$xml .= "</HEADER>";
			$xml .= "<ITEMS>";

			foreach($data as $item)
			{
				$xml .= "<ITEM>";
				$xml .= 	"<SKU>{$item->code}</SKU>";
				$xml .=  	"<NAME><![CDATA[{$item->name}]]></NAME>";
				$xml .=  	"<UOM>{$item->unit_code}</UOM>";
				$xml .=  	"<BARCODE>{$item->barcode}</BARCODE>";
				$xml .= 	"<QUANTITY>{$item->qty}</QUANTITY>";
				$xml .= "</ITEM>";
			}

			$xml .= "</ITEMS>";
			$xml .= "</WSTOCK>";
		}
		else
		{
			$sc = FALSE;
			$this->error = "No Item Found In {$warehouse_code}";
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
			}
			else
			{
				$this->ci->wms_error_logs_model->add('WSTOCK', 'S', 'No response');
			}
    }

		if($sc === TRUE)
		{
			$this->ci->wms_error_logs_model->add('WSTOCK', 'S', NULL);
		}
		else
		{
			$this->ci->wms_error_logs_model->add('WSTOCK', 'E', $this->error);
		}

		return $sc;
	}

}
?>
