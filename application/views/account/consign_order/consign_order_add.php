<?php $this->load->view('include/header'); ?>
<?php $manual_code = getConfig('MANUAL_DOC_CODE');  ?>
<?php if($manual_code == 1) :?>
	<input type="hidden" id="manualCode" value="<?php echo $manual_code; ?>">
	<input type="hidden" id="prefix" value="<?php echo getConfig('PREFIX_CONSIGN_SOLD'); ?>">
	<input type="hidden" id="runNo" value="<?php echo getConfig('RUN_DIGIT_CONSIGN_SOLD'); ?>">
<?php endif; ?>
<div class="row">
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-8 padding-5">
    <h3 class="title"><?php echo $this->title; ?></h3>
  </div>
  <div class="col-lg-6 col-md-6 col-sm-6 col-xs-4 padding-5">
  	<p class="pull-right top-p">
      <button type="button" class="btn btn-xs btn-warning" onclick="goBack()"><i class="fa fa-arrow-left"></i> กลับ</button>
    </p>
  </div>
</div><!-- End Row -->
<hr class=""/>
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
    <input type="text" class="form-control input-sm text-center" name="date_add" id="date" value="<?php echo date('d-m-Y'); ?>" readonly required />
  </div>

	<div class="col-lg-2 col-md-2 col-sm-2 col-xs-4 padding-5">
		<label>รหัสลูกค้า</label>
		<input type="text" class="form-control input-sm text-center" id="customerCode" name="customerCode" />
	</div>
  <div class="col-lg-7 col-md-6-harf col-sm-6-harf col-xs-12 padding-5">
    <label>ลูกค้า[ในระบบ]</label>
    <input type="text" class="form-control input-sm" name="customer" id="customer" value="" required />
  </div>

	<div class="col-lg-2 col-md-2 col-sm-2 col-xs-4 padding-5">
		<label>รหัสโซน</label>
		<input type="text" class="form-control input-sm text-center" id="zone_code" name="zone_code" />
	</div>

	<div class="col-lg-4 col-md-4 col-sm-4 col-xs-8 padding-5">
    <label>โซน[ฝากขาย]</label>
		<input type="text" class="form-control input-sm" name="zone" id="zone" value="" />
  </div>

  <div class="col-lg-5 col-md-4-harf col-sm-4-harf col-xs-9 padding-5">
    <label>หมายเหตุ</label>
    <input type="text" class="form-control input-sm" name="remark" id="remark" value="">
  </div>
  <div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-3 padding-5">
    <label class="display-block not-show">Submit</label>
		<?php if($manual_code == 1) : ?>
			<button type="button" class="btn btn-xs btn-success btn-block" onclick="validateOrder()"><i class="fa fa-plus"></i> เพิ่ม</button>
		<?php else : ?>
    <button type="button" class="btn btn-xs btn-success btn-block" onclick="add()"><i class="fa fa-plus"></i> เพิ่ม</button>
		<?php endif; ?>
  </div>
</div>
</form>
<hr class="margin-top-15">

<script src="<?php echo base_url(); ?>scripts/account/consign_order/consign_order.js?v=<?php echo date('YmdH'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/account/consign_order/consign_order_add.js?v=<?php echo date('YmdH'); ?>"></script>


<?php $this->load->view('include/footer'); ?>
