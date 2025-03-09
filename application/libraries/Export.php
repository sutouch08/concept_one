<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');
class Export
{
  protected $ci;
  protected $mc;
  public $error;

	public function __construct()
	{
    // Assign the CodeIgniter super-object
    $this->ci =& get_instance();
	}


  public function set_exported($code, $sc)
  {
    //--- ถ้า error  set เป็น 3(export แล้ว แต่ error) ถ้าไม่ error เป็น 1 (export แล้ว)
    $is_exported = $sc === FALSE ? 3 : 1;
    $export_error = $sc === FALSE ? $this->error : NULL;

    return $this->ci->orders_model->set_exported($code, $is_exported, $export_error );
  }

  //--- for export invoice to get data from ODPI for Invoice Downpayment Drawn
  public function get_dpm_by_base_ref($BaseRef)
  {
    $this->ci->load->model('orders/order_down_payment_model');
    $this->ci->load->model('orders/down_payment_invoice_model');

    $dpmInvData = NULL;
    $dpmInvCodes = [];
    $dpm = $this->ci->order_down_payment_model->get_invoice_by_target($BaseRef);

    if( ! empty($dpm))
    {
      foreach($dpm as $dp)
      {
        $dpmInvCodes[] = $dp->invoice_code;
      }
    }

    if( ! empty($dpmInvCodes))
    {
      $dpmInvData = $this->ci->down_payment_invoice_model->get_down_payments_by_array($dpmInvCodes);
    }

    return $dpmInvData;
  }


  //--- ODLN  DLN1
  public function export_order($code, $option = 'A')
  {
    //-- option 'A' = Aadd , U = 'update'
    $sc = TRUE;
    $this->ci->load->model('orders/orders_model');
    $this->ci->load->model('inventory/delivery_order_model');
    $this->ci->load->model('masters/customers_model');
    $this->ci->load->model('masters/products_model');
    $this->ci->load->model('discount/discount_policy_model');
    $this->ci->load->model('masters/zone_model');
    $this->ci->load->helper('discount');

    $order = $this->ci->orders_model->get($code);
    $cust = $this->ci->customers_model->get($order->customer_code);
    $total_amount = $this->ci->orders_model->get_bill_total_amount($code);

    $service_wh = getConfig('SERVICE_WAREHOUSE');
    $U_WhsCode = NULL;
    $U_BinCode = NULL;
    $U_Consignment = NULL;
    if($order->role === 'C')
    {
      $zone = $this->ci->zone_model->get($order->zone_code);
      if(!empty($zone))
      {
        $U_WhsCode = $zone->warehouse_code;
        $U_BinCode = $zone->code;
        $U_Consignment = 'Y';
      }
    }


    $do = $option == 'U' ? NULL : $this->ci->delivery_order_model->get_sap_delivery_order($code);

    if(empty($do))
    {
      $middle = $option == 'U' ? NULL : $this->ci->delivery_order_model->get_middle_delivery_order($code);

      if( ! empty($middle))
      {
        foreach($middle as $rows)
        {
          if($this->ci->delivery_order_model->drop_middle_exits_data($rows->DocEntry) === FALSE)
          {
            $sc = FALSE;
            $this->error = "ลบรายการที่ค้างใน Temp ไม่สำเร็จ";
          }
        }
      }

      if($sc === TRUE)
      {
        $currency = getConfig('CURRENCY');
        $vat_rate = getConfig('SALE_VAT_RATE');
        $vat_code = getConfig('SALE_VAT_CODE');
				$date_add = getConfig('ORDER_SOLD_DATE') == 'D' ? $order->date_add : (empty($order->shipped_date) ? now() : $order->shipped_date);

        $remark = empty($order->customer_ref) ? $order->remark : $order->customer_ref ."  ".$order->remark;
        //--- header
        $ds = array(
          'DocType' => 'I', //--- I = item, S = Service
          'CANCELED' => 'N', //--- Y = Yes, N = No
          'DocDate' => sap_date($date_add, TRUE), //--- วันที่เอกสาร
          'DocDueDate' => sap_date($date_add,TRUE), //--- วันที่เอกสาร
          'CardCode' => $order->customer_code, //--- รหัสลูกค้า
          'CardName' => $cust->name, //--- ชื่อลูกค้า
          'DiscPrcnt' => $order->bDiscText,
          'DiscSum' => $order->bDiscAmount,
          'DiscSumFC' => $order->bDiscAmount,
          'DocCur' => $currency,
          'DocRate' => 1.000000,
          'DocTotal' => $total_amount,
          'DocTotalFC' => $total_amount,
          'GroupNum' => $cust->GroupNum,
          'SlpCode' => empty($order->sale_code) ? -1 : $order->sale_code,
          'ToWhsCode' => NULL,
          'Comments' => limitText($remark, 250),
          'U_SONO' => $order->code,
          'U_ECOMNO' => $order->code,
          'U_BOOKCODE' => $order->bookcode,
          'F_E_Commerce' => $option,
          'F_E_CommerceDate' => sap_date(now(), TRUE),
          'F_Sap' => 'P',
          'U_WhsCode' => $U_WhsCode,
          'U_BinCode' => $U_BinCode,
          'U_Consignment' => $U_Consignment,
          'U_Channels' => $order->channels_code
        );

        $this->ci->mc->trans_begin();

        $docEntry = $this->ci->delivery_order_model->add_sap_delivery_order($ds);


        if($docEntry !== FALSE)
        {
          $details = $this->ci->delivery_order_model->get_sold_details($code);
          if(!empty($details))
          {
            $line = 0;

            foreach($details as $rs)
            {

              $arr = array(
                'DocEntry' => $docEntry,
                'U_ECOMNO' => $rs->reference,
                'LineNum' => $line,
                'ItemCode' => $rs->product_code,
                'Dscription' => limitText($rs->product_name, 95),
                'Quantity' => $rs->qty,
                'UnitMsr' => $this->ci->products_model->get_unit_code($rs->product_code),
                'PriceBefDi' => $rs->price,  //---มูลค่าต่อหน่วยก่อนภาษี/ก่อนส่วนลด
                'LineTotal' => $rs->total_amount,
                'Currency' => $currency,
                'Rate' => 1.000000,
                'DiscPrcnt' => discountAmountToPercent($rs->discount_amount, $rs->qty, $rs->price), ///--- discount_helper
                'Price' => remove_vat($rs->price, $vat_rate), //--- ราคา
                'TotalFrgn' => $rs->total_amount, //--- จำนวนเงินรวม By Line (Currency)
                'WhsCode' => ($rs->is_count == 1 ? $rs->warehouse_code : $service_wh),
                'BinCode' => ($rs->is_count == 1 ? $rs->zone_code : $service_wh."-SYSTEM-BIN-LOCATION"),
                'TaxStatus' => 'Y',
                'VatPrcnt' => $vat_rate,
                'VatGroup' => $vat_code,
                'PriceAfVat' => $rs->sell,
                'GTotal' => round($rs->total_amount, 2),
                'VatSum' => get_vat_amount($rs->total_amount), //---- tool_helper
                'TaxType' => 'Y', //--- คิดภาษีหรือไม่
                'SlpCode' => empty($rs->sale_code) ? -1 : $rs->sale_code,
                'F_E_Commerce' => $option, //--- A = Add , U = Update
                'F_E_CommerceDate' => sap_date(now(), TRUE),
                'U_PROMOTION' => $this->ci->discount_policy_model->get_code($rs->id_policy),
                'U_Channels' => $rs->channels_code
              );

              $this->ci->delivery_order_model->add_delivery_row($arr);
              $line++;
            }

            if($sc === TRUE)
            {
              $arr = array('F_Sap' => NULL);

              $this->ci->delivery_order_model->update_sap_delivery_order($docEntry, $arr);
            }
          }
          else
          {
            $sc = FALSE;
            $this->error = "ไม่พบรายการขาย";
          }
        }
        else
        {
          $sc = FALSE;
          $this->error = "เพิ่มเอกสารไม่สำเร็จ";
        }

        if($sc === TRUE)
        {
          $this->ci->mc->trans_commit();

          if($order->inv_code != NULL && $option == 'A')
          {
            $this->ci->orders_model->update($code, array('inv_code' => NULL));
            $this->ci->orders_model->un_complete($code);
          }
        }
        else
        {
          $this->ci->mc->trans_rollback();
        }
      }

    }
    else
    {
      $sc = FALSE;
      $this->error = "เอกสารถูกนำเข้า SAP แล้ว หากต้องการเปลี่ยนแปลงกรุณายกเลิกเอกสารใน SAP ก่อน";
    }

    $this->set_exported($code, $sc);

    return $sc;
  }


  //--- OINV  INV1
  public function export_invoice($code, $option = 'A')
  {
    $sc = TRUE;
    $this->ci->load->model('orders/order_invoice_model');
    $this->ci->load->model('orders/order_pos_payment_model');
    $this->ci->load->model('orders/order_payment_model');
    $this->ci->load->model('orders/down_payment_invoice_model');
    $this->ci->load->model('masters/customers_model');
    $this->ci->load->model('masters/pos_model');
    $this->ci->load->model('discount/discount_policy_model');
    $this->ci->load->helper('discount');
    $this->ci->load->helper('payment_method');

    $df_sale_id = getConfig('DEFAULT_SALES_ID');

    $order = $this->ci->order_invoice_model->get($code);

    $cust = $this->ci->customers_model->get($order->CardCode);

    $service_wh = getConfig('SERVICE_WAREHOUSE');

    $do = $this->ci->order_invoice_model->get_sap_doc_num($code);

    if(empty($do))
    {
      $middle = $this->ci->order_invoice_model->get_middle_invoice($code);

      if(!empty($middle))
      {
        foreach($middle as $rows)
        {
          if($this->ci->order_invoice_model->drop_middle_exits_data($rows->DocEntry) === FALSE)
          {
            $sc = FALSE;
            $this->error = "ลบรายการที่ค้างใน Temp ไม่สำเร็จ";
          }
        }
      }

      if($sc === TRUE)
      {
        $currency = getConfig('CURRENCY');
        $vat_code = getConfig('SALE_VAT_CODE');
        $vat_type = $order->vat_type;
        $address = parseAddress($order->address, $order->sub_district, $order->district, $order->province, $order->postcode);
        $payment_type = "";
        $payment_type .= "หักมัดจำ : ".$order->downPaymentAmount;
        $payments = [];

        if($order->BaseType == 'POS')
        {
          $payments[] = $order->BaseRef;

          $dpc = $this->ci->order_down_payment_model->get_invoice_by_target($order->BaseRef);

          if( ! empty($dpc))
          {
            foreach($dpc as $dpd)
            {
              $payments[] = $dpd->code;
            }
          }
        }
        else
        {
          $dpc = $this->ci->order_down_payment_model->get_invoice_by_target($order->BaseRef);

          if( ! empty($dpc))
          {
            foreach($dpc as $dpd)
            {
              $payments[] = $dpd->code;
            }
          }

          $payment_type .= ",เงินโอน : ".$this->ci->order_payment_model->get_amount($order->BaseRef);
        }

        $dpmAmount = 0;
        $dpmVatSum = 0;
        $dpmDrawn = 'N';

        $dpm = $this->get_dpm_by_base_ref($order->BaseRef);

        if( ! empty($dpm))
        {
          $dpmDrawn = 'Y';

          foreach($dpm as $dp)
          {
            $dpmAmount += $dp->DocTotal;
            $dpmVatSum += $dp->VatSum;
          }
        }

        //--- header
        $ds = array(
          'DocType' => 'I', //--- I = item, S = Service
          'CANCELED' => 'N', //--- Y = Yes, N = No
          'DocDate' => sap_date($order->DocDate, TRUE), //--- วันที่เอกสาร
          'DocDueDate' => sap_date($order->DocDueDate,TRUE), //--- วันที่เอกสาร
          'CardCode' => $order->CardCode, //--- รหัสลูกค้า
          'CardName' => $order->CardName, //--- ชื่อลูกค้า
          'PayToCode' => $order->branch_code,
          'Address' => get_null($address),
          'NumAtCard' => get_null($order->NumAtCard),
          'LicTradNum' => get_null($order->tax_id),
          'WTSum' => $order->WhtAmount,
          'VatSum' => $order->VatSum,
          'DiscPrcnt' => $order->DiscPrcnt,
          'DiscSum' => $order->DiscSum,
          'DiscSumFC' => 0.00,
          'DocCur' => $order->DocCur,
          'DocRate' => $order->DocRate,
          'DocTotal' => $order->DocTotal,
          'DocTotalFC' => 0.00,
          'RoundDif' => $order->RoundDif,
          'GroupNum' => $cust->GroupNum,
          'SlpCode' => empty($order->SlpCode) ? $df_sale_id : $order->SlpCode,
          'Comments' => $order->NumAtCard,
          'DpmAmnt' => $dpmAmount,
          'DpmVat' => $dpmVatSum,
          'DpmDrawn' => $dpmDrawn,
          'U_SONO' => $order->so_code,
          'U_ECOMNO' => $order->code,
          'U_BOOKCODE' => $order->bookcode,
          'F_E_Commerce' => $option,
          'F_E_CommerceDate' => sap_date(now(), TRUE),
          'F_Sap' => NULL,
          'U_WhsCode' => $order->WhsCode,
          'U_Consignment' => NULL,
          'U_Channels' => $order->channels_code,
          'U_TAX_STATUS' => $order->TaxStatus,
          'U_POSNO' => $this->ci->pos_model->get_code($order->pos_id),
          'U_PAYMENTTYPE' => $payment_type,
          'U_OLDTAX' => $order->code,
          'U_Incomming1' => (empty($payments[0]) ? NULL : $payments[0]),
          'U_Incomming2' => (empty($payments[1]) ? NULL : $payments[1]),
          'U_Incomming3' => (empty($payments[2]) ? NULL : $payments[2])
        );

        $this->ci->mc->trans_begin();

        $docEntry = $this->ci->order_invoice_model->add_sap_invoice($ds);


        if($docEntry !== FALSE)
        {
          $details = $this->ci->order_invoice_model->get_details($code);

          if(!empty($details))
          {
            $line = 0;

            foreach($details as $rs)
            {
              $arr = array(
                'DocEntry' => $docEntry,
                'U_ECOMNO' => $order->code,
                'LineNum' => $rs->LineNum,
                'ItemCode' => $rs->ItemCode,
                'Dscription' => limitText($rs->Dscription, 95),
                'Quantity' => $rs->Qty,
                'UnitMsr' => $rs->unitMsr,
                'PriceBefDi' => $rs->PriceBefDi,  //---มูลค่าต่อหน่วยก่อนภาษี/ก่อนส่วนลด
                'LineTotal' => $rs->LineTotal,
                'Currency' => $rs->Currency,
                'Rate' => $rs->Rate,
                'DiscPrcnt' => $rs->DiscPrcnt, ///--- discount_helper
                'Price' => $rs->Price, //--- ราคา
                'TotalFrgn' => 0.00, //--- จำนวนเงินรวม By Line (Currency)
                'WhsCode' => ($rs->is_count == 1 ? $rs->WhsCode : $service_wh),
                'BinCode' => ($rs->is_count == 1 ? $rs->BinCode : $service_wh."-SYSTEM-BIN-LOCATION"),
                'TaxStatus' => 'Y',
                'VatPrcnt' => $rs->VatRate,
                'VatGroup' => $rs->VatCode,
                'PriceAfVat' => $rs->PriceAfVAT,
                'GTotal' => round($rs->LineTotal, 2),
                'VatSum' => round($rs->VatSum, 2), //---- tool_helper
                'SlpCode' => $rs->SlpCode,
                'TaxType' => 'Y', //--- คิดภาษีหรือไม่
                'F_E_Commerce' => $option, //--- A = Add , U = Update
                'F_E_CommerceDate' => sap_date(now(), TRUE),
                'U_PROMOTION' => NULL
              );

              if( ! $this->ci->order_invoice_model->add_invoice_row($arr))
              {
                $sc = FALSE;
                $this->error = "Failed to insert invoice rows at Line : {$line}";
              }
            }

            if($sc === TRUE)
            {
              if( ! empty($dpm))
              {
                $ln = 0;

                foreach($dpm as $dp)
                {
                  $arr = array(
                    'DocEntry' => $docEntry,
                    'LineNum' => $ln,
                    'BaseAbs' => $dp->DocEntry,
                    'BaseDocNum' => $dp->DocNum,
                    'BaseLine' => NULL,
                    'DrawnSum' => $dp->DocTotal
                  );

                  if( ! $this->ci->order_invoice_model->add_dpm_drawn($arr))
                  {
                    $sc = FALSE;
                    $this->error = "Failed to insert invoice downpayment drawn";
                  }

                  $ln++;
                }
              }
            }

            if($sc === TRUE)
            {
              $arr = array('F_Sap' => NULL);

              $this->ci->order_invoice_model->update_sap_invoice($docEntry, $arr);
            }
          }
          else
          {
            $sc = FALSE;
            $this->error = "ไม่พบรายการขาย";
          }
        }
        else
        {
          $sc = FALSE;
          $this->error = "เพิ่มเอกสารไม่สำเร็จ";
        }

        if($sc === TRUE)
        {
          $this->ci->mc->trans_commit();

          if($order->DocNum != NULL)
          {
            $this->ci->order_invoice_model->update($code, ['status' => 'O', 'DocNum' => NULL]);
            $this->ci->order_invoice_model->update_details($code, ['LineStatus' => 'O']);
          }
        }
        else
        {
          $this->ci->mc->trans_rollback();
        }
      }

      //--- ถ้า error  set เป็น 3(export แล้ว แต่ error) ถ้าไม่ error เป็น 1 (export แล้ว)
      $arr = array(
        'isExported' => $sc === TRUE ? 'Y' : 'E',
        'export_error' => $sc === TRUE ? NULL : $this->error
      );

      $this->ci->order_invoice_model->update($code, $arr);

    }
    else
    {
      $sc = FALSE;
      $this->error = "เอกสารถูกนำเข้า SAP แล้ว หากต้องการเปลี่ยนแปลงกรุณายกเลิกเอกสารใน SAP ก่อน";
    }

    return $sc;
  }



  //--- ตัดยอดฝากขาย (WM)(เปิดใบกำกับเมื่อขายได้)
  //--- ODLN  DLN1
  public function export_consign_order($code, $option = 'A')
  {
    $sc = TRUE;
    $this->ci->load->model('account/consign_order_model');
    $this->ci->load->model('orders/orders_model');
    $this->ci->load->model('inventory/delivery_order_model');
    $this->ci->load->model('masters/customers_model');
    $this->ci->load->model('masters/products_model');
    $this->ci->load->model('discount/discount_policy_model');
    $this->ci->load->helper('discount');

    $order = $this->ci->consign_order_model->get($code);
    $cust = $this->ci->customers_model->get($order->customer_code);
    $total_amount = $this->ci->orders_model->get_bill_total_amount($code);

    $service_wh = getConfig('SERVICE_WAREHOUSE');

    $do = $option == 'U' ? NULL : $this->ci->delivery_order_model->get_sap_delivery_order($code);

    if(empty($do))
    {
      $middle = $option == 'U' ? NULL : $this->ci->delivery_order_model->get_middle_delivery_order($code);

      if(!empty($middle))
      {
        foreach($middle as $rows)
        {
          if($this->ci->delivery_order_model->drop_middle_exits_data($rows->DocEntry) === FALSE)
          {
            $sc = FALSE;
            $this->error = "ลบรายการที่ค้างใน Temp ไม่สำเร็จ";
          }
        }

      }

      if($sc === TRUE)
      {
        $currency = getConfig('CURRENCY');
        $vat_rate = getConfig('SALE_VAT_RATE');
        $vat_code = getConfig('SALE_VAT_CODE');
				$date_add = $order->date_add;
        //--- header
        $ds = array(
          'DocType' => 'I', //--- I = item, S = Service
          'CANCELED' => 'N', //--- Y = Yes, N = No
          'DocDate' => sap_date($date_add, TRUE), //--- วันที่เอกสาร
          'DocDueDate' => sap_date($date_add,TRUE), //--- วันที่เอกสาร
          'CardCode' => $order->customer_code, //--- รหัสลูกค้า
          'CardName' => $cust->name, //--- ชื่อลูกค้า
          'DocCur' => $currency,
          'DocRate' => 1.000000,
          'DocTotal' => $total_amount,
          'DocTotalFC' => $total_amount,
          'GroupNum' => $cust->GroupNum,
          'SlpCode' => empty($order->sale_code) ? -1 : $order->sale_code,
          'ToWhsCode' => NULL,
          'Comments' => limitText($order->remark,250),
          'U_SONO' => $order->code,
          'U_ECOMNO' => $order->code,
          'U_BOOKCODE' => $order->bookcode,
          'F_E_Commerce' => $option,
          'F_E_CommerceDate' => sap_date(now(), TRUE),
          'F_Sap' => 'P',
          'U_Channels' => $order->channels_code
        );

        $this->ci->mc->trans_begin();

        $docEntry = $this->ci->delivery_order_model->add_sap_delivery_order($ds);


        if($docEntry !== FALSE)
        {
          $details = $this->ci->delivery_order_model->get_sold_details($code);
          if(!empty($details))
          {
            $line = 0;

            foreach($details as $rs)
            {

              $arr = array(
                'DocEntry' => $docEntry,
                'U_ECOMNO' => $rs->reference,
                'LineNum' => $line,
                'ItemCode' => $rs->product_code,
                'Dscription' => limitText($rs->product_name, 95),
                'Quantity' => $rs->qty,
                'UnitMsr' => $this->ci->products_model->get_unit_code($rs->product_code),
                'PriceBefDi' => $rs->price,  //---มูลค่าต่อหน่วยก่อนภาษี/ก่อนส่วนลด
                'LineTotal' => $rs->total_amount,
                'Currency' => $currency,
                'Rate' => 1.000000,
                'DiscPrcnt' => discountAmountToPercent($rs->discount_amount, $rs->qty, $rs->price), ///--- discount_helper
                'Price' => remove_vat($rs->price, $vat_rate), //--- ราคา
                'TotalFrgn' => $rs->total_amount, //--- จำนวนเงินรวม By Line (Currency)
                'WhsCode' => ($rs->is_count == 1 ? $rs->warehouse_code : $service_wh),
                'BinCode' => $rs->zone_code,
                'TaxStatus' => 'Y',
                'VatPrcnt' => $vat_rate,
                'VatGroup' => $vat_code,
                'PriceAfVat' => $rs->sell,
                'GTotal' => round($rs->total_amount, 2),
                'VatSum' => get_vat_amount($rs->total_amount), //---- tool_helper
                'TaxType' => 'Y', //--- คิดภาษีหรือไม่
                'SlpCode' => empty($rs->sale_code) ? -1 : $rs->sale_code,
                'F_E_Commerce' => $option, //--- A = Add , U = Update
                'F_E_CommerceDate' => sap_date(now(), TRUE),
                'U_PROMOTION' => $this->ci->discount_policy_model->get_code($rs->id_policy)
              );

              $this->ci->delivery_order_model->add_delivery_row($arr);
              $line++;
            }

            if($sc === TRUE)
            {
              $arr = array('F_Sap' => NULL);

              $this->ci->delivery_order_model->update_sap_delivery_order($docEntry, $arr);
            }
          }
          else
          {
            $sc = FALSE;
            $this->error = "ไม่พบรายการขาย";
          }
        }
        else
        {
          $sc = FALSE;
          $this->error = "เพิ่มเอกสารไม่สำเร็จ";
        }

        if($sc === TRUE)
        {
          $this->ci->mc->trans_commit();
        }
        else
        {
          $this->ci->mc->trans_rollback();
        }
      }
    }
    else
    {
      $sc = FALSE;
      $this->error = "เอกสารถูกนำเข้า SAP แล้ว หากต้องการเปลี่ยนแปลงกรุณายกเลิกเอกสารใน SAP ก่อน";
    }

    $this->set_exported($code, $sc);

    return $sc;
  }




  //---- OWTR WTR1
  public function export_transfer_order($code, $option = 'A')
  {
    $sc = TRUE;
    $this->ci->load->model('orders/orders_model');
    $this->ci->load->model('inventory/delivery_order_model');
    $this->ci->load->model('inventory/transfer_model');
    $this->ci->load->model('masters/customers_model');
    $this->ci->load->model('masters/products_model');
    $this->ci->load->model('masters/zone_model');
    $this->ci->load->helper('discount');

    $doc = $this->ci->orders_model->get($code);
    $sap = $this->ci->transfer_model->get_sap_transfer_doc($code);
    $zone = $this->ci->zone_model->get($doc->zone_code);

    if($doc->role == 'L' OR $doc->role == 'R')
    {
      $cust = new stdClass();
      $cust->code = NULL;
      $cust->name = NULL;
    }
    else
    {
      $cust = $this->ci->customers_model->get($doc->customer_code);
    }

    if(!empty($doc))
    {
      if(empty($sap))
      {
        if($doc->status == 1)
        {
          //--- เช็คของเก่าก่อนว่ามีในถังกลางหรือยัง
          $middle = $this->ci->transfer_model->get_middle_transfer_doc($code);
          if(!empty($middle))
          {
            foreach($middle as $rows)
            {
              if($this->ci->transfer_model->drop_middle_exits_data($rows->DocEntry) === FALSE)
              {
                $sc = FALSE;
                $this->error = "ลบรายที่ค้างใน temp ไม่สำเร็จ";
              }
            }

          }

          if($sc === TRUE)
          {
            $currency = getConfig('CURRENCY');
            $vat_rate = getConfig('SALE_VAT_RATE');
            $vat_code = getConfig('SALE_VAT_CODE');
						$date_add = getConfig('ORDER_SOLD_DATE') == 'D' ? $doc->date_add : (empty($doc->shipped_date) ? now() : $doc->shipped_date);
            $total_amount = $this->ci->orders_model->get_bill_total_amount($code);

            $ds = array(
              'U_ECOMNO' => $doc->code,
              'DocType' => 'I',
              'CANCELED' => 'N',
              'DocDate' => sap_date($date_add, TRUE),
              'DocDueDate' => sap_date($date_add, TRUE),
              'CardCode' => $cust->code,
              'CardName' => $cust->name,
              'VatPercent' => $vat_rate,
              'VatSum' => round(get_vat_amount($total_amount), 6),
              'VatSumFc' => round(get_vat_amount($total_amount), 6),
              'DiscPrcnt' => 0.000000,
              'DiscSum' => 0.000000,
              'DiscSumFC' => 0.000000,
              'DocCur' => $currency,
              'DocRate' => 1,
              'DocTotal' => remove_vat($total_amount, $vat_rate),
              'DocTotalFC' => remove_vat($total_amount, $vat_rate),
              'Filler' => empty($zone) ? NULL : $zone->warehouse_code,
              'ToWhsCode' => empty($zone) ? NULL : $zone->warehouse_code,
              'Comments' => limitText($doc->remark, 250),
              'F_E_Commerce' => $option,
              'F_E_CommerceDate' => sap_date(now(), TRUE),
              'U_BOOKCODE' => $doc->bookcode,
              'U_REQUESTER' => $doc->empName,
              'U_APPROVER' => $doc->approver
            );

            $this->ci->mc->trans_begin();

            $docEntry = $this->ci->transfer_model->add_sap_transfer_doc($ds);

            if($docEntry)
            {
              $drop = $middle === TRUE ? $this->ci->transfer_model->drop_sap_exists_details($code) : TRUE;

              $details = $this->ci->delivery_order_model->get_sold_details($code);

              if(!empty($details) && $drop === TRUE)
              {
                $line = 0;
                foreach($details as $rs)
                {
                  $arr = array(
                    'DocEntry' => $docEntry,
                    'U_ECOMNO' => $rs->reference,
                    'LineNum' => $line,
                    'ItemCode' => $rs->product_code,
                    'Dscription' => limitText($rs->product_name, 95),
                    'Quantity' => $rs->qty,
                    'unitMsr' => $this->ci->products_model->get_unit_code($rs->product_code),
                    'PriceBefDi' => round($rs->price,2),
                    'LineTotal' => round($rs->total_amount,2),
                    'ShipDate' => $date_add,
                    'Currency' => $currency,
                    'Rate' => 1,
                    //--- คำนวณส่วนลดจากยอดเงินกลับมาเป็น % (เพราะบางทีมีส่วนลดหลายชั้น)
                    'DiscPrcnt' => discountAmountToPercent($rs->discount_amount, $rs->qty, $rs->price), ///--- discount_helper
                    'Price' => round(remove_vat($rs->price, $vat_rate),2),
                    'TotalFrgn' => round($rs->total_amount,2),
                    'FromWhsCod' => $rs->warehouse_code,
                    'WhsCode' => empty($zone) ? NULL : $zone->warehouse_code,
                    'FisrtBin' => $doc->zone_code, //-- โซนปลายทาง
                    'F_FROM_BIN' => $rs->zone_code, //--- โซนต้นทาง
                    'F_TO_BIN' => $doc->zone_code, //--- โซนปลายทาง
                    'TaxStatus' => 'Y',
                    'VatPrcnt' => $vat_rate,
                    'VatGroup' => $vat_code,
                    'PriceAfVAT' => round($rs->sell,2),
                    'VatSum' => round(get_vat_amount($rs->total_amount),2),
                    'GTotal' => round($rs->total_amount, 2),
                    'TaxType' => 'Y',
                    'F_E_Commerce' => $option,
                    'F_E_CommerceDate' => sap_date(now())
                  );

                  if( ! $this->ci->transfer_model->add_sap_transfer_detail($arr))
                  {
                    $sc = FALSE;
                    $this->error = 'เพิ่มรายการไม่สำเร็จ';
                  }

                  $line++;
                }
              }
              else
              {
                $sc = FALSE;
                $this->error = "ไม่พบรายการสินค้า";
              }
            }
            else
            {
              $sc = FALSE;
              $this->error = "เพิ่มเอกสารไม่สำเร็จ";
            }

            if($sc === TRUE)
            {
              $this->ci->mc->trans_commit();
            }
            else
            {
              $this->ci->mc->trans_rollback();
            }
          }
        }
        else
        {
          $sc = FALSE;
          $this->error = "สถานะเอกสารไม่ถูกต้อง";
        }
      }
      else
      {
        $sc = FALSE;
        $this->error = "เอกสารถูกนำเข้า SAP แล้ว หากต้องการเปลี่ยนแปลงกรุณายกเลิกเอกสารใน SAP ก่อน";
      }
    }
    else
    {
      $sc = FALSE;
      $this->error = "ไม่พบเอกสาร {$code}";
    }

    $this->set_exported($code, $sc);

    return $sc;
  }
//--- end export transfer order


  //---- WT ฝากขายโอนคลัง
  //---- OWTR WTR1
  public function export_transfer_draft($code, $option = 'A')
  {
    $sc = TRUE;
    $this->ci->load->model('orders/orders_model');
    $this->ci->load->model('inventory/delivery_order_model');
    $this->ci->load->model('inventory/transfer_model');
    $this->ci->load->model('masters/customers_model');
    $this->ci->load->model('masters/products_model');
    $this->ci->load->model('masters/zone_model');
    $this->ci->load->helper('discount');

    $doc = $this->ci->orders_model->get($code);
    $sap = $this->ci->transfer_model->get_sap_transfer_doc($code);
    $zone = $this->ci->zone_model->get($doc->zone_code);

    if($doc->role == 'L' OR $doc->role == 'U' OR $doc->role == 'R')
    {
      $cust = new stdClass();
      $cust->code = NULL;
      $cust->name = NULL;
    }
    else
    {
      $cust = $this->ci->customers_model->get($doc->customer_code);
    }

    if(!empty($doc))
    {
      if(empty($sap))
      {
        if($doc->status == 1)
        {
          $middle = $this->ci->transfer_model->get_middle_transfer_draft($code);
          if(!empty($middle))
          {
            foreach($middle as $rows)
            {
              if($this->ci->transfer_model->drop_middle_transfer_draft($rows->DocEntry) === FALSE)
              {
                $sc = FALSE;
                $this->error = "ลบรายการที่ค้างใน temp ไม่สำเร็จ";
              }
            }
          }

          if($sc === TRUE)
          {
            $currency = getConfig('CURRENCY');
            $vat_rate = getConfig('SALE_VAT_RATE');
            $vat_code = getConfig('SALE_VAT_CODE');
            $date_add = getConfig('ORDER_SOLD_DATE') == 'D' ? $doc->date_add : (empty($doc->shipped_date) ? now() : $doc->shipped_date);
            $total_amount = $this->ci->orders_model->get_bill_total_amount($code);
            $ds = array(
            'U_ECOMNO' => $doc->code,
            'DocType' => 'I',
            'CANCELED' => 'N',
            'DocDate' => sap_date($date_add, TRUE),
            'DocDueDate' => sap_date($date_add, TRUE),
            'CardCode' => $cust->code,
            'CardName' => $cust->name,
            'VatPercent' => $vat_rate,
            'VatSum' => round(get_vat_amount($total_amount), 6),
            'VatSumFc' => round(get_vat_amount($total_amount), 6),
            'DiscPrcnt' => 0.000000,
            'DiscSum' => 0.000000,
            'DiscSumFC' => 0.000000,
            'DocCur' => $currency,
            'DocRate' => 1,
            'DocTotal' => remove_vat($total_amount, $vat_rate),
            'DocTotalFC' => remove_vat($total_amount, $vat_rate),
            'Filler' => empty($zone) ? NULL : $zone->warehouse_code,
            'ToWhsCode' => empty($zone) ? NULL : $zone->warehouse_code,
            'Comments' => limitText($doc->remark, 250),
            'F_E_Commerce' => $option,
            'F_E_CommerceDate' => sap_date(now(), TRUE),
            'U_BOOKCODE' => $doc->bookcode,
            'U_REQUESTER' => $doc->empName,
            'U_APPROVER' => $doc->approver,
            'F_Receipt' => ($doc->is_valid == 1 ? 'Y' : NULL)
            );

            $this->ci->mc->trans_begin();

            $docEntry = $this->ci->transfer_model->add_sap_transfer_draft($ds);

            if($docEntry)
            {
              $details = $this->ci->delivery_order_model->get_sold_details($code);

              if(!empty($details))
              {
                $line = 0;
                foreach($details as $rs)
                {
                  $arr = array(
                  'DocEntry' => $docEntry,
                  'U_ECOMNO' => $rs->reference,
                  'LineNum' => $line,
                  'ItemCode' => $rs->product_code,
                  'Dscription' => limitText($rs->product_name, 95),
                  'Quantity' => $rs->qty,
                  'unitMsr' => $this->ci->products_model->get_unit_code($rs->product_code),
                  'PriceBefDi' => round($rs->price,2),
                  'LineTotal' => round($rs->total_amount,2),
                  'ShipDate' => $date_add,
                  'Currency' => $currency,
                  'Rate' => 1,
                  //--- คำนวณส่วนลดจากยอดเงินกลับมาเป็น % (เพราะบางทีมีส่วนลดหลายชั้น)
                  'DiscPrcnt' => discountAmountToPercent($rs->discount_amount, $rs->qty, $rs->price), ///--- discount_helper
                  'Price' => round(remove_vat($rs->price, $vat_rate),2),
                  'TotalFrgn' => round($rs->total_amount,2),
                  'FromWhsCod' => $rs->warehouse_code,
                  'WhsCode' => empty($zone) ? NULL : $zone->warehouse_code,
                  'FisrtBin' => $doc->zone_code, //-- โซนปลายทาง
                  'F_FROM_BIN' => $rs->zone_code, //--- โซนต้นทาง
                  'F_TO_BIN' => $doc->zone_code, //--- โซนปลายทาง
                  'TaxStatus' => 'Y',
                  'VatPrcnt' => $vat_rate,
                  'VatGroup' => $vat_code,
                  'PriceAfVAT' => round($rs->sell,2),
                  'VatSum' => round(get_vat_amount($rs->total_amount),2),
                  'GTotal' => round($rs->total_amount, 2),
                  'TaxType' => 'Y',
                  'F_E_Commerce' => $option,
                  'F_E_CommerceDate' => sap_date(now())
                  );

                  if( ! $this->ci->transfer_model->add_sap_transfer_draft_detail($arr))
                  {
                    $sc = FALSE;
                    $this->error = 'เพิ่มรายการไม่สำเร็จ';
                  }

                  $line++;
                }
              }
              else
              {
                $sc = FALSE;
                $this->error = "ไม่พบรายการสินค้า";
              }
            }
            else
            {
              $sc = FALSE;
              $this->error = "เพิ่มเอกสารไม่สำเร็จ";
            }

            if($sc === TRUE)
            {
              $this->ci->mc->trans_commit();
            }
            else
            {
              $this->ci->mc->trans_rollback();
            }
          }

        }
        else
        {
          $sc = FALSE;
          $this->error = "สถานะเอกสารไม่ถูกต้อง";
        }
      }
      else
      {
        $sc = FALSE;
        $this->error = "เอกสารถูกนำเข้า SAP แล้ว หากต้องการเปลี่ยนแปลงกรุณายกเลิกเอกสารใน SAP ก่อน";
      }
    }
    else
    {
      $sc = FALSE;
      $this->error = "ไม่พบเอกสาร {$code}";
    }

    $this->set_exported($code, $sc);

    return $sc;
  }
  //--- end export transfer draf





  public function export_transfer($code, $option = 'A')
  {
    $sc = TRUE;
    $this->ci->load->model('inventory/transfer_model');
    $doc = $this->ci->transfer_model->get($code);
    $sap = $this->ci->transfer_model->get_sap_transfer_doc($code);

    if(!empty($doc))
    {
      if(empty($sap))
      {
        if($doc->status == 1)
        {
          //--- เช็คของเก่าก่อนว่ามีในถังกลางหรือยัง
          $middle = $this->ci->transfer_model->get_middle_transfer_doc($code);
          if(!empty($middle))
          {
            foreach($middle as $rows)
            {
              if($this->ci->transfer_model->drop_middle_exits_data($rows->DocEntry) === FALSE)
              {
                $sc = FALSE;
                $this->error = "ลบรายที่ค้างใน temp ไม่สำเร็จ";
              }
            }
          }

          if($sc === TRUE)
          {
            $currency = getConfig('CURRENCY');
            $vat_rate = getConfig('SALE_VAT_RATE');
            $vat_code = getConfig('SALE_VAT_CODE');
            $date_add = getConfig('ORDER_SOLD_DATE') == 'D' ? $doc->date_add : (empty($doc->shipped_date) ? now() : $doc->shipped_date);

            $ds = array(
            'U_ECOMNO' => $doc->code,
            'DocType' => 'I',
            'CANCELED' => 'N',
            'DocDate' => sap_date($date_add, TRUE),
            'DocDueDate' => sap_date($date_add, TRUE),
            'CardCode' => NULL,
            'CardName' => NULL,
            'VatPercent' => 0.000000,
            'VatSum' => 0.000000,
            'VatSumFc' => 0.000000,
            'DiscPrcnt' => 0.000000,
            'DiscSum' => 0.000000,
            'DiscSumFC' => 0.000000,
            'DocCur' => $currency,
            'DocRate' => 1,
            'DocTotal' => 0.000000,
            'DocTotalFC' => 0.000000,
            'Filler' => $doc->from_warehouse,
            'ToWhsCode' => $doc->to_warehouse,
            'Comments' => limitText($doc->remark, 250),
            'F_E_Commerce' => $option,
            'F_E_CommerceDate' => sap_date(now(), TRUE),
            'U_BOOKCODE' => $doc->bookcode
            );

            $this->ci->mc->trans_begin();

            $docEntry = $this->ci->transfer_model->add_sap_transfer_doc($ds);

            if($docEntry !== FALSE)
            {
              $details = $this->ci->transfer_model->get_details($code);

              if(!empty($details))
              {
                $line = 0;

                foreach($details as $rs)
                {
                  $qty = $rs->qty;

                  if($qty > 0)
                  {
                    $arr = array(
                    'DocEntry' => $docEntry,
                    'U_ECOMNO' => $rs->transfer_code,
                    'LineNum' => $line,
                    'ItemCode' => $rs->product_code,
                    'Dscription' => limitText($rs->product_name, 95),
                    'Quantity' => $qty, //($doc->api == 1 ? $rs->wms_qty : $rs->qty),
                    'unitMsr' => NULL,
                    'PriceBefDi' => 0.000000,
                    'LineTotal' => 0.000000,
                    'ShipDate' => sap_date($date_add, TRUE),
                    'Currency' => $currency,
                    'Rate' => 1,
                    'DiscPrcnt' => 0.000000,
                    'Price' => 0.000000,
                    'TotalFrgn' => 0.000000,
                    'FromWhsCod' => $doc->from_warehouse,
                    'WhsCode' => $doc->to_warehouse,
                    'FisrtBin' => $rs->from_zone,
                    'F_FROM_BIN' => $rs->from_zone,
                    'F_TO_BIN' => $rs->to_zone,
                    'AllocBinC' => $rs->to_zone,
                    'TaxStatus' => 'Y',
                    'VatPrcnt' => 0.000000,
                    'VatGroup' => NULL,
                    'PriceAfVAT' => 0.000000,
                    'VatSum' => 0.000000,
                    'TaxType' => 'Y',
                    'F_E_Commerce' => $option,
                    'F_E_CommerceDate' => sap_date(now(), TRUE)
                    );

                    if( ! $this->ci->transfer_model->add_sap_transfer_detail($arr))
                    {
                      $sc = FALSE;
                      $this->error = 'เพิ่มรายการไม่สำเร็จ';
                    }

                    $line++;
                  }
                }
              }
              else
              {
                $sc = FALSE;
                $this->error = "ไม่พบรายการสินค้า";
              }
            }
            else
            {
              $sc = FALSE;
              $this->error = "เพิ่มเอกสารไม่สำเร็จ";
            }

            if($sc === TRUE)
            {
              $this->ci->mc->trans_commit();
            }
            else
            {
              $this->ci->mc->trans_rollback();
            }
          }

        }
        else
        {
          $sc = FALSE;
          $this->error = "สถานะเอกสารไม่ถูกต้อง";
        }
      }
      else
      {
        $sc = FALSE;
        $this->error = "เอกสารถูกนำเข้า SAP แล้ว หากต้องการเปลี่ยนแปลงกรุณายกเลิกเอกสารใน SAP ก่อน";
      }

    }
    else
    {
      $sc = FALSE;
      $this->error = "ไม่พบเอกสาร {$code}";
    }

    return $sc;
  }



  //--- export move
  public function export_move($code, $option = 'A')
  {
    $sc = TRUE;
    $this->ci->load->model('inventory/move_model');
    $doc = $this->ci->move_model->get($code);
    $sap = $this->ci->move_model->get_sap_move_doc($code);

    if(!empty($doc))
    {
      if(empty($sap))
      {
        if($doc->status == 1)
        {
          //--- เช็คของเก่าก่อนว่ามีในถังกลางหรือยัง
          $middle = $this->ci->move_model->get_middle_move_doc($code);
          if(!empty($middle))
          {
            foreach($middle as $rows)
            {
              if($this->ci->move_model->drop_middle_exits_data($rows->DocEntry) === FALSE)
              {
                $sc = FALSE;
                $this->error = "ลบรายที่ค้างใน temp ไม่สำเร็จ";
              }
            }
          }

          if($sc === TRUE)
          {
            $currency = getConfig('CURRENCY');
            $vat_rate = getConfig('SALE_VAT_RATE');
            $vat_code = getConfig('SALE_VAT_CODE');
            $date_add = getConfig('ORDER_SOLD_DATE') == 'D' ? $doc->date_add : (empty($doc->shipped_date) ? now() : $doc->shipped_date);

            $ds = array(
            'U_ECOMNO' => $doc->code,
            'DocType' => 'I',
            'CANCELED' => 'N',
            'DocDate' => sap_date($date_add),
            'DocDueDate' => sap_date($date_add),
            'CardCode' => NULL,
            'CardName' => NULL,
            'VatPercent' => 0.000000,
            'VatSum' => 0.000000,
            'VatSumFc' => 0.000000,
            'DiscPrcnt' => 0.000000,
            'DiscSum' => 0.000000,
            'DiscSumFC' => 0.000000,
            'DocCur' => $currency,
            'DocRate' => 1,
            'DocTotal' => 0.000000,
            'DocTotalFC' => 0.000000,
            'Filler' => $doc->from_warehouse,
            'ToWhsCode' => $doc->to_warehouse,
            'Comments' => limitText($doc->remark, 250),
            'F_E_Commerce' => $option ,
            'F_E_CommerceDate' => sap_date(now(), TRUE),
            'U_BOOKCODE' => $doc->bookcode
            );

            $this->ci->mc->trans_begin();

            $docEntry = $this->ci->move_model->add_sap_move_doc($ds);

            if($docEntry !== FALSE)
            {
              $details = $this->ci->move_model->get_details($code);

              if(!empty($details))
              {
                $line = 0;
                foreach($details as $rs)
                {
                  $arr = array(
                  'DocEntry' => $docEntry,
                  'U_ECOMNO' => $rs->move_code,
                  'LineNum' => $line,
                  'ItemCode' => $rs->product_code,
                  'Dscription' => limitText($rs->product_name, 95),
                  'Quantity' => $rs->qty,
                  'unitMsr' => NULL,
                  'PriceBefDi' => 0.000000,
                  'LineTotal' => 0.000000,
                  'ShipDate' => $date_add,
                  'Currency' => $currency,
                  'Rate' => 1,
                  'DiscPrcnt' => 0.000000,
                  'Price' => 0.000000,
                  'TotalFrgn' => 0.000000,
                  'FromWhsCod' => $doc->from_warehouse,
                  'WhsCode' => $doc->to_warehouse,
                  'F_FROM_BIN' => $rs->from_zone,
                  'F_TO_BIN' => $rs->to_zone,
                  'TaxStatus' => 'Y',
                  'VatPrcnt' => 0.000000,
                  'VatGroup' => NULL,
                  'PriceAfVAT' => 0.000000,
                  'VatSum' => 0.000000,
                  'TaxType' => 'Y',
                  'F_E_Commerce' => $option,
                  'F_E_CommerceDate' => sap_date(now(), TRUE)
                  );

                  if( ! $this->ci->move_model->add_sap_move_detail($arr))
                  {
                    $sc = FALSE;
                    $this->error = 'เพิ่มรายการไม่สำเร็จ';
                  }

                  $line++;
                }

                if($sc === TRUE)
                {
                  //---- set exported = 1
                  $this->ci->move_model->exported($doc->code);
                }
              }
              else
              {
                $sc = FALSE;
                $this->error = "ไม่พบรายการสินค้า";
              }
            }
            else
            {
              $sc = FALSE;
              $this->error = "เพิ่มเอกสารไม่สำเร็จ";
            }

            if($sc === TRUE)
            {
              $this->ci->mc->trans_commit();
            }
            else
            {
              $this->ci->mc->trans_rollback();
            }
          }
        }
        else
        {
          $sc = FALSE;
          $this->error = "สถานะเอกสารไม่ถูกต้อง";
        }
      }
      else
      {
        $sc = FALSE;
        $this->error = "เอกสารถูกนำเข้า SAP แล้ว หากต้องการเปลี่ยนแปลงกรุณายกเลิกเอกสารใน SAP ก่อน";
      }

    }
    else
    {
      $sc = FALSE;
      $this->error = "ไม่พบเอกสาร {$code}";
    }

    return $sc;
  }


  public function export_transform($code, $option = 'A')
  {
    $sc = TRUE;
    $this->ci->load->model('orders/orders_model');
    $this->ci->load->model('inventory/delivery_order_model');
    $this->ci->load->model('inventory/transfer_model');
    $this->ci->load->model('masters/customers_model');
    $this->ci->load->model('masters/products_model');
    $this->ci->load->model('masters/zone_model');
    $this->ci->load->helper('discount');

    $doc = $this->ci->orders_model->get($code);
    $sap = $this->ci->transfer_model->get_sap_transfer_doc($code);
    $cust = $this->ci->customers_model->get($doc->customer_code);

    if(!empty($doc))
    {
      if(empty($sap))
      {
        if($doc->status == 1)
        {
          $middle = $this->ci->transfer_model->get_middle_transfer_doc($code);
          if(!empty($middle))
          {
            foreach($middle as $rows)
            {
              if($this->ci->transfer_model->drop_middle_exits_data($rows->DocEntry) === FALSE)
              {
                $sc = FALSE;
                $this->error = "ลบรายที่ค้างใน temp ไม่สำเร็จ";
              }
            }
          }

          if($sc === TRUE)
          {
            $transform_warehouse = getConfig('TRANSFORM_WAREHOUSE');
            $currency = getConfig('CURRENCY');
            $vat_rate = getConfig('SALE_VAT_RATE');
            $vat_code = getConfig('SALE_VAT_CODE');
            $date_add = getConfig('ORDER_SOLD_DATE') == 'D' ? $doc->date_add : (empty($doc->shipped_date) ? now() : $doc->shipped_date);
            $total_amount = $this->ci->orders_model->get_bill_total_amount($code);

            $ds = array(
            'U_ECOMNO' => $doc->code,
            'DocType' => 'I',
            'CANCELED' => 'N',
            'DocDate' => sap_date($date_add, TRUE),
            'DocDueDate' => sap_date($date_add,TRUE),
            'CardCode' => $cust->code,
            'CardName' => $cust->name,
            'VatPercent' => $vat_rate,
            'VatSum' => get_vat_amount($total_amount),
            'VatSumFc' => get_vat_amount($total_amount),
            'DiscPrcnt' => 0.000000,
            'DiscSum' => 0.000000,
            'DiscSumFC' => 0.000000,
            'DocCur' => $currency,
            'DocRate' => 1,
            'DocTotal' => remove_vat($total_amount, $vat_rate),
            'DocTotalFC' => remove_vat($total_amount, $vat_rate),
            'Filler' => $doc->warehouse_code,
            'ToWhsCode' => $transform_warehouse,
            'Comments' => limitText($doc->remark, 250),
            'F_E_Commerce' => $option,
            'F_E_CommerceDate' => sap_date(now(), TRUE),
            'U_BOOKCODE' => $doc->bookcode,
            'U_REQUESTER' => $doc->user,
            'U_APPROVER' => $doc->approver
            );

            $this->ci->mc->trans_begin();
            $docEntry = $this->ci->transfer_model->add_sap_transfer_doc($ds);

            if($docEntry)
            {
              $details = $this->ci->delivery_order_model->get_sold_details($code);

              if(!empty($details))
              {
                $line = 0;
                foreach($details as $rs)
                {
                  $arr = array(
                  'DocEntry' => $docEntry,
                  'U_ECOMNO' => $rs->reference,
                  'LineNum' => $line,
                  'ItemCode' => $rs->product_code,
                  'Dscription' => limitText($rs->product_name, 95),
                  'Quantity' => $rs->qty,
                  'unitMsr' => $this->ci->products_model->get_unit_code($rs->product_code),
                  'PriceBefDi' => round($rs->price,2),
                  'LineTotal' => round($rs->total_amount,2),
                  'ShipDate' => $date_add,
                  'Currency' => $currency,
                  'Rate' => 1,
                  //--- คำนวณส่วนลดจากยอดเงินกลับมาเป็น % (เพราะบางทีมีส่วนลดหลายชั้น)
                  'DiscPrcnt' => discountAmountToPercent($rs->discount_amount, $rs->qty, $rs->price), ///--- discount_helper
                  'Price' => round(remove_vat($rs->price, $vat_rate),2),
                  'TotalFrgn' => round($rs->total_amount,2),
                  'FromWhsCod' => $rs->warehouse_code,
                  'WhsCode' => $transform_warehouse,
                  'FisrtBin' => $doc->zone_code, //--- zone ปลายทาง
                  'F_FROM_BIN' => $rs->zone_code, //--- โซนต้นทาง
                  'F_TO_BIN' => $doc->zone_code, //--- โซนปลายทาง
                  'TaxStatus' => 'Y',
                  'VatPrcnt' => $vat_rate,
                  'VatGroup' => $vat_code,
                  'PriceAfVAT' => round($rs->sell, 2),
                  'VatSum' => round(get_vat_amount($rs->total_amount),2),
                  'GTotal' => round($rs->total_amount, 2),
                  'TaxType' => 'Y',
                  'F_E_Commerce' => $option,
                  'F_E_CommerceDate' => sap_date(now(), TRUE)
                  );

                  if( ! $this->ci->transfer_model->add_sap_transfer_detail($arr))
                  {
                    $sc = FALSE;
                    $this->error = 'เพิ่มรายการไม่สำเร็จ';
                  }

                  $line++;
                }
              }
              else
              {
                $sc = FALSE;
                $this->error = "ไม่พบรายการสินค้า";
              }
            }
            else
            {
              $sc = FALSE;
              $this->error = "เพิ่มเอกสารไม่สำเร็จ";
            }

            if($sc === TRUE)
            {
              $this->ci->mc->trans_commit();
            }
            else
            {
              $this->ci->mc->trans_rollback();
            }
          }

        }
        else
        {
          $sc = FALSE;
          $this->error = "สถานะเอกสารไม่ถูกต้อง";
        }
      }
      else
      {
        $sc = FALSE;
        $this->error = "เอกสารถูกนำเข้า SAP แล้ว หากต้องการเปลี่ยนแปลงกรุณายกเลิกเอกสารใน SAP ก่อน";
      }

    }
    else
    {
      $sc = FALSE;
      $this->error = "ไม่พบเอกสาร {$code}";
    }

    $this->set_exported($code, $sc);

    return $sc;
  }


  //--- Receive PO
  //--- OPDN PDN1
  public function export_receive($code, $option = 'A')
  {
    $sc = TRUE;
    $this->ci->load->model('inventory/receive_po_model');
    $this->ci->load->model('masters/products_model');
    $doc = $this->ci->receive_po_model->get($code);
    $sap = $this->ci->receive_po_model->get_sap_receive_doc($code);

    if(!empty($doc))
    {
      if(empty($sap))
      {
        if($doc->status == 1)
        {
          //---- ถ้ามีรายการที่ยังไม่ได้ถูกเอาเข้า SAP ให้ลบรายการนั้นออกก่อน(SAP เอาเข้าซ้ำไม่ได้)
          $middle = $this->ci->receive_po_model->get_middle_receive_po($code);
          if(!empty($middle))
          {
            //--- Delete exists details
            foreach($middle as $rows)
            {
              if($this->ci->receive_po_model->drop_sap_received($rows->DocEntry) === FALSE)
              {
                $sc = FALSE;
                $this->error = "ลบรายการที่ค้างใน Temp ไม่สำเร็จ";
              }
            }
          }

          //--- หลังจากเคลียร์รายการค้างออกหมดแล้ว
          if($sc === TRUE)
          {
            $currency = $doc->currency;
            $rate = $doc->rate;
            //--- get Currency, VatGroup And VatPrcnt From SAP => POR1
            $po_data = $this->ci->receive_po_model->get_po_data($doc->po_code);
            if(!empty($po_data))
            {
              $vat_code = $po_data->VatGroup;
              $vat_rate = $po_data->VatPrcnt;
              $currency = $po_data->Currency;
            }
            else
            {
              $vat_code = getConfig('PURCHASE_VAT_CODE');
              $vat_rate = getConfig('PURCHASE_VAT_RATE');
              $currency = getConfig('CURRENCY');
            }

            $date_add = getConfig('ORDER_SOLD_DATE') == 'D' ? $doc->date_add : (empty($doc->shipped_date) ? now() : $doc->shipped_date);

            $total_amount = $this->ci->receive_po_model->get_sum_amount($code);

            $ds = array(
            'U_ECOMNO' => $doc->code,
            'DocType' => 'I',
            'CANCELED' => 'N',
            'DocDate' => sap_date($date_add, TRUE),
            'DocDueDate' => sap_date($date_add,TRUE),
            'CardCode' => $doc->vendor_code,
            'CardName' => $doc->vendor_name,
            'NumAtCard' => $doc->invoice_code,
            'VatPercent' => $vat_rate,
            'VatSum' => $doc->VatSum,
            'VatSumFc' => $doc->VatSum,
            'DiscPrcnt' => $doc->DiscPrcnt,
            'DiscSum' => $doc->DiscAmount,
            'DiscSumFC' => $doc->DiscAmount,
            'DocCur' => $currency,
            'DocRate' => $rate,
            'DocTotal' => $doc->DocTotal,
            'DocTotalFC' => $doc->DocTotal,
            'ToWhsCode' => $doc->warehouse_code,
            'Comments' => limitText($doc->remark, 250),
            'F_E_Commerce' => $option,
            'F_E_CommerceDate' => sap_date(now(),TRUE)
            );

            $this->ci->mc->trans_begin();

            $docEntry = $this->ci->receive_po_model->add_sap_receive_po($ds);


            if($docEntry !== FALSE)
            {
              $details = $this->ci->receive_po_model->get_details($code);

              if(!empty($details))
              {
                $line = 0;
                foreach($details as $rs)
                {
                  if($rs->receive_qty > 0)
                  {
                    $arr = array(
                    'DocEntry' => $docEntry,
                    'U_ECOMNO' => $rs->receive_code,
                    'LineNum' => $line,
                    'BaseEntry' => $rs->baseEntry,
                    'BaseLine' => $rs->baseLine,
                    'ItemCode' => $rs->product_code,
                    'Dscription' => limitText($rs->product_name, 95),
                    'Quantity' => $rs->receive_qty,
                    'unitMsr' => empty($rs->unitMsr) ? $rs->unit_code : $rs->unitMsr,
                    'NumPerMsr' => $rs->NumPerMsr,
                    'unitMsr2' => $rs->unitMsr2,
                    'NumPerMsr2' => $rs->NumPerMsr2,
                    'UomEntry' => $rs->UomEntry,
                    'UomEntry2' => $rs->UomEntry2,
                    'UomCode' => $rs->UomCode,
                    'UomCode2' => $rs->UomCode2,
                    'PriceBefDi' => round($rs->PriceBefDi, 3),
                    'LineTotal' => $rs->amount,
                    'ShipDate' => sap_date($date_add,TRUE),
                    'Currency' => $rs->currency,
                    'Rate' => $rs->rate,
                    'Price' => $rs->price,
                    'DiscPrcnt' => $rs->DiscPrcnt,
                    'TotalFrgn' => $rs->price,
                    'WhsCode' => $doc->warehouse_code,
                    'FisrtBin' => $doc->zone_code,
                    'BaseRef' => $doc->po_code,
                    'TaxStatus' => 'Y',
                    'VatPrcnt' => $rs->vatRate,
                    'VatGroup' => $rs->vatGroup,
                    'PriceAfVAT' => round(add_vat($rs->price, $rs->vatRate), 3),
                    'VatSum' => $rs->vatAmount,
                    'TaxType' => 'Y',
                    'F_E_Commerce' => $option,
                    'F_E_CommerceDate' => sap_date(now(), TRUE)
                    );

                    if( ! $this->ci->receive_po_model->add_sap_receive_po_detail($arr))
                    {
                      $sc = FALSE;
                      $this->error = 'เพิ่มรายการไม่สำเร็จ';
                    }

                    $line++;
                  }
                }
              }
              else
              {
                $sc = FALSE;
                $this->error = "ไม่พบรายการสินค้า";
              }
            }
            else
            {
              $sc = FALSE;
              $this->error = "เพิ่มเอกสารไม่สำเร็จ";
            }

            if($sc === TRUE)
            {
              $this->ci->mc->trans_commit();
            }
            else
            {
              $this->ci->mc->trans_rollback();
            }
          }
        }
        else
        {
          $sc = FALSE;
          $this->error = "สถานะเอกสารไม่ถูกต้อง";
        }
      }
      else
      {
        $sc = FALSE;
        $this->error = "เอกสารถูกนำเข้า SAP แล้ว หากต้องการเปลี่ยนแปลงกรุณายกเลิกเอกสารใน SAP ก่อน";
      }
    }
    else
    {
      $sc = FALSE;
      $this->error = "ไม่พบเอกสาร {$code}";
    }

    return $sc;
  }
  //--- end export Receive PO



  //---- receive transform
  //--- OIGN
  public function export_receive_transform($code, $option = 'A')
  {
    $sc = TRUE;
    $this->ci->load->model('inventory/receive_transform_model');
    $this->ci->load->model('masters/products_model');
    $doc = $this->ci->receive_transform_model->get($code);
    $sap = $this->ci->receive_transform_model->get_sap_receive_transform($code);

    if(!empty($doc))
    {
      if(empty($sap))
      {
        if($doc->status == 1)
        {
          $middle = $this->ci->receive_transform_model->get_middle_receive_transform($code);
          if(!empty($middle))
          {
            foreach($middle as $rows)
            {
              if($this->ci->receive_transform_model->drop_middle_exits_data($rows->DocEntry) === FALSE)
              {
                $sc = FALSE;
                $this->error = "ลบรายการที่ค้างใน temp ไม่สำเร็จ";
              }
            }
          }

          if($sc === TRUE)
          {
            $currency = getConfig('CURRENCY');
            $vat_rate = getConfig('PURCHASE_VAT_RATE');
            $vat_code = getConfig('PURCHASE_VAT_CODE');
            $date_add = getConfig('ORDER_SOLD_DATE') == 'D' ? $doc->date_add : (empty($doc->shipped_date) ? now() : $doc->shipped_date);
            $total_amount = $this->ci->receive_transform_model->get_sum_amount($code);

            $ds = array(
            'U_ECOMNO' => $doc->code,
            'DocType' => 'I',
            'CANCELED' => 'N',
            'DocDate' => $date_add,
            'DocDueDate' => $date_add,
            'DocCur' => $currency,
            'DocRate' => 1,
            'DocTotal' => remove_vat($total_amount, $vat_rate),
            'Comments' => limitText($doc->remark, 250),
            'U_PDNO' => $doc->order_code,
            'F_E_Commerce' => $option,
            'F_E_CommerceDate' => now()
            );

            $this->ci->mc->trans_begin();

            $docEntry = $this->ci->receive_transform_model->add_sap_receive_transform($ds);

            if($docEntry !== FALSE)
            {

              $details = $this->ci->receive_transform_model->get_details($code);

              if(!empty($details))
              {
                $line = 0;
                foreach($details as $rs)
                {
                  if($rs->receive_qty > 0)
                  {
                    $arr = array(
                    'DocEntry' => $docEntry,
                    'U_ECOMNO' => $rs->receive_code,
                    'LineNum' => $line,
                    'ItemCode' => $rs->product_code,
                    'Dscription' => limitText($rs->product_name, 95),
                    'Quantity' => $rs->receive_qty,
                    'unitMsr' => $this->ci->products_model->get_unit_code($rs->product_code),
                    'PriceBefDi' => round($rs->price,2),
                    'LineTotal' => round(remove_vat($rs->amount, $vat_rate), 2),
                    'ShipDate' => $date_add,
                    'Currency' => $currency,
                    'Rate' => 1,
                    'Price' => round(remove_vat($rs->price, $vat_rate), 2),
                    'TotalFrgn' => round($rs->amount, 2),
                    'WhsCode' => $doc->warehouse_code,
                    'FisrtBin' => $doc->zone_code,
                    'BaseRef' => $doc->order_code,
                    'TaxStatus' => 'Y',
                    'VatPrcnt' => $vat_rate,
                    'VatGroup' => $vat_code,
                    'PriceAfVAT' => $rs->price,
                    'VatSum' => round(get_vat_amount($rs->amount), 2),
                    'GTotal' => round(remove_vat($rs->amount, $vat_rate), 2),
                    'AcctCode' => '115020',
                    'TaxType' => 'Y',
                    'F_E_Commerce' => $option,
                    'F_E_CommerceDate' => now()
                    );

                    if( ! $this->ci->receive_transform_model->add_sap_receive_transform_detail($arr))
                    {
                      $sc = FALSE;
                      $this->error = 'เพิ่มรายการไม่สำเร็จ';
                    }

                    $line++;
                  }
                }
              }
              else
              {
                $sc = FALSE;
                $this->error = "ไม่พบรายการสินค้า";
              }
            }
            else
            {
              $sc = FALSE;
              $this->error = "เพิ่มเอกสารไม่สำเร็จ";
            }

            if($sc === TRUE)
            {
              $this->ci->mc->trans_commit();
            }
            else
            {
              $this->ci->mc->trans_rollback();
            }
          }

        }
        else
        {
          $sc = FALSE;
          $this->error = "สถานะเอกสารไม่ถูกต้อง";
        }
      }
      else
      {
        $sc = FALSE;
        $this->error = "เอกสารถูกนำเข้า SAP แล้ว หากต้องการเปลี่ยนแปลงกรุณายกเลิกเอกสารใน SAP ก่อน";
      }
    }
    else
    {
      $sc = FALSE;
      $this->error = "ไม่พบเอกสาร {$code}";
    }

    return $sc;
  }
  //--- end export receive transform



  //---- export return order
  //----
  public function export_return($code, $option = 'A')
  {
    $sc = TRUE;
    $this->ci->load->model('inventory/return_order_model');
    $this->ci->load->model('masters/customers_model');
    $this->ci->load->model('masters/products_model');
    $doc = $this->ci->return_order_model->get($code);
    $cust = $this->ci->customers_model->get($doc->customer_code);
    $or = $this->ci->return_order_model->get_sap_return_order($code);
    if(!empty($doc))
    {
      if(empty($or))
      {
        if($doc->is_approve == 1 && $doc->status == 1)
        {
          $middle = $this->ci->return_order_model->get_middle_return_doc($code);
          if(!empty($middle))
          {
            foreach($middle as $rows)
            {
              if($this->ci->return_order_model->drop_middle_exits_data($rows->DocEntry) === FALSE)
              {
                $sc = FALSE;
                $this->error = "ลบรายการที่ค้างใน temp ไม่สำเร็จ";
              }
            }
          }

          if($sc === TRUE)
          {
            $currency = getConfig('CURRENCY');
            $vat_rate = getConfig('SALE_VAT_RATE');
            $vat_code = getConfig('SALE_VAT_CODE');
            $date_add = getConfig('ORDER_SOLD_DATE') == 'D' ? $doc->date_add : (empty($doc->shipped_date) ? now() : $doc->shipped_date);
            $total_amount = $this->ci->return_order_model->get_total_return($code);

            $ds = array(
            'DocType' => 'I',
            'CANCELED' => 'N',
            'DocDate' => $date_add,
            'DocDueDate' => $date_add,
            'CardCode' => $cust->code,
            'CardName' => $cust->name,
            'VatSum' => $this->ci->return_order_model->get_total_return_vat($code),
            'DocCur' => $currency,
            'DocRate' => 1,
            'DocTotal' => $total_amount,
            'DocTotalFC' => $total_amount,
            'Comments' => limitText($doc->remark, 250),
            'GroupNum' => $cust->GroupNum,
            'SlpCode' => $cust->sale_code,
            'ToWhsCode' => $doc->warehouse_code,
            'U_ECOMNO' => $doc->code,
            'U_BOOKCODE' => $doc->bookcode,
            'F_E_Commerce' => $option,
            'F_E_CommerceDate' => now(),
            'U_OLDINV' => $doc->invoice
            );

            $this->ci->mc->trans_begin();

            $docEntry = $this->ci->return_order_model->add_sap_return_order($ds);

            if($docEntry !== FALSE)
            {
              $details = $this->ci->return_order_model->get_details($code);

              if( ! empty($details))
              {
                $line = 0;
                //--- insert detail to RDN1
                foreach($details as $rs)
                {
                  if($rs->receive_qty > 0)
                  {
                    $arr = array(
                    'DocEntry' => $docEntry,
                    'U_ECOMNO' => $rs->return_code,
                    'LineNum' => $line,
                    'ItemCode' => $rs->product_code,
                    'Dscription' => limitText($rs->product_name, 95),
                    'Quantity' => $rs->receive_qty,
                    'unitMsr' => $this->ci->products_model->get_unit_code($rs->product_code),
                    'PriceBefDi' => remove_vat($rs->price, $vat_rate),
                    'LineTotal' => remove_vat($rs->amount, $vat_rate),
                    'ShipDate' => $date_add,
                    'Currency' => $currency,
                    'Rate' => 1,
                    'DiscPrcnt' => $rs->discount_percent,
                    'Price' => remove_vat($rs->price, $vat_rate),
                    'TotalFrgn' => remove_vat($rs->amount, $vat_rate),
                    'WhsCode' => $doc->warehouse_code,
                    'BinCode' => $doc->zone_code,
                    'FisrtBin' => $doc->zone_code,
                    'TaxStatus' => 'Y',
                    'VatPrcnt' => $vat_rate,
                    'VatGroup' => $vat_code,
                    'PriceAfVAT' => $rs->price,
                    'VatSum' => $rs->vat_amount,
                    'GTotal' => round($rs->amount, 2),
                    'TaxType' => 'Y',
                    'F_E_Commerce' => $option,
                    'F_E_CommerceDate' => now(),
                    'U_OLDINV' => $rs->invoice_code
                    );

                    if( ! $this->ci->return_order_model->add_sap_return_detail($arr))
                    {
                      $sc = FALSE;
                      $this->error = 'เพิ่มรายการไม่สำเร็จ';
                    }

                    $line++;
                  }

                }
              }
              else
              {
                $sc = FALSE;
                $this->error = "ไม่พบรายการรับคืน";
              }
            }
            else
            {
              $sc = FALSE;
              $this->error = "เพิ่มเอกสารไม่สำเร็จ";
            }



            if($sc === TRUE)
            {
              $this->ci->mc->trans_commit();
            }
            else
            {
              $this->ci->mc->trans_rollback();
            }
          }

        }
        else
        {
          $sc = FALSE;
          $this->error = "{$code} ยังไม่ได้รับการอนุมัติ";
        }
      }
      else
      {
        $sc = FALSE;
        $this->error = "เอกสารถูกนำเข้า SAP แล้ว หากต้องการเปลี่ยนแปลงกรุณายกเลิกเอกสารใน SAP ก่อน";
      }

    }
    else
    {
      $sc = FALSE;
      $this->error = "ไม่พบเอกสาร {$code}";
    }

    return $sc;
  }



  //---- export return consignment
  //---- CNORDN, CNRDN1
  public function export_return_consignment($code)
  {
    $sc = TRUE;
    $this->ci->load->model('inventory/return_consignment_model');
    $this->ci->load->model('masters/customers_model');
    $this->ci->load->model('masters/products_model');
    $this->ci->load->helper('return_consignment');
    $doc = $this->ci->return_consignment_model->get($code);
    $cust = $this->ci->customers_model->get($doc->customer_code);
    $or = $this->ci->return_consignment_model->get_sap_return_consignment($code);
    if(!empty($doc))
    {
      if(empty($or))
      {
        if($doc->is_approve == 1 && $doc->status == 1)
        {
          $middle = $this->ci->return_consignment_model->get_middle_return_doc($code);
          if(!empty($middle))
          {
            foreach($middle as $rows)
            {
              if($this->ci->return_consignment_model->drop_middle_exits_data($rows->DocEntry) === FALSE)
              {
                $sc = FALSE;
                $this->error = "ลบรายการที่ค้างใน temp ไม่สำเร็จ";
              }
            }
          }

          if($sc === TRUE)
          {
            $currency = getConfig('CURRENCY');
            $vat_rate = getConfig('SALE_VAT_RATE');
            $vat_code = getConfig('SALE_VAT_CODE');
            $date_add = getConfig('ORDER_SOLD_DATE') == 'D' ? $doc->date_add : (empty($doc->shipped_date) ? now() : $doc->shipped_date);
            $total_amount = $this->ci->return_consignment_model->get_total_return($code);
            //$invoice = $this->ci->return_consignment_model->get_all_invoice($code);

            $ds = array(
            'DocType' => 'I',
            'CANCELED' => 'N',
            'DocDate' => $date_add,
            'DocDueDate' => $date_add,
            'CardCode' => $cust->code,
            'CardName' => $cust->name,
            'VatSum' => $this->ci->return_consignment_model->get_total_return_vat($code),
            'DocCur' => $currency,
            'DocRate' => 1,
            'DocTotal' => $total_amount,
            'DocTotalFC' => $total_amount,
            'Comments' => limitText($doc->remark, 250),
            'GroupNum' => $cust->GroupNum,
            'SlpCode' => $cust->sale_code,
            'ToWhsCode' => $doc->warehouse_code,
            'U_ECOMNO' => $doc->code,
            'U_BOOKCODE' => $doc->bookcode,
            'F_E_Commerce' => 'A',
            'F_E_CommerceDate' => now(),
            'U_OLDINV' => $doc->invoice //getAllInvoiceText($invoice)
            );

            $this->ci->mc->trans_begin();

            $docEntry = $this->ci->return_consignment_model->add_sap_return_consignment($ds);

            if($docEntry !== FALSE)
            {
              $details = $this->ci->return_consignment_model->get_details($code);

              if( ! empty($details))
              {
                $line = 0;
                //--- insert detail to RDN1
                foreach($details as $rs)
                {
                  if($rs->receive_qty > 0)
                  {
                    $arr = array(
                    'DocEntry' => $docEntry,
                    'U_ECOMNO' => $rs->return_code,
                    'LineNum' => $line,
                    'ItemCode' => $rs->product_code,
                    'Dscription' => limitText($rs->product_name, 95),
                    'Quantity' => $rs->receive_qty,
                    'unitMsr' => $rs->unit_code,
                    'PriceBefDi' => remove_vat($rs->price, $vat_rate),
                    'LineTotal' => remove_vat($rs->amount, $vat_rate),
                    'ShipDate' => $date_add,
                    'Currency' => $currency,
                    'Rate' => 1,
                    'DiscPrcnt' => $rs->discount_percent,
                    'Price' => remove_vat($rs->price, $vat_rate),
                    'TotalFrgn' => remove_vat($rs->amount, $vat_rate),
                    'WhsCode' => $doc->warehouse_code,
                    'BinCode' => $doc->zone_code,
                    'FisrtBin' => $doc->zone_code,
                    'TaxStatus' => 'Y',
                    'VatPrcnt' => $vat_rate,
                    'VatGroup' => $vat_code,
                    'PriceAfVAT' => $rs->price,
                    'VatSum' => $rs->vat_amount,
                    'TaxType' => 'Y',
                    'F_E_Commerce' => 'A',
                    'F_E_CommerceDate' => now(),
                    'U_OLDINV' => $rs->invoice_code
                    );

                    if( ! $this->ci->return_consignment_model->add_sap_return_detail($arr))
                    {
                      $sc = FALSE;
                      $this->error = 'เพิ่มรายการไม่สำเร็จ';
                    }

                    $line++;
                  }
                }
              }
              else
              {
                $sc = FALSE;
                $this->error = "ไม่พบรายการรับคืน";
              }
            }
            else
            {
              $sc = FALSE;
              $this->error = "เพิ่มเอกสารไม่สำเร็จ";
            }



            if($sc === TRUE)
            {
              $this->ci->mc->trans_commit();
            }
            else
            {
              $this->ci->mc->trans_rollback();
            }
          }

        }
        else
        {
          $sc = FALSE;
          $this->error = "{$code} ยังไม่ได้รับการอนุมัติ";
        }
      }
      else
      {
        $sc = FALSE;
        $this->error = "เอกสารถูกนำเข้า SAP แล้ว หากต้องการเปลี่ยนแปลงกรุณายกเลิกเอกสารใน SAP ก่อน";
      }

    }
    else
    {
      $sc = FALSE;
      $this->error = "ไม่พบเอกสาร {$code}";
    }

    return $sc;
  }




  public function export_return_lend($code)
  {
    $sc = TRUE;
    $this->ci->load->model('inventory/return_lend_model');
    $this->ci->load->model('inventory/transfer_model');
    $this->ci->load->model('masters/products_model');
    $this->ci->load->model('masters/employee_model');

    $doc = $this->ci->return_lend_model->get($code);
    $sap = $this->ci->transfer_model->get_sap_transfer_doc($code);

    if(!empty($doc))
    {
      if(empty($sap))
      {
        if($doc->status == 1)
        {
          $middle = $this->ci->transfer_model->get_middle_transfer_doc($code);

          if(!empty($middle))
          {
            foreach($middle as $rows)
            {
              if($this->ci->transfer_model->drop_middle_exits_data($rows->DocEntry) === FALSE)
              {
                $sc = FALSE;
                $this->error = "ลบรายการที่ค้างใน temp ไม่สำเร็จ";
              }
            }
          }

          if($sc === TRUE)
          {
            $currency = getConfig('CURRENCY');
            $vat_rate = getConfig('SALE_VAT_RATE');
            $vat_code = getConfig('SALE_VAT_CODE');
            $date_add = getConfig('ORDER_SOLD_DATE') == 'D' ? $doc->date_add : (empty($doc->shipped_date) ? now() : $doc->shipped_date);
            $total_amount = $this->ci->return_lend_model->get_sum_amount($code);

            $ds = array(
            'U_ECOMNO' => $doc->code,
            'DocType' => 'I',
            'CANCELED' => 'N',
            'DocDate' => $date_add,
            'DocDueDate' => $date_add,
            'CardCode' => NULL,
            'CardName' => NULL,
            'VatPercent' => $vat_rate,
            'VatSum' => round(get_vat_amount($total_amount), 6),
            'VatSumFc' => round(get_vat_amount($total_amount), 6),
            'DiscPrcnt' => 0.000000,
            'DiscSum' => 0.000000,
            'DiscSumFC' => 0.000000,
            'DocCur' => $currency,
            'DocRate' => 1,
            'DocTotal' => remove_vat($total_amount, $vat_rate),
            'DocTotalFC' => remove_vat($total_amount, $vat_rate),
            'Filler' => $doc->from_warehouse,
            'ToWhsCode' => $doc->to_warehouse,
            'Comments' => limitText($doc->remark, 250),
            'F_E_Commerce' => 'A',
            'F_E_CommerceDate' => now(),
            'U_BOOKCODE' => $doc->bookcode,
            'U_REQUESTER' => $this->ci->employee_model->get_name($doc->empID)
            );

            $this->ci->mc->trans_begin();

            $docEntry = $this->ci->transfer_model->add_sap_transfer_doc($ds);


            if($docEntry !== FALSE)
            {

              $details = $this->ci->return_lend_model->get_details($code);

              if(!empty($details))
              {
                $line = 0;
                foreach($details as $rs)
                {
                  if($rs->receive_qty > 0)
                  {
                    $arr = array(
                    'DocEntry' => $docEntry,
                    'U_ECOMNO' => $rs->return_code,
                    'LineNum' => $line,
                    'ItemCode' => $rs->product_code,
                    'Dscription' => limitText($rs->product_name, 95),
                    'Quantity' => $rs->receive_qty,
                    'unitMsr' => $this->ci->products_model->get_unit_code($rs->product_code),
                    'PriceBefDi' => round(remove_vat($rs->price, $vat_rate),6),
                    'LineTotal' => round(remove_vat($rs->amount, $vat_rate),6),
                    'ShipDate' => $date_add,
                    'Currency' => $currency,
                    'Rate' => 1,
                    //--- คำนวณส่วนลดจากยอดเงินกลับมาเป็น % (เพราะบางทีมีส่วนลดหลายชั้น)
                    'DiscPrcnt' => 0.000000, ///--- discount_helper
                    'Price' => round(remove_vat($rs->price, $vat_rate),6),
                    'TotalFrgn' => round(remove_vat($rs->amount, $vat_rate),6),
                    'FromWhsCod' => $doc->from_warehouse,
                    'WhsCode' => $doc->to_warehouse,
                    'F_FROM_BIN' => $doc->from_zone, //-- โซนต้นทาง
                    'F_TO_BIN' => $doc->to_zone, //--- โซนปลายทาง
                    'TaxStatus' => 'Y',
                    'VatPrcnt' => $vat_rate,
                    'VatGroup' => $vat_code,
                    'PriceAfVAT' => $rs->price,
                    'VatSum' => round($rs->vat_amount,6),
                    'TaxType' => 'Y',
                    'F_E_Commerce' => 'A',
                    'F_E_CommerceDate' => now()
                    );

                    if( ! $this->ci->transfer_model->add_sap_transfer_detail($arr))
                    {
                      $sc = FALSE;
                      $this->error = 'เพิ่มรายการไม่สำเร็จ';
                    }

                    $line++;
                  }
                }
              }
              else
              {
                $sc = FALSE;
                $this->error = "ไม่พบรายการสินค้า";
              }
            }
            else
            {
              $sc = FALSE;
              $this->error = "เพิ่มเอกสารไม่สำเร็จ";
            }

            if($sc === TRUE)
            {
              $this->ci->mc->trans_commit();
            }
            else
            {
              $this->ci->mc->trans_rollback();
            }
          }

        }
        else
        {
          $sc = FALSE;
          $this->error = "สถานะเอกสารไม่ถูกต้อง";
        }
      }
      else
      {
        $sc = FALSE;
        $this->error = "เอกสารถูกนำเข้า SAP แล้ว หากต้องการเปลี่ยนแปลงกรุณายกเลิกเอกสารใน SAP ก่อน";
      }
    }
    else
    {
      $sc = FALSE;
      $this->error = "ไม่พบเอกสาร {$code}";
    }

    return $sc;
  }



  //---- ตัดยอดฝากขายห้าง (WD)
  //---- CNODLN CNDLN1
  //---- WD
  public function export_consignment_order($code)
  {
    $sc = TRUE;
    $this->ci->load->model('account/consignment_order_model');
    $this->ci->load->model('masters/products_model');
    $this->ci->load->helper('discount');

    $doc = $this->ci->consignment_order_model->get($code);
    $sap = $this->ci->consignment_order_model->get_sap_consignment_order_doc($code);
    if(! empty($doc))
    {
      if(empty($sap))
      {
        $middle = $this->ci->consignment_order_model->get_middle_consignment_order_doc($code);
        if(!empty($middle))
        {
          foreach($middle as $rows)
          {
            if($this->ci->consignment_order_model->drop_middle_exits_data($rows->DocEntry) === FALSE)
            {
              $sc = FALSE;
              $this->error = "ลบรายการที่ค้างใน Temp ไม่สำเร็จ";
            }
          }
        }


        if($sc === TRUE)
        {
          $currency = getConfig('CURRENCY');
          $vat_rate = getConfig('SALE_VAT_RATE');
          $vat_code = getConfig('SALE_VAT_CODE');
          $date_add = $doc->date_add;
          $doc_total = $this->ci->consignment_order_model->get_sum_amount($code);

          //--- header
          $ds = array(
          'U_ECOMNO' => $doc->code,
          'DocType' => 'I', //--- I = item, S = Service
          'CANCELED' => 'N', //--- Y = Yes, N = No
          'DocDate' => sap_date($date_add, TRUE), //--- วันที่เอกสาร
          'DocDueDate' => sap_date($date_add,TRUE), //--- วันที่เอกสาร
          'CardCode' => $doc->customer_code, //--- รหัสลูกค้า
          'CardName' => $doc->customer_name, //--- ชื่อลูกค้า
          'DocCur' => $currency,
          'DocRate' => 1.000000,
          'DocTotal' => round($doc_total, 2),
          'DocTotalFC' => $doc_total,
          'Comments' => limitText($doc->remark, 250),
          'U_BOOKCODE' => $doc->bookcode,
          'F_E_Commerce' => 'A',
          'F_E_CommerceDate' => sap_date(now(), TRUE)
          );


          $this->ci->mc->trans_begin();

          $docEntry = $this->ci->consignment_order_model->add_sap_doc($ds);

          //--- now add details
          if($docEntry !== FALSE)
          {
            $details = $this->ci->consignment_order_model->get_details($code);
            if(! empty($details))
            {
              $line = 0;
              foreach($details as $rs)
              {
                $arr = array(
                'DocEntry' => $docEntry,
                'U_ECOMNO' => $rs->consign_code,
                'LineNum' => $line,
                'ItemCode' => $rs->product_code,
                'Dscription' => limitText($rs->product_name, 95),
                'Quantity' => $rs->qty,
                'UnitMsr' => $this->ci->products_model->get_unit_code($rs->product_code),
                'PriceBefDi' => $rs->price,  //---มูลค่าต่อหน่วยก่อนภาษี/ก่อนส่วนลด
                'LineTotal' => $rs->amount,
                'Currency' => $currency,
                'Rate' => 1.000000,
                'DiscPrcnt' => discountAmountToPercent($rs->discount_amount, $rs->qty, $rs->price), ///--- discount_helper
                'Price' => remove_vat($rs->price, $vat_rate), //--- ราคา
                'TotalFrgn' => $rs->amount, //--- จำนวนเงินรวม By Line (Currency)
                'WhsCode' => $doc->warehouse_code,
                'BinCode' => $doc->zone_code,
                'TaxStatus' => 'Y',
                'VatPrcnt' => $vat_rate,
                'VatGroup' => $vat_code,
                'PriceAfVat' => $rs->price,
                'GTotal' => $rs->amount,
                'VatSum' => get_vat_amount($rs->amount), //---- tool_helper
                'TaxType' => 'Y', //--- คิดภาษีหรือไม่
                'F_E_Commerce' => 'A', //--- A = Add , U = Update
                'F_E_CommerceDate' => sap_date(now(), TRUE)
                );

                $this->ci->consignment_order_model->add_sap_detail_row($arr);
                $line++;
              }
            }
            else
            {
              $sc = FALSE;
              $this->error = "ไม่พบรายการสินค้า";
            }
          }
          else
          {
            $sc = FALSE;
            $this->error = "เพิ่มเอกสารไม่สำเร็จ";
          }

          if($sc === TRUE)
          {
            $this->ci->mc->trans_commit();
          }
          else
          {
            $this->ci->mc->trans_rollback();
          }
        }

      }
      else
      {
        $sc = FALSE;
        $this->error = "เอกสารถูกนำเข้า SAP แล้ว หากต้องการเปลี่ยนแปลงกรุณายกเลิกเอกสารใน SAP ก่อน";
      }
    }
    else
    {
      $sc = FALSE;
      $this->error = "ไม่พบเลขที่เอกสาร";
    }

    return $sc;
  }




  //---- Good issue
  //---- OIGE IGE1
  //---- Transform
  //---- WG
  public function export_transform_goods_issue($code)
  {
    $sc = TRUE;
    $this->ci->load->model('inventory/adjust_transform_model');
    $doc = $this->ci->adjust_transform_model->get($code);
    if(! empty($doc) && $doc->status == 1)
    {
      $sap = $this->ci->adjust_transform_model->get_sap_issue_doc($code);
      if(empty($sap))
      {
        $middle = $this->ci->adjust_transform_model->get_middle_goods_issue($code);
        if(!empty($middle))
        {
          foreach($middle as $rows)
          {
            if($this->ci->adjust_transform_model->drop_middle_issue_data($rows->DocEntry) === FALSE)
            {
              $sc = FALSE;
              $this->error = "ลบรายการที่ค้างใน Temp ไม่สำเร็จ";
            }
          }
        }

        if($sc === TRUE)
        {
          $details = $this->ci->adjust_transform_model->get_details($code);
          if(!empty($details))
          {
            $doc_total = 0;

            foreach($details as $row)
            {
              $doc_total += $row->qty * $row->cost;
            }

            $date_add = $doc->date_add;

            $arr = array(
              'U_ECOMNO' => $code,
              'DocType' => 'I',
              'CANCELED' => 'N',
              'DocDate' => sap_date($date_add),
              'DocDueDate' => sap_date($date_add),
              'DocTotal' => $doc_total,
              'DocTotalFC' => $doc_total,
              'U_PDNO' => $doc->reference,
              'Comments' => limitText($doc->remark, 250),
              'F_E_Commerce' => 'A',
              'F_E_CommerceDate' => sap_date(now(), TRUE)
              );

              $this->ci->mc->trans_begin();

              $docEntry = $this->ci->adjust_transform_model->add_sap_goods_issue($arr);

              //--- now add details
              if($docEntry !== FALSE)
              {
                $line = 0;
                foreach($details as $rs)
                {
                  if($sc === FALSE)
                  {
                    break;
                  }

                  $arr = array(
                  'DocEntry' => $docEntry,
                  'U_ECOMNO' => $rs->adjust_code,
                  'BaseRef' => $doc->reference,
                  'LineNum' => $line,
                  'ItemCode' => $rs->product_code,
                  'Dscription' => limitText($rs->product_name, 95),
                  'Quantity' => $rs->qty,
                  'WhsCode' => $doc->from_warehouse,
                  'FisrtBin' => $doc->from_zone,
                  'AcctCode' => '115020',
                  'DocDate' => sap_date($date_add),
                  'F_E_Commerce' => 'A',
                  'F_E_CommerceDate' => sap_date(now(), TRUE)
                  );

                  if(!$this->ci->adjust_transform_model->add_sap_goods_issue_row($arr))
                  {
                    $sc = FALSE;
                    $this->error = "Insert Goods Issue Temp Error at line {$line}, ItemCode : {$rs->product_code} ";
                  }

                  $line++;
                }
              }
              else
              {
                $sc = FALSE;
                $this->error = "เพิ่มเอกสารไม่สำเร็จ";
              }

              if($sc === TRUE)
              {
                $this->ci->mc->trans_commit();
              }
              else
              {
                $this->ci->mc->trans_rollback();
              }

            }

          }
        }
        else
        {
          $sc = FALSE;
          $this->error = "เอกสาร Goods Issue ถูกนำเข้า SAP แล้วไม่อนุญาติให้แก้ไข";
        }
      }
      else
      {
        $sc = FALSE;
        $this->error = "ไม่พบเลขที่เอกสาร หรือ สถานะเอกสารไม่ถูกต้อง";
      }

      return $sc;
    }


    //---- Good issue
    //---- OIGE IGE1
    //---- Adjust
    public function export_adjust_goods_issue($code)
    {
      $sc = TRUE;
      $this->ci->load->model('inventory/adjust_model');
      $doc = $this->ci->adjust_model->get($code);
      if(! empty($doc) && $doc->status == 1 && $doc->is_approved == 1)
      {
        $sap = $this->ci->adjust_model->get_sap_issue_doc($code);
        if(empty($sap))
        {
          $middle = $this->ci->adjust_model->get_middle_goods_issue($code);
          if(!empty($middle))
          {
            foreach($middle as $rows)
            {
              if($this->ci->adjust_model->drop_middle_issue_data($rows->DocEntry) === FALSE)
              {
                $sc = FALSE;
                $this->error = "ลบรายการที่ค้างใน Temp ไม่สำเร็จ";
              }
            }
          }

          if($sc === TRUE)
          {
            $details = $this->ci->adjust_model->get_issue_details($code);
            if(!empty($details))
            {
              $doc_total = 0;

              foreach($details as $row)
              {
                $row->qty = $row->qty * (-1);
                $doc_total += $row->qty * $row->cost;
              }

              $date_add = $doc->date_add;

              $arr = array(
              'U_ECOMNO' => $code,
              'DocType' => 'I',
              'CANCELED' => 'N',
              'DocDate' => sap_date($date_add),
              'DocDueDate' => sap_date($date_add),
              'DocTotal' => $doc_total,
              'DocTotalFC' => $doc_total,
              'Comments' => limitText($doc->remark, 250),
              'F_E_Commerce' => 'A',
              'F_E_CommerceDate' => sap_date(now(), TRUE)
              );

              $this->ci->mc->trans_begin();

              $docEntry = $this->ci->adjust_model->add_sap_goods_issue($arr);

              //--- now add details
              if($docEntry !== FALSE)
              {
                $line = 0;
                foreach($details as $rs)
                {
                  if($sc === FALSE)
                  {
                    break;
                  }

                  $arr = array(
                  'DocEntry' => $docEntry,
                  'U_ECOMNO' => $rs->adjust_code,
                  'LineNum' => $line,
                  'ItemCode' => $rs->product_code,
                  'Dscription' => limitText($rs->product_name, 95),
                  'Quantity' => $rs->qty,
                  'WhsCode' => $rs->warehouse_code,
                  'FisrtBin' => $rs->zone_code,
                  'DocDate' => sap_date($date_add),
                  'AcctCode' => '531030',
                  'F_E_Commerce' => 'A',
                  'F_E_CommerceDate' => sap_date(now(), TRUE)
                  );

                  if(!$this->ci->adjust_model->add_sap_goods_issue_row($arr))
                  {
                    $sc = FALSE;
                    $this->error = "Insert Goods Issue Temp Error at line {$line}, ItemCode : {$rs->product_code} ";
                  }

                  $line++;
                }
              }
              else
              {
                $sc = FALSE;
                $this->error = "เพิ่มเอกสารไม่สำเร็จ";
              }

              if($sc === TRUE)
              {
                $this->ci->mc->trans_commit();
              }
              else
              {
                $this->ci->mc->trans_rollback();
              }

            }

          }
        }
        else
        {
          $sc = FALSE;
          $this->error = "เอกสาร Goods Issue ถูกนำเข้า SAP แล้วไม่อนุญาติให้แก้ไข";
        }
      }
      else
      {
        $sc = FALSE;
        $this->error = "ไม่พบเลขที่เอกสาร หรือ สถานะเอกสารไม่ถูกต้อง";
      }

      return $sc;
    }



    //---- adjust goods receive
    //---- OIGN
    public function export_adjust_goods_receive($code)
    {
      $sc = TRUE;
      $this->ci->load->model('inventory/adjust_model');
      $this->ci->load->model('masters/products_model');
      $doc = $this->ci->adjust_model->get($code);

      if(!empty($doc) && $doc->status == 1 && $doc->is_approved == 1)
      {
        $sap = $this->ci->adjust_model->get_sap_receive_doc($code);
        if(empty($sap))
        {
          $middle = $this->ci->adjust_model->get_middle_goods_receive($code);
          if(!empty($middle))
          {
            foreach($middle as $rows)
            {
              if($this->ci->adjust_model->drop_middle_receive_data($rows->DocEntry) === FALSE)
              {
                $sc = FALSE;
                $this->error = "ลบรายการที่ค้างใน temp ไม่สำเร็จ";
              }
            }
          }

          if($sc === TRUE)
          {
            $details = $this->ci->adjust_model->get_receive_details($code);
            if(!empty($details))
            {
              $currency = getConfig('CURRENCY');
              $vat_rate = getConfig('PURCHASE_VAT_RATE');
              $vat_code = getConfig('PURCHASE_VAT_CODE');
              $date_add = $doc->date_add;
              $doc_total = 0;

              foreach($details as $row)
              {
                $doc_total += $row->qty * $row->cost;
              }

              $ds = array(
              'U_ECOMNO' => $doc->code,
              'DocType' => 'I',
              'CANCELED' => 'N',
              'DocDate' => $date_add,
              'DocDueDate' => $date_add,
              'DocCur' => $currency,
              'DocRate' => 1,
              'DocTotal' => remove_vat($doc_total, $vat_rate),
              'Comments' => limitText($doc->remark, 250),
              'F_E_Commerce' => 'A',
              'F_E_CommerceDate' => sap_date(now())
              );

              $this->ci->mc->trans_begin();

              $docEntry = $this->ci->adjust_model->add_sap_goods_receive($ds);

              if($docEntry !== FALSE)
              {
                $line = 0;

                foreach($details as $rs)
                {
                  if($sc === FALSE)
                  {
                    break;
                  }

                  $amount = $rs->qty * $rs->cost;

                  $arr = array(
                  'DocEntry' => $docEntry,
                  'U_ECOMNO' => $rs->adjust_code,
                  'LineNum' => $line,
                  'ItemCode' => $rs->product_code,
                  'Dscription' => limitText($rs->product_name, 95),
                  'Quantity' => $rs->qty,
                  'unitMsr' => $rs->unit_code,
                  'PriceBefDi' => round($rs->cost,2),
                  'LineTotal' => round(remove_vat($amount, $vat_rate), 2),
                  'ShipDate' => $date_add,
                  'Currency' => $currency,
                  'Rate' => 1,
                  'Price' => round(remove_vat($rs->cost, $vat_rate), 2),
                  'TotalFrgn' => round(remove_vat($amount, $vat_rate), 2),
                  'WhsCode' => $rs->warehouse_code,
                  'FisrtBin' => $rs->zone_code,
                  'TaxStatus' => 'Y',
                  'VatPrcnt' => $vat_rate,
                  'VatGroup' => $vat_code,
                  'PriceAfVAT' => $rs->cost,
                  'VatSum' => round(get_vat_amount($amount), 2),
                  'GTotal' => round(remove_vat($amount, $vat_rate), 2),
                  'AcctCode' => '531030',
                  'TaxType' => 'Y',
                  'F_E_Commerce' => 'A',
                  'F_E_CommerceDate' => sap_date(now())
                  );

                  if( ! $this->ci->adjust_model->add_sap_goods_receive_row($arr))
                  {
                    $sc = FALSE;
                    $this->error = 'เพิ่มรายการไม่สำเร็จ';
                  }

                  $line++;

                } //--- end foreach

              }
              else
              {
                $sc = FALSE;
                $this->error = "เพิ่มเอกสารไม่สำเร็จ";
              }

              if($sc === TRUE)
              {
                $this->ci->mc->trans_commit();
              }
              else
              {
                $this->ci->mc->trans_rollback();
              }
            }

          }

        }
        else
        {
          $sc = FALSE;
          $this->error = "เอกสาร Goods Receive ถูกนำเข้า SAP แล้วไม่อนุญาติให้แก้ไข";
        }
      }
      else
      {
        $sc = FALSE;
        $this->error = "ไม่พบเลขที่เอกสาร หรือ สถานะเอกสารไม่ถูกต้อง";
      }

      return $sc;
    }
    //--- end export adjust goods receive



    //---- Good issue Consignment
    //---- CNOIGE CNIGE1
    //---- Adjust consignment
    public function export_adjust_consignment_goods_issue($code)
    {
      $sc = TRUE;
      $this->ci->load->model('inventory/adjust_consignment_model');
      $doc = $this->ci->adjust_consignment_model->get($code);
      if(! empty($doc) && $doc->status == 1 && $doc->is_approved == 1)
      {
        $sap = $this->ci->adjust_consignment_model->get_sap_issue_doc($code);
        if(empty($sap))
        {
          $middle = $this->ci->adjust_consignment_model->get_middle_goods_issue($code);
          if(!empty($middle))
          {
            foreach($middle as $rows)
            {
              if($this->ci->adjust_consignment_model->drop_middle_issue_data($rows->DocEntry) === FALSE)
              {
                $sc = FALSE;
                $this->error = "ลบรายการที่ค้างใน Temp ไม่สำเร็จ";
              }
            }
          }

          if($sc === TRUE)
          {
            $details = $this->ci->adjust_consignment_model->get_issue_details($code);
            if(!empty($details))
            {
              $doc_total = 0;

              foreach($details as $row)
              {
                $row->qty = $row->qty * (-1);
                $doc_total += $row->qty * $row->cost;
              }

              $date_add = $doc->date_add;

              $arr = array(
              'U_ECOMNO' => $code,
              'DocType' => 'I',
              'CANCELED' => 'N',
              'DocDate' => sap_date($date_add),
              'DocDueDate' => sap_date($date_add),
              'DocTotal' => $doc_total,
              'DocTotalFC' => $doc_total,
              'Comments' => limitText($doc->remark, 250),
              'F_E_Commerce' => 'A',
              'F_E_CommerceDate' => sap_date(now(), TRUE)
              );

              $this->ci->mc->trans_begin();

              $docEntry = $this->ci->adjust_consignment_model->add_sap_goods_issue($arr);

              //--- now add details
              if($docEntry !== FALSE)
              {
                $line = 0;
                foreach($details as $rs)
                {
                  if($sc === FALSE)
                  {
                    break;
                  }

                  $arr = array(
                  'DocEntry' => $docEntry,
                  'U_ECOMNO' => $rs->adjust_code,
                  'LineNum' => $line,
                  'ItemCode' => $rs->product_code,
                  'Dscription' => limitText($rs->product_name, 95),
                  'Quantity' => $rs->qty,
                  'WhsCode' => $rs->warehouse_code,
                  'FisrtBin' => $rs->zone_code,
                  'DocDate' => sap_date($date_add),
                  'F_E_Commerce' => 'A',
                  'F_E_CommerceDate' => sap_date(now(), TRUE)
                  );

                  if(!$this->ci->adjust_consignment_model->add_sap_goods_issue_row($arr))
                  {
                    $sc = FALSE;
                    $this->error = "Insert Goods Issue Temp Error at line {$line}, ItemCode : {$rs->product_code} ";
                  }

                  $line++;
                }
              }
              else
              {
                $sc = FALSE;
                $this->error = "เพิ่มเอกสารไม่สำเร็จ";
              }

              if($sc === TRUE)
              {
                $this->ci->mc->trans_commit();
              }
              else
              {
                $this->ci->mc->trans_rollback();
              }

            }

          }
        }
        else
        {
          $sc = FALSE;
          $this->error = "เอกสาร Goods Issue ถูกนำเข้า SAP แล้วไม่อนุญาติให้แก้ไข";
        }
      }
      else
      {
        $sc = FALSE;
        $this->error = "ไม่พบเลขที่เอกสาร หรือ สถานะเอกสารไม่ถูกต้อง";
      }

      return $sc;
    }




    //---- adjust goods receive consignment
    //---- CNOIGN CNIGN1
    public function export_adjust_consignment_goods_receive($code)
    {
      $sc = TRUE;
      $this->ci->load->model('inventory/adjust_consignment_model');
      $this->ci->load->model('masters/products_model');
      $doc = $this->ci->adjust_consignment_model->get($code);

      if(!empty($doc) && $doc->status == 1 && $doc->is_approved == 1)
      {
        $sap = $this->ci->adjust_consignment_model->get_sap_receive_doc($code);
        if(empty($sap))
        {
          $middle = $this->ci->adjust_consignment_model->get_middle_goods_receive($code);
          if(!empty($middle))
          {
            foreach($middle as $rows)
            {
              if($this->ci->adjust_consignment_model->drop_middle_receive_data($rows->DocEntry) === FALSE)
              {
                $sc = FALSE;
                $this->error = "ลบรายการที่ค้างใน temp ไม่สำเร็จ";
              }
            }
          }

          if($sc === TRUE)
          {
            $details = $this->ci->adjust_consignment_model->get_receive_details($code);
            if(!empty($details))
            {
              $currency = getConfig('CURRENCY');
              $vat_rate = getConfig('PURCHASE_VAT_RATE');
              $vat_code = getConfig('PURCHASE_VAT_CODE');
              $date_add = $doc->date_add;
              $doc_total = 0;

              foreach($details as $row)
              {
                $doc_total += $row->qty * $row->cost;
              }

              $ds = array(
              'U_ECOMNO' => $doc->code,
              'DocType' => 'I',
              'CANCELED' => 'N',
              'DocDate' => $date_add,
              'DocDueDate' => $date_add,
              'DocCur' => $currency,
              'DocRate' => 1,
              'DocTotal' => remove_vat($doc_total, $vat_rate),
              'Comments' => limitText($doc->remark, 250),
              'F_E_Commerce' => 'A',
              'F_E_CommerceDate' => sap_date(now())
              );

              $this->ci->mc->trans_begin();

              $docEntry = $this->ci->adjust_consignment_model->add_sap_goods_receive($ds);

              if($docEntry !== FALSE)
              {
                $line = 0;

                foreach($details as $rs)
                {
                  if($sc === FALSE)
                  {
                    break;
                  }

                  $amount = $rs->qty * $rs->cost;
                  $arr = array(
                  'DocEntry' => $docEntry,
                  'U_ECOMNO' => $rs->adjust_code,
                  'LineNum' => $line,
                  'ItemCode' => $rs->product_code,
                  'Dscription' => limitText($rs->product_name, 95),
                  'Quantity' => $rs->qty,
                  'unitMsr' => $rs->unit_code,
                  'PriceBefDi' => round($rs->cost,2),
                  'LineTotal' => round($amount, 2),
                  'ShipDate' => $date_add,
                  'Currency' => $currency,
                  'Rate' => 1,
                  'Price' => round(remove_vat($rs->cost, $vat_rate), 2),
                  'TotalFrgn' => round(remove_vat($amount, $vat_rate), 2),
                  'WhsCode' => $rs->warehouse_code,
                  'FisrtBin' => $rs->zone_code,
                  'TaxStatus' => 'Y',
                  'VatPrcnt' => $vat_rate,
                  'VatGroup' => $vat_code,
                  'PriceAfVAT' => $rs->cost,
                  'VatSum' => round(get_vat_amount($amount), 2),
                  'GTotal' => round(remove_vat($amount, $vat_rate), 2),
                  'TaxType' => 'Y',
                  'F_E_Commerce' => 'A',
                  'F_E_CommerceDate' => sap_date(now())
                  );

                  if( ! $this->ci->adjust_consignment_model->add_sap_goods_receive_row($arr))
                  {
                    $sc = FALSE;
                    $this->error = 'เพิ่มรายการไม่สำเร็จ';
                  }

                  $line++;

                } //--- end foreach

              }
              else
              {
                $sc = FALSE;
                $this->error = "เพิ่มเอกสารไม่สำเร็จ";
              }

              if($sc === TRUE)
              {
                $this->ci->mc->trans_commit();
              }
              else
              {
                $this->ci->mc->trans_rollback();
              }
            }

          }

        }
        else
        {
          $sc = FALSE;
          $this->error = "เอกสาร Goods Receive ถูกนำเข้า SAP แล้วไม่อนุญาติให้แก้ไข";
        }
      }
      else
      {
        $sc = FALSE;
        $this->error = "ไม่พบเลขที่เอกสาร หรือ สถานะเอกสารไม่ถูกต้อง";
      }

      return $sc;
    }
    //--- end export adjust goods receive


  //---Export Downpayment ODPI DPI1
  public function export_down_payment($code, $option = 'A')
  {
    //-- option 'A' = Aadd , U = 'update'
    $sc = TRUE;
    $this->ci->load->model('orders/down_payment_invoice_model');

    $doc = $this->ci->down_payment_invoice_model->get($code);

    if( ! empty($doc))
    {
      $sap = $this->ci->down_payment_invoice_model->get_sap_doc_num($code);

      if( ! empty($sap))
      {
        $sc = FALSE;
        $this->error = "เอกสารมัดจำเข้าระบบ SAP แล้วกรุณายกเลิกเอกสารในระบบ SAP ก่อน";
      }

      if($sc === TRUE)
      {
        if( ! $this->ci->down_payment_invoice_model->drop_middle_exits_data($code))
        {
          $sc = FALSE;
          $this->error = "ลบ Downpayment ใน Temp ไม่สำเร็จ";
        }
      }

      $details = $this->ci->down_payment_invoice_model->get_details($doc->code);

      if( empty($details))
      {
        $sc = FALSE;
        $this->error = "ไม่พบรายการขาย";
      }

      if($sc === TRUE)
      {
        $ds = array(
          'DocType' => 'I', //--- I = item, S = Service
          'CANCELED' => 'N', //--- Y = Yes, N = No
          'DocDate' => sap_date($doc->date_add, TRUE), //--- วันที่เอกสาร
          'DocDueDate' => sap_date($doc->date_add,TRUE), //--- วันที่เอกสาร
          'CardCode' => $doc->CardCode, //--- รหัสลูกค้า
          'CardName' => $doc->CardName, //--- ชื่อลูกค้า
          'LicTradNum' => get_null($doc->tax_id),
          'PayToCode' => $doc->branch_code,
          'Address' => parseAddress($doc->address, $doc->sub_district, $doc->district, $doc->province, $doc->postcode),
          'NumAtCard' => $doc->BaseDpm.'/'.$doc->BaseRef,
          'DiscPrcnt' => $doc->DiscPrcnt,
          'DiscSum' => $doc->DiscSum,
          'DiscSumFC' => 0.00,
          'DocCur' => $doc->DocCur,
          'DocRate' => $doc->DocRate,
          'DocTotal' => $doc->DocTotal,
          'VatPercent' => 0.00,
          'VatSum' => $doc->VatSum,
          'SlpCode' => $doc->SlpCode,
          'Comments' => $doc->BaseDpm,
          'U_ECOMNO' => $doc->code,
          'U_OLDTAX' => $doc->code,
          'U_SONO' => $doc->BaseRef,
          'U_TEL' => $doc->phone,
          'F_E_Commerce' => $option,
          'F_E_CommerceDate' => sap_date(now(), TRUE)
        );

        $this->ci->mc->trans_begin();

        $docEntry = $this->ci->down_payment_invoice_model->add_sap_doc($ds);

        if( ! empty($docEntry))
        {
          $line = 0;

          foreach($details as $rs)
          {
            $arr = array(
              'DocEntry' => $docEntry,
              'U_ECOMNO' => $doc->code,
              'LineNum' => $line,
              'ItemCode' => $rs->ItemCode,
              'Dscription' => $rs->Dscription,
              'BaseCard' => $doc->CardCode,
              'Quantity' => $rs->Qty,
              'OpenQty' => $rs->Qty,
              'UnitMsr' => $rs->unitMsr,
              'PriceBefDi' => $rs->PriceBefDi,  //---มูลค่าต่อหน่วยก่อนภาษี/ก่อนส่วนลด
              'LineTotal' => $rs->LineTotal,
              'Currency' => $rs->Currency,
              'Rate' => $rs->Rate,
              'DiscPrcnt' => $rs->DiscPrcnt, ///--- discount_helper
              'Price' => $rs->Price, //--- ราคา
              'PriceAfVAT' => $rs->PriceAfVAT,
              'WhsCode' => $rs->WhsCode,
              'TaxStatus' => 'Y',
              'TaxType' => 'Y', //--- คิดภาษีหรือไม่
              'VatSum' => $rs->VatSum,
              'VatPrcnt' => $rs->VatRate,
              'VatGroup' => $rs->VatCode,
              'GTotal' => $rs->LineTotal,
              'SlpCode' => $rs->SlpCode
            );

            if( ! $this->ci->down_payment_invoice_model->add_sap_row($arr))
            {
              $sc = FALSE;
              $this->error = "เพิ่มรายการใน Temp ไม่สำเร็จ";
            }

            $line++;
          }

          if($sc === TRUE)
          {
            $this->ci->mc->trans_commit();
          }
          else
          {
            $this->ci->mc->trans_rollback();
          }

          if($sc === TRUE)
          {
            $arr = array(
              'isExported' => 'Y',
              'export_error' => NULL
            );

            $this->ci->down_payment_invoice_model->update($doc->code, $arr);
          }
          else
          {
            $arr = array(
              'isExported' => 'E',
              'export_error' => $this->error
            );

            $this->ci->down_payment_invoice_model->update($doc->code, $arr);
          }
        }
        else
        {
          $sc = FALSE;
          $this->error = "สร้างเอกสารใน Temp ไม่สำเร็จ";
        }
      } //--- $sc === TRUE;
    }
    else
    {
      $sc = FALSE;
      $this->error = "ไม่พบเลขที่เอกสาร";
    }

    return $sc;
  }


  //---Export Incomming ORCT for POS
  public function export_incomming($code, $type = 'DP')
  {
    //-- option 'A' = Aadd , U = 'update'
    $sc = TRUE;
    $this->ci->load->model('orders/order_invoice_model');
    $this->ci->load->model('orders/order_down_payment_model');
    $this->ci->load->model('orders/order_pos_model');
    $this->ci->load->model('orders/order_pos_payment_model');
    $this->ci->load->model('masters/bank_model');

    $doc = NULL;

    if($type == 'POS')
    {
      $doc = $this->ci->order_pos_model->get($code);
    }
    else if($type == 'DP')
    {
      $doc = $this->ci->order_down_payment_model->get($code);
    }

    if( ! empty($doc))
    {
      if($doc->status != 'D')
      {
        $sap = $this->ci->order_down_payment_model->get_sap_doc_num($doc->code);

        if( ! empty($sap))
        {
          $sc = FALSE;
          $this->error = "เอกสารรับเงินเข้าระบบ SAP แล้วกรุณายกเลิกเอกสารในระบบ SAP ก่อน";
        }

        if($sc === TRUE)
        {
          $payment = $this->ci->order_pos_payment_model->get_payments($doc->code);

          if( ! empty($payment))
          {
            if( ! $this->ci->order_down_payment_model->drop_middle_exists_data($doc->code))
            {
              $sc = FALSE;
              $this->error = "Failed to delete Temp incomming data";
            }

            if($sc === TRUE)
            {
              $currency = getConfig('CURRENCY');

              $customer_name = $doc->customer_name;

              if( ! empty($doc->customer_ref) && $doc->customer_ref != "" && $doc->customer_ref != NULL && $doc->customer_ref != "-")
              {
                $customer_name = $doc->customer_ref;
              }

              $cashAcctCode = NULL;
              $cashAmount = 0.00;
              $transAcctCode = NULL;
              $transAmount = 0.00;
              $transDate = NULL;
              $cardAmount = 0.00;
              $checkAcctCode = NULL;
              $checkAmount = 0.00;

              foreach($payment as $pm)
              {
                if($pm->payment_role == 1)
                {
                  $cashAcctCode = getConfig('SAP_CASH_ACCT_CODE');
                  $cashAmount = $pm->amount;
                }

                if($pm->payment_role == 2)
                {
                  $bank = $this->ci->bank_model->get($pm->acc_id);
                  $transAcctCode = $bank->sapAcctCode;
                  $transAmount = $pm->amount;
                  $transDate = $pm->payment_date;
                }

                if($pm->payment_role == 3)
                {
                  $cardId = getConfig('SAP_CREDIT_CARD_ID');
                  $cardAcctCode = getConfig('SAP_CREDIT_CARD_ACCT_CODE');
                  $cardAmount = $pm->amount;
                }

                if($pm->payment_role == 7)
                {
                  $checkAcctCode = getConfig('SAP_CHECK_ACCT_CODE');
                  $checkAmount = $pm->amount;
                }
              }

              $pf = $type == 'DP' ? "มัดจำ" : "รับเงิน";

              $arr = array(
                'DocDate' => sap_date($doc->date_add),
                'DocDueDate' => sap_date($doc->date_add),
                'CardCode' => $doc->customer_code,
                'CardName' => $customer_name,
                'CashAcct' => $cashAcctCode,
                'CashSum' => $cashAmount,
                'CreditSum' => $cardAmount,
                'CheckAcct' => $checkAcctCode,
                'CheckSum' => $checkAmount,
                'TrsfrAcct' => $transAcctCode,
                'TrsfrSum' => $transAmount,
                'TrsfrDate' => $transDate,
                'DocCurr' => $currency,
                'DocTotal' => $doc->amount,
                'Ref1' => NULL,
                'Ref2' => $doc->code, //$type == 'DP' ? $doc->reference : $doc->so_code,
                'Comments' => $customer_name,
                'JrnlMemo' => "{$doc->code}". ($type == 'DP' ? " : {$doc->reference}" : (empty($doc->so_code) ? NULL : " : {$doc->so_code}")). " : {$customer_name}",
                'U_ECOMNO' => $doc->code,
                'F_E_Commerce' => 'A',
                'F_E_CommerceDate' => sap_date(now(), TRUE)
              );


              $this->ci->mc->trans_begin();

              $docEntry = $this->ci->order_down_payment_model->add_sap_doc($arr);

              if( ! $docEntry )
              {
                $sc = FALSE;
                $this->error = "Insert Incomming failed";
              }
              else
              {
                if($sc === TRUE && $pm->payment_role == 3)
                {
                  $arr = array(
                    'DocNum' => $docEntry,
                    'LineID' => 0,
                    'CreditCard' => $cardId,
                    'CreditAcct' => $cardAcctCode,
                    'FirstDue' => sap_date($doc->date_add),
                    'FirstSum' => $cardAmount,
                    'CreditSum' => $cardAmount,
                    'CreditCur' => $currency,
                    'CreditType' => 'S',
                    'U_ECOMNO' => $doc->code
                  );

                  if( ! $this->ci->order_down_payment_model->add_sap_card_row($arr))
                  {
                    $sc = FALSE;
                    $this->error = "Failed to insert credit card transection";
                  }
                }
              }

              if($sc === TRUE)
              {
                $this->ci->mc->trans_commit();

                if($type == 'DP')
                {
                  $arr = array(
                    'DocNum' => NULL,
                    'is_exported' => 1,
                    'export_error' => NULL
                  );

                  $this->ci->order_down_payment_model->update($doc->id, $arr);
                }

                if($type == 'POS')
                {
                  $arr = array(
                    'incomming_exported' => 'Y'
                  );

                  $this->ci->order_invoice_model->update($doc->invoice_code, $arr);
                }

              }
              else
              {
                $this->ci->mc->trans_rollback();

                if($type == 'DP')
                {
                  $arr = array(
                    'DocNum' => NULL,
                    'is_exported' => 3,
                    'export_error' => $this->error
                  );

                  $this->ci->order_down_payment_model->update($doc->id, $arr);
                }

                if($type == 'POS')
                {
                  $arr = array(
                    'incomming_exported' => 'E'
                  );

                  $this->ci->order_invoice_model->update($doc->invoice_code, $arr);
                }
              }
            } //-- if delete temp success
          } //--- if ! empty($payment)
        }
      }
      else
      {
        $sc = FALSE;
        $this->error = "This document already cancelled";
      }
    }
    else
    {
      $sc = FALSE;
      $this->error = "Document not found!";
    }

    return $sc;
  }

} //--- end class
