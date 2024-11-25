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
	<div class="form-group">
    <label class="col-lg-4-harf col-md-4 col-sm-3 col-xs-12 control-label no-padding-right">รหัส</label>
    <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
			<input type="text" name="code" id="code" maxlength="20" class="form-control input-sm" value="" onkeyup="validCode(this)" required autofocus />
    </div>
  </div>

  <div class="form-group">
    <label class="col-lg-4-harf col-md-4 col-sm-3 col-xs-12 control-label no-padding-right">ชื่อ</label>
    <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
			<input type="text" name="name" id="name" maxlength="250" class="form-control input-sm" value="" required />
    </div>
  </div>

	<div class="form-group">
    <label class="col-lg-4-harf col-md-4 col-sm-3 col-xs-12 control-label no-padding-right">โซน</label>
    <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
			<input type="text" name="zone" id="zone" maxlength="250" class="form-control input-sm" value=""  />
			<input type="hidden" name="zone_code" id="zone_code" value="" />
    </div>
  </div>

	<div class="form-group">
    <label class="col-lg-4-harf col-md-4 col-sm-3 col-xs-12 control-label no-padding-right">ลูกค้า</label>
    <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
			<input type="text" name="customer" id="customer" maxlength="250" class="form-control input-sm" value=""  />
			<input type="hidden" name="customer_code" id="customer_code" value="" />
    </div>
  </div>

	<div class="form-group">
    <label class="col-lg-4-harf col-md-4 col-sm-4 col-xs-3 control-label no-padding-right">เล่มใบกำกับ</label>
    <div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-3">
			<input type="text" name="prefix" id="prefix" maxlength="3" class="form-control input-sm text-center" value=""  />
    </div>
		<div class="col-lg-2 col-md-2-harf col-sm-2-harf col-xs-3 padding-5 last">
			<div class="input-group width-100">
				<span class="input-group-addon">รันนิ่ง</span>
				<select class="form-control input-sm" name="running" id="running">
					<option value="3">&nbsp;&nbsp; 3 &nbsp;&nbsp;&nbsp;หลัก</option>
					<option value="4">&nbsp;&nbsp; 4 &nbsp;&nbsp;&nbsp;หลัก</option>
					<option value="5">&nbsp;&nbsp; 5 &nbsp;&nbsp;&nbsp;หลัก</option>
					<option value="6">&nbsp;&nbsp; 6 &nbsp;&nbsp;&nbsp;หลัก</option>
				</select>
			</div>
    </div>
  </div>

	<div class="form-group">
    <label class="col-lg-4-harf col-md-4 col-sm-3 col-xs-12 control-label no-padding-right">ช่องทางขาย</label>
    <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
			<select class="form-control input-sm" name="channels" id="channels">
				<option value="">เลือกช่องทางขาย</option>
				<?php echo select_channels(); ?>
			</select>
    </div>
  </div>

	<div class="form-group">
    <label class="col-lg-4-harf col-md-4 col-sm-3 col-xs-12 control-label no-padding-right">เงินสด</label>
    <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
			<select class="form-control input-sm" name="cash" id="cash">
				<option value="">เลือกการชำระเงิน</option>
				<?php echo select_pos_payment_method(); ?>
			</select>
    </div>
  </div>


	<div class="form-group">
    <label class="col-lg-4-harf col-md-4 col-sm-3 col-xs-12 control-label no-padding-right">เงินโอน</label>
    <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
			<select class="form-control input-sm" name="transfer" id="transfer">
				<option value="">เลือกการชำระเงิน</option>
				<?php echo select_pos_payment_method(); ?>
			</select>
    </div>
  </div>

	<div class="form-group">
    <label class="col-lg-4-harf col-md-4 col-sm-3 col-xs-12 control-label no-padding-right">บัตรเครดิต</label>
    <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
			<select class="form-control input-sm" name="card" id="card">
				<option value="">เลือกการชำระเงิน</option>
				<?php echo select_pos_payment_method(); ?>
			</select>
    </div>
  </div>

	<div class="form-group">
    <label class="col-lg-4-harf col-md-4 col-sm-3 col-xs-12 control-label no-padding-right">ขนาดตัวอักษร</label>
    <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
			<select class="form-control input-sm input-medium" name="text-size" id="text-size">
				<option value="9">9 px</option>
				<option value="10" selected>10 px</option>
				<option value="11">11 px</option>
				<option value="12">12 px</option>
				<option value="13">13 px</option>
				<option value="14">14 px</option>
				<option value="15">15 px</option>
				<option value="16">16 px</option>
			</select>
    </div>
  </div>

	<div class="form-group">
    <label class="col-lg-4-harf col-md-4 col-sm-3 col-xs-12 control-label no-padding-right">ข้อความหัวบิล 1</label>
    <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
			<input type="text" id="bill-header-1" maxlength="100" class="form-control input-sm" value="<?php echo getConfig('COMPANY_FULL_NAME') ;?>"  />
    </div>
		<div class="col-lg-1-harf col-md-1-harf col-sm-2 padding-5">
			<div class="input-group width-100">
				<span class="input-group-addon">ขนาด</span>
				<input type="number" class="form-control input-sm text-center" id="header-size-1" value="14" />
			</div>
    </div>
		<div class="col-lg-2 col-md-2 col-sm-3 padding-5">
			<div class="input-group width-100">
				<span class="input-group-addon">ตำแหน่ง</span>
				<select class="form-control input-sm" id="header-align-1">
					<option value="text-center">&nbsp;&nbsp;&nbsp;&nbsp;กลาง</option>
					<option value="text-left">&nbsp;&nbsp;&nbsp;&nbsp;ซ้าย</option>
					<option value="text-right">&nbsp;&nbsp;&nbsp;&nbsp;ขวา</option>
				</select>
			</div>
    </div>
  </div>

	<div class="form-group">
    <label class="col-lg-4-harf col-md-4 col-sm-3 col-xs-12 control-label no-padding-right">ข้อความหัวบิล 2</label>
    <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
			<input type="text" name="bill_header_2" id="bill-header-2" maxlength="100" class="form-control input-sm" value="ใบเสร็จรับเงิน/ใบกำกับภาษีอย่างย่อ"  />
    </div>
		<div class="col-lg-1-harf col-md-1-harf col-sm-2 padding-5">
			<div class="input-group width-100">
				<span class="input-group-addon">ขนาด</span>
				<input type="number" class="form-control input-sm text-center" id="header-size-2" value="14" />
			</div>
    </div>
		<div class="col-lg-2 col-md-2 col-sm-3 padding-5">
			<div class="input-group width-100">
				<span class="input-group-addon">ตำแหน่ง</span>
				<select class="form-control input-sm" id="header-align-2">
					<option value="text-center">&nbsp;&nbsp;&nbsp;&nbsp;กลาง</option>
					<option value="text-left">&nbsp;&nbsp;&nbsp;&nbsp;ซ้าย</option>
					<option value="text-right">&nbsp;&nbsp;&nbsp;&nbsp;ขวา</option>
				</select>
			</div>
    </div>
  </div>

	<div class="form-group">
    <label class="col-lg-4-harf col-md-4 col-sm-3 col-xs-12 control-label no-padding-right">ข้อความหัวบิล 3</label>
    <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
			<input type="text" name="bill_header_3" id="bill-header-3" maxlength="100" class="form-control input-sm" value="** VAT INCLUDED **"  />
    </div>
		<div class="col-lg-1-harf col-md-1-harf col-sm-2 padding-5">
			<div class="input-group width-100">
				<span class="input-group-addon">ขนาด</span>
				<input type="number" class="form-control input-sm text-center" id="header-size-3" value="14" />
			</div>
    </div>
		<div class="col-lg-2 col-md-2 col-sm-3 padding-5">
			<div class="input-group width-100">
				<span class="input-group-addon">ตำแหน่ง</span>
				<select class="form-control input-sm" id="header-align-3">
					<option value="text-center">&nbsp;&nbsp;&nbsp;&nbsp;กลาง</option>
					<option value="text-left">&nbsp;&nbsp;&nbsp;&nbsp;ซ้าย</option>
					<option value="text-right">&nbsp;&nbsp;&nbsp;&nbsp;ขวา</option>
				</select>
			</div>
    </div>
  </div>

	<div class="form-group">
    <label class="col-lg-4-harf col-md-4 col-sm-3 col-xs-12 control-label no-padding-right">ข้อความท้ายบิล</label>
    <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
			<input type="text" id="bill-footer" maxlength="250" class="form-control input-sm" value="THANK YOU"  />
    </div>
		<div class="col-lg-1-harf col-md-1-harf col-sm-2 padding-5">
			<div class="input-group width-100">
				<span class="input-group-addon">ขนาด</span>
				<input type="number" class="form-control input-sm text-center" id="footer-size" value="14" />
			</div>
    </div>
  </div>

	<div class="form-group">
    <label class="col-lg-4-harf col-md-4 col-sm-3 col-xs-12 control-label no-padding-right">เลขประจำตัวผู้เสียภาษี</label>
    <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
			<input type="text" name="tax_id" id="tax_id" maxlength="20" class="form-control input-sm" value=""  />
    </div>
  </div>

	<div class="form-group">
 	 <label class="col-lg-4-harf col-md-4 col-sm-3 col-xs-12 control-label no-padding-right">VAT</label>
 	 <div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-12">
		 <select class="form-control input-sm" id="use_vat">
			 <option value="1">มี</option>
			 <option value="0">ไม่มี</option>
		 </select>
 	 </div>
  </div>

	<div class="form-group">
 	 <label class="col-lg-4-harf col-md-4 col-sm-3 col-xs-12 control-label no-padding-right">สถานะ</label>
 	 <div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-12">
		 <select class="form-control input-sm" id="active">
			 <option value="1">Active</option>
			 <option value="0">Inactive</option>
		 </select>
 	 </div>
  </div>

	<div class="form-group">
		<label class="col-lg-4-harf col-md-4 col-sm-3 col-xs-12 control-label no-padding-right">พิมพ์บาร์โค้ด</label>
		<div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-12">
			<select class="form-control input-sm" id="barcode">
 			 <option value="1">พิมพ์</option>
 			 <option value="0">ไม่พิมพ์</option>
 		 </select>
		</div>
	</div>

<?php if($this->pm->can_add) : ?>
	<div class="form-group">
    <label class="col-sm-3 control-label no-padding-right hidden-xs"></label>
    <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
			<button type="button" class="btn btn-sm btn-success pull-right" id="btn-save" onclick="save()"><i class="fa fa-save"></i> Save</button>
    </div>
  </div>
<?php endif; ?>

</form>

<script src="<?php echo base_url(); ?>scripts/masters/shop.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/code_validate.js"></script>
<?php $this->load->view('include/footer'); ?>
