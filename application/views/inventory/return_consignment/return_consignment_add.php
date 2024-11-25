<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-sm-6 col-xs-6 padding-5">
    	<h4 class="title" >
        <?php echo $this->title; ?>
      </h4>
	</div>
    <div class="col-sm-6 col-xs-6 padding-5">
      <p class="pull-right top-p">
				<button type="button" class="btn btn-sm btn-warning" onclick="goBack()"><i class="fa fa-arrow-left"></i> กลับ</button>
      </p>
    </div>
</div>
<hr />

<div class="row">
    <div class="col-sm-1 col-1-harf col-xs-6 padding-5">
    	<label>เลขที่เอกสาร</label>
        <input type="text" class="form-control input-sm text-center" disabled />
    </div>
		<div class="col-sm-1 col-1-harf col-xs-6 padding-5">
    	<label>วันที่</label>
      <input type="text" class="form-control input-sm text-center" name="date_add" id="dateAdd" value="<?php echo date('d-m-Y'); ?>" readonly />
    </div>
		<div class="col-sm-1 col-1-harf col-xs-6 padding-5">
			<label>รหัสลูกค้า</label>
			<input type="text" class="form-control input-sm text-center" id="customerCode" value="" />
		</div>
		<div class="col-sm-5 col-xs-12 padding-5">
			<label>ลูกค้า</label>
			<input type="text" class="form-control input-sm" id="customer" value="" />
		</div>
		<div class="col-sm-1 col-1-harf col-xs-6 padding-5">
			<label>เลขที่บิล[SAP]</label>
			<input type="text" class="form-control input-sm text-center" name="invoice" id="invoice" value="" />
		</div>
		<div class="col-sm-1 col-xs-6 padding-5">
			<label>GP(%)</label>
			<input type="number" class="form-control input-sm text-center" name="gp" id="gp" value="" />
		</div>
		<div class="col-sm-1 col-1-harf col-xs-6 padding-5">
			<label>รับที่</label>
			<select class="form-control input-sm" name="is_wms" id="is_wms" onchange="toggleInterface()">
				<option value="1">Pioneer</option>
				<option value="0">Warrix</option>
			</select>
		</div>
		<div class="col-sm-1 col-1-harf col-xs-6 padding-5">
			<label>Interface</label>
			<select class="form-control input-sm" name="is_api" id="is_api">
				<option value="1">ส่ง</option>
				<option value="0">ไม่ส่ง</option>
			</select>
		</div>
		<div class="col-sm-5 col-xs-12 padding-5">
			<label>โซนฝากขาย</label>
			<input type="text" class="form-control input-sm" name="fromZone" id="fromZone" />
		</div>
		<div class="col-sm-4 col-xs-12 padding-5">
			<label>โซน[รับคืน]</label>
			<input type="text" class="form-control input-sm" name="zone" id="zone" value="" />
		</div>

    <div class="col-sm-11 col-xs-12 padding-5">
    	<label>หมายเหตุ</label>
        <input type="text" class="form-control input-sm" name="remark" id="remark" placeholder="ระบุหมายเหตุเอกสาร (ถ้ามี)" />
    </div>
		<div class="col-sm-1 col-xs-6 padding-5">
			<label class="display-block not-show">save</label>
			<?php 	if($this->pm->can_add) : ?>
			<button type="button" class="btn btn-xs btn-success btn-block" onclick="addNew()"><i class="fa fa-plus"></i> เพิ่ม</button>
			<?php	endif; ?>
		</div>
</div>
<input type="hidden" name="customer_code" id="customer_code" value="" />
<input type="hidden" name="warehouse_code" id="warehouse_code" value="" />
<input type="hidden" name="zone_code" id="zone_code" value="" />
<input type="hidden" name="from_zone_code" id="from_zone_code" value="" />

<hr class="margin-top-15"/>

<script src="<?php echo base_url(); ?>scripts/inventory/return_consignment/return_consignment.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/inventory/return_consignment/return_consignment_add.js?v=<?php echo date('Ymd'); ?>"></script>
<?php $this->load->view('include/footer'); ?>
