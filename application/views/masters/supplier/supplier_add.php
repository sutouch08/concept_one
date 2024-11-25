<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-sm-6 padding-5">
    <h3 class="title"><?php echo $this->title; ?></h3>
  </div>
	<div class="col-sm-6 padding-5">
		<p class="pull-right top-p">
			<button type="button" class="btn btn-sm btn-warning" onclick="goBack()"><i class="fa fa-arrow-left"></i> Back</button>
		</p>
	</div>
</div><!-- End Row -->
<hr class="padding-5"/>
<form class="form-horizontal" id="addForm" method="post" action="<?php echo $this->home."/add"; ?>">

	<div class="form-group">
    <label class="col-sm-3 control-label no-padding-right">รหัส</label>
    <div class="col-xs-12 col-sm-3">
			<input type="text" name="code" id="code" class="width-100" maxlength="20" onkeyup="validCode(this)" placeholder="Required" required autofocus />
    </div>
  </div>

  <div class="form-group">
    <label class="col-sm-3 control-label no-padding-right">ชื่อ</label>
    <div class="col-xs-12 col-sm-3">
			<input type="text" name="name" id="name" class="width-100" maxlength="100" placeholder="Required" required />
    </div>
  </div>

  <div class="form-group">
    <label class="col-sm-3 control-label no-padding-right">ที่อยู่ 1</label>
    <div class="col-xs-12 col-sm-5">
			<input type="text" name="address1" id="address1" class="width-100" maxlength="250" placeholder="Required" required />
    </div>
  </div>

  <div class="form-group">
    <label class="col-sm-3 control-label no-padding-right">ที่อยู่ 2</label>
    <div class="col-xs-12 col-sm-5">
			<input type="text" name="address2" id="address2" class="width-100" maxlength="250"/>
    </div>
  </div>

  <div class="form-group">
    <label class="col-sm-3 control-label no-padding-right">เบอร์โทร</label>
    <div class="col-xs-12 col-sm-3">
			<input type="text" name="phone" id="phone" class="width-100" maxlength="50"/>
    </div>
  </div>

	<div class="form-group">
		<label class="col-sm-3 control-label no-padding-right">ใช้งาน</label>
		<div class="col-xs-12 col-sm-3">
			<label style="padding-top:5px;">
				<input name="active" id="active" class="ace ace-switch ace-switch-7" type="checkbox" checked />
				<span class="lbl"></span>
			</label>
		</div>
	</div>

	<div class="divider-hidden">

	</div>
  <div class="form-group">
    <label class="col-sm-3 control-label no-padding-right"></label>
    <div class="col-xs-12 col-sm-3">
      <p class="pull-right">
        <button type="button" class="btn btn-sm btn-success" onclick="save()"><i class="fa fa-save"></i> Save</button>
      </p>
    </div>
    <div class="help-block col-xs-12 col-sm-reset inline">
      &nbsp;
    </div>
  </div>
</form>

<script src="<?php echo base_url(); ?>scripts/masters/supplier.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/code_validate.js?v=<?php echo date('Ymd'); ?>"></script>
<?php $this->load->view('include/footer'); ?>
