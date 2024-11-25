<?php
class Sale_report_model extends CI_Model
{
	private $begin_date = '2020-12-15 00:00:00';
  public function __construct()
  {
      parent::__construct();
  }

	private function get_begin_date()
	{
		return date('Y-m-d 00:00:00', strtotime(getConfig('POWER_BI_BEGIN_DATE')));
	}


  public function get_orders($role = 'S', $limit = 100)
  {
    $begin_date = $this->get_begin_date();

    $rs = $this->db
    ->select('code, status, state')
    ->where('role', $role)
    ->where('date_add >=', $begin_date)
    ->where('is_report IS NULL', NULL, FALSE)
    ->where('state >=', 8)
    ->where('status !=', 0)
    ->order_by('date_add', 'ASC')
    ->limit($limit)
    ->get('orders');

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }



  public function get_error_orders($role = 'S', $limit = 100)
  {
    $begin_date = $this->get_begin_date();
    $rs = $this->db
    ->select('code, status, state')
    ->where('role', $role)
    ->where('date_add >=', $begin_date)
    ->where('is_report', 3)
    ->where('state >=', 8)
    ->where('status !=', 0)
    ->limit($limit)
    ->get('orders');

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function get_sold_data($code)
  {
    //$rs = $this->db->where('reference', $code)->get('order_sold');
    $rs = $this->db
    ->select('so.*')
    ->select('cn.name AS channels')
    ->select('pm.name AS payment')
    ->select('co.code AS color')
    ->select('size.name AS size')
    ->select('pg.name AS product_group')
    ->select('pc.name AS product_category')
    ->select('pk.name AS product_kind')
    ->select('pt.name AS product_type')
    ->select('pb.name AS brand')
    ->select('pd.year')
    ->select('c.name AS customer_name')
    ->select('cg.name AS customer_group')
    ->select('ck.name AS customer_kind')
    ->select('cc.name AS customer_class')
    ->select('ca.name AS customer_area')
    ->select('sale.name AS sale_name')
    ->select('user.name AS employee_name')
    ->from('order_sold AS so')
    ->join('channels AS cn', 'so.channels_code = cn.code', 'left')
    ->join('payment_method AS pm', 'so.payment_code = pm.code', 'left')
    ->join('products AS pd', 'so.product_code = pd.code', 'left')
    ->join('product_color AS co', 'pd.color_code = co.code', 'left')
    ->join('product_size AS size', 'pd.size_code = size.code', 'left')
    ->join('product_group AS pg', 'pd.group_code = pg.code', 'left')
    ->join('product_category AS pc', 'pd.category_code = pc.code', 'left')
    ->join('product_kind AS pk', 'pd.kind_code = pk.code', 'left')
    ->join('product_type AS pt', 'pd.type_code = pt.code', 'left')
    ->join('product_brand AS pb', 'pd.brand_code = pb.code', 'left')
    ->join('customers AS c', 'so.customer_code = c.code', 'left')
    ->join('customer_group AS cg', 'c.group_code = cg.code', 'left')
    ->join('customer_kind AS ck', 'c.kind_code = ck.code', 'left')
    ->join('customer_class AS cc', 'c.class_code = cc.code', 'left')
    ->join('customer_area AS ca', 'c.area_code = ca.code', 'left')
    ->join('saleman AS sale', 'c.sale_code = sale.id', 'left')
    ->join('user', 'so.user = user.uname', 'left')
    ->where('reference', $code)
    ->get();
    //echo $this->db->get_compiled_select();

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function set_report_status($code, $status, $error_message = NULL)
  {
    $arr = array(
      'is_report' => $status,
      'export_error' => $error_message
    );

    return $this->db->where('code', $code)->update('orders', $arr);
  }


} //--- end class
?>
