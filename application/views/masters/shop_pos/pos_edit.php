<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-sm-6 col-xs-6 padding-5">
    <h3 class="title"><i class="fa fa-cubes"></i> <?php echo $this->title; ?></h3>
  </div>
	<div class="col-sm-6 col-xs-6 padding-5">
		<p class="pull-right top-p">
			<button type="button" class="btn btn-sm btn-warning" onclick="goBack()"><i class="fa fa-arrow-left"></i> Back</button>
<?php if($this->pm->can_approve) : ?>
	<?php if( ! empty($deviceId)) : ?>
			<button type="button" class="btn btn-sm btn-danger" onclick="unRegister(<?php echo $id; ?>, '<?php echo $name; ?>')">Unregister</button>
	<?php else : ?>
			<button type="button" class="btn btn-sm btn-info" onclick="warningRegister(<?php echo $id; ?>, '<?php echo $name; ?>')">Register</button>
	<?php endif; ?>
<?php endif; ?>
		</p>
	</div>
</div><!-- End Row -->
<hr class="padding-5 padding-top-20"/>

<form class="form-horizontal">

	<div class="form-group">
    <label class="col-lg-4-harf col-md-4 col-sm-4 col-xs-3 control-label no-padding-right">รหัส</label>
    <div class="col-xs-12 col-sm-5 col-md-3 padding-5">
			<input type="text" class="form-control input-sm" value="<?php echo $code; ?>" disabled />
			<input type="hidden" name="code" id="code" value="<?php echo $code; ?>" />
    </div>
  </div>

  <div class="form-group">
    <label class="col-lg-4-harf col-md-4 col-sm-4 col-xs-3 control-label no-padding-right">ชื่อ</label>
    <div class="col-xs-12 col-sm-5 col-md-3 padding-5">
			<input type="text" name="name" id="name" maxlength="200" class="form-control input-sm" value="<?php echo $name; ?>" />
			<input type="hidden" name="old_name" id="old_name" value="<?php echo $name; ?>" />
    </div>
  </div>

	<div class="form-group">
    <label class="col-lg-4-harf col-md-4 col-sm-4 col-xs-3 control-label no-padding-right">POS NO</label>
    <div class="col-xs-12 col-sm-5 col-md-3 padding-5">
			<input type="text" name="pos_no" id="pos_no" maxlength="32" class="form-control input-sm" value="<?php echo $pos_no; ?>"  />
    </div>
  </div>

	<div class="form-group">
    <label class="col-lg-4-harf col-md-4 col-sm-4 col-xs-3 control-label no-padding-right">Bill Prefix</label>
    <div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-3 padding-5">
			<input type="text" name="prefix" id="prefix" maxlength="4" class="form-control input-sm text-center" value="<?php echo $prefix; ?>" />
    </div>
		<div class="col-lg-2 col-md-2-harf col-sm-2-harf col-xs-3 padding-5">
			<div class="input-group width-100">
				<span class="input-group-addon">Running</span>
				<select class="form-control input-sm" name="running" id="running">
					<option value="3" <?php echo is_selected('3', $running); ?>>&nbsp;&nbsp; 3 &nbsp;&nbsp;&nbsp;หลัก</option>
					<option value="4" <?php echo is_selected('4', $running); ?>>&nbsp;&nbsp; 4 &nbsp;&nbsp;&nbsp;หลัก</option>
					<option value="5" <?php echo is_selected('5', $running); ?>>&nbsp;&nbsp; 5 &nbsp;&nbsp;&nbsp;หลัก</option>
					<option value="6" <?php echo is_selected('6', $running); ?>>&nbsp;&nbsp; 6 &nbsp;&nbsp;&nbsp;หลัก</option>
				</select>
			</div>
    </div>
  </div>

	<div class="form-group">
    <label class="col-lg-4-harf col-md-4 col-sm-4 col-xs-3 control-label no-padding-right">Return Prefix</label>
    <div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-3 padding-5">
			<input type="text" id="return-prefix" maxlength="4" class="form-control input-sm text-center" value="<?php echo $return_prefix; ?>" />
    </div>
		<div class="col-lg-2 col-md-2-harf col-sm-2-harf col-xs-3 padding-5">
			<div class="input-group width-100">
				<span class="input-group-addon">Running</span>
				<select class="form-control input-sm" id="return-running">
					<option value="3" <?php echo is_selected('3', $return_running); ?>>&nbsp;&nbsp; 3 &nbsp;&nbsp;&nbsp;หลัก</option>
					<option value="4" <?php echo is_selected('4', $return_running); ?>>&nbsp;&nbsp; 4 &nbsp;&nbsp;&nbsp;หลัก</option>
					<option value="5" <?php echo is_selected('5', $return_running); ?>>&nbsp;&nbsp; 5 &nbsp;&nbsp;&nbsp;หลัก</option>
					<option value="6" <?php echo is_selected('6', $return_running); ?>>&nbsp;&nbsp; 6 &nbsp;&nbsp;&nbsp;หลัก</option>
				</select>
			</div>
    </div>
  </div>


	<div class="form-group">
    <label class="col-lg-4-harf col-md-4 col-sm-4 col-xs-3 control-label no-padding-right">จุดขาย</label>
    <div class="col-xs-12 col-sm-5 col-md-3 padding-5">
			<select class="form-control input-sm" name="shop" id="shop">
				<option value="">เลือก</option>
				<?php echo select_shop_id($shop_id); //--- shop_helper ?>
			</select>
    </div>
  </div>

	<div class="form-group">
 	 <label class="col-lg-4-harf col-md-4 col-sm-4 col-xs-3 control-label no-padding-right">สถานะ</label>
 	 <div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
		 <select class="form-control input-sm" id="active">
			 <option value="1" <?php echo is_selected('1', $active); ?>>Active</option>
			 <option value="0" <?php echo is_selected('0', $active); ?>>Inactive</option>
		 </select>
 	 </div>
  </div>

<?php if($this->pm->can_edit) : ?>
	<div class="divider-hidden"></div>
	<div class="divider-hidden"></div>
	<div class="divider-hidden"></div>
	<div class="form-group">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-center">
			<button type="button" class="btn btn-sm btn-success btn-100" id="btn-save" onclick="update()">Update</button>
    </div>
  </div>
<?php endif; ?>
</form>

<div class="modal fade" id="pos-data-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog" style="width:500px; max-width:90vw;">
        <div class="modal-content">
            <div class="modal-header" style="border-bottom:solid 1px #f4f4f4;">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
								<h3 class="modal-title" >POS Registration</h3>
            </div>
            <div class="modal-body">
							<div class="row">
								<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" id="pos-data-table">

								</div>
							</div>

							<input type="hidden" id="pos-data" value="" />
            </div>
						<div class="modal-footer">
							<button type="button" class="btn btn-sm btn-default btn-100" onclick="closeModal('pos-data-modal')">Cancel</button>
							<button type="button" class="btn btn-sm btn-primary btn-100" onclick="doRegister()">Register</button>
						</div>
        </div>
    </div>
</div>


<script id="pos-data-template" type="text/x-handlebarsTemplate">
	<table class="table table-bordered">
		<tr class="hide"><td class="width-40">&nbsp;</td><td class="width-60">&nbsp;</td></tr>
		<tr><td>รหัส</td><td>{{code}}</td></tr>
		<tr><td>ชื่อเครื่อง</td><td>{{name}}</td></tr>
		<tr><td>POS Number</td><td>{{pos_no}}</td></tr>
		<tr><td>จุดขาย</td><td>{{shop_name}}</td></tr>
		<tr><td>Prefix</td><td>{{prefix}}</td></tr>
		<tr><td>Running</td><td>{{running}}</td></tr>
		<tr><td>Register Code</td><td>{{deviceId}}</td></tr>
	</table>
</script>

<script src="<?php echo base_url(); ?>scripts/masters/pos.js?v=<?php echo date('YmdH'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/code_validate.js"></script>
<?php $this->load->view('include/footer'); ?>
