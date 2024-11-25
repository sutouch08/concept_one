<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-lg-6 col-md-6 col-sm-6 padding-5 hidden-xs">
    <h3 class="title"><?php echo $this->title; ?></h3>
  </div>
	<div class="col-xs-12 padding-5 visible-xs">
    <h3 class="title-xs"><?php echo $this->title; ?></h3>
  </div>
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5">
		<p class="pull-right">
			<button type="button" class="btn btn-sm btn-warning" onclick="goBack()"><i class="fa fa-arrow-left"></i> Back</button>
		</p>
	</div>
</div><!-- End Row -->
<hr class="title-block"/>
<form class="form-horizontal" id="addForm" method="post" action="<?php echo $this->home."/update"; ?>">
	<div class="form-group">
    <label class="col-lg-4-harf col-md-4 col-sm-4 col-xs-12 control-label no-padding-right">รหัส</label>
    <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
      <input type="text" name="code" id="code" class="width-100 code" maxlength="15" value="<?php echo $code; ?>" onkeyup="validCode(this)" disabled  />
    </div>
    <div class="help-block col-xs-12 col-sm-reset inline red" id="code-error"></div>
  </div>

	<div class="form-group">
    <label class="col-lg-4-harf col-md-4 col-sm-4 col-xs-12 control-label no-padding-right">ชื่อ</label>
    <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
			<input type="text" name="name" id="name" class="width-100" maxlength="50" value="<?php echo $name; ?>"  />
    </div>
    <div class="help-block col-xs-12 col-sm-reset inline red" id="name-error"></div>
  </div>

	<div class="form-group">
    <label class="col-lg-4-harf col-md-4 col-sm-4 col-xs-12 control-label no-padding-right">ประเภท</label>
    <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
			<select name="role" id="role" class="form-control input-sm" onchange="toggleBankAccount()">
				<?php echo select_payment_role($role); ?>
			</select>
    </div>
		<div class="help-block col-xs-12 col-sm-reset inline red" id="role-error"></div>
  </div>

	<?php $hide = $role == 2 ? '' : 'hide'; ?>
	<div class="form-group <?php echo $hide; ?>" id="bank-row">
    <label class="col-lg-4-harf col-md-4 col-sm-4 col-xs-12 control-label no-padding-right">บัญชีธนาคาร</label>
    <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
			<select name="account" id="account" class="form-control input-sm" required>
				<option value="">เลือกบัญชีธนาคาร</option>
				<?php echo select_bank_account($account_id); ?>
			</select>
    </div>
		<div class="help-block col-xs-12 col-sm-reset inline red" id="role-error"></div>
  </div>

	<div class="divider-hidden"></div>

  <div class="form-group">
    <label class="col-lg-4-harf col-md-4 col-sm-4 col-xs-12 control-label no-padding-right"></label>
    <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
      <p class="pull-right">
        <button type="button" class="btn btn-sm btn-success btn-100" onclick="update()"><i class="fa fa-save"></i> Update</button>
      </p>
    </div>
  </div>

	<input type="hidden" id="id" value="<?php echo $id; ?>" />
</form>

<script src="<?php echo base_url(); ?>scripts/masters/payment_methods.js?v=<?php echo date('Ymd'); ?>"></script>

<?php $this->load->view('include/footer'); ?>
