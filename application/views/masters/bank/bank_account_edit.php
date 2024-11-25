<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-sm-6 col-xs-6 padding-5">
    <h3 class="title">
      <i class="fa fa-credit-card"></i> <?php echo $this->title; ?>
    </h3>
  </div>
	<div class="col-sm-6 col-xs-6 padding-5">
		<p class="pull-right top-p">
			<button type="button" class="btn btn-sm btn-warning" onclick="goBack()"><i class="fa fa-arrow-left"></i>&nbsp; กลับ</button>
		</p>
	</div>
</div><!-- End Row -->
<hr class="margin-bottom-30 padding-5"/>
<?php if(empty($data)) : ?>
	<?php $this->load->view('page_error'); ?>
<?php else : ?>
<form class="form-horizontal">
	<div class="form-group">
    <label class="col-sm-3 col-xs-12 control-label no-padding-right">ธนาคาร</label>
    <div class="col-xs-12 col-sm-3">
        <select class="form-control input-sm" id="bank-code" name="bank_code" required>
					<option value="">เลือกธนาคาร</option>
					<?php echo select_bank($data->bank_code); ?>
  			</select>
    </div>
    <div class="help-block col-xs-12 col-sm-reset inline red" id="bank-code-error"></div>
  </div>



  <div class="form-group">
    <label class="col-sm-3 col-xs-12 control-label no-padding-right">ชื่อบัญชี</label>
    <div class="col-xs-12 col-sm-3">
			<input type="text" name="acc_name" id="acc-name" class="form-control input-sm" value="<?php echo $data->acc_name; ?>" required autofocus/>
    </div>
    <div class="help-block col-xs-12 col-sm-reset inline red" id="acc-name-error"></div>
  </div>

	<div class="form-group">
    <label class="col-sm-3 col-xs-12 control-label no-padding-right">เลขที่บัญชี</label>
    <div class="col-xs-12 col-sm-3">
			<input
				type="text"
				name="acc_no"
				id="acc-no"
				placeholder="000-0-00000-0"
				class="form-control input-sm"
				value="<?php echo $data->acc_no; ?>"
				required />
    </div>
    <div class="help-block col-xs-12 col-sm-reset inline red" id="acc-no-error"></div>
  </div>

	<div class="form-group">
    <label class="col-sm-3 col-xs-12 control-label no-padding-right">สาขา</label>
    <div class="col-xs-12 col-sm-3">
			<input type="text" name="branch" id="branch" class="form-control input-sm" value="<?php echo $data->branch; ?>" required />
    </div>
    <div class="help-block col-xs-12 col-sm-reset inline red" id="branch-error"></div>
  </div>

	<div class="form-group">
		<label class="col-sm-3 col-xs-12 control-label no-padding-right">รหัสผังบัญชี SAP</label>
		<div class="col-xs-12 col-sm-3">
			<input type="text" id="sap-code" class="form-control input-sm e" value="<?php echo $data->sapAcctCode; ?>"/>
		</div>
		<div class="help-block col-xs-12 col-sm-reset inline red" id="sap-code-error"></div>
	</div>

	<div class="form-horizontal">
		<div class="form-group">
			<label class="col-lg-3 col-md-3 col-sm-3 col-xs-3 control-label no-padding-right">Active</label>
			<div class="col-lg-9 col-md-9 col-sm-9 col-xs-9" style="padding-right:10px; padding-top:7px;">
				<label>
					<input type="checkbox" class="ace input-lg" id="active" value="1" <?php echo is_checked($data->active,1); ?> />
					<span class="lbl"></span>
				</label>
			</div>
		</div>
	</div>

	<input type="hidden" name="id" id="id" value="<?php echo $data->id; ?>" />

<?php if($this->pm->can_edit) : ?>
	<div class="divider-hidden"></div>
  <div class="form-group">
    <label class="col-sm-3 control-label no-padding-right"></label>
    <div class="col-xs-12 col-sm-3">
      <p class="pull-right">
        <button type="button" class="btn btn-sm btn-success" onclick="update()"><i class="fa fa-save"></i> Update</button>
      </p>
    </div>
    <div class="help-block col-xs-12 col-sm-reset inline">
      &nbsp;
    </div>
  </div>
<?php endif; ?>
</form>

<?php endif; ?>

<script src="<?php echo base_url(); ?>assets/js/jquery.maskedinput.js"></script>
<script src="<?php echo base_url(); ?>scripts/masters/bank_account.js"></script>


<script>
	$('#acc-no').mask('999-9-99999-9');
</script>

<?php $this->load->view('include/footer'); ?>
