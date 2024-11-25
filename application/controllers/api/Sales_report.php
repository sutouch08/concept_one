<?php
class Sales_report extends CI_Controller
{
  private $host = "https://api-report.warrix.co.th";
  private $endpoint = "/api/v1/sales-report";
  private $url = "";
  private $app_id = "MkgwaFZtZjBJSTRDclNncWlXd0poQ1ZEZnA1SVJvZjY=";
  private $app_secret = "Q213dGdRSUdmNFVWQWdyWg==";
  public $home;

  public function __construct()
  {
    parent::__construct();
    $this->url = $this->host.$this->endpoint;
    $this->home = base_url().'api/sales_report';
  }

  public function index()
  {
    $this->load->view('auto/sales_report_api');
  }


	public function resend()
	{
		$this->load->view('auto/resend_error');
	}


  public function get_query()
  {
		$code = 'WO-xxxxxx';
    $this->db
    ->select('so.*')
    ->select('cn.name AS channels')
    ->select('pm.name AS payment')
    ->select('co.code AS color')
    ->select('size.name AS size')
    ->select('pg.name AS product_group')
    ->select('pc.name AS product_category')
    ->select('pk.name AS product_kind')
    ->select('pt.name AS product_type')
    ->select('pb.name AS brand')
    ->select('pd.year')
    ->select('c.name AS customer_name')
    ->select('cg.name AS customer_group')
    ->select('ck.name AS customer_kind')
    ->select('cc.name AS customer_class')
    ->select('ca.name AS customer_area')
    ->select('sale.name AS sale_name')
    ->select('user.name AS employee_name')
    ->from('order_sold AS so')
    ->join('channels AS cn', 'so.channels_code = cn.code', 'left')
    ->join('payment_method AS pm', 'so.payment_code = pm.code', 'left')
    ->join('products AS pd', 'so.product_code = pd.code', 'left')
    ->join('product_color AS co', 'pd.color_code = co.code', 'left')
    ->join('product_size AS size', 'pd.size_code = size.code', 'left')
    ->join('product_group AS pg', 'pd.group_code = pg.code', 'left')
    ->join('product_category AS pc', 'pd.category_code = pc.code', 'left')
    ->join('product_kind AS pk', 'pd.kind_code = pk.code', 'left')
    ->join('product_type AS pt', 'pd.type_code = pt.code', 'left')
    ->join('product_brand AS pb', 'pd.brand_code = pb.code', 'left')
    ->join('customers AS c', 'so.customer_code = c.code', 'left')
    ->join('customer_group AS cg', 'c.group_code = cg.code', 'left')
    ->join('customer_kind AS ck', 'c.kind_code = ck.code', 'left')
    ->join('customer_class AS cc', 'c.class_code = cc.code', 'left')
    ->join('customer_area AS ca', 'c.area_code = ca.code', 'left')
    ->join('saleman AS sale', 'c.sale_code = sale.id', 'left')
    ->join('user', 'so.user = user.uname', 'left')
    ->where('reference', $code);
    echo $this->db->get_compiled_select();
  }


	public function get_json_data()
  {
    $this->load->model('api/sale_report_model');

    $limit = 100; //--- limit rows
    $role = 'S'; //--- only sale order
    $role_name = array(
      'S' => 'ขาย',
      'C' => 'ฝากขาย(SO)',
      'N' => 'ฝากขาย(TR)',
      'P' => 'สปอนเซอร์',
      'M' => 'ตัดยอดฝากขาย',
      'U' => 'อภินันท์'
    );

    $ds = array();
    $result = array();

    $data = $this->sale_report_model->get_orders($role, $limit);

    if(!empty($data))
    {
      foreach($data as $rs)
      {
        if($rs->status == 2 OR $rs->state == 9)
        {
          $arr = array(
            'reference' => $rs->code
          );

          array_push($ds, $arr);
        }
        else
        {
          $orders = $this->sale_report_model->get_sold_data($rs->code);

          if(!empty($orders))
          {
            foreach($orders as $order)
            {
              $arr = array(
                'reference' => $order->reference,
                'role_name' => $role_name[$order->role],
                'payment' => $order->payment,
                'channels' => $order->channels,
                'product_code' => $order->product_code,
                'product_name' => $order->product_name,
                'color' => $order->color,
                'size' => $order->size,
                'product_style' => $order->product_style,
                'product_group' => $order->product_group,
                'product_category' => $order->product_category,
                'product_kind' => $order->product_kind,
                'product_type' => $order->product_type,
                'brand' => $order->brand,
                'year' => $order->year,
                'price_ex' => remove_vat($order->price),
                'price_inc' => floatval($order->price),
                'sell_ex' => remove_vat($order->sell),
                'sell_inc' => floatval($order->sell),
                'qty' => floatval($order->qty),
                'discount_amount' => floatval($order->discount_amount),
                'total_amount_ex' => remove_vat($order->total_amount),
                'total_cost_ex' => remove_vat($order->total_cost),
                'margin_ex' => remove_vat($order->margin),
                'customer_name' => $order->customer_name,
                'customer_group' => $order->customer_group,
                'customer_kind' => $order->customer_kind,
                'customer_class' => $order->customer_class,
                'customer_area' => $order->customer_area,
                'sale_code' => $order->sale_code,
                'sale_name' => $order->sale_name,
                'employee_nam' => $order->employee_name,
                'date_add' => date('d/m/Y H:i:s', strtotime($order->date_add)),
                'date_upd' => date('d/m/Y H:i:s'),
                'id_zone' => $order->zone_code,
                'id_warehouse' => $order->warehouse_code
              );
              array_push($ds, $arr);
            } //--- end foreach
          } //--- end if
        } //-- end if

        //$this->sale_report_model->set_report_status($rs->code, 1);

      } //--- end foreach

      // $setHeaders = array(
      //   "Content-Type:application/json",
      //   "applicationID:{$this->app_id}",
      //   "applicationSecret:{$this->app_secret}"
      // );
			//
      // $apiUrl = str_replace(" ","%20",$this->url);
      // $method = 'POST';
      $data_set = array('data'=> $ds);
      $data_string = json_encode($data_set);

			echo $data_string;
			//
      // //echo $data_string;
			//
      // $ch = curl_init();
			//
			//
      // curl_setopt($ch, CURLOPT_URL, $apiUrl);
      // curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
      // curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
      // curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      // curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
      // curl_setopt($ch, CURLOPT_HTTPHEADER, $setHeaders);
      // $response = curl_exec($ch);
			//
      // curl_close($ch);
			//
			//
      // $res = json_decode($response);
			//
      // if($res->status->code != '0000')
      // {
      //   foreach($data as $rs)
      //   {
      //     $this->sale_report_model->set_report_status($rs->code, 3, $res->data->error);
      //   }
      // }

      //echo $response;
    }
  }


  public function do_export()
  {
    $this->load->model('api/sale_report_model');

    $limit = 100; //--- limit rows
    $role = 'S'; //--- only sale order
    $role_name = array(
      'S' => 'ขาย',
      'C' => 'ฝากขาย(SO)',
      'N' => 'ฝากขาย(TR)',
      'P' => 'สปอนเซอร์',
      'M' => 'ตัดยอดฝากขาย',
      'U' => 'อภินันท์'
    );

    $ds = array();
    $result = array();

    $data = $this->sale_report_model->get_orders($role, $limit);

    if(!empty($data))
    {
      foreach($data as $rs)
      {
        if($rs->status == 2 OR $rs->state == 9)
        {
          $arr = array(
            'reference' => $rs->code
          );

          array_push($ds, $arr);
        }
        else
        {
          $orders = $this->sale_report_model->get_sold_data($rs->code);

          if(!empty($orders))
          {
            foreach($orders as $order)
            {
              $arr = array(
                'reference' => $order->reference,
                'role_name' => $role_name[$order->role],
                'payment' => $order->payment,
                'channels' => $order->channels,
                'product_code' => $order->product_code,
                'product_name' => $order->product_name,
                'color' => $order->color,
                'size' => $order->size,
                'product_style' => $order->product_style,
                'product_group' => $order->product_group,
                'product_category' => $order->product_category,
                'product_kind' => $order->product_kind,
                'product_type' => $order->product_type,
                'brand' => $order->brand,
                'year' => $order->year,
                'price_ex' => remove_vat($order->price),
                'price_inc' => floatval($order->price),
                'sell_ex' => remove_vat($order->sell),
                'sell_inc' => floatval($order->sell),
                'qty' => floatval($order->qty),
                'discount_amount' => floatval($order->discount_amount),
                'total_amount_ex' => remove_vat($order->total_amount),
                'total_cost_ex' => remove_vat($order->total_cost),
                'margin_ex' => remove_vat($order->margin),
                'customer_name' => $order->customer_name,
                'customer_group' => $order->customer_group,
                'customer_kind' => $order->customer_kind,
                'customer_class' => $order->customer_class,
                'customer_area' => $order->customer_area,
                'sale_code' => $order->sale_code,
                'sale_name' => $order->sale_name,
                'employee_nam' => $order->employee_name,
                'date_add' => date('d/m/Y H:i:s', strtotime($order->date_add)),
                'date_upd' => date('d/m/Y H:i:s'),
                'id_zone' => $order->zone_code,
                'id_warehouse' => $order->warehouse_code
              );
              array_push($ds, $arr);
            } //--- end foreach
          } //--- end if
        } //-- end if

        $this->sale_report_model->set_report_status($rs->code, 1);

      } //--- end foreach

      $setHeaders = array(
        "Content-Type:application/json",
        "applicationID:{$this->app_id}",
        "applicationSecret:{$this->app_secret}"
      );

      $apiUrl = str_replace(" ","%20",$this->url);
      $method = 'POST';
      $data_set = array('data'=> $ds);
      $data_string = json_encode($data_set);

      //echo $data_string;

      $ch = curl_init();


      curl_setopt($ch, CURLOPT_URL, $apiUrl);
      curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
      curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
      curl_setopt($ch, CURLOPT_HTTPHEADER, $setHeaders);
      $response = curl_exec($ch);

      curl_close($ch);


      $res = json_decode($response);

      if($res->status->code != '0000')
      {
        foreach($data as $rs)
        {
          $this->sale_report_model->set_report_status($rs->code, 3, $res->data->error);
        }
      }

      //echo $response;
    }
  }



  public function export_error()
  {
    $this->load->model('api/sale_report_model');

    $limit = 100; //--- limit rows
    $role = 'S'; //--- only sale order
    $role_name = array(
      'S' => 'ขาย',
      'C' => 'ฝากขาย(SO)',
      'N' => 'ฝากขาย(TR)',
      'P' => 'สปอนเซอร์',
      'M' => 'ตัดยอดฝากขาย',
      'U' => 'อภินันท์'
    );

    $ds = array();
    $result = array();

    $data = $this->sale_report_model->get_error_orders($role, $limit);

    if(!empty($data))
    {
      foreach($data as $rs)
      {
        if($rs->status == 2 OR $rs->state == 9)
        {
          $arr = array(
            'reference' => $rs->code
          );

          array_push($ds, $arr);
        }
        else
        {
          $orders = $this->sale_report_model->get_sold_data($rs->code);

          if(!empty($orders))
          {
            foreach($orders as $order)
            {
              $arr = array(
                'reference' => $order->reference,
                'role_name' => $role_name[$order->role],
                'payment' => $order->payment,
                'channels' => $order->channels,
                'product_code' => $order->product_code,
                'product_name' => $order->product_name,
                'color' => $order->color,
                'size' => $order->size,
                'product_style' => $order->product_style,
                'product_group' => $order->product_group,
                'product_category' => $order->product_category,
                'product_kind' => $order->product_kind,
                'product_type' => $order->product_type,
                'brand' => $order->brand,
                'year' => $order->year,
                'price_ex' => remove_vat($order->price),
                'price_inc' => floatval($order->price),
                'sell_ex' => remove_vat($order->sell),
                'sell_inc' => floatval($order->sell),
                'qty' => floatval($order->qty),
                'discount_amount' => floatval($order->discount_amount),
                'total_amount_ex' => remove_vat($order->total_amount),
                'total_cost_ex' => remove_vat($order->total_cost),
                'margin_ex' => remove_vat($order->margin),
                'customer_name' => $order->customer_name,
                'customer_group' => $order->customer_group,
                'customer_kind' => $order->customer_kind,
                'customer_class' => $order->customer_class,
                'customer_area' => $order->customer_area,
                'sale_code' => $order->sale_code,
                'sale_name' => $order->sale_name,
                'employee_nam' => $order->employee_name,
								'date_add' => date('d/m/Y H:i:s', strtotime($order->date_add)),
                'date_upd' => date('d/m/Y H:i:s'),
                'id_zone' => $order->zone_code,
                'id_warehouse' => $order->warehouse_code
              );
              array_push($ds, $arr);
            } //--- end foreach
          } //--- end if
        } //-- end if

        $this->sale_report_model->set_report_status($rs->code, 1);

      } //--- end foreach

      $setHeaders = array(
        "Content-Type:application/json",
        "applicationID:{$this->app_id}",
        "applicationSecret:{$this->app_secret}"
      );

      $apiUrl = str_replace(" ","%20",$this->url);
      $method = 'POST';
      $data_set = array('data'=> $ds);
      $data_string = json_encode($data_set);

      //echo $data_string;

      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, $apiUrl);
      curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
      curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
      curl_setopt($ch, CURLOPT_HTTPHEADER, $setHeaders);
      $response = curl_exec($ch);

      curl_close($ch);


      $res = json_decode($response);

      if(empty($res) OR $res->status->code != '0000')
      {
        foreach($data as $rs)
        {
          if(empty($res->data->error))
          {
            $error_message = $response;
          }
          else
          {
            $error_message = $res->data->error;
          }

          $this->sale_report_model->set_report_status($rs->code, 3, $error_message);
        }
      }

      //echo $response;
    }
  }




  public function test()
  {
    $res = '{
      "status": {"code": "0000","namespace": "SAP"},
      "data": {"success": true}
      }';
    $rs = json_decode($res);
    if($rs->status->code != "0000")
    {
      echo "Error";
    }
    else
    {
      echo "OK";
    }

  }


}
?>
