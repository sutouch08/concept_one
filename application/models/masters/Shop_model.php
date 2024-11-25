<?php
class Shop_model extends CI_Model
{
  private $tb = "shop";
  private $tu = "shop_users";

  public function __construct()
  {
    parent::__construct();
  }

  //--- add new zone (use with sync only)
  public function add(array $ds = array())
  {
    if(!empty($ds))
    {
      if($this->db->insert($this->tb, $ds))
			{
				return $this->db->insert_id();
			}
    }

    return FALSE;
  }


  //--- update zone with sync only
  public function update($id, $ds = array())
  {
    if(!empty($ds))
    {
      return $this->db->where('id', $id)->update($this->tb, $ds);
    }

    return FALSE;
  }


	public function get($id)
	{
		$rs = $this->db->where('id', $id)->get($this->tb);
		if($rs->num_rows() === 1)
		{
			return $rs->row();
		}

		return NULL;
	}


  public function get_by_id($id)
  {
    $rs = $this->db
    ->select('shop.*, customers.name AS customer_name, zone.name AS zone_name, zone.warehouse_code')
    ->from('shop')
    ->join('customers', 'shop.customer_code = customers.code', 'left')
    ->join('zone', 'shop.zone_code = zone.code', 'left')
    ->where('shop.id', $id)
    ->get();

    if($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return NULL;
  }

	public function get_by_code($code = NULL)
	{
		if(! is_null($code))
		{
			$rs = $this->db
			->select('shop.*, customers.name AS customer_name, zone.name AS zone_name')
			->from('shop')
			->join('customers', 'shop.customer_code = customers.code', 'left')
			->join('zone', 'shop.zone_code = zone.code', 'left')
			->where('shop.code', $code)
			->get();

			if($rs->num_rows() === 1)
			{
				return $rs->row();
			}
		}

		return NULL;
	}



	public function add_user(array $ds = array())
	{
		if(!empty($ds))
		{
			return $this->db->insert('shop_users', $ds);
		}

		return FALSE;
	}


	public function delete_shop_user($id)
	{
		return $this->db->where('id', $id)->delete('shop_users');
	}



	public function get_shop_user($id = NULL)
	{
		$this->db
		->select('shop_users.*, user.name')
		->from('shop_users')
		->join('user', 'shop_users.uname = user.uname', 'left');

    if( ! empty($id))
    {
      $this->db->where('shop_users.shop_id', $id);
    }

    $rs = $this->db->get();

		if($rs->num_rows() > 0)
		{
			return $rs->result();
		}

		return NULL;
	}


  public function validate_shop_user($shop_id, $uname)
  {
    $count = $this->db
    ->where('shop_id', $shop_id)
    ->where('uname', $uname)
    ->where('active', 1)
    ->count_all_results($this->tu);

    if($count > 0)
    {
      return TRUE;
    }

    return FALSE;
  }


  public function get_shop_payments($id)
  {
    $rs = $this->db
    ->where_in('code', $this->shop_payment_in($id))
    ->get('payment_method');

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function shop_payment_in($shop_id)
  {
    $qr = "SELECT * FROM shop WHERE id = {$shop_id}";

    $rs = $this->db->query($qr);

    if($rs->num_rows() === 1)
    {
      return [$rs->row()->cash_payment, $rs->row()->transfer_payment, $rs->row()->card_payment];
    }

    return NULL;
  }


	public function is_exists_user($shop_id, $uname)
	{
		$rs = $this->db->where('shop_id', $shop_id)->where('uname', $uname)->get('shop_users');
		if($rs->num_rows() > 0)
		{
			return TRUE;
		}

		return FALSE;
	}


  public function is_exists_prefix($prefix, $shop_id = NULL)
  {
    if( ! empty($shop_id))
    {
      $this->db->where('id !=', $shop_id);
    }

    $count = $this->db->where('prefix', $prefix)->count_all_results($this->tb);

    return $count > 0 ? TRUE : FALSE;
  }


  //----
  public function delete($id)
  {
    return $this->db->where('id', $id)->delete($this->tb);
  }


	//---- checl transection
	public function has_transection($id)
	{
		$pos_machine = $this->db->where('shop_id', $id)->count_all_results('shop_pos');
    $pos_orders = $this->db->where('shop_id', $id)->count_all_results('order_pos');
    $transection = $pos_machine + $pos_orders;

		return $transection > 0 ? TRUE : FALSE;
	}



  //--- check shop exists or not
  public function is_exists($code)
  {
    $count = $this->db->where('code', $code)->count_all_results($this->tb);

    return $count > 0 ? TRUE : FALSE;
  }


  //--- check zone exists by id
  public function is_exists_id($id)
  {
    $count = $this->db->where('id', $id)->count_all_results($this->tb);

    return $count > 0 ? TRUE : FALSE;
  }


  public function is_exists_code($code, $id = NULL)
  {
    if( ! empty($id))
    {
      $this->db->where('id !=', $id);
    }

    $count = $this->db->where('code', $code)->count_all_results($this->tb);

    return $count > 0 ? TRUE : FALSE;
  }



  public function is_exists_name($name, $id = NULL)
  {
    if( ! empty($id))
    {
      $this->db->where('id !=', $id);
    }

    $count = $this->db->where('name', $name)->count_all_results($this->tb);

    return $count > 0 ? TRUE : FALSE;
  }


	public function is_exists_zone($zone_code, $id = NULL)
	{
    if( ! empty($id))
    {
      $this->db->where('id !=', $id);
    }

		$count = $this->db->where('zone_code', $zone_code)->count_all_results($this->tb);

		return $count > 0 ? TRUE : FALSE;
	}


  public function count_rows(array $ds = array())
  {
  	if(! empty($ds))
		{
			$this->db
      ->from('shop')
      ->join('zone', 'shop.zone_code = zone.code', 'left');

			if(!empty($ds['code']))
			{
				$this->db->like('shop.code', $ds['code']);
			}

			if(!empty($ds['name']))
			{
				$this->db->like('shop.name', $ds['name']);
			}

			if(!empty($ds['zone']))
			{
				$this->db
				->group_start()
				->like('zone.code', $ds['zone'])
				->or_like('zone.name', $ds['zone'])
				->group_end();
			}

			if($ds['status'] !== 'all')
			{
				$this->db->where('shop.active', $ds['status']);
			}

			return $this->db->count_all_results();
		}

		return 0;
  }



  public function get_list(array $ds = array(), $perpage = 20, $offset = 0)
  {
		if(! empty($ds))
		{
			$this->db
			->select('shop.*, zone.name AS zone_name, customers.name AS customer_name')
			->from('shop')
      ->join('customers', 'shop.customer_code = customers.code', 'left')
			->join('zone', 'shop.zone_code = zone.code', 'left');

			if(!empty($ds['code']))
			{
				$this->db->like('shop.code', $ds['code']);
			}

			if(!empty($ds['name']))
			{
				$this->db->like('shop.name', $ds['name']);
			}

			if(!empty($ds['zone']))
			{
				$this->db
				->group_start()
				->like('zone.code', $ds['zone'])
				->or_like('zone.name', $ds['zone'])
				->group_end();
			}

			if($ds['status'] !== 'all')
			{
				$this->db->where('shop.active', $ds['status']);
			}

			$this->db->limit($perpage, $offset);

			$rs = $this->db->get();

			if($rs->num_rows() > 0)
			{
				return $rs->result();
			}
		}

		return NULL;

  }



	public function get_all()
	{
		$rs = $this->db->get('shop');
		if($rs->num_rows() > 0)
		{
			return $rs->result();
		}

		return NULL;
	}


} //--- end class

 ?>
