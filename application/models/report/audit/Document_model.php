<?php
class Document_model extends CI_Model
{
	public function __construct()
	{
		parent::__construct();
	}


	public function getOrder($role, $fromDate, $toDate)
	{
		$rs = $this->db
		->select('o.date_add, o.code, o.inv_code, ch.name AS channels_name, st.name AS state_name, cn.reason')
		->from('orders AS o')
		->join('channels AS ch', 'o.channels_code = ch.code', 'left')
		->join('order_state AS st', 'o.state = st.state', 'left')
		->join('order_cancle_reason AS cn', 'o.code = cn.code', 'left')
		->where('o.role', $role)
		->where('o.date_add >=', from_date($fromDate))
		->where('o.date_add <=', to_date($toDate))
		->get();

		if($rs->num_rows() > 0)
		{
			return $rs->result();
		}

		return NULL;
	}


	public function WM($fromDate, $toDate)
	{
		$rs = $this->db
		->select('date_add, code, inv_code, status, cancle_reason AS reason')
		->where('date_add >=', from_date($fromDate))
		->where('date_add <=', to_date($toDate))
		->get('consign_order');

		if($rs->num_rows() > 0)
		{
			return $rs->result();
		}

		return NULL;
	}


	public function WD($fromDate, $toDate)
	{
		$rs = $this->db
		->select('date_add, code, inv_code, status, cancle_reason AS reason')
		->where('date_add >=', from_date($fromDate))
		->where('date_add <=', to_date($toDate))
		->get('consignment_order');

		if($rs->num_rows() > 0)
		{
			return $rs->result();
		}

		return NULL;
	}


	public function WR($fromDate, $toDate)
	{
		$rs = $this->db
		->select('date_add, code, inv_code, status, is_expire, cancle_reason AS reason')
		->where('date_add >=', from_date($fromDate))
		->where('date_add <=', to_date($toDate))
		->get('receive_product');

		if($rs->num_rows() > 0)
		{
			return $rs->result();
		}

		return NULL;
	}


	public function WW($fromDate, $toDate)
	{
		$rs = $this->db
		->select('date_add, code, inv_code, status, is_expire, cancle_reason AS reason')
		->where('date_add >=', from_date($fromDate))
		->where('date_add <=', to_date($toDate))
		->get('transfer');

		if($rs->num_rows() > 0)
		{
			return $rs->result();
		}

		return NULL;
	}

	public function MV($fromDate, $toDate)
	{
		$rs = $this->db
		->select('date_add, code, inv_code, status')
		->where('date_add >=', from_date($fromDate))
		->where('date_add <=', to_date($toDate))
		->get('move');

		if($rs->num_rows() > 0)
		{
			return $rs->result();
		}

		return NULL;
	}


	public function WG($fromDate, $toDate)
	{
		$rs = $this->db
		->select('date_add, code, reference, issue_code AS inv_code, status, cancle_reason AS reason')
		->where('date_add >=', from_date($fromDate))
		->where('date_add <=', to_date($toDate))
		->get('adjust_transform');

		if($rs->num_rows() > 0)
		{
			return $rs->result();
		}

		return NULL;
	}


	public function RT($fromDate, $toDate)
	{
		$rs = $this->db
		->select('date_add, code, order_code AS reference, inv_code, status, is_expire, cancle_reason AS reason')
		->where('date_add >=', from_date($fromDate))
		->where('date_add <=', to_date($toDate))
		->get('receive_transform');

		if($rs->num_rows() > 0)
		{
			return $rs->result();
		}

		return NULL;
	}

	public function RN($fromDate, $toDate)
	{
		$rs = $this->db
		->select('date_add, code, lend_code AS reference, inv_code, status, is_expire, cancle_reason AS reason')
		->where('date_add >=', from_date($fromDate))
		->where('date_add <=', to_date($toDate))
		->get('return_lend');

		if($rs->num_rows() > 0)
		{
			return $rs->result();
		}

		return NULL;
	}

	public function SM($fromDate, $toDate)
	{
		$rs = $this->db
		->select('date_add, code, invoice AS reference, inv_code, status, is_expire, cancle_reason AS reason')
		->where('date_add >=', from_date($fromDate))
		->where('date_add <=', to_date($toDate))
		->get('return_order');

		if($rs->num_rows() > 0)
		{
			return $rs->result();
		}

		return NULL;
	}


	public function CN($fromDate, $toDate)
	{
		$rs = $this->db
		->select('date_add, code, invoice AS reference, inv_code, status, cancle_reason AS reason')
		->where('date_add >=', from_date($fromDate))
		->where('date_add <=', to_date($toDate))
		->get('return_consignment');

		if($rs->num_rows() > 0)
		{
			return $rs->result();
		}

		return NULL;
	}


	public function WA($fromDate, $toDate)
	{
		$rs = $this->db
		->select('date_add, code, issue_code, receive_code, status, cancle_reason AS reason')
		->where('date_add >=', from_date($fromDate))
		->where('date_add <=', to_date($toDate))
		->get('adjust');

		if($rs->num_rows() > 0)
		{
			return $rs->result();
		}

		return NULL;
	}


	public function AC($fromDate, $toDate)
	{
		$rs = $this->db
		->select('date_add, code, reference, issue_code, receive_code, status, cancle_reason AS reason')
		->where('date_add >=', from_date($fromDate))
		->where('date_add <=', to_date($toDate))
		->get('adjust_consignment');

		if($rs->num_rows() > 0)
		{
			return $rs->result();
		}

		return NULL;
	}

} //--- end class

 ?>
