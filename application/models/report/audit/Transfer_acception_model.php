<?php
class Transfer_acception_model extends CI_Model
{
  public function __construct()
  {
    parent::__construct();
  }


  public function get($doc, $filter)
  {
    $result = NULL;

    switch($doc)
    {
      case 'WR' :
        $result = $this->WR($filter);
        break;
      case 'RT' :
        $result = $this->RT($filter);
        break;
      case 'SM' :
        $result = $this->SM($filter);
        break;
      case 'RN' :
        $result = $this->RN($filter);
        break;
      case 'WW' :
        $result = $this->WW($filter);
        break;
      case 'MV' :
        $result = $this->MV($filter);
        break;
    }

    return $result;
  }


  //---- WR, RT, SM
  public function WR($ds = array())
  {
    $this->db
    ->select("t.*, u.name AS accept_name, o.name AS owner_name")
    ->from("receive_product AS t")
    ->join("user AS u", "t.accept_by = u.uname", "left")
    ->join("zone AS z", "t.zone_code = z.code", "left")
    ->join("user AS o", "z.user_id = o.id", "left")
    ->where_in("t.status", array(1, 3, 4))
    ->where("t.must_accept", 1)
    ->where("t.is_expire", 0);

    if( ! empty($ds['from_date']) && ! empty($ds['to_date']))
    {
      $this->db
      ->where('t.date_add >=', from_date($ds['from_date']))
      ->where('t.date_add <=', to_date($ds['to_date']));
    }

    if( $ds['is_accept'] != 'all')
    {
      $this->db->where('t.is_accept', $ds['is_accept']);
    }

    $rs = $this->db->get();

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function RT($ds = array())
  {
    $this->db
    ->select("t.*, u.name AS accept_name, o.name AS owner_name")
    ->from("receive_transform AS t")
    ->join("user AS u", "t.accept_by = u.uname", "left")
    ->join("zone AS z", "t.zone_code = z.code", "left")
    ->join("user AS o", "z.user_id = o.id", "left")
    ->where_in("t.status", array(1, 3, 4))
    ->where("t.must_accept", 1)
    ->where("t.is_expire", 0);

    if( ! empty($ds['from_date']) && ! empty($ds['to_date']))
    {
      $this->db
      ->where('t.date_add >=', from_date($ds['from_date']))
      ->where('t.date_add <=', to_date($ds['to_date']));
    }

    if( $ds['is_accept'] != 'all')
    {
      $this->db->where('t.is_accept', $ds['is_accept']);
    }

    $rs = $this->db->get();

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function SM($ds = array())
  {
    $this->db
    ->select("t.*, u.name AS accept_name, o.name AS owner_name")
    ->from("return_order AS t")
    ->join("user AS u", "t.accept_by = u.uname", "left")
    ->join("zone AS z", "t.zone_code = z.code", "left")
    ->join("user AS o", "z.user_id = o.id", "left")
    ->where_in("t.status", array(1, 3, 4))
    ->where("t.must_accept", 1)
    ->where("t.is_expire", 0);

    if( ! empty($ds['from_date']) && ! empty($ds['to_date']))
    {
      $this->db
      ->where('t.date_add >=', from_date($ds['from_date']))
      ->where('t.date_add <=', to_date($ds['to_date']));
    }

    if( $ds['is_accept'] != 'all')
    {
      $this->db->where('t.is_accept', $ds['is_accept']);
    }

    $rs = $this->db->get();

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function RN($ds = array())
  {
    $this->db
    ->select("t.*, u.name AS accept_name, o.name AS owner_name")
    ->from("return_lend AS t")
    ->join("user AS u", "t.accept_by = u.uname", "left")
    ->join("zone AS z", "t.to_zone = z.code", "left")
    ->join("user AS o", "z.user_id = o.id", "left")
    ->where_in("t.status", array(1, 3, 4))
    ->where("t.must_accept", 1)
    ->where("t.is_expire", 0);

    if( ! empty($ds['from_date']) && ! empty($ds['to_date']))
    {
      $this->db
      ->where('t.date_add >=', from_date($ds['from_date']))
      ->where('t.date_add <=', to_date($ds['to_date']));
    }

    if( $ds['is_accept'] != 'all')
    {
      $this->db->where('t.is_accept', $ds['is_accept']);
    }

    $rs = $this->db->get();

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function WW($ds = array())
  {
    $this->db
    ->select("t.*")
    ->from("transfer AS t")
    ->where_in("t.status", array(1, 3, 4))
    ->where("t.must_accept", 1)
    ->where("t.is_expire", 0);

    if( ! empty($ds['from_date']) && ! empty($ds['to_date']))
    {
      $this->db
      ->where('t.date_add >=', from_date($ds['from_date']))
      ->where('t.date_add <=', to_date($ds['to_date']));
    }

    if( $ds['is_accept'] != 'all')
    {
      $this->db->where('t.is_accept', $ds['is_accept']);
    }

    $rs = $this->db->get();

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }

  public function MV($ds = array())
  {
    $this->db
    ->select("t.*")
    ->from("move AS t")
    ->where_in("t.status", array(1, 3, 4))
    ->where("t.must_accept", 1)
    ->where("t.is_expire", 0);

    if( ! empty($ds['from_date']) && ! empty($ds['to_date']))
    {
      $this->db
      ->where('t.date_add >=', from_date($ds['from_date']))
      ->where('t.date_add <=', to_date($ds['to_date']));
    }

    if( $ds['is_accept'] != 'all')
    {
      $this->db->where('t.is_accept', $ds['is_accept']);
    }

    $rs = $this->db->get();

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function get_owner_list($doc, $code)
  {
    if($doc == 'WW')
    {
      return $this->get_transfer_owner_list($code);
    }

    if($doc == 'MV')
    {
      return $this->get_move_owner_list($code);
    }
  }

  
  public function get_accept_list($doc, $code)
  {
    if($doc == 'WW')
    {
      return $this->get_transfer_accept_list($code);
    }

    if($doc == 'MV')
    {
      return $this->get_move_accept_list($code);
    }
  }


  public function get_transfer_owner_list($code)
  {
    $list = "";

    $rs = $this->db
    ->select('u.name AS owner_name')
    ->from('transfer_detail AS t')
    ->join('zone AS z', 't.to_zone = z.code', 'left')
    ->join('user AS u', 'z.user_id = u.id', 'left')
    ->where('t.transfer_code', $code)
    ->where('t.must_accept', 1)
    ->where('z.user_id IS NOT NULL', NULL, FALSE)
    ->group_by('z.user_id')
    ->get();

    if($rs->num_rows() > 0)
    {
      $i = 1;

      foreach($rs->result() as $rd)
      {
        $list .= $i === 1 ? $rd->owner_name : ", ".$rd->owner_name;
        $i++;
      }
    }

    return $list;
  }


  public function get_move_owner_list($code)
  {
    $list = "";

    $rs = $this->db
    ->select('u.name AS owner_name')
    ->from('move_detail AS t')
    ->join('zone AS z', 't.to_zone = z.code', 'left')
    ->join('user AS u', 'z.user_id = u.id', 'left')
    ->where('t.move_code', $code)
    ->where('t.must_accept', 1)
    ->where('z.user_id IS NOT NULL', NULL, FALSE)
    ->group_by('z.user_id')
    ->get();

    if($rs->num_rows() > 0)
    {
      $i = 1;

      foreach($rs->result() as $rd)
      {
        $list .= $i === 1 ? $rd->owner_name : ", ".$rd->owner_name;
        $i++;
      }
    }

    return $list;
  }


  public function get_transfer_accept_list($code)
  {
    $list = "";

    $rs = $this->db
    ->select("u.name AS display_name")
    ->from("transfer_detail AS t")
    ->join("user AS u", "t.accept_by = u.uname", "left")
    ->where("t.transfer_code", $code)
    ->where("t.must_accept", 1)
    ->where("t.is_accept", 1)
    ->group_by("t.accept_by")
    ->get();

    if($rs->num_rows() > 0)
    {
      $i = 1;

      foreach($rs->result() as $rd)
      {
        $list .= $i === 1 ? $rd->display_name : ", ".$rd->display_name;
        $i++;
      }
    }
    return $list;
  }


  public function get_move_accept_list($code)
  {
    $list = "";

    $rs = $this->db
    ->select("u.name AS display_name")
    ->from("move_detail AS t")
    ->join("user AS u", "t.accept_by = u.uname", "left")
    ->where("t.move_code", $code)
    ->where("t.must_accept", 1)
    ->where("t.is_accept", 1)
    ->group_by("t.accept_by")
    ->get();

    if($rs->num_rows() > 0)
    {
      $i = 1;

      foreach($rs->result() as $rd)
      {
        $list .= $i === 1 ? $rd->display_name : ", ".$rd->display_name;
        $i++;
      }
    }
    return $list;
  }

} //-- end class

 ?>
