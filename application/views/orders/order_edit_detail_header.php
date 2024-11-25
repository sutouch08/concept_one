<div class="row">
	<div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-6 padding-5">
    	<label>เลขที่เอกสาร</label>
      <input type="text" class="form-control input-sm text-center" value="<?php echo $order->code; ?>" disabled />
    </div>
    <div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
    	<label>วันที่</label>
			<input type="text" class="form-control input-sm text-center edit" name="date" id="date" value="<?php echo thai_date($order->date_add); ?>" disabled readonly />
    </div>
		<div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-4 padding-5">
			<label>รหัสลูกค้า</label>
			<input type="text" class="form-control input-sm text-center edit" id="customer_code" name="customer_code" value="<?php echo $order->customer_code; ?>" disabled />
		</div>
    <div class="col-lg-3-harf col-md-3 col-sm-4 col-xs-8 padding-5">
    	<label>ลูกค้า[ในระบบ]</label>
			<input type="text" class="form-control input-sm edit" id="customer" name="customer" value="<?php echo $order->customer_name; ?>" required disabled />
    </div>

    <div class="col-lg-2-harf col-md-2-harf col-sm-2-harf col-xs-6 padding-5">
    	<label>อ้างอิงลูกค้า</label>
      <input type="text" class="form-control input-sm edit" id="customer_ref" name="customer_ref" value="<?php echo str_replace('"', '&quot;',$order->customer_ref); ?>" disabled />
    </div>

		<div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-6 padding-5">
	    <label>เบอร์โทร</label>
			<input type="text" class="form-control input-sm edit" id="phone" name="phone" value="<?php echo $order->phone; ?>" required disabled/>
	  </div>

		<div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-6 padding-5">
			<label>อ้างอิง</label>
		  <input type="text" class="form-control input-sm text-center edit" name="reference" id="reference" value="<?php echo $order->reference; ?>" disabled />
		</div>

    <div class="col-lg-1-harf col-md-2 col-sm-2-harf col-xs-6 padding-5">
    	<label>ช่องทางขาย</label>
			<select class="form-control input-sm edit" name="channels" id="channels" required disabled>
				<option value="">เลือกรายการ</option>
				<?php echo select_channels($order->channels_code); ?>
			</select>
    </div>

    <div class="col-lg-1-harf col-md-2 col-sm-2-harf col-xs-6 padding-5">
    	<label>การชำระเงิน</label>
			<select class="form-control input-sm edit" name="payment" id="payment" required disabled>
				<option value="">เลือกรายการ</option>
				<?php echo select_payment_method($order->payment_code); ?>
			</select>
    </div>

		<div class="col-lg-2-harf col-md-2 col-sm-3 col-xs-6 padding-5">
	    <label>เจ้าของ</label>
			<input type="text" class="form-control input-sm" value="<?php echo $this->_user->name; ?>" disabled/>
	  </div>

		<div class="col-lg-2-harf col-md-2-harf col-sm-2-harf col-xs-6 padding-5">
			<label>พนักงานขาย</label>
			<select class="width-100 edit" disabled>
				<option value="-1">No Sale Emoloyee</option>
				<?php echo select_saleman($order->sale_code); ?>
			</select>
	  </div>

		<div class="col-lg-2-harf col-md-2 col-sm-2-harf col-xs-6 padding-5">
			<label>คลัง</label>
	    <select class="form-control input-sm edit" name="warehouse" id="warehouse" disabled>
				<option value="">เลือกคลัง</option>
				<?php echo select_warehouse($order->warehouse_code); ?>
			</select>
	  </div>
		<div class="col-lg-11 col-md-10-harf col-sm-5-harf col-xs-8 padding-5">
		 	<label>หมายเหตุ</label>
		  <input type="text" class="form-control input-sm edit" name="remark" id="remark" value="<?php echo $order->remark; ?>" disabled />
		</div>

		<?php if($order->state < 4 && $order->is_expired == 0 && ($this->pm->can_add OR $this->pm->can_edit)): ?>
		<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-4 padding-5">
			<label class="display-block not-show">แก้ไข</label>
			<button type="button" class="btn btn-xs btn-warning btn-block" id="btn-edit" onclick="getEdit()"><i class="fa fa-pencil"></i> แก้ไข</i></button>
			<button type="button" class="btn btn-xs btn-success btn-block hide" id="btn-update" onclick="validUpdate()"><i class="fa fa-save"></i> บันทึก</i></button>
		</div>
		<?php endif; ?>
    <input type="hidden" name="customerCode" id="customerCode" value="<?php echo $order->customer_code; ?>" />
		<input type="hidden" name="order_code" id="order_code" value="<?php echo $order->code; ?>" />
</div>
<hr class="margin-bottom-15"/>
