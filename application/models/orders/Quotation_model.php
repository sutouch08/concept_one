<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Quotation_model extends CI_Model
{

  public function __construct()
  {
    parent::__construct();
  }


	public function is_exists_details($docEntry)
	{
		$count = $this->ms->where('DocEntry', $docEntry)->count_all_results('QUT1');
		return $count > 0  ? TRUE : FALSE;
	}


	public function get_id($code)
	{
		$rs = $this->ms->select('DocEntry')->where('DocNum', $code)->get('OQUT');

		return $rs->num_rows() === 1 ? $rs->row()->DocEntry : NULL;
	}


  public function get_details($docEntry)
  {
    $rs = $this->ms
		->select('ItemCode AS code, PriceBefDi AS price, OpenQty AS qty, DiscPrcnt AS discount')
		->where('DocEntry', $docEntry)
		->get('QUT1');

		if($rs->num_rows() > 0)
		{
			return $rs->result();
		}

		return NULL;
  }


} //--- End class


 ?>
