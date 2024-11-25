<?php
class User_model extends CI_Model
{
  public function __construct()
  {
    parent::__construct();
  }



  public function get_all($all = TRUE)
  {
    $this->db
    ->select('u.*')
    ->select('p.name AS profile_name')
    ->from('user AS u')
    ->join('profile AS p', 'u.id_profile = p.id', 'left');

    if( ! $all)
    {
      $this->db->where('active', 1);
    }

    $rs = $this->db->order_by('u.uname', 'ASC')->get();

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }
  }


  public function new_user(array $data = array())
  {
    if(!empty($data))
    {
      return $this->db->insert('user', $data);
    }

    return FALSE;
  }




  public function update_user($id, array $ds = array())
  {
    if(!empty($ds))
    {
      $this->db->where('id', $id);
      return $this->db->update('user', $ds);
    }

    return FALSE;
  }



  public function delete_user($id)
  {
    return $this->db->where('id', $id)->delete('user');
  }



  public function get_user($id)
  {
    $rs = $this->db->where('id', $id)->get('user');
    if($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return FALSE;
  }


  public function get_user_by_uid($uid)
  {
    $rs = $this->db->where('uid', $uid)->get('user');
    if($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return FALSE;
  }


  public function get($uname)
  {
    $rs = $this->db->where('uname', $uname)->get('user');
    if($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return FALSE;
  }



  public function get_name($uname)
  {
    $rs = $this->db->where('uname', $uname)->get('user');
    if($rs->num_rows() == 1)
    {
      return $rs->row()->name;
    }

    return "";
  }


  public function get_users(array $ds = array(), $perpage = 50, $offset = 0)
  {

		$this->db
		->select('user.*, user.name AS dname, profile.name AS pname')
		->from('user')
		->join('profile', 'user.id_profile = profile.id', 'left');

		if(!empty($ds['uname']))
		{
			$this->db->like('user.uname', $ds['uname']);
		}

		if(!empty($ds['dname']))
		{
			$this->db->like('user.name', $ds['dname']);
		}

		if(!empty($ds['profile']))
		{
			$this->db->like('profile.name', $ds['profile']);
		}

		$rs = $this->db->order_by('user.name', 'ASC')->limit($perpage, $offset)->get();

		if($rs->num_rows() > 0)
		{
			return $rs->result();
		}

    return NULL;
  }



  public function get_list(array $ds = array(), $perpage = 50, $offset = 0)
  {

		$this->db
		->select('user.*, user.name AS dname, profile.name AS pname')
		->from('user')
		->join('profile', 'user.id_profile = profile.id', 'left');

		if( ! empty($ds['uname']))
		{
			$this->db->like('user.uname', $ds['uname']);
		}

		if( ! empty($ds['dname']))
		{
			$this->db->like('user.name', $ds['dname']);
		}

		if( ! empty($ds['profile']))
		{
			$this->db->like('profile.name', $ds['profile']);
		}

    if(isset($ds['status']) && $ds['status'] != "" && $ds['status'] != 'all')
    {
      $this->db->where('user.active', $ds['status']);
    }

		$rs = $this->db->order_by('user.name', 'ASC')->limit($perpage, $offset)->get();

		if($rs->num_rows() > 0)
		{
			return $rs->result();
		}

    return NULL;
  }





  public function count_rows(array $ds = array())
  {
		$this->db
		->from('user')
		->join('profile', 'user.id_profile = profile.id', 'left');

		if(!empty($ds['uname']))
		{
			$this->db->like('user.uname', $ds['uname']);
		}

		if(!empty($ds['dname']))
		{
			$this->db->like('user.name', $ds['dname']);
		}

		if(!empty($ds['profile']))
		{
			$this->db->like('profile.name', $ds['profile']);
		}

    if(isset($ds['status']) && $ds['status'] != "" && $ds['status'] != 'all')
    {
      $this->db->where('user.active', $ds['status']);
    }

    return $this->db->count_all_results();
  }






  public function get_permission($menu, $uid, $id_profile)
  {
    if(!empty($menu))
    {
      $rs = $this->db->where('code', $menu)->get('menu');
      if($rs->num_rows() === 1)
      {
        if($rs->row()->valid == 1)
        {
					if($id_profile == -987654321)
					{
						$ds = new stdClass();
	          $ds->can_view = 1;
	          $ds->can_add = 1;
	          $ds->can_edit = 1;
	          $ds->can_delete = 1;
	          $ds->can_approve = 1;
	          return $ds;
					}
					else
					{
						return $this->get_profile_permission($menu, $id_profile);
					}
        }
        else
        {
          $ds = new stdClass();
          $ds->can_view = 1;
          $ds->can_add = 1;
          $ds->can_edit = 1;
          $ds->can_delete = 1;
          $ds->can_approve = 1;
          return $ds;
        }
      }

    }

    return FALSE;
  }


  private function get_user_permission($menu, $uid)
  {
    $rs = $this->db->where('menu', $menu)->where('uid', $uid)->get('permission');
    return $rs->num_rows() == 1 ? $rs->row() : FALSE;
  }


  private function get_profile_permission($menu, $id_profile)
  {
    $rs = $this->db->where('menu', $menu)->where('id_profile', $id_profile)->get('permission');
    return $rs->num_rows() == 1 ? $rs->row() : FALSE;
  }


  //--- activate suspended user by id
  public function active_user($id)
  {
    $this->db->set('active', 1)->where('id', $id);

    if($this->db->update('user'))
    {
      return TRUE;
    }

    return $this->db->error();
  }




//---- Suspend activeted user by id
  public function disactive_user($id)
  {
    $this->db->set('active', 0)->where('id', $id);
    if($this->db->update('user'))
    {
      return TRUE;
    }

    return $this->db->error();
  }





  public function is_exists_uname($uname, $id)
  {
    if($id !== '')
    {
      $this->db->where('id !=', $id);
    }

    $rs = $this->db->where('uname', $uname)->get('user');

    if($rs->num_rows() > 0)
    {
      return TRUE;
    }

    return FALSE;
  }



  public function is_exists_display_name($dname, $id)
  {
    if($id !== '')
    {
      $this->db->where('id !=', $id);
    }

    $rs = $this->db->where('name', $dname)->get('user');

    if($rs->num_rows() > 0)
    {
      return TRUE;
    }

    return FALSE;
  }



  public function is_skey_exists($skey, $uid)
  {
    $rs = $this->db->where('skey', $skey)->where('uid !=', $uid)->get('user');
    if($rs->num_rows() > 0)
    {
      return TRUE;
    }

    return FALSE;
  }



  public function get_user_credentials($uname)
  {
    $this->db->where('uname', $uname);
    $rs = $this->db->get('user');
    return $rs->row();
  }





  public function change_password($id, $pwd)
  {
    $this->db->set('pwd', $pwd);
    $this->db->where('id', $id);
    return $this->db->update('user');
  }



  public function verify_uid($uid)
  {
    $this->db->select('uid');
    $this->db->where('uid', $uid);
    $this->db->where('active', 1);
    $rs = $this->db->get('user');

    return $rs->num_rows() === 1 ? TRUE : FALSE;
  }


  public function is_viewer($uid)
  {
    $rs = $this->db
    ->select('uid')
    ->where('uid', $uid)
    ->where('is_viewer', 1)
    ->get('user');

    return $rs->num_rows() === 1 ? TRUE : FALSE;
  }




  public function get_user_credentials_by_skey($skey)
  {
    if(!empty($skey))
    {
      $rs = $this->db->where('skey', $skey)->get('user');
      if($rs->num_rows() === 1)
      {
        return $rs->row();
      }
    }

    return FALSE;
  }


  public function search($txt)
  {
    $qr = "SELECT uname FROM user WHERE uname LIKE '%".$txt."%' OR name LIKE '%".$txt."%'";
    $rs = $this->db->query($qr);
    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }
    else
    {
      return array();
    }

  }


	public function has_transection($uname)
	{
		//-- all orders
		if($this->db->where('user', $uname)->count_all_results('orders') > 0)
		{
			return TRUE;
		}

		//--- Receive product
		if($this->db->where('user', $uname)->count_all_results('receive_product') > 0)
		{
			return TRUE;
		}

		//--- Receive transform
		if($this->db->where('user', $uname)->count_all_results('receive_transform') > 0)
		{
			return TRUE;
		}

		//--- Return order
		if($this->db->where('user', $uname)->count_all_results('return_order') > 0)
		{
			return TRUE;
		}

		//--- Transfer
		if($this->db->where('user', $uname)->count_all_results('transfer') > 0)
		{
			return TRUE;
		}

		//--- Move
		if($this->db->where('user', $uname)->count_all_results('move') > 0)
		{
			return TRUE;
		}

		//--- WD
		if($this->db->where('user', $uname)->count_all_results('consignment_order') > 0)
		{
			return TRUE;
		}

		//--- WM
		if($this->db->where('user', $uname)->count_all_results('consign_order') > 0)
		{
			return TRUE;
		}

		//--- AJ
		if($this->db->where('user', $uname)->count_all_results('adjust') > 0)
		{
			return TRUE;
		}

		//--- AC
		if($this->db->where('user', $uname)->count_all_results('adjust_consignment') > 0)
		{
			return TRUE;
		}

		//--- WG
		if($this->db->where('user', $uname)->count_all_results('adjust_transform') > 0)
		{
			return TRUE;
		}

		//--- WX
		if($this->db->where('user', $uname)->count_all_results('consign_check') > 0)
		{
			return TRUE;
		}

		return FALSE;
	}


} //---- End class

 ?>
