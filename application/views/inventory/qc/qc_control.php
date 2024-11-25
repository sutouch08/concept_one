<!--  Control -->
<div class="row">
  <div class="col-lg-1-harf col-md-1-harf col-sm-2-harf col-xs-6 padding-5">
    <label>บาร์โค้ดกล่อง</label>
    <input type="text" class="form-control input-sm text-center zone" id="barcode-box" autofocus <?php echo $disActive; ?> />
  </div>

  <div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-6 padding-5">
    <label class="display-block not-show">change box</label>
    <button
      type="button"
      class="btn btn-xs btn-info btn-block item"
      id="btn-change-box"
      onclick="confirmSaveBeforeChangeBox()"
      disabled >
      <i class="fa fa-refresh"></i> เปลี่ยนกล่อง
    </button>
  </div>

  <div class="col-lg-1 col-md-1 col-sm-1-harf col-xs-3">
    <label>Qty</label>
    <input type="number" class="form-control input-sm text-center" id="qc-qty" value="1" <?php echo $this->pm->can_approve == 0 ? 'disabled' : ''; ?>/>
  </div>

  <div class="col-lg-2 col-md-2 col-sm-2 col-xs-6 padding-5">
    <label>บาร์โค้ดสินค้า</label>
    <input type="text" class="form-control input-sm text-center item" id="barcode-item" disabled />
  </div>
  <div class="col-lg-1 col-md-1 col-sm-1 col-xs-3 padding-5">
    <label class="display-block not-show">submit</label>
    <button type="button" class="btn btn-xs btn-default btn-block item" id="btn-submit" onclick="qcProduct()" disabled>ตกลง</button>
  </div>


  <div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
    <label class="display-block not-show">submit</label>
    <button type="button" class="btn btn-xs btn-success btn-block item" onclick="saveQc(0)" <?php echo $disActive; ?>>
      <i class="fa fa-save"></i> บันทึก
    </button>
  </div>
  <div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
    <label class="display-block not-show">print</label>
    <button type="button" class="btn btn-xs btn-primary btn-block" id="btn-print-address" onclick="printAddress()">พิมพ์ใบปะหน้า</button>
  </div>
  <div class="col-sm-12 col-xs-12 visible-sm visible-xs">&nbsp;&nbsp;</div>

  <div class="col-lg-2 col-md-2 col-sm-3 col-xs-12 padding-5">
    <div class="title middle text-center" style="height:55px; background-color:black; color:white; padding-top:20px; margin-top:0px;">
      <h4 id="all_qty" style="display:inline;">
        <?php echo number($qc_qty); ?>
      </h4>
      <h4 style="display:inline;"> / <?php echo number($all_qty); ?></h4>
    </div>
  </div>
</div>

<input type="hidden" id="customer_ref" value="<?php echo $order->customer_ref; ?>" />
<input type="hidden" id="customer_code" value="<?php echo $order->customer_code; ?>" />

<hr/>
<!--  Control -->
