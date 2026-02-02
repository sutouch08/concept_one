<?php $this->load->view('include/header'); ?>

<div class="row">
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 padding-5">
    <h3 class="title"><?php echo $this->title; ?></h3>
  </div>
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 padding-5">
		<p class="pull-right top-p">
			<button type="button" class="btn btn-sm btn-warning" onclick="goBack()"><i class="fa fa-arrow-left"></i> Back</button>
		</p>
	</div>
</div><!-- End Row -->
<hr/>
<div class="row">
  <div class="form-horizontal">
    <div class="form-group margin-top-30">
      <label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label no-padding-right">รหัส</label>
      <div class="col-lg-2 col-md-3 col-sm-3 col-xs-12">
        <input type="text" class="width-100 e" id="code" maxlength="50" value="<?php echo $ds->code; ?>" disabled/>
				<input type="hidden" id="id" value="<?php echo $ds->id; ?>" />
      </div>
      <div class="help-block col-xs-12 col-sm-reset inline red" id="code-error"></div>
    </div>

    <div class="form-group">
      <label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label no-padding-right">ชื่อ</label>
      <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
        <input type="text" class="width-100 e" id="name" maxlength="150" value="<?php echo $ds->name; ?>"/>
      </div>
      <div class="help-block col-xs-12 col-sm-reset inline red" id="name-error"></div>
    </div>

		<div class="form-group">
			<label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label no-padding-right">คลัง</label>
			<div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
				<select class="width-100 e" id="warehouse">
					<option value="">เลือกคลัง</option>
					<?php echo select_warehouse($ds->warehouse_code); ?>
				</select>
			</div>
			<div class="help-block col-xs-12 col-sm-reset inline red" id="warehouse-error"></div>
		</div>

		<div class="form-group">
			<label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label no-padding-right">เจ้าของโซน</label>
			<div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
				<select class="width-100 e" id="user_id">
					<option value="">ไม่มีเจ้าของโซน</option>
					<?php echo select_user_id($ds->user_id); ?>
				</select>
			</div>
			<div class="help-block col-xs-12 col-sm-reset inline red" id="warehouse-error"></div>
		</div>

    <div class="form-group">
  		<label class="col-lg-3 col-md-3 col-sm-3 col-xs-2 control-label no-padding-right">Active</label>
  		<div class="col-lg-4 col-md-4 col-sm-4 col-xs-10">
  			<label style="padding-top:5px;">
  				<input name="active" class="ace ace-switch ace-switch-7" type="checkbox" id="active" value="1" <?php echo is_checked($ds->active, '1'); ?> disabled />
  				<span class="lbl"></span>
  			</label>
  		</div>
  	</div>

    <div class="form-group">
  		<label class="col-lg-3 col-md-3 col-sm-3 col-xs-2 control-label no-padding-right">Pickface</label>
  		<div class="col-lg-4 col-md-4 col-sm-4 col-xs-10">
  			<label style="padding-top:5px;">
  				<input name="is_pickface" class="ace ace-switch ace-switch-7" type="checkbox" id="is-pickface" value="1" <?php echo is_checked($ds->is_pickface, '1'); ?> />
  				<span class="lbl"></span>
  			</label>
  		</div>
  	</div>		

    <div class="divider-hidden"></div>
  	<div class="divider-hidden"></div>

  	<?php if($this->pm->can_edit) : ?>
  		<div class="form-group">
  	    <label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label no-padding-right"></label>
  	    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12 text-right">
  				<button type="button" class="btn btn-sm btn-success btn-100" onclick="update()">Update</button>
  	    </div>
  	  </div>
  	<?php endif; ?>
  </div><!-- form horizontal -->
</div>

<hr class="margin-top-10 margin-bottom-15">
<div class="row">
	<div class="form-horizontal">
		<div class="form-group">
  		<label class="col-lg-3 col-md-3 col-sm-1-harf col-xs-12 control-label no-padding-right">ลูกค้า</label>
  		<div class="col-lg-3 col-md-3 col-sm-4 col-xs-8">
				<input type="text" class="form-control input-sm" id="search-box" placeholder="ค้นหาลูกค้า" autofocus>
  		</div>
			<div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-4 padding-5">
				<button type="button" class="btn btn-xs btn-primary btn-block" onclick="addCustomer()">
					<i class="fa fa-plus"></i> เพิ่มลูกค้า
				</button>
			</div>
  	</div>

		<div class="form-group">
  		<label class="col-lg- col-md-3 col-sm-1-harf col-xs-12 control-label no-padding-right">&nbsp;</label>
  		<div class="col-lg-6 col-md-6 col-sm-9 col-xs-12">
				<table class="table table-striped border-1">
					<thead>
						<tr class="font-size-11">
							<th class="fix-width-40 text-center">No.</th>
							<th class="fix-width-100">รหัสลูกค้า</th>
							<th class="fix-width-250">ชิ้อลูกค้า</th>
							<th class="fix-width-80"></th>
						</tr>
					</thead>
					<tbody id="cust-table">
		<?php if( ! empty($customers)) : ?>
			<?php $no = 1; ?>
			<?php foreach($customers as $rs) : ?>
						<tr class="font-size-11" id="row-<?php echo $rs->id; ?>">
							<td class="middle text-center"><?php echo $no; ?></td>
							<td class="middle"><?php echo $rs->customer_code; ?></td>
							<td class="middle"><?php echo $rs->customer_name; ?></td>
							<td class="middle text-right">
					<?php if($this->pm->can_edit) : ?>
								<button type="button" class="btn btn-minier btn-danger" onclick="deleteCustomer(<?php echo $rs->id; ?>, '<?php echo $rs->customer_code; ?>')">
									<i class="fa fa-trash"></i>
								</button>
					<?php endif; ?>
							</td>
						</tr>
				<?php $no++; ?>
			<?php endforeach; ?>
		<?php else : ?>
						<tr class="font-size-11">
							<td colspan="4" class="text-center">--- No customer ---</td>
						</tr>
		<?php endif; ?>
					</tbody>
				</table>
  		</div>
  	</div>
	</div>
</div>


<?php if( ! empty($ds->role == 8)) : ?>
<hr class="margin-top-10 margin-bottom-15">
<div class="row">
	<div class="form-horizontal">
		<div class="form-group">
  		<label class="col-lg-3 col-md-3 col-sm-1-harf col-xs-12 control-label no-padding-right">พนักงาน</label>
  		<div class="col-lg-3 col-md-3 col-sm-4 col-xs-8">
				<select class="width-100" id="empID">
					<option value="">เลือกพนักงาน</option>
					<?php echo select_active_employee(); ?>
				</select>
  		</div>
			<div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-4 padding-5">
				<button type="button" class="btn btn-xs btn-purple btn-block" onclick="addEmployee()">
					<i class="fa fa-plus"></i> เพิ่มพนักงาน
				</button>
			</div>
  	</div>

		<div class="form-group">
  		<label class="col-lg- col-md-3 col-sm-1-harf col-xs-12 control-label no-padding-right">&nbsp;</label>
  		<div class="col-lg-6 col-md-6 col-sm-9 col-xs-12">
				<table class="table table-striped border-1">
					<thead>
						<tr class="font-size-11">
							<th class="fix-width-40 text-center">No.</th>
							<th class="fix-width-200">พนักงาน</th>
							<th class="fix-width-80"></th>
						</tr>
					</thead>
					<tbody id="cust-table">
		<?php if( ! empty($employees)) : ?>
			<?php $no = 1; ?>
			<?php foreach($employees as $rs) : ?>
						<tr class="font-size-11" id="emp-<?php echo $rs->id; ?>">
							<td class="middle text-center"><?php echo $no; ?></td>
							<td class="middle"><?php echo $rs->empName; ?></td>
							<td class="middle text-right">
					<?php if($this->pm->can_edit) : ?>
								<button type="button" class="btn btn-minier btn-danger" onclick="deleteEmployee(<?php echo $rs->id; ?>, '<?php echo $rs->empName; ?>')">
									<i class="fa fa-trash"></i>
								</button>
					<?php endif; ?>
							</td>
						</tr>
				<?php $no++; ?>
			<?php endforeach; ?>
		<?php else : ?>
						<tr class="font-size-11">
							<td colspan="4" class="text-center">--- No customer ---</td>
						</tr>
		<?php endif; ?>
					</tbody>
				</table>
  		</div>
  	</div>
	</div><!-- form -->
</div>
<?php endif; ?>


<input type="hidden" id="customer_code" value="" >
<input type="hidden" id="zone_code" value="<?php echo $ds->code; ?>">
<script src="<?php echo base_url(); ?>scripts/masters/zone.js?v=<?php echo date('Ymd'); ?>"></script>
<script>
	$('#warehouse').select2();
	$('#user_id').select2();
	$('#empID').select2();
</script>

<?php $this->load->view('include/footer'); ?>
