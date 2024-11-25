<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-lg-6 col-md-6 col-sm-6 hidden-xs padding-5">
    <h3 class="title"><?php echo $this->title; ?></h3>
  </div>
	<div class="col-xs-12 padding-5 visible-xs">
		<h3 class="title-xs"><?php echo $this->title; ?></h3>
	</div>
  <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5">
  	<p class="pull-right top-p">
      <button type="button" class="btn btn-xs btn-warning" onclick="goBack()"><i class="fa fa-arrow-left"></i> กลับ</button>
    </p>
  </div>
</div><!-- End Row -->
<hr class="padding-5"/>

<div class="row">
  <div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-4 padding-5">
    <label>เลขที่เอกสาร</label>
		<input type="text" class="form-control input-sm" value="" disabled />
  </div>

  <div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-4 padding-5">
    <label>วันที่</label>
    <input type="text" class="form-control input-sm text-center h" name="date" id="date" value="<?php echo date('d-m-Y'); ?>" required />
  </div>

	<div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-4 padding-5">
		<label>ลูกค้า</label>
		<input type="text" class="form-control input-sm text-center h" name="customer_code" id="customer-code" />
	</div>

  <div class="col-lg-4 col-md-5-harf col-sm-5 col-xs-12 padding-5">
    <label class="not-show">ลูกค้า[ในระบบ]</label>
    <input type="text" class="form-control input-sm h" name="customer" id="customer" value="" required />
  </div>

	<div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-6 padding-5">
    <label>ผู้เบิก[คนสั่ง]</label>
    <input type="text" class="form-control input-sm h" name="empName" id="empName" value="" required />
  </div>
	<div class="col-lg-2-harf col-md-2-harf col-sm-5-harf col-xs-6 padding-5">
		<label>โซนแปรสภาพ</label>
		<input type="text" class="form-control input-sm h" name="zone" id="zone" placeholder="ระบุโซนแปรสภาพ" value="">
	</div>

	<div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-6 padding-5">
    <label>ใบสั่งงาน</label>
		<input type="text" class="form-control input-sm h" name="reference" id="reference" value="" />
  </div>

	<div class="col-lg-2 col-md-2 col-sm-4-harf col-xs-6 padding-5">
		<label>คลัง</label>
    <select class="form-control input-sm h" name="warehouse" id="warehouse" required>
			<option value="">เลือกคลัง</option>
			<?php echo select_common_warehouse(); ?>
		</select>
  </div>
  <div class="col-lg-7-harf col-md-5 col-sm-10-harf col-xs-9 padding-5">
    <label>หมายเหตุ</label>
    <input type="text" class="form-control input-sm" name="remark" id="remark" value="">
  </div>
  <div class="col-lg-1 col-md-1 col-sm-1-harf col-xs-3 padding-5">
    <label class="display-block not-show">Submit</label>
    <button type="button" class="btn btn-xs btn-success btn-block" onclick="add()"><i class="fa fa-plus"></i> เพิ่ม</button>
  </div>
</div>
<hr class="margin-top-15 padding-5">
<input type="hidden" name="customerCode" id="customerCode" value="" />
<input type="hidden" name="role" id="role" value="<?php echo $this->role; ?>" />
<input type="hidden" name="zoneCode" id="zoneCode" value="">

<script src="<?php echo base_url(); ?>scripts/transform/transform.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/transform/transform_add.js?v=<?php echo date('Ymd'); ?>"></script>

<?php $this->load->view('include/footer'); ?>
