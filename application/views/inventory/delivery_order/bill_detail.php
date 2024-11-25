<?php $this->load->view('include/header'); ?>
<style media="screen">
.form-group {
	margin-bottom:5px !important;
}
</style>
<div class="row">
  <div class="col-lg-6 col-md-6 col-sm-6 col-xs-8 padding-5">
    <h4 class="title">รายการรอเปิดบิล</h4>
  </div>
  <div class="col-lg-6 col-md-6 col-sm-6 col-xs-4 padding-5">
    <p class="pull-right top-p">
      <button type="button" class="btn btn-sm btn-warning" onclick="goBack()"><i class="fa fa-arrow-left"></i> กลับ</button>
    </p>
  </div>
</div>
<hr/>


<?php
  if( $order->state == 7)
  {
    $this->load->view('inventory/delivery_order/bill_confirm_detail');
  }
  else if( $order->state == 8)
  {
    $this->load->view('inventory/delivery_order/bill_closed_detail');
  }
  else
  {
    $this->load->view('inventory/delivery_order/invalid_state');
  }
?>

<script src="<?php echo base_url(); ?>scripts/inventory/bill/bill.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/inventory/bill/bill_detail.js?v=<?php echo date('Ymd'); ?>"></script>
<?php $this->load->view('include/footer'); ?>
