<div class="row">
  <!--- left column -->
  <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5">
    <div class="form-horizontal">

			<div class="form-group">
        <label class="col-lg-3 col-md-4 col-sm-4 control-label no-padding-right">พนักงานขาย</label>
        <div class="col-lg-5 col-md-6 col-sm-6 col-xs-12">
          <select class="width-100" id="sale_id">
            <?php echo select_saleman($doc->sale_id); ?>
					</select>
        </div>
      </div>

      <div class="form-group">
        <label class="col-lg-3 col-md-4 col-sm-4 control-label no-padding-right">เจ้าของ</label>
        <div class="col-lg-5 col-md-6 col-sm-6 col-xs-12">
          <input type="text" class="form-control input-sm" value="<?php echo $this->user_model->get_name($doc->user); ?>" disabled />
  				<input type="hidden" id="owner" value="<?php echo $doc->user; ?>" />
        </div>
      </div>

      <div class="form-group">
        <label class="col-lg-3 col-md-4 col-sm-4 col-xs-12 control-label no-padding-right">หมายเหตุ</label>
        <div class="col-lg-9 col-md-8 col-sm-8 col-xs-12">
          <textarea id="remark" maxlength="254" rows="3" class="form-control"><?php echo $doc->remark; ?></textarea>
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
          <input type="text" class="form-control input-sm text-right" id="total-amount-label" value="<?php echo number($doc->TotalBfDisc, 2); ?>" disabled>
        </div>
      </div>
      <?php $disabled = $doc->isLinked > 0 ? 'disabled' : ''; ?>
      <div class="form-group">
        <label class="col-lg-6 col-md-5-harf col-sm-4 col-xs-3 control-label no-padding-right">ส่วนลด</label>
        <div class="col-lg-2 col-md-2-harf col-sm-3 col-xs-3 padding-5">
          <span class="input-icon input-icon-right">
          <input type="number" id="discPrcnt" class="form-control input-sm" value="<?php echo number($doc->DiscPrcnt, 2); ?>" <?php echo $disabled; ?>/>
          <i class="ace-icon fa fa-percent"></i>
          </span>
        </div>
        <div class="col-lg-4 col-md-4 col-sm-5 col-xs-6 padding-5 last">
          <input type="hidden" id="disc-amount" value="<?php echo $doc->DiscAmount; ?>" />
          <input type="text" id="disc-amount-label" class="form-control input-sm text-right" onchange="reCalDiscAmount()" value="<?php echo number($doc->DiscAmount, 2); ?>" <?php echo $disabled; ?> />
        </div>
      </div>

      <div class="form-group <?php echo ($doc->TaxStatus == 'Y' ? '' : 'hide'); ?>" id="bill-wht">
        <label class="col-lg-6 col-md-5-harf col-sm-4 col-xs-3 control-label no-padding-right">หัก ณ ที่จ่าย</label>
        <div class="col-lg-2 col-md-2-harf col-sm-3 col-xs-3 padding-5">
          <span class="input-icon input-icon-right">
          <input type="number" id="whtPrcnt" class="form-control input-sm" onchange="recalTotal()" value="<?php echo number($doc->WhtPrcnt, 2); ?>" <?php echo $disabled; ?>/>
          <i class="ace-icon fa fa-percent"></i>
          </span>
        </div>
        <div class="col-lg-4 col-md-4 col-sm-5 col-xs-6 padding-5 last">
          <input type="hidden" id="wht-amount" value="<?php echo $doc->WhtAmount; ?>" />
          <input type="text" id="wht-amount-label" class="form-control input-sm text-right" value="<?php echo number($doc->WhtAmount, 2); ?>" disabled />
        </div>
      </div>

      <div class="form-group <?php echo ($doc->TaxStatus == 'Y' ? '' : 'hide'); ?>" id="bill-vat">
        <label class="col-lg-8 col-md-8 col-sm-7 col-xs-6 control-label no-padding-right">VAT</label>
        <div class="col-lg-4 col-md-4 col-sm-5 col-xs-6 padding-5 last">
          <input type="hidden" id="vat-total" value="0.00"/>
          <input type="text" id="vat-total-label" class="form-control input-sm text-right" value="<?php echo number($doc->VatSum, 2); ?>" disabled />
        </div>
      </div>

      <div class="form-group">
        <label class="col-lg-8 col-md-8 col-sm-7 col-xs-6 control-label no-padding-right">รวมทั้งสิ้น</label>
        <div class="col-lg-4 col-md-4 col-sm-5 col-xs-6 padding-5 last">
          <input type="hidden" id="doc-total" value="<?php echo $doc->DocTotal; ?>"/>
          <input type="text" id="doc-total-label" class="form-control input-sm text-right" value="<?php echo number($doc->DocTotal, 2); ?>" disabled/>
        </div>
      </div>
    </div>
  </div>

  <div class="divider-hidden"></div>
  <div class="divider-hidden"></div>
  <div class="divider-hidden"></div>

  <?php if($mode == 'Add') : ?>
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 text-right">
      <button type="button" class="btn btn-sm btn-primary btn-100 btn-save" id="btn-save" onclick="saveAdd(0)">Save</button>
      <button type="button" class="btn btn-sm btn-warning btn-100 btn-save" onclick="leave()">Cancel</button>
      <button type="button" class="btn btn-sm btn-info btn-100 btn-save" id="btn-draft" onclick="saveAdd(1)">Save AS Draft</button>
    </div>
  <?php endif; ?>
  <?php if($mode == 'Edit') : ?>
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 text-right">
      <button type="button" class="btn btn-sm btn-primary btn-100 btn-save" id="btn-save" onclick="saveUpdate(0)">Save</button>
      <button type="button" class="btn btn-sm btn-warning btn-100 btn-save" onclick="leave()">Cancel</button>
      <button type="button" class="btn btn-sm btn-info btn-100 btn-save" id="btn-draft" onclick="saveUpdate(1)">Save AS Draft</button>
    </div>
  <?php endif; ?>
</div>

<script>
  $('#sale_id').select2();
</script>
