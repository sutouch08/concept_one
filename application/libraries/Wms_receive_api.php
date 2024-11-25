<?php
class Wms_receive_api
{

  private $url;
  private $WH_NO; //--- Wharehouse no from WMS
	private $CUS_CODE; //---- Customer No from WMS
	private $ORDER_LIST_NO = "";  //---- will be generate
	public $wms;
	protected $ci;
  public $error;
	public $log_xml;
	public $type = 'IB';
	public $sup_code = "WARRIX";
	public $sup_name = "Warrix Co., Ltd.";


  public function __construct()
  {
		$this->ci =& get_instance();
		$this->ci->load->model('rest/V1/wms_error_logs_model');
		$this->log_xml = getConfig('LOG_XML');
		$this->url = getConfig('WMS_IB_URL');
		$this->WH_NO = getConfig('WMS_WH_NO');
		$this->CUS_CODE = getConfig('WMS_CUST_CODE');
  }


	//--- export return order
	public function export_return_order($doc, $details)
	{
		// Assign the CodeIgniter super-object

		$sc = TRUE;
		$this->type = "SM";
		$xml = "";

		if(!empty($doc))
		{

			if(!empty($details))
			{
				$xml .= "<WIB>";

				//--- Header_list section
				$xml .= "<HEADER>";
				$xml .=   "<WH_NO>".$this->WH_NO."</WH_NO>";
				$xml .=   "<CUST_CODE>".$this->CUS_CODE."</CUST_CODE>";
				$xml .= "</HEADER>";
				//---- End header_list section

				//--- Order Start
				$xml .= "<ORDER>";
				$xml .=   "<ORDER_NO>".$doc->code."</ORDER_NO>";
				$xml .=   "<ORDER_TYPE>".$this->type."</ORDER_TYPE>";
				$xml .=   "<ORDER_DATE>".date('Y/m/d')."</ORDER_DATE>";
				$xml .=   "<SUPPLIER_CODE>CUSTOMER</SUPPLIER_CODE>";
				$xml .=   "<SUPPLIER_NAME><![CDATA[{$doc->customer_name}]]></SUPPLIER_NAME>";
				$xml .=   "<SUPPLIER_ADDRESS1></SUPPLIER_ADDRESS1>";
				$xml .=   "<SUPPLIER_ADDRESS2></SUPPLIER_ADDRESS2>";
				$xml .=   "<REF_NO1>".$doc->invoice."</REF_NO1>";
				$xml .=   "<REF_NO2></REF_NO2>";
				$xml .=   "<REMARK><![CDATA[".$doc->remark."]]></REMARK>";
				$xml .= "</ORDER>";
					//--- Item start
				$xml .= "<ITEMS>";

				foreach($details as $rs)
				{
					if($rs->qty > 0 && $rs->count_stock)
					{
						$xml .= "<ITEM>";
						$xml .= "<ITEM_NO>".$rs->product_code."</ITEM_NO>";
						$xml .= "<ITEM_DESC><![CDATA[".$rs->product_name."]]></ITEM_DESC>";
						$xml .= "<VARIANT></VARIANT>";
						$xml .= "<LOT_NO></LOT_NO>";
						$xml .= "<EXP_DATE></EXP_DATE>";
						$xml .= "<SERIAL_NO></SERIAL_NO>";
						$xml .= "<QUANTITY>".round($rs->qty,2)."</QUANTITY>";
						$xml .= "<UOM>".$rs->unit_code."</UOM>";
						$xml .= "<OUTBOUND_ORDER_NO>".$rs->order_code."</OUTBOUND_ORDER_NO>";
						$xml .= "</ITEM>";
					}
				}

				$xml .= "</ITEMS>";
				//--- End header section
				$xml .= "</WIB>";


				if($this->log_xml)
				{
					$arr = array(
						'order_code' => $doc->code,
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
					}
		    }
			}
			else
			{
				$sc = FALSE;
				$this->error = "No data";
			}
		}
		else
		{
			$sc = FALSE;
			$this->error = "Invalid document data";
		}

		if($sc === TRUE)
		{
			$this->ci->wms_error_logs_model->add($doc->code, 'S', NULL, $this->type);
		}
		else
		{
			$this->ci->wms_error_logs_model->add($doc->code, 'E', $this->error, $this->type);
		}

		return $sc;
	}



	//--- export return order
	public function export_return_consignment($doc, $details)
	{
		// Assign the CodeIgniter super-object

		$sc = TRUE;
		$this->type = "CN";
		$xml = "";

		if(!empty($doc))
		{

			if(!empty($details))
			{
				$xml .= "<WIB>";

				//--- Header_list section
				$xml .= "<HEADER>";
				$xml .=   "<WH_NO>".$this->WH_NO."</WH_NO>";
				$xml .=   "<CUST_CODE>".$this->CUS_CODE."</CUST_CODE>";
				$xml .= "</HEADER>";
				//---- End header_list section

				//--- Order Start
				$xml .= "<ORDER>";
				$xml .=   "<ORDER_NO>".$doc->code."</ORDER_NO>";
				$xml .=   "<ORDER_TYPE>".$this->type."</ORDER_TYPE>";
				$xml .=   "<ORDER_DATE>".date('Y/m/d')."</ORDER_DATE>";
				$xml .=   "<SUPPLIER_CODE>CUSTOMER</SUPPLIER_CODE>";
				$xml .=   "<SUPPLIER_NAME><![CDATA[{$doc->customer_name}]]></SUPPLIER_NAME>";
				$xml .=   "<SUPPLIER_ADDRESS1></SUPPLIER_ADDRESS1>";
				$xml .=   "<SUPPLIER_ADDRESS2></SUPPLIER_ADDRESS2>";
				$xml .=   "<REF_NO1>".$doc->invoice."</REF_NO1>";
				$xml .=   "<REF_NO2></REF_NO2>";
				$xml .=   "<REMARK><![CDATA[".$doc->remark."]]></REMARK>";
				$xml .= "</ORDER>";
					//--- Item start
				$xml .= "<ITEMS>";

				foreach($details as $rs)
				{
					if($rs->qty > 0 && $rs->count_stock)
					{
						$xml .= "<ITEM>";
						$xml .= "<ITEM_NO>".$rs->product_code."</ITEM_NO>";
						$xml .= "<ITEM_DESC><![CDATA[".$rs->product_name."]]></ITEM_DESC>";
						$xml .= "<VARIANT></VARIANT>";
						$xml .= "<LOT_NO></LOT_NO>";
						$xml .= "<EXP_DATE></EXP_DATE>";
						$xml .= "<SERIAL_NO></SERIAL_NO>";
						$xml .= "<QUANTITY>".round($rs->qty,2)."</QUANTITY>";
						$xml .= "<UOM>".$rs->unit_code."</UOM>";
						$xml .= "<OUTBOUND_ORDER_NO></OUTBOUND_ORDER_NO>";
						$xml .= "</ITEM>";
					}
				}

				$xml .= "</ITEMS>";
				//--- End header section
				$xml .= "</WIB>";


				if($this->log_xml)
				{
					$arr = array(
						'order_code' => $doc->code,
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
					}
		    }
			}
			else
			{
				$sc = FALSE;
				$this->error = "No data";
			}
		}
		else
		{
			$sc = FALSE;
			$this->error = "Invalid document data";
		}

		if($sc === TRUE)
		{
			$this->ci->wms_error_logs_model->add($doc->code, 'S', NULL, $this->type);
		}
		else
		{
			$this->ci->wms_error_logs_model->add($doc->code, 'E', $this->error, $this->type);
		}

		return $sc;
	}



	//--- export return lend
	public function export_return_lend($doc, $details)
	{
		$sc = TRUE;
		$this->type = "RN";
		$xml = "";

		if(!empty($doc))
		{

			if(!empty($details))
			{
				$xml .= "<WIB>";

				//--- Header_list section
				$xml .= "<HEADER>";
				$xml .=   "<WH_NO>".$this->WH_NO."</WH_NO>";
				$xml .=   "<CUST_CODE>".$this->CUS_CODE."</CUST_CODE>";
				$xml .= "</HEADER>";
				//---- End header_list section

				//--- Order Start
				$xml .= "<ORDER>";
				$xml .=   "<ORDER_NO>".$doc->code."</ORDER_NO>";
				$xml .=   "<ORDER_TYPE>".$this->type."</ORDER_TYPE>";
				$xml .=   "<ORDER_DATE>".date('Y/m/d')."</ORDER_DATE>";
				$xml .=   "<SUPPLIER_CODE><![CDATA[{$this->sup_code}]]></SUPPLIER_CODE>";
				$xml .=   "<SUPPLIER_NAME><![CDATA[{$this->sup_name}]]></SUPPLIER_NAME>";
				$xml .=   "<SUPPLIER_ADDRESS1></SUPPLIER_ADDRESS1>";
				$xml .=   "<SUPPLIER_ADDRESS2></SUPPLIER_ADDRESS2>";
				$xml .=   "<REF_NO1>".$doc->lend_code."</REF_NO1>";
				$xml .=   "<REF_NO2></REF_NO2>";
				$xml .=   "<REMARK><![CDATA[".$doc->remark."]]></REMARK>";
				$xml .= "</ORDER>";
					//--- Item start
				$xml .= "<ITEMS>";

				foreach($details as $rs)
				{
					if($rs->qty > 0)
					{
						$xml .= "<ITEM>";
						$xml .= "<ITEM_NO>".$rs->product_code."</ITEM_NO>";
						$xml .= "<ITEM_DESC><![CDATA[".$rs->product_name."]]></ITEM_DESC>";
						$xml .= "<VARIANT></VARIANT>";
						$xml .= "<LOT_NO></LOT_NO>";
						$xml .= "<EXP_DATE></EXP_DATE>";
						$xml .= "<SERIAL_NO></SERIAL_NO>";
						$xml .= "<QUANTITY>".round($rs->qty,2)."</QUANTITY>";
						$xml .= "<UOM>".$rs->unit_code."</UOM>";
						$xml .= "</ITEM>";
					}
				}

				$xml .= "</ITEMS>";
				//--- End header section
				$xml .= "</WIB>";

				if($this->log_xml)
				{
					$arr = array(
						'order_code' => $doc->code,
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
					}
		    }
			}
			else
			{
				$sc = FALSE;
				$this->error = "No data";
			}
		}
		else
		{
			$sc = FALSE;
			$this->error = "Invalid document data";
		}

		if($sc === TRUE)
		{
			$this->ci->wms_error_logs_model->add($doc->code, 'S', NULL, $this->type);
		}
		else
		{
			$this->ci->wms_error_logs_model->add($doc->code, 'E', $this->error, $this->type);
		}

		return $sc;
	}

	//---- export receive po
	public function export_receive_po($doc, $po_code, $invoice, $details)
	{
		$sc = TRUE;
		$this->type = "WR";
		$xml = "";


		if(!empty($details))
		{
			$xml .= "<WIB>";

			//--- Header_list section
			$xml .= "<HEADER>";
			$xml .=   "<WH_NO>".$this->WH_NO."</WH_NO>";
			$xml .=   "<CUST_CODE>".$this->CUS_CODE."</CUST_CODE>";
			$xml .= "</HEADER>";
			//---- End header_list section

			//--- Order Start
			$xml .= "<ORDER>";
			$xml .=   "<ORDER_NO>".$doc->code."</ORDER_NO>";
			$xml .=   "<ORDER_TYPE>".$this->type."</ORDER_TYPE>";
			$xml .=   "<ORDER_DATE>".date('Y/m/d')."</ORDER_DATE>";
			$xml .=   "<SUPPLIER_CODE><![CDATA[".$doc->vendor_code."]]></SUPPLIER_CODE>";
			$xml .=   "<SUPPLIER_NAME><![CDATA[".$doc->vendor_name."]]></SUPPLIER_NAME>";
			$xml .=   "<SUPPLIER_ADDRESS1></SUPPLIER_ADDRESS1>";
			$xml .=   "<SUPPLIER_ADDRESS2></SUPPLIER_ADDRESS2>";
			$xml .=   "<REF_NO1>".$po_code."</REF_NO1>";
			$xml .=   "<REF_NO2>".$invoice."</REF_NO2>";
			$xml .=   "<REMARK><![CDATA[".$doc->remark."]]></REMARK>";
			$xml .= "</ORDER>";
				//--- Item start
			$xml .= "<ITEMS>";

			foreach($details as $rs)
			{

				if($rs->qty > 0)
				{
					$xml .= "<ITEM>";
					$xml .= "<ITEM_NO>".$rs->product_code."</ITEM_NO>";
					$xml .= "<ITEM_DESC><![CDATA[".$rs->product_name."]]></ITEM_DESC>";
					$xml .= "<VARIANT></VARIANT>";
					$xml .= "<LOT_NO></LOT_NO>";
					$xml .= "<EXP_DATE></EXP_DATE>";
					$xml .= "<SERIAL_NO></SERIAL_NO>";
					$xml .= "<QUANTITY>".round($rs->qty,2)."</QUANTITY>";
					$xml .= "<UOM>".$rs->unit_code."</UOM>";
					$xml .= "</ITEM>";
				}
			}

			$xml .= "</ITEMS>";
			//--- End header section
			$xml .= "</WIB>";

			if($this->log_xml)
			{
				$arr = array(
					'order_code' => $doc->code,
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
				}
			}
		}
		else
		{
			$sc = FALSE;
			$this->error = "No data";
		}

		if($sc === TRUE)
		{
			$this->ci->wms_error_logs_model->add($doc->code, 'S', NULL, $this->type);
		}
		else
		{
			$this->ci->wms_error_logs_model->add($doc->code, 'E', $this->error, $this->type);
		}

		return $sc;
	}



	//---- export receive transform
  public function export_receive_transform($doc, $order_code, $invoice, $details)
  {
		$sc = TRUE;
		$this->type = "RT";
		$xml = "";


    if(!empty($details))
		{
			$xml .= "<WIB>";

			//--- Header_list section
			$xml .= "<HEADER>";
			$xml .=   "<WH_NO>".$this->WH_NO."</WH_NO>";
			$xml .=   "<CUST_CODE>".$this->CUS_CODE."</CUST_CODE>";
			$xml .= "</HEADER>";
			//---- End header_list section

			//--- Order Start
			$xml .= "<ORDER>";
			$xml .=   "<ORDER_NO>".$doc->code."</ORDER_NO>";
			$xml .=   "<ORDER_TYPE>".$this->type."</ORDER_TYPE>";
			$xml .=   "<ORDER_DATE>".date('Y/m/d')."</ORDER_DATE>";
			$xml .=   "<SUPPLIER_CODE>{$this->sup_code}</SUPPLIER_CODE>";
			$xml .=   "<SUPPLIER_NAME><![CDATA[{$this->sup_name}]]></SUPPLIER_NAME>";
			$xml .=   "<SUPPLIER_ADDRESS1></SUPPLIER_ADDRESS1>";
			$xml .=   "<SUPPLIER_ADDRESS2></SUPPLIER_ADDRESS2>";
			$xml .=   "<REF_NO1>".$order_code."</REF_NO1>";
			$xml .=   "<REF_NO2>".$invoice."</REF_NO2>";
			$xml .=   "<REMARK><![CDATA[".$doc->remark."]]></REMARK>";
			$xml .= "</ORDER>";
				//--- Item start
			$xml .= "<ITEMS>";

			foreach($details as $rs)
			{

				if($rs->qty > 0)
				{
					$xml .= "<ITEM>";
					$xml .= "<ITEM_NO>".$rs->product_code."</ITEM_NO>";
					$xml .= "<ITEM_DESC><![CDATA[".$rs->product_name."]]></ITEM_DESC>";
					$xml .= "<VARIANT></VARIANT>";
					$xml .= "<LOT_NO></LOT_NO>";
					$xml .= "<EXP_DATE></EXP_DATE>";
					$xml .= "<SERIAL_NO></SERIAL_NO>";
					$xml .= "<QUANTITY>".round($rs->qty,2)."</QUANTITY>";
					$xml .= "<UOM>".$rs->unit_code."</UOM>";
					$xml .= "</ITEM>";
				}
			}

			$xml .= "</ITEMS>";
			//--- End header section
			$xml .= "</WIB>";

			if($this->log_xml)
			{
				$arr = array(
					'order_code' => $doc->code,
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
				}
	    }
		}
		else
		{
			$sc = FALSE;
			$this->error = "No data";
		}

		if($sc === TRUE)
		{
			$this->ci->wms_error_logs_model->add($doc->code, 'S', NULL, $this->type);
		}
		else
		{
			$this->ci->wms_error_logs_model->add($doc->code, 'E', $this->error, $this->type);
		}

		return $sc;
  }



	//--- export receive transfer to  wms
	public function export_transfer($doc, $details)
	{
		// Assign the CodeIgniter super-object

		$sc = TRUE;
		$this->type = "WW";
		$xml = "";

		if(!empty($doc))
		{

			if(!empty($details))
			{
				$xml .= "<WIB>";

				//--- Header_list section
				$xml .= "<HEADER>";
				$xml .=   "<WH_NO>".$this->WH_NO."</WH_NO>";
				$xml .=   "<CUST_CODE>".$this->CUS_CODE."</CUST_CODE>";
				$xml .= "</HEADER>";
				//---- End header_list section

				//--- Order Start
				$xml .= "<ORDER>";
				$xml .=   "<ORDER_NO>".$doc->code."</ORDER_NO>";
				$xml .=   "<ORDER_TYPE>".$this->type."</ORDER_TYPE>";
				$xml .=   "<ORDER_DATE>".date('Y/m/d')."</ORDER_DATE>";
				$xml .=   "<SUPPLIER_CODE>{$this->sup_code}</SUPPLIER_CODE>";
				$xml .=   "<SUPPLIER_NAME><![CDATA[{$this->sup_name}]]></SUPPLIER_NAME>";
				$xml .=   "<SUPPLIER_ADDRESS1></SUPPLIER_ADDRESS1>";
				$xml .=   "<SUPPLIER_ADDRESS2></SUPPLIER_ADDRESS2>";
				$xml .=   "<REF_NO1></REF_NO1>";
				$xml .=   "<REF_NO2></REF_NO2>";
				$xml .=   "<REMARK><![CDATA[".$doc->remark."]]></REMARK>";
				$xml .= "</ORDER>";
					//--- Item start
				$xml .= "<ITEMS>";

				foreach($details as $rs)
				{
					if($rs->qty > 0)
					{
						$xml .= "<ITEM>";
						$xml .= "<ITEM_NO>".$rs->product_code."</ITEM_NO>";
						$xml .= "<ITEM_DESC><![CDATA[".$rs->product_name."]]></ITEM_DESC>";
						$xml .= "<VARIANT></VARIANT>";
						$xml .= "<LOT_NO></LOT_NO>";
						$xml .= "<EXP_DATE></EXP_DATE>";
						$xml .= "<SERIAL_NO></SERIAL_NO>";
						$xml .= "<QUANTITY>".round($rs->qty,2)."</QUANTITY>";
						$xml .= "<UOM>".$rs->unit_code."</UOM>";
						$xml .= "</ITEM>";
					}
				}

				$xml .= "</ITEMS>";
				//--- End header section
				$xml .= "</WIB>";

				if($this->log_xml)
				{
					$arr = array(
						'order_code' => $doc->code,
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
					}
		    }
			}
			else
			{
				$sc = FALSE;
				$this->error = "No data";
			}
		}
		else
		{
			$sc = FALSE;
			$this->error = "Invalid document data";
		}

		if($sc === TRUE)
		{
			$this->ci->wms_error_logs_model->add($doc->code, 'S', NULL, $this->type);
		}
		else
		{
			$this->ci->wms_error_logs_model->add($doc->code, 'E', $this->error, $this->type);
		}

		return $sc;
	}



	//--- export consign check to  wms
	public function export_consign_check($doc, $details)
	{

		$sc = TRUE;
		$this->type = "WX";
		$xml = "";

		if(!empty($doc))
		{

			if(!empty($details))
			{
				$xml .= "<WIB>";

				//--- Header_list section
				$xml .= "<HEADER>";
				$xml .=   "<WH_NO>".$this->WH_NO."</WH_NO>";
				$xml .=   "<CUST_CODE>".$this->CUS_CODE."</CUST_CODE>";
				$xml .= "</HEADER>";
				//---- End header_list section

				//--- Order Start
				$xml .= "<ORDER>";
				$xml .=   "<ORDER_NO>".$doc->code."</ORDER_NO>";
				$xml .=   "<ORDER_TYPE>".$this->type."</ORDER_TYPE>";
				$xml .=   "<ORDER_DATE>".date('Y/m/d')."</ORDER_DATE>";
				$xml .=   "<SUPPLIER_CODE>{$this->sup_code}</SUPPLIER_CODE>";
				$xml .=   "<SUPPLIER_NAME><![CDATA[{$this->sup_name}]]></SUPPLIER_NAME>";
				$xml .=   "<SUPPLIER_ADDRESS1></SUPPLIER_ADDRESS1>";
				$xml .=   "<SUPPLIER_ADDRESS2></SUPPLIER_ADDRESS2>";
				$xml .=   "<REF_NO1></REF_NO1>";
				$xml .=   "<REF_NO2></REF_NO2>";
				$xml .=   "<REMARK><![CDATA[".$doc->remark."]]></REMARK>";
				$xml .= "</ORDER>";
					//--- Item start
				$xml .= "<ITEMS>";

				foreach($details as $rs)
				{
					if($rs->stock_qty > 0)
					{
						$xml .= "<ITEM>";
						$xml .= "<ITEM_NO>".$rs->product_code."</ITEM_NO>";
						$xml .= "<ITEM_DESC><![CDATA[".$rs->product_name."]]></ITEM_DESC>";
						$xml .= "<VARIANT></VARIANT>";
						$xml .= "<LOT_NO></LOT_NO>";
						$xml .= "<EXP_DATE></EXP_DATE>";
						$xml .= "<SERIAL_NO></SERIAL_NO>";
						$xml .= "<QUANTITY>".round($rs->stock_qty,2)."</QUANTITY>";
						$xml .= "<UOM>".$rs->unit_code."</UOM>";
						$xml .= "</ITEM>";
					}
				}

				$xml .= "</ITEMS>";
				//--- End header section
				$xml .= "</WIB>";

				if($this->log_xml)
				{
					$arr = array(
						'order_code' => $doc->code,
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
					}
		    }
			}
			else
			{
				$sc = FALSE;
				$this->error = "No data";
			}
		}
		else
		{
			$sc = FALSE;
			$this->error = "Invalid document data";
		}

		if($sc === TRUE)
		{
			$this->ci->wms_error_logs_model->add($doc->code, 'S', NULL, $this->type);
		}
		else
		{
			$this->ci->wms_error_logs_model->add($doc->code, 'E', $this->error, $this->type);
		}

		return $sc;
	}



	//--- export return order
	public function export_return_request($doc, $details)
	{
		// Assign the CodeIgniter super-object
		$sc = TRUE;
		$this->type = "RC";
		$xml = "";

		if(!empty($doc))
		{

			if(!empty($details))
			{
				$xml .= "<WIB>";

				//--- Header_list section
				$xml .= "<HEADER>";
				$xml .=   "<WH_NO>".$this->WH_NO."</WH_NO>";
				$xml .=   "<CUST_CODE>".$this->CUS_CODE."</CUST_CODE>";
				$xml .= "</HEADER>";
				//---- End header_list section

				//--- Order Start
				$xml .= "<ORDER>";
				$xml .=   "<ORDER_NO>RC".$doc->code."</ORDER_NO>";
				$xml .=   "<ORDER_TYPE>".$this->type."</ORDER_TYPE>";
				$xml .=   "<ORDER_DATE>".date('Y/m/d')."</ORDER_DATE>";
				$xml .=   "<SUPPLIER_CODE><![CDATA[{$this->sup_code}]]></SUPPLIER_CODE>";
				$xml .=   "<SUPPLIER_NAME><![CDATA[{$this->sup_name}]]></SUPPLIER_NAME>";
				$xml .=   "<SUPPLIER_ADDRESS1></SUPPLIER_ADDRESS1>";
				$xml .=   "<SUPPLIER_ADDRESS2></SUPPLIER_ADDRESS2>";
				$xml .=   "<REF_NO1>".$doc->code."</REF_NO1>";
				$xml .=   "<REF_NO2></REF_NO2>";
				$xml .=   "<REMARK></REMARK>";
				$xml .= "</ORDER>";
					//--- Item start
				$xml .= "<ITEMS>";

				foreach($details as $rs)
				{
					if($rs->qty > 0 && $rs->is_count == 1)
					{
						$xml .= "<ITEM>";
						$xml .= "<ITEM_NO>".$rs->product_code."</ITEM_NO>";
						$xml .= "<ITEM_DESC><![CDATA[".$rs->product_name."]]></ITEM_DESC>";
						$xml .= "<VARIANT></VARIANT>";
						$xml .= "<LOT_NO></LOT_NO>";
						$xml .= "<EXP_DATE></EXP_DATE>";
						$xml .= "<SERIAL_NO></SERIAL_NO>";
						$xml .= "<QUANTITY>".round($rs->qty,2)."</QUANTITY>";
						$xml .= "<UOM>".$rs->unit_code."</UOM>";
						$xml .= "</ITEM>";
					}
				}

				$xml .= "</ITEMS>";
				//--- End header section
				$xml .= "</WIB>";


				if($this->log_xml)
				{
					$arr = array(
						'order_code' => $doc->code,
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
					}
		    }
			}
			else
			{
				$sc = FALSE;
				$this->error = "No data";
			}
		}
		else
		{
			$sc = FALSE;
			$this->error = "Invalid document data";
		}

		if($sc === TRUE)
		{
			$this->ci->wms_error_logs_model->add($doc->code, 'S', NULL, $this->type);
		}
		else
		{
			$this->ci->wms_error_logs_model->add($doc->code, 'E', $this->error, $this->type);
		}

		return $sc;
	}

}
?>
