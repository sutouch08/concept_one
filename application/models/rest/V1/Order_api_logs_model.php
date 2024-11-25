<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Order_api_logs_model extends CI_Model
{

	private $order_table = 'orders_api_logs';
	private $stock_table = 'stock_api_logs';
	private $approve_table = 'approve_api_logs';

  public function __construct()
  {
    parent::__construct();
  }


	public function log_json($json)
	{
		$arr = array(
			'json_text' => $json
		);

		return $this->logs->insert('json_logs', $arr);
	}


	public function logs($code, $status, $error)
	{
		$arr = array(
			'code' => $code,
			'status' => $status,
			'error_message' => $error
		);

		return $this->logs->insert($this->order_table, $arr);
	}

	public function logs_order($ds = array())
	{
		return $this->logs->insert($this->order_table, $ds);
	}


	public function logs_stock($ds = array())
	{
		return $this->logs->insert($this->stock_table, $ds);
	}


	public function logs_approve($ds = array())
	{
		return $this->logs->insert($this->approve_table, $ds);
	}

} //---
