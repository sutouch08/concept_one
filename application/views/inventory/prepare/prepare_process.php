<?php $this->load->view('include/header'); ?>
<div class="row">
  <div class="col-sm-6 padding-5">
    <h4 class="title"><?php echo $this->title; ?></h4>
  </div>
  <div class="col-sm-6 padding-5">
    <p class="pull-right top-p">
      <button type="button" class="btn btn-sm btn-info" onclick="printOrderSheetBarcode()"><i class="fa fa-barcode"></i> พิมพ์</button>
      <button type="button" class="btn btn-sm btn-purple" onclick="viewHistory('<?php echo $order->code; ?>')"><i class="fa fa-history"></i> ตรวจสอบ Buffer</button>
      <button type="button" class="btn btn-sm btn-warning" onclick="goBack()"><i class="fa fa-arrow-left"></i> รอจัด</button>
      <button type="button" class="btn btn-sm btn-yellow" onclick="goProcess()"><i class="fa fa-arrow-left"></i> กำลังจัด</button>
    </p>
  </div>
</div>

<hr class="padding-5" />
<?php if($order->state != 4 && $order->state != 7) : ?>
<?php   $this->load->view('inventory/prepare/invalid_state'); ?>
<?php else : ?>
  <div class="row">
    <div class="col-lg-2 col-md-2 col-sm-2 col-xs-6 padding-5 padding-top-10">
			<div class="input-group">
				<span class="input-group-addon">เลขที่</span>
				<input type="text" class="form-control input-sm" value="<?php echo $order->code; ?>" disabled>
			</div>
    </div>
    <div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-6 padding-5 padding-top-10">
			<div class="input-group">
				<span class="input-group-addon">วันที่</span>
				<input type="text" class="form-control input-sm text-center"
				value="<?php echo thai_date($order->date_add); ?>" disabled>
			</div>
    </div>
    <div class="col-lg-2-harf col-md-2-harf col-sm-3 col-xs-6 padding-5 padding-top-10">
			<div class="input-group">
				<span class="input-group-addon">อ้างอิง</span>
				<input type="text" class="form-control input-sm" value="<?php echo $order->reference; ?>" disabled>
			</div>
    </div>
    <div class="col-lg-6 col-md-6-harf col-sm-6-harf col-xs-12 padding-5 padding-top-10">
			<div class="input-group">
				<span class="input-group-addon">ลูกค้า/ผู้เบิก/ผู้ยืม</span>
				<input type="text" class="form-control input-sm"
				value="<?php echo ($order->customer_ref == '' ? $order->customer_name : $order->customer_ref);  ?>" disabled>
			</div>
    </div>
    <div class="col-lg-3 col-md-3-harf col-sm-4 col-xs-6 padding-5 padding-top-10">
			<div class="input-group">
				<span class="input-group-addon">ช่องทาง</span>
				<input type="text" class="form-control input-sm"
				value="<?php echo $order->channels_name; ?>" disabled>
			</div>
    </div>

    <div class="col-lg-3 col-md-3 col-sm-4 col-xs-6 padding-5 padding-top-10">
      <div class="input-group">
				<span class="input-group-addon">คลัง</span>
				<input type="text" class="form-control input-sm"
				value="<?php echo $order->warehouse_code.' | '.warehouse_name($order->warehouse_code); ?>" disabled>
			</div>
    </div>
    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5 padding-top-10">
      <div class="input-group">
        <span class="input-group-addon">หมายเหตุ</span>
        <input type="text" class="form-control input-sm"
        value="<?php echo $order->remark; ?>" disabled>
      </div>
    </div>
    <input type="hidden" id="order_code" value="<?php echo $order->code; ?>" />
  </div>


  <hr class="margin-top-10 margin-bottom-10"/>

  <?php if($order->state == 4) : ?>
    <?php $this->load->view('inventory/prepare/prepare_control'); ?>
  <?php endif; ?>    

  <hr class="margin-top-10 margin-bottom-10"/>

  <?php $this->load->view('inventory/prepare/prepare_incomplete_list');  ?>

  <?php $this->load->view('inventory/prepare/prepare_completed_list'); ?>

<?php endif; //--- endif order->state ?>

<script src="<?php echo base_url(); ?>scripts/inventory/prepare/prepare.js"></script>
<script src="<?php echo base_url(); ?>scripts/inventory/prepare/prepare_process.js?"></script>

<script src="<?php echo base_url(); ?>scripts/print/print_order.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/beep.js"></script>

<?php $this->load->view('include/footer'); ?>
