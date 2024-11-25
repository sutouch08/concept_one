<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-lg-6 col-md-6 col-sm-6 padding-5 hidden-xs">
    <h3 class="title"><?php echo $this->title; ?></h3>
  </div>
	<div class="col-xs-12 padding-5 visible-xs">
    <h3 class="title-xs"><?php echo $this->title; ?></h3>
  </div>
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5">
		<p class="pull-right top-p">
			<button type="button" class="btn btn-sm btn-warning" onclick="goBack()"><i class="fa fa-arrow-left"></i> Back</button>
		</p>
	</div>
</div><!-- End Row -->
<hr class=""/>
<form class="form-horizontal" id="addForm" method="post" action="<?php echo $this->home."/add"; ?>">

	<div class="form-group">
    <label class="col-lg-4-harf col-md-4 col-sm-4 col-xs-12 control-label no-padding-right">รหัส</label>
    <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
			<input type="text" name="code" id="code" class="width-100" maxlength="10" value="<?php echo $code; ?>" onkeyup="validCode(this)" autofocus required />
    </div>
    <div class="help-block col-xs-12 col-sm-reset inline red" id="code-error"></div>
  </div>



  <div class="form-group">
    <label class="col-lg-4-harf col-md-4 col-sm-4 col-xs-12 control-label no-padding-right">ชื่อ</label>
    <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
			<input type="text" name="name" id="name" class="width-100" value="<?php echo $name; ?>" required />
    </div>
    <div class="help-block col-xs-12 col-sm-reset inline red" id="name-error"></div>
  </div>

	<div class="form-group">
    <label class="col-lg-4-harf col-md-4 col-sm-4 col-xs-12 control-label no-padding-right">ลูกค้าเริ่มต้น</label>
    <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
			<input type="text" name="customer_name" id="customer_name" class="width-100" value="<?php echo $customer_name; ?>" />
    </div>
    <div class="help-block col-xs-12 col-sm-reset inline red" id="name-error"></div>
  </div>

	<div class="form-group">
    <label class="col-lg-4-harf col-md-4 col-sm-4 col-xs-12 control-label no-padding-right">ประเภท</label>
    <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
			<select class="form-control input-sm" name="is_online" id="is_online">
				<?php echo select_channels_type(); ?>
			</select>
    </div>
    <div class="help-block col-xs-12 col-sm-reset inline red" id="name-error"></div>
  </div>

	<div class="divider-hidden">

	</div>
  <div class="form-group">
    <label class="col-lg-4-harf col-md-4 col-sm-4 col-xs-12 control-label no-padding-right"></label>
    <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
      <p class="pull-right">
        <button type="submit" class="btn btn-sm btn-success"><i class="fa fa-save"></i> Save</button>
      </p>
    </div>
    <div class="help-block col-xs-12 col-sm-reset inline">
      &nbsp;
    </div>
  </div>
	<input type="hidden" name="channels_code" id="channels_code" value="0" />
	<input type="hidden" name="customer_code" id="customer_code" value="" />
</form>

<script src="<?php echo base_url(); ?>scripts/masters/channels.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/code_validate.js?v=<?php echo date('Ymd'); ?>"></script>
<?php $this->load->view('include/footer'); ?>
