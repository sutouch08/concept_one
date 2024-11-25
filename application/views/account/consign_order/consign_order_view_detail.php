<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-lg-4 col-md-4 col-sm-4 padding-5 hidden-xs">
    <h3 class="title"><?php echo $this->title; ?></h3>
  </div>
	<div class="col-xs-12 padding-5 visible-xs">
		<h3 class="title-xs"><?php echo $this->title; ?></h3>
	</div>
  <div class="col-lg-8 col-md-8 col-sm-8 col-xs-12 padding-5">
  	<p class="pull-right top-p">
      <button type="button" class="btn btn-sm btn-warning top-btn" onclick="goBack()"><i class="fa fa-arrow-left"></i> กลับ</button>
		<?php if($doc->status == 0 && $this->pm->can_edit) : ?>
			<button type="button" class="btn btn-sm btn-danger top-btn" onclick="getDelete('<?php echo $doc->code; ?>')">ยกเลิก</button>
		<?php endif; ?>
		<?php if($doc->status == 1) : ?>
			<?php if($this->pm->can_delete && $in_sap === FALSE) : ?>
				<button type="button" class="btn btn-sm btn-danger top-btn" onclick="unSaveConsign()"><i class="fa fa-refresh"></i> ยกเลิกการบันทึก</button>
			<?php endif; ?>
				<button type="button" class="btn btn-sm btn-primary top-btn" onclick="doExport()"><i class="fa fa-send"></i> ส่งข้อมูลไป SAP</button>
				<!--<button type="button" class="btn btn-sm btn-info  top-btn hidden-xs" onclick="printConsignOrder()"><i class="fa fa-print"></i> พิมพ์</button>-->
		<?php endif; ?>
    </p>
  </div>
</div><!-- End Row -->
<hr class=""/>
<?php if($doc->status == 2) : ?>
<?php 	$this->load->view('cancle_watermark'); ?>
<?php endif; ?>
<form id="addForm" method="post" action="<?php echo $this->home; ?>/update">
<div class="row">
  <div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-4 padding-5">
    <label>เลขที่เอกสาร</label>
    <input type="text" class="form-control input-sm text-center" value="<?php echo $doc->code; ?>" disabled />
  </div>

  <div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-4 padding-5">
    <label>วันที่</label>
    <input type="text" class="form-control input-sm text-center edit" name="date_add" id="date" value="<?php echo thai_date($doc->date_add); ?>" readonly disabled />
  </div>

	<div class="col-lg-2 col-md-2 col-sm-2 col-xs-4 padding-5">
		<label>รหัสลูกค้า</label>
		<input type="text" class="form-control input-sm text-center edit" name="customerCode" id="customerCode" value="<?php echo $doc->customer_code; ?>" disabled>
	</div>

	<div class="col-lg-5 col-md-6-harf col-sm-6-harf col-xs-12 padding-5">
    <label class="not-show">ลูกค้า[ในระบบ]</label>
    <input type="text" class="form-control input-sm edit" name="customer" id="customer" value="<?php echo $doc->customer_name; ?>" disabled />
  </div>

	<div class="col-lg-2 col-md-2 col-sm-2 col-xs-4 padding-5">
		<label>รหัสโซน</label>
		<input type="text" class="form-control input-sm edit text-center" id="zone_code" name="zone_code" value="<?php echo $doc->zone_code; ?>" disabled />
	</div>

	<div class="col-lg-4-harf col-md-8 col-sm-8 col-xs-8 padding-5">
    <label class="not-show">โซน[ฝากขาย]</label>
		<input type="text" class="form-control input-sm edit" name="zone" id="zone" value="<?php echo $doc->zone_name; ?>" disabled />
  </div>

	<div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-4 padding-5">
    <label>อ้างอิง</label>
		<input type="text" class="form-control input-sm"  value="<?php echo $doc->ref_code; ?>" disabled />
  </div>

	<div class="col-lg-4-harf col-md-10-harf col-sm-10-harf col-xs-5 padding-5">
		<label>หมายเหตุ</label>
		<input type="text" class="form-control input-sm edit" name="remark" id="remark" value="<?php echo $doc->remark; ?>" disabled />
	</div>
	<div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-3 padding-5">
    <label>SAP No.</label>
    <input type="text" class="form-control input-sm" id="inv_code" value="<?php echo $doc->inv_code; ?>" disabled>
  </div>

</div>
<hr class="margin-top-15">
<input type="hidden" name="consign_code" id="consign_code" value="<?php echo $doc->code; ?>">
<input type="hidden" name="customer_code" id="customer_code" value="<?php echo $doc->customer_code; ?>">
<input type="hidden" name="zone_code" id="zone_code" value="<?php echo $doc->zone_code; ?>" >
</form>

<?php $this->load->view('account/consign_order/consign_order_detail'); ?>
<?php $this->load->view('cancle_modal'); ?>


<script src="<?php echo base_url(); ?>scripts/account/consign_order/consign_order.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/account/consign_order/consign_order_add.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/account/consign_order/consign_order_control.js?v=<?php echo date('Ymd'); ?>"></script>

<?php $this->load->view('include/footer'); ?>
