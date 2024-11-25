<?php
class Temp_items_model extends CI_Model
{
  public function __construct()
  {
    parent::__construct();
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
		->get('OITM');

		if($rs->num_rows() > 0)
		{
			return $rs->row();
		}

		return NULL;
	}


	public function delete($docEntry)
	{
		return $this->mc->where('DocEntry', $docEntry)->delete('OITM');
	}


  public function count_rows(array $ds = array())
  {
    if(!empty($ds['code']))
    {
      $this->mc->like('ItemCode', $ds['code']);
    }

    if(!empty($ds['from_date']) && !empty($ds['to_date']))
    {
      $this->mc->where('F_E_CommerceDate >=', from_date($ds['from_date']));
      $this->mc->where('F_E_CommerceDate <=', to_date($ds['to_date']));
    }

    if($ds['status'] != 'all')
    {
      if($ds['status'] === 'Y')
      {
        $this->mc->where_in('F_Sap', array('A', 'U', 'Y'));
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

    return $this->mc->count_all_results('OITM');
  }



  public function get_list(array $ds = array(), $perpage = 20, $offset = 0)
  {
    $this->mc
    ->select('DocEntry, ItemCode, ItemName, CodeBars')
    ->select('BuyUnitMsr, SalUnitMsr, InvntryUom, InvntItem')
    ->select('F_E_Commerce, F_E_CommerceDate')
    ->select('F_Sap, F_SapDate, Message');

    if(!empty($ds['code']))
    {
      $this->mc->like('ItemCode', $ds['code']);
    }

    if(!empty($ds['from_date']) && !empty($ds['to_date']))
    {
      $this->mc->where('F_E_CommerceDate >=', from_date($ds['from_date']));
      $this->mc->where('F_E_CommerceDate <=', to_date($ds['to_date']));
    }

    if($ds['status'] != 'all')
    {
      if($ds['status'] === 'Y')
      {
        $this->mc->where_in('F_Sap', array('A', 'U', 'Y'));
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

    $this->mc->order_by('F_E_CommerceDate', 'DESC')->order_by('ItemCode', 'ASC');
    $this->mc->limit($perpage, $offset);
    $rs = $this->mc->get('OITM');

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }

} //--- end model

?>
