<?php $this->load->view('include/header'); ?>

<div class="row">
	<div class="col-sm-6 col-xs-6 padding-5">
    	<h3 class="title" >
        <?php echo $this->title; ?>
      </h3>
	</div>
    <div class="col-sm-6 col-xs-6 padding-5">
      <p class="pull-right top-p">
				<button type="button" class="btn btn-sm btn-warning" onclick="goBack()"><i class="fa fa-arrow-left"></i> กลับ</button>
				<?php if(($this->pm->can_add OR $this->pm->can_edit) && $doc->status == 0 && $doc->valid == 0) : ?>
	      <button type="button" class="btn btn-sm btn-primary" onclick="reloadStock()">
	        <i class="fa fa-refresh"></i> โหลดยอดตั้งต้นใหม่
	      </button>
				<?php if($doc->is_wms == 0) : ?>
	       <!--- consign_check_detail.js --->
	      <button type="button" class="btn btn-sm btn-success" onclick="closeCheck()">
	        <i class="fa fa-bolt"></i> บันทึกการตรวจนับ
	      </button>
				<?php endif; ?>
			<?php endif; ?>

			<?php if(($this->_SuperAdmin && $doc->status != 2) OR (($this->pm->can_edit OR $this->pm->can_delete) && $doc->status != 2 && $doc->valid == 0)) : ?>
				<!--- consign_check_detail.js --->
	      <button type="button" class="btn btn-sm btn-danger" onclick="openCheck()">
	        <i class="fa fa-bolt"></i> ยกเลิกการบันทึก
	      </button>
	    <?php endif; ?>

			<?php if($this->isAPI && $doc->is_wms == 1 && ($doc->status == 3 OR $doc->status == 0) && $doc->valid == 0) : ?>
				<button type="button" class="btn btn-sm btn-success" onclick="sendToWms()"><i class="fa fa-send"></i> Send to WMS</button>
			<?php endif; ?>

			<?php if($this->pm->can_delete && $doc->status == 0 && $doc->valid == 0) : ?>
				<!--- consign_check_detail.js --->
        <button type="button" class="btn btn-sm btn-danger" onclick="clearDetails()">
          <i class="fa fa-trash"></i> ยกเลิกการตรวจนับ
        </button>
			<?php endif; ?>
      </p>
    </div>
</div>
<hr />

<div class="row">
    <div class="col-sm-1 col-1-harf padding-5">
    	<label>เลขที่เอกสาร</label>
        <input type="text" class="form-control input-sm text-center" value="<?php echo $doc->code; ?>" disabled />
    </div>
		<div class="col-sm-1 col-1-harf padding-5">
    	<label>วันที่</label>
      <input type="text" class="form-control input-sm text-center" name="date_add" id="dateAdd" value="<?php echo thai_date($doc->date_add); ?>" disabled>
    </div>
		<div class="col-sm-4 padding-5">
			<label>ลูกค้า</label>
			<input type="text" class="form-control input-sm edit" name="customer" id="customer" value="<?php echo $doc->customer_name; ?>"  disabled/>
		</div>
		<div class="col-sm-5 padding-5">
			<label>โซน[ฝากขาย]</label>
			<input type="text" class="form-control input-sm edit" name="zone" id="zone" value="<?php echo $doc->zone_name; ?>" disabled />
		</div>
		<div class="col-sm-2 padding-5">
			<label>การรับสินค้า</label>
			<select class="form-control input-sm" name="is_wms" id="is_wms" disabled>
				<option value="1" <?php echo is_selected('1', $doc->is_wms); ?>>WMS</option>
				<option value="0" <?php echo is_selected('0', $doc->is_wms); ?>>Warrix</option>
			</select>
		</div>
		<div class="col-sm-10 padding-5">
    	<label>หมายเหตุ</label>
        <input type="text" class="form-control input-sm" name="remark" id="remark" value="<?php echo $doc->remark; ?>" disabled>
    </div>
	<?php if($doc->status == 2) : ?>
		<div class="col-lg-10 col-md-10 col-sm-10 col-xs-12 padding-5">
    	<label>เหตุผลในการยกเลิก</label>
        <input type="text" class="form-control input-sm" value="<?php echo $doc->cancle_reason; ?>" disabled>
    </div>
		<div class="col-lg-2 col-md-2 col-sm-2 col-xs-12 padding-5">
			<label>ยกเลิกโดย</label>
      <input type="text" class="form-control input-sm" value="<?php echo $doc->cancle_user; ?>" disabled>
		</div>
	<?php endif; ?>
</div>
<input type="hidden" name="zone_code" id="zone_code" value="<?php echo $doc->zone_code; ?>">
<input type="hidden" name="customer_code" id="customer_code" value="<?php echo $doc->customer_code; ?>">
<input type="hidden" name="check_code" id="check_code" value="<?php echo $doc->code; ?>">
<input type="hidden" name="id_box" id="id_box">

<hr class="margin-top-15"/>
<?php if($doc->status != 2) : ?>
	<?php if($doc->status == 3) : ?>
		<?php $this->load->view('on_process_watermark'); ?>
	<?php endif; ?>
<?php 	$this->load->view('inventory/consign_check/consign_check_edit_detail'); ?>
<?php else : ?>
	<?php $this->load->view('cancle_watermark'); ?>
<?php endif; ?>

<script src="<?php echo base_url(); ?>scripts/inventory/consign_check/consign_check.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/inventory/consign_check/consign_check_add.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/inventory/consign_check/consign_check_control.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/inventory/consign_check/consign_check_detail.js?v=<?php echo date('Ymd'); ?>"></script>
<?php $this->load->view('include/footer'); ?>
