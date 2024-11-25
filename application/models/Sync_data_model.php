<?php
class Sync_data_model extends CI_Model
{
  public $all = FALSE;

  public function __construct()
  {
    parent::__construct();
  }

  public function get_item_last_date()
  {
    if($this->all === TRUE)
    {
      return sap_date('2010-01-01 00:00:00');
    }
    else
    {
      $rs = $this->mc->select_max('UpdateDate', 'UpdateDate')->get('OITM');
      return sap_date($rs->row()->UpdateDate, TRUE);
    }

    return sap_date(date('Y-d-m 00:00:00'));
  }


  public function get_model_last_date()
  {
    if($this->all === TRUE)
    {
      return sap_date('2010-01-01 00:00:00');
    }
    else
    {
      $rs = $this->mc->select_max('UpdateDate', 'UpdateDate')->get('MODEL');
      return sap_date($rs->row()->UpdateDate, TRUE);
    }

    return sap_date(date('Y-d-m 00:00:00'));
  }

  public function get_color_last_date()
  {
    if($this->all === TRUE)
    {
      return sap_date('2010-01-01 00:00:00');
    }
    else
    {
      $rs = $this->mc->select_max('UpdateDate', 'UpdateDate')->get('COLOR');
      return sap_date($rs->row()->UpdateDate, TRUE);
    }

    return sap_date(date('Y-d-m 00:00:00'));
  }


  public function get_size_last_date()
  {
    if($this->all === TRUE)
    {
      return sap_date('2010-01-01 00:00:00');
    }
    else
    {
      $rs = $this->mc->select_max('UpdateDate', 'UpdateDate')->get('SIZE');
      return sap_date($rs->row()->UpdateDate, TRUE);
    }

    return sap_date(date('Y-d-m 00:00:00'));
  }



  public function get_group_last_date()
  {
    if($this->all === TRUE)
    {
      return sap_date('2010-01-01 00:00:00');
    }
    else
    {
      $rs = $this->mc->select_max('UpdateDate', 'UpdateDate')->get('GROUP');
      return sap_date($rs->row()->UpdateDate, TRUE);
    }

    return sap_date(date('Y-d-m 00:00:00'));
  }


  public function get_sub_group_last_date()
  {
    if($this->all === TRUE)
    {
      return sap_date('2010-01-01 00:00:00');
    }
    else
    {
      $rs = $this->mc->select_max('UpdateDate', 'UpdateDate')->get('MAJOR');
      return sap_date($rs->row()->UpdateDate, TRUE);
    }

    return sap_date(date('Y-d-m 00:00:00'));
  }


  public function get_cate_last_date()
  {
    if($this->all === TRUE)
    {
      return sap_date('2010-01-01 00:00:00');
    }
    else
    {
      $rs = $this->mc->select_max('UpdateDate', 'UpdateDate')->get('CATE');
      return sap_date($rs->row()->UpdateDate, TRUE);
    }

    return sap_date(date('Y-d-m 00:00:00'));
  }


  public function get_kind_last_date()
  {
    if($this->all === TRUE)
    {
      return sap_date('2010-01-01 00:00:00');
    }
    else
    {
      $rs = $this->mc->select_max('UpdateDate', 'UpdateDate')->get('SUBTYPE');
      return sap_date($rs->row()->UpdateDate, TRUE);
    }

    return sap_date(date('Y-d-m 00:00:00'));
  }


  public function get_type_last_date()
  {
    if($this->all === TRUE)
    {
      return sap_date('2010-01-01 00:00:00');
    }
    else
    {
      $rs = $this->mc->select_max('UpdateDate', 'UpdateDate')->get('TYPE');
      return sap_date($rs->row()->UpdateDate, TRUE);
    }

    return sap_date(date('Y-d-m 00:00:00'));
  }


  public function get_brand_last_date()
  {
    if($this->all === TRUE)
    {
      return sap_date('2010-01-01 00:00:00');
    }
    else
    {
      $rs = $this->mc->select_max('UpdateDate', 'UpdateDate')->get('BRAND');
      return sap_date($rs->row()->UpdateDate, TRUE);
    }

    return sap_date(date('Y-d-m 00:00:00'));
  }


  public function add_logs(array $ds = array())
  {
    if(!empty($ds))
    {
      return $this->db->insert('ix_sync_logs', $ds);
    }
    return FALSE;
  }


  public function clear_old_logs($days = 7)
  {

    $date = date('Y-m-d 00:00:00', strtotime("-{$days} days"));

    return $this->db->where('date_upd <', $date)->delete('ix_sync_logs');
  }

}//--- end class
