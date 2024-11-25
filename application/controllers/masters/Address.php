<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Address extends PS_Controller
{
  public $menu_code = 'DBADDR';
	public $menu_group_code = 'DB';
  public $menu_sub_group_code = 'CUSTOMER';
	public $title = 'ที่อยู่จัดส่ง';
  public $filter;
  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'masters/address';
    $this->load->model('address/address_model');
    $this->load->model('address/customer_address_model');
    $this->load->model('address/transport_model');
    $this->load->model('masters/customers_model');
  }


  public function get_online_address($customer_ref)
  {
    $rs = $this->address_model->get_default_address($customer_ref);
    if(!empty($rs))
    {
      echo $rs->id;
    }
    else
    {
      echo 'noaddress';
    }
  }



  public function print_online_address($id, $code)
  {
    $this->load->model('orders/orders_model');
    $this->load->model('inventory/invoice_model');
    $this->load->library('printer');
    $adr = $this->address_model->get_shipping_detail($id);
    $order = $this->orders_model->get($code);
    if(!empty($order))
    {
      $order->total_qty = $this->invoice_model->get_total_sold_qty($code);
    }

    $details = $order->state == 8 ? $this->invoice_model->get_details($code) : FALSE;

    if(!empty($adr))
    {
      $ds = array(
        'order' => $order,
        'details' => $details,
        'cusName' => $adr->name,
        'cusAdr1' => $adr->address,
        'cusAdr2' => ('ต.'.$adr->sub_district.' อ. '.$adr->district),
        'cusProv' => ('จ. '.$adr->province),
        'cusPostCode' => $adr->postcode,
        'cusPhone' => $adr->phone,
        'cusCode' => $code,
        'cName' => getConfig('COMPANY_FULL_NAME'),
        'cAddress' => getConfig('COMPANY_ADDRESS1').'<br>'.getConfig('COMPANY_ADDRESS2'),
        'cPhone' => getConfig('COMPANY_PHONE'),
        'cPostCode' => getConfig("COMPANY_POST_CODE")
      );

      $this->load->view('print/print_address_online_sheet', $ds);
    }
  }



  public function print_address_sheet($code, $customer_code, $id_address = '', $id_sender = '')
  {
    $this->load->library('printer');
    $this->load->model('inventory/qc_model');
    $id_address = empty($id_address) ? $this->address_model->get_id($customer_code) : $id_address;
    $id_sender = empty($id_sender) ? $this->transport_model->get_id($customer_code) : $id_sender;
    $ds = array(
      'reference' => $code,
      'boxes' => $this->qc_model->count_box($code),
      'ad' => $this->address_model->get_shipping_detail($id_address),
      'sd' => $this->transport_model->get_sender($id_sender),
      'cName' => getConfig('COMPANY_FULL_NAME'),
      'cAddress' => getConfig('COMPANY_ADDRESS1').'<br>'.getConfig('COMPANY_ADDRESS2'),
      'cPostCode' => getConfig('COMPANY_POST_CODE'),
      'cPhone' => getConfig('COMPANY_PHONE')
    );

    $this->load->view('print/print_address_sheet', $ds);
  }


  public function get_address_form()
  {
    $this->load->helper('address');
    $customer_code = $this->input->post('customer_code');
    if($customer_code)
    {
      //--- จำนวนที่อยู่
      $adn = $this->address_model->count_address($customer_code);
      //--- จำนวนผู้จัดส่ง
      $sdn = $this->transport_model->count_sender($customer_code);
      //--- ที่อยู่ลูกค้าทั้งหมด
      $adrs = $adn > 0 ? $this->address_model->get_shipping_address($customer_code) : FALSE;
      //--- รายชื่อผู้ให้บริการจัดส่ง
      $senders = $sdn > 0 ? $this->transport_model->get_senders($customer_code) : FALSE;

      if(!empty($senders))
      {
        $senders->main = $this->transport_model->get_name($senders->main_sender);
        $senders->second = $this->transport_model->get_name($senders->second_sender);
        $senders->third = $this->transport_model->get_name($senders->third_sender);
      }

      echo get_address_form($adn, $sdn, $adrs, $senders);
    }
    else
    {
      echo 'noaddress';
    }

  }


  public function get_customer_bill_to_address()
  {
    $sc = TRUE;
    $code = $this->input->get('code');

    if( ! empty($code))
    {
      $customer = $this->customers_model->get($code);

      if( ! empty($customer))
      {
        $addr = $this->customer_address_model->get_bill_to_address($customer->code);

        if( ! empty($addr))
        {
          $no = 1;
          
          foreach($addr AS $adr)
          {
            $adr->no = $no;
            $adr->name = $customer->name;
            $no++;
          }
        }
        else
        {
          $sc = FALSE;
          $this->error = "No address found";
        }
      }
      else
      {
        $sc = FALSE;
        $this->error = "Invalid customer code";
      }
    }
    else
    {
      $sc = FALSE;
      $this->error = "Missing required parameter";
    }

    $arr = array(
      'status' => $sc === TRUE ? 'success' : 'failed',
      'message' => $sc === TRUE ? 'success' : $this->error,
      'address' => $sc === TRUE ? $addr : NULL
    );

    echo json_encode($arr);
  }


} //--- end class

?>
