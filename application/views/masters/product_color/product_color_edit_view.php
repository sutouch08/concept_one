<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-sm-6">
    <h3 class="title"><?php echo $this->title; ?></h3>
  </div>
	<div class="col-sm-6">
		<p class="pull-right">
			<button type="button" class="btn btn-sm btn-warning" onclick="goBack()"><i class="fa fa-arrow-left"></i> Back</button>
			<button type="button" class="btn btn-sm btn-info" onclick="export_api()"><i class="fa fa-send"></i> Send To Web</button>
		</p>
	</div>
</div><!-- End Row -->
<hr class="title-block"/>
<form class="form-horizontal" id="addForm" method="post">

	<div class="form-group">
    <label class="col-sm-3 control-label no-padding-right">รหัส</label>
    <div class="col-xs-12 col-sm-3">
      <input type="text" name="code" id="code" class="width-100 req" value="<?php echo $code; ?>" autofocus required />
    </div>
    <div class="help-block col-xs-12 col-sm-reset inline red" id="code-error"></div>
  </div>

  <div class="form-group">
    <label class="col-sm-3 control-label no-padding-right">สี</label>
    <div class="col-xs-12 col-sm-3">
			<input type="text" name="name" id="name" class="width-100 req" value="<?php echo $name; ?>" required />
    </div>
    <div class="help-block col-xs-12 col-sm-reset inline red" id="name-error"></div>
  </div>

	<div class="form-group">
    <label class="col-sm-3 control-label no-padding-right">กลุ่มสี</label>
    <div class="col-xs-12 col-sm-3">
			<select class="form-control input-sm" id="color_group" name="color_group">
				<option value="">เลือกกลุ่มสี</option>
				<?php echo select_color_group($id_group); ?>
			</select>
    </div>
    <div class="help-block col-xs-12 col-sm-reset inline red" id="name-error"></div>
  </div>

	<div class="divider-hidden"></div>
  <div class="form-group">
    <label class="col-sm-3 control-label no-padding-right"></label>
    <div class="col-xs-12 col-sm-3">
      <p class="pull-right">
        <button type="button" class="btn btn-sm btn-success" onclick="update()"><i class="fa fa-save"></i> Save</button>
      </p>
    </div>
    <div class="help-block col-xs-12 col-sm-reset inline">
      &nbsp;
    </div>
  </div>
	<input type="hidden" nam="id" id="id" value="<?php echo $id; ?>" />
	<input type="hidden" name="product_color_code" id="color_code" value="<?php echo $code; ?>" />
	<input type="hidden" name="product_color_name" value="<?php echo $name; ?>" />
</form>

<script src="<?php echo base_url(); ?>scripts/masters/product_color.js?v=<?php echo date('Ymd'); ?>"></script>
<?php $this->load->view('include/footer'); ?>
