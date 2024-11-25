<?php
class Bank_model extends CI_Model
{
  public function __construct()
  {
    parent::__construct();
  }


	public function add(array $ds = array())
	{
		if(!empty($ds))
		{
			return $this->db->insert('bank_account', $ds);
		}

		return FALSE;
	}



	public function update($id, array $ds = array())
	{
		if(!empty($ds) && !empty($id))
		{
			return $this->db->where('id', $id)->update('bank_account', $ds);
		}

		return FALSE;
	}



	public function delete($id)
	{
		return $this->db->where('id', $id)->delete('bank_account');
	}


	public function get($id)
	{
		$rs = $this->db->where('id', $id)->get('bank_account');

		if($rs->num_rows() === 1)
		{
			return $rs->row();
		}

		return NULL;
	}


	public function get_id($acc_no)
	{
		$rs = $this->db->select('id')->where('acc_no', $acc_no)->get('bank_account');
		if($rs->num_rows() === 1)
		{
			return $rs->row()->id;
		}

		return NULL;
	}


  public function get_active_bank()
  {
    $rs = $this->db->where('active', 1)->get('bank_account');

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


	public function is_exists($acc_no = NULL, $id = NULL)
	{
		if(!empty($acc_no))
		{
			$this->db->where('acc_no', $acc_no);
			if($id !== NULL)
			{
				$this->db->where('id !=', $id);
			}

			$rs = $this->db->get('bank_account');

			if($rs->num_rows() > 0)
			{
				return TRUE;
			}

			return FALSE;
		}

		return FALSE;
	}


  public function get_data()
  {
    $rs = $this->db->get('bank_account');

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return array();
  }


  public function get_all()
  {
    $rs = $this->db->get('bank_account');

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }

  public function count_rows(array $ds = array())
  {
    if(!empty($ds['bank_code']) && $ds['bank_code'] !== 'all')
    {
      $this->db->where('bank_code', $ds['bank_code']);
    }

    if(!empty($ds['account_name']))
    {
      $this->db->like('acc_name', $ds['account_name']);
    }


    if(!empty($ds['account_no']))
    {
      $this->db->like('acc_no', $ds['account_no']);
    }

    if(!empty($ds['branch']))
    {
      $this->db->like('branch', $ds['branch']);
    }

    return $this->db->count_all_results('bank_account');
  }





  public function get_list(array $ds = array(), $perpage = 20, $offset = 0)
  {
    if(!empty($ds['bank_code']) && $ds['bank_code'] !== 'all')
    {
      $this->db->where('bank_code', $ds['bank_code']);
    }

    if(!empty($ds['account_name']))
    {
      $this->db->like('acc_name', $ds['account_name']);
    }


    if(!empty($ds['account_no']))
    {
      $this->db->like('acc_no', $ds['account_no']);
    }

    if(!empty($ds['branch']))
    {
      $this->db->like('branch', $ds['branch']);
    }

    $this->db->order_by('id', 'ASC')->limit($perpage, $offset);

    //echo $this->db->get_compiled_select('bank_account');

    $rs = $this->db->get('bank_account');

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }




  public function get_account_detail($id)
  {
    $rs = $this->db->where('id', $id)->get('bank_account');
    if($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return FALSE;
  }




  public function get_banks($code = NULL)
  {
    if(!empty($code))
    {
      $this->db->where('code', $code);
    }

    $rs = $this->db->get('bank');

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }



	public function get_bank($code)
	{
		$rs = $this->db->where('code', $code)->get('bank');

		if($rs->num_rows() === 1)
		{
			return $rs->row();
		}

		return NULL;
	}


} //---- End class


 ?>
