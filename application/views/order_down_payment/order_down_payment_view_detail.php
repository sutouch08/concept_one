<?php $this->load->view('include/header'); ?>
<script src="<?php echo base_url(); ?>assets/js/jquery.colorbox.js"></script>
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/colorbox.css" />
<div class="row">
	<div class="col-lg-4 col-md-4 col-sm-4 col-xs-12 padding-5">
		<h4 class="title"><?php echo $this->title; ?></h4>
	</div>
  <div class="col-lg-8 col-md-8 col-sm-8 col-xs-12 padding-5 text-right top-p">
    <button type="button" class="btn btn-white btn-warning" onclick="goBack()"><i class="fa fa-arrow-left"></i> Back</button>
		<?php if($doc->status != 'D') : ?>
			<button type="button" class="btn btn-white btn-success btn-top" onclick="showCustomerModal()">ข้อมูลลูกค้า</button>
			<div class="btn-group">
				<button data-toggle="dropdown" class="btn btn-primary btn-white margin-top-5 dropdown-toggle" aria-expanded="false">
					<i class="ace-icon fa fa-list-ul icon-on-left"></i>
					ตัวเลือก
					<i class="ace-icon fa fa-angle-down icon-on-right"></i>
				</button>
				<ul class="dropdown-menu dropdown-menu-right">
				<?php if((is_true(getConfig('ALLOW_CREATE_DOWN_PAYMENT_INVOICE'))) && ($doc->TaxStatus == 'N' OR empty($doc->invoice_code))) : ?>
					<li class="purple">
						<a href="javascript:createInvoice('<?php echo $doc->code; ?>')"><i class="fa fa-plus"></i> เปิดใบกำกับภาษี</a>
					</li>
				<?php endif; ?>

				<?php if($doc->is_interface == 1) : ?>
					<li class="success">
						<a  href="javascript:exportIncomming('<?php echo $doc->code; ?>')"><i class="fa fa-send"></i> Export ใบรับเงินมัดจำ</a>
					</li>
					<?php if($doc->TaxStatus == 'Y' && ! empty($doc->invoice_code)) : ?>
						<li class="success">
							<a href="javascript:exportDownpayment('<?php echo $doc->invoice_code; ?>')"><i class="fa fa-send"></i> Export ใบกำกับภาษี</a>
						</li>
					<?php endif; ?>
				<?php endif; ?>

				<?php if($doc->TaxStatus == 'Y' && ! empty($doc->invoice_code)) : ?>
					<li class="primary">
						<a href="javascript:printDownPaymentInvoice('<?php echo $doc->invoice_code; ?>', 'RTI')"><i class="fa fa-print"></i> ใบเสร็จรับเงิน/ใบกำกับภาษี</a>
					</li>
					<li class="primary">
						<a href="javascript:printDownPaymentInvoice('<?php echo $doc->invoice_code; ?>', 'RTIN')"><i class="fa fa-print"></i> ใบเสร็จรับเงิน/ใบกำกับภาษี (ไม่แสดงวันที่)</a>
					</li>
				<?php endif; ?>
					<li class="primary">
						<a href="javascript:printDownPaymentReceipt('<?php echo $doc->code; ?>', 'RE')"><i class="fa fa-print"></i> ใบรับเงินมัดจำ</a>
					</li>
					<?php if( ! empty($doc->pos_id)) : ?>
					<li class="primary">
						<a href="javascript:printDownPayment('<?php echo $doc->code; ?>')"><i class="fa fa-print"></i> สลิปใบรับเงินมัดจำ</a>
					</li>
					<?php endif; ?>
					<?php if($doc->status == 'O' && empty($doc->invoice_code) && $this->pm->can_delete) : ?>
						<li class="danger">
							<a href="javascript:cancelDownPayment('<?php echo $doc->code; ?>', <?php echo $doc->id; ?>)"><i class="fa fa-times"></i> ยกเลิก</a>
						</li>
					<?php endif; ?>
				</ul>
			</div>
		<?php endif; ?>
  </div>
</div>
<hr />
<?php	if($doc->status == 'D') : ?>
	<?php $this->load->view('cancle_watermark'); ?>
<?php endif; ?>
<?php $this->load->view('order_down_payment/order_down_payment_header'); ?>

<div class="row">
	<div class="col-lg-2 col-md-3 col-sm-3 col-xs-6 padding-5">
		<label>อ้างอิง</label>
		<div class="input-group">
			<input type="text" class="form-control input-sm text-center" id="reference" value="<?php echo $doc->reference; ?>" disabled />
			<?php if($doc->ref_type == 'WO') : ?>
				<span class="input-group-btn">
					<button type="button" class="btn btn-xs btn-info"
					onclick="viewWo('<?php echo $doc->reference; ?>')"
					<?php echo (empty($doc->reference) ? 'disabled' :''); ?>>		<i class="fa fa-external-link"></i></button>
				</span>
			<?php else : ?>
				<span class="input-group-btn">
					<button type="button" class="btn btn-xs btn-info"
					onclick="viewSo('<?php echo $doc->reference; ?>')"
					<?php echo (empty($doc->reference) ? 'disabled' :''); ?>>		<i class="fa fa-external-link"></i></button>
				</span>
			<?php endif; ?>
		</div>
	</div>
	<div class="col-lg-2-harf col-md-3 col-sm-3 col-xs-6 padding-5">
		<label>ใบกำกับภาษี <span style="font-size:10px;">(AR/Down Payment)</span></label>
		<div class="input-group width-100">
			<input type="text" class="form-control input-sm text-center" id="invoice-code" value="<?php echo $doc->invoice_code; ?>" disabled />
			<span class="input-group-btn">
				<button type="button" class="btn btn-xs btn-info"
				onclick="viewInvoice('<?php echo $doc->invoice_code; ?>')"
				<?php echo (empty($doc->invoice_code) ? 'disabled' :''); ?>><i class="fa fa-external-link"></i></button>
			</span>
		</div>
	</div>

	<div class="col-lg-4 col-md-1-harf col-sm-1-harf hidden-xs">&nbsp;</div>
	<div class="divider-hidden visible-xs"></div>

	<div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-4 padding-5">
		<label>SAP No.</label>
		<input type="text" class="form-control input-sm text-center" id="ORCT" value="<?php echo $doc->DocNum; ?>" disabled/>
	</div>
	<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-4 padding-5">
		<label>Tax Status</label>
		<input type="text" class="form-control input-sm text-center" value="<?php echo $doc->TaxStatus; ?>" disabled/>
	</div>
	<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-4 padding-5">
		<label>สถานะ</label>
		<input type="text" class="form-control input-sm text-center" value="<?php echo ($doc->status == 'D' ? 'Cancel' : ($doc->status == 'C' ? 'Closed' : 'Open')); ?>" disabled />
	</div>
</div>
<hr class="margin-top-10 margin-bottom-10"/>
<?php $this->load->view('order_down_payment/order_down_payment_details'); ?>
<div class="divider-hidden"></div>
<?php $this->load->view('order_down_payment/order_down_payment_footer'); ?>

<?php $this->load->view('order_down_payment/customer_modal'); ?>

<?php $this->load->view('order_down_payment/image_modal'); ?>

<?php $this->load->view('order_down_payment/cancel_modal'); ?>



<script src="<?php echo base_url(); ?>scripts/order_down_payment/order_down_payment.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/print/print_down_payment_receipt.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/print/print_down_payment_invoice.js?v=<?php echo date('Ymd'); ?>"></script>

<?php $this->load->view('include/footer'); ?>
