<?php $this->load->view('include/header'); ?>
<?php
	$pm = get_permission('APACRN', $this->_user->uid, $this->_user->id_profile);
	$canAccept = NULL;

	if( ! empty($pm))
	{
		$canAccept = (($pm->can_add + $pm->can_edit + $pm->can_delete + $pm->can_approve) > 0  OR $this->_SuperAdmin) ? TRUE : FALSE;
	}
	?>
<div class="row">
	<div class="col-lg-6 col-md-4 col-sm-4 hidden-xs padding-5">
    <h3 class="title"><?php echo $this->title; ?></h3>
  </div>
	<div class="col-xs-12 visible-xs padding-5">
    <h3 class="title-xs"><?php echo $this->title; ?></h3>
  </div>
  <div class="col-lg-6 col-md-8 col-sm-8 col-xs-12 padding-5">
    <p class="pull-right top-p">
			<button type="button" class="btn btn-sm btn-warning top-btn" onclick="goBack()"><i class="fa fa-arrow-left"></i> กลับ</button>
			<?php if($doc->status != 2 && $this->pm->can_delete) : ?>
				<button type="button" class="btn btn-sm btn-danger top-btn" onclick="goDelete('<?php echo $doc->code; ?>')"><i class="fa fa-trash"></i> ยกเลิก</button>
			<?php endif; ?>
			<?php if($doc->status == 1) : ?>
			<button type="button" class="btn btn-sm btn-success top-btn" onclick="doExport()"><i class="fa fa-send"></i> ส่งข้อมูลไป SAP </button>
			<?php endif; ?>
			<?php if($doc->status == 4 && ($doc->owner == $this->_user->uname OR $canAccept)) : ?>
				<button type="button" class="btn btn-sm btn-success top-btn" onclick="accept()"><i class="fa fa-check-circle"></i> ยืนยันการรับสินค้า</button>
			<?php endif; ?>
			<button type="button" class="btn btn-sm btn-info top-btn" onclick="printReturn()"><i class="fa fa-print"></i> พิมพ์</button>			
    </p>
  </div>
</div>
<hr />
<div class="row">
  <div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-4 padding-5">
  	<label>เลขที่เอกสาร</label>
    <input type="text" class="form-control input-sm text-center" value="<?php echo $doc->code; ?>" disabled />
  </div>
	<div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-3 padding-5">
  	<label>วันที่</label>
    <input type="text" class="form-control input-sm text-center" value="<?php echo thai_date($doc->date_add); ?>" readonly disabled />
  </div>
	<div class="col-lg-4 col-md-3-harf col-sm-3-harf col-xs-5 padding-5">
		<label>ผู้ยืม</label>
		<input type="text" class="form-control input-sm" value="<?php echo $doc->empName; ?>" disabled/>
	</div>

	<div class="col-lg-3-harf col-md-3 col-sm-3 col-xs-6 padding-5">
		<label>ผู้รับคืน</label>
		<input type="text" class="form-control input-sm"  value="<?php echo $doc->display_name; ?>" disabled/>
	</div>
	<div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-6 padding-5">
		<label>ใบยืมสินค้า</label>
		<input type="text" class="form-control input-sm text-center" value="<?php echo $doc->lend_code; ?>" disabled />
	</div>

	<div class="divider-hidden"></div>

	<div class="col-lg-2 col-md-2 col-sm-2 col-xs-4 padding-5">
		<label>รหัสโซน[ยืม]</label>
		<input type="text" class="form-control input-sm" value="<?php echo $doc->from_zone; ?>" disabled />
	</div>

	<div class="col-lg-4 col-md-4 col-sm-4 col-xs-8 padding-5">
		<label>ชื่อโซน[ยืม]</label>
		<input type="text" class="form-control input-sm" value="<?php echo $doc->from_zone_name; ?>" disabled />
	</div>
	<div class="col-lg-2 col-md-2 col-sm-2 col-xs-4 padding-5">
		<label>รหัสโซน[รับคืน]</label>
		<input type="text" class="form-control input-sm" value="<?php echo $doc->to_zone; ?>" disabled />
	</div>
	<div class="col-lg-4 col-md-4 col-sm-4 col-xs-8 padding-5">
		<label>เข้าโซน[รับคืน]</label>
		<input type="text" class="form-control input-sm" value="<?php echo $doc->to_zone_name; ?>" disabled />
	</div>
	<?php if($doc->status == 2) : ?>
		<div class="col-lg-7 col-md-7 col-sm-7 col-xs-8 padding-5">
			<label>หมายเหตุ</label>
			<input type="text" class="form-control input-sm" value="<?php echo $doc->remark; ?>" disabled/>
		</div>
		<div class="col-lg-3 col-md-3 col-sm-3 col-xs-4 padding-5">
			<label>เหตุผลในการยกเลิก</label>
			<input type="text" class="form-control input-sm text-center" value="<?php echo $doc->cancle_reason; ?>" disabled />
		</div>
		<div class="col-lg-2 col-md-2 col-sm-2 col-xs-4 padding-5">
			<label>ยกเลิกโดย</label>
			<input type="text" class="form-control input-sm text-center" value="<?php echo $doc->cancle_user; ?>" disabled />
		</div>
	<?php else : ?>
		<div class="col-lg-10-harf col-md-10-harf col-sm-10-harf col-xs-8 padding-5">
			<label>หมายเหตุ</label>
			<input type="text" class="form-control input-sm" value="<?php echo $doc->remark; ?>" disabled/>
		</div>
		<div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-4 padding-5">
			<label>SAP No.</label>
			<input type="text" class="form-control input-sm text-center" value="<?php echo $doc->inv_code; ?>" disabled />
		</div>
	<?php endif; ?>
</div>

<?php
if($doc->is_expire)
{
	$this->load->view('expire_watermark');
}
else
{
	if($doc->status == 2)
	{
		$this->load->view('cancle_watermark');
	}

	if($doc->status == 3)
	{
		$this->load->view('on_process_watermark');
	}

	if($doc->status == 4)
	{
		$this->load->view('accept_watermark');
	}
}
?>
<hr class="margin-top-15"/>
<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 table-responsive">
		<table class="table table-striped border-1" style="min-width:1000px;">
			<thead>
				<tr>
					<th class="fix-width-40 middle text-center">#</th>
					<th class="min-width-150 middle">รหัส</th>
					<th class="min-width-200 middle">สินค้า</th>
					<th class="fix-width-100 middle text-center">ยืม</th>
					<th class="fix-width-100 middle text-center">คืนแล้ว<br/>(รวมครั้งนี้)</th>
					<th class="fix-width-100 middle text-center">ยอดคืน<br/>(ครั้งนี้)</th>
					<th class="fix-width-100 middle text-center">ยอดรับ<br/>(ครั้งนี้)</th>
					<th class="fix-width-100 middle text-center">คงเหลือ</th>
				</tr>
			</thead>
			<tbody id="result">
<?php if(!empty($details)) : ?>
	<?php $no = 1; ?>
	<?php $total_lend = 0; ?>
	<?php $total_receive = 0; ?>
	<?php $total_receive_qty = 0; ?>
	<?php $total_qty = 0; ?>
	<?php $total_backlogs = 0; ?>
	<?php foreach($details as $rs) : ?>
		<?php $backlogs = $rs->qty - $rs->receive; ?>
		<?php $backlogs = $backlogs < 0 ? 0 : $backlogs; ?>
				<tr>
					<td class="middle text-center no"><?php echo $no; ?></td>
					<td class="middle"><?php echo $rs->product_code; ?></td>
					<td class="middle"><?php echo $rs->product_name; ?></td>
					<td class="middle text-center"><?php echo ac_format($rs->qty, 2); ?></td>
					<td class="middle text-center"><?php echo ac_format($rs->receive, 2); ?></td>
					<td class="middle text-center"><?php echo ac_format($rs->return_qty, 2); ?></td>
					<td class="middle text-center"><?php echo ac_format($rs->receive_qty, 2); ?></td>
					<td class="middle text-center"><?php echo ac_format($backlogs, 2); ?></td>
				</tr>
	<?php
				$no++;
				$total_lend += $rs->qty;
				$total_receive += $rs->receive;
				$total_qty += $rs->return_qty;
				$total_receive_qty += $rs->receive_qty;
				$total_backlogs += $backlogs;
	?>
	<?php endforeach; ?>
			  <tr>
			  	<td colspan="3" class="text-right">รวม</td>
					<td class="middle text-center"><?php echo number($total_lend, 2); ?></td>
					<td class="middle text-center"><?php echo number($total_receive, 2); ?></td>
					<td class="middle text-center"><?php echo number($total_qty, 2); ?></td>
					<td class="middle text-center"><?php echo number($total_receive_qty, 2); ?></td>
					<td class="middle text-center"><?php echo number($total_backlogs, 2); ?></td>
			  </tr>
<?php else : ?>
				<tr>
					<td colspan="8" class="text-center">--- ไม่พบรายการ ---</td>
				</tr>
<?php endif; ?>
			</tbody>
		</table>
	</div>
</div>
<input type="hidden" id="empID" value="<?php echo $doc->empID; ?>">
<input type="hidden" id="zone_code" value="<?php echo $doc->to_zone; ?>">
<input type="hidden" id="code" value="<?php echo $doc->code; ?>">

<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5">
		<?php if($doc->must_accept == 1 && $doc->is_accept == 1) : ?>
			<span class="green display-block">ยืนยันการรับโดย : <?php echo $doc->accept_by; ?> @ <?php echo thai_date($doc->accept_on, TRUE); ?></span>
			<span class="green display-block">หมายเหตุ : <?php echo $doc->accept_remark; ?></span>
		<?php endif; ?>
	</div>
</div>

<?php $this->load->view('cancle_modal'); ?>
<?php $this->load->view('accept_modal'); ?>

<script src="<?php echo base_url(); ?>scripts/inventory/return_lend/return_lend.js?v=<?php echo date('Ymd');?>"></script>
<script src="<?php echo base_url(); ?>scripts/inventory/return_lend/return_lend_add.js?v=<?php echo date('Ymd');?>"></script>
<script src="<?php echo base_url(); ?>scripts/inventory/return_lend/return_lend_control.js?v=<?php echo date('Ymd');?>"></script>
<?php $this->load->view('include/footer'); ?>
