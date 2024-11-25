<div class="row">
	<div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-6 padding-5">
    	<label>เลขที่เอกสาร</label>
        <input type="text" class="form-control input-sm text-center" value="<?php echo $order->code; ?>" disabled />
    </div>
    <div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-6 padding-5">
    	<label>วันที่</label>
			<input type="text" class="form-control input-sm text-center edit" name="date" id="date" value="<?php echo thai_date($order->date_add); ?>" disabled />
    </div>
    <div class="col-lg-2-harf col-md-4-harf col-sm-4 col-xs-6 padding-5">
    	<label>ผู้เบิก</label>
			<input type="text" class="form-control input-sm edit" id="empName" name="empName" value="<?php echo $order->empName; ?>" required disabled />
    </div>
		<div class="col-lg-2-harf col-md-4 col-sm-4 col-xs-6 padding-5">
			<label>ผู้รับ</label>
			<input type="text" class="form-control input-sm edit" id="user_ref" name="user_ref" value="<?php echo $order->user_ref; ?>" disabled />
		</div>
		<div class="col-lg-4 col-md-5 col-sm-5 col-xs-6 padding-5">
	    <label>โซน[คลังยืม]</label>
			<input type="text" class="form-control input-sm edit" name="zone" id="zone" value="<?php echo $order->zone_name; ?>" disabled />
	  </div>
		<div class="col-lg-2 col-md-4 col-sm-4 col-xs-6 padding-5">
			<label>คลัง</label>
	    <select class="form-control input-sm edit" name="warehouse" id="warehouse" disabled required>
				<option value="">เลือกคลัง</option>
				<?php echo select_sell_warehouse($order->warehouse_code); ?>
			</select>
	  </div>
		<div class="col-lg-2 col-md-3 col-sm-3 col-xs-6 padding-5">
			<label>ผู้ทำรายการ</label>
		  <input type="text" class="form-control input-sm" value="<?php echo $order->user; ?>" disabled />
		</div>

		<?php if(empty($approve_view) && ($this->pm->can_add OR $this->pm->can_edit)): ?>
			<div class="col-lg-6-harf col-md-10-harf col-sm-10-harf col-xs-6 padding-5">
			 	<label>หมายเหตุ</label>
			  <input type="text" class="form-control input-sm edit" name="remark" id="remark" value="<?php echo $order->remark; ?>" disabled />
			</div>
			<?php if($order->state >= 8) : ?>
				<div class="col-sm-1 col-1-harf col-xs-6 padding-5">
					<label class="display-block">SAN No.</label>
					<input type="text" class="form-control input-sm text-center" value="<?php echo $order->inv_code; ?>" disabled />
				</div>
			<?php else : ?>
				<div class="col-sm-1 col-1-harf col-xs-6 padding-5">
					<label class="display-block not-show">แก้ไข</label>
					<button type="button" class="btn btn-xs btn-warning btn-block" id="btn-edit" onclick="getEdit()"><i class="fa fa-pencil"></i> แก้ไข</i></button>
					<button type="button" class="btn btn-xs btn-success btn-block hide" id="btn-update" onclick="validUpdate()"><i class="fa fa-save"></i> บันทึก</i></button>
				</div>
			<?php endif; ?>
	<?php else : ?>
		<div class="col-sm-8 col-xs-12 padding-5">
			<label>หมายเหตุ</label>
			<input type="text" class="form-control input-sm edit" name="remark" id="remark" value="<?php echo $order->remark; ?>" disabled />
		</div>
		<?php endif; ?>


    <input type="hidden" name="order_code" id="order_code" value="<?php echo $order->code; ?>" />
    <input type="hidden" name="empID" id="empID" value="<?php echo $order->empID; ?>" />
		<input type="hidden" name="customerCode" id="customerCode" value="<?php echo $order->empID; ?>" />
		<input type="hidden" id="is_approved" value="<?php echo $order->is_approved; ?>" />
		<input type="hidden" name="zone_code" id="zone_code" value="<?php echo $order->zone_code; ?>" />
		<input type="hidden" name="is_wms" id="is_wms" value="<?php echo $order->is_wms; ?>" />
		<input type="hidden" name="address_id" id="address_id" value="<?php echo $order->id_address; //--- id_address ใช้แล้วใน online modal?>" />
</div>
<hr class="margin-bottom-15"/>
