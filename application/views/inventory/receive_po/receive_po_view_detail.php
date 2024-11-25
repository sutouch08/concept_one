<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-lg-4 col-md-4 col-sm-4 hidden-xs padding-5">
    <h3 class="title"><?php echo $this->title; ?></h3>
  </div>
	<div class="col-xs-12 padding-5 visible-xs">
		<h3 class="title-xs"><?php echo $this->title; ?> </h3>
	</div>
	<div class="col-lg-8 col-md-8 col-sm-8 col-xs-12 padding-5">
    <p class="pull-right top-p">
			<button type="button" class="btn btn-xs btn-warning btn-top btn-100" onclick="goBack()"><i class="fa fa-arrow-left"></i> กลับ</button>
      <button type="button" class="btn btn-xs btn-info btn-top btn-100" onclick="printReceived()"><i class="fa fa-print"></i> พิมพ์</button>
			<?php if($doc->status == 1) : ?>
			<button type="button" class="btn btn-xs btn-success btn-top btn-100" onclick="doExport()"><i class="fa fa-send"></i> ส่งข้อมูลไป SAP</button>
			<?php endif; ?>
			<?php if($doc->status == 0 && $this->pm->can_edit) : ?>
				<button type="button" class="btn btn-xs btn-warning btn-top btn-100" onclick="goEdit('<?php echo $doc->code; ?>')"><i class="fa fa-pencil"></i> แก้ไข</button>
			<?php endif; ?>
      <?php if($this->pm->can_delete && $doc->status != 2) : ?>
				<?php if($doc->status == 1) : ?>
				<button type="button" class="btn btn-xs btn-purple btn-top btn-100" onclick="unSave('<?php echo $doc->code; ?>')"><i class="fa fa-exclamation-circle"></i>&nbsp; ย้อนสถานะ</button>
				<?php endif; ?>
        <button type="button" class="btn btn-xs btn-danger btn-top btn-100" onclick="goDelete('<?php echo $doc->code; ?>')"><i class="fa fa-exclamation-triangle"></i>&nbsp; ยกเลิก</button>
      <?php endif; ?>
    </p>
  </div>
</div>
<hr class="padding-5" />

<div class="row">
  <div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-6 padding-5">
  	<label>เลขที่เอกสาร</label>
    <input type="text" class="form-control input-sm text-center" value="<?php echo $doc->code; ?>" disabled />
  </div>
	<div class="col-lg-1 col-md-1-harf col-sm-2 col-xs-6 padding-5">
    <label>วันที่</label>
    <input type="text" class="form-control input-sm text-center" name="date_add" id="dateAdd" value="<?php echo thai_date($doc->date_add); ?>" disabled />
  </div>
  <div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-6 padding-5">
    <label>รหัสผู้จำหน่าย</label>
    <input type="text" class="form-control input-sm text-center" value="<?php echo $doc->vendor_code; ?>" disabled />
  </div>
  <div class="col-lg-5 col-md-6-harf col-sm-6 col-xs-6 padding-5">
  	<label class="not-show">ผู้จำหน่าย</label>
    <input type="text" class="form-control input-sm" value="<?php echo $doc->vendor_name; ?>" disabled />
  </div>

	<div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-6 padding-5">
    <label>ใบสั่งซื้อ</label>
    <input type="text" class="form-control input-sm text-center" value="<?php echo $doc->po_code; ?>" disabled />
  </div>

  <div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-6 padding-5">
  	<label>ใบส่งสินค้า</label>
    <input type="text" class="form-control input-sm text-center" value="<?php echo $doc->invoice_code; ?>" disabled/>
  </div>

	<div class="col-lg-1 col-md-2 col-sm-2 col-xs-6 padding-5">
		<label>Currency</label>
		<input type="text" class="form-control input-sm text-center" value="<?php echo $doc->currency; ?>" disabled />
	</div>
	<div class="col-lg-1 col-md-2 col-sm-2 col-xs-6 padding-5">
		<label>Rate</label>
		<input type="number" class="form-control input-sm text-center" value="<?php echo $doc->rate; ?>" disabled />
	</div>

  <div class="col-lg-2-harf col-md-4 col-sm-4 col-xs-6 padding-5">
    <label>รหัสโซน</label>
    <input type="text" class="form-control input-sm" value="<?php echo $doc->zone_code; ?>" disabled />
  </div>
  <div class="col-lg-6 col-md-8 col-sm-8 col-xs-6 padding-5">
  	<label>ชื่อโซน</label>
    <input type="text" class="form-control input-sm" value="<?php echo $doc->zone_name; ?>" disabled/>
  </div>
	<div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-6 padding-5">
		<label>SAP No.</label>
		<input type="text" class="form-control input-sm text-center" value="<?php echo $doc->inv_code; ?>" disabled />
	</div>
  <input type="hidden" name="receive_code" id="receive_code" value="<?php echo $doc->code; ?>" />
</div>

<?php
if($doc->is_expire OR $doc->status == 2)
{
	if($doc->status == 2)
	{
		$this->load->view('cancle_watermark');
	}
	else
	{
		$this->load->view('expire_watermark');
	}
}
?>
<hr class="margin-top-15 padding-5"/>
<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 table-responsive">
    <table class="table table-striped table-bordered" style="min-width:1020px;">
      <thead>
      	<tr class="font-size-12">
        	<th class="fix-width-40 text-center">ลำดับ</th>
          <th class="fix-width-200 text-center">รหัสสินค้า</th>
          <th class="min-width-250">ชื่อสินค้า</th>
					<th class="fix-width-80 text-center">VatGroup</th>
					<th class="fix-width-100 text-right">ราคา</th>
					<th class="fix-width-100 text-right">ส่วนลด</th>
          <th class="fix-width-100 text-right">จำนวน</th>
					<th class="fix-width-150 text-right">มูลค่า</th>
        </tr>
      </thead>
      <tbody>
        <?php if(!empty($details)) : ?>
          <?php $no =  1; ?>
          <?php $totalQty = 0; ?>
					<?php $totalAmount = 0; ?>
          <?php foreach($details as $rs) : ?>
            <tr class="font-size-12">
              <td class="middle text-center"><?php echo $no; ?></td>
              <td class="middle"><?php echo $rs->product_code; ?></td>
              <td class="middle"><?php echo $rs->product_name; ?></td>
							<td class="middle text-center"><?php echo $rs->vatGroup; ?></td>
							<td class="middle text-right"><?php echo number($rs->PriceBefDi, 3); ?></td>
							<td class="middle text-right"><?php echo number($rs->DiscPrcnt, 2); ?></td>
							<td class="middle text-right"><?php echo number($rs->receive_qty); ?> <?php echo $rs->unitMsr; ?></td>
							<td class="middle text-right"><?php echo number($rs->amount, 2); ?></td>
            </tr>
            <?php $no++; ?>
						<?php $totalQty += $rs->receive_qty; ?>
						<?php $totalAmount += $rs->amount; ?>
          <?php endforeach; ?>
        <?php endif; ?>
			  </tbody>
      </table>
    </div>

		<div class="divider-hidden"></div>
		<div class="divider-hidden"></div>

		<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5">
	    <div class="form-horizontal">

	      <div class="form-group">
	        <label class="col-lg-3 col-md-4 col-sm-4 control-label no-padding-right">เจ้าของ</label>
	        <div class="col-lg-5 col-md-6 col-sm-6 col-xs-12">
	          <input type="text" class="form-control input-sm" value="<?php echo $this->user_model->get_name($doc->user); ?>" disabled />
	  				<input type="hidden" id="owner" value="<?php echo $doc->user; ?>" />
	        </div>
	      </div>

	      <div class="form-group">
	        <label class="col-lg-3 col-md-4 col-sm-4 col-xs-12 control-label no-padding-right">หมายเหตุ</label>
	        <div class="col-lg-9 col-md-8 col-sm-8 col-xs-12">
	          <textarea id="remark" maxlength="254" rows="3" class="form-control" disabled><?php echo $doc->remark; ?></textarea>
	        </div>
	      </div>

	    </div>
	  </div>


		<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5">
	    <div class="form-horizontal">
	      <div class="form-group" style="margin-bottom:5px;">
	        <label class="col-lg-8 col-md-8 col-sm-7 col-xs-6 control-label no-padding-right">จำนวนรวม</label>
	        <div class="col-lg-4 col-md-4 col-sm-5 col-xs-6 padding-5 last">
	          <input type="text" class="form-control input-sm text-right" id="total-qty" value="<?php echo number($doc->totalQty, 2); ?>" disabled>
	        </div>
	      </div>

				<div class="form-group" style="margin-bottom:5px;">
	        <label class="col-lg-8 col-md-8 col-sm-7 col-xs-6 control-label no-padding-right">มูลค่าก่อนส่วนลด</label>
	        <div class="col-lg-4 col-md-4 col-sm-5 col-xs-6 padding-5 last">
	          <input type="text" id="total-amount" class="form-control input-sm text-right" value="<?php echo number($totalAmount, 2); ?>" disabled/>
	        </div>
	      </div>

	      <div class="form-group" style="margin-bottom:5px;">
	        <label class="col-lg-6 col-md-5-harf col-sm-4 col-xs-3 control-label no-padding-right">ส่วนลด</label>
	        <div class="col-lg-2 col-md-2-harf col-sm-3 col-xs-3 padding-5">
	          <span class="input-icon input-icon-right">
	          <input type="number" id="discPrcnt" class="form-control input-sm" value="<?php echo number($doc->DiscPrcnt, 2); ?>" disabled/>
	          <i class="ace-icon fa fa-percent"></i>
	          </span>
	        </div>
	        <div class="col-lg-4 col-md-4 col-sm-5 col-xs-6 padding-5 last">
	          <input type="text" id="disc-amount" class="form-control input-sm text-right" onchange="reCalDiscAmount()" value="<?php echo number($doc->DiscAmount, 2); ?>" disabled>
	        </div>
	      </div>

	      <div class="form-group" style="margin-bottom:5px;">
	        <label class="col-lg-8 col-md-8 col-sm-7 col-xs-6 control-label no-padding-right">ภาษีมูลค่าเพิ่ม</label>
	        <div class="col-lg-4 col-md-4 col-sm-5 col-xs-6 padding-5 last">
	          <input type="text" id="vat-sum" class="form-control input-sm text-right" value="<?php echo number($doc->VatSum, 2); ?>" disabled />
	        </div>
	      </div>

	      <div class="form-group" style="margin-bottom:5px;">
	        <label class="col-lg-8 col-md-8 col-sm-7 col-xs-6 control-label no-padding-right">รวมทั้งสิ้น</label>
	        <div class="col-lg-4 col-md-4 col-sm-5 col-xs-6 padding-5 last">
	          <input type="text" id="doc-total" class="form-control input-sm text-right" value="<?php echo number($doc->DocTotal, 2); ?>" disabled/>
	        </div>
	      </div>
	    </div>
	  </div>
</div>

<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5">
		<?php if(!empty($approve_logs)) : ?>
				<?php foreach($approve_logs as $logs) : ?>
					<?php if($logs->approve == 1) : ?>
					  <span class="green">อนุมัติโดย : <?php echo $logs->approver; ?> @ <?php echo thai_date($logs->date_upd, TRUE); ?></span>
					<?php else : ?>
						<span class="red">ยกเลิกโดย : <?php echo $logs->approver; ?> @ <?php echo thai_date($logs->date_upd, TRUE); ?> </span>
					<?php endif; ?>
			<?php endforeach; ?>
		<?php endif; ?>

		<?php if($doc->status == 2) : ?>
			<span class="red display-block">ยกเลิกโดย : <?php echo $doc->cancle_user; ?> @ <?php echo thai_date($doc->cancle_date, TRUE); ?></span>
			<span class="red display-block">หมายเหตุ : <?php echo $doc->cancle_reason; ?></span>
		<?php endif; ?>
	</div>
</div>




<?php $this->load->view('cancle_modal'); ?>
<?php $this->load->view('accept_modal'); ?>

<script src="<?php echo base_url(); ?>scripts/inventory/receive_po/receive_po.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/inventory/receive_po/receive_po_add.js?v=<?php echo date('Ymd'); ?>"></script>

<?php $this->load->view('include/footer'); ?>
