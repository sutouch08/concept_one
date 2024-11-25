<form class="form-horizontal" id="addForm" method="post" action="<?php echo $this->home."/update"; ?>">

	<div class="form-group">
    <label class="col-sm-3 control-label no-padding-right">รหัส</label>
    <div class="col-xs-12 col-sm-3">
      <input type="text" class="form-control input-sm" value="<?php echo $ds->code; ?>" disabled />
			<input type="hidden" name="code" id="code" value="<?php echo $ds->code; ?>">
    </div>
    <div class="help-block col-xs-12 col-sm-reset inline red" id="code-error"></div>
  </div>



  <div class="form-group">
    <label class="col-sm-3 control-label no-padding-right">ชื่อ</label>
    <div class="col-xs-12 col-sm-7">
			<input type="text" name="name" id="name" class="form-control input-sm" value="<?php echo $ds->name; ?>" required />
    </div>
    <div class="help-block col-xs-12 col-sm-reset inline red" id="name-error"></div>
  </div>

	<div class="form-group">
    <label class="col-sm-3 control-label no-padding-right">รหัสเก่า</label>
    <div class="col-xs-12 col-sm-3">
      <input type="text" class="form-control input-sm" name="old_code" id="old_code" value="<?php echo $ds->old_code; ?>" />
    </div>
    <div class="help-block col-xs-12 col-sm-reset inline red" id="old_code-error"></div>
  </div>

	<div class="form-group">
    <label class="col-sm-3 control-label no-padding-right">เลขประจำตัว/Tax ID</label>
    <div class="col-xs-12 col-sm-4">
			<input type="text" name="Tax_Id" id="Tax_Id" class="width-100" value="<?php echo $ds->Tax_Id; ?>" />
    </div>
  </div>

	<div class="form-group">
 	 <label class="col-sm-3 control-label no-padding-right">รหัสบัญชีลูกหนี้</label>
 	 <div class="col-xs-12 col-sm-4">
 		 <select name="DebPayAcct" id="DebPayAcct" class="form-control" required>
			 <option value="">เลือกรายการ</option>
 			 <?php echo select_DebPayAcct($ds->DebPayAcct); ?>
 		 </select>
 	 </div>
  </div>



	<div class="form-group">
 	 <label class="col-sm-3 control-label no-padding-right">กลุ่มลูกหนี้</label>
 	 <div class="col-xs-12 col-sm-4">
 		 <select name="GroupCode" id="GroupCode" class="form-control" required>
 			 <?php echo select_GroupCode($ds->GroupCode); ?>
 		 </select>
 	 </div>
  </div>

	<div class="form-group">
 	 <label class="col-sm-3 control-label no-padding-right">รูปแบบ</label>
 	 <div class="col-xs-12 col-sm-4">
 		 <select name="cmpPrivate" id="cmpPrivate" class="form-control">
 			<option value="C" <?php echo is_selected('C', $ds->cmpPrivate); ?>>บริษัท/ร้าน</option>
			<option value="G" <?php echo is_selected('G', $ds->cmpPrivate); ?>>หน่วนงานรัฐ</option>
			<option value="I" <?php echo is_selected('I', $ds->cmpPrivate); ?>>บุคคลทั่วไป</option>
 		 </select>
 	 </div>
  </div>

	<div class="form-group">
 	 <label class="col-sm-3 control-label no-padding-right">รหัสกลุ่มเครดิต</label>
 	 <div class="col-xs-12 col-sm-4">
 		 <select name="GroupNum" id="GroupNum" class="form-control">
 			 <?php echo select_GroupNum($ds->GroupNum); ?>
 		 </select>
 	 </div>
  </div>

	<div class="form-group">
    <label class="col-sm-3 control-label no-padding-right">กลุ่มลูกค้า</label>
    <div class="col-xs-12 col-sm-4">
			<select name="group" id="group" class="form-control" >
				<option value="">เลือกรายการ</option>
				<?php echo select_customer_group($ds->group_code); ?>
			</select>
    </div>
    <div class="help-block col-xs-12 col-sm-reset inline red" id="group-error"></div>
  </div>


	<div class="form-group">
    <label class="col-sm-3 control-label no-padding-right">ประเภทลูกค้า</label>
    <div class="col-xs-12 col-sm-4">
			<select name="kind" id="kind" class="form-control">
				<option value="">เลือกรายการ</option>
				<?php echo select_customer_kind($ds->kind_code); ?>
			</select>
    </div>
    <div class="help-block col-xs-12 col-sm-reset inline red" id="kind-error"></div>
  </div>


	<div class="form-group">
    <label class="col-sm-3 control-label no-padding-right">ชนิดลูกค้า</label>
    <div class="col-xs-12 col-sm-4">
			<select name="type" id="type" class="form-control">
				<option value="">เลือกรายการ</option>
				<?php echo select_customer_type($ds->type_code); ?>
			</select>
    </div>
    <div class="help-block col-xs-12 col-sm-reset inline red" id="type-error"></div>
  </div>



	<div class="form-group">
    <label class="col-sm-3 control-label no-padding-right">เกรดลูกค้า</label>
    <div class="col-xs-12 col-sm-4">
			<select name="class" id="class" class="form-control" >
				<option value="">เลือกรายการ</option>
				<?php echo select_customer_class($ds->class_code); ?>
			</select>
    </div>
    <div class="help-block col-xs-12 col-sm-reset inline red" id="class-error"></div>
  </div>


	<div class="form-group">
    <label class="col-sm-3 control-label no-padding-right">พื้นที่ขาย</label>
    <div class="col-xs-12 col-sm-4">
			<select name="area" id="area" class="form-control">
				<option value="">เลือกรายการ</option>
				<?php echo select_customer_area($ds->area_code); ?>
			</select>
    </div>
    <div class="help-block col-xs-12 col-sm-reset inline red" id="area-error"></div>
  </div>


	<div class="form-group">
	 <label class="col-sm-3 control-label no-padding-right">พนักงานขาย</label>
	 <div class="col-xs-12 col-sm-4">
		 <select name="sale" id="sale" class="form-control">
			 <?php echo select_sale($ds->sale_code); ?>
		 </select>
	 </div>
	</div>

	<div class="form-group">
    <label class="col-sm-3 control-label no-padding-right">วงเงินเครดิต</label>
    <div class="col-xs-12 col-sm-3">
			<input type="number" name="CreditLine" id="CreditLine" class="width-100" value="<?php echo round($ds->CreditLine,2); ?>" />
    </div>
  </div>

	<?php if($ds->CreditLine > 0) : ?>
		<div class="form-group">
	    <label class="col-sm-3 control-label no-padding-right">วงเงินคงเหลือ</label>
	    <div class="col-xs-12 col-sm-3">
				<input type="number" class="width-100" value="<?php echo round($this->customers_model->get_credit($ds->code), 2); ?>" disabled/>
	    </div>
	  </div>
	<?php endif; ?>

	<div class="form-group">
    <label class="col-sm-3 control-label no-padding-right">GP(%)</label>
    <div class="col-xs-12 col-sm-3">
			<input type="number" name="gp" id="gp" class="width-100" value="<?php echo round($ds->gp,2); ?>" />
    </div>
  </div>

	<div class="form-group">
    <label class="col-sm-3 control-label no-padding-right">Over due</label>
    <div class="col-xs-12 col-sm-4">
			<label class="margin-top-5">
				<input type="checkbox" class="ace input-lg" name="skip_overdue" id="skip_overdue" value="1" <?php echo is_checked('1', $ds->skip_overdue); ?>>
				<span class="lbl bigger-120"> ไม่ตรวจสอบยอดค้างชำระ</span>
			</label>
    </div>
  </div>


	<div class="divider-hidden">

	</div>
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
	<input type="hidden" name="customers_code" id="customers_code" value="<?php echo $ds->code; ?>" />
	<input type="hidden" name="customers_name" value="<?php echo $ds->name; ?>" />
</form>
