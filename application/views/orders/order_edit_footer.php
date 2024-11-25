<?php $disabled = ($order->state == 1 && empty($order->BaseRef)) && ($this->pm->can_add OR $this->pm->can_edit) ? '' : 'disabled'; ?>
<div class="divider-hidden"></div>
<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5">
  <div class="form-horizontal">
    <div class="form-group">
      <label class="col-lg-3 col-md-4 col-sm-4 control-label no-padding-right">พนักงานขาย</label>
      <div class="col-lg-5 col-md-6 col-sm-6 col-xs-12">
        <select class="width-100 edit" id="sale-id" name="sale_id" <?php echo $disabled; ?>>
          <?php echo select_saleman($order->sale_code); ?>
        </select>
      </div>
    </div>

    <div class="form-group">
      <label class="col-lg-3 col-md-4 col-sm-4 control-label no-padding-right">Owner</label>
      <div class="col-lg-5 col-md-6 col-sm-6 col-xs-12">
        <input type="text" class="form-control input-sm" value="<?php echo $order->user; ?>" disabled>
      </div>
    </div>

    <div class="form-group">
      <label class="col-lg-3 col-md-4 col-sm-4 col-xs-12 control-label no-padding-right">Remark</label>
      <div class="col-lg-9 col-md-8 col-sm-8 col-xs-12">
        <textarea id="remark" maxlength="254" rows="3" class="form-control" onchange="updateRemark()" <?php echo $disabled; ?>><?php echo $order->remark; ?></textarea>
      </div>
    </div>

  </div>
</div>

<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5">
  <div class="form-horizontal">
    <div class="form-group">
      <label class="col-lg-3 col-md-3 col-sm-3 col-xs-6 control-label no-padding-right">จำนวน</label>
      <div class="col-lg-2-harf col-md-2-harf col-sm-2-harf col-xs-6 padding-5">
        <input type="text" class="form-control input-sm text-right" id="total-qty" value="<?php echo number($total_qty); ?>" disabled>
      </div>
      <label class="col-lg-2-harf col-md-2-harf col-sm-2-harf col-xs-6 control-label no-padding-right">มูลค่ารวม</label>
      <div class="col-lg-4 col-md-4 col-sm-4 col-xs-6 padding-5 last">
        <input type="text" class="form-control input-sm text-right" id="total-amount" value="<?php echo number($total_amount, 2); ?>" disabled>
      </div>
    </div>

    <div class="form-group">
      <label class="col-lg-6 col-md-5-harf col-sm-6 col-xs-6 control-label no-padding-right">ส่วนลด</label>
      <div class="col-lg-2 col-md-2-harf col-sm-2 col-xs-6 padding-5">
        <span class="input-icon input-icon-right">
          <input type="number" id="bill-disc-percent" class="form-control input-sm text-center" value="<?php echo number($order->bDiscText, 2); ?>" <?php echo $disabled; ?> />
          <i class="ace-icon fa fa-percent"></i>
        </span>
      </div>
      <div class="col-lg-4 col-md-4 col-sm-4 col-xs-6 padding-5 last">
        <input type="text" class="form-control input-sm text-right" id="bill-disc-amount" value="<?php echo number($order->bDiscAmount, 2); ?>" <?php echo $disabled; ?> />
      </div>
    </div>

    <div class="form-group <?php echo ($order->TaxStatus == 'Y' ? '' : 'hide'); ?>" id="bill-wht">
      <label class="col-lg-6 col-md-5-harf col-sm-6 col-xs-3 control-label no-padding-right">หัก ณ ที่จ่าย</label>
      <div class="col-lg-2 col-md-2-harf col-sm-2 col-xs-3 padding-5">
        <span class="input-icon input-icon-right">
        <input type="number" id="whtPrcnt" class="form-control input-sm text-center" value="<?php echo number($order->WhtPrcnt, 2); ?>" <?php echo $disabled; ?>/>
        <i class="ace-icon fa fa-percent"></i>
        </span>
      </div>
      <div class="col-lg-4 col-md-4 col-sm-4 col-xs-6 padding-5 last">
        <input type="hidden" id="wht-amount" value="<?php echo $order->WhtAmount; ?>" />
        <input type="text" id="wht-amount-label" class="form-control input-sm text-right" value="<?php echo number($order->WhtAmount, 2); ?>" disabled />
      </div>
    </div>

    <div class="form-group <?php echo ($order->TaxStatus == 'Y' ? '' : 'hide'); ?>" id="bill-vat">
      <label class="col-lg-8 col-md-8 col-sm-8 col-xs-6 control-label no-padding-right">VAT</label>
      <div class="col-lg-4 col-md-4 col-sm-4 col-xs-6 padding-5 last">
        <input type="hidden" id="vat-total" value="<?php echo $order->VatSum; ?>"/>
        <input type="text" id="vat-total-label" class="form-control input-sm text-right" value="<?php echo number($order->VatSum, 2); ?>" disabled >
      </div>
    </div>

    <div class="form-group">
      <label class="col-lg-8 col-md-8 col-sm-8 col-xs-6 control-label no-padding-right">รวมทั้งสิ้น</label>
      <div class="col-lg-4 col-md-4 col-sm-4 col-xs-6 padding-5 last">
        <input type="text" class="form-control input-sm text-right" id="doc-total" value="<?php echo number($order->doc_total, 2); ?>" disabled/>
      </div>
    </div>

    <div class="form-group hide">
      <label class="col-lg-8 col-md-8 col-sm-8 col-xs-6 control-label no-padding-right">ชำระแล้ว</label>
      <div class="col-lg-4 col-md-4 col-sm-4 col-xs-6 padding-5 last">
        <input type="text" class="form-control input-sm text-right" id="paid-amount" value="<?php echo number($order->paidAmount, 2); ?>" disabled/>
      </div>
    </div>

    <div class="form-group hide">
      <label class="col-lg-8 col-md-8 col-sm-8 col-xs-6 control-label no-padding-right">คงเหลือ</label>
      <div class="col-lg-4 col-md-4 col-sm-4 col-xs-6 padding-5 last">
        <input type="text" class="form-control input-sm text-right" id="total-balance" value="<?php echo number($order->TotalBalance, 2); ?>" disabled>
      </div>
    </div>

  </div> <!-- form horizontal -->
</div>

<?php if(($order->state == 1 OR $order->status == 0) && ($this->pm->can_add OR $this->pm->can_edit)) : ?>
<div class="divider-hidden"></div>
<div class="divider-hidden"></div>
<div class="divider-hidden"></div>

<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 text-right">
  <button type="button" class="btn btn-sm btn-warning btn-100" onclick="leave()">Cancel</button>
  <button type="button" class="btn btn-sm btn-primary btn-100" id="btn-save" onclick="saveOrder()">Save</button>
</div>
<?php endif; ?>
