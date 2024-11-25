<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Wms_error_logs_model extends CI_Model
{

	private $table = 'wms_error_logs';

  public function __construct()
  {
    parent::__construct();
  }


	public function add($code, $status, $message, $trans_no = NULL)
	{
		$arr = array(
			'trans_no' => $trans_no,
			'order_code' => $code,
			'status' => $status,
			'error_message' => $message
		);

		return $this->wms->insert($this->table, $arr);
	}


	public function get($code)
	{
		$rs = $this->wms->where('order_code', $code)->get($this->table);

		if($rs->num_rows() > 0)
		{
			return $rs->result();
		}

		return NULL;
	}


	public function get_list(array $ds = array(), $perpage = 20, $offset = 0)
	{
		if(!empty($ds['code']))
		{
			$this->wms->like('order_code', $ds['code']);
		}

		if(!empty($ds['status']) && $ds['status'] !== 'all')
		{
			$this->wms->where('status', $ds['status']);
		}

		if(!empty($ds['trans_no']) )
		{
			$this->wms->like('trans_no', $ds['trans_no']);
		}

		if(isset($ds['message']) && $ds['message'] != '')
		{
			$this->wms->like('error_message', $ds['message']);
		}

		if(!empty($ds['from_date']) && !empty($ds['to_date']))
		{
			$this->wms->where('date_upd >=', from_date($ds['from_date']))->where('date_upd <=', to_date($ds['to_date']));
		}

		$this->wms->order_by('id', 'DESC');
		$this->wms->limit($perpage, $offset);
		$rs = $this->wms->get($this->table);

		if($rs->num_rows() > 0)
		{
			return $rs->result();
		}

		return NULL;
	}



	public function count_rows(array $ds = array())
	{
		if(!empty($ds['code']))
		{
			$this->wms->like('order_code', $ds['code']);
		}

		if(!empty($ds['status']) && $ds['status'] !== 'all')
		{
			$this->wms->where('status', $ds['status']);
		}

		if(!empty($ds['trans_no']) )
		{
			$this->wms->like('trans_no', $ds['trans_no']);
		}

		if(isset($ds['message']) && $ds['message'] != '')
		{
			$this->wms->like('error_message', $ds['message']);
		}

		if(!empty($ds['from_date']) && !empty($ds['to_date']))
		{
			$this->wms->where('date_upd >=', from_date($ds['from_date']))->where('date_upd <=', to_date($ds['to_date']));
		}

		return $this->wms->count_all_results($this->table);
	}



	public function log_xml($ds = array())
	{
		return $this->wms->insert('test_xml', $ds);
	}

} //---
