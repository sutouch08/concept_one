<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-sm-6">
    <h3 class="title"><?php echo $this->title; ?></h3>
  </div>
	<div class="col-sm-6">
		<p class="pull-right">
			<button type="button" class="btn btn-sm btn-warning" onclick="goBack()"><i class="fa fa-arrow-left"></i> Back</button>
		</p>
	</div>
</div><!-- End Row -->
<hr class="title-block"/>
<form class="form-horizontal" id="addForm" method="post" action="<?php echo $this->home."/add"; ?>">

	<div class="form-group">
    <label class="col-sm-3 control-label no-padding-right">รหัส</label>
    <div class="col-xs-12 col-sm-3">
      <input type="text" name="code" id="code" class="width-100" value="<?php echo $code; ?>" autofocus required />
    </div>
    <div class="help-block col-xs-12 col-sm-reset inline red" id="code-error"></div>
  </div>



  <div class="form-group">
    <label class="col-sm-3 control-label no-padding-right">ชื่อ</label>
    <div class="col-xs-12 col-sm-3">
			<input type="text" name="name" id="name" class="width-100" value="<?php echo $name; ?>" required />
    </div>
    <div class="help-block col-xs-12 col-sm-reset inline red" id="name-error"></div>
  </div>

	<div class="form-group">
    <label class="col-sm-3 control-label no-padding-right">เลขประจำตัว/Tax ID</label>
    <div class="col-xs-12 col-sm-3">
			<input type="text" name="Tax_id" id="Tax_id" class="width-100" value="<?php echo $Tax_Id; ?>" />
    </div>
  </div>

	<div class="form-group">
 	 <label class="col-sm-3 control-label no-padding-right">รหัสบัญชีลูกหนี้</label>
 	 <div class="col-xs-12 col-sm-3">
 		 <select name="DebPayAcct" id="DebPayAcct" class="form-control" required>
			 <option value="">เลือกรายการ</option>
 			 <?php echo select_DebPayAcct($DebPayAcct); ?>
 		 </select>
 	 </div>
  </div>



	<div class="form-group">
 	 <label class="col-sm-3 control-label no-padding-right">กลุ่มลูกหนี้</label>
 	 <div class="col-xs-12 col-sm-3">
 		 <select name="GroupCode" id="GroupCode" class="form-control" required>
 			 <?php echo select_GroupCode($GroupCode); ?>
 		 </select>
 	 </div>
  </div>

	<div class="form-group">
 	 <label class="col-sm-3 control-label no-padding-right">รูปแบบ</label>
 	 <div class="col-xs-12 col-sm-3">
 		 <select name="cmpPrivate" id="cmpPrivate" class="form-control">
 			<option value="C" <?php echo is_selected('C', $cmpPrivate); ?>>บริษัท/ร้าน</option>
			<option value="G" <?php echo is_selected('G', $cmpPrivate); ?>>หน่วนงานรัฐ</option>
			<option value="I" <?php echo is_selected('I', $cmpPrivate); ?>>บุคคลทั่วไป</option>
 		 </select>
 	 </div>
  </div>

	<div class="form-group">
 	 <label class="col-sm-3 control-label no-padding-right">รหัสกลุ่มเครดิต</label>
 	 <div class="col-xs-12 col-sm-3">
 		 <select name="GroupNum" id="GroupNum" class="form-control">
 			 <?php echo select_GroupNum($GroupNum); ?>
 		 </select>
 	 </div>
  </div>


	<div class="form-group">
    <label class="col-sm-3 control-label no-padding-right">กลุ่มลูกค้า</label>
    <div class="col-xs-12 col-sm-3">
			<select name="group" id="group" class="form-control" required>
				<option value="">เลือกรายการ</option>
				<?php echo select_customer_group($group); ?>
			</select>
    </div>
    <div class="help-block col-xs-12 col-sm-reset inline red" id="group-error"></div>
  </div>


	<div class="form-group">
    <label class="col-sm-3 control-label no-padding-right">ประเภทลูกค้า</label>
    <div class="col-xs-12 col-sm-3">
			<select name="kind" id="kind" class="form-control" required>
				<option value="">เลือกรายการ</option>
				<?php echo select_customer_kind($kind); ?>
			</select>
    </div>
    <div class="help-block col-xs-12 col-sm-reset inline red" id="kind-error"></div>
  </div>


	<div class="form-group">
    <label class="col-sm-3 control-label no-padding-right">ชนิดลูกค้า</label>
    <div class="col-xs-12 col-sm-3">
			<select name="type" id="type" class="form-control" required>
				<option value="">เลือกรายการ</option>
				<?php echo select_customer_type($type); ?>
			</select>
    </div>
    <div class="help-block col-xs-12 col-sm-reset inline red" id="type-error"></div>
  </div>



	<div class="form-group">
    <label class="col-sm-3 control-label no-padding-right">เกรดลูกค้า</label>
    <div class="col-xs-12 col-sm-3">
			<select name="class" id="class" class="form-control" required>
				<option value="">เลือกรายการ</option>
				<?php echo select_customer_class($class); ?>
			</select>
    </div>
    <div class="help-block col-xs-12 col-sm-reset inline red" id="class-error"></div>
  </div>


	<div class="form-group">
    <label class="col-sm-3 control-label no-padding-right">พื้นที่ขาย</label>
    <div class="col-xs-12 col-sm-3">
			<select name="area" id="area" class="form-control" required>
				<option value="">เลือกรายการ</option>
				<?php echo select_customer_area($area); ?>
			</select>
    </div>
    <div class="help-block col-xs-12 col-sm-reset inline red" id="area-error"></div>
  </div>

	<div class="form-group">
	 <label class="col-sm-3 control-label no-padding-right">พนักงานขาย</label>
	 <div class="col-xs-12 col-sm-3">
		 <select name="sale" id="sale" class="form-control">
			 <?php echo select_sale($sale); ?>
		 </select>
	 </div>
	</div>

	<div class="form-group">
    <label class="col-sm-3 control-label no-padding-right">วงเงินเครดิต</label>
    <div class="col-xs-12 col-sm-3">
			<input type="number" name="CreditLine" id="CreditLine" class="width-100" value="<?php echo $CreditLine; ?>" />
    </div>
  </div>


	<div class="divider-hidden"></div>
  <div class="form-group">
    <label class="col-sm-3 control-label no-padding-right"></label>
    <div class="col-xs-12 col-sm-3">
      <p class="pull-right">
        <button type="submit" class="btn btn-sm btn-success"><i class="fa fa-save"></i> Save</button>
      </p>
    </div>
    <div class="help-block col-xs-12 col-sm-reset inline">
      &nbsp;
    </div>
  </div>
</form>

<script src="<?php echo base_url(); ?>scripts/masters/customers.js"></script>
<?php $this->load->view('include/footer'); ?>
