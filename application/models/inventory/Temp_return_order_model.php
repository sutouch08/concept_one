<?php
class Temp_return_order_model extends CI_Model
{
  public function __construct()
  {
    parent::__construct();
  }


  public function get($docEntry)
  {
    $rs = $this->mc->where('DocEntry', $docEntry)->get('ORDN');
    if($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return NULL;
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
    }

    return $this->mc->count_all_results('ORDN');
  }



  public function get_list(array $ds = array(), $perpage = NULL, $offset = 0)
  {
    $this->mc
    ->select('DocEntry, U_ECOMNO, DocDate, CardCode, CardName')
    ->select('F_E_Commerce, F_E_CommerceDate')
    ->select('F_Sap, F_SapDate, U_BOOKCODE')
    ->select('Message');

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
    }

    $this->mc->order_by('DocDate', 'DESC')->order_by('U_ECOMNO', 'DESC');

    if(!empty($perpage))
    {
      $this->mc->limit($perpage, $offset);
    }

    $rs = $this->mc->get('ORDN');

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }



  public function get_detail($docEntry)
  {
    $rs = $this->mc
    ->select('U_ECOMNO, ItemCode, Dscription, Quantity, FisrtBin AS BinCode')
    ->where('DocEntry', $docEntry)
    ->get('RDN1');

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }



  public function get_error_list()
  {
    $rs = $this->mc
    ->select('U_ECOMNO AS code')
    ->where('F_Sap', 'N')
    ->order_by('U_ECOMNO', 'ASC')
    ->get('ORDN');

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


	public function removeTemp($docEntry)
	{
		$this->mc->trans_begin();
		$rd = $this->mc->where('DocEntry', $docEntry)->delete('RDN1');
		$ro = $this->mc->where('DocEntry', $docEntry)->delete('ORDN');

		if($rd && $ro)
		{
			$this->mc->trans_commit();
			return TRUE;
		}
		else
		{
			$this->mc->trans_rollback();
			return FALSE;
		}

		return FALSE;
	}

} //--- end model

?>
