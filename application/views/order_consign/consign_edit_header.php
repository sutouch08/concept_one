<div class="row">
	<div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-4 padding-5">
    	<label>เลขที่เอกสาร</label>
        <input type="text" class="form-control input-sm text-center" value="<?php echo $order->code; ?>" disabled />
    </div>
    <div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-4 padding-5">
    	<label>วันที่</label>
			<input type="text" class="form-control input-sm text-center edit" name="date" id="date" value="<?php echo thai_date($order->date_add); ?>" disabled />
    </div>
		<div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-4 padding-5">
    	<label>รหัสลูกค้า</label>
			<input type="text" class="form-control input-sm text-center edit" id="customer-code" value="<?php echo $order->customer_code; ?>" disabled />
    </div>
    <div class="col-lg-4 col-md-5-harf col-sm-5 col-xs-12 padding-5">
    	<label>ลูกค้า[ในระบบ]</label>
			<input type="text" class="form-control input-sm edit" id="customer" name="customer" value="<?php echo $order->customer_name; ?>" required disabled />
    </div>
		<div class="col-lg-1 col-md-1 col-sm-1-harf col-xs-3 padding-5">
			<label>GP</label>
			<input type="text" class="form-control input-sm text-center edit" name="gp" id="gp" value="<?php echo $order->gp; ?>" disabled />
		</div>
		<div class="col-lg-2-harf col-md-4 col-sm-4 col-xs-9 padding-5">
			<label>คลัง[ต้นทาง]</label>
			<select class="form-control input-sm edit" name="warehouse" id="warehouse" required disabled>
				<option value="">เลือกคลัง</option>
				<?php echo select_sell_warehouse($order->warehouse_code); ?>
			</select>
		</div>
		<div class="col-lg-2 col-md-2-harf col-sm-3 col-xs-4 padding-5">
			<label>โซน[ปลายทาง]</label>
			<input type="text" class="form-control input-sm edit" name="zone_code" id="zone_code" value="<?php echo $order->zone_code; ?>" readonly disabled />
		</div>
		<div class="col-lg-4 col-md-5-harf col-sm-5 col-xs-8 padding-5">
	    <label class="not-show">โซน[ฝากขาย]</label>
			<input type="text" class="form-control input-sm edit" name="zone" id="zone" value="<?php echo $order->zone_name; ?>" required disabled/>
	  </div>


		<?php if(empty($approve_view) && ($this->pm->can_add OR $this->pm->can_edit)): ?>
		<div class="col-lg-5 col-md-10-harf col-sm-10-harf col-xs-9 padding-5">
		 	<label>หมายเหตุ</label>
		  <input type="text" class="form-control input-sm edit" name="remark" id="remark" value="<?php echo $order->remark; ?>" disabled />
		</div>
		<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-3 padding-5">
			<label class="display-block not-show">แก้ไข</label>
			<button type="button" class="btn btn-xs btn-warning btn-block" id="btn-edit" onclick="getEdit()"><i class="fa fa-pencil"></i> แก้ไข</i></button>
			<button type="button" class="btn btn-xs btn-success btn-block hide" id="btn-update" onclick="validUpdate()"><i class="fa fa-save"></i> บันทึก</i></button>
		</div>
		<?php else : ?>
			<div class="col-lg-6 col-md-12 col-sm-12 col-xs-12 padding-5">
			 	<label>หมายเหตุ</label>
			  <input type="text" class="form-control input-sm edit" name="remark" id="remark" value="<?php echo $order->remark; ?>" disabled />
			</div>
		<?php endif; ?>

    <input type="hidden" name="order_code" id="order_code" value="<?php echo $order->code; ?>" />
    <input type="hidden" name="customerCode" id="customerCode" value="<?php echo $order->customer_code; ?>" />
		<input type="hidden" id="is_approved" value="<?php echo $order->is_approved; ?>" />		
		<input type="hidden" name="address_id" id="address_id" value="<?php echo $order->id_address; //--- id_address ใช้แล้วใน online modal?>" />
</div>
<hr class="margin-bottom-15"/>
