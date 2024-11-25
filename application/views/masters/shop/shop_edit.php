<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 padding-5">
    <h3 class="title"><?php echo $this->title; ?></h3>
  </div>
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 padding-5">
		<p class="pull-right top-p">
			<button type="button" class="btn btn-sm btn-warning" onclick="goBack()"><i class="fa fa-arrow-left"></i> Back</button>
		</p>
	</div>
</div><!-- End Row -->
<hr class="padding-5"/>

<form class="form-horizontal margin-top-30">
	<input type="hidden" name="shop_id" id="shop_id" value="<?php echo $shop->id; ?>" />
	<input type="hidden" name="code" id="code" value="<?php echo $shop->code; ?>" />
	<input type="hidden" name="old_name" id="old_name" value="<?php echo $shop->name; ?>" />
	<div class="form-group">
    <label class="col-lg-4-harf col-md-4 col-sm-3 col-xs-12 control-label no-padding-right">รหัส</label>
    <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
			<input type="text"  class="form-control input-sm" value="<?php echo $shop->code; ?>" disabled />
    </div>
  </div>

  <div class="form-group">
    <label class="col-lg-4-harf col-md-4 col-sm-3 col-xs-12 control-label no-padding-right">ชื่อ</label>
    <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
			<input type="text" name="name" id="name" maxlength="250" class="form-control input-sm" value="<?php echo $shop->name; ?>" required />
    </div>
  </div>

	<div class="form-group">
    <label class="col-lg-4-harf col-md-4 col-sm-3 col-xs-12 control-label no-padding-right">โซน</label>
    <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
			<input type="text" name="zone" id="zone" maxlength="250" class="form-control input-sm" value="<?php echo $shop->zone_name; ?>"  />
			<input type="hidden" name="zone_code" id="zone_code" value="<?php echo $shop->zone_code; ?>" />
    </div>
  </div>

	<div class="form-group">
    <label class="col-lg-4-harf col-md-4 col-sm-3 col-xs-12 control-label no-padding-right">ลูกค้า</label>
    <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
			<input type="text" name="customer" id="customer" maxlength="250" class="form-control input-sm" value="<?php echo $shop->customer_name; ?>"  />
			<input type="hidden" name="customer_code" id="customer_code" value="<?php echo $shop->customer_code; ?>" />
    </div>
  </div>

	<div class="form-group">
    <label class="col-lg-4-harf col-md-4 col-sm-4 col-xs-3 control-label no-padding-right">เล่มใบกำกับ</label>
    <div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-3">
			<input type="text" name="prefix" id="prefix" maxlength="3" class="form-control input-sm text-center" value="<?php echo $shop->prefix; ?>"  />
    </div>
		<div class="col-lg-2 col-md-2-harf col-sm-2-harf col-xs-3 padding-5 last">
			<div class="input-group width-100">
				<span class="input-group-addon">รันนิ่ง</span>
				<select class="form-control input-sm" name="running" id="running">
					<option value="3" <?php echo is_selected('3', $shop->running); ?>>&nbsp;&nbsp; 3 &nbsp;&nbsp;&nbsp;หลัก</option>
					<option value="4" <?php echo is_selected('4', $shop->running); ?>>&nbsp;&nbsp; 4 &nbsp;&nbsp;&nbsp;หลัก</option>
					<option value="5" <?php echo is_selected('5', $shop->running); ?>>&nbsp;&nbsp; 5 &nbsp;&nbsp;&nbsp;หลัก</option>
					<option value="6" <?php echo is_selected('6', $shop->running); ?>>&nbsp;&nbsp; 6 &nbsp;&nbsp;&nbsp;หลัก</option>
				</select>
			</div>
    </div>
  </div>

	<div class="form-group">
    <label class="col-lg-4-harf col-md-4 col-sm-3 col-xs-12 control-label no-padding-right">ช่องทางขาย</label>
    <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
			<select class="form-control input-sm" name="channels" id="channels">
				<option value="">เลือกช่องทางขาย</option>
				<?php echo select_channels($shop->channels_code); ?>
			</select>
    </div>
  </div>

	<div class="form-group">
    <label class="col-lg-4-harf col-md-4 col-sm-3 col-xs-12 control-label no-padding-right">เงินสด</label>
    <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
			<select class="form-control input-sm" name="cash" id="cash">
				<option value="">เลือกการชำระเงิน</option>
				<?php echo select_pos_payment_method($shop->cash_payment); ?>
			</select>
    </div>
  </div>


	<div class="form-group">
    <label class="col-lg-4-harf col-md-4 col-sm-3 col-xs-12 control-label no-padding-right">เงินโอน</label>
    <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
			<select class="form-control input-sm" name="transfer" id="transfer">
				<option value="">เลือกการชำระเงิน</option>
				<?php echo select_pos_payment_method($shop->transfer_payment); ?>
			</select>
    </div>
  </div>

	<div class="form-group">
    <label class="col-lg-4-harf col-md-4 col-sm-3 col-xs-12 control-label no-padding-right">บัตรเครดิต</label>
    <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
			<select class="form-control input-sm" name="card" id="card">
				<option value="">เลือกการชำระเงิน</option>
				<?php echo select_pos_payment_method($shop->card_payment); ?>
			</select>
    </div>
  </div>

	<div class="form-group">
    <label class="col-lg-4-harf col-md-4 col-sm-3 col-xs-12 control-label no-padding-right">ขนาดตัวอักษร</label>
    <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
			<select class="form-control input-sm input-medium" name="text-size" id="text-size">
				<option value="9" <?php echo is_selected('9', $shop->font_size); ?>>9 px</option>
				<option value="10" <?php echo is_selected('10', $shop->font_size); ?>>10 px</option>
				<option value="11" <?php echo is_selected('11', $shop->font_size); ?>>11 px</option>
				<option value="12" <?php echo is_selected('12', $shop->font_size); ?>>12 px</option>
				<option value="13" <?php echo is_selected('13', $shop->font_size); ?>>13 px</option>
				<option value="14" <?php echo is_selected('14', $shop->font_size); ?>>14 px</option>
				<option value="15" <?php echo is_selected('15', $shop->font_size); ?>>15 px</option>
				<option value="16" <?php echo is_selected('16', $shop->font_size); ?>>16 px</option>
			</select>
    </div>
  </div>

	<div class="form-group">
    <label class="col-lg-4-harf col-md-4 col-sm-3 col-xs-12 control-label no-padding-right">ข้อความหัวบิล 1</label>
    <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
			<input type="text" id="bill-header-1" maxlength="100" class="form-control input-sm" value="<?php echo $shop->bill_header_1; ?>"  />
    </div>
		<div class="col-lg-1-harf col-md-1-harf col-sm-2 padding-5">
			<div class="input-group width-100">
				<span class="input-group-addon">ขนาด</span>
				<input type="number" class="form-control input-sm text-center" id="header-size-1" value="<?php echo $shop->header_size_1; ?>" />
			</div>
    </div>
		<div class="col-lg-2 col-md-2 col-sm-3 padding-5">
			<div class="input-group width-100">
				<span class="input-group-addon">ตำแหน่ง</span>
				<select class="form-control input-sm" id="header-align-1">
					<option value="text-center" <?php echo is_selected('text-center', $shop->header_align_1); ?>>&nbsp;&nbsp;&nbsp;&nbsp;กลาง</option>
					<option value="text-left" <?php echo is_selected('text-left', $shop->header_align_1); ?>>&nbsp;&nbsp;&nbsp;&nbsp;ซ้าย</option>
					<option value="text-right" <?php echo is_selected('text-right', $shop->header_align_1); ?>>&nbsp;&nbsp;&nbsp;&nbsp;ขวา</option>
				</select>
			</div>
    </div>
  </div>

	<div class="form-group">
    <label class="col-lg-4-harf col-md-4 col-sm-3 col-xs-12 control-label no-padding-right">ข้อความหัวบิล 2</label>
    <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
			<input type="text" name="bill_header_2" id="bill-header-2" maxlength="100" class="form-control input-sm" value="<?php echo $shop->bill_header_2; ?>"  />
    </div>
		<div class="col-lg-1-harf col-md-1-harf col-sm-2 padding-5">
			<div class="input-group width-100">
				<span class="input-group-addon">ขนาด</span>
				<input type="number" class="form-control input-sm text-center" id="header-size-2" value="<?php echo $shop->header_size_2; ?>" />
			</div>
    </div>
		<div class="col-lg-2 col-md-2 col-sm-3 padding-5">
			<div class="input-group width-100">
				<span class="input-group-addon">ตำแหน่ง</span>
				<select class="form-control input-sm" id="header-align-2">
					<option value="text-center" <?php echo is_selected('text-center', $shop->header_align_2); ?>>&nbsp;&nbsp;&nbsp;&nbsp;กลาง</option>
					<option value="text-left" <?php echo is_selected('text-left', $shop->header_align_2); ?>>&nbsp;&nbsp;&nbsp;&nbsp;ซ้าย</option>
					<option value="text-right" <?php echo is_selected('text-right', $shop->header_align_2); ?>>&nbsp;&nbsp;&nbsp;&nbsp;ขวา</option>
				</select>
			</div>
    </div>
  </div>

	<div class="form-group">
    <label class="col-lg-4-harf col-md-4 col-sm-3 col-xs-12 control-label no-padding-right">ข้อความหัวบิล 3</label>
    <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
			<input type="text" name="bill_header_3" id="bill-header-3" maxlength="100" class="form-control input-sm" value="<?php echo $shop->bill_header_3; ?>"  />
    </div>
		<div class="col-lg-1-harf col-md-1-harf col-sm-2 padding-5">
			<div class="input-group width-100">
				<span class="input-group-addon">ขนาด</span>
				<input type="number" class="form-control input-sm text-center" id="header-size-3" value="<?php echo $shop->header_size_3; ?>" />
			</div>
    </div>
		<div class="col-lg-2 col-md-2 col-sm-3 padding-5">
			<div class="input-group width-100">
				<span class="input-group-addon">ตำแหน่ง</span>
				<select class="form-control input-sm" id="header-align-3">
					<option value="text-center" <?php echo is_selected('text-center', $shop->header_align_3); ?>>&nbsp;&nbsp;&nbsp;&nbsp;กลาง</option>
					<option value="text-left" <?php echo is_selected('text-left', $shop->header_align_3); ?>>&nbsp;&nbsp;&nbsp;&nbsp;ซ้าย</option>
					<option value="text-right" <?php echo is_selected('text-right', $shop->header_align_3); ?>>&nbsp;&nbsp;&nbsp;&nbsp;ขวา</option>
				</select>
			</div>
    </div>
  </div>

	<div class="form-group">
    <label class="col-lg-4-harf col-md-4 col-sm-3 col-xs-12 control-label no-padding-right">ข้อความท้ายบิล</label>
    <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
			<input type="text" id="bill-footer" maxlength="250" class="form-control input-sm" value="<?php echo $shop->bill_footer; ?>"  />
    </div>
		<div class="col-lg-1-harf col-md-1-harf col-sm-2 padding-5">
			<div class="input-group width-100">
				<span class="input-group-addon">ขนาด</span>
				<input type="number" class="form-control input-sm text-center" id="footer-size" value="<?php echo $shop->footer_size; ?>" />
			</div>
    </div>
  </div>


	<div class="form-group">
    <label class="col-lg-4-harf col-md-4 col-sm-3 col-xs-12 control-label no-padding-right">เลขประจำตัวผู้เสียภาษี</label>
    <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
			<input type="text" name="tax_id" id="tax_id" maxlength="20" class="form-control input-sm" value="<?php echo $shop->tax_id; ?>"  />
    </div>
  </div>

	<div class="form-group">
 	 <label class="col-lg-4-harf col-md-4 col-sm-3 col-xs-12 control-label no-padding-right">VAT</label>
 	 <div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-12">
		 <select class="form-control input-sm" id="use_vat">
			 <option value="0" <?php echo is_selected('0', $shop->use_vat); ?>>ไม่มี</option>
			 <option value="1" <?php echo is_selected('1', $shop->use_vat); ?>>มี</option>
		 </select>
 	 </div>
  </div>

	<div class="form-group">
 	 <label class="col-lg-4-harf col-md-4 col-sm-3 col-xs-12 control-label no-padding-right">สถานะ</label>
 	 <div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-12">
		 <select class="form-control input-sm" id="active">
			 <option value="1" <?php echo is_selected('1', $shop->active); ?>>Active</option>
			 <option value="0" <?php echo is_selected('0', $shop->active); ?>>Inactive</option>
		 </select>
 	 </div>
  </div>

	<div class="form-group">
		<label class="col-lg-4-harf col-md-4 col-sm-3 col-xs-12 control-label no-padding-right">พิมพ์บาร์โค้ด</label>
		<div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-12">
			<select class="form-control input-sm" id="barcode">
 			 <option value="1" <?php echo is_selected('1', $shop->barcode); ?>>พิมพ์</option>
 			 <option value="0" <?php echo is_selected('0', $shop->barcode); ?>>ไม่พิมพ์</option>
 		 </select>
		</div>
	</div>

<?php if($this->pm->can_edit) : ?>
	<div class="divider-hidden"></div>
	<div class="divider-hidden"></div>
	<div class="divider-hidden"></div>

	<div class="form-group">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-center">
			<button type="button" class="btn btn-sm btn-success btn-100" id="btn-save" onclick="update()"><i class="fa fa-save"></i> Update</button>
    </div>
  </div>
<?php endif; ?>

</form>

<hr class="padding-5 margin-top-20 margin-bottom-20"/>

<div class="row">
	<?php $this->load->view('masters/shop/shop_user'); ?>
</div>

<script src="<?php echo base_url(); ?>scripts/masters/shop.js?v=<?php echo date('YmdH'); ?>"></script>
<?php $this->load->view('include/footer'); ?>
