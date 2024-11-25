<?php
class Document_audit_model extends CI_Model
{
  public function __construct()
  {
    parent::__construct();
  }


	public function get_ix_receive_data($table, array $ds = array())
	{
		if(!empty($ds))
		{
			$this->db
			->select('a.date_add, a.code AS order_code, a.status')
			->select('tmp.reference AS temp_code, tmp.type AS temp_type')
			->from("warrix_sap.{$table} AS a")
			->join('warrix_wms_temp.wms_temp_receive AS tmp', 'a.code = tmp.code', 'left')
			->where('a.is_wms', 1)
			->where_in('a.status', $ds['state'])
			->where('a.date_add >=', $ds['fromDate'])
			->where('a.date_add <=', $ds['toDate']);

			if($ds['allDoc'] != 1 && !empty($ds['docFrom']) && !empty($ds['docTo']))
			{
				$this->db->where('a.code >=', $ds['docFrom'])->where('a.code <=', $ds['docTo']);
			}


			$this->db->order_by('a.date_add', 'ASC');
			$this->db->order_by('a.code', 'ASC');

			$rs = $this->db->get();

			if($rs->num_rows() > 0)
			{
				return $rs->result();
			}
		}

		return NULL;
	}


	public function get_ix_return_data_qty($tb, $td, $df, array $ds = array())
	{
		if(!empty($ds))
		{
			$qr  = "SELECT o.date_add, o.code AS order_code, o.status, ";
			$qr .= "tmp.reference AS temp_code, ";
			$qr .= "(SELECT SUM(od.qty) FROM warrix_sap.{$td} AS od WHERE od.{$df} = o.code) AS order_qty, ";
			$qr .= "(SELECT SUM(tmd.qty) FROM warrix_wms_temp.wms_temp_receive_detail AS tmd WHERE tmd.id_receive = tmp.id) AS temp_qty ";
			$qr .= "FROM warrix_sap.{$tb} AS o ";
			$qr .= "LEFT JOIN warrix_wms_temp.wms_temp_receive AS tmp ON o.code = tmp.code ";
			$qr .= "WHERE o.is_wms = 1 ";
			if(!empty($ds['state']))
			{
				$qr .= "AND (";

					$i = 1;
				foreach($ds['state'] as $status)
				{

					if($status == 1)
					{
						$qr .= $i == 1 ? " o.is_complete = 1" : " OR o.is_complete = 1";
					}
					else
					{
						$qr .= $i == 1 ? "o.status = {$status}" : " OR o.status = {$status}";
					}

					$i++;
				}

				$qr .= ") ";
			}

			//$qr .= "AND o.status IN(".$this->parse_in($ds['state']).") ";
			$qr .= "AND o.date_add >= '".$ds['fromDate']."' ";
			$qr .= "AND o.date_add <= '".$ds['toDate']."' ";

			if($ds['allDoc'] != 1 && !empty($ds['docFrom']) && !empty($ds['docTo']))
			{
				$qr .= "AND o.code >= '".$ds['docFrom']."' AND o.code <= '".$ds['docTo']."' ";
			}

			$qr .= "ORDER BY o.date_add ASC, o.code ASC";

			$rs = $this->db->query($qr);

			if($rs->num_rows() > 0)
			{
				return $rs->result();
			}
		}

		return NULL;
	}


	public function get_ix_return_cancle_data_qty($ds = array())
	{
		if(!empty($ds))
		{
			$qr  = "SELECT o.date_add, o.cancle_date, o.code AS order_code, o.role, o.channels_code, ";
			$qr .= "tmp.code AS temp_code, ";
			$qr .= "(SELECT SUM(od.qty) FROM warrix_sap.order_details AS od WHERE od.order_code = o.code AND od.is_count = 1) AS order_qty, ";
			$qr .= "(SELECT SUM(tmd.qty) FROM warrix_wms_temp.wms_temp_receive_detail AS tmd WHERE tmd.id_receive = tmp.id) AS temp_qty ";
			$qr .= "FROM warrix_sap.orders AS o ";
			$qr .= "LEFT JOIN warrix_wms_temp.wms_temp_receive AS tmp ON o.code = tmp.order_code ";
			$qr .= "WHERE o.is_wms = 1 ";
			$qr .= "AND o.role IN(".$this->parse_in($ds['role']).") ";
			$qr .= "AND o.state = 9 ";
			$qr .= "AND o.is_cancled = 1 ";

			if(!empty($ds['fromDate']) && !empty($ds['toDate']))
			{
				$qr .= "AND o.date_add >= '".$ds['fromDate']."' ";
				$qr .= "AND o.date_add <= '".$ds['toDate']."' ";
			}

			if(!empty($ds['cancleFromDate']) && !empty($ds['cancleToDate']))
			{
				$qr .= "AND o.cancle_date >= '".$ds['cancleFromDate']."' ";
				$qr .= "AND o.cancle_date <= '".$ds['cancleToDate']."' ";
			}


			if($ds['allDoc'] != 1 && !empty($ds['docFrom']) && !empty($ds['docTo']))
			{
				$qr .= "AND o.code >= '".$ds['docFrom']."' AND o.code <= '".$ds['docTo']."' ";
			}


			if($ds['channels'] != "all")
			{
				$qr .= "AND o.channels_code = '".$ds['channels']."' ";
			}

			$qr .= "ORDER BY o.date_add ASC, o.code ASC";

			$rs = $this->db->query($qr);

			if($rs->num_rows() > 0)
			{
				return $rs->result();
			}
		}

		return NULL;
	}


	public function get_ix_receive_data_qty($tb, $td, $df, array $ds = array())
	{
		if(!empty($ds))
		{
			$qty = $tb === 'consign_check' ? 'stock_qty' : 'qty';

			$qr  = "SELECT o.date_add, o.code AS order_code, o.status, ";
			$qr .= "tmp.reference AS temp_code, ";
			$qr .= "(SELECT SUM(od.{$qty}) FROM warrix_sap.{$td} AS od WHERE od.{$df} = o.code) AS order_qty, ";
			$qr .= "(SELECT SUM(tmd.qty) FROM warrix_wms_temp.wms_temp_receive_detail AS tmd WHERE tmd.id_receive = tmp.id) AS temp_qty ";
			$qr .= "FROM warrix_sap.{$tb} AS o ";
			$qr .= "LEFT JOIN warrix_wms_temp.wms_temp_receive AS tmp ON o.code = tmp.code ";
			$qr .= "WHERE o.is_wms = 1 ";
			$qr .= "AND o.status IN(".$this->parse_in($ds['state']).") ";
			$qr .= "AND o.date_add >= '".$ds['fromDate']."' ";
			$qr .= "AND o.date_add <= '".$ds['toDate']."' ";

			if($ds['allDoc'] != 1 && !empty($ds['docFrom']) && !empty($ds['docTo']))
			{
				$qr .= "AND o.code >= '".$ds['docFrom']."' AND o.code <= '".$ds['docTo']."' ";
			}

			$qr .= "ORDER BY o.date_add ASC, o.code ASC";

			$rs = $this->db->query($qr);

			if($rs->num_rows() > 0)
			{
				return $rs->result();
			}
		}

		return NULL;
	}




  public function get_outbound_data(array $ds = array())
	{
		if(!empty($ds))
		{
			$this->db
			->select('o.date_add, o.code AS order_code, o.role, o.state, o.channels_code, o.inv_code, o.wms_export')
			->select('tmp.reference AS temp_code')
			->from('warrix_sap.orders AS o')
			->join('warrix_wms_temp.wms_temp_order AS tmp', 'o.code = tmp.code', 'left')
			->where('o.is_wms', 1)
			->where_in('o.role', $ds['role'])
			->where_in('o.state', $ds['state'])
			->where('o.date_add >=', $ds['fromDate'])
			->where('o.date_add <=', $ds['toDate']);

			if($ds['allDoc'] != 1 && !empty($ds['docFrom']) && !empty($ds['docTo']))
			{
				$this->db->where('o.code >=', $ds['docFrom'])->where('o.code <=', $ds['docTo']);
			}

			if($ds['channels'] != "all")
			{
				$this->db->where('o.channels_code', $ds['channels']);
			}

			$this->db->order_by('o.date_add', 'ASC');
			$this->db->order_by('o.code', 'ASC');

			$rs = $this->db->get();

			if($rs->num_rows() > 0)
			{
				return $rs->result();
			}
		}

		return NULL;
	}



	public function get_outbound_data_qty(array $ds = array())
	{
		if(!empty($ds))
		{
			$qr  = "SELECT o.date_add, o.code AS order_code, o.role, o.state, o.channels_code, ";
			$qr .= "tmp.reference AS temp_code, ";
			$qr .= "(SELECT SUM(od.qty) FROM warrix_sap.order_details AS od WHERE od.order_code = o.code AND od.is_count = 1) AS order_qty, ";
			$qr .= "(SELECT SUM(tmd.qty) FROM warrix_wms_temp.wms_temp_order_detail AS tmd WHERE tmd.id_order = tmp.id) AS temp_qty ";
			$qr .= "FROM warrix_sap.orders AS o ";
			$qr .= "LEFT JOIN warrix_wms_temp.wms_temp_order AS tmp ON o.code = tmp.code ";
			$qr .= "WHERE o.is_wms = 1 ";
			$qr .= "AND o.role IN(".$this->parse_in($ds['role']).") ";
			$qr .= "AND o.state IN(".$this->parse_in($ds['state']).") ";
			$qr .= "AND o.date_add >= '".$ds['fromDate']."' ";
			$qr .= "AND o.date_add <= '".$ds['toDate']."' ";

			if($ds['allDoc'] != 1 && !empty($ds['docFrom']) && !empty($ds['docTo']))
			{
				$qr .= "AND o.code >= '".$ds['docFrom']."' AND o.code <= '".$ds['docTo']."' ";
			}

			if($ds['channels'] != "all")
			{
				$qr .= "AND o.channels_code = '".$ds['channels']."' ";
			}

			$qr .= "ORDER BY o.date_add ASC, o.code ASC";

			$rs = $this->db->query($qr);

			if($rs->num_rows() > 0)
			{
				return $rs->result();
			}
		}

		return NULL;
	}



	public function get_outbound_qty($order_code)
	{
		$rs = $this->select_sum('qty')
		->where('order_code', $order_code)
		->where('status', 1)
		->group_by('order_code')
		->get('wms_temp_order_detail');

		if($rs->num_rows() == 1)
		{
			return $rs->row()->qty;
		}

		return 0;
	}



	public function get_doc_num($table, $code)
	{
		$rs = $this->ms
		->select('DocNum')
		->where('U_ECOMNO', $code)
		->where('CANCELED', 'N')
		->get($table);

		if($rs->num_rows() === 1)
		{
			return $rs->row()->DocNum;
		}

		return NULL;
	}



	public function get_do_code($code)
	{
		$rs = $this->ms
		->select('DocNum')
		->where('U_ECOMNO', $code)
		->where('CANCELED', 'N')
		->get('ODLN');

		if($rs->num_rows() === 1)
		{
			return $rs->row()->DocNum;
		}

		return NULL;
	}


	public function get_tr_code($code)
  {
    $rs = $this->ms
    ->select('DocNum')
    ->where('U_ECOMNO', $code)
    ->where('CANCELED', 'N')
    ->get('OWTR');

    if($rs->num_rows() > 0)
    {
      return $rs->row()->DocNum;
    }

    return NULL;
  }


	public function get_rt_code($code)
	{
		$rs = $this->ms
		->select('DocNum')
		->where('U_ECOMNO', $code)
		->where('CANCELED', 'N')
		->get('OIGN');

		if($rs->num_rows() > 0)
		{
			return $rs->row()->DocNum;
		}

		return NULL;
	}


	public function get_doc_num_and_qty($tb, $td, $code)
	{
		$qr  = "SELECT {$tb}.DocNum, ";
		$qr .= "(SELECT SUM({$td}.Quantity) FROM {$td} LEFT JOIN OITM ON {$td}.ItemCode = OITM.ItemCode ";
    $qr .= "WHERE {$td}.DocEntry = {$tb}.DocEntry AND OITM.InvntItem = 'Y') AS qty ";
		$qr .= "FROM {$tb} ";
		$qr .= "WHERE {$tb}.U_ECOMNO = '{$code}' AND {$tb}.CANCELED = 'N'";

		$rs = $this->ms->query($qr);
		if($rs->num_rows() === 1)
		{
			return $rs->row();
		}

		return NULL;
	}


	public function get_ww_from_wx($wx_code)
	{
		$rs = $this->db->select('code')->where('wx_code', $wx_code)->get('transfer');
		if($rs->num_rows() > 0)
		{
			return $rs->result();
		}

		return NULL;
	}



	public function get_do_code_and_qty($code)
	{
		$qr  = "SELECT ODLN.DocNum, ";
		$qr .= "(SELECT SUM(DLN1.Quantity) FROM DLN1 LEFT JOIN OITM ON DLN1.ItemCode = OITM.ItemCode ";
    $qr .= "WHERE DLN1.DocEntry = ODLN.DocEntry AND OITM.InvntItem = 'Y') AS qty ";
		$qr .= "FROM ODLN ";
		$qr .= "WHERE ODLN.U_ECOMNO = '{$code}' AND ODLN.CANCELED = 'N'";

		$rs = $this->ms->query($qr);
		if($rs->num_rows() === 1)
		{
			return $rs->row();
		}

		return NULL;
	}


	public function get_tr_code_and_qty($code)
	{
		$qr  = "SELECT OWTR.DocNum, ";
		$qr .= "(SELECT SUM(WTR1.Quantity) FROM WTR1 LEFT JOIN OITM ON WTR1.ItemCode = OITM.ItemCode ";
    $qr .= "WHERE WTR1.DocEntry = OWTR.DocEntry AND OITM.InvntItem = 'Y') AS qty ";
		$qr .= "FROM OWTR ";
		$qr .= "WHERE OWTR.U_ECOMNO = '{$code}' AND OWTR.CANCELED = 'N'";

		$rs = $this->ms->query($qr);
		if($rs->num_rows() === 1)
		{
			return $rs->row();
		}

		return NULL;
	}



	public function parse_in(array $ds = array())
	{
		$in = "";
		if(!empty($ds))
		{
			$i = 1;
			foreach($ds as $rs)
			{
				$in .= $i === 1 ? "'{$rs}'" : ", '{$rs}'";

				$i++;
			}
		}

		return $in;
	}

} //--- end class

?>
