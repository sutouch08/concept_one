<?php $this->load->view('include/header'); ?>
<?php $manual_code = getConfig('MANUAL_DOC_CODE');  ?>
<?php if($manual_code == 1) :?>
	<input type="hidden" id="manualCode" value="<?php echo $manual_code; ?>">
	<input type="hidden" id="prefix" value="<?php echo getConfig('PREFIX_SPONSOR'); ?>">
	<input type="hidden" id="runNo" value="<?php echo getConfig('RUN_DIGIT_SPONSOR'); ?>">
<?php endif; ?>
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
<form id="addForm" method="post" action="<?php echo $this->home; ?>/add">
<div class="row">
  <div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-4 padding-5">
    <label>เลขที่เอกสาร</label>
		<?php if($manual_code == 1) : ?>
	    <input type="text" class="form-control input-sm" name="code" id="code" value="" />
		<?php else : ?>
			<input type="text" class="form-control input-sm" value="" disabled />
		<?php endif; ?>
  </div>

  <div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-4 padding-5">
    <label>วันที่</label>
    <input type="text" class="form-control input-sm text-center" name="date" id="date" value="<?php echo date('d-m-Y'); ?>" required />
  </div>

	<div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-4 padding-5">
    <label>รหัสผู้รับ</label>
    <input type="text" class="form-control input-sm text-center" name="customerCode" id="customerCode" value="" required />
  </div>
  <div class="col-lg-4 col-md-6-harf col-sm-6-harf col-xs-12 padding-5">
    <label>ชื่อผู้รับ[สโมสร/ผู้รับการสนับสนุน]</label>
    <input type="text" class="form-control input-sm" name="customer" id="customer" value="" required />
  </div>

	<div class="col-lg-1-harf col-md-2 col-sm-2-harf col-xs-6 padding-5">
    <label>งบคงเหลือ</label>
    <input type="text" class="form-control input-sm text-center" name="budgetLabel" id="budgetLabel" value="" disabled />
		<input type="hidden" name="budgetAmount" id="budgetAmount" value="0.00" />
  </div>

	<div class="col-lg-2 col-md-4 col-sm-3-harf col-xs-6 padding-5">
    <label>ผู้เบิก[พนักงาน/คนสั่ง]</label>
    <input type="text" class="form-control input-sm" name="empName" id="empName" value="" required />
  </div>

	<div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-4 padding-5">
    <label>งานแปรสภาพ</label>
		<select class="form-control input-sm" name="transformed">
			<option value="0">No</option>
			<option value="1">Yes</option>
		</select>
  </div>

	<div class="col-lg-2-harf col-md-4 col-sm-4 col-xs-8 padding-5">
		<label>คลัง</label>
    <select class="form-control input-sm" name="warehouse" id="warehouse" required>
			<option value="">เลือกคลัง</option>
			<?php echo select_sell_warehouse(); ?>
		</select>
  </div>
  <div class="col-lg-7 col-md-10-harf col-sm-10-harf col-xs-9 padding-5">
    <label>หมายเหตุ</label>
    <input type="text" class="form-control input-sm" name="remark" id="remark" value="">
  </div>
  <div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-3 padding-5">
    <label class="display-block not-show">Submit</label>
    <button type="button" class="btn btn-xs btn-success btn-block" onclick="add()"><i class="fa fa-plus"></i> เพิ่ม</button>
  </div>
</div>
<hr class="margin-top-15 padding-5">
</form>

<script src="<?php echo base_url(); ?>scripts/sponsor/sponsor.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/sponsor/sponsor_add.js?v=<?php echo date('Ymd'); ?>"></script>

<?php $this->load->view('include/footer'); ?>
