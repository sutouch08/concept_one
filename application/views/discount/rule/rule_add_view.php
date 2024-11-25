<?php $this->load->view('include/header'); ?>
<div class="row top-row">
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-8 padding-5">
    <h4 class="title"><?php echo $this->title; ?></h4>
  </div>
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-4 padding-5">
    <p class="pull-right top-p">
      <button type="button" class="btn btn-xs btn-warning" onclick="goBack()"><i class="fa fa-arrow-left"></i> กลับ</button>
    </p>
  </div>
</div><!-- End Row -->
<hr class="padding-5"/>
<form id="addForm" method="post" action="<?php echo $this->home; ?>/add">
<div class="row">
  <div class="col-lg-2 col-md-2 col-sm-2 padding-5 hidden-xs">
    <label>เลขที่นโยบาย</label>
    <input type="text" class="form-control input-sm" name="code" id="code" value="" disabled />
  </div>

  <div class="col-lg-8 col-md-8 col-8 col-xs-9 padding-5">
    <label>ชื่อนโยบาย</label>
    <input type="text" class="form-control input-sm" name="name" id="name" value="" required />
  </div>
  <?php if($this->pm->can_add) : ?>
	<div class="col-lg-2 col-md-2 col-sm-2 col-xs-3 padding-5">
    <label class="display-block not-show">buton</label>
    <button type="button" class="btn btn-xs btn-success btn-block" onclick="addNew()">สร้างเงื่อนไข</button>
  </div>
  <?php endif; ?>
</div>
</form>

<script src="<?php echo base_url(); ?>scripts/discount/rule/rule.js"></script>
<script src="<?php echo base_url(); ?>scripts/discount/rule/rule_add.js"></script>


<?php $this->load->view('include/footer'); ?>
