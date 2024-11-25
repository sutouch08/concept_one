<?php $this->load->view('include/header'); ?>
<script>
	var USE_STRONG_PWD = <?php echo getConfig('USE_STRONG_PWD'); ?>;
</script>
<div class="row">
	<div class="col-sm-6 padding-5">
    <h3 class="title">
      <i class="fa fa-users"></i> <?php echo $this->title; ?>
    </h3>
    </div>
		<div class="col-sm-6 padding-5">
			<p class="pull-right top-p"></p>
		</div>
</div><!-- End Row -->
<hr class="padding-5"/>
<form class="form-horizontal" id="resetForm" method="post">

	<div class="form-group">
    <label class="col-sm-3 control-label no-padding-right">Display name</label>
    <div class="col-xs-12 col-sm-3">
			<span class="input-icon input-icon-right width-100">
      	<input type="text" name="dname" id="dname" class="width-100" value="<?php echo $data->name; ?>" disabled />
				<i class="ace-icon fa fa-user"></i>
			</span>
    </div>
    <div class="help-block col-xs-12 col-sm-reset inline red" id="dname-error"></div>
  </div>



  <div class="form-group">
    <label class="col-sm-3 control-label no-padding-right">User name</label>
    <div class="col-xs-12 col-sm-3">
			<span class="input-icon input-icon-right width-100">
        <input type="text" name="uname" id="uname" class="width-100" value="<?php echo $data->uname; ?>" disabled />
				<i class="ace-icon fa fa-user"></i>
			</span>
    </div>
    <div class="help-block col-xs-12 col-sm-reset inline red" id="uname-error"></div>
  </div>

	<div class="form-group">
    <label class="col-sm-3 control-label no-padding-right">รหัสผ่านปัจจุบัน</label>
    <div class="col-xs-12 col-sm-3">
			<span class="input-icon input-icon-right width-100">
        <input type="password" name="cu-pwd" id="cu-pwd" class="width-100" autofocus />
				<i class="ace-icon fa fa-key"></i>
			</span>
    </div>
    <div class="help-block col-xs-12 col-sm-reset inline red" style="padding-left:15px;" id="cu-pwd-error"></div>
  </div>

  <div class="form-group">
    <label class="col-sm-3 control-label no-padding-right">รหัสผ่านใหม่</label>
    <div class="col-xs-12 col-sm-3">
			<span class="input-icon input-icon-right width-100">
        <input type="password" name="pwd" id="pwd" class="width-100" />
				<i class="ace-icon fa fa-key"></i>
			</span>
    </div>
    <div class="help-block col-xs-12 col-sm-reset inline red" style="padding-left:15px;" id="pwd-error"></div>
  </div>

	<div class="form-group">
    <label class="col-sm-3 control-label no-padding-right">ยืนยันรหัสผ่านใหม่</label>
    <div class="col-xs-12 col-sm-3">
			<span class="input-icon input-icon-right width-100">
        <input type="password" name="cm-pwd" id="cm-pwd" class="width-100" required />
				<i class="ace-icon fa fa-key"></i>
			</span>
    </div>
    <div class="help-block col-xs-12 col-sm-reset inline red" style="padding-left:15px;" id="cm-pwd-error"></div>
  </div>

	<div class="divider-hidden">

	</div>
  <div class="form-group">
    <label class="col-sm-3 control-label no-padding-right"></label>
    <div class="col-xs-12 col-sm-3">
      <p class="pull-right">
        <button type="button" class="btn btn-sm btn-success" onclick="changePassword()"><i class="fa fa-save"></i> เปลี่ยนรหัสผ่าน</button>
      </p>
    </div>
    <div class="help-block col-xs-12 col-sm-reset inline">
      &nbsp;
    </div>
  </div>
	<input type="hidden" name="user_id" id="user_id" value="<?php echo $data->id; ?>" />
</form>


<hr/>

<form class="form-horizontal">

  <div class="form-group">
    <label class="col-sm-3 control-label no-padding-right">New PIN</label>
    <div class="col-xs-12 col-sm-3">
			<span class="input-icon input-icon-right width-100">
        <input type="password" name="skey" id="skey" class="width-100" />
				<i class="ace-icon fa fa-key"></i>
			</span>
    </div>
    <div class="help-block col-xs-12 col-sm-reset inline red" id="skey-error"></div>
  </div>

	<div class="form-group">
    <label class="col-sm-3 control-label no-padding-right">Confirm New PIN</label>
    <div class="col-xs-12 col-sm-3">
			<span class="input-icon input-icon-right width-100">
        <input type="password" name="cm-skey" id="cm-skey" class="width-100" />
				<i class="ace-icon fa fa-key"></i>
			</span>
    </div>
    <div class="help-block col-xs-12 col-sm-reset inline red" id="cm-skey-error"></div>
  </div>

	<div class="divider-hidden">

	</div>
  <div class="form-group">
    <label class="col-sm-3 control-label no-padding-right"></label>
    <div class="col-xs-12 col-sm-3">
      <p class="pull-right">
        <button type="button" class="btn btn-sm btn-success" onclick="change_skey()"><i class="fa fa-save"></i> เปลี่ยนรหัสลับ</button>
      </p>
    </div>
    <div class="help-block col-xs-12 col-sm-reset inline">
      &nbsp;
    </div>
  </div>
	<input type="hidden" name="uid" id="uid" value="<?php echo $data->uid; ?>" />
</form>

<script src="<?php echo base_url(); ?>scripts/users/user_pwd.js?v=<?php echo date('Ymd'); ?>"></script>
<?php $this->load->view('include/footer'); ?>
