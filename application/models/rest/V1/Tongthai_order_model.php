<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Tongthai_order_model extends CI_Model
{
  public function __construct()
  {
    parent::__construct();
  }


	public function count_rows(array $ds = array())
	{
		if(!empty($ds['reference']))
		{
			$this->tt->like('reference', $ds['reference']);
		}

		if(!empty($ds['company']))
		{
			$this->tt
			->group_start()
			->like('bCompany', $ds['company'])
			->or_like('sCompany', $ds['company'])
			->group_end();
		}

		if(!empty($ds['name']))
		{
			$this->tt
			->group_start()
			->like('bFirstName', $ds['name'])
			->or_like('bLastName', $ds['name'])
			->or_like('sFirstName', $ds['name'])
			->or_like('sLastName', $ds['name'])
			->group_end();
		}

		if(!empty($ds['phone']))
		{
			$this->tt
			->group_start()
			->like('bPhone', $ds['phone'])
			->or_like('sPhone', $ds['phone'])
			->group_end();
		}

		if(!empty($ds['from_date']) && !empty($ds['to_date']))
		{
			$this->tt
			->group_start()
			->where('tempDate >=', from_date($ds['from_date']))
			->where('tempDate <=', to_date($ds['to_date']))
			->group_end();
		}

		return $this->tt->count_all_results('orders');
	}



	public function get_list(array $ds = array(), $perpage = 20, $offset = 0)
	{
		if(!empty($ds['reference']))
		{
			$this->tt->like('reference', $ds['reference']);
		}

		if(!empty($ds['company']))
		{
			$this->tt
			->group_start()
			->like('bCompany', $ds['company'])
			->or_like('sCompany', $ds['company'])
			->group_end();
		}

		if(!empty($ds['name']))
		{
			$this->tt
			->group_start()
			->like('bFirstName', $ds['name'])
			->or_like('bLastName', $ds['name'])
			->or_like('sFirstName', $ds['name'])
			->or_like('sLastName', $ds['name'])
			->group_end();
		}

		if(!empty($ds['phone']))
		{
			$this->tt
			->group_start()
			->like('bPhone', $ds['phone'])
			->or_like('sPhone', $ds['phone'])
			->group_end();
		}

		if(!empty($ds['from_date']) && !empty($ds['to_date']))
		{
			$this->tt
			->group_start()
			->where('tempDate >=', from_date($ds['from_date']))
			->where('tempDate <=', to_date($ds['to_date']))
			->group_end();
		}

		$rs = $this->tt->order_by('tempDate', 'DESC')->limit($perpage, $offset)->get('orders');

		if($rs->num_rows() > 0)
		{
			return $rs->result();
		}


		return NULL;
	}


	public function get($id)
	{
		$rs = $this->tt->where('id', $id)->get('orders');

		if($rs->num_rows() === 1 )
		{
			return $rs->row();
		}

		return NULL;
	}


	public function get_details($id)
	{
		$rs = $this->tt->where('orderId', $id)->get('order_detail');
		if($rs->num_rows() > 0)
		{
			return $rs->result();
		}

		return NULL;
	}


	public function get_order_qty($id)
	{
		$rs = $this->tt->select_sum('quantity', 'qty')->where('orderId', $id)->get('order_detail');

		return $rs->row()->qty;
	}


	public function get_order_amount($id)
	{
		$rs = $this->tt->select_sum('total')->where('orderId', $id)->get('order_detail');

		return $rs->row()->total;
	}

} //--- end class
 ?>
