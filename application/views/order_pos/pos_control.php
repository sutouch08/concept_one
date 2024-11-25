<?php
  $disabled = empty($order->so_code) ? '' : 'disabled';
  $WhtPrcnt = $order->WhtPrcnt * 0.01;
  $WhtAmount = $order->vat_type == 'E' ? $amountAfterDisc * $WhtPrcnt : ($amountAfterDisc - $vatSum) * $WhtPrcnt;
?>

<div class="col-lg-12 col-md-12 col-sm-12 pg-footer pg-footer-inner padding-right-5">
  <div class="pg-footer-content">
    <!-- left -->
    <div class="col-lg-5 col-md-5 col-sm-5 padding-5">
      <div class="row">
        <div class="col-lg-2 col-md-3 col-sm-3 padding-5 first">
          <label class="font-size-11">จำนวน</label>
            <input type="number" class="form-control input-sm text-center focus" id="item-qty" value="1.0" <?php echo $disabled; ?>/>
        </div>
        <div class="col-lg-6 col-md-9 col-sm-9 padding-5">
          <label class="font-size-11 padding-5">สินค้า</label>
          <input type="text" class="form-control input-sm focus" id="item-barcode" value="" autofocus placeholder="สแกนบาร์โค้ด" <?php echo $disabled; ?>/>
        </div>
        <div class="col-lg-6 col-md-6 col-sm-6 padding-5 first">
          <label class="font-size-11 padding-5">ของแถม</label>
          <input type="text" class="form-control input-sm focus" id="free-item-barcode" value="" autofocus placeholder="สแกนบาร์โค้ด" <?php echo $disabled; ?>/>
        </div>
        <div class="col-lg-6 col-md-6 col-sm-6 padding-5">
          <label class="font-size-11 padding-5">ตรวจสอบสินค้า</label>
          <input type="text" class="form-control input-sm focus" id="pd-box" value="" placeholder="บาร์โค้ด หรือ รหัสสินค้า"/>
        </div>

        <div class="col-lg-4 col-md-4 col-md-offset-6 col-sm-5 col-sm-offset-4 padding-5">
          <label class="font-size-11 padding-5">ใบสั่งขาย</label>
          <input type="text" class="form-control input-sm text-center focus" id="so-code" value="<?php echo $order->so_code; ?>" <?php echo empty($order->so_code) ? '' : 'disabled'; ?> placeholder="เลขที่ใบสั่งงาน" />
        </div>
        <div class="col-lg-2 col-md-2 col-sm-3 padding-5">
          <label class="font-size-11 padding-5 not-show">ใบสั่งขาย</label>
          <?php if( ! empty($order->so_code)) : ?>
            <button type="button" class="btn btn-xs btn-warning btn-block" onclick="clearSaleOrder()">Clear</button>
          <?php else : ?>
            <button type="button" class="btn btn-xs btn-info btn-block" onclick="confirmAddSaleOrder()">Add</button>
          <?php endif; ?>
        </div>
      </div>
    </div>
    <!-- left -->

    <!-- right -->
    <div class="col-lg-7 col-md-7 col-sm-7 padding-5">
      <div class="form-horizontal">
        <div class="form-group">
          <label class="col-lg-4-harf col-md-4 col-sm-4 control-label no-padding-right font-size-11 ">จำนวน</label>
          <div class="col-lg-2 col-md-2-harf col-sm-2-harf padding-5">
            <input type="text" class="form-control input-sm text-right"
            style="background-color:black; color:lime;"
            id="total-item" value="<?php echo number($totalQty, 2); ?>" readonly>
          </div>
          <label class="col-lg-2-harf col-md-2-harf col-sm-2-harf control-label padding-5 font-size-11 ">มูลค่าก่อนส่วนลด</label>
          <div class="col-lg-3 col-md-3 col-sm-3 padding-5 last">
  					<input type="hidden" id="total-amount" value="<?php echo $totalBfDisc; ?>">
            <input type="text" class="form-control input-sm text-right" id="total-amount-label" value="<?php echo number($totalBfDisc, 2); ?>" disabled>
          </div>
        </div>

        <div class="form-group">
          <label class="col-lg-7-harf col-md-7 col-sm-7 control-label no-padding-right font-size-11 ">ส่วนลด</label>
          <div class="col-lg-1-harf col-md-2 col-sm-2 padding-5">
            <span class="input-icon input-icon-right">
            <input type="number" id="discPrcnt" class="form-control input-sm focus" value="<?php echo $order->bill_disc_percent; ?>" <?php echo $disabled; ?>/>
            <i class="ace-icon fa fa-percent"></i>
            </span>
          </div>
          <div class="col-lg-3 col-md-3 col-sm-3 padding-5 last">
            <input type="text" id="bill-disc-label" class="form-control input-sm text-right focus" onchange="reCalDiscAmount()" value="<?php echo number($order->bill_disc_amount, 2); ?>" <?php echo $disabled; ?>/>
            <input type="hidden" id="bill-disc-amount" value="<?php echo $order->bill_disc_amount; ?>" />
          </div>
        </div>

        <?php $vat_type = $order->vat_type == 'E' ? 'Exclude' : 'Include'; ?>
        <div class="form-group">
          <label class="col-lg-2-harf col-md-2 col-sm-2 control-label no-padding-right font-size-11 ">หัก ณ ที่จ่าย</label>
          <div class="col-lg-1-harf col-md-2 col-sm-2 padding-5">
            <span class="input-icon input-icon-right">
            <input type="number" id="whtPrcnt" class="form-control input-sm focus" onchange="recalTotal()" value="<?php echo number($order->WhtPrcnt, 2); ?>" <?php echo $disabled; ?>/>
            <i class="ace-icon fa fa-percent"></i>
            </span>
          </div>
          <div class="col-lg-2-harf col-md-2-harf col-sm-2-harf padding-5">
            <input type="text" id="whtAmount" class="form-control input-sm text-right" value="<?php echo number($WhtAmount, 2); ?>" disabled />
          </div>

          <label class="col-lg-1 col-md-1 col-sm-1 control-label no-padding-right font-size-11 ">VAT</label>
          <div class="col-lg-1-harf col-md-1-harf col-sm-1-harf padding-5">
            <input type="text" id="vat-type-label" class="form-control input-sm text-center" value="<?php echo $vat_type; ?>" disabled/>
            <input type="hidden" id="vat-type" value="<?php echo $order->vat_type; ?>" />
          </div>
          <div class="col-lg-3 col-md-3 col-sm-3 padding-5 last">
            <input type="hidden" id="vat-total" value="<?php echo $vatSum; ?>"/>
            <input type="text" id="vat-total-label" class="form-control input-sm text-right" value="<?php echo number($vatSum, 2); ?>" disabled />
          </div>
        </div>


        <div class="form-group">
          <label class="col-lg-9 col-md-9 col-sm-9 control-label padding-5 font-size-11 ">มูลค่ารวม</label>
          <div class="col-lg-3 col-md-3 col-sm-3 padding-5 last">
            <input type="hidden" id="amountAfterDiscAndTax" value="<?php echo $amountAfterDiscAndTax; ?>" />
            <input type="text" id="amountAfterDiscAndTax-label" class="form-control input-sm text-right" value="<?php echo number($amountAfterDiscAndTax, 2); ?>" disabled/>
          </div>
        </div>

        <div class="form-group">
          <label class="col-lg-3 col-md-4 col-sm-3-harf control-label no-padding-right font-size-11 ">มัดจำ</label>
          <div class="col-lg-3-harf col-md-3-harf col-sm-4 padding-5">
            <input type="hidden" id="down-payment" value="<?php echo $downPayment; ?>"/>
            <div class="input-group">
              <input type="text" id="down-payment-label" class="form-control input-sm text-right" value="<?php echo number($downPayment, 2); ?>" disabled />
              <span class="input-group-btn">
                <button type="button" class="btn btn-xs btn-info" style="width:60px;" onclick="showDownPayment()">แสดง</button>
              </span>
            </div>
          </div>

          <label class="col-lg-2-harf col-md-1-harf col-sm-1-harf control-label padding-5 font-size-11 ">ยอดชำระ</label>
          <div class="col-lg-3 col-md-3 col-sm-3 padding-5 last">
            <input type="hidden" id="amountAfterDiscAndTax" value="<?php echo $amountAfterDiscAndTax; ?>" />
            <input type="hidden" id="doc-total" value="<?php echo $docTotal; ?>"/>
            <input type="text" id="doc-total-label" class="form-control input-sm text-right" value="<?php echo number($docTotal, 2); ?>" disabled/>
          </div>
        </div>
      </div> <!-- form-horizontal -->
    </div>
    <!-- right -->

    <input type="hidden" id="vat-type" value="<?php echo $order->vat_type; ?>" />
    <input type="hidden" id="vat-rate" value="<?php echo $order->vat_rate; ?>" />
    <input type="hidden" id="soCode" value="<?php echo $order->so_code; ?>" />
  </div>
</div><!-- footer inner-->
