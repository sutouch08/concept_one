<?php
class Pos_model extends CI_Model
{
  private $tb = "shop_pos";

  public function __construct()
  {
    parent::__construct();
  }

  //--- add new zone (use with sync only)
  public function add(array $ds = array())
  {
    if(!empty($ds))
    {
      return $this->db->insert($this->tb, $ds);
    }

    return FALSE;
  }

  //--- update zone with sync only
  public function update($code, $ds = array())
  {
    if(!empty($ds))
    {
      return $this->db->where('code', $code)->update($this->tb, $ds);
    }

    return FALSE;
  }


  public function update_by_id($id, $ds = array())
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


  public function get_code($id)
  {
    $rs = $this->db->where('id', $id)->get($this->tb);

    if($rs->num_rows() === 1)
    {
      return $rs->row()->code;
    }

    return NULL;
  }

	public function get_by_code($code = NULL)
	{
		if(! is_null($code))
		{
			$rs = $this->db
			->select('pos.*, shop.code AS shop_code, shop.name AS shop_name, shop.zone_code')
			->from('shop_pos AS pos')
			->join('shop', 'pos.shop_id = shop.id', 'left')
			->where('pos.code', $code)
			->get();

			if($rs->num_rows() === 1)
			{
				return $rs->row();
			}
		}

		return NULL;
	}


  public function get_all($active = NULL)
  {
    if( ! empty($active))
    {
      $this->db->where('active', 1)->get($this->tb);
    }

    $rs = $this->db->get($this->tb);

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


	public function get_shop_pos($shop_id)
	{
		$rs = $this->db->where('shop_id', $shop_id)->get($this->tb);

		if($rs->num_rows() > 0)
		{
			return $rs->result();
		}

		return NULL;
	}


	public function get_active_pos_list()
	{
		$this->db
		->select('pos.*')
		->select('shop.code AS shop_code, shop.name AS shop_name')
		->select('zone.name AS zone_name')
		->from('shop_pos AS pos')
		->join('shop', 'pos.shop_id = shop.id', 'left')
		->join('zone', 'shop.zone_code = zone.code', 'left')
		->where('shop.active', 1)
		->where('pos.active', 1)
    ->where('pos.deviceId IS NULL', NULL, FALSE)
		->order_by('shop.code', 'ASC');

		$rs = $this->db->get();

		if($rs->num_rows() > 0)
		{
			return $rs->result();
		}

		return NULL;
	}


	public function get_pos($id)
	{
    $this->db
		->select('pos.*')
		->select('shop.code AS shop_code, shop.name AS shop_name')
    ->select('shop.channels_code, shop.cash_payment, shop.transfer_payment, shop.card_payment')
    ->select('shop.bill_header_1, shop.bill_header_2, shop.bill_header_3, shop.font_size')
    ->select('shop.header_size_1, shop.header_size_2, shop.header_size_3')
    ->select('shop.header_align_1, shop.header_align_2, shop.header_align_3')
    ->select('shop.bill_footer, shop.footer_size, shop.tax_id, shop.use_vat, shop.active, shop.barcode')
    ->select('shop.zone_code, zone.name AS zone_name, zone.warehouse_code')
    ->select('pm.account_id')
    ->select('customers.code AS customer_code, customers.name AS customer_name')
		->from('shop_pos AS pos')
		->join('shop', 'pos.shop_id = shop.id', 'left')
    ->join('payment_method AS pm', 'shop.transfer_payment = pm.code', 'left')
		->join('zone', 'shop.zone_code = zone.code', 'left')
		->join('customers', 'shop.customer_code = customers.code', 'left')
		->where('pos.id', $id);

		$rs = $this->db->get();

		if($rs->num_rows() > 0)
		{
			return $rs->row();
		}

		return NULL;
	}


  public function get_pos_by_device_id($deviceId)
	{
		$this->db
		->select('pos.*')
		->select('shop.code AS shop_code, shop.name AS shop_name')
    ->select('shop.channels_code, shop.cash_payment, shop.transfer_payment, shop.card_payment')
		->select('shop.zone_code, shop.use_vat, zone.name AS zone_name, zone.warehouse_code')
    ->select('customers.code AS customer_code, customers.name AS customer_name')
		->from('shop_pos AS pos')
		->join('shop', 'pos.shop_id = shop.id', 'left')    
		->join('zone', 'shop.zone_code = zone.code', 'left')
		->join('customers', 'shop.customer_code = customers.code', 'left')
		->where('shop.active', 1)
		->where('pos.active', 1)
		->where('pos.deviceId', $deviceId);

		$rs = $this->db->get();

		if($rs->num_rows() > 0)
		{
			return $rs->row();
		}

		return NULL;
	}



  //---- delete zone  must use only mistake on sap and delete zone in SAP already
  public function delete($code)
  {
    return $this->db->where('code', $code)->delete($this->tb);
  }


	//---- checl transection
	public function has_transection($code)
	{
		//---- order


		return FALSE;
	}



  public function is_exists_code($code, $old_code = NULL)
  {
    $this->db->where('code', $code);

    if(! is_null($old_code))
    {
      $this->db->where('code !=', $old_code);
    }

    $rs = $this->db->count_all_results($this->tb);

    if($rs > 0)
    {
      return TRUE;
    }

    return FALSE;
  }



  public function is_exists_name($name, $code = NULL)
  {
    $this->db->where('name', $name);

    if(! is_null($code))
    {
      $this->db->where('code !=', $code);
    }

    $rs = $this->db->count_all_results($this->tb);

    if($rs > 0)
    {
      return TRUE;
    }

    return FALSE;
  }



	public function is_exists_prefix($prefix, $code = NULL)
	{
		$this->db->where('prefix', $prefix);

		if( ! is_null($code))
		{
			$this->db->where('code !=', $code);
		}

		$count = $this->db->count_all_results($this->tb);

		return $count > 0 ? TRUE : FALSE;
	}


  public function is_exists_return_prefix($prefix, $code = NULL)
	{
		$this->db->where('return_prefix', $prefix);

		if(! is_null($code))
		{
			$this->db->where('code !=', $code);
		}

		$count = $this->db->count_all_results($this->tb);

		return $count > 0 ? TRUE : FALSE;

	}



  public function count_rows(array $ds = array())
  {
  	if(! empty($ds))
		{
			$this->db
			->from('shop_pos AS pos')
			->join('shop', 'pos.shop_id = shop.id', 'left');

			if($ds['code'] !== '')
			{
				$this->db->like('pos.code', $ds['code']);
			}

			if($ds['name'] !== '')
			{
				$this->db->like('pos.name', $ds['name']);
			}

			if($ds['pos_no'] !== '')
			{
				$this->db->like('pos.pos_no', $ds['pos_no']);
			}

			if($ds['shop'] !== '')
			{
				$this->db
				->group_start()
				->like('shop.code', $ds['shop'])
				->or_like('shop.name', $ds['shop'])
				->group_end();
			}

			if($ds['status'] !== 'all')
			{
				$this->db->where('pos.active', $ds['status']);
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
			->select('pos.*, shop.code AS shop_code, shop.name AS shop_name')
			->from('shop_pos AS pos')
			->join('shop', 'pos.shop_id = shop.id', 'left');

			if($ds['code'] !== '')
			{
				$this->db->like('pos.code', $ds['code']);
			}

			if($ds['name'] !== '')
			{
				$this->db->like('pos.name', $ds['name']);
			}

			if($ds['pos_no'] !== '')
			{
				$this->db->like('pos.pos_no', $ds['pos_no']);
			}

			if($ds['shop'] !== '')
			{
				$this->db
				->group_start()
				->like('shop.code', $ds['shop'])
				->or_like('shop.name', $ds['shop'])
				->group_end();
			}

			if($ds['status'] !== 'all')
			{
				$this->db->where('pos.active', $ds['status']);
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


  public function get_shop_payment_method($shop_id)
  {
    $rs = $this->db
    ->select('ps.shop_id, pm.*')
    ->from('shop_payment_method AS ps')
    ->join('payment_method AS pm', 'ps.payment_id = pm.id', 'left')
    ->where('ps.shop_id', $shop_id)
    ->get();

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


} //--- end class

 ?>
