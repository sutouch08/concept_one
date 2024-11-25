<?php
class Unit_model extends CI_Model
{
  public function __construct()
  {
    parent::__construct();
  }


  public function get_data()
  {
    $rs = $this->ms
    ->select('UomCode AS code')
    ->select('UomName AS name')
    ->order_by('UomCode', 'ASC')
    ->get('OUOM');

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return FALSE;
  }

  public function get_list()
  {
    $rs = $this->ms
    ->select('O.UomEntry AS id, O.UomCode AS code, O.UomName AS name, G.UgpEntry AS group_id')
    ->from('OUOM AS O')
    ->join('OUGP AS G', 'O.UomEntry = G.BaseUom', 'left')
    ->where('O.UomEntry >', 0)
    ->get();

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }
} //--- end class

 ?>
