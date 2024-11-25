<?php
class Order_payment_model extends CI_Model
{
  private $tb = "order_payment";

  public function __construct()
  {
    parent::__construct();
  }

  public function count_rows(array $ds = array())
  {
    $this->db
    ->from('order_payment AS op')
    ->join('orders AS od', 'od.code = op.order_code', 'left')
    ->join('customers AS cu', 'cu.code = od.customer_code', 'left');

    if($ds['valid'] != 'all')
    {
      $this->db->where('valid', $ds['valid']);
    }

    if(!empty($ds['code']))
    {
      $this->db->like('op.order_code', $ds['code']);
    }

    //--- dp code
    if( ! empty($ds['dp_code']))
    {
      $this->db->like('op.dp_code', $ds['dp_code']);
    }

    if(isset($ds['is_export']) && $ds['is_export'] != 'all')
    {
      if($ds['is_export'] == 'Y')
      {
        $this->db->where('dp_code IS NOT NULL', NULL, FALSE)->where('dp_code !=', '0');
      }
      else
      {
        $this->db->where('dp_code IS NULL', NULL, FALSE);
      }
    }

    if(!empty($ds['channels']) && $ds['channels'] !== 'all')
    {
      $this->db->where('od.channels_code', $ds['channels']);
    }

    if(!empty($ds['customer']))
    {
      $this->db->group_start();
      $this->db->like('cu.name', $ds['customer']);
      $this->db->or_like('od.customer_ref', $ds['customer']);
      $this->db->group_end();
    }

    //--- รหัส/ชื่อ ลูกค้า
    if(!empty($ds['account']))
    {
      $this->db->where('id_account', $ds['account']);
    }

    //---- user name / display name
    if( ! empty($ds['user']) && $ds['user'] != 'all')
    {
      $this->db->where('op.user', $ds['user']);
    }


    if($ds['from_date'] != '' && $ds['to_date'] != '')
    {
      $this->db->where('pay_date >=', from_date($ds['from_date']));
      $this->db->where('pay_date <=', to_date($ds['to_date']));
    }

    return $this->db->count_all_results();
  }


  public function get_list(array $ds = array(), $perpage = 20, $offset = 0)
  {
    $this->db
    ->select('op.*, od.channels_code, cu.name AS customer_name, od.customer_ref')
    ->from('order_payment AS op')
    ->join('orders AS od', 'od.code = op.order_code', 'left')
    ->join('customers AS cu', 'cu.code = od.customer_code', 'left');

    if(isset($ds['valid']) && $ds['valid'] != 'all')
    {
      $this->db->where('op.valid', $ds['valid']);
    }

    //---- เลขที่เอกสาร
    if( ! empty($ds['code']))
    {
      $this->db->like('op.order_code', $ds['code']);
    }

    //--- dp code
    if( ! empty($ds['dp_code']))
    {
      $this->db->like('op.dp_code', $ds['dp_code']);
    }

    if(isset($ds['is_export']) && $ds['is_export'] != 'all')
    {
      if($ds['is_export'] == 'Y')
      {
        $this->db->where('dp_code IS NOT NULL', NULL, FALSE)->where('dp_code !=', '0');
      }
      else
      {
        $this->db->where('dp_code IS NULL', NULL, FALSE);
      }
    }


    if( ! isset($ds['channels']) && $ds['channels'] != 'all')
    {
      $this->db->where('od.channels_code', $ds['channels']);
    }


    if( ! empty($ds['customer']))
    {
      $this->db->group_start();
      $this->db->like('cu.name', $ds['customer']);
      $this->db->or_like('od.customer_ref', $ds['customer']);
      $this->db->group_end();
    }

    //--- รหัส/ชื่อ ลูกค้า
    if( ! empty($ds['account']))
    {
      $this->db->where('op.id_account', $ds['account']);
    }

    //---- user name / display name
    if( ! empty($ds['user']) && $ds['user'] != 'all')
    {
      $this->db->where('op.user', $ds['user']);
    }

    if(!empty($ds['from_date']) && !empty($ds['to_date']))
    {
      $this->db->where('op.pay_date >=', from_date($ds['from_date']));
      $this->db->where('op.pay_date <=', to_date($ds['to_date']));
    }

    $rs = $this->db
    ->order_by('op.id', 'DESC')
    ->limit($perpage, $offset)
    ->get();

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function add(array $ds = array())
  {
    if(!empty($ds))
    {
      return $this->db->replace($this->tb, $ds);
    }

    return FALSE;
  }


  public function get($code)
  {
    $rs = $this->db->where('order_code', $code)->get($this->tb);
    if($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return FALSE;
  }


  public function get_by_id($id)
  {
    $rs = $this->db->where('id', $id)->get($this->tb);

    if($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return NULL;
  }


  public function get_amount($code)
  {
    $rs = $this->db->select_sum('pay_amount')->where('order_code', $code)->where('valid', 1)->get($this->tb);

    if($rs->num_rows() === 1)
    {
      return $rs->row()->pay_amount;
    }

    return 0;
  }


  public function get_detail($id)
  {
    $rs = $this->db->where('id', $id)->get($this->tb);
    if($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return FALSE;
  }


  public function get_by_dp_code($dp_code)
  {
    $rs = $this->db
    ->where('dp_code IS NOT NULL', NULL, FALSE)
    ->where('dp_code', $dp_code)
    ->get($this->tb);

    if($rs->num_rows() > 0)
    {
      return $rs->row();
    }

    return NULL;
  }


  public function update($id, array $ds = array())
  {
    return $this->db->where('id', $id)->update($this->tb, $ds);
  }


  public function valid_payment($id)
  {
    return $this->db->set('valid', 1)->where('id', $id)->update($this->tb);
  }


  public function un_valid_payment($id)
  {
    return $this->db->set('valid', 0)->where('id', $id)->update($this->tb);
  }


  public function delete($id)
  {
    return $this->db->where('id', $id)->delete($this->tb);
  }

  public function is_exists($code)
  {
    $rs = $this->db->select('order_code')
    ->where('order_code', $code)
    ->get($this->tb);
    if($rs->num_rows() === 1)
    {
      return TRUE;
    }

    return FALSE;
  }


	//---- for check transection
	public function has_account_transection($id_account)
	{
		$rs = $this->db->where('id_account', $id_account)->count_all_results($this->tb);

		if($rs > 0)
		{
			return TRUE;
		}

		return FALSE;
	}

} //--- end class
?>
