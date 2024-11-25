<?php
class Chatbot_api
{
	private $host;
	private $username;
	private $secret;
	private $credential;
	private $signature;
	private $timestamp;
	private $log_json = FALSE;
	protected $warehouse_code;
	protected $ci;


	public function __construct()
  {
		$this->ci =& get_instance();
		$this->ci->load->model('rest/V1/order_api_logs_model');
		$this->ci->load->model('stock/stock_model');
		$this->ci->load->model('orders/orders_model');

		$this->host = getConfig('CHATBOT_API_HOST');
		$this->username = getConfig('CHATBOT_API_USER_NAME');
		$this->secret = getConfig('CHATBOT_API_SECRET');
		$this->credential = getConfig('CHATBOT_API_CREDENTIAL');
		$this->warehouse_code = getConfig('CHATBOT_WAREHOUSE_CODE');
		$this->timestamp =  time() * 1000;
		$this->log_json = getConfig('CHATBOT_LOG_JSON') == 1 ? TRUE : FALSE;
  }

  private function get_signature()
  {
		$json = new stdClass();
		$json->credential = $this->credential;
		$json->timestamp  = $this->timestamp;
		return hash_hmac('sha256', json_encode($json), $this->secret, FALSE);
  }


  public function update_chatbot_stock(array $ds = array())
  {
		if(!empty($ds))
		{
			$this->signature = $this->get_signature();
	    $url = $this->host."/stock";
			$method = "POST";

			$headers = array(
				"x-ysentric-username: {$this->username}",
				"x-ysentric-signature: {$this->signature}",
				"x-ysentric-timestamp: {$this->timestamp}",
				"Content-Type: application/json"
			);


			$curl = curl_init();
			curl_setopt($curl, CURLOPT_URL, $url);
	    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
	    curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($ds));
	    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
	    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

			$response = curl_exec($curl);

			curl_close($curl);

			$res = json_decode($response);

      if(!empty($res))
			{
				if(!$res->complete)
				{
					$arr = array(
						'x-signature' => $this->signature,
						'status' => 'E',
						'message' => $res->message,
						'json_text' => ($this->log_json ? json_encode($ds) : NULL)
					);
				}
				else
				{
					$arr = array(
						'x-signature' => $this->signature,
						'status' => 'S',
						'json_text' => ($this->log_json ? json_encode($ds) : NULL)
					);
				}

				$this->ci->order_api_logs_model->logs_stock($arr);
			}
			else
			{
				$arr = array(
					'x-signature' => $this->signature,
					'status' => 'E',
					'message' => "no response",
					'json_text' => ($this->log_json ? json_encode($ds) : NULL)
				);

				$this->ci->order_api_logs_model->logs_stock($arr);
			}
		}
		else
		{
			echo "nodata";
		}
  }



	public function sync_stock(array $ds = array())
  {
		if(!empty($ds))
		{
			$limit = 100;
			$count = count($ds);
			$sync_stock = array();
			if($count > $limit)
			{
				$i = 0;

				foreach($ds as $item_code)
				{
					$inventory = $this->get_sell_stock($item_code, $this->warehouse_code);
					array_push($sync_stock, array('productCode' => $item_code, 'inventory' => $inventory));
					$i++;

					if($i == $limit)
					{
						$this->update_chatbot_stock($sync_stock);
						$i = 0;
						$sync_stock = array();
					}
				}
			}
			else
			{
				foreach($ds as $item_code)
				{
					$inventory = $this->get_sell_stock($item_code, $this->warehouse_code);
					array_push($sync_stock, array('productCode' => $item_code, 'inventory' => $inventory));
				}
			}

			if(!empty($sync_stock))
			{
				$this->update_chatbot_stock($sync_stock);
			}
		}

  }


	public function get_sell_stock($item_code, $warehouse = NULL, $zone = NULL)
  {
    $sell_stock = $this->ci->stock_model->get_sell_stock($item_code, $warehouse, $zone);
    $reserv_stock = $this->ci->orders_model->get_reserv_stock($item_code, $warehouse, $zone);
    $availableStock = $sell_stock - $reserv_stock;
		return $availableStock < 0 ? 0 : $availableStock;
  }

	public function approve_payment(array $ds = array())
	{
		if(!empty($ds))
		{
			$this->host = getConfig('CHATBOT_API_HOST');
			$this->username = getConfig('CHATBOT_API_USER_NAME');
			$this->secret = getConfig('CHATBOT_API_SECRET');
			$this->credential = getConfig('CHATBOT_API_CREDENTIAL');
			$this->signature = $this->get_signature();
	    $url = $this->host."/order";
			$method = "PATCH";
			$timestamp = time()*1000;
			$headers = array(
				"x-ysentric-username: {$this->username}",
				"x-ysentric-signature: {$this->signature}",
				"x-ysentric-timestamp: {$timestamp}",
				"Content-Type: application/json"
			);

			$curl = curl_init();
			curl_setopt($curl, CURLOPT_URL, $url);
	    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
	    curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($ds));
	    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
	    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

			$response = curl_exec($curl);

			curl_close($curl);

			$res = json_decode($response);

			if(!empty($res))
			{
				if(!$res->complete)
				{
					$arr = array(
						'code' => $ds['order_number'],
						'x-signature' => $this->signature,
						'status' => 'E',
						'message' => $res->message,
						'json_text' => ($this->log_json ? json_encode($ds) : NULL)
					);
				}
				else
				{
					$arr = array(
						'code' => $ds['order_number'],
						'x-signature' => $this->signature,
						'status' => 'S',
						'json_text' => ($this->log_json ? json_encode($ds) : NULL)
					);
				}

				$this->ci->order_api_logs_model->logs_approve($arr);
			}
			else
			{
				$arr = array(
					'code' => $ds['order_number'],
					'x-signature' => $this->signature,
					'status' => 'E',
					'message' => "no response",
					'json_text' => ($this->log_json ? json_encode($ds) : NULL)
				);

				$this->ci->order_api_logs_model->logs_approve($arr);
			}
		}
	}


} //-- end class

 ?>
