<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5">
    <h4 class="title"><?php echo $this->title; ?></h4>
    </div>
    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5">
    	<p class="pull-right top-p">
        <button type="button" class="btn btn-sm btn-warning" onclick="goBack()"><i class="fa fa-arrow-left"></i> กลับ</button>
				<button type="button" class="btn btn-sm btn-success btn-top" onclick="showCustomerModal()">ข้อมูลลูกค้า</button>
      </p>
    </div>
</div><!-- End Row -->
<hr class=""/>
<div class="row">
  <div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
    <label>เลขที่เอกสาร</label>
		<input type="text" class="form-control input-sm" value="" disabled />
  </div>
	<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
		<label>เล่มเอกสาร</label>
		<select class="form-control input-sm h" id="is-term">			
			<option value="1">อภินันท์</option>
		</select>
	</div>
	<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
		<label>ชนิด VAT</label>
		<select class="form-control input-sm h" id="vat-type" onchange="updateTaxStatus()">
			<option value="">เลือก</option>
			<option value="E">แยกนอก</option>
			<option value="I">รวมใน</option>
			<option value="N">ไม่ VAT</option>
		</select>
		<input type="hidden" id="tax-status" value="">
	</div>

  <div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
    <label>วันที่</label>
    <input type="text" class="form-control input-sm text-center h" name="date" id="date" value="<?php echo date('d-m-Y'); ?>" readonly />
  </div>

	<div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-4 padding-5">
		<label>รหัสลูกค้า</label>
		<input type="text" class="form-control input-sm text-center h" id="customer-code" value="" autofocus/>
	</div>

  <div class="col-lg-4-harf col-md-4-harf col-sm-4-harf col-xs-8 padding-5">
    <label>ลูกค้า</label>
    <input type="text" class="form-control input-sm h" id="customer-name" value="" />
  </div>

	<div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-4 padding-5">
		<label class="display-block not-show">isCompany</label>
		<label style="margin-top:0;">
			<input type="checkbox" class="ace" id="is-company" value="1" onchange="toggleBranch()" />
			<span class="lbl margin-top-5">&nbsp;&nbsp;นิติบุคคล</span>
		</label>
	</div>

	<div class="col-lg-5-harf col-md-3-harf col-sm-3-harf col-xs-8 padding-5">
    <label>ผู้ติดต่อ</label>
		<input type="text" class="form-control input-sm h" id="customer_ref" value="" />
  </div>

	<div class="col-lg-2 col-md-2 col-sm-2 col-xs-6 padding-5">
    <label>เบอร์โทร</label>
		<input type="text" class="form-control input-sm h" id="phone" value="" />
  </div>
	<div class="col-lg-2 col-md-2 col-sm-2 col-xs-6 padding-5">
		<label>Tax ID</label>
		<input type="text" class="form-control input-sm h" id="tax-id" value="" />
	</div>
	<div class="col-lg-1 col-md-1 col-sm-1 col-xs-6 padding-5">
		<label>สาขา</label>
		<input type="text" class="form-control input-sm h" id="branch-code" value="" />
	</div>
	<div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-6 padding-5">
		<label>ชื่อสาขา</label>
		<input type="text" class="form-control input-sm h" id="branch-name" value="" />
	</div>
	<div class="col-lg-5 col-md-4-harf col-sm-4-harf col-xs-12 padding-5">
		<label>ที่อยู่เปิดบิล</label>
		<input type="text" class="form-control input-sm h" id="address" value="" />
	</div>
	<div class="col-lg-2 col-md-2 col-sm-2 col-xs-6 padding-5">
		<label>ตำบล</label>
		<input type="text" class="form-control input-sm h" id="sub-district" value="" />
	</div>
	<div class="col-lg-2 col-md-2 col-sm-2 col-xs-6 padding-5">
		<label>อำเภอ</label>
		<input type="text" class="form-control input-sm h" id="district" value="" />
	</div>
	<div class="col-lg-2 col-md-2 col-sm-2 col-xs-6 padding-5">
		<label>จังหวัด</label>
		<input type="text" class="form-control input-sm h" id="province" value="" />
	</div>
	<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
		<label>ไปรษณีย์</label>
		<input type="text" class="form-control input-sm h" id="postcode" value="" />
	</div>

	<div class="divider-hidden"></div>	

	<div class="col-lg-3 col-md-3 col-sm-3 col-xs-6 padding-5">
		<label>คลัง</label>
    <select class="form-control input-sm h" name="warehouse" id="warehouse">
			<option value="">เลือกคลัง</option>
			<?php echo select_sell_warehouse(); ?>
		</select>
  </div>

	<div class="col-lg-2 col-md-2 col-sm-2-harf col-xs-6 padding-5">
		<label>User</label>
		<input type="text" class="form-control input-sm" value="<?php echo $this->_user->uname; ?>" disabled/>
	</div>

  <div class="col-lg-6 col-md-5-harf col-sm-5 col-xs-8 padding-5">
    <label>หมายเหตุ</label>
    <input type="text" class="form-control input-sm" name="remark" id="remark" value="">
  </div>
  <div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-4 padding-5">
    <label class="display-block not-show">Submit</label>
		<button type="button" class="btn btn-xs btn-success btn-block" id="btn-save" onclick="add()"><i class="fa fa-plus"></i> เพิ่ม</button>
  </div>
</div>
<hr class="margin-top-15">
<input type="hidden" name="customerCode" id="customerCode" value="" />

<?php $this->load->view('order_invoice/customer_modal'); ?>
<?php $this->load->view('order_invoice/address_modal'); ?>

<script>
	$('#sale-id').select2();
</script>
<script src="<?php echo base_url(); ?>scripts/support/support.js?v=<?php echo date('YmdH'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/support/support_add.js?v=<?php echo date('YmdH'); ?>"></script>

<?php $this->load->view('include/footer'); ?>
