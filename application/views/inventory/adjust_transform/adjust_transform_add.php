<?php $this->load->view('include/header'); ?>
<?php $manual_code = getConfig('MANUAL_DOC_CODE');  ?>
<?php if($manual_code == 1) :?>
	<input type="hidden" id="manualCode" value="<?php echo $manual_code; ?>">
	<input type="hidden" id="prefix" value="<?php echo getConfig('PREFIX_ADJUST_TRANSFORM'); ?>">
	<input type="hidden" id="runNo" value="<?php echo getConfig('RUN_DIGIT_ADJUST_TRANSFORM'); ?>">
<?php endif; ?>
<div class="row">
	<div class="col-sm-6 col-xs-6 padding-5">
    	<h3 class="title" >
        <?php echo $this->title; ?>
      </h3>
	</div>
    <div class="col-sm-6 col-xs-6 padding-5">
      	<p class="pull-right top-p">
			    <button type="button" class="btn btn-sm btn-warning" onclick="goBack()"><i class="fa fa-arrow-left"></i> กลับ</button>
        </p>
    </div>
</div>
<hr class="padding-5" />

<div class="row">
    <div class="col-sm-1 col-1-harf col-xs-6 padding-5">
    	<label>เลขที่เอกสาร</label>
			<?php if($manual_code == 1) : ?>
				<input type="text" class="form-control input-sm" name="code" id="code" value="" required />
			<?php else : ?>
				<input type="text" class="form-control input-sm" value="" disabled />
			<?php endif; ?>
    </div>
		<div class="col-sm-1 col-xs-6 padding-5">
    	<label>วันที่</label>
      <input type="text" class="form-control input-sm text-center" name="date_add" id="date_add" value="<?php echo date('d-m-Y'); ?>" readonly />
    </div>
		<div class="col-sm-2 col-xs-4 padding-5">
			<label>โซนแปรสภาพ</label>
			<input type="text" class="form-control input-sm text-center" name="zone" id="zone" value="" placeholder="ระบุโซนแปรสภาพที่ตัดยอด"/>
		</div>

		<div class="col-sm-3 col-xs-8 padding-5">
			<label class="not-show">ชื่อโซน</label>
			<input type="text" class="form-control input-sm" name="zone" id="zoneName" value="" placeholder="ระบุโซนแปรสภาพที่ตัดยอด" disabled/>
		</div>
		<div class="col-sm-3 col-3-harf col-xs-12 padding-5">
    	<label>หมายเหตุ</label>
        <input type="text" class="form-control input-sm" name="remark" id="remark" placeholder="ระบุหมายเหตุเอกสาร (ถ้ามี)" />
    </div>
		<div class="col-sm-1 col-xs-12 padding-5">
			<label class="display-block not-show">add</label>
			<?php if($this->pm->can_add) : ?>
				<?php if($manual_code == 1) : ?>
							<button type="button" class="btn btn-xs btn-success btn-block" onclick="validateOrder()"><i class="fa fa-plus"></i> เพิ่ม</button>
				<?php else : ?>
							<button type="button" class="btn btn-xs btn-success btn-block" onclick="add()"><i class="fa fa-plus"></i> เพิ่ม</button>
				<?php endif; ?>
			<?php	endif; ?>
		</div>
</div>

<input type="hidden" id="zone_code" value="" />
<hr class="margin-top-15 padding-5"/>


<script src="<?php echo base_url(); ?>scripts/inventory/adjust_transform/adjust_transform.js"></script>
<script src="<?php echo base_url(); ?>scripts/inventory/adjust_transform/adjust_transform_add.js"></script>
<?php $this->load->view('include/footer'); ?>
