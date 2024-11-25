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
<hr class="padding-5"/>

<form class="form-horizontal margin-top-30">
	<div class="form-group">
    <label class="col-lg-4-harf col-md-4 col-sm-3 col-xs-12 control-label no-padding-right">รหัส</label>
    <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
			<input type="text" name="code" id="code" maxlength="20" class="form-control input-sm" value="" onkeyup="validCode(this)" required autofocus />
    </div>
  </div>

  <div class="form-group">
    <label class="col-lg-4-harf col-md-4 col-sm-3 col-xs-12 control-label no-padding-right">ชื่อ</label>
    <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
			<input type="text" name="name" id="name" maxlength="100" class="form-control input-sm" value="" required />
    </div>
  </div>

	<div class="form-group">
 	 <label class="col-lg-4-harf col-md-4 col-sm-3 col-xs-12 control-label no-padding-right">สถานะ</label>
 	 <div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-12">
		 <select class="form-control input-sm" id="active">
			 <option value="1">Active</option>
			 <option value="0">Inactive</option>
		 </select>
 	 </div>
  </div>

<?php if($this->pm->can_add) : ?>
	<div class="form-group">
    <label class="col-sm-3 control-label no-padding-right hidden-xs"></label>
    <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
			<button type="button" class="btn btn-sm btn-success pull-right" id="btn-save" onclick="save()"><i class="fa fa-save"></i> Save</button>
    </div>
  </div>
<?php endif; ?>

</form>

<script src="<?php echo base_url(); ?>scripts/masters/shop_pc.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/code_validate.js"></script>
<?php $this->load->view('include/footer'); ?>
