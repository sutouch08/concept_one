<?php
class Wms_product_api
{

  private $url;
  private $WH_NO; //--- Wharehouse no from WMS
	private $CUS_CODE; //---- Customer No from WMS
  public $home;
	public $wms;
	protected $ci;
  public $error;
	public $log_xml;

  public function __construct()
  {
		$this->ci =& get_instance();
		$this->ci->load->model('rest/V1/wms_error_logs_model');
		$this->url = getConfig('WMS_PM_URL');
		$this->WH_NO = getConfig('WMS_WH_NO');
		$this->CUST_CODE = getConfig('WMS_CUST_CODE');
		$this->log_xml = getConfig('LOG_XML');
  }


	//---- export
  public function export_style($style_code, $items = NULL)
  {
		$sc = TRUE;
		$xml = "";

		if(!empty($items)) //--- sku object
		{
			$xml .= "<WPM>";

			//--- Header section
			$xml .= "<HEADER>";
			$xml .=   "<WH_NO>".$this->WH_NO."</WH_NO>";
			$xml .=   "<CUST_CODE>".$this->CUST_CODE."</CUST_CODE>";
			$xml .= "</HEADER>";
			//---- End header_list section

			$xml .= "<ITEMS>";

			foreach($items as $item)
			{
				$xml .= "<ITEM>";
				$xml .=  "<SKU>{$item->code}</SKU>";
				$xml .=  "<NAME><![CDATA[{$item->name}]]></NAME>";
				$xml .=  "<UOM>{$item->unit_code}</UOM>";
				$xml .=  "<BARCODE>{$item->barcode}</BARCODE>";
				$xml .=  "<HEIGHT></HEIGHT>";
				$xml .=  "<LENGTH></LENGTH>";
				$xml .=  "<WIDTH></WIDTH>";
				$xml .=  "<WEIGHT_KG></WEIGHT_KG>";
				$xml .=  "<ITEMTRACKING_CODE></ITEMTRACKING_CODE>";
				$xml .= "</ITEM>";
			}

			$xml .= "</ITEMS>";
			//--- End header section
			$xml .= "</WPM>";

			if($this->log_xml)
			{
				$arr = array(
					'order_code' => $style_code,
					'xml_text' => $xml
				);

				$this->ci->wms_error_logs_model->log_xml($arr);
			}
		}
		else
		{
			$sc = FALSE;
			$this->error = "Empty Items";
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
					$this->ci->wms_error_logs_model->add($style_code, 'E', 'Error-'.$res->SERVICE_RESULT->ERROR_CODE.' : '.$res->SERVICE_RESULT->ERROR_MESSAGE);
				}
			}
			else
			{
				$this->ci->wms_error_logs_model->add($style_code, 'S', 'No response');
			}
    }


		if($sc === TRUE)
		{
			$this->ci->wms_error_logs_model->add($style_code, 'S', NULL);
		}
		else
		{
			$this->ci->wms_error_logs_model->add($style_code, 'E', $this->error);
		}

		return $sc;
  }




	//---- export
  public function export_item($item_code, $item = NULL)
  {
		$sc = TRUE;
		$xml = "";

		if(!empty($item)) //--- sku object
		{
			$xml .= "<WPM>";

			//--- Header section
			$xml .= "<HEADER>";
			$xml .=   "<WH_NO>".$this->WH_NO."</WH_NO>";
			$xml .=   "<CUST_CODE>".$this->CUST_CODE."</CUST_CODE>";
			$xml .= "</HEADER>";
			//---- End header_list section

			$xml .= "<ITEMS>";
			$xml .= "<ITEM>";
			$xml .=  "<SKU>{$item->code}</SKU>";
			$xml .=  "<NAME><![CDATA[{$item->name}]]></NAME>";
			$xml .=  "<UOM>{$item->unit_code}</UOM>";
			$xml .=  "<BARCODE>{$item->barcode}</BARCODE>";
			$xml .=  "<HEIGHT></HEIGHT>";
			$xml .=  "<LENGTH></LENGTH>";
			$xml .=  "<WIDTH></WIDTH>";
			$xml .=  "<WEIGHT_KG></WEIGHT_KG>";
			$xml .=  "<ITEMTRACKING_CODE></ITEMTRACKING_CODE>";
			$xml .= "</ITEM>";
			$xml .= "</ITEMS>";
			//--- End header section
			$xml .= "</WPM>";

			if($this->log_xml)
			{
				$arr = array(
					'order_code' => $item_code,
					'xml_text' => $xml
				);

				$this->ci->wms_error_logs_model->log_xml($arr);
			}
		}
		else
		{
			$sc = FALSE;
			$this->error = "Empty Items";
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
				$this->ci->wms_error_logs_model->add($item_code, 'S', 'No response');
			}
    }


		if($sc === TRUE)
		{
			$this->ci->wms_error_logs_model->add($item_code, 'S', NULL);
		}
		else
		{
			$this->ci->wms_error_logs_model->add($item_code, 'E', $this->error);
		}

		return $sc;
  }



	//---- export
  public function export_items($items = NULL)
  {
		$sc = TRUE;
		$xml = "";
		$first_item = "";
		$last_item = "";

		if(!empty($items)) //--- sku object
		{
			$xml .= "<WPM>";

			//--- Header section
			$xml .= "<HEADER>";
			$xml .=   "<WH_NO>".$this->WH_NO."</WH_NO>";
			$xml .=   "<CUST_CODE>".$this->CUST_CODE."</CUST_CODE>";
			$xml .= "</HEADER>";
			//---- End header_list section

			$xml .= "<ITEMS>";

			$i = 1;

			foreach($items as $item)
			{
				if($i === 1)
				{
					$first_item = $item->code;
				}

				$xml .= "<ITEM>";
				$xml .=  "<SKU>{$item->code}</SKU>";
				$xml .=  "<NAME><![CDATA[{$item->name}]]></NAME>";
				$xml .=  "<UOM>{$item->unit_code}</UOM>";
				$xml .=  "<BARCODE>{$item->barcode}</BARCODE>";
				$xml .=  "<HEIGHT></HEIGHT>";
				$xml .=  "<LENGTH></LENGTH>";
				$xml .=  "<WIDTH></WIDTH>";
				$xml .=  "<WEIGHT_KG></WEIGHT_KG>";
				$xml .=  "<ITEMTRACKING_CODE></ITEMTRACKING_CODE>";
				$xml .= "</ITEM>";

				$last_item = $item->code;
				$i++;
			}

			$xml .= "</ITEMS>";
			//--- End header section
			$xml .= "</WPM>";

		}
		else
		{
			$sc = FALSE;
			$this->error = "Empty Items";
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
					$this->ci->wms_error_logs_model->add($first_item.' - '.$last_item, 'E', 'Error-'.$res->SERVICE_RESULT->ERROR_CODE.' : '.$res->SERVICE_RESULT->ERROR_MESSAGE);
				}
			}

    }


		if($sc === TRUE)
		{
			$this->ci->wms_error_logs_model->add($first_item.' - '.$last_item, 'S', NULL);
		}
		else
		{
			$this->ci->wms_error_logs_model->add($first_item.' - '.$last_item, 'E', $this->error);
		}

		return $sc;
  }

}
?>
