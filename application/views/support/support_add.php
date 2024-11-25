<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 padding-5">
    <h3 class="title"><?php echo $this->title; ?></h3>
  </div>
  <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 padding-5">
  	<p class="pull-right top-p">
      <button type="button" class="btn btn-sm btn-warning" onclick="goBack()"><i class="fa fa-arrow-left"></i> กลับ</button>
    </p>
  </div>
</div><!-- End Row -->
<hr class="padding-5"/>
<div class="row">
  <div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-4 padding-5">
    <label>เลขที่เอกสาร</label>
		<input type="text" class="form-control input-sm h" value="" disabled />
  </div>

  <div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-4 padding-5">
    <label>วันที่</label>
    <input type="text" class="form-control input-sm text-center h" name="date" id="date" value="<?php echo date('d-m-Y'); ?>" required />
  </div>

	<div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-4 padding-5">
		<label>รหัสผู้เบิก</label>
		<input type="text" class="form-control input-sm text-center h" name="customerCode" id="customerCode" value="" required />
	</div>
  <div class="col-lg-4 col-md-5 col-sm-6-harf col-xs-6 padding-5">
    <label>ชื่อผู้เบิก[ใช้งบ]</label>
    <input type="text" class="form-control input-sm h" name="customer" id="customer" value="" required />
  </div>

	<div class="col-lg-1-harf col-md-2 col-sm-3 col-xs-6 padding-5">
    <label>งบคงเหลือ</label>
    <input type="text" class="form-control input-sm text-center h" name="budgetLabel" id="budgetLabel" value="" disabled />
		<input type="hidden"  name="budgetAmount" id="budgetAmount" value="0.00" />
  </div>

	<div class="col-lg-2 col-md-2-harf col-sm-5 col-xs-6 padding-5">
    <label>ผู้รับสินค้า</label>
    <input type="text" class="form-control input-sm h" name="empName" id="empName" value="" required />
  </div>

	<div class="col-lg-3 col-md-3 col-sm-4 col-xs-6 padding-5">
		<label>คลัง</label>
    <select class="form-control input-sm h" name="warehouse" id="warehouse" required>
			<option value="">เลือกคลัง</option>
			<?php echo select_sell_warehouse(); ?>
		</select>
  </div>

  <div class="col-lg-8 col-md-5 col-sm-10-harf col-xs-9 padding-5">
    <label>หมายเหตุ</label>
    <input type="text" class="form-control input-sm h" name="remark" id="remark" value="">
  </div>
  <div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-3 padding-5">
    <label class="display-block not-show">Submit</label>
    <button type="button" class="btn btn-xs btn-success btn-block" onclick="addOrder()"><i class="fa fa-plus"></i> เพิ่ม</button>
  </div>
</div>
<hr class="margin-top-15 padding-5">

<script src="<?php echo base_url(); ?>scripts/support/support.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/support/support_add.js?v=<?php echo date('Ymd'); ?>"></script>

<?php $this->load->view('include/footer'); ?>
