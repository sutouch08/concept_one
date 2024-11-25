<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Sales_order_state_model extends CI_Model
{

  private $tb = "sales_order_state_change";

  public function __construct()
  {
    parent::__construct();
  }


  public function add_state(array $ds = array())
  {
    if(!empty($ds))
    {
      return $this->db->insert($this->tb, $ds);
    }

    return FALSE;
  }


	public function is_exists_state($code, $state)
	{
		$rs = $this->db->where('code', $code)->where('state', $state)->count_all_results($this->tb);

		if($rs > 0)
		{
			return TRUE;
		}

		return FALSE;
	}



  public function get_order_state($code)
  {
    $rs = $this->db->where('code', $code)->get($this->tb);
    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


}//--- end class
?>
