<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Pick_list extends PS_Controller
{
  public $menu_code = 'ICODPL';
	public $menu_group_code = 'IC';
  public $menu_sub_group_code = 'PICKPACK';
	public $title = 'Pick List';
  public $segment = 4;
  public $is_mobile = FALSE;

  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'inventory/pick_list';
    $this->load->model('inventory/pick_list_model');
    $this->load->model('orders/orders_model');
    $this->load->model('masters/warehouse_model');
    $this->load->model('masters/products_model');
    $this->load->model('masters/channels_model');
    $this->load->model('stock/stock_model');

    $this->load->helper('channels');
    $this->load->helper('warehouse');
    $this->load->helper('pick_list');
  }


  public function index()
  {
    $filter = array(
      'code' => get_filter('code', 'pl_code', ''),
      'warehouse' => get_filter('warehouse', 'pl_warehouse', 'all'),
      'channels' => get_filter('channels', 'pl_channels', 'all'),
      'status' => get_filter('status', 'pl_status', 'all'),
      'from_date' => get_filter('from_date', 'pl_from_date', ''),
      'to_date' => get_filter('to_date', 'pl_to_date', '')
    );

    if($this->input->post('search'))
    {
      redirect($this->home);
    }
    else
    {
  		$perpage = get_rows();
  		$rows = $this->pick_list_model->count_rows($filter, $this->is_mobile);
      $filter['list'] = $this->pick_list_model->get_list($filter, $perpage, $this->uri->segment($this->segment), $this->is_mobile);
  		$init = pagination_config($this->home.'/index/', $rows, $perpage, $this->segment);
      $this->pagination->initialize($init);

      $this->load->view('inventory/pick_list/pick_list', $filter);
    }
  }


  public function add_new()
  {
    if($this->pm->can_add)
    {
      $this->load->view('inventory/pick_list/pick_list_add');
    }
    else
    {
      $this->deny_page();
    }
  }


  public function add()
  {
    $sc = TRUE;

    if($this->pm->can_add)
    {
      $ds = json_decode($this->input->post('data'));

      if( ! empty($ds))
      {
        $date_add = db_date($ds->date_add, TRUE);
        $code = $this->get_new_code($date_add);

        $arr = array(
          'code' => $code,
          'date_add' => $date_add,
          'channels_code' => get_null($ds->channels_code),
          'warehouse_code' => $ds->warehouse_code,
          'user' => $this->_user->uname,
          'remark' => get_null($ds->remark)
        );

        if( ! $this->pick_list_model->add($arr))
        {
          $sc = FALSE;
          set_error('insert');
        }
      }
      else
      {
        $sc = FALSE;
        set_error('required');
      }
    }
    else
    {
      $sc = FALSE;
      set_error('permission');
    }

    $arr = array(
      'status' => $sc === TRUE ? 'success' : 'failed',
      'message' => $sc === TRUE ? 'success' : $this->error,
      'code' => $sc === TRUE ? $code : NULL
    );

    echo json_encode($arr);
  }


  public function edit($code)
  {
    $doc = $this->pick_list_model->get($code);

    if( ! empty($doc))
    {
      if($doc->status == 'P')
      {
        $ds = array(
          'doc' => $doc,
          'details' => $this->pick_list_model->get_details($code),
          'orders' => $this->pick_list_model->get_pick_orders($code),
          'rows' => $this->pick_list_model->get_pick_rows($code)
        );

        $this->load->view('inventory/pick_list/pick_list_edit', $ds);
      }
      else
      {
        redirect("{$this->home}/view_detail/{$code}");
      }
    }
    else
    {
      $this-page_error();
    }
  }


  public function update()
  {
    $sc = TRUE;

    if($this->pm->can_add OR $this->pm->can_edit)
    {
      $ds = json_decode($this->input->post('data'));

      if( ! empty($ds))
      {
        $date_add = db_date($ds->date_add, TRUE);

        $arr = array(
          'date_add' => $date_add,
          'channels_code' => get_null($ds->channels_code),
          'warehouse_code' => $ds->warehouse_code,
          'user' => $this->_user->uname,
          'remark' => get_null($ds->remark)
        );

        if( ! $this->pick_list_model->update($ds->code, $arr))
        {
          $sc = FALSE;
          set_error('insert');
        }
      }
      else
      {
        $sc = FALSE;
        set_error('required');
      }
    }
    else
    {
      $sc = FALSE;
      set_error('permission');
    }

    $arr = array(
      'status' => $sc === TRUE ? 'success' : 'failed',
      'message' => $sc === TRUE ? 'success' : $this->error,
      'code' => $sc === TRUE ? $ds->code : NULL
    );

    echo json_encode($arr);
  }


  public function cancel()
  {
    $sc = TRUE;

    if($this->pm->can_delete)
    {
      $code = $this->input->post('code');

      if( ! empty($code))
      {
        $doc = $this->pick_list_model->get($code);

        if( ! empty($doc))
        {
          if($doc->status != 'D')
          {
            $this->db->trans_begin();

            if( ! $this->pick_list_model->update_details($code, ['line_status' => 'D']))
            {
              $sc = FALSE;
              $this->error = "Failed to update details status";
            }

            if($sc === TRUE)
            {
              if( ! $this->pick_list_model->update($code, ['status' => 'D', 'update_user' => $this->_user->uname]))
              {
                $sc = FALSE;
                $this->error = "Failed to update document status";
              }
            }

            if($sc === TRUE)
            {
              if( ! $this->pick_list_model->remove_order_pick_list_id($doc->id))
              {
                $sc = FALSE;
                $this->error = "Failed to remove order pick list id";
              }
            }

            if($sc === TRUE)
            {
              $this->db->trans_commit();
            }
            else
            {
              $this->db->trans_rollback();
            }
          }
        }
        else
        {
          $sc = FALSE;
          set_error('notfound');
        }
      }
      else
      {
        $sc = FALSE;
        set_error('required');
      }
    }
    else
    {
      $sc = FALSE;
      set_error('permission');
    }

    $this->_response($sc);
  }


  public function view_detail($code)
  {
    $doc = $this->pick_list_model->get($code);

    if( ! empty($doc))
    {
      $rows = $this->pick_list_model->get_pick_rows($code);

      if( ! empty($rows))
      {
        foreach($rows as $rs)
        {
          $rs->stock = $this->stock_model->get_sell_stock($rs->product_code, $doc->warehouse_code);
        }
      }

      $ds = array(
        'doc' => $doc,
        'details' => $this->pick_list_model->get_details($code),
        'orders' => $this->pick_list_model->get_pick_orders($code),
        'rows' => $rows
      );

      $this->load->view('inventory/pick_list/pick_list_view_details', $ds);
    }
    else
    {
      $this-page_error();
    }
  }


  public function add_order_by_reference()
  {
    $sc = TRUE;

    $code = $this->input->post('code');
    $ref = $this->input->post('reference');
    $ds = [];

    if( ! empty($code) && ! empty($ref))
    {
      $doc = $this->pick_list_model->get($code);

      if( ! empty($doc))
      {
        if($doc->status == 'P')
        {
          $order = $this->orders_model->get_order_by_reference($ref);

          if(empty($order))
          {
            $order = $this->orders_model->get_order_by_tracking($ref);
          }

          if(empty($order))
          {
            $order = $this->orders_model->get($ref);
          }

          if( ! empty($order))
          {
            $res = [];

            $order_code = $order->code;

            if($this->pick_list_model->is_order_in_correct_state($order_code))
            {
              if( ! empty($doc->channels_code) && $order->channels_code != $doc->channels_code)
              {
                $sc = FALSE;
                $this->error = "ช่องทางขายไม่ตรงกับเอกสาร";
              }

              if($sc === TRUE)
              {
                $details = $this->pick_list_model->get_order_details($order_code);

                if( ! empty($details))
                {
                  foreach($details as $rs)
                  {
                    if($rs->is_count)
                    {
                      if( ! $this->pick_list_model->is_exists_order_detail($doc->id, $order_code, $rs->product_code))
                      {
                        $res[] = array(
                          'pick_id' => $doc->id,
                          'pick_code' => $doc->code,
                          'order_code' => $order_code,
                          'reference' => $order->reference,
                          'channels_code' => $order->channels_code,
                          'product_code' => $rs->product_code,
                          'product_name' => $rs->product_name,
                          'qty' => $rs->qty,
                          'user' => $this->_user->uname
                        );
                      }
                    }
                  }
                }
              }
            }
            else
            {
              $sc = FALSE;
              $this->error = "Invalid order status";
            }

            if($sc === TRUE)
            {
              if( ! empty($res))
              {
                $this->db->trans_begin();

                foreach($res as $row)
                {
                  if($sc === FALSE)
                  {
                    break;
                  }

                  if( ! $this->pick_list_model->add_detail($row))
                  {
                    $sc = FALSE;
                    $this->error = "Failed to add pick list detail";
                  }
                }

                if($sc === TRUE)
                {
                  $po = array(
                    'pick_id' => $doc->id,
                    'pick_code' => $doc->code,
                    'order_code' => $order_code,
                    'reference' => $order->reference,
                    'channels_code' => $order->channels_code
                  );

                  if( ! $this->pick_list_model->add_pick_order($po))
                  {
                    $sc = FALSE;
                    $this->error = "Failed to add pick list order";
                  }
                }

                if($sc === TRUE)
                {
                  $this->orders_model->update($order_code, ['pick_list_id' => $doc->id]);
                }

                if($sc === TRUE)
                {
                  $this->db->trans_commit();
                  $ds[] = array(
                    'order_code' => $order_code,
                    'reference' => $order->reference,
                    'channels' => channels_name($order->channels_code),
                    'ruid' => $order->id
                  );
                }
                else
                {
                  $this->db->trans_rollback();
                }
              }
              else
              {
                $sc = FALSE;
                $this->error = "ไม่พบรายการสินค้าในออเดอร์ {$ref}";
              }
            }
          }
          else
          {
            $sc = FALSE;
            $this->error = "ไม่พบออเดอร์ {$ref} ในระบบ";
          }
        }
        else
        {
          $sc = FALSE;
          set_error('status');
        }
      }
      else
      {
        $sc = FALSE;
        set_error('notfound');
      }
    }
    else
    {
      $sc = FALSE;
      set_error('required');
    }

    $arr = array(
      'status' =>  $sc === TRUE ? 'success' : 'failed',
      'message' => $sc === TRUE ? 'success' : $this->error,
      'data' => $sc === TRUE ? $ds : NULL
    );

    echo json_encode($arr);
  }


  public function add_to_pick_list()
  {
    $sc = TRUE;

    $ds = json_decode($this->input->post('data'));

    if( ! empty($ds))
    {
      $code = $ds->code;

      $doc = $this->pick_list_model->get($code);

      if( ! empty($doc))
      {
        if($doc->status == 'P')
        {
          if( ! empty($ds->orders))
          {
            $res = [];

            foreach($ds->orders as $od)
            {
              $order = $this->orders_model->get($od->code);

              if(($order->state == 3 OR $order->state == 4) && $order->is_cancled == 0)
              {
                $details = $this->pick_list_model->get_order_details($order->code);

                if( ! empty($details))
                {
                  $res[$order->code] = (object) array(
                    'pick_id' => $doc->id,
                    'pick_code' => $doc->code,
                    'code' => $order->code,
                    'reference' => get_null($order->reference),
                    'channels_code' => get_null($order->channels_code),
                    'rows' => []
                  );

                  foreach($details as $rs)
                  {
                    if( ! $this->pick_list_model->is_exists_order_detail($doc->id, $order->code, $rs->product_code))
                    {
                      $res[$order->code]->rows[] = array(
                        'pick_id' => $doc->id,
                        'pick_code' => $doc->code,
                        'order_code' => $order->code,
                        'reference' => get_null($order->reference),
                        'channels_code' => get_null($order->channels_code),
                        'product_code' => $rs->product_code,
                        'product_name' => $rs->product_name,
                        'qty' => $rs->qty,
                        'user' => $this->_user->uname
                      );
                    }
                  }
                }
              }
            }

            if( ! empty($res))
            {
              $this->db->trans_begin();

              foreach($res as $order)
              {
                if($sc === FALSE) { break; }

                if( ! empty($order->rows))
                {
                  foreach($order->rows as $row)
                  {
                    if( ! $this->pick_list_model->add_detail($row))
                    {
                      $sc = FALSE;
                      $this->error = "Failed to add pick list detail";
                    }
                  }

                  if($sc === TRUE)
                  {
                    $po = array(
                      'pick_id' => $doc->id,
                      'pick_code' => $doc->code,
                      'order_code' => $order->code,
                      'reference' => $order->reference,
                      'channels_code' => $order->channels_code
                    );

                    if( ! $this->pick_list_model->add_pick_order($po))
                    {
                      $sc = FALSE;
                      $this->error = "Failed to add pick list order";
                    }
                  }

                  if($sc === TRUE)
                  {
                    $this->orders_model->update($order->code, ['pick_list_id' => $doc->id]);
                  }
                }
              }

              if($sc === TRUE)
              {
                $this->db->trans_commit();
              }
              else
              {
                $this->db->trans_rollback();
              }
            }
          }
        }
        else
        {
          $sc = FALSE;
          set_error('status');
        }
      }
      else
      {
        $sc = FALSE;
        set_error('notfound');
      }
    }
    else
    {
      $sc = FALSE;
      set_error('required');
    }

    $arr = array(
      'status' =>  $sc === TRUE ? 'success' : 'failed',
      'message' => $sc === TRUE ? 'success' : $this->error
    );

    echo json_encode($arr);
  }


  public function get_details_table($code)
  {
    $sc = TRUE;
    $ds = [];

    $details = $this->pick_list_model->get_details($code);

    if( ! empty($details))
    {
      $no = 1;
      $totalQty = 0;

      $channels = [];

      foreach($details as $rs)
      {
        if(empty($channels[$rs->channels_code]))
        {
          $channels[$rs->channels_code] = channels_name($rs->channels_code);
        }

        $ds[] = array(
          'no' => $no,
          'order_code' => $rs->order_code,
          'reference' => $rs->reference,
          'channels' => empty($channels[$rs->channels_code]) ? NULL : $channels[$rs->channels_code],
          'product_code' => $rs->product_code,
          'product_name' => $rs->product_name,
          'qty' => number($rs->qty)
        );

        $no++;
        $totalQty += $rs->qty;
      }

      $ds[] = array('totalQty' => number($totalQty));
    }

    $arr = array(
      'status' => $sc === TRUE ? 'success' : 'failed',
      'message' => $sc === TRUE ? 'success' : $this->error,
      'data' => $ds
    );

    echo json_encode($arr);
  }


  public function get_order_list()
  {
    $sc = TRUE;
    $filter = json_decode($this->input->post('filter'));
    $res = [];

    if( ! empty($filter))
    {
      $ds = array(
        'code' => $filter->order_code,
        'channels' => $filter->channels,
        'customer' => $filter->customer,
        'warehouse_code' => $filter->warehouse_code,
        'from_date' => $filter->from_date,
        'to_date' => $filter->to_date,
        'is_pick_list' => $filter->is_pick_list,
        'limit' => $filter->limit
      );

      $orders = $this->pick_list_model->get_order_list($ds);

      if( ! empty($orders))
      {
        $no = 1;

        foreach($orders as $rs)
        {
          $res[] = (object) array(
            'no' => $no,
            'id' => $rs->id,
            'code' => $rs->code,
            'reference' => $rs->reference,
            'channels' => $rs->channels_name,
            'customer' => $rs->customer_name,
            'date_add' => thai_date($rs->date_add, FALSE),
            'pick_list_id' => $rs->pick_list_id
          );

          $no++;
        }
      }
    }

    $arr = array(
      'status' => $sc === TRUE ? 'success' : 'failed',
      'message' => $sc === TRUE ? 'success' : $this->error,
      'data' => $sc === TRUE ? $res : NULL
    );

    echo json_encode($arr);
  }


  public function delete_orders()
  {
    $sc = TRUE;

    $ds = json_decode($this->input->post('data'));

    if( ! empty($ds))
    {
      $code = $ds->code;

      $doc = $this->pick_list_model->get($code);

      if( ! empty($doc))
      {
        if($doc->status == 'P')
        {
          if( ! empty($ds->orders))
          {
            foreach($ds->orders as $order_code)
            {
              $so = TRUE;

              $this->db->trans_begin();
              //--- delete details
              if(! $this->pick_list_model->delete_order($code, $order_code))
              {
                $so = FALSE;
              }

              if($so === TRUE)
              {
                if( ! $this->pick_list_model->delete_detail_by_order($code, $order_code))
                {
                  $so = FALSE;
                }
              }

              if($so === TRUE)
              {
                if( ! $this->orders_model->update($order_code, ['pick_list_id' => NULL]))
                {
                  $so = FALSE;
                }
              }

              if($so === TRUE)
              {
                $this->db->trans_commit();
              }
              else
              {
                $this->db->trans_rollback();
              }
            }
          }
          else
          {
            $sc = FALSE;
            set_error('required');
          }
        }
        else
        {
          $sc = FALSE;
          set_error('status');
        }
      }
      else
      {
        $sc = FALSE;
        set_error('notfound');
      }
    }
    else
    {
      $sc = FALSE;
      set_error('required');
    }

    $this->_response($sc);
  }


  public function save()
  {
    $sc = TRUE;
    $code = $this->input->post('code');

    $doc = $this->pick_list_model->get($code);

    if( ! empty($doc))
    {
      if($doc->status == 'P')
      {
        $details = $this->pick_list_model->get_details($code);

        if( ! empty($details))
        {
          $rows = [];

          foreach($details as $rs)
          {
            $key = $rs->product_code;

            if(isset($rows[$key]))
            {
              $rows[$key]->qty += $rs->qty;
            }
            else
            {
              $rows[$key] = (object) array(
                'product_code' => $rs->product_code,
                'product_name' => $rs->product_name,
                'qty' => $rs->qty
              );
            }
          }
        }

        if( ! empty($rows))
        {
          $this->db->trans_begin();

          $arr = ['status' => 'C', 'update_user' => $this->_user->uname];

          if( ! $this->pick_list_model->update($code, $arr))
          {
            $sc = FALSE;
            $this->error = "Failed to update document status";
          }

          if($sc === TRUE)
          {
            foreach($rows as $row)
            {
              if($sc === FALSE) { break; }

              $arr = array(
                'pick_id' => $doc->id,
                'pick_code' => $doc->code,
                'product_code' => $row->product_code,
                'product_name' => $row->product_name,
                'qty' => $row->qty
              );

              if( ! $this->pick_list_model->add_row($arr))
              {
                $sc = FALSE;
                $this->error = "Failed to insert pick rows";
              }
            }
          }

          if($sc === TRUE)
          {
            $arr = array(
              'line_status' => 'C',
              'date_upd' => now()
            );

            if( ! $this->pick_list_model->update_details($doc->code, $arr))
            {
              $sc = FALSE;
              $this->error = "Failed to update line status";
            }
          }

          if($sc === TRUE)
          {
            $this->db->trans_commit();
          }
          else
          {
            $this->db->trans_rollback();
          }
        }
        else
        {
          $sc = FALSE;
          $this->error = "Cannot calculate summary pick items";
        }
      }
      else
      {
        $sc = FALSE;
        set_error('status');
      }
    }
    else
    {
      $sc = FALSE;
      set_error('notfound');
    }

    $this->_response($sc);
  }


  public function rollback($code)
  {
    $sc = TRUE;

    $doc = $this->pick_list_model->get($code);

    if( ! empty($doc))
    {
      if($doc->status == 'C')
      {
        if($sc === TRUE)
        {
          if( ! $this->pick_list_model->delete_rows($code))
          {
            $sc = FALSE;
            $this->error = "Cannot rollback status : Failed to delete summary rows";
          }
        }

        if($sc === TRUE)
        {
          $arr = ['status' => 'P', 'update_user' => $this->_user->uname];

          if( ! $this->pick_list_model->update($code, $arr))
          {
            $sc = FALSE;
            $this->error = "Failed to update document status";
          }
        }
      }
      else
      {
        if($doc->status != 'P')
        {
          $sc = FALSE;
          set_error('status');
        }
      }
    }
    else
    {
      $sc = FALSE;
      set_error('notfound');
    }

    $this->_response($sc);
  }


  public function print_pick_list($code)
  {
    $this->load->library('printer');
    $ds = array(
      'doc' => $this->pick_list_model->get($code),
      'details' => $this->pick_list_model->get_pick_rows($code)
    );

    $this->load->view('print/print_pick_list', $ds);
  }


  public function print_order_list($code)
  {
    $orders = [];
    $doc = $this->pick_list_model->get($code);
    $ps = $this->pick_list_model->get_pick_orders($code);

    if( ! empty($ps))
    {
      $i = 0;
      $j = 2;
      $rows = 0;

      foreach($ps as $rs)
      {
        $orders[$rows][] = $rs;

        if($i <= $j)
        {
          $i++;
        }

        if($i > $j)
        {
          $i = 0;
          $rows++;
        }
      }
    }

    $this->load->library('printer');

    $ds = array(
      'details' => $orders,
      'doc' => $doc
    );

    $this->load->view('print/print_pick_order_list', $ds);
  }


  public function get_new_code($date = NULL)
  {
    $date = empty($date) ? date('Y-m-d') : $date;
    $Y = date('y', strtotime($date));
    $M = date('m', strtotime($date));
    $prefix = getConfig('PREFIX_PICK_LIST');
    $run_digit = getConfig('RUN_DIGIT_PICK_LIST');
    $prefix = empty($prefix) ? 'PL' : $prefix;
    $run_digit = empty($run_digit) ? 5 : $run_digit;
    $pre = $prefix.'-'.$Y.$M;
    $code = $this->pick_list_model->get_max_code($pre);

    if( ! empty($code))
    {
      $run_no = mb_substr($code, ($run_digit * -1), NULL, 'UTF-8') + 1;
      $new_code = $pre . sprintf('%0'.$run_digit.'d', $run_no);
    }
    else
    {
      $new_code = $pre . sprintf('%0'.$run_digit.'d', '001');
    }

    return $new_code;
  }


  public function clear_filter()
  {
    $filter = array(
      'pl_code',
      'pl_warehouse',
      'pl_channels',
      'pl_status',
      'pl_from_date',
      'pl_to_date',
    );

    return clear_filter($filter);
  }


} //--- end class
?>
