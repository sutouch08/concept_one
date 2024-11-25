<?php $this->load->view('include/header'); ?>
<?php $manual_code = getConfig('MANUAL_DOC_CODE');  ?>
<?php if($manual_code == 1) :?>
<?php  $prefix = getConfig('PREFIX_MOVE'); ?>
<?php  $runNo = getConfig('RUN_DIGIT_MOVE'); ?>
	<input type="hidden" id="manualCode" value="<?php echo $manual_code; ?>">
	<input type="hidden" id="prefix" value="<?php echo $prefix; ?>">
	<input type="hidden" id="runNo" value="<?php echo $runNo; ?>">
<?php endif; ?>
<input type="hidden" id="require_remark" value="<?php echo $this->require_remark; ?>" />
<div class="row">
	<div class="col-lg-6 col-md-6 col-sm-6 padding-5 hidden-xs">
    <h3 class="title"><?php echo $this->title; ?></h3>
  </div>
	<div class="col-xs-12 padding-5 visible-xs">
		<h3 class="title-xs"><?php echo $this->title; ?></h3>
	</div>
  <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5">
  	<p class="pull-right top-p">
      <button type="button" class="btn btn-sm btn-warning" onclick="goBack()"><i class="fa fa-arrow-left"></i> กลับ</button>
    </p>
  </div>
</div><!-- End Row -->
<hr class=""/>
<div class="row">
	<div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
    <label>เลขที่เอกสาร</label>
		<?php if($manual_code == 1) : ?>
	    <input type="text" class="form-control input-sm" name="code" id="code" value="" />
		<?php else : ?>
			<input type="text" class="form-control input-sm" value="" disabled />
		<?php endif; ?>
  </div>

  <div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
    <label>วันที่</label>
    <input type="text" class="form-control input-sm text-center" name="date" id="date" value="<?php echo date('d-m-Y'); ?>" readonly required />
  </div>

	<div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-4 padding-5">
		<label>คลังต้นทาง</label>
		<input type="text" class="form-control input-sm text-center" id="from_warehouse_code" autofocus/>
	</div>
  <div class="col-lg-3 col-md-3 col-sm-3 col-xs-8 padding-5">
    <label class="not-show">&nbsp;</label>
    <input type="text" class="form-control input-sm" id="from_warehouse_name" value="" readonly />
  </div>


	<div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-4 padding-5">
		<label>คลังปลายทาง</label>
		<input type="text" class="form-control input-sm text-center" id="to_warehouse_code" autofocus/>
	</div>
	<div class="col-lg-3 col-md-3 col-sm-3 col-xs-8 padding-5">
    <label>&nbsp;</label>
		<input type="text" class="form-control input-sm" id="to_warehouse_name" value="" readonly />
  </div>

  <div class="col-lg-10-harf col-md-10-harf col-sm-10-harf col-xs-9 padding-5">
    <label>หมายเหตุ</label>
    <input type="text" class="form-control input-sm" name="remark" id="remark" value="">
  </div>
  <div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-3 padding-5">
    <label class="display-block not-show">Submit</label>
    <button type="button" class="btn btn-xs btn-success btn-block" onclick="add()"><i class="fa fa-plus"></i> เพิ่ม</button>
  </div>
</div>
<hr class="padding-5 margin-top-15">

<script src="<?php echo base_url(); ?>scripts/move/move.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/move/move_add.js?v=<?php echo date('Ymd'); ?>"></script>

<?php $this->load->view('include/footer'); ?>
