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
			<input type="text" class="form-control input-sm text-center edit" id="customer-code"  value="<?php echo $order->customer_code; ?>" disabled/>
		</div>
    <div class="col-lg-3-harf col-md-6-harf col-sm-6-harf col-xs-12 padding-5">
    	<label>ลูกค้า[ในระบบ]</label>
			<input type="text" class="form-control input-sm edit" id="customer" name="customer" value="<?php echo $order->customer_name; ?>" required disabled />
    </div>
    <div class="col-lg-2 col-md-2-harf col-sm-3 col-xs-6 padding-5 ">
    	<label>ผู้เบิก[คนสั่ง]</label>
      <input type="text" class="form-control input-sm edit" id="empName" name="empName" value="<?php echo $order->user_ref; ?>" disabled />
    </div>

		<div class="col-lg-2 col-md-3-harf col-sm-4 col-xs-6 padding-5">
			<label>โซนแปรสภาพ</label>
			<input type="text" class="form-control input-sm edit" name="zone" id="zone" placeholder="ระบุโซนแปรสภาพ" value="<?php echo $order->zone_name; ?>" disabled>
		</div>

		<div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-4 padding-5">
    	<label>อ้างอิง</label>
        <input type="text" class="form-control input-sm text-center edit" name="reference" id="reference" value="<?php echo $order->reference; ?>" disabled />
    </div>

		<div class="col-lg-2 col-md-4-harf col-sm-3 col-xs-8 padding-5">
			<label>คลัง</label>
	    <select class="form-control input-sm edit" name="warehouse" id="warehouse" required disabled>
				<option value="">เลือกคลัง</option>
				<?php echo select_sell_warehouse($order->warehouse_code); ?>
			</select>
	  </div>

		<?php if(empty($approve_view) && ($this->pm->can_add OR $this->pm->can_edit)): ?>
			<div class="col-lg-5-harf col-md-7-harf col-sm-7-harf col-xs-12 padding-5">
			 	<label>หมายเหตุ</label>
			  <input type="text" class="form-control input-sm edit" name="remark" id="remark" value="<?php echo $order->remark; ?>" disabled />
			</div>
			<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-4 padding-5">
				<label>SAP No.</label>
				<input type="text" class="form-control input-sm text-center" value="<?php echo $order->inv_code; ?>" disabled />
			</div>
			<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-4 padding-5">
				<label>สถานะ</label>
				<input type="text" class="form-control input-sm text-center" id="transform-status" value="<?php echo $this->isClosed == TRUE ? 'Closed' : 'Open'; ?>" disabled />
			</div>
			<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-4 padding-5">
				<label class="display-block not-show">แก้ไข</label>
				<button type="button" class="btn btn-xs btn-warning btn-block" id="btn-edit" onclick="getEdit()"><i class="fa fa-pencil"></i> แก้ไข</i></button>
				<button type="button" class="btn btn-xs btn-success btn-block hide" id="btn-update" onclick="validUpdate()"><i class="fa fa-save"></i> บันทึก</i></button>
			</div>
	<?php else : ?>
			<div class="col-lg-6-harf col-md-9 col-sm-9 col-xs-6 padding-5">
				<label>หมายเหตุ</label>
				<input type="text" class="form-control input-sm edit" name="remark" id="remark" value="<?php echo $order->remark; ?>" disabled />
			</div>
			<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-3 padding-5">
				<label>SAP No.</label>
				<input type="text" class="form-control input-sm text-center" value="<?php echo $order->inv_code; ?>" disabled />
			</div>
			<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-3 padding-5">
				<label>สถานะ</label>
				<input type="text" class="form-control input-sm text-center" id="transform-status" value="<?php echo $this->isClosed == TRUE ? 'Closed' : 'Open'; ?>" disabled />
			</div>
		<?php endif; ?>



		<input type="hidden" id="require_remark" value="<?php echo empty($this->require_remark) ? 0 : 1; ?>" />
    <input type="hidden" name="order_code" id="order_code" value="<?php echo $order->code; ?>" />
    <input type="hidden" name="customerCode" id="customerCode" value="<?php echo $order->customer_code; ?>" />
		<input type="hidden" id="role" name="role" value="<?php echo $this->role; ?>" />
		<input type="hidden" id="is_approved" value="<?php echo $order->is_approved; ?>" />
		<input type="hidden" name="zoneCode" id="zoneCode" value="<?php echo $order->zone_code; ?>">	
		<input type="hidden" name="address_id" id="address_id" value="<?php echo $order->id_address; //--- id_address ใช้แล้วใน online modal?>" />
</div>
<hr class="margin-bottom-15 padding-5"/>
