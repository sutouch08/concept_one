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
<hr class=""/>
<form class="form-horizontal" id="editForm" method="post" action="<?php echo $this->home."/update/".$id; ?>">
	<input type="hidden" name="id" id="id" value="<?php echo $id; ?>" />
	<div class="form-group">
    <label class="col-sm-3 control-label no-padding-right">รหัส</label>
    <div class="col-xs-12 col-sm-3">
			<input type="text" name="code" id="code" class="width-100" maxlength="9" value="<?php echo $code; ?>" onkeyup="validCode(this)" autofocus required />
    </div>
  </div>

  <div class="form-group">
    <label class="col-sm-3 control-label no-padding-right">ชื่อ</label>
    <div class="col-xs-12 col-sm-3">
			<input type="text" name="name" id="name" class="width-100" maxlength="250" value="<?php echo $name; ?>" required />
    </div>
  </div>

  <div class="form-group">
    <label class="col-sm-3 control-label no-padding-right">ที่อยู่ 1</label>
    <div class="col-xs-12 col-sm-3">
			<input type="text" name="address1" id="address1" class="width-100" value="<?php echo $address1; ?>" />
    </div>
  </div>

  <div class="form-group">
    <label class="col-sm-3 control-label no-padding-right">ที่อยู่ 2</label>
    <div class="col-xs-12 col-sm-3">
			<input type="text" name="address2" id="address2" class="width-100" value="<?php echo $address2; ?>" />
    </div>
  </div>

  <div class="form-group">
    <label class="col-sm-3 control-label no-padding-right">เบอร์โทร</label>
    <div class="col-xs-12 col-sm-3">
			<input type="text" name="phone" id="phone" class="width-100" value="<?php echo $phone; ?>" />
    </div>
  </div>

  <div class="form-group">
    <label class="col-sm-3 control-label no-padding-right">เวลาทำการ</label>
    <div class="col-xs-12 col-sm-3">
      <select class="form-control input-sm input-small" name="open" id="open" style="display:inline-block;">
      <?php echo selectTime($open); ?>
      </select>
      -
      <select class="form-control input-sm input-small" name="close" id="close" style="display:inline-block;">
      <?php echo selectTime($close); ?>
      </select>
    </div>
  </div>

  <div class="form-group">
    <label class="col-sm-3 control-label no-padding-right">เงื่อนไข</label>
    <div class="col-xs-12 col-sm-3">
			<select class="form-control input-sm" name="type" id="type">
        <option value="ปลายทาง" <?php echo is_selected('ปลายทาง', $type); ?>>เก็บเงินปลายทาง</option>
        <option value="ต้นทาง" <?php echo is_selected('ต้นทาง', $type); ?>>เก็บเงินต้นทาง</option>
      </select>
    </div>
  </div>

	<div class="form-group">
    <label class="col-sm-3 control-label no-padding-right"></label>
    <div class="col-xs-12 col-sm-3">
			<label>
				<input type="checkbox" name="in_list" id="in_list" class="ace" value="1" <?php echo is_checked($show_in_list, '1'); ?> />
				<span class="lbl">  แสดงในตัวเลือก</span>
			</label>
    </div>
  </div>

	<div class="form-group">
    <label class="col-sm-3 control-label no-padding-right"></label>
    <div class="col-xs-12 col-sm-3">
			<label>
				<input type="checkbox" name="force_tracking" id="force_tracking" class="ace" value="1" onchange="toggleAutoGen()" <?php echo is_checked($force_tracking, '1'); ?>/>
				<span class="lbl">  บังคับใส่ Tracking</span>
			</label>
    </div>
  </div>

	<div class="form-group <?php echo ($auto_gen == 1 ? "" : 'hide'); ?>" id="gen_potion">
    <label class="col-sm-3 control-label no-padding-right"></label>
    <div class="col-xs-12 col-sm-3">
			<label>
				<input type="checkbox" name="auto_gen" id="auto_gen" class="ace" value="1" onchange="togglePrefix()" <?php echo is_checked($auto_gen, '1'); ?>/>
				<span class="lbl">  Auto Generate Tracking No</span>
			</label>
    </div>
  </div>

	<div class="form-group <?php echo ($auto_gen == 1 ? "" : 'hide'); ?>" id="prefix">
    <label class="col-sm-3 control-label no-padding-right">Tracking Prefix</label>
    <div class="col-xs-12 col-sm-3">
			<input type="text" name="tracking_prefix" id="tracking_prefix" maxlength="10" class="form-control input-medium" value="<?php echo $prefix; ?>"/>
    </div>
  </div>

	<div class="divider-hidden">

	</div>
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
</form>

<script src="<?php echo base_url(); ?>scripts/masters/sender.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/code_validate.js?v=<?php echo date('Ymd'); ?>"></script>
<?php $this->load->view('include/footer'); ?>
