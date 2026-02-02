<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5 padding-top-5">
    <h3 class="title"><?php echo $this->title; ?></h3>
  </div>
  <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5 text-right">
		<button type="button" class="btn btn-sm btn-default top-btn" onclick="goBack()"><i class="fa fa-arrow-left"></i> กลับ</button>
  </div>
</div><!-- End Row -->
<hr class=""/>
<div class="row">
	<div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-6 padding-5">
    <label>เลขที่เอกสาร</label>
		<input type="text" class="width-100 e" value="" disabled />
  </div>

  <div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
    <label>วันที่</label>
    <input type="text" class="width-100 text-center e" id="date" value="<?php echo date('d-m-Y'); ?>" readonly />
  </div>

	<div class="col-lg-4 col-md-5 col-sm-5 col-xs-12 padding-5">
		<label>คลังต้นทาง</label>
		<select class="width-100 e" id="warehouse">
			<option value="">เลือกคลัง</option>
			<?php echo select_common_warehouse(getConfig('DEFAULT_WAREHOUSE')); ?>
		</select>
	</div>

	<div class="col-lg-2-harf col-md-2-harf col-sm-2-harf col-xs-12 padding-5">
		<label>ช่องทางขาย</label>
		<select class="width-100 e" id="channels">
			<option value="">เลือกช่องทางขาย</option>
			<?php echo select_channels(); ?>
		</select>
	</div>

  <div class="col-lg-9-harf col-md-6 col-sm-5-harf col-xs-9 padding-5">
    <label>หมายเหตุ</label>
		<input type="text" class="width-100 e" id="remark" />
  </div>

  <div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-3 padding-5">
    <label class="display-block not-show">Submit</label>
    <button type="button" class="btn btn-xs btn-success btn-block" onclick="add()"><i class="fa fa-plus"></i> เพิ่ม</button>
  </div>
</div>
<hr class="padding-5 margin-top-15">

<script>
	$('#warehouse').select2();
	$('#zone').select2();
	$('#channels').select2();
	$('#sender').select2();
</script>
<script src="<?php echo base_url(); ?>scripts/inventory/pick_list/pick_list.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/inventory/pick_list/pick_list_add.js?v=<?php echo date('Ymd'); ?>"></script>

<?php $this->load->view('include/footer'); ?>
