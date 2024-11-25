<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-sm-6 col-xs-6 padding-5">
    <h3 class="title"><i class="fa fa-cubes"></i> <?php echo $this->title; ?></h3>
  </div>
	<div class="col-sm-6 col-xs-6 padding-5">
		<p class="pull-right top-p">
			<button type="button" class="btn btn-sm btn-warning" onclick="goBack()"><i class="fa fa-arrow-left"></i> Back</button>
		</p>
	</div>
</div><!-- End Row -->
<hr class="padding-5 padding-top-20"/>

<form class="form-horizontal">

	<div class="form-group">
    <label class="col-lg-4-harf col-md-4 col-sm-4 col-xs-3 control-label no-padding-right">รหัส</label>
    <div class="col-xs-12 col-sm-5 col-md-3 padding-5">
			<input type="text" name="code" id="code" maxlength="20" class="form-control input-sm" value="" onkeyup="validCode(this)" required autofocus />
    </div>
  </div>

  <div class="form-group">
    <label class="col-lg-4-harf col-md-4 col-sm-4 col-xs-3 control-label no-padding-right">ชื่อ</label>
    <div class="col-xs-12 col-sm-5 col-md-3 padding-5">
			<input type="text" name="name" id="name" maxlength="200" class="form-control input-sm" value="" required />
    </div>
  </div>

	<div class="form-group">
    <label class="col-lg-4-harf col-md-4 col-sm-4 col-xs-3 control-label no-padding-right">POS NO</label>
    <div class="col-xs-12 col-sm-5 col-md-3 padding-5">
			<input type="text" name="pos_no" id="pos_no" maxlength="32" class="form-control input-sm" value=""  />
    </div>
  </div>

	<div class="form-group">
    <label class="col-lg-4-harf col-md-4 col-sm-4 col-xs-3 control-label no-padding-right">Bill Prefix</label>
    <div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-3 padding-5">
			<input type="text" name="prefix" id="prefix" maxlength="4" class="form-control input-sm text-center" value="" />
    </div>
		<div class="col-lg-2 col-md-2-harf col-sm-2-harf col-xs-3 padding-5">
			<div class="input-group width-100">
				<span class="input-group-addon">Running</span>
				<select class="form-control input-sm" name="running" id="running">
					<option value="3">&nbsp;&nbsp; 3 &nbsp;&nbsp;&nbsp;หลัก</option>
					<option value="4">&nbsp;&nbsp; 4 &nbsp;&nbsp;&nbsp;หลัก</option>
					<option value="5">&nbsp;&nbsp; 5 &nbsp;&nbsp;&nbsp;หลัก</option>
					<option value="6">&nbsp;&nbsp; 6 &nbsp;&nbsp;&nbsp;หลัก</option>
				</select>
			</div>
    </div>
  </div>

	<div class="form-group">
    <label class="col-lg-4-harf col-md-4 col-sm-4 col-xs-3 control-label no-padding-right">Return Prefix</label>
    <div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-3 padding-5">
			<input type="text" id="return-prefix" maxlength="4" class="form-control input-sm text-center" value="" />
    </div>
		<div class="col-lg-2 col-md-2-harf col-sm-2-harf col-xs-3 padding-5">
			<div class="input-group width-100">
				<span class="input-group-addon">Running</span>
				<select class="form-control input-sm" id="return-running">
					<option value="3">&nbsp;&nbsp; 3 &nbsp;&nbsp;&nbsp;หลัก</option>
					<option value="4">&nbsp;&nbsp; 4 &nbsp;&nbsp;&nbsp;หลัก</option>
					<option value="5">&nbsp;&nbsp; 5 &nbsp;&nbsp;&nbsp;หลัก</option>
					<option value="6">&nbsp;&nbsp; 6 &nbsp;&nbsp;&nbsp;หลัก</option>
				</select>
			</div>
    </div>
  </div>

	<div class="form-group">
    <label class="col-lg-4-harf col-md-4 col-sm-4 col-xs-3 control-label no-padding-right">จุดขาย</label>
    <div class="col-xs-12 col-sm-5 col-md-3 padding-5">
			<select class="form-control input-sm" name="shop" id="shop">
				<option value="">เลือก</option>
				<?php echo select_shop_id(); //--- shop_helper ?>
			</select>
    </div>
  </div>

	<div class="form-group">
 	 <label class="col-lg-4-harf col-md-4 col-sm-4 col-xs-3 control-label no-padding-right">สถานะ</label>
 	 <div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
		 <select class="form-control input-sm" id="active">
			 <option value="1">Active</option>
			 <option value="0">Inactive</option>
		 </select>
 	 </div>
  </div>

<?php if($this->pm->can_add) : ?>
	<div class="divider-hidden"></div>
	<div class="divider-hidden"></div>
	<div class="divider-hidden"></div>
	<div class="form-group">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-center">
			<button type="button" class="btn btn-sm btn-success btn-100" id="btn-save" onclick="save()">Add</button>
    </div>
  </div>
<?php endif; ?>

</form>

<script src="<?php echo base_url(); ?>scripts/masters/pos.js?v=<?php echo date('YmdH'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/code_validate.js"></script>
<?php $this->load->view('include/footer'); ?>
