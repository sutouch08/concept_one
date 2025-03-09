<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Sync_data extends CI_Controller
{
  public $title = 'Sync Data';
	public $menu_code = '';
	public $menu_group_code = '';
	public $pm;
  public $limit = 100;
  public $date;

  public function __construct()
  {
    parent::__construct();
    $this->ms = $this->load->database('ms', TRUE); //--- SAP database
    $this->mc = $this->load->database('mc', TRUE); //--- Temp Database
		// $this->cn = $this->load->database('cn', TRUE);
    $this->load->model('sync_data_model');
    $this->date = date('Y-d-m H:i:s');
  }


  public function index()
  {
    set_time_limit(300);

		$this->syncWarehouse();

		$this->syncZone();

		$this->syncCustomer();

    $this->syncSaleman();

    $this->syncIncommingCode();

    $this->syncDownPaymentCode();

    $this->syncOrderInvCode();

    $this->syncInvoiceCode();

		$this->syncReceiveTransformInvCode();

    $this->syncReceivePoInvCode();

		// $this->syncSponsorInvCode();

		// $this->syncConsignmentInvCode();

		$this->syncOrderTransformInvCode();

		$this->syncOrderTransferInvCode();

		$this->syncOrderLendInvCode();

		$this->syncTransferInvCode();

    $this->syncReturnLendInvCode();

		$this->syncMoveInvCode();

		$this->syncTransformGoodsIssueCode();

		$this->syncAdjustGoodsIssueCode();

		$this->syncAdjustGoodsReceiveCode();

		$this->syncReturnOrderCode();

		$this->syncConsignSoldInvCode();

		// $this->syncConsignmentSoldInvCode();

		// $this->syncReturnConsignmentOrderCode();

  }


  public function clear_old_logs()
  {
		$days = 7;
    $this->sync_data_model->clear_old_logs($days);
  }



  public function syncWarehouse()
  {
    $this->load->model('masters/warehouse_model');
    $last_sync = from_date($this->warehouse_model->get_last_sync_date());
    $newData = $this->warehouse_model->get_new_data($last_sync);

		$count = 0;
		$update = 0;

    if(!empty($newData))
    {
      foreach($newData as $rs)
      {
				$count++;

        if($this->warehouse_model->is_exists($rs->code))
        {
          $ds = array(
            'name' => $rs->name,
            'active' => $rs->Inactive == 'Y' ? 0 : 1,
            'last_sync' => date('Y-m-d H:i:s'),
            'update_user' => 'SAP',
            'old_code' => $rs->old_code,
            'limit_amount' => 0//$rs->limit_amount
          );

          $this->warehouse_model->update($rs->code, $ds);
					$update++;
        }
        else
        {
          $ds = array(
            'code' => $rs->code,
            'name' => $rs->name,
            'active' => $rs->Inactive == 'Y' ? 0 : 1,
            'last_sync' => date('Y-m-d H:i:s'),
            'update_user' => 'SAP',
            'old_code' => $rs->old_code,
            'limit_amount' => 0//$rs->limit_amount
          );

          $this->warehouse_model->add($ds);
					$update++;
        }
      }
    }

		$logs = array(
      'sync_item' => 'WAREHOUSE',
      'get_item' => $count,
      'update_item' => $update
    );

    //--- add logs
    $this->sync_data_model->add_logs($logs);

    //echo 'done';
  }


  public function syncZone()
  {
    $this->load->model('masters/zone_model');
    $last_sync = from_date($this->zone_model->get_last_sync_date());
    $newData = $this->zone_model->get_new_data($last_sync);

		$count = 0;
		$update = 0;

    if(!empty($newData))
    {
      foreach($newData as $rs)
      {
				$count++;

        if($this->zone_model->is_exists_id($rs->id))
        {
          $ds = array(
            'code' => $rs->code,
            'name' => empty($rs->name) ? $rs->code : $rs->name,
						'warehouse_code' => $rs->warehouse_code,
            'active' => $rs->Disabled == 'N' ? 1 : 0,
            'old_code' => $rs->old_code,
            'last_sync' => date('Y-m-d H:i:s'),
          );

          $this->zone_model->update($rs->id, $ds);
					$update++;
        }
        else
        {
          $ds = array(
            'id' => $rs->id,
            'code' => $rs->code,
            'name' => empty($rs->name) ? $rs->code : $rs->name,
            'warehouse_code' => $rs->warehouse_code,
            'active' => $rs->Disabled == 'N' ? 1 : 0,
            'last_sync' => date('Y-m-d H:i:s'),
            'old_code' => $rs->old_code
          );

          $this->zone_model->add($ds);
					$update++;
        }
      }
    }

		$logs = array(
      'sync_item' => 'ZONE',
      'get_item' => $count,
      'update_item' => $update
    );

    //--- add logs
    $this->sync_data_model->add_logs($logs);

  }


  public function syncCustomer()
  {
    $this->load->model('masters/customers_model');
    $last_sync = from_date($this->customers_model->get_last_sync_date());
    $ds = $this->customers_model->get_update_data($last_sync);
		$count = 0;
		$update = 0;

    if(!empty($ds))
    {
      foreach($ds as $rs)
      {
				$count++;
        $arr = array(
          'code' => $rs->code,
          'name' => $rs->name,
          'Tax_Id' => $rs->Tax_Id,
          'DebPayAcct' => $rs->DebPayAcct,
          'CardType' => $rs->CardType,
          'GroupCode' => $rs->GroupCode,
          'cmpPrivate' => $rs->CmpPrivate,
          'GroupNum' => $rs->GroupNum,
          'sale_code' => $rs->sale_code,
          'CreditLine' => get_zero($rs->CreditLine),
          'old_code' => $rs->old_code,
          'active' => ($rs->validFor == 'Y' ? 1 : 0),
          'last_sync' => now()
        );

        if($this->customers_model->is_exists($rs->code) === TRUE)
        {
          $this->customers_model->update($rs->code, $arr);
					$update++;
        }
        else
        {
          $this->customers_model->add($arr);
					$update++;
        }
      }
    }

		$logs = array(
      'sync_item' => 'CUSTOMER',
      'get_item' => $count,
      'update_item' => $update
    );

    //--- add logs
    $this->sync_data_model->add_logs($logs);

  }


  public function syncSaleman()
  {
    $this->load->model('masters/slp_model');

    $ds = $this->slp_model->get_all_slp();
		$count = 0;
		$update = 0;

    if(!empty($ds))
    {
      foreach($ds as $rs)
      {
				$count++;

        if( ! $this->slp_model->is_exists($rs->id))
        {
          $arr = array(
            'id' => $rs->id,
            'name' => $rs->name,
            'active' => ($rs->active == 'Y' ? 1 : 0)
          );

          $this->slp_model->add($arr);
          $update++;
        }
        else
        {
          $arr = array(
						'name' => $rs->name,
						'active' => $rs->active == 'Y' ? 1 : 0
					);

					$this->slp_model->update($rs->id, $arr);
          $update++;
        }
      }
    }

		$logs = array(
      'sync_item' => 'SALEMAN',
      'get_item' => $count,
      'update_item' => $update
    );

    //--- add logs
    $this->sync_data_model->add_logs($logs);

  }


  public function syncReceivePoInvCode()
  {
    $this->load->model('inventory/receive_po_model');
    $ds = $this->receive_po_model->get_non_inv_code($this->limit);
    $count = 0;
    $update = 0;

    if(!empty($ds))
    {
      foreach($ds as $rs)
      {
        $count++;
        $inv = $this->receive_po_model->get_sap_doc_num($rs->code);

        if(!empty($inv))
        {
          $this->receive_po_model->update_inv($rs->code, $inv);
          $update++;
        }

      }
    }


    $logs = array(
      'sync_item' => 'WR',
      'get_item' => $count,
      'update_item' => $update
    );

    //--- add logs
    $this->sync_data_model->add_logs($logs);

  }


  public function syncReceiveTransformInvCode()
  {
    $this->load->model('inventory/receive_transform_model');
    $ds = $this->receive_transform_model->get_non_inv_code($this->limit);
    $count = 0;
    $update = 0;

    if(!empty($ds))
    {
      foreach($ds as $rs)
      {
        $count++;
        $inv = $this->receive_transform_model->get_sap_doc_num($rs->code);

        if(!empty($inv))
        {
          $this->receive_transform_model->update_inv($rs->code, $inv);
          $update++;
        }

      }
    }


    $logs = array(
      'sync_item' => 'RT',
      'get_item' => $count,
      'update_item' => $update
    );

    //--- add logs
    $this->sync_data_model->add_logs($logs);

  }


  //---- sync incomming ORCT
  public function syncIncommingCode()
  {
    $this->load->model('orders/order_down_payment_model');

    $ds = $this->order_down_payment_model->get_sync_list($this->limit);

    $count = 0;
    $update = 0;

    if( ! empty($ds))
    {
      foreach($ds as $rs)
      {
        $count++;

        $DocNum = $this->order_down_payment_model->get_sap_doc_num($rs->code);

        if( ! empty($DocNum))
        {
          $arr = array(
            'DocNum' => $DocNum
          );

          $this->order_down_payment_model->update_by_code($rs->code, $arr);

          $update++;
        }
      }
    }


    $logs = array(
      'sync_item' => 'INC',
      'get_item' => $count,
      'update_item' => $update
    );

    //--- add logs
    $this->sync_data_model->add_logs($logs);

  }


  //---- sync ODPI
  public function syncDownPaymentCode()
  {
    $this->load->model('orders/down_payment_invoice_model');

    $ds = $this->down_payment_invoice_model->get_sync_list($this->limit);

    $count = 0;
    $update = 0;

    if( ! empty($ds))
    {
      foreach($ds as $rs)
      {
        $count++;

        $Dpm = $this->down_payment_invoice_model->get_sap_doc($rs->code);

        if( ! empty($Dpm))
        {
          $arr = array(
            'DocEntry' => $Dpm->DocEntry,
            'DocNum' => $Dpm->DocNum
          );

          $this->down_payment_invoice_model->update_by_code($rs->code, $arr);

          $update++;
        }
      }
    }


    $logs = array(
      'sync_item' => 'DPM',
      'get_item' => $count,
      'update_item' => $update
    );

    //--- add logs
    $this->sync_data_model->add_logs($logs);

  }


  //---- order pos invoice
  public function syncInvoiceCode()
  {
    $this->load->model('orders/order_invoice_model');
    $ds = $this->order_invoice_model->get_order_non_inv_code($this->limit);
    $count = 0;
    $update = 0;

    if( ! empty($ds))
    {
      foreach($ds as $rs)
      {
        $count++;

        $inv = $this->order_invoice_model->get_sap_doc_num($rs->code);

        if( ! empty($inv))
        {
          $arr = array(
            'status' => 'C',
            'DocNum' => $inv,
            'last_sync' => now()
          );

          if($this->order_invoice_model->update($rs->code, $arr))
          {
            $this->order_invoice_model->update_details($rs->code, array('LineStatus' => 'C'));
          }

          $update++;
        }
        else
        {
          $arr = array(
            'last_sync' => now()
          );

          $this->order_invoice_model->update($rs->code, $arr);
        }
      }
    }


    $logs = array(
      'sync_item' => 'INV',
      'get_item' => $count,
      'update_item' => $update
    );

    //--- add logs
    $this->sync_data_model->add_logs($logs);

  }


  public function syncOrderInvCode()
  {
    $this->load->model('orders/orders_model');
    $ds = $this->orders_model->get_order_non_inv_code($this->limit);
    $count = 0;
    $update = 0;

    if(!empty($ds))
    {
      foreach($ds as $rs)
      {
        $count++;
        $inv = $this->orders_model->get_sap_doc_num($rs->code);
        if(!empty($inv))
        {
          if($this->orders_model->update_inv($rs->code, $inv))
          {
            $this->orders_model->set_complete($rs->code);
          }
          $update++;
        }
      }
    }


    $logs = array(
      'sync_item' => 'WO',
      'get_item' => $count,
      'update_item' => $update
    );

    //--- add logs
    $this->sync_data_model->add_logs($logs);

  }


  public function syncSponsorInvCode()
  {
    $this->load->model('orders/orders_model');
    $ds = $this->orders_model->get_sponsor_non_inv_code($this->limit);
    $count = 0;
    $update = 0;

    if(!empty($ds))
    {
      foreach($ds as $rs)
      {
        $count++;
        $inv = $this->orders_model->get_sap_doc_num($rs->code);
        if(!empty($inv))
        {
          if($this->orders_model->update_inv($rs->code, $inv))
          {
            $this->orders_model->set_complete($rs->code);
          }
          $update++;
        }
      }
    }


    $logs = array(
      'sync_item' => 'WS-WU',
      'get_item' => $count,
      'update_item' => $update
    );

    //--- add logs
    $this->sync_data_model->add_logs($logs);

  }


  public function syncConsignmentInvCode()
  {
    $this->load->model('orders/orders_model');
    $ds = $this->orders_model->get_consignment_non_inv_code($this->limit);
    $count = 0;
    $update = 0;

    if(!empty($ds))
    {
      foreach($ds as $rs)
      {
        $count++;
        $inv = $this->orders_model->get_sap_doc_num($rs->code);
        if(!empty($inv))
        {
          if($this->orders_model->update_inv($rs->code, $inv))
          {
            $this->orders_model->set_complete($rs->code);
          }
          $update++;
        }
      }
    }
    else
    {
      $message = 'not found';
    }

    $logs = array(
      'sync_item' => 'WC',
      'get_item' => $count,
      'update_item' => $update
    );

    //--- add logs
    $this->sync_data_model->add_logs($logs);

  }


  public function syncOrderTransformInvCode()
  {
    $this->load->model('orders/orders_model');
    $this->load->model('inventory/transfer_model');
    $ds = $this->orders_model->get_order_transform_non_inv_code($this->limit);
    $count = 0;
    $update = 0;

    if(!empty($ds))
    {
      foreach($ds as $rs)
      {
        $count++;
        $inv = $this->transfer_model->get_sap_doc_num($rs->code);
        if(!empty($inv))
        {
          if($this->orders_model->update_inv($rs->code, $inv))
          {
            $this->orders_model->set_complete($rs->code);
          }
          $update++;
        }
      }
    }


    $logs = array(
      'sync_item' => 'WQ-WV',
      'get_item' => $count,
      'update_item' => $update
    );

    //--- add logs
    $this->sync_data_model->add_logs($logs);

  }


  //---- WT
  public function syncOrderTransferInvCode()
  {
    $this->load->model('orders/orders_model');
    $this->load->model('inventory/transfer_model');
    $ds = $this->orders_model->get_order_transfer_non_inv_code($this->limit);
    $count = 0;
    $update = 0;

    if(!empty($ds))
    {
      foreach($ds as $rs)
      {
        $count++;
        $inv = $this->transfer_model->get_sap_doc_num($rs->code);
        if(!empty($inv))
        {
          if($this->orders_model->update_inv($rs->code, $inv))
          {
            $this->orders_model->set_complete($rs->code);
          }
          $update++;
        }
      }
    }


    $logs = array(
      'sync_item' => 'WT',
      'get_item' => $count,
      'update_item' => $update
    );

    //--- add logs
    $this->sync_data_model->add_logs($logs);


  }


  //---- WT
  public function syncOrderLendInvCode()
  {
    $this->load->model('orders/orders_model');
    $this->load->model('inventory/transfer_model');
    $ds = $this->orders_model->get_order_lend_non_inv_code($this->limit);
    $count = 0;
    $update = 0;

    if(!empty($ds))
    {
      foreach($ds as $rs)
      {
        $count++;
        $inv = $this->transfer_model->get_sap_doc_num($rs->code);
        if(!empty($inv))
        {
          if($this->orders_model->update_inv($rs->code, $inv))
          {
            $this->orders_model->set_complete($rs->code);
          }
          $update++;
        }
      }
    }


    $logs = array(
      'sync_item' => 'WL',
      'get_item' => $count,
      'update_item' => $update
    );

    //--- add logs
    $this->sync_data_model->add_logs($logs);

  }


  public function syncTransferInvCode()
  {
    $this->load->model('inventory/transfer_model');
    $ds = $this->transfer_model->get_non_inv_code($this->limit);
    $count = 0;
    $update = 0;

    if(!empty($ds))
    {
      foreach($ds as $rs)
      {
        $count++;
        $inv = $this->transfer_model->get_sap_doc_num($rs->code);
        if(!empty($inv))
        {
          $this->transfer_model->update_inv($rs->code, $inv);
          $update++;
        }
      }
    }

    $logs = array(
      'sync_item' => 'WW',
      'get_item' => $count,
      'update_item' => $update
    );

    //--- add logs
    $this->sync_data_model->add_logs($logs);

  }


  public function syncReturnLendInvCode()
  {
    $this->load->model('inventory/return_lend_model');
    $ds = $this->return_lend_model->get_non_inv_code($this->limit);

    $count = 0;
    $update = 0;

    if(!empty($ds))
    {
      foreach($ds as $rs)
      {
        $count++;
        $inv = $this->return_lend_model->get_sap_doc_num($rs->code);
        if(!empty($inv))
        {
          $this->return_lend_model->update_inv($rs->code, $inv);
          $update++;
        }
      }
    }

    $logs = array(
      'sync_item' => 'RN',
      'get_item' => $count,
      'update_item' => $update
    );

    //--- add logs
    $this->sync_data_model->add_logs($logs);

  }


  public function syncMoveInvCode()
  {
    $this->load->model('inventory/move_model');
    $ds = $this->move_model->get_non_inv_code($this->limit);
    $count = 0;
    $update = 0;

    if(!empty($ds))
    {
      foreach($ds as $rs)
      {
        $count++;
        $inv = $this->move_model->get_sap_doc_num($rs->code);
        if(!empty($inv))
        {
          $this->move_model->update_inv($rs->code, $inv);
          $update++;
        }
      }
    }


    $logs = array(
      'sync_item' => 'MV',
      'get_item' => $count,
      'update_item' => $update
    );

    //--- add logs
    $this->sync_data_model->add_logs($logs);

  }


  public function syncTransformGoodsIssueCode()
  {
    $this->load->model('inventory/adjust_transform_model');
    $ds = $this->adjust_transform_model->get_non_issue_code($this->limit);
    $count = 0;
    $update = 0;

    if(!empty($ds))
    {
      foreach($ds as $rs)
      {
        $count++;
        $inv = $this->adjust_transform_model->get_sap_issue_doc($rs->code);

        if(!empty($inv))
        {
          $this->adjust_transform_model->update_issue_code($rs->code, $inv->DocNum);
          $update++;
        }

      }
    }


    $logs = array(
      'sync_item' => 'WG',
      'get_item' => $count,
      'update_item' => $update
    );

    //--- add logs
    $this->sync_data_model->add_logs($logs);

  }


  public function syncAdjustGoodsIssueCode()
  {
    $this->load->model('inventory/adjust_model');
    $ds = $this->adjust_model->get_non_issue_code($this->limit);
    $count = 0;
    $update = 0;

    if(!empty($ds))
    {
      foreach($ds as $rs)
      {
        $count++;
        $inv = $this->adjust_model->get_sap_issue_doc($rs->code);

        if(!empty($inv))
        {
          $this->adjust_model->update_issue_code($rs->code, $inv->DocNum);
          $update++;
        }

      }
    }


    $logs = array(
      'sync_item' => 'AJ-IGE',
      'get_item' => $count,
      'update_item' => $update
    );

    //--- add logs
    $this->sync_data_model->add_logs($logs);

  }


  public function syncAdjustGoodsReceiveCode()
  {
    $this->load->model('inventory/adjust_model');
    $ds = $this->adjust_model->get_non_receive_code($this->limit);
    $count = 0;
    $update = 0;

    if(!empty($ds))
    {
      foreach($ds as $rs)
      {
        $count++;
        $inv = $this->adjust_model->get_sap_receive_doc($rs->code);

        if(!empty($inv))
        {
          $this->adjust_model->update_receive_code($rs->code, $inv->DocNum);
          $update++;
        }

      }
    }


    $logs = array(
      'sync_item' => 'AJ-IGN',
      'get_item' => $count,
      'update_item' => $update
    );

    //--- add logs
    $this->sync_data_model->add_logs($logs);

  }


  public function syncReturnOrderCode()
  {
    $this->load->model('inventory/return_order_model');
    $ds = $this->return_order_model->get_non_inv_code($this->limit);
    $count = 0;
    $update = 0;

    if(!empty($ds))
    {
      foreach($ds as $rs)
      {
        $count++;
        $inv = $this->return_order_model->get_sap_doc_num($rs->code);

        if(!empty($inv))
        {
          $this->return_order_model->update_inv($rs->code, $inv);
          $update++;
        }

      }
    }


    $logs = array(
      'sync_item' => 'SM',
      'get_item' => $count,
      'update_item' => $update
    );

    //--- add logs
    $this->sync_data_model->add_logs($logs);

  }


  public function syncConsignSoldInvCode()
  {
    $this->load->model('account/consign_order_model');
    $ds = $this->consign_order_model->get_non_inv_code($this->limit);
    $count = 0;
    $update = 0;

    if(!empty($ds))
    {
      foreach($ds as $rs)
      {
        $count++;
        $inv = $this->consign_order_model->get_sap_doc_num($rs->code);

        if(!empty($inv))
        {
          $this->consign_order_model->update_inv($rs->code, $inv);
          $update++;
        }

      }
    }

    $logs = array(
      'sync_item' => 'WM',
      'get_item' => $count,
      'update_item' => $update
    );

    //--- add logs
    $this->sync_data_model->add_logs($logs);
  }


  public function syncConsignmentSoldInvCode()
  {
    $this->load->model('account/consignment_order_model');
    $ds = $this->consignment_order_model->get_non_inv_code($this->limit);
    $count = 0;
    $update = 0;

    if(!empty($ds))
    {
      foreach($ds as $rs)
      {
        $count++;
        $inv = $this->consignment_order_model->get_sap_doc_num($rs->code);

        if(!empty($inv))
        {
          $this->consignment_order_model->update_inv($rs->code, $inv);
          $update++;
        }

      }
    }


    $logs = array(
      'sync_item' => 'WD',
      'get_item' => $count,
      'update_item' => $update
    );

    //--- add logs
    $this->sync_data_model->add_logs($logs);

  }


  public function syncReturnConsignmentOrderCode()
  {
    $this->load->model('inventory/return_consignment_model');
    $ds = $this->return_consignment_model->get_non_inv_code($this->limit);
    $count = 0;
    $update = 0;

    if(!empty($ds))
    {
      foreach($ds as $rs)
      {
        $count++;
        $inv = $this->return_consignment_model->get_sap_doc_num($rs->code);

        if(!empty($inv))
        {
          $this->return_consignment_model->update_inv($rs->code, $inv);
          $update++;
        }

      }
    }


    $logs = array(
      'sync_item' => 'CN',
      'get_item' => $count,
      'update_item' => $update
    );

    //--- add logs
    $this->sync_data_model->add_logs($logs);

  }
} //--- end class

 ?>
