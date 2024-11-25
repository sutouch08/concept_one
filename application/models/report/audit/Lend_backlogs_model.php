<?php
class Lend_backlogs_model extends CI_Model
{
  public function __construct()
  {
    parent::__construct();
  }


  public function get_data(array $ds = array())
	{
		if(!empty($ds))
		{
			$this->db
			->select('od.order_code, od.product_code, od.qty, od.receive, (od.qty - od.receive) AS balance')
			->select('ol.empName, ol.user_ref, u.name AS user_name')
			->select('pd.price')
			->from('order_lend_detail AS od')
			->join('orders AS ol', 'od.order_code = ol.code', 'left')
			->join('products AS pd', 'od.product_code = pd.code', 'left')
			->join('user AS u', 'ol.user = u.uname', 'left')
			->where('ol.date_add >=', $ds['from_date'])
			->where('ol.date_add <=', $ds['to_date'])
			->where('od.receive < od.qty',FALSE,FALSE);

			if($ds['allEmp'] != 1 && !empty($ds['empId'])) {
				$this->db->where('ol.empID', $ds['empId']);
			}

			if($ds['allPd'] != 1 && !empty($ds['pdFrom']) && !empty($ds['pdTo']))
			{
				$this->db->where('od.product_code >=', $ds['pdFrom'])->where('od.product_code <=', $ds['pdTo']);
			}

			$rs = $this->db->get();

			if($rs->num_rows() > 0)
			{
				return $rs->result();
			}
		}

		return NULL;
	}

} //--- end class

?>
