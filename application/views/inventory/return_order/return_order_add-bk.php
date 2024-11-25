<?php $this->load->view('include/header'); ?>
<input type="hidden" id="required_remark" value="<?php echo $this->required_remark; ?>" />
<div class="row">
	<div class="col-lg-6 col-md-6 col-sm-6 hidden-xs padding-5">
    <h3 class="title"><?php echo $this->title; ?></h3>
  </div>
	<div class="col-xs-12 visible-xs padding-5">
    <h3 class="title-xs"><?php echo $this->title; ?></h3>
  </div>
  <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5">
  	<p class="pull-right top-p">
		<button type="button" class="btn btn-sm btn-warning" onclick="goBack()"><i class="fa fa-arrow-left"></i> กลับ</button>
    </p>
  </div>
</div>
<hr />

<form id="addForm" action="<?php echo $this->home.'/add'; ?>" method="post">
	<div class="row">
    <div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-4 padding-5">
    	<label>เลขที่เอกสาร</label>
        <input type="text" class="form-control input-sm text-center" disabled />
    </div>
		<div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-4 padding-5">
    	<label>วันที่</label>
      <input type="text" class="form-control input-sm text-center" name="date_add" id="dateAdd" value="<?php echo date('d-m-Y'); ?>" readonly />
    </div>
		<div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-4 padding-5">
			<label>เลขที่บิล[SAP]</label>
			<input type="text" class="form-control input-sm text-center" name="invoice" id="invoice" value="" />
		</div>
		<div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-4 padding-5">
			<label>รหัสลูกค้า</label>
			<input type="text" class="form-control input-sm text-center" name="customer_code" id="customer_code" value="" />
		</div>
		<div class="col-lg-6 col-md-5 col-sm-5 col-xs-8 padding-5">
			<label>ชื่อลูกค้า</label>
			<input type="text" class="form-control input-sm" id="customer" value="" />
		</div>
		<div class="col-lg-3 padding-5">
			<label>คลัง</label>
			<select class="form-control input-sm" name="warehouse" id="warehouse" onchange="zoneInit()">
				<option value="">เลือกคลัง</option>
				<?php echo select_common_warehouse(); ?>
			</select>
		</div>
		<div class="col-lg-2-harf col-md-2-harf col-sm-2-harf col-xs-4 padding-5">
			<label>รหัสโซน</label>
			<input type="text" class="form-control input-sm text-center" name="zone_code" id="zone_code" value="" />
		</div>
		<div class="col-lg-6-harf col-md-6-harf col-sm-6-harf col-xs-8 padding-5">
			<label>ชื่อโซน</label>
			<input type="text" class="form-control input-sm" name="zone" id="zone" value="" />
		</div>

    <div class="col-lg-10-harf col-md-10-harf col-sm-10-harf col-xs-9 padding-5">
    	<label>หมายเหตุ</label>
        <input type="text" class="form-control input-sm" name="remark" id="remark" placeholder="ระบุหมายเหตุเอกสาร (ถ้ามี)" />
    </div>
		<div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-3 padding-5">
			<label class="display-block not-show">save</label>
			<?php 	if($this->pm->can_add) : ?>
			<button type="button" class="btn btn-xs btn-success btn-block" onclick="addNew()"><i class="fa fa-plus"></i> เพิ่ม</button>
			<?php	endif; ?>
		</div>
	</div>

	<input type="hidden" name="warehouse_code" id="warehouse_code" value="" />
	<input type="hidden" name="allow_return_no_inv" id="allow-return-no-inv" value="<?php echo getConfig('ALLOW_RETURN_NO_INV'); ?>" />
</form>
<hr class="margin-top-15"/>

<script src="<?php echo base_url(); ?>scripts/inventory/return_order/return_order.js?v=<?php echo date('Ymd');?>"></script>
<script src="<?php echo base_url(); ?>scripts/inventory/return_order/return_order_add.js?v=<?php echo date('Ymd');?>"></script>
<?php $this->load->view('include/footer'); ?>
