<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-lg-4 col-md-4 col-sm-4 hidden-xs padding-5">
    <h3 class="title">
      <?php echo $this->title; ?>
    </h3>
  </div>
	<div class="col-xs-12 visible-xs">
		<h3 class="title-xs"><?php echo $this->title; ?></h3>
	</div>
  <div class="col-lg-8 col-md-8 col-sm-8 col-xs-12 padding-5">
    	<p class="pull-right top-p">
				<button type="button" class="btn btn-xs btn-warning top-btn" onclick="goBack()"><i class="fa fa-arrow-left"></i> กลับ</button>
		    <?php if($doc->status == 1) : ?>
					<button type="button" class="btn btn-xs btn-danger top-btn" onclick="unSave()"><i class="fa fa-exclamation-triangle"></i> ยกเลิกการบันทึก</button>
		      <button type="button" class="btn btn-xs btn-info top-btn" onclick="doExport()"><i class="fa fa-send"></i> ส่งข้อมูลไป SAP</button>
		    <?php endif; ?>
				<?php if($doc->status == 0 && $doc->must_approve == 1 && $doc->is_approve == 0 && ($this->pm->can_approve OR $this->_SuperAdmin)) : ?>
					<button type="button" class="btn btn-xs btn-success top-btn" onclick="doApprove()"><i class="fa fa-check-circle"></i> อนุมัติ</button>
					<button type="button" class="btn btn-xs btn-danger top-btn" onclick="doReject()"><i class="fa fa-times-circle"></i> ไม่อนุมัติ</button>
				<?php endif; ?>
				<button type="button" class="btn btn-xs btn-primary top-btn" onclick="printTransfer()"><i class="fa fa-print"></i> ใบโอน</button>				
      </p>
    </div>
</div><!-- End Row -->
<input type="hidden" id="transfer_code" name="transfer_code" value="<?php echo $doc->code; ?>" />
<input type="hidden" id="can-accept" name="can_accept" value="0" />
<hr/>
<?php
	if($doc->is_expire == 1 OR $doc->status == 2)
	{
		if($doc->status == 2)
		{
			$this->load->view('cancle_watermark');
		}
		else
		{
			$this->load->view('expire_watermark');
		}
	}
	else
	{
		if($doc->status == 3)
		{
			$this->load->view('on_process_watermark');
		}

		if($doc->status == 0 && $doc->is_approve == 3)
		{
			$this->load->view('reject_watermark');
		}

		if($doc->status == 4)
		{
			$this->load->view('accept_watermark');
		}
	}

	$this->load->view('transfer/transfer_view_header');
	$this->load->view('transfer/transfer_view_detail');
	// $this->load->view('accept_modal');
?>

<script src="<?php echo base_url(); ?>scripts/transfer/transfer.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/transfer/transfer_add.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/transfer/transfer_control.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/transfer/transfer_detail.js?v=<?php echo date('Ymd'); ?>"></script>

<?php $this->load->view('include/footer'); ?>
