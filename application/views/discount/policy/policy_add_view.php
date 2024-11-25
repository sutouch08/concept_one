<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-8 padding-5">
    <h3 class="title"><?php echo $this->title; ?></h3>
  </div>
  <div class="col-lg-6 col-md-6 col-sm-6 col-xs-4 padding-5">
    	<p class="pull-right top-p">
        <button type="button" class="btn btn-sm btn-warning" onclick="goBack()"><i class="fa fa-arrow-left"></i> กลับ</button>
      </p>
    </div>
</div><!-- End Row -->
<hr class="padding-5"/>
<form id="addForm" method="post" action="<?php echo $this->home; ?>/add_policy">
<div class="row">
  <div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-4 padding-5">
    <label>เลขที่นโยบาย</label>
    <input type="text" class="form-control input-sm" name="policy_code" id="policy_code" value="" disabled />
  </div>

  <div class="col-lg-6 col-md-6 col-sm-6 col-xs-8 padding-5">
    <label>ชื่อนโยบาย</label>
    <input type="text" class="form-control input-sm" name="policy_name" id="policy_name" value="" required />
  </div>

  <div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-4 padding-5">
    <label>เริ่มต้น</label>
		  <input type="text" class="form-control input-sm text-center" name="start_date" id="fromDate" value="" required />
  </div>
  <div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-4 padding-5">
    <label>สิ้นสุด</label>
    <input type="text" class="form-control input-sm text-center" name="end_date" id="toDate" value="" required />
  </div>
  <?php if($this->pm->can_add) : ?>
<div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-4 padding-5">
    <label class="display-block not-show">buton</label>
    <button type="button" class="btn btn-xs btn-success btn-block" onclick="addNew()">เพิ่มนโยบาย</button>
  </div>
  <?php endif; ?>
</div>
<hr class="margin-top-15">
</form>

<script src="<?php echo base_url(); ?>scripts/discount/policy/policy.js"></script>
<script src="<?php echo base_url(); ?>scripts/discount/policy/policy_list.js"></script>
<script src="<?php echo base_url(); ?>scripts/discount/policy/policy_add.js"></script>

<?php $this->load->view('include/footer'); ?>
