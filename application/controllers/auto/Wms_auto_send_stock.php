<?php
class Wms_auto_send_stock extends CI_Controller
{
  public $home;
	public $wms;
  public $ms;
	public $user;
	public $error;
	public $warehouse_code = 'AFG-0010';

  public function __construct()
  {
    parent::__construct();
		$this->wms = $this->load->database('wms', TRUE);
    $this->ms = $this->load->database('ms', TRUE); //--- SAP database
    $this->home = base_url().'auto/wms_auto_send_stock';
		$this->load->model('masters/products_model');
		$this->load->model('stock/stock_model');
		$this->load->model('rest/V1/wms_receive_import_logs_model');
		$this->warehouse_code = getConfig('WMS_WAREHOUSE');
		$this->user = 'api@wms';
  }

  public function index()
  {
		$this->load->library('wms_stock_api');
		$this->wms_stock_api->send_stock($this->warehouse_code);
  }


	public function send_stock()
	{
		$sc = TRUE;
		$this->load->library('wms_stock_api');
		$rs = $this->wms_stock_api->send_stock($this->warehouse_code);

		if(! $rs)
		{
			$sc = FALSE;
			$this->error = $this->wms_stock_api->error;
		}

		echo $sc === TRUE ? 'success' : $this->error;
	}


	public function get_stock_file()
	{
		ini_set('memory_limit','512M'); // This also needs to be increased in some cases. Can be changed to a higher value as per need)
    ini_set('sqlsrv.ClientBufferMaxKBSize','524288'); // Setting to 512M
    ini_set('pdo_sqlsrv.client_buffer_max_kb_size','524288'); // Setting to 512M - for pdo_sqlsrv
    ini_set('max_execution_time', 600);

		$data = $this->stock_model->get_items_stock($this->warehouse_code);
		if(!empty($data))
		{
			$xml  = '<?xml version="1.0"?>';
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

			header('Content-type: text/xml');
			header('Content-disposition: attachment; filename="stock.xml"');
			echo $xml;
		}
		else
		{
			echo "No data";
		}
	}

} //--- end class
 ?>
