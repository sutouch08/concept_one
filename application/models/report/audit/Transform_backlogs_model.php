<?php
class Transform_backlogs_model extends CI_Model
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
			->select('od.order_code, od.original_code, od.product_code, od.sold_qty AS qty, od.receive_qty AS receive, (od.sold_qty - od.receive_qty) AS balance')
			->select('o.date_add, o.user_ref, u.name AS user_name')
			->select('pd.price, so.code AS so_code, so.bill_code')
			->from('order_transform_detail AS od')
      ->join('order_transform AS ot', 'od.order_code = ot.order_code', 'left')
      ->join('sale_order AS so', 'ot.so_code = so.code', 'left')
			->join('orders AS o', 'od.order_code = o.code', 'left')
			->join('products AS pd', 'od.product_code = pd.code', 'left')
			->join('user AS u', 'o.user = u.uname', 'left')
			->where('od.receive_qty < od.sold_qty', FALSE, FALSE);

			if(!empty($ds['from_date']) && !empty($ds['to_date']))
			{
				$this->db->where('o.date_add >=', $ds['from_date'])->where('o.date_add <=', $ds['to_date']);
			}


			if($ds['allUser'] != 1 && !empty($ds['dname'])) {
				$this->db->where('o.user_ref', $ds['dname']);
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
