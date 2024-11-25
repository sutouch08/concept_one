<?php $this->load->view('include/header'); ?>
<input type="hidden" id="required_remark" value="<?php echo $this->required_remark; ?>" />
<div class="row">
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-8 padding-5">
    <h3 class="title" >
      <?php echo $this->title; ?>
    </h3>
	</div>
  <div class="col-lg-6 col-md-6 col-sm-6 col-xs-4 padding-5">
    <p class="pull-right top-p">
			<button type="button" class="btn btn-xs btn-warning btn-100" onclick="leave()"><i class="fa fa-arrow-left"></i> กลับ</button>
			<?php if($this->pm->can_add) : ?>
				<button type="button" class="btn btn-xs btn-purple btn-100" onclick="saveAsDraft()">Save As Draft</button>
				<button type="button" class="btn btn-xs btn-success btn-100" onclick="validateData()">Save</button>
			<?php	endif; ?>
    </p>
  </div>
</div>
<hr />

<div class="row">
	<div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-6 padding-5">
		<label>เลขที่เอกสาร</label>
		<input type="text" class="form-control input-sm" value="" disabled />
	</div>
	<div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
		<label>วันที่</label>
		<input type="text" class="form-control input-sm text-center" name="date_add" id="dateAdd" value="<?php echo date('d-m-Y'); ?>" readonly/>
	</div>
	<div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-6 padding-5">
		<label>รหัสผู้ขาย</label>
		<input type="text" class="form-control input-sm text-center" name="vendor_code" id="vendor_code" placeholder="รหัสผู้ขาย" autofocus/>
	</div>
	<div class="col-lg-6 col-md-6 col-sm-4-harf col-xs-6 padding-5">
		<label>ชื่อผู้ขาย</label>
		<input type="text" class="form-control input-sm" name="vendorName" id="vendorName" placeholder="ชื่อผู้ขาย" />
	</div>
	<div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-4 padding-5">
		<label>ใบส่งสินค้า</label>
		<input type="text" class="form-control input-sm text-center" name="invoice" id="invoice" placeholder="ใบส่งสินค้า" />
	</div>
	<div class="divider">	</div>
	<div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-6 padding-5">
		<label>ใบสั่งซื้อ</label>
		<input type="text" class="form-control input-sm text-center"	name="poCode" id="poCode" placeholder="ค้นหาใบสั่งซื้อ" />
		<input type="hidden" id="po-code" />
	</div>
	<div class="col-lg-1 col-md-1 col-sm-1-harf col-xs-3 padding-5">
		<label class="display-block not-show">confirm</label>
		<button type="button" class="btn btn-xs btn-primary btn-block" id="btn-confirm-po" onclick="confirmPO()">ยืนยัน</button>
		<button type="button" class="btn btn-xs btn-primary btn-block hide" id="btn-get-po" onclick="getPoDetail()">แสดงรายการ</button>
	</div>
	<div class="col-lg-1 col-md-1 col-sm-1-harf col-xs-3 padding-5">
		<label class="display-block not-show">confirm</label>
		<button type="button" class="btn btn-xs btn-warning btn-block" id="btn-clear-po" onclick="clearPo()">Clear</button>
	</div>

	<div class="col-lg-2-harf col-md-3 col-sm-3-harf col-xs-6 padding-5">
		<label>โซนรับสินค้า</label>
		<input type="text" class="form-control input-sm" name="zone_code" id="zone_code" placeholder="รหัสโซน" value="" />
	</div>
	<div class="col-lg-4 col-md-5 col-sm-3-harf col-xs-6 padding-5">
		<label class="not-show">zone</label>
		<input type="text" class="form-control input-sm zone" name="zoneName" id="zoneName" placeholder="ชื่อโซน" value="" />
	</div>

	<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-4 padding-5">
		<label>Currency</label>
		<select class="form-control input-sm width-100" id="DocCur" onchange="changeRate()">
			<?php echo select_currency("THB"); ?>
		</select>
	</div>
	<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-4 padding-5">
		<label>Rate</label>
		<input type="number" class="form-control input-sm text-center" id="DocRate" value="1.00" />
	</div>


	<input type="hidden" name="receive_code" id="receive_code" value="" />
	<input type="hidden" id="zone-code" value="" />
	<input type="hidden" name="approver" id="approver" value="" />
	<input type="hidden" id="allow_over_po" value="<?php echo getConfig('ALLOW_RECEIVE_OVER_PO'); ?>">
	<input type="hidden" id="purchase-vat-code" value="<?php echo getConfig('PURCHASE_VAT_CODE'); ?>" />
	<input type="hidden" id="purchase-vat-rate" value="<?php echo getConfig('PURCHASE_VAT_RATE'); ?>" />
	<input type="hidden" id="no" value="0" />
</div>
<hr class="margin-top-10 margin-bottom-10"/>

<?php $this->load->view('inventory/receive_po/receive_control'); ?>
<?php $this->load->view('inventory/receive_po/receive_po_detail'); ?>

<script src="<?php echo base_url(); ?>scripts/inventory/receive_po/receive_po.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/inventory/receive_po/receive_po_add.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/inventory/receive_po/receive_control.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/validate_credentials.js"></script>
<?php $this->load->view('include/footer'); ?>
