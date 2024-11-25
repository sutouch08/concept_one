<style media="screen">
.form-group {
  margin-bottom:5px !important;
}
</style>
<div class="row">
  <!--- left column -->
  <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5">
    <div class="form-horizontal">
      <div class="form-group">
        <label class="col-lg-3 col-md-4 col-sm-4 col-xs-6 control-label no-padding-right">เจ้าของ</label>
        <div class="col-lg-5 col-md-6 col-sm-6 col-xs-6 padding-5 last">
          <input type="text" class="form-control input-sm" value="<?php echo $this->user_model->get_name($doc->user); ?>" disabled />
  				<input type="hidden" id="owner" value="<?php echo $doc->user; ?>" />
        </div>
      </div>

      <div class="form-group">
        <label class="col-lg-3 col-md-4 col-sm-4 col-xs-6 control-label no-padding-right">จุดขาย</label>
        <div class="col-lg-5 col-md-6 col-sm-6 col-xs-6 padding-5 last">
          <input type="text" class="form-control input-sm" id="shop" value="<?php echo empty($pos) ? NULL : $pos->shop_name; ?>" disabled />
        </div>
      </div>

      <div class="form-group">
        <label class="col-lg-3 col-md-4 col-sm-4 col-xs-6 control-label no-padding-right">เครื่อง POS</label>
        <div class="col-lg-5 col-md-6 col-sm-6 col-xs-6 padding-5 last">
          <input type="text" class="form-control input-sm" id="pos" value="<?php echo empty($pos) ? NULL : $pos->name; ?>" disabled />
        </div>
      </div>
    </div>
  </div>

  <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5">
    <div class="form-horizontal">
      <div class="form-group">
        <label class="col-lg-8 col-md-8 col-sm-7 col-xs-6 control-label no-padding-right">มูลค่ารวม</label>
        <div class="col-lg-4 col-md-4 col-sm-5 col-xs-6 padding-5 last">
					<input type="hidden" id="total-amount" value="0.00">
          <input type="text" class="form-control input-sm text-right" id="total-amount-label" value="<?php echo number($doc->amount, 2); ?>" disabled>
        </div>
      </div>

      <div class="form-group">
        <label class="col-lg-8 col-md-8 col-sm-7 col-xs-6 control-label no-padding-right">ภาษีมูลค่าเพิ่ม</label>
        <div class="col-lg-4 col-md-4 col-sm-5 col-xs-6 padding-5 last">
          <input type="hidden" id="vat-total" value="0.00"/>
          <input type="text" id="vat-total-label" class="form-control input-sm text-right" value="<?php echo number($doc->VatSum, 2); ?>" readonly disabled/>
        </div>
      </div>

      <div class="form-group">
        <label class="col-lg-8 col-md-8 col-sm-7 col-xs-6 control-label no-padding-right">รวมทั้งสิ้น</label>
        <div class="col-lg-4 col-md-4 col-sm-5 col-xs-6 padding-5 last">
          <input type="hidden" id="doc-total" value="<?php echo $doc->amount; ?>"/>
          <input type="text" id="doc-total-label" class="form-control input-sm text-right" value="<?php echo number($doc->amount, 2); ?>" disabled/>
        </div>
      </div>
    </div>
  </div>

  <div class="divider-hidden"></div>
  <div class="divider-hidden"></div>
  <div class="divider-hidden"></div>
</div>
