<?php
class Api_key extends CI_Controller
{
	public $logs;

	public function __construct()
	{
		parent::__construct();
	}

	public function index()
	{
		$json = new stdClass();
		$secret = 'ZDE5OTY3NjYtNTE4NS00ODk5LWFmNGUtNWE2NjE0N2Y0MGVl';
		$json->credential = 'hzVK/1If6k/cJ9h1buEea2j0/c8fm1FJI9CubghJWTc=';
		$json->timestamp = time() * 1000;
		$signature = hash_hmac('sha256', json_encode($json), $secret, FALSE);

		echo $signature;
		echo "<br/>".time() * 1000;
	}



	public function test_api()
	{
		$arr = array(
			"orderNumber" => "NO123456",
			"amount" => 256.00,
			"action" => "approve"
		);

		echo json_encode($arr);
		// $this->logs = $this->load->database('logs', TRUE);
		// $this->load->library('chatbot_api');
		// $ds = array(
		// 	array("inventory" => 1, "productCode" => "WA-RNA616-YY-XS"),
		// 	array("inventory" => 1, "productCode" => "WA-RNA616-YY-S")
		// );
		//
		// $this->chatbot_api->sync_stock($ds);
	}


}

 ?>
