<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Order_state_model extends CI_Model
{

  public function __construct()
  {
    parent::__construct();
  }



  public function add_state(array $ds = array())
  {
    if(!empty($ds))
    {
      $arr = array(
        'order_code' => $ds['order_code'],
        'state' => $ds['state'],
        'update_user' => (empty($ds['update_user']) ? get_cookie('uname') : $ds['update_user']),
        'time_upd' => date('H:i:s')
      );

      return $this->db->insert('order_state_change', $arr);
    }

    return FALSE;
  }



	public function add_wms_state(array $ds=array())
	{
		return $this->db->insert('order_state_change', $ds);
	}


	public function is_exists_state($order_code, $state)
	{
		$rs = $this->db->where('order_code', $order_code)->where('state', $state)->count_all_results('order_state_change');

		if($rs > 0)
		{
			return TRUE;
		}

		return FALSE;
	}



  public function get_order_state($code)
  {
    $rs = $this->db->where('order_code', $code)->get('order_state_change');
    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return array();
  }


}//--- end class
?>
