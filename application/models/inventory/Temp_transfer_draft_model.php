<?php
class Temp_transfer_draft_model extends CI_Model
{
  public function __construct()
  {
    parent::__construct();
  }


  public function count_rows(array $ds = array())
  {
    if(!empty($ds['code']))
    {
      $this->mc->like('U_ECOMNO', $ds['code']);
    }

    if(!empty($ds['customer']))
    {
      $this->mc->group_start();
      $this->mc->like('CardCode', $ds['customer']);
      $this->mc->or_like('CardName', $ds['customer']);
      $this->mc->group_end();
    }

    if(!empty($ds['from_date']) && !empty($ds['to_date']))
    {
      $this->mc->where('DocDate >=', from_date($ds['from_date']));
      $this->mc->where('DocDate <=', to_date($ds['to_date']));
    }

    if($ds['status'] != 'all')
    {
      if($ds['status'] === 'Y')
      {
        $this->mc->where('F_Sap', 'Y');
      }
      else if($ds['status'] === 'N')
      {
        $this->mc->where('F_Sap IS NULL', NULL, FALSE);
      }
      else if($ds['status'] === 'E')
      {
        $this->mc->where('F_Sap', 'N');
      }
			else if($ds['status'] ===  'D')
			{
				$this->mc->where('F_Sap', 'D');
			}
    }


    if($ds['is_received'] != 'all')
    {
      if($ds['is_received'] === 'Y')
      {
        $this->mc->where('F_Receipt', 'Y');
      }
      else if($ds['is_received'] === 'N')
      {
        $this->mc->where('F_Receipt IS NULL', NULL, FALSE);
      }
    }

    return $this->mc->count_all_results('DFOWTR');
  }



  public function get_list(array $ds = array(), $perpage = NULL, $offset = 0)
  {
    $this->mc
    ->select('DocEntry, U_ECOMNO, DocDate, CardCode, CardName')
    ->select('Filler, ToWhsCode')
    ->select('F_E_Commerce, F_E_CommerceDate')
    ->select('F_Sap, F_SapDate')
    ->select('F_Receipt, F_ReceiptDate')
    ->select('Message AS Message');

    if(!empty($ds['code']))
    {
      $this->mc->like('U_ECOMNO', $ds['code']);
    }

    if(!empty($ds['customer']))
    {
      $this->mc->group_start();
      $this->mc->like('CardCode', $ds['customer']);
      $this->mc->or_like('CardName', $ds['customer']);
      $this->mc->group_end();
    }

    if(!empty($ds['from_date']) && !empty($ds['to_date']))
    {
      $this->mc->where('DocDate >=', from_date($ds['from_date']));
      $this->mc->where('DocDate <=', to_date($ds['to_date']));
    }

    if($ds['status'] != 'all')
    {
      if($ds['status'] === 'Y')
      {
        $this->mc->where('F_Sap', 'Y');
      }
      else if($ds['status'] === 'N')
      {
        $this->mc->where('F_Sap IS NULL', NULL, FALSE);
      }
      else if($ds['status'] === 'E')
      {
        $this->mc->where('F_Sap', 'N');
      }
			else if($ds['status'] ===  'D')
			{
				$this->mc->where('F_Sap', 'D');
			}
    }


    if($ds['is_received'] != 'all')
    {
      if($ds['is_received'] === 'Y')
      {
        $this->mc->where('F_Receipt', 'Y');
      }
      else if($ds['is_received'] === 'N')
      {
        $this->mc->where('F_Receipt IS NULL', NULL, FALSE);
      }
    }

    $this->mc->order_by('DocDate', 'DESC')->order_by('U_ECOMNO', 'DESC');

    if(!empty($perpage))
    {
      $this->mc->limit($perpage, $offset);
    }

    $rs = $this->mc->get('DFOWTR');

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }



  public function get_detail($docEntry)
  {
    $rs = $this->mc
    ->select('U_ECOMNO, ItemCode, Dscription, Quantity, F_FROM_BIN, F_TO_BIN')
    ->where('DocEntry', $docEntry)
    ->get('DFWTR1');

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }

  public function get($docEntry)
	{
		$rs = $this->mc
		->select('F_Sap')
		->where('DocEntry', $docEntry)
		->group_start()
		->where('F_Sap =', 'N')
		->or_where('F_Sap IS NULL', NULL, FALSE)
		->group_end()
		->get('DFOWTR');

		if($rs->num_rows() > 0)
		{
			return $rs->row();
		}

		return NULL;
	}


	public function delete($docEntry)
	{
		$this->mc->trans_start();
		$this->mc->where('DocEntry', $docEntry)->delete('DFWTR1');
		$this->mc->where('DocEntry', $docEntry)->delete('DFOWTR');
		$this->mc->trans_complete();

		return $this->mc->trans_status();
	}


  public function get_error_list()
  {
    $rs = $this->mc
    ->select('U_ECOMNO AS code')
    ->where('F_Sap', 'N')
    ->order_by('U_ECOMNO', 'ASC')
    ->get('DFOWTR');

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }

} //--- end model

?>
