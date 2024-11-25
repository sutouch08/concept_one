<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-lg-6 col-md-6 col-sm-8 col-xs-9 padding-5">
    <h3 class="title"><?php echo $this->title; ?></h3>
  </div>
  <div class="col-lg-6 col-md-6 col-sm-4 col-xs-3 padding-5">
    	<p class="pull-right top-p">
        <button type="button" class="btn btn-sm btn-warning" onclick="goBack()"><i class="fa fa-arrow-left"></i> กลับ</button>
      </p>
    </div>
</div><!-- End Row -->
<hr class="padding-5"/>

<?php $this->load->view('discount/policy/policy_view_header'); ?>
<?php $this->load->view('discount/policy/policy_rule_list_view', array('view_detail' => 'Y')); ?>


<script src="<?php echo base_url(); ?>scripts/discount/policy/policy.js"></script>
<script src="<?php echo base_url(); ?>scripts/discount/policy/policy_list.js"></script>
<script src="<?php echo base_url(); ?>scripts/discount/policy/policy_add.js"></script>

<?php $this->load->view('include/footer'); ?>
