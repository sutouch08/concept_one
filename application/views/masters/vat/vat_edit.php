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
<?php if(!empty($data)) : ?>
<form class="form-horizontal margin-top-30" id="addForm" method="post">

	<div class="form-group">
    <label class="col-sm-3 control-label no-padding-right">รหัส</label>
    <div class="col-xs-12 col-sm-3">
			<input type="text" name="code" id="code" class="width-100 code" maxlength="20" value="<?php echo $data->code; ?>" onkeyup="validCode(this)" disabled />
    </div>
    <div class="help-block col-xs-12 col-sm-reset inline red" id="code-error"></div>
  </div>

  <div class="form-group">
    <label class="col-sm-3 control-label no-padding-right">ชื่อ</label>
    <div class="col-xs-12 col-sm-3">
			<input type="text" name="name" id="name" class="width-100" maxlength="50" value="<?php echo $data->name; ?>"  />
    </div>
    <div class="help-block col-xs-12 col-sm-reset inline red" id="name-error"></div>
  </div>

	<div class="form-group">
    <label class="col-sm-3 control-label no-padding-right">อัตราภาษี(%)</label>
    <div class="col-xs-12 col-sm-3">
			<input type="number" name="rate" id="rate" class="form-control input-sm text-right" maxlength="5" value="<?php echo $data->rate; ?>" />
    </div>
    <div class="help-block col-xs-12 col-sm-reset inline red" id="rate-error"></div>
  </div>

	<?php
		$on = $data->active == 1 ? 'btn-primary' : '';
		$off = $data->active == 0 ? 'btn-danger' : '';
	?>

	<div class="form-group">
    <label class="col-sm-3 control-label no-padding-right">สถานะ</label>
    <div class="col-xs-12 col-sm-3">
			<div class="btn-group input-medium">
				<button type="button" class="btn btn-sm width-50 <?php echo $on; ?>" id="btn-on" onclick="toggleActive(1)">ใช้งาน</button>
				<button type="button" class="btn btn-sm width-50 <?php echo $off; ?>" id="btn-off" onclick="toggleActive(0)">ไม่ใช้งาน</button>
				<input type="hidden" id="active" name="active" value="<?php echo $data->active; ?>">
			</div>
    </div>
  </div>

	<div class="divider-hidden">

	</div>
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
</form>

<?php else : ?>
	<?php $this->load->view('page_error'); ?>
<?php endif; ?>
<script src="<?php echo base_url(); ?>scripts/masters/vat.js?v=<?php echo date('YmdH'); ?>"></script>

<?php $this->load->view('include/footer'); ?>
