<?php $this->load->view('include/pos_header'); ?>
<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 text-center">
		<h1>Hello! <?php echo get_cookie('displayName'); ?></h1>
		<h5>Good to see you here</h5>
	</div>
	<div class="divider-hidden"></div>
	<div class="divider"></div>
</div>

<div class="row">
	<div id="not-register" class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 text-center hide">
		<h4>กรุณาลงทะเบียนเครื่อง POS ให้กับคอมพิวเตอร์เครื่องนี เพื่อทำการขายต่อไป</h4>
	</div>
	<div id="init-message" class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 text-center">
		<h4><i class="fa fa-spinner fa-pulse fa-spin fa-3x fa-fw"></i><br/><br/><span class="display-block text-center">กำลังเตรียมข้อมูล กรุณารอซักครู่</span></h4>
	</div>
</div>

<script src="<?php echo base_url(); ?>scripts/order_pos/order_pos.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/order_pos/pos_init.js?v=<?php echo date('Ymd'); ?>"></script>

<?php $this->load->view('include/pos_footer'); ?>
