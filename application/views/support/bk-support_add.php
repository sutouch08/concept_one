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

  <div class="col-lg-1 col-md-1-harf col-sm-2 col-xs-4 padding-5">
    <label>ชนิด VAT</label>
    <select class="form-control input-sm h" id="vat-type" onchange="updateTaxStatus()">
      <option value="">เลือก</option>
      <option value="E">แยกนอก</option>
      <option value="I">รวมใน</option>
      <option value="N">ไม่มี VAT</option>
    </select>
    <input type="hidden" id="tax-status" value="" />
  </div>

  <div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-4 padding-5">
    <label>วันที่</label>
    <input type="text" class="form-control input-sm text-center h" id="date" value="<?php echo date('d-m-Y'); ?>" />
  </div>

	<div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-4 padding-5">
		<label>รหัสลูกค้า</label>
		<input type="text" class="form-control input-sm text-center h" id="customer-code" value="" />
	</div>

  <div class="col-lg-4-harf col-md-5 col-sm-6-harf col-xs-6 padding-5">
    <label>ชื่อลูกค้า</label>
    <input type="text" class="form-control input-sm h" id="customer-name" value=""  />
  </div>	

	<div class="col-lg-2-harf col-md-3 col-sm-4 col-xs-6 padding-5">
		<label>คลัง</label>
    <select class="form-control input-sm h" id="warehouse" >
			<option value="">เลือกคลัง</option>
			<?php echo select_sell_warehouse(); ?>
		</select>
  </div>

  <div class="col-lg-11 col-md-5 col-sm-10-harf col-xs-9 padding-5">
    <label>หมายเหตุ</label>
    <input type="text" class="form-control input-sm h" id="remark" value="">
  </div>
  <div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-3 padding-5">
    <label class="display-block not-show">Submit</label>
    <button type="button" class="btn btn-xs btn-success btn-block" onclick="add()"><i class="fa fa-plus"></i> เพิ่ม</button>
  </div>
</div>
<hr class="margin-top-15 padding-5">

<script src="<?php echo base_url(); ?>scripts/support/support.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/support/support_add.js?v=<?php echo date('Ymd'); ?>"></script>

<?php $this->load->view('include/footer'); ?>
