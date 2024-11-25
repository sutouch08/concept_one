<?php $this->load->view('include/header'); ?>
<?php
	$pm = get_permission('APACMV', $this->_user->uid, $this->_user->id_profile);
	$canAccept = FALSE;
	$accept_user = FALSE;

	if( ! empty($pm))
	{
		$canAccept = (($pm->can_add + $pm->can_edit + $pm->can_delete + $pm->can_approve) > 0  OR $this->_SuperAdmin) ? TRUE : FALSE;
	}

	if( ! empty($accept_list))
	{
		foreach($accept_list as $au)
		{
			if($au->uname == $this->_user->uname && $au->is_accept == 0)
			{
				$accept_user = TRUE;
			}
		}
	}
	?>
<div class="row">
	<div class="col-lg-3 col-md-3 col-sm-3 padding-5 hidden-xs">
    <h3 class="title"><?php echo $this->title; ?></h3>
  </div>
	<div class="col-xs-12 padding-5 visible-xs">
		<h3 class="title-xs"><?php echo $this->title; ?></h3>
	</div>
  <div class="col-lg-9 col-md-9 col-sm-9 col-xs-12 padding-5">
  	<p class="pull-right top-p">
			<button type="button" class="btn btn-sm btn-warning btn-top" onclick="goBack()"><i class="fa fa-arrow-left"></i> กลับ</button>
			<?php if($doc->status == 4 && ($accept_user OR $canAccept)) : ?>
				<button type="button" class="btn btn-sm btn-success btn-top" onclick="accept()"><i class="fa fa-check-circle"></i> ยืนยันการรับสินค้า</button>
			<?php endif; ?>
	    <?php if($doc->status == 1) : ?>
	      <button type="button" class="btn btn-sm btn-info btn-top" onclick="doExport()"><i class="fa fa-send"></i> ส่งข้อมูลไป SAP</button>
	    <?php endif; ?>
			<button type="button" class="btn btn-sm btn-primary btn-top" onclick="printMove()"><i class="fa fa-print"></i> พิมพ์</button>
    </p>
  </div>
</div><!-- End Row -->
<input type="hidden" id="move_code" name="move_code" value="<?php echo $doc->code; ?>" />
<input type="hidden" id="can-accept" name="can_accept" value="<?php echo $canAccept ? 1 : 0; ?>" />
<hr/>
<?php
	$this->load->view('move/move_view_header');
	$this->load->view('move/move_view_detail');
	$this->load->view('accept_modal');
?>

<script src="<?php echo base_url(); ?>scripts/move/move.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/move/move_add.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/move/move_control.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/move/move_detail.js?v=<?php echo date('Ymd'); ?>"></script>

<?php $this->load->view('include/footer'); ?>
