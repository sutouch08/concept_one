<?php
class Auto_send_delivery extends PS_Controller
{
  public $title = 'รายการที่รอส่งเข้า SAP';
	public $menu_code = '';
	public $menu_group_code = '';
	public $error;

  public function __construct()
  {
    parent::__construct();
    _check_login();
		$this->pm = new stdClass();
		$this->pm->can_view = 1;
    $this->home = base_url().'auto/auto_send_delivery';
		$this->load->library('export');
  }

  public function index()
  {
    $ds = ['orders' => $this->get_list()];

    $this->load->view('auto/order_list_to_send', $ds);
  }

  public function process()
  {
    $orders = $this->get_order_list();

    if( ! empty($orders))
    {
      foreach($orders as $rs)
      {
        if( ! $this->export->export_order($rs->code))
				{
					$arr = array(
						'status' => 3,
						'message' => $this->export->error
					);

					$this->update_status($rs->id, $arr);
				}
				else
				{
					$arr = array(
						'status' => 1,
            'message' => NULL
					);

					$this->update_status($rs->id, $arr);
				}
      }
    }

    echo 'success';
  }


	private function update_status($id, array $ds = array())
	{
		return $this->db->where('id', $id)->update('auto_send_to_sap_order', $ds);
	}

  public function get_list()
  {
    $rs = $this->db->where_in('status', array(0, 3))->get('auto_send_to_sap_order');

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function count_rows()
  {
    return $this->db->where_in('status', array(0, 3))->count_all_results('auto_send_to_sap_order');
  }


  public function get_order_list()
  {
    $rs  = $this->db->where('status', 0)->limit(100)->get('auto_send_to_sap_order');

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }

} //--- end class
 ?>
