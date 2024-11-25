<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5">
    <h4 class="title"><?php echo $this->title; ?></h4>
  </div>
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5">
		<p class="pull-right top-p">
			<button type="button" class="btn btn-sm btn-warning" onclick="goBack()"><i class="fa fa-arrow-left"></i>&nbsp; กลับ</button>
		</p>
	</div>
</div><!-- End Row -->
<hr class="margin-bottom-30 padding-5"/>

<div class="form-horizontal">
	<div class="form-group">
		<label class="col-sm-3 col-xs-12 control-label no-padding-right">ธนาคาร</label>
		<div class="col-xs-12 col-sm-3">
			<select class="form-control input-sm e" id="bank-code">
				<option value="">เลือกธนาคาร</option>
				<?php echo select_bank(); ?>
			</select>
		</div>
		<div class="help-block col-xs-12 col-sm-reset inline red" id="bank-code-error"></div>
	</div>

	<div class="form-group">
		<label class="col-sm-3 col-xs-12 control-label no-padding-right">ชื่อบัญชี</label>
		<div class="col-xs-12 col-sm-3">
			<input type="text" id="acc-name" class="form-control input-sm e" autofocus />
		</div>
		<div class="help-block col-xs-12 col-sm-reset inline red" id="acc-name-error"></div>
	</div>

	<div class="form-group">
		<label class="col-sm-3 col-xs-12 control-label no-padding-right">เลขที่บัญชี</label>
		<div class="col-xs-12 col-sm-3">
			<input type="text" class="form-control input-sm e" id="acc-no" placeholder="000-0-00000-0"/>
		</div>
		<div class="help-block col-xs-12 col-sm-reset inline red" id="acc-no-error"></div>
	</div>

	<div class="form-group">
		<label class="col-sm-3 col-xs-12 control-label no-padding-right">สาขา</label>
		<div class="col-xs-12 col-sm-3">
			<input type="text" id="branch" class="form-control input-sm e" />
		</div>
		<div class="help-block col-xs-12 col-sm-reset inline red" id="branch-error"></div>
	</div>

	<div class="form-group">
		<label class="col-sm-3 col-xs-12 control-label no-padding-right">รหัสผังบัญชี SAP</label>
		<div class="col-xs-12 col-sm-3">
			<input type="text" id="sap-code" class="form-control input-sm e" />
		</div>
		<div class="help-block col-xs-12 col-sm-reset inline red" id="sap-code-error"></div>
	</div>

	<div class="form-horizontal">
		<div class="form-group">
			<label class="col-lg-3 col-md-3 col-sm-3 col-xs-3 control-label no-padding-right">Active</label>
			<div class="col-lg-9 col-md-9 col-sm-9 col-xs-9" style="padding-right:10px; padding-top:7px;">
				<label>
					<input type="checkbox" class="ace input-lg" id="active" value="1" checked />
					<span class="lbl"></span>
				</label>
			</div>
		</div>
	</div>

	<?php if($this->pm->can_add) : ?>
		<div class="divider-hidden"></div>
		<div class="form-group">
			<label class="col-sm-3 control-label no-padding-right"></label>
			<div class="col-xs-12 col-sm-3">
				<p class="pull-right">
					<button type="button" class="btn btn-sm btn-success" onclick="add()"><i class="fa fa-save"></i> Add</button>
				</p>
			</div>
			<div class="help-block col-xs-12 col-sm-reset inline">
				&nbsp;
			</div>
		</div>
	<?php endif; ?>
</div>

<script src="<?php echo base_url(); ?>assets/js/jquery.maskedinput.js"></script>
<script src="<?php echo base_url(); ?>scripts/masters/bank_account.js?v=<?php echo date('Ymd'); ?>"></script>


<script>
	$('#acc-no').mask('999-9-99999-9');
</script>

<?php $this->load->view('include/footer'); ?>
