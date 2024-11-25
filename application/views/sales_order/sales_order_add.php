<?php $this->load->view('include/header'); ?>
<?php $this->load->view('sales_order/style'); ?>
<script src="<?php echo base_url(); ?>assets/js/jquery.colorbox.js"></script>
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/colorbox.css" />
<div class="row">
  <div class="col-lg-6 col-md-6 col-sm-6 padding-5 hidden-xs">
    <h3 class="title"><?php echo $this->title; ?></h3>
  </div>
  <div class="col-xs-12 padding-5 visible-xs">
    <h3 class="title-xs"><?php echo $this->title; ?></h3>
  </div>
  <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5">
    <p class="pull-right top-p">
      <button type="button" class="btn btn-xs btn-default" onclick="leave()"><i class="fa fa-arrow-left"></i> Back</button>
      <button type="button" class="btn btn-xs btn-success" onclick="showCustomerModal()">ข้อมูลลูกค้า</button>
    </p>
  </div>
</div>
<hr class="padding-5"/>
<?php $this->load->view('sales_order/sales_order_header'); ?>
<?php $this->load->view('sales_order/sales_order_control'); ?>
<?php $this->load->view('sales_order/sales_order_detail'); ?>
<?php $this->load->view('sales_order/sales_order_footer'); ?>
<?php $this->load->view('sales_order/sales_order_template'); ?>
<?php $this->load->view('sales_order/customer_modal'); ?>
<?php $this->load->view('sales_order/address_modal'); ?>

<script src="<?php echo base_url(); ?>scripts/sales_order/sales_order.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/sales_order/sales_order_add.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/sales_order/sales_order_control.js?v=<?php echo date('Ymd'); ?>"></script>

<?php $this->load->view('include/footer'); ?>
