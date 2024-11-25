<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 padding-5">
    <h4 class="title"><?php echo $this->title; ?></h4>
    </div>
    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 padding-5">
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
			<option value="">เลือก</option>
			<option value="0">ขายสด</option>
			<option value="1">ขายเชื่อ</option>
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
    <input type="text" class="form-control input-sm text-center h" name="date" id="date" value="<?php echo date('d-m-Y'); ?>" required readonly />
  </div>

	<div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-4 padding-5">
		<label>รหัสลูกค้า</label>
		<input type="text" class="form-control input-sm text-center h" id="customer-code" name="customer_code" value="" autofocus required/>
	</div>

  <div class="col-lg-4-harf col-md-4-harf col-sm-4-harf col-xs-8 padding-5">
    <label>ลูกค้า</label>
    <input type="text" class="form-control input-sm h" id="customer-name" value=""  required />
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
		<input type="text" class="form-control input-sm h" name="cust_ref" id="customer_ref" value="" required/>
  </div>

	<div class="col-lg-2 col-md-2 col-sm-2 col-xs-6 padding-5">
    <label>เบอร์โทร</label>
		<input type="text" class="form-control input-sm h" name="phone" id="phone" value="" required/>
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

	<div class="divider">	</div>

	<div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-6 padding-5">
    <label>อ้างอิงออเดอร์</label>
		<input type="text" class="form-control input-sm h" name="reference" id="reference" value="" />
  </div>

	<div class="col-lg-1-harf col-md-2 col-sm-2-harf col-xs-6 padding-5">
    <label>ช่องทางขาย</label>
		<select class="form-control input-sm h" name="channels" id="channels-code">
			<option value="">เลือก</option>
			<?php echo select_channels(); ?>
		</select>
  </div>

<?php $sale_id = empty($this->_user->sale_id) ? getConfig('DEFAULT_SALES_ID') : $this->_user->sale_id; ?>

	<div class="col-lg-2-harf col-md-2-harf col-sm-2-harf col-xs-6 padding-5">
		<label>พนักงานขาย</label>
		<select class="width-100 h" id="sale-id" name="sale_id">
			<?php echo select_saleman($sale_id); ?>
		</select>
  </div>

	<div class="col-lg-2 col-md-2 col-sm-2-harf col-xs-6 padding-5">
		<label>คลัง</label>
    <select class="form-control input-sm h" name="warehouse" id="warehouse" required>
			<option value="">เลือกคลัง</option>
			<?php echo select_sell_warehouse(); ?>
		</select>
  </div>

	<div class="col-lg-2 col-md-2 col-sm-2-harf col-xs-6 padding-5">
		<label>User</label>
		<input type="text" class="form-control input-sm" value="<?php echo $this->_user->uname; ?>" disabled/>
	</div>

  <div class="col-lg-11 col-md-10-harf col-sm-10-harf col-xs-8 padding-5">
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
<script src="<?php echo base_url(); ?>scripts/orders/orders.js?v=<?php echo date('YmdH'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/orders/order_add.js?v=<?php echo date('YmdH'); ?>"></script>

<?php $this->load->view('include/footer'); ?>
