<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-lg-6 col-md-6 col-sm-6 hidden-xs padding-5">
    <h3 class="title"><?php echo $this->title; ?></h3>
  </div>
	<div class="col-xs-12 padding-5 visible-xs">
		<h3 class="title-xs"><?php echo $this->title; ?> </h3>
	</div>
  <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5">
    <p class="pull-right top-p">
			<button type="button" class="btn btn-xs btn-warning top-btn" onclick="goBack()"><i class="fa fa-arrow-left"></i> กลับ</button>
      <button type="button" class="btn btn-xs btn-info top-btn" onclick="printReceived()"><i class="fa fa-print"></i> พิมพ์</button>
			<?php if($doc->status == 1) : ?>
				<button type="button" class="btn btn-xs btn-info top-btn" onclick="printReceiveLabel()"><i class="fa fa-print"></i> พิมพ์ใบนำส่ง</button>
				<button type="button" class="btn btn-xs btn-success top-btn" onclick="doExport()"><i class="fa fa-send"></i> ส่งข้อมูลไป SAP</button>
			<?php endif; ?>
			<?php if($doc->status == 4) : ?>
				<button type="button" class="btn btn-xs btn-purple top-btn" onclick="acceptConfirm()">ยืนยันการรับสินค้า</button>
			<?php endif; ?>
			<?php if($this->pm->can_delete && $doc->status != 2 && ($doc->status == 0 OR $doc->status == 1 OR $this->_SuperAdmin)) : ?>
        <button type="button" class="btn btn-xs btn-danger top-btn" onclick="goDelete('<?php echo $doc->code; ?>')"><i class="fa fa-exclamation-triangle"></i> ยกเลิก</button>
      <?php endif; ?>
    </p>
  </div>
</div>
<hr class="padding-5" />

<div class="row">
  <div class="col-lg-1-harf col-md-1-harf col-sm-3 col-xs-4 padding-5">
  	<label>เลขที่เอกสาร</label>
    <input type="text" class="form-control input-sm text-center" value="<?php echo $doc->code; ?>" disabled />
  </div>
	<div class="col-lg-1 col-md-1-harf col-sm-3 col-xs-4 padding-5">
    <label>วันที่</label>
    <input type="text" class="form-control input-sm text-center" name="date_add" id="dateAdd" value="<?php echo thai_date($doc->date_add); ?>" disabled />
  </div>

	<div class="col-lg-1-harf col-md-1-harf col-sm-3 col-xs-4 padding-5">
    <label>ใบเบิก</label>
    <input type="text" class="form-control input-sm text-center" value="<?php echo $doc->order_code; ?>" disabled />
  </div>

  <div class="col-lg-1-harf col-md-1-harf col-sm-3 col-xs-6 padding-5">
  	<label>ใบส่งสินค้า</label>
    <input type="text" class="form-control input-sm text-center" value="<?php echo $doc->invoice_code; ?>" disabled/>
  </div>
  <div class="col-lg-1-harf col-md-1-harf col-sm-4 col-xs-6 padding-5">
    <label>รหัสโซน</label>
    <input type="text" class="form-control input-sm text-center" value="<?php echo $doc->zone_code; ?>" disabled />
  </div>
  <div class="col-lg-5 col-md-4-harf col-sm-8 col-xs-12 padding-5">
  	<label>ชื่อโซน</label>
    <input type="text" class="form-control input-sm" value="<?php echo $doc->zone_name; ?>" disabled/>
  </div>

	<div class="col-lg-9 col-md-9 col-sm-8 col-xs-12 padding-5">
		<label>หมายเหตุ</label>
		<input type="text" class="form-control input-sm" value="<?php echo $doc->remark; ?>" disabled />
	</div>
	<div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-6 padding-5">
		<label>สถานะ</label>
		<select class="form-control input-sm header-box" name="status" id="status" disabled>
			<option value="0" <?php echo is_selected('0', $doc->status); ?>>ยังไม่บันทึก</option>
			<option value="1" <?php echo is_selected('1', $doc->status); ?>>บันทึกแล้ว</option>
			<option value="2" <?php echo is_selected('2', $doc->status); ?>>ยกเลิก</option>
			<option value="3" <?php echo is_selected('3', $doc->status); ?>>WMS Process</option>
			<option value="4" <?php echo is_selected('4', $doc->status); ?>>รอยืนยัน</option>
		</select>
	</div>
	<div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-6 padding-5">
		<label>SAP No.</label>
		<input type="text" class="form-control input-sm" value="<?php echo $doc->inv_code; ?>" disabled />
	</div>

  <input type="hidden" name="receive_code" id="receive_code" value="<?php echo $doc->code; ?>" />
</div>

<?php
if($doc->is_expire == 1)
{
	$this->load->view('expire_watermark');
}
else
{
	if($doc->status == 2)
	{
		$this->load->view('cancle_watermark');
	}
}
?>
<hr class="margin-top-15 margin-bottom-15"/>
<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 table-responsive padding-5">
		<table class="table table-striped table-bordered" style="min-width:1000px;">
      <thead>
      	<tr class="font-size-12">
        	<th class="fix-width-40 text-center">ลำดับ</th>
          <th class="fix-width-150 text-center">รหัสสินค้า</th>
          <th class="min-width-250">ชื่อสินค้า</th>
					<th class="fix-width-100 text-right">ต้นทุน</th>
          <th class="fix-width-100 text-right">จำนวน</th>
					<th class="fix-width-100 text-right">จำนวนรับ</th>
					<th class="fix-width-120 text-right">มูลค่า</th>
        </tr>
      </thead>
      <tbody>
        <?php if(! empty($details)) : ?>
          <?php $no =  1; ?>
          <?php $total_qty = 0; ?>
					<?php $total_receive = 0; ?>
					<?php $total_amount = 0; ?>
          <?php foreach($details as $rs) : ?>
						<?php $red = ($rs->qty > $rs->receive_qty) ? 'red' : ''; ?>
            <tr class="font-size-12 <?php echo $red; ?>">
              <td class="middle text-center"><?php echo $no; ?></td>
              <td class="middle"><?php echo $rs->product_code; ?></td>
              <td class="middle"><?php echo $rs->product_name; ?></td>
							<td class="middle text-right"><?php echo number($rs->price, 2); ?></td>
              <td class="middle text-right"><?php echo number($rs->qty, 2); ?></td>
							<td class="middle text-right"><?php echo number($rs->receive_qty, 2); ?></td>
							<td class="middle text-right"><?php echo number($rs->amount, 2); ?></td>
            </tr>
            <?php $no++; ?>
            <?php $total_qty += $rs->qty; ?>
						<?php $total_receive += $rs->receive_qty; ?>
						<?php $total_amount += $rs->amount; ?>
          <?php endforeach; ?>
          <tr>
            <td colspan="4" class="text-right"><strong>รวม</strong></td>
            <td class="text-right"><strong><?php echo number($total_qty, 2); ?></strong></td>
						<td class="text-right"><strong><?php echo number($total_receive, 2); ?></strong></td>
						<td class="text-right"><strong><?php echo number($total_amount, 2); ?></strong></td>
          </tr>
        <?php endif; ?>
			  </tbody>
      </table>
    </div>
</div>

<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5">
		<?php if($doc->status == 2) : ?>
			<span class="red display-block">ยกเลิกโดย : <?php echo $doc->cancle_user; ?> @ <?php echo thai_date($doc->cancle_date, TRUE); ?></span>
			<span class="red display-block">หมายเหตุ : <?php echo $doc->cancle_reason; ?></span>
		<?php endif; ?>
		<?php if($doc->status == 1 && $doc->must_accept == 1 && $doc->is_accept == 1) : ?>
			<span class="green display-block">ยืนยันการรับโดย : <?php echo $doc->accept_by; ?> @ <?php echo thai_date($doc->accept_on, TRUE); ?></span>
		<?php endif; ?>
	</div>
</div>

<?php $this->load->view('cancle_modal'); ?>
<?php $this->load->view('accept_modal'); ?>

<script src="<?php echo base_url(); ?>scripts/inventory/receive_transform/receive_transform.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/inventory/receive_transform/receive_transform_add.js?v=<?php echo date('Ymd'); ?>"></script>

<?php $this->load->view('include/footer'); ?>
