<?php $this->load->view('include/pos_header'); ?>
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/pos.css"/>
<div class="row hidden-xs" style="margin-left:-5px; padding:10px; background-color:#f2f2f2; border:solid 1px #ddd; border-radius:10px;">
	<div class="col-lg-4-harf col-md-4-harf col-sm-4-harf padding-5">
		<table class="width-100">
			<tr><td class="fix-width-80">สาขา</td><td><?php echo $pos->shop_code .' - '.$pos->shop_name; ?></td></tr>
			<tr><td>รหัสเครื่อง</td><td><?php echo $pos->code .' - '.$pos->name; ?></td></tr>
			<tr><td>วันที่</td><td><?php echo date('d/m/Y'); ?></td></tr>
		</table>
  </div>
	<div class="col-lg-4-harf col-md-4-harf col-sm-4-harf padding-5">
		<table class="width-100">
			<tr><td class="fix-width-80">เลขที่</td><td><?php echo $pos->order_code; ?></td></tr>
			<tr><td>ลูกค้า</td><td><?php echo $pos->customer_name; ?></td></tr>
			<tr><td>เจ้าหน้าที่</td><td><?php echo $this->_user->name; ?></td></tr>
		</table>
  </div>
	<!-- <div class="col-lg-2-harf col-md-2-harf col-sm-2-harf padding-5">
		<div class="input-group width-100">
			<span class="input-group-addon fix-width-60" style="border:solid 1px #d5d5d5 !important; border-right:0 !important;">PC</span>
			<input type="text" class="form-control input-sm text-center margin-bottom-5 focus" id="pc-code" value="<?php echo $order->pc_code; ?>" placeholder="รหัสพนักงานขาย (Tab)" />
		</div>
		<input type="text" class="form-control input-sm text-center margin-top-5" id="pc-name" value="<?php echo $order->pc_name; ?>" placeholder="ชื่อพนักงานขาย" disabled/>
		<input type="hidden" id="pc-id" value="<?php echo $order->pc_id; ?>" />
		<input type="hidden" id="pcCode" value="<?php echo $order->pc_code; ?>" />
	</div> -->
	<div class="col-lg-3 col-md-3 col-sm-3 padding-5">
		<input type="text" class="form-control input-lg width-100 text-right" id="net-amount"
		style="font-size:35px; background-color:black; color:lime; height:65px; padding-right:10px;" value="<?php echo number($docTotal, 2); ?>" readonly/>
	</div>
</div><!-- End Row -->
<hr class="padding-5"/>

<div class="row hidden-xs">
	<?php $this->load->view('order_pos/pos_body_left'); ?>
	<?php $this->load->view('order_pos/pos_body_right'); ?>
</div>
<div class="row hidden-xs">
	<div class="col-lg-1 col-md-1 hidden-sm">&nbsp;</div>
	<div class="col-lg-1-harf col-md-1-harf col-sm-1-harf padding-5">
		<?php $active = $order->payment_code == $pos->cash_payment ? 'btn-success' : ''; ?>
		<button type="button" class="btn btn-block pos-payment-btn payment-btn <?php echo $active; ?>"
			id="btn-<?php echo $pos->cash_payment; ?>" onclick="setPayment('<?php echo $pos->cash_payment; ?>', 1)">
			<p style="margin-bottom:0px;">เงินสด</p>
			<p style="font-size:10px; margin-bottom:0px;">(Ctrl 1)</p>
		</button>
	</div>
	<div class="col-lg-1-harf col-md-1-harf col-sm-1-harf padding-5">
		<?php $active = $order->payment_code == $pos->transfer_payment ? 'btn-success' : 'button-default'; ?>
		<button type="button" class="btn btn-block pos-payment-btn payment-btn <?php echo $active; ?>"
			id="btn-<?php echo $pos->transfer_payment; ?>" onclick="setPayment('<?php echo $pos->transfer_payment; ?>', 2)">
			<p style="margin-bottom:0px;">เงินโอน</p>
			<p style="font-size:10px; margin-bottom:0px;">(Ctrl 2)</p>
		</button>
	</div>
	<div class="col-lg-1-harf col-md-1-harf col-sm-1-harf padding-5">
		<?php $active = $order->payment_code == $pos->card_payment ? 'btn-success' : 'button-default'; ?>
		<button type="button" class="btn btn-block pos-payment-btn payment-btn <?php echo $active; ?>"
			id="btn-<?php echo $pos->card_payment; ?>" onclick="setPayment('<?php echo $pos->card_payment; ?>', 3)">
			<p style="margin-bottom:0px;">บัตรเครดิต</p>
			<p style="font-size:10px; margin-bottom:0px;">(Ctrl 3)</p>
		</button>
	</div>
	<div class="col-lg-1-harf col-md-1-harf col-sm-1-harf padding-5">
		<?php $active = $order->payment_code == "MULTIPAYMENT" ? 'btn-success' : 'button-default'; ?>
		<button type="button" class="btn btn-block pos-payment-btn payment-btn <?php echo $active; ?>"
			id="btn-MULTIPAYMENT" onclick="setPayment('MULTIPAYMENT', 6)">
			<p style="margin-bottom:0px;">หลายช่องทาง</p>
			<p style="font-size:10px; margin-bottom:0px;">(Ctrl 4)</p>
		</button>
	</div>

	<div class="col-lg-1-harf col-md-1-harf col-sm-1-harf padding-5">
		<?php $active = $order->payment_code == "CHEQUE" ? 'btn-success' : 'button-default'; ?>
		<button type="button" class="btn btn-block pos-payment-btn payment-btn <?php echo $active; ?>"
			id="btn-CHEQUE" onclick="setPayment('CHEQUE', 7)">
			<p style="margin-bottom:0px;">เช็ค</p>
			<p style="font-size:10px; margin-bottom:0px;">(Ctrl 5)</p>
		</button>
	</div>

	<div class="col-lg-1-harf col-md-1-harf col-sm-2 padding-5">
		<button type="button" class="btn btn-primary btn-block pos-payment-btn" id="recal-btn" onclick="reCalDiscount()">
			<p style="margin-bottom:0px;">สรุปยอด</p>
			<p style="font-size:10px; margin-bottom:0px;">(F10)</p>
		</button>
	</div>
	<div class="col-lg-2 col-md-2 col-sm-2 padding-5">
		<button type="button" class="btn btn-success btn-block pos-payment-btn" id="pay-btn" onclick="showPayment()">
			<p style="margin-bottom:0px;">รับเงิน</p>
			<p style="font-size:10px; margin-bottom:0px;">(F12)</p>
		</button>
	</div>
</div>
<div class="row hidden-xs">
	<?php $this->load->view('order_pos/pos_control'); ?>
</div>

<div class="row visible-xs">
	<div class="col-xs-12"><h1 class="text-center">Not supporrt</h1></div>
</div>


<input type="hidden" id="pos_id" value="<?php echo $pos->id; ?>">
<input type="hidden" id="round-id" value="<?php echo $pos->round_id; ?>" />
<input type="hidden" id="zone_code" value="<?php echo $pos->zone_code; ?>">
<input type="hidden" id="shop_id" value="<?php echo $pos->shop_id; ?>">
<input type="hidden" id="customer-code" value="<?php echo $pos->customer_code; ?>" />
<input type="hidden" id="channels-code" value="<?php echo $pos->channels_code; ?>" />
<input type="hidden" id="payment-code" value="<?php echo $pos->cash_payment; ?>" />
<input type="hidden" id="role-1-code" value="<?php echo $pos->cash_payment; ?>" /> <!-- Payment_code for cash payment -->
<input type="hidden" id="role-2-code" value="<?php echo $pos->transfer_payment; ?>" /> <!-- Payment_code for bank transfer -->
<input type="hidden" id="role-3-code" value="<?php echo $pos->card_payment; ?>" /><!-- Payment_code for credit card payment -->
<input type="hidden" id="role-6-code" value="MULTIPAYMENT" /><!-- Payment_code for credit card payment -->
<input type="hidden" id="role-7-code" value="CHEQUE" /><!-- Payment_code for credit card payment -->
<input type="hidden" id="payment-role" value="<?php echo $order->payment_role; ?>" /><!-- 1 = cash, 2 = Transfer, 3 = Credit card -->
<input type="hidden" id="order-temp-id" value="<?php echo $order->id; ?>" />

<?php $this->load->view('order_pos/pos_template'); ?>

<script src="<?php echo base_url(); ?>scripts/order_pos/order_pos.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/order_pos/order_pos_control.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/order_pos/order_pos_hold_bill.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/order_pos/order_pos_short_cut.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/beep.js"></script>
<?php $this->load->view('include/pos_footer'); ?>
